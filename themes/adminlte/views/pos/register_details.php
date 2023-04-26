        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fas fa-2x">&times;</i>
            </button>
            
            <h4 class="modal-title"
                id="myCashModalLabel"><?php echo lang('Sales') . ' (' .(date('m-d-Y H:i:s' ,strtotime($this->session->userdata('register_open_time')))) . ' - ' . (date('m-d-Y H:i:s')) . ')'; ?></h4>
        </div>
        <div class="modal-body">
            <table class="table ">
                <tr>
                    <td><h4><?php echo lang('Opening Cash');?>:</h4></td>
                    <td style="text-align:right; border-bottom: 1px solid #EEE;"><h4>
                            <span><?php echo $this->repairer->formatMoney($this->session->userdata('cash_in_hand')); ?></span></h4>
                    </td>
                </tr>
                <?php if($this->mSettings->accept_cash): ?>
                <tr>
                    <td style="border-bottom: 1px solid #EEE;"><h4><?php echo lang('Cash Sales');?>:</h4></td>
                    <td style="text-align:right; border-bottom: 1px solid #EEE;"><h4>
                            <span><?php echo $this->repairer->formatMoney($cashsales->paid ? $cashsales->paid : '0.00'); ?></span>
                        </h4></td>
                </tr>
                <?php endif; ?>
                <?php if($this->mSettings->accept_cheque): ?>
                <tr>
                    <td style="border-bottom: 1px solid #EEE;"><h4><?php echo lang('Check Sales');?>:</h4></td>
                    <td style="text-align:right;border-bottom: 1px solid #EEE;"><h4>
                            <span><?php echo $this->repairer->formatMoney($chsales->paid ? $chsales->paid : '0.00'); ?></span>
                        </h4></td>
                </tr>
                <?php endif; ?>
                <?php if($this->mSettings->accept_cc): ?>
                <tr>
                    <td style="border-bottom: 1px solid #DDD;"><h4><?php echo lang('Credit Card Sales');?>:</h4></td>
                    <td style="text-align:right;border-bottom: 1px solid #DDD;"><h4>
                            <span><?php echo $this->repairer->formatMoney($ccsales->paid ? $ccsales->paid : '0.00'); ?></span>
                        </h4></td>
                </tr>
                <?php endif; ?>
                <?php if($this->mSettings->accept_paypal): ?>
                <tr>
                    <td style="border-bottom: 1px solid #DDD;"><h4><?php echo lang('Paypal Sales');?>:</h4></td>
                    <td style="text-align:right;border-bottom: 1px solid #DDD;"><h4>
                            <span><?php echo $this->repairer->formatMoney($pppsales->paid ? $pppsales->paid : '0.00'); ?></span>
                        </h4></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td style="border-bottom: 1px solid #DDD;"><h4><?php echo lang('Other Sales');?>:</h4></td>
                    <td style="text-align:right;border-bottom: 1px solid #DDD;"><h4>
                            <span><?php echo $this->repairer->formatMoney($othersales->paid ? $othersales->paid : '0.00'); ?></span>
                        </h4></td>
                </tr>
               <tr>
                    <td width="300px;" style="font-weight:bold;"><h4><?php echo lang('Total Sales');?>:</h4></td>
                    <td width="200px;" style="font-weight:bold;text-align:right;"><h4>
                            <span><?php echo $this->repairer->formatMoney($totalsales->paid ? $totalsales->paid : '0.00') . ' (' . $this->repairer->formatMoney($totalsales->total ? $totalsales->total : '0.00') . ')'; ?></span>
                        </h4></td>
                </tr>

                <tr>
                    <td style="border-top: 1px solid #DDD;"><h4><?php echo lang('Refunds');?>:</h4></td>
                    <td style="text-align:right;border-top: 1px solid #DDD;"><h4>
                            <span><?php echo $this->repairer->formatMoney($refunds->returned ? $refunds->returned : '0.00') . ' (' . $this->repairer->formatMoney($refunds->total ? $refunds->total : '0.00') . ')'; ?></span>
                        </h4></td>
                </tr>
                
                <!-- Safe Start -->
                <tr>
                    <td width="300px;" style="font-weight:bold;"><h4><?php echo lang('Total Deposit to Safe');?>:</h4></td>
                    <td width="200px;" style="font-weight:bold;text-align:right;"><h4>
                            <span><?php echo $this->repairer->formatMoney($tosafetranfers->total); ?></span>
                        </h4></td>
                </tr>
                <tr>
                    <td width="300px;" style="font-weight:bold;"><h4><?php echo lang('Total Transfer to Drawer');?>:</h4></td>
                    <td width="200px;" style="font-weight:bold;text-align:right;"><h4>
                            <span><?php echo $this->repairer->formatMoney($todrawertranfers->total); ?></span>
                        </h4></td>
                </tr>
                <!-- Safe End -->
                <tr>
                    <td width="300px;" style="font-weight:bold;"><h4><strong><?php echo lang('Cash on Hand');?></strong>:</h4>
                    </td>
                    <td style="text-align:right;"><h4>
                            <span><strong><?php echo $cashsales->paid ? $this->repairer->formatMoney((($cashsales->paid - ($refunds->returned ? $refunds->returned : 0) + ($this->session->userdata('cash_in_hand'))) + $todrawertranfers->total) - $tosafetranfers->total) : $this->repairer->formatMoney(((($this->session->userdata('cash_in_hand'))) + $todrawertranfers->total) - $tosafetranfers->total -  ($refunds->returned ? $refunds->returned : 0)); ?></strong></span>
                        </h4></td>
                </tr>
            </table>
        </div>
   