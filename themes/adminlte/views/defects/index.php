<script>
    $(document).ready(function () {

        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[0, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page; ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?=base_url(); ?>panel/defects/getAllDefects',
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
            ],
        });
    });

  

    jQuery(document).on("click", "#delete_defect", function () {
        var num = jQuery(this).data("num");
        bootbox.confirm({
            message: "Are you sure!",
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Cancel'
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Confirm'
                }
            },
            callback: function (result) {
                if (result) {
                    jQuery.ajax({
                        type: "POST",
                        url: base_url + "panel/defects/delete",
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
                            toastr['success']("<?= lang('deleted'); ?>");
                            $('#dynamic-table').DataTable().ajax.reload();
                        }
                    });

                }
            }
        });
        
    });
</script>

<button href="#defectmodal" class="add_defect btn btn-primary">
    <i class="fa fa-plus-circle"></i> <?= lang('add'); ?> <?= lang('repair_defect'); ?>
</button>
    
<!-- Main content -->
<section class="content">
    <div class="row">
        <section class="panel">
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="display compact table table-bordered table-striped" id="dynamic-table">
                        <thead>
                            <tr>
                                <th><?= lang('name'); ?></th>
                                <th><?= lang('description'); ?></th>
                                <th><?= lang('actions'); ?></th>
                            </tr>
                        </thead>
                
                        <tfoot>
                            <tr>
                              
                                <th><?= lang('name'); ?></th>
                                <th><?= lang('description'); ?></th>
                                <th><?= lang('actions'); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                   
                </div>
            </div>
        </section>
    </div>
</section>


