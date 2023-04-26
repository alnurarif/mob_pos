<!doctype html>
<html dir="ltr" lang="en" class="no-js">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width" />

	<title>sprSimple Invoice</title>

	<link rel="stylesheet" href="<?=$assets;?>dist/invoice/reset.css" media="all" />
	<link rel="stylesheet" href="<?=$assets;?>dist/invoice/style.css" media="all" />
	<link rel="stylesheet" href="<?=$assets;?>dist/invoice/print.css" media="print" />

	<!-- give life to HTML5 objects in IE -->
	<!--[if lte IE 8]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<!-- js HTML class -->
	<script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>
</head>
<body>
<!-- begin markup -->



<div id="invoice" class="new"><!-- INVOICE -->

	<header id="header"><!-- HEADER -->
		<div class="invoice-logo"><img height="70" src="<?php echo base_url();?>assets/uploads/logos/<?php echo $settings->logo;?>"></div><!-- LOGO -->
       
        <div class="invoice-from"><!-- HEADER FROM -->
			<div class="org"><h2><?php echo escapeStr($this->activeStoreData->name);?></h2><?php echo escapeStr($this->activeStoreData->address);?></div>
                    <div><?php echo escapeStr($this->activeStoreData->city); ?> <?php echo escapeStr($this->activeStoreData->zipcode); ?></div>

            <div class="org">Phone: <?php echo escapeStr($this->activeStoreData->phone);?></div>
				<a class="email" href="mailto:<?php echo escapeStr($this->activeStoreData->invoice_mail); ?>">E-mail: <?php echo escapeStr($this->activeStoreData->invoice_mail); ?></a>
            <div class="org"><?php echo escapeStr($settings->vat);?></div>

		</div><!-- HEADER FROM -->

	</header><!-- HEADER -->
	<!-- e: invoice header -->
  
    <div class="this-is-line">
    	<div class="this-is">INVOICE</div><!-- DOC TITLE -->
	</div>

	<section id="info-to"><!-- TO SECTION -->

		<div class="invoice-to-title">INVOICE TO</div><!-- INVOICE TO -->
        <div class="invoice-to">
			<div class="to-org"><strong><?php echo escapeStr($client->name);?></strong> <br><?php echo escapeStr($client->address);?><br><?php echo ($client ?  escapeStr($client->city) : ''); ?> <?php echo ($client ? escapeStr($client->postal_code ): ''); ?></div>
            <div class="to-phone"><?php echo escapeStr($client->telephone);?></div>
			<a class="to-email" href="mailto:<?php echo escapeStr($client->email);?>">
                <?php echo escapeStr($client->email);?></a>
		</div><!-- INVOICE TO -->

		<div class="invoice-meta">
			<div class="meta-uno invoice-number">Invoice No:</div>
			<div class="meta-duo"><?php echo escapeStr($db['code']);?></div>
			<div class="meta-uno invoice-date">Invoice Date:</div>
			<div class="meta-duo"><?php echo date('dS F Y', strtotime($db['date_opening']));?></div>
			<div class="meta-uno invoice-due">Total Due:</div>
			<div class="meta-duo"><?php echo $settings->currency;?> <?php echo ($db['pos_sold']) ? '0' : escapeStr($db['grand_total'] - $db['advance']);?></div>
		</div>

	</section><!-- TO SECTION -->
	<section class="invoice-financials"><!-- FINANCIALS SECTION -->

		<div class="invoice-items"><!-- INVOICE ITEMS -->
			<table>
				<thead>
					<tr>
						<th class="col-1">Item &amp; Description</th>
						<th class="col-2">Quantity</th>
						<th class="col-3">Unit Price</th>
                        <th class="col-4">Total</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>
                    
                        	<h1><?php echo $db['defect'];?> (<?php echo $db['model_name'];?>)</h1>
                            <p class="description"><?php echo $db['public_note'];?></p>
                        </th>
						<td>1</td>
						<td><?=$this->repairer->formatMoney($db['total'] + $db['service_charges']);?></td>
                        <td><?=$this->repairer->formatMoney($db['total'] + $db['service_charges']);?></td>
					</tr>
					
				</tbody>
				
			</table>
		</div><!-- INVOICE ITEMS -->
        
		<div class="lower-block"><!-- TERMS&PAYMENT INFO -->
        
            <div class="info">
                <!-- <div class="info-time"><strong>Pay by:</strong> Credit Card</div> -->
               
                <div class="info-terms" style="margin-top: 0;"><strong>Terms & Conditions</strong><br>
    				<?php echo escapeStr($settings->disclaimer); ?>
                </div>
            </div>

            <div class="invoice-totals"><!-- TOTALS -->
                <table>
                    <tbody>
                        <tr>
                            <th>Subtotal</th>						
                            <td><?=$this->repairer->formatMoney($db['total'] + $db['service_charges']);?></td>
                        </tr>
                        <tr>
                            <th>Tax <?php echo $tax_rate ? $tax_rate->name : '' ?></th>
                            <td><?=$this->repairer->formatMoney($db['tax']);?></td>
                        </tr>
                        <tr>
                            <th>Advance</th>						
                            <td><?=$this->repairer->formatMoney($db['advance']);?></td>
                        </tr>
                        <tr>
                            <th class="col-1">Total Due:</th>						
                            <td class="col-2"><?=($db['pos_sold']) ? '0' : escapeStr($db['grand_total'] - $db['advance']);?></td>
                        </tr>
                    </tbody>
                </table>
         		<div style="margin-top:10px">
	         		<?php echo $this->repairer->barcode($db['code'], 'code128', 50, false); ?>
	         	</div>
    
             
            </div><!-- TOTALS -->
           
		</div><!-- TERMS&PAYMENT INFO -->
        
	</section><!-- FINANCIALS SECTION -->

	<footer id="footer"><!-- FOOTER -->
		<div class="footer-mail"><?php echo escapeStr($settings->invoice_mail); ?></div>
        <div class="footer-phone"><?php echo escapeStr($settings->phone); ?></div>
        <div class="footer-web"><?=base_url();?></div>
	</footer><!-- FOOTER -->

</div><!-- INVOICE -->

</body>
</html>