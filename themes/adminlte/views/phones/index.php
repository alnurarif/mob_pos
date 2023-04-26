<?php
$v='?v=1';
if (isset($_GET['cat']) && is_numeric($_GET['cat'])) {
    $v .= '&cat_id='.$_GET['cat'];
}
if (isset($_GET['sub_id']) && is_numeric($_GET['sub_id'])) {
    $v .= '&sub_id='.$_GET['sub_id'];
}
?>
<script>

function discount(x) {
    var pqc = x.split("__");
    if(pqc[1] == 1){
      return formatDecimal(pqc[0])+'%';
    }else{
      return site.settings.currency+(pqc[0]);
    }
}
function actions_used(x) {
    var pqc = x.split("___");
    // if (pqc[2] == 1) {
    //     var button = "<li><a id='toggle' data-num='"+pqc[0]+"' data-mode='enable'><i class='fas fa-toggle-on'></i> "+lang.enable+"</a></li>";
    // }else{
    var button = "";
    <?php if(($this->Admin || ($GP['phones-delete_new'] && $type == 'new')) || ($this->Admin || ($GP['phones-delete_used'] && $type == 'used') )): ?>
        button += "<li><a id='delete_phone' data-num='"+pqc[0]+"'><i class='fas fa-trash'></i> <?=lang('delete');?></a></li>";
    <?php endif;?>
    // }

    var return_var = "<div class='btn-group'><button type=\"button\" class=\"btn btn-default btn-xs btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-expanded=\"true\"><?php echo lang('Actions');?> <span class=\"caret\"></button><ul class=\"dropdown-menu pull-right\" role=\"menu\">";
    return_var += "<li><a data-dismiss='modal' id='assign_sap_btn' href='#assign_sap' data-toggle='modal' data-num='"+pqc[0]+"'><i class='fas fa-plus-circle'></i> <?php echo lang('Assign Activation Plan');?></a></li>";

    <?php if(($this->Admin || ($GP['phones-edit_new'] && $type == 'new')) || ($this->Admin || ($GP['phones-edit_used'] && $type == 'used') )): ?>
    return_var += "<li><a href='<?php echo base_url('panel/phones/edit/');?>"+pqc[1]+"/"+pqc[0]+"'><i class='fas fa-edit'></i> <?php echo lang('Edit Phone');?></a></li>";
    <?php endif; ?>
    return_var += button;
    return_var += '</ul></div>';

    return return_var;
}


function actions(x) {
    var pqc = x.split("___");
    // if (pqc[2] == 1) {
    //     var button = "<li><a id='toggle' data-num='"+pqc[0]+"' data-mode='enable'><i class='fas fa-toggle-on'></i> "+lang.enabled+"</a></li>";
    // }else{

        <?php if(($this->Admin || ($GP['phones-delete_new'] && $type == 'new')) || ($this->Admin || ($GP['phones-delete_used'] && $type == 'used') )): ?>
            var button = "<li><a id='delete_phone' data-num='"+pqc[0]+"'><i class='fas fa-trash'></i> <?=lang('delete');?></a></li>";
        <?php endif;?>
    // }

    var return_var = "<div class='btn-group'><button type=\"button\" class=\"btn btn-default btn-xs btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-expanded=\"true\"><?php echo lang('Actions');?> <span class=\"caret\"></button><ul class=\"dropdown-menu pull-right\" role=\"menu\">";
        return_var += "<li><a href='<?php echo base_url('panel/pos_inventory/index/phones/');?>"+pqc[0]+"'><i class='fas fa-edit'></i> <?php echo lang('Manage Stock');?></a></li>";

    return_var += "<li><a data-dismiss='modal' id='assign_sap_btn' href='#assign_sap' data-toggle='modal' data-num='"+pqc[0]+"'><i class='fas fa-plus-circle'></i> <?php echo lang('Assign Activation Plan');?></a></li>";

    <?php if(($this->Admin || ($GP['phones-edit_new'] && $type == 'new')) || ($this->Admin || ($GP['phones-edit_used'] && $type == 'used') )): ?>
    return_var += "<li><a href='<?php echo base_url('panel/phones/edit/');?>"+pqc[1]+"/"+pqc[0]+"'><i class='fas fa-edit'></i> <?php echo lang('Edit Phone');?></a></li>";
    <?php endif; ?>
    return_var += button;
    return_var += '</ul></div>';

    return return_var;
}
function checkbox_q(x) {
    var pqc = x.split("__");
    if(pqc[1] == 1){
      return '<div class="text-center"><div class="checkbox-styled checkbox-inline"><input checked type="checkbox" onclick="quick_sale('+pqc[0]+',\'dynamic-table\');" id="qcheck'+pqc[0]+'"><label for="qcheck'+pqc[0]+'"></label></div></div>';
    }else{
      return '<div class="text-center"><div class="checkbox-styled checkbox-inline"><input type="checkbox" onclick="quick_sale('+pqc[0]+',\'dynamic-table\');" id="qcheck'+pqc[0]+'"><label for="qcheck'+pqc[0]+'"></label></div></div>';
    }
}

    <?php if($type == 'new'): ?>
        
        $(document).ready(function () {
        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[3, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo base_url(); ?>panel/phones/getAllPhones/<?php echo $type; ?>/<?php echo $type2.$v; ?>',
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
                null,
                null,
                {"bSortable": false, "mRender": currencyFormat}, 
                null,
                {"bSortable": false, "mRender": checkbox_q},
                {"bSortable": false, "mRender": actions},
            ],
        });
    });
    <?php endif; ?>
</script>

<?php if($type == 'used'): ?>
<?php
 if (isset($used_type)) {
    if (!($used_type == 'ready' or $used_type == 'repairs' or $used_type == 'hold')) {
        $used_type = NULL;
    }
 }
?>
<script>
        function used_status(x){
            if (x == 1) {
                return "<?php echo lang('Ready to Sale');?>";
            }else if (x == 2) {
                return "<?php echo lang('Needs Repair');?>";
            }else if (x == 3) {
                return "<?php echo lang('On Hold');?>";
            }else if (x == 4) {
                return "<?php echo lang('Sold');?>";
            }else if (x == 5) {
                return "<?php echo lang('Lost/Damaged');?>";
            }
        }
        function rating(x){
            var string = "";
            for (i = 0; i < x; i++) {
                string += "<i class='fas fa-star'></i>";
            }
            return string;
        }
        function unlocked(x){
            if (parseInt(x) == 1) {
                return "<?php echo lang("yes");?>";
            }else{
                return "<?php echo lang("no");?>";
            }
        }
        $(document).ready(function () {
        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[3, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo base_url(); ?>panel/phones/getAllPhones/<?php echo $type; ?>/<?php echo $type2.$v; ?>',
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
                null,
                {"bSortable": false, "mRender": currencyFormat}, 
                null,
                null,
                null,
                {mRender: used_status},
                {mRender: rating},
                {mRender: rating},
                {mRender: unlocked},
                {"bSortable": false, "mRender": checkbox_q},
                {mRender: unlocked},
                {"bSortable": false, "mRender": actions_used},
            ],
            "createdRow": function( row, data, dataIndex){
                x = data[11];

                if('<?php echo $type; ?>' == 'used' && parseInt(x) == 1) {
                    $(row).addClass('green');
                }
                    
               
            },

            
        });
    });
</script>
<style>
.green {
    background-color: lightgreen !important;    
    color: black;    
}
</style>
    <?php endif; ?>
<script>

    function quick_sale(id, tbl) {
        var val1;
        if ($("#qcheck" + id).is(':checked')) {
            val1 = '1';//
        } else {
            val1 = '0';
        }
        update_qs_value(val1, id, tbl);
    }
    function update_qs_value(val1, id, tbl) {
        var row_id = id;
        var ajaxurl = "<?php echo base_url("panel/phones"); ?>/update_qs";
        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {val1, row_id, tbl},
            dataType: "HTML",
            success: function (data) {
                toastr['success']("<?php echo lang('Done');?>");
            },
            error: function () {
                toastr['error']("<?php echo lang('Error');?>");
            }
        });
    }
    jQuery(document).on("click", "#delete_phone", function () {
        var num = jQuery(this).data("num");
        // var mode = jQuery(this).data("mode");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/phones/delete",
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
                // toastr['success']("<?php echo lang('Toggle');?>: ", data.toggle);
                toastr.success('<?=lang('deleted');?>')
                $('#dynamic-table').DataTable().ajax.reload();
            }
        });
    });
</script>

<!-- ============= MODAL MODIFICA CLIENTI ============= -->
<div class="modal fade" id="assign_sap" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="assign_sap_t"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <p class="tips custip"></p>
                    <div class="row">
                        <form id="assign_sap_form" class="parsley-form" method="post">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-12 col-lg-4 input-field">
                                        <div class="form-group">
                                            <label><?php echo lang('Special Activation Plan');?></label>
                                            <?php 
                                            $data = array(); 
                                            foreach ($saps as $sap) {
                                                $data[$sap['id']] = $sap['name'];
                                            }
                                            echo form_dropdown('sap', $data, "", 'class="form-control" id="phone_sap"'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="footer_Sap">
                
            </div>
        </div>
    </div>
</div>

<?php if(($this->Admin || $GP['phones-add_used']) && $type == 'used'): ?>
    <a href="<?php echo base_url('panel/phones/add/').$type; ?>" class="btn btn-primary">
        <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo ucfirst($type); ?> <?php echo lang('Phone');?>
    </a>
<?php endif; ?>

<?php if(($this->Admin || $GP['phones-add_new']) && $type == 'new'): ?>
    <a href="<?php echo base_url('panel/phones/add/').$type; ?>" class="btn btn-primary">
        <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo ucfirst($type); ?> <?php echo lang('Phone');?>
    </a>
<?php endif; ?>

<!-- Main content -->
<div class="clearfix"></div>
<?php echo form_open('panel/phones/misc_actions/'.$type, 'id="action-form"'); ?>

<div class="box box-primary ">
    <div class="box-header with-border">
        <?php if($type == 'new'): ?>
            <h3 class="box-title"><?php echo lang('New Phones');?></h3>

            <div class="box-tools pull-right">
                <div class="btn-group">
                   
                    <li class="btn btn-sm btn-default" style="list-style-type: none;">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <i class="icon fas fa-tasks tip" data-placement="left" title="<?php echo lang("actions") ?>"></i>
                        </a>
                        <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                            <li>
                                <a href="#" id="excel" data-action="export_excel">
                                    <i class="fas fa-file-excel"></i> <?php echo lang('export_to_excel') ?>
                                </a></li>
                            <li>
                                <a href="#" id="pdf" data-action="export_pdf">
                                    <i class="fas fa-file-pdf"></i> <?php echo lang('export_to_pdf') ?>
                                </a>
                            </li>
                        </ul>
                    </li>
                </div>
               <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-default"><?php echo lang('filter_by_category');?></button>
                    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span> <span class="sr-only"><?php echo lang('toggle_dropdown');?></span> </button>
                    <ul class="dropdown-menu" role="menu">
                        <?php if($cat_filter): ?>
                        <?php foreach ($cat_filter as $cat): ?>
                            <li>
                                <a href="<?php echo current_url(); ?>?cat=<?php echo $cat['id'];?>"><strong><?php echo escapeStr($cat['name']); ?></strong></a>
                            </li>
                            <?php if($cat['children']): ?>
                                <?php foreach ($cat['children'] as $child): ?>
                                    <li>
                                        <a href="<?php echo current_url(); ?>?cat=<?php echo $cat['id'];?>&sub_id=<?php echo $child['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo escapeStr($child['name']); ?></a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php else: ?>
                            <li>
                                <a href="#"><?php echo lang('no_categories_found');?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        <?php else: ?>
            <h3 class="box-title"><?php echo lang('Used Phones');?></h3>
            <div class="box-tools pull-right">
                <div class="btn-group">
                    
                    <a href="<?php echo base_url('panel/phones/view/used/ready'); ?>" class="btn btn-sm btn-primary">
                        <?php echo lang('Ready to Sale');?>
                    </a>
                    <a href="<?php echo base_url('panel/phones/view/used/repairs'); ?>" class="btn btn-sm btn-primary">
                        <?php echo lang('Needs Repair');?>
                    </a>
                    <a href="<?php echo base_url('panel/phones/view/used/hold'); ?>" class="btn btn-sm btn-primary">
                        <?php echo lang('On Hold');?>
                    </a>
                    <li class="btn btn-sm btn-default" style="list-style-type: none;">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <i class="icon fas fa-tasks tip" data-placement="left" title="<?php echo lang("actions") ?>"></i>
                        </a>
                        <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                            <li>
                                <a href="#" id="excel" data-action="export_excel">
                                    <i class="fas fa-file-excel"></i> <?php echo lang('export_to_excel') ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" id="pdf" data-action="export_pdf">
                                    <i class="fas fa-file-pdf"></i> <?php echo lang('export_to_pdf') ?>
                                </a>
                            </li>
                        </ul>
                    </li>
                </div>
               <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-default"><?php echo lang('filter_by_category');?></button>
                    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span> <span class="sr-only"><?php echo lang('toggle_dropdown');?></span> </button>
                    <ul class="dropdown-menu" role="menu">
                        <?php if($cat_filter): ?>
                        <?php foreach ($cat_filter as $cat): ?>
                            <li>
                                <a href="<?php echo current_url(); ?>?cat=<?php echo $cat['id'];?>"><strong><?php echo escapeStr($cat['name']); ?></strong></a>
                            </li>
                            <?php if($cat['children']): ?>
                                <?php foreach ($cat['children'] as $child): ?>
                                    <li>
                                        <a href="<?php echo current_url(); ?>?cat=<?php echo $cat['id'];?>&sub_id=<?php echo $child['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo escapeStr($child['name']); ?></a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php else: ?>
                            <li>
                                <a href="#"><?php echo lang('no_categories_found');?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

    </div>
    <div class="box-body">
         <div class="table-responsive">
            <?php if($type == 'new'): ?>
            <table class=" compact table table-bordered table-striped" id="dynamic-table" width="100%">
                <thead>
                    <tr>
                         <th style="min-width:30px; width: 30px; text-align: center;">
                            <input class="checkbox checkth" type="checkbox" name="check"/>
                        </th>
                        <th><?php echo lang('Phone Name');?></th>
                        <th><?php echo lang('Manufacturer');?></th>
                        <th><?php echo lang('Model');?></th>
                        <th><?php echo lang('Price');?></th>
                        <th><?php echo lang('Current Stock');?></th>
                        <th><?php echo lang('Quick Sale');?></th>
                        <th><?php echo lang('actions'); ?></th>
                    </tr>
                </thead>
        
            </table>
            <?php endif; ?>
            <?php if($type == 'used'): ?>
                <table class=" compact table table-bordered table-striped" id="dynamic-table" width="100%">
                    <thead>
                        <tr>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th><?php echo lang('Phone Name');?></th>
                            <th><?php echo lang('IMEI(s)');?></th>
                            <th><?php echo lang('Cost(s)');?></th>
                            <th><?php echo lang('Price(s)');?></th>
                            <th><?php echo lang('Manufacturer');?></th>
                            <th><?php echo lang('Model');?></th>
                            <th><?php echo lang('Status');?></th>
                            <th><?php echo lang('Cosmetic Condition');?></th>
                            <th><?php echo lang('Operational Condition');?></th>
                            <th><?php echo lang('Unlocked');?></th>
                            <th><?php echo lang('Quick Sale');?></th>
                            <th><?php echo lang('Sold');?></th>
                            <th><?php echo lang('actions'); ?></th>
                        </tr>
                    </thead>
            
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
<div style="display: none;">
    <input type="hidden" name="form_action" value="" id="form_action"/>
    <?php echo form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
</div>
<?php echo form_close() ?>





<script type="text/javascript">
    jQuery(document).on("click", "#assign_sap_btn", function () {
        jQuery('#assign_sap_t').html("<?php echo lang('Assign Special Activation Plan');?>");
        
        var num = jQuery(this).data("num");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/phones/assign_sap",
            data: "id=" + encodeURI(num) + "&token=<?php echo $_SESSION['token'];?>",
            cache: false,
            dataType: "json",
            success: function (data) {
                jQuery('#assign_sap_t').html("<?php echo lang('Assign Special Activation Plan');?> ");
                if (data.data.s_activation_plan) {
                    jQuery('#phone_sap').val(data.data.s_activation_plan);
                }
                jQuery('#footer_Sap').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button type="submit" id="submit" class="btn btn-success" form="assign_sap_form" value="Submit" data-num="' + encodeURI(num) + '"><i class="fas fa-save"></i><?php echo lang('submit');?></button>')
            }
        });
    });

    // process the form
    $('#assign_sap_form').on( "submit", function(event) {
        event.preventDefault();
        var mode = jQuery('#submit').data("mode");
        var id = jQuery('#submit').data("num");
        form = $(this);
        var valid = form.parsley().validate();
        if (valid) {
            url = base_url + "panel/phones/assign_sap_save";
            dataString = $('#assign_sap_form').serialize() + "&id=" + encodeURI(id);
            jQuery.ajax({
                type: "POST",
                url: url,
                data: dataString,
                cache: false,
                success: function (data) {
                    if (data.success) {
                        toastr['success']("<?php echo lang('Assigned Successfully');?>");
                        setTimeout(function () {
                            $('#assign_sap').modal('hide');
                            $('#dynamic-table').DataTable().ajax.reload();
                        }, 500);
                    }else{
                        bootbox.alert(data.message);
                    }
                }
            });
        }
        return false;
    });
</script>
