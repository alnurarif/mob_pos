<script>
    $(document).ready(function () {
        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[3, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo base_url(); ?>panel/inventory/getAllModels',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            }, 
            "aoColumns": [
            null,
            null,
            null,
            null,
            ],
        });
    });

    jQuery(document).on("click", "#delete", function () {
        var num = jQuery(this).data("num");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/inventory/delete_model",
            data: "id=" + encodeURI(num),
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
                toastr['success']("<?php echo lang('deleted'); ?>: ", "<?php echo lang('model_deleted'); ?>");
                $('#dynamic-table').DataTable().ajax.reload();
            }
        });
    });


</script>

<!-- ============= MODAL MODIFICA supplierI ============= -->
<div class="modal fade" id="modelmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="titsupplieri"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <p class="tips custip"></p>
                    <div class="row">
                        <form class="col s12">
                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <?php echo lang('model_name', 'model_name'); ?>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-user"></i>
                                        </div>
                                        <input id="model_name" type="text" class="validate form-control" required>
                                    </div>
                                   
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <?php echo lang('model_manufacturer', 'model_manufacturer'); ?>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-user"></i>
                                        </div>
                                        <input id="model_manufacturer" type="text" class="validate form-control" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-12 col-lg-12 input-field">
                                <div class="form-group">
                                    <?php echo lang('model_description', 'model_description'); ?>
                                    <textarea class="form-control" id="model_description"></textarea>
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


<button href="#modelmodal" class="add_c btn btn-primary">
                        <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo lang('model_title'); ?>
                    </button>
<!-- Main content -->
    <section class="content">
        <div class="row">
            <section class="panel">


                    
                    <div class="panel-body">
                        <div class="adv-table">
                            <table class=" compact table table-bordered table-striped" id="dynamic-table">
                                <thead>
                                    <tr>
                                        <th><?php echo lang('model_name'); ?></th>
                                        <th><?php echo lang('model_manufacturer'); ?></th>
                                        <th><?php echo lang('model_description'); ?></th>
                                        <th><?php echo lang('actions'); ?></th>
                                    </tr>
                                </thead>
                        
                                <tfoot>
                                    <tr>
                                        <th><?php echo lang('model_name'); ?></th>
                                        <th><?php echo lang('model_manufacturer'); ?></th>
                                        <th><?php echo lang('model_description'); ?></th>
                                        <th><?php echo lang('actions'); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </section>
        </div>
    </section>
<script type="text/javascript">

    jQuery(".add_c").on("click", function (e) {
        $('#modelmodal').modal('show');
        

        jQuery('#model_name').val('');
        jQuery('#model_manufacturer').val('');
        jQuery('#model_description').val('');
       
        jQuery('#titsupplieri').html("<?php echo lang('add'); ?> <?php echo lang('model_title'); ?>");

        jQuery('#footersupplier1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back") ?></button><button id="submit" class="btn btn-success" data-mode="add"><i class="fas fa-user"></i> <?php echo lang("add"); ?> <?php echo lang("model_title"); ?></button>');
    });

    jQuery(document).on("click", "#modify", function () {
        jQuery('#titsupplieri').html('<?php echo lang("edit"); ?> <?php echo lang("model_title"); ?>');
        
        var num = jQuery(this).data("num");
            jQuery.ajax({
                type: "POST",
                url: base_url + "panel/inventory/getModelByID",
                data: "id=" + encodeURI(num) + "&token=<?php echo $_SESSION['token'];?>",
                cache: false,
                dataType: "json",
                success: function (data) {
                    jQuery('#model_name').val(data.name);
                    jQuery('#model_manufacturer').val(data.manufacturer);
                    jQuery('#model_description').val(data.description);
                    

    jQuery('#footersupplier1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back") ?></button><button id="submit" class="btn btn-success" data-mode="modify" data-num="' + encodeURI(num) + '"><i class="fas fa-save"></i> <?php echo lang("save"); ?> <?php echo lang("model_title"); ?></button>')
                }
            });
        });
    jQuery(document).on("click", "#submit", function () {
        var mode = jQuery(this).data("mode");
        var id = jQuery(this).data("num");

        var name = jQuery('#model_name').val();
        var manufacturer = jQuery('#model_manufacturer').val();
        var description = jQuery('#model_description').val();
       


        //validate
        var valid = validLongName(jQuery('#model_name'), "<?php echo lang('model_name'); ?>", jQuery('.tips')) && validLongName(jQuery('#model_manufacturer'), "<?php echo lang('model_manufacturer'); ?>", jQuery('.tips')) && validLongName(jQuery('#model_description'), "<?php echo lang('model_description'); ?>", jQuery('.tips')) ;

        if (valid) {
            var url = "";
            var dataString = "";

            if (mode == "add") {
                url = base_url + "panel/inventory/add_model";
                dataString = "name=" + encodeURI(name)+ "&manufacturer=" + encodeURI(manufacturer) + "&description=" + encodeURI(description);
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        toastr['success']("<?php echo lang('add'); ?>", "<?php echo lang('model_title'); ?>: " + name + " " + manufacturer + " <?php echo lang('added'); ?>");
                        setTimeout(function () {
                            $('#modelmodal').modal('hide');
                            $('#dynamic-table').DataTable().ajax.reload();
                        }, 500);
                    }
                });
            } else {
                url = base_url + "panel/inventory/edit_model";
                dataString = "name=" + encodeURI(name)+ "&manufacturer=" + encodeURI(manufacturer) + "&description=" + encodeURI(description) + "&id=" + encodeURI(id);
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        toastr['success']("<?php echo lang('save'); ?>", "<?php echo lang('model_title'); ?>: " + name + " " + manufacturer + "<?php echo lang('updated'); ?>");
                        setTimeout(function () {
                            $('#modelmodal').modal('hide');
                            $('#dynamic-table').DataTable().ajax.reload();
                        }, 500);
                    }
                });
            }
        }
        return false;
    });

</script>