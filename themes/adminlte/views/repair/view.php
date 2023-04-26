
<div class="box box-primary ">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo lang('repair/index');?></h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12 col-lg-4 bio-row">
                <p><span class="bold"><i class="fas fa-user"></i> <?php echo lang('client_title'); ?></span><span id="rv_client"></span></p>
            </div>

            <div class="col-md-12 col-lg-4 bio-row">
                <p><span class="bold"><i class="fas fa-phone"></i> <?php echo lang('client_telephone'); ?></span><span id="rv_telephone"></span></p>
            </div>

            <div class="col-md-12 col-lg-4 bio-row stato">
                <p><span class="bold"><i class="fas fa-signal"></i> <?php echo lang('repair_condition'); ?> </span><span id="rv_condition"></span></p>
            </div>
            <div class="col-md-12 col-lg-4 bio-row">
                <p><span class="bold"><i class="fas fa-calendar"></i> <?php echo lang('repair_opened_at'); ?> </span><span id="rv_created_at"></span></p>
            </div>
            <div class="col-md-12 col-lg-4 bio-row">
                <p><span class="bold"><i class="fas fa-link"></i> <?php echo lang('repair_defect'); ?> </span><span id="rv_defect"></span></p>
            </div>
            <div class="col-md-12 col-lg-4 bio-row">
                <p><span class="bold"><i class="fas fa-folder-open"></i> <?php echo lang('repair_category'); ?> </span><span id="rv_category"></span></p>
            </div>
            <div class="col-md-12 col-lg-4 bio-row">
                <p><span class="bold"><i class="fas fa-tag"></i> <?php echo lang('repair_imei'); ?> </span><span id="rv_imei"></span></p>
            </div>
            <div class="col-md-12 col-lg-4 bio-row">
                <p><span class="bold"><i class="fas fa-tag"></i> <?php echo lang('model_manufacturer'); ?> </span><span id="rv_manufacturer"></span></p>
            </div>
            <div class="col-md-12 col-lg-4 bio-row">
                <p><span class="bold"><i class="fas fa-tag"></i> <?php echo lang('repair_model'); ?> </span><span id="rv_model"></span></p>
            </div>

            <div class="col-md-12 col-lg-4 bio-row">
                <p><span class="bold"><i class="fas fa-pound-sign"></i><?php echo lang('repair_advance'); ?></span><span id="rv_advance"></span></p>
            </div>
            <div class="col-md-12 col-lg-4 bio-row nofloat">
                <p><span class="bold"><i class="fas fa-pound-sign"></i><?php echo lang('repair_price'); ?> </span><span id="rv_price"></span></p>
            </div>
            <div class="col-md-12 col-lg-4 bio-row">
                <p><span class="bold"><i class="fas fa-phone"></i> <?php echo lang('client_telephone'); ?> </span><span id="rv_phone_number"></span></p>
            </div>
            <div class="col-md-12 col-lg-4 bio-row">
                <p><span class="bold"><i class="fas fa-eye"></i> <?php echo lang('repair_code'); ?> </span><span id="rv_rep_code"></span></p>
            </div>

            <div class="col-md-12 col-lg-4 bio-row">
                <p><span class="bold"><i class="fas fa-eye"></i> <?php echo lang('Assign Repair To'); ?> </span><span id="rv_assigned_to"></span></p>
            </div>

            <?php
            $custom_fields = explode(',', $settings->custom_fields);
            foreach($custom_fields as $line){
                if ($line) {
            ?>
                <div class="col-md-12 col-lg-4 bio-row">
                    <p><span class="bold"><i class="fas fa-info-circle"></i> <?php echo $line; ?> </span><span class="show_custom" id="v<?php echo bin2hex($line); ?>"></span></p>
                </div>
            <?php } } ?>


             <div class="col-md-12">
                            <label class="table-label" for="combo"><?php echo lang("defective_items"); ?></label>

                            <div class="controls table-controls">
                                <table id="prTable"
                                       class="table items table-striped table-bordered table-condensed table-hover">
                                    <thead>
                                    <tr>
                                        <th class="col-md-5"><?php echo lang("product_name") . " (" . lang("product_code") . ")"; ?></th>
                                        <th class="col-md-2"><?php echo lang("unit_cost"); ?></th>
                                        <th class="col-md-2"><?php echo lang("unit_price"); ?></th>
                                        <th class="col-md-2"><?php echo lang('Discount');?></th>
                                        <th class="col-md-2"><?php echo lang('Tax');?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <td colspan="5"><?php echo lang('table_empty');?></td>
                                    </tbody>

                                </table>
                                <table class="table items table-striped table-bordered table-condensed table-hover">
                                    <tfoot>
                                        <tr>
                                            <th colspan="1" class="warning"><span class="pull-right"><?php echo lang('subtotal')?></span></th>
                                            <th colspan="1" class="info"><span id="price_span">0.00</span></th>

                                            <th colspan="1" class="warning"><span class="pull-right"><?php echo lang('Total Tax');?></span></th>
                                            <th colspan="1" class="success"><span id="tax_spane">0.00</span></th>

                                        </tr>
                                        <tr id="labor_tr">
                                            <th colspan="1" class="warning"><span class="pull-right"><?php echo lang('labor_cost_summay')?></span></th>
                                            <th colspan="1" class="success"><span id="sc_span">0.00</span></th>
                                            <th colspan="1" class="warning"><span class="pull-right"><?php echo lang('labor_cost_summay')?> + <?php echo lang('total'); ?></span></th>
                                            <th colspan="1" class="success"><span id="totalprice_span">0.00</span></th>

                                        </tr>
                                        <tr>
                                            <th colspan="3" class="warning"><span class="pull-right"><?php echo lang('grand_total'); ?></span></th>
                                            <th class="success"><span id="gtotal">0.00</span></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6 bio-row fastsms">
                        <div class="form-group rv_comment">
                            <?php echo lang('quick_sms', 'fastsms');?>
                            <textarea class="form-control" id="fastsms" rows="6" placeholder="Instantly send a text message to the client by entering your text here"></textarea>
                            <button type="button" id="sendsmsfast" class="btn btn-xs btn-primary"><i class="fas fa-check"></i> <?php echo lang('send');?></button>
                        </div>
                    </div>
                    <div class="col-md-6 bio-row textareacom">
                        <div class="form-group comment">
                            <?php echo lang('repair_comment', 'rv_comment'); ?>&nbsp;(<?php echo lang('please_note_defects'); ?>)
                            <textarea class="form-control" id="rv_comment" rows="6"></textarea>
                            <button type="button" id="updateComment" class="btn btn-xs btn-primary"><i class="fas fa-check"></i> <?php echo lang('update');?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box-footer" id="footerOR">
    </div>
</div>

<script type="text/javascript">
        data = <?php echo json_encode($repair); ?>;

    var hasOwnProperty = Object.prototype.hasOwnProperty;
    function isEmptyObject(obj) {
        if (obj == null) return true;
        if (obj.length > 0)    return false;
        if (obj.length === 0)  return true;
        if (typeof obj !== "object") return true;
        for (var key in obj) {
            if (hasOwnProperty.call(obj, key)) return false;
        }
        return true;
    }

    var items = {};
    
    
    function add_product_item_repair(item, edit_item=true) {
        $.post( "<?php echo base_url();?>panel/inventory/setSelected", { stock_id: item.stock_id} );

        if (item == null) {
            return false;
        }


        if (item.type != 'service') {

            t_qty = parseInt(item.total_qty);
            selected_qty = 0;
            $.each(items, function(){
                if (this.product_id == item.product_id) {
                    selected_qty += 1;
                }
            });

            if (selected_qty+1 > t_qty) {
                bootbox.alert("<?php echo lang('You dont have this item in stock');?>");
                return false;
            }

            // items[item_id].available_now -= 1;
        }

        item_id = item.row_id;
        items[item_id] = item;

        localStorage.setItem('slitems', JSON.stringify(items));
        loadRVItems(edit_item);
        return true;
    }
     function loadRVItems(edit_items = true) {
        if (localStorage.getItem('slitems')) {
            items = JSON.parse(localStorage.getItem('slitems'));
            var pp = 0;
            var total_tax = 0;


            $("#prTable tbody").empty();
            $.each(items, function () {
                var row_no = this.row_id;
                var item_id = this.product_id;
                if (this.cost === null) {
                    if (localStorage.getItem('to_order')) {
                        var order_items = JSON.parse(localStorage.getItem('to_order'));
                        item_id = this.row_id;
                        order_items[item_id] = this;
                        localStorage.setItem('to_order', JSON.stringify(order_items));
                    }else{
                        order_items = {};
                        order_items[this.row_id] = this;
                        localStorage.setItem('to_order', JSON.stringify(order_items));
                    }

                }

                var price = this.price;
                var discount = this.discount;

                var sel_opt = '';
                var item_option = this.option;
                $.each(this.options, function (x, y) {
                    if(y.id == item_option) {
                        sel_opt = y.variant_name;
                        price = y.price;
                    }
                });
                var product_option = '';
                var product_variant = '';

                if ((this.variants == true && this.option_selected == false) || (!this.serialed)) {
                    var opt = '<p style="margin: 12px 0 0 0;">n/a</p>';
                    if(this.options !== false) {
                        var o = 1;
                        opt = $("<select id=\"poption\" name=\"poption\" class=\"form-control select\" />");
                        $.each(this.options, function (x, y) {
                            if(o == 1) {
                                price = y.price;
                                sel_opt = this.variant_name;
                                if(product_option == '') { product_variant = y.id; } else { product_variant = product_option; }
                            }
                            $("<option/>", {value: y.id, text: y.variant_name}).appendTo(opt);
                            o++;
                        });
                    } else {
                        product_variant = 0;
                    }
                    var serial = '';
                    if (this.is_serialized) {
                        serial = $("<label class=\"col-sm-4 control-label\">Serial Number: </label><div class=\"col-sm-8\"><input id=\"pserial_number\" name=\"pserial_number\" class=\"form-control select\" /></div>");
                    }
                    $('#myModalLG #pserial_number-div').html(serial);
                    $('#myModalLG #poptions-div').html(opt);
                    $('#myModalLG #id-div').html('<input type="hidden" name="prow_id" id="prow_id" value="'+row_no+'">');
                    $('#myModalLG #prModal').appendTo("body").modal('show');
                }

                var product_tax = 0;
                var pr_tax = this.pr_tax;
                var pr_tax_val = 0, pr_tax_rate = [];
                var pr_tax_val_fixed = 0;
                if(this.taxable == 1){
                    $.each(pr_tax, function (tax, tax_detaild) {
                        if (tax_detaild !== false) {
                            if (tax_detaild.type == 1) {
                                pr_tax_val += parseFloat(tax_detaild.rate);
                                pr_tax_rate[tax_detaild.id] = formatDecimal(tax_detaild.rate) + '%';
                            } else if (tax_detaild.type == 2) {
                                pr_tax_val_fixed += parseFloat(tax_detaild.rate);
                                pr_tax_rate[tax_detaild.id] = formatDecimal(tax_detaild.rate);
                            }
                        }
                    });
                    percent_tax = formatDecimal((parseFloat(price) - parseFloat(discount)) * parseFloat(pr_tax_val) / (100), 4);
                    pr_tax_val_fixed = parseFloat(pr_tax_val_fixed);
                    product_tax = percent_tax + pr_tax_val_fixed;
                }
                pr_tax_rate = pr_tax_rate.filter(function(e){ return e === 0 || e }).join(', ');

                var newTr = $('<tr id="row_' + row_no + '" class="item_' + this.id + '" data-item-id="' + row_no + '"></tr>');
                tr_html = '<td><input name="item_discount[]" id="item_discount" type="hidden" value="' + discount + '"><input name="item_id[]" id="item_id" type="hidden" value="' + this.product_id + '"><input name="item_cost[]" id="item_cost" type="hidden" value="' + this.cost + '"><input name="item_name[]" type="hidden" value="' + this.name + '"><input name="item_code[]" type="hidden" value="' + this.code + '"><input name="product_option[]" type="hidden" class="roption" value="' + item_option + '"><input name="item_serial[]" id="item_serial" type="hidden" value="' + this.serial_number + '"><span class="sname" id="name_' + row_no + '">' + this.name + '<br><small>'+this.item_details+'</small>' +(sel_opt != '' ? ' ('+sel_opt+')' : '')+'</span>';
                

                tr_html += '</td><td class="col-md-2">'+formatMoney(this.cost)+'</td>';


                tr_html += '</td><td class="col-md-2">'+formatMoney(price)+'<input class="form-control text-center rprice" name="item_price[]" type="hidden" value="' + (price) + '" data-id="' + row_no + '" data-item="' + this.product_id + '" id="item_price_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td class="col-md-2">'+formatMoney(discount)+'</td>';
                tr_html += '<td class="col-md-2">'+formatMoney(product_tax)+'<input class="form-control text-center rtax" name="item_tax[]" type="hidden" value="' + formatDecimal(product_tax) + '" data-id="' + row_no + '" data-item="' + this.product_id + '" id="item_price_' + row_no + '" onClick="this.select();"><input class="form-control text-center" name="item_tax_id[]" type="hidden" value="' + encodeURIComponent(JSON.stringify(pr_tax)) + '"></td>';
                
                newTr.html(tr_html);
                newTr.prependTo("#prTable");
                pp += ((parseFloat(price) - parseFloat(discount)));
                total_tax += product_tax;
                $('.item_' + item_id).addClass('warning');

            });
            $('#myModalLG #price_span').html(formatDecimal(pp));
            var service_charges = parseFloat(data.service_charges);
            var total_ = parseFloat(pp) + parseFloat(service_charges)+ parseFloat(total_tax);
            $('#myModalLG #totalprice_span').html(formatDecimal(total_));
            $('#myModalLG #tax_spane').html(formatDecimal(total_tax));
            $('#myModalLG #sc_span').html(formatDecimal(service_charges));
            $('#myModalLG #gtotal').html(formatDecimal(total_));
            var deposit = parseFloat($('#myModalLG #advance').val());
            $('#myModalLG #deposit_span').html(formatDecimal(deposit));
            $('#myModalLG #balance_span').html(formatDecimal(parseFloat(total_) - parseFloat(deposit)));

        }else{
            $('#myModalLG #price_span').html(0);
            var service_charges = parseFloat(data.service_charges);
            var total_ = parseFloat(service_charges);
            $('#myModalLG #totalprice_span').html(formatDecimal(total_));
            $('#myModalLG #tax_spane').html(formatDecimal(total_tax));
            $('#myModalLG #sc_span').html(formatDecimal(service_charges));
            $('#myModalLG #gtotal').html(formatDecimal(total_));
            var deposit = parseFloat($('#myModalLG #advance').val());
            $('#myModalLG #deposit_span').html(formatDecimal(deposit));
            $('#myModalLG #balance_span').html(formatDecimal(parseFloat(total_) - parseFloat(deposit)));
        }
    }

    $(document).ready(function () {
        if (localStorage.getItem('to_order')) {
            localStorage.removeItem('to_order');
        }
        if (localStorage.getItem('slitems')) {
            localStorage.removeItem('slitems');
        }
        items = {};
        $('#myModalLG #prTable tbody').empty();
        $('#myModalLG #prTable tbody').html('<tr><td colspan="4">nothing to display</td></tr>');

        jQuery('#rv_client').html(data.name);
        jQuery('#rv_telephone').html(data.telephone);
        jQuery('#rv_imei').html(data.serial_number);
        jQuery('#rv_condition').html(data.status);
        jQuery('#rv_created_at').html(fld(data.date_opening));
        jQuery('#rv_defect').html(data.defect);
        jQuery('#rv_category').html(data.category);
        jQuery('#rv_model').html(data.model_name);
        jQuery('#rv_manufacturer').html(data.manufacturer_name);
        jQuery('#rv_advance').html(formatMoney(data.advance));
        jQuery('#rv_price').html(formatMoney(data.grand_total));
        jQuery('#rv_phone_number').html((data.telephone).replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3'));
        jQuery('#rv_rep_code').html(data.code);
        jQuery('#rv_comment').html(data.comment);
        jQuery('#rv_assigned_to').html(data.assigned_to_name);

        var ci = data.items;
        if (!isEmptyObject(ci)) {
            $.each(ci, function() { add_product_item_repair(this, false); });
        }else{
            items = {};
            $('#myModalLG #prTable tbody').empty();
            $('#myModalLG #prTable tbody').html('<tr><td colspan="4">nothing to display</td></tr>');
            loadRVItems();
        }


        jQuery('.show_custom').html('');
        var IS_JSON = true;
        try
        {
            var json = $.parseJSON(data.custom_field);
        }
        catch(err)
        {
            IS_JSON = false;
        }
        if(IS_JSON)
        {
            $.each(json, function(id_field, val_field) {
                jQuery('#v'+id_field).html(val_field);
            });
        }

        
        
        var string = "<div class=\"pull-right btn-group\"><button type=\"button\" data-type=\"2\" data-num=\"" + data.id + "\" id=\"print_repair\" class=\"btn btn-default\"><i class=\"fas fa-print\"></i> <?php echo lang('report'); ?></button>";

        if (parseInt(data.pos_sold) == 1 && data.invoice.id) {

            string += "<a class='btn btn-default' target='_blank' href='<?=base_url();?>panel/pos/view/"+data.invoice.sale_id+"/'><i class='fas fa-print'></i> <?=lang('invoice');?></a>";
        }else{
            string += "<a class='btn btn-default' target='_blank' href='<?=base_url();?>panel/repair/invoice/"+data.id+"/1'><i class='fas fa-print'></i> <?=lang('invoice');?></a>";

        }


        string +="<a class=\"btn btn-default\" href=\"<?php echo base_url('panel/repair'); ?>\"><i class=\"fas fa-reply\"></i> <?php echo lang('go_back');?></a></div><div class=\"btn-group pull-left\">";
        string += "<button class='btn btn-danger' id='delete_repair' data-num='"+data.id+"'><i class='fas fa-trash'></i> <?=lang('delete');?></button>";

        

        string += '<button id="upload_modal_btn" class="btn btn-default" data-mode="edit" data-num="'+data.id+'"><i class="fa fa-cloud"></i> <?php echo lang('view_attached');?></button>';

        
        <?php if($this->Admin || $GP['repair-edit']): ?>

        string += "<a class=\"btn btn-success\" data-dismiss='modal' id='modify_reparation' href='#repairmodal' data-toggle='modal' data-num='"+data.id+"'><i class='fas fa-edit'></i> <?=lang('edit_repair');?></a>"; 
        <?php endif;?>

        string = string + "</div>";

        if (data.status > 0) {
            jQuery('#rv_condition').html(data.status_name);
            jQuery('#rv_condition').css('color',data.fg_color);
            jQuery('#rv_condition').css('background-color',data.bg_color);
        } else {
            jQuery('#rv_condition').html("<?php echo lang('cancelled');?>");
            jQuery('#rv_condition').css('color', '#FFF');
            jQuery('#rv_condition').css('background-color', '#000');

        }

        jQuery('#footerOR').html(string+'<div class="clearfix"></div>');
    });
    (function(){
    jQuery(document).on("click", "#toggle", function () {
        var num = jQuery(this).data("num");
        var mode = jQuery(this).data("mode");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/repair/toggle",
            data: "id=" + encodeURI(num) +"&toggle=" + encodeURI(mode),
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
                toastr['success']("<?php echo lang('Toggle');?>: ", data.toggle);
                window.location.reload();
            }
        });
    });

    jQuery(document).on("click", "#sendsmsfast", function() {
        var txt = jQuery('#fastsms').val();
        var number = jQuery('#rv_phone_number').html();
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/repair/send_sms",
            data: "text=" + txt + "&number=" + number + "&token=<?php echo $_SESSION['token'];?>",
            cache: false,
            dataType: "json",
            success: function(data) {
                if(data.status == true) toastr['success']("<?php echo $this->lang->line('quick_sms');?>", '<?php echo $this->lang->line('sms_sent');?>');
                else toastr['error']("<?php echo $this->lang->line('quick_sms');?>", '<?php echo $this->lang->line('sms_not_sent');?>');
            }
        });
    });

    jQuery(document).on("click", "#updateComment", function() {
        var txt = jQuery('#rv_comment').val();
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/repair/updateComment",
            data: "comment=" + txt + "&id=" + data.id,
            cache: false,
            dataType: "json",
            success: function(data) {
                if(data.success == true) toastr['success']("<?php echo $this->lang->line('update');?>", '<?php echo $this->lang->line('repair_comment');?>');
                else toastr['error']("<?php echo $this->lang->line('update');?>", '<?php echo $this->lang->line('repair_comment');?>');
            }
        });
    });


}());
</script>
            