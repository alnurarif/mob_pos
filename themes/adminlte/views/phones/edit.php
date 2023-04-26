    <div class="panel panel-default" id="dripicons-iconz">
        <div class="panel-heading ui-sortable-handle">
            <div class="panel-title"><?php echo lang('Phone');?> </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <?php echo validation_errors(); ?>
            <?php $attribs = array('class'=>'parsley-form'); ?>
            <?php echo form_open('panel/phones/edit/'.$type.'/'.$phone->id ,$attribs); ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <?php echo lang('phone_name', 'phone_name'); ?>
                            <input type="text" class="form-control" value="<?php echo set_value('phone_name', $phone->phone_name); ?>" name="phone_name">
                        </div>
                    </div>
                </div>
                    <?php if($type == 'used'): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="rating">
                                        <?php for ($i=5; $i > 0; $i--): ?>
                                            <input <?php echo $i == $phone->cosmetic_condition ? 'checked' : ''; ?> name="cosmetic_condition" value="<?php echo $i; ?>" id="cosmetic_<?php echo $i; ?>" type="radio" <?php echo $frm_priv['cosmetic_condition'] ? 'required' :''; ?>>
                                            <label for="cosmetic_<?php echo $i; ?>"><i class="fas fa-star"></i>
                                            </label>
                                        <?php endfor; ?>
                                        <?php echo lang('Cosmetic Condition');?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-group">
                                        <div class="rating">
                                            <?php for ($i=5; $i > 0; $i--): ?>
                                                <input <?php echo $i == $phone->operational_condition ? 'checked' : ''; ?> name="operational_condition" value="<?php echo $i; ?>" id="operational_<?php echo $i; ?>" type="radio" <?php echo $frm_priv['opperational_condition'] ? 'required' :''; ?>>
                                                <label for="operational_<?php echo $i; ?>"><i class="fas fa-star"></i>
                                                </label>
                                            <?php endfor; ?>
                                            <?php echo lang('Operational Condition');?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                        <?php echo lang('p_manufacturer', 'manufacturer');?>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fas  fa-folder"></i>
                                            </div>
                                            <?php
                                                $models_dd = array(); 
                                                foreach ($manufacturers as $manufacturer) {
                                                    $models_dd[$manufacturer->id] = $manufacturer->name;
                                                }
                                            ?>
                                            <?php echo form_dropdown('manufacturer', $models_dd, set_value('manufacturer', $phone->manufacturer_id), 'class="form-control" id="manufacturer" style="width:100%;"'.($frm_priv['manufacturer'] ? 'required' : '')); ?>
                                            <a class="add_manufacturer btn input-group-addon"><i class="fas fa-plus"></i></a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <?php echo lang('p_model', 'p_model'); ?>
                                    <input <?php echo $frm_priv['model'] ? 'required' :''; ?> type="text" class="form-control" value="<?php echo set_value('model', $phone->model_name); ?>" name="model">
                                </div>
                            </div>
                            <div class="col-md-12 input-field">
                                <div class="form-group">
                                    <?php echo lang('warranty_plans', 'warranty_plans'); ?>
                                    <?php $tr = array();
                                    foreach ($warranty_plans as $plan) {
                                        $tr[$plan['id']] = $plan['name'];
                                    }
                                    echo form_dropdown('warranty_id', $tr, $phone->warranty_id, 'class="form-control tip" id="warranty_id" style="width:100%;" required');
                                    ?>
                               </div>
                            </div>
                            <?php if($type == 'new'): ?>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="price"><?php echo lang('Outright Price');?></label>
                                        <input type="text" required class="form-control" value="<?php echo set_value('price', $phone->price); ?>" name="price">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="price"><?php echo lang('Activation Price');?></label>
                                        <input type="text" required class="form-control" value="<?php echo set_value('activation_price', $phone->activation_price); ?>" name="activation_price">
                                    </div>
                                </div>
                                <div class="col-lg-12 input-field" >
                                    <div class="form-group">
                                        <label for="price"><?php echo lang('alert_quantity');?></label>
                                        <input value="<?php echo $phone->alert_quantity; ?>" id="alert_quantity" min="0" name="alert_quantity" type="number" step="any" class="validate form-control" required>
                                    </div>
                                </div>
                                
                            <?php endif; ?>
                             <?php if($type == 'used'): ?>
                                <?php
                                    $used_status = array(
                                        '1' => lang('Ready to Sale'),
                                        '2' => lang('Needs Repair'),
                                        '3' => lang('On Hold'),
                                        '4' => lang('Sold'),
                                        '5' => lang('Lost/Damaged'),
                                    ); 
                                ?>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label><?php echo lang('Select Phone Status');?></label>
                                        <?php echo form_dropdown('phone_status', $used_status, set_value('phone_status', $phone->used_status), 'class="form-control" id="phone_status"'.($frm_priv['status'] ? 'required' : '')); ?>
                                    </div>
                                </div>
                                <?php
                                    $unlock_status = array(
                                        '0' => lang('no'),
                                        '1' => lang('yes'),
                                    ); 
                                ?>
                                <div class="col-lg-12 col-sm-12">
                                    <div class="form-group">
                                        <label><?php echo lang('Unlocked');?></label>
                                        <?php echo form_dropdown('unlock_status', $unlock_status, set_value('unlock_status', $phone->unlocked), 'class="form-control" id="unlock_status"'.($frm_priv['unlocked'] ? 'required' : '')); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                       
                            <div class="col-md-12 input-field">
                                <div class="form-group">
                                    <label><?php echo lang('Outright Price - Max Discount');?></label>
                                    <div class="input-group">
                                        <input id="max_discount" <?php echo $frm_priv['max_discount'] ? 'required' :''; ?> name="max_discount" value="<?php echo set_value('max_discount', $phone->max_discount); ?>" type="text" class="validate form-control">
                                        <div class="input-group-addon">
                                            <?php
                                                $dts = array(
                                                    '1' => lang('%'),
                                                    '2' => lang('Fixed'),
                                                ); 
                                            ?>
                                            <?php echo form_dropdown('discount_type', $dts, set_value('discount_type', $phone->discount_type), 'class="skip" id="discount_type"'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 input-field">
                                <div class="form-group">
                                    <label><?php echo lang('Activation Price - Max Discount');?></label>
                                    <div class="input-group">
                                        <input id="max_discount2" name="max_discount2" value="<?php echo set_value('max_discount2', $phone->max_discount2); ?>" type="text" class="validate form-control">
                                        <div class="input-group-addon">
                                            <?php
                                                $dts = array(
                                                    '1' => lang('%'),
                                                    '2' => lang('Fixed'),
                                                ); 
                                            ?>
                                            <?php echo form_dropdown('discount_type2', $dts, set_value('discount_type2', $phone->discount_type2), 'class="skip" id="discount_type2"'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                           
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <?php echo lang('p_description', 'p_description'); ?>
                                    <textarea <?php echo $frm_priv['description'] ? 'required' :''; ?> class="form-control" id="p_description" name="description" rows="6"><?php echo set_value('description', $phone->description); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                            <div class="col-md-12 input-field">
                                <div class="form-group">
                                    <label><?php echo lang('Special Activation Plan');?></label>
                                    <?php 
                                    $data = array(''=>lang('No Plan')); 
                                    foreach ($saps as $sap) {
                                        $data[$sap['id']] = $sap['name'];
                                    }
                                    echo form_dropdown('activation_plan', $data, $phone->s_activation_plan, 'class="form-control" id="phone_sap"'); ?>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <div class="form-group">
                                        <?php echo lang('p_carrier', 'carrier');?>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fas  fa-folder"></i>
                                            </div>
                                            <?php
                                                $carriers_dd = array(); 
                                                foreach ($carriers as $carrier) {
                                                    $carriers_dd[$carrier->id] = $carrier->name;
                                                }
                                            ?>
                                            <?php echo form_dropdown('carrier', $carriers_dd, set_value('carrier', $phone->carrier_id), 'class="form-control" id="carrier" style="width:100%;"'.($frm_priv['carrier'] ? 'required' : '')); ?>
                                            <a class="add_carrier btn input-group-addon"><i class="fas fa-plus"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <?php echo lang('category', 'category');?>
                                    
                                    <?php 
                                    $tr = array();
                                    foreach ($categories as $category) {
                                        $tr[$category['id']] = $category['name'];
                                    }
                                    echo form_dropdown('category_id', $tr, $phone->category, 'class="form-control tip" id="category_id" style="width:100%;"'.($frm_priv['category'] ? 'required' : ''));
                                    ?>
                               </div>
                            </div>
                           
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <?php echo lang('subcategory', 'subcategory');?>
                                    <?php 
                                    $tr = array();
                                    foreach ($subcategories as $category) {
                                        $tr[$category['id']] = $category['name'];
                                    }
                                        echo form_dropdown('sub_category', $tr, $phone->sub_category, 'class="form-control tip" id="sub_category" style="width:100%;"'.($frm_priv['sub_category'] ? 'required' : ''));
                                    ?>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group all">
                                    <div class="checkbox-styled checkbox-inline checkbox-circle">
                                        <input type="hidden"  name="taxable" value="0">
                                        <input type="checkbox" id="taxable" name="taxable" value="1" <?php echo ($phone->taxable == 1) ? 'checked' : ''?>>
                                        <?php echo lang('is_taxable', 'taxable');?>
                                    </div>
                                </div>
                            </div>
                          
                    <?php if($type == 'used'): ?>
                    
                            <table id='mup' class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th><?php echo lang('Serial/IMEI Number(s)');?></th>
                                    <th><?php echo lang('Price Cost');?></th>
                                    <th><?php echo lang('Outright Price');?></th>
                                    <th colspan="2"><?php echo lang('Activation Price');?></th>

                                </tr>
                                </thead>
                                <tbody>
                                <?php if ($this->input->post('imei')): ?>
                                    <?php 
                                    $i = sizeof($_POST['imei']);
                                    for ($r = 0; $r < $i; $r++) {
                                        $imei = escapeStr($_POST['imei'][$r]);
                                        $cost = escapeStr($_POST['purchase_price'][$r]);
                                        $price = escapeStr($_POST['list_price'][$r]);
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="imei[]"
                                                       placeholder="<?php echo lang('Serial/IMEI number');?>"
                                                       value="<?php echo $imei; ?>" required/>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                   name="purchase_price[]" placeholder="<?php echo lang('Price Cost');?>"
                                                   value="<?php echo $cost; ?>" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="list_price[]"
                                                   placeholder="<?php echo lang('List Price');?>"
                                                   value="<?php echo $price; ?>" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="activation_price" placeholder="<?php echo lang('Activation Price');?>" value="<?php echo set_value('activation_price'); ?>" required>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                <?php else: ?>
                                     <?php 
                                       foreach ($phone_items as $item):
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="imei[]"
                                                           placeholder="<?php echo lang('Serial/IMEI number');?>"
                                                           value="<?php echo escapeStr($item->imei); ?>" required/>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <input type="text" class="form-control"
                                                       name="purchase_price[]" placeholder="<?php echo lang('Price Cost');?>"
                                                       value="<?php echo escapeStr($item->cost); ?>" required>
                                                   </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="list_price[]"
                                                       placeholder="<?php echo lang('List Price');?>"
                                                       value="<?php echo escapeStr($item->price); ?>" required>
                                                   </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="activation_price" placeholder="<?php echo lang('Activation Price');?>" value="<?php echo $phone->activation_price; ?>" required>
                                                </div>
                                            </td>
                                            <td></td>
                                        </tr>
                                    <?php
                                        endforeach;
                                    ?>
                                <?php endif; ?>
                                
                                </tbody>
                            </table>
                        <?php endif; ?>
                        </div>
                    </div>
                <div class="col-md-12 row">
                    <?php echo form_submit('submit',lang('submit'), 'class="btn btn-primary"'); ?>
                </div>
            </div>
            
            <?php echo form_close(); ?>
         </div>
    </div>

   


<script type="text/javascript">
    $("#addupimei").on("click", function () {
            var url = "<?php echo base_url("panel/phones/addmore");?>";
            $.get(url, {}, function (data) {
                $("#mup tbody").append(data);
            });
        });
        $(document).on('click', '.imei_delete_item', function (e) {
            var id = $(this).data("id");
            $("#" + id).remove();
        });
    $('select').not('.skip').select2();
    jQuery('.inp_cat').hide();
    jQuery("#p_carrier").on("select2:select", function (e) {
        var selected = jQuery("#p_carrier").val();
        if(selected === 'other') {
            jQuery('.select_cat').hide();
            jQuery('.inp_cat').show();
            jQuery('#carrier_input').val('');
            jQuery('#carrier_input').focus();
        }
        else
        {
            jQuery('#p_carrier').val(selected);
        }
    });

    jQuery(document).ready( function($) {
        $( "#category_id" ).select2('destroy'); 
        $( "#sub_category" ).select2('destroy');
        $('#category_id').on('change', function (e) {
            $('#sub_category').val('').trigger('change');
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
        $( "#category_id" ).select2({
            placeholder: "<?php echo lang('select_placeholder');?>",
        });
        $( "#sub_category" ).select2({  
            placeholder: "<?php echo lang('select_placeholder');?>",
              
            ajax: {
                placeholder: "<?php echo lang('Select a Category');?>",
                url: "<?php echo base_url(); ?>panel/settings/getCategoriesAjax/0",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        category_id: $('#category_id').val(),
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