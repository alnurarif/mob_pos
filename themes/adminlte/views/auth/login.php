<style type="text/css">
    
</style>
<div class="login-box">

  <div class="login-logo"><img style="width: 100%" src="<?php echo base_url(); ?>assets/uploads/logos/<?php echo $settings->logo; ?>"></div>

  <div class="login-box-body">
    <p class="login-box-msg"><?php echo lang('login_subheading');?></p>

      <div id="infoMessage"><?php echo escapeStr($message);?></div>
      <?php echo form_open("panel/login", 'id="login_form"');?>

        <div class="form-group has-feedback">
          <?php echo form_input($identity, '', "class='form-control'");?>
          <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>

        <div class="form-group has-feedback">
          
          <?php echo form_input($password, '', "class='form-control'");?>
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="row">
          
          <div class="col-xs-8">
          <label>
            <?php echo form_checkbox('remember', '1', FALSE, 'id="remember"');?><?php echo lang('remember_me');?></label>
          </div>
          <div class="col-xs-4">
            <?php echo form_submit('submit', lang('login'), 'class="btn btn-primary btn-block btn-flat"');?>
          </div>
        </div>

      <?php echo form_close();?>

        <a  href="<?php echo base_url();?>/panel/login/forgot_password"><?php echo lang('login_forgot_password');?></a>
        <br>
        <a href="<?php echo base_url();?>"><?php echo lang('back_homepage');?></a>
        
        <div class="copyrights">Powered By <a href="https://i-fixnyc.com">iFIX NYC | Upstairs NY Inc</a></div>

  </div>
  
</div>

<script type="text/javascript">
$('#remember').iCheck({ checkboxClass: 'icheckbox_square-blue',
  radioClass: 'iradio_square-blue',
  increaseArea: '20%' 
});

</script>