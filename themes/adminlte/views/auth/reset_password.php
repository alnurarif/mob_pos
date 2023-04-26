
<div class="login-box">
  <div class="login-logo"><img style="width: 100%" src="<?php echo base_url(); ?>assets/uploads/logos/<?php echo $settings->logo; ?>"></div>
  <div class="login-box-body">
    <p class="login-box-msg"><?php echo lang('reset_password_heading');?></p>
        

		    <?php echo form_open('panel/login/reset_password/' . $code);?>
          <div class="form-group has-feedback">

          	<?php echo form_input($new_password, '', "class='form-control' placeholder='".sprintf(lang('reset_password_new_password_label'), $min_password_length)."'");?>
          	<span class="glyphicon glyphicon-lock form-control-feedback"></span>

          </div>

          <div class="form-group has-feedback">
          	<?php echo form_input($new_password_confirm, '', "class='form-control' placeholder='".lang('reset_password_new_password_confirm_label')."'");?>
          	<span class="glyphicon glyphicon-lock form-control-feedback"></span>

          </div>

          	<?php echo form_input($user_id);?>
			       <?php echo form_hidden($csrf); ?>

          <div class="row">
            
            <div class="col-xs-8"></div>
            <div class="col-xs-4">
              <?php echo form_submit('submit', lang('reset_password_submit_btn'), 'class="btn btn-primary btn-block btn-flat"');?>
            </div>
          </div>
      <?php echo form_close();?>
      <a href="<?php echo base_url();?>/panel/login"> &larr; <?php echo lang('back_to_login');?></a>
  </div>
</div>


