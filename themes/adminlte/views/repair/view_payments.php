
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
        </button>
        <h4 class="modal-title" id="myModalLabel"><strong><?php echo lang('Repair History'); ?></strong></h4>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="compact table table-bordered table-striped" width="100%">
                    <thead>
                        <tr>
                            
                            <th><?php echo lang('Date'); ?></th>
                            <th><?php echo lang('ref'); ?></th>
                            <th><?php echo lang('Amount'); ?></th>
                            <th><?php echo lang('Payment Type'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($payments) > 0){ ?> 
                            <?php foreach($payments as $single_payment){ ?> 
                                <tr>
                                    <td><?=  $single_payment->payment_date; ?></td>
                                    <td><?= $single_payment->repair_reference_no; ?></td>
                                    <td><?= $single_payment->subtotal; ?></td>
                                    <td><?= ucfirst($single_payment->payment_method); ?></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>