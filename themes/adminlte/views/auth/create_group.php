<div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo lang('create_group_heading');?></h3>
<small><?php echo lang('create_group_subheading');?></small>
              <div class="box-tools">
                
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">

           	<?php echo form_open("panel/auth/create_group", 'id="add_group_form"');?>

			      <p>
			            <?php echo lang('group_name', 'group_name');?> <br />
			            <?php echo form_input($group_name);?>
			      </p>

			      <p>
			            <?php echo lang('group_description', 'description');?> <br />
			            <?php echo form_input($description);?>
			      </p>

			      <p><?php echo form_submit('submit', lang('create_group_submit_btn'), 'class="form-control"');?></p>

			<?php echo form_close();?>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
  </div>


