<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
        </button>
        <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_payment'); ?></h4>
    </div>
    <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
    echo form_open_multipart("panel/sales/edit_payment/" . $payment->id, $attrib); ?>
    <div class="modal-body">
        <p><?php echo lang('enter_info'); ?></p>
      
        <input type="hidden" value="<?php echo escapeStr($payment->sale_id); ?>" name="sale_id"/>
        <div class="clearfix"></div>
        <div id="payments">
            <div class="well well-sm well_1">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="payment">
                                <div class="form-group">
                                    <?php echo lang("amount", "amount_1"); ?>
                                    <input name="amount-paid"
                                           value="<?php echo $this->repairer->formatDecimal($payment->amount); ?>" type="text"
                                           id="amount_1" class="pa form-control kb-pad amount"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <?php echo lang("paying_by", "paid_by_1"); ?>
                                <select name="paid_by" id="paid_by_1" class="form-control paid_by">
                                    <?php echo $this->repairer->paid_opts($payment->paid_by); ?>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="clearfix"></div>
                    <div class="pcc_1" style="display:none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input name="pcc_no" value="<?php echo escapeStr($payment->cc_no); ?>" type="text" id="pcc_no_1"
                                           class="form-control" placeholder="<?php echo lang('cc_no') ?>"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">

                                    <input name="pcc_holder" value="<?php echo $payment->cc_holder; ?>" type="text"
                                           id="pcc_holder_1" class="form-control"
                                           placeholder="<?php echo lang('cc_holder') ?>"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select name="pcc_type" id="pcc_type_1" class="form-control pcc_type"
                                            placeholder="<?php echo lang('card_type') ?>">
                                        <option
                                            value="Visa"<?php echo $payment->cc_type == 'Visa' ? ' checked="checcked"' : '' ?>><?php echo lang("Visa"); ?></option>
                                        <option
                                            value="MasterCard"<?php echo $payment->cc_type == 'MasterCard' ? ' checked="checcked"' : '' ?>><?php echo lang("MasterCard"); ?></option>
                                        <option
                                            value="Amex"<?php echo $payment->cc_type == 'Amex' ? ' checked="checcked"' : '' ?>><?php echo lang("Amex"); ?></option>
                                        <option
                                            value="Discover"<?php echo $payment->cc_type == 'Discover' ? ' checked="checcked"' : '' ?>><?php echo lang("Discover"); ?></option>
                                    </select>
                                    <!-- <input type="text" id="pcc_type_1" class="form-control" placeholder="<?php echo lang('card_type') ?>" />-->
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input name="pcc_month" value="<?php echo escapeStr($payment->cc_month); ?>" type="text"
                                           id="pcc_month_1" class="form-control"
                                           placeholder="<?php echo lang('month') ?>"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">

                                    <input name="pcc_year" value="<?php echo escapeStr($payment->cc_year); ?>" type="text"
                                           id="pcc_year_1" class="form-control" placeholder="<?php echo lang('year') ?>"/>
                                </div>
                            </div>
                            <!--<div class="col-md-3">
                                <div class="form-group">
                                    <input name="pcc_ccv" type="text" id="pcc_cvv2_1" class="form-control" placeholder="<?php echo lang('cvv2') ?>" />
                                </div>
                            </div>-->
                        </div>
                    </div>
                    <div class="pcheque_1" style="display:none;">
                        <div class="form-group"><?php echo lang("cheque_no", "cheque_no_1"); ?>
                            <input name="cheque_no" value="<?php echo $payment->cheque_no; ?>" type="text" id="cheque_no_1"
                                   class="form-control cheque_no"/>
                        </div>
                    </div>
                    <div class="form-group v_1" style="display: none;">
                            <?php echo lang("voucher_no", "voucher_no_1");?>
                            <input name="voucher_no" type="text" id="voucher_no_1"
                                   class="pa form-control voucher_no"/>
                            <div id="v_details_1"></div>
                        </div>
                </div>
                <div class="clearfix"></div>
            </div>

        </div>
        <div class="form-group">
            <?php echo lang("note", "note"); ?>
            <?php
            $options = array(
                'name' => 'note',
                'rows' => '1',
                'value'=> set_value('note'),
                'class' => 'form-control',
            );
            echo form_textarea($options);
            ?>
        </div>
       
    </div>
    <div class="modal-footer">

         <button class="btn-icon btn btn-primary">
                <i class="fa fa-reply img-circle text-muted"></i> 
                <?php echo lang('edit_payment');?>
            </button>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript" charset="UTF-8">
$(document).ready(function () {
        $('.file').fileinput();

    $(document).on('change', '.paid_by', function () {
        var p_val = $(this).val();
        $('#rpaidby').val(p_val);
        if (p_val == 'cash') {
            $('.pcheque_1').hide();
            $('.pcc_1').hide();
            $('.pcash_1').show();
            $('.v_1').hide();
            $('#amount_1').focus();
        } else if (p_val == 'CC' || p_val == 'stripe') {
            $('.pcheque_1').hide();
            $('.pcash_1').hide();
            $('.pcc_1').show();
            $('.v_1').hide();
            $('#pcc_no_1').focus();
        } else if (p_val == 'Cheque') {
            $('.pcc_1').hide();
            $('.pcash_1').hide();
            $('.pcheque_1').show();
            $('.v_1').hide();
            $('#cheque_no_1').focus();
        } else if (p_val == 'voucher') {
            $('.v_1').show();
            $('.pcc_1').hide();
            $('.pcash_1').hide();
            $('.pcheque_1').hide();
            $('#voucher_no_1').focus();
        } else {
            $('.pcheque_1').hide();
            $('.pcc_1').hide();
            $('.pcash_1').hide();
            $('.v_1').hide();
        }
    });
    var p_val = '<?php echo $payment->paid_by?>';
    localStorage.setItem('paid_by', p_val);
    if (p_val == 'cash' || p_val == 'CC') {
        $('.pcheque_1').hide();
        $('.pcc_1').hide();
        $('.pcash_1').show();
        $('.v_1').hide();
        $('#amount_1').focus();
    } else if (p_val == 'Cheque') {
        $('.pcc_1').hide();
        $('.pcash_1').hide();
        $('.pcheque_1').show();
        $('.v_1').hide();
        $('#cheque_no_1').focus();
    } else if (p_val == 'voucher') {
        $('.v_1').show();
        $('.pcc_1').hide();
        $('.pcash_1').hide();
        $('.pcheque_1').hide();
        $('#voucher_no_1').focus();
    } else {
        $('.pcheque_1').hide();
        $('.pcc_1').hide();
        $('.pcash_1').hide();
        $('.v_1').hide();
    }

    $('#pcc_no_1').on("change", function (e) {
        var pcc_no = $(this).val();
        localStorage.setItem('pcc_no_1', pcc_no);
        var CardType = null;
        var ccn1 = pcc_no.charAt(0);
        if (ccn1 == 4)
            CardType = 'Visa';
        else if (ccn1 == 5)
            CardType = 'MasterCard';
        else if (ccn1 == 3)
            CardType = 'Amex';
        else if (ccn1 == 6)
            CardType = 'Discover';
        else
            CardType = 'Visa';

        $('#pcc_type_1').select2("val", CardType);
    });
    $('#paid_by_1').select2("val", '<?php echo $payment->paid_by?>');
});

$(document).on('change', '.voucher_no', function () {
    var cn = $(this).val() ? $(this).val() : '';
    id = 1;
    if (cn != '') {
        $.ajax({
            type: "get", async: false,
            url: site.base_url + "panel/sales/validate_voucher/" + cn,
            dataType: "json",
            success: function (data) {
                if (data === false) {
                    $('#voucher_no_' + id).parent('.form-group').addClass('has-error');
                    bootbox.alert('<?php echo lang('incorrect_gift_card')?>');
                } else if (data.customer_id !== null && parseInt( data.customer_id) !== <?php echo $inv->customer_id;?>) {
                    $('#voucher_no_' + id).parent('.form-group').addClass('has-error');
                    bootbox.alert('<?php echo lang('gift_card_not_for_customer')?>');
                } else {
                    $('#v_details_' + id).html('<small>Card No: ' + data.card_no + '<br>Value: ' + data.value + '</small>');
                    $('#voucher_no_' + id).parent('.form-group').removeClass('has-error');
                    $('#amount_' + id).val(gtotal >= data.value ? data.value : gtotal).focus();
                    $('#amount_' + id).attr('readonly', true);
                }
            }
        });
    }
});
</script>
