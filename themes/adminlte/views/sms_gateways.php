<script>
    $(document).ready(function () {
        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[0, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?php echo escapeStr($settings->rows_per_page); ?>,
            'bProcessing': true, 'bServerSide': false,
            'sAjaxSource': '<?php echo base_url(); ?>panel/settings/getSMSGateways',
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
            ],
           
        });
    });


    jQuery(document).on("click", "#add_additional_field", function (e) {
        e.preventDefault();
        html = '<tr> \
                <td><input type="text" class="form-control" placeholder="'+"<?php echo lang('parameter_name');?>"+'" name="postdata[name][]"></td> \
                <td><input type="text" class="form-control" placeholder="'+"<?php echo lang('parameter_value');?>"+'" name="postdata[value][]"></td> \
                <td><i id="remove_field" class="fas fa-times"></i></td> \
            </tr>'
        $('#additional_post_data tbody').append(html);
    });



    jQuery(document).on("click", "#remove_field", function (e) {
        e.preventDefault();
        $(this).parent().parent().remove();
    });



    jQuery(document).on("click", "#delete", function () {
        var num = jQuery(this).data("num");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/settings/delete_smsgateway",
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
                toastr['success']("<?php echo lang('deleted');?>: ", "<?php echo lang('smsgateway_deleted'); ?>");
                $('#dynamic-table').DataTable().ajax.reload();
            }
        });
    });
</script>

<!-- ============= MODAL MODIFICA supplierI ============= -->
<div class="modal fade" id="smsgatewaymodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                        <form class="col s12" id="smsgateway_form">
                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <?php echo lang('smsgateway_name', 'smsgateway_name'); ?>
                                    <input id="smsgateway_name" name="name" type="text" class="validate form-control" required>
                                </div>
                            </div>

                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <?php echo lang('smsgateway_url', 'smsgateway_url'); ?>
                                        <input id="smsgateway_url" name="url" type="text" class="validate form-control" required>
                                </div>
                            </div>

                            <div class="col-md-12 col-lg-4 input-field">
                                <div class="form-group">
                                    <?php echo lang('smsgateway_toname', 'smsgateway_toname'); ?>
                                        <input id="smsgateway_toname" name="to_name" type="text" class="validate form-control" required>
                                </div>
                            </div>

                          <!--   <div class="col-md-12 col-lg-4 input-field">
                                <div class="form-group">
                                    <?php echo lang('smsgateway_fromname', 'smsgateway_fromname'); ?>
                                    <input id="smsgateway_fromname" name="from_name" type="text" class="validate form-control" required>
                                </div>
                            </div> -->

                            <div class="col-md-12 col-lg-4 input-field">
                                <div class="form-group">
                                    <?php echo lang('smsgateway_messagename', 'smsgateway_messagename'); ?>
                                    <input id="smsgateway_messagename" name="message_name" type="text" class="validate form-control" required>
                                </div>
                            </div>

                            <div class="col-xs-12 input-field">
                                <h2><?php echo lang('additional_post_data');?></h2>
                                <table id="additional_post_data" class="table">
                                    <thead>
                                        <tr>
                                            <th class="col-md-5"><?php echo lang('parameter_name');?></th>
                                            <th class="col-md-6"><?php echo lang('parameter_value');?></th>
                                            <th class="col-md-1"><i class="fas fa-times"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" placeholder="<?php echo lang('parameter_name');?>" class="form-control" name="postdata[name][]"></td>
                                            <td><input type="text" placeholder="<?php echo lang('parameter_value');?>" class="form-control" name="postdata[value][]"></td>
                                            <td><i id="remove_field" class="fas fa-times"></i></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <button class="btn btn-default" id="add_additional_field"><?php echo lang('add_additional_field');?></button>
                            </div>

                            <div class="col-md-12 col-lg-12 input-field">
                                <div class="form-group">
                                    <?php echo lang('smsgateway_notes', 'smsgateway_notes'); ?>
                                    <textarea class="form-control" id="smsgateway_notes" name="notes"></textarea>
                                </div>
                            </div>
                        </form>
                </div>
            </div>
            <div class="modal-footer" id="footersmsgateway">
                  <!--    -->
            </div>
        </div>
    </div>
</div>
</div>

<button href="#smsgatewaymodal" class="add_smsgateway btn btn-primary">
    <i class="fas fa-plus-circle"></i> <?php echo lang('add').' '.lang('sms_gateway'); ?>
</button>

<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('settings/sms_gateways');?></h3>
      <div class="box-tools pull-right">
      </div>
  </div>
  <div class="box-body">
     <table class="display compact table table-bordered table-striped" id="dynamic-table">
                    <thead>
                        <tr>
                            <th><?php echo lang('name'); ?></th>
                            <th><?php echo lang('message'); ?></th>
                            <th><?php echo lang('actions'); ?></th>
                        </tr>
                    </thead>
            
                    <tfoot>
                        <tr>
                            <th><?php echo lang('name'); ?></th>
                            <th><?php echo lang('message'); ?></th>
                            <th><?php echo lang('actions'); ?></th>
                        </tr>
                    </tfoot>
                </table>
  </div>
</div>

<script type="text/javascript">


    jQuery(".add_smsgateway").on("click", function (e) {
        $('#smsgatewaymodal').modal('show');
        $('#smsgateway_form').trigger("reset");
        $('#smsgateway_form').parsley().reset();
        
        $('#additional_post_data tbody').empty();
        html = '<tr> \
            <td><input type="text" class="form-control" placeholder="'+"<?php echo lang('parameter_name');?>"+'" name="postdata[name][]"></td> \
            <td><input type="text" class="form-control" placeholder="'+"<?php echo lang('parameter_value');?>"+'" name="postdata[value][]"></td> \
            <td><i id="remove_field" class="fas fa-times"></i></td> \
        </tr>'
        $('#additional_post_data tbody').append(html);


        jQuery('#titsupplieri').html("<?php echo lang('add').' '.lang('smsgateway_title'); ?>");

        jQuery('#footersmsgateway').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i><?php echo lang("go_back"); ?></button><button id="submit" class="btn btn-success" role="submit" form="smsgateway_form" data-mode="add"><i class="fas fa-user"></i> <?php echo lang("add"); ?></button>');
    });

    jQuery(document).on("click", "#modify", function () {
        jQuery('#titsupplieri').html('Edit smsgateway Rate');
        $('#smsgateway_form').trigger("reset");
        $('#smsgateway_form').parsley().reset();
        var num = $(this).data('num');
            jQuery.ajax({
                type: "POST",
                url: base_url + "panel/settings/get_smsgateway_id",
                data: "id=" + encodeURI(num),
                cache: false,
                dataType: "json",
                success: function (data) {
                    data = data.data;
                    $('#smsgateway_name').val(data.name)
                    $('#smsgateway_url').val(data.url)
                    $('#smsgateway_toname').val(data.to_name)
                    $('#smsgateway_messagename').val(data.message_name)
                    $('#smsgateway_notes').val(data.notes)


                    var IS_JSON = true;
                    try {
                        var json = $.parseJSON(data.postdata);
                    } catch(err) {
                        IS_JSON = false;
                    }                

                    if(IS_JSON)  {
                        $('#additional_post_data tbody').empty();
                        $.each(json, function(key, value){
                            html = '<tr> \
                                <td><input type="text" class="form-control" value="'+key+'" placeholder="'+"<?php echo lang('parameter_name');?>"+'" name="postdata[name][]"></td> \
                                <td><input type="text" class="form-control" value="'+value+'" placeholder="'+"<?php echo lang('parameter_value');?>"+'" name="postdata[value][]"></td> \
                                <td><i id="remove_field" class="fas fa-times"></i></td> \
                            </tr>'
                            $('#additional_post_data tbody').append(html);
                        });
                    }

                    jQuery('#footersmsgateway').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button id="submit" class="btn btn-success" data-mode="modify" role="submit" form="smsgateway_form" data-num="' + encodeURI(num) + '"><i class="fas fa-save"></i> <?php echo lang("save"); ?></button>')
                    
                    console.log(data);
                }
            });
        });


$(function () {
    $('#smsgateway_form').parsley({
        errorsContainer: function(pEle) {
            var $err = pEle.$element.closest('.form-group');
            return $err;
        }
    }).on('form:submit', function(event) {
        var mode = jQuery('#submit').data("mode");
        var id = jQuery('#submit').data("num");

        var name = sanitizer.sanitize(jQuery('#smsgateway_name').val());
        var url = "";
        var dataString = $('#smsgateway_form').serialize();

        if (mode == "add") {
            url = base_url + "panel/settings/add_smsgateway";
            jQuery.ajax({
                type: "POST",
                url: url,
                data: dataString,
                cache: false,
                success: function (data) {
                    toastr['success']("<?php echo lang('add'); ?>", "<?php echo lang('smsgateway_title'); ?>: " + name + " <?php echo lang('added'); ?>");
                    setTimeout(function () {
                        $('#smsgatewaymodal').modal('hide');
                        find(data);
                        $('#dynamic-table').DataTable().ajax.reload();
                    }, 500);
                }
            });
        } else {
            url = base_url + "panel/settings/edit_smsgateway";
            dataString =  dataString + "&id=" + encodeURI(id);
            jQuery.ajax({
                type: "POST",
                url: url,
                data: dataString,
                cache: false,
                success: function (data) {
                    toastr['success']("<?php echo lang('save'); ?>", "<?php echo lang('smsgateway_title'); ?>: " + name + "<?php echo lang('updated'); ?>");
                    setTimeout(function () {
                        $('#smsgatewaymodal').modal('hide');
                        find(id);
                        $('#dynamic-table').DataTable().ajax.reload();
                    }, 500);
                }
            });
        }
        return false;
    });
});
    
</script>