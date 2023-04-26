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
        <link href="" rel="stylesheet">
        <script src="<?php echo site_url('assets/plugins/jQuery/jquery-2.2.3.min.js'); ?>"></script>
        <!--[if lt IE 9]>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/vendors/jSignature/flashcanvas.js"></script>
        <![endif]-->
        <script src="<?php echo base_url(); ?>assets/js/vendors/jSignature/jSignature.min.js"></script>
        <style type="text/css">
            <?php echo file_get_contents(FCPATH.'assets/css/invoice.css'); ?>
        </style>
        <script>
            $(document).ready(function() {
                $("#signature").jSignature()
            })
           

        </script>
    </head>


    <body>

        <div class="halfinvoice" >

            <header class="clearfix">
                <img align="left" src="<?php echo base_url() ?>panel/inventory/gen_barcode/<?php echo $db['id']; ?>/code39/30/0/1" alt="<?php echo $db['id'] ?>"/>
                <div id="company" contentEditable="false">
                    <h2 class="name"><?php echo escapeStr($settings->title); ?></h2>
                </div>
            </header>
            <main>
                <div id="details" class="clearfix">

                    <div id="client" contentEditable="false">

                        <div class="to"><?php echo $this->lang->line('client_title');?>:</div>

                        <h2 class="name"><?php echo escapeStr($db['name']); ?></h2>
                        <h2 class="name"><?php echo escapeStr($db['telephone']); ?></h2>

                    </div>
                   

                    <div id="invoice" contentEditable="false">

                        <h1><?php echo $this->lang->line('report').' '.escapeStr($db['model_name']);?></h1>

                        <div class="date"><?php echo $this->lang->line('date_opening');?> <?php echo date_format(date_create($db['date_opening']),"m/d/Y"); ?></div>
                        

                    </div>

                </div>


                <div class="clearfix"></div>
                <div id="dati">

				<?php if($db['model_name']): ?>
                    <div class="col"><b class="b"><?php echo $this->lang->line('model_name');?>:</b> <?php echo escapeStr($db['model_name']);?></div>
					<?php else: ?>
					
					<?php endif; ?>
<?php if($db['category']): ?>
                    <div class="col"><b class="b"><?php echo $this->lang->line('repair_category');?>:</b> <?php echo escapeStr($db['category']);?></div>
<?php else: ?>
					
					<?php endif; ?>
					<?php if($db['defect']): ?>
                    <div class="col"><b class="b"><?php echo $this->lang->line('repair_defect');?>:</b> <?php echo escapeStr($db['defect']);?></div>
					<?php else: ?>
					
					<?php endif; ?>

                    <div class="col"><b class="b"><?php echo $this->lang->line('repair_advance');?>:</b> <?php echo escapeStr($db['advance']);?></div>

                    <div class="col"><b class="b"><?php echo $this->lang->line('grand_total');?>:</b><?php echo escapeStr($db['grand_total']);?></div>

                    <div class="col"><b class="b">Repair <?php echo $this->lang->line('code');?>:</b> <?php echo escapeStr($db['code']);?></div>
                 
					<div class="col"><b class="b">Serial Number:</b><?php echo $db['serial_number'];?></div>
				
					
					<?php if($db['pin_code']): ?>
						<div class="col"><b class="b">Pin Code:</b> <?php echo escapeStr($db['pin_code']);?></div>
						<?php else: ?>
					
					<?php endif; ?>
					<?php if($db['pattern']): ?>
					<div class="col"><b class="b">Pattern:</b> <?php echo escapeStr($db['pattern']);?></div>
					<?php else: ?>
					
					<?php endif; ?>
					
					

					
					 <?php $custom_toggles = explode(',', $settings->repair_custom_toggles);

                    $value = json_decode($db['custom_toggles'], true);
					
					
                    foreach($custom_toggles as $line1){ ?>
					<div class="col"><b class="b"> <?php echo $line1; ?> :</b> <?php echo preg_replace(
					array('/0/', '/1/'),
					array('Working', 'Not Working'),
					$value[bin2hex($line1)]
					); ?>
					</div>

                    <?php } ?>


                    <?php if($db['warranty'] && json_decode($db['warranty']) && isset(json_decode($db['warranty'])->details)): ?><div class="col txt" style="padding: 5px;width: calc(100% - 10px);font-size: 10px;">
                        <strong><?php echo lang('Warranty Details');?>:</strong> <?php echo json_decode($db['warranty'])->details; ?>
                    </div>
                <?php endif; ?>
                    <div style="clear: both;"></div>
                    <div class="col txt"><?php echo lang('Technician Comments');?>: <?php echo $db['comment']; ?></div>
                    <div style="clear: both;"></div>
					<div class="col txt" style="padding: 5px;width: calc(100% - 10px);font-size: 10px;">
                            <?php echo escapeStr($settings->disclaimer);?>
                    </div>
					
                    <div style="clear: both;"></div>
                    
                    <div class="well" style="width:100%;height:200px;background-color:lightgrey;color:black;border:5px;font:11px/15px;align-content:left  sans-serif;">
                        <?php if($db['sign']): ?>
                            <label><?php echo lang('Customer Signature');?>: </label>
                            <img height="200px" src="<?php echo base_url('assets/uploads/signs/').$db['sign']; ?>">
                        <?php else: ?>
                            <?php if(!$pdf): ?>
                                <label><?php echo lang('Customer Signature (Please sign below)');?></label>
                                <div id="signature"></div>
                                <button id="submit" class="btn btn-primary pull-right"><?php echo lang('Save');?></button>
                                <button id="reset" class="btn btn-primary pull-left"><?php echo lang('Reset');?></button>
                            <?php endif; ?>
                        <?php endif; ?>
                    <div style="clear: both;"></div>
                    </div>
                </div>
            </main>
            <footer>
            </footer>
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

        jQuery("#reset").on("click", function (e) {
            $("#signature").jSignature('reset');
        });
        jQuery("#submit").on("click", function (e) {
            var datapair = $('#signature').jSignature("getData", 'base30');
            datapair = 'data='+(datapair[1])+'&id=<?php echo $db['id'];?>';
            jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>panel/repair/save_signature",
                data: datapair,
                cache: false,
                success: function (data) {
                    location.reload();
                }
            });

        });

    </script>

</html>