<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ('SALE') . ' ' . $inv->reference_no; ?></title>
    <!-- <link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet"> -->
    <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url(); ?>assets/css/styles.css" rel="stylesheet" type="text/css">

    <style type="text/css">
        html, body {
            height: 100%;
            background: #FFF;
        }
        body:before, body:after {
            display: none !important;
        }
        .table th {
            text-align: center;
            padding: 5px;
        }
        .table td {
            padding: 4px;
        }
    </style>
</head>

<body>
<div id="wrap">
    <div class="row">
        <div class="col-lg-10">
            <div class="row bold">
                <div class="col-xs-5">
                    <img class="img-responsive" src="<?php echo FCPATH.'assets/uploads/logos/'.$settings->logo;?>" alt="Logo">
                </div>
                <div class="col-xs-6">
                    <p class="bold" style="text-align: right;">
                        <?php echo lang("date"); ?>: <?php echo ($inv->date); ?><br>
                        <?php echo lang("ref"); ?>: <?php echo escapeStr($inv->reference_no); ?><br>
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

           
            <div class="well">
                <div class="row">
                    <div class="col-xs-6">
                        <h5><?php echo lang('From');?>:</h5>
                        <address>
                            <?php echo lang('Location');?>: <?php echo escapeStr($this->activeStoreData->name); ?><br>
                            <?php echo escapeStr($this->activeStoreData->address); ?><br>
                            <?php echo escapeStr($this->activeStoreData->city); ?>, <?php echo escapeStr($this->activeStoreData->state); ?> <?php echo escapeStr($this->activeStoreData->zipcode); ?><br>
                            <abbr title="Phone">P:</abbr> <?php echo escapeStr($this->activeStoreData->phone); ?>
                        </address>
                    </div>
                    <?php if((int)$inv->customer_id !== -1): ?>
                    <div class="col-xs-5">
                        <h5>To:</h5>
                        <address>
                                <strong><?php echo escapeStr($inv->customer); ?></strong><br>
                                <?php echo escapeStr($client->address); ?><br>
                                <abbr title="Phone">P:</abbr> <?php echo preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $client->telephone); ?>
                            </address>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" width="100%">

                    <thead>

                    <tr>
                        <th><?php echo lang("no"); ?></th>
                        <th><?php echo lang("description"); ?></th>
                        <th><?php echo lang("quantity"); ?></th>
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
                            </td>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?php echo ($row->quantity);?></td>
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
                        <td style="text-align:right; padding-right:10px; font-weight:bold;"><?php echo $this->repairer->formatMoney(trim($return_sale ? ($inv->grand_total+$return_sale->grand_total) : $inv->grand_total)); ?></td>
                    </tr>
                    <tr>
                        <td colspan="6"
                            style="text-align:right; font-weight:bold;"><?php echo lang('paid');?>
                            (<?php echo escapeStr($settings->currency); ?>)
                        </td>
                        <td style="text-align:right; font-weight:bold;"><?php echo $this->repairer->formatMoney($return_sale ? ($inv->paid+$return_sale->paid) : $inv->paid); ?></td>
                    </tr>
                    <tr>
                        <td colspan="6"
                            style="text-align:right; font-weight:bold;"><?php echo lang('balance');?>
                            (<?php echo escapeStr($settings->currency); ?>)
                        </td>
                        <td style="text-align:right; font-weight:bold;"><?php echo $this->repairer->formatMoney(($return_sale ? ($inv->grand_total+$return_sale->grand_total) : $inv->grand_total) - ($return_sale ? $this->repairer->formatMoney($inv->paid+$return_sale->paid) : ($inv->sale_id ? 0-$inv->paid: $inv->paid))); ?></td>
                    </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row">
                <div class="col-xs-10">
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
    </div>
</div>
</body>
</html>

