<?php
/**
 * Task Organizer Template
 * 
 * @package    PyroCMS
 * @subpackage Modules
 * @author     Pascal Kleindienst
 * @copyright  Copyright (c) 2013, Pascal Kleindienst
 * @link       http://www.pascalkleindienst.de
 * @license    LGPLv3
 */
?>
<section class="title"> 
	<h4><?php echo lang('to.title'); ?> &mdash; <?php echo $task->title; ?></h4>
	<small class="task_deadline"><?php echo lang('to.form:deadline');?>: {{ helper:date timestamp="<?php echo strtotime($task->deadline); ?>" }}</small>
</section>

<section class="item">
	<div class="content task">
		<div id="task_creator">
			{{ user:profile id="<?php echo $task->creator;?>"}}
			    {{ helper:gravatar email="{{ email }}" size="75"}}<br />
			    <strong>{{ display_name}}</strong><br />
			    {{ username }}<br />
			    <em class="last_login">{{ helper:date timestamp="{{ last_login }}" }}</em><br />
			{{ /user:profile }}
			<?php if($task->creator == $this->current_user->id || $this->ion_auth->is_admin()): ?>
			<p>
				<?php echo anchor('admin/task_organizer/edit/'.$task->id.'/', lang('global:edit'), 'class="button"'); ?>
				<?php echo anchor('admin/task_organizer/delete/'.$task->id.'/', lang('global:delete'), 'class="confirm button"'); ?>	
			</p>
			<?php endif ;?>
		</div>

		<div id="task_content">
			<h5><?php echo $task->title; ?></h5>
			<?php echo $task->description; ?>
		</div>
		
		<ul id="task_chat">
			<?php foreach($chat AS $msg): ?>
			<li>
				<div class="message"><?php echo $msg->message; ?></div>
				<div class="author">
					{{ user:profile id="<?php echo $msg->user_id;?>"}}
					    {{ helper:gravatar email="{{ email }}" size="25"}}<br />
					    <strong>{{ display_name}}</strong><br />
					    {{ username }}<br />
					    <em class="last_login">{{ helper:date timestamp="{{ last_login }}" }}</em><br />
					{{ /user:profile }}
				</div>
			</li>
			<?php endforeach; ?>
			<li class="chat_form">
				<?php echo form_open(uri_string(), 'class="chat"'); ?>
				<div class="form_inputs">
					<label for="chat"><?php echo lang('to.form:chat'); ?></label>
					<br style="clear:both" />
					<?php echo form_textarea('chat', NULL, 'id="chat" class="wysiwyg-simple"');?>
				</div>
				<div class="buttons">
					<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save'))); ?>
				</div>
				<?php echo form_close(); ?>
			</li>
		</ul>
	</div>
</section>