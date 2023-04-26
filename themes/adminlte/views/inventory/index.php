<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$v='?v=1';
if (isset($_GET['cat']) && is_numeric($_GET['cat'])) {
    $v .= '&cat_id='.$_GET['cat'];
}
if (isset($_GET['sub_id']) && is_numeric($_GET['sub_id'])) {
    $v .= '&sub_id='.$_GET['sub_id'];
}
?>
<script>
    function checkbox_qs(x) {
        var pqc = x.split("__");
        if(pqc[1] == 1){
          return '<div class="text-center"><div class="checkbox-styled checkbox-inline"><input checked type="checkbox" onclick="quick_sale('+pqc[0]+',\'dynamic-table\');" id="qcheck'+pqc[0]+'"><label for="qcheck'+pqc[0]+'"></label></div></div>';
        }else{
          return '<div class="text-center"><div class="checkbox-styled checkbox-inline"><input type="checkbox" onclick="quick_sale('+pqc[0]+',\'dynamic-table\');" id="qcheck'+pqc[0]+'"><label for="qcheck'+pqc[0]+'"></label></div></div>';
        }
    }

    function manage_stock(x) {
        
        return '<div class="text-center"><a href="<?php echo base_url("panel/pos_inventory/index/repair"); ?>/'+x+'">Manage Stock</a></div>';
    }

    function actions(x) {
        var pqc = x.split("___");
        var action = "<div class='btn-group'><button type=\"button\" class=\"btn btn-default btn-xs btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-expanded=\"true\"><?php echo lang('Actions');?> <span class=\"caret\"></button><ul class=\"dropdown-menu pull-right\" role=\"menu\">";
        <?php if($this->Admin || $GP['inventory-edit']): ?>
            action += "<li><a data-dismiss='modal' id='modify_inventory' href='#productmodal' data-toggle='modal' data-num='"+pqc[0]+"'><i class='fas fa-edit'></i> <?php echo lang('Edit Product');?></a></li>";
// 
            // action += "<li><a href='<?php echo base_url('panel/inventory/edit/');?>"+pqc[0]+"'><i class='fas fa-edit'></i> </a></li>";
        <?php endif; ?>
        <?php if($this->Admin || $GP['inventory-add']): ?>
            action += "<li><a href='<?php echo base_url('panel/inventory/add/');?>"+pqc[0]+"'><i class='fas fa-plus-square'></i> <?php echo lang('Duplicate Product');?></a></li>";
        <?php endif; ?>
        action += '<li><a href="<?php echo base_url("panel/pos_inventory/index/repair"); ?>/'+pqc[0]+'"><i class="fas fa-edit"></i> '+"<?php echo lang('Manage Stock');?>"+'</a></li>';

        <?php if($this->Admin || $GP['inventory-delete']): ?>
            action += "<li><a id='toggle' data-num='"+pqc[0]+"' data-mode='disable'><i class='fas fa-trash'></i> <?php echo lang('delete');?></a></li>";
        <?php endif; ?>
        
        action += '</ul></div></div>';
        return action;
    }
    var oTable;
    $(document).ready(function () {
        oTable = $('#PRData').dataTable({
            "aaSorting": [[2, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('panel/inventory/getProducts/'.$toggle_type).$v; ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[0];
                nRow.className = "product_link";
                return nRow;
            },
            "aoColumns": [
                {"searchable": false, "bSortable": false, "mRender": checkbox}, 
                {"searchable": false },
                {"searchable": true },
                {"searchable": false, 'mRender': currencyFormat},
                {"searchable": false },
                {"searchable": false, "bSortable": false, "mRender": checkbox_qs},
                {"searchable": false, 'mRender': manage_stock},
                {"searchable": false, 'mRender': actions},
            ]
        });

    });
   
   $('body').on('click', '.product_link td:not(:first-child, :nth-child(2), :nth-last-child(2),:nth-last-child(3), :last-child)', function() {
        $('#myModal').modal({remote: site.base_url + 'panel/inventory/modal_view/' + $(this).parent('.product_link').attr('id')});
        $('#myModal').modal('show');
        
    });
    $('body').on('click', '.bpo', function(e) {
        e.preventDefault();
        $(this).popover({html: true, trigger: 'manual'}).popover('toggle');
        return false;
    });
    $('body').on('click', '.bpo-close', function(e) {
        $('.bpo').popover('hide');
        return false;
    });

     jQuery(document).on("click", "#toggle", function (event) {
        event.preventDefault();
        var num = jQuery(this).data("num");
        var mode = jQuery(this).data("mode");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/inventory/toggle",
            data: "id=" + encodeURI(num) +"&toggle=" + encodeURI(mode),
            cache: false,
            dataType: "json",
            success: function (data) {
                console.log(data);
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
                toastr['success']("<?php echo lang('Toggle');?>: ", data.toggle);
                $('#PRData').DataTable().ajax.reload();
            }
        });
    });

    function quick_sale(id, tbl) {
        var val1;
        if ($("#qcheck" + id).is(':checked')) {
            val1 = '1';//
        } else {
            val1 = '0';
        }
        update_qs_value(val1, id, tbl);
    }
    function update_qs_value(val1, id, tbl) {
        var row_id = id;
        var ajaxurl = "<?php echo base_url("panel/inventory"); ?>/update_qs";
        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {val1, row_id, tbl},
            dataType: "HTML",
            success: function (data) {
                toastr['success']("<?php echo lang('Done');?>");
            },
            error: function () {
                toastr['error']("<?php echo lang('Error');?>");
            }
        });
    }
</script>



<?php echo form_open('panel/inventory/product_actions', 'id="action-form"'); ?>

    <?php if ($this->Admin || $GP['inventory-add']): ?>
    <!-- <a class="btn btn-primary" href="<?php echo base_url();?>panel/inventory/add"><i class="fa fa-plus-circle"></i> <?php echo lang('Add Repair Product');?></a> -->

    <button href="#inventory_form" class="add_inventory btn btn-primary">
        <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo lang('inventory');?>
    </button>
<?php endif; ?>


<div class="box box-primary ">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo lang('Repair Parts');?></h3>
        <div class="box-tools pull-right">
            <div class="btn-group">
                <a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>panel/inventory/"><?php echo lang('All');?></a>
                <a class="btn btn-sm btn-success" href="<?php echo base_url(); ?>panel/inventory/index/enabled"><i class='fas fa-toggle-on'></i> <?php echo lang('enabled');?></a>
                <a class="btn btn-sm btn-danger" href="<?php echo base_url(); ?>panel/inventory/index/disabled"><i class='fas fa-toggle-off'></i> <?php echo lang('disabled');?></a>

                <li class="btn btn-default btn-sm" style="list-style-type: none;">
                    <span data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fas fa-tasks tip" data-placement="left" title="<?php echo lang("actions") ?>"></i>
                    </span>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        
                       
                        <li>
                            <a href="#" id="excel" data-action="export_excel">
                                <i class="fas fa-file-excel"></i> <?php echo lang('export_to_excel') ?>
                            </a> 
                        </li>
                        <li>
                            <a href="#" id="pdf" data-action="export_pdf">
                                <i class="fas fa-file-pdf"></i> <?php echo lang('export_to_pdf') ?>
                            </a>
                        </li>
                      
                    </ul>
                </li>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-default"><?php echo lang('filter_by_category');?></button>
                <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span> <span class="sr-only"><?php echo lang('toggle_dropdown');?></span> </button>
                <ul class="dropdown-menu" role="menu">
                    <?php if($cat_filter): ?>
                    <?php foreach ($cat_filter as $cat): ?>
                        <li>
                            <a href="<?php echo current_url(); ?>?cat=<?php echo $cat['id'];?>"><strong><?php echo escapeStr($cat['name']); ?></strong></a>
                        </li>
                        <?php if($cat['children']): ?>
                            <?php foreach ($cat['children'] as $child): ?>
                                <li>
                                    <a href="<?php echo current_url(); ?>?cat=<?php echo $cat['id'];?>&sub_id=<?php echo $child['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo escapeStr($child['name']); ?></a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <li>
                            <a href="#"><?php echo lang('no_categories_found');?></a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="table-responsive"  style="width:100%">
              <table id="PRData" class="table table-bordered table-condensed table-hover table-striped" width="100%">
                    <thead>
                        <tr class="primary">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th><?php echo lang("code") ?></th>
                            <th><?php echo lang("name") ?></th>
                            <th><?php echo lang("price") ?></th>
                            <th><?php echo lang('Current Stock');?></th>
                            <th><?php echo lang('Quick Sale');?></th>
                            <th><?php echo lang('manage_stock');?></th>
                            <th style="min-width:65px; text-align:center;"><?php echo lang("actions") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="11" class="dataTables_empty"><?php echo lang('loading_data_from_server'); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
    </div>
</div>


    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?php echo form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?php echo form_close() ?>







<!-- ============= MODAL MODIFICA CLIENTI ============= -->
<div class="modal fade" id="productmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="titleProduct"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <p class="tips custip"></p>
                    <div class="row">
                    <?php
                        $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'class'=>'parsley-form', 'id' => "inventory_form");
                        echo form_open_multipart("panel/inventory/add", $attrib)
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group all">
                                <?php echo lang("product_name", 'name') ?>
                                <?php echo form_input('name', '', 'class="form-control" id="name" required="required"'); ?>
                            </div>
                            <div class="form-group all">
                                <?php echo lang("product_code", 'code') ?>
                                <div class="input-group">
                                    <?php echo form_input('code', '', 'class="form-control" id="code" required') ?>
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
                                    <?php echo form_dropdown('manufacturer', $mm, set_value('manufacturer'), 'id="manufacturer" class="form-control select" style="width: 100%"'.($frm_priv_inventory['manufacturer'] ? 'required' : '')); ?>
                                    
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
                                        <input <?php echo $frm_priv_inventory['model'] ? 'required' : ''; ?> type="text" name="model" id="model" class="form-control " value="<?php echo set_value('model');?>">
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
                                <?php echo form_input('alert_quantity', '', 'class="form-control tip" id="alert_quantity"'.($frm_priv_inventory['alert_quantity'] ? 'required' : '')) ?>
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
                                echo form_dropdown('category_id', $tr, '', 'class="form-control tip" id="pcategory_id" style="width:100%;"'.($frm_priv_inventory['category'] ? 'required' : ''));
                                ?>
                            </div>
                            <div class="form-group">
                                <label><?php echo lang('Sub Category');?></label>
                                <?php 
                                $tr = array();
                                foreach ($subcategories as $category) {
                                    $tr[$category['id']] = $category['name'];
                                }
                                    echo form_dropdown('sub_category', $tr, '', 'class="form-control tip" id="psub_category" style="width:100%;"'.($frm_priv_inventory['sub_category'] ? 'required' : ''));
                                ?>
                            </div>
                            <div class="form-group all">
                                <?php echo lang("product_price", 'price') ?>
                                <?php echo form_input('price', '', 'class="form-control tip" id="price" required="required"') ?>
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
                            <div id="pvariants_table" style="display: none;">
                                <table id='pmup' class="table table-striped table-bordered table-hover">
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
                                                    id="padd_row">
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
                                <?php echo form_textarea('details', '', 'class="form-control" id="details"'); ?>
                            </div>
                        </div>
                        </div>
                        
                        </div>
                        <input type="hidden" id="inventory_id" name="id" />
                    <?php echo form_close(); ?>
                    </div>
                <div class="clearfix"></div>

            </div>
            <div class="modal-footer" id="footerProduct">
                  <!--    -->
            </div>
        </div>
    </div>
</div>
</div>

<script>
    jQuery(".add_inventory").on("click", function (e) {
        e.preventDefault();
        $('#productmodal').modal('show');
        jQuery('#inventory_form').attr('action', base_url + 'panel/inventory/add');

        jQuery('#inventory_form :input').val('');
        $('#discount_type').val('1');
        jQuery('#titleProduct').html("<?php echo lang('add'); ?> <?php echo lang('inventory'); ?>");
        jQuery('#footerProduct').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button type="submit" class="btn btn-success" id="submit_inventory" data-mode="add" form="inventory_form" value="Submit"><?php echo lang('submit'); ?></button>');
    });
</script>



<script type="text/javascript">
    $(document).ready(function () {
        var items = {};
        function calculate_price() {
            var rows = $('#prTable').children('tbody').children('tr');
            var pp = 0;
            $.each(rows, function () {
                pp += formatDecimal(parseFloat($(this).find('.rprice').val())*parseFloat($(this).find('.rquantity').val()));
            });
            $('#price').val(pp);
            return true;
        }

        $(document).on('change', '.rquantity, .rprice', function () {
            calculate_price();
        });

        $(document).on('click', '.del', function () {
            var id = $(this).attr('id');
            delete items[id];
            $(this).closest('#row_' + id).remove();
            calculate_price();
        });

        var su = 2;
        $(document).on('click', '.delAttr', function () {
            $(this).closest("tr").remove();
        });

        $(document).on('click', '.attr-remove-all', function () {
            $('#attrTable tbody').empty();
            $('#attrTable').hide();
        });
    });

    $('#random_num').on( "click", function(){
        $(this).parent('.input-group').children('input').val(generateCardNo(8));
    });
    
    $(document).ready(function () {
        $('input[name=variants]').on("change", function(){
          if($(this).is(':checked')){
            $('#pvariants_table').slideDown();
            $('.required').attr('required', true);
          } else {
            $('#pvariants_table').slideUp();
            $('.required').attr('required', false);
            $('input[name=variant_name],input[name=variant_price]').prop('disabled', true);
          }
        });
    });

    if($('input[name=variants]').is(':checked')){
        $('#pvariants_table').slideDown();
        $('.required').attr('required', true);
    } else {
        $('#pvariants_table').slideUp();
        $('.required').attr('required', false);
        $('input[name=variant_name],input[name=variant_price]').prop('disabled', true);
    }
    
    $("#padd_row").on("click", function () {
        var url = "<?php echo base_url("panel/inventory/addmore");?>";
        $.get(url, {}, function (data) {
            $("#pmup tbody").append(data);
        });
    });
    $(document).on('click', '.pdelete_row', function (e) {
        var id = $(this).data("id");
        $("#pvariant_" + id).remove();
    });
    
    jQuery(document).ready( function($) {
        
        $('#pcategory_id').on('select2:select', function (e) {
            $('#psub_category').val('').trigger('change');
        });

        $( "#pcategory_id" ).select2({
            placeholder: "<?php echo lang('select_placeholder');?>",
        });
        $( "#psub_category" ).select2({
            placeholder: "<?php echo lang('select_placeholder');?>",

            ajax: {
                placeholder: "<?php echo lang('Select a Category');?>",
                url: "<?php echo base_url(); ?>panel/settings/getCategoriesAjax/0",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        category_id: $('#pcategory_id').val(),
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

    jQuery(document).on("click", "#modify_inventory", function () {
            <?php if(!$this->Admin && !$GP['inventory-edit']): ?>
                $('#productmodal').modal('hide');
                toastr.error("<?php echo lang('not_allowed_inventory');?>"); 
                return;
            <?php else: ?>
                jQuery('#titleProduct').html('<?php echo lang('edit'); ?> <?php echo lang('inventory'); ?>');
                
                var num = jQuery(this).data("num");
                jQuery.ajax({
                    type: "POST",
                    url: base_url + "panel/inventory/getInventoryByID",
                    data: "id=" + encodeURI(num) + "&token=<?php echo $_SESSION['token'];?>",
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        jQuery('#titleProduct').html("<?php echo lang('edit'); ?> inventory");

                        jQuery('#inventory_form').attr('action', base_url + 'panel/inventory/edit');
                        jQuery('#inventory_id').val(data.id);
                        

                        jQuery('#inventory_form #name').val(data.name);
                        jQuery('#inventory_form #code').val(data.code);
                        jQuery('#inventory_form #manufacturer').val(data.manufacturer_id).trigger('change');
                        jQuery('#inventory_form #model').val(data.model_name);
                        jQuery('#inventory_form #warranty_id').val(data.warranty_id);
                        jQuery('#inventory_form #alert_quantity').val(data.alert_quantity);
                        jQuery('#inventory_form #max_discount').val(data.max_discount);
                        jQuery('#inventory_form #discount_type').val(data.discount_type);
                        jQuery('#inventory_form #details').val(data.details);
                        jQuery('#inventory_form #delivery_note_number').val(data.delivery_note_number);
						jQuery('#inventory_form #pcategory_id').val(data.category).trigger('change');
                        jQuery('#inventory_form #psub_category').val(data.sub_category).trigger('change');
                        jQuery('#inventory_form #price').val(data.price);
                        

                        
                        if (data.discount_type == 1) {
                            $("#discount_type option[value='1']").prop("selected", true);
                        }else{
                            $("#discount_type option[value='2']").prop("selected", true);
                        }


                        $('#inventory_form #taxable').attr('checked', false)
                        $('#inventory_form #is_serialized').attr('checked', false)
                        $('#inventory_form #pv').attr('checked', false)
                        $('#inventory_form #universal').attr('checked', false)
                        if (data.taxable == 1) {
                            $('#inventory_form #taxable').attr('checked', true)
                        }
                        if (data.is_serialized == 1) {
                            $('#inventory_form #is_serialized').attr('checked', true)
                        }
                        if (data.universal == 1) {
                            $('#inventory_form #universal').attr('checked', true)
                        }

                        if (data.variants) {
                            $('#inventory_form #pv').attr('checked', true).trigger('change');


                            $('#inventory_form #pmup tbody').empty();

                            $.each( data.variants, function( key, value ) {
                                $('#inventory_form #pmup tbody').append(`<tr>
                                    <td>
                                        <input type="text" value="${value.variant_name}" class="form-control required" name="variant_name[]" placeholder="<?php echo lang('Name');?>"/>
                                    </td>
                                    <td>
                                        <input type="number" value="${value.price}" step="any" min="0"  class="form-control required" name="variant_price[]" placeholder="<?php echo lang('Price');?>">
                                    </td>
                                </tr>`);
                            });

                            
                            
                        }


                        

                        jQuery('#footerProduct').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button type="submit" id="submit_inventory" class="btn btn-success" data-mode="modify" form="inventory_form" value="Submit" data-num="' + encodeURI(num) + '"><i class="fas fa-save"></i ><?php echo lang('submit');?></button>')
                    }
                });
            <?php endif; ?>

        });
</script>
