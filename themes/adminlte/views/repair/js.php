
<div class="modal modal-default-filled fade " id="rprModal" data-backdrop="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
      </div>
        <div class="modal-body">
        <form class="form-horizontal" id="rserial_form" role="form">
            <div class="form-group" id="roptions_div_">
                <label for="rpoption" class="col-sm-4 control-label"><?php echo lang('PRODUCT OPTIONS');?></label>
                <div class="col-sm-8">
                    <div id="rproptions-div"></div>
                </div>
            </div>

            <div id="id-div"></div>
            <div class="form-group">
                <div id="rpserial_number-div"></div>
            </div>
            <div class="form-group" id="hidden_sap" style="display: none;">
                <div class="form-group">
                    <label for="pplans" class="col-sm-4 control-label"><?php echo lang('Activation Plan');?></label>
                    <div class="col-sm-8">
                        <?php 
                        $data = array();
                        if ($plans) {
                            foreach ($plans as $plan) {
                                $data[$plan->id] = $plan->name;
                            }
                        }
                        echo form_dropdown('sap_pplan', $data, '', 'class="form-control" id="sap_pplan"'); ?>
                    </div>
                </div>
            </div>

                <div id="pprice-div"></div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-danger" id="rcancel_edit"><?php echo lang('Cancel');?></button>
        <button role="submit" form="rserial_form" class="btn btn-primary" id=""><?php echo lang('submit') ?></button>
    </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal modal-success" id="rprdModal" tabindex="-1" role="dialog" aria-labelledby="rprdModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i class="fas fa-2x">&times;</i></span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="rprdModalLabel"></h4>
            </div>
            <div class="modal-body">
                <form id="repair_discount_form" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="pdiscount" class="col-sm-4 control-label"><?php echo lang('Discount');?></label>
                        <div class="col-sm-8">
                            <div id="rdid-div"></div>
                            <div id="rpdiscount-div"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="pull-left btn btn-danger"><?php echo lang('Cancel');?></button>
                <button type="submit" class="btn btn-primary" form="repair_discount_form"><?php echo lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>
<script>


    ritems = {};
    


    $("#add_r_item").autocomplete({
        source: function(request, response) {
            var is_repair = true;
            var value = $('#add_r_item').val();
            $.getJSON('<?php echo site_url('panel/pos/suggestions'); ?>', { is_repair: is_repair, term: value }, 
                      response);
        },
        minLength: 1,
        delay: 250,
        autoFocus: true,
        focus: function( event, ui ) { event.preventDefault(); },
        select: function (event, ui) {
            event.preventDefault();
            // console.log(ui);
            if (ui.item.id !== 0) {
                var row = add_product_item(ui.item);
                if (row)
                    $(this).val(''); 
            } else {
                var row = add_product_item(ui.item);
                if (row)
                    $(this).val(''); 
            }
        }
    });

    function loadRepairItems(edit_items = true) {
        if (localStorage.getItem('slitems')) {
            ritems = JSON.parse(localStorage.getItem('slitems'));
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


            $("#rprTable tbody").empty();
            $.each(ritems, function () {
                var row_no = this.row_id;
                var item_id = this.product_id;
                var price = this.price;
                var cost = this.cost;
                var sel_opt = '';
                var item_option = this.option;
                var discount = this.discount;
                var type = this.row.type;
                var code = this.code;
                var sale_item_id = this.row.sale_item_id;
                var warranty_id = (this.row.warranty_id);
                var activation_spiff = parseFloat(this.activation_spiff);
                $.each(this.options, function (x, y) {
                    if(y.id == item_option) {
                        if (type !== 'repair') {
                            cost = y.cost;
                        }
                        sel_opt = y.name;
                        price = y.price;
                        if (type === 'cp') {
                            price = 0-parseFloat(y.cost);
                        }
                    }
                });
                var product_option = '';
                var product_variant = '';
                var variable_price_picked = true;
                if (this.row.type == 'other' && parseInt(this.row.variable_price) == 1) {
                    variable_price_picked = false;
                }

                var opt = '<p style="margin: 12px 0 0 0;">n/a</p>';
                $('#roptions_div_').hide();
               

                if ((this.variants == true && this.option_selected == false) || (!this.serialed) || !variable_price_picked || ((type == 'new_phone' || type == 'used_phone' ) && !this.purchase_type)) {
                    var opt = '<p style="margin: 12px 0 0 0;">n/a</p>';
                    $('#roptions_div_').hide();
                    if(this.options !== null && this.options !== false) {
                            $('#roptions_div_').show();
                            var o = 1;
                            opt = $("<select id=\"rpoption\" name=\"rpoption\" class=\"form-control select\" />");
                            $.each(this.options, function (x, y) {
                            if(o == 1) {
                                if (type !== 'repair') {
                                    cost = y.cost;
                                }
                                price = y.price;
                                sel_opt = y.name;
                                if(product_option == '') { product_variant = y.id; } else { product_variant = product_option; }
                            }
                            
                            $("<option/>", {value: y.id, text: y.name}).appendTo(opt);
                            o++;
                        });
                    } else {
                        product_variant = 0;
                    }
                    var serial = '';
                    if (this.is_serialized) {
                        serial = $("<div class=\"form-group\"><label class=\"col-sm-4 control-label\"><?php echo lang('Serial Number');?>: </label><div class=\"col-sm-8\"><input value=\""+(this.serial_number?this.serial_number:'')+"\" autocomplete=\"off\" id=\"pserial_number\" name=\"pserial_number\" data-id=\""+item_id+"\" data-type=\""+type+"\" class=\"form-control select\" /></div>");
                    }

                    

                    var script = document.createElement('script');
                    script.type = 'text/javascript';
                    script.text = "$(\"#pserial_number\").autocomplete({source:function(e,a){$.ajax({type:\"post\",url:'<?php echo site_url('panel/pos/getProductSerials'); ?>',dataType:\"JSON\",data:{type:$(\"#pserial_number\").data(\"type\"),id:$(\"#pserial_number\").data(\"id\"),term:$(\"#pserial_number\").val()},success:function(e){a(e)}})}});";
                    $('#rpserial_number-div').html(serial);
                    $('#rpserial_number-div').append(script);
                    $('#rproptions-div').html(opt);
                    $('#id-div').html('<input type="hidden" name="prow_id" id="prow_id" value="'+row_no+'"><input type="hidden" name="prtype" id="prtype" value="'+this.row.type+'">');

                    var vprice = '';
                    if (!variable_price_picked) {
                        vprice = $("<div class=\"form-group\"><label class=\"col-sm-4 control-label\"><?php echo lang('Price');?>: </label><div class=\"col-sm-8\"><input id=\"pprice\" name=\"pprice\" class=\"form-control\" /></div></div><div class=\"form-group\"><label class=\"col-sm-4 control-label\"><?php echo lang('cost');?>: </label><div class=\"col-sm-8\"><input id=\"pcost\" name=\"pcost\" class=\"form-control\" /></div></div><div class=\"form-group\"><label class=\"col-sm-4 control-label\"><?php echo lang('description');?>: </label><div class=\"col-sm-8\"><textarea id=\"pdescription\" name=\"pdescription\" class=\"form-control\" /></div></div>");
                    }

                    $('#pprice-div').html(vprice);
                    if ((!this.refund_item) && this.type !== 'cp') {
                        $('#rprModal').appendTo("body").modal({backdrop: 'static', keyboard: false});
                        $('#rprModal').css('display', 'block');
                    }
                }

                var product_tax = 0;
                var pr_tax = this.pr_tax;
                var pr_tax_val = 0, pr_tax_rate = [];
                var pr_tax_val_fixed = 0;
                if(parseInt(this.taxable) == 1){
                    $.each(pr_tax, function (tax, tax_detaild) {
                        if (tax_detaild && tax_detaild !== false) {
                            if (tax_detaild.type == 1) {
                                pr_tax_val += parseFloat(tax_detaild.rate);
                                pr_tax_rate[tax_detaild.id] = formatPOSDecimal(tax_detaild.rate) + '%';
                            } else if (parseInt(tax_detaild.type) == 2) {
                                pr_tax_val_fixed += parseFloat(tax_detaild.rate);
                                pr_tax_rate[tax_detaild.id] = formatPOSDecimal(tax_detaild.rate);
                            }
                        }
                    });
                    percent_tax = formatPOSDecimal(parseFloat(Math.abs(price)-discount) * parseFloat(pr_tax_val) / (100), 4);
                    product_tax = parseFloat(percent_tax) + parseFloat(pr_tax_val_fixed);
                }
                if (this.refund_item || (this.row.type == 'other' && parseInt(this.row.cash_out))) {
                    product_tax = 0-product_tax;
                }

                if (this.row.type == 'drepairs' || this.row.type == 'crepairs') {
                    product_tax = parseFloat(this.row.tax);
                }
                invoice_tax += product_tax;
                pr_tax_rate = pr_tax_rate.filter(function(e){ return e === 0 || e }).join(', ');
                var subtotal = (parseFloat(price)+parseFloat(product_tax)-parseFloat(discount));
                var newTr = $('<tr id="row_' + row_no + '" class="item_' + this.row.id + '" data-item-id="' + row_no + '"></tr>');
                
                var is_manual = this.type == 'manual' || this.code.substring(0, 6) == 'manual';
                tr_html = '<td style="width: 40%;"><input name="item_id[]" id="item_id" type="hidden" value="' + this.product_id + '"><input name="item_details[]" id="item_details" type="hidden" value="' + this.item_details + '"><input name="activation_spiff[]" id="activation_spiff" type="hidden" value="' + activation_spiff + '"><input name="disount_code[]" id="disount_code" type="hidden" value="' + this.discount_code_used + '"><input name="product_warranty[]" id="product_warranty" type="hidden" value="' + warranty_id + '"><input name="item_discount[]" id="item_discount" type="hidden" value="' + discount + '"><input name="item_type[]" id="item_type" type="hidden" value="' + this.row.type + '"><input name="item_cost[]" id="item_cost" type="hidden" value="' + cost + '"><input name="phone_classification[]" id="phone_classification" type="hidden" value="' + this.phone_classification + '"><input name="used_phone_vals[]" id="used_phone_vals" type="hidden" value="' + this.used_phone_vals + '"><input class="form-control mname" name="item_name[]" type="'+(is_manual ? 'text' : 'hidden')+'" value="' + this.name + '"><input name="items_restock[]" type="hidden" value="' + this.items_restock + '"><input name="item_code[]" type="hidden" value="' + code + '"><input name="item_serial[]" type="hidden" value="' + this.serial_number + '"><input name="product_option[]" type="hidden" class="roption" value="' + item_option + '">' + (is_manual ? '' : ('<span class="sname" id="name_' + row_no + '">' +  this.name + (sel_opt != '' ? ' ('+sel_opt+')' : '')+'<br><small>'+this.item_details+'</small>')) + '</span>';
                if ((!this.refund_item)) {
                    <?php if($this->Admin || $GP['pos-add_discounts']): ?>
                        tr_html += ' <button id="' + row_no + '" data-item="' + item_id + '" data-price="' + price + '" title="'+"<?php echo lang('Edit');?>"+'" style="cursor:pointer;" class="repair_discount btn btn-xs btn-primary"><i class="fas fa-cut" aria-hidden="false"></i></button>';
                    <?php endif; ?>
                }
                if (this.refund_item){
                    tr_html += '<input name="refund_item[]" type="hidden" value="1">';
                    tr_html += '<input name="add_to_stock[]" type="hidden" value="'+this.add_to_stock+'">';
                    tr_html += '<input name="sale_item_id[]" type="hidden" class="rsiid" value="' + sale_item_id + '">';

                }else{
                    tr_html += '<input name="refund_item[]" type="hidden" value="0">';
                    tr_html += '<input name="add_to_stock[]" type="hidden" value="0">'
                    tr_html += '<input name="sale_item_id[]" type="hidden" value="0">';
                    ;
                }
                tr_html += '<input name="phone_number[]" type="hidden" value="'+this.phone_number+'">';
                tr_html += '<input name="set_reminder[]" type="hidden" value="'+this.set_reminder+'">';
                tr_html += '</td>';
                tr_html += '<td style="width: 15%;">'+(is_manual ? '' : formatMoney(price))+'<input class="form-control text-center rprice" name="item_price[]" type="'+(is_manual ? 'text' : 'hidden')+'" value="' + (price) + '" data-id="' + row_no + '" data-item="' + this.product_id + '" id="item_price_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td style="width: 10%;">'+formatMoney(discount)+'</td>';
                tr_html += '<td style="width: 15%;">'+formatMoney(product_tax)+'<input class="form-control text-center rtax" name="item_tax[]" type="hidden" value="' + formatPOSDecimal(product_tax) + '" data-id="' + row_no + '" data-item="' + this.product_id + '" id="item_price_' + row_no + '" onClick="this.select();"><input class="form-control text-center" name="item_tax_id[]" type="hidden" value="' + encodeURIComponent(JSON.stringify(pr_tax)) + '"></td>';
                
                tr_html += '<td style="width: 20%;">'+formatMoney((subtotal))+'</td>';
                if (edit_items) {
                    tr_html += '<td style="width: 5%;" class="text-center"><i class="fas fa-times tip del" id="' + row_no + '" title="<?php echo lang('remove');?>" style="cursor:pointer;"></i></td>';
                }else{
                    tr_html += '<td style="width: 5%;" class="text-center">-</td>';
                }
                newTr.html(tr_html);
                newTr.prependTo("#rprTable");
                total += parseFloat(subtotal);
                count += 1;
                an++;
                pp += (parseFloat(price));
                total_tax += parseFloat(product_tax);
                total_discount += parseFloat(discount);
                $('.item_' + item_id).addClass('warning');

            });



            var service_charges = ($('#service_charges').val()) ? parseFloat($('#service_charges').val()) : '0';
            var deposit = parseFloat($('#advance').val());

            total = parseFloat(total) + service_charges;
            product_tax = formatPOSDecimal(total_tax);
            var gtotal = (parseFloat(total));


            $('#price_span').html(formatMoney(total - total_tax - service_charges));
            $('#tax_spane').html(formatMoney(total_tax));
            $('#sc_span').html(formatMoney(service_charges));
            $('#totalprice_span').html(formatMoney(total));
            $('#deposit_span').html(formatDecimal(deposit));
            $('#balance_span').html(formatDecimal(parseFloat(total) - parseFloat(deposit)));
            $('#gtotal').html(formatMoney(gtotal));

            calculate_price();

            
        }else{
            calculate_price(true);
        }
    }

    $(document).on("change", '.mname', function () {
       var row = $(this).closest('tr');
       if(row){
         var name = $(this).val(),
         item_id = row.attr('data-item-id');
          if(ritems[item_id]) {
            item = ritems[item_id];
           ritems[item_id].name = name;
           localStorage.setItem('slitems', JSON.stringify(ritems));
          }
         loadRepairItems();
       }
    });

     $(document).on("change", '.rprice', function () {
       var row = $(this).closest('tr');
       if(row){
         var price = $(this).val(),
         item_id = row.attr('data-item-id');
          if(ritems[item_id]) {
            item = ritems[item_id];
           ritems[item_id].price = price;
           localStorage.setItem('slitems', JSON.stringify(ritems));
          }
         loadRepairItems();
       }
    });

    function add_product_item(item, edit_item=true, manual = false) {
        if (item == null) {
            return false;
        }

        if(manual){
            item_id = item.row_id;
            ritems[item_id] = item;
        }else{
            if (item.type == 'other' && parseInt(item.row.keep_stock) == 0) {
                console.log(item);
                item.cost = item.row.no_stock_cost;
            }else{
                t_qty = parseInt(item.qty);
                console.log(t_qty);
                current_type = item.type;
                selected_qty = 0;
                $.each(ritems, function(){
                    if (this.type == current_type && this.product_id == item.product_id && !this.previous_item) {
                        selected_qty += 1;
                    }
                });
                if (selected_qty + 1 > t_qty) {
                    bootbox.alert("<?php echo lang('You dont have this item in stock');?>");
                    return false;
                }
            }
            item_id = item.row_id;
            ritems[item_id] = item;
        }
        
        localStorage.setItem('slitems', JSON.stringify(ritems));
        setTimeout(function () {
            loadRepairItems(edit_item);
        }, 100);

        return true;
    }


     function formatPOSDecimal(x, d) {
        if (!d) { d = 2; }
        return accounting.formatMoney(x, '', 2, '', '.', "%s%v");
    }




    /* -----------------------
     * Edit Row Modal Hanlder
     ----------------------- */
     $(document).on('click', '.repair_discount', function (event) {
            event.preventDefault();

            var row = $(this).closest('tr');
            var row_id = row.attr('id');
            item_id = row.attr('data-item-id');
            price = $(this).data('price');

            item = ritems[item_id];
            console.log(item);

            $('#rprdModalLabel').text(item.name + ' (' + item.code + ')');


            var repair_max_discount_rate = item.row.max_discount;
            var repair_max_discount_type = item.row.discount_type; // 1: %, 2: Fixed
            var repair_max_discount = parseFloat(repair_max_discount_rate);

            if (repair_max_discount_type == 1) {
                var repair_max_discount = price*(parseFloat(repair_max_discount_rate)/100);
            }

            $('#rpdiscount-div').html('<input type="hidden" value="'+repair_max_discount_rate+'" id="repair_max_discount_rate"><input type="hidden" value="'+repair_max_discount_type+'" id="repair_max_discount_type"><input class="form-control" value="'+parseFloat(item.discount)+'" type="number" id="repair_discount_input" step="any" max="'+repair_max_discount+'" /><br><select id="rtype_dd" class="form-control"><option value="1">'+"<?php echo lang('%');?>"+'</option><option value="2">'+"<?php echo lang('Fixed');?>"+'</option></select><br><span id="repair_max_dd_value"></span>');
           
            $("#rtype_dd option[value='2']").prop("selected", true);
            $('#repair_max_dd_value').html("<?php echo lang('Max Discount allowed is');?>" +' "'+ formatMoney(parseFloat(repair_max_discount).toFixed(2)) + '".');
            $('#rdid-div').html('<input type="hidden" name="pdrow_id" id="pdrow_id" value="'+item_id+'"><input type="hidden" name="price_dd" id="price_dd" value="'+price+'">');
            $('#rprdModal').appendTo("body").modal('show');
        });

        $(document).on('change', '#rtype_dd', function () {
            var type = $(this).val();
            
            var price = $('#price_dd').val();
            var repair_max_discount_rate = $('#repair_max_discount_rate').val();
            var repair_max_discount_type = $('#repair_max_discount_type').val();
            
            var repair_max_discount = parseFloat(repair_max_discount_rate);
            if (repair_max_discount_type == 1) {
                var repair_max_discount = price*(parseFloat(repair_max_discount_rate)/100);
            }

            // price = 5 || max  0.25
            if (type == 1){
                var discount = (parseFloat(repair_max_discount) / parseFloat(price)) * 100;
                document.getElementById("repair_discount_input").max = discount;
            }else{
                var discount = parseFloat(repair_max_discount);
                document.getElementById("repair_discount_input").max = discount;
            }
            var input = document.getElementById("repair_discount_input").setAttribute("max",discount);

            if (type == 1) {
                $('#repair_max_dd_value').html("<?php echo lang('Max Discount allowed is');?>" + ' "'+ parseFloat(discount).toFixed(2) + '%".');
            }else{
                $('#repair_max_dd_value').html("<?php echo lang('Max Discount allowed is');?>" + ' "'+ formatMoney(parseFloat(discount).toFixed(2)) + '".');
            }
        });

    $("#repair_discount_form").on( "submit", function( event ) {
        event.preventDefault();
        var item = ritems[item_id];
        var discount = $('#repair_discount_input').val() ? parseFloat($('#repair_discount_input').val()) : parseFloat(0);

        var dtype = $('#rtype_dd').val();
        var price = $('#price_dd').val() ? parseFloat($('#price_dd').val()) : parseFloat(0);

        if (dtype == 1){
            discount = ((parseFloat(price)) * (parseFloat(discount)/100)).toFixed(2);
        }

        ritems[item_id].discount = discount,
        ritems[item_id].discount_type = 2,
        ritems[item_id].price = parseFloat(price),
        localStorage.setItem('slitems', JSON.stringify(ritems));
        $('#rprdModal').modal('hide');
        loadRepairItems();
        return;
    });

    $(document).on('click', '.del', function () {
        var id = $(this).attr('id');
        var item = ritems[id];
        
        $(this).closest('#row_' + id).remove();
        delete ritems[id];
        if(ritems.hasOwnProperty(id)) { } else {
            localStorage.setItem('slitems', JSON.stringify(ritems));
            loadRepairItems();
            return;
        }
        calculate_price();
    });


    //

    $('#rcancel_edit').on( "click", function () {
        event.preventDefault();
        var row = $('#' + $('#prow_id').val());
        var item_id = $('#prow_id').val();
        delete ritems[item_id];
        localStorage.setItem('slitems', JSON.stringify(ritems));
        loadRepairItems();
        $('#rprModal').modal('hide');
    });

    var error = false;
    var ajax = false;
    var others = false;

    $("#rserial_form").on( "submit", function( event ) {
        event.preventDefault();
        var row = $('#' + $('#prow_id').val());
        var item_id = $('#prow_id').val();
        var type = $('#prtype').val();
        var item = ritems[item_id];

        if(item.options !== null && item.options !== false) {
            others = true;
            price = 0;
            var opt = $('#rpoption').val();
            $.each(item.options, function () {
                if(this.id == opt && this.price != '' && this.price != null) {
                    price = parseFloat(this.price);
                    name = (this.variant_name);
                }
            });
            ritems[item_id].price = price;
            ritems[item_id].option = $('#rpoption').val() ? $('#rpoption').val() : '',
            ritems[item_id].option_name = name,
            ritems[item_id].option_selected = true;

        }

        if (item.row.type == 'other' && parseInt(item.row.variable_price) == 1) {
            others = true;
            if( !$('#pprice').val() ) {
                bootbox.alert("<?php echo lang('Price cannot be empty');?>");
                return;
            }
            if( $('#pprice').val() <= 0 ) {
                bootbox.alert("<?php echo lang('Please enter a valid price');?>");
                return;
            }

            price = $('#pprice').val();
            if (parseInt(item.row.cash_out)) {
                price = price * -1;
            }

            ritems[item_id].item_details = '';
            pdescription = $('#pdescription').val();
            if (pdescription) {
                ritems[item_id].item_details = pdescription;
            }
           
            ritems[item_id].cost = 0;
            pcost = $('#pcost').val();
            if (pcost) {
                ritems[item_id].cost = pcost;
            }
           
            ritems[item_id].cost = pcost,
            ritems[item_id].price = price,
            ritems[item_id].row.variable_price = 0;
        }

        error = false;
        ajax = false;
        if(item.is_serialized) {
            if( !$('#pserial_number').val() ) {
                bootbox.alert("<?php echo lang('Serial Number cannot be empty');?>");
                return;
            }
            var type    = $('#pserial_number').data('type');
            var id      = $('#pserial_number').data('id');
            var serial  = $('#pserial_number').val();
            ajax        = true;
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('panel/pos/verifyProductSerial'); ?>',
                dataType: "JSON", // edit: fixed ;)
                data: {
                    type: $('#pserial_number').data('type'),
                    id: $('#pserial_number').data('id'),
                    term: $('#pserial_number').val(),
                },
                success: function(data) { 
                    if (data) {
                        ritems[item_id].serial_number =  serial?serial:'';
                        ritems[item_id].serialed =  true;
                    }else{
                        $('#rprModal').appendTo("body").modal({backdrop: 'static', keyboard: false});
                        $('#rprModal').css('display', 'block');
                        bootbox.alert("<?php echo lang('Incorrect Serial Number');?>");
                        error = true;
                    }
                } 
            });
        }

        
        if (ajax) {
            $(document).ajaxStop(function () {
                if (error) {
                    return;
                }else{
                    localStorage.setItem('slitems', JSON.stringify(ritems));
                    $('#rprModal').modal('hide');
                    loadRepairItems();
                    return;
                }
            });
        }else{
            localStorage.setItem('slitems', JSON.stringify(ritems));
            $('#rprModal').modal('hide');
            loadRepairItems();
            return;
        }
    });
</script>