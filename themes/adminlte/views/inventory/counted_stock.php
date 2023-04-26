<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
    function wrong_upcs(x) {
        if (x) {
            x = JSON.parse(x);
            var string = '';
            $.each(x, function () {
                string += '<fieldset><legend><?php echo lang('UPC');?>: '+this.name+'</legend><textarea disabled class="form-control" rows="4" style="width: 100%;">'+this.explanation+'</textarea><fieldset>';
            });
            return string;
        }
        return "<?php echo lang('No wrong UPC was entered');?>";
    }
    var oTable;
    $(document).ready(function () {
        oTable = $('#PRData').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('panel/inventory/getCountedStock/'); ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[2];
                nRow.className = "count_id";
                return nRow;
            },
            "aoColumns": [
                {mRender: fld},
                {mRender: wrong_upcs},
            ]
        });

    });
    $('body').on('click', '.count_id td', function() {
        $('#myModal').modal({remote: site.base_url + 'panel/inventory/stocked_items/' + $(this).parent('.count_id').attr('id')});
        $('#myModal').modal('show');
    });
</script>

<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('counted_stock');?></h3>
      <div class="box-tools pull-right">
      </div>
  </div>
  <div class="box-body">
    <div class="table-responsive"  style="width:100%">
            <table id="PRData" class="table table-bordered table-condensed table-hover table-striped" width="100%">
                <thead>
                    <tr class="primary">
                        <th class="col-md-2"><?php echo lang('Date');?></th>
                        <th class="col-md-10"><?php echo lang('Wrong UPC(s)');?></th>
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="2" class="dataTables_empty"><?php echo lang('loading_data_from_server'); ?></td>
                </tr>
                </tbody>
            </table>
        </div>
  </div>
</div>

