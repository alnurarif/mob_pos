<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- For Sandbox/Testing, use: -->
<!-- <script type="text/javascript" src="https://jstest.authorize.net/v1/Accept.js" charset="utf-8"></script> -->
<!-- For Production, use: -->
<script type="text/javascript" src="https://js.authorize.net/v1/Accept.js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $assets;?>dist/css/pos.css">
<style type="text/css">
.ui-autocomplete {
z-index: 99999;
}
</style>
<script type="text/javascript">

    function widthFunctions(e) {
        var wh = $(window).height(),
            lth = $('#left-top').height(),
            lbh = $('#left-bottom').height();
        $('.nav-tabs-custom').css("height", wh - 210);
        $('.nav-tabs-custom').css("min-height", 515);
        $('#left-middle').css("height", wh - lth - lbh - 170);
        $('#left-middle').css("min-height", 278);
        $('#product-list').css("height", wh - lth - lbh - 107);
        $('#product-list').css("min-height", 278);
    }
    $(window).on("resize", widthFunctions);
<?php if ($remove_posls): ?>
    if (localStorage.getItem('positems')) {
        localStorage.removeItem('positems');
    }
<?php endif; ?>
<?php if(empty($this->input->get('customer')) && empty($this->input->get('refund'))): ?>
    if (localStorage.getItem('renote')) {
        localStorage.removeItem('renote');
    }
    if (localStorage.getItem('return_surcharge')) {
        localStorage.removeItem('return_surcharge');
    }
    if (localStorage.getItem('reitems')) {
        localStorage.removeItem('reitems');
        localStorage.removeItem('positems');
    }
<?php endif; ?>
jQuery(document).ready( function($) {

    widthFunctions();
    $( ".client_name" ).select2({        
        ajax: {
            url: "<?php echo base_url(); ?>panel/customers/getAjax",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term 
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        // minimumInputLength: 2
    });

    
    $('#client_name').on("select2:select", function(e) {
        item_id = $(this).val();
        $('.edit_c').attr('data-num', item_id);
        $('.edit_c').show();
    });
});
</script>

<?php
$register_d = ($this->session->get_userdata());
$register_d = ($register_d['register_open_time']); // 2 05 2017 6AM
$closed = false; 

$register_t = date('H:i:s', strtotime($register_d));
if (strtotime($settings->auto_close_drawer) - strtotime($register_t) > 0) {
    if(time() > strtotime(date('Y-m-d', strtotime($register_d)).' '.$settings->auto_close_drawer)){
        $closed = true;
    }
}else{
    $datetime = new DateTime(date('Y-m-d', strtotime($register_d)));
    $datetime->modify('+1 day');
    $val = $datetime->format('Y-m-d');
    if(time() > strtotime($val.' '.$settings->auto_close_drawer)){
        $closed = true;
    }
}


?>
<?php if($closed): ?>
    <div class="alert alert-warning">
        <?php echo lang('Looks like you forgot to close this drawer last night. Please close the drawer and reopen it to continue using.');?>
    </div>
<?php endif; ?>

<?php if($max_drawer_lock): ?>
    <div class="alert alert-warning">
        <?php echo lang('You have too much money in your drawer right now. Please deposit money to the safe to continue using the POS.');?>
    </div>
<?php endif; ?>

<div <?php echo $closed || $max_drawer_lock ? 'style="pointer-events:none"' : '' ?>>
    

<form id="pos-sale-form" name="pos-sale-form" action="" method="post">

<!-- Main content -->
   
<div class="row">
<div class="col-md-7">
    <div class="form-group">
        <input type="text" id="add_item" placeholder="<?php echo lang('Scan/Search Code');?>" class="form-control">
    </div>

    <div class="nav-tabs-custom">
                            <!-- Nav tabs -->
                    <ul id="products-tab" class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a id="new_phones" href="#tab-new_phones" aria-controls="tab-new_phones" role="tab" data-toggle="tab">
                                <font size="0"><?php echo lang('New Phones');?></font>
                            </a>
                        </li>
                        <li role="presentation" class="">
                            <a id="used_phones" href="#tab-used_phones" aria-controls="tab-used_phones" role="tab" data-toggle="tab">
                                <font size="0"><?php echo lang('Used Phones');?></font>
                            </a>
                        </li>
                        <li role="presentation" class="">
                            <a id="accessories" href="#tab-accessories" aria-controls="tab-accessories" role="tab" data-toggle="tab">
                                <font size="0"><?php echo lang('Accessories');?></font>
                            </a>
                        </li>
                        <li role="presentation" class="">
                            <a id="cellular_plans" href="#tab-cellular_plans" aria-controls="tab-cellular_plans" role="tab" data-toggle="tab">
                                <font size="0"><?php echo lang('Cellular Plans');?></font>
                            </a>
                        </li>
                        <li role="presentation" class="">
                            <a id="other_products" href="#tab-other_products" aria-controls="tab-other_products" role="tab" data-toggle="tab">
                                <font size="0"><?php echo lang('Other Products');?></font>
                            </a>
                        </li>
                        <?php if($this->mSettings->sell_repair_parts): ?>
                        <li role="presentation" class="">
                            <a id="repairs" href="#tab-repairs" aria-controls="tab-repairs" role="tab" data-toggle="tab">
                                <font size="0"><?php echo lang('Repair Parts');?></font>
                            </a>
                        </li>
                        <?php endif; ?>

                        <li role="presentation" class="">
                            <a id="crepair" href="#tab-crepairs" aria-controls="tab-crepairs" role="tab" data-toggle="tab">
                                <font size="0"><?php echo lang('Repair Deposits');?></font>
                            </a>
                        </li>
                        <li role="presentation" class="">
                            <a id="drepair" href="#tab-drepairs" aria-controls="tab-drepairs" role="tab" data-toggle="tab">
                                <font size="0"><?php echo lang('Finished Repairs');?></font>
                            </a>
                        </li>
                        <?php if($this->Admin || $GP['pos-purchase_phones']): ?>
                            <li role="presentation" class="">
                                <a id="cp" href="#tab-cp" aria-controls="tab-cp" role="tab" data-toggle="tab">
                                    <font size="0"><?php echo lang('Buy Phones');?></font>
                                </a>
                            </li> 
                        <?php endif; ?>
                    </ul>
                    
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tab-new_phones">
                            <?php if($new_phones): foreach($new_phones as $phone): ?>
                                <button data-type="new_phone" value="<?php echo $phone->id; ?>" class="btn btn-pr btn-default">
                                    <span>
                                        <?php echo character_limiter($phone->name, 40) ?>
                                    </span>
                                </button>
                            <?php endforeach; endif;?>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="tab-used_phones">
                            <?php if($used_phones): foreach($used_phones as $phone): ?>
                                <button data-type="used_phone" value="<?php echo $phone->id; ?>" class="btn btn-pr btn-default">
                                    <span>
                                        <?php echo character_limiter($phone->name, 40) ?>
                                    </span>
                                </button>
                            <?php endforeach; endif;?>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="tab-accessories">
                            <?php if($accessories): foreach($accessories as $accessory): ?>
                                <button data-type="accessory" value="<?php echo $accessory->id; ?>" class="btn-pr btn btn-default"><span><?php echo character_limiter($accessory->name, 40) ?></span></button>
                            <?php endforeach; endif;?>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="tab-cellular_plans">
                            <?php if($plans): foreach($plans as $plan): ?>
                                <button data-type="plan" value="<?php echo $plan->id; ?>" class="btn-pr btn btn-default"><span><?php echo character_limiter($plan->name , 40)?></span></button>
                            <?php endforeach; endif;?>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="tab-other_products">
                            <?php if($others): foreach($others as $other): ?>
                                <button data-type="other" value="<?php echo $other->id; ?>" class="btn-pr btn btn-default"><span><?php echo character_limiter($other->name, 40) ?></span></button>
                            <?php endforeach; endif;?>
                        </div>
                        <?php if($this->mSettings->sell_repair_parts): ?>
                            <div role="tabpanel" class="tab-pane" id="tab-repairs">
                                <?php if($repair_items): foreach($repair_items as $repair_item): ?>
                                    <button data-type="repair" value="<?php echo $repair_item->id; ?>" class="btn-pr btn btn-default"><span><?php echo character_limiter($repair_item->name, 40) ?></span></button>
                                <?php endforeach; endif;?>
                            </div>
                        <?php endif;?>
                        <div role="tabpanel" class="tab-pane" id="tab-crepairs">
                            <?php if($crepairs): foreach($crepairs as $repair): ?>
                                <button data-type="crepair" value="<?php echo $repair->id; ?>" class="btn-pr btn btn-default"><span><?php echo character_limiter($repair->name, 40) ?></span></button>
                            <?php endforeach; endif;?>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="tab-drepairs">
                            <?php if($drepairs): foreach($drepairs as $repair): ?>
                                <button data-type="drepair" value="<?php echo $repair->id; ?>" class="btn-pr btn btn-default"><span><?php echo character_limiter($repair->name, 40) ?></span></button>
                            <?php endforeach; endif;?>
                        </div>
                        <?php if($this->Admin || $GP['pos-purchase_phones']): ?>
                            <div role="tabpanel" class="tab-pane" id="tab-cp">
                                <?php if($cp): foreach($cp as $p): ?>
                                    <button data-type="cp" value="<?php echo $p->id; ?>" class="btn-pr btn btn-default"><span><?php echo character_limiter($p->name, 40) ?></span></button>
                                <?php endforeach; endif;?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
    </div>
    <input type="hidden" name="sale_type" value="<?php echo $this->input->get('refund') ? 'refund' : 'sale' ?>">
    <input type="hidden" name="surcharge" value="" id="surcharge">
    <input type="hidden" name="renote" value="" id="renote">
    <div class="col-md-5">
            <div id="left-top">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fas  fa-user"></i>
                        </div>
                        <select id="client_name" name="client_name" class="client_name form-control" style="width: 100%" required>
                            <option selected disabled><?php echo lang('Please Select A Client Or Add A New One');?></option>
                            <option value="-1" <?php echo $this->input->get('customer') && $this->input->get('customer') == -1 ? 'selected' : ''; ?>><?php echo lang('Walk in Customer');?></option>
                            <?php 
                                foreach ($customers as $client) :
                                echo '<option '.(($this->input->get('customer') && $this->input->get('customer') == $client->id ) ? 'selected' : '' ).' value="'.$client->id.'">'.$client->first_name.' '.$client->last_name.' '.preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $client->telephone).'</option>';
                                endforeach; 
                            ?>
                        </select>
                        <a class="add_c btn input-group-addon"><i class="fas fa-user-plus"></i></a>
                        <a  style="display: none;"  class="edit_c btn input-group-addon"  id="modify_client"><i class="fas fa-edit"></i></a>
                    </div>
                </div>
            </div>
            <div id="left-middle">
                <div id="product-list">
                    <table class="table items table-striped table-bordered table-condensed table-hover"
                           id="posTable" style="margin-bottom: 0;">
                        <thead>
                        <tr>
                            <th width="40%"><?php echo lang('Product');?></th>
                            <th width="15%"><?php echo lang('Price');?></th>
                            <th width="15%"><?php echo lang('Tax');?></th>
                            <th width="10%"><?php echo lang('Discount');?></th>
                            <th width="20%"><?php echo lang('Subtotal');?></th>
                            <th style="width: 5%; text-align: center;">
                                <i class="fas fa-trash" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                            </th>
                        </tr>
                        </thead>
                        <tbody id="Pro-table" bgcolor="white">
                        </tbody>
                    </table>
                    <div style="clear:both;"></div>
                </div>
            </div>
            <div id="left-bottom">
                <table id="totalTable"
                       style="padding: 10px; width:100%; float:right; color:#000; background: #FFF;">
                    <tr>
                        <td style="padding: 5px 10px; width: 24%; border-top: 1px solid #DDD;"><?php echo lang('items');?></td>
                        <td class="text-right" style="padding: 5px 10px; width: 24%;font-size: 14px; font-weight:bold;border-top: 1px solid #DDD;padding: 5px 10px;">
                            <span id="titems">0</span>
                        </td>
                        <td style="padding: 5px 10px; width: 24%;border-top: 1px solid #DDD;"><?php echo lang('subtotal');?></td>
                        <td class="text-right" style="padding: 5px 10px; width: 24%;font-size: 14px; font-weight:bold;border-top: 1px solid #DDD;">
                            <span id="subtotal">0.00</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 24%; padding: 5px 10px;"><?php echo lang('order_tax');?>
                        </td>
                        <td class="text-right" style="width: 24%; padding: 5px 10px;font-size: 14px; font-weight:bold;">
                            <span id="ttax2">0.00</span>
                        </td>
                        <td style="width: 24%; padding: 5px 10px;"><?php echo lang('discount');?>
                        </td>
                        <td class="text-right" style="width: 24%; padding: 5px 10px;font-weight:bold;">
                            <span id="tds">0.00</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="gtotals_tds surcharge_td" style="display:none; padding: 5px 10px; border-top: 1px solid #666; border-bottom: 1px solid #333; font-weight:bold; background:#333; color:#FFF;" colspan="2">
                            <?php echo lang('Surcharge');?>
                        </td>
                        <td class="gtotals_tds surcharge_td text-right" style="display:none; padding:5px 10px 5px 10px; font-size: 14px;border-top: 1px solid #666; border-bottom: 1px solid #333; font-weight:bold; background:#333; color:#FFF;" colspan="2">
                            <span id="surcharge_span">0.00</span>
                        </td>
                        <td class="gtotals_tds" style="padding: 5px 10px; border-top: 1px solid #666; border-bottom: 1px solid #333; font-weight:bold; background:#333; color:#FFF;" colspan="2">
                            <?php echo lang('Grand Total');?>
                        </td>
                        <td class="gtotals_tds text-right" style="padding:5px 10px 5px 10px; font-size: 14px;border-top: 1px solid #666; border-bottom: 1px solid #333; font-weight:bold; background:#333; color:#FFF;" colspan="2">
                            <span id="gtotal">0.00</span>
                        </td>
                    </tr>
                </table>

                <div class="clearfix"></div>
                <div id="botbuttons" class="col-xs-12 text-center btn-group">
                    <input type="hidden" name="biller" id="biller" value="<?php echo $this->ion_auth->user()->row()->id;?>"/>
                    <div class="row">
                        <div class="col-xs-6" style="padding: 0;">
                                <button type="button" class="btn btn-danger btn-block btn-flat" style="height:67px;" 
                                id="reset">
                                    <?php echo lang('Clear Sale');?>
                                </button>
                        </div>
                        <div class="col-xs-6" style="padding: 0;">
                            <button type="button" class="btn btn-success btn-block" id="payment" style="height:67px;">
                                <i class="fas fa-money" style="margin-right: 5px;"></i><?php echo lang('Payment');?>
                            </button>
                        </div>
                    </div>
                </div>
                <div id="payment-con">
                    <?php for ($i = 1; $i <= 5; $i++) {?>
                        <input type="hidden" name="amount[]" id="amount_val_<?php echo $i?>" value=""/>
                        <input type="hidden" name="balance_amount[]" id="balance_amount_<?php echo $i?>" value=""/>
                        <input type="hidden" name="paid_by[]" id="paid_by_val_<?php echo $i?>" value="cash"/>
                        <input type="hidden" name="cc_no[]" id="cc_no_val_<?php echo $i?>" value=""/>
                        <input type="hidden" name="cc_holder[]" id="cc_holder_val_<?php echo $i?>" value=""/>
                        <input type="hidden" name="cheque_no[]" id="cheque_no_val_<?php echo $i?>" value=""/>
                        <input type="hidden" name="cc_month[]" id="cc_month_val_<?php echo $i?>" value=""/>
                        <input type="hidden" name="cc_year[]" id="cc_year_val_<?php echo $i?>" value=""/>
                        <input type="hidden" name="cc_type[]" id="cc_type_val_<?php echo $i?>" value=""/>
                        <input type="hidden" name="cc_cvv2[]" id="cc_cvv2_val_<?php echo $i?>" value=""/>
                        <input type="hidden" name="payment_note[]" id="payment_note_val_<?php echo $i?>" value=""/>
                    <?php }
                    ?>
                    <?php if($settings->random_admin): ?>
                        <input type="hidden" name="pin_code" id="pin_code_v" value=""/>
                    <?php endif; ?>
                    <input type="hidden" name="biller_id" id="biller_id" value=""/>

                </div>
                <div style="clear:both; height:5px;"></div>
            </div>
    </div>
    
    <?php echo form_close();?>
</div>
</div>
    <div class="modal fade in" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fas ">&times;</i></span><span class="sr-only"><?php echo lang('Close');?></span></button>
                <h4 class="modal-title" id="payModalLabel"><?php echo lang('Finalize Sale');?></h4>
            </div>
            <br>
            <div class="modal-body" id="payment_content">
                <div class="row">
                    <div class="col-md-9 col-sm-9">

                        <?php if($settings->random_admin): ?>
                            <div class="form-group">
                                <label for="pin_code"><?php echo lang('Enter Employee Pin Number');?></label>
                                <input name="pin_code_" type="text" id="pin_code" class="form-control" required>
                            </div>
                        <?php else: ?>
                            <div class="form-group">
                                <select required id="assigned_to" name="assigned_to" class="form-control" style="width: 100%">
                                    <?php
                                        foreach ($users as $user) :
                                        echo '<option value="'.$user->id.'">'.$user->first_name.' '.$user->last_name.'</option>';
                                        endforeach;
                                    ?>
                                </select>
                            </div>
                        <?php endif;?>

                        <div class="clearfir"></div>
                        <div id="payments">
                            <div class="well well-sm well_1">
                                <div class="payment">
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <div class="form-group">
                                                <label for="amount_1"><?php echo lang('Amount');?></label>
                                                <input name="amount[]" type="text" id="amount_1"
                                                       class="pa form-control kb-pad1 amount"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-5 col-sm-offset-1">
                                            <div class="form-group">
                                                <label for="paid_by_1"><?php echo lang('Paying By');?></label>
                                                <select style="width: 100%" name="paid_by[]" id="paid_by_1" class="form-control paid_by">
                                                    <option disabled><?php echo lang('Please Select Payment Method');?></option>
                                                    <?php if($settings->accept_cash): ?>
                                                        <option value="cash"><?php echo lang('Cash');?></option>
                                                    <?php endif; ?>
                                                    <?php if($settings->accept_cc): ?>
                                                        <option value="CC"><?php echo lang('Credit Card');?></option>
                                                    <?php endif; ?>
                                                    <?php if($settings->accept_cheque): ?>
                                                        <option value="Cheque"><?php echo lang('Cheque');?></option>
                                                    <?php endif; ?>
                                                    <?php if($settings->accept_paypal): ?>
                                                        <option value="ppp"><?php echo lang('PayPal');?></option>
                                                    <?php endif; ?>
                                                    <option value="other"><?php echo lang('Other');?></option>
                                                    <option value="authorize"><?php echo lang('Authorize.Net');?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-11">
                                            <div class="pcc_1" style="display:none;">
                                                <div class="form-group">
                                                    <input type="text" id="swipe_1" class="form-control swipe"
                                                           placeholder="<?php echo lang('Swipe Your Card');?>"/>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <input name="cc_no[]" type="text" id="pcc_no_1"
                                                                   class="form-control"
                                                                   placeholder="<?php echo lang('Credit Card Number');?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">

                                                            <input name="cc_holer[]" type="text" id="pcc_holder_1"
                                                                   class="form-control"
                                                                   placeholder="<?php echo lang('Credit Card Holder Name');?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <select name="cc_type[]" id="pcc_type_1"
                                                                    class="form-control pcc_type"
                                                                    placeholder="<?php echo lang('card_type')?>">
                                                                <option value="Visa">Visa</option>
                                                                <option value="MasterCard">Master Card</option>
                                                                <option value="Amex">Amex</option>
                                                                <option value="Discover">Discover</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <input name="cc_month[]" type="text" id="pcc_month_1"
                                                                   class="form-control"
                                                                   placeholder="<?php echo lang('Month');?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">

                                                            <input name="cc_year" type="text" id="pcc_year_1"
                                                                   class="form-control"
                                                                   placeholder="<?php echo lang('Year');?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">

                                                            <input name="cc_cvv2" type="text" id="pcc_cvv2_1"
                                                                   class="form-control"
                                                                   placeholder="<?php echo lang('CVV');?>"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="pcheque_1" style="display:none;">
                                                <div class="form-group"><?php echo lang('Cheque Number');?>
                                                    <input name="cheque_no[]" type="text" id="cheque_no_1" class="form-control cheque_no"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label><?php echo lang('Payment Note');?></label>
                                                <textarea name="payment_note[]" id="payment_note_1"
                                                          class="pa form-control kb-text payment_note"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="multi-payment"></div>
                        <button type="button" class="btn btn-primary col-md-12 addButton"><i
                                class="fas fa-plus"></i> <?php echo lang('Add More Payments');?></button>
                        <div style="clear:both; height:15px;"></div>
                    </div>
                    <div class="col-md-3">
                        <div class="font16">
                            <table class="table table-bordered table-condensed table-striped" style="margin-bottom: 0;">
                                <tbody>
                                <tr>
                                    <td style="width: 25%;"><?php echo lang('Quantity');?></td>
                                    <td style="width: 25%;" class="text-right"><span id="item_count">0.00</span></td>
                                <tr>
                                </tr>
                                    <td style="width: 25%;"><?php echo lang('Total');?></td>
                                    <td style="width: 25%;" class="text-right"><span id="twt">0.00</span></td>
                                </tr>
                                <tr>
                                    <td width="25%"><?php echo lang('Total of Payments');?></td>
                                    <td style="width: 25%;" class="text-right"><span id="total_paying">0.00</span></td>
                                <tr>
                                </tr>
                                    <td style="width: 25%;"><?php echo lang('Change');?></td>
                                    <td style="width: 25%;" class="text-right"><span id="balance">0.00</span></td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <!--  -->
            </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-block btn-lg btn-primary" id="submit-sale"><?php echo lang('submit');?></button>
            </div>
        </div>
    </div>
    </div>
<div class="modal modal-success" id="prdModal" tabindex="-1" role="dialog" aria-labelledby="prdModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i class="fas">&times;</i></span><span class="sr-only"><?php echo lang('Close');?></span></button>
                <h4 class="modal-title" id="prdModalLabel"></h4>
            </div>
            <div class="modal-body">
                <form id="discount_form" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="pdiscount" class="col-sm-4 control-label"><?php echo lang('Discount Type');?></label>
                        <div class="col-sm-8">
                            <select name="discount_uni" id="discount_uni" class="form-control">
                                <option value="1"><?php echo lang('Default');?></option>
                                <option value="2"><?php echo lang('Universal Discount Code');?></option>
                            </select>
                        </div>
                    </div>

                    <div class="default">
                        <div class="form-group">
                            <label for="pdiscount" class="col-sm-4 control-label"><?php echo lang('Discount');?></label>
                            <div class="col-sm-8">
                                <div id="did-div"></div>
                                <div id="pdiscount-div"></div>
                            </div>
                        </div>
                    </div>

                    <div class="dcode" style="display: none;">
                        <div class="form-group">
                            <label for="pdiscount" class="col-sm-4 control-label"><?php echo lang('Discount Code');?></label>
                            <div class="col-sm-8">
                                <input type="text" name="discount_code" id="discount_code" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="dcode_div" style="display: none;">
                        <div class="form-group">
                            <label for="uni_discount_input" class="col-sm-4 control-label"><?php echo lang('Discount');?></label>
                            <div class="col-sm-8">
                                <input class="form-control" name="uni_discount_input" type="number" id="uni_discount_input" step="any" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="uni_type_dd" class="col-sm-4 control-label"></label>
                            <div class="col-sm-8">
                                <select id="uni_type_dd" name="uni_type_dd" required class="form-control">
                                    <option value="1"><?php echo lang('%');?></option>
                                    <option value="2"><?php echo lang('Fixed');?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                   
                    
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="pull-left btn btn-danger"><?php echo lang('Cancel');?></button>
                <button type="submit" id="discount_form_btn" class="btn btn-primary" form="discount_form"><?php echo lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-default-filled fade " id="prModal" data-backdrop="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
      </div>
        <div class="modal-body">
        <form class="form-horizontal" id="serial_form" role="form">
            <div class="form-group" id="options_div_">
                <label for="poption" class="col-sm-4 control-label"><?php echo lang('PRODUCT OPTIONS');?></label>
                <div class="col-sm-8">
                    <div id="poptions-div"></div>
                </div>
            </div>

            <div id="id-div"></div>
            <div class="form-group">
                <div id="pserial_number-div"></div>
            </div>
            <div class="form-group" id="hidden_sap" style="display: none;">
                <div class="form-group">
                    <label for="pplans" class="col-sm-4 control-label"><?php echo lang('Activation Plan');?></label>
                    <div class="col-sm-8">
                        <?php 
                        $data = array();
                        if ($plans) {
                            foreach ($plans as $plan) {
                                $data[$plan->id] = $plan->name;
                            }
                        }
                        echo form_dropdown('sap_pplan', $data, '', 'class="form-control" id="sap_pplan"'); ?>
                    </div>
                </div>
            </div>

                <div id="pprice-div"></div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-danger" id="cancel_edit"><?php echo lang('Cancel');?></button>
        <button role="submit" form="serial_form" class="btn btn-primary" id=""><?php echo lang('submit') ?></button>
    </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal modal-default-filled fade" id="prvModal" tabindex="-1" role="dialog" aria-labelledby="prvModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" >
            <div class="modal-header">
                <h4 class="modal-title" id=""><?php echo lang('Select Variant/Plan');?></h4>
            </div>
            <div class="modal-body">
                <form class="margin-bottom-10px" id="basic-wizard" data-parsley-validate="" novalidate="">
                    <div class="first block1 show">
                        <div id="vid-div"></div>
                        <div style="min-height:87px">
                            <div class="form-group has-feedback" id="voptions_div_">
                                <label for="vpoption" class="col-sm-4 control-label"><?php echo lang('PRODUCT OPTIONS');?></label>
                                <div class="col-sm-8">
                                    <div id="vpoptions-div"></div>
                                </div>
                            </div>
                        </div>
                        
                            
                        <div class="clearfix"></div>
                        <div class="row">
                            <div style="margin-top: 20px"  class="col-md-12">
                                <button class="btn btn-danger" id="vcancel_edit"><?php echo lang('Cancel');?></button>   
                                <span class="next btn btn-primary pull-right" data-current-block="1" data-next-block="2"><?php echo lang('Next');?> <i class="fas fa-arrow-right"></i></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="second block2 hidden">
                        <div class="form-group has-feedback">
                            <label class="control-label" for="v_phone_number"><?php echo lang('Phone Number:');?></label>
                            <input class="form-control" data-parsley-pattern="^[\d\+\-\.\(\)\/\s]*$" type="text" id="v_phone_number" name="v_phone_number"  data-inputmask='"mask": "(999) 999-9999"' data-mask="" data-parsley-group="block2" required="">
                        </div>
                        <div class="checkbox-styled">
                            <input type="checkbox" name="vset_reminder" id="vset_reminder" value="1">
                            <label for="vset_reminder"><?php echo lang('Set Reminder?');?></label>
                        </div>
                        <div class="row">
                            <div style="margin-top: 20px"  class="col-md-12">
                                <span class="vreset-btn next btn btn-primary pull-left" data-current-block="2" data-next-block="1"><i class="fas fa-arrow-left"></i> <?php echo lang('Previous');?></span>
                                <button class="btn btn-success pull-right" type="submit"><?php echo lang('Send Request');?></button>
                            </div>
                        </div>
                        
                        <div class="clearfix"></div>
                    </div>
                </form>
               
            </div>
            
        </div>
    </div>
</div>

<div style="clear: both;"></div>
<style type="text/css">

</style>
<script type="text/javascript">
    <?php if($settings->random_admin): ?>
        $('#paymentModal').on('change', '#pin_code', function (e) {
            $('#pin_code_v').val($(this).val());
        });
    <?php endif; ?>
     $('#paymentModal').on('change', '#assigned_to', function (e) {
        $('#biller_id').val($(this).val());
    });
  <?php for ($i = 1; $i <= 5; $i++) {?>
        $('#paymentModal').on('change', '#amount_<?php echo $i?>', function (e) {
            $('#amount_val_<?php echo $i?>').val($(this).val());
        });
        $('#paymentModal').on('blur', '#amount_<?php echo $i?>', function (e) {
            $('#amount_val_<?php echo $i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#paid_by_<?php echo $i?>', function (e) {
            $('#paid_by_val_<?php echo $i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_no_<?php echo $i?>', function (e) {
            $('#cc_no_val_<?php echo $i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_holder_<?php echo $i?>', function (e) {
            $('#cc_holder_val_<?php echo $i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_month_<?php echo $i?>', function (e) {
            $('#cc_month_val_<?php echo $i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_year_<?php echo $i?>', function (e) {
            $('#cc_year_val_<?php echo $i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_type_<?php echo $i?>', function (e) {
            $('#cc_type_val_<?php echo $i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#pcc_cvv2_<?php echo $i?>', function (e) {
            $('#cc_cvv2_val_<?php echo $i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#cheque_no_<?php echo $i?>', function (e) {
            $('#cheque_no_val_<?php echo $i?>').val($(this).val());
        });
        $('#paymentModal').on('change', '#payment_note_<?php echo $i?>', function (e) {
            $('#payment_note_val_<?php echo $i?>').val($(this).val());
        });
        <?php }
        ?>
jQuery(document).ready( function($) {
    
    if (localStorage.getItem('return_surcharge')) {
        var i = parseFloat(localStorage.getItem('return_surcharge'));
        $('#surcharge_span').html(formatPOSDecimal(i));
        $('.gtotals_tds').attr('colspan',1);
        $('.surcharge_td').slideDown();
    }
    if (localStorage.getItem('return_surcharge')) {
        $('#surcharge').val(localStorage.getItem('return_surcharge'));
    }
    if (localStorage.getItem('renote')) {
        $('#renote').val(localStorage.getItem('renote'));
    }
    $('#paid_by_1, #pcc_type_1').select2({minimumResultsForSearch: 7});
    // Disable scroll when focused on a number input.
    $('form').on('focus', 'input[type=number]', function(e) {
        $(this).on('wheel', function(e) {
            e.preventDefault();
        });
    });
 
    // Restore scroll on number inputs.
    $('form').on('blur', 'input[type=number]', function(e) {
        $(this).off('wheel');
    });
 
    // Disable up and down keys.
    $('form').on('keydown', 'input[type=number]', function(e) {
        if ( e.which == 38 || e.which == 40 )
            e.preventDefault();
    });  
      
});
    $(document).on('change', '#purchase_type', function () {
        var purchase_type = $(this).val();
        if (purchase_type == 2) {
            $('#hidden_sap').slideDown();
        }else{
            $('#hidden_sap').hide();
        }
    });

    if (!localStorage.getItem('added_discount_codes')) {
        localStorage.setItem('added_discount_codes', '{}');
    }

    added_discount_codes = JSON.parse(localStorage.getItem('added_discount_codes'));
    // console.log(added_discount_codes);
    $(document).on('blur', '#discount_code', function () {
        code = $(this).val();
        code_used = false;
        $.each(added_discount_codes, function (x, y) {
            if (x !== $('#pdrow_id').val() && y == code) {
                code_used = true;
            }
        });
        if (code_used) {
            $('.dcode_div').hide();
            bootbox.alert("<?php echo lang('Error: This code is already used for one of the products.');?>");
            return false;
        }

        if (code !== '') {
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('panel/pos/verifyDiscountCode'); ?>',
                dataType: "JSON", 
                data: {
                    code: code,
                    type: $('#ptypeid').data('type'),
                    id: $('#ptypeid').data('id'),
                },
                success: function(result) { 
                    if (result.success) {
                        row_id = $('#pdrow_id').val();
                        added_discount_codes[row_id] = code;
                        localStorage.setItem('added_discount_codes', JSON.stringify(added_discount_codes));

                        $('.dcode_div').show();
                        $('#discount_form_btn').attr('disabled', false);
                    }else{
                       bootbox.alert(result.message);
                    }
                } 
            });
        }
    });

    $(document).on('change', '#discount_uni', function () {
        discount_uni = parseInt($('#discount_uni').val());
        if (discount_uni == 1) {
            $('.default').slideDown();
            $('.dcode').slideUp();
            $('.dcode_div').slideUp();
            $('#discount_form_btn').attr('disabled', false);
        }else{
            $('.dcode').slideDown();
            $('.dcode_div').slideUp();
            $('.default').slideUp();
            $('#discount_form_btn').attr('disabled', true);
        }
    });

    $('#cancel_edit').on( "click", function () {
        event.preventDefault();
        var row = $('#' + $('#prow_id').val());
        var item_id = $('#prow_id').val();
        delete items[item_id];
        localStorage.setItem('positems', JSON.stringify(items));
        loadItems();
        $('#prModal').modal('hide');
    });

    $('#vcancel_edit').on( "click", function () {
        event.preventDefault();
        var row = $('#' + $('#prvow_id').val());
        var item_id = $('#prvow_id').val();
        delete items[item_id];
        localStorage.setItem('positems', JSON.stringify(items));
        loadItems();
        $('#prvModal').modal('hide');
    });
    
    var error = false;
    var ajax = false;
    var others = false;
    $("#serial_form").on( "submit", function( event ) {
        event.preventDefault();
        var row = $('#' + $('#prow_id').val());
        var item_id = $('#prow_id').val();
        var type = $('#prtype').val();
        var item = items[item_id];

       
        if(item.options !== null && item.options !== false) {
            others = true;
            var opt = $('#poption').val();
            $.each(item.options, function () {
                if(this.id == opt && this.price != '' && this.price != null) {
                    if (item.row.type == 'plans') {
                        cost = parseFloat(this.cost);
                    }
                    price = parseFloat(this.price);
                    name = (this.variant_name);
                }
            });
            items[item_id].price = price;
            if (item.row.type == 'plans') {
                items[item_id].price = cost;
            }
            items[item_id].option = $('#poption').val() ? $('#poption').val() : '',
            items[item_id].option_name = name,
            items[item_id].option_selected = true;
        }
        if (item.row.type == 'other' && parseInt(item.row.variable_price) == 1) {
            others = true;
            if( !$('#pprice').val() ) {
                bootbox.alert("<?php echo lang('Price cannot be empty');?>");
                return;
            }
            if( $('#pprice').val() <= 0 ) {
                bootbox.alert("<?php echo lang('Please enter a valid price');?>");
                return;
            }

            price = $('#pprice').val();
            if (parseInt(item.row.cash_out)) {
                price = price * -1;
            }

            items[item_id].item_details = '';
            pdescription = $('#pdescription').val();
            if (pdescription) {
                items[item_id].item_details = pdescription;
            }
           
            items[item_id].cost = 0;
            pcost = $('#pcost').val();
            if (pcost) {
                items[item_id].cost = pcost;
            }
           
            items[item_id].cost = pcost,
            items[item_id].price = price,
            items[item_id].row.variable_price = 0;
        }

        error = false;
        ajax = false;
        if(item.is_serialized) {
            if( !$('#pserial_number').val() ) {
                bootbox.alert("<?php echo lang('Serial Number cannot be empty');?>");
                return;
            }
            var type    = $('#pserial_number').data('type');
            var id      = $('#pserial_number').data('id');
            var serial  = $('#pserial_number').val();
            ajax        = true;
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('panel/pos/verifyProductSerial'); ?>',
                dataType: "JSON", // edit: fixed ;)
                data: {
                    type: $('#pserial_number').data('type'),
                    id: $('#pserial_number').data('id'),
                    term: $('#pserial_number').val(),
                },
                success: function(data) { 
                    console.log(data);
                    if (data) {
                        items[item_id].serial_number =  serial?serial:'';
                        items[item_id].serialed =  true;
                    }else{
                        $('#prModal').appendTo("body").modal({backdrop: 'static', keyboard: false});
                        // $('#prModal').css('display', 'block');
                        bootbox.alert("<?php echo lang('Incorrect Serial Number');?>");
                        error = true;
                    }
                } 
            });
        }

        var purchase_type = null;
        if (document.getElementById('purchase_type')) {
            purchase_type = $('#purchase_type').val()
            if(purchase_type == 2) {
                items[item_id].price = parseFloat(item.row.activation_price);
                items[item_id].row.discount_type = parseInt(item.row.discount_type2);
                items[item_id].row.max_discount = parseInt(item.row.max_discount2);
                items[item_id].purchase_type = purchase_type;
                sap_pplan = $('#sap_pplan').val()
                addbyTypeAndID('plan', sap_pplan);
                s_activation_items = JSON.parse(item.activation_items);
                $.each(s_activation_items, function () {
                    addbyTypeAndID(this.type, parseInt(this.id));
                });
            }else{
                items[item_id].purchase_type = purchase_type;
            }
        }
        
        if (ajax) {
            $(document).ajaxStop(function () {
                if (error) {
                    return;
                }else{
                    if ($('#pserial_number').hasClass('ui-autocomplete-input')) {
                       $('#pserial_number').autocomplete("destroy");
                    }
                    localStorage.setItem('positems', JSON.stringify(items));
                    $('#prModal').modal('hide');
                    loadItems();
                    return;
                }
            });
        }else{
            if ($('#pserial_number').hasClass('ui-autocomplete-input')) {
               $('#pserial_number').autocomplete("destroy");
            }
            localStorage.setItem('positems', JSON.stringify(items));
            $('#prModal').modal('hide');
            loadItems();
            return;
        }
    });

    $("#basic-wizard").on( "submit", function( event ) {
        event.preventDefault();
        $('#basic-wizard').parsley().validate();

        if (!$('#basic-wizard').parsley().isValid()){
            return false;
        }
        data = $(this).serialize();

        var row = $('#' + $('#prvow_id').val());
        var item_id = $('#prvow_id').val();
        var type = $('#prvtype').val();
        var item = items[item_id];
        if(item.options !== null && item.options !== false) {
            others = true;
            var opt = $('#vpoption').val();
            $.each(item.options, function () {
                if(this.id == opt && this.price != '' && this.price != null) {
                    cost = parseFloat(this.cost);
                    price = parseFloat(this.price);
                    name = (this.variant_name);
                }
            });
            var vset_reminder = 0;
            if(document.getElementById('vset_reminder').checked) {
                var vset_reminder = 1;
            } 
            items[item_id].price = cost;
            items[item_id].option = $('#vpoption').val() ? $('#vpoption').val() : '',
            items[item_id].option_name = name,
            items[item_id].option_selected = true;
            items[item_id].phone_number = $('#v_phone_number').inputmask('unmaskedvalue');
            items[item_id].set_reminder = vset_reminder;
        }

        localStorage.setItem('positems', JSON.stringify(items));
        $('#prvModal').modal('hide');
        loadItems();
        return;
    });
    $("#add_item").autocomplete({
        source: function(request, response) {
            var id = $("ul#products-tab li.active a").attr('id');
            var value = $('#add_item').val();
            $.getJSON('<?php echo site_url('panel/pos/suggestions'); ?>', { type: id, term: value }, 
                      response);
        },
        minLength: 1,
        delay: 250,
        autoFocus: true,
        select: function( event, ui ) {
            $( "#id_city" ).val( ui.item.id );
            $(this).closest('form').submit();
        },
        focus: function( event, ui ) { event.preventDefault(); },
        select: function (event, ui) {
            event.preventDefault();
            // console.log(ui);
            if (ui.item.id !== 0) {
                var row = add_pos_item(ui.item);
                if (row)
                    $(this).val(''); 
            } else {
                var row = add_pos_item(ui.item);
                if (row)
                    $(this).val(''); 
            }
        }
    });

    var index = 0;
    function loadItems(edit_items = true) {
        $('#prModal').modal('hide');
        if (localStorage.getItem('positems')) {
            items = JSON.parse(localStorage.getItem('positems'));
            var pp = 0;
            var total_tax = 0;
            total = 0;
            count = 1;
            an = 1;
            product_tax = 0;
            invoice_tax = 0;
            product_discount = 0;
            order_discount = 0;
            total_discount = 0;


            $("#posTable tbody").empty();
            $.each(items, function () {

                var row_no = this.row_id;
                var item_id = this.product_id;
                var price = this.price;
                var cost = this.cost;
                var sel_opt = '';
                var item_option = this.option;
                var discount = this.discount;
                var type = this.row.type;
                var code = this.code;
                var sale_item_id = this.row.sale_item_id;
                var warranty_id = (this.row.warranty_id);
                var activation_spiff = parseFloat(this.activation_spiff);
                $.each(this.options, function (x, y) {
                    if(y.id == item_option) {
                        if (type !== 'repair') {
                            cost = y.cost;
                        }
                        if (type == 'new_phone' || type == 'used_phone') {
                            code = y.name;
                        }   
                        if (type == 'plans') {
                            activation_spiff = y.activation_spiff
                        }

                        sel_opt = y.name;
                        price = y.price;
                        if (type === 'cp') {
                            price = 0-parseFloat(y.cost);
                        }
                    }
                });
                var product_option = '';
                var product_variant = '';
                var variable_price_picked = true;
                if (this.row.type == 'other' && parseInt(this.row.variable_price) == 1) {
                    variable_price_picked = false;
                }

                var opt = '<p style="margin: 12px 0 0 0;">n/a</p>';
                $('#options_div_').hide();
                if (this.variants == true && this.option_selected == false && this.type == "plans") {
                    $('#vid-div').html('<input type="hidden" name="prvow_id" id="prvow_id" value="'+row_no+'"><input type="hidden" name="prvtype" id="prvtype" value="'+this.row.type+'">');

                    if(this.options !== null && this.options !== false) {
                            $('#voptions_div_').show();
                            var o = 1;
                            opt = $("<select data-parsley-group=\"block1\" required=\"required\" id=\"vpoption\" name=\"vpoption\" class=\"form-control select\" />");
                            $.each(this.options, function (x, y) {
                            if(o == 1) {
                                if (type !== 'repair') {
                                    cost = y.cost;
                                }
                                price = y.price;
                                sel_opt= y.name;
                                if(product_option == '') { product_variant = y.id; } else { product_variant = product_option; }
                            }
                            $("<option/>", {value: y.id, text: y.name}).appendTo(opt);
                            o++;
                        });
                        $('#vpoptions-div').html(opt);
                        $(".vreset-btn").click();
                        $("input[name=vpoption]").val('');
                        $("input[name=v_phone_number]").val('');
                        document.getElementById('vset_reminder').checked = false;
                        $('#prvModal').appendTo("body").modal({backdrop: 'static', keyboard: false});
                    } else {
                        product_variant = 0;
                    }
                }

                if ((this.variants == true && this.option_selected == false && this.type !== "plans") || (!this.serialed) || !variable_price_picked || ((type == 'new_phone' || type == 'used_phone' ) && !this.purchase_type)) {
                    var opt = '<p style="margin: 12px 0 0 0;">n/a</p>';
                    $('#options_div_').hide();
                    if(this.options !== null && this.options !== false) {
                            $('#options_div_').show();
                            var o = 1;
                            opt = $("<select id=\"poption\" name=\"poption\" class=\"form-control select\" />");
                            $.each(this.options, function (x, y) {
                            if(o == 1) {
                                if (type !== 'repair') {
                                    cost = y.cost;
                                }
                                if (type == 'new_phone' || type == 'used_phone') {
                                    code = y.name;
                                }
                                if (type == 'plans') {
                                    activation_spiff = y.activation_spiff
                                }
                                price = y.price;
                                if (type === 'cp') {
                                    price = 0-parseFloat(y.cost);
                                }
                                sel_opt = y.name;
                                if(product_option == '') { product_variant = y.id; } else { product_variant = product_option; }
                            }
                            
                            $("<option/>", {value: y.id, text: y.name}).appendTo(opt);
                            o++;
                        });
                    } else {
                        product_variant = 0;
                    }
                    var serial = '';
                    if (this.is_serialized) {
                        serial = $("<div class=\"form-group\"><label class=\"col-sm-4 control-label\"><?php echo lang('Serial Number');?>: </label><div class=\"col-sm-8\"><input value=\""+(this.serial_number?this.serial_number:'')+"\" autocomplete=\"off\" id=\"pserial_number\" name=\"pserial_number\" data-id=\""+item_id+"\" data-type=\""+type+"\" class=\"form-control select\" /></div>");
                    }

                    var purchase_type = '';
                    if (type == 'new_phone' || type == 'used_phone') {
                        // if ( == ) {}
                        purchase_type = $("<div class=\"form-group\"><label class=\"col-sm-4 control-label\"><?php echo lang('Purchase Type');?>: </label><div class=\"col-sm-8\"><select name='purchase_type' id='purchase_type' class='form-control'><option value=\"1\"><?php echo lang('Outright Purchase');?></option><option value=\"2\"><?php echo lang('Activation Purchase');?></option></select></div>");
                    }

                    var script = document.createElement('script');
                    script.type = 'text/javascript';
                    script.text = "$(\"#pserial_number\").autocomplete({source:function(e,a){$.ajax({type:\"post\",url:'<?php echo site_url('panel/pos/getProductSerials'); ?>',dataType:\"JSON\",data:{type:$(\"#pserial_number\").data(\"type\"),id:$(\"#pserial_number\").data(\"id\"),term:$(\"#pserial_number\").val()},success:function(e){a(e)}})}});";
                    $('#pserial_number-div').html(serial);
                    $('#pserial_number-div').append(purchase_type);
                    $('#pserial_number-div').append(script);
                    $('#poptions-div').html(opt);
                    $('#id-div').html('<input type="hidden" name="prow_id" id="prow_id" value="'+row_no+'"><input type="hidden" name="prtype" id="prtype" value="'+this.row.type+'">');

                    var vprice = '';
                    if (!variable_price_picked) {
                        vprice = $("<div class=\"form-group\"><label class=\"col-sm-4 control-label\"><?php echo lang('Price');?>: </label><div class=\"col-sm-8\"><input id=\"pprice\" name=\"pprice\" class=\"form-control\" /></div></div><div class=\"form-group\"><label class=\"col-sm-4 control-label\"><?php echo lang('cost');?>: </label><div class=\"col-sm-8\"><input id=\"pcost\" name=\"pcost\" class=\"form-control\" /></div></div><div class=\"form-group\"><label class=\"col-sm-4 control-label\"><?php echo lang('description');?>: </label><div class=\"col-sm-8\"><textarea id=\"pdescription\" name=\"pdescription\" class=\"form-control\" /></div></div>");
                    }

                    $('#pprice-div').html(vprice);
                    if ((!this.refund_item) && this.type !== 'cp') {
                        console.log(this);
                        index++;
                        console.log(index);

                        setTimeout(function () {
                            $('#prModal').appendTo("body").modal({backdrop: 'static', keyboard: false});
                            // $('#prModal').css('display', 'block');
                        }, 300);

                    }
                }

                var product_tax = 0;
                var pr_tax = this.pr_tax;
                var pr_tax_val = 0, pr_tax_rate = [];
                var pr_tax_val_fixed = 0;
                if(parseInt(this.taxable) == 1){
                    $.each(pr_tax, function (tax, tax_detaild) {
                        if (tax_detaild !== false) {
                            if (tax_detaild.type == 1) {
                                pr_tax_val += parseFloat(tax_detaild.rate);
                                pr_tax_rate[tax_detaild.id] = formatPOSDecimal(tax_detaild.rate) + '%';
                            } else if (parseInt(tax_detaild.type) == 2) {
                                pr_tax_val_fixed += parseFloat(tax_detaild.rate);
                                pr_tax_rate[tax_detaild.id] = formatPOSDecimal(tax_detaild.rate);
                            }
                        }
                    });
                    percent_tax = formatPOSDecimal(parseFloat(Math.abs(price)-discount) * parseFloat(pr_tax_val) / (100), 4);
                    product_tax = parseFloat(percent_tax) + parseFloat(pr_tax_val_fixed);
                }
                if (this.refund_item || (this.row.type == 'other' && parseInt(this.row.cash_out))) {
                    product_tax = 0-product_tax;
                }

                if (this.row.type == 'drepairs' || this.row.type == 'crepairs') {
                    product_tax = parseFloat(this.row.tax);
                }
                invoice_tax += product_tax;
                pr_tax_rate = pr_tax_rate.filter(function(e){ return e === 0 || e }).join(', ');
                var subtotal = (parseFloat(price)+parseFloat(product_tax)-parseFloat(discount));
                var newTr = $('<tr id="row_' + row_no + '" class="item_' + this.row.id + '" data-item-id="' + row_no + '"></tr>');
                

                tr_html = '<td style="width: 40%;"><input name="item_id[]" id="item_id" type="hidden" value="' + this.product_id + '"><input name="item_details[]" id="item_details" type="hidden" value="' + this.item_details + '"><input name="activation_spiff[]" id="activation_spiff" type="hidden" value="' + activation_spiff + '"><input name="disount_code[]" id="disount_code" type="hidden" value="' + this.discount_code_used + '"><input name="product_warranty[]" id="product_warranty" type="hidden" value="' + warranty_id + '"><input name="item_discount[]" id="item_discount" type="hidden" value="' + discount + '"><input name="item_type[]" id="item_type" type="hidden" value="' + this.row.type + '"><input name="item_cost[]" id="item_cost" type="hidden" value="' + cost + '"><input name="phone_classification[]" id="phone_classification" type="hidden" value="' + this.phone_classification + '"><input name="used_phone_vals[]" id="used_phone_vals" type="hidden" value="' + this.used_phone_vals + '"><input name="item_name[]" type="hidden" value="' + this.name + '"><input name="items_restock[]" type="hidden" value="' + this.items_restock + '"><input name="item_code[]" type="hidden" value="' + code + '"><input name="item_serial[]" type="hidden" value="' + this.serial_number + '"><input name="product_option[]" type="hidden" class="roption" value="' + item_option + '"><span class="sname" id="name_' + row_no + '">' + code +' - '+ this.name +(sel_opt != '' ? ' ('+sel_opt+')' : '')+'<br><small>'+this.item_details+'</small></span>';
                if ((!this.refund_item)) {
                    <?php if($this->Admin || $GP['pos-add_discounts']): ?>
                        tr_html += ' <button id="' + row_no + '" data-item="' + item_id + '" data-price="' + price + '" title="'+"<?php echo lang('Edit');?>"+'" style="cursor:pointer;" class="discount btn btn-xs btn-primary"><i class="fas fa-cut" aria-hidden="false"></i></button>';
                    <?php endif; ?>
                }
                if (this.refund_item){
                    tr_html += '<input name="refund_item[]" type="hidden" value="1">';
                    tr_html += '<input name="add_to_stock[]" type="hidden" value="'+this.add_to_stock+'">';
                    tr_html += '<input name="sale_item_id[]" type="hidden" class="rsiid" value="' + sale_item_id + '">';

                }else{
                    tr_html += '<input name="refund_item[]" type="hidden" value="0">';
                    tr_html += '<input name="add_to_stock[]" type="hidden" value="0">'
                    tr_html += '<input name="sale_item_id[]" type="hidden" value="0">';
                    ;
                }
                tr_html += '<input name="phone_number[]" type="hidden" value="'+this.phone_number+'">';
                tr_html += '<input name="set_reminder[]" type="hidden" value="'+this.set_reminder+'">';
                tr_html += '</td>';
                tr_html += '<td style="width: 15%;">'+formatMoney(price)+'<input class="form-control text-center rprice" name="item_price[]" type="hidden" value="' + (price) + '" data-id="' + row_no + '" data-item="' + this.product_id + '" id="item_price_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td style="width: 15%;">'+formatMoney(product_tax)+'<input class="form-control text-center rtax" name="item_tax[]" type="hidden" value="' + formatPOSDecimal(product_tax) + '" data-id="' + row_no + '" data-item="' + this.product_id + '" id="item_price_' + row_no + '" onClick="this.select();"><input class="form-control text-center" name="item_tax_id[]" type="hidden" value="' + encodeURIComponent(JSON.stringify(pr_tax)) + '"></td>';
                tr_html += '<td style="width: 10%;">'+formatMoney(discount)+'</td>';
                tr_html += '<td style="width: 20%;">'+formatMoney((subtotal))+'</td>';
                if (edit_items) {
                    tr_html += '<td style="width: 5%;" class="text-center"><i class="fas fa-times tip del" id="' + row_no + '" title="<?php echo lang('remove');?>" style="cursor:pointer;"></i></td>';
                }else{
                    tr_html += '<td style="width: 5%;" class="text-center">-</td>';
                }
                newTr.html(tr_html);
                newTr.appendTo("#posTable");
                total += parseFloat(subtotal);
                count += 1;
                an++;
                pp += (parseFloat(price));
                total_tax += parseFloat(product_tax);
                total_discount += parseFloat(discount);
                $('.item_' + item_id).addClass('warning');

            });

            total = formatPOSDecimal(total);
            product_tax = formatPOSDecimal(total_tax);

            // Totals calculations after item addition
            surcharge = 0;
            if (localStorage.getItem('return_surcharge')) {
                surcharge = parseFloat(localStorage.getItem('return_surcharge')); 
            }
            var gtotal = (parseFloat(total)+parseFloat(surcharge));
            $('#subtotal').text(formatMoney(total-total_tax));
            $('#titems').text((an - 1));
            $('#total_items').val((parseFloat(count) - 1));
            $('#tds').text(formatMoney(total_discount));
            $('#ttax2').text(formatMoney(invoice_tax));
            $('#gtotal').text(formatMoney(gtotal));
            $('#gtotal').val(Math.abs(gtotal));
            
        }
    }
    
    $('#payment').on( "click", function () {
        if ($('#client_name').val() == '' || $('#client_name').val() == null) {
            bootbox.alert("<?php echo lang('Please select an existing customer or add a new one');?>");
            return;
        } else {
            surcharge = 0;
            if (localStorage.getItem('return_surcharge')) {
                surcharge = parseFloat(localStorage.getItem('return_surcharge')); 
            }
            var twt = formatDecimal(((total + invoice_tax) - order_discount) + surcharge);
            if (count == 1) {
                bootbox.alert("<?php echo lang('Please add a product before payment. Thank you!');?>");
                return false;
            }

            gtotal = formatDecimal(twt);
            <?php if(!$this->Admin && !$GP['pos-checkout_negative']): ?>
                if (gtotal < 0) {
                    bootbox.alert("<?php echo lang('You do not have permissions to check out with a negative total');?>");
                    return;
                }
            <?php endif; ?>
            $('#twt').text($('#gtotal').html());
            $('#item_count').text(count - 1);
            $('#paymentModal').appendTo("body").modal('show');
            $('#amount_1').focus();
        }
    });
    function formatCNum(x) {
        if (site.settings.decimals_sep == ',') {
            var x = x.toString();
            var x = x.replace(",", ".");
            return parseFloat(x);
        }
        return x;
    }
    function calculateTotals() {
        gtotal = $('#gtotal').val();
        var total_paying = 0;
        var ia = $(".amount");
        $.each(ia, function (i) {
            var this_amount = formatCNum($(this).val() ? $(this).val() : 0);
            total_paying += parseFloat(this_amount);
        });
        $('#total_paying').text(formatMoney(total_paying));
       
        $('#balance').text(formatMoney(total_paying - gtotal));
        $('#balance_' + pi).val(formatDecimal(total_paying - gtotal));
        total_paid = total_paying;
        grand_total = gtotal;
    }
    function sendPaymentDataToAnet(authorize) {
	    $('#submit-sale').attr('disabled', true);
	    var secureData = {}; authData = {}; cardData = {};
		
		cc = document.getElementById('pcc_no_'+authorize).value.split(" ").join("");
		month = document.getElementById('pcc_month_'+authorize).value;
		year = document.getElementById('pcc_year_'+authorize).value;
		ccv = document.getElementById('pcc_cvv2_'+authorize).value;

		
	    // Extract the card number, expiration date, and card code.
	    cardData.cardNumber = $.trim(cc);
	    cardData.month 		= $.trim(month);
	    cardData.year 		= $.trim(year);
	    cardData.cardCode 	= $.trim(ccv);
	    secureData.cardData = cardData;
	
	    authData.clientKey 	= "<?php echo $settings->authorize_client_key; ?>";
	    authData.apiLoginID = "<?php echo $settings->authorize_login_id; ?>";
	    secureData.authData = authData;
	
	    // Pass the card number and expiration date to Accept.js for submission to Authorize.Net.
	    Accept.dispatchData(secureData, responseHandler);
	
	    // Process the response from Authorize.Net to retrieve the two elements of the payment nonce.
	    // If the data looks correct, record the OpaqueData to the console and call the transaction processing function.
	    function responseHandler(response) {
		    if (response.messages.resultCode === "Error") {
			    $('#submit-sale').attr('disabled', false);
	            for (var i = 0; i < response.messages.message.length; i++) {
	                console.log(response.messages.message[i].code + ": " + response.messages.message[i].text);
	            }
	            bootbox.alert("acceptJS library error!")
	        } else {
		    	processTransaction(response.opaqueData);
	        }
	    }
	    
	}
		
	function processTransaction(responseData) {
		$("#pos-sale-form").append('<input type="hidden" name="dataDesc" value="'+responseData.dataDescriptor+'"><input type="hidden" name="dataValue" value="'+responseData.dataValue+'">');
		
		$('#submit-sale').attr('disabled', true);
            <?php if($settings->random_admin): ?>
            if ($('#pin_code').val() === '') {
                $('#submit-sale').attr('disabled', false);
                bootbox.alert("<?php echo lang('Please enter your employee pin number');?>");
                return false;
            }else{
                var pin_code = $('#pin_code').val();
                jQuery.ajax({
                    type: "POST",
                    url: base_url + "panel/pos/verifyPin",
                    data: "pin_code=" + encodeURI(pin_code),
                    cache: false,
                    dataType: "html",
                    success: function (data) {
                        if (data == 'false') {
                            $('#submit-sale').attr('disabled', false);
                            bootbox.alert("<?php echo lang('Incorrect Pin Code');?>");
                            return;
                        }
                        return;
                    }
                });
            }
            <?php endif; ?>

        $(document).on('click', '#submit-sale', function () {
            $('#submit-sale').attr('disabled', true);
            if (total_paid == 0 || total_paid < grand_total) {
                 bootbox.confirm("<?php echo lang('paid_l_t_payable');?>", function (res) {
                  if (res == true) {
                    $('#pos_note').val(localStorage.getItem('posnote'));
                    $('#submit-sale').text('<?php echo lang('loading');?>').attr('disabled', true);

                    $.post( "<?php echo base_url();?>panel/inventory/removeSelected");
                    $('#pos-sale-form').submit();
                  }
                });
                return false;
            } else {
                $('#pos_note').val(localStorage.getItem('posnote'));

                $.post( "<?php echo base_url();?>panel/inventory/removeSelected");
                $('#pos-sale-form').submit();
            }
        });

          

	}
    $(document).on('click', '#submit-sale', function () {
        if (parseFloat($('#gtotal').val()) < 0) {
            $('#pos-sale-form').submit();
        }else{
	        var authorize = false;
	        $('.paid_by').each(function(){
		        if($(this).val() == 'authorize'){
			    	authorize = $(this).attr('id');
			    }
		    });
		    
	        if(authorize){
		        authorize = authorize.substring(8);
				sendPaymentDataToAnet(authorize);
	        }else{
		        $('#submit-sale').attr('disabled', true);
	            <?php if($settings->random_admin): ?>
	            if ($('#pin_code').val() === '') {
	                $('#submit-sale').attr('disabled', false);
	                bootbox.alert("<?php echo lang('Please enter your employee pin number');?>");
	                return false;
	            }else{
	                var pin_code = $('#pin_code').val();
	                jQuery.ajax({
	                    type: "POST",
	                    url: base_url + "panel/pos/verifyPin",
	                    data: "pin_code=" + encodeURI(pin_code),
	                    cache: false,
	                    dataType: "html",
	                    success: function (data) {
	                        if (data == 'false') {
	                            $('#submit-sale').attr('disabled', false);
	                            bootbox.alert("<?php echo lang('Incorrect Pin Code');?>");
	                            return;
	                        }
	                        return;
	                    }
	                });
	            }
	            <?php endif; ?>
	   

                if (total_paid == 0 || total_paid < grand_total) {
                     bootbox.confirm("<?php echo lang('paid_l_t_payable');?>", function (res) {
                      if (res == true) {
                        $('#pos_note').val(localStorage.getItem('posnote'));
                        $('#submit-sale').text('<?php echo lang('loading');?>').attr('disabled', true);

                        $.post( "<?php echo base_url();?>panel/inventory/removeSelected");
                        $('#pos-sale-form').submit();
                      }
                    });
                    return false;
                } else {
                    $('#pos_note').val(localStorage.getItem('posnote'));

                    $.post( "<?php echo base_url();?>panel/inventory/removeSelected");
                    $('#pos-sale-form').submit();
                }
	            
	        }
	        	        
        }
    });

   
    $(document).on('focus', '.amount', function () {
        pi = $(this).attr('id');
        calculateTotals();
    }).on('blur', '.amount', function () {
        calculateTotals();
    }).on('keyup', '.amount', function () {
        calculateTotals();
    });

    $(document).on('click', '.del', function () {
        var id = $(this).attr('id');
        var item = items[id];
        if (item.serial_search) {
            $.post( "<?php echo base_url();?>panel/inventory/removeSelectedByStockID", { id: item.row.stock_id} );
        }
        $(this).closest('#row_' + id).remove();
        delete items[id];
        if(items.hasOwnProperty(id)) { } else {
            localStorage.setItem('positems', JSON.stringify(items));
            loadItems();
            return;
        }
    });
    
    items = {};
    function add_pos_item(item, edit_item=true) {
        if (item == null) {
            return false;
        }

        if (item.serial_search) {
            $.post( "<?php echo base_url();?>panel/inventory/setSelected", { stock_id: item.row.stock_id} );
        }

        if (item.type == 'other' && parseInt(item.row.keep_stock) == 0) {
            item.cost = item.row.no_stock_cost;
        }else{
            t_qty = parseInt(item.qty);
            current_type = item.type;
            selected_qty = 0;
            $.each(items, function(){
                if (this.type == current_type && this.product_id == item.product_id) {
                    selected_qty += 1;
                }
            });
            if (current_type == 'drepairs' || current_type == 'crepairs') {
               $('#client_name').val(item.row.client_id).trigger('change');
            }else{
                 if (selected_qty+1 > t_qty) {
                    bootbox.alert("<?php echo lang('You dont have this item in stock');?>");
                    return false;
                }
            }
        }
        
        item_id = item.row_id;
        items[item_id] = item;
        localStorage.setItem('positems', JSON.stringify(items));
        loadItems(edit_item);
        return true;
    }


    $(document).on('click', '.btn-pr', function (e) {
        e.preventDefault();
        code = $(this).val(),
        type = $(this).data('type'),
        $.ajax({
            type: "get",
            url: "<?php echo site_url('panel/pos/getProductDataByTypeAndID')?>",
            data: {code: code, type: type},
            dataType: "json",
            success: function (data) {
                // e.preventDefault();
                if (data !== null) {
                    $.each(data, function () {
                        add_pos_item(this);
                    });
                } else {
                    bootbox.alert("<?php echo lang('No Match Found');?>");
                }
            }
        });
    });
    function addbyTypeAndID(type, code) {
        $.ajax({
            type: "get",
            url: "<?php echo site_url('panel/pos/getProductDataByTypeAndID')?>",
            data: {code: code, type: type},
            dataType: "json",
            success: function (data) {
                if (data !== null) {
                    $.each(data, function () {
                        add_pos_item(this);
                    });
                }
            }
        });
    }
    <?php if($this->Admin || $GP['pos-add_discounts']): ?>

    /* -----------------------
     * Edit Row Modal Hanlder
     ----------------------- */
     $(document).on('click', '.discount', function (event) {
        event.preventDefault();
        $('.dcode').hide();
        $('.dcode_div').hide();
        $('.default').hide();
        var row = $(this).closest('tr');
        var row_id = row.attr('id');
        item_id = row.attr('data-item-id');
        price = $(this).data('price');

        item = items[item_id];
       

        $('#prdModalLabel').text(item.row.name + ' (' + item.row.code + ')');

        console.log(item);
        if (item.row.max_discount) {
            var max_discount_rate = item.row.max_discount;
            var max_discount_type = item.row.discount_type; // 1: %, 2: Fixed
            var max_discount = parseFloat(max_discount_rate);
            if (max_discount_type == 1) {
                var max_discount = price*(parseFloat(max_discount_rate)/100);
            }
            $('#pdiscount-div').html('<input type="hidden" value="'+max_discount_rate+'" id="max_discount_rate"><input type="hidden" value="'+max_discount_type+'" id="max_discount_type"><input class="form-control" value="'+parseFloat(item.discount)+'" type="number" id="discount_input" step="any" max="'+max_discount+'" /><br><select id="type_dd" class="form-control"><option value="1">'+"<?php echo lang('%');?>"+'</option><option value="2">'+"<?php echo lang('Fixed');?>"+'</option></select><br><span id="max_dd_value"></span>');
            $("#type_dd option[value='2']").prop("selected", true);
            $('#max_dd_value').html('<?php echo lang('Max Discount allowed is');?> "'+ formatMoney(parseFloat(max_discount).toFixed(2)) + '".');
            $('.dcode').slideUp();
            $('.dcode_div').slideUp();
            $('.default').slideDown();
            $('#discount_form_btn').attr('disabled', false);
            $('#discount_uni option:first').attr('disabled', false);
            $('#discount_uni option:first').attr('selected', true);
            $('#discount_uni option:last').attr('selected', false);
        }else{

            var max_discount_rate = item.price;
            var max_discount_type = 2; // 1: %, 2: Fixed
            var max_discount = parseFloat(max_discount_rate);
            if (max_discount_type == 1) {
                var max_discount = price*(parseFloat(max_discount_rate)/100);
            }
            $('#pdiscount-div').html('<input type="hidden" value="'+max_discount_rate+'" id="max_discount_rate"><input type="hidden" value="'+max_discount_type+'" id="max_discount_type"><input class="form-control" value="'+parseFloat(item.discount)+'" type="number" id="discount_input" step="any" max="'+max_discount+'" /><br><select id="type_dd" class="form-control"><option value="1">'+"<?php echo lang('%');?>"+'</option><option value="2">'+"<?php echo lang('Fixed');?>"+'</option></select><br><span id="max_dd_value"></span>');
            $("#type_dd option[value='2']").prop("selected", true);
            $('#max_dd_value').html('<?php echo lang('Max Discount allowed is');?> "'+ formatMoney(parseFloat(max_discount).toFixed(2)) + '".');
            $('.dcode').slideUp();
            $('.dcode_div').slideUp();
            $('.default').slideDown();
            $('#discount_form_btn').attr('disabled', false);
            $('#discount_uni option:first').attr('disabled', false);
            $('#discount_uni option:first').attr('selected', true);
            $('#discount_uni option:last').attr('selected', false);

        }
       
        $('#did-div').html('<input type="hidden" name="pdrow_id" id="pdrow_id" value="'+item_id+'"><input type="hidden" name="price_dd" id="price_dd" value="'+price+'"><input type="hidden" id="ptypeid" name="ptypeid" data-id="'+item.row.id+'" data-type="'+item.type+'">');
        $('#prdModal').appendTo("body").modal('show');
    });
    
    $(document).on('change', '#type_dd', function () {
        var type = $(this).val();
        var price = $('#price_dd').val();
        var max_discount_rate = $('#max_discount_rate').val();
        var max_discount_type = $('#max_discount_type').val();
        
        var max_discount = parseFloat(max_discount_rate);
        if (max_discount_type == 1) {
            var max_discount = price*(parseFloat(max_discount_rate)/100);
        }

        // price = 5 || max  0.25
        if (type == 1){
            var discount = (parseFloat(max_discount) / parseFloat(price)) * 100;
            document.getElementById("discount_input").max = discount;
        }else{
            var discount = parseFloat(max_discount);
            document.getElementById("discount_input").max = discount;
        }
        var input = document.getElementById("discount_input").setAttribute("max",discount);

        if (type == 1) {
            $('#max_dd_value').html("<?php echo lang('Max Discount allowed is');?>"+' "'+ parseFloat(discount).toFixed(2) + '%".');
        }else{
            $('#max_dd_value').html("<?php echo lang('Max Discount allowed is');?>"+' "'+ formatMoney(parseFloat(discount).toFixed(2)) + '".');
        }
    });

    $("#discount_form").on( "submit", function( event ) {
        event.preventDefault();

        var item = items[item_id];
        

        if ($('#discount_uni').val() == 1) {
            var discount = $('#discount_input').val() ? parseFloat($('#discount_input').val()) : parseFloat(0);
            var dtype = $('#type_dd').val();
            var price = $('#price_dd').val() ? parseFloat($('#price_dd').val()) : parseFloat(0);

            if (dtype == 1){
                discount = ((parseFloat(price))*(parseFloat(discount)/100)).toFixed(2);
            }
        }else{
            var discount = $('#uni_discount_input').val() ? parseFloat($('#uni_discount_input').val()) : parseFloat(0);
            var dtype = $('#uni_type_dd').val();
            var price = $('#price_dd').val() ? parseFloat($('#price_dd').val()) : parseFloat(0);
            if (dtype == 1){
                discount = ((parseFloat(price))*(parseFloat(discount)/100)).toFixed(2);
            }
            items[item_id].discount_code_used = $('#discount_code').val();
        }
       
        items[item_id].discount = discount,
        items[item_id].discount_type = 2,
        items[item_id].price = parseFloat(price),
        localStorage.setItem('positems', JSON.stringify(items));
        $('#prdModal').modal('hide');
        loadItems();
        return;
    });
    <?php endif; ?>
    $("#client_name").select2();
    
    // If there is any item in localStorage
    if (localStorage.getItem('positems')) {
        loadItems();
    }

    function formatPOSDecimal(x, d) {
        if (!d) { d = 2; }
        return accounting.formatMoney(x, '', 2, '', '.', "%s%v");
    }


    $(document).on('change', '.paid_by', function () {
        var p_val = $(this).val(),
            id = $(this).attr('id'),
            pa_no = id.substr(id.length - 1);
        $('#rpaidby').val(p_val);
        
        if (p_val == 'CC' || p_val == 'Cheque') {
            $('#amount_' + pa_no).focus().val(gtotal).trigger('change');
        }else{
            $('#amount_' + pa_no).focus().val(0).trigger('change');
        }

        if (p_val == 'cash' || p_val == 'other' || p_val == 'CC' || p_val == 'ppp') {
            $('.pcheque_' + pa_no).slideUp();
            $('.pcc_' + pa_no).slideUp();
            $('.pcash_' + pa_no).slideDown();
            $('#payment_note_' + pa_no).focus();
        } else if (p_val == 'authorize') {
            $('.pcheque_' + pa_no).slideUp();
            $('.pcash_' + pa_no).slideUp();
            $('.pcc_' + pa_no).slideDown();
            $('#swipe_' + pa_no).focus();
        }else if (p_val == 'Cheque') {
            $('.pcc_' + pa_no).slideUp();
            $('.pcash_' + pa_no).slideUp();
            $('.pcheque_' + pa_no).slideDown();
            $('#cheque_no_' + pa_no).focus();
        } else {
            $('.pcheque_' + pa_no).slideUp();
            $('.pcc_' + pa_no).slideUp();
            $('.pcash_' + pa_no).slideUp();
        }
    });

    $('#paymentModal').on('shown.bs.modal', function(e){
        $('#amount_1').focus();
    });
    var pi = 'amount_1', pa = 2;
    $(document).on('click', '.addButton', function () {
        if (pa <= 5) {
            $('#paid_by_1, #pcc_type_1').select2('destroy');
            var phtml = $('#payments').html(),
                update_html = phtml.replace(/_1/g, '_' + pa);
            pi = 'amount_' + pa;
            $('#multi-payment').append('<button type="button" class="close close-payment" style="margin: -10px 0px 0 0;"><i class="fas fa-2x">&times;</i></button>' + update_html);
            $('#paid_by_1, #pcc_type_1, #paid_by_' + pa + ', #pcc_type_' + pa).select2({minimumResultsForSearch: 7});
            pa++;
        } else {
            bootbox.alert("<?php echo lang('Max allowed limit reached');?>");
            return false;
        }
        $('#paymentModal').css('overflow-y', 'scroll');
    });

    $(document).on('click', '.close-payment', function () {
        $(this).next().remove();
        $(this).remove();
        pa--;
    });

    // clear localStorage and reload
    $('#reset').on( "click", function (e) {
        bootbox.confirm("<?php echo lang('r_u_sure');?>", function (result) {
            if (result) {
                if (localStorage.getItem('positems')) {
                    localStorage.removeItem('positems');
                }
                //location.reload();
                window.location.href = site.base_url+"panel/pos";
            }
        });
    });

    $("#vprice_form").on( "submit", function( event ) {
        event.preventDefault();
        var row = $('#' + $('#pprow_id').val());
        var item_id = $('#pprow_id').val();
        var type = $('#prptype').val();
        var item = items[item_id];
        price = $('#pprice').val();

        items[item_id].price = price,
        items[item_id].row.variable_price = 0;
        localStorage.setItem('positems', JSON.stringify(items));
        $('#prpModal').modal('hide');
        loadItems();
        return;
    });


     $("#pserial_number").autocomplete({
        source: function (request, response) {
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('panel/pos/getProductSerials'); ?>',
                dataType: "JSON", // edit: fixed ;)
                data: {
                    type: $('#pserial_number').data('type'),
                    id: $('#pserial_number').data('id'),
                    term: $('#pserial_number').val(),
                },
                success: function(data) { response(data); } // add this line
            });
        },
        minLength: 1,
        delay: 250,
        autoFocus: true,
        focus: function( event, ui ) { event.preventDefault(); },
        select: function( event, ui ) {
            $( this ).val( ui.item );
        },
        
    });

   

</script>

<!-- Modal -->
<div class="modal modal-primary fade" id="myCashModal" tabindex="-1" role="dialog" aria-labelledby="myCashModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        </div><!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal fade" id="myCloseModal" tabindex="-1" role="dialog" aria-labelledby="myCloseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div><!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade safeModal" id="mySafeModal" tabindex="-1" role="dialog" aria-labelledby="mySafeModalLabel" aria-hidden="true">
    <div class="modal-dialog safemodal-b">
        <div class="modal-content">
        </div><!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade safeModal" id="myDrawerModal" tabindex="-1" role="dialog" aria-labelledby="mySafeModalLabel" aria-hidden="true">
    <div class="modal-dialog safemodal-b">
        <div class="modal-content">
        </div><!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>