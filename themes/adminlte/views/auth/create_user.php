<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>
     
<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('add_user');?></h3>
      <div class="box-tools pull-right">
      </div>
  </div>
  <div class="box-body">
     <?php echo validation_errors('<div class="alert alert-warning">', '</div>'); ?>
              <?php echo form_open_multipart('panel/auth/create_user', array('class'=>'parsley-form'));?>

                    <div class="form-group">
                            <?php echo lang('first_name', 'first_name');?> <br />
                            <?php echo form_input($first_name);?>
                      </div>
                    <div class="form-group">
                            <?php echo lang('last_name', 'last_name');?> <br />
                            <?php echo form_input($last_name);?>
                      </div>
                      <?php
                      if($identity_column!=='email') {
                          echo '<p>';
                          echo lang('create_user_identity_label', 'identity');
                          echo '<br />';
                          echo form_error('identity');
                          echo form_input($identity);
                          echo '</p>';
                      }
                      ?>
                      <div class="form-group">
                        <label for="user_image"><?php echo lang('user_image_upload'); ?></label>                        
                        <input id="user_image" type="file" name="user_image" data-show-upload="false" data-show-preview="false" accept="image/*" class="form-control file">

                    </div>
                    <div class="form-group">
                          <label>Pin Code:</label> <br />
                          <?php echo form_input($pin_code);?>
                    </div>
                    <div class="form-group">
                            <?php echo lang('company', 'company');?> <br />
                            <?php echo form_input($company);?>
                     </div>
                    <div class="form-group">
                            <?php echo lang('email', 'email');?> <br />
                            <?php echo form_input($email);?>
                     </div>
                    <div class="form-group">
                            <?php echo lang('phone', 'phone');?> <br />
                            <input type="text" name="phone" value="" id="phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask="" <?php echo $frm_priv['phone'] ? 'required': '';?>>
                     </div>
                    <div class="form-group">
                            <?php echo lang('password', 'password');?> <br />
                            <?php echo form_input($password);?>
                      </div>
                    <div class="form-group">
                            <?php echo lang('confirm_password', 'password_confirm');?> <br />
                            <?php echo form_input($password_confirm);?>
                      </div>
                      <div class="form-group">
                          <?php echo lang('group', 'group'); ?>
                          <?php
                          foreach ($groups as $group) {
                              if ($group['name'] != 'customer' && $group['name'] != 'supplier') {
                                  $gp[$group['id']] = $group['name'];
                              }
                          }
                          echo form_dropdown('group', $gp, (isset($_POST['group']) ? $_POST['group'] : ''), 'id="group" required="required" class="form-control select" style="width:100%;"');
                          ?>
                      </div>

                      <div class="row">
                        <div class="col-md-12">

                          <div class="form-group">
                            <label><?php echo lang('Accessible Stores');?></label>
                            <div class="checkbox-styled">
                                <input type="hidden" value="0" name="all_stores">
                                <input type="checkbox" class="skip" value="1" name="all_stores" id="all_stores">
                                <label for="all_stores"><?php echo lang('All Stores');?></label>
                            </div>
                            <div class="hide_stores">
                              <select class="form-control" name="stores[]" id="accessibleStores" multiple required>
                                <?php foreach ($stores as $store):?>
                                  <option value="<?php echo $store['id']; ?>"><?php echo escapeStr($store['name']); ?></option>
                                <?php endforeach?>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>

                      <p><?php echo form_submit('submit', lang('create_user'), 'class="form-control"');?></p>
                <?php echo form_close();?>
  </div>
</div>
<script type="text/javascript">
  jQuery(document).ready( function($) {
    $('.parsley-form').parsley();
  });
  $(":checkbox").on("change", function () {
      $(".hide_stores").slideToggle(!this.checked);
      if (!this.checked) {
         $("#accessibleStores").attr('required', false);
      }else{
         $("#accessibleStores").attr('required', false);
      }
  });
</script>