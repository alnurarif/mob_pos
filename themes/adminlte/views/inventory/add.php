<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style type="text/css">
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>


<div class="box box-primary ">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo lang('add');?> <?php echo lang('Repair Parts');?></h3>
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
                        echo form_open_multipart("panel/inventory/add", $attrib)
                    ?>
                    <div class="row">
                    <div class="col-md-6">
                        <div class="form-group all">
                            <?php echo lang("product_name", 'name') ?>
                            <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : ($product ? $product->name : '')), 'class="form-control" id="name" required="required"'); ?>
                        </div>
                        <div class="form-group all">
                            <?php echo lang("product_code", 'code') ?>
                            <div class="input-group">
                                <?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : ($product ? $product->code : '')), 'class="form-control" id="code" required') ?>
                                <span class="input-group-addon pointer" id="random_num" style="padding: 1px 10px;">
                                    <i class="fas fa-random"></i>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo lang('model_manufacturer', 'model_manufacturer');?>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas  fa-folder"></i>
                                </div>
                                <?php 
                                $mm = []; 
                                foreach ($manufacturers as $manufacturer) {
                                    $mm[$manufacturer->id] = $manufacturer->name;
                                }
                                ?>
                                <?php echo form_dropdown('manufacturer', $mm, set_value('manufacturer'), 'id="manufacturer" class="form-control select" style="width: 100%"'.($frm_priv['manufacturer'] ? 'required' : '')); ?>
                                
                                    <a class="add_manufacturer btn input-group-addon"><i class="fas fa-plus"></i></a>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group">
                                <?php echo lang('repair_model', 'model');?>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fas  fa-folder"></i>
                                    </div>
                                    <input <?php echo $frm_priv['model'] ? 'required' : ''; ?> type="text" name="model" id="model" class="form-control " value="<?php echo set_value('model');?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label><?php echo lang('warranty_plans');?></label>
                            <?php $tr = array();
                            foreach ($warranty_plans as $plan) {
                                $tr[$plan['id']] = $plan['name'];
                            }
                            echo form_dropdown('warranty_id', $tr, '', 'class="form-control select" id="warranty_id" style="width:100%;" required');
                            ?>
                           </div>
                        <div class="form-group standard">
                            <?php echo lang("alert_quantity", 'alert_quantity') ?>
                             <?php echo form_input('alert_quantity', (isset($_POST['alert_quantity']) ? $_POST['alert_quantity'] : ($product ?($product->alert_quantity) : '')), 'class="form-control tip" id="alert_quantity"'.($frm_priv['alert_quantity'] ? 'required' : '')) ?>
                        </div>
                         <div class="form-group">
                                <?php echo lang('p_max_discount', 'p_max_discount');?>
                                <div class="input-group">
                                    <input id="max_discount"  name="max_discount" value="<?php echo set_value('max_discount'); ?>" type="text" class="validate form-control">
                                    <div class="input-group-addon">
                                        <?php
                                            $dts = array(
                                                '1' => '%',
                                                '2' => 'Fixed',
                                            ); 
                                        ?>
                                       <?php echo form_dropdown('discount_type', $dts, set_value('discount_type'), 'class="skip" id="discount_type"'); ?>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group standard">
                            <?php echo lang("delivery_note_number", 'delivery_note_number') ?>
                            <?php echo form_input('delivery_note_number', '', 'class="form-control tip" id="delivery_note_number"') ?>
                        </div>

                        <div class="form-group">
                            <label><?php echo lang('Category');?></label>
                            <?php 
                            $tr = array();
                            foreach ($categories as $category) {
                                $tr[$category['id']] = $category['name'];
                            }
                            echo form_dropdown('category_id', $tr, '', 'class="form-control tip" id="category_id" style="width:100%;"'.($frm_priv['category'] ? 'required' : ''));
                            ?>
                        </div>
                        <div class="form-group">
                            <label><?php echo lang('Sub Category');?></label>
                            <?php 
                            $tr = array();
                            foreach ($subcategories as $category) {
                                $tr[$category['id']] = $category['name'];
                            }
                                echo form_dropdown('sub_category', $tr, '', 'class="form-control tip" id="sub_category" style="width:100%;"'.($frm_priv['sub_category'] ? 'required' : ''));
                            ?>
                        </div>
                        <div class="form-group all">
                            <?php echo lang("product_price", 'price') ?>
                            <?php echo form_input('price', (isset($_POST['price']) ? $_POST['price'] : ($product ? ($product->price) : '')), 'class="form-control tip" id="price" required="required"') ?>
                        </div>
                        <div class="form-group all">
                            <div class="checkbox-styled checkbox-inline checkbox-circle">
                                <input type="hidden"  name="taxable" value="0">
                                <input type="checkbox" id="taxable" checked name="taxable" value="1">
                                <label for="taxable"><?php echo lang('is_taxable');?></label>
                            </div>
                        </div>
                        <div class="form-group all">
                            <div class="checkbox-styled checkbox-inline checkbox-circle">
                                <input type="hidden" name="is_serialized" value="0">
                                <input type="checkbox" id="is_serialized" name="is_serialized" value="1">
                                <label for="is_serialized"><?php echo lang('is_stock_serialized');?></label>
                            </div>
                        </div>
                        <div class="form-group all">
                            <div class="checkbox-styled checkbox-inline">
                                <input type="hidden"  name="universal" value="0">
                                <input type="checkbox" id="universal" name="universal" value="1">
                                <label for="universal"><?php echo lang('is_universal'); ?></label>
                            </div>
                        </div>
                        <div class="form-group all">
                            <div class="checkbox-styled checkbox-inline checkbox-circle">
                                <input type="checkbox" name="variants"  id="pv" value="1" <?php echo ($this->input->post('variants')) ? 'checked' : ''; ?>>
                                <label for="pv"><?php echo lang('Product Variants');?> </label>
                            </div>
                        </div>
                        <div id="variants_table" style="display: none;">
                            <table id='mup' class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th><?php echo lang('Name');?></th>
                                        <th><?php echo lang('Price');?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($this->input->post('variants') && $this->input->post('variant_name')){ ?>
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
                                                <input type="number" step="any" min="0"  class="form-control" name="variant_price[]"
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
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control" name="variant_name[]"
                                                               placeholder="<?php echo lang('Name');?>"
                                                               value="<?php echo $name; ?>" required/>
                                                </td>
                                                <td>
                                                    <input type="number" step="any" min="0"  class="form-control" name="variant_price[]"
                                                           placeholder="<?php echo lang('Price');?>"
                                                           value="<?php echo $price; ?>" required>
                                                </td>
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
                                                    <input type="number" step="any" min="0"  class="form-control required" name="variant_price[]" placeholder="<?php echo lang('Price');?>">
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
                    </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group all">
                            <?php echo lang("product_details", 'details') ?>
                            <?php echo form_textarea('details', (isset($_POST['details']) ? $_POST['details'] : ($product ? $product->details : '')), 'class="form-control" id="details"'); ?>
                        </div>
                        <div class="form-group">
                            <?php echo form_submit('add_product', lang("add_product"), 'class="btn btn-primary submit_btn"'); ?>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
    </div>
</div>
            


    