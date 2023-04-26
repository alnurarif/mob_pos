
<script type="text/javascript">
    $(document).ready(function() {
        $('#total_cash_submitted').on("change", function(e) {
            if ($(this).val() && !is_numeric($(this).val())) {
                bootbox.alert("<?php echo lang('unexpected_value');?>");
                $(this).val('');
            }
        })
    });
    function countCash(class_cur, amount) {
        var total = amount * $("."+class_cur).val();
        console.log()
        $(".v" + class_cur).val(total.toFixed(2));
        getTotal();
    }

    function countTotal(class_cur, amount) {
        var round_amount = Math.round($(".v" + class_cur).val() / amount) * amount;
        $(".v" + class_cur).val(round_amount.toFixed(2));
        $("."+class_cur).val((round_amount.toFixed(2) / amount));
        getTotal();
    }

    function getTotal() {
        var total = 0;
        $('.cash').each(function(){
            total += parseFloat($(this).val());
        });
        $("#total_cash_submitted").val(total.toFixed(2));
        return total;
    }
</script>
<!--Panel-->


      
<div class="box box-primary ">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo lang('close_register');?></h3>
    </div>
    <div class="box-body" id="formz-wizard">
         <div class="row">
                    <?php $attribs = array('id'=>'basic-wizard', 'data-parsley-validate'=>'true', 'class'=>'margin-bottom-10px'); 
                echo form_open_multipart("panel/pos/close_register/" . $user_id, $attribs);?>
                        <div class="first block1 show">
                            <center><font size="5" color="grey"><?=lang('Cash Count Sheet');?></font></center>
                            <br>
                            <div class="">
                                <?php $currency_sets = $this->repairer->returnOpenRegisterSets(); ?>
                                <?php foreach($currency_sets as $input => $name): ?>

                                   <div class="col-md-6">
                                     <div class="form-group">
                                       <div class="row">
                                            <div class="col-lg-2">
                                            <span><?php echo $this->mSettings->currency; ?><?php echo $name;?></span>
                                            </div>
                                            <div class="col-lg-3">
                                            <input type="number" min="0" class="form-control <?php echo $input;?>" name="n<?php echo $name;?>"  onchange="countCash('<?php echo $input;?>',<?php echo $name;?>)" value="0">
                                            </div>
                                            <div class="col-lg-7">
                                              <div class="input-group">
                                                   <span class="input-group-addon"><?php echo $this->mSettings->currency; ?></span>

                                                   <input type="text" class="form-control cash v<?php echo $input;?>" name="v<?php echo $name;?>"  onchange="countTotal(<?php echo $input;?>,<?php echo $name;?>)" value="0" readonly>
                                              </div>
                                            </div>
                                       </div>
                                     </div>
                                  </div>
                                <?php endforeach; ?>
                              </div>
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label for="total_cash_submitted"><?=lang('total_value_cash');?></label>
                                    <?php $total_cash =   $cashsales->paid ? ((($cashsales->paid - ($refunds->returned ? $refunds->returned : 0) + ($this->session->userdata('cash_in_hand'))) + $todrawertranfers->total) - $tosafetranfers->total) : (((($this->session->userdata('cash_in_hand') - ($refunds->returned ? $refunds->returned : 0))) + $todrawertranfers->total) - $tosafetranfers->total); ?>

                                    <?php echo form_input('total_cash_submitted', (isset($_POST['total_cash_submitted']) ? $_POST['total_cash_submitted'] : $total_cash), 'class="form-control input-tip" id="total_cash_submitted" required="required"  readonly tabindex=1'); ?>
                                </div>
                                <span class="next btn btn-primary pull-right" data-current-block="1" data-next-block="2"><?php echo lang('Next');?> <i class="fas fa-arrow-right"></i></span>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        <div class="second block2 hidden">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php if($settings->accept_cheque): ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="total_cheques_submitted"><?php echo lang('Total Value Checks');?></label>
                                                <?php echo form_input('total_cheques_submitted', (isset($_POST['total_cheques_submitted']) ? $_POST['total_cheques_submitted'] : ($chsales->paid?$chsales->paid:0)), 'class="form-control input-tip" id="total_cheques_submitted" required="required" tabindex=1 data-parsley-group="block2"'); ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <input type="hidden" name="total_cheques_submitted" value="<?=($chsales->paid?$chsales->paid:0);?>">
                                    <?php endif; ?>


                                    <?php if($settings->accept_cc): ?>
                                        <div class="col-md-4">
                                          <div class="form-group">
                                                  <label for="total_cc_submitted"><?php echo lang('Total Value Credit Cards');?></label>
                                                  <?php echo form_input('total_cc_submitted', (isset($_POST['total_cc_submitted']) ? $_POST['total_cc_submitted'] : ($ccsales->paid?$ccsales->paid:0)), 'class="form-control input-tip" id="total_cc_submitted" required="required" tabindex=2 data-parsley-group="block2"'); ?>
                                              </div>
                                        </div>
                                    <?php else: ?>
                                        <input type="hidden" name="total_cc_submitted" value="<?=($ccsales->paid?$ccsales->paid:0);?>">
                                    <?php endif; ?>

                                    <?php if($settings->accept_paypal): ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="total_ppp_submitted"><?php echo lang('Total Value Paypal');?></label>
                                                <?php echo form_input('total_ppp_submitted', (isset($_POST['total_ppp_submitted']) ? $_POST['total_ppp_submitted'] : ($pppsales->paid?$pppsales->paid:0)), 'class="form-control input-tip" id="total_ppp_submitted" required="required" tabindex=3 data-parsley-group="block2"'); ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <input type="hidden" name="total_ppp_submitted" value="<?=($ccsales->paid?$ccsales->paid:0);?>">
                                    <?php endif; ?>

                                   
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="total_others_submitted"><?php echo lang('Total Value Other');?></label>
                                            <?php echo form_input('total_others_submitted', (isset($_POST['total_others_submitted']) ? $_POST['total_others_submitted'] : ($othersales->paid?$othersales->paid:0)), 'class="form-control input-tip" id="total_others_submitted" required="required" tabindex=4 data-parsley-group="block2"'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <span class="next btn btn-primary pull-left" data-current-block="2" data-next-block="1"><i class="fas fa-arrow-left"></i> <?php echo lang('Previous');?></span>
                                <span class="next btn btn-primary pull-right" data-current-block="2" data-next-block="3"><?php echo lang('Next');?> <i class="fas fa-arrow-right"></i></span>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        <div class="third block3 hidden col-md-12">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><?php echo lang('Payment Type');?></th>
                                        <th><?php echo lang('Your Total');?></th>
                                        <th><?php echo lang('System Total');?></th>
                                        <th><?php echo lang('Difference');?></th>
                                        <th><?php echo lang('Qty');?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($settings->accept_cash): ?>
                                    <tr>
                                        <td><?php echo lang('Cash + Cash in Hand');?></th>
                                        <td><span id="tcash"></span></th>
                                        <td><?php echo $total_cash; ?></th>
                                        <td><span id="tcash_diff"></span></th>
                                        <td><?php echo $cashsales->total_cash_qty; ?></th>
                                    </tr>
                                    <?php endif;?>

                                    <?php if($settings->accept_cheque): ?>
                                    <tr>
                                            <td><?php echo lang('Cheques');?></th>
                                            <td><span id="tcheques"></span></th>
                                            <td><?php echo $chsales->paid?$chsales->paid:number_format(0,2); ?></th>
                                            <td><span id="tcheques_diff"></span></th>
                                            <td><?php echo $chsales->total_cheques; ?></th>
                                        </tr>
                                    <?php endif;?>

                                    <?php if($settings->accept_cc): ?>
                                    <tr>
                                        <td><?php echo lang('Credit Cards');?></th>
                                        <td><span id="tcc"></span></th>
                                        <td><?php echo $ccsales->paid?$ccsales->paid:number_format(0,2); ?></th>
                                        <td><span id="tcc_diff"></span></th>
                                        <td><?php echo $ccsales->total_cc_slips; ?></th>
                                    </tr>
                                    <?php endif;?>

                                    <tr>
                                        <td><?php echo lang('Others');?></th>
                                        <td><span id="tothers"></span></th>
                                        <td><?php echo $othersales->paid?$othersales->paid:number_format(0,2); ?></th>
                                        <td><span id="tothers_diff"></span></th>
                                        <td><?php echo $othersales->total_others; ?></th>
                                    </tr>
                                    
                                    <?php if($settings->accept_paypal): ?>
                                    <tr>
                                        <td><?php echo lang('PayPal');?></th>
                                        <td><span id="tppp"></span></th>
                                        <td><?php echo $pppsales->paid ?$pppsales->paid:number_format(0,2); ?></th>
                                        <td><span id="tppp_diff"></span></th>
                                        <td><?php echo $pppsales->total_cheques; ?></th>
                                    </tr>
                                    <?php endif;?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th><?php echo lang('Totals');?></th>
                                        <th><span id="total_all"></span></th>
                                        <th><span id="system_total_all"><?php echo ($total_cash) + ($chsales->paid?$chsales->paid:number_format(0,2)) + ($ccsales->paid?$ccsales->paid:number_format(0,2)) + ($othersales->paid?$othersales->paid:number_format(0,2)) + ($pppsales->paid ?$pppsales->paid:number_format(0,2)); ?></span></th>
                                        <th><span id="total_all_diff"></span></th>
                                    </tr>
                                </tfoot>
                            </table>

                            <!-- Form Data - Hidden - Start -->
                            <?php echo form_hidden('total_cash', $total_cash); ?>
                            <?php echo form_hidden('total_cheques', $chsales->paid?$chsales->paid:number_format(0,2)); ?>
                            <?php echo form_hidden('total_cc', $ccsales->paid?$ccsales->paid:number_format(0,2)); ?>
                            <?php echo form_hidden('total_others', $othersales->paid?$othersales->paid:number_format(0,2) ); ?>
                            <?php echo form_hidden('total_ppp', $pppsales->paid ?$pppsales->paid:number_format(0,2)); ?>

                            <?php echo form_hidden('total_cash_qty', $cashsales->total_cash_qty?$cashsales->total_cash_qty:number_format(0,2)); ?>
                            <?php echo form_hidden('total_cheques_qty', $chsales->total_cheques?$chsales->total_cheques:number_format(0,2)); ?>
                            <?php echo form_hidden('total_cc_qty', $ccsales->total_cc_slips?$ccsales->total_cc_slips:number_format(0,2)); ?>
                            <?php echo form_hidden('total_others_qty', $othersales->total_others?$othersales->total_others:number_format(0,2) ); ?>
                            <?php echo form_hidden('total_ppp_qty', $pppsales->total_cheques ?$pppsales->total_cheques:number_format(0,2)); ?>

                            <!-- Safe Start -->
                            <?php echo form_hidden('tosafetranfers', $tosafetranfers->total ?$tosafetranfers->total:number_format(0,2)); ?>
                            <?php echo form_hidden('todrawertranfers', $todrawertranfers->total ?$todrawertranfers->total:number_format(0,2)); ?>
                            <!-- Safe End -->
                            <!-- Form Data - Hidden - End -->
                            
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="comment_cash" style="display: none;">
                                      <div class="col-md-12 form-group no-print">
                                          <label for="count_note"><font size="3" color="red"><?php echo lang('count_note_label');?></font></label>
                                          <div class="controls"> <?php echo form_textarea('count_note', (isset($_POST['count_note']) ? $_POST['count_note'] : ""), 'class="form-control" id="count_note" placeholder="Explain count differential here." style="margin-top: 10px; height: 100px;"'); ?> </div>
                                      </div>
                                  </div>
                                </div>
                                <input type="hidden" name="pin_close" id="pin_close_form_input">
                            </div>
                            <span class="next btn btn-primary pull-left" data-current-block="3" data-next-block="2"><i class="fas fa-arrow-left"></i> <?php echo lang('Previous');?></span>
                            <?php echo form_submit('close_register', lang("close_register"), 'id="close_register_button" class="btn btn-default pull-right"'); ?>
                            <div class="clearfix"></div>
                        </div>
                    </form>
                </div>
            </div>
    </div>
</div>

<!--/Panel-->
<script type="text/javascript">
  $(document).on('change', '#pin_close', function (e) {
      $('#pin_close_form_input').val($(this).val());
  });
  var count = 0;
  var error = false;

  $(document).on('click', '#close_register_button', function (event) {
    event.preventDefault();
    count += 1;
    var cash    = $('input[name=total_cash]').val();
    var cc      = $('input[name=total_cc]').val();
    var cheques = $('input[name=total_cheques]').val();
    var ppp     = $('input[name=total_ppp]').val();
    var others  = $('input[name=total_others]').val();

    var cash_submitted    = $('input[name=total_cash_submitted]').val()?$('input[name=total_cash_submitted]').val():0;
    var cc_submitted      = $('input[name=total_cc_submitted]').val()?$('input[name=total_cc_submitted]').val():0;
    var cheques_submitted = $('input[name=total_cheques_submitted]').val()?$('input[name=total_cheques_submitted]').val():0;
    var ppp_submitted     = $('input[name=total_ppp_submitted]').val()?$('input[name=total_ppp_submitted]').val():0;
    var others_submitted  = $('input[name=total_others_submitted]').val()? $('input[name=total_others_submitted]').val():0;
    
    var msg = "";
    var items = new Array();
    
    var total_all_submitted = (parseFloat($('input[name=total_cash_submitted]').val() ? $('input[name=total_cash_submitted]').val() : 0 ) + parseFloat($('input[name=total_cheques_submitted]').val() ? $('input[name=total_cheques_submitted]').val() : 0 ) + parseFloat($('input[name=total_cc_submitted]').val() ? $('input[name=total_cc_submitted]').val() : 0 ) + parseFloat($('input[name=total_others_submitted]').val() ? $('input[name=total_others_submitted]').val() : 0 ) + parseFloat($('input[name=total_ppp_submitted]').val() ? $('input[name=total_ppp_submitted]').val() : 0 )).toFixed(2);
    var total_all = parseFloat($('#system_total_all').html()).toFixed(2);
    
    if (!(total_all == total_all_submitted)) {


        if (!(parseFloat(cash_submitted) == parseFloat(cash))) {
            message = "<?php echo lang('total_cash_error');?>";
            message = message.replace(/%type/g, "cash");
            message = message.replace(/%cash/g, formatMoney(cash));
            message = message.replace(/%csh_submitted/g, formatMoney(cash_submitted));
            items.push(message);
        }
        if (!(parseFloat(cc_submitted) == parseFloat(cc))) {
            message = "<?php echo lang('total_cash_error');?>";
            message = message.replace(/%type/g, "Credit Card");
            message = message.replace(/%cash/g, parseInt(cc));
            message = message.replace(/%csh_submitted/g, parseInt(cc_submitted));
            items.push(message);
        }
        if (!(parseFloat(cheques_submitted) == parseFloat(cheques))) {
            message = "<?php echo lang('total_cash_error');?>";
            message = message.replace(/%type/g, "Cheque");
            message = message.replace(/%cash/g, parseInt(cheques));
            message = message.replace(/%csh_submitted/g, parseInt(cheques_submitted));
            items.push(message);
        }
        if (!(parseFloat(ppp_submitted) == parseFloat(ppp))) {
            message = "<?php echo lang('total_cash_error');?>";
            message = message.replace(/%type/g, "PayPal");
            message = message.replace(/%cash/g, parseInt(ppp));
            message = message.replace(/%csh_submitted/g, parseInt(ppp_submitted));
            items.push(message);
        }
        if (!(parseFloat(others_submitted) == parseFloat(others))) {
            message = "<?php echo lang('total_cash_error');?>";
            message = message.replace(/%type/g, "Other");
            message = message.replace(/%cash/g, parseInt(others));
            message = message.replace(/%csh_submitted/g, parseInt(others_submitted));
            items.push(message);
        }
    }
    
    if ((parseInt(items.length) > 0) && (count >= 2)) {
        $('.comment_cash').slideDown();
        $('#count_note').prop('required', true);
    }
    console.log($('#count_note').val());
    if (parseInt(items.length) > 0) {
        if (document.getElementById("count_note").value !== '') {
          var dialog = bootbox.dialog({
              title: "<?php echo lang('Enter Employee Pin Code to Verify Register Count');?>",
              message: '<span id="pin_close_error_span"></span><p><input class="form-control" type="text" name="pin_close" id="pin_close"><input type="submit" value="<?=lang('submit');?>" id="pin_close_submit" class="form-control btn btn-primary"></p>',
              className: "PinCloseDrawer",
          });
          return;
        }else{
          bootbox.alert(items.join('<br>'));
        }
        return;
    }else{
        var dialog = bootbox.dialog({
            title: "<?php echo lang('Please Enter you Pin Code');?>",
            message: '<span id="pin_close_error_span"></span><p><input class="form-control" type="text" name="pin_close" id="pin_close"><br><input type="submit" value="<?=lang('submit');?>" id="pin_close_submit" class="form-control btn btn-primary"></p>',
            className: "PinCloseDrawer",
        });
        return;
    }

  });
  $(document).on('click', '#pin_close_submit', function () {
    var pin_code = $('#pin_close').val();
    jQuery.ajax({
        type: "POST",
        url: base_url + "panel/pos/verifyPin",
        data: "pin_code=" + encodeURI(pin_code),
        cache: false,
        dataType: "html",
        success: function (data) {
            if (data == 'false') {
                $('#pin_close_error_span').html("<?php echo lang('incorrect_pin');?>");
            }else{
              $('.PinCloseDrawer').modal('hide');
              $('#basic-wizard').submit();
            }
        }
    });
  });
  $(document).on('click', '.next', function () {
    var nb = $(this).data('next-block');
    var cb = $(this).data('current-block');
    if (parseInt(cb) == 2) {
        $('#tcash').html($('input[name=total_cash_submitted]').val() ? $('input[name=total_cash_submitted]').val() : 0);
        $('#tcheques').html($('input[name=total_cheques_submitted]').val()?$('input[name=total_cheques_submitted]').val():0);
        $('#tcc').html($('input[name=total_cc_submitted]').val()?$('input[name=total_cc_submitted]').val():0);
        $('#tothers').html($('input[name=total_others_submitted]').val()?$('input[name=total_others_submitted]').val():0);
        $('#tppp').html($('input[name=total_ppp_submitted]').val()?$('input[name=total_ppp_submitted]').val():0);

        var total_all_submitted = (parseFloat($('input[name=total_cash_submitted]').val() ? $('input[name=total_cash_submitted]').val() : 0 ) + parseFloat($('input[name=total_cheques_submitted]').val() ? $('input[name=total_cheques_submitted]').val() : 0 ) + parseFloat($('input[name=total_cc_submitted]').val() ? $('input[name=total_cc_submitted]').val() : 0 ) + parseFloat($('input[name=total_others_submitted]').val() ? $('input[name=total_others_submitted]').val() : 0 ) + parseFloat($('input[name=total_ppp_submitted]').val() ? $('input[name=total_ppp_submitted]').val() : 0 )).toFixed(2);
        $('#total_all').html(total_all_submitted);
        var total_all = parseFloat($('#system_total_all').html());
        $('#total_all_diff').html((total_all - total_all_submitted).toFixed(2));
        $('#tcash_diff').html(($('input[name=total_cash_submitted]').val() - <?php echo $total_cash? $total_cash : 0; ?>).toFixed(2));
        $('#tcheques_diff').html(($('input[name=total_cheques_submitted]').val() - <?php echo $chsales->paid? $chsales->paid : 0; ?>).toFixed(2));
        $('#tcc_diff').html(($('input[name=total_cc_submitted]').val() - <?php echo $ccsales->paid? $ccsales->paid : 0; ?>).toFixed(2));
        $('#tothers_diff').html(($('input[name=total_others_submitted]').val() - <?php echo $othersales->paid? $othersales->paid : 0; ?>).toFixed(2));
        $('#tppp_diff').html(($('input[name=total_ppp_submitted]').val() - <?php echo $pppsales->paid? $pppsales->paid : 0; ?>).toFixed(2));
    }
  });
</script>