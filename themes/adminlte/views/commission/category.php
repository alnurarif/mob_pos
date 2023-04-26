<script type="text/javascript">
function plan(x) {
    var pqc = x.split('____');
    var type = pqc[1];
    var value = pqc[0];

    if (type == 'sales') {
        type = "<?php echo lang('% of Sales');?>";
    } else if (type == 'profit') {
        type = "<?php echo lang('% of Profit');?>";
    } else if (type == 'flat') {
        type = "<?php echo lang('Flat Rate');?>";
    } 
    return value + " ("+type+")";
}

function category(x) {
    if (x == 'repair_parts') {
        return "<?php echo lang('Repair Parts');?>";
    }else if (x == 'new_phones') {
        return "<?php echo lang('New Phones');?>";
    }else if (x == 'used_phones') {
        return "<?php echo lang('Used Phones');?>";
    }else if (x == 'accessories') {
        return "<?php echo lang('accessories');?>";
    }else if (x == 'other') {
        return "<?php echo lang('Other Products');?>";
    }else if (x == 'plans') {
        return "<?php echo lang('Cellular Plans');?>";
    }else{
        return "<?php echo lang('error');?>";
    }
}
var all_groups = <?php echo json_encode($this->ion_auth->groups()->result()); ?>;
function groups(x) {
    var pqc = $.map(x.split(','), function(value){
        return parseInt(value, 10);
    });

    var to_re = [];
    $.each(all_groups, function () {
        if (pqc.includes(parseInt(this.id))) {
            to_re.push(this.name);
        }
    });
    return to_re.join(' | ');
}
    var oTable;
    $(document).ready(function () {
        oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[1, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?php echo lang('All');?>"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('panel/commission/getAllCategoryCommission/'); ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [
                {mRender: category},
                {mRender: plan},
                {mRender: groups},
                null
            ]
        });
    });
    jQuery(document).on("click", "#delete", function () {
        var num = jQuery(this).data("num");
        console.log(num);
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/commission/delete_category",
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
                toastr['success']("<?php echo lang('Deleted');?>");
                $('#dynamic-table').DataTable().ajax.reload();
            }
        });
    });
</script>
<?php if($this->Admin || $GP['commission-assign']): ?>
    <a href="<?php echo base_url(); ?>panel/commission/assign" class="btn btn-primary">
        <i class="fas fa-plus-circle"></i> <?php echo lang('Assign Commission');?>
    </a>
<?php endif; ?>


<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('commission/category');?></h3>
      <div class="box-tools pull-right">
      </div>
  </div>
  <div class="box-body">
    <div class="table-responsive">
            <table class=" compact table table-bordered table-striped" id="dynamic-table" width="100%">
                <thead>
                    <tr>
                        <th><?php echo lang('Category');?></th>
                        <th><?php echo lang('Plan');?></th>
                        <th><?php echo lang('Groups');?></th>
                        <th><?php echo lang('Actions');?></th>
                    </tr>
                </thead>
            </table>
        </div>
  </div>
</div>