
<script type="text/javascript">
    
    if (localStorage.getItem('slitems')) {
        localStorage.removeItem('slitems');
    }
    jQuery(document).ready( function($) {

         $('.model_name_typeahead').typeahead(null, {
            name: 'model',
            display: 'name',
            source: function(query, syncResults, asyncResults) {
                $.get( '<?=base_url();?>panel/inventory/getModels/'+query+'?manufacturer='+encodeURI($('#reparation_manufacturer').val()), function(data) {
                    asyncResults(data);
                });
            }
        });

         
        $('.parsley-form').parsley({
            successClass: 'has-success',
            errorClass: 'has-error',
            classHandler: function(el) {
                return el.$element.closest(".form-group");
            },
            errorsWrapper: '<span class="help-block"></span>',
            errorTemplate: "<span></span>",
            errorsContainer: function(el) {
                return el.$element.closest('.form-group');
            },
        });
        $( ".client_name" ).select2({
            ajax: {
                url: "<?php echo base_url(); ?>panel/customers/getAjax/no",
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
        });
    });
</script>

<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('edit_repair');?></h3>
    </div>
    <div class="box-body">
          <form id="rpair_form" class="parsley-form">
           <div id="preprepair_hide">

           </div>
            <div class="row">
                <div class="col-md-6">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <?php echo lang('client_title', 'client_name');?>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas  fa-user"></i>
                                </div>
                                <select required="" id="client_name" name="client_name" data-num="1" class="form-control client_name" style="width: 100%">
                                    <option></option>
                                    <?php
                                        foreach ($clients as $client) :
                                        echo '<option value="'.$client->id.'">'.$client->first_name.' '.$client->last_name.' '.preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $client->telephone).'</option>';
                                        endforeach;
                                    ?>
                                </select>
                                <a class="add_c btn input-group-addon"><i class="fas fa-user-plus"></i></a>
                                <a  style="display: none;" class="edit_c btn input-group-addon"  id="modify_client"><i class="fas fa-edit"></i></a>

                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <?php echo lang('repair_category', 'category_select');?>
                            <div class="select_cat">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fas  fa-folder"></i>
                                    </div>
                                    <select <?php echo $frm_priv['category'] ? 'required' : ''; ?> id="category_select" name="category_select" data-num="1" class="form-control m-bot15" style="width: 100%">
                                    <option></option>

                                        <?php
                                        $categories = explode(',', $settings->category);
                                        foreach($categories as $line){
                                            echo '<option value="'.$line.'">'.$line.'</option>';
                                        }
                                        ?>
                                        <option value="other">Other</option>

                                    </select>
                                </div>
                            </div>
                            <div class="input-group inp_cat">
                                <div class="input-group-addon">
                                    <i class="fas  fa-folder"></i>
                                </div>
                                <input id="category_input" name="category_input" type="text" class="validate form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <div class="form-group">
                                <?php echo lang('model_manufacturer', 'model_manufacturer');?>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fas  fa-folder"></i>
                                    </div>
                                     <select <?php echo $frm_priv['manufacturer'] ? 'required' : ''; ?> id="manufacturer" name="manufacturer" data-num="1" class="form-control m-bot15" style="width: 100%">
                                    <option></option>
                                        <?php
                                            foreach ($manufacturers as $manufacturer) :
                                            echo '<option value="'.$manufacturer->id.'">'.$manufacturer->name.'</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                        <a class="add_manufacturer btn input-group-addon"><i class="fas fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <div class="form-group">
                                <?php echo lang('repair_model', 'model');?>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fas  fa-folder"></i>
                                    </div>
                                    <input class="form-control model_name_typeahead" id="reparation_model" name="model" <?php echo $frm_priv['model'] ? 'required' : ''; ?> style="width: 100%;">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="row">

                    <div class="col-lg-6">
                        <div class="form-group">
                            <div class="form-group">
                                <?php echo lang('repair_defect', 'defect');?>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fas  fa-link"></i>
                                    </div>
                                     <input <?php echo $frm_priv['defect'] ? 'required' : ''; ?>  id="defect" name="defect" type="text" class="validate form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <div class="form-group">
                                <label><?php echo lang('Assign Repair To');?></label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fas  fa-folder"></i>
                                    </div>
                                    <select <?php echo $frm_priv['assign_repair'] ? 'required' : ''; ?>  id="assigned_to" name="assigned_to" data-num="1" class="form-control m-bot15" style="width: 100%">
                                        <option></option>
                                        <?php
                                            foreach ($users as $user) :
                                            echo '<option value="'.$user->id.'">'.$user->first_name.' '.$user->last_name.'</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label><?php echo lang('Serial Number');?></label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas  fa-code"></i>
                                </div>
                                <input <?php echo $frm_priv['serial_number'] ? 'required' : ''; ?> id="serial_number" name="serial_number" class="validate form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 input-field">
                        <div class="form-group">
                            <label><?php echo lang('warranty_plans');?></label>
                            <?php $tr = array();
                            foreach ($warranty_plans as $plan) {
                                $tr[$plan['id']] = $plan['name'];
                            }
                            $warranty = json_decode($repair['warranty']);

                            echo form_dropdown('warranty_id', $tr, $warranty ? $warranty->id :'' , 'class="form-control tip" id="warranty_id" style="width:100%;" required');
                            ?>
                       </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <?php echo lang('repair_sms', 'sms');?>
                            <div class="checkbox-styled">
                                <input type="checkbox" class="skip" value="1" <?php echo $repair['sms'] ? 'checked' : '';?> name="sms" id="sms">
                                <label for="sms"><?php echo sprintf(lang('repair_sms_info'), ($settings->usesms == 1) ? 'Nexmo' : 'Plivo');?></label>
                            </div>
                        </div>
                       
                    </div>
                    <div class="col-lg-6">
                        
                        <div class="form-group">
                            <?php echo lang('repair_email', 'email');?>
                            <div class="checkbox-styled">
                                <input type="checkbox" class="skip" value="1" <?php echo $repair['email'] ? 'checked' : '';?> name="email" id="email">
                                <label for="email"><?php echo lang('repair_email');?></label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <?php
                        $custom = trim($settings->custom_fields);
                        if ($custom !== '') {
                            $custom = explode(',', $custom);
                            foreach($custom as $line){ 
                    ?>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label><?php echo $line; ?></label>
                                <input id="custom_<?php echo bin2hex($line); ?>" name="custom_<?php echo bin2hex($line); ?>" type="text" class="custom validate form-control">
                            </div>
                        </div>
                    <?php }}  ?>
                
                    <?php
                        $custom_checkmarks = trim($settings->repair_custom_checkbox);
                        if ($custom_checkmarks !== ''):
                            $custom_checkmarks = explode(',', $custom_checkmarks);
                            foreach($custom_checkmarks as $line):
                    ?>
                        <div class="col-lg-3">

                            <div class="checkbox-styled">
                                <input name="checkcustom_<?php echo bin2hex($line); ?>" type="hidden" value="0">
                                <input type="checkbox" class="skip" value="1" name="checkcustom_<?php echo bin2hex($line); ?>" id="checkcustom_<?php echo bin2hex($line); ?>">
                                <label for="checkcustom_<?php echo bin2hex($line); ?>"><?php echo $line; ?></label>
                            </div>
                            
                        </div>
                    <?php endforeach; endif;?>
                </div>

                
            </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group combo">
                        <?php echo lang("add_item", 'add_item'); ?>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fas  fa-link"></i>
                            </div>
                        <?php echo form_input('add_item', '', 'class="form-control ttip" id="add_item" data-placement="top" data-trigger="focus" data-bv-notEmpty-message="' . ('please_add_items_below') . '" placeholder="' . lang("add_item") . '"'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?php echo lang('repair_advance', 'advance');?>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fas  fa-dollar-sign"></i>
                            </div>
                            <input id="advance" name="advance" type="text" value="0" class="validate form-control">
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?=lang('taxrate_title', 'potax2');?>
                        <select id="potax2" class="form-control input-tip select" name="tax_id" style="width: 100%;">
                            <?php foreach ($taxRates as  $tax): ?>
                                <option value="<?= $tax->id ?>"><?= $tax->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <?php if(!$settings->disable_labor): ?>
                        <div class="form-group" data-toggle="tooltip" data-placement="top" title="<?php echo lang('If you are charging labor on this repair, enter it here. Labor is not taxed.');?>">
                            <?php echo lang('repair_service_charges', 'service_charges');?>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas  fa-dollar-sign"></i>
                                </div>
                                <input id="service_charges" name="service_charges" type="text" value="0" class="validate form-control" data-toggle="tooltip" data-placement="top" title="If you are charging labor on this repair, enter it here. Labor is not taxed.">
                            </div>
                        </div>

                    <?php else: ?>
                        <input type="hidden" name="service_charges" value="0">
                    <?php endif; ?>
                </div>

                
            </div>
            
            <div class="row">

                <div class="col-md-12">
                        <div class="control-group table-group">
                            <label class="table-label" for="combo"><?php echo lang("defective_items"); ?></label>

                            <div class="controls table-controls">
                                <table id="prTable"
                                       class="table items table-striped table-bordered table-condensed table-hover">
                                    <thead>
                                    <tr>
                                        <th class="col-md-5"><?php echo lang("product_name") . " (" . lang("product_code") . ")"; ?></th>
                                        <th class="col-md-3"><?php echo lang("unit_price"); ?></th>
                                        <th class="col-md-3"><?php echo lang('Discount');?></th>
                                        <th class="col-md-3"><?php echo lang('Tax');?></th>
                                        <th class="col-md-3"><?php echo lang('subtotal');?></th>
                                        <th class="col-md-1 text-center">
                                            <i class="fas fa-trash" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <td colspan="5"><?php echo lang('table_empty');?></td>
                                    </tbody>

                                </table>
                                <table class="table items table-striped table-bordered table-condensed table-hover">
                                    <tfoot>
                                        <tr>
                                            <th colspan="1" class="warning"><span class="pull-right"><?php echo lang('subtotal')?></span></th>
                                            <th colspan="1" class="info"><span id="price_span">0.00</span></th>

                                            <th colspan="1" class="warning"><span class="pull-right"><?php echo lang('Total Tax');?></span></th>
                                            <th colspan="1" class="success"><span id="tax_spane">0.00</span></th>

                                        </tr>
                                        <tr id="labor_tr">
                                            <th colspan="1" class="warning"><span class="pull-right"><?php echo lang('labor_cost_summay')?></span></th>
                                            <th colspan="1" class="success"><span id="sc_span">0.00</span></th>
                                            <th colspan="1" class="warning"><span class="pull-right"><?php echo lang('labor_cost_summay')?> + <?php echo lang('total'); ?></span></th>
                                            <th colspan="1" class="success"><span id="totalprice_span">0.00</span></th>

                                        </tr>
                                        <tr>
                                            <th colspan="3" class="warning"><span class="pull-right"><?php echo lang('grand_total'); ?></span></th>
                                            <th class="success"><span id="gtotal">0.00</span></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="clear: both;"></div>

        <div class="row">
            <div class="col-lg-12">
                <button href="#prerepair" class="prerepair_show btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Pre Repair Checklist
                </button>
                <hr>
                <div class="form-group">
                    <?php echo lang('repair_comment', 'comment'); ?>&nbsp;(<?php echo lang('please_note_defects'); ?>)
                    <textarea class="form-control" id="comment" name="comment" rows="6"></textarea>
                </div>
            </div>
        </div>

        <a class="pull-left btn btn-default" href="<?php echo base_url(); ?>panel/repair">
            <i class="fas fa-reply"></i> 
            <?php echo lang('go_back'); ?>
        </a>

            <div class="col-sm-2"> 
                <?php 
                    $statuses_ = []; 
                    foreach ($statuses as $status){
                        $statuses_[$status->id] = $status->label;
                    }
                ?>
                <?php 
                    echo form_dropdown('status', $statuses_, $repair['status'], 'class="form-control" id="status_edit"');
                ?>
            </div>
            <div class="col-sm-3" style="padding-left: 0;">
                    <input id="code" type="text" value="<?php echo $repair['code']; ?>" class="validate form-control" value="" placeholder="<?php echo lang('repair_code');?>">
                    </div>
            <div class="text-right">
                    

                    <button id="upload_modal_btn" class="btn btn-default" data-mode="edit" data-num="<?php echo $repair['id'];?>"><i class="fa fa-cloud"></i> <?php echo lang('view_attached');?></button>

                    <button class="btn btn-primary" id="sign_repair" href="#signModal" data-toggle="modal" data-mode="update_sign" data-num="<?php echo $repair['id'];?>"><i class="fas fa-signature"></i> <?php echo lang('sign_repair');?></button>

                    <button id="submit" form="rpair_form" class="btn btn-success">
                        <i class="fas fa-save"></i> <?php echo lang('Update Repair');?>
                    </button>
            </div>
        </form>
    </div>
</div>

 <div class="modal modal-default-filled fade" id="prerepair" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="exit_prepair" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo lang('Pre Repair Checklist');?></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <div class="row">
                        <form id="prerepair_form">
                        <div class="col-md-6">
                            <?php
                                $repair_custom_toggles = trim($settings->repair_custom_toggles);
                                if ($repair_custom_toggles !== ''):
                                    $repair_custom_toggles = explode(',', $repair_custom_toggles);
                                    foreach($repair_custom_toggles as $line):
                            ?>
                                    <div class="col-lg-6">
                                        <div class="checkbox-toggle-styled-on-off">
                                            <input name="checktoggle_<?php echo bin2hex($line); ?>" type="hidden" value="0">
                                            <input name="checktoggle_<?php echo bin2hex($line); ?>" id="checktoggle_<?php echo bin2hex($line); ?>" value="1" type="checkbox">
                                            <label for="checktoggle_<?php echo bin2hex($line); ?>"><?php echo $line; ?></label>
                                        </div>
                                    </div>
                            <?php endforeach; endif;?>
                        </div>
                        <div class="col-md-6">
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs">
                                    <li class=""><a href="#one-tab-default" data-toggle="tab" aria-expanded="false"><?php echo lang('Pin Code');?></a></li>
                                    <li class="active"><a href="#two-tab-default" data-toggle="tab" aria-expanded="true"><?php echo lang('Pattern');?></a></li>
                                </ul>
                                
                                <div class="tab-content">
                                    <div class="tab-pane" id="one-tab-default">
                                        <div class="form-group">
                                            <label><?php echo lang('Pin Code');?></label>
                                            <input type="text" name="cust_pin_code" class="form-control">
                                        </div>
                                    </div>
                                    <div class="tab-pane active" id="two-tab-default">
                                        <div id="patternHolder"></div>
                                        <input type="hidden" name="patternlock" id="patternlock">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="" class="modal-footer">
                <button class="btn btn-submit btn-primary" id="submit_prerepairs"><?php echo lang('Submit');?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function calculate_price() {
        var rows = $('#prTable').children('tbody').children('tr');
        
        var pp = 0;
        var pt = 0;
        $.each(rows, function () {
            pp += parseFloat($(this).find('.rprice').val());
            pt += parseFloat($(this).find('.rtax').val());
        });


        sc_tax = 0;
        var service_charges = $('#service_charges').val() ? parseFloat($('#service_charges').val()) : 0;
        potax2 = $('#potax2').val();
        $.each(tax_rates, function () {
            if (this.id == potax2) {
                if (this.type == 2) {
                    sc_tax = parseFloat(this.rate);
                }
                if (this.type == 1) {
                    sc_tax = parseFloat((((service_charges) * this.rate) / 100), 4);
                }
            }
        });


        $('#price_span').html(formatDecimal((parseFloat(pp)), 2));
        $('#totalprice_span').html(formatDecimal(parseFloat(pp) + parseFloat(service_charges), 2));
        total = parseFloat($('#totalprice_span').html());

        $('#sc_span').html(formatDecimal(service_charges));
        $('#gtotal').html(formatDecimal(parseFloat(total)+parseFloat(pt) + parseFloat(sc_tax)));

        var deposit = parseFloat($('#advance').val());
        $('#deposit_span').html(formatDecimal(deposit));
        $('#balance_span').html(formatDecimal(parseFloat(total) - parseFloat(deposit)));
        return true;
    }


    $('#potax2').on('change', function() {
        calculate_price()
    });
</script>

<?= $this->load->view($theme . 'repair/js');?>

<script type="text/javascript">
     (function(){
        var lock = new PatternLock('#patternHolder',{
            enableSetPattern : true,
            onDraw:function(pattern){
                $('#patternlock').val(pattern);
            }
        });
        function setEditPattern(argument) {
            lock.setPattern(argument);
        }

        jQuery("#submit_prerepairs").on("click", function (e) {
            e.preventDefault();
            $('#preprepair_hide').empty();
            $('#prerepair_form :input').not(':submit').clone().hide().appendTo('#preprepair_hide');
            $('#prerepair').modal('hide');

        });
        jQuery("#exit_prepair").on("click", function (e) {
            e.preventDefault();
            $('#preprepair_hide').empty();
            $('#prerepair_form :input').not(':submit').clone().hide().appendTo('#preprepair_hide');
            $('#prerepair').modal('hide');
        });
   
        items = {};
        window.onbeforeunload = function() {
            $.post( "<?php echo base_url();?>panel/inventory/removeSelected" );
        }
        

    // $( "#add_item" ).autocomplete( "option", "appendTo", ".combo" );
   

    if (localStorage.getItem('slitems')) {
        loadItems();
    }
    if (!($('#service_charges').val())) {
        $('#labor_tr').hide();
    }
    
    var total = null;


    $(document).on('change', '#service_charges, #advance', function () {
        calculate_price();
    });
     $(document).on('keyup', '#service_charges, #advance', function () {
        calculate_price();
    });
        
    $('#repairmodal').on('hidden.bs.modal', function () {
        $.post( "<?php echo base_url();?>panel/inventory/removeSelected" );
        localStorage.clear();
        items = {};
    })
    
    $('select').select2({placeholder: "<?php echo lang('select_placeholder');?>"});


    jQuery('.inp_cat').hide();

    jQuery("#category_select").on("select2:select", function (e) {
        var selected = jQuery("#category_select").val();
        if(selected === 'other') {
            jQuery('.select_cat').hide();
            jQuery('.inp_cat').show();
            jQuery('#category_input').val('');
            jQuery('#category_input').focus();
            <?php if($frm_priv['category']): ?>
                jQuery('#category_input').attr('required', true);
            <?php endif;?>
        }
        else
        {
            jQuery('#category_select').val(selected);
        }
    });
    jQuery(document).on("click", ".prerepair_show", function(event) {
        event.preventDefault();
        $('#preprepair_hide').empty();
        $('#prerepair').modal({
            backdrop: 'static',
            keyboard: false
        }).appendTo('body');
    });

    var hasOwnProperty = Object.prototype.hasOwnProperty;
    function isEmptyObject(obj) {
        if (obj == null) return true;
        if (obj.length > 0)    return false;
        if (obj.length === 0)  return true;
        if (typeof obj !== "object") return true;
        for (var key in obj) {
            if (hasOwnProperty.call(obj, key)) return false;
        }
        return true;
    }
  

    data = <?php echo json_encode($repair); ?>;
    select = document.getElementById('category_select');
    var opt = document.createElement('option');
    opt.value = data.category;
    opt.innerHTML = data.category;
    select.appendChild(opt);
    jQuery('#client_name').val(parseInt(data.client_id)).trigger("change");
    jQuery('#category_select').val((data.category)).trigger("change");
    jQuery('#reparation_model').val(data.model_name);
    jQuery('#defect').val(data.defect);
    jQuery('#advance').val(data.advance);

    $('.edit_c').attr('data-num', data.client_id);
    $('.edit_c').show();


    if (data.deposit_collected == 1) {
        document.getElementById('advance').readOnly = true;
    }else{
        document.getElementById('advance').readOnly = false;
    }
    jQuery('#service_charges').val(data.service_charges);
    jQuery('#potax2').val(parseInt(data.tax_id)).trigger("change");
    jQuery('#manufacturer').val(parseInt(data.manufacturer_id)).trigger("change");
    jQuery('#assigned_to').val(parseInt(data.assigned_to)).trigger("change");
    jQuery('#serial_number').val(data.serial_number);
    jQuery('#comment').val(data.comment);
    if (parseInt(data.sms) === 1) { $('#sms').prop('checked', true); }
    var ci = data.items;
    var edit_item = true;
    if (parseInt(data.status) === 2 || parseInt(data.status) === 0) {
        var edit_item = false;
    }
    if (!isEmptyObject(ci)) {
        items = {};
        localStorage.setItem('slitems', JSON.stringify(ci));
        loadItems(edit_item);
        // $.each(ci, function() { add_product_item(this, edit_item); });
    }else{
        items = {};
        $('#prTable tbody').empty();
        $('#prTable tbody').html('<tr><td colspan="4">nothing to display</td></tr>');
        loadItems();
    }

    var IS_JSON = true;
    try {
        var json = $.parseJSON(data.custom_field);
    } catch(err) {
        IS_JSON = false;
    }
    if(IS_JSON) {
        $.each(json, function(id_field, val_field) {
            jQuery('#custom_'+id_field).val(val_field);
        });
    }
    // Custom Checkboxes
    var IS_JSON = true;
    try {
        var json = $.parseJSON(data.custom_checkboxes);
    } catch(err) {
        IS_JSON = false;
    }
    if(IS_JSON) {
        $.each(json, function(id_field, val_field) {
            if (parseInt(val_field) == 1) {
                $('#checkcustom_'+id_field).iCheck('check');
            }else{
                $('#checkcustom_'+id_field).iCheck('uncheck');
            }
        });
    }
    $('#preprepair_hide').empty();
    $('#prerepair_form')[0].reset();

    // Custom Toggles
    var IS_JSON = true;
    try {
        var json = $.parseJSON(data.custom_toggles);
    } catch(err) {
        IS_JSON = false;
    }
    if(IS_JSON) {
        $.each(json, function(id_field, val_field) {
            if (parseInt(val_field) == 1) {
                document.getElementById('checktoggle_'+id_field).checked = true;
            }else{
                document.getElementById('checktoggle_'+id_field).checked = false;
            }
        });
    }

    jQuery('input[name=cust_pin_code]').val(data.pin_code);
    jQuery('input[name=patternlock]').val(data.pattern);
    setEditPattern(data.pattern);

}());
$('#rpair_form').on( "submit", function(event) {
    event.preventDefault();
    form = $(this);
    var mode = jQuery('#submit').data("mode");
    var id = jQuery('#submit').data("num");
    var code = jQuery('#code').val();
    var status_code = jQuery('#status_edit').val();

   
    //validate
    var valid = form.parsley().validate();
    if (valid) {
        var dataString = $('#rpair_form').serialize() +'&'+ $('#prerepair_form').serialize() + '&code=' + code; 
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/repair/edit/<?php echo $repair['id']; ?>",
            data: dataString,
            cache: false,
            success: function (data) {
                toastr['success']("<?php echo lang('edit'); ?>", "<?php echo lang('repair_title'); ?>: " + name + " " + "<?php echo lang('edited'); ?>");
                setTimeout(function () {
                     window.location.href="<?php echo base_url(); ?>panel/repair/edit/<?php echo $repair['id']; ?>";
                }, 500);
            }
        });
    }
    return false;
});

    $('#client_name').on("select2:select", function(e) {
        item_id = $(this).val();
        $('.edit_c').attr('data-num', item_id);
        $('.edit_c').show();
    });
</script>
