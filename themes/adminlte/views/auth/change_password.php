<div class="row">
  <div class="col-xs-12">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title"><?php echo lang('change_password_heading');?></h3>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
      <?php echo form_open("panel/auth/change_password");?>
        <p>
          <?php echo lang('change_password_old_password_label', 'old_password');?> <br />
          <?php echo form_input($old_password);?>
        </p>
        <p>
          <label for="new_password"><?php echo sprintf(lang('change_password_new_password_label'), $min_password_length);?></label> <br />
          <?php echo form_input($new_password);?>
        </p>
        <p>
          <?php echo lang('change_password_new_password_confirm_label', 'new_password_confirm');?> <br />
          <?php echo form_input($new_password_confirm);?>
        </p>
        <?php echo form_input($user_id);?>
        <p><?php echo form_submit('submit', lang('change_password_submit_btn'), 'class="form-control"');?></p>
      <?php echo form_close();?>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
</div>


