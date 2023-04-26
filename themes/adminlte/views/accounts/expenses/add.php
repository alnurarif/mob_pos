<style type="text/css">
    .col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {
        padding: 0 5px;
    }

    .box-body {
        padding-bottom: 0;
    }
     .ui-spinner{
        width: 100%;
    }
</style>
<script type="text/javascript">
     jQuery(document).ready( function($) {
        $('#expense_to').select2();

        var spinner = $( "#recurF" ).spinner({
            spin: function( event, ui ) {
                $(this).val(ui.value).trigger('change');
            }
        });
    });
</script>
 <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><?php echo lang('Add Expense');?></h3>
        </div>
        <?php echo form_open_multipart('panel/expenses/add', 'autocomplete="off"'); ?>
        <div class="box-body">
            <input type="hidden" name="attachment_data" id="expense_attachment_data">
                <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4">
                    <div class="form-group">
                        <label for="expense_type_id" class=" control-label color_blue_bg"><?php echo lang('Expense Type');?></label>
                        <select name="type_id" id="expense_type_id" class="form-control" required>
                            <?php foreach ($expense_types as $type): ?>
                                <option value="<?php echo $type->id;?>"><?php echo escapeStr($type->name);?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 ">
                    <div class="form-group">
                        <label for="amount" class="color_blue_bg"><?php echo lang('Expense Amount');?></label>
                        <input class="form-control touchspin" placeholder="" required="required" name="amount" type="text" id="amount">
                    </div>
                </div>
                <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 ">
                    <div class="form-group">
                        <label for="to" class="color_blue_bg"><?php echo lang('Paid To');?></label>
                        <div class="input-group">
                            <select name="expense_to" id="expense_to" class="form-control" required>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?php echo $supplier->id;?>"><?php echo escapeStr($supplier->company);?></option>
                            <?php endforeach;?>
                            </select>
                            <a class="add_supplier btn input-group-addon">
                                <i class="fa fa-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 ">
                    <div class="form-group">
                        <label for="bank_account" class="color_blue_bg control-label"><?php echo lang('Bank Account');?></label>
                        <?php echo form_dropdown('bank_account', $bank_accounts, '','id="bank_account" class="form-control" required'); ?>
                    </div>
                </div>

                  <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 ">
                    <div class="form-group">
                        <label for="fund_type" class="color_blue_bg control-label"><?php echo lang('Fund Type');?></label>
                        <?php echo form_dropdown('fund_type', $all_funds, '','id="fund_type" class="form-control" required'); ?>
                    </div>
                </div>
               
                <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 ">
                    <div class="form-group">
                        <label for="date" class="color_blue_bg"><?php echo lang('Date');?></label>
                        <input class="form-control date" placeholder="" required="required" name="date" type="text" id="date" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4">
                    <div class="form-group">
                        <label for="notes" class="color_blue_bg"><?php echo lang('Description');?></label>
                        <input class="form-control" placeholder="" name="notes" type="text" id="notes">
                    </div>
                </div>
               
                <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 " style="display: none">
                    <div class="form-group">
                    <label for="recurring" class="active color_blue_bg"><?php echo lang('Is Expense Recurring?');?></label>
                        <input type="hidden" name="recurring" value="0">
                        <div class="switch switch-sm switch-success">
                            <input type="checkbox" class="toggle_on_off" value="1"  id="recurring" name="recurring" />
                        </div>
                    </div>
                </div>
                <div id="recur">
                <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 ">
                    <div class="form-group">
                        <div class="form-line">
                            <label for="recur_frequency" class="color_blue_bg"><?php echo lang('Recur Frequency');?></label>
                            <input class="form-control" id="recurF" name="recur_frequency" type="text" value="1">
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 ">
                    <div class="form-group">
                        <div class="form-line">
                            <label for="recur_type" class="active color_blue_bg"><?php echo lang('Recur Type');?></label>
                            <select class="form-control" id="recurT" name="recur_type"><option value="day"><?php echo lang('Day(s)');?></option><option value="week"><?php echo lang('Week(s)');?></option><option value="month" selected="selected"><?php echo lang('Month(s)');?></option><option value="year"><?php echo lang('Year(s)');?></option></select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 ">
                    <div class="form-group">
                        <div class="form-line">
                            <label for="recur_start_date" class="color_blue_bg"><?php echo lang('Recur Starts');?></label>
                            <input class="form-control date" id="recur_start_date" name="recur_start_date" type="text" value="<?php echo date($dateFormats['php_sdate']);?>" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 ">
                    <div class="form-group">
                        <div class="form-line">
                            <label for="recur_end_date" class="color_blue_bg"><?php echo lang('Recur Ends');?></label>
                            <input class="form-control date" id="recur_end_date" name="recur_end_date" type="text" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
            </div>
           
            

        <div class="box-footer" style="text-align: right;">
            <a href="<?php echo base_url();?>panel/expenses" class="btn-icon btn btn-default" type="button">
                <i class="fa fa-reply"></i> <?php echo lang("go_back") ?>
            </a>
            <button style="" id="expense_upload_modal_btn" class="btn-responsive btn-icon btn btn-info" data-mode="add"><i class="fa fa-cloud"></i> <?php echo lang('upload_file');?></button>

            <button type="submit" class="btn-icon btn btn-success btn-icon"><i class="fa fa-save"></i> <?php echo lang('Save');?></button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function (e) {
        var checked = ($('#recurring').is(':checked')) ? true : false;
        if (checked == '1') {
            $('#recur').show();
            $('#recurT').attr('required', 'required');
            $('#recur_start_date').attr('required', 'required');
            $('#recurF').attr('required', 'required');
        } else {
            $('#recur').hide();
            $('#recurT').removeAttr('required');
            $('#recur_start_date').removeAttr('required');
            $('#recurF').removeAttr('required');
        }

        $('body').delegate('#recurring', 'lcs-statuschange', function() {
            var checked = ($(this).is(':checked')) ? true : false;
            if (checked) {
                $('#recur').slideDown();
                $('#recurT').attr('required', 'required');
                $('#recurF').attr('required', 'required');
                $('#recur_start_date').attr('required', 'required');
            } else {
                $('#recur').slideUp();
                $('#recurT').removeAttr('required');
                $('#recur_start_date').removeAttr('required');
                $('#recurF').removeAttr('required');
            }
        });
            
        $('#expense_type_id').on("change", function () {
            id = $(this).val();
            a = $('#expense_type_id option[value='+id+']').data('supplier');
            $('#expense_to').val(a).trigger('change');
        });

        $('#recurring').on( "click", function () {
            if ($('#recurring').prop('checked')) {
                $('#recur').show();
                $('#recurT').attr('required', 'required');
                $('#recurF').attr('required', 'required');
                $('#recur_start_date').attr('required', 'required');
            } else {
                $('#recur').hide();
                $('#recurT').removeAttr('required');
                $('#recur_start_date').removeAttr('required');
                $('#recurF').removeAttr('required');
            }
        })
       
    })


    jQuery(document).on("click", "#expense_upload_modal_btn", function(e) {
        e.preventDefault();
        $("#expense_upload_manager").fileinput({
            uploadUrl: "<?php echo base_url();?>panel/expenses/upload_attachments",
            uploadAsync: false,
            language: 'mylang',
        }).on('filebatchuploadsuccess', function(event, data, previewId, index) {
            response = data.response;
            data = JSON.parse(response.data);
            var attachments = $('#expense_attachment_data').val();
            $('#expense_attachment_data').val((attachments != '' ? attachments + ',' : '')+ data.join(','))
        });
        jQuery('#expense_upload_modal').modal("show");
    });
</script>



<!-- ============= MODAL Upload Manager ============= -->
<div class="modal fade" id="expense_upload_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="expense_upload_modal_title"></h4>
            </div>
            <div class="modal-body">
                <label for="expense_upload_manager"><?php echo lang('Attachments');?></label>
                <div class="file-loading">
                    <input id="expense_upload_manager" name="upload_manager[]" type="file" multiple>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-icon btn btn-default" data-dismiss="modal"><i class="fa fa-reply img-circle text-muted"></i> <?php echo lang('Close');?></button>
            </div>
        </div>    
    </div>
</div>
