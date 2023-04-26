<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style type="text/css">

  /* Hide HTML5 Up and Down arrows. */

  input[type="number"]::-webkit-outer-spin-button, input[type="number"]::-webkit-inner-spin-button {

      -webkit-appearance: none;

      margin: 0;

  }

   

  input[type="number"] {

      -moz-appearance: textfield;

  }

</style>

<script type="text/javascript">

jQuery(document).ready( function($) {

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

</script>

<!-- Main content -->
<div class="box box-primary ">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo lang('open_register');?></h3>
    </div>
    <div class="box-body">
      <?php echo validation_errors('<div class="alert alert-info">', '</div>'); ?>
        <?php 
        $attribs = array('id'=>'open_register_form');
        echo form_open("panel/pos/open_register", $attribs); ?>
        <div class="form-group">
            <label><?php echo lang('cash_in_hand'); ?></label>
            <input type="number" name="cash_in_hand" value="" id="cash_in_hand" class="form-control" readonly>
        </div>
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
                        <input type="number" min="0" class="form-control <?php echo $input;?>" name="n<?php echo $name;?>"  onchange="countCash('<?php echo $input;?>',<?php echo $name;?>)" value="0">
                        </div>
                        <div class="col-lg-7">
                          <div class="input-group">
                               <span class="input-group-addon"><?php echo $this->mSettings->currency; ?></span>

                               <input type="text" class="form-control cash v<?php echo $input;?>" name="v<?php echo $name;?>"  onchange="countTotal('<?php echo $input;?>',<?php echo $name;?>)" value="0" readonly>
                          </div>
                        </div>
                   </div>
                 </div>
              </div>
            <?php endforeach; ?>

        <?php echo form_submit('open_register', (lang('open_register')), 'style="width:100%;" class="btn btn-primary btn-lg" id="open_register"'); ?>
      </div>
    <?php echo form_close(); ?>
    </div>
</div>


<script type="text/javascript">

    $(document).ready(function() {

        $('#cash_in_hand').on("change", function(e) {

            if ($(this).val() && !is_numeric($(this).val())) {

                bootbox.alert(lang.unexpected_value);

                $(this).val('');

            }

        })

    });
  

    function countCash(class_cur, amount) {
        var total = amount * $("."+class_cur).val();
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

        $("#cash_in_hand").val(total.toFixed(2));

        return total;

    }

    $('#open_register_form').on( "submit", function(e) {
          e.preventDefault();
          var cash = $("#cash_in_hand").val();
          if(parseFloat($("#cash_in_hand").val()) < <?php echo $settings->drawer_amount; ?> || parseFloat($("#cash_in_hand").val()) > <?php echo $settings->drawer_amount; ?>){

            message = "<?php echo lang('open_register_warning');?>";
            message = message.replace(/%s/g, "<?php echo $this->repairer->formatMoney($settings->drawer_amount);?>");
            message = message.replace(/%cash/g, formatMoney(cash));
            bootbox.confirm({
                message: message,
                buttons: {
                    confirm: {
                        label: "<?php echo lang('Open Register');?>",
                        className: 'btn-success'
                    },
                    cancel: {
                        label: "<?php echo lang('Recount Drawer');?>",
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                  if (result) {
                    $('#open_register_form')[0].submit();
                  } 
                }
            });
          }else if ($("#cash_in_hand").val() == '') {
            bootbox.alert(lang.enter_value);
          } else {
            $('#open_register_form')[0].submit();
          }
      })

</script>