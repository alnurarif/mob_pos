<?php
if ($this->uri->segment(5) == 'enabled' or $this->uri->segment(5) == 'disabled') {
    $type = $this->uri->segment(5);
}else{
    $type = NULL;
}
?>
<script>
function action(x) {
    var pqc = x.split("___");
    if (pqc[1] == 1) {
        var button = "<a id='toggle_model' data-num='"+pqc[0]+"' data-mode='enable'>" + "<button class='btn btn-danger btn-xs'><i class='fas fa-toggle-off'></i> "+lang.enable+"</button>" +"</a>";
    }else{
        var button = "<a id='toggle_model' data-num='"+pqc[0]+"' data-mode='disable'>" + "<button class='btn btn-success btn-xs'><i class='fas fa-toggle-on'></i> "+lang.disable+"</button>" +"</a>";
    }
    var return_var = "";
    <?php if($this->Admin || $GP['models-edit']): ?>
    return_var += "<a  data-dismiss='modal' id='modify_model' href='#modelmodal' data-toggle='modal' data-num='"+pqc[0]+"'><button class='btn btn-primary btn-xs'><i class='fas fa-edit'></i> <?php echo lang('edit');?></button></a> ";
    <?php endif; ?>
    return_var += button;
    return return_var;
}
    $(document).ready(function () {
        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[0, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo base_url(); ?>panel/settings/models/getAll/<?php echo $type;?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            }, 
            "aoColumns": [
            null,
            {mRender: action},
            ],
           
        });
              
    });


    jQuery(document).on("click", "#toggle_model", function () {
        var num = jQuery(this).data("num");
        var mode = jQuery(this).data("mode");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/settings/models/toggle",
            data: "id=" + encodeURI(num) +"&toggle=" + encodeURI(mode),
            cache: false,
            dataType: "json",
            success: function (data) {
                console.log(data);
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
                toastr['success']("<?php echo lang('Toggle');?>: ", data.toggle);
                $('#dynamic-table').DataTable().ajax.reload();
            }
        });
    });

</script>


    <?php if($this->Admin || $GP['models-add']): ?>

<button href="#modelmodal" class="add_model btn btn-primary">
    <i class="fas fa-plus-circle"></i> <?php echo lang('add').' ' . lang('model'); ?>
</button>
<?php endif; ?>


<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('settings/models');?></h3>
      <div class="box-tools pull-right">
        <div class="btn-group">
            <a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>panel/settings/models/"><?php echo lang('All');?></a>
            <a class="btn btn-sm btn-success" href="<?php echo base_url(); ?>panel/settings/models/index/enabled"><i class='fas fa-toggle-on'></i> <?php echo lang('enabled');?></a>
            <a class="btn btn-sm btn-danger" href="<?php echo base_url(); ?>panel/settings/models/index/disabled"><i class='fas fa-toggle-off'></i> <?php echo lang('disabled');?></a>
        </div>
      </div>
  </div>
  <div class="box-body">
    <table class=" compact table table-bordered table-striped" id="dynamic-table" width="100%">
                <thead>
                    <tr>
                        <th><?php echo lang('tax_name'); ?></th>
                        <th><?php echo lang('actions'); ?></th>
                    </tr>
                </thead>
        
                <tfoot>
                    <tr>
                        <th><?php echo lang('tax_name'); ?></th>
                        <th><?php echo lang('actions'); ?></th>
                    </tr>
                </tfoot>
            </table>
  </div>
</div>
    