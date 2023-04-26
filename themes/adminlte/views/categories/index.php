<script>

function actions(x) {
    var pqc = x.split("___");
    var button = "";
    <?php if($this->Admin || $GP['categories-disable']): ?>
    if (pqc[1] == 1) {
        var button = "<a id='toggle_categories' data-num='"+pqc[0]+"' data-mode='enable'>" + "<button class='btn btn-danger btn-xs'><i class='fas fa-toggle-off'></i> "+lang.disabled+"</button>" +"</a>";
    }else{
        var button = "<a id='toggle_categories' data-num='"+pqc[0]+"' data-mode='disable'>" + "<button class='btn btn-success btn-xs'><i class='fas fa-toggle-on'></i> "+lang.enabled+"</button>" +"</a>";
    }
    <?php endif; ?>

    var return_var = "";
    <?php if($this->Admin || $GP['categories-edit']): ?>
    return_var += "<a  data-dismiss='modal' id='modify' href='<?php echo base_url();?>panel/settings/edit_categories/"+pqc[0]+"'><button class='btn btn-primary btn-xs'><i class='fas fa-edit'></i> <?php echo lang('edit');?></button></a> ";
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
            'sAjaxSource': '<?php echo base_url(); ?>panel/settings/getAllCategories/<?php echo $toggle_type; ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            }, 
            "aoColumns": [
                null,
                {mRender: actions},
            ],
        });
    });

   
  

    jQuery(document).on("click", "#toggle_categories", function () {
        var num = jQuery(this).data("num");
        var mode = jQuery(this).data("mode");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/settings/toggle_categories",
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
  
<?php if($this->Admin || $GP['categories-add']): ?>
    <a href="<?php echo base_url(); ?>panel/settings/add_categories" class="btn btn-primary">
        <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo lang('Category');?>
    </a>
<?php endif; ?>


<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('settings/categories');?></h3>
      <div class="box-tools pull-right">
        <div class="btn-group">
            <a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>panel/settings/categories"><?php echo lang('All');?></a>
            <a class="btn btn-sm btn-success" href="<?php echo base_url(); ?>panel/settings/categories/enabled"><i class='fas fa-toggle-on'></i> <?php echo lang('enabled');?></a>
            <a class="btn btn-sm btn-danger" href="<?php echo base_url(); ?>panel/settings/categories/disabled"><i class='fas fa-toggle-off'></i> <?php echo lang('disabled');?></a>
        </div>
      </div>
  </div>
  <div class="box-body">
    <div class="table-responsive">
                <table style="width: 100%;" class=" compact table table-bordered table-striped" id="dynamic-table">
                    <thead>
                        <tr>
                            <th><?php echo lang('Category Name');?></th>
                            <th><?php echo lang('actions'); ?></th>
                        </tr>
                    </thead>
                </table>
                
            </div>
  </div>
</div>