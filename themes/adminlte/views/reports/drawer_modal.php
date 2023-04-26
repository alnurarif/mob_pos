    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Drawer report for <?php echo escapeStr($ruser->first_name;)?> <?php echo escapeStr($ruser->last_name); ?> on <?php echo $this->repairer->hrld($register->date); ?><?php echo $register->closed_at ? ' through ' .$this->repairer->hrld($register->closed_at) : ''; ?></h4>
    </div>
    <div class="modal-body">
        <div class="panel-body">
        		<table class="table table-bordered">
        			<thead>
        				<tr>
        					<th>#</th>
                            <th><?php echo lang('System Total');?></th>
                            <th><?php echo lang('Your Total');?></th>
                            <th><?php echo lang('Difference');?></th>
        				</tr>
        			</thead>
        			<tbody>
        				<tr>
        					<td><?php echo lang('Credit Cards');?></td>
        					<td><?php echo $register->total_cc; ?></td>
        					<td><?php echo $register->total_cc_submitted; ?></td>
        					<td><?php echo $register->total_cc-$register->total_cc_submitted; ?></td>
        				</tr>
        				<tr>
        					<td><?php echo lang('Cheques');?></td>
        					<td><?php echo $register->total_cheques; ?></td>
        					<td><?php echo $register->total_cheques_submitted; ?></td>
                            <td><?php echo $register->total_cheques-$register->total_cheques_submitted; ?></td>
        				</tr>
        				<tr>
        					<td><?php echo lang('Closing Cash');?></td>
        					<td><?php echo $register->total_cash; ?></td>
                            <td><?php echo $register->total_cash_submitted; ?></td>
                            <td><?php echo $register->total_cash-$register->total_cash_submitted; ?></td>
        				</tr>
        				<tr>
        					<td><?php echo lang('Paypal');?></td>
        					<td><?php echo $register->total_ppp; ?></td>
                            <td><?php echo $register->total_ppp_submitted; ?></td>
                            <td><?php echo $register->total_ppp-$register->total_ppp_submitted; ?></td>
        				</tr>
        				<tr>
        					<td><?php echo lang('Other');?></td>
        					<td><?php echo $register->total_others; ?></td>
                            <td><?php echo $register->total_others_submitted; ?></td>
                            <td><?php echo $register->total_others-$register->total_others_submitted; ?></td>
        				</tr>
                        <?php
                            $systemtotals = $register->total_cc + $register->total_cheques + $register->total_cash + $register->total_ppp + $register->total_others;
                            $usertotals = $register->total_cc_submitted + $register->total_cheques_submitted + $register->total_cash_submitted + $register->total_ppp_submitted + $register->total_others_submitted;
                        ?>
        				<tr>
        					<td><?php echo lang('Total');?></td>
        					<td><?php echo $systemtotals; ?></td>
        					<td><?php echo $usertotals; ?></td>
        					<td><?php echo $systemtotals-$usertotals; ?></td>
        				</tr>
        			</tbody>
        			<tfoot>
        				
        				<tr>
        					<td colspan="2"><?php echo lang('Deposits To Safe From Register');?></td>
        					<td><?php echo $register->tosafetranfers; ?></td>
        					<td rowspan="2"></td>
        				</tr>
        				<tr>
        					<td colspan="2"><?php echo lang('Deposits to Register From Safe');?></td>
                            <td><?php echo $register->todrawertranfers; ?></td>
        				</tr>
        				
        				<tr>
        					<td rowspan="4" colspan="4"><label><?php echo lang('notes');?>:</label><textarea class="form-control" rows="4" readonly><?php echo $register->count_note; ?></textarea></td>
        				</tr>
        			</tfoot>
        		</table>
        </div>
    </div>
	<div class="modal-footer" style="text-align: right;">
        <a class="btn btn-default"  href="<?php echo base_url(); ?>panel/reports/drawer_modal_view/<?php echo $register->id; ?>/pdf">Export to PDF</a>
        <a class="btn btn-default"  href="<?php echo base_url(); ?>panel/reports/drawer_modal_view/<?php echo $register->id; ?>/xls">Export to Excel</a>
    </div>
