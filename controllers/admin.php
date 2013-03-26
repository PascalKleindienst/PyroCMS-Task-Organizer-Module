<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Task Organizer Module Admin Controller
 * 
 * @package    PyroCMS
 * @subpackage Modules
 * @author     Pascal Kleindienst
 * @copyright  Copyright (c) 2013, Pascal Kleindienst
 * @link       http://www.pascalkleindienst.de
 * @license    LGPLv3
 */
class Admin extends Admin_Controller 
{
	/**
	 * Section
	 * @var string
	 */
	protected $section = 'task_organizer';

	/**
	 * Validation Rules
	 * @var array
	 */
	private $validation_rules = array(
		'title' => array(
			'field' => 'title',
			'label' => 'lang:to.label:title',
			'rules' => 'trim|htmlspecialchars|required|max_length[75]'
		),
		'description' => array(
			'field' => 'description',
			'label' => 'lang:to.label:description',
			'rules' => 'trim|required'
		),
		'deadline' => array(
			'field' => 'deadline',
			'label' => 'lang:to.label:deadline',
			'rules' => 'trim|required|exact_length[10]|alpha_dash'
		),
		'email_notification' => array(
			'field' => 'email_notification',
			'label' => 'lang:to.label:email_notification',
			'rules' => 'trim|required|exact_length[1]|integer'
		),
		'done' => array(
			'field' => 'done',
			'label' => 'lang:to.label:done',
			'rules' => 'trim|required|exact_length[1]|integer'
		),
	);

	/**
	 * Load all needed languages/model/libraries
	 * @access public
	 */
	public function __construct()
	{
		if($this->uri->segment(3) == 'done')
			$this->section = 'task_organizer_done';

		parent::__construct();
		
		$this->load->language('task_organizer');
		$this->load->model('task_organizer_m');
		$this->load->model('task_organizer_participants_m');
		$this->load->model('task_organizer_chat_m');
		$this->load->library('form_validation');
	}

	/**
	 * View all unfinished tasks
	 */
	public function index()
	{
		$this->_view_all();
	}

	/**
	 * View all finished tasks
	 */
	public function done()
	{
		$this->_view_all(TRUE);
	}

	/**
	 * View all tasks
	 * @access private
	 * @param  boolean $done finished or unfinished tasks
	 */
	private function _view_all($done = FALSE)
	{
		$data['tasks'] = $this->task_organizer_m->get_all($done);

		$this->template
			->title($this->module_details['name'])
			->append_metadata('<link rel="stylesheet" href="' . base_url($this->module_details['path'] . '/css/style.css') . '" />')
			->enable_parser(TRUE)
			->build('admin/index', $data);
	}

	/**
	 * View a specific task
	 * @param  integer $id Task ID
	 */
	public function view($id = 0)
	{
		// post a chat entry
		if(!empty($_POST))
		{
			$pdata = array(
				'task_id' => $id,
				'user_id' => $this->current_user->id,
				'message' => $this->input->post('chat')
			);

			$insert_id = $this->task_organizer_chat_m->insert($pdata);
		}

		// Get the task and chat
		$data['task'] = $this->task_organizer_m->get($id);
		$data['chat'] = $this->task_organizer_chat_m->order_by('id', 'DESC')->get_many_by(array('task_id' => $id));

		$this->template
			->title($this->module_details['name'])
			->append_metadata('<link rel="stylesheet" href="' . base_url($this->module_details['path'] . '/css/style.css') . '" />')
			->append_metadata($this->load->view('fragments/wysiwyg', array(), TRUE))
			->enable_parser(TRUE)
			->build('admin/view', $data);
	}

	/**
	 * Create a new task
	 */
	public function create()
	{
		$this->method = 'create';
		$this->form_validation->set_rules($this->validation_rules);	

		// form was send and passed validation
		if($this->form_validation->run()) 
		{				
			// setup post data, and insert the entry
			$data = array(
				'creator'            => $this->current_user->id,
				'title'              => $this->input->post('title'),
				'deadline'           => $this->input->post('deadline'),
				'email_notification' => $this->input->post('email_notification'),
				'done'               => $this->input->post('done'),
				'description'        => $this->input->post('description')
			);
			
			$insert_id = $this->task_organizer_m->insert($data);

			if($insert_id && $this->input->post('participants') != NULL) 
			{
				$data = array();
				foreach ($this->input->post('participants') as $key => $value)
					$data[] = array('task_id' => $insert_id, 'user_id' => $value);
				
				$this->task_organizer_participants_m->insert_many($data);
			}
			
			// success message and redirection	
			if($insert_id) 
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('to.add_success'), $this->input->post('title')));
				
				$this->input->post('btnAction') == 'save_exit' ? 
					redirect('admin/task_organizer/') : 
					redirect('admin/task_organizer/edit/' . $insert_id);
			}
		}
		
		$data['form']                        = new stdClass();
		$data['form']->title                 = null;
		$data['form']->description           = null;
		$data['form']->deadline              = null;
		$data['form']->email_notification    = null;
		$data['form']->done                  = null;
		$data['form']->participants          = array();
		$data['form']->possible_participants = $this->task_organizer_participants_m->get_possible_participants();

		$this->template
			->title($this->module_details['name'])
			->append_metadata($this->load->view('fragments/wysiwyg', array(), TRUE))
			->build('admin/form',$data);
	}

	/**
	 * Edit a task
	 * @param  integer $id Task ID
	 */
	public function edit($id = 0)
	{
		$this->method = 'edit';
		$this->form_validation->set_rules($this->validation_rules);	

		// form was send and passed validation
		if($this->form_validation->run()) 
		{				
			// setup post data, and insert the entry
			$data = array(
				'creator'            => $this->current_user->id,
				'title'              => $this->input->post('title'),
				'deadline'           => $this->input->post('deadline'),
				'email_notification' => $this->input->post('email_notification'),
				'done'               => $this->input->post('done'),
				'description'        => $this->input->post('description')
			);
			
			$update_id = $this->task_organizer_m->update($id, $data);

			if($update_id) 
			{
				$data = array();
				foreach ($this->input->post('participants') as $key => $value)
					$data[] = array('task_id' => $id, 'user_id' => $value);
				
				$this->task_organizer_participants_m->delete_by_task($id);
				$this->task_organizer_participants_m->insert_many($data);
			}
			
			// success message and redirection	
			if($update_id) 
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('to.add_success'), $this->input->post('title')));
				
				$this->input->post('btnAction') == 'save_exit' ? 
					redirect('admin/task_organizer/') : 
					redirect('admin/task_organizer/edit/' . $update_id);
			}
		}

		$data['form']                        = $this->task_organizer_m->get($id);
		$data['form']->participants          = $this->task_organizer_participants_m->get_participants($id);
		$data['form']->possible_participants = $this->task_organizer_participants_m->get_possible_participants();

		$this->template
			->title($this->module_details['name'])
			->append_metadata($this->load->view('fragments/wysiwyg', array(), TRUE))
			->build('admin/form',$data);
	}

	/**
	 * Delete a task
	 * @param  int $id Task ID
	 */
	public function delete($id)
	{
		if(!isset($id)) 
		{ 
			$this->session->set_flashdata('error', lang('to.delete_fail') );
			redirect('admin/task_organizer'); 
		} else {
			$this->session->set_flashdata('success', lang('to.delete_success') );
			$this->task_organizer_m->delete($id); 
			redirect('admin/task_organizer');
		}
	}

}