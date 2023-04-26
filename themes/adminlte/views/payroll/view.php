
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo lang('payroll/index');?></h3>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table id="view-repayments" class="table table-bordered table-striped " style="table-layout: fixed">
                <thead>
                    <tr role="row">
                        <th width="130px"><?php echo lang('Pay Date - From');?></th>
                        <th width="120px"><?php echo lang('Pay Date - To');?></th>
                        <th width="90px"><?php echo lang('Gross');?></th>
                        <th width="120px"><?php echo lang('Total Deductions');?></th>
                        <th width="90px"><?php echo lang('Net');?></th>
                        <th width="90px"><?php echo lang('Paid');?></th>
                        <th width="120px"><?php echo lang('Recurring');?></th>
                        <th width="190px"><?php echo lang('Payslip');?></th>
                        <th width="90px"><?php echo lang('Options');?></th>

                    </tr>
                </thead>
                <tbody>
                <?php foreach($payrolls as $key): ?>
                    <tr>
                     
                        <td>
                                    <?php echo $this->repairer->hrsd($key->from_date);?> 
                        </td>
                        <td>
                                    <?php echo $this->repairer->hrsd($key->to_date);?> 
                        </td>
                        <td>
                            <?php echo $total_pay = $this->payroll_model->single_payroll_total_pay($key->id); ?>
                        </td>
                        <td>
                            <?php echo $total_deductions = $this->payroll_model->single_payroll_total_deductions($key->id); ?>
                        </td>
                        <td>
                            <?php echo $total_pay -$total_deductions ?>
                        </td>
                        <td>
                            <?php echo $key->paid_amount;?>
                        </td>
                        <td>
                            <?php if($key->recurring==1):?>
                                Yes
                            <?php else: ?>
                                No
                            <?php endif; ?>
                        </td>
 
                        <td>
                            <div class="btn-group-horizontal">
                                <a type="button" class="btn btn-success btn-icon"
                                    href="<?php echo base_url();?>panel/payroll/payslip/<?php echo $key->id;?>"
                                   target="_blank">
                                    <i class="fa fa-print"></i> <?php echo lang('Download Wage Slip');?></a>
                            </div>
                        </td>
                        
                           <td>
                            <div class="text-center">
                                <div class="btn-group text-left">
                                    <button type="button" class="btn-round btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Options <span class="caret"></span></button>
                                    <ul class="dropdown-menu pull-left" role="menu">
                                        <?php if($this->Admin || $GP['payroll-view']): ?>
                                            <li>
                                                <a data-dismiss='modal' class='view_payroll' href='#view_payroll' data-toggle='modal' data-num='<?php echo $key->id;?>'>
                                                    <i class="fas fa-check"></i> <?php echo lang('View');?>
                                                </a>
                                            </li>
                                        <?php endif;?>
                                        <?php if($this->Admin || $GP['payroll-edit']): ?>
                                        <li>
                                            <a href="<?php echo base_url();?>panel/payroll/edit/<?php echo $key->id;?>">
                                                <i class="fas fa-edit"></i> <?php echo lang('Edit');?>
                                            </a>
                                        </li>
                                        <?php endif;?>
                                        <?php if($this->Admin || $GP['payroll-payslip']): ?>
                                        <li>
                                            <a target="_blank" href="<?php echo base_url();?>panel/payroll/payslip/<?php echo $key->id;?>/1">
                                                <i class="fas fa-print"></i> <?php echo lang('Print');?>
                                            </a>
                                        </li>
                                        <?php endif;?>
                                        <?php if($this->Admin || $GP['payroll-edit']): ?>
                                        <li>
                                            <a data-dismiss='modal' class='set_recurring' href='#set_recurring' data-toggle='modal' data-num='<?php echo $key->id;?>'>
                                                <i class="fas fa-undo"></i> <?php echo lang('Recurring');?>
                                            </a>
                                        </li>
                                        <?php endif;?>
                                        <?php if($this->Admin || $GP['payroll-delete']): ?>
                                        <li>
                                            <a href="<?php echo base_url();?>panel/payroll/delete/<?php echo $key->id;?>">
                                                <i class="fas fa-trash"></i> <?php echo lang('Delete');?>
                                            </a>
                                        </li>
                                        <?php endif;?>
                                    </ul>
                                </div>
                            </div>
                        </td>

                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->



<!-- ============= MODAL VISUALIZZA ORDINI/RIPARAZIONI ============= -->
<div class="col-md-12 modal fade" id="view_payroll" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-ku">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><span id="titlePR"></span></h4>
            </div>
            <div class="modal-body">
                    <div class="row view_reparation_row">
                        <div class="col-lg-3 col-sm-6 col-xs-6 col-md-4 bio-row">
                            <p><span class="bold"><i class="fa img-circle text-primary fa-user"></i> <?php echo lang('Employee Name');?> </span><span id="pr_emp"></span></p>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-xs-6 col-md-4 bio-row">
                            <p><span class="bold"><i class="fa img-circle text-primary fa-calendar"></i> <?php echo lang('Payroll Date');?> </span><span id="pr_date"></span></p>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-xs-6 col-md-4 bio-row">
                            <p><span class="bold"><i class="fa img-circle text-primary fa-building"></i> <?php echo lang('Business Name');?> </span><span id="pr_company"></span></p>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-xs-6 col-md-4 bio-row">
                            <p><span class="bold"><i class="fa img-circle text-primary fa-undo"></i> <?php echo lang('Recurring');?> </span><span id="pr_recur"></span></p>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-xs-6 col-md-4 bio-row">
                            <p><span class="bold"><i class="fa img-circle text-primary fa-money-bill-alt"></i> <?php echo lang('Payment Method');?> </span><span id="pr_payment_method"></span></p>
                        </div>

                         <div class="col-lg-3 col-sm-6 col-xs-6 col-md-4 bio-row">
                            <p><span class="bold"><i class="fa img-circle text-primary fa-university"></i> <?php echo lang('Bank Name');?> </span><span id="pr_payment_bank"></span></p>
                        </div>

                         <div class="col-lg-3 col-sm-6 col-xs-6 col-md-4 bio-row">
                            <p><span class="bold"><i class="fa img-circle text-primary fa-university"></i> <?php echo lang('Account Number');?> </span><span id="pr_payment_accnumber"></span></p>
                        </div>


                         <div class="col-lg-3 col-sm-6 col-xs-6 col-md-4 bio-row">
                            <p><span class="bold"><i class="fa img-circle text-primary fa-sticky-note"></i> <?php echo lang('Description');?> </span><span id="pr_payment_desc"></span></p>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-xs-6 col-md-4 bio-row">
                            <p><span class="bold"><i class="fa img-circle text-primary fa-money-bill-alt"></i> <?php echo lang('Net Pay');?>  </span><span id="total_pay"></span></p>
                        </div>

                       
                        
                    </div>
                    <div class="row">
                        <div class="col-md-6 control-group table-group">
                            <label class="table-label" for="combo"><?php echo lang('Additions');?></label>

                            <div class="controls table-controls">
                                <table id="additions_table" class="table items table-striped table-bordered table-condensed table-hover">
                                    <thead>
                                    <tr>
                                        <th class="col-md-5 col-sm-5 col-xs-5"><?php echo lang('Description');?></th>
                                        <th class="col-md-2 col-sm-2 col-xs-2"><?php echo lang('Amount');?></th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <th><?php echo lang('Total');?></th>
                                        <th id="total_additions"></th>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6 control-group table-group">
                            <div class="controls table-controls">
                            <label class="table-label" for="combo"><?php echo lang('Deductions');?></label>

                                <table id="deductions_table" class="table items table-striped table-bordered table-condensed table-hover">
                                    <thead>
                                    <tr>
                                        <th class="col-md-5 col-sm-5 col-xs-5"><?php echo lang('Description');?></th>
                                        <th class="col-md-2 col-sm-2 col-xs-2"><?php echo lang('Amount');?></th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                     <tfoot>
                                        <th><?php echo lang('Total');?></th>
                                        <th id="total_deductions"></th>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        
                    </div>
                   
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12" id="footerPR" ></div>
                </div>
            </div>
        </div>
    </div>
</div>





<div class="col-md-12 modal fade" id="set_recurring" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-ku">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><span id="titlePRR"></span></h4>
            </div>
            <form id="set_pr_recurring_form">
            <div class="modal-body">
                 <div class="row">
                    <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4"> 
                        <div class="form-group">
                        <label for="recurring" class="active color_blue_bg"><?php echo lang('Recurring?');?></label>
                            <input type="hidden" name="recurring" value="0">
                            <div class="switch switch-sm switch-success">
                                <input type="checkbox" class="toggle_on_off" value="1"  id="recurring" name="recurring" />
                            </div>
                        </div>
                    </div>
                </div>
                <div id="recur">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 col-xs-6 col-md-4 ">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="recur_frequency" class="color_blue_bg"><?php echo lang('Recur Frequency');?></label>
                                    <input class="form-control" id="recurF" value="" name="recur_frequency" type="text" value="1">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-xs-6 col-md-4 ">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="recur_type" class="active color_blue_bg"><?php echo lang('Recur Type');?></label>
                                    <?php 
                                        $recur = array(
                                            'day' => 'Day(s)',
                                            'week' => 'Week(s)',
                                            'month' => 'Month(s)',
                                            'year' => 'Year(s)',
                                        );
                                        echo form_dropdown('recur_type', $recur, '', 'class="form-control" id="recurT" style="width: 100%"');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-xs-6 col-md-4 ">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="recur_start_date" class="color_blue_bg"><?php echo lang('Recur Starts');?></label>
                                    <input class="form-control date" id="recur_start_date" name="recur_start_date" type="text" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-xs-6 col-md-4 ">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="recur_end_date" class="color_blue_bg"><?php echo lang('Recur Ends');?></label>
                                    <input class="form-control date" id="recur_end_date" value="" name="recur_end_date" type="text">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="payroll_submit" class="btn-icon btn btn-success btn-icon"><i class="fa fa-save img-circle text-success"></i> <?php echo lang('Save');?></button>
            </div>
            </form>
        </div>
    </div>
</div>


<style type="text/css">
    .table tfoot {
    background: #4082c1;
    color: white;
    font-size: 13px;
}

.table {
    margin-bottom: 0;
}
</style>
<script type="text/javascript">
$(document).ready(function(e) {

     $('body').delegate('#recurring', 'lcs-statuschange', function() {
            var checked = ($(this).is(':checked')) ? true : false;
            if (checked) {
                $('#recur').slideDown();
                $('#recurT').attr('required', 'required');
                $('#recurF').attr('required', 'required');
                $('#recur_start_date').attr('required', 'required');
                $('#recurring').attr('checked', true);
            } else {
                $('#recur').slideUp();
                $('#recurT').removeAttr('required');
                $('#recur_start_date').removeAttr('required');
                $('#recurF').removeAttr('required');
                $('#recurring').attr('checked', false);
            }
        });

    jQuery(document).on("click", ".view_payroll", function () {
        var num = jQuery(this).data("num");
        find_payroll(num);
    });
});

jQuery(document).on("click", ".set_recurring", function () {
    $('#set_pr_recurring_form')[0].reset();
    $('#set_pr_recurring_form').parsley().reset();
    $('#set_pr_recurring_form').find("select").val("").trigger('change');
    var num = $(this).data('num');
    jQuery('#titlePRR').html("Set Recurring");
    jQuery.ajax({
        type: "POST",
        url: base_url + "panel/payroll/getPayrollByID",
        data: "id=" + encodeURI(num),
        cache: false,
        dataType: "json",
        success: function (data) {
            $('#recur').hide();
            $('#recurring').removeAttr('checked').trigger('change');
            $('#recurring').lcs_off();
            $('#payroll_submit').attr('data-num', data.payroll.id);
            if(parseInt(data.payroll.recurring) == 1){
                $('#recurring').lcs_on();
                $('#recurring').attr('checked', true).trigger('change');
                $('#recurF').val(data.payroll.recur_frequency);
                $('#recurT').val(data.payroll.recur_type).trigger('change');
                $('#recur_start_date').val(data.payroll.recur_start_date);
                $('#recur_end_date').val(data.payroll.recur_end_date);
                $('#recur').show();
            }
            
        }
    });
});



    $(function () {
        $('#set_pr_recurring_form').parsley({
        successClass: "has-success",
        errorClass: "has-error",
        classHandler: function (el) {
            return el.$element.closest(".form-group");
        },
        errorsContainer: function (el) {
            return el.$element.closest(".form-group");
        },
        errorsWrapper: "<span class='help-block'></span>",
        errorTemplate: "<span></span>"
        }).on('form:submit', function(event) {
            var id = jQuery('#payroll_submit').data("num");
            url = base_url + "panel/payroll/set_recurring/"+ id;
            var dataString = new FormData($('#set_pr_recurring_form')[0]);

            $.ajax({
                url: url,
                type: "POST",
                data:  dataString,
                contentType:false,
                cache: false,
                processData:false,
                success: function (result) {
                    toastr['success'](result.msg);
                    setTimeout(function () {
                        window.location.reload();
                    }, 500);
                }
            });
            return false;
        });
    });
function find_payroll(num) {
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/payroll/getPayrollByID",
            data: "id=" + num,
            cache: false,
            dataType: "json",
            success: function(data) {
                if (data.payroll) {
                    jQuery('#titlePR').html("<?php echo lang('payroll');?>: " + " " + data.payroll.employee_name + " <span>");
                    jQuery('#pr_emp').html(data.payroll.employee_name);
                    jQuery('#pr_recur').html(data.payroll.recurring ? "<?php echo lang('yes');?>" : "<?php echo lang('no');?>");
                    jQuery('#pr_date').html(data.payroll.from_date);
                    jQuery('#pr_company').html(data.payroll.business_name);
                    jQuery('#pr_payment_method').html(data.payroll.payment_method);
                    jQuery('#pr_payment_bank').html(data.payroll.bank_name);
                    jQuery('#pr_payment_accnumber').html(data.payroll.account_number);
                    jQuery('#pr_payment_desc').html(data.payroll.comments);


                    $("#additions_table tbody").empty();
                    var additions = 0;

                    // Table of Items
                    $.each(data.bottom_left, function() { 
                        var newTr = $('<tr></tr>');
                        tr_html = '<td>' + this.name + '</td>';
                        tr_html += '<td>'+this.value+'</td>';
                        newTr.html(tr_html);
                        newTr.prependTo("#additions_table");
                        additions += parseFloat(this.value);
                    });


                    $("#deductions_table tbody").empty();
                    var deductions = 0;

                    // Table of Items
                    $.each(data.bottom_right, function() { 
                        var newTr = $('<tr></tr>');
                        tr_html = '<td>' + this.name + '</td>';
                        tr_html += '<td>'+this.value+'</td>';
                        newTr.html(tr_html);
                        newTr.prependTo("#deductions_table");
                        deductions += parseFloat(this.value);
                    });


                    $('#total_additions').html(formatMoney(additions));
                    $('#total_deductions').html(formatMoney(deductions));
                    jQuery('#total_pay').html(formatMoney(additions - deductions));

                    string = "<div class=\"btn-row\" style=\"text-align: right;\">";
                        string += "<button data-dismiss=\"modal\" class=\"btn-icon btn btn-goback\" type=\"button\"><i class=\"fa fa-reply img-circle text-muted\"></i> <?php echo lang('go_back');?></button>";
                    string += "</div>";
                    $('#footerPR').html(string);

                }else{
                    bootbox.alert("<?php echo lang('not found');?>");
                }
            }
        });
    }

</script>