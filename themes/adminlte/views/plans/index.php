<script>

function actions(x) {
    var pqc = x.split("___");
    var action = "<div class='btn-group'><button type=\"button\" class=\"btn btn-default btn-xs btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-expanded=\"true\"><?php echo lang('Actions');?> <span class=\"caret\"></button><ul class=\"dropdown-menu pull-right\" role=\"menu\">";
    <?php if($this->Admin || $GP['plans-edit']): ?>
        action += "<li><a href='<?php echo base_url('panel/plans/edit/');?>"+pqc[0]+"'><i class='fas fa-edit'></i> "+lang.edit+"</a></li>";
    <?php endif; ?>
    <?php if($this->Admin || $GP['plans-delete']): ?>
        action += "<li><a id='delete_plan' data-num='"+pqc[0]+"' data-mode='disable'><i class='fas fa-trash'></i> <?=lang('delete');?></a></li>";
    <?php endif; ?>
    action += '</ul></div></div>';
    return action;
}
    $(document).ready(function () {
        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[0, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo base_url(); ?>panel/plans/getAllPlans/<?php echo $toggle_type; ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            }, 
            "aoColumns": [
                {"bSortable": false, "mRender": checkbox},
                null,
                {mRender: actions},
            ],
        });
    });

   
  

    jQuery(document).on("click", "#delete_plan", function () {
        var num = jQuery(this).data("num");
        var mode = jQuery(this).data("mode");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/plans/delete",
            data: "id=" + encodeURI(num),
            cache: false,
            dataType: "json",
            success: function (data) {
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "progressBar": true,
                    "positionClass": "toast-bottom-right",
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                }
                toastr['success']("<?php echo lang('deleted');?>");
                $('#dynamic-table').DataTable().ajax.reload();
            }
        });
    });
    
</script>
  
<?php if($this->Admin || $GP['plans-add']): ?>
<a href="<?php echo base_url(); ?>panel/plans/add" class="add_plans btn btn-primary">
    <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo lang('Plan');?>
</a>
<?php endif; ?>
<?php echo form_open('panel/plans/actions', 'id="action-form"'); ?>


<div class="box box-primary ">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo lang('Cellular Plans');?></h3>
        <div class="box-tools pull-right">
            <div class="btn-group">
                <a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>panel/plans/"><?php echo lang('All');?></a>
                <a class="btn btn-sm btn-success" href="<?php echo base_url(); ?>panel/plans/index/enabled"><i class='fas fa-toggle-on'></i> <?php echo lang('enabled');?></a>
                <a class="btn btn-sm btn-danger" href="<?php echo base_url(); ?>panel/plans/index/disabled"><i class='fas fa-toggle-off'></i> <?php echo lang('disabled');?></a>
                <li class="btn btn-default" style="list-style-type: none;">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fas fa-tasks tip" data-placement="left" title="<?php echo lang("actions") ?>"></i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        
                       
                        <li>
                            <a href="#" id="excel" data-action="export_excel">
                                <i class="fas fa-file-excel"></i> <?php echo lang('export_to_excel') ?>
                            </a                        </li>
                        <li>
                            <a href="#" id="pdf" data-action="export_pdf">
                                <i class="fas fa-file-pdf"></i> <?php echo lang('export_to_pdf') ?>
                            </a>
                        </li>
                      
                    </ul>
                </li>
            </div>
        </div>
    </div>
    <div class="box-body">
         <div class="table-responsive">
            <table class=" compact table table-bordered table-striped" id="dynamic-table" width="100%">
                <thead>
                    <tr>
                        <th style="min-width:30px; width: 30px; text-align: center;">
                            <input class="checkbox checkth" type="checkbox" name="check"/>
                        </th>
                        <th><?php echo lang('name'); ?></th>
                        <th><?php echo lang('actions'); ?></th>
                    </tr>
                </thead>
            </table>
            
        </div>
    </div>
</div>

<div style="display: none;">
    <input type="hidden" name="form_action" value="" id="form_action"/>
    <?php echo form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
</div>
<?php echo form_close() ?>