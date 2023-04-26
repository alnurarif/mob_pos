<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- <link rel="stylesheet" type="text/css" href="<?php echo $assets;?>dist/css/custom/table-print.css"> -->
        <link rel="stylesheet" href="<?php echo $assets;?>bower_components/bootstrap/dist/css/bootstrap.min.css">
        <!-- <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet"> -->

        <link href="https://fonts.googleapis.com/css?family=Prompt" rel="stylesheet">
        <script src="<?php echo $assets;?>bower_components/jquery/dist/jquery.min.js"></script>
        <script src="<?php echo $assets;?>plugins/jSignature/jSignature.min.js"></script>

        <!-- <script type="text/javascript" src="<?php echo base_url();?>assets/plugins/custom/jquery-barcode.min.js"></script>   -->
        <title><?php echo lang('report');?></title>
        <style type="text/css">

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
            .border {
                border-bottom: 3px solid black
            }
            .col-xs-5ths,
            .col-sm-5ths,
            .col-md-5ths,
            .col-lg-5ths {
                position: relative;
                min-height: 1px;
                padding-right: 15px;
                    display: flex;
                padding-left: 15px;
            }

            .col-xs-5ths {
                width: 20%;
                float: left;
                font-size: 8px;
            }
            .font-small {
                font-size: 12px
            }
            @media (min-width: 768px) {
                .col-sm-5ths {
                    width: 20%;
                    float: left;
                }
            }

            @media (min-width: 992px) {
                .col-md-5ths {
                    width: 20%;
                    float: left;
                }
            }

            @media (min-width: 1200px) {
                .col-lg-5ths {
                    width: 20%;
                    float: left;
                }
            }

            .circle {
                width: 20px;height: 20px;border: 1px solid black; border-radius: 50px;display: inline-block;
            }
        </style>
    </head>
<body>

    <div class="x_content">
    <div id="copy" class="row" style="width: 21cm;height: 29.7cm;margin: auto;">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0.5cm 21px 0px 21px;">
            <div class="row">
                <div class="col-xs-6 no-padding text-left" >
                    Scheda Assitenza <strong>N <?php echo escapeStr($db['code']); ?></strong>
                </div>
                <div class="col-xs-6 no-padding text-right" >
                    Verifica tatus online
                </div>

                <div class="col-xs-12 no-padding" >
                    <div class="border"></div>
                </div>  
            </div>
            
        </div>

        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0.2cm 21px 0px 21px;">
            <div class="col-xs-10" style="padding:0 !important;margin-bottom: 5px">

                <div class="col-xs-6" style="padding-left: 0;padding-right: 5px;font-size: 12px">
                    <div class="body" style="text-align:left;border-top: 1px solid #000;background-color: white;border-right: 1px solid #000;border-bottom: 1px solid #000;border-left: 1px solid #000;padding: 5px 10px;height:110px;">
                        <strong><?=$this->activeStoreData->name;?></strong><br>
                        <?=$this->activeStoreData->address;?>,  <br><?=$this->activeStoreData->zipcode;?> - <?=$this->activeStoreData->city;?> <br>
                        P.IVA <?=$settings->vat;?><br>
                        Tel. <?=$this->activeStoreData->phone;?><br>
                        <?=$this->activeStoreData->invoice_mail;?>
                    </div>
                </div>
                <div class="col-xs-6" style="padding-right: 0;padding-left: 5px;font-size: 12px">
                    <div class="body" style="text-align:left;border-top: 1px solid #000;background-color: white;border-right: 1px solid #000;border-bottom: 1px solid #000;border-left: 1px solid #000;padding: 5px 10px;height:110px;">
                        <strong><?=$client->name;?></strong><br>

                        <?=$client->address;?>,  <br><?=$client->postal_code;?> - <?=$client->city;?> <br>
                        CF. <?=$client->cf;?><br>
                        Tel. <?=$client->telephone;?><br>
                        <?=$client->email;?>
                    </div>
                </div>
            </div>


            <div class="col-xs-2 text-center" style="padding:0 !important;margin:0 !important;border-right: 1px solid black">
                <span style="font-size: 16px"><?=getDomain();?></span>
                <style type="text/css">
                    .qrimg {
                        height: 95px !important;
                    }
                </style>
                <?php echo $this->repairer->qrcode('link', urlencode(base_url()), 1); ?>

            </div>


            <div class="col-xs-10" style="padding:0 !important;border-right: 1px solid #000;border-left: 1px solid #000;height: 117px">

                <div class="clearfix"></div>
                <!-- 
                <div class="col-xs-6 bg-col" style="text-align:left;border: 1px solid #000;padding: 10px 1px;">
                    <span><?=lang('status');?>: </span>
                    <strong><?=$status->label;?></strong>
                </div> -->
                <!-- <div class="col-xs-6 bg-col" style="text-align:left;border: 1px solid #000;padding: 10px;">
                    <span><?=lang('technician');?>: </span>
                    <strong><?=$assigned_to ? $assigned_to->first_name : '';?></strong>
                </div> -->

                <div class="col-xs-6 font-small" style="text-align:left;border: 1px solid #000;border-left:none;border-bottom:none;border-right:none;padding: 10px;">
                    <span><?=lang('repair_category');?>: </span>
                    <strong><?=$db['category'];?></strong>
                </div>
                <div class="col-xs-6 font-small" style="text-align:left;border: 1px solid #000;border-bottom:none;border-right:none;padding: 10px;">
                    <span><?=lang('Manufacturer');?>: </span>
                    <strong><?=$manufacturer ? $manufacturer->name : '';?></strong>
                </div>
                <div class="clearfix" ></div>

                <div class="col-xs-6 font-small" style="text-align:left;border: 1px solid #000;border-left:none;border-bottom:none;border-right:none;padding: 10px;">
                    <span><?=lang('model_name');?>: </span>
                    <strong><?=$db['model_name'];?></strong>
                </div>
                <div class="col-xs-6 font-small" style="text-align:left;border: 1px solid #000;border-bottom:none;border-right:none;padding: 10px;">
                    <span><?=lang('Serial Number');?>: </span>
                    <strong><?=$db['serial_number'];?></strong>
                </div>

                <div class="clearfix" ></div>
                <div class="col-xs-6 font-small" style="text-align:left;border: 1px solid #000;border-left:none;border-bottom:none;border-right:none;padding: 10px;">
                    <span><?=lang('repair_condition');?>: </span>
                    <strong><?=$db['aesthetic_conditions'];?></strong>
                </div>

                <div class="col-xs-3 font-small" style="text-align:left;border: 1px solid #000;border-bottom:none;border-right:none;padding: 10px;">
                    <span><?=lang('repair_advance');?>: </span>
                    <strong><?=$db['advance'];?></strong>
                </div>

                <div class="col-xs-3 font-small" style="text-align:left;border: 1px solid #000;border-bottom:none;border-right:none;padding: 10px;">
                    <span><?=lang('total');?>: </span>
                    <strong><?=$db['grand_total'];?></strong>
                </div>

                <div class="clearfix" ></div>

            </div>

            <div class="col-xs-2 text-center" style="padding:0 !important;margin:0 !important;border-right: 1px solid black">
               
                <div style=" 
                    border: 1px solid black;
                    margin: 16px 16px;
                    padding: 4px 5px;
                    background: white;
                    z-index: 1;">
                    <?php for ($i=0; $i < 3; $i++): ?>
                        <div class="circle" style=""></div>
                    <?php endfor; ?>
                    <br>
                    <?php for ($i=0; $i < 3; $i++): ?>
                        <div class="circle" style=""></div>
                    <?php endfor; ?>
                    <br>
                    <?php for ($i=0; $i < 3; $i++): ?>
                        <div class="circle" style=""></div>
                    <?php endfor; ?>
                </div>
            </div>


            <div class="col-xs-12" style="padding:0 !important">

                <div class="col-xs-10 font-small" style="text-align:left;border: 1px solid #000;padding: 10px;">
                    <span><?=lang('defect_');?>: </span>
                    <strong><?=$db['defect'];?></strong>
                </div>


                 <div class="col-xs-2 font-small" style="text-align:left;border: 1px solid #000;border-left:0;padding: 10px;">
                    <span><?=lang('Pin');?>: </span>
                    <strong><?=$db['pin_code'];?>&nbsp;</strong>
                </div>
            </div>
            
            
        </div>

      


        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 21px 0px 21px;margin-top: 5px">
            
            <div class="col-xs-12" style="padding-right: 0;border: 1px solid #000;min-height: 200px">
                <br>
                
                <p><?=$db['public_note'];?></p>
            </div>


            <div class="col-xs-12" style="margin-top:10px;padding:10px;padding-right: 0;border: 1px solid #000;min-height: 200px">
                <p><?=$settings->disclaimer;?></p>
            </div>
        </div>

        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 21px 0px 21px;">

            <div class="col-xs-12" style="margin-top:15px;padding:10px;padding-right: 0;border: 1px solid #000;min-height: 80px">

                <div class="col-md-6" style="padding: 5px 20px 0px 20px;">
                    <p><strong>Laborotario</strong> Sede legale</p>
                    Date: <?=date('Y-m-d', strtotime($db['date_opening']));?> Ore:<?= date('H:i', strtotime($db['date_opening']));?>
                </div>

                <div class="col-md-6" style="padding: 5px 20px 0px 20px;text-align: right;">
                    <p><strong>Firma per accettazine LEGGIBILE</strong></p>

                    <?php if($db['invoice_sign'] && $db['invoice_sign'] !== ''): ?>
                        <img height="40px" src="<?php echo base_url('assets/uploads/signs/invoice_').$db['invoice_sign']; ?>">
                        <div style="clear: both;"></div>
                    <?php else: ?>
                    <br>
                    _____________
                    <?php endif;?>
                </div>
                
            </div>

            


        </div>
        
       
        <div class="clearfix"></div>
       



    <?php if($db['invoice_sign'] && $db['invoice_sign'] !== ''): ?>
        <?php else: ?>

    <div class="<?php echo ($db['invoice_sign'] && $db['invoice_sign'] !== '') ? '' : 'no-print';?> well" style="width:100%;height:200px;background-color:lightgrey;color:black;border:5px;font:11px/15px;align-content:left  sans-serif;">
          
            
            <script type="text/javascript">
                $(document).ready(function() {
                    $("#signature").jSignature({ lineWidth: 1,  height: 100 });
                });
            </script>

            <label id="signature_label"><?php echo lang('Customer Signature (Please sign below)');?></label>
            <div id="signature" ></div>
            <input type="hidden" name="sign_id" id="sign_id" value="">
            <button id="submit_sign" class="btn-icon btn btn-primary btn-icon pull-right"><?php echo lang('Save');?></button>
            <button id="reset_sign" class="btn-icon btn btn-primary btn-icon pull-left"><?php echo lang('Reset');?></button>
        <br>
        <br>
        <br>
        <br>
        <br>
        <?php endif; ?>

    </div>

      </div>


      <?php if($two_copies): ?>
          <img src="<?php echo base_url();?>assets/images/cut.png" style="width: 27px;margin-top: -15px;margin-bottom: -100px;margin-left: -700px;">
          <div id="clone"></div>
      <?php endif;?>
    </div>


    <div id="print_button" style="margin-top: 40px"><?php echo $this->lang->line('print');?></div>

</body>
</html>
<script type="text/javascript">
    $( document ).ready(function() {

     jQuery("#reset_sign").on("click", function (e) {
            $("#signature").jSignature('reset');
        });

        jQuery("#submit_sign").on("click", function (e) {
            var datapair = $('#signature').jSignature("getData", 'base30');
            datapair = 'data='+(datapair[1])+'&id=<?php echo $db['id'];?>';
            jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>panel/misc/save_invoice_signature",
                data: datapair,
                cache: false,
                success: function (data) {
                    location.reload();
                }
            });
        });

        jQuery(document).on("click", "#print_button", function() {
            window.print();
            // setInterval(function() {
            //     window.close();
            // }, 500);
        });

        <?php if($two_copies): ?>
            $('#copy').clone().appendTo('#clone');
            $('#copy').css('border-bottom', '#999999 1px dotted');
        <?php endif;?>

        setTimeout(function() {
            window.print();
        }, 3000);
        // window.onafterprint = function(){
            // setTimeout(function() {
            //     window.close();
            // }, 10000);
        // }
    });
</script>
<style type="text/css">
    @media print {
        html, body {
            height: 99%;    
            font-size: 12px;
        }

        .no-print {
            display: none;
        }

        #print-only {
            display: block !important;
        }
    }
</style>