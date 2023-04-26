<!-- ============= MODAL MODIFICA CLIENTI ============= -->
<div class="modal fade" id="repairmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="titclienti"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <p class="tips custip"></p>
                    <div class="row">
                        <form id="repair_form" class="parsley-form" method="post">
                            <div class="row">
                                <div class="col-md-12 col-lg-6 input-field">
                                    <div class="form-group all">
                                        <?php echo lang("product_name", 'name') ?>
                                        <?php echo form_input('name', '', 'class="form-control" id="name" required="required"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6 input-field">
                                    <div class="form-group">
                                        <?php echo lang("product_code", 'code') ?>
                                        <div class="input-group">
                                            <?php echo form_input('code', '', 'class="form-control" id="code"  required="required"') ?>
                                            <span class="input-group-addon pointer" id="random_num" style="padding: 1px 10px;">
                                                <i class="fas fa-random"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-6 input-field">
                                   <div class="form-group">
                                        <?php echo lang('model_manufacturer', 'model_manufacturer');?>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fas  fa-folder"></i>
                                            </div>
                                             <select <?php echo $mand_inventory['manufacturer'] ? 'required' : ''; ?> id="manufacturer" name="manufacturer" data-num="1" class="form-control m-bot15" style="width: 100%">
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
                                <div class="col-md-12 col-lg-6 input-field">

                                    <div class="form-group">
                                        <?php echo lang('repair_model', 'model');?>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fas  fa-folder"></i>
                                            </div>
                                            <input <?php echo $mand_inventory['model'] ? 'required' : ''; ?> type="text" name="model" id="model" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                
                                <div class="col-md-12 col-lg-6 input-field">
                                    <div class="form-group all">
                                        <?php echo lang("product_price", 'price') ?>
                                        <?php echo form_input('price', '', 'class="form-control tip" id="price" required="required"') ?>
                                    </div>
                                </div>
                            
                                <div class="col-md-12 col-lg-6 input-field">
                                    <div class="form-group standard">
                                        <?php echo lang("alert_quantity", 'alert_quantity') ?>
                                         <?php echo form_input('alert_quantity', '', 'class="form-control tip" id="alert_quantity"'.($mand_inventory['alert_quantity'] ? 'required':'')) ?>
                                    </div>                                
                                </div>

                                <div class="col-md-12 col-lg-6 input-field">
                                    <div class="form-group">
                                        <?php echo lang('p_max_discount', 'p_max_discount');?>
                                        <div class="input-group">
                                            <input id="inv_max_discount" name="max_discount" value="<?php echo set_value('max_discount'); ?>" type="text" class="validate form-control">
                                            <div class="input-group-addon">
                                                <?php
                                                    $dts = array(
                                                        '1' => '%',
                                                        '2' => 'Fixed',
                                                    ); 
                                                ?>
                                                <?php echo form_dropdown('discount_type', $dts, set_value('discount_type'), 'class="skip" id="inv_discount_type"'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-md-12 col-lg-6 input-field">
                                    <div class="form-group">
                                        <?php echo lang("warranty_plans", 'warranty_plans') ?>
                                        <?php $tr = array();
                                        foreach ($warranty_plans as $plan) {
                                            $tr[$plan['id']] = $plan['name'];
                                        }
                                        echo form_dropdown('warranty_id', $tr, '', 'class="form-control tip" id="r_warranty_id" style="width:100%;" required');
                                        ?>
                                   </div>
                                </div>                               
                            </div>
                            <div class="row">
                                
                                <div class="col-md-12 col-lg-6 input-field">
                                    <div class="form-group">
                                        <?php echo lang("category", 'category') ?>
                                        
                                        <?php 
                                        $tr = array();
                                        foreach ($categories as $category) {
                                            $tr[$category['id']] = $category['name'];
                                        }
                                        echo form_dropdown('category_id', $tr, '', 'class="form-control tip" id="r_category_id" style="width:100%;"'.($mand_inventory['sub_category'] ? 'required':''));
                                        ?>
                                   </div>
                                </div>
                                <div class="col-md-12 col-lg-6 input-field">
                                    <div class="form-group">
                                        <?php echo lang("subcategory", 'subcategory') ?>
                                        <?php 
                                        $tr = array();
                                        foreach ($subcategories as $category) {
                                            $tr[$category['id']] = $category['name'];
                                        }
                                            echo form_dropdown('sub_category', $tr, '', 'class="form-control tip" id="r_sub_category" style="width:100%;"'.($mand_inventory['category'] ? 'required':''));
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-4 input-field">
                                    <div class="form-group all">
                                        <div class="checkbox-styled checkbox-inline checkbox-circle">
                                            <input type="hidden" name="is_serialized" value="0">
                                            <input id="is_serialized"  type="checkbox" name="is_serialized" value="1">

                                            <label for="is_serialized"><?php echo lang("is_stock_serialized");?></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-4 input-field">
                                    <div class="form-group all">
                                        <div class="checkbox-styled checkbox-inline">
                                            <input type="hidden"  name="universal" value="0">
                                            <input type="checkbox" id="universal_repair" name="universal" value="1">
                                            <label for="universal_repair"><?php echo lang('is_universal'); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group all">
                                        <?php echo lang("product_details", 'details') ?>
                                        <?php echo form_textarea('details', '', 'class="form-control" id="details"'); ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                </div>
            </div>
            <div class="modal-footer" id="repair_footerClient1">
                  <!--    -->
            </div>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">

    $('#random_num').on( "click", function(){
        $(this).parent('.input-group').children('input').val(generateCardNo(8));
    });
   
    $(document).ready(function () {
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
        $('#keep_stock').on("change", function(){
          if($(this).is(':checked')){
            $('.cost_div').slideUp();
            $('#cost').prop('disabled', true);
            $('#cost').prop('required', false);
            $('.serial_div').slideDown();
            document.getElementById("is_serialized").checked = false;
          } else {
            $('.cost_div').slideDown();
            $('#cost').prop('disabled', false);
            $('#cost').prop('required', true);
            $('.serial_div').slideUp();
            document.getElementById("is_serialized").checked = false;
          }
        });
    });
    jQuery(document).ready( function($) {
    $('#r_category_id').on('change', function (e) {
        $('#r_sub_category').val('').trigger('change');
    });
    $( "#r_category_id" ).select2();
    $( "#r_sub_category" ).select2({        
        ajax: {
            placeholder: 'Select a Category',
            url: "<?php echo base_url(); ?>panel/settings/getCategoriesAjax/0",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    category_id: $('#r_category_id').val(),
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

<!-- ============= MODAL MODIFICA CLIENTI ============= -->
<div class="modal fade" id="othermodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="titclienti"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <p class="tips custip"></p>
                    <div class="row">
                        <form class="parsley-form" id="other_form" method="post">
                            <div class="row">
                                <div class="col-md-12 col-lg-6 input-field">
                                    <div class="form-group">
                                        <label><?php echo lang('Name');?></label>

                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fas fas fa-file-signature"></i>
                                            </div>
                                            <input id="o_name" name="o_name" type="text" class="validate form-control" required>
                                        </div>
                                       
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6 input-field">
                                    <div class="form-group">
                                        <label><?php echo lang('UPC Code');?></label>
                                        <div class="input-group">
                                            
                                            <input id="o_upc_code" name="o_upc_code" type="text" class="validate form-control">
                                            <span class="input-group-addon pointer random_num" style="padding: 1px 10px;">
                                                <i class="fas fa-random"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-6 input-field">
                                    <div class="form-group">
                                        <?php echo lang('p_max_discount', 'p_max_discount');?>
                                        <div class="input-group">
                                            <input id="o_max_discount" max="100" value="0" step="any" name="o_max_discount" required type="number" class="validate form-control">
                                            <div class="input-group-addon">
                                                <?php
                                                    $dts = array(
                                                        '1' => lang('%'),
                                                        '2' => lang('Fixed'),
                                                    ); 
                                                ?>
                                                <?php echo form_dropdown('o_discount_type', $dts, set_value('o_discount_type'), 'class="skip" id="discount_type"'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6 input-field" >
                                    <div class="form-group">
                                    <?php echo lang('alert_quantity', 'alert_quantity');?>
                                        <input id="o_alert_quantity" min="0" name="alert_quantity" type="number" step="any" class="validate form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-6 input-field">
                                    <div class="form-group">
                                <?php echo lang('category', 'category');?>
                                        
                                        <?php 
                                        $tr = array();
                                        foreach ($categories as $category) {
                                            $tr[$category['id']] = $category['name'];
                                        }
                                        echo form_dropdown('category_id', $tr, '', 'class="form-control tip" id="o_category_id" style="width:100%;"');
                                        ?>
                                   </div>
                                </div>
                               
                                <div class="col-md-12 col-lg-6 input-field">
                                    <div class="form-group">
                                <?php echo lang('subcategory', 'subcategory');?>
                                        <?php 
                                        $tr = array();
                                        foreach ($subcategories as $category) {
                                            $tr[$category['id']] = $category['name'];
                                        }
                                            echo form_dropdown('sub_category', $tr, '', 'class="form-control tip" id="o_sub_category" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-lg-12 input-field" >
                                    <div class="form-group">
                                <?php echo lang('warranty_plans', 'warranty_plans');?>
                                        <?php $tr = array();
                                        foreach ($warranty_plans as $plan) {
                                            $tr[$plan['id']] = $plan['name'];
                                        }
                                        echo form_dropdown('warranty_id', $tr, '', 'class="form-control tip" id="o_warranty_id" style="width:100%;" required');
                                        ?>
                                   </div>
                               </div>
                           </div>
                            <div class="row">

                                <div class="col-md-12 col-lg-6 input-field" >
                                    <div class="form-group" id="o_price_div">
                                <?php echo lang('price', 'price');?>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fas  fa-pound-sign"></i>
                                            </div>
                                            <input name="o_price" type="hidden" value="0">
                                            <input id="o_price" name="o_price" type="number" step="any" class="validate form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6 input-field">
                                    
                                    <div class="cost_div">
                                        <div class="form-group">
                                            <?php echo lang('cost', 'cost');?>
                                            <input id="cost" class="form-control" type="text" name="cost" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-4">

                                    <div class="form-group">
                                        <div class="checkbox-styled checkbox-inline checkbox-circle">
                                            <input type="hidden"  name="keep_stock" value="0">
                                            <input id="keep_stock" type="checkbox" name="keep_stock" value="1">
                                            <label for="keep_stock"><?php echo lang('keep_stock');?></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-4 input-field serial_div" style="display: none;">
                                    <div class="form-group all">
                                        <div class="checkbox-styled checkbox-inline checkbox-circle">
                                            <input type="hidden" name="is_serialized" value="0">
                                            <input id="is_serializeds"  type="checkbox" name="is_serialized" value="1">
                                            <label for="is_serializeds"><?php echo lang('is_stock_serialized'); ?></label>
                                        </div>  
                                    </div>
                                </div>

                                <?php if(!$settings->universal_others): ?>
                                <div class="col-md-12 col-lg-4 input-field">
                                    <div class="form-group all">
                                        <div class="checkbox-styled checkbox-inline">
                                            <input type="hidden"  name="universal" value="0">
                                            <input type="checkbox" id="universal_oother" name="universal" value="1">
                                            <label for="universal_oother"><?php echo lang('is_universal'); ?></label>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="col-md-12 col-lg-4 input-field">
                                    <div class="form-group">
                                        <div class="checkbox-styled checkbox-inline checkbox-circle">
                                            <input type="hidden"  name="variable_price" value="0">
                                            <input id="variable_price" type="checkbox" name="variable_price" value="1">
                                            <label for="variable_price"><?php echo lang('variable_price');?></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-4 input-field">
                                    <div class="form-group all">
                                        <div class="checkbox-styled checkbox-inline checkbox-circle">
                                            <input type="hidden"  name="cash_out" value="0">
                                            <input type="checkbox" id="cash_out" name="cash_out" value="1">
                                            <label for="cash_out"><?php echo lang('cash_out');?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col-lg-12">
                                    <div class="form-group">
                                        <label><?php echo lang('notes'); ?></label>
                                        <textarea class="form-control" name="note" id="note" rows="6"></textarea>
                                    </div>
                                </div>
                            </div>
                           
                        </form>
                </div>
            </div>
            <div class="modal-footer" id="footerClient1">
                  <!--    -->
            </div>
        </div>
    </div>
</div>
</div>

<!-- ============= MODAL MODIFICA CLIENTI ============= -->
<div class="modal fade" id="accessorymodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="acctitclienti"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <p class="tips custip"></p>
                    <div class="row">
                        <form class="parsley-form" id="accessories_form" method="post">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-12 col-lg-4 input-field">
                                        <div class="form-group">
                                            <label><?php echo lang('name');?></label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fas fas fa-file-signature"></i>
                                                </div>
                                                <input id="a_name" name="a_name" type="text" class="validate form-control" required>
                                            </div>
                                           
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-4 input-field">
                                        <div class="form-group">
                                            <label><?php echo lang('upc_code');?></label>
                                            <div class="input-group">
                                                <input <?php echo $mand_acc['code'] ? 'required' : ''; ?> id="a_upc_code" name="a_upc_code" type="text" class="validate form-control">
                                                <span class="input-group-addon pointer a_random_num" style="padding: 1px 10px;">
                                                    <i class="fas fa-random"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                  
                                    <div class="col-md-12 col-lg-4 input-field">
                                        <div class="form-group">
                                            <label><?php echo lang('price');?></label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fas  fa-pound-sign"></i>
                                                </div>
                                                <input id="a_price" name="a_price" type="number" step="any" required class="validate form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-12 col-lg-4 input-field" >
                                        <div class="form-group">
                                            <label><?php echo lang('alert_quantity');?></label>
                                            <input id="a_alert_quantity" min="0" name="alert_quantity" type="number" step="any" class="validate form-control" required>
                                        </div>
                                    </div>
                                   
                                    <div class="col-md-12 col-lg-4 input-field">
                                        <div class="form-group">
                                            <label><?php echo lang('category');?></label>
                                            
                                            <?php 
                                            $tr = array();
                                            foreach ($categories as $category) {
                                                $tr[$category['id']] = $category['name'];
                                            }
                                            echo form_dropdown('category_id', $tr, '', 'class="form-control tip" id="a_category_id" style="width:100%;"'.($mand_acc['category'] ? 'required':''));
                                            ?>
                                       </div>
                                    </div>
                                   
                                    <div class="col-md-12 col-lg-4 input-field">
                                        <div class="form-group">
                                            <label><?php echo lang('subcategory');?></label>
                                            <?php 
                                            $tr = array();
                                            foreach ($subcategories as $category) {
                                                $tr[$category['id']] = $category['name'];
                                            }
                                                echo form_dropdown('sub_category', $tr, '', 'class="form-control tip" id="a_sub_category" style="width:100%;"'.($mand_acc['sub_category'] ? 'required':''));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                           
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-12 col-lg-4 input-field">
                                        <div class="form-group">
                                            <?php echo lang('p_max_discount', 'p_max_discount');?>
                                            <div class="input-group">
                                                <input <?php echo $mand_acc['max_discount'] ? 'required' : ''; ?> id="a_max_discount" name="a_max_discount" value="0" type="text" required class="validate form-control">
                                                <div class="input-group-addon">
                                                    <?php
                                                        $dts = array(
                                                            '1' => '%',
                                                            '2' => 'Fixed',
                                                        ); 
                                                    ?>
                                                    <?php echo form_dropdown('a_discount_type', $dts, '', 'class="skip" id="discount_type"'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                   <div class="col-md-12 col-lg-4 input-field">
                                        <div class="form-group">
                                            <label><?php echo lang('warranty_plans');?></label>
                                            <?php $tr = array();
                                            foreach ($warranty_plans as $plan) {
                                                $tr[$plan['id']] = $plan['name'];
                                            }
                                            echo form_dropdown('warranty_id', $tr, '', 'class="form-control tip" id="warranty_id" style="width:100%;" required');
                                            ?>
                                       </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-12 col-lg-4 input-field">
                                        <div class="form-group all">
                                            <div class="checkbox-styled checkbox-inline checkbox-circle">
                                                <input type="hidden" name="is_serialized" value="0">
                                                <input id="is_serializeda"  type="checkbox" name="is_serialized" value="1">
                                                <label for="is_serializeda"><?php echo lang('is_stock_serialized');?></label>

                                            </div>
                                        </div>
                                    </div>
                                    <?php if(!$settings->universal_accessories): ?>
                                    <div class="col-md-12 col-lg-4 input-field">
                                        <div class="form-group all">
                                            <div class="checkbox-styled checkbox-inline">
                                                <input type="hidden"  name="universal" value="0">
                                                <input type="checkbox" id="universal_acc" name="universal" value="1">
                                                <label for="universal_acc"><?php echo lang('is_universal'); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col-lg-12">
                                    <div class="form-group">
                                        <label>Notes</label>
                                        <textarea <?php echo $mand_acc['notes'] ? 'required' : ''; ?> class="form-control" name="note" id="note" rows="6"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                </div>
            </div>
            <div class="modal-footer" id="accfooterClient1">
                  <!--    -->
            </div>
        </div>
    </div>
</div>
</div>
  <script type="text/javascript">
      jQuery(".add_accessory").on("click", function (e) {
        $('#accessorymodal').modal('show');
        

        jQuery('#a_name').val('');
        jQuery('#a_upc_code').val('');
        jQuery('#a_price').val('');
        jQuery('#a_max_discount').val('');
        jQuery('#a_d_s_l').val('');
        jQuery('#re_at').val('');
        jQuery('#note').val('');
        if (document.getElementById('universal_acc')) {
            document.getElementById("universal_acc").checked = false;
        }
        jQuery('#acctitclienti').html("<?php echo lang('add'); ?> <?php echo lang('Accessory'); ?>");

        jQuery('#accfooterClient1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button type="submit" class="btn btn-success" id="submit" data-mode="add" form="accessories_form" value="Submit"><?php echo lang('Submit'); ?></button>');
    });
      
        // process the form
        $('#accessories_form').on( "submit", function(event) {
            event.preventDefault();
            form = $(this);
            var valid = form.parsley().validate();
            if (!valid) {
                return false;
            }
            var mode = jQuery('#submit').data("mode");
            var id = jQuery('#submit').data("num");
            console.log(mode);
            console.log(id);
            //validate
            var valid = true;
            if (valid) {
                var url = "";
                var dataString = "";

                url = base_url + "panel/accessory/addByAjax";
                dataString = $('#accessories_form').serialize();
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        if(data.msg == 'error'){
                            bootbox.alert(data.message);
                        }else{
                            if (data.msg == 'success') {
                                row = add_purchase_item(data.result);
                                if (row) {
                                    $('#accessorymodal').modal('hide');
                                } else {
                                    $('#mError').text(msg);
                                    $('#mError-con').show();
                                }
                            } else {
                                msg = data.msg;
                            }
                        }
                    }
                });
                
            }

            
            return false;
        });
        
    jQuery(".add_repair").on("click", function (e) {
        $('#repairmodal').modal('show');
        $('#repair_form').find("input[type=text], textarea").val("");
        
        jQuery('#titclienti').html("<?php echo lang('add'); ?> <?php echo lang('Inventory'); ?>");
        jQuery('#repair_footerClient1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button type="submit" class="btn btn-success" id="submit" data-mode="add" form="repair_form" value="Submit"><?php echo lang('Submit'); ?></button>');
    });
    // process the form
    $('#repair_form').on( "submit", function(event) {
        event.preventDefault();

        form = $(this);
        var valid = form.parsley().validate();
        if (!valid) {
            return false;
        }
        var mode = jQuery('#submit').data("mode");
        var id = jQuery('#submit').data("num");
    
        //validate
        var url = "";
        var dataString = "";

        url = base_url + "panel/inventory/addByAjax";
        dataString = $('#repair_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: url,
            data: dataString,
            cache: false,
            success: function (data) {
                if(data.msg == 'error'){
                    bootbox.alert(data.message);
                }else{
                    if (data.msg == 'success') {
                        row = add_purchase_item(data.result);
                        if (row) {
                            $('#repairmodal').modal('hide');
                            //audio_success.play();
                        }else {
                            $('#mError').text(msg);
                            $('#mError-con').show();
                        }
                    } else {
                        msg = data.msg;
                    }
                }
            }
        });
        
        return false;
    });
    jQuery(".add_other").on("click", function (e) {
        $('#othermodal').modal('show');
        

        jQuery('#o_name').val('');
        jQuery('#o_upc_code').val('');
        jQuery('#o_cost').val('');
        jQuery('#o_price').val('');
        jQuery('#o_max_discount').val('');
        jQuery('#o_d_s_l').val('');
        jQuery('#re_at').val('');
        jQuery('#note').val('');
        jQuery('#tax_id').val('');
        jQuery('#o_quantity').val('');
        if (document.getElementById('universal_oother')) {
            document.getElementById("universal_oother").checked = false;
        }
        jQuery("#o_quantity").prop('disabled', false);

        jQuery('#titclienti').html("<?php echo lang('add'); ?> <?php echo lang('Other'); ?>");

        jQuery('#footerClient1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button type="submit" class="btn btn-success" id="submit" data-mode="add" form="other_form" value="Submit"><?php echo lang('Submit'); ?></button>');
    }); 
    // process the form
    $('#other_form').on( "submit", function(event) {
        event.preventDefault();
        form = $(this);
        var valid = form.parsley().validate();
        if (!valid) {
            return false;
        }

        var mode = jQuery('#submit').data("mode");
        var id = jQuery('#submit').data("num");
    
        //validate
        var url = "";
        var dataString = "";

        url = base_url + "panel/other/addByAjax";
        dataString = $('#other_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: url,
            data: dataString,
            cache: false,
            success: function (data) {
                if(data.msg == 'error'){
                    bootbox.alert(data.message);
                }else{
                    if (data.msg == 'success') {
                        row = add_purchase_item(data.result);
                        if (row) {
                            $('#othermodal').modal('hide');
                            //audio_success.play();
                        } else {
                            $('#mError').text(msg);
                            $('#mError-con').show();
                        }
                    } else {
                        msg = data.msg;
                    }
                }
            }
        });
        
        return false;
    });
    $(document).ready(function () {
        $('#variable_price').on("change", function(){
          if($(this).is(':checked')){
            $('#o_price_div').slideUp();
            $('#o_price').prop('disabled', true);
          } else {
            $('#o_price_div').slideDown();
            $('#o_price').prop('disabled', false);

          }
        });
    });
    jQuery(document).ready( function($) {
        $('#a_category_id').on('change', function (e) {
            $('#a_sub_category').val('').trigger('change');
        });
        $( "#a_category_id" ).select2();
        $( "#a_sub_category" ).select2({        
            ajax: {
                placeholder: "<?php echo lang('Select a Category');?>",
                url: "<?php echo base_url(); ?>panel/settings/getCategoriesAjax/0",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        category_id: $('#a_category_id').val(),
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
        $('#o_category_id').on('change', function (e) {
            $('#o_sub_category').val('').trigger('change');
        });
        $( "#o_category_id" ).select2();
        $( "#o_sub_category" ).select2({        
            ajax: {
                placeholder: "<?php echo lang('Select a Category');?>",
                url: "<?php echo base_url(); ?>panel/settings/getCategoriesAjax/0",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        category_id: $('#o_category_id').val(),
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
  <script type="text/javascript">
    $('.random_num').on( "click", function(){
        $(this).parent('.input-group').children('input').val(generateCardNo(8));
    });
    $('.a_random_num').on( "click", function(){
        $(this).parent('.input-group').children('input').val(generateCardNo(8));
    });
</script>

