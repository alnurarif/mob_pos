<script type="text/javascript">
    var oTable;
    $(document).ready(function () {
        oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[1, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('panel/commission/getAllCommission/'); ?><?php echo $toggle_type; ?>',
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
            ]
        });
    });
    function actions(x) {
        var pqc = x.split("__");
        var button = "";
        
        <?php if($this->Admin || $GP['commission-disable']): ?>
        if (pqc[1] == 1) {
            button += "<a id='toggle' data-num='"+pqc[0]+"' data-mode='enable'>" + "<button class='btn btn-success tooltiped btn-xs' data-toggle='tooltip' data-placement='top' title='<?php echo lang('Enable This Commission');?>'><i class='fas fa-toggle-on'></i> "+lang.enable+"</button>" +"</a>";
        }else{
            button += "<a id='toggle' data-num='"+pqc[0]+"' data-mode='disable'>" + "<button class='btn btn-danger tooltiped btn-xs' data-toggle='tooltip' data-placement='top' title='<?php echo lang('Disable This Commission');?>'><i class='fas fa-toggle-off'></i> "+lang.disable+"</button>" +"</a>";
        }
        <?php endif; ?>

        var return_var = "";
        <?php if($this->Admin || $GP['commission-edit']): ?>
        return_var += "<a href=\"<?php echo base_url(); ?>panel/commission/edit/"+pqc[0]+"\" class='btn btn-primary tooltiped btn-xs' data-toggle='tooltip' data-placement='top' title='<?php echo lang('Edit Commission');?>'><i class='fas fa-edit'></i></a>";
        <?php endif; ?>
        return_var += button;
        return return_var;
    }
    jQuery(document).on("click", "#toggle", function () {
        var num = jQuery(this).data("num");
        var mode = jQuery(this).data("mode");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/commission/toggle",
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

<?php if($this->Admin || $GP['commission-add']): ?>
<a href="<?php echo base_url(); ?>panel/commission/add" class="btn btn-primary">
    <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo lang('Commission Plan');?>
</a>
<?php endif; ?>
<?php if($this->Admin || $GP['commission-assign']): ?>
<a href="<?php echo base_url(); ?>panel/commission/assign" class="btn btn-primary">
    <i class="fas fa-plus-circle"></i> <?php echo lang('Assign Commission');?>
</a>
<?php endif; ?>


<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('commission/index');?></h3>
      <div class="box-tools pull-right">
        <div class="btn-group">
            <a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>panel/commission/"><?php echo lang('All');?></a>
            <a class="btn btn-sm btn-success" href="<?php echo base_url(); ?>panel/commission/index/enabled"><i class='fas fa-toggle-on'></i> <?php echo lang('enabled');?></a>
            <a class="btn btn-sm btn-danger" href="<?php echo base_url(); ?>panel/commission/index/disabled"><i class='fas fa-toggle-off'></i> <?php echo lang('disabled');?></a>
        </div>
      </div>
  </div>
  <div class="box-body">
    <div class="table-responsive">
            <table class=" compact table table-bordered table-striped" id="dynamic-table" width="100%">
                <thead>
                    <tr>
                        <th><?php echo lang('Name');?></th>
                        <th><?php echo lang('actions'); ?></th>
                    </tr>
                </thead>
            </table>
        </div>
  </div>
</div>