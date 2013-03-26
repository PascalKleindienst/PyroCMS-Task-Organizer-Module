<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Task Organizer Events
 * 
 * @package    PyroCMS
 * @subpackage Modules
 * @author     Pascal Kleindienst
 * @copyright  Copyright (c) 2013, Pascal Kleindienst
 * @link       http://www.pascalkleindienst.de
 * @license    LGPLv3
 */
class Events_Task_Organizer
{
	/**
	 * CodeIgniter Reference
	 */
	protected $ci;

	/**
	 * Register event
	 */
	public function __construct()
	{
		$this->ci =& get_instance();

		Events::register('public_controller', array($this, 'send_mail'));
	}

	/**
	 * Registered Event - Send email notifications
	 * @return [type] [description]
	 */
	public function send_mail()
	{
		$this->ci->load->library('email');
		$this->ci->load->model('task_organizer/task_organizer_m');
		
		$tasks = $this->ci->task_organizer_m->get_email_notifications();

		// Add in some extra details
		$notification['slug'] = 'task_organizer_reminder';
		$notification['name'] = 'system';
		
		foreach($tasks AS $t)
		{
			$notification['title']    = $t->title;
			$notification['deadline'] = $t->deadline;

			foreach($t->users AS $u) 
			{
				$notification['to']           = $u->email;
				$notification['username']     = $u->username;
				$notification['display_name'] = $u->display_name ? $u->display_name : $u->username;

				Events::trigger('email', $notification);
			}

			$this->ci->task_organizer_m->notification_shipped($t->id);
		}
	}
}