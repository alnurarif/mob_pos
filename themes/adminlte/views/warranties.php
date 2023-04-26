<script>
function actions(x) {
    var pqc = x.split("___");
    if (pqc[1] == 1) {
        var button = "<li><a id='toggle' data-num='"+pqc[0]+"' data-mode='enable'><i class='fas fa-toggle-on'></i> "+lang.enable+"</a></li>";
    }else{
        var button = "<li><a id='toggle' data-num='"+pqc[0]+"' data-mode='disable'><i class='fas fa-toggle-off'></i> "+lang.disable+"</a></li>";
    }
    var return_var = "<div class='btn-group'><button type=\"button\" class=\"btn btn-default btn-xs btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-expanded=\"true\">Actions <span class=\"caret\"></button><ul class=\"dropdown-menu pull-right\" role=\"menu\">";

    return_var += "<li><a data-dismiss='modal' id='modify' href='#warrantymodal' data-toggle='modal' data-num='"+pqc[0]+"'><i class='fas fa-edit'></i> <?php echo lang('edit');?></a></li>";
    return_var += button;
    return_var += '</ul></div>';

    return return_var;
}

 
    $(document).ready(function () {
        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[3, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo base_url(); ?>panel/settings/getAllWarranties/<?php echo $toggle_type; ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            }, 
            "aoColumns": [
                null,
                {mRender: t_duration},
                null,
                {mRender: actions},
            ],
        });
    });

    function t_duration(x) {
        if(x == 'months') {
            return "<?=lang('months');?>"
        }
        if(x == 'days') {
            return "<?=lang('days');?>"
        }
        if(x == 'years') {
            return "<?=lang('years');?>"
        }
    }


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
<div class="modal fade" id="warrantymodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                        <form id="warranties_form" class="parsley-form" method="post">
                            <div class="row">
                                <div class="col-md-12 col-lg-4">
                                    <div class="form-group">
                                       	<label><?php echo lang('Duration');?></label>
                                        <div class="input-group">
                                            <input data-parsley-type="number" id="duration" name="duration" value="0" type="text" class="form-control">
                                            <div class="input-group-addon">
                                                <?php
                                                    $dts = array(
                                                        'days' => lang('Days'),
                                                        'months' => lang('Months'),
                                                        'years' => lang('Years'),
                                                    ); 
                                                ?>
                                                <?php echo form_dropdown('duration_type', $dts, '', 'class="skip" id="duration_type"'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col-lg-12">
                                    <div class="form-group">
                                        <label><?php echo lang('Details');?></label>
                                        <textarea class="form-control" name="details" id="details" rows="6"></textarea>
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
<button href="#warrantymodal" class="add_warranties btn btn-primary">
    <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo lang('Warranty Plan');?>
</button>

<?php echo form_open('panel/settings/warranty_add', 'id="action-form"'); ?>

<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('settings/warranties');?></h3>
      <div class="box-tools pull-right">
        <div class="btn-group">
            <a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>panel/settings/warranties/"><?php echo lang('All');?></a>
            <a class="btn btn-sm btn-success" href="<?php echo base_url(); ?>panel/settings/warranties/enabled"><i class='fas fa-toggle-on'></i> <?php echo lang('enabled');?></a>
            <a class="btn btn-sm btn-danger" href="<?php echo base_url(); ?>panel/settings/warranties/disabled"><i class='fas fa-toggle-off'></i> <?php echo lang('disabled');?></a>
        </div>
      </div>
  </div>
  <div class="box-body">
    <div class="table-responsive">
            <table class=" compact table table-bordered table-striped" id="dynamic-table" width="100%">
                <thead>
                    <tr>
                        
                        <th><?php echo lang('Duration');?></th>
                        <th><?php echo lang('Duration Type');?></th>
                        <th><?php echo lang('Details');?></th>
                        <th><?php echo lang('actions'); ?></th>
                    </tr>
                </thead>
            </table>
            
        </div>
  </div>
</div>

<script type="text/javascript">

      jQuery(".add_warranties").on("click", function (e) {
        $('#warrantymodal').modal('show');
        

        jQuery('#a_name').val('');
        jQuery('#a_upc_code').val('');
        jQuery('#a_price').val('');
        jQuery('#a_max_discount').val('');
        jQuery('#a_d_s_l').val('');
        jQuery('#re_at').val('');
        jQuery('#note').val('');
		jQuery('#category').val('').trigger('change');
		jQuery('#sub_category').val('').trigger('change');
        if (document.getElementById('universal')) {
            document.getElementById("universal").checked = false;
        }
        jQuery('#titclienti').html("<?php echo lang('add'); ?> <?php echo lang('Warranty');?>");

        jQuery('#footerClient1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button type="submit" class="btn btn-success" id="submit" data-mode="add" form="warranties_form" value="Submit">Submit</button>');
    });
       jQuery(document).on("click", "#modify", function () {
            jQuery('#titclienti').html('<?php echo lang('edit'); ?> <?php echo lang('Warranty');?>');
            
            var num = jQuery(this).data("num");
            jQuery.ajax({
                type: "POST",
                url: base_url + "panel/settings/getWarrantyByID",
                data: "id=" + encodeURI(num) + "&token=<?php echo $_SESSION['token'];?>",
                cache: false,
                dataType: "json",
                success: function (data) {
                	var plan = data.data;
                    jQuery('#titclienti').html("<?php echo lang('edit'); ?> <?php echo lang('Warranty');?>");
                    jQuery('#duration').val(plan.warranty_duration);
                    jQuery('#duration_type').val(plan.warranty_duration_type);
                    jQuery('#details').val(plan.details);

                    jQuery('#footerClient1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button type="submit" id="submit" class="btn btn-success" data-mode="modify" form="warranties_form" value="Submit" data-num="' + encodeURI(num) + '"><i class="fas fa-save"></i>Submit</button>')
                }
            });

        });
    // process the form
    $('#warranties_form').on( "submit", function(event) {
        event.preventDefault();
        var mode = jQuery('#submit').data("mode");
        var id = jQuery('#submit').data("num");
        form = $(this);
        var valid = form.parsley().validate();
        if (valid) {
            var url = "";
            var dataString = "";

            if (mode == "add") {
                url = base_url + "panel/settings/warranty_add";
                dataString = $('form').serialize();
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        toastr['success']("<?php echo lang('add');?>", " <?php echo lang('Warranty');?>: " + name + " <?php echo lang('added');?>");
                        setTimeout(function () {
                            $('#warrantymodal').modal('hide');
                            $('#dynamic-table').DataTable().ajax.reload();
                        }, 500);
                    }
                });
            } else {
                url = base_url + "panel/settings/warranty_edit";
                dataString = $('form').serialize() + "&id=" + encodeURI(id);
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        toastr['success']("<?php echo lang('edit');?>", " <?php echo lang('Warranty');?>: " + name + "<?php echo lang('updated');?>");
                        setTimeout(function () {
                            $('#warrantymodal').modal('hide');
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