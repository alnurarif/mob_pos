<style type="text/css">
  .modal {
    overflow-y:auto;
  }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        $('#total_cash_submitted').on("change", function(e) {
            if ($(this).val() && !is_numeric($(this).val())) {
                bootbox.alert("Unexpected Value");
                $(this).val('');
            }
        })
    });
    function countCash(class_cur, amount) {
        var total = amount * $(".n" + class_cur).val();
        $(".v" + class_cur).val(total.toFixed(2));
        getTotal();
    }

    function countTotal(class_cur, amount) {
        var round_amount = Math.round($(".v" + class_cur).val() / amount) * amount;

        console.log(total);
        console.log(amount);

        $(".v" + class_cur).val(round_amount.toFixed(2));
        $(".n" + class_cur).val((round_amount.toFixed(2) / amount));
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
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fas fa-2x">&times;</i>
            </button>
            <h5 class="modal-title"
                id="myModalLabel"><font color="black"><?php echo ('<h4>Register Closing Report</h4>') . ' Opened On ' .(date('m-d-Y' ,strtotime($this->session->userdata('register_open_time')))) . ' At ' .(date('H:i:s' ,strtotime($this->session->userdata('register_open_time')))). ' ||| Closing On ' . (date('m-d-Y')). ' At ' .(date('H:i:s')); ?></font></h5>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id'=> 'close_register_form');
        echo form_open_multipart("panel/pos/close_register/" . $user_id, $attrib);
        ?>
        <div class="modal-body" style="padding: 15px;">
            <!-- <div id="alerts"></div> -->
            <table width="100%" class="table">
                <tr>
					<td style="border-bottom: 1px solid #EEE;"><h6>Opening Cash:</h6></td>
                    <td style="text-align:right; border-bottom: 1px solid #EEE;"><h6>
                            <span><?php echo $this->repairer->formatMoney($cash_in_hand ? $cash_in_hand : $this->session->userdata('cash_in_hand')); ?></span>
                        </h6></td>
                </tr>
                <?php if($this->mSettings->accept_cash): ?>
                  <tr>
                      <td style="border-bottom: 1px solid #EEE;"><h6>Cash Sales:</h6></td>
                      <td style="text-align:right; border-bottom: 1px solid #EEE;"><h6>
                              <span><?php echo $this->repairer->formatMoney($cashsales->paid ? $cashsales->paid : '0.00'); ?></span>
                          </h6></td>
                  </tr>
                <?php endif; ?>
                <?php if($this->mSettings->accept_cheque): ?>
                <tr>
                    <td style="border-bottom: 1px solid #EEE;"><h6>Check Sales:</h6></td>
                    <td style="text-align:right;border-bottom: 1px solid #EEE;"><h6>
                            <span><?php echo $this->repairer->formatMoney($chsales->paid ? $chsales->paid : '0.00'); ?></span>
                        </h6></td>
                </tr>
                <?php endif; ?>
                <?php if($this->mSettings->accept_cc): ?>
                <tr>
                    <td style="border-bottom: 1px solid #DDD;"><h6>Credit Card Sales:</h6></td>
                    <td style="text-align:right;border-bottom: 1px solid #DDD;"><h6>
                            <span><?php echo $this->repairer->formatMoney($ccsales->paid ? $ccsales->paid : '0.00'); ?></span>
                        </h6></td>
                </tr>
                <?php endif; ?>
                <?php if($this->mSettings->accept_paypal): ?>
                <tr>
                    <td style="border-bottom: 1px solid #DDD;"><h6>Paypal Sales:</h6></td>
                    <td style="text-align:right;border-bottom: 1px solid #DDD;"><h6>
                            <span><?php echo $this->repairer->formatMoney($pppsales->paid ? $pppsales->paid : '0.00'); ?></span>
                        </h6></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td style="border-bottom: 1px solid #DDD;"><h6>Other Sales:</h6></td>
                    <td style="text-align:right;border-bottom: 1px solid #DDD;"><h6>
                            <span><?php echo $this->repairer->formatMoney($othersales->paid ? $othersales->paid : '0.00'); ?></span>
                        </h6></td>
                </tr>
            
                <tr>
                    <td width="300px;" style="font-weight:bold;"><h6>Total Sales:</h6></td>
                    <td width="200px;" style="font-weight:bold;text-align:right;"><h6>
                            <span><?php echo $this->repairer->formatMoney($totalsales->paid ? $totalsales->paid : '0.00'); ?></span>
                        </h6></td>
                </tr>
                <tr>
                    <td style="border-top: 1px solid #DDD;"><h4>Refunds:</h4></td>
                    <td style="text-align:right;border-top: 1px solid #DDD;"><h4>
                            <span><?php echo $this->repairer->formatMoney($refunds->returned ? $refunds->returned : '0.00') . ' (' . $this->repairer->formatMoney($refunds->total ? $refunds->total : '0.00') . ')'; ?></span>
                        </h4></td>
                </tr>
                <!-- Safe Start -->
                <tr>
                    <td width="300px;" style="font-weight:bold;"><h4>Total Deposit to Safe:</h4></td>
                    <td width="200px;" style="font-weight:bold;text-align:right;"><h4>
                            <span><?php echo $this->repairer->formatMoney($tosafetranfers->total); ?></span>
                        </h4></td>
                </tr>
                <tr>
                    <td width="300px;" style="font-weight:bold;"><h4>Total Transfer to Drawer:</h4></td>
                    <td width="200px;" style="font-weight:bold;text-align:right;"><h4>
                            <span><?php echo $this->repairer->formatMoney($todrawertranfers->total); ?></span>
                        </h4></td>
                </tr>
                <!-- Safe End -->
                <tr>
                    <td width="300px;" style="font-weight:bold;"><h4><strong>Total Cash</strong>:</h4>
                    </td>
                    <td style="text-align:right;"><h4>
                            <span><strong><?php echo $cashsales->paid ? $this->repairer->formatMoney((($cashsales->paid - ($refunds->returned ? $refunds->returned : 0)  + ($this->session->userdata('cash_in_hand'))) + $todrawertranfers->total) - $tosafetranfers->total) : $this->repairer->formatMoney(((($this->session->userdata('cash_in_hand'))) - ($refunds->returned ? $refunds->returned : 0)  + $todrawertranfers->total) - $tosafetranfers->total); ?></strong></span>
                        </h4></td>
                </tr>
            </table>

            
            <hr>
            
            <div class="row">
                <div class="row">
                    <?php $currency_sets = $this->repairer->returnOpenRegisterSets(); ?>
                    <?php foreach($currency_sets as $input => $name): ?>

                       <div class="col-md-6">
                         <div class="form-group">
                           <div class="row">
                                <div class="col-lg-2">
                                <span><?php echo $this->mSettings->currency; ?><?php echo $name;?></span>
                                </div>
                                <div class="col-lg-3">
                                <input type="number" min="0" class="form-control n<?php echo $input;?>" name="n<?php echo $name;?>"  onchange="countCash(<?php echo $input;?>,<?php echo $name;?>)" value="0">
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
                   <br>
                    
                        <br>
                        <div class="row">
                            <div class="comment_cash" style="display: none;">
                    <div class="col-md-12 form-group no-print">
                        <label for="count_note"><font size="3" color="red">Your count is incorrect. You must explain the difference below you can close the register.</font></label>
                        <div class="controls"> <?php echo form_textarea('count_note', (isset($_POST['count_note']) ? $_POST['count_note'] : ""), 'class="form-control" id="count_note" placeholder="Explain count differential here." style="margin-top: 10px; height: 100px;"'); ?> </div>
                    </div>
                </div>
                        </div>
                        <input type="hidden" name="pin_close" id="pin_close_form_input">
                        <hr>
                        <div class="row no-print">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="total_cash_submitted">Total Value Cash</label>

                         <?php $total_cash = $cashsales->paid ? ((($cashsales->paid - ($refunds->returned ? $refunds->returned : 0) + ($this->session->userdata('cash_in_hand')) - ($refunds->returned ? $refunds->returned : 0) ) + $todrawertranfers->total) - $tosafetranfers->total) : (((($this->session->userdata('cash_in_hand'))) + $todrawertranfers->total) - $tosafetranfers->total); 

                         ?>

                        <?php echo form_hidden('total_cash', $total_cash); ?>
                        <?php echo form_input('total_cash_submitted', (isset($_POST['total_cash_submitted']) ? $_POST['total_cash_submitted'] : $total_cash), 'class="form-control input-tip" id="total_cash_submitted" required="required"  readonly tabindex=1'); ?>
                    </div>
                    </div>
                    <div class="col-sm-4">
                    <div class="form-group">
                        <label for="total_cheques_submitted">Total Value Checks</label>
                        <?php echo form_hidden('total_cheques', $chsales->total_cheques); ?>
                        <?php echo form_input('total_cheques_submitted', (isset($_POST['total_cheques_submitted']) ? $_POST['total_cheques_submitted'] : $chsales->total_cheques), 'class="form-control input-tip" id="total_cheques_submitted" required="required" tabindex=3'); ?>
                    </div>
				</div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="total_cc_slips_submitted">Total Value Credit Cards</label>
                        <?php echo form_hidden('total_cc_slips', $ccsales->total_cc_slips); ?>
                        <?php echo form_input('total_cc_slips_submitted', (isset($_POST['total_cc_slips_submitted']) ? $_POST['total_cc_slips_submitted'] : $ccsales->total_cc_slips), 'class="form-control input-tip" id="total_cc_slips_submitted" required="required" tabindex=2'); ?>
                    </div>
                </div>
                
            </div>

        </div>
        
        <div class="modal-footer no-print">
            <?php echo form_submit('close_register', lang("Close Register"), 'id="close_register_button" class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

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
    var cc      = $('input[name=total_cc_slips]').val();
    var cheques = $('input[name=total_cheques]').val();

    var cash_submitted    = $('input[name=total_cash_submitted]').val();
    var cc_submitted      = $('input[name=total_cc_slips_submitted]').val();
    var cheques_submitted = $('input[name=total_cheques_submitted]').val();
    
    var msg = "";
    var items = new Array();
    
    if (!(parseFloat(cash_submitted) == parseFloat(cash))) {
      items.push('The total cash amount should be '+formatMoney(cash) + ' and it is '+formatMoney(cash_submitted)+'. Please verify that you have counted correctly.');
    }
    if (!(parseFloat(cc_submitted) == parseFloat(cc))) {
      items.push('The total Credit Card Slips should be '+ parseInt(cc) + ' and it is '+parseInt(cc_submitted)+'. Please verify that you have counted correctly.');
    }
    if (!(parseFloat(cheques_submitted) == parseFloat(cheques))) {
      items.push('The total Cheque Slips should be '+ parseInt(cheques) + ' and it is '+parseInt(cheques_submitted)+'. Please verify that you have counted correctly.');
    }
    
    if ((parseInt(items.length) > 0) && (count >= 2)) {
      $('.comment_cash').slideDown();
      $('#count_note').prop('required', true);
    }
    console.log($('#count_note').val());
    if (parseInt(items.length) > 0) {
        if (document.getElementById("count_note").value !== '') {
          var dialog = bootbox.dialog({
              title: 'Enter Employee Pin Code to Verify Register Count',
              message: '<span id="pin_close_error_span"></span><p><input class="form-control" type="text" name="pin_close" id="pin_close"><input type="submit" value="Submit!" id="pin_close_submit" class="form-control btn btn-primary"></p>',
              className: "PinCloseDrawer",
          });
          return;
        }else{
          bootbox.alert(items.join('<br>'));
        }
        return;
    }else{
        var dialog = bootbox.dialog({
            title: 'Please Enter you Pin Code',
            message: '<span id="pin_close_error_span"></span><p><input class="form-control" type="text" name="pin_close" id="pin_close"><br><input type="submit" value="Submit!" id="pin_close_submit" class="form-control btn btn-primary"></p>',
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
                $('#pin_close_error_span').html('Incorrect Pin Code');
            }else{
              $('.PinCloseDrawer').modal('hide');
              $('#close_register_form').submit();
            }
        }
    });
  });
</script>


