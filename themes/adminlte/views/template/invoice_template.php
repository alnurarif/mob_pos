<?php
$tax = $db['tax'];
$price_without_tax = $db['total']; // PRICE WITHOUT TAX
$total = $db['grand_total']; // PRICE WITH TAX
$advance = number_format($db['advance'], 2);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php echo $this->lang->line('invoice');?></title>

        <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo base_url(); ?>assets/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo base_url(); ?>assets/css/dripicons.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo base_url(); ?>assets/css/animate/animate.css" rel="stylesheet" type="text/css">
        <link href="<?php echo base_url(); ?>assets/css/hover/hover-min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo base_url(); ?>assets/css/styles.css" rel="stylesheet" type="text/css">
        <script src="<?php echo site_url('assets/plugins/jQuery/jquery-2.2.3.min.js'); ?>"></script>
        <script src="<?php echo base_url();?>assets/dist/js/accounting.min.js"></script>
        <style type="text/css">
            
            #print_button {
                height 50px;
                width: 50%;
                line-height: 50px;
                position: fixed;
                left: 25%;
                bottom: 0px;
                color: white;
                font-weight: bold;
                text-align: center;
                font-size: 17px;
                border-top-left-radius: 15px;
                border-top-right-radius: 15px;
                cursor: pointer;
                background-color: crimson;

            }

#comment {
    border: 1px solid #EEEEEE;
    border-top: 0;
    height: 100%;
    font-size: 13px;
    text-align: left;
}
            #print_button:hover {
                background-color: #3A3A3A;
            }

            #editable_invoice {
                position: fixed;

                top: 15px;
                left: 0;
                background-color: rgba(239, 255, 148, 0.95);
                padding: 10px;
                color: black;
                border: 3px solid #C4E606;
                border-left: 0;
                border-top-right-radius: 5px;
                border-bottom-right-radius: 5px;
                box-shadow: #A7C308 0px 0px 11px -4px;
            }
            @media print {
                #editable_invoice {display: none;}  
                #print_button {display: none;}  
                .halfinvoice.seconda {display: block;}
                .show {width: 100% !important;}
            }
        </style>


        <script>



            function formatDecimal(x, d) {

                if (!d) { d = 2; }

                return parseFloat(accounting.formatNumber(x, d, ',', '.'));


            }

        </script>

    </head>

    

    <body>
        <div class="show" style="width: 70%; margin: 0 auto;">
                            <div class="row">
                                <div class="col-md-11 col-sm-10 col-xs-9 ui-sortable">
                                    <h3 class="margin-top-0"><?php echo $this->lang->line('invoice_n');?> #<?php echo str_pad($db['id'], 4, '0', STR_PAD_LEFT); ?></h3>
                                    <img src="<?php echo base_url() ?>panel/inventory/gen_barcode/<?php echo $db['id']; ?>/code128/40/0" alt="<?php echo $db['id'] ?>"/>
                                    <ul class="list-unstyled">
                                        <li><strong>Invoice date:</strong>  <?php echo date_format(date_create($db['date_opening']),"m/d/Y"); ?></li>
                                    </ul>
                                </div>
                                <div class="col-md-1 col-sm-2 col-xs-3 ui-sortable"><img class="img-responsive" src="<?php echo site_url().'/assets/uploads/logos/'.$settings->logo;?>" alt="Logo"></div>
                            </div>
                            <div class="well">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <h5>From:</h5>
                                        <address>
                                            <?php echo lang('Location');?>: <?php echo escapeStr($this->activeStoreData->name); ?><br>
                                            <?php echo escapeStr($this->activeStoreData->address); ?><br>
                                            <?php echo escapeStr($this->activeStoreData->city); ?>, <?php echo escapeStr($this->activeStoreData->state); ?> <?php echo escapeStr($this->activeStoreData->zipcode); ?><br>
                                            <abbr title="Phone">P:</abbr> <?php echo escapeStr($this->activeStoreData->phone); ?>
                                        </address>

                                    </div>
                                    <div class="col-xs-6">
                                        <h5>To:</h5>
                                        <address>
                                            <strong><?php echo escapeStr($db['name']); ?></strong><br>
                                            <?php echo escapeStr($client->address); ?><br>
                                            <abbr title="Phone">P:</abbr> <?php echo preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $client->telephone); ?>
                                        </address>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-horizontal-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product/Product description</th>
                                            <th>Qty</th>
                                            <th>Unit Price </th>
                                            <th>Tax</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php $a = 1; if($items) { foreach ($items as $item): ?>
                                        <tr>
                                            <td><span class="circle-nums"><?php echo str_pad($a, 2, '0', STR_PAD_LEFT); ?></span></td>
                                            <td>
                                                <h4 class="margin-0"><?php echo escapeStr($item->product_name); ?> (<?php echo escapeStr($item->product_code); ?>)</h4>
                                            </td>
                                            <td>1</td>
                                            <td><?php echo escapeStr($settings->currency) ?> <?php echo number_format($item->unit_price, 2);?></td>
                                            <td><?php echo escapeStr($settings->currency) ?> <?php echo number_format($item->tax, 2);?></td>
                                            <td><?php echo escapeStr($settings->currency) ?> <?php echo number_format($item->subtotal, 2);?></td>
                                        </tr>
                                     <?php $a++; endforeach; }else{ ?>
                                        <tr>
                                            <td class="no">1</td>
                                            <td class="desc">No Products Used</td>
                                            <td class="unit"><?php echo escapeStr($settings->currency) ?> <?php echo number_format(0, 2);?></td>
                                            <td class="qty"><?php echo number_format(0, 1);?></td>
                                            <td class="tax"><?php echo number_format(0, 1);?></td>
                                            <td class="total"><?php echo escapeStr($settings->currency) ?> <?php echo number_format(0, 2);?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                    <tfoot>
                                
                                        <tr>
                                            <td class="noborders" colspan="3" rowspan="6"></td>
                                            <td colspan="2">Subtotal</td>
                                            <td colspan="2"><?php echo escapeStr($settings->currency) ?> <?php echo number_format($price_without_tax, 2);?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Tax</td>
                                            <td colspan="2"><?php echo escapeStr($settings->currency) ?> <?php echo number_format($tax, 2);?></td>
                                        </tr>
                                         <tr>
                                            <?php if($db['service_charges'] > 0): ?>
                                                <td colspan="2"><?php echo $this->lang->line('repair_service_charges');?></td>
                                                <td colspan="2"><?php echo escapeStr($settings->currency) ?> <?php echo number_format($db['service_charges'], 2); ?></td>
                                            <?php endif;?>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><?php echo $this->lang->line('total');?></td>
                                            <td colspan="2"><?php echo escapeStr($settings->currency) ?> <?php echo number_format($total, 2);?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td colspan="2"><?php echo lang('repair_advance'); ?> </td>
                                            <td colspan="2"><?php echo escapeStr($settings->currency) ?> <?php echo number_format($advance, 2);?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><?php echo lang('payable'); ?></td>
                                            <td colspan="2"><?php echo number_format(($total - $advance), 2) ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <?php if($db['warranty'] && json_decode($db['warranty']) && isset(json_decode($db['warranty'])->details)): ?><div class="col txt" style="padding: 5px;width: calc(100% - 10px);font-size: 10px;">
                                <div class="well">
                                    <strong>Warranty Details:</strong><br>
                                    <?php echo escapeStr(json_decode($db['warranty'])->details); ?>
                                </div>
                            <?php endif; ?>

                            <?php echo $settings->disclaimer; ?>
                        </div>

                        <div class="print_button" id="print_button"><?php echo $this->lang->line('print');?></div>
                        <a href="<?php echo (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url('panel/pos')); ?>" id="editable_invoice"><< Back To POS</a>
       </body>
    <script>
        jQuery(document).on("click", "#print_button", function() {
            window.print();
            setInterval(function() {
                window.close();
            }, 500);
        });
    </script>
</html>



