<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Task Organizer Participants Model
 * 
 * @package    PyroCMS
 * @subpackage Modules
 * @author     Pascal Kleindienst
 * @copyright  Copyright (c) 2013, Pascal Kleindienst
 * @link       http://www.pascalkleindienst.de
 * @license    LGPLv3
 */
class Task_Organizer_Participants_m extends MY_Model 
{
	/**
	 * Primary Key
	 * @var string
	 */
	protected $primary_key = 'task_id';

	/**
	 * Set the table name
	 */
	public function __construct()
	{
		parent::__construct();
		$this->set_table_name('pk_task_organizer_participants');
	}

	/**
	 * Get all user IDs of participants of a task
	 * @param  int $id Task ID
	 * @return mixed
	 */
	public function get_participants($id)
	{
		$users = parent::get_many($id);
		$u = array();

		foreach($users AS $user)
			$u[] = $user->user_id;

		return $u;
	}

	/**
	 * Get all participants which are should be emaild a notification
	 * @param  int $id Task ID
	 * @return mixed
	 */
	public function get_participants_mail($id)
	{
		$q = $this->db
			->select('email, username, display_name')
			->join('users', 'user_id = id')
			->join('profiles', 'users.id = profiles.id')
			->where('task_id', $id)
			->get($this->_table);

		return $q->result();
	}

	/**
	 * Delete all participants of a task
	 * @param  int $task_id Task ID
	 * @return bool
	 */
	public function delete_by_task($task_id)
	{
		return $this->db
			->where('task_id', $task_id)
			->delete($this->_table);
	}

	/**
	 * Get all users which could be participants in a task
	 * @return mixed
	 */
	public function get_possible_participants()
	{
		$groups = array('admin');

		$perm_groups = $this->db
			->select('name')
			->where('module', 'task_organizer')
			->join('groups', 'groups.id = group_id')
			->get('permissions')
			->result();

		foreach ($perm_groups as $key => $value) {
			$groups[] = $value->name;
		}

		$users = $this->ion_auth->get_users($groups);

		return $users;
	}
}