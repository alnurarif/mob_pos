<!DOCTYPE html>
<html>
<head>
	<title>Repair Reciept</title>
    <script src="<?php echo $assets;?>bower_components/jquery/dist/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $assets;?>/dist/css/custom/thermal.css">
	<style type="text/css">
		/*body   { font-family: 'Arial', sans-serif; font-weight: bold;}*/
		#invoice-POS {
		  box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
		  padding: 2mm;
		  margin: 0 auto;
		  width: 80mm;
		  background: #FFF;
		}
		#invoice-POS ::selection {
		  background: #f31544;
		  color: #FFF;
		}
		#invoice-POS ::moz-selection {
		  background: #f31544;
		  color: #FFF;
		}
		#invoice-POS h1 {
		  font-size: 1.5em;
		  color: #222;
		}
		#invoice-POS h2 {
		  font-size: .9em;
		}
		#invoice-POS h3 {
		  font-size: 1.2em;
		  /*font-weight: 300;*/
		  line-height: 2em;
		}
		#invoice-POS p {
		  font-size: .7em;
		  color: #666;
		  line-height: 1.2em;
		}
		#invoice-POS #top, #invoice-POS #mid, #invoice-POS #bot {
		  /* Targets all id with 'col-' */
		  border-bottom: 1px solid #EEE;
		}
		#invoice-POS #top {
		  min-height: 100px;
		}
		#invoice-POS #mid {
		  min-height: 80px;
		}
		#invoice-POS #bot {
		  min-height: 50px;
		}
		#invoice-POS #top .logo img {
		  height: 60px;
		  /*width: 60px;*/
		  /*background: url('') no-repeat;*/
		  /*background-size: 60px auto;*/
		}
		#invoice-POS .info {
		  display: block;
		  margin-left: 0;
		}
		#invoice-POS .title {
		  float: right;
		}
		#invoice-POS .title p {
		  text-align: right;
		}
		#invoice-POS table {
		  width: 100%;
		  border-collapse: collapse;
		}
		#invoice-POS .tabletitle {
		  font-size: .7em;
		  background: #EEE;
		}
		#invoice-POS .service {
		  border-bottom: 1px solid #EEE;
		}
		#invoice-POS .item {
		  width: 47mm;
		}
		#invoice-POS .itemtext {
		  font-size: .7em;
		}
		#invoice-POS #legalcopy {
		  margin-top: 5mm;
		}

		@media print {
			body * { visibility: hidden; }
			#invoice-POS * { visibility: visible; }
			#invoice-POS { margin: 0; padding: 0; padding-top: 5px }
		}
		@page  
		{ 
		    size: auto;   /* auto is the initial value */ 
		    margin: 0;  
		} 
	</style>
</head>
<body>

  <div id="invoice-POS">
    
    <center id="top">
      <div class="logo">
      	<img src="<?php echo base_url();?>assets/uploads/logos/<?php echo $settings->logo; ?>">
      </div>
      <div class="info"> 
        <h2><?php echo escapeStr($this->activeStoreData->title);?></h2>
        <p> 
            <?php echo lang('client_address');?> : <?php echo escapeStr($this->activeStoreData->address);?></br>
            <?php echo lang('client_email');?>   : <?php echo escapeStr($this->activeStoreData->invoice_mail);?></br>
            <?php echo lang('client_telephone');?>   : <?php echo escapeStr($this->activeStoreData->phone);?></br>
        </p>
      </div><!--End Info-->
    </center><!--End InvoiceTop-->
	<div class="clearfix"></div>
    
    <div id="mid">
      <div class="info">
      	<h2></h2>
      	<center>
	    	<div id="" style="margin-left: -10px; margin-top: -3px;margin-bottom: 9px;">
		        <?php echo $this->repairer->barcode($db['code'], 'code128', 30, true); ?>
		    </div>
		</center>
        <h2><?php echo lang('customer_name');?>: <?php echo escapeStr($db['name']);?></h2>
		<div class="clearfix"></div>
      </div>

    </div><!--End Invoice Mid-->
   
    <div id="bot">
		<div id="table">
			<table>
				<tr class="tabletitle">
					<td class="item"><h2><?php echo lang('repair_item');?></h2></td>
					<td class="Hours"></td>
					<td class="Rate price"></h2></td>
				</tr>

				<tr class="service">
					<td colspan="3" class="tableitem"><p class="itemtext">
						<strong><?php echo $db['model_name'];?></strong>
						<small><?php echo $db['imei'] ? '('.$db['imei'].')' : '';?>
						<br>
						<?php echo $db['defect'];?></small>
					</p></td>
				</tr>

				<tr class="tabletitle">
					<td></td>
					<td class="Rate"><h2><?php echo lang('tax');?></h2></td>
					<td class="payment"><h2><?php echo $this->repairer->formatMoney($db['tax']); ?></h2></td>
				</tr>

				<tr class="tabletitle">
					<td></td>
					<td class="Rate"><h2><?php echo lang('total_price');?></h2></td>
					<td class="payment"><h2><?php echo $this->repairer->formatMoney($db['grand_total']); ?></h2></td>
				</tr>

				<tr class="tabletitle">
					<td></td>
					<td class="Rate"><h2><?php echo lang('paid');?></h2></td>
					<td class="payment"><h2><?php echo $this->repairer->formatMoney($db['advance']); ?></h2></td>
				</tr>

				<tr class="tabletitle">
					<td></td>
					<td class="Rate"><h2><?php echo lang('balance');?></h2></td>
					<td class="payment"><h2><?php echo $db['grand_total'] - $db['advance']; ?></h2></td>
				</tr>



			</table>

			<small style="text-align: right !important;">
                <?php if($payments): ?>
                    <?php foreach ($payments as $payment){
                        echo sprintf(lang('paid_by_date'), lang($payment->paid_by), $this->repairer->formatMoney($payment->amount), $payment->date).'<br>';
                    }?>
                <?php endif; ?>
            </small>

            <?php if($db['warranty'] && json_decode($db['warranty']) && isset(json_decode($db['warranty'])->details)): ?><div class="col txt" style="padding: 5px;width: calc(100% - 10px);font-size: 10px;">
                <div class="well">
                    <strong><?php echo lang('Warranty Details');?>:</strong><br>
                    <?php echo escapeStr(json_decode($db['warranty'])->details); ?>
                </div>
            <?php endif; ?>
		</div><!--End Table-->

		<div id="legalcopy">
			<p class="legal"><?php echo escapeStr($settings->disclaimer);?> 
			</p>


		</div>
			<center>
				<?php echo $this->repairer->qrcode('link', urlencode(base_url()), 1); ?>
			</center>
			<div class="clearfix"></div>


        
	</div><!--End InvoiceBot-->
	
  </div><!--End Invoice-->
<script type="text/javascript">
	// $( document ).ready(function() {
 //        setTimeout(function() {
 //            window.print();
 //        }, 500);
 //        window.onafterprint = function(){
 //            setTimeout(function() {
 //                window.close();
 //            }, 10000);
 //        }
 //    });
</script>
</body>
</html>
