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
	<h4><?php echo lang('to.title'); ?><?php if ($this->method == 'done') echo ' &mdash; ' . lang('to.title_done');?></h4>
</section>
<section class="item">
	<div class="content">
	<?php if($tasks): ?>
		<table class="table-list">
			<thead>
				<tr>
					<th><?php echo lang('to.form:title');?></th>
					<th><?php echo lang('to.form:deadline');?></th>
					<th><?php echo lang('to.form:creator');?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($tasks as $t): ?>
				<tr <?php if($this->method != 'done') echo $this->task_organizer_m->deadline_class($t->deadline);?>>
					<td><?php echo $t->title; ?></td>
					<td>{{ helper:date timestamp="<?php echo strtotime($t->deadline); ?>" }}</td>
					<td><?php echo $t->username; ?></td>
					<td class="actions">
						<?php echo anchor('admin/task_organizer/view/'.$t->id.'/', lang('global:view'), 'class="button"'); ?>
					<?php if($this->ion_auth->is_admin() || $t->creator == $this->current_user->id): ?>
						<?php echo anchor('admin/task_organizer/edit/'.$t->id.'/', lang('global:edit'), 'class="button"'); ?>
						<?php echo anchor('admin/task_organizer/delete/'.$t->id.'/', lang('global:delete'), 'class="confirm button"'); ?>
					<?php endif; ?>
					</td>
				</tr>	
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else: ?>
		<div class="no_data"><?php echo lang('to.no_data'); ?></div>
	<?php endif; ?>
	</div>
</section>