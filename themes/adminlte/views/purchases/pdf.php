<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->lang->line("purchase") . " " . $inv->reference_no; ?></title>

        <link rel="stylesheet" href="<?php echo $assets ?>dist/css/custom/kv-mpdf-bootstrap.css">
    <!-- <link href="<?php echo base_url('assets/bootstrap/css/'); ?>bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="<?php echo base_url('assets/dist/css/'); ?>style.css" rel="stylesheet"> -->
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
        <div class="col-lg-12">
            <?php if ($logo) { ?>
                <div class="text-center" style="margin-bottom:20px;">
                    <img width="500px" src="<?php echo base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>"
                         alt="<?php echo $Settings->title; ?>">
                </div>
            <?php } ?>
            <div class="well well-sm">
                <div class="row bold">
                    <div class="col-xs-5">
                    <p class="bold">
                       <strong><?php echo lang("date"); ?>:</strong> <?php echo $this->repairer->hrld($inv->date); ?><br>
                        <strong><?php echo lang("ref"); ?>:</strong> <?php echo escapeStr($inv->reference_no); ?><br>
                        <?php if (!empty($inv->return_purchase_ref)) {
                            echo lang("return_ref").': '.$inv->return_purchase_ref.'<br>';
                        } ?>
                        <strong><?php echo lang("status"); ?>:</strong> <?php echo lang($inv->status); ?><br>
                        <strong><?php echo lang('Tracking Number');?>:</strong> <?php echo escapeStr($inv->track_code) ? '<a target="_blank" href="https://www.packagemapping.com/track/auto/'.escapeStr($inv->track_code).'">'.escapeStr($inv->track_code).'</a>' : 'Not Available'; ?><br>
                        <strong><?php echo lang('Shipping Provider');?>:</strong> <?php echo $inv->provider?strtoupper($inv->provider):'Not Available'; ?>
                    </div>
                    <div class="col-xs-7 text-right">
                        <?php echo $this->repairer->barcode($inv->reference_no, 'code128', 66, false); ?>
                        <?php echo $this->repairer->qrcode('link', urlencode(site_url('panel/purchases/view/' . $inv->id)), 2); ?> 
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="row" style="margin-bottom:15px;">
                <div class="col-xs-6">
                    <?php echo $this->lang->line("from"); ?>:
                   <h2 style="margin-top:10px;"><?php echo escapeStr($Settings->title); ?></h2>

                    <address>
                        <?php echo lang('Location');?>: <?php echo escapeStr($this->activeStoreData->name); ?><br>
                        <?php echo escapeStr($this->activeStoreData->address); ?><br>
                        <?php echo escapeStr($this->activeStoreData->city); ?>, <?php echo escapeStr($this->activeStoreData->state); ?> <?php echo escapeStr($this->activeStoreData->zipcode); ?><br>
                        <abbr title="Phone">P:</abbr> <?php echo escapeStr($this->activeStoreData->phone); ?>
                    </address>

                 
                </div>
                <div class="col-xs-6">
                    <?php echo $this->lang->line("to"); ?>:<br/>
                    <h2 style="margin-top:10px;"><?php echo $supplier->company ? $supplier->company : $supplier->name; ?></h2>
                    <?php echo $supplier->company ? "" : lang('attn') . ": " . $supplier->name ?>

                    <?php
                    echo escapeStr($supplier->address) . "<br />" . escapeStr($supplier->city) . " " . escapeStr($supplier->postal_code) . " " . escapeStr($supplier->state) . "<br />" . escapeStr($supplier->country);

                    echo "<p>";

                    if ($supplier->vat_no != "-" && $supplier->vat_no != "") {
                        echo "<br>" . lang("vat_no") . ": " . escapeStr($supplier->vat_no);
                    }

                    echo "</p>";
                    echo lang("tel") . ": " . escapeStr($supplier->phone) . "<br />" . lang("email") . ": " . escapeStr($supplier->email);
                    ?>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped print-table order-table">

                    <thead>

                    <tr>
                        <th><?php echo lang("no"); ?></th>
                        <th><?php echo lang("description"); ?></th>
                        <th><?php echo lang("quantity"); ?></th>
                        <?php
                            if ($inv->status == 'partial') {
                                echo '<th>'.lang("received").'</th>';
                            }
                        ?>
                        <th><?php echo lang("unit_cost"); ?></th>
                        <?php
                        if ($Settings->tax1 && $inv->product_tax > 0) {
                            echo '<th>' . lang("tax") . '</th>';
                        }
                        if ($Settings->product_discount && $inv->product_discount != 0) {
                            echo '<th>' . lang("discount") . '</th>';
                        }
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
                                <?php echo escapeStr($row->details) ? '<br>' . escapeStr($row->details) : ''; ?>
                            </td>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?php echo $this->repairer->formatQuantity($row->quantity); ?></td>
                            <?php
                            if ($inv->status == 'partial') {
                                echo '<td style="text-align:center;vertical-align:middle;width:80px;">'.$this->repairer->formatQuantity($row->quantity_received).'</td>';
                            }
                            ?>
                            <td style="text-align:right; width:100px;"><?php echo $this->repairer->formatMoney($row->net_unit_cost); ?></td>
                            <?php
                            if ($Settings->tax1 && $inv->product_tax > 0) {
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 && $row->tax_code ? '<small>('.$row->tax_code.')</small>' : '') . ' ' . $this->repairer->formatMoney($row->item_tax) . '</td>';
                            }
                            if ($Settings->product_discount && $inv->product_discount != 0) {
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->repairer->formatMoney($row->item_discount) . '</td>';
                            }
                            ?>
                            <td style="text-align:right; width:120px;"><?php echo $this->repairer->formatMoney($row->subtotal); ?></td>
                        </tr>
                        <?php
                        $r++;
                    endforeach;
                    if (isset($return_rows)) {
                        echo '<tr class="warning"><td colspan="100%" class="no-border"><strong>Returned Items</strong></td></tr>';
                        foreach ($return_rows as $row):
                        ?>
                            <tr class="warning">
                                <td style="text-align:center; width:40px; vertical-align:middle;"><?php echo $r; ?></td>
                                <td style="vertical-align:middle;">
                                    <?php echo escapeStr($row->product_name) . " (" . escapeStr($row->product_code) . ")"; ?>
                                    <?php echo escapeStr($row->details) ? '<br>' . escapeStr($row->details) : ''; ?>
                                </td>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?php echo $this->repairer->formatQuantity($row->quantity); ?></td>
                                <?php
                                if ($inv->status == 'partial') {
                                    echo '<td style="text-align:center;vertical-align:middle;width:80px;">'.$this->repairer->formatQuantity($row->quantity_received).'</td>';
                                }
                                ?>
                                <td style="text-align:right; width:100px;"><?php echo $this->repairer->formatMoney($row->net_unit_cost); ?></td>
                                <?php
                                if ($Settings->tax1 && $inv->product_tax > 0) {
                                    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 && $row->tax_code ? '<small>('.$row->tax_code.')</small>' : '') . ' ' . $this->repairer->formatMoney($row->item_tax) . '</td>';
                                }
                                if ($Settings->product_discount && $inv->product_discount != 0) {
                                    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->repairer->formatMoney($row->item_discount) . '</td>';
                                }
                                ?>
                                <td style="text-align:right; width:120px;"><?php echo $this->repairer->formatMoney($row->subtotal); ?></td>
                            </tr>
                            <?php
                            $r++;
                        endforeach;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <?php
                    $col = 4;
                    if ($inv->status == 'partial') {
                        $col++;
                    }
                    if ($Settings->product_discount && $inv->product_discount != 0) {
                        $col++;
                    }
                    if ($Settings->tax1 && $inv->product_tax > 0) {
                        $col++;
                    }
                    if ( $Settings->product_discount && $inv->product_discount != 0 && $Settings->tax1 && $inv->product_tax > 0) {
                        $tcol = $col - 2;
                    } elseif ( $Settings->product_discount && $inv->product_discount != 0) {
                        $tcol = $col - 1;
                    } elseif ($Settings->tax1 && $inv->product_tax > 0) {
                        $tcol = $col - 1;
                    } else {
                        $tcol = $col;
                    }
                    ?>
                    <?php if ($inv->grand_total != $inv->total) { ?>
                        <tr>
                            <td colspan="<?php echo $tcol; ?>"
                                style="text-align:right; padding-right:10px;"><?php echo lang("total"); ?>
                                (<?php echo escapeStr($settings->currency); ?>)
                            </td>
                            <?php
                            if ($Settings->tax1 && $inv->product_tax > 0) {
                                echo '<td style="text-align:right;">' . $this->repairer->formatMoney(isset($return_purchase) ? ($inv->product_tax+$return_purchase->product_tax) : $inv->product_tax) . '</td>';
                            }
                            if ($Settings->product_discount && $inv->product_discount != 0) {
                                echo '<td style="text-align:right;">' . $this->repairer->formatMoney(isset($return_purchase) ? ($inv->product_discount+$return_purchase->product_discount) : $inv->product_discount) . '</td>';
                            }
                            ?>
                            <td style="text-align:right; padding-right:10px;"><?php echo $this->repairer->formatMoney(isset($return_purchase) ? (($inv->total + $inv->product_tax)+($return_purchase->total + $return_purchase->product_tax)) : ($inv->total + $inv->product_tax)); ?></td>
                        </tr>
                    <?php } ?>
                    <?php if (isset($return_purchase)) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;;">' . lang("return_total") . ' (' . $Settings->currency . ')</td><td style="text-align:right; padding-right:10px;">' . $this->repairer->formatMoney($return_purchase->grand_total) . '</td></tr>';
                        if ($return_purchase->surcharge != 0) {
                            echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;;">' . lang("return_surcharge") . ' (' . $Settings->currency . ')</td><td style="text-align:right; padding-right:10px;">' . $this->repairer->formatMoney($return_purchase->surcharge) . '</td></tr>';
                        }
                    }
                    ?>

                    <?php if ($inv->order_discount != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;;">' . lang("order_discount") . ' (' . $Settings->currency . ')</td><td style="text-align:right; padding-right:10px;">' . $this->repairer->formatMoney($return_purchase ? ($inv->order_discount+$return_purchase->order_discount) : $inv->order_discount) . '</td></tr>';
                    }
                    ?>
                    <?php if ($inv->order_tax != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;">' . lang("order_tax") . ' (' . $Settings->currency . ')</td><td style="text-align:right; padding-right:10px;">' . $this->repairer->formatMoney($return_purchase ? ($inv->order_tax+$return_purchase->order_tax) : $inv->order_tax) . '</td></tr>';
                    }
                    ?>
                    <?php if ($inv->shipping != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;;">' . lang("shipping") . ' (' . $Settings->currency . ')</td><td style="text-align:right; padding-right:10px;">' . $this->repairer->formatMoney($inv->shipping) . '</td></tr>';
                    }
                    ?>
                    <tr>
                        <td colspan="<?php echo $col; ?>"
                            style="text-align:right; font-weight:bold;"><?php echo lang("total_amount"); ?>
                            (<?php echo escapeStr($settings->currency); ?>)
                        </td>
                        <td style="text-align:right; padding-right:10px; font-weight:bold;"><?php echo $this->repairer->formatMoney($return_purchase ? ($inv->grand_total+$return_purchase->grand_total) : $inv->grand_total); ?></td>
                    </tr>



                    </tfoot>
                </table>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <?php
                        if ($inv->note || $inv->note != "") { ?>
                            <div class="well well-sm">
                                <p class="bold"><?php echo lang("note"); ?>:</p>
                                <div><?php echo $this->repairer->decode_html($inv->note); ?></div>
                            </div>
                        <?php
                        }
                        ?>
                </div>

                <?php if ($created_by || $updated_by) { ?>
                <div class="col-xs-5 pull-right">
                    <div class="well well-sm">
                        <?php if ($created_by) { ?>
                            <p>
                                <?php echo lang("created_by"); ?>: <?php echo $created_by->first_name . ' ' . $created_by->last_name; ?> <br>
                                <?php echo lang("date"); ?>: <?php echo $this->repairer->hrld($inv->date); ?>
                            </p>
                        <?php } ?>
                        <?php if ($inv->updated_by) { ?>
                            <p>
                                <?php echo lang("updated_by"); ?>: <?php echo $updated_by->first_name . ' ' . $updated_by->last_name;; ?><br>
                                <?php echo lang("update_at"); ?>: <?php echo $this->repairer->hrld($inv->updated_at); ?>
                            </p>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
                
            </div>
        </div>
    </div>
</div>
</body>
</html>
