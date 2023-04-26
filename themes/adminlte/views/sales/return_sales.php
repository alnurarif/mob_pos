<?php
$v = "";
if ($this->input->post('date_range')) {
    $dr = json_decode($this->input->post('date_range'));
    $v .= "&start_date=" . $dr->start;
    $v .= "&end_date=" . $dr->end;
}
?>
<script>
    $(document).ready(function () {
        var oTable = $('#RESLData').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?php echo lang('all') ?>"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('panel/sales/getReturns/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[7];
                nRow.className = "invoice_link2";
                return nRow;
            },
            "aoColumns": [{"mRender": fld}, null, null, null, null, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"bSortable": false, "bVisible": false}, {"bSortable": false, "bSearchable": false}],
        });

        <?php if($this->session->userdata('remove_rels')) { ?>
        localStorage.setItem('remove_rels', '1');
        <?php $this->sma->unset_data('remove_rels'); } ?>
        if (localStorage.getItem('remove_rels')) {
            localStorage.removeItem('reref');
            localStorage.removeItem('renote');
            localStorage.removeItem('reitems');
            localStorage.removeItem('rediscount');
            localStorage.removeItem('retax2');
            localStorage.removeItem('return_surcharge');
            localStorage.removeItem('remove_rels');
        }

        $(document).on('click', '.sledit', function (e) {
            if (localStorage.getItem('slitems')) {
                e.preventDefault();
                var href = $(this).attr('href');
                bootbox.confirm("<?php echo lang('you_will_loss_sale_data')?>", function (result) {
                    if (result) {
                        window.location.href = href;
                    }
                });
            }
        });

    });

</script>


<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('sales/return_sales');?></h3>
      <div class="box-tools pull-right">
        <div class="btn-group">
            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                <i class="icon fas fa-tasks tip" data-placement="left" title="<?php echo lang("actions") ?>"></i>
            </a>
            <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                <li>
                    <a href="#" id="xls" data-action="export_excel">
                        <i class="fas fa-file-excel"></i> <?php echo lang('export_to_excel') ?>
                    </a                        </li>
                <li>
                    <a href="#" id="pdf" data-action="export_pdf">
                        <i class="fas fa-file-pdf"></i> <?php echo lang('export_to_pdf') ?>
                    </a>
                </li>
            </ul>
        </div>
      </div>
  </div>
  <div class="box-body">
    <?php echo form_open("panel/sales/return_sales"); ?>
                <div class="form-group">
                    <label><?php echo lang('Date Range');?></label>
                    <?php echo form_input('date_range_o', (isset($_POST['date_range_o']) ? $_POST['date_range_o'] : ""), 'class="form-control derp" id="daterange"'); ?>
                        <input type="hidden" name="date_range" class="date_range" id="date_range" value='<?php echo (isset($_POST['date_range']) ? htmlspecialchars($_POST['date_range']) : "");?>'>
                </div>

                <div class="form-group">
                    <div
                        class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
                </div>
            <?php echo form_close(); ?>
            <div class="table-responsive">
                 <table id="RESLData" class="table table-bordered table-hover table-striped" width="100%">
                        <thead>
                        <tr>
                            <th><?php echo lang("date"); ?></th>
                            <th><?php echo lang("reference_no"); ?></th>
                            <th><?php echo lang('Sale Reference');?></th>
                            <th><?php echo lang('Biller');?></th>
                            <th><?php echo lang('Customer');?></th>
                            <th><?php echo lang('Surcharge');?></th>
                            <th><?php echo lang('Grand Total');?></th>
                            <th><?php echo lang("id"); ?></th>
                            <th><?php echo lang("actions"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="9" class="dataTables_empty">
                                    <?php echo lang("loading_data"); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
  </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').on( "click", function (event) {
            event.preventDefault();
            window.location.href = "<?php echo site_url('panel/sales/getReturns/pdf/?v=1'.$v)?>";
            return false;
        });
        $('#xls').on( "click", function (event) {
            event.preventDefault();
            window.location.href = "<?php echo site_url('panel/sales/getReturns/0/xls/?v=1'.$v)?>";
            return false;
        });
    });
</script>