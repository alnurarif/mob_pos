<?php
$type = (in_array($this->uri->segment(4), array('past', 'future', 'today')) ) ? '&type='.$this->uri->segment(4) : '';

?>

<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('Activities');?></h3>
      <div class="box-tools pull-right">
        <div class="btn-group">
            <a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>panel/reports/activities/"><?php echo lang('All');?></a>
            <a class="btn btn-sm btn-success" href="<?php echo base_url(); ?>panel/reports/activities/past"><?php echo lang('Past');?></a>
            <a class="btn btn-sm btn-warning" href="<?php echo base_url(); ?>panel/reports/activities/today"><?php echo lang('Today');?></a>
            <a class="btn btn-sm btn-info" href="<?php echo base_url(); ?>panel/reports/activities/future"><?php echo lang('Future');?></a>
        </div>
      </div>
  </div>
  <div class="box-body">
        <table class=" compact table table-bordered table-striped" id="activity-table" width="100%">
            <thead>
                <tr>
                    <th><?php echo lang('Client');?></th>
                    <th><?php echo lang('Activity');?></th>
                    <th><?php echo lang('Sub Activity');?></th>
                    <th><?php echo lang('Locations');?></th>
                    <th><?php echo lang('Due Date');?></th>
                    <th><?php echo lang('Remind Status');?></th>
                    <th><?php echo lang('Priority');?></th>
                    <th><?php echo lang('Status');?></th>
                    <!-- <th>Actions</th> -->
                </tr>
            </thead>
           
        </table>
  </div>
</div>
<script type="text/javascript">
    
    $(document).on('click', '#change_to_close', function (event) {
       event.preventDefault();
       var id = $(this).data('num');
       jQuery.ajax({
            type: "POST",
            url: base_url + "panel/customers/closeActivity",
            data: "id=" + encodeURI(id),
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
                toastr['success']("<?php echo lang('Activity Status Changed');?>");
                $('#activity-table').DataTable().ajax.reload();
            }
        });
    });
</script>

<script type="text/javascript">
    var stores = (<?php echo json_encode($this->settings_model->getAllStores(TRUE, TRUE)); ?>);
    function activity_priority(x) {
        if (x == 'low') {
            return '<span class="label label-info">'+(x)+'</span>';
        } else if (x == 'medium') {
            return '<span class="label label-warning">'+(x)+'</span>';
        } else if (x == 'high') {
            return '<span class="label label-danger">'+(x)+'</span>';
        }else{
            return '<span class="label label-primary">'+(x)+'</span>';
        }
    }

    function activity_status(x) {
        var pqc = x.split('___');
        if ((pqc[1] == 'open')) {
            return '<button id="change_to_close" data-num="'+pqc[0]+'" class="btn btn-warning btn-sm">'+lang.mark_closed+'/button>';
        }else{
            return '<span class="label label-primary">'+(pqc[1])+'</span>';
        }
    }

    function activity_remind(x) {
        if(x == 'future') {
            return('<span class="label label-success">'+"<?php echo lang('Coming in future!');?>"+'</span>');
        }else if(x == 'today'){
            return('<span class="label label-warning">'+"<?php echo lang('Due Today!');?>"+'</span>');
        }else{
            return('<span class="label label-danger">'+"<?php echo lang('Past');?>"+'</span>');
        }
    }

    function client(x) {
        var pqc = x.split('___');
        return '<a href="<?php echo base_url(); ?>panel/customers/edit/'+pqc[1]+'#activities">'+pqc[0]+'</a>';
    }

    $(document).ready(function () {
        var oTable = $('#activity-table').dataTable({
            "aaSorting": [[4, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('panel/reports/getAllActivities/?v=1'.$type); ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            }, 
            "aoColumns": [
                {mRender: client},
                null,
                null,
                null,
                {mRender: fld},
                {mRender: activity_remind},
                {mRender: activity_priority},
                {mRender: activity_status},
            ], 
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[8];
                nRow.className = "view_act";
                return nRow;
            },
           
        });
    });

    $('body').on('click', '.view_act td:not(:last-child)', function() {
        $('#myModal').modal({remote: site.base_url + 'panel/customers/view_note/' + $(this).parent('.view_act').attr('id') + '/1'});
        $('#myModal').modal('show');
    });

</script>