<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        oTable = $('#NTTable').dataTable({
            "aaSorting": [[1, "asc"], [2, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?php echo lang('all') ?>"]],
            "iDisplayLength": <?php echo $settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo base_url('panel/notifications/getNotifications') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [null, {"mRender": fld}, {"mRender": fld}, {"mRender": fld}, {"bSortable": false}]
        });
    });
</script>


<a class="btn btn-primary" href="<?php echo base_url('panel/notifications/add'); ?>" data-toggle="modal" data-target="#myModal"><i class="icon fa fa-plus"></i> <?php echo lang('add_notification');?></a>

<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('notifications');?></h3>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table id="NTTable" cellpadding="0" cellspacing="0" border="0"
                    class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th><?php echo $this->lang->line('notification'); ?></th>
                    <th style="width: 140px;"><?php echo $this->lang->line('submitted_at'); ?></th>
                    <th style="width: 140px;"><?php echo $this->lang->line('from'); ?></th>
                    <th style="width: 140px;"><?php echo $this->lang->line('till'); ?></th>
                    <th style="width:80px;"><?php echo $this->lang->line('actions'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="5" class="dataTables_empty"><?php echo lang('loading_data_from_server') ?></td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>
