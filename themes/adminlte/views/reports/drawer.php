<?php
$v = "";
if ($this->input->post('date_range')) {
    $dr = json_decode($this->input->post('date_range'));
    $v .= "&start_date=" . $dr->start;
    $v .= "&end_date=" . $dr->end;
}
?>

<script type="text/javascript">
function pqFormat(x) {
    if (x != null) {
        var d = '', pqc = x.split("___");
        for (index = 0; index < pqc.length; ++index) {
            var pq = pqc[index];
            var v = pq.split("__");
            d += v[0]+'<br>';
        }
        return d;
    } else {
        return '';
    }
}

function formatProfit(x) {
    if (x < 0) {
        return '<span class="label label-danger">'+formatToMoney(x)+'</span>'
    }else{
        return '<span class="label label-success">'+formatToMoney(x)+'</span>'
    }
}

    $(document).ready(function () {
        var tt = '';
        $('.date').datepicker({ dateFormat: 'mm-dd-yy' });
        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('panel/reports/getDrawerReport/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            }, 
            "aoColumns": [
            null,
            {mRender: fld},
            null,
            null,
            null,
            null,
            null,
            ],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                console.log(nRow);
                nRow.id = aData[7];
                if (aData[8] == 'open') {
                    nRow.className = "register_link danger";
                }else{
                    nRow.className = "register_link success";
                }
                return nRow;
            },
            columnDefs: [
                { width: 200, targets: [4] }
            ],
        });

        $('body').on('click', '.register_link td', function() {
            $('#myModal').modal({remote: site.base_url + 'panel/reports/drawer_modal_view/' + $(this).parent('.register_link').attr('id')});
            $('#myModal').modal('show');
        });
    });
</script>
<!-- Main content -->


<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('reports/drawer');?></h3>
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
    <?php echo form_open("panel/reports/drawer"); ?>
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
        <table class=" compact table table-bordered table-striped" id="dynamic-table">
            <thead>
                <tr>
                    <th><?php echo lang('Opened By');?></th>
                    <th><?php echo lang('Opening Date');?></th>
                    <th><?php echo lang('Cash in Hand');?></th>
                    <th><?php echo lang('Closed By');?></th>
                    <th><?php echo lang('Closing Date');?></th>
                    <th><?php echo lang('Closing Cash');?></th>
                    <th><?php echo lang('Count Notes');?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th><?php echo lang('Opened By');?></th>
                    <th><?php echo lang('Opening Date');?></th>
                    <th><?php echo lang('Cash in Hand');?></th>
                    <th><?php echo lang('Closed By');?></th>
                    <th><?php echo lang('Closing Date');?></th>
                    <th><?php echo lang('Closing Cash');?></th>
                    <th><?php echo lang('Count Notes');?></th>
                </tr>
            </tfoot>
        </table>
  </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').on( "click", function (event) {
            event.preventDefault();
            window.location.href = "<?php echo site_url('panel/reports/getDrawerReport/pdf/?v=1'.$v)?>";
            return false;
        });
        $('#xls').on( "click", function (event) {
            event.preventDefault();
            window.location.href = "<?php echo site_url('panel/reports/getDrawerReport/0/xls/?v=1'.$v)?>";
            return false;
        });
    });
</script>