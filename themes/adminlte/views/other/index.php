<?php
$v='?v=1';
if (isset($_GET['cat']) && is_numeric($_GET['cat'])) {
    $v .= '&cat_id='.$_GET['cat'];
}
if (isset($_GET['sub_id']) && is_numeric($_GET['sub_id'])) {
    $v .= '&sub_id='.$_GET['sub_id'];
}
?><script>
        function variable_price(x) {
            if (x == 'variable_price') {
                return "<span class='label label-info'><?php echo lang('variable_price');?></span>";
            }
            return currencyFormat(x);
        }
        function checkbox_q(x) {
            var pqc = x.split("__");
            if(pqc[1] == 1){
              return '<div class="text-center"><div class="checkbox-styled checkbox-inline"><input checked type="checkbox" onclick="quick_sale('+pqc[0]+',\'dynamic-table\');" id="qcheck'+pqc[0]+'"><label for="qcheck'+pqc[0]+'"></label></div></div>';
            }else{
              return '<div class="text-center"><div class="checkbox-styled checkbox-inline"><input type="checkbox" onclick="quick_sale('+pqc[0]+',\'dynamic-table\');" id="qcheck'+pqc[0]+'"><label for="qcheck'+pqc[0]+'"></label></div></div>';
            }
        }

      

        function actions(x) {
            var pqc = x.split("__");
            x = pqc[0];
            y = pqc[1];
            var string = "<div class=\"btn-group\"><button type=\"button\" class=\"btn btn-default btn-xs btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-expanded=\"true\"><?php echo lang('Actions');?> <span class=\"caret\"></span></button><ul class=\"dropdown-menu pull-right\" role=\"menu\">";
            
            <?php if($this->Admin || $GP['other-manage_stock']): ?>
                if (y == 1) {
                    string += "<li><a href='<?php echo base_url('panel/pos_inventory/index/other/')?>"+x+"'><i class='fas fa-edit'></i> <?php echo lang('Manage Stock');?></a></li>";
                }
            <?php endif; ?>

            <?php if($this->Admin || $GP['other-edit']): ?>
            string += "<li><a  data-dismiss='modal' id='modify' href='#othermodal' data-toggle='modal' data-num='"+x+"'><i class='fas fa-edit'></i> <?php echo lang('Edit');?></a></li>";
            <?php endif; ?>
            
            <?php if($this->Admin || $GP['other-delete']): ?>
                string += "<li><a id='delete_other' data-num='"+pqc[0]+"' data-mode='disable'><i class='fas fa-trash'></i> <?php echo lang('delete');?>" +"</a></li>";
            <?php endif; ?>

            string += "</ul></div>";
            return string;
        }
        $(document).ready(function () {
        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[3, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo base_url(); ?>panel/other/getAllOthers/<?php echo $toggle_type.$v; ?>',
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
                {"bSortable": false, "mRender": variable_price},
                {"bSortable": false, "mRender": discount},
                {"bSortable": false, "mRender": checkbox_q},
                {"bSortable": false, "mRender": actions},
            ],
        });
    });

    function discount(x) {
        var pqc = x.split("__");
        if(pqc[1] == 1){
          return formatDecimal(pqc[0])+'%';
        }else{
          return formatMoney(pqc[0]);
        }
    }
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
        var ajaxurl = "<?php echo base_url("panel/other"); ?>/update_qs";
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
    jQuery(document).on("click", "#delete_other", function () {
        var num = jQuery(this).data("num");
        var mode = jQuery(this).data("mode");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/other/delete",
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
                toastr['success']("<?php echo lang('deleted');?>");
                $('#dynamic-table').DataTable().ajax.reload();
            }
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
                <p class="tips custip"></p>
                <form id="other_form" method="post">
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
                                    <span class="input-group-addon pointer" id="random_gen_other" style="padding: 1px 10px;">
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
                                <input id="alert_quantity" min="0" name="alert_quantity" type="number" step="any" class="validate form-control" required>
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
                                echo form_dropdown('warranty_id', $tr, '', 'class="form-control tip" id="warranty_id" style="width:100%;" required');
                                ?>
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
                                echo form_dropdown('category_id', $tr, '', 'class="form-control tip" id="category_id" style="width:100%;"');
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
                                    echo form_dropdown('sub_category', $tr, '', 'class="form-control tip" id="sub_category" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-6 cost_div">
                            <div class="form-group">
                                <?php echo lang('cost', 'cost');?>
                                <input id="cost" class="form-control" type="text" name="cost" value="">
                            </div>
                        </div>
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
                    </div>
                   
                    <div class="row">
                        <div class="col-md-12 col-lg-4 input-field">
                            <div class="form-group">
                                <div class="checkbox-styled checkbox-inline">
                                    <input type="hidden"  name="variable_price" value="0">
                                    <input id="variable_price" type="checkbox" name="variable_price" value="1">
                                    <label for="variable_price"><?php echo lang('variable_price');?></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 col-lg-4 input-field">
                            <div class="form-group">
                                <div class="checkbox-styled checkbox-inline">
                                    <input type="hidden"  name="keep_stock" value="0">
                                    <input id="keep_stock" type="checkbox" name="keep_stock" value="1">
                                    <label for="keep_stock"><?php echo lang('keep_stock');?></label>
                                </div>
                            </div>
                        </div>

                     
                        <div class="col-md-12 col-lg-4 input-field">
                            <div class="form-group all">
                                <div class="checkbox-styled checkbox-inline">
                                    <input type="hidden"  name="cash_out" value="0">
                                    <input type="checkbox" id="cash_out" name="cash_out" value="1">
                                    <label for="cash_out"><?php echo lang('cash_out');?></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <?php if(!$settings->universal_others): ?>
                        <div class="col-md-12 col-lg-4 input-field">
                            <div class="form-group all">
                                <div class="checkbox-styled checkbox-inline">
                                    <input type="hidden"  name="universal" value="0">
                                    <input type="checkbox" id="universal" name="universal" value="1">
                                    <label for="universal"><?php echo lang('is_universal'); ?></label>
                                </div>
                            </div>
                        </div>
                        <?php endif;?>
                        <div class="col-md-12 col-lg-4 input-field serial_div" style="display: none;">
                            <div class="form-group all">
                                <div class="checkbox-styled checkbox-inline">
                                    <input type="hidden" name="is_serialized" value="0">
                                    <input id="is_serialized"  type="checkbox" name="is_serialized" value="1">
                                    <label for="is_serialized"><?php echo lang('is_stock_serialized'); ?></label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12 col-lg-4 input-field">
                            <div class="form-group all">
                                <div class="checkbox-styled checkbox-inline">
                                    <input type="hidden"  name="taxable" value="0">

                                    <input type="checkbox" id="taxable" checked name="taxable" value="1">
                                    <label for="taxable"><?php echo lang('is_taxable'); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                          
                           
                            <div class="input-field">
                                <div class="form-group">
                                    <label><?php echo lang('notes'); ?></label>
                                    <textarea class="form-control" name="note" id="note" rows="6"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="" id="footerClient1">
                    <!--    -->
                </div>
            </div>
        </div>
    </div>
</div>

  


<?php if($this->Admin || $GP['other-add']): ?>
    <button href="#othermodal" class="add_other btn btn-primary">
        <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo lang('Other Product');?>
    </button>
<?php endif; ?>



<?php echo form_open('panel/other/actions', 'id="action-form"'); ?>

<div class="box box-primary ">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo lang('Other Products');?></h3>
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
                        <th><?php echo lang('Name');?></th>
                        <th><?php echo lang('UPC Code');?></th>
                        <th><?php echo lang('Price(s)');?></th>
                        <th><?php echo lang('Max Discount');?></th>
                        <th><?php echo lang('Quick Sale');?></th>
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
    $(document).ready(function () {
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

    <?php if($this->Admin || $GP['other-add']): ?>

    <?php if($this->Admin || $GP['other-add']): ?>
<?php endif; ?>
      jQuery(".add_other").on("click", function (e) {
        $('#othermodal').modal('show');
        

        jQuery('#o_name').val('');
        jQuery('#o_upc_code').val('');
        jQuery('#o_cost').val('');
        jQuery('#o_price').val('');
        jQuery('#o_max_discount').val('');
        jQuery('#note').val('');
        jQuery('#cost').val('');
        jQuery('#category').val('').trigger('change');
        jQuery('#sub_category').val('').trigger('change');

        document.getElementById("taxable").checked = true;
        taxes = <?php echo json_encode($this->mSettings->other_items_tax); ?>;
        $.each(taxes.split(","), function(i,e){
            $("#tax_id option[value='" + e + "']").prop("selected", true);
        });

        jQuery('#o_quantity').val('');

        jQuery("#o_quantity").prop('disabled', false);
        if (document.getElementById('universal')) {
            document.getElementById("universal").checked = false;
        }
        jQuery('#titclienti').html("<?php echo lang('add'); ?> <?php echo lang('other');?>");

        jQuery('#footerClient1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button type="submit" class="btn btn-success" id="submit" data-mode="add" form="other_form" value="Submit">'+"<?php echo lang('submit');?>"+'</button>');
    });
      <?php endif; ?>

      <?php if($this->Admin || $GP['other-edit']): ?>
       jQuery(document).on("click", "#modify", function () {
        jQuery('#titclienti').html('<?php echo lang('edit'); ?> <?php echo lang('client_title'); ?>');
        
        var num = jQuery(this).data("num");
            jQuery.ajax({
                type: "POST",
                url: base_url + "panel/other/getOtherByID",
                data: "id=" + encodeURI(num) + "&token=<?php echo $_SESSION['token'];?>",
                cache: false,
                dataType: "json",
                success: function (data) {
                    jQuery('#titclienti').html("<?php echo lang('edit'); ?> <?php echo lang('Other Product');?>");

                    jQuery('#o_name').val(data.name);
                    jQuery('#o_upc_code').val(data.upc_code);
                    jQuery('#o_price').val(data.price);
                    jQuery('#o_max_discount').val(data.max_discount);
                    jQuery('#cost').val(data.cost);
                    jQuery('#tax_id').val('');
                    jQuery('#category').val(data.category).trigger('change');
                    jQuery('#sub_category').val(data.sub_category).trigger('change');
                    jQuery('#alert_quantity').val(data.alert_quantity).trigger('change');
                    jQuery('#warranty_id').val(data.warranty_id).trigger('change');
                    if (data.taxable == 1) {
                        document.getElementById("taxable").checked = true;
                    }else{
                        $('#tax_box').hide();
                    }

                    if (data.keep_stock == 1) {
                        $('.cost_div').slideUp();
                        $('#cost').prop('disabled', true);
                        $('#cost').prop('required', false);
                        $('.serial_div').slideDown();
                    }else{
                        $('.cost_div').slideDown();
                        $('#cost').prop('disabled', false);
                        $('#cost').prop('required', true);
                        $('.serial_div').slideUp();
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
                    if (data.cash_out == 1) {
                        document.getElementById("cash_out").checked = true;
                    }else{
                        document.getElementById("cash_out").checked = false;
                    }
                    if (data.variable_price == 1) {
                        $('#o_price_div').slideUp();
                        $('#o_price').prop('disabled', true);
                        document.getElementById("variable_price").checked = true;
                    }else{
                        $('#o_price_div').slideDown();
                        $('#o_price').prop('disabled', false);
                        document.getElementById("variable_price").checked = false;
                    }
                    if (data.keep_stock == 1) {
                        document.getElementById("keep_stock").checked = true;
                    }else{
                        document.getElementById("keep_stock").checked = false;
                    }

                    jQuery('#note').val(data.note);
                    jQuery('#o_discount_type').val(data.discount_type);

                    jQuery('#footerClient1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button type="submit" id="submit" class="btn btn-success" data-mode="modify" form="other_form" value="Submit" data-num="' + encodeURI(num) + '"><i class="fas fa-save"></i>'+"<?php echo lang('submit');?>"+'</button>')
                }
            });
        });
        <?php endif; ?>
        // process the form
       <?php if($this->Admin || $GP['other-add'] || $GP['other-edit']): ?>

        $('#other_form').on( "submit", function(event) {
            event.preventDefault();
            var mode = sanitizer.sanitize(jQuery('#submit').data("mode"));
            var id = sanitizer.sanitize(jQuery('#submit').data("num"));
            var name = sanitizer.sanitize(jQuery('#o_name').val());
            
            //validate
            var valid = true;
            if (valid) {
                var url = "";
                var dataString = "";

                if (mode == "add") {
                    url = base_url + "panel/other/add";
                    dataString = $('#other_form').serialize();
                    jQuery.ajax({
                        type: "POST",
                        url: url,
                        data: dataString,
                        cache: false,
                        success: function (data) {
                            data = JSON.parse(data);
                            if (data.success) {
                                toastr['success']("<?php echo lang('add');?>", "<?php echo lang('Other Product');?>: " + name + " <?php echo lang('added');?>");
                                setTimeout(function () {
                                    $('#othermodal').modal('hide');
                                    $('#dynamic-table').DataTable().ajax.reload();
                                }, 500);
                            }else{
                                bootbox.alert(data.message);
                            }
                        }
                    });
                } else {
                    url = base_url + "panel/other/edit";
                    dataString = $('#other_form').serialize() + "&id=" + encodeURI(id);
                    jQuery.ajax({
                        type: "POST",
                        url: url,
                        data: dataString,
                        cache: false,
                        success: function (data) {
                            data = JSON.parse(data);
                            if (data.success) {
                                toastr['success']("<?php echo lang('edit');?>", "<?php echo lang('Other Product');?>: " + name + "<?php echo lang('updated');?>");
                                setTimeout(function () {
                                    $('#othermodal').modal('hide');
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

        <?php endif; ?>
    $('#random_gen_other').on( "click", function(){
        $(this).parent('.input-group').children('input').val(generateCardNo(8));
    });
jQuery(document).ready( function($) {
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