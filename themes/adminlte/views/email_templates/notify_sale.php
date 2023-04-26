<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?php echo $page_title . " " . lang("no") . " " . $inv->id; ?></title>
    <style>
        * { font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0; } img { max-width: 100%; } body { -webkit-font-smoothing: antialiased; height: 100%; -webkit-text-size-adjust: none; width: 100% !important; } a { color: #348eda; } .btn-primary { Margin-bottom: 10px; width: auto !important; } .btn-primary td { background-color: #62cb31; border-radius: 3px; font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; font-size: 14px; text-align: center; vertical-align: top; } .btn-primary td a { background-color: #62cb31; border: solid 1px #62cb31; border-radius: 3px; border-width: 4px 20px; display: inline-block; color: #ffffff; cursor: pointer; font-weight: bold; line-height: 2; text-decoration: none; } .last { margin-bottom: 0; } .first { margin-top: 0; } .padding { padding: 10px 0; } table.body-wrap { padding: 20px; width: 100%; } table.body-wrap .container { border: 1px solid #e4e5e7; } table.footer-wrap { clear: both !important; width: 100%; } .footer-wrap .container p { color: #666666; font-size: 12px; } table.footer-wrap a { color: #999999; } h1, h2, h3 { color: #111111; font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; font-weight: 200; line-height: 1.2em; margin: 10px 0 10px; } h1 { font-size: 36px; } h2 { font-size: 28px; } h3 { font-size: 22px; } p, ul, ol {font-size: 14px;font-weight: normal;margin-bottom: 10px;} ul li, ol li {margin-left: 5px;list-style-position: inside;} .container { clear: both !important; display: block !important; Margin: 0 auto !important; max-width: 600px !important; } .body-wrap .container { padding: 20px; } .content { display: block; margin: 0 auto; max-width: 600px; } .content table { width: 100%; }
    </style>
</head>

<body bgcolor="#f7f9fa">
<table class="body-wrap" bgcolor="#f7f9fa">
    <tr>
        <td></td>
        <td class="container" bgcolor="#FFFFFF">
            <div class="content">
                <table>
                    <tr>
                        <td>
                            <h2>
                                <center><img src="<?php echo base_url().'assets/uploads/logos/' . $settings->logo; ?>" alt="<?php echo $settings->title; ?>"></center>
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div style="clear:both;height:15px;"></div>
                                <div id="receiptData" style="border:1px solid #DDD; padding:10px; text-align:center;">

                                    <div class="text-center">
                                        <h3 style="text-transform:uppercase;"><?php echo lang('Biller');?>: <?php echo escapeStr($biller); ?></h3>
                                        <address>
                                            <?php echo lang('Location');?>: <?php echo escapeStr($this->activeStoreData->name); ?><br>
                                            <?php echo escapeStr($this->activeStoreData->address); ?><br>
                                            <?php echo escapeStr($this->activeStoreData->city); ?>, <?php echo escapeStr($this->activeStoreData->state); ?> <?php echo escapeStr($this->activeStoreData->zipcode); ?><br>
                                            <abbr title="Phone"><?php echo lang('Phone');?>:</abbr> <?php echo escapeStr($this->activeStoreData->phone); ?>
                                        </address>
                                    </div>
                                    <?php
                                      echo "<p>" . lang("reference_no") . ": " . $inv->reference_no . "<br>";
                                      echo lang("customer") . ": " . escapeStr($inv->customer) . "<br>";
                                      echo lang("date") . ": " . $this->repairer->hrld($inv->date) . "</p>";
                                    ?>
                                    <div style="clear:both;"></div>
                                    <table width="100%" style="margin:15px 0;">
                                        <tbody>
                                        <?php
                                        $r = 1;
                                        foreach ($rows as $row) {
                                            echo '<tr><td colspan="2" style="text-align:left;">#' . $r . ': &nbsp;&nbsp;' . escapeStr($row->product_name)  . '</td></tr>';
                                            echo '<tr><td style="border-bottom:1px solid #DDD;">' . $this->repairer->formatQuantity($row->quantity) . ' x ';
                                            if ($row->item_discount != 0) {
                                                echo '<del>' . $this->repairer->formatMoney($row->unit_price + ($row->item_discount / $row->quantity) + ($row->tax / $row->quantity)) . '</del> ';
                                            }
                                             if ($inv->surcharge != 0) {
                                                echo '<tr><th style="text-align:left;border-bottom:1px solid #DDD;">' . "Surcharge" . '</th><th style="text-align:right;border-bottom:1px solid #DDD;">' . $this->repairer->formatMoney($inv->surcharge) . '</th></tr>';
                                            }
                                            echo $this->repairer->formatMoney($row->unit_price + ($row->tax / $row->quantity)).' ('.$this->repairer->formatMoney($row->unit_price).' + '.$this->repairer->formatMoney($row->tax / $row->quantity) . ')</td><td style="text-align:right;border-bottom:1px solid #DDD;">' . $this->repairer->formatMoney($row->subtotal) . '</td></tr>';
                                            $r++;
                                        }
                                        ?>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th style="text-align:left;border-bottom:1px solid #DDD;"><?php echo lang("total"); ?></th>
                                            <th style="text-align:right;border-bottom:1px solid #DDD;"><?php echo $this->repairer->formatMoney($inv->grand_total-$inv->total_tax); ?></th>
                                        </tr>
                                        <?php
                                        if ($inv->total_tax != 0 && $settings->tax2) {
                                            echo '<tr><th style="text-align:left;border-bottom:1px solid #DDD;">' . lang("tax") . '</th><th style="text-align:right;border-bottom:1px solid #DDD;">' . $this->repairer->formatMoney($inv->total_tax) . '</th></tr>';
                                        }
                                        if ($inv->total_discount != 0) {
                                            echo '<tr><th style="text-align:left;border-bottom:1px solid #DDD;">' . lang("discount") . '</th><th style="text-align:right;border-bottom:1px solid #DDD;">' . $this->repairer->formatMoney($inv->total_discount) . '</th></tr>';
                                        }
                                        ?>
                                        <tr>
                                            <th style="text-align:left;border-bottom:1px solid #DDD;"><?php echo lang("grand_total"); ?></th>
                                            <th style="text-align:right;border-bottom:1px solid #DDD;"><?php echo $this->repairer->formatMoney($inv->grand_total); ?></th>
                                        </tr>

                                        </tfoot>
                                    </table>
                                    <?php
                            if ($payments) {
                                echo '<table class="table  table-horizontal-bordered table-condensed"><tbody>';
                                echo '<thead><tr><td colspan="3">'.lang('payments').'</td></tr></thead>';
                                foreach ($payments as $payment) {
                                    echo '<tr>';
                                    if (($payment->paid_by == 'cash' || $payment->paid_by == 'ppp' || $payment->paid_by == 'CC') && $payment->pos_paid) {
                                        echo '<td class="">' . lang('paid_by') . ': ' . lang($payment->paid_by) . '</td>';
                                        echo '<td>' . lang("amount") . ': ' . $this->repairer->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . '</td>';
                                        echo '<td>' . lang("change") . ': ' . $this->repairer->formatMoney($payment->pos_balance > 0 ? ($payment->pos_balance) : 0) . '</td>';
                                    } elseif ($payment->paid_by == 'Cheque' && $payment->cheque_no) {
                                        echo '<td class="">' . lang('paid_by') . ': ' . lang($payment->paid_by) . '</td>';
                                        echo '<td>' . lang("amount") . ': ' . $this->repairer->formatMoney($payment->pos_paid) . '</td>';
                                        echo '<td>' . lang("cheque_no") . ': ' . $payment->cheque_no . '</td>';
                                    } elseif ($payment->paid_by == 'other' && $payment->amount) {
                                        echo '<td class="">' . lang('paid_by') . ': ' . lang($payment->paid_by) . '</td>';
                                        echo '<td>' . lang("amount") . ': ' . $this->repairer->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid)  . '</td>';
                                        echo escapeStr($payment->note) ? '</tr><td colspan="2">' . lang('payment_note') . ': ' . escapeStr($payment->note) . '</td>' : '';
                                    }
                                    echo '</tr>';
                                }
                                echo '</tbody></table>';
                            }
                            ?>
                                    <p class="text-center">
                                        <?php echo $this->repairer->decode_html($settings->disclaimer_sale); ?>
                                    </p>
                                    <div style="clear:both;"></div>
                                </div>

                            </div>
                            <div style="clear:both;height:25px;"></div>
                            <strong><?php echo $settings->title; ?></strong>
                        </td>
                    </tr>
                </table>
            </div>

        </td>
        <td></td>
    </tr>
</table>

</body>
</html>
