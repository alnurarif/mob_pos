<style type="text/css">
    
    .box-body {
        padding-bottom: 0;
    } .ui-spinner{
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
            <h3 class="box-title"><?php echo lang('Edit Expense');?></h3>
        </div>
        <?php echo form_open_multipart('panel/expenses/edit/'.$expense->id, 'autocomplete="off"'); ?>
        <div class="box-body color_blue">
             <div class="row">
                <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 ">
                    <div class="form-group">
                        <label for="expense_type_id" class="color_blue_bg control-label"><?php echo lang('Expense Type');?></label>
                         <select name="type_id" id="expense_type_id" class="form-control" required>
                        <?php foreach ($expense_types as $type): ?>
                            <option <?php echo $type->id ==  $expense->type_id ? 'selected' : '' ;?>  value="<?php echo $type->id;?>"><?php echo escapeStr($type->name);?></option>
                        <?php endforeach;?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 ">
                    <div class="form-group">
                        <label for="amount" class="color_blue_bg"><?php echo lang('Expense Amount');?></label>
                        <input class="form-control touchspin" placeholder="" value="<?php echo escapeStr($expense->amount);?>" required="required" name="amount" type="text" id="amount">
                    </div>
                </div>
                <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 ">
                    <div class="form-group">
                        <label for="to" class="color_blue_bg"><?php echo lang('Paid To');?></label>
                        <div class="input-group">
                            <select name="expense_to" id="expense_to" class="form-control" required>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option  <?php echo $supplier->id ==  $expense->to_from_id ? 'selected' : '' ;?> value="<?php echo $supplier->id;?>"><?php echo escapeStr($supplier->company);?></option>
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
                        <label for="date" class="color_blue_bg">Date</label>
                        <input class="form-control date" placeholder=""  value="<?php echo date($dateFormats['php_sdate'], strtotime($expense->date));?>" required="required" name="date" type="text" id="date">
                    </div>
                </div>
                <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 ">
                    <div class="form-group">
                        <label for="notes" class="color_blue_bg"><?php echo lang('Description');?></label>
                        <input class="form-control" placeholder="" value="<?php echo escapeStr($expense->notes);?>" name="notes" type="text" id="notes">
                    </div>
                </div>
                

                <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 " style="display: none">
                     <div class="form-group">
                        <label for="recurring" class="active color_blue_bg"><?php echo lang('Is Expense Recurring?');?></label>
                        <input type="hidden" name="recurring" value="0">
                        <div class="switch switch-sm switch-success">
                            <input type="checkbox" class="toggle_on_off" <?php echo $expense->recurring?'checked':'';?> value="1"  id="recurring" name="recurring" />
                        </div>
                    </div>
                </div>
            </div>


            <div id="recur">
                <div class="row">
                    <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 ">
                        <div class="form-group">
                            <div class="form-line">
                                <label for="recur_frequency" class="color_blue_bg"><?php echo lang('Recur Frequency');?></label>
                                <input class="form-control" id="recurF" value="<?php echo escapeStr($expense->recur_frequency);?>" name="recur_frequency" type="text" value="1">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 ">
                        <div class="form-group">
                            <div class="form-line">
                                <label for="recur_type" class="active color_blue_bg"><?php echo lang('Recur Type');?></label>
                                <?php 
                                    $recur = array(
                                        'day' => lang('Day(s)'),
                                        'week' => lang('Week(s)'),
                                        'month' => lang('Month(s)'),
                                        'year' => lang('Year(s)'),
                                    );
                                    echo form_dropdown('recur_type', $recur, escapeStr($expense->recur_type), 'class="form-control" id="recurT"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 ">
                        <div class="form-group">
                            <div class="form-line">
                                <label for="recur_start_date" class="color_blue_bg"><?php echo lang('Recur Starts');?></label>
                                <input class="form-control date" id="recur_start_date" name="recur_start_date" type="text" value="<?php echo date($dateFormats['php_sdate'], strtotime($expense->recur_start_date));?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 ">
                        <div class="form-group">
                            <div class="form-line">
                                <label for="recur_end_date" class="color_blue_bg"><?php echo lang('Recur Ends');?></label>
                                <input class="form-control date" id="recur_end_date" value="<?php echo date($dateFormats['php_sdate'], strtotime($expense->recur_end_date));?>" name="recur_end_date" type="text">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box-footer" style="text-align: right;">


            <a href="<?php echo base_url();?>panel/expenses" class="btn-icon btn btn-default " type="button"><i class="fa fa-reply"></i> <?php echo lang("go_back") ?></a>
            <button style="" id="expense_upload_modal_btn" class="btn-responsive btn-icon btn btn-info" data-mode="edit" data-num="<?php echo $expense->id;?>"><i class="fa fa-cloud"></i> <?php echo lang('upload_file');?></button>
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

        jQuery(document).on("click", "#expense_upload_modal_btn", function(e) {
            e.preventDefault();
            mode = $(this).attr('data-mode');
            num = $(this).attr('data-num');
            $.ajax({
                type: 'POST',
                url: "<?php echo base_url();?>panel/expenses/getAttachments",
                dataType: "json",
                data:({"id":num}),
                success: function (data) {
                    $('#expense_upload_manager').fileinput('destroy');
                    $("#expense_upload_manager").fileinput({
                        initialPreviewAsData: true, 
                        initialPreview: data.urls,
                        initialPreviewConfig: data.previews,
                        deleteUrl: "<?php echo base_url();?>panel/expenses/delete_attachment",
                        maxFileSize: 999999,
                        uploadExtraData: {id:num},
                        uploadUrl: "<?php echo base_url();?>panel/expenses/upload_attachments",
                        uploadAsync: false,
                        overwriteInitial: false,
                        showPreview: true,
                        language: 'mylang',
                    });
                }
            });
            jQuery('#expense_upload_modal').modal("show");
        });
    })
    
    $('#expense_type_id').on("change", function () {
        id = $(this).val();
        a = $('#expense_type_id option[value='+id+']').data('supplier');
        $('#expense_to').val(a).trigger('change');
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
