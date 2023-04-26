
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="<?php echo $assets;?>dist/css/custom/table-print.css">
        <link rel="stylesheet" href="<?php echo $assets;?>bower_components/bootstrap/dist/css/bootstrap.min.css">
        <link href="https://fonts.googleapis.com/css?family=Prompt" rel="stylesheet">
        <!-- <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet"> -->
        <script src="<?php echo $assets;?>bower_components/jquery/dist/jquery.min.js"></script>
        <script src="<?php echo $assets;?>plugins/jSignature/jSignature.min.js"></script>

        <title><?php echo lang('invoice');?></title>
        <style type="text/css">
            * {
                font-family: Prompt !important;
                /*font-weight: bolder !important;*/
                color: #777; 
                font-weight:400;
            }
            #print_button {
                height 50px;
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

        </style>
    </head>
<body>


<center>
    <div class="x_content" contentEditable="true">
        <?php if($is_a4): ?>
            <div id="copy" class="row" style="width: 21cm;height: 29.5cm;margin: 0 auto;padding:0;">
        <?php else: ?>
            <div id="copy" class="row" style="width: 21cm;height: 14.8cm;margin: 0 auto;padding:0;">
        <?php endif;?>
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0.5cm 21px 0px 21px;">
            <div class="col-xs-5" style="text-align:left;padding-left: 0;">
                <div class="text-muted well well-sm no-shadow head_left" style="background: #3d3d3d;border: 1px solid #2f2f2f;">
                    <h4 class="text-head1" style="margin-top: 0px;margin-bottom: 0px;color: #ffffff;"><?php echo lang('invoice');?> <?php echo escapeStr($db['code']); ?></h4>
                    <h6 class="text-head1" style="margin-top: 0px;margin-bottom: 0px;color: #ffffff;"><?php echo lang('invoice_subheading');?></h6>                   
                </div>   
                
                <h5 style="margin: 4px 1px;" class="color"><?php echo lang('inv_date_opening');?>: <?php echo date('d/m/Y',strtotime($db['date_opening']));?></h5>
                <h5 style="margin: 4px 1px;" class="color"><?php echo lang('report_code');?>: <?php echo ($db['id']); ?></h5>
                <h5 style="margin: 4px 1px;" class="color"><?php echo lang('report_client');?>:  <?php echo escapeStr($client->name);?></h5>
                <h5 style="margin: 4px 1px;" class="color"><?php echo lang('report_telephone');?>: <?php echo escapeStr($client->telephone);?> </h5>              
            </div>
            <div class="col-xs-4 bg-col" style="text-align:left;border-top: 1px solid #D8D8D8;background-color: #f5f5f5;border-right: 1px solid #D8D8D8;border-bottom: 1px solid #D8D8D8;padding: 10px;height:136px;">
                <h5 style="margin: 4px 1px;" class="color"><?php echo lang('bc_management');?></h5>
                <h5 style="margin: 4px 1px;" class="color">
                    <div id="" style="margin-left: -10px; margin-top: -3px;margin-bottom: 9px;">
                        <?php echo $this->repairer->barcode($db['code'], 'code128', 20, false); ?>
                    </div>
                </h5>  
                    <h5 style="margin: 5px 7px;" class="color">
                        <div style="float: left;margin-top: 0px;margin-left: -7px;margin-right: 8px;">
                            <?php echo $this->repairer->qrcode('link', urlencode(base_url()), 1); ?>
                        </div>                  
                    </h5>
                    <h4 style="margin: 23px 1px 0px 0px;font-size:16px;" class="color"><?php echo lang('check_online');?></h4>
                    <h5 style="margin: 4px 1px;font-size:10px;" class="color">
                        <?php echo base_url();?>
                    </h5>
                            
            </div>
            <div class="col-xs-3" style="padding-right: 0;">
                <h4 class="color" style="margin-top: 0px;margin-bottom: 0px;text-align: right;">
                    <img src="<?php echo base_url();?>assets/uploads/logos/<?php echo $settings->logo;?>" style="height: 70px;padding-bottom: 10px;;">
                </h4>
                <h4 class="color" style="margin-top: 0px;margin-bottom: 0px;text-align: right;"><?php echo escapeStr($this->activeStoreData->name);?></h4>
                <h5 class="color" style="margin-top: 4px;margin-bottom: 0px;text-align: right;"><?php echo escapeStr($this->activeStoreData->address);?></h5>
                <h5 class="color" style="margin-top: 4px;margin-bottom: 0px;text-align: right;"><?php echo lang('report_telephone');?>: <?php echo escapeStr($this->activeStoreData->phone);?></h5>
            </div>
        </div>


        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0.5cm 21px 0px 21px;">
         <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="col-md-1" scope="col">
                        <?php echo lang('inv_at');?>
                    </th>
                    <th class="col-md-3"><?=lang('inv_item');?></th>
                    <th class="col-md-2"><?=lang('inv_defect');?></th>
                    <th class="col-md-2"><?=lang('inv_details');?></th>
                    <th class="col-md-2"><?=lang('inv_price');?></th>
                </tr>
            </thead>
            <tbody>
                <?php if($items): ?>
                <?php $a = 1; foreach ($items as $item): ?>
                    <tr>
                        <td class="no"><?php echo str_pad($a, 2, '0', STR_PAD_LEFT); ?></td>
                        <td class="desc"><?php echo escapeStr($item->product_name); ?></td>
                        <td class="unit"><?php echo escapeStr($settings->currency) ?> <?php echo $this->repairer->formatMoney($item->unit_price);?></td>
                        <td class="qty"><?php echo number_format($item->quantity, 1);?></td>
                        <td class="total"><?php echo escapeStr($settings->currency) ?> <?php echo $this->repairer->formatMoney($item->subtotal);?></td>
                    </tr>
                <?php $a++; endforeach; ?>
                <?php else: ?>
                    <tr style="height: 160px;">
                        <td class="col-md-1">1</td>
                        <td class="col-md-3"><?= $db['category'];?> (<?php echo $db['model_name'];?>)</td>
                        <td class="col-md-2"><?= $db['defect'];?></td>
                        <td class="col-md-2"><?= $db['public_note'];?></td>
                        <td class="col-md-2"><?=$this->repairer->formatMoney($db['grand_total']);?></td>
                    </tr>
                <?php endif; ?>



               
            </tbody>

            <tfoot>
                <tr>
                    <th  class="text-right col-md-6" colspan="4"><?php echo lang('tax');?></th>
                    <th class="col-md-2 text-center"><?=$this->repairer->formatMoney($db['tax']);?></th>
                </tr>
                <tr>
                    <th  class="text-right col-md-6" colspan="4"><?php echo lang('inv_total_price');?></th>
                    <th class="col-md-2 text-center"><?=$this->repairer->formatMoney($db['grand_total']);?></th>
                </tr>

                <tr>
                    <th  class="text-right col-md-6" colspan="4"><?php echo lang('paid');?></th>
                    <th class="col-md-2 text-center"><?=$this->repairer->formatMoney($db['advance']);?></th>
                </tr>

                <tr>
                    <th  class="text-right col-md-6" colspan="4"><?php echo lang('balance');?></th>
                    <th class="col-md-2 text-center"><?=$this->repairer->formatMoney($db['grand_total'] - $db['paid']);?></th>
                </tr>
            </tfoot>
        </table>

        <style>
            table {
                text-align: center;
            }
            tbody tr td {
                /*vertical-align: middle !important;*/
            }
            thead th {
                text-align:center;border-top: 1px solid #D8D8D8;background-color: #f5f5f5;
                border-right: 1px solid #D8D8D8;border-bottom: 1px solid #D8D8D8;border-left: 1px solid #D8D8D8;padding: 0px;height:30px;
            }
            tfoot th{
                text-align:left;border-top: 1px solid #D8D8D8;background-color: #f5f5f5;
                border-right: 1px solid #D8D8D8;border-bottom: 1px solid #D8D8D8;border-left: 1px solid #D8D8D8;padding: 2px 5px !important;height:10px;
            }
        </style>
                    
     
            <small class="pull-right">
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
        </div>


         <?php if($is_a4): ?>
            <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 400px 20px 0px 80px;">
        <?php else: ?>
            <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 40px 20px 0px 80px;">
        <?php endif;?>


            <div class="col-xs-6" style="text-align: left;padding: 0;">
                <h5 style="margin: 4px 1px 0px -52px;text-align: center;" class="color"><?php echo lang('sign_recipient');?> (.................................................)</h5>
            </div>
            <div class="col-xs-6" style="text-align: left;padding: 0;">
                <h5 style="margin: 4px 1px 0px -65px;text-align: center;" class="color"><?php echo lang('sign_client');?> (.................................................)</h5>
            </div>

        </div>

      </div>
      <?php if($two_copies): ?>
          <img src="<?php echo base_url();?>assets/images/cut.png" style="width: 27px;margin-top: -15px;margin-bottom: -100px;margin-left: -700px;">
          <div id="clone"></div>
      <?php endif;?>

    </div>
     
</center>
<div class="x_content n">
    <div id="copy" class="row" style="width: 21cm;margin: 0 auto;padding:0;">
            <div class="col-md-12 col-sm-12 col-xs-12">

              <div class="<?=($db['invoice_sign'] && $db['invoice_sign'] !== '') ? '' : 'no-print';?> well" style="width:100%;background-color:lightgrey;color:black;border:5px;font:11px/15px;align-content:left  sans-serif;">

                    <?php if($db['invoice_sign'] && $db['invoice_sign'] !== ''): ?>
                        <label>Customer Signature: </label>
                        <div class="clearfix"></div>
                        <img height="200px" src="<?= base_url('assets/uploads/signs/invoice_').$db['invoice_sign']; ?>">
                        <div style="clear: both;"></div>
                    <?php else: ?>
                        <script type="text/javascript">
                            $(document).ready(function() {
                                $("#signature").jSignature();
                            });
                        </script>
                        <div class="no-print">
                            <label id="signature_label">Customer Signature (Please sign below)</label>
                            <div id="signature"></div>
                            <input type="hidden" name="sign_id" id="sign_id" value="">
                            <br>
                            <button id="submit_sign" class="no-print btn-icon btn btn-primary btn-icon pull-right">Save</button>
                            <button id="reset_sign" class="no-print btn-icon btn btn-primary btn-icon pull-left">Reset</button>
                            <div class="clearfix"></div>
                        </div>
                    <?php endif; ?>

                </div>


                <div id="<?=($db['invoice_sign'] && $db['invoice_sign'] !== '') ? 'no-print' : 'print-only';?>" style="display: none;">
                    <label>Customer Signature (Please sign below)</label>
                    <div class="col-md-6" style="border-bottom: 2px solid black; width: 50%; margin-top: 80px;"></div>
                </div>
            </div>

    </div>
</div>
    <div id="print_button"><?php echo $this->lang->line('print');?></div>

</body>
</html>
<script type="text/javascript">
    $( document ).ready(function() {
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
        window.onafterprint = function(){
            setTimeout(function() {
                window.close();
            }, 10000);
        }
    });

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


<?php if(($pdf)): ?>
    .no-print {
        display: block;
    }
    .table-condensed {
        width: 97%;
        border: 1px solid #ccc;
    }
    .table-condensed td {
        height: 30px !important;
        padding-top: 12px !important;
    }
}
@media print {
    #print_button, #save_button, #email_button {display: none;}

    .col-xs-7 {
        width: 57.5%;
    }
    .col-xs-5 {
        width: 40%;
    }
    .col-xs-4 {
        width: 30%;
    }
 .col-xs-3 {
        width: 23%;
    }
    .is-table-row .col-xs-3 {
        width: 28%;
    }

   

    .col-xs-2 {
        width: 13.5%;
    }

    .col-xs-1 {
        width: 11.5%;
    }
    .col-xs-8 {
        width: 60%;
    }

    .row_item{
        height: 200px !important;
    }
}


@page {
    margin: 0mm;
    margin-header: 0mm;
    margin-footer: 0mm;
}

<?php endif; ?>
</style>