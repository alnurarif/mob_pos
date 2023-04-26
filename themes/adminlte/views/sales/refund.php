<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<script type="text/javascript">
    var count = 1, an = 1, DT = <?php echo $settings->default_tax_rate ?>, po_edit = 1,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0, shipping = 0, surcharge = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>;
	function formatPOSDecimal(x, d) {
        if (!d) { d = 2; }
        return accounting.formatMoney(x, '', 2, '', '.', "%s%v");
    }

    $(document).ready(function () {
        <?php if ($inv) { ?>
        localStorage.setItem('reitems', JSON.stringify(<?php echo $inv_items; ?>));
        localStorage.setItem('return_surcharge', 0);
        localStorage.setItem('renote', '');
        <?php } ?>
         if (return_surcharge = localStorage.getItem('return_surcharge')) {
            $('#return_surcharge').val(return_surcharge);
        }
        var old_surcharge;
        $(document).on("focus", '#return_surcharge', function () {
            old_surcharge = $(this).val() ? parseFloat($(this).val()) : 0;
        }).on("change", '#return_surcharge', function () {
            var new_surcharge = $(this).val() ? parseFloat($(this).val()) : 0;
            if (!is_valid_discount(new_surcharge)) {
                $(this).val(new_surcharge);
                bootbox.alert('<?php echo lang('unexpected_value'); ?>');
                return;
            }
            localStorage.setItem('return_surcharge', new_surcharge);
            loadItems();
        });

        $(document).on('change', '#renote', function (e) {
            localStorage.setItem('renote', $(this).val());
        });

        // If there is any item in localStorage
	    if (localStorage.getItem('reitems')) {
	        loadItems();
	    }

        
    });
    function loadItems() {
        if (localStorage.getItem('reitems')) {
            items = JSON.parse(localStorage.getItem('reitems'));
            var pp = 0;
            var total_tax = 0;
            total = 0;
            count = 1;
            an = 1;
            product_tax = 0;
            invoice_tax = 0;
            product_discount = 0;
            order_discount = 0;
            total_discount = 0;
            var i = 0;

            $("#reTable tbody").empty();
            $.each(items, function () {
                var row_no = this.row_id;
                var item_id = this.product_id;

                var price = this.price;
                var cost = this.cost;
                var sel_opt = '';
                var item_option = this.row.option_id;
                var discount = this.discount;
                var type = this.row.type;
                var code = this.code;
                var sale_item_id = this.row.sale_item_id;
                $.each(this.options, function (x, y) {
                    if(y.id == item_option) {
                        if (type !== 'repair') {
                            cost = y.cost;
                        }
                        if (type == 'new_phone' || type == 'used_phone') {
                            code = y.name;
                        }

                        sel_opt = y.name;
                        price = y.price;
                        if (type === 'cp') {
                            price = 0-parseFloat(y.cost);
                        }
                    }
                });
                var product_tax = 0;
                var pr_tax = this.pr_tax;
                var pr_tax_val = 0, pr_tax_rate = [];
                var pr_tax_val_fixed = 0;
                if(parseInt(this.taxable) == 1){
                    $.each(pr_tax, function (tax, tax_detaild) {
                        if (tax_detaild !== false) {
                            if (tax_detaild.type == 1) {
                                pr_tax_val += parseFloat(tax_detaild.rate);
                                pr_tax_rate[tax_detaild.id] = formatPOSDecimal(tax_detaild.rate) + '%';
                            } else if (parseInt(tax_detaild.type) == 2) {
                                pr_tax_val_fixed += parseFloat(tax_detaild.rate);
                                pr_tax_rate[tax_detaild.id] = formatPOSDecimal(tax_detaild.rate);
                            }
                        }
                    });
                    percent_tax = formatPOSDecimal(parseFloat(price-discount) * parseFloat(pr_tax_val) / (100), 4);
                    product_tax = parseFloat(percent_tax) + parseFloat(pr_tax_val_fixed);
                }

                if (this.row.type == 'drepairs' || this.row.type == 'crepairs') {
                    product_tax = parseFloat(this.row.tax);
                }
                invoice_tax += product_tax;
                pr_tax_rate = pr_tax_rate.filter(function(e){ return e === 0 || e }).join(', ');
                var subtotal = (parseFloat(price)+parseFloat(product_tax)-parseFloat(discount));
                var newTr = $('<tr id="row_' + row_no + '" class="item_' + this.row.id + '" data-item-id="' + row_no + '"></tr>');
                tr_html = '<td style="width: 40%;"><input name="sale_item_id['+i+']" type="hidden" class="rsiid" value="' + sale_item_id + '"><input name="item_id['+i+']" id="item_id" type="hidden" value="' + this.product_id + '"><input name="item_discount['+i+']" id="item_discount" type="hidden" value="' + discount + '"><input name="item_type['+i+']" id="item_type" type="hidden" value="' + this.type + '"><input name="item_cost['+i+']" id="item_cost" type="hidden" value="' + cost + '"><input name="item_name['+i+']" type="hidden" value="' + this.name + '"><input name="item_code['+i+']" type="hidden" value="' + code + '"><input name="item_serial['+i+']" type="hidden" value="' + this.serial_number + '"><input name="product_option['+i+']" type="hidden" class="roption" value="' + item_option + '"><span class="sname" id="name_' + row_no + '">' + code +' - '+ this.name +(sel_opt != '' ? ' ('+sel_opt+')' : '')+'</span>';
                
                tr_html += '</td>';
                tr_html += '<td style="width: 15%;">'+formatMoney(price)+'<input class="form-control text-center rprice" name="item_price['+i+']" type="hidden" value="' + (price) + '" data-id="' + row_no + '" data-item="' + this.product_id + '" id="item_price_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td style="width: 15%;">'+formatMoney(product_tax)+'<input class="form-control text-center rtax" name="item_tax['+i+']" type="hidden" value="' + formatPOSDecimal(product_tax) + '" data-id="' + row_no + '" data-item="' + this.product_id + '" id="item_price_' + row_no + '" onClick="this.select();"><input class="form-control text-center" name="item_tax_id['+i+']" type="hidden" value="' + encodeURIComponent(JSON.stringify(pr_tax)) + '"></td>';
                tr_html += '<td style="width: 10%;">'+formatMoney(discount)+'</td>';
                tr_html += '<td style="width: 20%;">'+formatMoney((subtotal))+'</td>';
                tr_html += '<td style="width: 15%;"><div style="padding-bottom: 20px;" class="checkbox-styled checkbox-inline checkbox-circle"><input name="add_to_stock['+i+']" type="checkbox" data-item-id="'+row_no+'" class="checkbox add_to_stock"  id="add_to_stock_'+row_no+'" value="1" '+(this.add_to_stock?'checked':'')+'/><label for="add_to_stock_'+row_no+'" class=""></label></div></td>';

                    tr_html += '<td style="width: 5%;" class="text-center"><i class="fas fa-times tip redel" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
                newTr.html(tr_html);
                newTr.prependTo("#reTable");
                total += parseFloat(subtotal);
                count += 1;
                an++;
                i++;
                pp += (parseFloat(price));
                total_tax += product_tax;
                total_discount += discount;
                $('.item_' + item_id).addClass('warning');

            });

            total = formatPOSDecimal(total);
            product_tax = formatPOSDecimal(total_tax);

            // Totals calculations after item addition
            var gtotal = parseFloat(total);
            if (return_surcharge = localStorage.getItem('return_surcharge')) {
                var rs = return_surcharge.replace(/"/g, '');
                if (rs.indexOf("%") !== -1) {
                    var prs = rs.split('%');
                    var percentage = parseFloat(prs[0]);
                    if (!isNaN(prs[0])) {
                        surcharge = parseFloat((gtotal * percentage) / 100);
                    } else {
                        surcharge = parseFloat(rs);
                    }
                } else {
                    surcharge = parseFloat(rs);
                }
            }
            //console.log(surcharge);
            gtotal -= surcharge;
            $('#trs').text(formatMoney(surcharge));
            $('#total').text(formatMoney(total));
            $('#titems').text((an - 1));
            $('#total_items').val((parseFloat(count) - 1));
            $('#tds').text(formatMoney(total_discount));
            $('#ttax2').text(formatMoney(invoice_tax));
            $('#gtotal').text(formatMoney(gtotal));
            $('#gtotal').val(Math.abs(gtotal));
            
        }
    }
    $(document).on('click', '.redel', function () {
        var id = $(this).attr('id');
        $(this).closest('#row_' + id).remove();
        delete items[id];
        if(items.hasOwnProperty(id)) { } else {
            localStorage.setItem('reitems', JSON.stringify(items));
            loadItems();
            return;
        }
    });

        $(document).on('change', '.add_to_stock', function() {
            var row_no = $(this).data('item-id');
            item = (items[row_no]);
            if(this.checked) {
                item.add_to_stock = 1;
                if (item.type == 'crepairs' || item.type == 'drepairs') {
                    bootbox.confirm({
                        message: "<?php echo lang('Do you want to add the repair items back to the stock?');?>",
                        buttons: {
                            confirm: {
                                label: "<?php echo lang('yes');?>",
                                className: 'btn-success'
                            },
                            cancel: {
                                label: "<?php echo lang('no');?>",
                                className: 'btn-danger'
                            }
                        },
                        callback: function (result) {
                            if (result) {
                                item.items_restock = 1;
                                item.row.items_restock = 1;
                            } else {
                                item.items_restock = 1;
                                item.row.items_restock = 0;
                            }
                            items[row_no] = item;
                            localStorage.setItem('reitems', JSON.stringify(items));
                            loadItems();
                        }
                    });
                }
                if (item.type == 'new_phone') {
                    bootbox.confirm({
                        message: "<?php echo lang('Classify As');?>",
                        buttons: {
                            confirm: {
                                label: "<?php echo lang('Used');?>",
                                className: 'btn-success'
                            },
                            cancel: {
                                label: "<?php echo lang('New');?>",
                                className: 'btn-danger'
                            }
                        },
                        callback: function (result) {
                            if (result) {
                                item.phone_classification = "used";
                                $('#id-div').html('<input type="hidden" name="prow_id" id="prow_id" value="'+row_no+'">');
                                $('#prModal').modal('show');
                            } else {
                                item.phone_classification = "new";
                            }
                            items[row_no] = item;
                            localStorage.setItem('reitems', JSON.stringify(items));
                            loadItems();
                        }
                    });
                }
            }else{
                item.add_to_stock = 0;
            }
            items[row_no] = item;
            localStorage.setItem('reitems', JSON.stringify(items));
            loadItems();
            return;
        });
    
    

</script>

<!-- Main content -->

<div class="modal modal-default-filled fade" id="prModal" tabindex="-1" role="dialog" aria-labelledby="prModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="padding: 10px">
            <div class="modal-header">
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="pr_form" role="form">
                    <div id="id-div"></div>
                    <div class="form-group">
                        <label><?php echo lang('Cosmetic Condition');?></label>
                        <select class="form-control" name="cosmetic_condition" id="cosmetic_condition" required="">
                            <option value="1">
                                *
                            </option>
                            <option value="2">
                                **
                            </option>
                            <option value="3">
                                ***
                            </option>
                            <option value="4">
                                ****
                            </option>
                            <option value="5">
                                *****
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('Operational Condition');?></label>
                        <select class="form-control" name="operational_condition" id="operational_condition" required="">
                            <option value="1">
                                *
                            </option>
                            <option value="2">
                                **
                            </option>
                            <option value="3">
                                ***
                            </option>
                            <option value="4">
                                ****
                            </option>
                            <option value="5">
                                *****
                            </option>
                        </select>
                    </div>
                     <div class="form-group">
                        <label><?php echo lang('Status');?></label>
                        <select class="form-control" name="phone_status" id="phone_status" required="">
                            <option value=""><?php echo lang('Select Phone Status');?></option>
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
                    <div class="form-group">
                        <label><?php echo lang('Unlocked');?></label>
                        <select class="form-control" name="unlock_status" id="unlock_status" required="">
                            <option value="0">
                                <?php echo lang('no');?>
                            </option>
                            <option value="1">
                                <?php echo lang('yes');?>
                            </option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button role="button" form="pr_form" class="btn btn-primary"><?php echo lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>
<section class="panel">
    <div class="panel-body">
        <?php
        $attrib = array('class' => 'edit-resl-form', 'id'=>'refund_form');
        echo form_open("panel/pos/refund", $attrib);
        ?>

        <div class="row">
            <div class="col-lg-12">
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label><?php echo lang('Return Surcharge');?></label>
                        <?php echo form_input('return_surcharge', set_value('return_surcharge'), 'class="form-control input-tip" id="return_surcharge" required="required"'); ?>
                    </div>
                </div>


                <div class="col-md-12">
                    <div class="control-group table-group">
                        <label class="table-label"><?php echo lang("order_items"); ?></label> <?php echo lang('(Please edit the return quantity below. You can remove the item or set the return quantity to zero if it is not being returned)');?>

                        <div class="controls table-controls">
                            <table id="reTable"
                                   class="table items table-striped table-bordered table-condensed table-hover">
                                <thead>
                                <tr>
                                    <th class="col-md-4"><?php echo lang("product_name") . " (" . $this->lang->line("product_code") . ")"; ?></th>
                                    <th class="col-md-1"><?php echo lang('Unit Price');?></th>
                                    <th class="col-md-1"><?php echo lang('Tax');?></th>
                                    <th class="col-md-1"><?php echo lang('Discount');?></th>
                                    <th><?php echo lang("subtotal"); ?> (<span
                                            class="currency"><?php echo escapeStr($settings->currency) ?></span>)
                                    </th>
                                    <th><?php echo lang('Add to Stock');?></th>
                                    

                                    <th style="width: 30px !important; text-align: center;">
                                        <i class="fas fa-trash" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                    </th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="bottom-total" class="well well-sm" style="margin-bottom: 0;">
                        <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                            <tr class="warning">
                                <td>
                                    <?php echo lang('items') ?>
                                    <span class="totals_val pull-right" id="titems">0</span>
                                </td>
                                <td>
                                    <?php echo lang('total') ?>
                                    <span class="totals_val pull-right" id="total">0.00</span>
                                </td>
                                <td>
                                    <?php echo lang('Product Taxes');?>
                                    <span class="totals_val pull-right" id="ttax1">0.00</span>
                                </td>
                                <td>
                                    <?php echo lang('Surcharges');?>
                                    <span class="totals_val pull-right" id="trs">0.00</span>
                                </td>
                                <td>
                                    <?php echo lang('Order Tax');?>
                                    <span class="totals_val pull-right" id="ttax2">0.00</span>
                                </td>
                                <td>
                                    <?php echo lang('Return Amount');?>
                                    <span class="totals_val pull-right" id="gtotal">0.00</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div style="height:15px; clear: both;"></div>

                <input type="hidden" name="total_items" value="" id="total_items" required="required"/>
                <input type="hidden" name="order_tax" value="" id="retax2" required="required"/>
                <input type="hidden" name="discount" value="" id="rediscount" required="required"/>

                <div class="row" id="bt">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="renote"><?php echo lang('Return Note');?></label>
                                <?php echo form_textarea('note', set_value('note'), 'class="form-control" id="renote" style="margin-top: 10px; height: 100px;"'); ?>

                            </div>
                        </div>

                    </div>

                </div>
                <div class="col-md-12">
                    <div class="fprom-group"><?php echo form_submit('add_return', $this->lang->line("submit"), 'id="add_return" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?></div>
                </div>
            </div>
        </div>


        <?php echo form_close(); ?>

    </div>
</section>
<script type="text/javascript">
$("#pr_form").on( "submit", function( event ) {
    event.preventDefault();
    var item_id = $('#prow_id').val();
    var cosmetic_condition = $('#cosmetic_condition').val();
    var operational_condition = $('#operational_condition').val();
    var phone_status = $('#phone_status').val();
    var unlock_status = $('#unlock_status').val();
    var used_phone_vals = [cosmetic_condition, operational_condition, phone_status, unlock_status];
    used_phone_vals = used_phone_vals.join(',');
    items[item_id].used_phone_vals = used_phone_vals;
    localStorage.setItem('reitems', JSON.stringify(items));
    $('#prModal').modal('hide');
    loadItems();
    return;
});
$('#refund_form').on( "submit", function(event) {
    event.preventDefault();
    console.log('asda');
    if (localStorage.getItem('reitems')) {
        items = JSON.parse(localStorage.getItem('reitems'));
        $.each(items, function () {
            this.price = 0-parseFloat(this.price);
        });
        localStorage.setItem('positems', JSON.stringify(items));
        window.location.href = "<?php echo base_url(); ?>panel/pos?refund=<?php echo $refund_id; ?>&customer=<?php echo $inv->customer_id;?>";
    }
});
</script>