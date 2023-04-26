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
        var button = "<a id='toggle_taxrates' data-num='"+pqc[0]+"' data-mode='enable'>" + "<button class='btn btn-danger btn-xs'><i class='fas fa-toggle-off'></i> "+lang.enable+"</button>" +"</a>";
    }else{
        var button = "<a id='toggle_taxrates' data-num='"+pqc[0]+"' data-mode='disable'>" + "<button class='btn btn-success btn-xs'><i class='fas fa-toggle-on'></i> "+lang.disable+"</button>" +"</a>";
    }
    var return_var = "";
    
    <?php if($this->Admin || $GP['tax_rates-edit']): ?>
    return_var += "<a  data-dismiss='modal' id='modify' href='#taxmodal' data-toggle='modal' data-num='"+pqc[0]+"'><button class='btn btn-primary btn-xs'><i class='fas fa-edit'></i> <?php echo lang('edit');?></button></a> ";
    <?php endif;?>
    return_var += button;
    return return_var;
}
function type(x) {
    if (x == 1) {
        return "Percentage";
    }
    if (x == 2) {
        return "Fixed";
    }
}
    $(document).ready(function () {
        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[0, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo base_url(); ?>panel/settings/tax_rates/getAll/<?php echo $type; ?>',
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
            {"mRender": type },
            {mRender: action},
            ],
           
        });
              
    });
    
    jQuery(document).on("click", "#toggle_taxrates", function () {
        var num = jQuery(this).data("num");
        var mode = jQuery(this).data("mode");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/settings/tax_rates/toggle",
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

<!-- ============= MODAL MODIFICA supplierI ============= -->
<div class="modal fade" id="taxmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="title_taxrate"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <p class="tips custip"></p>
                    <div class="row">
                        <form class="col s12">
                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <?php echo lang('tax_name', 'tax_name'); ?>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-user"></i>
                                        </div>
                                        <input id="tax_name" type="text" class="validate form-control" required>
                                    </div>
                                   
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <?php echo lang('tax_code', 'tax_code'); ?>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-user"></i>
                                        </div>
                                        <input id="tax_code" type="text" class="validate form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <?php echo lang('tax_rate', 'tax_rate'); ?>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-road"></i>
                                        </div>
                                        <input id="tax_rate" type="text" class="validate form-control">
                                    </div>
                                    
                                </div>
                            </div>

							<div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <?php echo lang('tax_type', 'tax_type'); ?>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-road"></i>
                                        </div>
                                        <select id="tax_type" class="validate form-control">
                                            <option value="1"><?php echo lang('%');?></option>
                                            <option value="2"><?php echo lang('Fixed');?></option>
                                        </select>
                                    </div>
                                    
                                </div>
                            </div>
                        </form>
                </div>
            </div>
            <div class="modal-footer" id="footer_taxrate">
                  <!--    -->
            </div>
        </div>
    </div>
</div>
</div>


 <?php if($this->Admin || $GP['tax_rates-add']): ?>
    <button href="#taxmodal" class="add_taxrate btn btn-primary">
        <i class="fas fa-plus-circle"></i> <?php echo lang('add').' '.lang('taxrate_title'); ?>
    </button>
<?php endif; ?>
<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('settings/tax_rates');?></h3>
      <div class="box-tools pull-right">
        <div class="btn-group">
            <a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>panel/settings/tax_rates/"><?php echo lang('All');?></a>
            <a class="btn btn-sm btn-success" href="<?php echo base_url(); ?>panel/settings/tax_rates/index/enabled"><i class='fas fa-toggle-on'></i> <?php echo lang('enabled');?></a>
            <a class="btn btn-sm btn-danger" href="<?php echo base_url(); ?>panel/settings/tax_rates/index/disabled"><i class='fas fa-toggle-off'></i> <?php echo lang('disabled');?></a>
        </div>
      </div>
  </div>
  <div class="box-body">
    <div class="adv-table">
                <table class=" compact table table-bordered table-striped" id="dynamic-table" width="100%">
                    <thead>
                        <tr>
                            <th><?php echo lang('tax_name'); ?></th>
                            <th><?php echo lang('tax_code'); ?></th>
                            <th><?php echo lang('tax_rate'); ?></th>
                            <th><?php echo lang('tax_type'); ?></th>
                            <th><?php echo lang('actions'); ?></th>
                        </tr>
                    </thead>
            
                    <tfoot>
                        <tr>
                            <th><?php echo lang('tax_name'); ?></th>
                            <th><?php echo lang('tax_code'); ?></th>
                            <th><?php echo lang('tax_rate'); ?></th>
                            <th><?php echo lang('tax_type'); ?></th>
                            <th><?php echo lang('actions'); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
  </div>
</div>

<script type="text/javascript">



    jQuery(".add_taxrate").on("click", function (e) {
        $('#taxmodal').modal('show');

        jQuery('#tax_name').val('');
        jQuery('#tax_code').val('');
        jQuery('#tax_rate').val('');
        jQuery('#tax_type').val('');
        jQuery('#title_taxrate').html("<?php echo lang('add').' '.lang('taxrate_title'); ?>");

        jQuery('#footer_taxrate').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i><?php echo lang("go_back"); ?></button><button id="submit_taxrate" class="btn btn-success" data-mode="add"><i class="fas fa-user"></i> <?php echo lang("add"); ?></button>');
    });

    jQuery(document).on("click", "#modify", function () {
        jQuery('#title_taxrate').html('Edit Tax Rate');
        
        var num = jQuery(this).data("num");
            jQuery.ajax({
                type: "POST",
                url: base_url + "panel/settings/tax_rates/byID",
                data: "id=" + encodeURI(num) + "&token=<?php echo $_SESSION['token'];?>",
                cache: false,
                dataType: "json",
                success: function (data) {
                    jQuery('#tax_name').val(data.name);
                    jQuery('#tax_code').val(data.code);
                    jQuery('#tax_rate').val(data.rate);
                    jQuery('#tax_type').val(data.type)

                    jQuery('#footer_taxrate').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button id="submit_taxrate" class="btn btn-success" data-mode="modify" data-num="' + encodeURI(num) + '"><i class="fas fa-save"></i> <?php echo lang("save"); ?></button>')
                }
            });
        });
    jQuery(document).on("click", "#submit_taxrate", function () {
        var mode = jQuery(this).data("mode");
        var id = jQuery(this).data("num");

        var name = sanitizer.sanitize(jQuery('#tax_name').val());
        var code = sanitizer.sanitize(jQuery('#tax_code').val());
        var rate = sanitizer.sanitize(jQuery('#tax_rate').val());
        var type = sanitizer.sanitize(jQuery('#tax_type').val());

        //validate
        var valid = true;

        if (valid) {
            var url = "";
            var dataString = "";

            if (mode == "add") {
                url = base_url + "panel/settings/tax_rates/add";
                dataString = "name=" + encodeURI(name) + "&code=" + encodeURI(code)  + "&rate=" + encodeURI(rate)  + "&type=" + encodeURI(type);
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        toastr['success']("<?php echo lang('add'); ?>", "<?php echo lang('taxrate_title'); ?>: " + name + " <?php echo lang('added'); ?>");
                        setTimeout(function () {
                            $('#taxmodal').modal('hide');
                            $('#dynamic-table').DataTable().ajax.reload();
                        }, 500);
                    }
                });
            } else {
                url = base_url + "panel/settings/tax_rates/edit";
                dataString =  "name=" + encodeURI(name) + "&code=" + encodeURI(code)  + "&rate=" + encodeURI(rate)  + "&type=" + encodeURI(type) + "&id=" + encodeURI(id);
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        toastr['success']("<?php echo lang('save'); ?>", "<?php echo lang('taxrate_title'); ?>: " + name + "<?php echo lang('updated'); ?>");
                        setTimeout(function () {
                            $('#taxmodal').modal('hide');
                            $('#dynamic-table').DataTable().ajax.reload();
                        }, 500);
                    }
                });
            }
        }
        return false;
    });
    
</script>