<script>
    function actions(x) {
        var pqc = x.split("___");
        if (pqc[1] == 1) {
            var button = "<li><a id='toggle' data-num='"+pqc[0]+"' data-mode='enable'><i class='fas fa-toggle-on'></i> "+lang.enable+"</a></li>";
        }else{
            var button = "<li><a id='toggle' data-num='"+pqc[0]+"' data-mode='disable'><i class='fas fa-toggle-off'></i> "+lang.disable+"</a></li>";
        }
        var return_var = "<div class='btn-group'><button type=\"button\" class=\"btn btn-default btn-xs btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-expanded=\"true\"><?php echo lang('Actions');?> <span class=\"caret\"></button><ul class=\"dropdown-menu pull-right\" role=\"menu\">";
        return_var += "<li><a data-dismiss='modal' id='modify' href='#aplanmodal' data-toggle='modal' data-num='"+pqc[0]+"'><i class='fas fa-edit'></i> <?php echo lang('Edit');?></a></li>";
        return_var += button;
        return_var += '</ul></div>';

        return return_var;
    }
    function items(x) {
        items = JSON.parse(x);
        var string = "";
        $.each(items, function () {
            string += "<strong>"+this.name + "</strong> ("+this.code+")<br>";
        });
        return string;
    }

    $(document).ready(function () {
        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[0, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo base_url(); ?>panel/settings/getAllAPlans/<?php echo $toggle_type; ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            }, 
            "aoColumns": [
                null,
                {mRender: items},
                {mRender: actions},
            ],
        });
    });
    jQuery(document).on("click", "#toggle", function () {
        var num = jQuery(this).data("num");
        var mode = jQuery(this).data("mode");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/settings/warranty_toggle",
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
                toastr['success']("<?php echo lang('Toggle');?>", data.toggle);
                $('#dynamic-table').DataTable().ajax.reload();
            }
        });
    });
    
</script>

<!-- ============= MODAL MODIFICA CLIENTI ============= -->
<div class="modal fade" id="aplanmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="titclienti"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <p class="tips custip"></p>
                    <div class="row">
                    <form id="aplan_form" class="parsley-form" method="post">
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <div class="form-group">
                                   	<label><?php echo lang('Name');?></label>
                                    <input id="name" name="name" value="" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 input-field">
                                <div class="form-group all">
                                    <div class="checkbox-styled checkbox-inline">
                                        <input type="hidden"  name="universal" value="0">
                                        <input type="checkbox" id="universal" name="universal" value="1">
                                        <label for="universal"><?php echo lang('is_universal'); ?></label>
                                    </div>
                                </div>
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
                                <div class="control-group table-group">

                                    <div class="controls table-controls">
                                        <table id="poTable"
                                               class="table items table-striped table-bordered table-condensed table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="col-md-10"><?php echo lang('product_name'); ?>(<?php echo lang('product_code'); ?>)</th>
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
<button href="#aplanmodal" class="add_warranties btn btn-primary">
    <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo lang('Activation Plan');?>
</button>

<?php echo form_open('panel/settings/activation_plan_add', 'id="action-form"'); ?>

<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('settings/activation_plans');?></h3>
      <div class="box-tools pull-right">
        <div class="btn-group">
            <a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>panel/settings/activation_plans/"><?php echo lang('All');?></a>
            <a class="btn btn-sm btn-success" href="<?php echo base_url(); ?>panel/settings/activation_plans/enabled"><i class='fas fa-toggle-on'></i> <?php echo lang('enabled');?></a>
            <a class="btn btn-sm btn-danger" href="<?php echo base_url(); ?>panel/settings/activation_plans/disabled"><i class='fas fa-toggle-off'></i> <?php echo lang('disabled');?></a>
        </div>
      </div>
  </div>
  <div class="box-body">
    <div class="table-responsive">
            <table class=" compact table table-bordered table-striped" id="dynamic-table" width="100%">
                <thead>
                    <tr>
                        <th><?php echo lang('Name');?></th>
                        <th><?php echo lang('Items');?></th>
                        <th><?php echo lang('actions'); ?></th>
                    </tr>
                </thead>
            </table>
            
        </div>
  </div>
</div>
<style type="text/css">
    .ui-widget-content{
        z-index: 9999;
    }
</style>
<script type="text/javascript">
    var count = 1, total_count = 1;
    var total = 0, shipping = 0;
    $(document).ready(function () {
        $("#add_item").autocomplete({
            source: function (request, response) {
                $.ajax({
                    type: 'get',
                    url: '<?php echo site_url('panel/pos/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
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
                    var row = add_transfer_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    bootbox.alert('<?php echo lang("nothing_found"); ?>');
                }
            }
        });


       
    });
    /* -----------------------------
     * Add Tranfer Item Function
     * @param {json} item
     * @returns {Boolean}
     ---------------------------- */
    function add_transfer_item(item) {
        if (count == 1) {
            titems = {};
        }
        console.log(item);
        if (item == null)
            return;
        var universal = parseInt(item.row.universal);
        var is_universal = document.getElementById('universal').checked;

        if (is_universal && !universal) {
            bootbox.alert("<?php echo lang('check_universal_error');?>");
            return false;
        }

        var item_id = item.item_id;
        titems[item_id] = item;
        titems[item_id].order = new Date().getTime();
        localStorage.setItem('titems', JSON.stringify(titems));
        loadItems();
        return true;
    }
    $(document).on('click', '#universal', function () {
        if ($(this).is(':checked')) {
            if (typeof titems !== 'undefined' && !jQuery.isEmptyObject(titems)) {
                var all_universal = true;
                $.each(titems, function () {
                    this.row.universal = parseInt(this.row.universal);
                    if (!this.row.universal) {
                        all_universal = false;
                    }
                });
                if (!all_universal) {
                    bootbox.alert("<?php echo lang('should_be_universal');?>");
                    return false;
                }
            }
        }
    });
    $(document).on('click', '.podel', function () {
        var row = $(this).closest('tr');
        var item_id = row.attr('data-item-id');
        delete titems[item_id];
        row.remove();
        if(titems.hasOwnProperty(item_id)) { } else {
            localStorage.setItem('titems', JSON.stringify(titems));
            loadItems();
            return;
        }
    });




    function loadItems() {
        if (localStorage.getItem('titems')) {
            total = 0;
            count = 1;
            an = 1;
          
            $("#poTable tbody").empty();
            titems = JSON.parse(localStorage.getItem('titems'));
            sortedItems = _.sortBy(titems, function(o){return [parseInt(o.order)];}) ;
            var order_no = new Date().getTime();

            $.each(sortedItems, function () {
                var item = this;
                var type = item.row.type;
                var item_id = item.item_id;
                var product_id = item.row.id,item_code = item.row.code,item_name = item.row.name.replace(/"/g, "&#034;").replace(/'/g, "&#039;")
                var row_no = (new Date).getTime();
                var newTr = $('<tr id="row_' + row_no + '" class="row_' + item_id + '" data-item-id="' + item_id + '"></tr>');
                tr_html = '<td class="col-md-10"><input name="product_id[]" type="hidden" class="rid" value="' + product_id + '"><input name="product_type[]" type="hidden" value="' + item.row.type + '"><input name="product[]" type="hidden" class="rcode" value="' + item_code + '"><input name="product_name[]" type="hidden" class="rname" value="' + item_name + '"><span class="sname" id="name_' + row_no + '">' + item_code +' - '+ item_name +' </span></td>';
                tr_html += '<td class="text-center col-md-2"><i class="fas fa-times tip podel" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
                newTr.html(tr_html);
                newTr.prependTo("#poTable");
                count+=1;
                an++;
            });
        }
    }
    
    function getProduct(code,type) {
        $.ajax({
            type: "get",
            url: "<?php echo site_url('panel/pos/getProductDataByTypeAndID')?>",
            data: {code: code, type: type},
            dataType: "json",
            success: function (data) {
                if (data !== null) {
                    $.each(data, function () {
                        add_transfer_item(this);
                    });
                } else {
                    bootbox.alert(lang.nothing_found);
                }
            }
        });
    }
    jQuery(".add_warranties").on("click", function (e) {
        $('#aplanmodal').modal('show');

        jQuery('#name').val('');
        if (document.getElementById('universal')) {
            document.getElementById("universal").checked = false;
        }
        titems = {};
        localStorage.removeItem('titems');
        $('#poTable tbody').empty();

        jQuery('#titclienti').html("<?php echo lang('add'); ?> ");
        jQuery('#footerClient1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button type="submit" class="btn btn-success" id="submit" data-mode="add" form="aplan_form" value="Submit">Submit</button>');
    });
    jQuery(document).on("click", "#modify", function () {
        jQuery('#titclienti').html('<?php echo lang('edit'); ?> <?php echo lang('Special Activation Plan');?>');
        
        var num = jQuery(this).data("num");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/settings/getAPlanByID",
            data: "id=" + encodeURI(num) + "&token=<?php echo $_SESSION['token'];?>",
            cache: false,
            dataType: "json",
            success: function (data) {
            	var plan = data.data;
                jQuery('#titclienti').html("<?php echo lang('edit'); ?><?php echo lang('Special Activation Plan');?> ("+name+")");
                jQuery('#name').val(plan.name);
                titems = {};
                if (plan.universal == 1) {
                    $('#universal').attr('checked', true); // true
                }else{
                    $('#universal').attr('checked', false); // true
                }

                $.each(plan.items, function () {
                    getProduct(this.id,this.type);
                });

                jQuery('#footerClient1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button type="submit" id="submit" class="btn btn-success" data-mode="modify" form="aplan_form" value="Submit" data-num="' + encodeURI(num) + '"><i class="fas fa-save"></i><?php echo lang('submit');?></button>')
            }
        });

    });
    // process the form
    $('#aplan_form').on( "submit", function(event) {
        event.preventDefault();
        var mode = jQuery('#submit').data("mode");
        var id = jQuery('#submit').data("num");
        form = $(this);
        var valid = form.parsley().validate();
        if (valid) {
            var url = "";
            var dataString = "";

            if (mode == "add") {
                url = base_url + "panel/settings/aplan_add";
                dataString = $('#aplan_form').serialize();
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        toastr['success']("<?php echo lang('add');?>", " <?php echo lang('Special Activation Plan');?>: " + name + " <?php echo lang('added');?>");
                        setTimeout(function () {
                            $('#aplanmodal').modal('hide');
                            $('#dynamic-table').DataTable().ajax.reload();
                        }, 500);
                    }
                });
            } else {
                url = base_url + "panel/settings/aplan_edit";
                dataString = $('#aplan_form').serialize() + "&id=" + encodeURI(id);
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        toastr['success']("<?php echo lang('edit');?>", " <?php echo lang('Special Activation Plan');?>: " + name + "<?php echo lang('updated');?>");
                        setTimeout(function () {
                            $('#aplanmodal').modal('hide');
                            $('#dynamic-table').DataTable().ajax.reload();
                        }, 500);
                    }
                });
            }
        }
        return false;
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
});		

</script>