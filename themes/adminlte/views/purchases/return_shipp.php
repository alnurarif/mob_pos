<!-- Modal -->
<div class="modal fade" id="return_ship" tabindex="-1" role="dialog" aria-labelledby="returnShipTit">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="returnShipTit"><?php echo lang('Return');?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form id="return_ship_form">
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo lang('shipping', 'poshipping'); ?>
                                <?php echo form_input('shipping', '', 'class="form-control input-tip" id="poshipping"'); ?>
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
                                    <?php echo form_dropdown('shipping_provider', $dp_p, '' ,'class="form-control" id="provider_select" style="width: 100%"'); ?>
                                </div>
                                <div class="inp_provider">
                                    <input id="provider_input" name="provider_input" type="text" class="validate form-control">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" id="footerShipfoot"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#provider_select').select2();
    jQuery('.inp_provider').hide();
    jQuery("#provider_select").on("select2:select", function (e) {
        var selected = jQuery("#provider_select").val();
        if(selected === 'other') {
            jQuery('.select_provider').hide();
            jQuery('.inp_provider').show();
            jQuery('#provider_input').val('');
            jQuery('#provider_input').focus();
        } else {
            jQuery('#category_select').val(selected);
        }
    });
    $('#return_ship_form').on( "submit", function(event) {
        event.preventDefault();
        var id = jQuery('#submit').data("num");
        var page = jQuery('#submit').data("refresh");

        url = base_url + "panel/purchases/ship_complete";
        dataString = $(this).serialize() + '&id='+id;
        jQuery.ajax({
            type: "POST",
            url: url,
            data: dataString,
            cache: false,
            success: function (data) {
                data = JSON.parse(data);
                if (data.success) {
                    if (page) {
                        window.location.reload();
                    }
                    toastr['success']("<?php echo lang('Return Purchase Shipment Details are successfully added.');?>");
                    setTimeout(function () {
                        $('#return_ship').modal('hide');
                        $('#myModal').modal('hide');
                        $('#POData').DataTable().ajax.reload();
                    }, 500);
                }else{
                    bootbox.alert(data.message);
                }
            }
        });
        return false;
    });

    jQuery(".ship_return").on("click", function (e) {
        var num = $(this).data('num');
        var page = $(this).data('refresh');
        $('#return_ship').appendTo("body").modal('show');
        jQuery('#return_ship_cost').val('');
        jQuery('#return_ship_carrier').val('');
        jQuery('#return_ship_code').val('');
        jQuery('#returnShipTit').html("<?php echo lang('Return Purchase - Ship Complete');?>");
        jQuery('#footerShipfoot').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button type="submit" class="btn btn-success" id="submit" data-mode="add" data-refresh="'+page+'" data-num="'+num+'" form="return_ship_form" value="Submit"><?php echo lang('Submit');?></button>');
    });

    jQuery(".return_accept").on("click", function (e) {
        var num = $(this).data('num');
        var page = $(this).data('refresh');

        url = base_url + "panel/purchases/return_complete";
        jQuery.ajax({
            type: "POST",
            url: url,
            data: 'id='+num,
            cache: false,
            success: function (data) {
                data = JSON.parse(data);
                if (data.success) {
                    if (page) {
                        window.location.reload();
                    }
                    toastr['success']("<?php echo lang('Return Purchase Accepted');?>");
                    setTimeout(function () {
                        $('#myModal').modal('hide');
                        $('#POData').DataTable().ajax.reload();
                    }, 500);
                }else{
                    bootbox.alert(data.message);
                }
            }
        });
    });
</script>