<h3>Inactive users</h3>

<p class="float-right">
	[ <a href="<?=site_url('admin/users/index');?>">Active users</a> ]
</p>

<?=form_open('admin/users/action'); ?>
<table border="0" class="listTable clear-both">
    
  <thead>
	<tr>
		<th class="first"><div></div></th>
		<th><a href="#">Name</a></th>
		<th><a href="#">E-mail</a></th>
		<th><a href="#">Role</a></th>
		<th><a href="#">Joined</a></th>
		<th><a href="#">Last visit</a></th>
		<th class="last"><span>Actions</span></th>
	</tr>
  </thead>
  
  	<tbody>
	<? if (!empty($users)): ?>
		<? foreach ($users as $member): ?>
			<tr>
				<td align="center"><input type="checkbox" name="action_to[<?= $member->id; ?>]" /></td>
				<td><?=$member->full_name; ?></td>
				<td><?=anchor('admin/users/edit/' . $member->id, $member->email); ?></td>
				<td><?=$member->role; ?></td>
				<td><?=date('M d, Y', $member->created_on); ?></td>
				<td><?=($member->last_login > 0 ? date('M d, Y', $member->last_login) : 'Never'); ?></td>
				<td>
					<?= anchor('admin/users/activate/' . $member->id, 'Activate') . ' | ' .
						anchor('admin/users/delete/' . $member->id, 'Delete', array('class'=>'confirm')); ?>
				</td>
			  </tr>
		<? endforeach; ?>
	<? else: ?>
		<tr><td colspan="7">There are no inactive users.</td></tr>
	<? endif; ?>
	</tbody>

</table>

<div class="fcc-table-buttons">
	<input type="image" name="submit" value="add" src="/assets/img/admin/fcc/btn-add.jpg" />

	<? if ($users): ?>
		<input type="image" name="submit" value="activate" src="/assets/img/admin/fcc/btn-activate.jpg" />
		<input type="image" name="submit" value="delete" src="/assets/img/admin/fcc/btn-delete.jpg" />
	<? endif; ?>
</div>

<?=form_close(); ?>