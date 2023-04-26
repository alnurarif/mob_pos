<?php
$v = "";
if ($this->input->post('date_range')) {
    $dr = json_decode($this->input->post('date_range'));
    $v .= "&start_date=" . $dr->start;
    $v .= "&end_date=" . $dr->end;
}

if ($this->input->post('created_by')) {
    $v .= "&created_by=" . $this->input->post('created_by');
}

if ($this->input->post('payment_type')) {
    $v .= "&payment_type=" . $this->input->post('payment_type');
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

    
    function getFormattedDate(date){
        var dd = date.getDate();
        var mm = date.getMonth()+1;
        var yyyy = date.getFullYear();
        return mm +'/'+dd+'/'+yyyy;
    }
    function warranty(x) {
        x = x.split('____');
        var sold_date = x[1];
        try {
            var sold_date = new Date(x[1]);
            var json = $.parseJSON(x[0]);
            if (json){
                if (json.success) {
                    var warranty_duration = json.warranty_duration;
                    var warranty_duration_type = json.warranty_duration_type;
                    if (warranty_duration_type === 'years') {
                        days = warranty_duration * 365;
                        sold_date.setDate(sold_date.getDate() + parseInt(days));
                    }else if (warranty_duration_type === 'months') {
                        sold_date.setMonth(sold_date.getMonth() + parseInt(warranty_duration));
                    }else{ // days
                        sold_date.setDate(sold_date.getDate() + parseInt(warranty_duration));
                    }
                    days = Math.round((sold_date - new Date()) / (1000 * 60 * 60 * 24));
                    if (days > 0) {
                        return "<?php echo lang('Under warranty until');?>"+' "'+getFormattedDate(sold_date)+'"';
                    }else{
                        return "<?php echo lang('Warrnaty expired on');?>"+' "'+getFormattedDate(sold_date)+'"';
                    }
                    return x;
                }
                return '<a href="<?php echo base_url();?>panel/pos/view/'+x[2]+'">'+"<?php echo lang('Multiple warranties');?>"+'<br><small> '+"<?php echo lang('Click for details');?>"+'</small></a>';
            }else{
                return "<?php echo lang('No Warranty');?>";
            }
        } catch(err) {
            return "<?php echo lang('No Warranty');?>";
        }                
    }
    
    
    jQuery(document).on("click", "#email_invoice", function() {
        num = $(this).attr('data-num');
        email = $(this).attr('data-email');
        
            bootbox.prompt({
                title: "<?php echo lang('Enter Email Address');?>",
                inputType: 'email',
                value: "",
                callback: function (email_addr) {
                    if (email_addr != null) {
                        $.ajax({
                            type: "post",
                            url: "<?php echo base_url('panel/pos/email_receipt') ?>",
                            data: {email: email_addr, id: num},
                            dataType: "json",
                            success: function (data) {
                                toastr.success(data.msg);
                            },
                            error: function () {
                                toastr.error("<?php echo lang('ajax_request_failed'); ?>");
                                return false;
                            }
                        });
                    }
                }
            });
        // }
        return false;
    });


function pay_status(x) {


        if(x == null) {
            return '';
        } else if(x == 'pending') {
            return '<div class="text-center"><span class="payment_status label label-warning">'+lang[x]+'</span></div>';
        } else if(x == 'completed' || x == 'paid' || x == 'sent' || x == 'received') {
            return '<div class="text-center"><span class="payment_status label label-success">'+lang[x]+'</span></div>';
        } else if(x == 'partial' || x == 'transferring' || x == 'ordered') {
            return '<div class="text-center"><span class="payment_status label label-info">'+lang[x]+'</span></div>';
        } else if(x == 'due' || x == 'returned') {
            return '<div class="text-center"><span class="payment_status label label-danger">'+lang[x]+'</span></div>';
        } else {
            return '<div class="text-center"><span class="payment_status label label-default">'+x+'</span></div>';
        }
    }
    $(document).ready(function () {

        // $('.date').datepicker({ dateFormat: 'mm-dd-yy' });
        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('panel/reports/getAllSales/?v=1' . $v) ?>',
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
            { mRender: pqFormat},
            null,
            {mRender: formatToMoney},
            {mRender: formatToMoney},
            {mRender: formatToMoney},
            {mRender: formatToMoney},
            {"searchable":false,"mRender": pay_status}, 
            {"searchable":false,},
            {mRender: warranty},
            null,
            ],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var subtotal = 0, tax = 0, total = 0, paid = 0;
                for (var i = 0; i < aaData.length; i++) {
                    subtotal += parseFloat(aaData[aiDisplay[i]][5]);
                    tax += parseFloat(aaData[aiDisplay[i]][6]);
                    total += parseFloat(aaData[aiDisplay[i]][7]);
                    paid += parseFloat(aaData[aiDisplay[i]][8]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[5].innerHTML = formatMoney(parseFloat(subtotal));
                nCells[6].innerHTML = formatMoney(parseFloat(tax));
                nCells[7].innerHTML = formatMoney(parseFloat(total));
                nCells[8].innerHTML = formatMoney(parseFloat(paid));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?php echo lang('ID');?>]", filter_type: "text", data: []},
            {column_number: 1, filter_default_label: "[<?php echo lang('date');?> (mm-dd-yyyy)]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?php echo lang('Customer Name');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?php echo lang('Items');?>]", filter_type: "text", data: []},
        ], "footer");
    });
</script>


<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('reports/sales');?></h3>
      <div class="box-tools pull-right">
        <div class="btn-group">
            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                <i class="icon fas fa-tasks tip" data-placement="left" title="<?php echo lang("actions") ?>"></i>
            </a>
            <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                <li>
                    <a href="#" id="xls" data-action="export_excel">
                        <i class="fas fa-file-excel"></i> <?php echo lang('export_to_excel') ?>
                    </a>                        </li>
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
    <?php echo form_open("panel/reports/sales"); ?>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label><?=lang('Date Range');?></label>
                    <?php echo form_input('date_range_o', (isset($_POST['date_range_o']) ? $_POST['date_range_o'] : ""), 'class="form-control derp" id="daterange"'); ?>
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
            <div class="col-md-4">
                <div class="form-group">
                    <label><?=lang('payment_type');?></label>
                    <?php
                        $us = ['' => ""];

                        if($settings->accept_cash){
                            $us['cash'] = lang('Cash');
                        }
                        if($settings->accept_cc){
                            $us['CC'] = lang('Credit Card');
                        }
                        if($settings->accept_cheque){
                            $us['Cheque'] = lang('Cheque');
                        }
                        if($settings->accept_paypal){
                            $us['ppp'] = lang('PayPal');
                        }
                        $us['other'] = lang('Other');
                        $us['authorize'] = lang('Authorize.Net');
                    ?>
                    <?= form_dropdown('payment_type', $us, set_value('payment_type'), 'class="form-control" style="width: 100%"'); ?>
                </div>
            </div>


            
        </div>

        <div class="form-group">
            <div
                class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
        </div>
        <?php echo form_close(); ?>
                <div class="table-responsive">
                    <table style="width: 100%;" class=" compact table table-bordered table-striped" id="dynamic-table">
                        <thead>
                            <tr>
                                <th><?=lang('Sale ID');?></th>
                                <th><?=lang('Date');?></th>
                                <th><?=lang('Customers');?></th>
                                <th><?=lang('Items');?></th>
                                <th><?=lang('created_by');?></th>
                                <th><?=lang('Subtotal');?></th>
                                    <th><?=lang('Tax');?></th>
                                    <th><?=lang('Total');?></th>
                                    <th><?=lang('Paid');?></th>
                                    <th><?=lang('payment_status');?></th>
                                    <th><?=lang('paid_by');?></th>
                                    <th><?=lang('Warranty');?></th>
                                    <th><?=lang('Actions');?></th>
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
                                <th><?=lang('payment_status');?></th>
                                    <th><?=lang('paid_by');?></th>
                                    <th><?=lang('Warranty');?></th>
                                    <th><?=lang('Actions');?></th>
                            </tr>
                        </tfoot>
                    </table>
  </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').on( "click", function (event) {
            event.preventDefault();
            window.location.href = "<?php echo site_url('panel/reports/getAllSales/pdf/?v=1'.$v)?>";
            return false;
        });
        $('#xls').on( "click", function (event) {
            event.preventDefault();
            window.location.href = "<?php echo site_url('panel/reports/getAllSales/0/xls/?v=1'.$v)?>";
            return false;
        });
    });
</script>