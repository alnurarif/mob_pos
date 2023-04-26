<!doctype html>
<html dir="ltr" lang="en" class="no-js">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width" />

	<title>Facture PDF</title>
	<link rel="stylesheet" href="<?=$assets;?>dist/invoice/reset.css" media="all" />
	<link rel="stylesheet" href="<?=$assets;?>dist/invoice/pdf_style.css" media="all" />
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
			<div class="org"><?php echo escapeStr($settings->title);?><br> <?php echo escapeStr($settings->address);?><br> <?php echo escapeStr($settings->zipcode);?> - <?php echo escapeStr($settings->city);?></div>
            <div class="org"><?=lang('tel');?> : <?php echo escapeStr($settings->phone);?></div>
                <a class="email"><?=lang('site_url');?> <?=base_url();?></a><br>
                <a class="email" href="mailto:<?php echo escapeStr($settings->invoice_mail); ?>"><?=lang('email');?> : <?php echo escapeStr($settings->invoice_mail); ?></a>
		

</div><!-- HEADER FROM -->

	</header><!-- HEADER -->
	<!-- e: invoice header -->
    <hr>
    <div class="this-is-line">
        <div class="this-is"><?=lang('invoice');?></div><!-- DOC TITLE -->
	</div>

	<section id="info-to"><!-- TO SECTION -->

        <div class="invoice-to-title"><?=lang('invoice_to');?></div><!-- INVOICE TO -->
        <div class="invoice-to">
			<div class="to-org"><?= ($customer ? ($customer->first_name . ' ' . $customer->last_name . '<br> ' . $customer->address .'<br>' . $customer->postal_code . ($customer->city !== '' ? ' - ' . $customer->city : '')) : lang('walk_in')) ;?></div>
            <div class="to-phone"><?=$customer ? $customer->telephone : ''?></div>
			<div class="to-email"><?=$customer ? $customer->email : ''?></div>
		</div><!-- INVOICE TO -->

		<div class="invoice-meta">
            <div class="meta-uno invoice-number"><?=lang('invoice_no');?>:</div>
			<div class="meta-duo"><?=str_replace('SALE/', '', $inv->reference_no);?></div>
        <div class="meta-uno invoice-date"><?=lang('date');?> :</div>
            <?php
            if ($settings->language == 'french') {
                setlocale (LC_TIME, 'fr_FR.utf8','fra');  ?>
                    <div class="meta-duo"><?php echo strftime('%d %B %Y', strtotime($inv->date));?></div>
                <?php
            }else{
                ?>
                    <div class="meta-duo"><?php echo date('d F Y', strtotime($inv->date));?></div>
                <?php
            }
            ?>
            <div class="meta-uno invoice-due"><?=lang('balance');?>:</div>
			<div class="meta-duo"><?=$this->repairer->formatMoney($inv->grand_total - $inv->paid);?></div>
		</div>

	</section><!-- TO SECTION -->

	<section class="invoice-financials"><!-- FINANCIALS SECTION -->

		<div class="invoice-items"><!-- INVOICE ITEMS -->
			<table>
				<thead>
					<tr>
                        <th class="col-1"><?=lang('item_title');?></th>
                        <th class="col-2"><?=lang('qty');?></th>
                        <th class="col-3"><?=lang('Unit Price');?></th>
                        <th class="col-4"><?=lang('Total');?></th>
                    </tr>
				</thead>
				<tbody>

					<?php
                        $r = 1;
                        foreach ($rows as $row) : ?>
                        	<?php
                                $subtitle = '';
                                if ($row->item_type == 'drepairs') {
                                    ;
                                }elseif ($row->item_type == 'crepairs') {
                                    $subtitle = ' - '.lang('Repair Deposit');
                                }elseif ($row->item_type == 'plans') {
                                    $subtitle = ' - '.preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $row->phone_number);
                                }elseif ($row->item_type == 'repair') {
                                    ;
                                }elseif ($row->item_details !== '') {
                                    $subtitle = ' - '.$row->item_details.'';
                                }

                                $name = escapeStr($row->product_name);
                                if ($row->item_type == 'drepairs' || $row->item_type == 'crepairs') {
                                    $repair = $this->repair_model->getRepairByID($row->product_id);
                                    $name = $repair['defect'] . ' - ' . $repair['model_name'];
                                }


                            ?>
                        	<tr>
								<th>
		                        	<h1><?php echo $name . $subtitle; ?> <br>
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
			                            <?php if($warranty): ?>
	                                    	<?php echo lang('Warranty Details');?>: <?php echo $warranty->details; ?>
	                                    <?php endif; ?>
                                    </p>
		                        </th>
                                <?php if($row->has_items):?>
                                    <td colspan="3"></td>
                                <?php else:?>
    								<td><?php echo $this->repairer->formatQuantity($row->quantity); ?></td>
    								<td><?php echo $this->repairer->formatMoney($row->unit_price); ?></td>
    		                        <td><?php echo $this->repairer->formatMoney($row->subtotal); ?></td>
                                <?php endif;?>

							</tr>
							

                       <?php $r++; endforeach; ?>

					
				</tbody>
				
			</table>
		</div><!-- INVOICE ITEMS -->
        
		<div class="lower-block"><!-- TERMS&PAYMENT INFO -->
        
		<div class="invoice-totals"><!-- TOTALS -->


                <table>
                    <tbody>
                        <tr>
                            <th><?=lang('subtotal');?></th>                       
                            <td><?php echo $this->repairer->formatMoney(($inv->grand_total + $inv->total_discount - $inv->total_tax));?></td>
                        </tr>

                        <tr>
                            <th><?=lang('tax');?></th>                       
                            <td><?php echo $this->repairer->formatMoney(($inv->total_tax));?></td>
                        </tr>
                        
                        <tr>
                            <th><?=lang('discount');?></th>                       
                            <td><?php echo $this->repairer->formatMoney(($inv->total_discount));?></td>
                        </tr>
                        <tr>
                            <th class="col-1"><?=lang('grand_total');?></th>                        
                            <td class="col-2"><?php echo $this->repairer->formatMoney($inv->grand_total);?></td>
                        </tr>
                    </tbody>
                </table>
    
            </div><!-- TOTALS -->
			
            <div class="info">
                <div class="info-time"><strong><?=lang('payment_list');?> :</strong> 
                	<?php 
                	$pay = array(); 
                    if ($payments) {
                        foreach ($payments as $payment) {
                            $pay[] = $this->repairer->formatMoney($payment->amount) . ' by ' . lang($payment->paid_by);
                        } 
                        echo implode($pay, ', ');
                    }else{
                        echo lang('invoice_not_paid');
                    }
                	?>
                </div>
                
                <div class="info-terms">
                        <?=$settings->disclaimer_sale;?>
                    </div>  
				</div>
								
            
          
		</div><!-- TERMS&PAYMENT INFO -->
        
	</section><!-- FINANCIALS SECTION -->


	
</div><!-- INVOICE -->



</body>
</html>