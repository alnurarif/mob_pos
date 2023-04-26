<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$v = "";
if ($this->input->post('date_range')) {
    $dr = json_decode($this->input->post('date_range'));
    $v .= "&start_date=" . $dr->start;
    $v .= "&end_date=" . $dr->end;
}
if ($this->input->post('customers[]')) {
    $v .= "&customers=" . implode(',', $this->input->post('customers'));
}
?>
<script>
    function row_status(x) {
        if (x == 1) {
            return '<div class="text-center"><span class="label label-warning"><?php echo lang('ready_to_purchase');?></span></div>';
        } else if(x == 2) {
            return '<div class="text-center"><span class="label label-success"><?php echo lang('purchased');?></span></div>';
        }else{
            return x;
        }
    }
  

jQuery(document).ready( function($) {
    
$( ".client_name" ).select2({        
    ajax: {
        url: "<?php echo base_url(); ?>panel/customers/getAjax/no",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                q: params.term 
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    },
    // minimumInputLength: 2
});
});
    $(document).ready(function () {
        var oTable = $('#POData').dataTable({
            "aaSorting": [[1, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?php echo lang('all')?>"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('panel/reports/getCustomerPurchases?v=1'.$v); ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name()?>",
                    "value": "<?php echo $this->security->get_csrf_hash()?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [
                    {"mRender": fld},
                    null, 
                    {"mRender": row_status}, 
                    {"mRender": currencyFormat}, 
                ],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[4];
                nRow.className = "purchase_link";
                return nRow;
            },
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var total = 0, paid = 0, balance = 0;
                for (var i = 0; i < aaData.length; i++) {
                    total   +=  parseInt(aaData[aiDisplay[i]]['3']);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[3].innerHTML = '<?php echo escapeStr($settings->currency); ?> ' + (total);
            },
            
        })
    });

    $('body').on('click', '.bpo', function(e) {
        e.preventDefault();
        $(this).popover({html: true, trigger: 'manual'}).popover('toggle');
        return false;
    });
    $('body').on('click', '.bpo-close', function(e) {
        $('.bpo').popover('hide');
        return false;
    });
</script>


<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('reports/customer_purchases');?></h3>
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
     <?php echo form_open("panel/reports/customer_purchases"); ?>
            <div class="form-group">
                <label><?php echo lang('Date Range');?></label>
                <?php echo form_input('date_range_o', (isset($_POST['date_range_o']) ? $_POST['date_range_o'] : ""), 'class="form-control derp" id="daterange"'); ?>
                <input type="hidden" name="date_range" class="date_range" id="date_range" value='<?php echo (isset($_POST['date_range']) ? htmlspecialchars($_POST['date_range']) : "");?>'>
            </div>
            <div class="form-group">
                <?php echo form_dropdown('customers[]', $customers, set_value('customers'), 'class="form-control client_name" multiple'); ?>
            </div>
           

            <div class="form-group">
                <div
                    class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
            </div>
            <?php echo form_close(); ?>
                    <table id="POData" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-hover table-striped" width="100%">
                        <thead>
                        <tr class="default">
                            <th><?php echo lang('date'); ?></th>
                            <th><?php echo lang('Customer'); ?></th>
                            <th><?php echo lang('status'); ?></th>
                            <th><?php echo lang('grand_total'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?php echo lang('loading_data_from_server');?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr>
                            <th><?php echo lang('date'); ?></th>
                            <th><?php echo lang('Customer'); ?></th>
                            <th><?php echo lang('status'); ?></th>
                            <th><?php echo lang('grand_total'); ?></th>
                        </tr>
                        </tfoot>
                    </table>
  </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').on( "click", function (event) {
            event.preventDefault();
            window.location.href = "<?php echo site_url('panel/reports/getCustomerPurchases/pdf/?v=1'.$v)?>";
            return false;
        });
        $('#xls').on( "click", function (event) {
            event.preventDefault();
            window.location.href = "<?php echo site_url('panel/reports/getCustomerPurchases/0/xls/?v=1'.$v)?>";
            return false;
        });
    });
</script>
