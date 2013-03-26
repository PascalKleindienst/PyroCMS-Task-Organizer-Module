<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Task Organizer Model
 * 
 * @package    PyroCMS
 * @subpackage Modules
 * @author     Pascal Kleindienst
 * @copyright  Copyright (c) 2013, Pascal Kleindienst
 * @link       http://www.pascalkleindienst.de
 * @license    LGPLv3
 */
class Task_Organizer_m extends MY_Model 
{
	/**
	 * Set the table name
	 */
	public function __construct()
	{
		parent::__construct();
		$this->set_table_name('pk_task_organizer');
	}

	/**
	 * Get all tasks which the current user can access
	 * @param  boolean $done Get all tasks which are (not) done
	 * @return mixed
	 */
	public function get_all($done = FALSE)
	{
		if($this->ion_auth->is_admin() || group_has_role('task_organizer', 'view_all')) {
			$q = $this->db
				->select('pk_task_organizer.*, users.username')
				->join('pk_task_organizer_participants', 'task_id = id', 'LEFT')
				->join('users', 'creator = users.id')
				->where('done', (int)$done)
				->order_by('deadline', 'ASC')
				->get('pk_task_organizer');

			return $q->result();
		}

		return $this->get_own($done);
	}

	/**
	 * Get all tasks which the current user has created
	 * @param  boolean $done Get all tasks which are (not) done
	 * @return mixed
	 */
	public function get_own($done = FALSE)
	{
		$q = $this->db
			->select('pk_task_organizer.*, users.username')
			->join('pk_task_organizer_participants', 'task_id = id')
			->join('users', 'creator = users.id')
			->where('done', (int)$done)
			->where('creator', $this->current_user->id)
			->or_where('user_id', $this->current_user->id)
			->order_by('deadline', 'ASC')
			->get('pk_task_organizer');

		return $q->result();
	}

	/**
	 * Create a css class
	 * @param  string $deadline The Deadline
	 * @return string
	 */
	public function deadline_class($deadline)
	{
		$deadline = new DateTime($deadline);
		$now      = new DateTime();
		$diff     = $deadline->diff($now);
		
		if($diff->invert == 1 && $diff->days < 7)
			return 'class="soon-overdue"';
		else if($diff->invert == 0)
			return 'class="overdue"';

		return '';
	}

	/**
	 * Get all tasks which shoud be notified via email
	 * @return mixed
	 */
	public function get_email_notifications()
	{
		$q = $this->db
			->select('title, deadline, c.email, t.id')
			->from('pk_task_organizer AS t')
			->where('email_notification', 1)
			->where('done', 0)
			->where('DATEDIFF(deadline, CURDATE()) <', 7)
			->join('users AS c', 'creator = c.id')
			->get();

		$tasks = $q->result();
		$this->load->model('task_organizer/task_organizer_participants_m');

		foreach($tasks AS $key => $t) 
		{
			$users = $this->task_organizer_participants_m->get_participants_mail($t->id);
			$tasks[$key]->users = $users;
		}

		return $tasks;
	}

	/**
	 * Update the email_notification status, so we now the email was sent
	 * @param  int $task_id The ID of the task
	 * @return boll
	 */
	public function notification_shipped($task_id)
	{
		return parent::update($task_id, array('email_notification' => 2));
	}
}