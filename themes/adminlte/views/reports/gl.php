<?php
$v = "";
if ($this->input->post('date_range')) {
    $dr = json_decode($this->input->post('date_range'));
    $v .= "&start_date=" . $dr->start;
    $v .= "&end_date=" . $dr->end;
}
?>
<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('reports/gl');?></h3>
      <div class="box-tools pull-right">
      </div>
  </div>
  <div class="box-body">
    <?php echo form_open("panel/reports/gl"); ?>
                    <div class="form-group">
                        <label><?php echo lang('Date Range');?></label>
                        <?php echo form_input('date_range_o', (isset($_POST['date_range_o']) ? htmlspecialchars($_POST['date_range_o']) : ""), 'class="form-control derp" id="daterange"'); ?>
                        <input type="hidden" name="date_range" class="date_range" id="date_range" value='<?php echo (isset($_POST['date_range']) ? htmlspecialchars($_POST['date_range']) : "");?>'>
                    </div>

                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                    

                     <a class="btn btn-default btn-xs pull-right"  href="<?php echo base_url(); ?>panel/reports/gl/pdf?<?php echo $v; ?>"><?php echo lang('export_to_pdf');?></a>
                    <a class="btn btn-default btn-xs pull-right"  href="<?php echo base_url(); ?>panel/reports/gl/xls?<?php echo $v; ?>"><?php echo lang('export_to_excel');?></a>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td colspan="2" style="text-align: center;"><?php echo lang('General Ledger Report');?></td>
                    </tr>
                </thead>
                <tr>
                    <td><?php echo lang('Inventory Received From Vendors');?></td>
                    <td><?php echo $vpt ? number_format($vpt, 2) : number_format(0, 2); ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Purchases Returned');?></td>
                    <td><?php echo $pr ? number_format($pr, 2) : number_format(0, 2); ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Vendor Orders Placed');?></td>
                    <td><?php echo $vop ? number_format($vop, 2) : number_format(0, 2); ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Shipping Cost');?></td>
                    <td><?php echo $vs ? number_format($vs, 2) : number_format(0, 2); ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Purchases Made From Customers');?></td>
                    <td><?php echo $cs ? number_format($cs, 2) : number_format(0, 2); ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Repair Deposits Received');?></td>
                    <td><?php echo $rd ? number_format($rd, 2) : number_format(0, 2); ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Repairs Closed Out');?></td>
                    <td><?php echo $rc ? number_format($rc, 2) : number_format(0, 2); ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Used Phones Sales');?></td>
                    <td><?php echo $ups ? number_format($ups, 2) : number_format(0, 2); ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('New Phones Sales');?></td>
                    <td><?php echo $nps ? number_format($nps, 2) : number_format(0, 2); ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Accessories Sales');?></td>
                    <td><?php echo $as ? number_format($as, 2) : number_format(0, 2); ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Other Sales');?></td>
                    <td><?php echo $os ? number_format($os, 2) : number_format(0, 2); ?></td>
                </tr>

                <tr>
                    <td><?php echo lang('Cellular Plan Sales');?></td>
                    <td><?php echo $pt ? number_format($pt, 2) : number_format(0, 2); ?></td>
                </tr>

                <tr>
                    <td><?php echo lang('Total Tax Collected');?></td>
                    <td><?php echo $tc ? number_format($tc, 2) : number_format(0, 2); ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Refunds Issued');?></td>
                    <td><?php echo $ri ? number_format($ri, 2) : number_format(0, 2); ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Refund Surcharges (Restocking Fees)');?></td>
                    <td><?php echo $rs ? number_format($rs, 2) : number_format(0, 2); ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Inventory Out');?></td>
                    <td><?php echo $io ? number_format($io, 2) : number_format(0, 2); ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Regular transfers');?></td>
                    <td><?php echo $rt ? number_format($rt, 2) : number_format(0, 2); ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('transfers through refunds');?></td>
                    <td><?php echo $ttf ? number_format($ttf, 2) : number_format(0, 2); ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Transfers Received');?></td>
                    <td><?php echo $ttr ? number_format($ttr, 2) : number_format(0, 2); ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Pending Transfers');?></td>
                    <td><?php echo $tpt ? number_format($tpt, 2) : number_format(0, 2); ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Expenses');?></td>
                    <td><?php echo $expenses ? number_format($expenses, 2) : number_format(0, 2); ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Deposits');?></td>
                    <td><?php echo $deposits ? number_format($deposits, 2) : number_format(0, 2); ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Gross Profit');?></td>
                    <?php 
                        $profit += $deposits ? number_format($deposits, 2) : number_format(0, 2);
                        $profit += $expenses ? number_format($expenses, 2) : number_format(0, 2);
                    ?>

                    <td><?php echo $profit ? number_format($profit, 2) : number_format(0, 2); ?></td>
                </tr>

            </table>
  </div>
</div>
