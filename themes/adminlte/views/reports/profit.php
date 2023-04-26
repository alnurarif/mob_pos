<script>
    $(document).ready(function () {
        // CURI = '<?php echo base_url('panel/reports/profit'); ?>';
    });
</script>
<?php
$v = "";
if ($start) {
    $v .= "&start_date=" . $start;
    $v .= "&end_date=" . $end;
}
$start_date = null;
$end_date = null;
if ($this->input->post('date_range')) {
    $dr = json_decode($this->input->post('date_range'));

    $v .= "&start_date=" . $dr->start;
    $v .= "&end_date=" . $dr->end;
}

if ($this->input->post('created_by')) {
    $v .= "&created_by=" . $this->input->post('created_by');
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

        $('.date').datepicker({ dateFormat: 'mm-dd-yy' });
        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 100,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('panel/reports/getProfitReport/?v=1' . $v) ?>',
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
            {mRender: pqFormat},
            {mRender: formatToMoney},
            {mRender: formatToMoney},
            {mRender: formatToMoney},
            {mRender: formatToMoney},
            {mRender: formatToMoney},
            {mRender: formatProfit},
            null,
            null,
            ],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var gtotal = 0, paid = 0, cost = 0, price = 0, balance = 0, profit = 0;
                for (var i = 0; i < aaData.length; i++) {
                    gtotal += parseFloat(aaData[aiDisplay[i]][4]);
                    cost += parseFloat(aaData[aiDisplay[i]][5]);
                    price += parseFloat(aaData[aiDisplay[i]][6]);
                    paid += parseFloat(aaData[aiDisplay[i]][7]);
                    balance += parseFloat(aaData[aiDisplay[i]][8]);
                    profit += parseFloat(aaData[aiDisplay[i]][9]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[4].innerHTML = formatMoney(parseFloat(gtotal));
                nCells[5].innerHTML = formatMoney(parseFloat(cost));
                nCells[6].innerHTML = formatMoney(parseFloat(price));
                nCells[7].innerHTML = formatMoney(parseFloat(paid));
                nCells[8].innerHTML = formatMoney(parseFloat(balance));
                nCells[9].innerHTML = formatMoney(parseFloat(profit));
            }, 
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[12];
                nRow.className = "sale_link";
                return nRow;
            },
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[ID]", filter_type: "text", data: []},
            {column_number: 1, filter_default_label: "[<?php echo lang('date');?> (mm-dd-yyyy)]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[Customer Name]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[Items]", filter_type: "text", data: []},
        ], "footer");

        $('body').on('click', '.sale_link td:not(:first-child, :last-child)', function() {
            $('#myModal').modal({remote: site.base_url + 'panel/sales/modal_view/' + $(this).parent('.sale_link').attr('id')});
            $('#myModal').modal('show');
        });
    });
</script>
<!-- Main content -->

<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('reports/profit');?></h3>
      <div class="box-tools pull-right">
        <div class="btn-group">
            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                <i class="icon fas fa-tasks tip" data-placement="left" title="<?php echo lang("actions") ?>"></i>
            </a>
            <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                <li>
                    <a href="#" id="xls" data-action="export_excel">
                        <i class="fas fa-file-excel"></i> <?php echo lang('export_to_excel') ?>
                    </a>
                </li>
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
     <?php echo form_open("panel/reports/profit"); ?>
     <div class="col-md-4">
         <div class="form-group">
            <label><?php echo lang('Date Range') ?></label>
            <?php echo form_input('date_range', (isset($_POST['date_range_o']) ? $_POST['date_range_o'] : ""), 'class="form-control derp" id="daterange"'); ?>
            <input type="hidden" name="date_range" class="date_range" id="date_range" value='<?php echo (isset($_POST['date_range']) ? htmlspecialchars($_POST['date_range']) : "");?>'>
        </div>
     </div>
     <div class="col-md-4">
        <div class="form-group">
            <label><?=lang('created_by');?></label>
            <?php
                $us = ['' => ""];
                foreach ($users as $user) {
                    $us[$user->id] = $user->first_name.' '.$user->last_name;
                }
            ?>
            <?= form_dropdown('created_by', $us, set_value('created_by'), 'class="form-control" style="width: 100%"'); ?>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <div class="controls">
                 <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?>
            </div>
        </div>
    </div>

        <?php echo form_close(); ?>
        <div class="clearfix"></div>
    <div class="table-responsive">
        <table class=" compact table table-bordered table-striped" id="dynamic-table">
            <thead>
                <tr>
                    <th><?php echo lang('Sale ID');?></th>
                    <th><?php echo lang('Date');?></th>
                    <th><?php echo lang('Customers');?></th>
                    <th><?php echo lang('Items');?></th>
                    <th><?php echo lang('Subtotal');?></th>
                    <th><?php echo lang('Tax');?></th>
                    <th><?php echo lang('Total');?></th>
                    <th><?php echo lang('Cost');?></th>
                    <th><?php echo lang('Price');?></th>
                    <th><?php echo lang('Profit');?></th>
                    <th><?php echo lang('Created By');?></th>
                    <th><?php echo lang('Actions');?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th><?php echo lang('Created By');?></th>
                    <th><?php echo lang('Actions');?></th>
                </tr>
            </tfoot>
        </table>
    </div>
  </div>
</div>


   
   <script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').on( "click", function (event) {
            event.preventDefault();
            window.location.href = "<?php echo site_url('panel/reports/getProfitReport/pdf/?v=1'.$v)?>";
            return false;
        });
        $('#xls').on( "click", function (event) {
            event.preventDefault();
            window.location.href = "<?php echo site_url('panel/reports/getProfitReport/0/xls/?v=1'.$v)?>";
            return false;
        });
    });
</script>