<div class="modal-content">
    <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
            <i class="fa">&times;</i>
        </button>
        <h4 class="modal-title" id="myModalLabel"><?php echo lang('view_payments').' ('.lang('sale').' '.lang('reference').': '.$inv->reference_no.')'; ?></h4>
    </div>
    <div class="modal-body">
        <div class="table-responsive">
            <table id="CompTable" cellpadding="0" cellspacing="0" border="0"
                   class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th style="width:30%;"><?php echo $this->lang->line("date"); ?></th>
                    <th style="width:15%;"><?php echo $this->lang->line("amount"); ?></th>
                    <th style="width:15%;"><?php echo $this->lang->line("notes"); ?></th>
                    <th style="width:15%;"><?php echo $this->lang->line("paid_by"); ?></th>
                    <th style="width:10%;"><?php echo $this->lang->line("actions"); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($payments)) {
                    foreach ($payments as $payment) { ?>
                        <tr class="row<?php echo $payment->id ?>">
                            <td><?php echo $this->repairer->hrld($payment->date); ?></td>
                            <td><?php echo $this->repairer->formatMoney($payment->amount) . ' ' . (($payment->attachment) ? '<a href="' . base_url('panel/welcome/download/' . $payment->attachment) . '"><i class="fa fa-chain"></i></a>' : ''); ?></td>
                            <td><?php echo $payment->note ?></td>
                            <td><?php echo lang($payment->paid_by); ?></td>
                            <td>
                                <div class="text-center">
                                    <a href="<?php echo base_url('panel/sales/payment_note/' . $payment->id) ?>"
                                       data-toggle="modal" data-target="#myModal2"><i class="fa fa-file-text-o"></i></a>
                                        <a href="<?php echo base_url('panel/sales/email_payment/' . $payment->id) ?>" class="email_payment"><i class="fa fa-envelope"></i></a>
                                        <a href="<?php echo base_url('panel/sales/edit_payment/' . $payment->id.'/'. $payment->sale_id) ?>"
                                           data-toggle="modal" data-target="#myModal2"><i
                                                class="fa fa-edit"></i></a>

                                        <a href="#" class="po"
                                           title="<b><?php echo $this->lang->line("delete_payment") ?></b>"
                                           data-content="<p><?php echo lang('r_u_sure') ?></p><a class='btn-icon btn btn-danger' id='<?php echo $payment->id ?>' href='<?php echo base_url('panel/sales/delete_payment/' . $payment->id) ?>'><i class='fa fa-trash img-circle text-danger'></i> <?php echo lang('i_m_sure') ?></a> <button class='btn po-close btn-default btn-icon '><i class='fa fa-reply img-circle text-muted'></i> <?php echo lang('no') ?></button>"
                                           rel="popover"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php }
                } else {
                    echo "<tr><td colspan='4'>" . lang('no_payments') . "</td></tr>";
                } ?>
                </tbody>
            </table>
        </div>
    </div>
     <div class="modal-footer">
        <button data-dismiss="modal" class="btn-responsive  btn-icon btn btn-goback" type="button">
            <i class="fa fa-reply img-circle text-muted"></i> 
            <?php echo lang('go_back'); ?>
        </button>
    </div>
</div>
<script type="text/javascript" charset="UTF-8">
    $(document).ready(function () {
        $(document).on('click', '.po-delete', function () {
            var id = $(this).attr('id');
            $(this).closest('tr').remove();
        });
        $(document).on('click', '.email_payment', function (e) {
            e.preventDefault();
            var link = $(this).attr('href');
            $.get(link, function(data) {
                bootbox.alert(data.msg);
            });
            return false;
        });
    });
</script>