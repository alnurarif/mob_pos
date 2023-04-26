<style>
    .borderOk {

        border-right: solid 1px #000000;
        border-left: solid 1px #000000;
        border-top: solid 1px #000000;
        border-bottom: solid 1px #000000;
    }

    table #hours_and_earnings td, table #tax_deductions td, table #pre_tax_deductions td, table #after_tax_deductions td, table #payslip_employee_header td, table #payslip_employer_header td, table #pay_period_and_salary td, table #summary td, table #net_pay_distribution td, table #messages td {
        padding: 2px;
    }

    .bg-navy {
        background-color: #001f3f;
        color: #fff;
    }

    .bg-gray {
        color: #000;
        background-color: #d2d6de;
    }

    .text-bold, .text-bold.table td, .text-bold.table th {
        font-weight: 700;
    }

    .margin {
        margin: 10px;
    }

    .text-center {
        text-align: center;
    }
</style>
<div style="width: 40%; float: left;">
    <img src="<?php echo base_url();?>assets/uploads/logos/<?php echo $settigns->logo;?>" height="200px">
</div>
<div style="width: 60%; float: left;">
    <h3 style="text-align: center;"><b><?php echo escapeStr($settings->title); ?></b></h3>
    <h3 style="text-align: center;"><b><?php echo lang('Payslip');?></b></h3>
</div>
<div style="clear: both"></div>
<table width="100%">
    <tbody>
    <tr style="margin: 20px">
        <td style="padding-bottom:10px;">
            <table width="100%" class="borderOk">
                <tbody>
                <tr>
                    <td style="vertical-align: top;" width="50%">

                        <table width="100%" id="payslip_employee_header">
                            <tbody>
                            <tr>
                                <td width="50%" class="cell_format">
                                    <div class="margin"><?php echo lang('Employee Name');?>
                                    </div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                        <?php echo escapeStr($payroll->employee_name); ?>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td width="50%" class="cell_format">
                                    <div class="margin"><?php echo lang('Address');?>
                                    </div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                        <?php echo escapeStr($puser->address); ?>
                                    </div>
                                </td>
                            </tr>

                            
                            
                            </tbody>
                        </table>
                    </td>

                    <td style="vertical-align: top" width="50%">

                        <table width="100%" id="pay_period_and_salary">

                            <tbody>
                            <tr>
                                <td width="50%" class="cell_format">
                                    <div class="margin"><b><?php echo lang('From Date');?></b></div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                        <?php echo $payroll->from_date; ?>
                                    </div>
                                </td>
                            </tr>
                             <tr>
                                <td width="50%" class="cell_format">
                                    <div class="margin"><b><?php echo lang('To Date');?></b></div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                        <?php echo $payroll->to_date; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td width="50%" class="cell_format">
                                    <div class="margin"><?php echo lang('Address');?></div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                        <?php echo escapeStr($settings->address); ?>
                                    </div>
                                </td>
                            </tr>

                             <tr>
                                <td width="50%" class="cell_format">
                                    <div class="margin"><?php echo lang('Telephone');?></div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                        <?php echo escapeStr($settings->phone); ?>
                                    </div>
                                </td>
                            </tr>

                           
                            </tbody>
                        </table>
                        <!--Pay Period and Salary-->
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr style="height: 20px">
        <td></td>
    </tr>
    <tr>
        <td>
            <table width="100%" class="borderOk">
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
                                    <td width="50%" class="cell_format">
                                        <div class="margin">
                                            <?php echo escapeStr($key->name); ?>
                                        </div>
                                    </td>
                                    <td width="50%" class="cell_format">
                                        <div class="margin text-bold">
                                            <?php echo escapeStr($key->value); ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php $count++; endforeach;?>

                            </tbody>
                        </table>
                        <!--Hours and Earnings-->
                    </td>

                    <td width="50%" valign="top">
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
                                    <td width="50%" class="cell_format">
                                        <div class="margin">
                                            <?php echo escapeStr($key->name); ?>
                                        </div>
                                    </td>
                                    <td width="50%" class="cell_format">
                                        <div class="margin text-bold">
                                            <?php echo escapeStr($key->value); ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php $count++; endforeach;?>
                            </tbody>
                        </table>
                        <!--Pre-Tax Deductions-->
                    </td>
                </tr>
                <tr>
                    <td width="50%" class="bg-gray">
                        <table width="100%" id="gross_pay">
                            <tbody>
                            <tr>
                                <td width="50%" class="cell_format">
                                    <div class="margin"><b><?php echo lang('Total Pay');?></b></div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                        <?php echo $total = $this->payroll_model->single_payroll_total_pay($payroll->id);?>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </td>
                    <td width="50%" class="bg-gray">

                        <table width="100%" id="gross_pay">
                            <tbody>
                            <tr>
                                <td width="50%" class="cell_format">
                                    <div class="margin"><b><?php echo lang('Total Reduction');?></b></div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                        <?php echo $deductions =  $this->payroll_model->single_payroll_total_deductions($payroll->id);?>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        <br>
                    </td>
                    <td width="50%" class="bg-gray">
                        <table width="100%" id="gross_pay">
                            <tbody>
                            <tr>
                                <td width="50%" class="cell_format">
                                    <div class="margin"><b><?php echo lang('Net Pay');?></b></div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                        <?php echo $total - $deductions;?>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr style="height: 20px">
        <td></td>
    </tr>

    <tr>
        <td style="padding-top:10px;">
            <table width="100%" class="borderOk" id="net_pay_distribution">
                <tbody>
                <tr>
                    <td colspan="5" class="bg-navy">
                        <b><?php echo lang('Net Pay Distrubution');?></b>
                    </td>
                </tr>
                <tr>
                    <td width="20%" class="cell_format">
                        <div class="margin">
                            <b><?php echo lang('Payment Method');?></b>
                        </div>
                    </td>
                    <td width="20%" class="cell_format">
                        <div class="margin">
                            <b><?php echo lang('Bank Name');?></b>
                        </div>
                    </td>
                    <td width="20%" class="cell_format">
                        <div class="margin">
                            <b><?php echo lang('Account Number');?></b>
                        </div>
                    </td>
                    <td width="20%" class="cell_format">
                        <div class="margin">
                            <b><?php echo lang('Description');?></b>
                        </div>
                    </td>
                    <td width="20%" class="cell_format">
                        <div class="margin">
                            <b><?php echo lang('Paid Amount');?></b>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="20%" class="cell_format">
                        <div class="margin text-bold">
                            <?php echo escapeStr($payroll->payment_method);?>
                        </div>
                    </td>
                    <td width="20%" class="cell_format">
                        <div class="margin text-bold">
                            <?php echo escapeStr($payroll->bank_name);?>
                        </div>
                    </td>
                    <td width="20%" class="cell_format">
                        <div class="margin text-bold">
                            <?php echo escapeStr($payroll->account_number);?>
                        </div>
                    </td>
                    <td width="20%" class="cell_format">
                        <div class="margin text-bold">
                            <?php echo escapeStr($payroll->sort_code);?>
                        </div>
                    </td>
                    <td width="20%" class="cell_format">
                        <div class="margin text-bold">
                            <?php echo escapeStr($payroll->paid_amount);?>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <!--Net Pay Distribution-->
        </td>
    </tr>
    <?php if(!empty($payroll->comments)): ?>
        <tr style="height: 20px">
            <td></td>
        </tr>
        <tr>
            <td>
                <table width="100%" class="borderOk" style="margin-top:10px;padding: 10px" id="messages">
                    <tbody>
                    <tr>
                        <td width="100%" class="cell_format">
                            <div class="margin"><b><?php echo lang('Comment');?></b></div>
                        </td>
                    </tr>
                    <tr>
                        <td width="100%" class="cell_format">
                            <div class="margin text-bold">
                                <?php echo escapeStr($payroll->comments) ;?>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>