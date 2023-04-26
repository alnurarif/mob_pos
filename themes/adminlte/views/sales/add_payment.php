<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_payment'); ?></h4>
            
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open_multipart("panel/sales/add_payment/" . $inv->id, $attrib); ?>
        <div class="modal-body">
            <p><?php echo lang('enter_info'); ?></p>
           
            <input type="hidden" value="<?php echo escapeStr($inv->id); ?>" name="sale_id"/>
            <div class="clearfix"></div>
            <div id="payments">
                <div class="well well-sm well_1">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="payment">
                                    <div class="form-group">
                                        <?php echo lang("amount", "amount_1"); ?>
                                        <input name="amount-paid" type="text" id="amount_1" value="<?php echo $this->repairer->formatDecimal($inv->grand_total - $inv->paid); ?>" class="pa form-control kb-pad amount" required="required"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <?php echo lang("paying_by", "paid_by_1"); ?>
                                    <select name="paid_by" id="paid_by_1" class="form-control paid_by" required="required">
                                        <?php echo $this->repairer->paid_opts(); ?>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="clearfix"></div>
                        <div class="pcc_1" style="display:none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input name="pcc_no" type="text" id="pcc_no_1" class="form-control" placeholder="<?php echo lang('cc_no') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input name="pcc_holder" type="text" id="pcc_holder_1" class="form-control" placeholder="<?php echo lang('cc_holder') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select name="pcc_type" id="pcc_type_1" class="form-control pcc_type" placeholder="<?php echo lang('card_type') ?>">
                                            <option value="Visa"><?php echo lang("Visa"); ?></option>
                                            <option value="MasterCard"><?php echo lang("MasterCard"); ?></option>
                                            <option value="Amex"><?php echo lang("Amex"); ?></option>
                                            <option value="Discover"><?php echo lang("Discover"); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input name="pcc_month" type="text" id="pcc_month_1" class="form-control" placeholder="<?php echo lang('month') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input name="pcc_year" type="text" id="pcc_year_1" class="form-control" placeholder="<?php echo lang('year') ?>"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pcheque_1" style="display:none;">
                            <div class="form-group"><?php echo lang("cheque_no", "cheque_no_1"); ?>
                                <input name="cheque_no" type="text" id="cheque_no_1" class="form-control cheque_no"/>
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
                <?php echo lang('add_payment');?>
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
        });
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
    });
    $(document).on('change', '.voucher_no', function () {
        var cn = $(this).val() ? $(this).val() : '';
        var payid = $(this).attr('id'),
            id = payid.substr(payid.length - 1);
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
