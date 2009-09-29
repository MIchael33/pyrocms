<?php echo form_open('admin/widgets/manage');?>
<div class="fieldset fieldsetBlock active tabs">
	<!-- Header div -->
	<div class="header">
		<h3>Install Widget</h3>
	</div>
	<!-- Tabs div -->
	<div class="tabs">
		<ul class="clear-box">
			<li><a href="#fieldset1"><span>Upload Widget</span></a></li>
		</ul>
		<!-- Install widget fieldset -->
		<fieldset id="fieldset1">
			<div class="field">
				<label for="widget">Widget</label>
				<input type="file" id="widget" name="widget" value="" />
			</div>
		</fieldset>
		<!-- Upload widget fieldset -->
		<fieldset id="fieldset2">
			
		</fieldset>
	</div>
</div>
<?php $this->load->view('admin/fragments/table_buttons', array('buttons' => array('save','cancel') )); ?>
<?php echo form_close(); ?>