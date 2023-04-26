<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style type="text/css">
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        /* display: none; <- Crashes Chrome on hover */
        -webkit-appearance: none;
        margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
    }
</style>

<div class="box box-primary ">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo lang('edit');?> <?php echo lang('Repair Parts');?></h3>
    </div>
    <div class="box-body">
        <div class="row">
                <div class="col-lg-12">
                <?php if (validation_errors()) { ?>
                    <div class="alert alert-warning">
                        <?php echo trim(validation_errors()); ?>
                    </div>
                <?php } ?>
                    <?php
                    $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'class'=>'parsley-form');
                    echo form_open("panel/inventory/edit/".$product->id, $attrib)
                    ?>
                    <div class="col-md-6">
                        <div class="form-group all">
                            <?php echo lang("product_name", 'name') ?>
                            <?php echo form_input('name', set_value('name', $product->name), 'class="form-control" id="name" required="required"'); ?>
                        </div>
                        <div class="form-group all">
                            <?php echo lang("product_code", 'code') ?>
                            <div class="input-group">
                                <?php echo form_input('code', set_value('code', $product->code), 'class="form-control" id="code"  required="required"') ?>
                                <span class="input-group-addon pointer" id="random_num" style="padding: 1px 10px;">
                                    <i class="fas fa-random"></i>
                                </span>
                            </div>
                        </div>
                            <div class="form-group">
                                <div class="form-group">
                                    <?php echo lang('model_manufacturer', 'model_manufacturer');?>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-folder"></i>
                                        </div>
                                         <select  <?php echo $frm_priv['manufacturer'] ? 'required' : ''; ?> id="manufacturer" name="manufacturer" data-num="1" class="form-control select" style="width: 100%">
                                        <option></option>
                                            <?php 
                                                foreach ($manufacturers as $manufacturer) : ?>
                                                <option value="<?php echo $manufacturer->id ?>" <?php echo ($product->manufacturer_id == $manufacturer->id) ? "selected" : "" ?>><?php echo $manufacturer->name ?></option>
                                                <?php endforeach; 
                                            ?>
                                        </select>
                                            <a class="add_manufacturer btn input-group-addon"><i class="fas fa-plus"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('repair_model', 'model');?>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fas  fa-folder"></i>
                                    </div>
                                    <input <?php echo $frm_priv['model'] ? 'required' : ''; ?> type="text" name="model" value="<?php echo set_value('model', $product->model_name); ?>" id="model" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('warranty_plans', 'warranty_plans');?>
                                <?php $tr = array();
                                foreach ($warranty_plans as $plan) {
                                    $tr[$plan['id']] = $plan['name'];
                                }
                                echo form_dropdown('warranty_id', $tr, $product->warranty_id, 'class="form-control select" id="warranty_id" style="width:100%;" required');
                                ?>
                           </div>
                        <div class="form-group standard">
                            <?php echo lang("alert_quantity", 'alert_quantity') ?>
                            <?php echo form_input('alert_quantity', set_value('alert_quantity', $product->alert_quantity), 'class="form-control tip" id="alert_quantity"'.($frm_priv['alert_quantity'] ? 'required' : '')) ?>
                        </div>
                        <div class="form-group">
                            <?php echo lang('p_max_discount', 'p_max_discount');?>
                            <div class="input-group">
                                <input id="max_discount"  name="max_discount" value="<?php echo set_value('max_discount', $product->max_discount); ?>" type="text" class="validate form-control">
                                <div class="input-group-addon">
                                    <?php
                                        $dts = array(
                                            '1' => lang('%'),
                                            '2' => lang('Fixed'),
                                        ); 
                                    ?>
                                   <?php echo form_dropdown('discount_type', $dts, set_value('discount_type', $product->discount_type), 'class="skip" id="discount_type"'); ?>
                                </div>
                            </div>
                        </div>
                        
                       
                    </div>
                    <div class="col-md-6">
                        
                        <div class="form-group standard">
                            <?php echo lang("delivery_note_number", 'delivery_note_number') ?>
                            <?php echo form_input('delivery_note_number', set_value('delivery_note_number', $product->delivery_note_number), 'class="form-control tip" id="delivery_note_number"') ?>
                        </div>
                    <div class="form-group">
                                            <label><?php echo lang('Category');?></label>
                                            
                                            <?php 
                                            $tr = array();
                                            foreach ($categories as $category) {
                                                $tr[$category['id']] = $category['name'];
                                            }
                                            echo form_dropdown('category_id', $tr, escapeStr($product->category), 'class="form-control tip" id="category_id" style="width:100%;"'.($frm_priv['category'] ? 'required' : ''));
                                            ?>
                                       </div>
                                   
                                        <div class="form-group">
                                            <label><?php echo lang('Sub Category');?></label>
                                            <?php 
                                            $tr = array();
                                            foreach ($subcategories as $category) {
                                                $tr[$category['id']] = $category['name'];
                                            }
                                                echo form_dropdown('sub_category', $tr, escapeStr($product->sub_category), 'class="form-control tip" id="sub_category" style="width:100%;"'.($frm_priv['sub_category'] ? 'required' : ''));
                                            ?>
                                        </div>
                        
                      
                        <div class="form-group all">
                            <?php echo lang("product_price", 'price') ?>
                            <?php echo form_input('price', set_value('price', $product->price),  'class="form-control tip" id="price" required="required"') ?>
                        </div>
                        <div class="form-group all">
                            <div class="checkbox-styled checkbox-inline checkbox-circle">
                                <input type="hidden"  name="taxable" value="0">

                                <input type="checkbox" id="taxable" name="taxable" value="1" <?php echo ($product->taxable == 1) ? 'checked' : ''?>>
                                <label for="taxable"><?php echo lang('is_taxable');?></label>
                            </div>
                        </div>
                        
                     <!--   <div class="form-group all" id="tax_box">
                            <?php echo lang("product_tax", 'tax_rate') ?>
                            <?php
                            $tr = array();
                            foreach ($tax_rates as $tax) {
                                $tr[$tax->id] = $tax->name;
                            }
                            echo form_dropdown('tax_rate[]', $tr, set_value('tax_rate[]', explode(',', $product->tax_rate)), 'class="form-control select skip" id="tax_rate" style="width:100%" multiple')
                            ?>
                        </div> -->
                        <div class="form-group all">
                            <div class="checkbox-styled checkbox-inline checkbox-circle">
                                <input type="hidden" name="is_serialized" value="0">
                                <input type="checkbox" id="is_serialized" name="is_serialized" value="1"  <?php echo ($product->is_serialized) ? 'checked' : '' ?>>
                                <label for="is_serialized"><?php echo lang('is_stock_serialized');?></label>
                            </div>
                        </div>
                          <div class="form-group all">
                                <div class="checkbox-styled checkbox-inline">
                                    <input type="hidden"  name="universal" value="0">
                                    <input type="checkbox" <?php echo $product->universal ? 'checked' :'';?> id="universal" name="universal" value="1">
                                    <label for="universal"><?php echo lang('is_universal'); ?></label>
                                </div>
                            </div>
                        <div class="form-group all">
                            <div class="checkbox-styled checkbox-inline checkbox-circle">
                                <input type="checkbox" name="variants"  id="pv" value="1"  <?php echo ($this->input->post('variant_name') || $variants) ? 'checked' : ''; ?>>
                                <label for="pv"><?php echo lang('Product Variants');?> </label>
                            </div>
                        </div>
                      
                                <div id="variants_table" style="display: none;">
                                    <table id='mup' class="table table-striped table-bordered table-hover">
                                            <thead>
                                            <tr>
                                                <th><?php echo lang('Name');?></th>
                                                <th><?php echo lang('Price');?></th>
                                                <th><?php echo lang('X');?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php if ($this->input->post('variant_name')){ ?>
                                                <?php 
                                                $i = sizeof($_POST['variant_name']);
                                                for ($r = 0; $r < $i; $r++) {
                                                    $name = escapeStr($_POST['variant_name'][$r]);
                                                    $price = escapeStr($_POST['variant_price'][$r]);
                                                ?>
                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control" name="variant_name[]"
                                                                   placeholder="<?php echo lang('Name');?>"
                                                                   value="<?php echo $name; ?>" required/>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="any" min="0" class="form-control" name="variant_price[]"
                                                               placeholder="<?php echo lang('Price');?>"
                                                               value="<?php echo $price; ?>" required>
                                                    </td>
                                                </tr>
                                                <?php
                                                }
                                                ?>
                                            <?php }elseif($variants){ ?>
                                                <?php 
                                                foreach ($variants as $variant) {
                                                    $name = escapeStr($variant->variant_name);
                                                    $price = escapeStr($variant->price);

                                                ?>
                                                    <tr id="variant_<?php echo $variant->id;?>">
                                                        <td>
                                                            <input type="text" class="form-control" name="variant_name[]"
                                                                       placeholder="<?php echo lang('Name');?>"
                                                                       value="<?php echo $name; ?>" required/>
                                                        </td>
                                                        <td>
                                                            <input type="number" step="any" min="0" class="form-control" name="variant_price[]"
                                                                   placeholder="<?php echo lang('Price');?>"
                                                                   value="<?php echo $price; ?>" required>
                                                        </td>
                                                        <td><button type="button" class="btn btn-danger btn-xs delete delete_row" data-id="<?php echo $variant->id;?>"><i class="fas fa-trash"></i></button></td>
                                                    </tr>
                                                <?php
                                                    }
                                                ?>
                                            <?php }else{ ?>
                                                 <tr>
                                                    <td>
                                                        <input type="text" class="form-control required" name="variant_name[]" placeholder="<?php echo lang('Name');?>"/>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="any" min="0" class="form-control required" name="variant_price[]" placeholder="<?php echo lang('Price');?>">
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td colspan="4">
                                                    <a class="btn btn-default btn-xs" href="javascript:void(0);"
                                                       id="add_row">
                                                        <i class="fas fa-plus"></i>
                                                        <?php echo lang('Add More...');?>
                                                    </a>
                                                </td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                </div> 
                    </div>

                    <div class="col-md-12">
                        <div class="form-group all">
                            <?php echo lang("product_details", 'details') ?>
                            <?php echo form_textarea('details', (isset($_POST['details']) ? $_POST['details'] : ($product ? $product->details : '')), 'class="form-control" id="details"'); ?>
                        </div>

                        <div class="form-group">
                            <?php echo form_submit('edit_product', lang("edit_product"), 'class="btn btn-primary"'); ?>
                        </div>

                    </div>
                    <?php echo form_close(); ?>
                </div>
        </div>
    </div>
</div>



<script type="text/javascript">
    $(document).ready(function () {
        var items = {};
   
      
    });

    <?php if ($product) { ?>
    $(document).ready(function () {
        $("#code").parent('.form-group').addClass("has-error");
        $("#code").focus();
    });
    <?php } ?>
        $('#random_num').on( "click", function(){
        $(this).parent('.input-group').children('input').val(generateCardNo(8));
    });
       
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('input[name=variants]').on("change", function(){
          if($(this).is(':checked')){
            $('#variants_table').slideDown();
            $('.required').attr('required', true);
          } else {
            $('#variants_table').slideUp();
            $('.required').attr('required', false);
            $('input[name=variant_name],input[name=variant_price]').prop('disabled', true);
          }
        });
    });
    if($('input[name=variants]').is(':checked')){
        $('#variants_table').slideDown();
        $('.required').attr('required', true);
    } else {
        $('#variants_table').slideUp();
        $('.required').attr('required', false);
        $('input[name=variant_name],input[name=variant_price]').prop('disabled', true);
    }
    
    $("#add_row").on("click", function () {
        var url = "<?php echo base_url("panel/inventory/addmore");?>";
        $.get(url, {}, function (data) {
            $("#mup tbody").append(data);
        });
    });
    $(document).on('click', '.delete_row', function (e) {
        var id = $(this).data("id");
        $("#variant_" + id).remove();
    });
    jQuery(document).ready( function($) {
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
    $('#category_id').on('change', function (e) {
        $('#sub_category').val('').trigger('change');
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


