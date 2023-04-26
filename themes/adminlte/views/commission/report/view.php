<script type="text/javascript" src="<?php echo $assets;?>/plugins/datatables/ext/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="<?php echo $assets;?>/plugins/datatables/ext/jszip.min.js"></script>
<script type="text/javascript" src="<?php echo $assets;?>/plugins/datatables/ext/pdfmake.min.js"></script>
<script type="text/javascript" src="<?php echo $assets;?>/plugins/datatables/ext/vfs_fonts.js"></script>
<script type="text/javascript" src="<?php echo $assets;?>/plugins/datatables/ext/buttons.html5.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $assets;?>/plugins/datatables/ext/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo $assets;?>/plugins/datatables/ext/buttons.dataTables.min.css">

<?php echo form_open('panel/commission/report', array('name'=> 'commission')); ?>
    <input type="hidden" name="pin_code" id="pincode" value="<?php echo set_value('pin_code'); ?>">
    <input type="hidden" name="sort_by" id="sort_by" value="<?php echo set_value('sort_by'); ?>">
    <input type="hidden" name="sort_with" id="sort_with" value="<?php echo set_value('sort_with'); ?>">
    <div class="row" id="edit_div">
        <div class="form-group col-md-6">
            <label><?php echo lang('Date Range');?></label>
            <input class="form-control derp btn btn-primary" id="daterange" name="date_range_o">
            <input type="hidden" name="date_range" class="date_range" id="date_range" value='<?php echo (isset($_POST['date_range']) ? htmlspecialchars($_POST['date_range']) : "");?>'>
        </div>
    </div>
    <input type="submit" class="btn btn-primary" name="submit" value="<?php echo lang('Submit');?>">
<?php echo form_close();?>

<script type="text/javascript">
    function formatMyDecimal(x) {
        return formatDecimal(x);
    }
</script><!-- Main content -->
<?php
if ($this->input->post('date_range')) {
    $date_range = json_decode($this->input->post('date_range')); 
    $date_range = $this->repairer->hrsd($date_range->start). ' - '.$this->repairer->hrsd($date_range->end);
}else{
    $date_range = $this->repairer->hrsd(date('Y-m-d')). ' - '. $this->repairer->hrsd(date('Y-m-d'));
}
?>
<section class="panel">
    <div class="panel-body">
        <?php if($users): ?>
            <?php foreach ($users as $user): ?>
                <script type="text/javascript">
                    $(document).ready(function () {
                        var oTable = $('#dynamic-table<?php echo $user->biller_id; ?>').dataTable({
                            dom: 'Bfrtip',
                            buttons: [
                               {
                                    extend: 'pdf',
                                    'sButtonText': "<?php echo lang('PDF');?>",
                                    orientation: 'landscape',
                                    footer: true,
                                    title: '<?php echo $user->biller; ?> | <?php echo $date_range; ?> | <?php echo $site_name; ?>',

                               },
                               {
                                    extend: 'csv',
                                    'sButtonText': "<?php echo lang('CSV');?>",
                                    title: '<?php echo $user->biller; ?> | <?php echo $date_range; ?> | <?php echo $site_name; ?>',

                                  
                               },
                               {
                                   extend: 'excel',
                                   'sButtonText': "<?php echo lang('Excel');?>",
                                    title: '<?php echo $user->biller; ?> | <?php echo $date_range; ?> | <?php echo $site_name; ?>',

                               }         
                            ],
                           
                            "aaSorting": [[1, "desc"]],
                            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                            "iDisplayLength": -1,
                            'bProcessing': true, 'bServerSide': true,
                            'sAjaxSource': '<?php echo base_url(); ?>panel/commission/getAllRecords/<?php echo $user->biller_id; ?>',
                            'fnServerData': function (sSource, aoData, fnCallback) {
                                aoData.push({
                                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                                    "value": "<?php echo $this->security->get_csrf_hash() ?>",
                                });
                                aoData.push({
                                    "name": "pin_code",
                                    "value": "<?php echo $pin_code ?>",
                                });
                                aoData.push({
                                    "name": "sort_by",
                                    "value": "<?php echo $sort_by ?>",
                                });
                                aoData.push({
                                    "name": "sort_with",
                                    "value": "<?php echo $sort_with ?>",
                                });
                                aoData.push({
                                    "name": "from_date",
                                    "value": "<?php echo $from_date ?>",
                                });
                                aoData.push({
                                    "name": "to_date",
                                    "value": "<?php echo $to_date ?>",
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
                                null,
                                {mRender: formatMyDecimal},
                                null,
                                null,
                                null,
                            ],
                            searching: false,
                            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                                var commission = 0, profit = 0,unit_cost = 0,unit_price = 0, subtotal = 0;
                                for (var i = 0; i < aaData.length; i++) {
                                    commission   +=  parseFloat(aaData[aiDisplay[i]]['11']);
                                    profit   +=  parseFloat(aaData[aiDisplay[i]]['9']);
                                    unit_cost   +=  parseFloat(aaData[aiDisplay[i]]['6']);
                                    unit_price   +=  parseFloat(aaData[aiDisplay[i]]['7']);
                                    subtotal   +=  parseFloat(aaData[aiDisplay[i]]['8']);
                                }
                                var nCells = nRow.getElementsByTagName('th');
                                nCells[11].innerHTML = '<?php echo escapeStr($settings->currency); ?>' + parseFloat(commission).toFixed(2);
                                nCells[9].innerHTML = '<?php echo escapeStr($settings->currency); ?> ' + parseFloat(profit).toFixed(2);
                                nCells[6].innerHTML = '<?php echo escapeStr($settings->currency); ?> ' + parseFloat(unit_cost).toFixed(2);
                                nCells[7].innerHTML = '<?php echo escapeStr($settings->currency); ?> ' + parseFloat(unit_price).toFixed(2);
                                nCells[8].innerHTML = '<?php echo escapeStr($settings->currency); ?> ' + parseFloat(subtotal).toFixed(2);
                            },
                        });
                    });
                </script>
                <fieldset>
                    <legend><?php $row = $this->ion_auth->user($user->biller_id)->row(); ?>
                        <?php echo $row->first_name; ?> <?php echo $row->last_name; ?> 
                    </legend>
                    <table id="dynamic-table<?php echo $user->biller_id; ?>" class="table table-bordered table-hover table-striped dataTable">
                         <thead>
                            <tr>
                                <th><?php echo lang('Sale ID');?></th>
                                <th><?php echo lang('Date');?></th>
                                <th><?php echo lang('Product Name');?></th>
                                <th><?php echo lang('Store');?></th>
                                <th><?php echo lang('User Group');?></th>
                                <th><?php echo lang('Biller');?></th>
                                <th><?php echo lang('Unit Cost');?></th>
                                <th><?php echo lang('Unit Price');?></th>
                                <th><?php echo lang('Subtotal');?></th>
                                <th><?php echo lang('Profit');?></th>
                                <th><?php echo lang('Commission Type');?></th>
                                <th><?php echo lang('Commission');?></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><?php echo lang('Sale ID');?></th>
                                <th><?php echo lang('Date');?></th>
                                <th><?php echo lang('Product Name');?></th>
                                <th><?php echo lang('Store');?></th>
                                <th><?php echo lang('User Group');?></th>
                                <th><?php echo lang('Biller');?></th>
                                <th><?php echo lang('Unit Cost');?></th>
                                <th><?php echo lang('Unit Price');?></th>
                                <th><?php echo lang('Subtotal');?></th>
                                <th><?php echo lang('Profit');?></th>
                                <th><?php echo lang('Commission Type');?></th>
                                <th><?php echo lang('Commission');?></th>
                            </tr>
                        </tfoot>
                    </table>
                </fieldset>
            <?php endforeach; ?>
        <?php else: ?>
            <script type="text/javascript">
            $(document).ready(function () {
                var oTable = $('#dynamic-table').dataTable({
                    dom: 'Bfrtip',
                    buttons: [

                       {
                            extend: 'pdf',
                            footer: true,
                            orientation: 'landscape',
                            title: "<?php echo lang('Commission Report');?> | <?php echo $date_range; ?> | <?php echo $site_name; ?>",


                       },
                       {
                            extend: 'csv',
                            footer: false,
                            title: "<?php echo lang('Commission Report');?> | <?php echo $date_range; ?> | <?php echo $site_name; ?>",

                       },
                       {
                           extend: 'excel',
                           footer: false,
                            title: "<?php echo lang('Commission Report');?> | <?php echo $date_range; ?> | <?php echo $site_name; ?>",


                       }         
                    ],

                    searching: false,
                    
                    "aaSorting": [[1, "desc"]],
                    "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    "iDisplayLength": -1,
                    'bProcessing': true, 'bServerSide': true,
                    'sAjaxSource': '<?php echo base_url(); ?>panel/commission/getAllRecords/',
                    'fnServerData': function (sSource, aoData, fnCallback) {
                        aoData.push({
                            "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                            "value": "<?php echo $this->security->get_csrf_hash() ?>",
                        });
                        aoData.push({
                            "name": "pin_code",
                            "value": "<?php echo $pin_code ?>",
                        });
                        aoData.push({
                            "name": "sort_by",
                            "value": "<?php echo $sort_by ?>",
                        });
                        aoData.push({
                            "name": "sort_with",
                            "value": "<?php echo $sort_with ?>",
                        });
                        aoData.push({
                            "name": "from_date",
                            "value": "<?php echo $from_date ?>",
                        });
                        aoData.push({
                            "name": "to_date",
                            "value": "<?php echo $to_date ?>",
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
                            null,
                            {mRender: formatMyDecimal},
                            null,
                            null,
                            null,
                    ],
                    "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                            var commission = 0, profit = 0,unit_cost = 0,unit_price = 0, subtotal = 0;
                            for (var i = 0; i < aaData.length; i++) {
                                commission   +=  parseFloat(aaData[aiDisplay[i]]['11']);
                                profit   +=  parseFloat(aaData[aiDisplay[i]]['9']);
                                unit_cost   +=  parseFloat(aaData[aiDisplay[i]]['6']);
                                unit_price   +=  parseFloat(aaData[aiDisplay[i]]['7']);
                                subtotal   +=  parseFloat(aaData[aiDisplay[i]]['8']);
                            }
                            var nCells = nRow.getElementsByTagName('th');
                            nCells[11].innerHTML = '<?php echo escapeStr($settings->currency); ?>' + parseFloat(commission).toFixed(2);
                            nCells[9].innerHTML = '<?php echo escapeStr($settings->currency); ?> ' + parseFloat(profit).toFixed(2);
                            nCells[6].innerHTML = '<?php echo escapeStr($settings->currency); ?> ' + parseFloat(unit_cost).toFixed(2);
                            nCells[7].innerHTML = '<?php echo escapeStr($settings->currency); ?> ' + parseFloat(unit_price).toFixed(2);
                            nCells[8].innerHTML = '<?php echo escapeStr($settings->currency); ?> ' + parseFloat(subtotal).toFixed(2);
                        },
                });
            });

        </script>
        <table id="dynamic-table" class="table table-bordered table-hover table-striped dataTable">
         <thead>
                <tr>
                    <th><?php echo lang('Sale ID');?></th>
                    <th><?php echo lang('Date');?></th>
                    <th><?php echo lang('Product Name');?></th>
                    <th><?php echo lang('Store');?></th>
                    <th><?php echo lang('User Group');?></th>
                    <th><?php echo lang('Biller');?></th>
                    <th><?php echo lang('Unit Cost');?></th>
                    <th><?php echo lang('Unit Price');?></th>
                    <th><?php echo lang('Subtotal');?></th>
                    <th><?php echo lang('Profit');?></th>
                    <th><?php echo lang('Commission Type');?></th>
                    <th><?php echo lang('Commission');?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th><?php echo lang('Sale ID');?></th>
                    <th><?php echo lang('Date');?></th>
                    <th><?php echo lang('Product Name');?></th>
                    <th><?php echo lang('Store');?></th>
                    <th><?php echo lang('User Group');?></th>
                    <th><?php echo lang('Biller');?></th>
                    <th><?php echo lang('Unit Cost');?></th>
                    <th><?php echo lang('Unit Price');?></th>
                    <th><?php echo lang('Subtotal');?></th>
                    <th><?php echo lang('Profit');?></th>
                    <th><?php echo lang('Commission Type');?></th>
                    <th><?php echo lang('Commission');?></th>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>
    </div>
</section>


<script type="text/javascript">
    $(function () {
        $('.datetimepicker').datetimepicker({
            format:'MM-DD-YYYY HH:mm:ss',
        });
    });
</script>

