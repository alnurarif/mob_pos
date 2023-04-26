<?php
if ($this->uri->segment(5) == 'available' or $this->uri->segment(5) == 'locked') {
    $type = $this->uri->segment(5);
}else{
    $type = NULL;
}
?>
<style type="text/css">
    span.select2-container {
        z-index:10050;
    }
</style>
<script>
function action(x) {
    var pqc = x.split("___");
    <?php if($this->Admin || $GP['store-disable']): ?>
        if (pqc[1] == 1) {
            var button = "<a id='toggle_store' data-num='"+pqc[0]+"' data-mode='enable'>" + "<button class='btn btn-danger btn-xs'><i class='fas fa-toggle-off'></i> <?php echo lang('Unlock');?></button>" +"</a>";
        }else{
            var button = "<a id='toggle_store' data-num='"+pqc[0]+"' data-mode='disable'>" + "<button class='btn btn-success btn-xs'><i class='fas fa-toggle-on'></i> <?php echo lang('Lock');?></button>" +"</a>";
        }
    <?php endif; ?>

    var return_var = "";

    <?php if($this->Admin || $GP['store-edit']): ?>
    return_var += "<a  data-dismiss='modal' id='modify' href='#storemodal' data-toggle='modal' data-num='"+pqc[0]+"'><button class='btn btn-primary btn-xs'><i class='fas fa-edit'></i> <?php echo lang('Edit');?></button></a>";
    <?php endif; ?>

    <?php if($this->Admin || $GP['store-disable']): ?>
        return_var += button;
    <?php endif; ?>

    <?php if($this->Admin || $GP['store-delete']): ?>
        return_var += "<a id='delete' data-num='"+pqc[0]+"'>" + "<button class='btn btn-danger btn-xs'><?php echo lang('Delete');?></button>" +"</a>";
    <?php endif; ?>

    return return_var;
}
    $(document).ready(function () {
    $('.select2').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent()
        });
    });

    var oTable = $('#dynamic-table').dataTable({
        "aaSorting": [[0, "asc"]],
        "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "iDisplayLength": <?=$settings->rows_per_page;?>,
        'bProcessing': true, 'bServerSide': true,
        'sAjaxSource': '<?php echo base_url(); ?>panel/settings/store/getAll/<?php echo $type;?>',
        'fnServerData': function (sSource, aoData, fnCallback) {
            aoData.push({
                "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                "value": "<?php echo $this->security->get_csrf_hash() ?>"
            });
            $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
        }, 
        "aoColumns": [
        null,
        {mRender: action},
        ],
       
    });
    
});


    jQuery(document).on("click", "#toggle_store", function () {
        var num = jQuery(this).data("num");
        var mode = jQuery(this).data("mode");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/settings/store/toggle",
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
                toastr['success']("<?php echo lang('Toggle');?>: ", data.toggle);
                $('#dynamic-table').DataTable().ajax.reload();
            }
        });
    });

     jQuery(document).on("click", "#delete", function () {
        var num = jQuery(this).data("num");

        bootbox.confirm({ 
              message: "<?php echo lang('action_cannot_be_undone');?>", 
              buttons: {
                confirm: {
                    label: '<?php echo lang('yes');?>',
                    className: 'btn-success'
                },
                cancel: {
                    label: '<?php echo lang('no');?>',
                    className: 'btn-danger'
                }
            },
              callback: function(result){ 
                if (result) {
                    jQuery.ajax({
                        type: "POST",
                        url: base_url + "panel/settings/store/delete",
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
                } 
            }
        })
    });

</script>

<!-- ============= MODAL MODIFICA supplierI ============= -->
<div class="modal fade" id="storemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="titsupplieri"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
            
                  
                    <p class="tips custip"></p>
                    <div class="row">
                        <form id="store_form" method="post" data-parsley-validate>
                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <label><?php echo lang('store_name');?></label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-user"></i>
                                        </div>
                                        <input name="name" type="text" class="validate form-control" required>
                                    </div>
                                        <span id="errorText"></span>
                                </div>
                            </div>

                          

                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <?php echo lang('invoice_email', 'email');?>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-quote-left"></i>
                                        </div>
                                        <input required name="email" id="email" type="text" class="validate form-control" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <?php echo lang('invoice_phone', 'phone');?>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <input required id="phone" name="phone" type="text" class="validate form-control">
                                    </div>
                                </div>
                            </div>
                                
                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <?php echo lang('timezone', 'timezone');?>
                                    <?php 
                                    $tr = array();
                                    foreach ($timezones as $timezone) {
                                        $tr[$timezone] = $timezone;
                                    }
                                    echo form_dropdown('timezone', $tr, '', 'class="form-control tip select2" id="timezone" required="required" style="width:100%;"');

                                    ?>
                                </div>
                            </div>
                              <div class="col-md-12 col-lg-12 input-field">
                                <div class="form-group">
                                    <?php echo lang('invoice_address', 'address');?>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas fa-street-view"></i>
                                        </div>
                                        <input id="address" required name="address" type="text" class="validate form-control">
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-6 form-group ">
                                <label class="control-label" for="city"><?php echo lang('City');?>:</label>
                                <input class="form-control" name="city" type="text" id="city" required >
                            </div>
                            <div class="col-md-3 form-group ">
                                <label class="control-label" for="state"><?php echo lang('State/Province');?>:</label>
                                <input class="form-control" name="state" type="text" id="state" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="control-label" for="zip"><?php echo lang('Postal/Zip Code');?>:</label>
                                <input class="form-control" name="zip" type="text" id="zip" required >
                            </div>

                            <div class="col-sm-12">

                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label><?php echo lang('New Phone Taxes');?></label>
                                        <?php 
                                        foreach ($tax_rates as $rate):  ?>
                                            <div class="checkbox-styled">
                                                <input class="checkbox_req" type="checkbox" name="new_phone_tax[]" id="new_phone_tax_<?php echo $rate->id;?>" required value="<?php echo $rate->id; ?>">
                                                <label for="new_phone_tax_<?php echo $rate->id;?>"><?php echo escapeStr($rate->name); ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label><?php echo lang('Used Phone Taxes');?></label>
                                        <?php foreach ($tax_rates as $rate): ?>
                                            <div class="checkbox-styled">
                                                <input type="checkbox" name="used_phone_tax[]" id="used_phone_tax<?php echo $rate->id;?>" required value="<?php echo $rate->id; ?>">
                                                <label for="used_phone_tax<?php echo $rate->id;?>"><?php echo escapeStr($rate->name); ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label><?php echo lang('Accessories Taxes');?></label>
                                        <?php foreach ($tax_rates as $rate): ?>
                                            <div class="checkbox-styled">
                                                <input type="checkbox" name="accessories_tax[]" id="accessories_tax<?php echo $rate->id;?>" required value="<?php echo $rate->id; ?>">
                                                <label for="accessories_tax<?php echo $rate->id;?>"><?php echo escapeStr($rate->name); ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                 <div class="col-lg-4">
                                    <div class="form-group">
                                        <label><?php echo lang('Repair Item Taxes');?></label>
                                        <?php foreach ($tax_rates as $rate): ?>
                                            <div class="checkbox-styled">
                                                <input type="checkbox" name="repair_items_tax[]" id="repair_items_tax<?php echo $rate->id;?>" required value="<?php echo $rate->id; ?>">
                                                <label for="repair_items_tax<?php echo $rate->id;?>"><?php echo escapeStr($rate->name); ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label><?php echo lang('Other Item Taxes');?></label>
                                         <?php foreach ($tax_rates as $rate): ?>
                                            <div class="checkbox-styled">
                                                <input type="checkbox" name="other_items_tax[]" id="other_items_tax<?php echo $rate->id;?>" required value="<?php echo $rate->id; ?>">
                                                <label for="other_items_tax<?php echo $rate->id;?>"><?php echo escapeStr($rate->name); ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label><?php echo lang('Default Plan Taxes');?></label>
                                         <?php foreach ($tax_rates as $rate): ?>
                                            <div class="checkbox-styled">
                                                <input type="checkbox" required name="plans_tax[]" id="plans_tax<?php echo $rate->id;?>" value="<?php echo $rate->id; ?>">
                                                <label for="plans_tax<?php echo $rate->id;?>"><?php echo escapeStr($rate->name); ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div> 
                                </div>
                                </div>

                        </form>
                    </div>
                </div>
                <div class="modal-footer" id="footersupplier1">
                      <!--    -->
                </div>
            </div>
        </div>
    </div>
</div>


<?php if($this->Admin || $GP['store-add']): ?>
    <button href="#storemodal" class="add_store btn btn-primary">
        <i class="fas fa-plus-circle"></i> <?php echo lang('add').' '.lang('Store'); ?>
    </button>
<?php endif; ?>


<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('settings/store');?></h3>
      <div class="box-tools pull-right">
         <div class="btn-group">
            <a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>panel/settings/store/"><?php echo lang('All');?></a>
            <a class="btn btn-sm btn-success" href="<?php echo base_url(); ?>panel/settings/store/index/available"><i class='fas fa-toggle-on'></i> <?php echo lang('Available');?></a>
            <a class="btn btn-sm btn-danger" href="<?php echo base_url(); ?>panel/settings/store/index/locked"><i class='fas fa-toggle-off'></i> <?php echo lang('Locked');?></a>
        </div>
      </div>
  </div>
  <div class="box-body">
    <div class="adv-table">
            <table class=" compact table table-bordered table-striped" id="dynamic-table" width="100%">
                <thead>
                    <tr>
                        <th><?php echo lang('tax_name'); ?></th>
                        <th><?php echo lang('actions'); ?></th>
                    </tr>
                </thead>
        
                <tfoot>
                    <tr>
                        <th><?php echo lang('tax_name'); ?></th>
                        <th><?php echo lang('actions'); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
  </div>
</div>
<script type="text/javascript">
   
    jQuery(".add_store").on("click", function (e) {
        $('#storemodal').modal('show');
        
        
        jQuery('input[name=name]').val('');
        jQuery('input[name=email]').val('');
        jQuery('input[name=phone]').val('');
        jQuery('input[name=timezone]').val('');
        jQuery('input[name=address]').val('');
        jQuery('input[name=city]').val('');
        jQuery('input[name=state]').val('');
        jQuery('input[name=zip]').val('');
        $(":checked").prop("checked", false);
        jQuery('#titsupplieri').html("<?php echo lang('add').' '.lang('Store'); ?>");
        jQuery('#footersupplier1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i><?php echo lang("go_back"); ?></button><button role="button" form="store_form" id="submit_store" class="btn btn-success" data-mode="add"><i class="fas fa-user"></i> <?php echo lang("add"); ?></button>');
    });

    jQuery(document).on("click", "#modify", function () {
        jQuery('#titsupplieri').html("<?php echo lang('edit').' '.lang('Store'); ?>");
        
            var num = jQuery(this).data("num");
            jQuery.ajax({
                type: "POST",
                url: base_url + "panel/settings/store/byID",
                data: "id=" + encodeURI(num) + "&token=<?php echo $_SESSION['token'];?>",
                cache: false,
                dataType: "json",
                success: function (data) {
                    jQuery('#store_form input[name=name]').val(data.name);
                    jQuery('#store_form input[name=address]').val(data.address);
                    jQuery('#store_form input[name=email]').val(data.invoice_mail);
                    jQuery('#store_form input[name=phone]').val(data.phone);
                    jQuery('#store_form #timezone').val(data.timezone).trigger('change');
                    jQuery('#store_form #city').val(data.city);
                    jQuery('#store_form #state').val(data.state);
                    jQuery('#store_form #zip').val(data.zipcode);
                    $(":checked").prop("checked", false);
                    $.each((data.new_phone_tax).split(","), function(i,e){
                        $("#new_phone_tax_"+e).prop("checked", true);
                    });
                    $.each((data.used_phone_tax).split(","), function(i,e){
                        $("#used_phone_tax"+e).prop("checked", true);
                    });
                    $.each((data.accessories_tax).split(","), function(i,e){
                        $("#accessories_tax"+e).prop("checked", true);
                    });
                    $.each((data.repair_items_tax).split(","), function(i,e){
                        $("#repair_items_tax"+e).prop("checked", true);
                    });
                    $.each((data.other_items_tax).split(","), function(i,e){
                        $("#other_items_tax"+e).prop("checked", true);
                    });
                    $.each((data.plans_tax).split(","), function(i,e){
                        $("#plans_tax"+e).prop("checked", true);
                    });

                    jQuery('#footersupplier1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button role="button" form="store_form" id="submit_store" class="btn btn-success" data-mode="modify" data-num="' + encodeURI(num) + '"><i class="fas fa-save"></i> <?php echo lang("save"); ?></button>')
                }
            });
        });

   $('#store_form').on( "submit", function(event) {

        event.preventDefault();
        var mode = jQuery('#submit_store').data("mode");
        var id = jQuery('#submit_store').data("num");
        
        if ($(this).parsley().isValid()) {
            var url = "";
            var dataString = "";

            if (mode == "add") {
                $('#submit_store').attr('disabled', true);
                url = base_url + "panel/settings/store/add";
                dataString = $('#store_form').serialize();
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        if (data) {
                            toastr['success']("<?php echo lang('add'); ?>", "<?php echo lang('Store'); ?>: " + name + " <?php echo lang('added'); ?>");
                            setTimeout(function () {
                                $('#storemodal').modal('hide');
                                find(data);
                                $('#dynamic-table').DataTable().ajax.reload();
                                $('#submit_store').attr('disabled', false);
                            }, 500);
                        } else {
                            toastr['info']("<?php echo lang('failed_to_create_store');?>");
                            $('#submit_store').attr('disabled', false);
                        }
                       
                    },
                    error: function (data) {
                        $('#submit_store').attr('disabled', false);
                    }
                });
            } else {
                url = base_url + "panel/settings/store/edit";
                dataString = $('#store_form').serialize() + "&id=" + encodeURI(id);
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        toastr['success']("<?php echo lang('save'); ?>", "<?php echo lang('Store'); ?>: " + name + "<?php echo lang('updated'); ?>");
                        setTimeout(function () {
                            $('#storemodal').modal('hide');
                            find(id);
                            $('#dynamic-table').DataTable().ajax.reload();
                        }, 500);
                    }
                });
            }
        }
        return false;
    });

</script>