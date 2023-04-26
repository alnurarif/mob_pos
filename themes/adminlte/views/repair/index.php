<link rel="stylesheet" type="text/css" href="<?= $assets ?>plugins/datatables/ext/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="<?= $assets ?>plugins/datatables/ext/buttons.dataTables.min.css">
<script type="text/javascript" src="<?= $assets ?>plugins/datatables/ext/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>plugins/datatables/ext/jszip.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>plugins/datatables/ext/pdfmake.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>plugins/datatables/ext/vfs_fonts.js"></script>
<script type="text/javascript" src="<?= $assets ?>plugins/datatables/ext/buttons.html5.min.js"></script>

<style type="text/css">
    .payment_row td:first-child {
        position: relative;
        overflow: hidden;
        width: 300px;
    }
    .payment_row td:first-child:before {
        content: "";
        position: absolute;
        width: 30px;
        height: 30px;
        background: red;
        top: -16px;
        left: -15px;
        text-align: center;
        line-height: 90px;
        transform: rotate(49deg);
    }
</style>
<script>
    jQuery(document).on("click", "#email", function() {
      var id = $(this).data('num');
        bootbox.prompt({
            title: "<?php echo lang('Send Email');?>",
            inputType: 'email',
            value: "",
            callback: function (email) {
                $('.bootbox-input-email').val('');
                if (email) {
                     if(email !== null && isValidEmailAddress(email) ) {
                        $.ajax({
                            type: "post",
                            url: "<?php echo base_url('panel/repair/email') ?>",
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
               
            }
        });
    });

    function actions(x) {
        var pqc = x.split("___");
        // if (pqc[1] == 1) {
        //     var button = "<li><a id='toggle' data-num='"+pqc[0]+"' data-mode='enable' data-toggle='tooltip' data-placement='top' title='Enable This Repair'><i class='fas fa-toggle-on'></i> "+lang.enable+"</a></li>";
        // }else{
        //     var button = "<li><a id='toggle' data-num='"+pqc[0]+"' data-mode='disable' data-toggle='tooltip' data-placement='top' title='Disable This Repair'><i class='fas fa-toggle-off'></i> "+lang.disable+"</a></li>";
        // }

        var button = "<li><a id='delete_repair' data-num='"+pqc[0]+"'><i class='fas fa-trash'></i> <?=lang('delete');?></a></li>";

        var view = "<li><a class='view' data-toggle=\"modal\" data-target=\"#myModalLG\"  href='<?php echo base_url();?>panel/repair/view/"+pqc[0]+"'><i class='fas fa-check'></i> <?php echo lang('view_repair');?></a></li>";
        
       
        // var edit = "<li><a href=<?php echo base_url();?>panel/repair/edit/"+pqc[0]+"><i class='fas fa-edit'></i> <?php echo lang('edit_repair');?></a></li>";

        var edit = "<li><a  data-dismiss='modal' id='modify_reparation' href='#repairmodal' data-toggle='modal' data-num='"+pqc[0]+"'><i class='fas fa-edit'></i> <?=lang('edit_repair');?></a></li>"; 

        var email = "<li><a id='email' data-num='"+pqc[0]+"'><i class='fas fa-envelope'></i> <?php echo lang('send_email');?></a></li>";



        var print_barcode = '<li><a href="<?php echo base_url();?>panel/repair/print_barcodes/'+pqc[0]+'"><i class="fas fa-print"></i> <?php echo lang('print_barcode');?></a></li>'; 


        var return_var = `

        <button class="btn btn-xs btn-primary" type="button" data-type="2" data-num="${pqc[0]}" id="print_repair">
            <i class="fas fa-print"></i> 
            <?php echo lang('report'); ?>
        </button>
        <button class="btn btn-xs btn-primary" type="button" data-type="1" data-num="${pqc[0]}" id="print_repair">
            <i class="fas fa-print"></i> 
            <?php echo lang('invoice');?>
        </button>
        <div class='btn-group'><button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><?php echo lang('Actions');?></button><ul class="dropdown-menu pull-right" role="menu">`;



        <?php if($this->Admin || $GP['repair-edit']): ?>
            return_var += edit;
        <?php endif; ?>

        if(pqc[2] == 1) {
            return_var += "<li><a data-toggle=\"modal\" data-target=\"#myModal\"  href='<?php echo base_url();?>panel/sales/modal_view/"+pqc[3]+"'><i class='fas fa-check'></i> <?php echo lang('view_sale');?></a></li>";
        }
        return_var += view;
        // return_var += view_payments;
        // return_var += add_payment;

        return_var += `

        


        <li><a id="upload_modal_btn" data-mode="edit" data-num="${pqc[0]}"><i class="fas fa-cloud"></i> <?php echo lang('view_attached');?></a></li>

        `;

        return_var += print_barcode;
        return_var += email;
        <?php if($this->Admin || $GP['repair-delete']): ?>
            return_var += button;
        <?php endif; ?>

        return_var += '</ul></div>';

        return return_var;
    }

    $(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
    $(document).ready(function () {
        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[5, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo base_url(); ?>panel/repair/getAllrepairs/<?php echo $toggle_type; ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[12];
                pos_paid = aData[14];

                var classes = "repair_link";
                if (parseInt(pos_paid) == 1) {
                    classes += " success";
                }

                nRow.className = classes;
                return nRow;
            },
            dom: 'lBfrtip',
            buttons: [{
                extend: 'excelHtml5',
                exportOptions: {
                  columns: ':not(:last-child)',
                },
                text: '<?= lang('export_to_excel');?>',
            }, {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':not(:last-child)',
                },
                text: '<?= lang('export_to_pdf');?>',
            }],
            "aoColumns": [
                {
                    "bSortable": false,
                    "mRender": checkbox
                },
                null,
                null,
                {"mRender": tp},
                null,
                null,
                {"mRender": fld},
                {"mRender": status},
                null,
                null,
                {mRender: currencyFormat},
                {"mRender": warranty},
                {"mRender": actions},
            ],
            "createdRow": function( row, data, dataIndex){
                deposit_collect = data[13] ? data[13].split('x') : [''];
               
            },
        });
    });
  
    $('body').on('click', '.repair_link td:not(:last-child)', function() {
        id = $(this).parent('.repair_link').attr('id');
        id  = id.split('__');
        id = id[0];
        $('#myModalLG').modal({remote: site.base_url + 'panel/repair/view/' + id});
        $('#myModalLG').modal('show');
    });


    jQuery(document).on("click", "#print_repair", function() {
        var num = jQuery(this).data("num");
        var type = jQuery(this).data("type");
        toastr['success']("<?php echo $this->lang->line('repair_is_printing');?>");
        var thePopup = window.open( base_url + "panel/repair/invoice/" + encodeURI(num) + "/" + type, '_blank', "width=890, height=700");
    });

</script>


<?php if($this->Admin || $GP['repair-add']): ?>
    <button href="#repairmodal" class="add_repair btn btn-primary">
        <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo lang('repair_title'); ?>
    </button>
<?php endif; ?>
<!-- Main content -->

<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('repair/index');?></h3>
      <div class="box-tools pull-right">
            <div class="btn-group">
                <!-- <a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>panel/repair/"><?php echo lang('All');?></a> -->
                <!-- <a class="btn btn-sm btn-success" href="<?php echo base_url(); ?>panel/repair/index/enabled"><i class='fas fa-toggle-on'></i> <?php echo lang('enabled');?></a> -->
                <!-- <a class="btn btn-sm btn-danger" href="<?php echo base_url(); ?>panel/repair/index/disabled"><i class='fas fa-toggle-off'></i> <?php echo lang('disabled');?></a> -->
            </div>
            <div class="box-tools pull-right">
                <a data-toggle="dropdown" class="btn btn-primary" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?php echo lang('actions') ?>"></i></a>
                <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                    <li><a href="#" id="excel" data-action="export_excel"><i class="fas fa-file-excel"></i> <?php echo lang('export_to_excel') ?></a></li>
                    <li class="divider"></li>
                    <li><a href="#" class="po" title="<b><?php echo $this->lang->line('delete') ?></b>" data-content="<p><?php echo lang('r_u_sure') ?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?php echo lang('i_m_sure') ?></a> <button class='btn po-close'><?php echo lang('no') ?></button>" data-html="true" data-placement="left"><i class="fas fa-trash"></i> <?php echo lang('delete') ?></a></li>
                </ul>
              </div>

            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-default"><?php echo lang('Filter by Current Status');?></button>
                <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="<?php echo base_url(); ?>panel/repair/index/default"><?php echo lang('Default');?></a></li>
                    <li><a href="<?php echo base_url(); ?>panel/repair/index/completed"><?php echo lang('completed_repairs');?></a></li>
                    <li><a href="<?php echo base_url(); ?>panel/repair/index/pending"><?php echo lang('pending_repairs');?></a></li>
                    <li class="divider"></li>
                    <?php foreach ($statuses as $status): ?>
                    <li>
                        <a href="<?php echo base_url(); ?>panel/repair/index/<?php echo $status->id;?>"><?php echo escapeStr($status->label);?></a>
                    </li>
                    <?php endforeach;?>
                   <!--  <li><a href="<?php echo base_url(); ?>panel/repair/index/0"><?php echo lang('cancelled');?></a>
                    </li> -->
                </ul>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <?php  echo form_open('panel/repair/actions', 'id="action-form"'); ?>
            <table class="compact table table-bordered table-striped" id="dynamic-table" width="100%">
                <thead>
                    <tr>
                        <th style="min-width:30px; width: 30px; text-align: center;">
                        <input class="checkbox checkth" type="checkbox" name="check"/>
                    </th>
                        <th><?php echo lang('Serial Number'); ?></th>
                        <th><?php echo lang('repair_name'); ?></th>
                        <th><?php echo lang('client_telephone'); ?></th>
                        <th><?php echo lang('repair_defect'); ?></th>
                        <th><?php echo lang('repair_model'); ?></th>
                        <th><?php echo lang('repair_opened_at'); ?></th>
                        <th><?php echo lang('repair_status'); ?></th>
                        <th><?php echo lang('assigned_to'); ?></th>
                        <th><?php echo lang('repair_code'); ?></th>
                        <th><?php echo lang('grand_total'); ?></th>
                        <th><?php echo lang('warranty'); ?></th>
                        <th><?php echo lang('actions'); ?></th>

                        <!-- <th><?php echo lang('repair_name'); ?></th>
                        <th><?php echo lang('client_telephone'); ?></th>
                        <th><?php echo lang('repair_model'); ?></th>
                        <th><?php echo lang('repair_opened_at'); ?></th>
                        <th><?php echo lang('repair_status'); ?></th>
                        <th><?php echo lang('repair_code'); ?></th>
                        <th><?php echo lang('grand_total'); ?></th>
                        <th><?php echo lang('paid'); ?></th>
                        <th><?php echo lang('payment_status'); ?></th>
                        <th><?php echo lang('actions'); ?></th> -->
                    </tr>
                </thead>
            </table>
            <div style="display: none;">
                <input type="hidden" name="form_action" value="" id="form_action"/>
                <?php echo form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
            </div>
            <?php echo form_close() ?>
        </div>
    </div>
</div>