<section class="content">
    <div class="row">
        <div class="form-group">
            <button class="btn-icon btn btn-primary btn-icon add_fund">Add Fund</button>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Funds</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table style="width: 100%" class="display compact table table-bordered table-striped" id="funds-table">
                        <thead>
                            <th>Name</th>
                            <th>Notes</th>
                            <th>Bank Account</th>
                            <th>Total Amount</th>
                            <th style="width: 90px !important"><?=lang('actions');?></th>
                        </thead>
                        <tbody>
                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- /.box -->
<script type="text/javascript">
$(document).ready(function () {
    oTable = $('#funds-table').dataTable({
        "aaSorting": [[1, "asc"]],
        "aLengthMenu": [[10, 15, 20, 25, 50, 100, -1], [10, 15, 20, 25, 50, 100, "All"]],
        "iDisplayLength": <?=$settings->rows_per_page; ?>,
        'bProcessing': true, 'bServerSide': true,
        'sAjaxSource': '<?= site_url('panel/accounts/getAllFunds'); ?>',
        'fnServerData': function (sSource, aoData, fnCallback) {
            aoData.push({
                "name": "<?= $this->security->get_csrf_token_name() ?>",
                "value": "<?= $this->security->get_csrf_hash() ?>"
            });
            $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
        },
        "aoColumns": [
            null,
            null,
            null,
            null,
            {"bSortable": false},
            
        ]
    });
});

jQuery(document).on("click", "#delete_fund", function () {
    var num = jQuery(this).data("num");
    jQuery.ajax({
        type: "POST",
        url: base_url + "panel/accounts/delete_fund",
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
            toastr['success'](data.msg);
            $('#funds-table').DataTable().ajax.reload();
        }
    });
});

jQuery(document).on("click", ".add_fund", function (e) {
    $('#fundmodal').modal('show');
    $('#fund_form').trigger("reset");
    $('#fund_form').parsley().reset();
    jQuery('#fundmodalheader').html("Add Fund");
    jQuery('#fundfooter').html('<button data-dismiss="modal" class="btn-icon btn btn-goback" type="button"><i class="fa fa-reply"></i> <?= lang("go_back") ?></button><button id="submit_fund" role="button" form="fund_form" class="btn-icon btn btn-success" data-mode="add"><i class="fa fa-plus"></i> <?= lang("add"); ?></button>');
});

    jQuery(document).on("click", "#edit_fund", function () {
        $('#fund_form')[0].reset();
        $('#fund_form').find("select").val("").trigger('change');
        var num = $(this).data('num');
        jQuery('#fundmodalheader').html("Edit Fund");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/accounts/getFundByID",
            data: "id=" + encodeURI(num) + "&token=<?=$_SESSION['token'];?>",
            cache: false,
            dataType: "json",
            success: function (data) {
                jQuery('#fund_name').val(data.name);
                jQuery('#fund_notes').val(data.notes);
                jQuery('#fund_type').val(data.type).trigger('change');
                jQuery('#fundfooter').html('<button data-dismiss="modal" class="btn-icon btn btn-goback" type="button"><i class="fa fa-reply"></i> <?= lang("go_back") ?></button><button role="button" id="submit_fund" form="fund_form" class="btn-icon btn btn-success" data-mode="modify" data-num="' + encodeURI(num) + '"><i class="fa fa-plus"></i> <?= lang("save"); ?></button>');
            }
        });
    });


     $(function () {
      $('#fund_form').parsley({
        // errorsContainer: function(pEle) {
        //     var $err = pEle.$element.closest('.form-group');
        //     return $err;
        // }
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
        var mode = jQuery('#submit_fund').data("mode");
        var id = jQuery('#submit_fund').data("num");
        var url = "";
        var dataString = new FormData($('#fund_form')[0]);
        if (mode == "add") {
            url = base_url + "panel/accounts/add_fund";
            $.ajax({
                url: url,
                type: "POST",
                data:  dataString,
                contentType:false,
                cache: false,
                processData:false,
                success: function (result) {
                    toastr['success']("Fund successfully added");
                    setTimeout(function () {
                        $('#fundmodal').modal('hide');
                        $('#dynamic-table').DataTable().ajax.reload();
                    }, 500);
                }
            });
        } else {
            url = base_url + "panel/accounts/edit_fund";
            dataString.append('id',id);
            $.ajax({
                url: url,
                type: "POST",
                data:  dataString,
                contentType:false,
                cache: false,
                processData:false,
                success: function (result) {
                    toastr['success']("Fund successfully edited");
                    setTimeout(function () {
                        $('#fundmodal').modal('hide');
                        $('#dynamic-table').DataTable().ajax.reload();
                    }, 500);
                }
            });
        }
        return false;
    });
});
</script>

<div class="modal fade" id="fundmodal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="fundmodalheader"></h4>
            </div>
            <div class="modal-body">
                <form id="fund_form" class="col s12">
                    <div class="row">
                        <div class="col-md-12 input-field">
                            <div class="form-group">
                                <label>Bank Account</label>
                                <?= form_dropdown('bank_id', $bank_accounts, '', 'class="form-control" style="width:100%;"'); ?>
                            </div>
                        </div>
                        <div class="col-md-12 input-field">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" id="fund_name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12 input-field">
                            <div class="form-group">
                                <label>Notes</label>
                                <textarea name="notes" class="form-control" id="fund_notes"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer" id="fundfooter">
                <!--    -->
            </div>
        </div>
    </div>
</div>