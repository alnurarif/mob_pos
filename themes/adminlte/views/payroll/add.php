<style type="text/css">
    .borderOk {
        text-align: left;
    }

    .borderOk table {
        text-align: left;
    }

    .margin {
        margin: 6px;
    }

</style>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo lang('Add Payroll');?></h3>

        <div class="box-tools pull-right">

        </div>
    </div>
    <?php echo form_open_multipart('panel/payroll/add', 'autocomplete="off"'); ?>
    <input type="hidden" name="template_id" value="<?php echo $template->id; ?>">
    <input type="hidden" name="employee_name" id="employee_name" value="">

    <div class="box-body">
        <div class="row">

            <div class="col-lg-3 col-sm-6 col-xs-6 col-md-4">

                <div class="form-group">
                    <?php
                        $uarray = [];
                        $uarray[''] = lang('Select Employee');
                        foreach ($users as $user) {
                            $uarray[$user->id] = $user->first_name . ' ' . $user->last_name;
                        }
                    ?>
                    <label class="color_blue_bg"><?php echo lang('Staff');?></label>
                    <?php echo form_dropdown('user_id', $uarray,'' ,array('class' => 'form-control select2','id'=>'user_id','placeholder'=>lang('Select Employee'))); ?>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-xs-6 col-md-4">
                <label class="color_blue_bg"><?php echo lang('Payroll Date');?></label>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-sm-4" style="padding-right: 0;padding-left: 0;padding-top: 6px;" for="date"><?php echo lang('From');?>:</label>
                            <div class="col-sm-8 no-padding">
                                <?php echo form_input('from_date', '', array('class' => 'form-control date', 'required'=>"required")); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-sm-3" style="padding-right: 0;padding-left: 0;padding-top: 6px;" for="date"><?php echo lang('To');?>:</label>
                            <div class="col-sm-9 no-padding">
                                <?php echo form_input('to_date', '', array('class' => 'form-control date', 'required'=>"required")); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-xs-6 col-md-4">
                <div class="form-group">
                    <label class="color_blue_bg"><?php echo lang('Business Name');?></label>
                    <?php echo form_input('business_name', $this->mSettings->title, array('class' => 'form-control', 'id'=>"business_name",'required'=>"required"));?>
                </div>
            </div>


            <div class="col-lg-3 col-sm-6 col-xs-6 col-md-4" style="display: none"> 
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
                                <?php 
                                    $recur = array(
                                        'day' => lang('Day(s)'),
                                        'week' => lang('Week(s)'),
                                        'month' => lang('Month(s)'),
                                        'year' => lang('Year(s)'),
                                    );
                                    echo form_dropdown('recur_type', $recur, 'month', 'class="form-control" id="recurT"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 ">
                        <div class="form-group">
                            <div class="form-line">
                                <label for="recur_start_date" class="color_blue_bg"><?php echo lang('Recur Starts');?></label>
                                <input class="form-control date" id="recur_start_date" name="recur_start_date" type="text" value="<?php echo date($dateFormats['php_sdate']);?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6 col-xs-6 col-md-4 ">
                        <div class="form-group">
                            <div class="form-line">
                                <label for="recur_end_date" class="color_blue_bg"><?php echo lang('Recur Ends');?></label>
                                <input class="form-control date" id="recur_end_date" value="<?php echo date($dateFormats['php_sdate']);?>" name="recur_end_date" type="text">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <div class="borderOk row">

            <div class="col-md-12">
                <div class="col-lg-3 col-sm-6 col-md-3 col-xs-12 no-padding">
                <table width="100%" class="">
                    <tbody>
                    <tr>
                        <td style="vertical-align: top" width="50%" class="borderRight">

                            <table width="100%" id="hours_and_earnings">
                                <tbody>
                                <tr>
                                    <td width="50%" class="bg-navy"><b><?php echo lang('Description');?></b></td>
                                    <td width="50%" class="bg-navy"><b><?php echo lang('Amount');?></b></td>
                                </tr>
                                <?php
                                $count = 0;
                                ?>

                                <?php foreach($bottom_left as $key): ?>
                                    <tr>
                                        <td width="50%" class="cell_format"><span class="margin"><?php echo $key->name;?></span></td>
                                        <td width="50%" class="cell_format">
                                            <div class="margin text-bold">
                                                <?php echo form_input('template_metas['.$key->id.']', null, array('class' => 'form-control touchspin bottom_left', 'placeholder'=>"",'onkeyup'=>'refresh_totals()','id'=>'bottom_left'.$count)); ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php $count++; endforeach; ?>

                                </tbody>
                            </table>
                            <!--Hours and Earnings-->
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
            <div class="col-lg-3 col-sm-6 col-md-3 col-xs-12 no-padding">
                <table width="100%" class="">
                    <tbody>
                    <tr>
                        <td width="100%" valign="top">
                            <table width="100%" id="pre_tax_deductions">
                                <tbody>
                                <tr>
                                    <td width="50%" class="bg-navy"><b><?php echo lang('Description');?></b></td>
                                    <td width="50%" class="bg-navy"><b><?php echo lang('Amount');?></b></td>
                                </tr>
                                <?php
                                $count = 0;
                                ?>

                                <?php foreach($bottom_right as $key): ?>
                                    <tr>
                                        <td width="50%" class="cell_format"><span class="margin"><?php echo escapeStr($key->name);?></span></td>
                                        <td width="50%" class="cell_format">
                                            <div class="margin text-bold">
                                                <?php echo form_input('template_metas['.$key->id.']', null, array('class' => 'form-control touchspin bottom_right', 'placeholder'=>"",'onkeyup'=>'refresh_totals()','id'=>'bottom_right'.$count)); ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php $count++; endforeach; ?>

                                </tbody>
                            </table>
                            <!--Pre-Tax Deductions-->
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
            <div class="col-lg-3 col-sm-6 col-md-3 col-xs-12 no-padding">


                 <table width="100%" class="">
                    <tbody>
                    <tr>
                        <!-- <td width="10%" class="bg-navy"></td> -->
                        <td  class="bg-navy">
                            <b><?php echo lang('Net Pay Distribution');?></b>
                        </td>
                        <!-- <td width="10%" class="bg-navy"></td> -->
                    </tr>
                    <tr></tr>
                    <tr>
                        <!-- <td width="10%" ></td> -->
                        <td width="70%" class="bg-gray">
                            <table width="100%" id="gross_pay">
                                <tbody>
                                <tr>
                                    <td width="50%" class="cell_format">
                                        <div class="margin"><b><?php echo lang('Total Pay');?></b></div>
                                    </td>
                                    <td width="50%" class="cell_format">
                                        <div class="margin text-bold">
                                            <?php echo form_input('total_pay',null, array('class' => 'form-control', 'readonly'=>"",'id'=>'total_pay')); ?>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <!-- <td width="10%"></td> -->
                        <td  class="bg-gray">
                            <table width="100%" id="gross_pay">
                                <tbody>
                                <tr>
                                    <td width="50%" class="cell_format">
                                        <div class="margin"><b><?php echo lang('Total Deductions');?></b></div>
                                    </td>
                                    <td width="50%" class="cell_format">
                                        <div class="margin text-bold">
                                            <?php echo form_input('total_deductions',null, array('class' => 'form-control', 'readonly'=>"",'id'=>'total_deductions')); ?>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <!-- <td width="10%"></td> -->
                        <td  class="bg-gray">
                            <table width="100%" id="gross_pay">
                                <tbody>
                                <tr>
                                    <td width="50%" class="cell_format">
                                        <div class="margin"><b><?php echo lang('Net Pay');?></b></div>
                                    </td>
                                    <td width="50%" class="cell_format">
                                        <div class="margin text-bold">
                                            <?php echo form_input('net_pay',null, array('class' => 'form-control', 'readonly'=>"",'id'=>'net_pay')); ?>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div >
            <div class="col-lg-3 col-sm-6 col-md-3 col-xs-12 no-padding">
                 <table width="100%" class="" id="net_pay_distribution">
                    <tbody>
                   


                    <tr>
                        <td width="50%" class="cell_format">
                            <div class="margin">
                                <b><?php echo lang('Bank Name');?></b>
                            </div>
                        </td>
                    
                        <td width="50%" class="cell_format">
                            <div class="margin text-bold">
                                <?php echo form_input('bank_name',null, array('class' => 'form-control', 'id'=>"bank_name")); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" class="cell_format">
                            <div class="margin">
                                <b><?php echo lang('Account Number');?></b>
                            </div>
                        </td>
                   
                        <td width="50%" class="cell_format">
                            <div class="margin text-bold">
                                <?php echo form_input('account_number',null, array('class' => 'form-control', 'id'=>"bank_account_number")); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" class="cell_format">
                            <div class="margin">
                                <b><?php echo lang('Sort Code');?></b>
                            </div>
                        </td>
                    
                        <td width="50%" class="cell_format">
                            <div class="margin text-bold">
                                <?php echo form_input('sort_code',null, array('class' => 'form-control', 'id'=>"sort_code")); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" class="cell_format">
                            <div class="margin">
                                <b><?php echo lang('Paid Amount');?></b>
                            </div>
                        </td>
                    
                        <td width="50%" class="cell_format">
                            <div class="margin text-bold">
                                <?php echo form_input('paid_amount',null, array('class' => 'form-control', 'id'=>"paid_amount")); ?>

                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
                
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row">
             <div class="col-lg-4 col-sm-6 col-xs-12 col-md-4">
                <div class="form-group">
                    <label><?php echo lang('Comment');?></label>
                    <?php echo form_input('comments',null, array('class' => 'form-control', ''=>"")); ?>
                </div>
            </div>
        </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer" style="text-align: right;">
         <a href="<?php echo base_url();?>panel/payroll" class="btn-icon btn btn-goback" type="button"><i class="fa fa-reply img-circle text-muted"></i> <?php echo lang("go_back") ?></a>
        <button type="submit" class="btn-icon btn btn-primary btn-icon"><i class="fa fa-save img-circle text-primary"></i> <?php echo lang('Save');?></button>
    </div>
    <?php echo form_close(); ?>
</div>
    <!-- /.box -->
<script>
    $('#user_id').on("change", function (e) {
        $.ajax({
            type: 'GET',
            url: '<?php echo base_url('panel/payroll/getUser');?>/' + $('#user_id').val(),
            success: function (data) {
                $('#employee_name').val(data.name);
                $('#bank_name').val(data.bank_name);
                $('#bank_account_number').val(data.bank_account_number);
                $('#sort_code').val(data.sort_code);
            }
        });
    })
    $(document).ready(function (e) {

        $("input[name=from_date]").datetimepicker({
            format: site.dateFormats.js_ldate,
            fontAwesome: true,
            language: 'sma',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0
        }).datetimepicker('update', new Date());


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
        
        var spinner = $(".touchspin").spinner({
            spin: function( event, ui ) {
                $(this).val(ui.value).trigger('change');
                refresh_totals();

            }
        });

    })
    function refresh_totals(e) {
        var totalPay = 0;
        var totalDeductions = 0;
        var totalPaid = 0;
        var netPay = 0;
        for (var i = 0; i < '<?php echo count($bottom_left);?>'; i++) {
            var pay = document.getElementById("bottom_left" + i).value;
            if (pay == "")
                pay = 0;
            totalPay = parseFloat(totalPay) + parseFloat(pay);
        }
        for (var i = 0; i < '<?php echo count($bottom_right);?>'; i++) {
            var deduction = document.getElementById("bottom_right" + i).value;
            if (deduction == "")
                deduction = 0;
            totalDeductions = parseFloat(totalDeductions) + parseFloat(deduction);
        }

        document.getElementById("total_pay").value = totalPay;
        document.getElementById("total_deductions").value = totalDeductions;
        document.getElementById("net_pay").value = totalPay - totalDeductions;
        document.getElementById("paid_amount").value = totalPay - totalDeductions;
    }
</script>