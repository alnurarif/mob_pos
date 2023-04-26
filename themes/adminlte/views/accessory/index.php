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
function actions(x) {
    var pqc = x.split("___");

    var button = "";
    <?php if($this->Admin || $GP['accessory-delete']): ?>
        button = "<li><a id='delete_accessories' data-num='"+pqc[0]+"'><i class='fas fa-trash'></i> <?=lang('delete');?></a></li>";
    <?php endif; ?>
    var return_var = "<div class='btn-group'><button type=\"button\" class=\"btn btn-default btn-xs btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-expanded=\"true\">Actions <span class=\"caret\"></button><ul class=\"dropdown-menu pull-right\" role=\"menu\">";
    <?php if($this->Admin || $GP['accessory-manage_stock']): ?>
        return_var += "<li><a href='"+site.base_url+"panel/pos_inventory/index/accessory/"+pqc[0]+"'><i class='fas fa-edit'></i> "+lang.manage_stock+"</a></li>";
    <?php endif; ?>

    <?php if($this->Admin || $GP['accessory-edit']): ?>
    return_var += "<li><a data-dismiss='modal' id='modify' href='#accessorymodal' data-toggle='modal' data-num='"+pqc[0]+"'><i class='fas fa-edit'></i> "+lang.edit+"</a></li>";
    <?php endif; ?>
    return_var += button;
    return_var += '</ul></div>';

    return return_var;
}

    function checkbox_q(x) {
        var pqc = x.split("__");
        if(pqc[1] == 1){
          return '<div class="text-center"><div class="checkbox-styled checkbox-inline"><input checked type="checkbox" onclick="quick_sale('+pqc[0]+',\'dynamic-table\');" id="qcheck'+pqc[0]+'"><label for="qcheck'+pqc[0]+'"></label></div></div>';
        }else{
          return '<div class="text-center"><div class="checkbox-styled checkbox-inline"><input type="checkbox" onclick="quick_sale('+pqc[0]+',\'dynamic-table\');" id="qcheck'+pqc[0]+'"><label for="qcheck'+pqc[0]+'"></label></div></div>';
        }
    }

    function discount(x) {
        var pqc = x.split("__");
        if(pqc[1] == 1){
          return formatDecimal(pqc[0])+'%';
        }else{
          return site.settings.currency+(pqc[0]);
        }
    }

    $(document).ready(function () {
        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[3, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo base_url(); ?>panel/accessory/getAllAccessories/<?php echo $toggle_type.$v; ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            }, 
            "aoColumns": [
                {"bSortable": false, "mRender": checkbox},
                null,
                null,
                {"bSortable": false, "mRender": currencyFormat},
                {"bSortable": false, "mRender": discount},
                {"bSortable": false, "mRender": checkbox_q},
                {"bSortable": false, "mRender": actions},
            ],
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
        var ajaxurl = "<?php echo base_url("panel/accessory"); ?>/update_qs";
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

    jQuery(document).on("click", "#delete_accessories", function () {
        var num = jQuery(this).data("num");
        var mode = jQuery(this).data("mode");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/accessory/delete",
            data: "id=" + encodeURI(num),
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
                toastr['success']("<?php echo lang('deleted')?>");
                $('#dynamic-table').DataTable().ajax.reload();
            }
        });
    });
    
</script>
<?php if($this->Admin || $GP['accessory-add'] || $GP['accessory-edit']): ?>

<!-- ============= MODAL MODIFICA CLIENTI ============= -->
<div class="modal fade" id="accessorymodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="titleAccessory"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <p class="tips custip"></p>
                    <div class="row">
                        <form id="accessories_form" class="parsley-form" method="post">
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
                                                <input <?php echo $frm_priv["code"] ? "required": "" ?> id="a_upc_code" name="a_upc_code" type="text" class="validate form-control"  >
                                                <span class="input-group-addon" id="upc_gen_accessory" style="padding: 1px 10px;">
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
                                            <input id="alert_quantity" min="0" name="alert_quantity" type="number" step="any" class="validate form-control" required>
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
                                            echo form_dropdown('category_id', $tr, '', 'class="form-control tip" id="category_id" style="width:100%;"'.($frm_priv['category'] ? 'required' : ''));
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
                                                echo form_dropdown('sub_category', $tr, '', 'class="form-control tip" id="sub_category" style="width:100%;"'.($frm_priv['sub_category'] ? 'required' : ''));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-12 col-lg-4">
                                        <div class="form-group">
                                            <?php echo lang('p_max_discount', 'p_max_discount');?>
                                            <div class="input-group">
                                                <input data-parsley-type="number" id="a_max_discount" name="a_max_discount" value="0" type="text" <?php echo $frm_priv["max_discount"] ? "required": "" ?> class="form-control">
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
                                            <div class="checkbox-styled checkbox-inline">
                                                <input type="hidden"  name="taxable" value="0">
                                                <input type="checkbox" id="taxable" checked name="taxable" value="1">
                                                <label for="taxable"><?php echo lang('is_taxable');?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-4 input-field">
                                        <div class="form-group all">
                                            <div class="checkbox-styled checkbox-inline">
                                                <input type="hidden"  name="is_serialized" value="0">
                                                <input type="checkbox" id="is_serialized" name="is_serialized" value="1">
                                                <label for="is_serialized"><?php echo lang('is_stock_serialized');?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if(!$settings->universal_accessories): ?>
                                    <div class="col-md-12 col-lg-4 input-field">
                                        <div class="form-group all">
                                            <div class="checkbox-styled checkbox-inline">
                                                <input type="hidden"  name="universal" value="0">
                                                <input type="checkbox" id="universal" name="universal" value="1">
                                                <label for="universal"><?php echo lang('is_universal'); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col-lg-12">
                                    <div class="form-group">
                                        <label><?php echo lang('notes');?></label>
                                        <textarea <?php echo $frm_priv["notes"] ? "required": "" ?> class="form-control" name="note" id="note" rows="6"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                </div>
            </div>
            <div class="modal-footer" id="footerAccessory">
                  <!--    -->
            </div>
        </div>
    </div>
</div>
</div>

<?php endif; ?>
  
<?php if($this->Admin || $GP['accessory-add']): ?>
    <button href="#accessorymodal" class="add_accessory btn btn-primary">
        <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo lang('Accessory');?>
    </button>
<?php endif; ?>
<?php echo form_open('panel/accessory/actions', 'id="action-form"'); ?>

<div class="box box-primary ">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo lang('Accessories');?></h3>
        <div class="box-tools pull-right">
            <div class="btn-group">
                
                <li class="btn btn-sm btn-default" style="list-style-type: none;">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fas fa-tasks tip" data-placement="left" title="<?php echo lang("actions") ?>"></i>
                    </a>
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
        <div class="table-responsive">
            <table class=" compact table table-bordered table-striped" id="dynamic-table" width="100%">
                <thead>
                    <tr>
                        <th style="min-width:30px; width: 30px; text-align: center;">
                            <input class="checkbox checkth" type="checkbox" name="check"/>
                        </th>
                        <th><?php echo lang('name');?></th>
                        <th><?php echo lang('upc_code');?></th>
                        <th><?php echo lang('price');?></th>
                        <th><?php echo lang('p_max_discount');?></th>
                        <th><?php echo lang('quick_sale');?></th>
                        <th><?php echo lang('actions'); ?></th>
                    </tr>
                </thead>
            </table>
            
        </div>
    </div>
</div>


<div style="display: none;">
    <input type="hidden" name="form_action" value="" id="form_action"/>
    <?php echo form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
</div>
<?php echo form_close() ?>

<script type="text/javascript">
<?php if($this->Admin || $GP['accessory-add']): ?>

      jQuery(".add_accessory").on("click", function (e) {
        $('#accessorymodal').modal('show');
        

        jQuery('#a_name').val('');
        jQuery('#a_upc_code').val('');
        jQuery('#a_price').val('');
        jQuery('#a_max_discount').val('0');
        jQuery('#a_d_s_l').val('');
        jQuery('#re_at').val('');
        jQuery('#note').val('');
		jQuery('#category').val('').trigger('change');
		jQuery('#sub_category').val('').trigger('change');
        if (document.getElementById('universal')) {
            document.getElementById("universal").checked = false;
        }
        jQuery('#titleAccessory').html("<?php echo lang('add'); ?> <?php echo lang('accessory'); ?>");

        jQuery('#footerAccessory').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button type="submit" class="btn btn-success" id="submit_accessory" data-mode="add" form="accessories_form" value="Submit"><?php echo lang('submit'); ?></button>');
    });
<?php endif; ?>
       jQuery(document).on("click", "#modify", function () {
            <?php if(!$this->Admin && !$GP['accessory-edit']): ?>
                $('#accessorymodal').modal('hide');
                toastr.error("<?php echo lang('not_allowed_accessory');?>"); 
                return;
            <?php else: ?>
                jQuery('#titleAccessory').html('<?php echo lang('edit'); ?> <?php echo lang('accessory'); ?>');
                
                var num = jQuery(this).data("num");
                jQuery.ajax({
                    type: "POST",
                    url: base_url + "panel/accessory/getAccessoryByID",
                    data: "id=" + encodeURI(num) + "&token=<?php echo $_SESSION['token'];?>",
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        jQuery('#titleAccessory').html("<?php echo lang('edit'); ?> Accessory");

                        jQuery('#a_name').val(data.name);
                        jQuery('#a_upc_code').val(data.upc_code);
                        jQuery('#a_price').val(data.price);
                        jQuery('#a_max_discount').val(data.max_discount);
                        jQuery('#a_discount_type').val(data.discount_type);
                        jQuery('#alert_quantity').val(data.alert_quantity);
						jQuery('#category_id').val(data.category).trigger('change');
                        jQuery('#sub_category').val(data.sub_category).trigger('change');
                        jQuery('#warranty_id').val(data.warranty_id).trigger('change');
                        if (data.taxable == 1) {
                            document.getElementById("taxable").checked = true;
                        }else{
                            document.getElementById("taxable").checked = false;
                        }
                        if (data.is_serialized == 1) {
                            document.getElementById("is_serialized").checked = true;
                        }else{
                            document.getElementById("is_serialized").checked = false;
                        }

                        if (document.getElementById('universal')) {
                            if (data.universal == 1) {
                                document.getElementById("universal").checked = true;
                            }else{
                                document.getElementById("universal").checked = false;
                            }
                        }
                        if (data.discount_type == 1) {
                            $("#discount_type option[value='1']").prop("selected", true);
                        }else{
                            $("#discount_type option[value='2']").prop("selected", true);
                        }

                       
                        jQuery('#note').val(data.note);

                        jQuery('#footerAccessory').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button type="submit" id="submit_accessory" class="btn btn-success" data-mode="modify" form="accessories_form" value="Submit" data-num="' + encodeURI(num) + '"><i class="fas fa-save"></i ><?php echo lang('submit');?></button>')
                    }
                });
            <?php endif; ?>

        });
        // process the form
        $('#accessories_form').on( "submit", function(event) {
            event.preventDefault();
            var mode = jQuery('#submit_accessory').data("mode");
            var id = jQuery('#submit_accessory').data("num");
            form = $(this);
            var valid = form.parsley().validate();
            if (valid) {
                var url = "";
                var dataString = "";

                if (mode == "add") {
                    url = base_url + "panel/accessory/add";
                    dataString = $('form').serialize();
                    jQuery.ajax({
                        type: "POST",
                        url: url,
                        data: dataString,
                        cache: false,
                        success: function (data) {
                            if (data.success) {
                                toastr['success']("<?php echo lang('add');?>", "<?php echo lang('accessory');?>: " + name + " <?php echo lang('added');?>");
                                setTimeout(function () {
                                    $('#accessorymodal').modal('hide');
                                    $('#dynamic-table').DataTable().ajax.reload();
                                }, 500);
                            }else{
                                bootbox.alert(data.message);
                            }
                            
                        }
                    });
                } else {
                    url = base_url + "panel/accessory/edit";
                    dataString = $('form').serialize() + "&id=" + encodeURI(id);
                    jQuery.ajax({
                        type: "POST",
                        url: url,
                        data: dataString,
                        cache: false,
                        success: function (data) {
                            data = JSON.parse(data);
                            if (data.success) {
                                toastr['success']("<?php echo lang('edit');?>", "<?php echo lang('accessory');?>: " + name + "<?php echo lang('updated');?>");
                                setTimeout(function () {
                                    $('#accessorymodal').modal('hide');
                                    $('#dynamic-table').DataTable().ajax.reload();
                                }, 500);
                            }else{
                                bootbox.alert(data.message);
                            }
                        }
                    });
                }
            }
            return false;
        });
        $('#upc_gen_accessory').on( "click", function(){
            $(this).parent('.input-group').children('input').val(generateCardNo(8));
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
    $( "#category_id" ).select2();
    $( "#sub_category" ).select2({        
        ajax: {
            placeholder: 'Select a Category',
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