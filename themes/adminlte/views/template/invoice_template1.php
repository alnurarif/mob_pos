<?php
$tax = $db['tax'];
$price_without_tax = $db['total'] - $db['service_charges']; // PRICE WITHOUT TAX
$total = $db['grand_total']; // PRICE WITH TAX
$paid = $db['advance']; // PRICE WITH TAX

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php echo $this->lang->line('invoice');?></title>
        <link href="<?php echo $assets ?>dist/css/custom/invoice.css" rel="stylesheet">
        <!-- jQuery 2.2.3 -->
        <script src="<?php echo $assets ?>bower_components/jquery/dist/jquery.min.js"></script>
        <!-- Accounting.js -->
        <script src="<?php echo $assets ?>plugins/custom/accounting.min.js"></script>

        <script src="<?php echo $assets;?>plugins/jSignature/jSignature.min.js"></script>
        <script>

            function formatDecimal(x, d) {
                if (!d) { d = 2; }
                return parseFloat(accounting.formatNumber(x, d, ',', '.'));
            }
        </script>
        <style type="text/css">
            table .no, table .total {
                background: <?php echo $settings->invoice_table_color; ?>
            }
        </style>
    </head>
    
    <body>
        <div id="editable_invoice"><?php echo $this->lang->line('editable_invoice');?></div>
        <header class="clearfix">
            <div id="logo">
                <img src="<?php echo base_url(); ?>assets/uploads/logos/<?php echo $settings->logo; ?>">
            </div>
            <div id="company" contentEditable="true">
                <h2 class="name"><?php echo escapeStr($this->activeStoreData->name); ?></h2>
                <div><?php echo escapeStr($this->activeStoreData->address); ?></div>
                <div><?php echo escapeStr($this->activeStoreData->phone); ?></div>
                <div><a href="mailto:<?php echo escapeStr($this->activeStoreData->invoice_mail); ?>"><?php echo escapeStr($this->activeStoreData->invoice_mail); ?></a></div>
                <div><?php echo escapeStr($settings->vat); ?></div>
            </div>
            </div>
        </header>
    <main>
        <div id="details" class="clearfix">
            <div id="client" contentEditable="true">
                <div class="to"><?php echo $this->lang->line('client_title');?>:</div>
                <h2 class="name"><?php echo $db['name']; ?></h2>
                <div class="company"><?php echo ($client ?  escapeStr($client->company) : ''); ?></div>
                <div class="address"><?php echo ($client ?  escapeStr($client->address) : ''); ?></div>
                <div class="postal_code"><?php echo ($client ?  escapeStr($client->city) : ''); ?> <?php echo ($client ? escapeStr($client->postal_code ): ''); ?></div>
                <div class="email"><a <?php echo $client ? 'href="mailto:'. escapeStr($client->email).'"' : ''; ?> ><?php echo $client ? escapeStr($client->email) : ''; ?></a></div>
                <div class="telephone"><?php echo $client ?  escapeStr($client->telephone) : ''; ?></div>
                <div class="vat">
                    <?php 
                        if(isset($client->vat)) {
                            echo  escapeStr($client->vat); 
                            $ve = 1;
                        }
                        if(isset($client->cf)) {
                            if($ve) echo ' / ';
                            echo  escapeStr($client->cf);
                        }
                    ?>
                </div>
            </div>
            <div id="invoice" contentEditable="true">
                <h1><?php echo $this->lang->line('invoice_n');?> <i><?php echo str_pad($db['id'], 4, '0', STR_PAD_LEFT); ?></i></h1>
                <div class="date"><?php echo $this->lang->line('date_opening');?>: <?php echo date_format(date_create($db['date_opening']),"Y/m/d"); ?></div>
            </div>
        </div>
        <h3><?php echo $this->lang->line('reparation_title').': '.$db['defect'].' '.$db['model_name']; ?></h3>
        <pre>
        </pre>
        <table border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="no">#</th>
                    <th class="desc"><?php echo $this->lang->line('description');?></th>
                    <th class="unit"><?php echo $this->lang->line('unit_price');?></th>
                    <th class="qty"><?php echo $this->lang->line('quantity');?></th>
                    <th class="total"><?php echo $this->lang->line('subtotal');?></th>
                </tr>
            </thead>
            <tbody contentEditable="true">
                <?php $items_total = 0;?>
                <?php if($items): ?>
                <?php $a = 1; foreach ($items as $item): ?>
                    <?php $items_total += $item->subtotal;?>

                    <tr>
                        <td class="no"><?php echo str_pad($a, 2, '0', STR_PAD_LEFT); ?></td>
                        <td class="desc"><h3><?php echo escapeStr($item->product_name); ?></td>
                        <td class="unit"><?php echo $this->repairer->formatMoney($item->unit_price, 2);?></td>
                        <td class="qty"><?php echo $this->repairer->formatQuantity($item->quantity, 1);?></td>
                        <td class="total"><?php echo $this->repairer->formatMoney($item->subtotal, 2);?></td>
                    </tr>
                <?php $a++; endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="no"><?php echo str_pad(1, 2, '0', STR_PAD_LEFT); ?></td>
                        <td class="desc"><h3><?php echo lang('no_items_used');?></td>
                        <td class="unit"><?=$this->repairer->formatMoney(0.00);?></td>
                        <td class="qty"><?=$this->repairer->formatQuantity(0.00);?></td>
                        <td class="total"><?=$this->repairer->formatMoney(0.00);?></td>
                    </tr>
                <?php endif; ?>

                
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" rowspan="6" id="comment"><textarea id="comment" onkeyup="auto_grow(this)" contentEditable="true"><?php echo escapeStr($db['public_note']); ?></textarea></td>
                    <td colspan="2"><?php echo $this->lang->line('subtotal');?></td>
                    <td contentEditable="true"><?php echo $this->repairer->formatMoney($items_total, 2);?></td>
                </tr>
                <tr>
                    <td colspan="2"><?php echo $this->lang->line('labor_cost_summay');?></td>
                    <td contentEditable="true"><?php echo $this->repairer->formatMoney($db['service_charges'], 2); ?></td>
                </tr>
                <tr>
                    <td colspan="2"><?php echo $this->lang->line('tax');?> <?php echo $tax_rate ? $tax_rate->name : '' ?></td>
                    <td contentEditable="true"><?php echo $this->repairer->formatMoney($tax, 2);?></td>
                </tr>
                <tr>
                    <td colspan="2"><?php echo $this->lang->line('total');?></td>
                    <td contentEditable="true"><?php echo $this->repairer->formatMoney($total, 2);?></td>
                </tr>
                <tr>
                    <td colspan="2"><?php echo $this->lang->line('paid');?></td>
                    <td contentEditable="true"><?php echo $this->repairer->formatMoney($paid, 2);?></td>
                </tr>
                <tr>
                    <td colspan="2"> <?php echo lang('payable'); ?> </td>
                    <td contentEditable="true"><?php echo $this->repairer->formatMoney($total - $paid, 2) ?> </td>
                </tr>
                
            </tfoot>
        </table>

            <small style="text-align: right !important;">
                <?php if($payments): ?>
                    <?php foreach ($payments as $payment){
                        echo sprintf(lang('paid_by_date'), lang($payment->paid_by), $this->repairer->formatMoney($payment->amount), $payment->date).'<br>';
                    }?>
                <?php endif; ?>
            </small>
        <hr>

        <?php if($db['warranty'] && json_decode($db['warranty']) && isset(json_decode($db['warranty'])->details)): ?><div class="col txt" style="padding: 5px;width: calc(100% - 10px);font-size: 10px;">
            <div class="well">
                <strong><?php echo lang('Warranty Details');?>:</strong><br>
                <?php echo escapeStr(json_decode($db['warranty'])->details); ?>
            </div>
        <?php endif; ?>
        <hr>
        <?php echo escapeStr($settings->disclaimer); ?>

    </main>


    <div class="<?php echo ($db['invoice_sign'] && $db['invoice_sign'] !== '') ? '' : 'no-print';?> well" style="width:100%;height:200px;background-color:lightgrey;color:black;border:5px;font:11px/15px;align-content:left  sans-serif;">
        <?php if($db['invoice_sign'] && $db['invoice_sign'] !== ''): ?>
            <label>Customer Signature: </label>
            <div class="clearfix"></div>
            <img height="200px" src="<?php echo base_url('assets/uploads/signs/invoice_').$db['invoice_sign']; ?>">
            <div style="clear: both;"></div>
        <?php else: ?>
            <script type="text/javascript">
                $(document).ready(function() {
                    $("#signature").jSignature();
                });
            </script>

            <label id="signature_label"><?php echo lang('Customer Signature (Please sign below)');?></label>
            <div id="signature"></div>
            <input type="hidden" name="sign_id" id="sign_id" value="">
            <button id="submit_sign" class="no-print btn-icon btn btn-primary btn-icon pull-right"><?php echo lang('Save');?></button>
            <button id="reset_sign" class="no-print btn-icon btn btn-primary btn-icon pull-left"><?php echo lang('Reset');?></button>
        <?php endif; ?>
    </div>

    <div id="<?php echo ($db['invoice_sign'] && $db['invoice_sign'] !== '') ? 'no-print' : 'print-only';?>" style="display: none;">
        <label><?php echo lang('Customer Signature (Please sign below)');?></label>
        <div class="col-md-6" style="border-bottom: 2px solid black; width: 50%; margin-top: 80px;"></div>
    </div>
    
    <br><br>
    <br><br>
    <br><br>
    <div id="print_button"><?php echo $this->lang->line('print');?></div>

    </body>

<script>

     jQuery("#reset_sign").on("click", function (e) {
            $("#signature").jSignature('reset');
        });

        jQuery("#submit_sign").on("click", function (e) {
            var datapair = $('#signature').jSignature("getData", 'base30');
            datapair = 'data='+(datapair[1])+'&id=<?php echo $db['id'];?>';
            jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>panel/misc/save_invoice_signature",
                data: datapair,
                cache: false,
                success: function (data) {
                    location.reload();
                }
            });
        });
    jQuery(document).on("click", "#print_button", function() {
        window.print();
        setInterval(function() {
            window.close();
        }, 500);
    });
    function auto_grow(element) {
        element.style.height = "5px";
        element.style.height = (element.scrollHeight)+"px";
    }
    auto_grow(document.getElementById("comment"));
</script>
<style type="text/css">
    @media print {
        html, body {
            height: 99%;    
            font-size: 12px;
        }

        .no-print {
            display: none;
        }

        #print-only {
            display: block !important;
        }
    }
</style>

</html>

