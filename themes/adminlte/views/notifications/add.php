<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_notification'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => 'off'];
        echo form_open('panel/notifications/add', $attrib); ?>
        <div class="modal-body">
            <p><?php echo lang('enter_info'); ?></p>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <?php echo lang('from', 'from_date'); ?>
                        <div class="controls">
                            <?php echo form_input('from_date', '', 'class="form-control datetime" id="from_date" required="required"'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <?php echo lang('till', 'to_date'); ?>
                        <div class="controls">
                            <?php echo form_input('to_date', '', 'class="form-control datetime" id="to_date" required="required"'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <?php echo lang('comment', 'comment'); ?>
                <div class="controls">
                    <?php echo form_textarea($comment); ?>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_notification', lang('add_notification'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>