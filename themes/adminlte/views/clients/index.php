 <style>
  /* Always set the map height explicitly to define the size of the div
   * element that contains the map. */
  #map {
    height: 100%;
  }
  /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    #autocomplete{
        z-index: 9999;   
    }
    .pac-container {
        background-color: #FFF;
        z-index: 9999;
        position: fixed;
        display: inline-block;
        float: left;
    }
</style>
<script>

function actions(x) {

    var pqc = x.split("___");
    var action = "<div class='btn-group'><button type=\"button\" class=\"btn btn-default btn-xs btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-expanded=\"true\"><?php echo lang('actions');?> <span class=\"caret\"></button><ul class=\"dropdown-menu pull-right\" role=\"menu\">";

    button = "";

    <?php if($this->Admin || $GP['customers-edit']): ?>
        action += "<li><a href='<?php echo base_url('panel/customers/edit/');?>"+pqc[0]+"'><i class='fas fa-edit'></i> <?php echo lang('Edit');?>   </a></li>";
    <?php endif; ?>


    <?php if($this->Admin || $GP['customers-delete']): ?>
        button += "<li><a id='delete_customer' data-num='"+pqc[0]+"' >" + "<i class='fas fa-trash'></i> <?php echo lang('delete');?>" +"</a></li>";
    <?php endif; ?>
   



    action += button;
    return action;
}
function tp(x) {
    return x.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
}
    $(document).ready(function () {
        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[3, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo base_url(); ?>panel/customers/getAllCustomers/<?php echo $toggle_type;?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            }, 
            "aoColumns": [
            null,
            null,
            null,
            null,
            {mRender: tp},
            {mRender: actions},
                    
            ],
           
        });
              
    });

    jQuery(document).on("click", "#delete_customer", function () {
        var num = jQuery(this).data("num");
        var mode = jQuery(this).data("mode");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/customers/delete",
            data: "id=" + encodeURI(num) ,
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
                toastr['success']("<?php echo lang('deleted');?>");
                $('#dynamic-table').DataTable().ajax.reload();
            }
        });
    });


</script>


<?php if($this->Admin || $GP['customers-add']): ?>
    <button href="#clientmodal" class="add_c btn btn-primary">
        <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo lang('client_title'); ?>
    </button>
<?php endif; ?>

 <a href="<?=base_url();?>panel/customers/export_csv" class="btn btn-primary">
        <i class="fas fa-file-excel"></i> <?= lang('export_to_excel') ?>
</a>
<div class="box box-primary ">
  
    <div class="box-body">
        <div class="table-responsive">
            <table class=" compact table table-bordered table-striped" id="dynamic-table" width="100%">
                <thead>
                    <tr>
                        <th><?php echo lang('client_name'); ?></th>
                        <th><?php echo lang('client_company'); ?></th>
                        <th><?php echo lang('client_address'); ?></th>
                        <th><?php echo lang('client_email'); ?></th>
                        <th><?php echo lang('client_telephone'); ?></th>
                        <th><?php echo lang('actions'); ?></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
