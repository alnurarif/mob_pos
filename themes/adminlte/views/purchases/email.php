<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
    $(function () {
        $('.bcc').hide();
        $('#note').wysihtml5();
        $(".toggle_form").slideDown('hide');
        $('.toggle_form').on( "click", function () {
            $("#bcc").slideToggle();
            return false;
        });
    });
</script>

    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fas fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('email_purchase'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open("panel/purchases/email/" . $id, $attrib); ?>
        <div class="modal-body">
            <p><?php echo lang('enter_info'); ?></p>
            <div class="form-group">
                <?php echo lang("to", 'to'); ?>
                <input type="email" name="to" id="to" class="form-control" value="<?php echo escapeStr($supplier->email) ?>"
                       required="required"/>
            </div>
            <div id="bcc" style="display:none;">
                <div class="form-group">
                    <label for="cc"><?php echo lang('CC');?></label>
                    <input type="text" name="cc" id="cc" class="form-control" />
                </div>
                <div class="form-group">
                    <label for="bcc"><?php echo lang('BCC');?></label>
                    <input type="text" name="bcc" id="bcc" class="form-control" />
                </div>
            </div>
            <div class="form-group">
                <?php echo lang("subject", 'subject'); ?>
                <?php echo form_input($subject, '', 'class="form-control" id="subject" pattern=".{2,255}" required="required" '); ?>
            </div>
            <div class="form-group">
                <?php echo lang("message", 'note'); ?>
                <?php echo form_textarea($note, set_value('note'), 'class="form-control" id="note" '); ?>
            </div>

        </div>
        <div class="modal-footer">
            <a href="#"
               class="btn btn-sm btn-default pull-left toggle_form"><?php echo "Show CC/BCC"; ?></a>
            <?php echo form_submit('send_email','Send Email', 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
