<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style type="text/css">
    .modal.in .modal-lg {
        max-width: 70%;
        width: 100%;
        margin: auto; 
    }
</style>
<script>
function trackit(x) {
    var p = x.split('____');
    if (p[1] !== '') {
        return '<a href="https://www.packagemapping.com/track/auto/'+p[1]+'" target="_blank">'+p[0]+'</a>';
    }else{
        return p[0]+'No Track Code';
    }
}


function row_status(pqc) {
    pqc = pqc.split('__');
    x = pqc[0];
    if(x == null) {
        return '';
    } else if(x == 'pending') {
        return '<div class="text-center"><span class="label label-warning">'+[x]+'</span></div>';
    } else if(x == 'completed' || x == 'paid' || x == 'sent' || x == 'received') {
        return '<div class="text-center"><span class="label label-success">'+[x]+'</span></div>';
    } else if(x == 'partial' || x == 'transferring' || x == 'ordered') {
        return '<div class="text-center"><span class="label label-info">'+[x]+'</span></div>';
    } else if(x == 'returned') {
        if (pqc[1] == 1) {
            return '<div class="text-center"><span class="label label-danger">'+"<?php echo lang('RMA Requested');?>"+'</span></div>';
        }else if(pqc[1] == 2){
            return '<div class="text-center"><span class="label label-danger">'+"<?php echo lang('Shipped');?>"+'</span></div>';
        }else if(pqc[1] == 3){
            return '<div class="text-center"><span class="label label-danger">'+"<?php echo lang('Return Accepted');?>"+'</span></div>';
        }else{
            return '<div class="text-center"><span class="label label-danger">'+[x]+'</span></div>';
        }
    } else {
        return '<div class="text-center"><span class="label label-default">'+[x]+'</span></div>';
    }
}

    $(document).ready(function () {

        var oTable = $('#POData').dataTable({
            "aaSorting": [[2, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?php echo lang('all')?>"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('panel/purchases/getPurchases/'); ?><?php echo $status_view; ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name()?>",
                    "value": "<?php echo $this->security->get_csrf_hash()?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [
                    {"bSortable": false,"mRender": checkbox},
                    {"mRender": trackit}, 
                    {"mRender": fld}, 
                    null, 
                    null, 
                    {"mRender": row_status}, 
                    {"mRender": currencyFormat}, 
                    {"bSortable": false, "mRender": attachment}, 
                    {"bSortable": false}
                ],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[0];
                nRow.className = "purchase_link";
                return nRow;
            },
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var total = 0, paid = 0, balance = 0;
                for (var i = 0; i < aaData.length; i++) {
                    total   +=  parseInt(aaData[aiDisplay[i]]['6']);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[6].innerHTML = '<?php echo escapeStr($settings->currency); ?> ' + (total);
            }
        })
    });
   
$('body').on('click', '.purchase_link td:not(:first-child, :nth-child(2),:nth-child(5), :nth-last-child(2), :last-child)', function() {
        $('#myModal').modal({remote: site.base_url + 'panel/purchases/modal_view/' + $(this).parent('.purchase_link').attr('id')});
        $('#myModal').modal('show');
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
<?php 
    echo form_open('panel/purchases/purchase_actions', 'id="action-form"');
?>

<?php if($this->Admin || $GP['purchases-add']): ?>
<a class="btn btn-primary" href="<?php echo site_url('panel/purchases/add')?>">
    <i class="fas fa-plus-circle"></i> <?php echo lang('add_purchase'); ?>
</a>
<?php endif; ?>

<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo lang('purchases/index');?></h3>

        <div class="box-tools pull-right">
            <div class="btn-group">
                <a class="btn btn-default" href="<?php echo site_url('panel/purchases/index')?>">
                     <?php echo lang('All');?>
                </a>
                <a class="btn btn-warning" style="border-color: #00c0ef; background-color: #00c0ef !important;" href="<?php echo site_url('panel/purchases/index/ordered')?>">
                    <?php echo lang('Ordered');?>
                </a>
                <a class="btn btn-warning" href="<?php echo site_url('panel/purchases/index/pending')?>">
                    <?php echo lang('Received');?>
                </a>
                <a class="btn btn-success" href="<?php echo site_url('panel/purchases/index/received')?>">
                    <?php echo lang('Received &amp; Verified');?>
                </a>
            </div>

            <div class="btn-group">
                 <button type="button" class="btn  btn-default dropdown-toggle" data-toggle="dropdown"> <?php echo lang('Actions');?> <span class="caret"></span> <span class="sr-only"></span> </button>
                <ul class="dropdown-menu" role="menu">
                    <li>
                        <a href="#" id="excel" data-action="export_excel">
                            <i class="fas fa-file-excel"></i> <?php echo lang('export_to_excel'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" id="pdf" data-action="export_pdf">
                            <i class="fas fa-file-pdf"></i> <?php echo lang('export_to_pdf'); ?>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="#" class="bpo" title="<b><?php echo lang('delete_purchases'); ?></b>"
                            data-content="<p><?php echo lang('r_u_sure'); ?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?php echo lang('i_m_sure'); ?></a> <button class='btn bpo-close'><?php echo lang('no'); ?></button>"
                            data-html="true" data-placement="left">
                            <i class="fas fa-trash"></i> <?php echo lang('delete_purchases'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="box-body">
        <table id="POData" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-hover table-striped" width="100%">
                <thead>
                    <tr class="default">
                        <th style="min-width:30px; width: 30px; text-align: center;">
                            <input class="checkbox checkft" type="checkbox" name="check"/>
                        </th>
                        <th><?php echo lang('Provider - Track Code');?></th>
                        <th><?php echo lang('date'); ?></th>
                        <th><?php echo lang('reference_no'); ?></th>
                        <th><?php echo lang('supplier'); ?></th>
                        <th><?php echo lang('status'); ?></th>
                        <th><?php echo lang('grand_total'); ?></th>
                        <th style="min-width:30px; width: 30px; text-align: center;"><i class="fas fa-chain"></i></th>
                        <th style="width:100px;"><?php echo lang('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="11" class="dataTables_empty"><?php echo lang('loading_data_from_server');?></td>
                    </tr>
                </tbody>
                <tfoot class="dtFilter">
                    <tr>
                        <th style="min-width:30px; width: 30px; text-align: center;">
                            <input class="checkbox checkft" type="checkbox" name="check"/>
                        </th>
                        <th><?php echo lang('Provider - Track Code');?></th>
                        <th><?php echo lang('date'); ?></th>
                        <th><?php echo lang('reference_no'); ?></th>
                        <th><?php echo lang('supplier'); ?></th>
                        <th><?php echo lang('status'); ?></th>
                        <th><?php echo lang('grand_total'); ?></th>
                        <th style="min-width:30px; width: 30px; text-align: center;"><i class="fas fa-chain"></i></th>
                        <th style="width:100px;"><?php echo lang('actions'); ?></th>
                    </tr>
                </tfoot>
            </table>
    </div>
</div>



<div style="display: none;">
    <input type="hidden" name="form_action" value="" id="form_action"/>
    <?php echo form_submit('performAction', 'performAction', 'id="action-form-submit"')?>
</div>
<?php echo form_close()?>

