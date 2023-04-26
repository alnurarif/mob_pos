<!-- Main content -->
<section class="panel">
    <div class="panel-body">
        <form id="pin_form">
            <div class="form-group">
                <label><?php echo lang('Pin Code');?></label>
                <input type="text" placeholder="<?php echo lang('Enter your Pin Code');?>" class="form-control" name="pin_code" id="pin_code">
            </div>
            <button class="form-control" id="submit_pin"><?php echo lang('Submit');?></button>
        </form>
        <div id="sort_dp">
            
        </div>
    </div>
</section>

<?php echo form_open('panel/commission/report', array('id'=> 'timeclock_tt')); ?>
    <input type="hidden" name="pin_code" value="<?php echo $this->ion_auth->user()->row()->pin_code; ?>">
    <input type="hidden" name="sort_by" value="user">
    <input type="hidden" name="sort_with" id="single_user_id" value="<?php echo $this->ion_auth->user()->row()->id; ?>">
    <input type="hidden" name="show_form"  value="1">
<?php echo form_close(); ?>

<script type="text/javascript">
    jQuery(document).on("click", "#submit_pin", function () {
        event.preventDefault();
        var pin_code = $('#pin_code').val();
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/commission/getSortMenu",
            data: "pin_code=" + encodeURI(pin_code),
            cache: false,
            dataType: "json",
            success: function (data) {
                if (data.success) {
                    if (data.user_id) {
                        $('#single_user_id').val(data.user_id);
                        document.forms['timeclock_tt'].submit();
                    }else{
                        $('#sort_dp').html('');
                        $('#sort_dp').append(data.html);
                        $('#sort_by').select2();
                    }
                } else {
                    toastr.error("<?php echo lang('Incorrect Pin Code');?>");
                }
            }
        });
    });
   
    $(function () {
        $('.datetimepicker').datetimepicker({
            format:'MM-DD-YYYY HH:mm:ss',
        });
    });
</script>