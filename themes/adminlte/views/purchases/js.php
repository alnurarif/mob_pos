<style type="text/css">

.hfieldset {
    border: 4px groove rgba(0, 136, 250, 0.4) !important;
    padding: 0 1.4em 1.4em 1.4em !important;
    margin: 0 0 1.5em 0 !important;
    background: linear-gradient(rgba(0, 136, 204, 0.03), #fff);
    background-attachment: fixed;
}
.hlegend {
    background: rgba(0, 136, 204, 0.04);
    border-radius: 5px;
}

</style>
<script type="text/javascript">
function form1_onsubmit()
{
    event.preventDefault();
    $('#addRecItems :input').not(':submit').clone().hide().appendTo('#purchase_form');
    document.getElementById("purchase_form").submit();
    return true;
}

$(document).ready(function () {
$('body a, body button').attr('tabindex', -1);
check_add_item_val();
if (site.settings.set_focus != 1) {
    $('#add_item').focus();
}
// Order level shipping and discoutn localStorage
if (podiscount = localStorage.getItem('podiscount')) {
    $('#podiscount').val(podiscount);
}
$('#potax2').on("change", function (e) {
    localStorage.setItem('potax2', $(this).val());
});
if (potax2 = localStorage.getItem('potax2')) {
    $('#potax2').val(potax2);
}
$('#postatus').on("change", function (e) {
    localStorage.setItem('postatus', $(this).val());
});
if (postatus = localStorage.getItem('postatus')) {
    $('#postatus').val(postatus);
}
var old_shipping;
$('#poshipping').focus(function () {
    old_shipping = $(this).val();
}).on("change", function () {
    if (!is_numeric($(this).val())) {
        $(this).val(old_shipping);
        bootbox.alert('unexpected_value');
        return;
    } else {
        shipping = $(this).val() ? parseFloat($(this).val()) : 0;
    }
    localStorage.setItem('poshipping', shipping);
    var gtotal = ((total + invoice_tax) - order_discount) + shipping;
    $('#gtotal').text(formatMoney(gtotal));
    $('#tship').text(formatMoney(shipping));
});
if (poshipping = localStorage.getItem('poshipping')) {
    shipping = parseFloat(poshipping);
    $('#poshipping').val(shipping);
}

$('#popayment_term').on("change", function (e) {
    localStorage.setItem('popayment_term', $(this).val());
});
if (popayment_term = localStorage.getItem('popayment_term')) {
    $('#popayment_term').val(popayment_term);
}

// If there is any item in localStorage
if (localStorage.getItem('poitems')) {
    loadItems();
}

    // clear localStorage and reload
    $('#reset').on( "click", function (e) {
        bootbox.confirm('Are you sure!', function (result) {
            if (result) {
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
                if (localStorage.getItem('popayment_term')) {
                    localStorage.removeItem('popayment_term');
                }

                $('#modal-loading').show();
                location.reload();
            }
        });
    });

// save and load the fields in and/or from localStorage
var $supplier = $('#posupplier'), $currency = $('#pocurrency');

$('#poref').on("change", function (e) {
    localStorage.setItem('poref', $(this).val());
});
if (poref = localStorage.getItem('poref')) {
    $('#poref').val(poref);
}
$('#powarehouse').on("change", function (e) {
    localStorage.setItem('powarehouse', $(this).val());
});
if (powarehouse = localStorage.getItem('powarehouse')) {
    $('#powarehouse').val(powarehouse);
}

        if (ponote = localStorage.getItem('ponote')) {
            $('#ponote').val(ponote);
        }
        $supplier.on("change", function (e) {
            localStorage.setItem('posupplier', $(this).val());
            $('#supplier_id').val($(this).val());
        });
        if (posupplier = localStorage.getItem('posupplier')) {
            $supplier.select2({
                minimumInputLength: 1,
                data: [],
                ajax: {
                    url: base_url + "panel/purchases/supplier_suggestions",
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            term: term,
                            limit: 10
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {results: data.results};
                        } else {
                            return {results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                }
            });
            var $option = $('<option selected>Loading...</option>').val(posupplier);

            $supplier.append($option).trigger('change'); // append the option and update Select2

            $.ajax({ // make the request for the selected data object
                type: 'POST',
                url: base_url + "panel/purchases/getSupplier/" + posupplier,
                dataType: 'json'
            }).then(function (data) {
              // Here we should have the data object
                $option.text(data.text).val(data.id); // update the text that is displayed (and maybe even the value)
                $option.removeData(); // remove any caching data that might be associated
                $supplier.trigger('change'); // notify JavaScript components of possible changes
            });
            

} else {
    nsSupplier();
}

    /*$('.rexpiry').on("change", function (e) {
        var item_id = $(this).closest('tr').attr('data-item-id');
        poitems[item_id].row.expiry = $(this).val();
        localStorage.setItem('poitems', JSON.stringify(poitems));
    });*/
if (localStorage.getItem('poextras')) {
    $('#extras').iCheck('check');
    $('#extras-con').show();
}
$('#extras').on('ifChecked', function () {
    localStorage.setItem('poextras', 1);
    $('#extras-con').slideDown();
});
$('#extras').on('ifUnchecked', function () {
    localStorage.removeItem("poextras");
    $('#extras-con').slideUp();
});
$(document).on('change', '.rexpiry', function () {
    var item_id = $(this).closest('tr').attr('data-item-id');
    poitems[item_id].row.expiry = $(this).val();
    localStorage.setItem('poitems', JSON.stringify(poitems));
});


// prevent default action upon enter
$('body').on('keypress', function (e) {
    if ($(e.target).hasClass('redactor_editor')) {
        return true;
    }
    if (e.keyCode == 13) {
        e.preventDefault();
        return false;
    }
});

// Order tax calcuation
$('#potax2').on("change", function () {
    localStorage.setItem('potax2', $(this).val());
    loadItems();
    return;
});

// Order discount calcuation
var old_podiscount;
$('#podiscount').focus(function () {
    old_podiscount = $(this).val();
}).on("change", function () {
    localStorage.removeItem('podiscount');
    localStorage.setItem('podiscount', $(this).val());
    loadItems();
    return;
   
});


    /* ----------------------
     * Delete Row Method
     * ---------------------- */

     $(document).on('click', '.podel', function () {
        var row = $(this).closest('tr');
        var item_id = row.attr('data-item-id');
        delete poitems[item_id];
        row.remove();
        if(poitems.hasOwnProperty(item_id)) { } else {
            localStorage.setItem('poitems', JSON.stringify(poitems));
            loadItems();
            return;
        }
    });

    /* -----------------------
     * Edit Row Modal Hanlder
     ----------------------- */
     
        $('#nphone_form').on( "submit", function(event) {
            form = $(this);
            var valid = form.parsley().validate();
            if (!valid) {
                return false;
            }
            
            var phone_tax               = $('#phone_tax').val(), 
                phone_name              = $('#phone_name').val(), 
                phone_manufacturer      = $('#phone_manufacturer').val(), 
                phone_model             = $('#phone_model').val(), 
                phone_description       = $('#phone_description').val(), 
                phone_max_discount      = $('#phone_max_discount').val(), 
                phone_discount_type     = $('#phone_discount_type').val(), 
                phone_carrier           = $('#phone_carrier').val(),
                phone_price             = $('#nphone_price').val(),
                phone_aq                = $('#nphone_alert_quantity').val(),
                phone_wid               = $('#npwarranty_id').val(),
                category                = $('#np_category_id').val(),
                sub_category            = $('#np_sub_category').val(),
                phone_taxable           = 1;


            var msg, row = null, product = {
                type: 'new',
                phone_name:      phone_name,
                manufacturer_id: phone_manufacturer,
                model_name:      phone_model,
                carrier_id:      phone_carrier,
                description:     phone_description,
                max_discount:    phone_max_discount,
                discount_type:   phone_discount_type,
                tax_id:          phone_tax,
                taxable:         phone_taxable,
                price:           phone_price,
                alert_quantity:  phone_aq,
                warranty_id:     phone_wid,
                category:        category,
                sub_category:    sub_category,
            };

            $.ajax({
                type: "get", async: false,
                url: "<?php echo base_url(); ?>panel/phones/addByAjax",
                data: {token: "<?php echo $csrf; ?>", product: product},
                dataType: "json",
                success: function (data) {
                    if (data.msg == 'success') {
                        row = add_purchase_item(data.result);
                    } else {
                        msg = data.msg;
                    }
                }
            });
            if (row) {
                $('#phoneModal').modal('hide');
            } else {
                $('#mError').text(msg);
                $('#mError-con').show();
            }
            return false;

    });
     /* -----------------------
     * Edit Row Modal Hanlder
     ----------------------- */
      $('#uphone_form').on( "submit", function(event) {
        form = $(this);
        var valid = form.parsley().validate();
        if (!valid) {
            return false;
        }
        var used_phone_tax               = $('#used_phone_tax').val(), 
            used_phone_name              = $('#used_phone_name').val(), 
            used_phone_manufacturer      = $('#used_phone_manufacturer').val(), 
            used_phone_model             = $('#used_phone_model').val(), 
            used_phone_description       = $('#used_phone_description').val(), 
            used_phone_max_discount      = $('#used_phone_max_discount').val(), 
            used_phone_carrier           = $('#used_phone_carrier').val(), 
            cosmetic_condition           = $('#cosmetic_condition').val(),
            operational_condition        = $('#operational_condition').val(),
            acquired_date                = $('#acquired_date').val(),
            used_phone_status            = $('#used_phone_status').val(),
            unlock_status                = $('#unlock_status').val(),
            category                     = $('#up_category_id').val(),
            sub_category                 = $('#up_sub_category').val(),
            phone_wid                    = $('#upwarranty_id').val(),
            phone_taxable                = 1;

            var msg, row = null, product = {
                type: 'used',
                tax_id: used_phone_tax,
                phone_name: used_phone_name,
                manufacturer_id: used_phone_manufacturer,
                model_name: used_phone_model,
                description: used_phone_description,
                max_discount: used_phone_max_discount,
                carrier_id: used_phone_carrier,
                cosmetic_condition: cosmetic_condition,
                operational_condition: operational_condition,
                date_acquired: acquired_date,
                used_status: used_phone_status,
                unlocked: unlock_status,
                taxable: phone_taxable,
                category: category,
                sub_category: sub_category,
                warranty_id:     phone_wid,
            };

            $.ajax({
                type: "get", async: false,
                url: "<?php echo base_url(); ?>panel/phones/addUsedByAjax",
                data: {token: "<?php echo $csrf; ?>", product: product},
                dataType: "json",
                success: function (data) {
                    if (data.msg == 'success') {
                        row = add_purchase_item(data.result);
                    } else {
                        msg = data.msg;
                    }
                }
            });
            if (row) {
                $('#UsedPhoneModal').modal('hide');
            } else {
                $('#mUsedError').text(msg);
                $('#mUsedError-con').show();
            }
            return false;

    });
    $(document).on('click', '#addManually', function (e) {
        $('#phoneModal').appendTo("body").modal('show');
        return false;
    });
    $(document).on('click', '#addUsedManually', function (e) {
        $('#UsedPhoneModal').appendTo("body").modal('show');
        return false;
    });
   
    $('#prModal').on('shown.bs.modal', function (e) {
        
    });
    
    


    $(document).on('click', '#calculate_unit_price', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var item = poitems[item_id];
        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert('unexpected_value');
            return;
        }
        var subtotal = parseFloat($('#psubtotal').val()),
        qty = parseFloat($('#pquantity').val());
        $('#pcost').val(formatDecimal((subtotal/qty), 4)).change();
        return false;
    });

    /* -----------------------
     * Edit Row Method
     ----------------------- */
     $(document).on('click', '#editItem', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id'), 
            new_pr_tax = $('#ptax').val(), 
            new_pr_tax_rate = [];
        var taxable = $('#taxable').is(":checked");
        if (!taxable) {new_pr_tax = 0;}
       
        if (new_pr_tax) {
            for ( var i = 0; i < tax_rates.length; i++ ) {
                for ( var e = 0; e < new_pr_tax.length; e++ ) {
                    if ( tax_rates[i].id === new_pr_tax[e] ) {
                        if (tax_rates[i].type == 1) {
                            new_pr_tax_rate[i] = formatDecimal(tax_rates[i].rate) + '%';
                        } else if (tax_rates[i].type == 2) {
                            new_pr_tax_rate[i] = tax_rates[i].rate;
                        }
                    }
                }
            }
        }


        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert('unexpected_value');
            return;
        }

        var unit = $('#punit').val();
        var base_quantity = parseFloat($('#pquantity').val());
        if(unit != poitems[item_id].row.base_unit) {
            $.each(poitems[item_id].units, function(){
                if (this.id == unit) {
                    base_quantity = unitToBaseQty($('#pquantity').val(), this);
                }
            });
        }

        poitems[item_id].row.fup = 1,
        poitems[item_id].row.qty = parseFloat($('#pquantity').val()),
        poitems[item_id].row.base_quantity = parseFloat(base_quantity),
        poitems[item_id].row.unit = unit,
        poitems[item_id].row.cost = parseFloat($('#pcost').val()),
        poitems[item_id].row.tax_rate = new_pr_tax,
        poitems[item_id].tax_rate = new_pr_tax_rate,
        poitems[item_id].row.discount = $('#pdiscount').val() ? $('#pdiscount').val() : '0',
        localStorage.setItem('poitems', JSON.stringify(poitems));
        $('#prModal').modal('hide');
        loadItems();
        return;
    });


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
        var new_qty = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        poitems[item_id].row.base_quantity = new_qty;
        if(poitems[item_id].row.unit != poitems[item_id].row.base_unit) {
            $.each(poitems[item_id].units, function(){
                if (this.id == poitems[item_id].row.unit) {
                    poitems[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }
        poitems[item_id].row.qty = new_qty;
        poitems[item_id].row.received = new_qty;
        localStorage.setItem('poitems', JSON.stringify(poitems));
        loadItems();
    });
    
    $(document).on("focus", '.pcost', function () {}).on("change", '.pcost', function () {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val()) || parseFloat($(this).val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert('unexpected_value');
            return;
        }
        var new_qty = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        poitems[item_id].row.cost = new_qty;
        localStorage.setItem('poitems', JSON.stringify(poitems));
        loadItems();
    });
    $(document).on("focus", '.pdiscount', function () {}).on("change", '.pdiscount', function () {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val()) || parseFloat($(this).val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert('unexpected_value');
            return;
        }
        item_id = row.attr('data-item-id');
        poitems[item_id].row.discount = parseFloat($(this).val());
        localStorage.setItem('poitems', JSON.stringify(poitems));
        loadItems();
    });

    var old_received;
     $(document).on("focus", '.received', function () {
        old_received = $(this).val();
    }).on("change", '.received', function () {
        var row = $(this).closest('tr');
        new_received = $(this).val() ? $(this).val() : 0;
        if (!is_numeric(new_received)) {
            $(this).val(old_received);
            bootbox.alert('unexpected_value');
            return;
        }
        var new_received = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        if (new_received > poitems[item_id].row.qty) {
            $(this).val(old_received);
            bootbox.alert('unexpected_value');
            return;
        }
        unit = formatDecimal(row.children().children('.runit').val()),
        $.each(poitems[item_id].units, function(){
            if (this.id == unit) {
                qty_received = formatDecimal(unitToBaseQty(new_received, this), 4);
            }
        });
        poitems[item_id].row.unit_received = new_received;
        poitems[item_id].row.received = qty_received;
        localStorage.setItem('poitems', JSON.stringify(poitems));
        loadItems();
    });

    /* --------------------------
     * Edit Row Cost Method
     -------------------------- */
     var old_cost;
     $(document).on("focus", '.rcost', function () {
        old_cost = $(this).val();
    }).on("change", '.rcost', function () {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val())) {
            $(this).val(old_cost);
            bootbox.alert('unexpected_value');
            return;
        }
        var new_cost = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        poitems[item_id].row.cost = new_cost;
        localStorage.setItem('poitems', JSON.stringify(poitems));
        loadItems();
    });

    $(document).on("click", '#removeReadonly', function () {
        $('#posupplier').prop('disabled', false);
     return false;
 });

    if (po_edit) {
        $('#posupplier').prop('disabled', true);
    }

});
/* -----------------------
 * Misc Actions
 ----------------------- */

// hellper function for supplier if no localStorage value
function nsSupplier() {
    $('#posupplier').select2({
        minimumInputLength: 1,
        ajax: {
            url: base_url + "panel/purchases/supplier_suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function (data, page) {
                if (data.results != null) {
                    return {results: data.results};
                } else {
                    return {results: [{id: '', text: 'No Match Found'}]};
                }
            }
        }
    });
}

function loadItems() {

    if (localStorage.getItem('poitems')) {
        total = 0;
        count = 1;
        an = 1;
        product_tax = 0;
        invoice_tax = 0;
        product_discount = 0;
        order_discount = 0;
        total_discount = 0;
        $("#poTable tbody").empty();
        poitems = JSON.parse(localStorage.getItem('poitems'));
        sortedItems = _.sortBy(poitems, function(o){return [parseInt(o.order)];}) ;

        var order_no = new Date().getTime();
        $.each(sortedItems, function () {
            var item = this;
            var type = item.row.type;
            var item_id = item.item_id;
            item.order = item.order ? item.order : order_no++;
            var product_id = item.row.id, item_type = item.row.type, combo_items = item.combo_items, item_cost = item.row.cost, item_oqty = item.row.oqty, item_qty = item.row.qty, item_bqty = item.row.quantity_balance, item_expiry = item.row.expiry, item_tax_method = item.row.tax_method, item_ds = parseFloat(item.row.discount), item_discount = 0, item_option = item.row.option, item_code = item.row.code, item_name = item.row.name.replace(/"/g, "&#034;").replace(/'/g, "&#039;");
            var qty_received = (item.row.received >= 0) ? item.row.received : item.row.qty;
            if (item.row.new_entry == 1) { item_bqty = item_qty; item_oqty = item_qty; }
            var unit_cost = item.row.cost;
            var aunit_cost = item.row.cost;
            var product_unit = item.row.unit, base_quantity = item.row.base_quantity;
            var supplier = localStorage.getItem('posupplier'), belong = false;
            var is_serialized = parseInt(item.row.is_serialized);
            var ds = item_ds ? item_ds : '0';
            if (ds.toString().indexOf("%") !== -1) {
                var pds = ds.split("%");
                if (!isNaN(pds[0])) {
                    item_discount = formatDecimal((parseFloat(((unit_cost) * parseFloat(pds[0])) / 100)), 4);
                } else {
                    item_discount = formatDecimal(ds);
                }
            } else {
                 item_discount = formatDecimal(ds);
            }
            product_discount += parseFloat(item_discount * item_qty);

            unit_cost = formatDecimal(unit_cost-item_discount);
            var pr_tax = item.pr_tax;
            var pr_tax_val = 0, pr_tax_rate = [];
            
            pr_tax_rate = pr_tax_rate.filter(function(e){ return e === 0 || e }).join(', ');
            
            item_cost = formatDecimal(unit_cost);
            unit_cost = formatDecimal(unit_cost+item_discount, 4);
            
            if(item.row.type == 'used_phone'){
                aunit_cost = 1;
                item_qty = 1;
                disabled = 'readonly';
            }else if(item.row.type == 'new_phone'){
                aunit_cost = unit_cost;
                item_qty = 1;
                disabled = '';
            }else{
                disabled = '';
            }
            var row_no = (new Date).getTime();
            var newTr = $('<tr id="row_' + row_no + '" class="row_' + item_id + '" data-item-id="' + item_id + '"></tr>');
            tr_html = '<td><input name="product_id[]" type="hidden" class="rid" value="' + product_id + '"><input name="is_serialized[]" type="hidden" class="rid" value="' + is_serialized + '"><input name="product_type[]" type="hidden" value="' + item.row.type + '"><input name="product[]" type="hidden" class="rcode" value="' + item_code + '"><input name="product_name[]" type="hidden" class="rname" value="' + item_name + '"><span class="sname" id="name_' + row_no + '">' + item_code +' - '+ item_name +' </span></td>';
            tr_html += '<td class="text-right"><input type="text" class="form-control pcost" value="'+aunit_cost+'" id="pcost" '+disabled+'><input class="form-control input-sm text-right rcost" name="net_cost[]" type="hidden" id="cost_' + row_no + '" value="' + aunit_cost + '"><input class="rucost" name="unit_cost[]" type="hidden" value="' + aunit_cost + '"></td>';
            tr_html += '<td><input class="form-control text-center rquantity" name="quantity[]" type="text" tabindex="'+((site.settings.set_focus == 1) ? an : (an+1))+'" value="' + formatDecimal(item_qty) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="quantity_' + row_no + '" onClick="this.select();" '+disabled+'></td>';
            
            if (site.settings.product_discount == 1) {
                tr_html += '<td class="text-right"><input class="form-control input-sm rdiscount" name="product_discount[]" type="hidden" id="discount_' + row_no + '" value="' + item_ds + '"><input type="text" class="form-control pdiscount" id="pdiscount" value="'+((item_discount))+'"><span>'+formatMoney(item_discount*item_qty)+'<span></td>';
            }



            tr_html += '<td class="text-right"><span class="text-right ssubtotal" id="subtotal_' + row_no + '">' + formatMoney((((aunit_cost) + (pr_tax_val)) * parseFloat(item_qty)) - (item_discount * item_qty)) + '</span></td>';
            tr_html += '<td class="text-center"><i class="fas fa-times tip podel" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
            newTr.html(tr_html);
            newTr.prependTo("#poTable");

            total += ((((aunit_cost) + (pr_tax_val)) * (item_qty)) - product_discount);
            count += item_qty;
            an++;
            if(!belong)
                $('#row_' + row_no).addClass('warning');
        });

        var col = 2;
        if (site.settings.product_expiry == 1) { col++; }
        var tfoot = '<tr id="tfoot" class="tfoot active"><th colspan="'+col+'">Total</th><th class="text-center">' + formatNumber(parseFloat(count) - 1) + '</th>';
        
        if (site.settings.product_discount == 1) {
            tfoot += '<th class="text-right">'+formatMoney(product_discount)+'</th>';
        }

        tfoot += '<th class="text-right">'+formatMoney((total))+'</th><th class="text-center"><i class="fas fa-trash" style="opacity:0.5; filter:alpha(opacity=50);"></i></th></tr>';
        $('#poTable tfoot').html(tfoot);

        // Order level discount calculations
        if (localStorage.getItem('podiscount')) {
            var podiscount = localStorage.getItem('podiscount');
            var ds = podiscount;
            if (ds.toString().indexOf("%") !== -1) {
                var pds = ds.split("%");
                if (!isNaN(pds[0])) {
                    order_discount = formatDecimal(((total * parseFloat(pds[0])) / 100), 4);
                } else {
                    order_discount = formatDecimal(ds);
                }
            } else {
                order_discount = formatDecimal(ds);
            }
        }

        // Order level tax calculations
        if (potax2 = localStorage.getItem('potax2')) {
            var ds = potax2;
            if (ds.indexOf("%") !== -1) {
                var pds = ds.split("%");
                if (!isNaN(pds[0])) {
                    invoice_tax = parseFloat((((total - order_discount) * parseFloat(pds[0])) / 100), 4);
                } else {
                    invoice_tax = parseFloat(ds);
                }
            } else {
                invoice_tax = parseFloat(ds);
            }
        }
        
        total_discount = parseFloat(order_discount + product_discount);
        // Totals calculations after item addition
        var gtotal = ((total + invoice_tax) - order_discount) + shipping;
        $('#total').text(formatMoney(total + total_discount));
        $('#titems').text((an-1)+' ('+(parseFloat(count)-1)+')');
        
        $('#tds').text(formatMoney(total_discount));
        $('#ttax1').text(formatMoney(product_tax));
        $('#ttax2').text(formatMoney(invoice_tax));
        $('#gtotal').text(formatMoney(gtotal));
        if (an > parseInt(site.settings.bc_fix) && parseInt(site.settings.bc_fix) > 0) {
            $("html, body").animate({scrollTop: $('#sticker').offset().top}, 500);
            $(window).scrollTop($(window).scrollTop() + 1);
        }
        set_page_focus();
    }
}

/* -----------------------------
 * Add Purchase Item Function
 * @param {json} item
 * @returns {Boolean}
 ---------------------------- */
 function add_purchase_item(item) {

    if (count == 1) {
        poitems = {};
        if ($('#posupplier').val()) {
            $('#posupplier').prop('disabled', true);
        } else {
            bootbox.alert('Please Select Above First');
            item = null;
            return;
        }

    }
    if (item == null)
        return;

    var item_id = item.item_id;
    if (poitems[item_id]) {
        poitems[item_id].row.qty = parseFloat(poitems[item_id].row.qty) + 1;
    } else {
        poitems[item_id] = item;
    }
    poitems[item_id].order = new Date().getTime();
    localStorage.setItem('poitems', JSON.stringify(poitems));
    loadItems();
    return true;

}

if (typeof (Storage) === "undefined") {
    $(window).on('beforeunload', function (e) {
        if (count > 1) {
            var message = "You will loss data!";
            return message;
        }
    });
}
</script>
<script type="text/javascript">
$(document).ready(function(){
    $('#extras').on("change", function() {
      if ($(this).is(':checked')) {
        $('#extras-con').slideDown();
      } else {
        $('#extras-con').slideUp();
      }
    });
   
});
    jQuery(document).ready( function($) {
        $('#np_category_id').on('change', function (e) {
            $('#np_sub_category').val('').trigger('change');
        });
        $( "#np_category_id" ).select2();
        $( "#np_sub_category" ).select2({        
            ajax: {
                placeholder: "<?php echo lang('Select a Category');?>",
                url: "<?php echo base_url(); ?>panel/settings/getCategoriesAjax/0",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        category_id: $('#np_category_id').val(),
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
    jQuery(document).ready( function($) {
        $('#up_category_id').on('change', function (e) {
            $('#up_sub_category').val('').trigger('change');
        });
        $( "#up_category_id" ).select2();
        $( "#up_sub_category" ).select2({        
            ajax: {
                placeholder: "<?php echo lang('Select a Category');?>",
                url: "<?php echo base_url(); ?>panel/settings/getCategoriesAjax/0",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        category_id: $('#up_category_id').val(),
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
