<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo "Invoice " . lang("no") . ": " . $inv->id;?></title>
    <base href="<?php echo base_url()?>"/>
    <meta http-equiv="cache-control" content="max-age=0"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="0"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <link rel="stylesheet" href="<?php echo $assets ?>bower_components/bootstrap/dist/css/bootstrap.min.css">

        <script src="<?php echo $assets ?>bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo $assets ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>
    <link rel="stylesheet" href="<?php echo $assets ?>plugins/toastr/toastr.min.css">
    <script src="<?php echo $assets ?>plugins/toastr/toastr.min.js"></script>

    <!-- <link rel="stylesheet" href="<?php echo base_url();?>assets/dist/css/theme.css" type="text/css"/> -->
    <style type="text/css" media="all">
            body { color: #000; }
            #wrapper { max-width: 480px; margin: 0 auto; padding-top: 20px; }
            /*.btn { border-radius: 0; margin-bottom: 5px; }*/

            .bootbox .modal-footer { border-top: 0; text-align: right; }
            h3 { margin: 5px 0; }
            .order_barcodes img { float: none !important; margin-top: 5px; }
            @media print {
                .no-print { display: none; }
                #wrapper { max-width: 480px; width: 100%; min-width: 250px; margin: 0 auto; }
                .no-border { border: none !important; }
                .border-bottom { border-bottom: 1px solid #ddd !important; }
                table tfoot { display: table-row-group; }
            }


            #back_button {
                width: 25%;
                line-height: 25px;
                position: fixed;
                left: 40%;
                bottom: 0px;
                color: white;
                font-weight: bold;
                text-align: center;
                font-size: 17px;
                border-top-left-radius: 15px;
                border-top-right-radius: 15px;
                cursor: pointer;
                background-color: green;
                margin-bottom: 0; 

            }

            #back_button:hover {
                background-color: #3A3A3A;
            }

            

            #view_pdf {
                height 25px;
                width: 20%;
                line-height: 25px;
                position: fixed;
                left: 10%;
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

            #view_pdf:hover {
                background-color: #3A3A3A;
            }

            #print_button {
                height 25px;
                width: 20%;
                line-height: 25px;
                position: fixed;
                left: 31%;
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
          

            #email_btn {
                height 25px;
                width: 20%;
                line-height: 25px;
                position: fixed;
                left: 52%;
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

            #refund_btn {
                height 25px;
                width: 20%;
                line-height: 25px;
                position: fixed;
                left: 73%;
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

            #email_btn:hover {
                background-color: #3A3A3A;
            }
            #refund_btn:hover {
                background-color: #3A3A3A;
            }

            #editable_invoice {
                position: fixed;

                top: 15px;
                left: 0;
                background-color: rgba(239, 255, 148, 0.95);
                padding: 10px;
                color: black;
                border: 3px solid #C4E606;
                border-left: 0;
                border-top-right-radius: 5px;
                border-bottom-right-radius: 5px;
                box-shadow: #A7C308 0px 0px 11px -4px;
            }

            @media print {
                #editable_invoice {display: none;}  
                * {
                    font-size: 12px;
                }
                #print_button {display: none;}  
                #email_btn {display: none;}  
                .halfinvoice.seconda {display: block;}
                .show {width: 100% !important;}
                .well {padding:0 !important;}
               
                @page {
                    margin: 2px;
                }
                .show {width: 100% !important;}
                .well {padding:0 !important;}
                .title {
                    word-wrap:break-word;
                    font-size: 8px;
                    font-weight: normal;
                }
                .items {
                    font-size: 10px;
                    font-weight: normal;
                }
                .title_span{
                    width: 10px;
                }
                .id_span{
                    width: 2px !important;
                }
                .circle-nums{
                    font-size: 8px;
                    text-align: left;
                    width: auto;
                    margin:0;
                    padding: 0;
                }

            }

    </style>
</head>
<body>
    <div id="wrapper">
        <div id="receiptData">
            <div class="no-print">
                <?php
                if ($message) {
                    ?>
                    <div class="alert alert-success">
                        <button data-dismiss="alert" class="close" type="button">×</button>
                        <?php echo is_array($message) ? print_r($message, true) : $message;?>
                    </div>
                    <?php
                } ?>
            </div>
            <div id="receipt-data">
                <div class="text-center">
                    <?php echo '<img width="400px" src="'.base_url('assets/uploads/logos/'.$logo).'" alt="">' ?>
                    <h3 style="text-transform:uppercase;"><?php echo $settings->title;?></h3>
                    <?php
                    echo "<p>" . $settings->address . "<br>". $settings->city . " " . $settings->zipcode . "<br>". $settings->invoice_mail . "<br>" . $settings->vat . "<br>" . lang("tel") . ": " . $settings->phone;
                    echo '</p>';
                    ?>
                </div>
               
                <?php
                
                echo "<p>" .lang("date") . ": " . $this->repairer->hrld($inv->date) . "<br>";
                echo lang("sale_no_ref") . ": " . $inv->reference_no . "<br>";
                
                echo lang("sales_person") . ": " . $biller->first_name." ".$biller->last_name . "</p>";
                echo "<p>";
                

                echo lang("customer") . ": " . ($customer ? $customer->first_name . ' ' . $customer->last_name : lang('walk_in')) . "<br>";
                echo "</p>";
                ?>
                <div style="clear:both;"></div>
                <table class="table table-striped table-condensed">
                    <thead>
                        <tr>
                            <th><?php echo lang('#'); ?></th>
                            <th><?php echo lang('product_name'); ?></th>
                            <th><?php echo lang('price'); ?> (<?php echo lang('qty'); ?>)</th>
                            <th class="text-right"><?php echo lang('discount');?></th>
                            <th class="text-right"><?php echo lang('tax');?></th>
                            <th class="text-right"><?php echo lang('total');?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $r = 1;
                        foreach ($rows as $row) : ?>
                            <tr>
                                <td><?php echo $r; ?></td>
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
                                <td colspan="<?=($row->has_items) ? '5':'1';?>">

                                    <?php
                                        $product =  product_name(escapeStr($row->product_name) . $subtitle, null);
                                        if($row->item_type == 'drepairs' || $row->item_type == 'crepairs') {
                                            $rep = $this->repair_model->getRepairByID($row->product_id);

                                            if($rep){
                                                $product = product_name(escapeStr($rep['defect']) . ' - ' . $rep['model_name'] . $subtitle, null);
                                            }
                                        }
                                    ?>

                                    <?php echo $product; ?> 


                                    <?php if($row->item_details && $row->item_details != ''): ?>
                                    <br>
                                    <small><?php echo escapeStr($row->item_details);?></small>
                                    <?php endif;?>
                                    <br>
                                    <?php if(!$row->is_item): ?>

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
                                        <small class="title"><strong class="title"><?php echo lang('Under Warranty until');?></strong>: <?php echo $text;  ?></small><br>
                                        <?php if($warranty): ?>
                                        <small class="title"><strong class="title"><?php echo lang('Warranty Details');?></strong>: <?php echo $warranty->details; ?></small>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                </td>
                                <?php if(!$row->has_items): ?>
                                    <td><?php echo $this->repairer->formatMoney($row->unit_price); ?> (<?php echo $this->repairer->formatQuantity($row->quantity); ?>)</td>
                                    <td class="items"><?php echo $this->repairer->formatMoney($row->discount, 2);?></td>
                                    <td class="items"><?php echo $this->repairer->formatMoney($row->tax, 2);?></td>
                                    <td class="text-right"><?php echo $this->repairer->formatMoney($row->subtotal); ?></td>
                                <?php endif;?>
                            </tr>
                       <?php $r++; endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5"><?php echo lang("discount");?></th>
                            <th class="text-right"><?php echo $this->repairer->formatMoney(($inv->total_discount));?></th>
                        </tr>
                        <tr>
                            <th colspan="5"><?php echo lang("total");?></th>
                            <th class="text-right"><?php echo $this->repairer->formatMoney(($inv->grand_total - $inv->total_tax));?></th>
                        </tr>
                        <tr>
                            <th colspan="5"><?php echo lang("tax");?></th>
                            <th class="text-right"><?php echo $this->repairer->formatMoney($inv->total_tax);?></th>
                        </tr>

                        <?php if($inv->sale_id): ?>
                             <tr>
                                <th colspan="5"><?php echo lang("surcharge");?></th>
                                <th class="text-right"><?php echo $this->repairer->formatMoney($inv->surcharge);?></th>
                            </tr>
                        <?php endif; ?>

                       
                        <tr>
                            <th colspan="5"><?php echo lang("grand_total");?></th>
                            <th class="text-right"><?php echo $this->repairer->formatMoney($inv->grand_total);?></th>
                        </tr>
                    </tfoot>
                </table>


                <?php
                if ($payments) {
                    echo '<table class="table table-striped table-condensed"><tbody>';
                    foreach ($payments as $payment) {
                        echo '<tr>';
                        if (($payment->paid_by == 'cash') && $payment->pos_paid) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td colspan="2">' . lang("amount") . ': ' . $this->repairer->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid). '</td>';
                            echo '<td>' . lang("change") . ': ' . ($payment->pos_balance > 0 ? $this->repairer->formatMoney($payment->pos_balance) : 0) . '</td>';
                        } elseif (($payment->paid_by == 'CC' || $payment->paid_by == 'ppp') && $payment->cc_no) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->repairer->formatMoney($payment->pos_paid) . '</td>';
                            echo '<td>' . lang("no") . ': ' . 'xxxx xxxx xxxx ' . substr($payment->cc_no, -4) . '</td>';
                            echo '<td>' . lang("name") . ': ' . $payment->cc_holder . '</td>';
                        } elseif (strtolower($payment->paid_by) == 'cheque' && $payment->cheque_no) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->repairer->formatMoney($payment->pos_paid) . '</td>';
                            echo '<td>' . lang("cheque_no") . ': ' . $payment->cheque_no . '</td>';
                        } elseif ($payment->paid_by == 'other' && $payment->amount) {
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->repairer->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid)  . '</td>';
                            echo $payment->note ? '</tr><td colspan="2">' . lang("payment_note") . ': ' . $payment->note . '</td>' : '';
                        }else{
                            echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                            echo '<td>' . lang("amount") . ': ' . $this->repairer->formatMoney($payment->pos_paid) . '</td>';
                        }
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                }
                ?>
                <?php echo $inv->note ? '<p class="text-center">' . $this->repairer->decode_html($inv->note) . '</p>' : ''; ?>
                
        <div>
                        <?=$settings->disclaimer_sale;?>
        </div>
                 <div class="well">
                    <div class="order_barcodes text-center">
                         <?php echo $this->repairer->barcode($inv->reference_no, 'code128', 74, false); ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div style="clear:both;"></div>
        </div>

        <div id="buttons" style="padding-top:10px; text-transform:uppercase;" class="no-print">
            <hr>


            <a href="<?php echo base_url('panel/pos/view_pdf/').$inv->id; ?>"><div class="view_pdf" id="view_pdf"><?php echo lang('view_pdf');?></div></a>

            <?php
            if ($message) {
                ?>
                <div class="alert alert-success">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <?php echo is_array($message) ? print_r($message, true) : $message;?>
                </div>
                <?php
            } ?>
             
             <div class="print_button" id="print_button"><?php echo $this->lang->line('print');?></div>
            <div class="email_btn" id="email_btn"><?php echo lang('Send Email');?></div>
            <a href="<?php echo base_url('panel/sales/refund/').$inv->id; ?>"><div class="refund_btn" id="refund_btn"><?php echo lang('Refund');?></div></a>
            <?php 
            if(isset($_SERVER['HTTP_REFERER'])){
                $uri_parts = explode('?', $_SERVER['HTTP_REFERER'], 2);
            }
            ?>
            <a href="<?php echo (isset($_SERVER['HTTP_REFERER']) ? $uri_parts[0] : base_url('panel/pos')); ?>" id="editable_invoice"><?php echo lang('<< Back To POS');?></a>
        </div>
    </div>

<script>

    jQuery(document).on("click", "#email_btn", function() {
        bootbox.prompt({
            title: "Send Email",
            inputType: 'email',
            value: "<?php echo $customer?$customer->email:''; ?>",
            callback: function (email) {
                if (email) {
                    $('.bootbox-input-email').val('');
                    if(email !== null && isValidEmailAddress(email) ) {
                        $.ajax({
                            type: "post",
                            url: "<?php echo base_url('panel/pos/email') ?>",
                            data: {email: email, id: <?php echo $inv->id; ?>},
                            dataType: "json",
                            success: function (data) {
                                bootbox.alert({message: data.msg, size: 'small'});
                            },
                            error: function () {
                                bootbox.alert({message: 'Request Failed', size: 'small'});
                                return false;
                            }
                        });
                    }else{
                        bootbox.alert("<?php echo lang('Format Incorrect');?>");
                    }
                }
                
            }
        });
    });

    function isValidEmailAddress(emailAddress) {
        var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
        return pattern.test(emailAddress);
    };
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
    //  $( document ).ready(function() {
    //     setTimeout(function() {
    //         window.print();
    //     }, 500);
    // });

    auto_grow(document.getElementById("comment"));
   
</script>
</body>
</html>


