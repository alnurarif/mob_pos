<?php
if ($this->uri->segment(4) == 'sent' or $this->uri->segment(4) == 'received') {
    $v = '&status='.$this->uri->segment(4);
}else{
    $v = '';
}
if ($this->input->post('date_range')) {
    $dr = json_decode($this->input->post('date_range'));
    $v .= "&start_date=" . $dr->start;
    $v .= "&end_date=" . $dr->end;
}

?>

<style type="text/css">
    .ui-widget-content{
        z-index: 9999;
    }
</style>
<script>
    var activeStore = <?php echo $this->activeStore; ?>;
    function status(x) {
        var pqc = x.split('____');
        if (parseInt(pqc[1]) === activeStore) {

            if (pqc[2] == 'sent') {
                return '<button class="complete_transfer btn btn-primary btn-sm" data-num="'+pqc[0]+'">'+lang.received+'</button>';
            } else {
                return "<span class='label label-success'>"+pqc[2]+"</span>";
            }
        } else {
            if (pqc[2] == 'sent') {
                return "<span class='label label-info'>"+pqc[2]+"</span>";
            } else {
                return "<span class='label label-success'>"+pqc[2]+"</span>";
            }
        }

    }


    $(document).ready(function () {
        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[3, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo base_url(); ?>panel/transfers/getAllTransfers/?v=1<?php echo $v; ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            }, 
            "aoColumns": [
                {mRender: fld},
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                {mRender: status},
            ],
        });
    });


</script>

<!-- ============= MODAL MODIFICA CLIENTI ============= -->
<div class="modal fade" id="transfers_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                        <?php echo form_open('panel/transfers/add_transfer', 'id="transfers_form"'); ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo lang('Invoice Transfer Number');?></label>
                                    <?php echo form_input('transfer_code', '', 'class="form-control" required'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo lang('Recieving Store');?></label>
                                    <select class="form-control" id="receiving_store" name="receiving_store" required="required" style="width:100%;">
                                        <option selected disabled><?php echo lang('select_placeholder');?></option>
                                        <?php foreach ($this->mStores as $store): ?>
                                            <?php if (!((int)$this->activeStore == (int)trim($store['id']))): ?>
                                                <option value="<?php echo $store['id']; ?>"><?php echo escapeStr($store['name']); ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                           
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo lang('Shipping Cost');?></label>
                                    <?php echo form_input('shipping_cost', '', 'class="form-control input-tip" id="tshipping"'); ?>
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
                                        <?php echo form_dropdown('shipping_provider', $dp_p, '' ,'class="form-control" id="provider_select" required style="width: 100%"'); ?>
                                    </div>
                                    <div class="inp_provider">
                                        <input id="provider_input" name="provider_input" type="text" class="validate form-control">
                                    </div>
                                </div>
                            </div>
                                    <div class="clearfix"></div>
                            
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
                                    <label class="table-label"><?php echo lang('Transfer Items');?></label>

                                    <div class="controls table-controls">
                                        <table id="poTable"
                                               class="table items table-striped table-bordered table-condensed table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="col-md-4"><?php echo lang('product_name'); ?>(<?php echo lang('product_code'); ?>)</th>
                                                    <th class="col-md-1"><?php echo lang('Total');?> <?php echo lang('quantity'); ?></th>
                                                    <th class="col-md-1"><?php echo lang('quantity'); ?></th>
                                                   
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

                                <div id="bottom-total" class="well well-sm" style="margin-bottom: 0;">
                                    <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                                        <tr class="warning">
                                            <td><?php echo lang('items') ?> <span class="totals_val pull-right" id="titems">0</span></td>
                                            <td><?php echo lang('total') ?> <span class="totals_val pull-right" id="total">0.00</span></td>
                                            <td><?php echo lang('shipping') ?> <span class="totals_val pull-right" id="tship">0.00</span></td>
                                            <td><?php echo lang('grand_total') ?> <span class="totals_val pull-right" id="gtotal">0.00</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="footerClient1">
                  
            </div>
        </div>
    </div>
</div>



<!-- ============= MODAL MODIFICA CLIENTI ============= -->
<div class="modal modal-primary-filled fade in animated" id="transfers_serialed" tabindex="-1" role="dialog" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="transfers_serialed_title"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <p class="tips custip"></p>
                    <div class="row">
                        <?php echo form_open('', 'id="serial_form"'); ?>
                            <div class="clearfix"></div>
                            <div class="col-md-12" id="sticker">
                                <div class="well well-sm">
                                    <div class="form-group" style="margin-bottom:0;">
                                        <div class="input-group wide-tip">
                                            <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                                <i class="fas fa-2x fa-barcode addIcon"></i></a></div>
                                            <?php echo form_input('add_serial', '', 'class="form-control input-lg" id="add_serial" placeholder="' . lang('add_product') . '"'); ?>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="control-group table-group">
                                    <label class="table-label"><?php echo lang('Serial');?></label>

                                    <div class="controls table-controls">
                                        <table id="TTable"
                                               class="table items table-striped table-bordered table-condensed table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th><?php echo lang('Serial Number');?></th>
                                                    <th><?php echo lang('Price');?></th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                            <tfoot></tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="transfers_serialed_footer">
                <button type="button" class="btn btn-success" data-dismiss="modal" aria-hidden="true"><?php echo lang('Submit');?></button>
            </div>
        </div>
    </div>
</div>




  
<button href="#transfers_modal" class="add_transfer btn btn-primary">
    <i class="fas fa-plus-circle"></i> <?php echo lang('Transfer Stock');?>
</button>
<div class="pull-right">
    <div class="btn-group">
        <a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>panel/transfers/"><?php echo lang('All');?></a>
        <a class="btn btn-sm btn-info" href="<?php echo base_url(); ?>panel/transfers/index/sent"><?php echo lang('Sent');?></a>
        <a class="btn btn-sm btn-success" href="<?php echo base_url(); ?>panel/transfers/index/received"><?php echo lang('Received');?></a>
    </div>
</div>

<section class="panel">
    <div class="panel-body">
    <?php echo form_open(""); ?>
<div class="form-group">
    <label><?php echo lang('Date Range');?></label>
    <?php echo form_input('date_range_o', (isset($_POST['date_range_o']) ? $_POST['date_range_o'] : ""), 'class="form-control derp" id="daterange"'); ?>
    <input type="hidden" name="date_range" class="date_range" id="date_range" value='<?php echo (isset($_POST['date_range']) ? htmlspecialchars($_POST['date_range']) : "");?>'>
</div>

<div class="form-group">
    <div
        class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
</div>
<?php echo form_close(); ?>
        <div class="table-responsive">
            <table style="width: 100%;" class=" compact table table-bordered table-striped" id="dynamic-table">
                <thead>
                    <tr>
                        <th><?php echo lang('Date');?></th>
                        <th><?php echo lang('Transfer Number');?></th>
                        <th><?php echo lang('Shipping Provider - Track Code');?></th>
                        <th><?php echo lang('Sending Store');?></th>
                        <th><?php echo lang('Recieving Store');?></th>
                        <th><?php echo lang('Product Name');?></th>
                        <th><?php echo lang('Quantity');?></th>
                        <th><?php echo lang('Total Cost');?></th>
                        <th><?php echo lang('Shipping Cost');?></th>
                        <th><?php echo lang('Grand Total');?></th>
                        <th><?php echo lang('Status');?></th>
                    </tr>
                </thead>
            </table>
            
        </div>
    </div>
</section>

<div class="modal fade" id="complete_transfer_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="titclienti"><?php echo lang('Recieve Transfer');?></h4>
            </div>
            <div class="modal-body" id="modal_body_transfer">
                <center><img src="<?php echo base_url(); ?>assets/images/loading_bar.gif"></center>
            </div>
            <div class="modal-footer" id="footerClient1">
                  
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).on("click", ".complete_transfer", function() {
        var num = $(this).data('num');
        $('#complete_transfer_modal').modal('show');
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/transfers/complete_modal",
            data: "id=" + num,
            cache: false,
            dataType: "html",
            success: function(data) {
                console.log(data);
                $('#modal_body_transfer').empty();
                $('#modal_body_transfer').append(data);

            }
        });
    });
      $(document).on('click', '.add_to_stock', function (event) {
       event.preventDefault();
       var id = $(this).data('num');
       var transfer_id = $(this).data('transfer_id');
       jQuery.ajax({
            type: "POST",
            url: base_url + "panel/transfers/completed",
            data: "id=" + encodeURI(id) + "&transfer_id=" + encodeURI(transfer_id),
            cache: false,
            dataType: "json",
            success: function (data) {
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
                toastr['success']("<?php echo lang('Added to Stock');?>");
                $('#tree_'+id).html('<span class="label label-success"><?php echo lang('Added');?></span>');
                $('#treeC_'+id).html('');
                $('#dynamic-table').DataTable().ajax.reload();
            }
        });
    });

</script>

<script type="text/javascript">
    var count = 1, total_count = 1;
    var total = 0, shipping = 0;
    $('#tshipping').focus(function () {
        old_shipping = $(this).val();
    }).on("change", function () {
        if (!is_numeric($(this).val())) {
            $(this).val(old_shipping);
            bootbox.alert('unexpected_value');
            return;
        } else {
            shipping = $(this).val() ? parseFloat($(this).val()) : '0';
        }
        var gtotal = total + shipping;
        $('#gtotal').text(formatMoney(gtotal));
        $('#tship').text(formatMoney(shipping));
    });
    jQuery(".add_transfer").on("click", function (e) {
        $('#transfers_modal').modal('show');
        
        jQuery('#transfers_modal :input').val('');
        jQuery('#titclienti').html("Transfer Stock");
        jQuery('#footerClient1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button type="submit" class="btn btn-success" id="submit" role="button" form="transfers_form" value="Submit"><?php echo lang('Submit');?></button>');
    });
    
    var old_row_qty;
    $(document).on("focus", '.rquantity', function () {
        old_row_qty = $(this).val();
    }).on("change", '.rquantity', function () {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val()) || parseFloat($(this).val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert('unexpected_value');
            return;
        }
        max = $(this).attr('max');
        if (parseFloat($(this).val()) > max) {
            $(this).val(old_row_qty);
            bootbox.alert("<?php echo lang('Quantity cannot be more than ');?>"+max);
            return;
        }
        var new_qty = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');

        titems[item_id].row.selected_qty = parseInt(new_qty);
        localStorage.setItem('titems', JSON.stringify(titems));
        loadItems();
    });


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

    $(document).ready(function () {
        $("#add_item").autocomplete({
            source: function (request, response) {
                $.ajax({
                    type: 'get',
                    url: '<?php echo site_url('panel/transfers/suggestions'); ?>',
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
        $("#add_serial").autocomplete({
            source: function (request, response) {
                var term = (request.term);
                var data = [];
                item_id = $('#add_serial').data('item-id');
                item = titems[item_id];
                stock = item.row.stock_data;
                stock = stock.split(',');
                $.each(stock, function () {
                    stock_result = this.split('____');
                    var serial = stock_result[2];
                    var id = stock_result[0];
                    var price = stock_result[1];
                    var matches = serial.indexOf(term) >= 0 ? true : false;
                    if (matches) {
                        var prod = {};
                        prod['id'] = id;
                        prod['item_id'] = item_id;
                        prod['label'] = serial;
                        prod['price'] = price;
                        data.push(prod);
                    }
                });
                if (data.length === 0) {
                    var prod = {};
                    prod['id'] = 0;
                    prod['label'] = term;
                    data.push(prod);
                }
                response(data);
            },
            minLength: 3,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    bootbox.alert('<?php echo lang("nothing_found"); ?>', function () {
                        $('#count_item').focus();
                    });
                    $(this).removeClass('ui-autocomplete-loading');
                    $(this).val('');
                }
                else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                    $(this).removeClass('ui-autocomplete-loading');
                }
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    bootbox.alert('<?php echo lang("nothing_found"); ?>', function () {
                        $('#count_item').focus();
                    });
                    $(this).removeClass('ui-autocomplete-loading');
                    $(this).val('');
                }
            },
            select: function (event, ui) {
                if (parseInt(ui.item.id) > 0) {
                    var row = add_serial(ui.item);
                } 
                $(this).val('');
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
    if (item == null)
        return;
    var item_id = item.item_id;
    titems[item_id] = item;
    titems[item_id].order = new Date().getTime();
    localStorage.setItem('titems', JSON.stringify(titems));
    loadItems();
    return true;
}

 function add_serial(item) {
    if (item == null)
        return;

    item_id = item.item_id;
    item_stock_id = item.id;
    var row_no = item_stock_id+'__'+item_id;

    witem = titems[item_id];
    serials = witem.serials;
    if (serials) {
        serials = serials.split(',');
        if (jQuery.inArray(item.label, serials) == -1) {
            serials.push(item.label);
        }
        witem.row.selected_qty = serials.length;
        serials = serials.join(',');
    }else{
        serials = [];
        serials.push(item.label);
        witem.row.selected_qty = serials.length;
        serials = serials.join(',');
    }
    witem.serials = serials;
    titems[item_id] = witem;
    localStorage.setItem('titems', JSON.stringify(titems));
    loadItems();

    if (!document.getElementById('row_' + row_no)) {
        var newTr = $('<tr id="row_' + row_no + '" class="row_' + item_id + '" data-item-id="' + item_id + '"></tr>');
        tr_html = '<td>1</td>';
        tr_html += '<td>'+item.label+'</td>';
        tr_html += '<td>'+item.price+'</td>';
        newTr.html(tr_html);
        newTr.prependTo("#TTable");
    }
}


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
        total_count = 1;
        an = 1;
        product_tax = 0;
        invoice_tax = 0;
        product_discount = 0;
        order_discount = 0;
        total_discount = 0;
        $("#poTable tbody").empty();
        titems = JSON.parse(localStorage.getItem('titems'));
        sortedItems = _.sortBy(titems, function(o){return [parseInt(o.order)];}) ;
        var order_no = new Date().getTime();

        $.each(sortedItems, function () {
            var item = this;
            var type = item.row.type;
            var item_id = item.item_id;
            var unit_cost = item.row.cost;
            item.order = item.order ? item.order : order_no++;
            var product_id = item.row.id,item_code = item.row.code,item_name = item.row.name.replace(/"/g, "&#034;").replace(/'/g, "&#039;"),item_qty = parseFloat(item.row.qty), selected_qty = item.row.selected_qty;
            var row_no = (new Date).getTime();
            var total_stock = item.row.stock_data
            total_stock = total_stock.split(',');
            var subtotal_item = 0;
            var is_serialized = parseInt(item.row.is_serialized);
            console.log(item);
            
             if ((is_serialized == 1 && !item.serials)) {
                $('#add_serial').attr('data-item-id', item_id);
                $('#add_serial').attr('data-row_no', row_no);
                $('#transfers_serialed').modal({backdrop: 'static', keyboard: false});
            }

            var sstc_ids = [];
            if (is_serialized == 0) {
                for (var sstc = total_stock.length - 1; sstc >= (total_stock.length-selected_qty); sstc--) {
                    sstc_data = (total_stock[sstc]).split('____');
                    subtotal_item += parseFloat(sstc_data[1]);
                    sstc_ids.push(sstc_data[0]);
                }
            }else{
                if (item.serials) {
                    serials = item.serials;
                    serials = serials.split(',');
                    for (var sstc = 0; sstc < total_stock.length; sstc++) {
                        sstc_data = (total_stock[sstc]).split('____');
                        if (jQuery.inArray(sstc_data[2], serials) > -1) {
                            subtotal_item += parseFloat(sstc_data[1]);
                            sstc_ids.push(sstc_data[0]);
                        }
                    }
                }
            }
            

            var newTr = $('<tr id="row_' + row_no + '" class="row_' + item_id + '" data-item-id="' + item_id + '"></tr>');
            tr_html = '<td><input name="product_id[]" type="hidden" class="rid" value="' + product_id + '"><input name="product_type[]" type="hidden" value="' + item.row.type + '"><input name="product[]" type="hidden" class="rcode" value="' + item_code + '"><input name="product_name[]" type="hidden" class="rname" value="' + item_name + '"><span class="sname" id="name_' + row_no + '">' + item_code +' - '+ item_name +' </span></td>';
            
            tr_html += '<input name="stock_to_transfer[]" type="hidden" value="' + sstc_ids.join(',') + '">';
            tr_html += '<td>' + formatDecimal(item_qty) + '</td>';
            tr_html += '<td><input class="form-control text-center rquantity" name="quantity[]" '+(is_serialized?'readonly':'')+' type="number" min="1" max="'+item_qty+'" value="' + formatDecimal(selected_qty) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="quantity_' + row_no + '" onClick="this.select();"></td>';
            tr_html += '<td class="text-right"><span class="text-right ssubtotal" id="subtotal_' + row_no + '">' + formatMoney(subtotal_item) + '</span></td>';
            tr_html += '<td class="text-center"><i class="fas fa-times tip podel" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
            tr_html += '<input name="subtotal_item[]" type="hidden" value="' + subtotal_item + '">';
            newTr.html(tr_html);
            newTr.prependTo("#poTable");
            total += formatDecimal(subtotal_item);
            count += item_qty;
            total_count += selected_qty;
            an++;
        });

        var col = 1;
        if (site.settings.product_expiry == 1) { col++; }
        var tfoot = '<tr id="tfoot" class="tfoot active"><th colspan="'+col+'">Total</th>'+'<th class="text-center">' + formatNumber(parseFloat(count) - 1) + '</th>'+'<th class="text-center">' + formatNumber(parseFloat(total_count) - 1) + '</th>';
      
        tfoot += '<th class="text-right">'+formatMoney((total))+'</th><th class="text-center"><i class="fas fa-trash" style="opacity:0.5; filter:alpha(opacity=50);"></i></th></tr>';
        $('#poTable tfoot').html(tfoot);

        var gtotal = shipping + total;
        $('#total').text(formatMoney(total));
        $('#titems').text((an-1)+' ('+(parseFloat(count)-1)+')');
        $('#gtotal').text(formatMoney(gtotal));
    }
}


</script>