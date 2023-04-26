<?php
$tax = $db['tax'];
$price_without_tax = $db['total']; // PRICE WITHOUT TAX
$total = $db['grand_total']; // PRICE WITH TAX
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php echo $this->lang->line('report');?></title>
        <link href="<?php echo $assets ?>dist/css/custom/invoice.css" rel="stylesheet">
        <!-- jQuery 2.2.3 -->
        <script src="<?php echo $assets ?>bower_components/jquery/dist/jquery.min.js"></script>
        <!-- Accounting.js -->
        <script src="<?php echo $assets ?>plugins/custom/accounting.min.js"></script>
    </head>

    <body>
        <div id="editable_invoice"><?php echo $this->lang->line('editable_invoice');?></div>
        <div class="halfinvoice">
            <header class="clearfix">
                <div id="logo">
                    <img src="<?php echo base_url(); ?>assets/uploads/logos/<?php echo $settings->logo; ?>">
                </div>
                 <div id="company" contentEditable="true">
                    <h2 class="name"><?php echo escapeStr($this->activeStoreData->name); ?></h2>
                    <div><?php echo escapeStr($this->activeStoreData->address); ?></div>
                    <div><?php echo escapeStr($this->activeStoreData->city); ?> <?php echo escapeStr($this->activeStoreData->zipcode); ?></div>
                    <div><?php echo escapeStr($this->activeStoreData->phone); ?></div>
                    <div><a href="mailto:<?php echo escapeStr($this->activeStoreData->invoice_mail); ?>"><?php echo escapeStr($this->activeStoreData->invoice_mail); ?></a></div>
                    <div><?php echo escapeStr($settings->vat); ?></div>
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
                                if(isset($client->cf) && $client->cf !== '') {
                                    if($ve) echo ' / ';
                                    echo  escapeStr($client->cf);
                                }
                            ?>
                        </div>
                    </div>
                    <div id="invoice" contentEditable="true">
                        <h1><?php echo $this->lang->line('report').' '.$db['category'];?></h1>
                        <div class="date"><?php echo $this->lang->line('date_opening');?>: <?php echo date_format(date_create($db['date_opening']),"Y/m/d"); ?></div>
                    </div>
                </div>

                <div id="dati">
                    <div class="col"><b><?php echo $this->lang->line('model_name');?>:</b> <?php echo escapeStr($db['model_name']);?></div>
                    <div class="col"><b><?php echo $this->lang->line('repair_category');?>:</b> <?php echo escapeStr($db['category']);?></div>
                    <div class="col"><b><?php echo $this->lang->line('repair_defect');?>:</b> <?php echo escapeStr($db['defect']);?></div>
                    <div class="col"><b><?php echo $this->lang->line('total');?>:</b><?php echo $this->repairer->formatMoney($db['total'] + $db['service_charges']);?></div>
                    <div class="col"><b><?php echo $this->lang->line('tax');?>:</b><?php echo $this->repairer->formatMoney($db['tax']);?></div>
                    <div class="col"><b><?php echo $this->lang->line('grand_total');?>:</b><?php echo $this->repairer->formatMoney($db['grand_total']);?></div>
                    <div class="col"><b><?php echo $this->lang->line('paid');?>:</b><?php echo $this->repairer->formatMoney($db['advance']);?></div>
                    <div class="col"><b><?php echo $this->lang->line('balance');?>:</b><?php echo $this->repairer->formatMoney($db['grand_total'] - $db['advance'] - $db['paid']);?></div>
                    <div class="col"><b><?php echo $this->lang->line('code');?>:</b> <?php echo escapeStr($db['code']);?></div>
                    <?php $custom_fields = explode(',', $settings->custom_fields);
                    if (!empty(array_filter($custom_fields))) {
                        $value = json_decode($db['custom_field'], true);
                        foreach($custom_fields as $line){ 
                             if(!empty(array_filter($value))): ?>
                                <div class="col"><b> <?php echo escapeStr($line); ?> :</b> <?php echo escapeStr($value[bin2hex($line)]); ?></div>
                    <?php 
                            endif;
                        } 
                    }
                    ?>
                    <div class="col txt"><textarea id="comment" onkeyup="auto_grow(this)" contentEditable="true"><?php echo escapeStr($db['comment']); ?></textarea></div>
                    <div style="clear: both;"></div>
                </div>
                <?php echo escapeStr($settings->disclaimer); ?>

            </main>
            <div id="print_button"><?php echo $this->lang->line('print');?></div>
        </div>
    </body>

    <script>
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

</html>

