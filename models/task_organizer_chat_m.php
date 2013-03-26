<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Task Organizer Chat Model
 * 
 * @package    PyroCMS
 * @subpackage Modules
 * @author     Pascal Kleindienst
 * @copyright  Copyright (c) 2013, Pascal Kleindienst
 * @link       http://www.pascalkleindienst.de
 * @license    LGPLv3
 */
class Task_Organizer_Chat_m extends MY_Model 
{
	/**
	 * Setting the table name
	 */
	public function __construct()
	{
		parent::__construct();
		$this->set_table_name('pk_task_organizer_chat');
	}
}