<div class="modal-body">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        <i class="fas fa-2x">&times;</i>
    </button>
        
    <div class="well well-sm">
        <div class="row bold">
            <div class="col-xs-5">
            <p class="bold">
                <?php echo lang("date"); ?>: <?php echo $this->repairer->hrld($inv->date); ?><br>
                <?php echo lang("ref"); ?>: <?php echo $inv->reference_no; ?><br>
                <?php if (!empty($inv->return_sale_ref)) {
                    echo lang("return_ref").': '.$inv->return_sale_ref;
                    if ($inv->return_id) {
                        echo ' <a data-target="#myModal2" data-toggle="modal" href="'.site_url('sales/modal_view/'.$inv->return_id).'"><i class="fas fa-external-link no-print"></i></a><br>';
                    } else {
                        echo '<br>';
                    }
                } ?>
            </p>
            </div>
           
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="row" style="margin-bottom:15px;">
        <div class="col-xs-6">
            <?php echo $this->lang->line("from"); ?>:
            <h2 style="margin-top:10px;"><?php echo $inv->biller; ?></h2>
        </div>
        <div class="col-xs-6">
            <?php echo $this->lang->line("to"); ?>:<br/>
            <h2 style="margin-top:10px;"><?php echo $inv->customer; ?></h2>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped print-table order-table" width="100%">

            <thead>

            <tr>
                <th><?php echo lang("no"); ?></th>
                <th><?php echo lang("description"); ?></th>
                <th><?php echo lang("quantity"); ?></th>
                <?php if($this->Admin): ?>
                    <th><?php echo lang("cost"); ?></th>
                <?php endif;?>
                <th><?php echo lang("unit_price"); ?></th>
                <?php
                    echo '<th>' . lang("tax") . '</th>';
                    echo '<th>' . lang("discount") . '</th>';
                ?>
                <th><?php echo lang("subtotal"); ?></th>
            </tr>

            </thead>

            <tbody>

            <?php $r = 1;
            $tax_summary = array();
            foreach ($rows as $row):
            ?>
                <tr>
                    <td style="text-align:center; width:40px; vertical-align:middle;"><?php echo $r; ?></td>
                    <td style="vertical-align:middle;">
                        <?php echo escapeStr($row->product_name) . " (" . escapeStr($row->product_code) . ")"; ?>
                        <?php echo escapeStr($row->serial_number) ? '<br>' . escapeStr($row->serial_number) : ''; ?>
                        <?php if($row->item_details && $row->item_details != ''): ?>
                        <br>
                        <small><?php echo escapeStr($row->item_details);?></small>
                        <?php endif;?>

                    </td>
                    <td style="width: 80px; text-align:center; vertical-align:middle;"><?php echo ($row->quantity);?></td>
                    <?php if($this->Admin): ?>
                        <td style="text-align:right; width:100px;"><?php echo $this->repairer->formatMoney($row->cost); ?></td>
                    <?php endif;?>

                    <td style="text-align:right; width:100px;"><?php echo $this->repairer->formatMoney($row->unit_price); ?></td>
                    <?php
                        echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' .  $this->repairer->formatMoney($row->tax) . '</td>';
                        echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' .  $this->repairer->formatMoney($row->discount) . '</td>';
                    ?>
                    <td style="text-align:right; width:120px;"><?php echo $this->repairer->formatMoney($row->subtotal); ?></td>
                </tr>
                <?php
                $r++;
            endforeach;
            if ($return_rows) {
                echo '<tr class="warning"><td colspan="100%" class="no-border"><strong>'."Returned Items".'</strong></td></tr>';
                foreach ($return_rows as $row):
                ?>
                    <tr class="warning">
                        <td style="text-align:center; width:40px; vertical-align:middle;"><?php echo $r; ?></td>
                        <td style="vertical-align:middle;">
                            <?php echo escapeStr($row->product_name) . " (" . escapeStr($row->product_code) . ")"; ?>
                            <?php echo escapeStr($row->serial_number) ? '<br>' . escapeStr($row->serial_number) : ''; ?>
                        </td>
                        <td style="width: 80px; text-align:center; vertical-align:middle;"><?php echo ($row->quantity); ?></td>
                        <td style="text-align:right; width:100px;"><?php echo ($row->unit_price); ?></td>
                        <?php
                            echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax) . '</td>';
                            echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->discount) . '</td>';
                        ?>
                        <td style="text-align:right; width:120px;"><?php echo ($row->subtotal); ?></td>
                    </tr>
                    <?php
                    $r++;
                endforeach;
            }
            ?>
            </tbody>
            <tfoot>
           
            
            <?php if ($return_sale) {
                echo '<tr><td colspan="' . 6 . '" style="text-align:right; padding-right:10px;;">' . lang("return_total") . ' (' . $settings->currency . ')</td><td style="text-align:right; padding-right:10px;">' .  $this->repairer->formatMoney($return_sale->grand_total) . '</td></tr>';
                if ($return_sale->surcharge != 0) {
                    echo '<tr><td colspan="' . 6 . '" style="text-align:right; padding-right:10px;;">' . "Surcharge" . ' (' . $settings->currency . ')</td><td style="text-align:right; padding-right:10px;">' .  $this->repairer->formatMoney($return_sale->surcharge) . '</td></tr>';
                }
            }
            ?>
            
            <tr>
                <td colspan="6"
                    style="text-align:right; font-weight:bold;"><?php echo lang('total_amount');?>
                    (<?php echo escapeStr($settings->currency); ?>)
                </td>
                <td style="text-align:right; padding-right:10px; font-weight:bold;"><?php echo ($return_sale ? $this->repairer->formatMoney($inv->grand_total+$return_sale->grand_total) : $inv->grand_total); ?></td>
            </tr>
            <tr>
                <td colspan="6"
                    style="text-align:right; font-weight:bold;"><?php echo lang('paid');?>
                    (<?php echo escapeStr($settings->currency); ?>)
                </td>
                <td style="text-align:right; font-weight:bold;"><?php echo $this->repairer->formatMoney($return_sale ? ($inv->paid - ($return_sale->paid)) : $inv->paid); ?></td>
            </tr>
            <tr>
                <td colspan="6"
                    style="text-align:right; font-weight:bold;"><?php echo lang('balance');?>
                    (<?php echo escapeStr($settings->currency); ?>)
                </td>
                <td style="text-align:right; font-weight:bold;"><?php echo $this->repairer->formatMoney(($return_sale ? ($inv->grand_total+$return_sale->grand_total) : $inv->grand_total) - ($return_sale ? ($inv->paid - $return_sale->paid) : ($inv->sale_id ? 0-$inv->paid: $inv->paid))); ?></td>
            </tr>

            </tfoot>
        </table>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <?php if ($inv->note || $inv->note != "") { ?>
                    <div class="well well-sm">
                        <p class="bold"><?php echo lang("note"); ?>:</p>
                        <div><?php echo ($inv->note); ?></div>
                    </div>
            <?php
                }
            ?>
        </div>
    </div>
    
</div>
<script type="text/javascript">
    $(document).ready( function() {
    $('.tip').tooltip();
});
</script>
