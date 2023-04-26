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
		<div class="invoice-logo" style="background-image: url('<?php echo base_url();?>assets/uploads/logos/<?php echo $settings->logo;?>')"></div><!-- LOGO -->
       
        <div class="invoice-from"><!-- HEADER FROM -->
            <div class="org"><?php echo escapeStr($settings->title);?>, <?php echo escapeStr($settings->address);?>, <?php echo escapeStr($settings->city);?> <?php echo escapeStr($settings->zipcode);?></div>
            <div class="org">Phone: <?php echo escapeStr($settings->phone);?></div>
				<a class="email" href="mailto:<?php echo escapeStr($settings->invoice_mail); ?>">E-mail: <?php echo escapeStr($settings->invoice_mail); ?></a>
		</div><!-- HEADER FROM -->

	</header><!-- HEADER -->
	<!-- e: invoice header -->
  
    <div class="this-is-line">
    	<div class="this-is">INVOICE</div><!-- DOC TITLE -->
	</div>

	<section id="info-to"><!-- TO SECTION -->

		<div class="invoice-to-title">INVOICE TO</div><!-- INVOICE TO -->
        <div class="invoice-to">



			<div class="to-org"><?= ($customer ? ($customer->first_name . ' ' . $customer->last_name . '<br> ' . $customer->address .'<br>' . ' <br>' . $customer->postal_code . ($customer->city !== '' ? ' - ' . $customer->city : '')) : lang('walk_in')) ;?></div>
            <div class="to-phone"><?=$customer ? $customer->telephone : ''?></div>
			<a class="to-email" href="mailto:<?=$customer ? $customer->email : ''?>">
                <?=$customer ? $customer->email : ''?></a>
		</div><!-- INVOICE TO -->

		<div class="invoice-meta">
			<div class="meta-uno invoice-number">Invoice No:</div>
			<div class="meta-duo"><?=$inv->reference_no;?></div>
			<div class="meta-uno invoice-date">Invoice Date:</div>
			<div class="meta-duo"><?php echo date('d F Y', strtotime($inv->date));?></div>
			<div class="meta-uno invoice-due">Total Due:</div>
			<div class="meta-duo"><?=$this->repairer->formatMoney($inv->grand_total - $inv->paid);?></div>
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

					<?php
                        $r = 1;
                        foreach ($rows as $row) : ?>
                        	<?php
                                $subtitle = '';
                                if ($row->item_type == 'drepairs') {
                                    $subtitle = ' - '.lang('Completed Repair');
                                }elseif ($row->item_type == 'crepairs') {
                                    $subtitle = ' - '.lang('Repair Deposit');
                                }elseif ($row->item_type == 'plans') {
                                    $subtitle = ' - '.preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $row->phone_number);
                                }elseif ($row->item_type == 'repair') {
                                    $subtitle = ' - '.lang('Part Only');
                                }
                            ?>
                        	<tr>
								<th>
		                        	<h1><?php echo product_name(escapeStr($row->product_name) . $subtitle, null); ?> <br>
                                    <?php $warranty = json_decode($row->warranty);
                                        if ($warranty) {
                                            $expire = date('Y-m-d H:i:s', strtotime($row->date. ' + '.$warranty->warranty_duration.' '.$warranty->warranty_duration_type.''));
                                            $from = strtotime($expire);
                                            $today = time();
                                            $difference = $today - $from;
                                            $days = floor($difference / 86400);
                                            $text = $days < 0 ? ($this->repairer->hrld($expire)) : lang('No Warranty');
                                        }else{
                                            $text = lang('No Warranty');
                                        }
                                    ?>
                                </h1>
		                            <p class="description"> 
			                            <?php echo lang('Under Warranty until');?>: <?php echo $text;  ?>
	                                    <?php if($warranty): ?>
	                                    	<?php echo lang('Warranty Details');?>: <?php echo $warranty->details; ?>
	                                    <?php endif; ?>
                                    </p>
		                        </th>
								<td><?php echo $this->repairer->formatQuantity($row->quantity); ?></td>
								<td><?php echo $this->repairer->formatMoney($row->unit_price); ?></td>
		                        <td><?php echo $this->repairer->formatMoney($row->subtotal); ?></td>
							</tr>
							

                       <?php $r++; endforeach; ?>

					
				</tbody>
				
			</table>
		</div><!-- INVOICE ITEMS -->
        
		<div class="lower-block"><!-- TERMS&PAYMENT INFO -->
        
            <div class="info">
                <div class="info-time"><strong>Pay by:</strong> 
                	<?php 
                	$pay = array(); 
                	foreach ($payments as $payment) {
                		$pay[] = lang($payment->paid_by);
                	} ?>
                	<?= implode($pay, ', '); ?>
                </div>
                <div class="info-payment"><strong>Terms</strong><br>
    Sime omnimag natibus es nis eum re prepuditest, tem que numqui doluptas sinvel mod eos rem fuga. Ribus es ailiqui il maiori sit unti sit et lam quam volum</div>
                <div class="info-terms"><strong>Terms & Conditions</strong><br>
    Sime omnimag natibus es nis eum re prepuditest, tem que numqui doluptas sinvel mod eos rem fuga. Ribus es ailiqui il maiori sit unti sit et lam quam volum
                </div>
            </div>

            <div class="invoice-totals"><!-- TOTALS -->
                <table>
                    <tbody>
                        <tr>
                            <th>Subtotal</th>						
                            <td><?php echo $this->repairer->formatMoney(($inv->grand_total - $inv->total_tax));?></td>
                        </tr>
                        <tr>
                            <th>Tax</th>						
                            <td><?php echo $this->repairer->formatMoney($inv->total_tax);?></td>
                        </tr>
                        <tr>
                            <th>Discount</th>						
                            <td><?php echo $this->repairer->formatMoney(($inv->total_discount));?></td>
                        </tr>
                        <tr>
                            <th class="col-1">Total Due:</th>						
                            <td class="col-2"><?php echo $this->repairer->formatMoney($inv->grand_total);?></td>
                        </tr>
                    </tbody>
                </table>
    
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