
<a class="btn-icon btn btn-primary btn-icon" href="<?php echo base_url();?>panel/payroll/add"><i class="fa fa-plus-circle"></i> <?php echo lang('Add Payroll');?></a>

<div class="box box-primary">
        <div class="box-header">
        <h3 class="box-title"><?php echo lang('payroll/index');?></h3>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table id="view-repayments" class="table table-bordered table-striped" style="table-layout: fixed;">
                    <thead>
                        <tr role="row">
                            <th width="160px"><?php echo lang('Name');?></th>
                            <th width="110px"><?php echo lang('Pay Date - From');?></th>
                            <th width="110px"><?php echo lang('Pay Date - To');?></th>
                            <th width="90px"><?php echo lang('Gross');?></th>
                            <th width="120px"><?php echo lang('Total Deductions');?></th>
                            <th width="90px"><?php echo lang('Paid');?></th>
                            <th width="170px"><?php echo lang('Payslip');?></th>
                            <th width="145px"><?php echo lang('Options');?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $key): ?>
                        <tr>

                            <?php 
                                $p = $this->payroll_model->getPayrollByUserId($key->id);
                            ?>
                            

                            <td><?php echo escapeStr($key->first_name);?> <?php echo escapeStr($key->last_name);?></td>
                            
                            <?php if($p): ?>
                                
                                <td>
                                    <?php echo $this->repairer->hrsd($p->from_date);?> 
                                </td>
                                <td>
                                    <?php echo $this->repairer->hrsd($p->to_date);?>
                                </td>
                                <td>
                                    <?php echo $this->payroll_model->single_payroll_total_pay($p->id); ?>
                                </td>
                                <td>
                                    <?php echo $this->payroll_model->single_payroll_total_deductions($p->id); ?>
                                </td>
                                <td>
                                    <?php echo $p->paid_amount; ?>
                                </td>
                                
                                
                            <?php else: ?>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            <?php endif; ?>

                            <?php if($p): ?>
                              
                                <td>
                                    <div class="btn-group-horizontal">
                                        <a type="button" class="btn btn-success btn-icon "
                                           href="<?php echo base_url();?>panel/payroll/payslip/<?php echo $p->id;?>"
                                           target="_blank">
                                            <i class="fa fa-print"></i> <?php echo lang('Download');?></a>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group-horizontal">
                                        <a type="button" class="btn btn-primary btn-icon "  href="<?php echo base_url();?>panel/payroll/view/<?php echo $key->id;?>">
                                            <i class="fas fa-list-alt"></i> <?php echo lang('View All');?></a>
                                    </div>
                                </td>

                            <?php else: ?>
                                <td></td>
                                <td>    
                                    <div class="btn-group-horizontal">
                                         <a type="button" class="btn btn-primary btn-icon "  href="<?php echo base_url();?>panel/payroll/view/<?php echo $key->id;?>">
                                                <i class="fas fa-list-alt"></i> <?php echo lang('View All');?></a>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->