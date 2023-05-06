            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fas ">&times;</i></span><span class="sr-only"><?php echo lang('Close');?></span></button>
                <h4 class="modal-title" id="payModalLabel"><?php echo lang('Finalize Sale');?></h4>
            </div>
            <br>
            <div class="modal-body" id="payment_content">
                <div class="row">
                    <div class="col-md-9 col-sm-9">
                        <div class="clearfir"></div>
                        <div id="payments">
                            <div class="well well-sm well_1">
                                <div class="payment">
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <div class="form-group">
                                                <label for="amount"><?php echo lang('Amount');?></label>
                                                <input name="amount" type="text" id="amount"
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
                                                    <!-- <option value="authorize"><?php echo lang('Authorize.Net');?></option> -->
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
                                                <textarea name="payment_note" id="payment_note"
                                                          class="pa form-control kb-text payment_note"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="multi-payment"></div>
                        
                        <div style="clear:both; height:15px;"></div>
                    </div>
                    <div class="col-md-3">
                        <div class="font16">
                            <table class="table table-bordered table-condensed table-striped" style="margin-bottom: 0;">
                                <tbody>
                                <tr>
                                    <td style="width: 25%;"><?php echo lang('Quantity');?></td>
                                    <td style="width: 25%;" class="text-right"><span id="item_count">1</span></td>
                                <tr>
                                </tr>
                                    <td style="width: 25%;"><?php echo lang('Total');?></td>
                                    <td style="width: 25%;" class="text-right"><span id="twt"><?= number_format(($repair['grand_total'] - $payments_amount[0]->total_paid), 2, '.', '' ) ?></span></td>
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
            <script>
                $(document).ready(function () {
                    $('#amount').on('keyup',function(){
                        let amount = ($(this).val() == "") ? 0 : parseFloat($(this).val());
                        let total = parseFloat($('#twt').text());
                        let balance = amount - total;
                        $('#total_paying').text(amount.toFixed(2));
                        $('#balance').text(balance.toFixed(2));
                    });
                });
            </script>