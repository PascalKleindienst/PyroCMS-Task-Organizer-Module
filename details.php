<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Task Organizer Module Details
 * 
 * @package    PyroCMS
 * @subpackage Modules
 * @author     Pascal Kleindienst
 * @copyright  Copyright (c) 2013, Pascal Kleindienst
 * @link       http://www.pascalkleindienst.de
 * @license    LGPLv3
 */
class Module_Task_Organizer extends Module
{
	/**
	 * Current Version
	 * @var string
	 */
	public $version = '0.1';

	/**
	 * module informations
	 * @return array
	 */
	public function info()
	{
		return array(
			'name' => array(
				'de' => 'Aufgabenplaner',
				'en' => 'Task Organizer',
				'es' => 'Gestor de tareas',
			),
			'description' => array(
				'de' => 'Ein kleines Modul mit den man noch zu erledigende Aufgaben managen kann',
				'en' => 'A small Modul with whom you can manage tasks',
				'es' => 'Un pequeño módulo para gestionar tareas',
			),
			'frontend' => FALSE,
			'backend'  => TRUE,
			'menu'     => 'content',
			'roles'    => array(
				'view_all'
			),
			'sections' => array(
				'task_organizer' => array(
					'name' => 'to.title',
					'uri'  => 'admin/task_organizer',
					'shortcuts' => array(
						'create' => array(
 							'name'  => 'to.create_button',
							'uri'   => 'admin/task_organizer/create',
							'class' => 'add'
						)
					)	
				),
				'task_organizer_done' => array(
					'name' => 'to.title_done',
					'uri'  => 'admin/task_organizer/done',
					'shortcuts' => array(
						'create' => array(
 							'name'  => 'to.create_button',
							'uri'   => 'admin/task_organizer/create',
							'class' => 'add'
						)
					)	
				)
			)
		);
	}

	/**
	 * Install Routine
	 * @return boolean
	 */
	public function install()
	{
		// delete all installation
		$this->dbforge->drop_table('pk_task_organizer', TRUE);
		$this->dbforge->drop_table('pk_task_organizer_participants', TRUE);
		$this->dbforge->drop_table('pk_task_organizer_chat', TRUE);
		$this->db->delete('settings', array('module' => 'task_organizer'));

		// Task Organizer Table
		$to = array(
			'id' => array(
				'type'           => 'SMALLINT',
				'unsigned'       => TRUE,
				'auto_increment' => TRUE
			),
			'creator' => array(
				'type'     => 'MEDIUMINT',
				'unsigned' => TRUE
			),
			'title' => array(
				'type'       => 'VARCHAR',
				'constraint' => 75
			),
			'description' => array(
				'type' => 'TEXT'
			),
			'deadline' => array(
				'type' => 'DATE'
			),
			'email_notification' => array(
				'type' => 'TINYINT'
			),
			'done' => array(
				'type' => 'TINYINT'
			)
		);

		$this->dbforge->add_field($to);
		$this->dbforge->add_key('id',TRUE);
		$this->dbforge->create_table('pk_task_organizer',TRUE);

		// Task Organizer Participants Table
		$to_p = array(
			'task_id' => array(
				'type'     => 'SMALLINT',
				'unsigned' => TRUE
			),
			'user_id' => array(
				'type'     => 'MEDIUMINT',
				'unsigned' => TRUE
			)
		);

		$this->dbforge->add_field($to_p);
		$this->dbforge->add_key('task_id',TRUE);
		$this->dbforge->add_key('user_id',TRUE);
		$this->dbforge->create_table('pk_task_organizer_participants',TRUE);

		// Task Organizer Chat Table
		$to_c = array(
			'id' => array(
				'type'           => 'MEDIUMINT',
				'unsigned'       => TRUE,
				'auto_increment' => TRUE
			),
			'task_id' => array(
				'type'     => 'SMALLINT',
				'unsigned' => TRUE
			),
			'user_id' => array(
				'type'     => 'MEDIUMINT',
				'unsigned' => TRUE
			),
			'message' => array(
				'type'       => 'VARCHAR',
				'constraint' => 250
			)
		);

		$this->dbforge->add_field($to_c);
		$this->dbforge->add_key('id',TRUE);
		$this->dbforge->add_key(array('task_id', 'user_id'));
		$this->dbforge->create_table('pk_task_organizer_chat',TRUE);

		// Email Templates
		$email_tmpl = array(
			'slug'        => 'task_organizer_reminder',
			'name'        => 'Reminder of Task Organizer',
			'description' => 'Reminder that is send to all participants and the creator a weak before the deadline ends',
			'subject'     => 'Task {{ title }} - Deadline: {{ deadline }}',
			'body'        => 'Hello {{ display_name }},<br /><br />the deadline of the task {{ title }} is on {{ deadline }}, but the task is not done yet.',
			'lang'        => 'en',
			'is_default'  => 1,
			'module'      => 'task_organizer'
		);
		$this->db->insert('email_templates', $email_tmpl);

		return TRUE;
	}

	/**
	 * Uninstall Routine
	 * @return bool
	 */
	public function uninstall()
	{
		$this->dbforge->drop_table('pk_task_organizer');
		$this->dbforge->drop_table('pk_task_organizer_participants');
		$this->dbforge->drop_table('pk_task_organizer_chat');

		$this->db->delete('email_templates', array('module', 'task_organizer'));

		return TRUE;
	}

	/**
	 * Upgrade Routine
	 * @param  int $old_version
	 * @return bool
	 */
	public function upgrade($old_version)
	{
		return TRUE;
	}
}
