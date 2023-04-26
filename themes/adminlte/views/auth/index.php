<script>
    oTable = null;
    $(document).ready(function () {
        'use strict';
        oTable = $('#UsrTable').dataTable({
            "aaSorting": [[2, "asc"], [3, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?php echo lang('all') ?>"]],
            "iDisplayLength": <?php echo $settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo base_url('panel/auth/getUsers') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{
                "bSortable": false,
                "mRender": checkbox
            }, null, null, null,  null, null, {"mRender": user_status}, {"bSortable": false}]
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?php echo lang('first_name');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?php echo lang('last_name');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?php echo lang('email_address');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?php echo lang('company');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?php echo lang('award_points');?>]", filter_type: "text", data: []},
            {column_number: 6, filter_default_label: "[<?php echo lang('group');?>]", filter_type: "text", data: []},
            {
                column_number: 7, select_type: 'select2',
                select_type_options: {
                    placeholder: '<?php echo lang('status');?>',
                    width: '100%',
                    style: 'width:100%;',
                    minimumResultsForSearch: -1,
                    allowClear: true
                },
                data: [{value: '1', label: '<?php echo lang('active');?>'}, {value: '0', label: '<?php echo lang('inactive');?>'}]
            }
        ], "footer");
    });
</script>
<style>.table td:nth-child(6) {
        text-align: right;
        width: 10%;
    }

    .table td:nth-child(8) {
        text-align: center;
    }</style>
<?php if ($Admin) {
    echo form_open('panel/auth/user_actions', 'id="action-form"');
} ?>


<div class="box box-default">
    <div class="box-header with-border">
      <i class="fa fa-warning"></i>

      <h3 class="box-title"><?php echo lang('users'); ?></h3>
      <div class="box-tools pull-right">
        <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?php echo lang('actions') ?>"></i></a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li><a href="<?php echo base_url('panel/auth/create_user'); ?>"><i class="fa fa-plus-circle"></i> <?php echo lang('add_user'); ?></a></li>
                        <li><a href="#" id="excel" data-action="export_excel"><i class="fas fa-file-excel"></i> <?php echo lang('export_to_excel') ?></a></li>
                        <li class="divider"></li>
                        <li><a href="#" class="po" title="<b><?php echo $this->lang->line('delete_users') ?></b>" data-content="<p><?php echo lang('r_u_sure') ?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?php echo lang('i_m_sure') ?></a> <button class='btn po-close'><?php echo lang('no') ?></button>" data-html="true" data-placement="left"><i class="fas fa-trash"></i> <?php echo lang('delete_users') ?></a></li>
                    </ul>
      </div>

    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="table-responsive">
            <table id="UsrTable" cellpadding="0" cellspacing="0" border="0"
                   class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th style="min-width:30px; width: 30px; text-align: center;">
                        <input class="checkbox checkth" type="checkbox" name="check"/>
                    </th>
                    <th class="col-xs-2"><?php echo lang('first_name'); ?></th>
                    <th class="col-xs-2"><?php echo lang('last_name'); ?></th>
                    <th class="col-xs-2"><?php echo lang('email'); ?></th>
                    <th class="col-xs-2"><?php echo lang('company'); ?></th>
                    <th class="col-xs-1"><?php echo lang('group'); ?></th>
                    <th style="width:100px;"><?php echo lang('status'); ?></th>
                    <th style="width:80px;"><?php echo lang('actions'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="8" class="dataTables_empty"><?php echo lang('loading_data_from_server') ?></td>
                </tr>
                </tbody>
                <tfoot class="dtFilter">
                <tr class="active">
                    <th style="min-width:30px; width: 30px; text-align: center;">
                        <input class="checkbox checkft" type="checkbox" name="check"/>
                    </th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th style="width:100px;"></th>
                    <th style="width:85px;"><?php echo lang('actions'); ?></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <!-- /.box-body -->
  </div>

<?php if ($Admin) {
    ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?php echo form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?php echo form_close() ?>

    <script language="javascript">
        $(document).ready(function () {
            $('#set_admin').on( "click", function () {
                $('#usr-form-btn').trigger('click');
            });

        });
    </script>
<?php
} ?>