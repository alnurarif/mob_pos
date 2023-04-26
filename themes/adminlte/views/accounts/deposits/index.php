<a href="<?php echo base_url();?>panel/deposits/add" class="btn-icon btn btn-primary btn-icon">
    <i class="fa fa-plus"></i> <?php echo lang('Add Deposit');?>
</a>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo lang('Deposits');?></h3>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table style="width: 100%" class="display compact table table-bordered table-striped" id="dynamic-table">
                <thead>
                <tr>
                    <th><?php echo lang('Deposit Type');?></th>
                    <th><?php echo lang('Amount');?></th>
                    <th><?php echo lang('Date');?></th>
                    <!-- <th><?php echo lang('Recurring');?></th> -->
                    <th><?php echo lang('Description');?></th>
                    <th><?php echo lang('Bank Account');?></th>
                    <th><?php echo lang('Fund Type');?></th>
                    <th><?php echo lang('Files');?></th>
                    <th style="width: 90px;"><?php echo lang('Action');?></th>
                </tr>
                </thead>
                <tbody>
                
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    function recurring_label(x) {
        if (parseInt(x) == 0){
            return 'No';
        }
        return 'Yes';
    }
$(document).ready(function () {
    oTable = $('#dynamic-table').dataTable({
        "aaSorting": [[2, "asc"]],
        "aLengthMenu": [[10, 15, 20, 25, 50, 100, -1], [10, 15, 20, 25, 50, 100, "All"]],
        "iDisplayLength": <?php echo escapeStr($settings->rows_per_page); ?>,
        'bProcessing': true, 'bServerSide': true,
        'sAjaxSource': '<?php echo site_url('panel/deposits/getAllDeposits'); ?>',
        'fnServerData': function (sSource, aoData, fnCallback) {
            aoData.push({
                "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                "value": "<?php echo $this->security->get_csrf_hash() ?>"
            });
            $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
        },
        "aoColumns": [
            null,
            {mRender: currencyFormat},
            {mRender: fsd},
            null,
            null,
            null,
            null,
            {bSortable: false},
        ]
    });
});
</script>