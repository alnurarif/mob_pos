<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="<?php echo $assets;?>dist/css/custom/table-print.css">
        <link rel="stylesheet" href="<?php echo $assets;?>bower_components/bootstrap/dist/css/bootstrap.min.css">
        <!-- <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet"> -->

        <link href="https://fonts.googleapis.com/css?family=Prompt" rel="stylesheet">
        <script src="<?php echo $assets;?>bower_components/jquery/dist/jquery.min.js"></script>
        <!-- <script type="text/javascript" src="<?php echo base_url();?>assets/plugins/custom/jquery-barcode.min.js"></script>   -->
        <title><?php echo lang('report');?></title>
        <style type="text/css">
            *
            {
               font-family: Prompt !important;
               font-weight: bolder !important;
            }

             #print_button {
                height: 50px;
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

            #print_button:hover {
                background-color: #3A3A3A;

            }

            @media print {
                #print_button {display: none;}
            }

            .qrcode img {
                height: 65px
            }

        </style>
        <script type="text/javascript">


        
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
        </script>


    </head>
<body>

<center>
    <div class="x_content">
        <?php if($is_a4): ?>
            <div id="copy" class="row" style="width: 21cm;height: 29.7cm;margin: auto;">
        <?php else: ?>
            <div id="copy" class="row" style="width: 21cm;height: 14.8cm;margin: auto;">
        <?php endif;?>
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0.5cm 21px 0px 21px;">
            <div class="col-xs-5" style="text-align:left;padding-left: 0;">
                <div class="text-muted well well-sm no-shadow head_left" style="background: #3d3d3d;border: 1px solid #2f2f2f;">
                    <h4 class="text-head1" style="margin-top: 0px;margin-bottom: 0px;color: #ffffff;"><?php echo lang('invoice');?> <?php echo escapeStr($db['code']); ?></h4>
                    <h6 class="text-head1" style="margin-top: 0px;margin-bottom: 0px;color: #ffffff;"><?php echo lang('invoice_subheading');?></h6>                   
                </div>        
                <h5 style="margin: 4px 1px;" class="color"><?php echo lang('report_code');?>: <?php echo escapeStr($db['code']); ?>
 
                </h5>
                <h5 style="margin: 4px 1px;" class="color"><?php echo lang('report_client');?>:  <?php echo escapeStr($client->name);?></h5>
                <h5 style="margin: 4px 1px;" class="color"><?php echo lang('report_telephone');?>: <?php echo escapeStr($client->telephone);?> </h5>                
            </div>
            <div class="col-xs-7" style="padding-right: 0;">
                <h4 class="color" style="margin-top: 0px;margin-bottom: 0px;text-align: right;">
                    <img src="<?php echo base_url();?>assets/uploads/logos/<?php echo $settings->logo;?>" style="height: 70px;padding-bottom: 10px;;">
                </h4>
                <h4 class="color" style="margin-top: 0px;margin-bottom: 0px;text-align: right;"><?php echo escapeStr($this->activeStoreData->name);?></h4>
                <h5 class="color" style="margin-top: 4px;margin-bottom: 0px;text-align: right;"><?php echo escapeStr($this->activeStoreData->address);?></h5>
                <h5 class="color" style="margin-top: 4px;margin-bottom: 0px;text-align: right;"><?php echo lang('report_telephone');?>: <?php echo escapeStr($this->activeStoreData->phone);?></h5>
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 5px 20px 0px 20px;">
            <div class="col-xs-4 bg-col" style="text-align:left;border-top: 1px solid #D8D8D8;background-color: #f5f5f5;border-right: 1px solid #D8D8D8;border-bottom: 1px solid #D8D8D8;border-left: 1px solid #D8D8D8;padding: 10px;height:136px;">
                <h5 style="margin: 4px 1px;" class="color"><?php echo lang('report_equipment');?>: <?php echo escapeStr($db['model_name']);?></h5>
                <h5 style="margin: 4px 1px;" class="color"><?php echo lang('report_Status');?>:  <?php echo escapeStr($status->label);?></h5>
                <h5 style="margin: 4px 1px;" class="color"><?php echo lang('report_defect');?>:  <?php echo escapeStr($db['defect']);?></h5>

                <h5 style="margin: 4px 1px;" class="color">
                    <?php echo lang('warranty');?> :  <br>
                    <span id="warranty"></span>
                </h5>  
            </div>
            <div class="col-xs-4 bg-col" style="text-align:left;border-top: 1px solid #D8D8D8;background-color: #f5f5f5;border-right: 1px solid #D8D8D8;border-bottom: 1px solid #D8D8D8;padding: 10px;height:136px;">
                <h5 style="margin: 4px 1px;" class="color"><?php echo lang('report_category');?>: <?php echo $db['category'];?></h5>
                <h5 style="margin: 4px 1px;" class="color"><?php echo lang('report_total');?>:  <?php echo $this->repairer->formatMoney($db['grand_total'], 2);?></h5>
                <h5 style="margin: 4px 1px;" class="color"><?php echo lang('advance');?>:  <?php echo $this->repairer->formatMoney($db['advance'], 2);?></h5>
                <h5 style="margin: 4px 1px;" class="color"><?php echo lang('balance');?>:  <?php echo $this->repairer->formatMoney( $db['grand_total'] - $db['advance'], 2);?></h5>
                <h5 style="margin: 4px 1px;" class="color">
                    <?php echo lang('report_date_opening');?> :  
                    <?php echo date('m/d/Y', strtotime($db['date_opening']));?>        
                </h5>  
          
            </div>
            <div class="col-xs-4 bg-col" style="text-align:left;border-top: 1px solid #D8D8D8;background-color: #f5f5f5;border-right: 1px solid #D8D8D8;border-bottom: 1px solid #D8D8D8;padding: 10px;height:136px;">
                <h5 style="margin: 4px 1px;" class="color"><?php echo lang('bc_management');?></h5>
                <h5 style="margin: 4px 1px;" class="color">
                    <div id="" style="margin-left: -10px; margin-top: -3px;margin-bottom: 9px;">
                        <?php echo $this->repairer->barcode($db['code'], 'code128', 20, false); ?>
                    </div>
                </h5>  
                        <h5 style="margin: 5px 7px;" class="color">
                        <div style="float: left;margin-top: 0px;margin-left: -7px;margin-right: 8px;" class="qrcode">
                            <?php echo $this->repairer->qrcode('link', urlencode(base_url()), 1); ?>
                        </div>                  
                    </h5>
                    <h4 style="margin: 23px 1px 0px 0px;font-size:16px;" class="color"><?php echo lang('check_online');?></h4>
                    <h5 style="margin: 4px 1px;font-size:10px;" class="color">
                        <?php echo base_url();?>
                    </h5>
                            
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 5px 20px 0px 20px;">
            <div class="col-xs-12" style="text-align: left;padding: 2;">
                <?php echo $settings->disclaimer; ?>
            </div>
        </div>
        <div class="clearfix"></div>
        <?php if($is_a4): ?>
            <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 100px 20px 0px 80px;" style="position: fixed;bottom: 0px">
        <?php else: ?>
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 5px 20px 0px 80px;" style="position: fixed;bottom: 0px">
        <?php endif;?>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
            <div class="col-xs-6" style="text-align: left;padding: 0;">
                <h5 style="margin: 4px 1px 0px -52px;text-align: center;" class="color"><?php echo lang('Repairer');?>(.................................................)</h5>
                <h5 style="margin: 4px 1px;text-align: center;" class="color"><?php echo escapeStr($settings->title);?></h5>
            </div>
            <div class="col-xs-6" style="text-align: left;padding: 0;">
                <h5 style="margin: 4px 1px 0px -65px;text-align: center;" class="color"><?php echo lang('sign_recipient');?> (.................................................)</h5>
                <h5 style="margin: 4px 1px;text-align: center;" class="color"><?php echo escapeStr($user->first_name).' '.escapeStr($user->last_name);?></h5>
            </div>
        </div>
      </div>


      <?php if($two_copies): ?>
          <img src="<?php echo base_url();?>assets/images/cut.png" style="width: 27px;margin-top: -15px;margin-bottom: -100px;margin-left: -700px;">
          <div id="clone"></div>
      <?php endif;?>
    </div>
</center>



    <div id="print_button" style="margin-top: 40px"><?php echo $this->lang->line('print');?></div>

</body>
</html>
<script type="text/javascript">

function getFormattedDate(date){
    var dd = date.getDate();
    var mm = date.getMonth()+1;
    var yyyy = date.getFullYear();
    return mm +'/'+dd+'/'+yyyy;
}
    function warranty() {
        var close_date = "<?=$db['date_opening'];?>";
            var close_date = new Date(close_date);
            var json = <?=($db['warranty']);?>;
            if (json){
                var warranty_duration = json.warranty_duration;
                var warranty_duration_type = json.warranty_duration_type;
                if (warranty_duration_type === 'years') {
                    days = warranty_duration * 365;
                    close_date.setDate(close_date.getDate() + parseInt(days));
                }else if (warranty_duration_type === 'months') {
                    close_date.setMonth(close_date.getMonth() + parseInt(warranty_duration));
                }else{ // days
                    close_date.setDate(close_date.getDate() + parseInt(warranty_duration));
                }
                days = Math.round((close_date - new Date()) / (1000 * 60 * 60 * 24));

                if (days > 0) {
                    text = lang.under_warranty_until + ' "'+getFormattedDate(close_date)+'"';
                }else{
                    text = lang.warrnaty_expired_on + ' "'+getFormattedDate(close_date)+'"';
                }
            }else{
                text = lang.no_warranty;
            }
       
        $('#warranty').html(text);              
    }
    $( document ).ready(function() {
        warranty();
        jQuery(document).on("click", "#print_button", function() {
            window.print();
            setInterval(function() {
                window.close();
            }, 500);
        });

        <?php if($two_copies): ?>
            $('#copy').clone().appendTo('#clone');
            $('#copy').css('border-bottom', '#999999 1px dotted');
        <?php endif;?>

        setTimeout(function() {
            window.print();
        }, 3000);
        // window.onafterprint = function(){
        //     setTimeout(function() {
        //         window.close();
        //     }, 10000);
        // }
    });
</script>