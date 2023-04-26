 <div class="col-md-12">
    <div class="panel panel-default" id="dripicons-iconz">
        <div class="panel-heading ui-sortable-handle">
            <div class="panel-title"><?php echo lang('edit_group_heading');?></div>
            <small><?php echo lang('edit_group_subheading');?></small>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
          <div class="row">

			<?php echo form_open(current_url());?>

			      <p>
			            <?php echo lang('group_name', 'group_name');?> <br />
			            <?php echo form_input($group_name);?>
			      </p>

			      <p>
			            <?php echo lang('group_description', 'description');?> <br />
			            <?php echo form_input($group_description);?>
			      </p>

			      <p><?php echo form_submit('submit', lang('save'), 'class="form-control"');?></p>
			      
			<?php echo form_close();?>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
  </div>