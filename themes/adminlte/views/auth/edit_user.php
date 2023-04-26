<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>


<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('edit_user');?></h3>
      <div class="box-tools pull-right">
      </div>
  </div>
  <div class="box-body">
    
            <?php echo validation_errors('<div class="alert alert-warning">', '</div>'); ?>

             
              <?php echo form_open_multipart(uri_string(), array('class'=>'parsley-form'));?>

                    <div class="form-group">
                        <?php echo lang('first_name', 'first_name');?> <br />
                        <?php echo form_input($first_name);?>
                    </div>
                    <div class="form-group">
                        <?php echo lang('last_name', 'last_name');?> <br />
                        <?php echo form_input($last_name);?>
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
                        <?php echo lang('phone', 'phone');?> <br />
                        <input type="text" name="phone" value="<?php echo $phone; ?>" id="phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask="" <?php echo $frm_priv['phone'] ? 'required': '';?>>
                    </div>
                    <div class="form-group">
                      <label for="user_image"><?php echo lang('user_image_upload'); ?></label> 
                      <center><div style="image image-responsive"><img height="60px" src="<?php echo base_url(); ?>assets/uploads/members/<?php echo $image; ?>"></div>    </center>                   

                      <input id="user_image" type="file" name="user_image" data-show-upload="false" data-show-preview="false" accept="image/*" class="form-control file">
                  </div>
                    <div class="form-group">
                          <?php echo lang('new_password', 'password');?> <br />
                          <?php echo form_input($password);?>
                    </div>
                    <div class="form-group">
                          <?php echo lang('new_password_confirm', 'password_confirm');?><br />
                          <?php echo form_input($password_confirm);?>
                    </div>
                      <div class="clearfix"></div>
                      <?php if($this->Admin): ?>

                        <div class="form-group">
                            <?php echo lang('group', 'group'); ?>
                            <?php
                              $gp[''] = '';
                              foreach ($groups as $group) {
                                  $gp[$group['id']] = $group['name'];
                              }
                              echo form_dropdown('group', $gp, (isset($_POST['group']) ? $_POST['group'] : $user->group_id), 'id="group" data-placeholder="' . $this->lang->line('select') . ' ' . $this->lang->line('group') . '" required="required" class="form-control input-tip select" style="width:100%;"'); 
                            ?>
                        </div>


                    <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label><?php echo lang('Accessible Stores');?></label>
                            <div class="checkbox-styled">
                                <input type="hidden" value="0" name="all_stores">
                                <input type="checkbox" class="skip"  <?php echo ($edit_user->all_stores) ? 'checked' : ''; ?> value="1" name="all_stores" id="all_stores">
                                <label for="all_stores"><?php echo lang('All Stores');?></label>
                            </div>
                            <div <?php echo ($edit_user->all_stores) ? 'style="display:none;"' : ''; ?> class="hide_stores">
                              <select class="form-control" name="stores[]" id="accessibleStores" multiple required>
                                <?php foreach ($stores as $store):?>
                                  <option <?php echo ($edit_user->stores && in_array($store['id'], $edit_user->stores) )? 'selected' : ''; ?> value="<?php echo $store['id']; ?>"><?php echo escapeStr($store['name']); ?></option>
                                <?php endforeach?>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php endif ?>

                    <?php echo form_hidden('id', $edit_user->id);?>
                    <?php echo form_hidden($csrf); ?>
  
                    <p><?php echo form_submit('submit', lang('edit_user'), 'class="form-control"');?></p>

              <?php echo form_close();?>
  </div>
</div>

<script type="text/javascript">
jQuery(document).ready( function($) {
    $('.parsley-form').parsley();
  });
<?php if($edit_user->all_stores): ?>
  jQuery(document).ready( function($) {
     $("#accessibleStores").attr('required', false);
  });
<?php endif; ?>
  $(":checkbox").on("change", function () {
      $(".hide_stores").slideToggle(!this.checked);
      if (!this.checked) {
         $("#accessibleStores").attr('required', false);
      }else{
         $("#accessibleStores").attr('required', false);
      }
  });
</script>