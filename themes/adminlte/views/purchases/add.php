<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php echo $this->load->view($theme.'purchases/js'); ?>

<script type="text/javascript">
    
    <?php if ($this->session->userdata('remove_pols')) { ?>
    if (localStorage.getItem('poitems')) {
        localStorage.removeItem('poitems');
    }
    if (localStorage.getItem('podiscount')) {
        localStorage.removeItem('podiscount');
    }
    if (localStorage.getItem('potax2')) {
        localStorage.removeItem('potax2');
    }
    if (localStorage.getItem('poshipping')) {
        localStorage.removeItem('poshipping');
    }
    if (localStorage.getItem('poref')) {
        localStorage.removeItem('poref');
    }
    if (localStorage.getItem('powarehouse')) {
        localStorage.removeItem('powarehouse');
    }
    if (localStorage.getItem('ponote')) {
        localStorage.removeItem('ponote');
    }
    if (localStorage.getItem('posupplier')) {
        localStorage.removeItem('posupplier');
    }
    if (localStorage.getItem('pocurrency')) {
        localStorage.removeItem('pocurrency');
    }
    if (localStorage.getItem('poextras')) {
        localStorage.removeItem('poextras');
    }
    if (localStorage.getItem('podate')) {
        localStorage.removeItem('podate');
    }
    if (localStorage.getItem('postatus')) {
        localStorage.removeItem('postatus');
    }
  
    <?php $this->repairer->unset_data('remove_pols');
} ?>
    var count = 1, an = 1, po_edit = false, product_variant = 0, DT = <?php echo $settings->default_tax_rate ?>, DC = '<?php echo escapeStr($settings->currency) ?>', shipping = 0,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>, poitems = {};
        
    $(document).ready(function () {
        <?php if($this->input->get('supplier')) { ?>
        if (!localStorage.getItem('poitems')) {
            localStorage.setItem('posupplier', <?php echo $this->input->get('supplier');?>);
        }
        <?php } ?>

        if (!localStorage.getItem('podate')) {
            $('#podate').val("<?php echo date('m-d-Y H:i:s'); ?>");
        }
        $(document).on('change', '#podate', function (e) {
            localStorage.setItem('podate', $(this).val());
        });
        if (podate = localStorage.getItem('podate')) {
            $('#podate').val(podate);
        }
      
        ItemnTotals();
        $("#add_item").autocomplete({
            source: function (request, response) {
                $.ajax({
                    type: 'get',
                    url: '<?php echo site_url('panel/purchases/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        supplier_id: $("#posupplier").val()
                    },
                    success: function (data) {
                        response(data);
                    }
                });
            },
            minLength: 1,
            autoFocus: false,
            delay: 250,
           
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_purchase_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    bootbox.alert('<?php echo lang("nothing_found"); ?>');
                }
            }
        });
    });
 
</script>

<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo lang('add_purchase');?></h3>
    </div>
    <div class="box-body">
        <div class="row">
                    <div class="col-lg-12">
                        <?php echo validation_errors(); ?>
                        <?php
                        $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id'=>'purchase_form');
                        echo form_open_multipart("panel/purchases/add", $attrib)
                        ?>
                        <div class="row">
                            <div class="col-lg-12">

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?php echo lang('date', 'podate'); ?>
                                            <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : ""), 'class="form-control input-tip datetime" id="podate" required="required"'); ?>
                                        </div>
                                    </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php echo lang('reference_no', 'poref'); ?>
                                        <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $ponumber), 'class="form-control input-tip" id="poref"'); ?>
                                    </div>
                                </div>
                                

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php echo lang('status', 'postatus'); ?>
                                        <?php
                                        $post = array('ordered' => lang('ordered'), 
                                    );
                                        echo form_dropdown('status', $post, (isset($_POST['status']) ? $_POST['status'] : ''), 'id="postatus" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("status") . '" required="required" style="width:100%;" ');
                                        ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php echo lang('document', 'document'); ?>
                                        <input id="document" type="file" data-browse-label="<?php echo lang('browse'); ?>" name="document" data-show-upload="false" data-language="mylang"
                                               data-show-preview="false" class="form-control file">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="panel panel-warning">
                                        <div
                                            class="panel-heading"><?php echo lang('please_select_these_before_adding_product'); ?></div>
                                        <div class="panel-body" style="padding: 5px;">
                                            <div class="col-md-4">
                                                <div class="form-group">

                                                    <?php echo lang('supplier', 'posupplier'); ?>
                                                    <select class="form-control" name="posupplier" id="posupplier">
                                                    </select>

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>


                                <div class="col-md-12" id="sticker">
                                    <div class="well well-sm">
                                        <div class="form-group" style="margin-bottom:0;">
                                            <div class="input-group wide-tip">
                                                <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                                    <i class="fas fa-2x fa-barcode addIcon"></i></a></div>
                                                <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . lang('add_product') . '"'); ?>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div>
                                        <?php if($this->Admin || $GP['inventory-add']): ?>
                                        <button href="#repairmodal" role="button" type="button" class="add_repair btn btn-primary">
                                            <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo lang('Repair Parts');?>
                                        </button>
                                        <?php endif; ?>
                                        <?php if($this->Admin || $GP['other-add']): ?>
                                        <button href="#othermodal" role="button" type="button" class="add_other btn btn-primary">
                                            <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo lang('Other');?>
                                        </button>
                                        <?php endif; ?>
                                        <?php if($this->Admin || $GP['accessory-add']): ?>
                                        <button href="#accessorymodal" role="button" type="button" class="add_accessory btn btn-primary">
                                            <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo lang('Accessory');?>
                                        </button>
                                        <?php endif; ?>
                                        <?php if($this->Admin || $GP['phones-add_new']): ?>
                                        <button id="addManually" class="btn btn-primary">
                                            <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo lang('New Phone');?>
                                        </button>
                                        <?php endif; ?>
                                        <?php if($this->Admin || $GP['phones-add_used']): ?>
                                         <button id="addUsedManually" class="btn btn-primary">
                                            <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo lang('Used Phone');?>
                                        </button>
                                        <?php endif; ?>

                                    </div>
                                    <div class="control-group table-group">
                                        <label class="table-label"><?php echo lang('order_items'); ?></label>

                                        <div class="controls table-controls">
                                            <table id="poTable"
                                                   class="table items table-striped table-bordered table-condensed table-hover">
                                                <thead>
                                                <tr>
                                                    <th class="col-md-4"><?php echo lang('product_name'); ?>(<?php echo lang('product_code'); ?>)</th>
                                                    

                                                    <th class="col-md-1"><?php echo lang('unit_cost'); ?></th>
                                                    <th class="col-md-1"><?php echo lang('quantity'); ?></th>
                                                    <th class="col-md-1"><?php echo lang('discount'); ?></th>
                                                   
                                                    <th><div class="pull-right"><?php echo lang("subtotal"); ?> (<span
                                                            class="currency"><?php echo escapeStr($settings->currency) ?></span>)</div>
                                                    </th>
                                                    <th style="width: 30px !important; text-align: center;"><i
                                                            class="fas fa-trash"
                                                            style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot></tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <input type="hidden" name="total_items" value="" id="total_items" required="required"/>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="checkbox-styled checkbox-inline checkbox-circle">
                                            <input type="checkbox" class="checkbox" id="extras" value=""/>
                                            <label for="extras" class="padding05"><?php echo lang('more_options'); ?></label>
                                        </div>
                                    </div>
                                    <div class="row" id="extras-con" style="display: none;">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <?php echo lang('order_tax', 'potax2'); ?>
                                                <?php echo form_input('order_tax', '', 'class="form-control input-tip" id="potax2"'); ?>
                                            </div>
                                        </div>

                                    

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <?php echo lang('shipping', 'poshipping'); ?>
                                                <?php echo form_input('shipping', '', 'class="form-control input-tip" id="poshipping"'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><?php echo lang('Tracking Number');?></label>
                                                <?php echo form_input('track_code', '', 'class="form-control"'); ?>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label><?php echo lang('Shipping Provider');?></label>
                                                <?php
                                                     $dp_p = $this->repairer->returnShippingMethods();
                                                ?>
                                                <div class="select_provider">
                                                    <?php echo form_dropdown('shipping_provider', $dp_p, '' ,'class="form-control" id="provider_select" style="width: 100%"'); ?>
                                                </div>
                                                <div class="inp_provider">
                                                    <input id="provider_input" name="provider_input" type="text" class="validate form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="form-group">
                                        <?php echo lang('note', 'ponote');?>
                                        <?php echo form_textarea('note', set_value('note'), 'class="form-control" id="ponote" style="margin-top: 10px; height: 100px;"'); ?>
                                    </div>

                                </div>
                                <div class="col-md-12">
                                    <div
                                        class="from-group"><?php echo form_submit('add_pruchase', lang('submit'), 'id="add_pruchase" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                        <button type="button" class="btn btn-danger" id="reset"><?php echo lang('reset'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="bottom-total" class="well well-sm" style="margin-bottom: 0;">
                            <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                                <tr class="warning">
                                    <td><?php echo lang('items') ?> <span class="totals_val pull-right" id="titems">0</span></td>
                                    <td><?php echo lang('total') ?> <span class="totals_val pull-right" id="total">0.00</span></td>
                                    <td><?php echo lang('order_discount') ?> <span class="totals_val pull-right" id="tds">0.00</span></td>
                                    <td><?php echo lang('order_tax') ?> <span class="totals_val pull-right" id="ttax2">0.00</span></td>
                                    <td><?php echo lang('shipping') ?> <span class="totals_val pull-right" id="tship">0.00</span></td>
                                    <td><?php echo lang('grand_total') ?> <span class="totals_val pull-right" id="gtotal">0.00</span></td>
                                </tr>
                            </table>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
    </div>
</div>




<div class="modal" id="phoneModal" tabindex="-1" role="dialog" aria-labelledby="phoneModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fas fa-2x">&times;</i></span><span class="sr-only"><?php echo lang('close');?></span></button>
                <h4 class="modal-title" id="phoneModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <div class="alert alert-danger" id="NewPhoneError-con" style="display: none;">
                    <!--<button data-dismiss="alert" class="close" type="button">×</button>-->
                    <span id="NewPhoneError"></span>
                </div>
                    <form class="form-horizontal parsley-form" id="nphone_form" role="form">
                        <div class="form-group">
                            <div class="col-sm-8">
                                <input type="hidden" id="phone_tax" value="<?php echo $this->mSettings->new_phone_tax; ?>" name="phone_tax" >
                            </div>
                        </div>
                        <div class="form-group">
                            

                            <label for="pquantity" class="col-sm-3 control-label"><?php echo lang('phone_name'); ?></label>
                            <div class="col-sm-8">
                                <input type="text" required="" class="form-control" id="phone_name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pquantity" class="col-sm-3 control-label"><?php echo lang('p_manufacturer');?></label>
                            <div class="col-sm-8">
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
                                    <?php echo form_dropdown('phone_manufacturer', $models_dd, set_value('manufacturer'), 'class="form-control" id="phone_manufacturer"'.($mand_nphone['manufacturer'] ? 'required':'')); ?>
                                    <a class="add_manufacturer btn input-group-addon"><i class="fas fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pquantity" class="col-sm-3 control-label"><?php echo lang('p_model');?></label>
                            <div class="col-sm-8">
                                <input type="text" <?php echo $mand_nphone['model'] ? 'required' : ''; ?> class="form-control" value="<?php echo set_value('phone_model'); ?>" name="phone_model" id="phone_model">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="pwarranty" class="col-sm-3 control-label"><?php echo lang('warranty_plans'); ?></label>
                            <div class="col-sm-8">
                                <?php $tr = array();
                                foreach ($warranty_plans as $plan) {
                                    $tr[$plan['id']] = $plan['name'];
                                }
                                echo form_dropdown('warranty_id', $tr, '', 'class="form-control tip" id="npwarranty_id" style="width:100%;" required');
                                ?>
                            </div>
                       </div>

                        <div class="form-group">
                            <label for="nphone_price" class="col-sm-3 control-label"><?php echo lang('price'); ?></label>
                            <div class="col-sm-8">
                                <input type="text" required class="form-control" value="<?php echo set_value('nphone_price'); ?>" name="nphone_price" id="nphone_price">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nphone_aq" class="col-sm-3 control-label"><?php echo lang('alert_quantity'); ?></label>
                            <div class="col-sm-8">
                                <input min="0" name="nphone_alert_quantity" type="number" step="any" required class="form-control" name="nphone_alert_quantity" id="nphone_alert_quantity">
                            </div>
                        </div>
                       
                        <div class="form-group">
                            <label for="pquantity" class="col-sm-3 control-label"><?php echo lang('p_description');?></label>
                            <div class="col-sm-8">
                                <textarea <?php echo $mand_nphone['description'] ? 'required' : ''; ?> class="form-control" id="phone_description" name="description" rows="6"><?php echo set_value('description'); ?></textarea>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="pquantity" class="col-sm-3 control-label"><?php echo lang('p_max_discount');?></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input <?php echo $mand_nphone['max_discount'] ? 'required' : ''; ?> id="phone_max_discount" name="max_discount" value="<?php echo set_value('max_discount'); ?>" type="text" class="validate form-control" name="phone_max_discount">
                                    <div class="input-group-addon">
                                        <?php
                                            $dts = array(
                                                '1' => '%',
                                                '2' => 'Fixed',
                                            ); 
                                        ?>
                                        <?php echo form_dropdown('discount_type', $dts, set_value('discount_type'), 'class="skip" id="phone_discount_type"'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3"><?php echo lang('category'); ?></label>
                            <div class="col-sm-8">
                                <?php 
                                $tr = array();
                                foreach ($categories as $category) {
                                    $tr[$category['id']] = $category['name'];
                                }
                                echo form_dropdown('category_id', $tr, '', 'class="form-control tip" id="np_category_id" style="width:100%;"'.($mand_nphone['category'] ? 'required':''));
                                ?>
                            </div>
                       </div>
                   
                        <div class="form-group">
                            <label class="control-label col-md-3"><?php echo lang('subcategory'); ?></label>
                            <div class="col-sm-8">
                                <?php 
                                $tr = array();
                                foreach ($subcategories as $category) {
                                    $tr[$category['id']] = $category['name'];
                                }
                                    echo form_dropdown('sub_category', $tr, '', 'class="form-control tip" id="np_sub_category" style="width:100%;"'.($mand_nphone['sub_category'] ? 'required':''));
                                ?>
                            </div>
                        </div>

                       <div class="form-group">
                            <label for="pquantity" class="col-sm-3 control-label"><?php echo lang('p_carrier');?></label>
                            <div class="col-sm-8">
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
                                    <?php echo form_dropdown('carrier', $carriers_dd, set_value('carrier'), 'class="form-control" id="phone_carrier"'.($mand_nphone['carrier'] ? 'required':'')); ?>
                                    <a class="add_carrier btn input-group-addon"><i class="fas fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                        
                        
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="nphone_form" class="btn btn-primary" id="addPhoneItemManually"><?php echo lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="UsedPhoneModal" tabindex="-1" role="dialog" aria-labelledby="phoneModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fas fa-2x">&times;</i></span><span class="sr-only"><?php echo lang('close');?></span></button>
                <h4 class="modal-title" id="phoneModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <div class="alert alert-danger" id="mUsedError-con" style="display: none;">
                    <!--<button data-dismiss="alert" class="close" type="button">×</button>-->
                    <span id="mUsedError"></span>
                </div>
                <form class="form-horizontal parsley-form" id="uphone_form" role="form">
                    <div class="form-group">
                        <div class="col-sm-8">
                            <input type="hidden" name="used_phone_tax" id="used_phone_tax" value="<?php echo $this->mSettings->used_phone_tax; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pquantity" class="col-sm-3 control-label"><?php echo lang('phone_name'); ?></label>
                        <div class="col-sm-8">
                            <input type="text" required="" class="form-control" id="used_phone_name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pquantity" class="col-sm-3 control-label"><?php echo lang('p_manufacturer');?></label>
                        <div class="col-sm-8">
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
                                <?php echo form_dropdown('used_phone_manufacturer', $models_dd, set_value('manufacturer'), 'class="form-control" id="used_phone_manufacturer"'.($mand_uphone['manufacturer'] ? 'required':'')); ?>
                                <a class="add_manufacturer btn input-group-addon"><i class="fas fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pquantity" class="col-sm-3 control-label"><?php echo lang('p_model');?></label>
                        <div class="col-sm-8">
                            <input type="text" <?php echo $mand_uphone['model'] ? 'required' : ''; ?> class="form-control" value="<?php echo set_value('used_phone_model'); ?>" name="used_phone_model" id="used_phone_model">
                        </div>
                    </div>
                   
                    <div class="form-group">
                        <label for="pwarranty" class="col-sm-3 control-label"><?php echo lang('warranty_plans'); ?></label>
                        <div class="col-sm-8">
                            <?php $tr = array();
                            foreach ($warranty_plans as $plan) {
                                $tr[$plan['id']] = $plan['name'];
                            }
                            echo form_dropdown('warranty_id', $tr, '', 'class="form-control tip" id="upwarranty_id" style="width:100%;" required');
                            ?>
                        </div>
                   </div>
                    <div class="form-group">
                        <label for="pquantity" class="col-sm-3 control-label"><?php echo lang('p_description');?></label>
                        <div class="col-sm-8">
                            <textarea class="form-control" <?php echo $mand_uphone['description'] ? 'required' : ''; ?> id="used_phone_description" name="description" rows="6"><?php echo set_value('description'); ?></textarea>
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="pquantity" class="col-sm-3 control-label"><?php echo lang('p_max_discount');?></label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input id="used_phone_max_discount" name="max_discount" value="<?php echo set_value('max_discount'); ?>" type="text" class="validate form-control" <?php echo $mand_uphone['max_discount'] ? 'required' : ''; ?> name="used_phone_max_discount">
                                <div class="input-group-addon">
                                    <?php
                                        $dts = array(
                                            '1' => '%',
                                            '2' => 'Fixed',
                                        ); 
                                    ?>
                                    <?php echo form_dropdown('discount_type', $dts, set_value('discount_type'), 'class="skip" id="used_phone_discount_type"'); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('category'); ?></label>
                        <div class="col-sm-8">
                            <?php 
                            $tr = array();
                            foreach ($categories as $category) {
                                $tr[$category['id']] = $category['name'];
                            }
                            echo form_dropdown('category_id', $tr, '', 'class="form-control tip" id="up_category_id" style="width:100%;"'.($mand_uphone['category'] ? 'required':''));
                            ?>
                        </div>
                   </div>
               
                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('subcategory'); ?></label>
                        <div class="col-sm-8">
                            <?php 
                            $tr = array();
                            foreach ($subcategories as $category) {
                                $tr[$category['id']] = $category['name'];
                            }
                                echo form_dropdown('sub_category', $tr, '', 'class="form-control tip" id="up_sub_category" style="width:100%;"'.($mand_uphone['sub_category'] ? 'required':''));
                            ?>
                        </div>
                    </div>
                   <div class="form-group">
                        <label for="pquantity" class="col-sm-3 control-label"><?php echo lang('p_carrier');?></label>
                        <div class="col-sm-8">
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
                                <?php echo form_dropdown('carrier', $carriers_dd, set_value('carrier'), 'class="form-control" id="used_phone_carrier"'.($mand_uphone['carrier'] ? 'required':'')); ?>
                                <a class="add_carrier btn input-group-addon"><i class="fas fa-plus"></i></a>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label"><?php echo lang('Cosmetic Condition'); ?></label>
                        <div class="rating col-sm-8">
                            <?php for ($i=5; $i > 0; $i--): ?>
                                <input <?php echo $mand_uphone['cosmetic_condition'] ? 'required' : ''; ?> name="cosmetic_condition" value="<?php echo $i; ?>" id="cosmetic_<?php echo $i; ?>" type="radio">
                                <label for="cosmetic_<?php echo $i; ?>"><i class="fas fa-star"></i>
                                </label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label"><?php echo lang('Operational Condition'); ?></label>
                        <div class="rating col-sm-8">
                            <?php for ($i=5; $i > 0; $i--): ?>
                                <input <?php echo $mand_uphone['opperational_condition'] ? 'required' : ''; ?> name="operational_condition" value="<?php echo $i; ?>" id="operational_<?php echo $i; ?>" type="radio">
                                <label for="operational_<?php echo $i; ?>"><i class="fas fa-star"></i>
                                </label>
                            <?php endfor; ?>
                        </div>
                    </div>

                 
                    <div class="form-group">
                        <label for="pquantity" class="col-sm-3 control-label"><?php echo lang('Status'); ?></label>
                        <div class="col-sm-8">
                            <select <?php echo $mand_uphone['status'] ? 'required' : ''; ?> class="form-control" id="used_phone_status" required="">
                                <option value=""><?php echo lang('Select Phone Status'); ?></option>
                                <option value="1">
                                    <?php echo lang('Ready to Sale');?>
                                </option>
                                <option value="2">
                                    <?php echo lang('Needs Repair');?>
                                </option>
                                <option value="3">
                                    <?php echo lang('On Hold');?>
                                </option>
                                <option value="4">
                                    <?php echo lang('Sold');?>
                                </option>
                                <option value="5">
                                    <?php echo lang('Lost/Damaged');?>
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pquantity" class="col-sm-3 control-label"><?php echo lang('Unlocked'); ?></label>
                        <div class="col-sm-8">
                            <select <?php echo $mand_uphone['unlocked'] ? 'required' : ''; ?> class="form-control" id="unlock_status" required="">
                                <option value="0">
                                    <?php echo lang('no');?>
                                </option>
                                <option value="1">
                                    <?php echo lang('yes');?>
                                </option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" form="uphone_form"  id="addUsedPhoneItemManually"><?php echo lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="recPhoneModal" tabindex="-1" role="dialog" aria-labelledby="recPhoneModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fas fa-2x">&times;</i></span><span class="sr-only"><?php echo lang('close');?></span></button>
                <h4 class="modal-title" id="recPhoneModalTitle"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <div class="alert alert-danger" id="mError-con" style="display: none;">
                    <!--<button data-dismiss="alert" class="close" type="button">×</button>-->
                    <span id="recPhoneError"></span>
                </div>
                <div class="col-md-12">
                    <form class="form-horizontal" id="addRecItems" onsubmit="form1_onsubmit()" role="form">
                        
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" form="addRecItems" class="btn btn-primary" value="submit" id="recPhoneModalClose"><?php echo lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $( function() {
        $( "#acquired_date" ).datepicker();
    } );

    $('#posupplier').select2({
        language: "<?= $this->repairer->get_parseley_lang(); ?>"
    });
    
    function addHidden(theForm, key, value) {
        // Create a hidden input element, and append it to the form:
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        theForm.appendChild(input);
    }
    $('#purchase_form').on( "submit", function(event) {
        $('#posupplier').prop('disabled', false);
        poitems = (localStorage.getItem('poitems'));
        addHidden(this, 'poitems', poitems);
        var t = $('#postatus').val();
        if (t == 'received') {
            new_phone_items = {};
            new_phone_html = '';
            used_phone_items = {};
            used_phone_html = '';
            if (localStorage.getItem('poitems')) {
                poitems = JSON.parse(localStorage.getItem('poitems'));
                $.each(poitems, function () {
                    if (this.row.type == 'new_phone') {
                        new_phone_items[this.item_id] = this;
                        new_phone_html += '<fieldset> <legend>'+this.row.phone_name+'</legend><input type="hidden" name="new_phone_id[]" value="'+this.row.id+'"> <div class="form-group"> <label for="pquantity" class="col-sm-3 control-label">'+"<?php echo lang('Cost');?>"+'</label> <div class="col-sm-8"> <input type="text" class="form-control" value="" name="new_phone_cost[]" id="phone_cost"> </div></div><div class="form-group"> <label for="pquantity" class="col-sm-3 control-label">'+"<?php echo lang('IMEI');?>"+'</label> <div class="col-sm-8"> <input type="text" class="form-control" value="" name="new_phone_imei[]" id="phone_imei"> </div></div></fieldset>';
                    }
                     if (this.row.type == 'used_phone') {
                        used_phone_items[this.item_id] = this;
                        used_phone_html += '<fieldset> <legend>'+this.row.phone_name+'</legend><input type="hidden" name="used_phone_id[]" value="'+this.row.id+'"> <div class="form-group"> <label for="pquantity" class="col-sm-3 control-label">'+"<?php echo lang('Cost');?>"+'</label> <div class="col-sm-8"> <input type="text" class="form-control" value="" name="used_phone_cost[]" id="phone_cost"> </div></div><div class="form-group"> <label for="pquantity" class="col-sm-3 control-label">'+"<?php echo lang('Price');?>"+'</label> <div class="col-sm-8"> <input type="text" class="form-control" value="" name="used_phone_price[]" id="phone_price"> </div></div><div class="form-group"> <label for="pquantity" class="col-sm-3 control-label">'+"<?php echo lang('IMEI');?>"+'</label> <div class="col-sm-8"> <input type="text" class="form-control" value="" name="used_phone_imei[]" id="phone_imei"> </div></div></fieldset>';
                    }
                });

                if(isEmpty(new_phone_html)  && isEmpty(used_phone_html)){
                    document.getElementById("purchase_form").submit();
                }else{
                    $('#addRecItems').html("<fieldset class='hfieldset'><legend class='hlegend'><?php echo lang('New Phones');?></legend>"+new_phone_html+"</fieldset><fieldset class='hfieldset'><legend class='hlegend'><?php echo lang('Used Phones');?></legend>"+used_phone_html+"</fieldset>");
                    $('#recPhoneModal').appendTo("body").modal('show');
                }
                
            }
        }else{
            document.getElementById("purchase_form").submit();
        }
        event.preventDefault();
    });
    function isEmpty(value){
      return (value == null || value.length === 0);
    }
    $('#provider_select').select2();
    jQuery('.inp_provider').hide();

    jQuery("#provider_select").on("select2:select", function (e) {
        var selected = jQuery("#provider_select").val();
        if(selected === 'other') {
            jQuery('.select_provider').hide();
            jQuery('.inp_provider').show();
            jQuery('#provider_input').val('');
            jQuery('#provider_input').focus();

        }
        else
        {
            jQuery('#category_select').val(selected);
        }
    });
</script>
<?php echo $this->load->view($theme.'purchases/add_items'); ?>

