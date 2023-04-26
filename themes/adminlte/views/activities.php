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
        var button = "<a id='toggle_activities' data-num='"+pqc[0]+"' data-mode='enable'>" + "<button class='btn btn-danger btn-xs'><i class='fas fa-toggle-off'></i> "+lang.enable+"</button>" +"</a>";
    }else{
        var button = "<a id='toggle_activities' data-num='"+pqc[0]+"' data-mode='disable'>" + "<button class='btn btn-success btn-xs'><i class='fas fa-toggle-on'></i> "+lang.disable+"</button>" +"</a>";
    }
    var return_var = "";
    <?php if($this->Admin || $GP['activities-edit']): ?>
    return_var += "<a  data-dismiss='modal' id='modify' href='#activitiesmodal' data-toggle='modal' data-num='"+pqc[0]+"'><button class='btn btn-primary btn-xs'><i class='fas fa-edit'></i> <?php echo lang('edit');?></button></a> ";
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
            'sAjaxSource': '<?php echo base_url(); ?>panel/settings/activities/getAll/<?php echo $type;?>',
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
            {mRender: action},
            ],
           
        });
              
    });


    jQuery(document).on("click", "#toggle_activities", function () {
        var num = jQuery(this).data("num");
        var mode = jQuery(this).data("mode");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/settings/activities/toggle",
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
<div class="modal fade" id="activitiesmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="titsupplieri"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <p class="tips custip"></p>
                    <div class="row">
                        <form id="activities_form" method="post">
                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <label><?php echo lang('Activity Name');?></label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-user"></i>
                                        </div>
                                        <input name="name" type="text" class="validate form-control" required>
                                    </div>
                                        <span id="errorText"></span>
                                </div>
                            </div>
                            
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label><?php echo lang('Activity');?></label>
                                    <?php 
                                    $tr = array();
                                    foreach ($activities as $activity) {
                                        $tr[$activity['id']] = $activity['name'];
                                    }
                                    echo form_dropdown('sub_id', $tr, '', 'class="form-control tip" id="sub_id" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 input-field">
                                <div class="form-group all">
                                    <div class="checkbox-styled checkbox-inline">
                                        <input type="checkbox" id="universal_act" value="1">
                                        <label for="universal_act"><?php echo lang('is_universal'); ?></label>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="modal-footer" id="footersupplier1">
                      <!--    -->
                </div>
            </div>
        </div>
    </div>
</div>


    <button href="#activitiesmodal" class="add_activities btn btn-primary">
        <i class="fas fa-plus-circle"></i> <?php echo lang('add').' ' . lang('Activity'); ?>
    </button>


<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('Activities');?></h3>
      <div class="box-tools pull-right">
        <div class="btn-group">
            <a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>panel/settings/activities/"><?php echo lang('All');?></a>
            <a class="btn btn-sm btn-success" href="<?php echo base_url(); ?>panel/settings/activities/index/enabled"><i class='fas fa-toggle-on'></i> <?php echo lang('enabled');?></a>
            <a class="btn btn-sm btn-danger" href="<?php echo base_url(); ?>panel/settings/activities/index/disabled"><i class='fas fa-toggle-off'></i> <?php echo lang('disabled');?></a>
        </div>
      </div>
    </div>
    <div class="box-body">
        <div class="adv-table">
            <table class=" compact table table-bordered table-striped" id="dynamic-table" width="100%">
                <thead>
                    <tr>
                        <th><?php echo lang('tax_name'); ?></th>
                        <th><?php echo lang('Sub Activity Name'); ?></th>
                        <th><?php echo lang('actions'); ?></th>
                    </tr>
                </thead>
        
                <tfoot>
                    <tr>
                        <th><?php echo lang('tax_name'); ?></th>
                        <th><?php echo lang('Sub Activity Name'); ?></th>
                        <th><?php echo lang('actions'); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">

    jQuery(".add_activities").on("click", function (e) {
        $('#activitiesmodal').modal('show');
        
        document.getElementById("universal_act").checked = false;
        jQuery('#sub_id').val('').trigger('change');
        
        jQuery('form :input').val('');
        jQuery('#titsupplieri').html("<?php echo lang('add').' '.lang('Activity'); ?>");
        jQuery('#footersupplier1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i><?php echo lang("go_back"); ?></button><button role="button" form="activities_form" id="submit_activities" class="btn btn-success" data-mode="add"><i class="fas fa-user"></i> <?php echo lang("add"); ?></button>');
    });

    jQuery(document).on("click", "#modify", function () {
        jQuery('#titsupplieri').html("<?php echo lang('edit');?> <?php echo lang('Activity');?>");
        
            var num = jQuery(this).data("num");
            jQuery.ajax({
                type: "POST",
                url: base_url + "panel/settings/activities/byID",
                data: "id=" + encodeURI(num) + "&token=<?php echo $_SESSION['token'];?>",
                cache: false,
                dataType: "json",
                success: function (data) {
                    jQuery('input[name=name]').val(data.name);
                    jQuery('#sub_id').val('').trigger('change');

                    if (data.sub_id) {
                        $('#sub_id').val(data.sub_id).trigger('change');
                    }
                    
                    if (data.universal == 1) {
                        document.getElementById("universal_act").checked = true;
                    }else{
                        document.getElementById("universal_act").checked = false;
                    }

                    jQuery('#footersupplier1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button role="button" form="activities_form" id="submit_activities" class="btn btn-success" data-mode="modify" data-num="' + encodeURI(num) + '"><i class="fas fa-save"></i> <?php echo lang("save"); ?></button>')
                }
            });
        });

   $('#activities_form').on( "submit", function(event) {
        event.preventDefault();
        var mode = jQuery('#submit_activities').data("mode");
        var id = jQuery('#submit_activities').data("num");
        if (document.getElementById('universal_act').checked){
            var universal = 1;
        }else{
            var universal = 0;
        }

        //validate
        var valid = true;
        if (valid) {
            var url = "";
            var dataString = "";

            if (mode == "add") {
                url = base_url + "panel/settings/activities/add";
                dataString = $('#activities_form').serialize() + "&universal=" + encodeURIComponent(universal);
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        toastr['success']("<?php echo lang('add'); ?>", "<?php echo lang('Activity');?>: " + name + " <?php echo lang('added'); ?>");
                        setTimeout(function () {
                            $('#activitiesmodal').modal('hide');
                            find(data);
                            $('#dynamic-table').DataTable().ajax.reload();
                        }, 500);
                    }
                });
            } else {
                url = base_url + "panel/settings/activities/edit";
                dataString = $('#activities_form').serialize() + "&universal=" + encodeURIComponent(universal) + "&id=" + encodeURI(id);
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        toastr['success']("<?php echo lang('save'); ?>", "<?php echo lang('Activity');?>: " + name + "<?php echo lang('updated'); ?>");
                        setTimeout(function () {
                            $('#activitiesmodal').modal('hide');
                            find(id);
                            $('#dynamic-table').DataTable().ajax.reload();
                        }, 500);
                    }
                });
            }
        }
        return false;
    });


jQuery(document).ready( function($) {

    $( "#sub_id" ).select2({        
        ajax: {
            placeholder: 'Select a Activity',
            url: "<?php echo base_url(); ?>panel/settings/getActivitiesAjax",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term 
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
    });
});

</script>