<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('change_logo'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open_multipart("panel/payroll/change_logo/".$id, $attrib); ?>
        <div class="modal-body">
            <?php if($template->logo): ?>
                <img class="img-responsive" src="<?php echo base_url();?>assets/uploads/payroll_templates/<?php echo $template->logo;?>" width="200px">
            <?php endif; ?>
            <div class="clearfix"></div>
            <div class="form-group">
                <?php echo lang("select_logo_file", "logo") ?>
                <input id="logo" type="file" data-browse-label="<?php echo lang('browse'); ?>" name="logo" data-show-upload="false" data-show-preview="false" class="form-control file">
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success  btn-icon "><i class="fa fa-save img-circle text-success"></i> <?php echo lang('upload_logo');?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
