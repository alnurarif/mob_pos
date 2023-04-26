<?php if($this->Admin || $GP['deposit_type-add']): ?>
<button class="btn-icon btn btn-primary btn-icon add_entrytype"><i class="fa fa-plus-circle"></i> <?php echo lang('Entry Type');?></button>
<?php endif;?>
<div class="box">
    <div class="box-header">
        <div class="box-title"><?php echo lang('Entry Type');?></div>
    </div>
    <div class="box-body">
        <div class="table-responsive">
           <table style="width: 100%" class="display compact table table-bordered table-striped" id="entrytypes-table">
                <thead>
                    <tr>
                        <th><?php echo lang('name');?></th>
                        <th><?php echo lang('Description');?></th>
                        <th style="width: 90px !important"><?php echo lang('actions');?></th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th><?php echo lang('name');?></th>
                        <th><?php echo lang('Description');?></th>
                        <th style="width: 90px !important"><?php echo lang('actions');?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>


<script type="text/javascript">
$(document).ready(function () {

    oTable = $('#entrytypes-table').dataTable({
      autoWidth: false,
        "aaSorting": [[1, "asc"]],
        "aLengthMenu": [[10, 15, 20, 25, 50, 100, -1], [10, 15, 20, 25, 50, 100, "All"]],
        "iDisplayLength": <?php echo escapeStr($settings->rows_per_page); ?>,
        'bProcessing': true, 'bServerSide': true,
        'sAjaxSource': '<?php echo site_url('panel/deposits/getAllEntryTypes/'); ?>',
        'fnServerData': function (sSource, aoData, fnCallback) {
            aoData.push({
                "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                "value": "<?php echo $this->security->get_csrf_hash() ?>"
            });
            $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
        },
        "aoColumns": [
            {"bSortable": false},
            null,
            null,
        ],
    });
});



// 
// 
 jQuery(document).on("click", "#delete_entrytype", function () {
        var num = jQuery(this).data("num");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/deposits/delete_entrytype",
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
                if (data.success) {
                    toastr['success'](data.msg);
                }else{
                    toastr['warning'](data.msg);
                }
                $('#entrytypes-table').DataTable().ajax.reload();

            }
        });
    });

    jQuery(document).on("click", ".add_entrytype", function (e) {
        $('#entrytypemodal').modal('show');
        $('#entrytype_form').trigger("reset");
        $('#entrytype_form').parsley().reset();
        jQuery('#entrytypemodalheader').html("<?php echo lang('Add Entry Type');?>");
        jQuery('#entrytypefooter').html('<button data-dismiss="modal" class="btn-icon btn btn-default" type="button"><i class="fa fa-reply"></i> <?php echo lang("go_back") ?></button><button id="submit_entrytype" role="button" form="entrytype_form" class="btn-icon btn btn-success" data-mode="add"><i class="fa fa-plus-circle"></i> <?php echo lang("add"); ?></button>');
    });

    jQuery(document).on("click", "#edit_entrytype", function () {
        $('#entrytype_form')[0].reset();
        $('#entrytype_form').find("select").val("").trigger('change');
        var num = $(this).data('num');
        jQuery('#entrytypemodalheader').html("<?php echo lang('Edit Entry Type');?>");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/deposits/getEntryTypeByID",
            data: "id=" + encodeURI(num) + "&token=<?php echo $_SESSION['token'];?>",
            cache: false,
            dataType: "json",
            success: function (data) {
                jQuery('#entrytype_name').val(data.name);
                jQuery('#entrytype_description').val(data.description);

                jQuery('#entrytypefooter').html('<button data-dismiss="modal" class="btn-icon btn btn-default" type="button"><i class="fa fa-reply"></i> <?php echo lang("go_back") ?></button><button role="button" id="submit_entrytype" form="entrytype_form" class="btn-icon btn btn-success" data-mode="modify" data-num="' + encodeURI(num) + '"><i class="fa fa-plus"></i> <?php echo lang("save"); ?></button>');
            }
        });
    });


     $(function () {
      $('#entrytype_form').parsley({
        successClass: "has-success",
        errorClass: "has-error",
        classHandler: function (el) {
            return el.$element.closest(".form-group");
        },
        errorsContainer: function (el) {
            return el.$element.closest(".form-group");
        },
        errorsWrapper: "<span class='help-block'></span>",
        errorTemplate: "<span></span>"
      }).on('form:submit', function(event) {
        var mode = jQuery('#submit_entrytype').data("mode");
        var id = jQuery('#submit_entrytype').data("num");
        var url = "";
        var dataString = new FormData($('#entrytype_form')[0]);
        if (mode == "add") {
            url = base_url + "panel/deposits/add_entrytype";
            $.ajax({
                url: url,
                type: "POST",
                data:  dataString,
                contentType:false,
                cache: false,
                processData:false,
                success: function (result) {
                    


                    if (result.success) {
                        toastr['success']("<?php echo lang('entrytype successfully added');?>");
                        setTimeout(function () {
                            $('#entrytypemodal').modal('hide');
                            $('#entrytypes-table').DataTable().ajax.reload();
                        }, 500);
                    }else{
                        toastr['error'](result.error);
                    }
                }
            });
        } else {
            url = base_url + "panel/deposits/edit_entrytype";
            dataString.append('id',id);
            $.ajax({
                url: url,
                type: "POST",
                data:  dataString,
                contentType:false,
                cache: false,
                processData:false,
                success: function (result) {
                    if (result.success) {
                        toastr['success']("<?php echo lang('entrytype successfully edited');?>");
                        setTimeout(function () {
                            $('#entrytypemodal').modal('hide');
                            $('#entrytypes-table').DataTable().ajax.reload();
                        }, 500);
                    }else{
                        toastr['error'](result.error);
                    }
                }
            });
        }
        return false;
    });
    });

</script>



<!-- Model Add -->
<div class="modal fade" id="entrytypemodal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="entrytypemodalheader"></h4>
            </div>
            <div class="modal-body">
                <form id="entrytype_form" class="col s12">
                    <div class="row">
                        <div class="col-md-12 input-field">
                            <div class="form-group">
                                <label><?php echo lang('name');?></label>
                                <input type="text" name="name" id="entrytype_name" class="form-control">
                            </div>
                        </div>

                         <div class="col-md-12 input-field">
                            <div class="form-group">
                                <label><?php echo lang('Description');?></label>
                                <textarea name="description" class="form-control" id="entrytype_description"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer" id="entrytypefooter">
                  <!--    -->
            </div>
        </div>
    </div>
</div>