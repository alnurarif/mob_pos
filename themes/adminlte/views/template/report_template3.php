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
        <link href="<?php echo $assets ?>dist/css/custom/invoice2.css" rel="stylesheet">
        <!-- jQuery 2.2.3 -->

        <script src="<?php echo $assets ?>bower_components/jquery/dist/jquery.min.js"></script>

        <!-- Accounting.js -->

        <script src="<?php echo $assets ?>plugins/custom/accounting.min.js"></script>
        <style type="text/css">
            #invoice-POS {
              box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
              padding: 2mm;
              margin: 0 auto;
              width: 80mm;
              background: #FFF;
            }
            @media print {
                body * { visibility: hidden; }
                #invoice-POS * { visibility: visible; }
            }
            @page  
            { 
                size: auto;   /* auto is the initial value */ 
                margin: 0;  
            } 
        </style>
      </head>

    <body>
        <div id="editable_invoice"><?php echo $this->lang->line('editable_invoice');?></div>
        <div class="halfinvoice" id="invoice-POS">
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
          </header>
            <div style="text-align: center">
                <?php echo $this->repairer->barcode($db['code'], 'code128', 40, false); ?>
            </div>
            <div style="text-align: center"><?php echo escapeStr($db['telephone']); ?></div>
            <main>
                <div id="details" class="clearfix">
                    <div id="client" contentEditable="true">
                        <div class="to"><?php echo $this->lang->line('client_title');?>:</div>
                        <h2 class="name"><?php echo escapeStr($db['name']);?></h2>
                      </div>
                  </div>

                <div id="dati">
                    <div class="col"><b><?php echo $this->lang->line('date_opening');?>:</b> <?php echo date_format(date_create($db['date_opening']),"d/m/Y"); ?></div>
                    <div class="col"><b><?php echo $this->lang->line('model_name');?>:</b> <?php echo escapeStr($db['model_name']);?></div>
                    <div class="col"><b><?php echo $this->lang->line('reparation_category');?>:</b> <?php echo escapeStr($db['category']);?></div>
                    <div class="col"><b><?php echo $this->lang->line('reparation_defect');?>:</b> <?php echo escapeStr($db['defect']);?></div>
                    <div class="col"><b><?php echo $this->lang->line('grand_total');?>:</b> <?php echo $this->repairer->formatDecimal($db['grand_total']);?> <?php echo $this->mSettings->currency;?></div>
                    <div class="col"><b><?php echo $this->lang->line('advance');?>:</b> <?php echo $this->repairer->formatDecimal($db['advance']);?> <?php echo $this->mSettings->currency;?></div>
                    <div class="col"><b><?php echo $this->lang->line('balance');?>:</b> <?php echo $this->repairer->formatDecimal($db['grand_total'] - $db['advance']);?><?php echo $this->mSettings->currency;?></div>
                    <div class="col"><b><?php echo $this->lang->line('id');?>:</b> <?php echo escapeStr($db['id']);?></div> <!--ID ADDED -->
                    <div class="col"><b><?php echo $this->lang->line('code');?>:</b> <?php echo escapeStr($db['code']);?></div>
                    <div class="col"><b><?php echo $this->lang->line('reparation_imei');?>:</b> <?php echo escapeStr($db['imei']);?></div>
                    <?php $custom_fields = explode(',', $settings->custom_fields);
                        if (!empty(array_filter($custom_fields))) {
                            $value = json_decode($db['custom_field'], true);
                            foreach($custom_fields as $line){
                                 if(!empty(array_filter($value))): ?>
                                    <div class="col"><b> <?php echo $line; ?> :</b> <?php echo $value[bin2hex($line)]; ?></div>
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
