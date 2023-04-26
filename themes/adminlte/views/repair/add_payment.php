<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_payment'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open_multipart("panel/repair/add_payment/" . $inv->id, $attrib); ?>
        <div class="modal-body">
            <p><?php echo lang('enter_info'); ?></p>

            <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php echo lang("date", "date"); ?>
                            <?php echo form_input('date', set_value('date'), 'class="form-control datetime" id="date" required="required"'); ?>
                        </div>
                    </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <?php echo lang("reference_no", "reference_no"); ?>
                        <?php echo form_input('reference_no', set_value('reference_no', $payment_ref), 'class="form-control tip" id="reference_no" required="required"'); ?>
                    </div>
                </div>

                <input type="hidden" value="<?php echo $inv->id; ?>" name="sale_id"/>
            </div>
            <div class="clearfix"></div>
            <div id="payments">

                <div class="well well-sm well_1">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="payment">
                                    <div class="form-group">
                                        <?php echo lang("amount", "amount_1"); ?>
                                        <input name="amount-paid" type="text" id="amount_1"
                                               value="<?php echo $inv->grand_total - $inv->paid ?>"
                                               class="pa form-control kb-pad amount" required="required"/>
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
                            <div class="form-group">
                                <input type="text" id="swipe_1" class="form-control swipe"
                                       placeholder="<?php echo lang('swipe') ?>"/>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input name="pcc_no" type="text" id="pcc_no_1" class="form-control"
                                               placeholder="<?php echo lang('cc_no') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <input name="pcc_holder" type="text" id="pcc_holder_1" class="form-control"
                                               placeholder="<?php echo lang('cc_holder') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select name="pcc_type" id="pcc_type_1" class="form-control pcc_type"
                                                placeholder="<?php echo lang('card_type') ?>">
                                            <option value="Visa"><?php echo lang("Visa"); ?></option>
                                            <option value="MasterCard"><?php echo lang("MasterCard"); ?></option>
                                            <option value="Amex"><?php echo lang("Amex"); ?></option>
                                            <option value="Discover"><?php echo lang("Discover"); ?></option>
                                        </select>
                                        <!-- <input type="text" id="pcc_type_1" class="form-control" placeholder="<?php echo lang('card_type') ?>" />-->
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input name="pcc_month" type="text" id="pcc_month_1" class="form-control"
                                               placeholder="<?php echo lang('month') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">

                                        <input name="pcc_year" type="text" id="pcc_year_1" class="form-control"
                                               placeholder="<?php echo lang('year') ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-3" id='ppp-stripe'>
                                    <div class="form-group">
                                        <input name="pcc_ccv" type="text" id="pcc_cvv2_1" class="form-control"
                                               placeholder="<?php echo lang('cvv2') ?>"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pcheque_1" style="display:none;">
                            <div class="form-group"><?php echo lang("cheque_no", "cheque_no_1"); ?>
                                <input name="cheque_no" type="text" id="cheque_no_1" class="form-control cheque_no"/>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>

            </div>

            <div class="form-group">
                <?php echo lang("note", "note"); ?>
                <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ""), 'class="form-control" id="note"'); ?>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_payment', lang('add_payment'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript" charset="UTF-8">
    $(document).ready(function () {
        $("#date").datetimepicker({
        }).datetimepicker('update', new Date());
        $(document).on('change', '.paid_by', function () {
            var p_val = $(this).val();
            localStorage.setItem('paid_by', p_val);
            $('#rpaidby').val(p_val);
            if (p_val == 'cash') {
                $('.pcheque_1').hide();
                $('.pcc_1').hide();
                $('.pcash_1').show();
                $('#amount_1').focus();
            } else if (p_val == 'CC' || p_val == 'stripe' || p_val == 'ppp' || p_val == 'authorize') {
                if (p_val == 'CC') {
                    $('#ppp-stripe').hide();
                } else {
                    $('#ppp-stripe').show();
                }
                $('.pcheque_1').hide();
                $('.pcash_1').hide();
                $('.pcc_1').show();
                $('#swipe_1').focus();
            } else if (p_val == 'Cheque') {
                $('.pcc_1').hide();
                $('.pcash_1').hide();
                $('.pcheque_1').show();
                $('#cheque_no_1').focus();
            } else {
                $('.pcheque_1').hide();
                $('.pcc_1').hide();
                $('.pcash_1').hide();
            }
            if (p_val == 'gift_card') {
                $('.gc').show();
                $('#gift_card_no').focus();
            } else {
                $('.gc').hide();
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

        $('.swipe').keypress(function (e) {
            var self = $(this);
            var id = 1;
            var TrackData = $(this).val();
            if (e.keyCode == 13) {
                e.preventDefault();

                var p = new SwipeParserObj(TrackData);

                if (p.hasTrack1) {
                    var CardType = null;
                    var ccn1 = p.account.charAt(0);
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

                    $('#pcc_no_' + id).val(p.account);
                    $('#pcc_holder_' + id).val(p.account_name);
                    $('#pcc_month_' + id).val(p.exp_month);
                    $('#pcc_year_' + id).val(p.exp_year);
                    $('#pcc_cvv2_' + id).val('');
                    $('#pcc_type_' + id).val(CardType);
                    self.val('');
                }
                else {
                    $('#pcc_no_' + id).val('');
                    $('#pcc_holder_' + id).val('');
                    $('#pcc_month_' + id).val('');
                    $('#pcc_year_' + id).val('');
                    $('#pcc_cvv2_' + id).val('');
                    $('#pcc_type_' + id).val('');
                }

                $('#pcc_cvv2_' + id).focus();
            }

        }).blur(function (e) {
            $(this).val('');
        }).focus(function (e) {
            $(this).val('');
        });
        
    });
</script>
