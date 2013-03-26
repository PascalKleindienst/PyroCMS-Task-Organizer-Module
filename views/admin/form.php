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
<?php if ($this->method == 'create'): ?>
	<h4><?php echo lang('to.create_title'); ?></h4>
<?php else: ?>
	<h4><?php echo sprintf(lang('to.edit_title'), $form->title); ?></h4>
<?php endif; ?>
</section>

<section class="item">
<div class="content">
<?php echo form_open(uri_string(), 'class="task_organizer"'); ?>
	<div class="form_inputs">
		<ul>
			<li>
		        <label for="title"><?php echo lang('to.form:title'); ?> <span>*</span></label>
		        <div class="input"><?php echo form_input('title', set_value('title', $form->title)); ?></div>
		    </li>

		    <li>
		        <label for="deadline"><?php echo lang('to.form:deadline'); ?> <span>*</span></label>
		        <div class="input">
		        	<input type="date" name="deadline" value="<?php echo set_value('deadline', $form->deadline);?>" />
		        </div>
		    </li>
		    <li>
		        <label for="email_notification"><?php echo lang('to.form:email_notification'); ?> <span>*</span></label>
		        <div class="input type-radio">
					<label class="inline">
						<?php echo form_radio('email_notification', 1, $form->email_notification	) ?>
						<?php echo lang('to.yes'); ?>
					</label>
					<label class="inline">
						<?php echo form_radio('email_notification', 0, !$form->email_notification	) ?>
						<?php echo lang('to.no'); ?>
					</label>
				</div>
		    </li>
		    <li>
		        <label for="done"><?php echo lang('to.form:done'); ?> <span>*</span></label>
		        <div class="input type-radio">
					<label class="inline">
						<?php echo form_radio('done', 1, $form->done) ?>
						<?php echo lang('to.yes'); ?>
					</label>
					<label class="inline">
						<?php echo form_radio('done', 0, !$form->done) ?>
						<?php echo lang('to.no'); ?>
					</label>
				</div>
			</li>
			<li>
				<label><?php echo lang('to.form:participants');?></label>
				<div class="input">
					<select size="5" multiple="multiple" name="participants[]">
					<?php foreach($form->possible_participants AS $u): ?>
						<option value="<?php echo $u->id;?>" <?php if(in_array($u->id, $form->participants)) echo 'selected="selected"';?>>
							<?php echo $u->username;?>
						</option>
					<?php endforeach; ?>
					</select>
				</div>
			</li>
			<li>
				<label for="description"><?php echo lang('to.form:description'); ?> <span>*</span></label>
				<br style="clear:both" />
				<?php echo form_textarea('description', set_value('description', $form->description), 'id="description" class="wysiwyg-advanced"');?>
			</li>
		</ul>
	</div>
	<div class="buttons">
		<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'save_exit', 'cancel'))); ?>
	</div>
<?php echo form_close(); ?>
</div>
</section>