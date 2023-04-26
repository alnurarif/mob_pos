<script type="text/javascript">
	$(document).ready(function() {
    loadItems();
    $('#sticker').hide();
    if (!localStorage.getItem('count_start')) {
        localStorage.setItem('count_start', <?php echo json_encode(false); ?>);
    }
    $(document).on('click', '#start_count', function () {
        localStorage.setItem('count_start', <?php echo json_encode(true); ?>);
        $('#sticker').slideDown();
        $('#start_count').hide();
        loadItems(true);
    });
    var start_count = localStorage.getItem('count_start');
    if (start_count == 'true') {
        $('#sticker').show();
        $('#start_count').hide();
        loadItems(true);
    }
    $("#count_item").autocomplete({
        source: function (request, response) {
            var term = (request.term);
            var data = [];
            
            $.each(countitems, function () {
                console.log(this);
                var serial = this.serial;
                var code = this.code;
                if (parseInt(this.is_serialized) == 1) {

                    if (term == code) {
                        bootbox.alert("<?php echo lang('You need to scan the serial number for a serialized inventory');?>");
                        return false;
                    }

                    var matches = serial && serial.indexOf(term) >= 0 ? true : false;
                    if (matches) {
                        var prod = this;
                        prod['value'] = this.id;
                        prod['label'] = this.name + ' (' + this.serial + ')';
                        data.push(prod);
                    }

                }else{
                    var matches = code && code.indexOf(term) >= 0 ? true : false;
                    if (matches) {
                        var prod = this;
                        prod['value'] = this.id;
                        prod['label'] = this.name + ' (' + this.code + ')';
                        data.push(prod);
                    }
                }
            });

            if (data.length === 0) {
                var prod = {};
                prod['product_id'] = -1;
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
                bootbox.alert('<?php // echo lang("nothing_found"); ?>', function () {
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
            if (parseInt(ui.item.product_id) > 0) {
                var row = add_item(ui.item);
                $(this).val('');
            } else {
                var row = add_wrong_upc(ui.item.label);
                $(this).val('');
            } 
        }
    });
});
wrong_upcs = [];
function add_wrong_upc(item) {
    wrong_upcs.push(item);
    localStorage.setItem('wrong_upcs', JSON.stringify(wrong_upcs));
}
function add_item(item) {
    if (item == null)
        return;
    var item_id = item.item_id;
    if (countitems[item_id]) {
        row = countitems[item_id];
        if (parseInt(item.is_serialized) == 1) {
           if (row.counted_qty < row.qty) {
                countitems[item_id].counted_qty = parseFloat(countitems[item_id].counted_qty) + 1;
            }else{
                bootbox.alert("<?php echo lang('Counted stock cannot be more than Expected stock!');?>")
            } 
        }else{
            countitems[item_id].counted_qty = parseFloat(countitems[item_id].counted_qty) + 1;
        }
        
    } 
    localStorage.setItem('countitems', JSON.stringify(countitems));
    loadItems(start_count);
    return true;
}

function loadItems(editable = false) {
    if (localStorage.getItem('countitems')) {
        total = 0;
        count = 1;
        an = 1;
        $("#countTable tbody").empty();
        countitems = JSON.parse(localStorage.getItem('countitems'));
        $.each(countitems, function (x, item) {
            row_no = item.row_no;
            item_id = item.item_id;
            item_qty = item.qty;
            counted_qty = item.counted_qty;
            serial = item.serial ? item.serial : '<span class="label label-info">'+"<?php echo lang('Non-Serialized Stock');?>"+'</span>';
            diff = item_qty - counted_qty;

            var total_stock = item.stock_data
            total_stock = total_stock.split(',');

            if (!parseInt(item.is_serialized)) {
                var total_cost = 0;
                var selected_cost = 0;
                var sstc_ids = [];
                for (var sstc = 0; sstc <= total_stock.length-1; sstc++) 
                {
                    sstc_data = (total_stock[sstc]).split('____');
                    total_cost += parseFloat(sstc_data[1]);

                    if (sstc < counted_qty) {
                        selected_cost += parseFloat(sstc_data[1]);
                        sstc_ids.push(sstc_data[0]);
                    }
                }
            }else{
                var total_cost = 0;
                var selected_cost = 0;
                var sstc_ids = [];
                $.each(countitems, function () {
                    if (this.product_id == item.product_id && this.type == item.type) {
                        total_cost += parseFloat(this.cost);
                        selected_cost += this.counted_qty > 0 ? parseFloat(this.cost) : 0;
                    }
                });
            }
            
            var row_no = (new Date).getTime();
            var newTr = $('<tr id="row_' + row_no + '" class="row_' + item_id + '" data-item-id="' + item_id + '"></tr>');
            tr_html = '<td><input class="form-control" name="product_name[]" type="hidden" value="'+item.name+'"><input class="form-control" name="product_id[]" type="hidden" value="'+item.product_id+'"><input class="form-control" name="product_type[]" type="hidden" value="'+item.type+'"><input class="form-control" name="total_cost[]" type="hidden" value="'+total_cost+'"><input class="form-control" name="selected_cost[]" type="hidden" value="'+selected_cost+'">'+item.name+'</td>';
            tr_html += '<td><input class="form-control" name="humanized_type[]" type="hidden" value="'+item.humanized_type+'">'+item.humanized_type+'</td>';
            tr_html += '<td><input class="form-control" name="category[]" type="hidden" value="'+item.category+'">'+item.category+'</td>';
            tr_html += '<td><input class="form-control" name="sub_category[]" type="hidden" value="'+item.sub_category+'">'+item.sub_category+'</td>';
            tr_html += '<td><input class="form-control" name="code[]" type="hidden" value="'+item.code+'">'+item.code+'</td>';
            tr_html += '<td><input class="form-control" name="serial[]" type="hidden" value="'+item.serial+'">'+serial+'</td>';
            if (editable) {
                tr_html += '<td><input class="form-control rquantity" name="counted_qty[]" type="number" value="'+counted_qty+'"></td>';
            }else{
                tr_html += '<td><input class="form-control" name="counted_qty[]" type="hidden" value="'+counted_qty+'">'+counted_qty+'</td>';
            }
            tr_html += '<td><input class="form-control" name="item_qty[]" type="hidden" value="'+item_qty+'">'+item_qty+'</td>';
            tr_html += '<td>'+(diff)+'</td>';
            newTr.html(tr_html);
            newTr.prependTo("#countTable");
            count += parseFloat(item_qty);
            an++;
        });

        return true;
    }
    return false;
}

function loadWrongUPCs() {
    if (localStorage.getItem('wrong_upcs')) {
        $("#wrong_upcs_table").show();
        $("#wUPCTable tbody").empty();
        wrong_upcs = JSON.parse(localStorage.getItem('wrong_upcs'));
        $.each(wrong_upcs, function () {
            var newTr = $('<tr></tr>');
            tr_html = '<td><input class="form-control" name="wrong_upc_name[]" type="hidden" value="'+this+'">'+this+'</td>';
            tr_html += '<td><textarea name="wrong_upc_explanation[]" rows="4" class="form-control"></textarea></td>';
            newTr.html(tr_html);
            newTr.prependTo("#wUPCTable");
        });
        return true;
    }
    $("#wrong_upcs_table").hide();
    return false;
}

/* --------------------------
 * Edit Row Quantity Method
 -------------------------- */
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
    var new_qty = parseInt($(this).val()),
    item_id = row.attr('data-item-id');
    item = (countitems[item_id]);
    if (parseInt(item.is_serialized) == 1) {
       if (new_qty <= item.qty) {
            countitems[item_id].counted_qty = new_qty;
        }else{
            bootbox.alert("<?php echo lang('Counted stock cannot be more than Expected stock!');?>")
        } 
    }else{
        countitems[item_id].counted_qty = new_qty;
    }
    localStorage.setItem('countitems', JSON.stringify(countitems));
    loadItems(start_count);
});

$(document).on('click', '#commitCount', function () {
    event.preventDefault();
    loadWrongUPCs();
    $('#commitCountModal').modal('show');
});

$(document).on('click', '#submitCommit', function () {
    event.preventDefault();
    $('#upcFormAdd').html($('#wrong_upcs_table').find('input,textarea').clone());
    $('#count_form').submit();

});
</script>

<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo lang('inventory/count_stock');?></h3>
    </div>
    <div class="box-body">
        <div class="col-md-12" id="sticker">
            <div class="well well-sm">
                <div class="form-group" style="margin-bottom:0;">
                    <div class="input-group wide-tip">
                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                            <i class="fas fa-2x fa-barcode addIcon"></i></a></div>
                        <?php echo form_input('count_item', '', 'class="form-control input-lg" id="count_item" placeholder="' . 'Count by Scanning UPC Code or Serial Number' . '"'); ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <button class="btn btn-primary bottom10" id="start_count"><?php echo lang('Start counting');?></button>

        <?php echo form_open('panel/inventory/count_save', 'id="count_form"'); ?>
        <div id="upcFormAdd"></div>
        <table class="table table-striped" id="countTable">
            <thead>
                <tr>
                    <th><?php echo lang('Name');?></th>
                    <th><?php echo lang('Type');?></th>
                    <th><?php echo lang('Category');?></th>
                    <th><?php echo lang('Subcategory');?></th>
                    <th><?php echo lang('UPC Code');?></th>
                    <th><?php echo lang('Serial Number');?></th>
                    <th><?php echo lang('Counted Quantity');?></th>
                    <th><?php echo lang('Expected Quantity');?></th>
                    <th><?php echo lang('Difference');?></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
    <div class="box-footer">
        <button class="btn btn-primary" role="button" id="commitCount"><?php echo lang('Commit Count');?></button>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="commitCountModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php echo lang('Commit Count');?></h4>
      </div>
      <div class="modal-body">
            <fieldset id="wrong_upcs_table">
                <legend><?php echo lang('Wrong UPC(s)');?></legend>
                 <table class="table table-striped" id="wUPCTable">
                    <thead>
                        <tr>
                            <th><?php echo lang('Code');?></th>
                            <th><?php echo lang('Explanation');?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </fieldset>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang('Close');?></button>
        <button type="button" class="btn btn-primary" id="submitCommit"><?php echo lang('Save changes');?></button>
      </div>
    </div>
  </div>
</div>
