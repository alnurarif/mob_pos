
<div class="login-box">

  <div class="login-logo"><img style="width: 100%" src="<?php echo base_url(); ?>assets/uploads/logos/<?php echo $settings->logo; ?>"></div>

  <div class="login-box-body">
    <p class="login-box-msg"><?php echo lang('forgot_password_heading');?><br><small><?php echo sprintf(lang('forgot_password_subheading'), 'email');?></small></p>


      <div id="infoMessage"><?php echo escapeStr($message);?></div>

      <?php echo form_open("panel/login/forgot_password");?>
          <?php $label = lang('email');?>
          <div class="form-group has-feedback">
            <?php echo form_input($identity, '', "class='form-control' placeholder='".$label."'");?>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>
          <div class="row">
           
            <div class="col-xs-6"></div>
            <div class="col-xs-6" style="text-align: right;">
              <?php echo form_submit('submit', lang('forgot_password'), 'class="btn btn-primary  btn-flat"');?>
            </div>
          </div>
      <?php echo form_close();?>
      <a href="<?php echo base_url();?>/panel/login"> &larr; <?php echo lang('back_to_login');?></a>
  </div>
</div>