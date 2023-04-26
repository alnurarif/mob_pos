<!DOCTYPE html>
<html>
	<head>
	  	<meta charset="utf-8">
	  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta http-equiv="pragma" content="no-cache">

	  	<title><?php echo $page_title; ?></title>
	  	<!-- Tell the browser to be responsive to screen width -->
	  	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	  	<!-- Bootstrap 3.3.6 -->
	  	<link rel="stylesheet" href="<?php echo $assets ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
	  	<!-- Font Awesome -->

	  	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

	  	<!-- Ionicons -->
	  	<link rel="stylesheet" href="<?php echo $assets ?>bower_components/Ionicons/css/ionicons.min.css">
	  	<!-- Select2 -->
	  	<link rel="stylesheet" href="<?php echo $assets ?>bower_components/select2/dist/css/select2.css">
	  	<!-- Theme style -->
  

  		<?php if($settings->use_rtl): ?>
		    <link rel="stylesheet" href="<?php echo $assets ?>dist/rtl/css/AdminLTE.min.css">
		    <link rel="stylesheet" href="<?php echo $assets ?>dist/rtl/css/skins/_all-skins.min.css">
		    <link rel="stylesheet" href="<?php echo $assets ?>dist/rtl/css/bootstrap-rtl.min.css">
		    <link rel="stylesheet" href="<?php echo $assets ?>dist/rtl/css/rtl.css">
    		<link rel="stylesheet" href="<?php echo $assets ?>dist/rtl/fonts/fonts-fa.css">

  		<?php else: ?>
	  		<link rel="stylesheet" href="<?php echo $assets ?>dist/css/AdminLTE.min.css">
		  	<!-- AdminLTE Skins. Choose a skin from the css/skins
		       folder instead of downloading all of them to reduce the load. -->
	  		<link rel="stylesheet" href="<?php echo $assets ?>dist/css/skins/_all-skins.min.css">
		<?php endif;?>


	  	<!-- iCheck -->
	  	<link rel="stylesheet" href="<?php echo $assets ?>plugins/iCheck/all.css">
	  	<!-- Morris chart -->
	  	<link rel="stylesheet" href="<?php echo $assets ?>bower_components/morris.js/morris.css">
	  	<!-- jvectormap -->
	  	<!-- <link rel="stylesheet" href="<?php echo $assets ?>plugins/jvectormap/jquery-jvectormap-1.2.2.css"> -->
	  	<!-- Date Picker -->
	  	<link rel="stylesheet" href="<?php echo $assets ?>bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
	  	<link rel="stylesheet" href="<?php echo $assets ?>plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css">
	  	<!-- jQueryUI -->
	  	<link rel="stylesheet" href="<?php echo $assets ?>bower_components/jquery-ui/themes/ui-lightness/jquery-ui.min.css">
	  	<link rel="stylesheet" href="<?php echo $assets ?>bower_components/jquery-ui/themes/ui-lightness/theme.css">
	  	<!-- Daterange picker -->
	  	<link rel="stylesheet" href="<?php echo $assets ?>bower_components/bootstrap-daterangepicker/daterangepicker.css">
	  	<!-- DataTables -->
  	  	<link rel="stylesheet" href="<?php echo $assets ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  	  	<!-- DataTables -->
      	<link rel="stylesheet" href="<?php echo $assets ?>plugins/toastr/toastr.min.css">
      	<link rel="stylesheet" href="<?php echo $assets ?>bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css">
	  	<!-- bootstrap wysihtml5 - text editor -->
	  	<link rel="stylesheet" href="<?php echo $assets ?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
	  	<!-- datetime picker -->
	  	<link rel="stylesheet" href="<?php echo $assets ?>plugins/timepicker/bootstrap-timepicker.min.css">
	  	<!-- Custom CSS -->
	  	<link rel="stylesheet" href="<?php echo $assets ?>plugins/parsley/parsley.css">
	  	
	  	<link rel="stylesheet" href="<?php echo $assets ?>dist/css/custom/custom.css">
	  
		<script type="text/javascript">
        	var base_url = "<?php echo site_url();?>";
			var site = <?php echo json_encode(array('base_url' => base_url(), 'settings' => $settings, 'dateFormats' => $dateFormats));?>;
	        var tax_rates = <?php echo json_encode($taxRates); ?>;
	        var CURI = null;
		</script>

		<!-- jQuery 2.2.3 -->
		<script src="<?php echo $assets ?>bower_components/jquery/dist/jquery.min.js"></script>
		<script src="//code.jquery.com/jquery-migrate-3.0.0.js"></script>
		<script type="text/javascript">
			jQuery.migrateMute = true; 
		</script>
		
		<!-- InputMask -->
		<script src="<?php echo $assets ?>bower_components/inputmask/dist/jquery.inputmask.bundle.js"></script>
		
		<!-- jQuery UI 1.11.4 -->
		<script src="<?php echo $assets ?>bower_components/jquery-ui/jquery-ui.min.js"></script>
		<!-- Bootstrap JS -->
		<script src="<?php echo $assets ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
		
		<!-- DataTables -->
		<script src="<?php echo $assets ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
		<script src="<?php echo $assets ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
		<script src="<?php echo $assets ?>plugins/datatables/fnSetFilteringDelay.js"></script>
		<script src="<?php echo $assets ?>plugins/datatables/jquery.dataTables.dtFilter.min.js"></script>

		<!-- Select2 -->
		<script src="<?php echo $assets ?>bower_components/select2/dist/js/select2.min.js"></script>
		<script src="<?php echo $assets ?>bower_components/select2/dist/js/i18n/<?= $this->repairer->get_parseley_lang(); ?>.js"></script>
		<script src="<?php echo $assets ?>bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>
		<script src="<?php echo $assets ?>bower_components/moment/moment.js"></script>
		<script src="<?php echo $assets ?>plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
		<script src="<?php echo $assets ?>plugins/timepicker/bootstrap-timepicker.min.js"></script>

		<!-- iCheck -->
		<script src="<?php echo $assets ?>plugins/iCheck/icheck.min.js"></script>
		<!-- _Underscore.js -->
		<script src="<?php echo $assets ?>plugins/custom/underscore.js"></script>
		<!-- Toastr -->
		<script src="<?php echo $assets ?>plugins/toastr/toastr.min.js"></script>
		<!-- Accounting.js -->
		<script src="<?php echo $assets ?>plugins/custom/accounting.min.js"></script>
		<!-- Bootbox.js -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>
		<!-- Bootstrap Validator -->
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.js"></script>

		<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.min.js"></script>
		
		<!-- Morris.js charts -->
		<script src="<?php echo $assets ?>bower_components/raphael/raphael.js"></script>
		<script src="<?php echo $assets ?>bower_components/morris.js/morris.min.js"></script>
		<!-- Custom -->
		<script src="<?php echo $assets ?>plugins/custom/core.js"></script>
		<script src="<?php echo $assets ?>plugins/custom/custom.js"></script>

		<script src="<?php echo $assets ?>plugins/parsley/parsley.min.js"></script>


		<link rel="stylesheet" href="<?php echo $assets ?>plugins/bootstrap-fileinput/css/fileinput.min.css">
		<script src="<?php echo $assets ?>plugins/bootstrap-fileinput/js/fileinput.min.js"></script>
		<script src="<?php echo $assets ?>plugins/typeahead.bundle.js"></script>
	  	

		<script src='<?php echo $assets ?>/dist/js/jquery.plugin.min.js'></script>
		<script src='<?php echo $assets ?>/dist/js/jquery.calculator.min.js'></script>


		<link href="<?php echo $assets; ?>plugins/patternlock/patternLock.css"  rel="stylesheet" type="text/css" />
		<script src="<?php echo $assets; ?>plugins/patternlock/patternLock.min.js"></script>
		 <!--Parsley-->
		<script type="text/javascript" src="<?php echo $assets; ?>plugins/parsley/parsley-config.js"></script>
		<script type="text/javascript" src="<?php echo $assets; ?>plugins/parsley/parsley.min.js"></script>
		<script src="<?php echo $assets ?>plugins/parsley/i18n/<?= $this->repairer->get_parseley_lang(); ?>.js"></script>

		<link rel="stylesheet" type="text/css" href="//printjs-4de6.kxcdn.com/print.min.css">
		<script type="text/javascript" src="//printjs-4de6.kxcdn.com/print.min.js"></script>
	  		
	  
		<script type="text/javascript">
		    $(function() {
		    	    window.ParsleyValidator.setLocale('<?= $this->repairer->get_parseley_lang(); ?>');
				$.fn.select2.defaults.set("language", "<?= $this->repairer->get_parseley_lang(); ?>")

		        $('#sidebar_toggle').on('click', function(e) {
		            var body = $('body');
		            var state = '';

		            if (!body.hasClass('sidebar-collapse')) {
		                state = 'sidebar-collapse';
		            }

		            $.ajax({
		                type: 'post',
		                mode: 'queue',
		                url: '<?php echo base_url('panel/welcome/nav_toggle'); ?>',
		                data: {
		                    state: state
		                },
		                success: function(data) {

		                }
		            });
		        });
		        $('.select').select2();
		        
		    });

			$(document).ajaxStart(function() {
			  $("#loadingmessage").show();
			});

			$(document).ajaxStop(function() {
			  $("#loadingmessage").hide();
			});
			function formatMyDecimal(number) {
				var options = {
					decimal : "<?php echo $settings->decimal_seperator; ?>",
					thousand: "<?php echo $settings->thousand_seperator; ?>",
					precision : 2,
				};
				return accounting.formatNumber(number, options)
			}
			function formatDecimal(number) {
				var options = {
					decimal : ".",
					thousand: "",
					precision : 2,
				};
				return accounting.formatNumber(number, options)
			}

			$('#sidebar_toggle').on('click', function(e) {
	            var body = $('body');
	            var state = '';

	            if (!body.hasClass('sidebar-collapse')) {
	                state = 'sidebar-collapse';
	            }

	            $.ajax({
	                type: 'post',
	                mode: 'queue',
	                url: '<?php echo base_url('panel/welcome/nav_toggle'); ?>',
	                data: {
	                    state: state
	                },
	                success: function(data) {
	                	if (state == 'sidebar-collapse') {

	                	}else{
	            			body.removeClass('sidebar-collapse');
	                	}
	                }
	            });
	        });

		
			var lang = {paid: '<?php echo lang('paid');?>', pending: '<?php echo lang('pending');?>', completed: '<?php echo lang('completed');?>', ordered: '<?php echo lang('ordered');?>', received: '<?php echo lang('received');?>', partial: '<?php echo lang('partial');?>', sent: '<?php echo lang('sent');?>', r_u_sure: '<?php echo lang('r_u_sure');?>', due: '<?php echo lang('due');?>', returned: '<?php echo lang('returned');?>', active: '<?php echo lang('active');?>', inactive: '<?php echo lang('inactive');?>', unexpected_value: '<?php echo lang('unexpected_value');?>', select_above: '<?php echo lang('select_above');?>', download: '<?php echo lang('download');?>',
			    bill: '<?php echo lang('bill');?>',
			    order: '<?php echo lang('order');?>',
			    total: '<?php echo lang('total');?>',
			    items: '<?php echo lang('items');?>',
			    discount: '<?php echo lang('discount');?>',
			    order_tax: '<?php echo lang('order_tax');?>',
			    grand_total: '<?php echo lang('grand_total');?>',
			    total_payable: '<?php echo lang('total_payable');?>',
			    rounding: '<?php echo lang('rounding');?>',
			    merchant_copy: '<?php echo lang('merchant_copy');?>',
			    not_in_stock: '<?php echo lang('not_in_stock');?>',
			    no_tax: '<?php echo lang('no_tax');?>',
			    remove: '<?php echo lang('remove') ?>',
			    edit: '<?php echo lang('edit') ?>',
			    comment: '<?php echo lang('comment') ?>',
			    password: '<?php echo lang('password') ?>',
			    pin_code: '<?php echo lang('pin_code') ?>',
			    enable: '<?php echo lang('enable') ?>',
			    enabled: '<?php echo lang('enabled') ?>',
			    disable: '<?php echo lang('disable') ?>',
			    disabled: '<?php echo lang('disabled') ?>',
			    manage_stock: '<?php echo lang('manage_stock') ?>',
			    edit: '<?php echo lang('edit') ?>',
			    mark_closed: '<?php echo lang('mark_closed') ?>',
			    ready_to_purchase: '<?php echo lang('ready_to_purchase') ?>',
			    purchased: '<?php echo lang('purchased') ?>',
			    view_repair: '<?php echo lang('view_repair');?>',
			    edit_repair: '<?php echo lang('edit_repair');?>',
			    send_email: '<?php echo lang('send_email');?>',
			    enter_value: '<?php echo lang('enter_value');?>',
			    active: '<?php echo lang('active');?>',
			    inactive: '<?php echo lang('inactive');?>',
			    under_warranty_until: "<?=lang('under_warranty_until'); ?>",
				warrnaty_expired_on: "<?=lang('Warrnaty expired on'); ?>",
				multiple_warranties: "<?=lang('Multiple warranties'); ?>",
				click_for_details: "<?=lang('Click for details'); ?>",
				no_warranty: "<?=lang('No Warranty'); ?>",
				warranty_not_started_yet: "<?=lang('Warranty not started yet'); ?>",

			};
			var dp_lang = <?=$dp_lang?>;
			$.fn.datetimepicker.dates['sma'] = <?=$dp_lang?>;


    function printBarcode(){
    	setTimeout(function(){ 

		 	let barcode_height = '0.99in';
	        let barcode_width = '1.6in';

	        printJS({
	            printable: `<div id="page" style="margin:0 !important;margin-top:10px;padding:0 !important">
	            <div class="item style50" style="margin:0 !important;padding:0 !important;width:${barcode_width};height:${barcode_height};border:0;">
	                <div class="" style="margin:0 !important;padding:0 !important;width:${barcode_width};height:${'0.96in'};padding-top:0.025in;">
	                    <div style="clear: both;"></div>
	                        <b>
	                            <span class="barcode_name" style="font-size: 10px;line-height:1">${$('#client_name').find('option:selected').attr('data-name') ? $('#client_name').find('option:selected').attr('data-name') : ''} (${$('#client_name').find('option:selected').attr('data-phone') ? $('#client_name').find('option:selected').attr('data-phone') : ''}) </span>
	                                <span class="barcode_name" style="font-size: 10px;line-height:1">${$('#reparation_model').val() ? $('#reparation_model').val() : ''} - ${$('#serial_number').val() ? $('#serial_number').val() : ''}</span>
	                                <span class="barcode_name barcode_defect"  style="font-size: 10px;line-height:1">${$('#defect').val() ? $('#defect').val() : ''}</span>
	                                <span class="barcode_name barcode_defect"  style="font-size: 10px;line-height:1">${formatMoney($('#gtotal').html() > 0 ? $('#gtotal').html() : 0)}</span>
	                                <span class="barcode_image">
	                                    <img style="position:fixed;bottom:0;left: 50%;transform: translate(-50%, 0);" height="50px" src="<?=base_url();?>panel/misc/barcode?code=${$('#code').val()}" class="bcimg">
	                                </span>
	                            </b>
	                        </b>
	                    </div>
	                </div>
	            </div>`,
	            type: 'raw-html',
	            css: ['<?=$assets;?>dist/css/barcode1.css'],
	            style: `body{font-family: 'Arial'} @page { margin:0;size:${barcode_width} ${barcode_height};}`
          	})

    	}, 500);


       
    }


		</script>
		<style type="text/css">
		  	.loader {
		      	color: white;
		        top: 30px;
				right: -9px;
		      	position:fixed; z-index:9999;
		      	width: 106px;
		      	height: 106px;
		      	background: url('<?php echo $assets ?>dist/img/loading-page.gif') no-repeat center;
		  	}
		  	/* Styling for Select2 with error */
			div.has-error ul.select2-choices {
			  border-color: rgb(185, 74, 72) !important;
			}

		</style>

  		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

		  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		  <!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		  <![endif]-->
		 
	</head>
	
		<body class="<?php echo $body_class; ?> skin-red <?php echo $this->session->userdata('main_sidebar_state'); ?>">

<div id='loadingmessage' class="loader" style="display: none;"></div>
