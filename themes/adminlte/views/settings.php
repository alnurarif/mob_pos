<style type="text/css">



    #uploadimage .submit {

        display: none;

    }

    .radio-sms label > input {

        visibility: hidden;

        position: absolute;

    }



    .radio-sms label > input + img{ /* IMAGE STYLES */

        cursor:pointer;

        opacity: 0.3;

        max-width: 100%;

    }



    .radio-sms  label {

        width: 100%;

    }



    .radio-sms  label > input:checked + img{/* (RADIO CHECKED) IMAGE STYLES */

        opacity: 1;

        margin: auto;

        display: block;

    }

    .twilio-info, .nexmo-info, .smsgateway-info, .http_api-info {
        opacity: 0.2;
    }



    span.label {

        width: 100%;

        display: inline-block;

    }



</style>
<script type="text/javascript">
    $(document).ready(function(){
        $('.nav-tabs a:first').tab('show');
    });
</script>

<?php

$wm = array('0' => lang('no'), '1' => lang('yes'));

$ps = array('0' => lang("enable"), '1' => lang("disable"));

?>

<script type="text/javascript">

    jQuery(document).ready(function () {
        <?php if(!$this->Admin && !$GP['settings-general_settings_edit']): ?>
            $('#general *').prop("disabled", true);
        <?php endif;?>
        <?php if(!$this->Admin && !$GP['settings-default_taxes_edit']): ?>
            $('#def_taxes *').prop("disabled", true);
        <?php endif;?>
        <?php if(!$this->Admin && !$GP['settings-order_repairs_edit']): ?>
            $('#orders *').prop("disabled", true);
        <?php endif;?>
        <?php if(!$this->Admin && !$GP['settings-quote_edit']): ?>
            $('#invoice *').prop("disabled", true);
        <?php endif;?>
        <?php if(!$this->Admin && !$GP['settings-sms_edit']): ?>
            $('#sms *').prop("disabled", true);
        <?php endif;?>
        <?php if(!$this->Admin && !$GP['settings-pos_configuration_edit']): ?>
            $('#pos *').prop("disabled", true);
        <?php endif;?>

    var postJSON;

    postJSON = 'aa'



    toastr.options = {

        "closeButton": true,

        "debug": false,

        "progressBar": true,

        "positionClass": "toast-bottom-right",

        "onclick": null,

        "showDuration": "300",

        "hideDuration": "1000",

        "timeOut": "5000",

        "extendedTimeOut": "1000",

        "showEasing": "swing",

        "hideEasing": "linear",

        "showMethod": "fadeIn",

        "hideMethod": "fadeOut"

    }


   $('#settings_form').on( "submit", function(event) {
        event.preventDefault();
        <?php if($this->Admin || $GP['settings-pos_configuration']): ?>

            var drawer_amount = $('input[name=drawer_amount]').val();
            var max_drawer_amount = $('input[name=max_drawer_amount]').val();

            if (parseFloat(max_drawer_amount) <= parseFloat(drawer_amount)) {
                bootbox.alert("<?php echo lang('max_drawer_amount_error');?>");
                return;
            }
         <?php endif; ?>


        var url = "";

        var dataString = "";



        url = base_url + "panel/settings/save_settings";

        var dataString = $('form#settings_form').serialize() + "&token=<?php echo $_SESSION['token'];?>";




        jQuery.ajax({

            type: "POST",

            url: url,

            data: dataString,

            cache: false,

            success: function (data) {

                toastr['success']("<?php echo lang('Settings');?>", "<?php echo lang('Updated Succesfully');?>");

                $("#black").fadeIn(100);

                // location.reload();

            }

        });

        return false;

    });

   
    jQuery(document).on("click", "#twilio", function () {
        jQuery(".twilio-info").fadeTo( 120 , 1);
        jQuery(".nexmo-info").fadeTo( 120 , 0.3);
        jQuery(".smsgateway-info").fadeTo( 120 , 0.3);
        jQuery(".http_api-info").fadeTo( 120 , 0.3);

    });
    

    jQuery(document).on("click", "#nexmo", function () {
        jQuery(".nexmo-info").fadeTo( 120 , 1);
        jQuery(".twilio-info").fadeTo( 120 , 0.3);
        jQuery(".smsgateway-info").fadeTo( 120 , 0.3);
        jQuery(".http_api-info").fadeTo( 120 , 0.3);

    });

    jQuery(document).on("click", "#smsgateway", function () {
        jQuery(".smsgateway-info").fadeTo( 120 , 1);
        jQuery(".nexmo-info").fadeTo( 120 , 0.3);
        jQuery(".twilio-info").fadeTo( 120 , 0.3);
        jQuery(".http_api-info").fadeTo( 120 , 0.3);
    });

    jQuery(document).on("click", "#http_api", function () {
        jQuery(".http_api-info").fadeTo( 120 , 1);
        jQuery(".nexmo-info").fadeTo( 120 , 0.3);
        jQuery(".twilio-info").fadeTo( 120 , 0.3);
        jQuery(".smsgateway-info").fadeTo( 120 , 0.3);
    });




    $("#t_mode").select2({placeholder: "Twilio Mode"});



    $("#category").select2({tags: true, tokenSeparators: [','],});

    $("#custom_fields").select2({tags: true, tokenSeparators: [',']});

    $("#repair_custom_checkbox").select2({tags: true, tokenSeparators: [',']});

    $("#repair_custom_toggles").select2({tags: true, tokenSeparators: [','],});




    $(".nav-tabs a").on( "click", function() {

        $(this).tab('show');

    });





    if(window.location.hash) {

        $('.nav-tabs a[href="'+window.location.hash+'"]').tab('show') // Select tab by name

    }


    var _URL = window.URL || window.webkitURL;



    $("#logo_upload").on("change", function() {
        $("#error_message").empty(); // To remove the previous error message
        var file = this.files[0];
        img = new Image();
        var imgwidth = 0;
        var imgheight = 0;
        var maxwidth = 600;
        var maxheight = 200;

        img.src = _URL.createObjectURL(file);
        img.onload = function() {
            imgwidth = this.width;
            imgheight = this.height;

            $("#width").text(imgwidth);
            $("#height").text(imgheight);
            if(imgwidth <= maxwidth && imgheight <= maxheight){
                var reader = new FileReader();
                reader.onload = imageIsLoaded;
                reader.readAsDataURL(file);
                $( "#uploadimage .submit" ).trigger( "click" );
            }else{

                message = "<?php echo lang('image_size_not_optimal');?>";
                message = message.replace(/%maxheight/g, maxheight);
                message = message.replace(/%maxwidth/g, maxwidth);
                bootbox.confirm(message, function(result){
                    if (result) {
                        var reader = new FileReader();
                        reader.onload = imageIsLoaded;
                        reader.readAsDataURL(file);
                        $( "#uploadimage .submit" ).trigger( "click" );
                    }else{
                        return;
                    }
                })
            }
        };
        img.onerror = function() {
            bootbox.alert("<?php echo lang('not a valid file');?>: " + file.type);
        }
    });


    $('#uploadimage').on( "submit", function(event){

        event.preventDefault();

        var formData = new FormData($(this)[0]);

        var request = $.ajax({

            type: 'POST',

            url: base_url + 'panel/settings/upload_image',

            data: formData,

            contentType: false,

            processData: false,

            success: function(data){
                if(data != 'false')
                {
                    toastr['success']("<?php echo lang('Uploaded Succesfully');?>", "<?php echo lang('Image');?>");
                    $("nav .navbar-brand img").attr("src","<?php echo site_url('assets/uploads/logos/'); ?>"+data);
                } else {
                    toastr['error']("<?php echo lang('Error');?>", "");
                }
            }

        });

    });



    $(".applyToAll").on( "click", function() {

        var item_type = jQuery(this).data("type");

        if (

            item_type ==  'repair' ||

            item_type ==  'other' ||

            item_type ==  'accessories' ||

            item_type ==  'newphones' ||

            item_type ==  'plans' ||

            item_type ==  'usedphones'

        ){
            message = "<?php echo lang('not_reversible_tax');?>";
            message = message.replace(/%s/g, item_type);
            bootbox.confirm(message,  function(result){

                    if(result){

                        var dataString = "type=" + item_type;

                        $.ajax({

                          method: "POST",

                          url: base_url + 'panel/settings/applyToAll',

                          data: { type: item_type }

                        }).done(function( data ) {

                            if(data == 'true') {

                                toastr['success']("<?php echo lang('Updated Succesfully');?>", "<?php echo lang('Default Taxes');?>");

                            } else if(data == 'false') {

                                toastr['error']("<?php echo lang('Error updating taxes');?>", "<?php echo lang('Default Taxes');?>");

                            }

                        });

                    }

                }

            );

        }



    });



    function imageIsLoaded(e) {

        $('#preview_logo').attr('src', e.target.result);

    };



    $.fn.realVal = function(){

        var $obj = $(this);

        var val = $obj.val();

        var type = $obj.attr('type');

        if (type && type==='checkbox') {

            var un_val = $obj.attr('data-unchecked');

            if (typeof un_val==='undefined') un_val = '';

            return $obj.prop('checked') ? val : un_val;

        } else {

            return val;

        }

    };



    var addRule = function(sheet, selector, styles) {

        if (sheet.insertRule) return sheet.insertRule(selector + " {" + styles + "}", sheet.cssRules.length);

        if (sheet.addRule) return sheet.addRule(selector, styles);

    };

});



</script>



<!-- Default box -->



           <!-- Nav tabs -->
<div class="nav-tabs-custom">
                               
    <ul class="nav nav-tabs" id="settings_tabs" role="tablist">
        <?php if($this->Admin || $GP['settings-general_settings']): ?>
            <li role="presentation"><a class="active" href="#general" aria-controls="home" role="tab" data-toggle="tab"><?php echo lang('general_settings_title'); ?></a></li>
        <?php endif; ?>
        <?php // if($this->Admin || $GP['settings-default_taxes']): ?>
            <!-- <li role="presentation"><a href="#def_taxes" aria-controls="profile" role="tab" data-toggle="tab">Default Taxes</a></li> -->
        <?php //endif; ?>
        <?php if($this->Admin || $GP['settings-order_repairs']): ?>
            <li role="presentation"><a href="#orders" aria-controls="profile" role="tab" data-toggle="tab"><?php echo lang('repair');?></a></li>
        <?php endif; ?>
        <?php if($this->Admin || $GP['settings-quote']): ?>
            <li role="presentation"><a href="#invoice" aria-controls="messages" role="tab" data-toggle="tab"><?php echo lang('invoice_title'); ?></a></li>
        <?php endif; ?>
        <?php if($this->Admin || $GP['settings-sms']): ?>
            <li role="presentation"><a href="#sms" aria-controls="settings" role="tab" data-toggle="tab"><?php echo lang('sms_title'); ?></a></li>
        <?php endif; ?>
        <?php if($this->Admin || $GP['settings-pos_configuration']): ?>
            <li role="presentation"><a href="#pos" aria-controls="settings" role="tab" data-toggle="tab"><?php echo lang('POS Configuration');?></a></li>
        <?php endif; ?>

        <!--  -->
        <li role="presentation"><a href="#notify" aria-controls="notify" role="tab" data-toggle="tab"><?php echo lang('Notifications');?></a></li>

        <!--  -->
        <li role="presentation">
            <a href="#customer_notifications" aria-controls="customer_notifications" role="tab" data-toggle="tab"><?php echo lang('Customer Notifications');?>
            </a>
        </li>
    </ul>



  <!-- Tab panes -->
                    <form id="settings_form">

  <div class="tab-content">

    <?php if($this->Admin || $GP['settings-general_settings']): ?>
        <div role="tabpanel"  class="tab-pane active" id="general">
                    <div class="row">
                        <div class="col-lg-12">

                    <fieldset>
                        <legend><?php echo lang('general_settings_title');?></legend>
                        <div class="col-lg-12">

                            <div class="form-group">

                                <?php echo lang('company_title', 'title');?>

                                <div class="input-group">

                                    <div class="input-group-addon">

                                        <i class="fas  fa-quote-left"></i>

                                    </div>

                                    <input id="title" name="title" type="text" class="validate form-control" value="<?php echo escapeStr($settings->title); ?>">

                                </div>

                            </div>

                        </div>

                        <div class="col-lg-4">

                            <div class="form-group">

                                <?php echo lang('language', 'language');?>

                                <?php $scanned_lang_dir = array_map(function ($path) {

                                    return basename($path);

                                }, glob(APPPATH . 'language/*', GLOB_ONLYDIR));

                                ?>

                                <select id="language" name="language" data-num="1" class="form-control m-bot15" style="width: 100%">

                                    <?php foreach ($scanned_lang_dir as $dir):

                                        $language = basename($dir); ?>

                                        <option value="<?php echo $language; ?>" <?php echo ($language == $settings->language) ? 'selected' : '' ?>><?php echo escapeStr($language); ?></option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo lang('google_api_key', 'google_api_key');?>
                                <input name="google_api_key" id="google_api_key" type="text" class="validate form-control" value="<?php echo $settings->google_api_key; ?>">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="dateformat"><?php echo lang('dateformat'); ?></label>

                                <div class="controls">
                                    <?php
                                    foreach ($date_formats as $date_format) {
                                        $dt[$date_format->id] = $date_format->js;
                                    }
                                    echo form_dropdown('dateformat', $dt, $settings->dateformat, 'id="dateformat" class="form-control tip" style="width:100%;" required="required"');
                                    ?>
                                </div>
                            </div>
                            </div>

                        <div class="col-md-4">
                            <div class="form-group">
                               <label class="control-label" for="use_defects_input_dropdown"><?php echo lang('use_defects_input_dropdown'); ?></label>
                               <div class="controls">
                                  <?php
                                     echo form_dropdown('use_defects_input_dropdown', $wm, $settings->use_defects_input_dropdown, 'id="use_defects_input_dropdown" class="form-control tip" style="width:100%;" required="required"');
                                     ?>
                               </div>
                            </div>
                         </div>
                         <div class="col-md-4">
                            <div class="form-group">
                               <label class="control-label" for="use_models_input_dropdown"><?php echo lang('use_models_input_dropdown'); ?></label>
                               <div class="controls">
                                  <?php
                                     echo form_dropdown('use_models_input_dropdown', $wm, $settings->use_models_input_dropdown, 'id="use_models_input_dropdown" class="form-control tip" style="width:100%;" required="required"');
                                     ?>
                               </div>
                            </div>
                         </div>

                         <div class="col-md-4">
                            <div class="form-group">
                               <label class="control-label" for="use_rtl"><?php echo lang('use_rtl'); ?></label>
                               <div class="controls">
                                  <?php
                                     echo form_dropdown('use_rtl', $wm, $settings->use_rtl, 'id="use_rtl" class="form-control tip" style="width:100%;" required="required"');
                                     ?>
                               </div>
                            </div>
                         </div>
                        
                        
                        <div class="col-lg-4">

                            <div class="form-group">

                                <?php echo lang('currency', 'currency');?> <strong>Symbol</strong>

                                <input id="currency" name="currency" type="text" class="validate form-control" value="<?php echo escapeStr($settings->currency); ?>">

                            </div>

                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('display_currency_symbol', 'display_symbol'); ?>
                                <?php $opts = [0 => lang('disable'), 1 => lang('before'), 2 => lang('after')]; ?>
                                <?= form_dropdown('display_symbol', $opts, $settings->display_symbol, 'class="form-control" id="display_symbol" style="width:100%;" required="required"'); ?>
                            </div>
                        </div>


                        <input type="hidden" name="product_discount" value="<?php echo $settings->product_discount; ?>">


                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="purchase_prefix"><?php echo lang("purchase_prefix"); ?></label>
                                <?php echo form_input('purchase_prefix', $settings->purchase_prefix, 'class="form-control tip" id="purchase_prefix"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="sales_prefix"><?php echo lang("sales_prefix"); ?></label>
                                <?php echo form_input('sales_prefix', $settings->sales_prefix, 'class="form-control tip" id="sales_prefix"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="payment_prefix"><?php echo lang("payment_prefix"); ?></label>
                                <?php echo form_input('payment_prefix', $settings->payment_prefix, 'class="form-control tip" id="payment_prefix"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="return_prefix"><?php echo lang("return_prefix"); ?></label>
                                <?php echo form_input('return_prefix', $settings->return_prefix, 'class="form-control tip" id="return_prefix"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="repair_prefix"><?php echo lang("repair_prefix"); ?></label>
                                <?php echo form_input('repair_prefix', $settings->repair_prefix, 'class="form-control tip" id="repair_prefix"'); ?>
                            </div>
                        </div>



                        <div class="col-md-4">

                            <div class="form-group">

                                <label class="control-label"

                                       for="reference_format"><?php echo lang("reference_format"); ?></label>



                                <div class="controls">

                                    <?php

                                    $ref = array(1 => lang('prefix_year_no'), 2 => lang('prefix_month_year_no'), 3 => lang('sequence_number'));

                                    echo form_dropdown('reference_format', $ref, $settings->reference_format, 'class="form-control tip" required="required" id="reference_format" style="width:100%;"');

                                    ?>

                                </div>

                            </div>

                        </div>



                        <input type="hidden" name="disable_editing" value="<?php echo $settings->disable_editing; ?>">


                        <div class="col-md-4">

                            <div class="form-group">

                                <label><?php echo lang('Require pin number at checkout');?></label>

                                <?php echo form_dropdown('random_admin', $wm, $settings->random_admin, 'class="form-control" id="random_admin" required="required"'); ?>

                            </div>

                        </div>
                        

                        <div class="col-md-4">

                            <div class="form-group">

                                <label class="control-label"> <?php echo lang('Labor Cost');?></label>

                                <div class="controls">

                                    <?php

                                    echo form_dropdown('disable_labor', $ps, $settings->disable_labor, 'id="disable_labor" class="form-control tip" required="required" style="width:100%;"');

                                    ?>

                                </div>

                            </div>

                        </div>

                         <div class="col-md-4">

                            <div class="form-group">

                                <label class="control-label"> <?php echo lang('store_wise_reference');?></label>

                                <div class="controls">

                                    <?php

                                    echo form_dropdown('store_wise_reference', $wm, $settings->store_wise_reference, 'id="store_wise_reference" class="form-control tip" required="required" style="width:100%;"');

                                    ?>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"><?php echo lang('Next Sale Number');?></label>
                                <div class="controls">
                                    <input type="number" min="<?php echo $auto_increment_value; ?>" name="sale_start_number" class="form-control" value="<?php echo ($auto_increment_value); ?>">
                                </div>
                            </div>
                        </div>


                        <?php 
                            $templates = array(
                                1 => lang('BasicTemplate'),
                                2 => lang('ProTemplate'),
                                3 => lang('RecieptTemplate'),
                                4 => lang('E-Services Template'),
                            );
                        ?>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <?php echo lang('invoice_template', 'invoice_template');?>
                                <?php echo form_dropdown('invoice_template', $templates, $settings->invoice_template, 'class="form-control m-bot15" style="width: 100%"'); ?>
                            </div>
                        </div>

                        <?php 
                            $templates = array(
                                1 => lang('BasicTemplate'),
                                2 => lang('ProTemplate'),
                                3 => lang('RecieptTemplate'),
                                5 => lang('ReportExtendedTemplate'),
                            );
                        ?>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <?php echo lang('report_template', 'report_template');?>
                                <?php echo form_dropdown('report_template', $templates, $settings->report_template, 'class="form-control m-bot15" style="width: 100%"'); ?>
                            </div>
                        </div>
                        
                         <div class="col-md-4">
                            <div class="form-group">
                                <label>Require Clock In</label>
                                <?php echo form_dropdown('require_clockin', $wm, $settings->require_clockin, 'class="form-control" id="require_clockin" required="required"'); ?>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" data-toggle='tooltip' data-placement='top' title='Pick a time when the user will automatically clocked out.'><?php echo lang('Auto Clock Out');?></label>
                                 <div class="input-group bootstrap-timepicker timepicker">
                                    <input type="text" class="form-control" id="datetimepicker-2" name="auto_clockout" value="<?php echo $settings->auto_clockout; ?>"  data-toggle='tooltip' data-placement='top' title='Pick a time when the drawer will automatically close for the day.'>
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"><?php echo lang('Default Purchase Bank');?></label>
                                <?php echo form_dropdown('purchase_bank_id', $bank_accounts, set_value('purchase_bank_id',$settings->purchase_bank_id),'id="purchase_bank_id" class="form-control" required'); ?>
                                
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"><?php echo lang('Default POS Bank');?></label>
                                <?php echo form_dropdown('pos_bank_id', $bank_accounts, set_value('pos_bank_id',$settings->pos_bank_id),'id="pos_bank_id" class="form-control" required'); ?>
                            </div>
                        </div>

                        <div class="nsac">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="decimals_sep"><?= lang('decimals_sep'); ?></label>

                                    <div class="controls"> <?php
                                        $dec_point = ['.' => lang('dot'), ',' => lang('comma')];
                                        echo form_dropdown('decimals_sep', $dec_point, $settings->decimals_sep, 'class="form-control tip" id="decimals_sep"  style="width:100%;" required="required"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-4">
                                  <div class="form-group">
                                     <?php
                                        $rows_per_page = array(
                                            -1 => "All",
                                            10 => "10",
                                            25 => "25",
                                            50 => "50",
                                            100 => "100",
                                        ); 
                                        
                                        ?>
                                     <label class="control-label" for="rows_per_page"><?= lang("rows_per_page"); ?></label>
                                     <?= form_dropdown('rows_per_page', $rows_per_page,$settings->rows_per_page, 'class="form-control tip" id="rows_per_page" required="required"'); ?>
                                  </div>
                               </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="thousands_sep"><?= lang('thousands_sep'); ?></label>
                                    <div class="controls"> <?php
                                        $thousands_sep = ['.' => lang('dot'), ',' => lang('comma'), '0' => lang('space')];
                                        echo form_dropdown('thousands_sep', $thousands_sep, $settings->thousands_sep, 'class="form-control tip" id="thousands_sep"  style="width:100%;" required="required"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>






                        </fieldset>
                        </div>
                        <div class="col-md-12">
                            <fieldset>
                                <legend><?php echo lang('Universal Settings');?></legend>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo lang('Make all customers universal');?></label>
                                        <?php echo form_dropdown('universal_clients', $wm, $settings->universal_clients, 'class="form-control" id="universal_clients" required="required"'); ?>
                                        <small><?php echo lang('warning_universal_customers');?></small>
                                    </div>
                                </div>
                                 <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo lang('Make all accessories universal');?></label>
                                        <?php echo form_dropdown('universal_accessories', $wm, $settings->universal_accessories, 'class="form-control" id="universal_accessories" required="required"'); ?>
                                        <small><?php echo lang('warning_universal_accessories');?></small>
                                    </div>
                                </div>
                                 <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo lang('Make all plans universal');?></label>
                                        <?php echo form_dropdown('universal_plans', $wm, $settings->universal_plans, 'class="form-control" id="universal_plans" required="required"'); ?>
                                        <small><?php echo lang('warning_universal_plans');?></small>
                                    </div>
                                </div>
                                 <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo lang('Make all other products universal');?></label>
                                        <?php echo form_dropdown('universal_others', $wm, $settings->universal_others, 'class="form-control" id="universal_others" required="required"'); ?>
                                        <small><?php echo lang('warning_universal_other');?></small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo lang('Make all Manufacturers universal');?></label>
                                        <?php echo form_dropdown('universal_manufacturers', $wm, $settings->universal_manufacturers, 'class="form-control" id="universal_manufacturers" required="required"'); ?>
                                        <small><?php echo lang('warning_universal_Manufacturers');?></small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo lang('Make all Carriers universal');?></label>
                                        <?php echo form_dropdown('universal_carriers', $wm, $settings->universal_carriers, 'class="form-control" id="universal_carriers" required="required"'); ?>
                                        <small><?php echo lang('warning_universal_Carriers');?></small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo lang('Make all Suppliers universal');?></label>
                                        <?php echo form_dropdown('universal_suppliers', $wm, $settings->universal_suppliers, 'class="form-control" id="universal_suppliers" required="required"'); ?>
                                        <small><?php echo lang('warning_universal_Suppliers');?></small>
                                    </div>
                                </div>

                            </fieldset>
                        </div>
                         </div>

                           <div class="clearfix"></div>

                    </div>
                    <?php endif; ?>


                    <?php if($this->Admin || $GP['settings-order_repairs']): ?>
                    <div   role="tabpanel" class="tab-pane" id="orders"><h3><?php echo lang('repair'); ?></h3>

                        <div class="col-lg-12">

                            <div class="form-group">

                                <strong><?php echo lang('repair_category_span');?></strong>

                                <select id="category" name="category[]" class="form-control m-bot15 select2-hidden-accessible" multiple="" width="100%" tabindex="-1" aria-hidden="true" style="width: 100%">

                                    <?php

                                    foreach(explode(",", $settings->category) as $line){

                                        echo '<option data-select2-tag="true" selected value="'.$line.'">'.$line.'</option>';

                                    }

                                    ?>

                                </select>

                            </div>

                        </div>

                        <div class="col-lg-12">

                            <div class="form-group">

                                <?php echo lang('custom_fields', 'custom_fields');?>



                                <select id="custom_fields" name="custom_fields[]" class="form-control m-bot15 select2-hidden-accessible" multiple="" width="100%" tabindex="-1" aria-hidden="true" style="width: 100%">

                                <?php

                                    foreach(explode(",", $settings->custom_fields) as $line){

                                        echo '<option data-select2-tag="true" selected value="'.$line.'">'.$line.'</option>';

                                    }

                                    ?>

                                </select>

                            </div>

                        </div>

                        <div class="col-lg-12">

                            <div class="form-group">

                                <label><?php echo lang('Custom Checkmarks');?></label>



                                <select id="repair_custom_checkbox" name="repair_custom_checkbox[]" class="form-control" multiple="" width="100%" style="width: 100%">

                                <?php

                                    foreach(explode(",", $settings->repair_custom_checkbox) as $line){

                                        echo '<option selected value="'.$line.'">'.$line.'</option>';

                                    }

                                    ?>

                                </select>

                            </div>

                        </div>

                        <div class="col-lg-12">

                            <div class="form-group">

                                <label><?php echo lang('repair_checklist_span');?></label>



                                <select id="repair_custom_toggles" name="repair_custom_toggles[]" class="form-control" multiple="" width="100%" style="width: 100%">

                                <?php

                                    foreach(explode(",", $settings->repair_custom_toggles) as $line){

                                        echo '<option selected value="'.$line.'">'.$line.'</option>';

                                    }

                                    ?>

                                </select>

                            </div>

                        </div>

                           <div class="clearfix"></div>


                    </div>
                    <?php endif; ?>
<?php if($this->Admin || $GP['settings-quote']): ?>
    <div   role="tabpanel" class="tab-pane" id="invoice">



        <h3><?php echo lang('invoice_title');?></h3>

                        <div class="col-lg-6">

                            <div class="row">

                                <div class="col-lg-6">

                                    <div class="form-group">

                                        <?php echo lang('invoice_name', 'invoice_name');?>

                                        <div class="input-group">

                                            <div class="input-group-addon">

                                                <i class="fas  fa-user"></i>

                                            </div>

                                            <input name="invoice_name" id="invoice_name" type="text" class="validate form-control" value="<?php echo escapeStr($settings->invoice_name); ?>">

                                        </div>



                                    </div>

                                </div>

                                <div class="col-lg-6">

                                    <div class="form-group">

                                        <?php echo lang('invoice_email', 'invoice_mail');?>



                                        <div class="input-group">

                                            <div class="input-group-addon">

                                                <i class="fas  fa-quote-left"></i>

                                            </div>

                                            <input name="invoice_mail" id="invoice_mail" type="text" class="validate form-control" value="<?php echo escapeStr($settings->invoice_mail); ?>">

                                        </div>

                                    </div>

                                </div>

                                <div class="col-lg-12">

                                    <div class="form-group">

                                        <?php echo lang('invoice_address', 'invoice_address');?>

                                        <div class="input-group">

                                            <div class="input-group-addon">

                                                <i class="fas fa-street-view"></i>

                                            </div>

                                            <input id="invoice_address" name="invoice_address" type="text" class="validate form-control" value="<?php echo escapeStr($settings->address); ?>">

                                        </div>

                                    </div>

                                </div>
                                <div class="col-md-6 form-group ">
                                    <label class="control-label" for="city"><?php echo lang('City');?>:</label>
                                    <input class="form-control" name="city" type="text" id="city" value="<?php echo escapeStr($settings->city); ?>" >
                                </div>
                                <div class="col-md-3 form-group ">
                                    <label class="control-label" for="state"><?php echo lang('State/Province');?>:</label>
                                    <input class="form-control" name="state" type="text" id="state" value="<?php echo escapeStr($settings->state); ?>">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="control-label" for="zip"><?php echo lang('Postal/Zip Code');?>:</label>
                                    <input class="form-control" name="zip" type="text" id="zip" value="<?php echo escapeStr($settings->zipcode); ?>" >
                                </div>


                            </div>

                        </div>

                        <div class="col-lg-6">

                            <div class="row">

                                <div class="col-lg-12">

                                    <div class="form-group">

                                        <?php echo lang('invoice_phone', 'invoice_phone');?>

                                        <div class="input-group">

                                            <div class="input-group-addon">

                                                <i class="fas fa-phone"></i>

                                            </div>

                                            <input id="invoice_phone" name="invoice_phone" type="text" class="validate form-control" value="<?php echo escapeStr($settings->phone); ?>">

                                        </div>

                                    </div>

                                </div>

                                <div class="col-lg-12">

                                    <div class="form-group">

                                        <?php echo lang('invoice_vat', 'invoice_vat');?>

                                        <div class="input-group">

                                            <div class="input-group-addon">

                                                <i class="fas fa-certificate"></i>

                                            </div>

                                            <input id="invoice_vat" name="invoice_vat" type="text" class="validate form-control" value="<?php echo escapeStr($settings->vat); ?>">

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-lg-12">

                            <div class="form-group">

                                <?php echo lang('invoice_disclaimer', 'disclaimer');?>

                                <textarea class="form-control" id="disclaimer" name="disclaimer" style="height: 107px" maxlength="2500" rows="6"><?php echo escapeStr($settings->disclaimer); ?></textarea>

                            </div>

                        </div>
                           <div class="clearfix"></div>

    </div>
<?php endif; ?>
<?php if($this->Admin || $GP['settings-sms']): ?>
    <div role="tabpanel" class="tab-pane" id="sms">

        <h3><?php echo $this->lang->line('sms_title');?></h3>

                            <form action="">

                            <div class="col-lg-3 nexmo-info radio-sms" <?php if($settings->usesms == 1 ) echo 'style="opacity: 1;"'; ?>>
                            <label>
                                <input type="radio" id="nexmo" name="usesms" value="1" <?php if($settings->usesms == 1 ) echo 'checked'; ?> />
                                <img style="width: 48%" src="<?php echo site_url(); ?>assets/images/nexmo.png">
                            </label>
                            <div class="form-group">
                                <label class="title">
                                    <?php echo lang('nexmo');?>
                                </label>
                                <input name="n_api_key" id="n_api_key" type="text" class="validate form-control" placeholder="Nexmo <?php echo lang('api_key')?>" value="<?php echo escapeStr($settings->nexmo_api_key) ?>">
                                <input name="n_api_secret" id="n_api_secret" type="text" class="validate form-control" placeholder="Nexmo <?php echo lang('api_secret')?>" value="<?php echo escapeStr($settings->nexmo_api_secret) ?>">
                                
                            </div>
                        </div>
                        <div class="col-lg-3 twilio-info radio-sms" <?php if($settings->usesms == 2 ) echo 'style="opacity: 1;"'; ?>>
                            <label>
                                <input type="radio" id="twilio" name="usesms" value="2" <?php if($settings->usesms == 2 ) echo 'checked'; ?> />
                                <img style="width: 48%" src="<?php echo site_url(); ?>assets/images/twilio.jpg">
                            </label>
                            <div class="form-group">
                                <label class="title">
                                    <?php echo lang('twilio');?>
                                </label>
                                <select name="t_mode" id="t_mode" data-num="1" class="form-control m-bot15" style="width: 100%">
                                    <option <?php if($settings->twilio_mode == 'sandbox' ) echo 'selected'; ?>>sandbox</option>
                                    <option <?php if($settings->twilio_mode == 'prod' ) echo 'selected'; ?>>prod</option>
                                </select>
                                <input name="t_account_sid" id="t_account_sid" type="text" class="validate form-control" placeholder="<?php echo lang('sid');?>" value="<?php echo escapeStr($settings->twilio_account_sid); ?>">
                                <input name="t_token" id="t_token" type="text" class="validate form-control" placeholder="<?php echo lang('token');?>" value="<?php echo escapeStr($settings->twilio_auth_token); ?>">
                                <input name="t_number" id="t_number" type="text" class="validate form-control" placeholder="Twilio <?php echo lang('number');?>" value="<?php echo escapeStr($settings->twilio_number); ?>">
                            </div>
                        </div>

                        <div class="col-lg-3 smsgateway-info radio-sms" <?php if($settings->usesms == 3 ) echo 'style="opacity: 1;"'; ?>>
                            <label>
                                <input type="radio" id="smsgateway" name="usesms" value="3" <?php if($settings->usesms == 3 ) echo 'checked'; ?> />
                                <img style="width: 48%" src="<?php echo site_url(); ?>assets/images/smsgateway.jpg">
                            </label>
                            <div class="form-group">
                                <label class="title">
                                    <?php echo lang('smsgateway');?>
                                </label>
                                <input name="smsgateway_device_id" id="device_id" type="text" class="validate form-control" placeholder="<?php echo lang('smsgateway_device_id');?>" value="<?php echo escapeStr($settings->smsgateway_device_id); ?>">
                                <input name="smsgateway_token" id="token" type="text" class="validate form-control" placeholder="<?php echo lang('smsgateway_token');?>" value="<?php echo escapeStr($settings->smsgateway_token); ?>">
                            </div>
                        </div>

                         <div class="col-lg-3 http_api-info radio-sms" <?php if($settings->usesms == 4 ) echo 'style="opacity: 1;"'; ?>>
                            <label>
                                <input type="radio" id="http_api" name="usesms" value="4" <?php if($settings->usesms == 4 ) echo 'checked'; ?> />
                                <img style="width: 48%" src="<?php echo site_url(); ?>assets/images/http_api.png">
                            </label>
                            <div class="form-group">
                                <label class="title">
                                    <?php echo lang('http_api');?>
                                </label>
                                <?php echo form_dropdown('default_http_api', $this->settings_model->getSMSGatewaysDP(), ($settings->default_http_api), 'style="width:100%;"');?>
                            </div>
                        </div>
                        
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="title">
                                   <?php echo lang('email_smtp');?>
                                </label>
                                <input name="smtp_host" id="smtp_host" type="text" class="validate form-control" placeholder="<?php echo lang('smtp_host');?>" value="<?php echo escapeStr($settings->smtp_host); ?>">
                                <input name="smtp_user" id="smtp_user" type="text" class="validate form-control" placeholder="<?php echo lang('smtp_user');?>" value="<?php echo escapeStr($settings->smtp_user); ?>">
                                <input name="smtp_pass" id="smtp_pass" type="text" class="validate form-control" placeholder="<?php echo lang('smtp_pass');?>" value="<?php echo escapeStr($settings->smtp_pass); ?>">
                                <input name="smtp_port" id="smtp_port" type="text" class="validate form-control" placeholder="<?php echo lang('smtp_port');?>" value="<?php echo escapeStr($settings->smtp_port); ?>">
                                <?php
                                    $crypto_opt = array('' => lang('none'), 'tls' => 'TLS', 'ssl' => 'SSL');
                                    echo form_dropdown('smtp_crypto', $crypto_opt, escapeStr($settings->smtp_crypto), 'class="form-control tip" id="smtp_crypto"');
                                    ?> 
                            </div>
                        </div>
                        
                            <div class="col-lg-12">
                                    <div class="form-group">
                                            <label><?php echo lang('Customer Purchase - Email');?></label>
                                            <textarea class="form-control" id="customer_purchase" name="customer_purchase" style="height: 107px"  rows="6">
                                            <?php echo file_get_contents(FCPATH.'themes/'.$this->theme.'/email_templates/customer_purchase.html'); ?>
                                            </textarea>
                                    </div>
                            </div>

                        <div class="clearfix"></div>

                </div>
<?php endif; ?>
 <?php if($this->Admin || $GP['settings-pos_configuration']): ?>
                <div   role="tabpanel" class="tab-pane" id="pos">

                        <h3><?php echo lang('Point Of Sale Config');?></h3>

                         <div class="col-md-4">

                            <div class="form-group">

                                <label class="control-label" data-toggle='tooltip' data-placement='top' title='This is the amount required to be in the drawer before it can be opened.'> <?php echo lang('Set Drawer Amount');?></label>

                                <div class="controls">

                                    <input type="text" class="form-control" name="drawer_amount" value="<?php echo $settings->drawer_amount; ?>"  data-toggle='tooltip' data-placement='top' title='This is the amount required to be in the drawer before it can be opened.'>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <?= lang('after_sale_page', 'after_sale_page'); ?>
                                <?php $popts = [0 => lang('receipt'), 1 => lang('POS')]; ?>
                                <?= form_dropdown('after_sale_page', $popts, $settings->after_sale_page, 'class="form-control" id="after_sale_page" required="required"'); ?>
                            </div>
                        </div>


                        <div class="col-md-4">

                            <div class="form-group">

                                <label class="control-label" data-toggle='tooltip' data-placement='top' title='When the drawer reaches this amount of cash, a safe deposit will be required.'> <?php echo lang('Set Max Drawer Amount');?></label>

                                <div class="controls">

                                    <input type="text" class="form-control" name="max_drawer_amount" value="<?php echo $settings->max_drawer_amount; ?>"  data-toggle='tooltip' data-placement='top' title='When the drawer reaches this amount of cash, a safe deposit will be required.'>

                                </div>

                            </div>

                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" data-toggle='tooltip' data-placement='top' title='Pick a time when the drawer will automatically close for the day.'><?php echo lang('Time to Auto Close Drawer');?></label>
                                 <div class="input-group bootstrap-timepicker timepicker">
                                    <input type="text" class="form-control datetimepicker"  name="auto_close_drawer" value="<?php echo $settings->auto_close_drawer; ?>"  data-toggle='tooltip' data-placement='top' title='Pick a time when the drawer will automatically close for the day.'>
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label data-toggle="tooltip" data-placement="top" title="This setting allows repair parts to be sold to customers, outside of a repair."><?php echo lang('Sell Repair Parts');?></label>
                                <?php echo form_dropdown('sell_repair_parts', $wm, $settings->sell_repair_parts, 'class="form-control" id="sell_repair_parts" required="required" data-toggle="tooltip" data-placement="top" title="This setting allows repair parts to be sold to customers, outside of a repair."' ); ?>
                            </div>
                        </div>

                                                <div class="clearfix"></div>
                        <div class="col-md-12">
                        <hr>
                                                    <span><h4><?php echo lang('Accepted Payment Methods');?></h4></span>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <div class="checkbox-styled">
                                        <input type="hidden" name="accept_cash" value="0">
                                        <input <?php echo ($settings->accept_cash) ? 'checked' : '' ?> type="checkbox" name="accept_cash" id="accept_cash" value="1">
                                        <label for="accept_cash"><?php echo lang('Accept Cash');?></label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="checkbox-styled">
                                        <input type="hidden" name="accept_cheque" value="0">
                                        <input <?php echo ($settings->accept_cheque) ? 'checked' : '' ?> type="checkbox" name="accept_cheque" id="accept_cheque" value="1">
                                        <label for="accept_cheque"><?php echo lang('Accept Check');?></label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="checkbox-styled">
                                        <input type="hidden" name="accept_cc" value="0">
                                        <input <?php echo ($settings->accept_cc) ? 'checked' : '' ?> type="checkbox" name="accept_cc" id="accept_cc" value="1">
                                        <label for="accept_cc"><?php echo lang('Accept Credit Card');?></label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="checkbox-styled">
                                        <input type="hidden" name="accept_paypal" value="0">
                                        <input <?php echo ($settings->accept_paypal) ? 'checked' : '' ?> type="checkbox" name="accept_paypal" id="accept_paypal" value="1">
                                        <label for="accept_paypal"><?php echo lang('Accept Paypal');?></label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="checkbox-styled">
                                        <input type="hidden" name="accept_authorize" value="0">
                                        <input <?php echo ($settings->accept_authorize) ? 'checked' : '' ?> type="checkbox" name="accept_authorize" id="accept_authorize" value="1">
                                        <label for="accept_authorize"><?php echo lang('Accept Authorize');?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"><?php echo lang('Authorize.Net Login ID');?></label>
                                <input type="text" class="form-control"  name="authorize_login_id" value="<?php echo $settings->authorize_login_id; ?>" >
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"><?php echo lang('Authorize.Net Transaction Key');?></label>
                                <input type="text" class="form-control"  name="authorize_transaction_id" value="<?php echo $settings->authorize_transaction_id; ?>" >
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"><?php echo lang('Authorize.Net Client Key');?></label>
                                <input type="text" class="form-control"  name="authorize_client_key" value="<?php echo $settings->authorize_client_key; ?>" >
                            </div>
                        </div>
                                                <div class="clearfix"></div>

                        <div class="col-lg-12">
                            <div class="form-group">
                            <hr>
                                <strong><?php echo lang('receipt_footer');?></strong>
                                <textarea class="form-control" id="disclaimer_sale" name="disclaimer_sale" style="height: 107px" rows="6"><?php echo $settings->disclaimer_sale; ?></textarea>
                            </div>
                        </div>
                           <div class="clearfix"></div>

                    </div>
 <?php endif; ?>
        <div   role="tabpanel" class="tab-pane" id="notify">
            <h3><?php echo lang('Email Notifications');?></h3>
            <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <td>
                            <?php echo lang('User');?>
                        </td>
                        <td>
                            <?php echo lang('Sale');?>
                        </td>
                        <td>
                            <?php echo lang('Sale Refund');?>
                        </td>
                        <td>
                           <?php echo lang('Repair Recieved');?>
                        </td>
                        <td>
                            <?php echo lang('Inventory Ordered');?>
                        </td>
                        <td>
                            <?php echo lang('Inventory Recieved');?>
                        </td>
                        <td>
                            <?php echo lang('Customer Purchase');?>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                                $rows = $this->db->where('hidden', 0)->where('active', 1)->get('users')->result();
                                foreach($rows as $row): ?>
                                <tr>
                                    <td><?php echo $row->first_name; ?> <?php echo $row->last_name; ?></td>
                                    <td>
                                        <input type="checkbox" <?php echo in_array($row->id ,explode(',', $settings->notify_sales)) ? 'checked' : ''; ?> class="notify" name="notify_sales[]" id="notify_sales<?php echo $row->id; ?>" value="<?php echo $row->id; ?>">
                                    </td>
                                    <td>
                                        <input type="checkbox" <?php echo in_array($row->id ,explode(',', $settings->notify_refund)) ? 'checked' : ''; ?> class="notify" name="notify_refund[]" id="notify_refund<?php echo $row->id; ?>" value="<?php echo $row->id; ?>">
                                    </td>
                                    <td>
                                        <input type="checkbox" <?php echo in_array($row->id ,explode(',', $settings->notify_repair)) ? 'checked' : ''; ?> class="notify" name="notify_repair[]" id="notify_repair<?php echo $row->id; ?>" value="<?php echo $row->id; ?>">
                                    </td>
                                    <td>
                                        <input type="checkbox" <?php echo in_array($row->id ,explode(',', $settings->notify_porder)) ? 'checked' : ''; ?> class="notify" name="notify_porder[]" id="notify_porder<?php echo $row->id; ?>" value="<?php echo $row->id; ?>">
                                    </td>
                                    <td>
                                        <input type="checkbox" <?php echo in_array($row->id ,explode(',', $settings->notify_preceive)) ? 'checked' : ''; ?> class="notify" name="notify_preceive[]" id="notify_preceive<?php echo $row->id; ?>" value="<?php echo $row->id; ?>">
                                    </td>
                                    <td>
                                        <input type="checkbox" <?php echo in_array($row->id ,explode(',', $settings->notify_cpurchase)) ? 'checked' : ''; ?> class="notify" name="notify_cpurchase[]" id="notify_cpurchase<?php echo $row->id; ?>" value="<?php echo $row->id; ?>">
                                    </td>
                            </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        </div>

        <div role="tabpanel" class="tab-pane" id="customer_notifications">
            <h3><?php echo lang('Customer Notifications');?></h3>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label"><?php echo lang('due_bill_notify_before');?></label>
                      <input type="number" value="<?php echo $settings->due_bill_notify_before; ?>" class="form-control" name="due_bill_notify_before" min="1" max="29" id="due_bill_notify_before">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label"><?php echo lang('due_bill_notify_when');?></label>
                    <?php echo form_input('due_bill_notify_when', $settings->due_bill_notify_when, 'class="form-control datetimepicker tip" id="due_bill_notify_when"'); ?>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="form-group">
                    <label><?php echo lang('due_bill_message');?></label>
                    <textarea maxlength="160" class="form-control" id="due_bill_message" name="due_bill_message" rows="6"><?php echo escapeStr($settings->due_bill_message); ?></textarea>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
</div>

        <!-- /.box-body -->
        <div class="box-footer">
            <button style="width: 100%" id='submit' class='btn btn-success'><i class="fas fa-save"></i>
                <?php echo lang('save'); ?>
            </button>
        </form>
<div class="clearfix"></div>

        </div>



        <?php if($this->Admin || $GP['settings-general_settings']): ?>
        <div class="row">

            <div class="col-lg-12">



                <h3> <?php echo lang('logo_upload_title'); ?> </h3>

                <div class="col-lg-12">

                    <div class="row form-group">

                        <div class="col-lg-12">

                            <img id="preview_logo" width="60px" src="<?php echo base_url(); ?>assets/uploads/logos/<?php echo $settings->logo;?>">

                        </div>

                        <div class="col-lg-12">

                            <label id="error_message"></label>

                            <div class="input-group logo_upload">

                                <span><?php echo lang('upload_label'); ?></span>

                                <form name="uploadImage" id="uploadimage" action="<?php echo site_url('panel/settings/upload_image');?>" method="post" enctype="multipart/form-data">

                                    <input id="logo_upload" type="file" data-browse-label="Browse" name="logo_upload" data-show-upload="false" data-show-preview="false" accept="image/*" class="form-control file">
                                     <br/>
                                    <?php echo lang('width');?>: <span id='width'></span><br/>
                                    <?php echo lang('height');?>: <span id='height'></span>
                                    <input type="submit" value="<?php echo lang('upload_label'); ?>" class="submit" style="display: none;">

                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
        <?php endif; ?>
      </div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#disclaimer, #disclaimer_sale, #r_opening_email, #customer_purchase').wysihtml5();
    });
</script>
<script type="text/javascript">
        $(document).ready(function(){


            $('.notify').iCheck({
                checkboxClass: 'icheckbox_flat',
                radioClass: 'iradio_flat'
            });
            $('#default_smtp').on("change", function () {
                if($(this).is(":checked"))   
                    $(".smtp_div").slideUp();
                else
                    $(".smtp_div").slideDown();

            });

        });
</script>