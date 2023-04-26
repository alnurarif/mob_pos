<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
    function row_status(x) {
        if (x == 1) {
            return '<div class="text-center"><span class="label label-warning">'+lang.ready_to_purchase+'</span></div>';
        } else if(x == 2) {
            return '<div class="text-center"><span class="label label-success">'+lang.purchased+'</span></div>';
        }else{
            return x;
        }
    }
    function actions(x) {
        rq = x.split('___');
        var string  = '<div class="btn-group text-left"><button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?php echo lang('Actions');?> <span class="caret"></span></button><ul class="dropdown-menu pull-right" role="menu">';
        if (rq[1] == 1) {
            <?php if($this->Admin || $GP['purchases-customer_edit']): ?>
            string += '<li><a href="<?php echo base_url();?>panel/purchases/customer_edit/'+rq[0]+'"><i class="fas fa-edit"></i> '+"<?php echo lang('edit_purchase');?>"+'</a></li>';
            <?php endif; ?>
        }





        string += '<li><a data-toggle="modal" data-target="#myModal" href="<?php echo base_url();?>panel/purchases/customer_modal_view/'+rq[0]+'"><i class="fas fa-check"></i> '+"<?php echo lang('View Purchase');?>"+'</a></li>';


        string += '<li><a href="#" id="email" data-num="'+rq[0]+'"><i class="fas fa-edit"></i> '+"<?php echo lang('email_purchase');?>"+'</a></li>';
        <?php if($this->Admin || $GP['purchases-customer_delete']): ?>
        string += '<li><a href="#" class="po" title="<b><?php echo lang('delete_purchase');?></b>" data-content="<p><?php echo lang('r_u_sure');?></p><a class=\'btn btn-danger po-delete\' href=\'<?php echo base_url();?>panel/purchases/customer_delete/'+rq[0]+'\'><?php echo lang('yes');?></a> <button class=\'btn po-close\'><?php echo lang('no');?></button>" rel="popover"><i class="fas fa-trash"></i> Delete Purchase</a></li></ul></div>';
        <?php endif; ?>
        return string;
}

jQuery(document).on("click", "#email", function() {
  var id = $(this).data('num');
    bootbox.prompt({
        title: "Send Email",
        inputType: 'email',
        value: "",
        callback: function (email) {
            $('.bootbox-input-email').val('');
            if(email !== null && isValidEmailAddress(email) ) {
                $.ajax({
                    type: "post",
                    url: "<?php echo base_url('panel/purchases/customer_email') ?>",
                    data: {email: email, id: id},
                    dataType: "json",
                    success: function (data) {
                        bootbox.alert({message: data.msg, size: 'small'});
                    },
                    error: function () {
                        bootbox.alert({message: 'Request Failed', size: 'small'});
                        return false;
                    }
                });
            }else{
                bootbox.alert("<?php echo lang('Format Incorrect');?>");
            }
        }
    });
});

function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return pattern.test(emailAddress);
};
    $(document).ready(function () {
        var oTable = $('#POData').dataTable({
            "aaSorting": [[1, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?php echo lang('all')?>"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('panel/purchases/getCustomerPurchases'); ?>/<?php echo $status_view; ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name()?>",
                    "value": "<?php echo $this->security->get_csrf_hash()?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [
                    {"bSortable": false,"mRender": checkbox},
                    {"mRender": fsd}, 
                    null,
                    {"mRender": row_status},
                    null,
                    {"mRender": currencyFormat},
                    {"mRender": actions},
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
                    total   +=  parseInt(aaData[aiDisplay[i]]['5']);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[5].innerHTML = '<?php echo escapeStr($settings->currency); ?> ' + (total);
            }
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
<?php
    echo form_open('panel/purchases/customer_purchase_actions', 'id="action-form"');
?>
  <?php if($this->Admin || $GP['purchases-customer_add']): ?>
                    <a class="btn btn-primary" href="<?php echo site_url('panel/purchases/customer_add')?>">
                        <i class="fas fa-plus-circle"></i> <?php echo lang('Add Customer Purchase');?>
                    </a>
                <?php endif; ?>
    <div class="box" id="dripicons-iconz">
        <div class="box-header">
            <div class="box-title"><?php echo lang('Customer Purchase');?> </div>
            <div class="box-tools pull-right">
                <div class="btn-group">
                    <a class="btn btn-default" href="<?php echo site_url('panel/purchases/customer')?>">
                        <?php echo lang('All');?>
                    </a>
                    <a class="btn btn-warning"  href="<?php echo site_url('panel/purchases/customer/ready')?>">
                        <?php echo lang('ready_to_purchase');?>
                    </a>
                    <a class="btn btn-success" href="<?php echo site_url('panel/purchases/customer/purchased')?>">
                        <?php echo lang('purchased');?>
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
                            <th><?php echo lang('date'); ?></th>
                            <th><?php echo lang('Customer');?></th>
                            <th><?php echo lang('status'); ?></th>
                            <th><?php echo lang('IMEI'); ?></th>
                            <th><?php echo lang('grand_total'); ?></th>
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
                            <th><?php echo lang('date'); ?></th>
                            <th><?php echo lang('Customer');?></th>
                            <th><?php echo lang('status'); ?></th>
                            <th><?php echo lang('IMEI'); ?></th>
                            <th><?php echo lang('grand_total'); ?></th>
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
