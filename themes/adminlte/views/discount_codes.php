<script type="text/javascript">
    function actions(x) {
        var return_var = "<div class='btn-group'><button type=\"button\" class=\"btn btn-default btn-xs btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-expanded=\"true\">Actions <span class=\"caret\"></button><ul class=\"dropdown-menu pull-right\" role=\"menu\">";
        return_var += "<li><a id='delete' data-num='"+x+"'><i class='fas fa-trash'></i> Delete</a></li>";
        return_var += '</ul></div>';
        return return_var;
    }
    function humanize(text) {
        return text
        .replace(/_/g, ' ')
        .trim()
        .replace(/\b[A-Z][a-z]+\b/g, function(word) {
          return word.toLowerCase()
        })
        .replace(/^[a-z]/g, function(first) {
          return first.toUpperCase()
        })
    }
    function status(x) {
        if (x) {
            return '<span class="label label-info">'+"<?php echo lang('Used');?>"+'</span>';
        }else{
            return '<span class="label label-success">'+"<?php echo lang('Ready');?>"+'</span>';
        }
    }
    function type(x) {
        if (x == 'master') {
            return '<span class="label label-success">'+"<?php echo lang('Master');?>"+'</span>';
        }else if(x == 'category'){
            return '<span class="label label-info">'+"<?php echo lang('Category');?>"+'</span>';
        }else if(x == 'product'){
            return '<span class="label label-warning">'+"<?php echo lang('Product');?>"+'</span>';
        }else{
            return '<span class="label label-warning">'+x+'</span>';
        }
    }
    
    function used_for(x) {
        x = x.split('____');
        if (x[1] !== '') {
            return humanize(x[0]) + ' - '+x[1];
        }else if(x[0] == ''){
            return "<?php echo lang('All Products');?>";
        }
        return humanize(x[0]);
    }
    
    $(document).ready(function () {
        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[3, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo base_url(); ?>panel/settings/discount_codes/getAll/',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            }, 
            "aoColumns": [
                null,
                {mRender: type},
                {mRender: used_for},
                {mRender: fsd},
                null,
                null,
                {mRender: status},
                {mRender: actions},
            ],
        });
    });
</script>

<!-- ============= MODAL MODIFICA CLIENTI ============= -->
<div class="modal fade" id="discountmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="titclienti"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <p class="tips custip"></p>
                    <div class="row">
                        <form id="discount_form" class="parsley-form form-horizontal" method="post">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label"><?php echo lang('Discount Code');?></label>
                                        <div class="col-md-9">
                                            <div class="input-group">
                                                 <input id="code" name="code" type="text" class="validate form-control" required>
                                                <span class="input-group-addon" id="discount_code_gen" style="padding: 1px 10px;">
                                                    <i class="fas fa-random"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label"><?php echo lang('Code Type');?></label>
                                        <div class="col-md-9">
                                            <select class="form-control" required id="code_type" name="type">
                                                <option value="master"><?php echo lang('Master');?></option>
                                                <option value="category"><?php echo lang('Category');?></option>
                                                <option value="product"><?php echo lang('Product Code');?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="sort_with_div" style="display: none;">
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" id="sort_with_label" style="text-transform: capitalize;"><?php echo lang('Code Type');?></label>
                                            <div class="col-md-9">
                                                <select style="width: 100%;" class="form-control" name="used_for" id="second_d"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                </div>
            </div>
            <div class="modal-footer" id="footerClient1">
                  <!--    -->
            </div>
        </div>
    </div>
</div>
</div>

<button href="#discountmodal" class="add_discount btn btn-primary">
    <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo lang('Discount Codes');?>
</button>


<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('settings/discount_codes');?></h3>
      <div class="box-tools pull-right">
      </div>
  </div>
  <div class="box-body">
    <table class=" compact table table-bordered table-striped" id="dynamic-table" width="100%">
                <thead>
                    <tr>
                        <th><?php echo lang('Code');?></th>
                        <th><?php echo lang('Code Type');?></th>
                        <th><?php echo lang('Code For');?></th>
                        <th><?php echo lang('Used On');?></th>
                        <th><?php echo lang('Used By');?></th>
                        <th><?php echo lang('Sale ID');?></th>
                        <th><?php echo lang('Status');?></th>
                        <th><?php echo lang('actions'); ?></th>
                    </tr>
                </thead>
            </table>
  </div>
</div>
<script type="text/javascript">
    jQuery(document).on("click", "#delete", function () {
        var num = jQuery(this).data("num");
        bootbox.confirm({ 
              message: "<?php echo lang('action_cannot_be_undone');?>", 
              buttons: {
                confirm: {
                    label: "<?php echo lang('yes');?>",
                    className: 'btn-success'
                },
                cancel: {
                    label: "<?php echo lang('no');?>",
                    className: 'btn-danger'
                }
            },
              callback: function(result){ 
                if (result) {
                    jQuery.ajax({
                        type: "POST",
                        url: base_url + "panel/settings/discount_codes/delete",
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
                            toastr['success']("<?php echo lang('Discount Code Delete');?>");
                            $('#dynamic-table').DataTable().ajax.reload();
                        }
                    });
                } 
            }
        })
    });

    jQuery(document).on("change", "#code_type", function () {
        $('.sort_with_div').slideDown();
        $('#sort_with_label').html($(this).val());
        if ($(this).val() == 'master') {
            $('.sort_with_div').slideUp();
            $('.sort_with_div').disabled = true;
        }
    });

    $("#second_d").select2({
        placeholder: "<?php echo lang('please_select_code_type');?>",
        ajax: {
            url: '<?php echo base_url();?>panel/settings/discount_codes/json_sort',
            type: 'POST',
            data: function (params) {
                return {
                    id: $("#code_type").val(),
                    search: params.term,

                }
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.text,
                            id: item.id
                        }
                    })
                };
            }
        }
    });
    jQuery(".add_discount").on("click", function (e) {
        $('#discountmodal').modal('show');
        
        jQuery('#code').val('');
        jQuery('#code_type').val('master');
        jQuery('#second_d').val('').trigger('change');
        $('#discount_form').parsley().reset();
        jQuery('#titclienti').html("<?php echo lang('add'); ?> <?php echo lang('Discount Code');?>");
        jQuery('#footerClient1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button type="submit" class="btn btn-success" id="submit" data-mode="add" form="discount_form" value="Submit"><?php echo lang('submit');?></button>');
    });
  
    // process the form
    $('#discount_form').on( "submit", function(event) {
        event.preventDefault();
        var mode = jQuery('#submit').data("mode");
        var id = jQuery('#submit').data("num");
        form = $(this);
        var valid = form.parsley().validate();
        if (valid) {
            var url = "";
            var dataString = "";
            if (mode == "add") {
                url = base_url + "panel/settings/discount_codes/add";
                dataString = $('#discount_form').serialize();
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        console.log(data);
                        if (data.success) {
                            toastr['success']("<?php echo lang('add');?>", "<?php echo lang('Discount Code');?>: " + name + " <?php echo lang('added');?>");
                            setTimeout(function () {
                                $('#discountmodal').modal('hide');
                                $('#dynamic-table').DataTable().ajax.reload();
                            }, 500);
                        }else{
                            bootbox.alert(data.message);
                        }
                        
                    }
                });
            } else {
                url = base_url + "panel/settings/discount_codes/edit";
                dataString = $('#discount_form').serialize() + "&id=" + encodeURI(id);
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        data = JSON.parse(data);
                        if (data.success) {
                            toastr['success']("<?php echo lang('edit');?>", "<?php echo lang('Discount Code');?>: " + name + "<?php echo lang('updated');?>");
                            setTimeout(function () {
                                $('#discountmodal').modal('hide');
                                $('#dynamic-table').DataTable().ajax.reload();
                            }, 500);
                        }else{
                            bootbox.alert(data.message);
                        }
                    }
                });
            }
        }
        return false;
    });

    $('#discount_code_gen').on( "click", function(){
        $(this).parent('.input-group').children('input').val(generateCardNo(15));
    });

    jQuery(document).ready( function($) {
        $('.parsley-form').parsley();
    });		

</script>