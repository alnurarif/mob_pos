<button class="btn-icon btn btn-primary btn-icon add_bank"><i class="fa fa-plus-circle"></i> <?php echo lang('Bank Account');?></button>

<div class="box">
    <div class="box-header">
    <h3 class="box-title"><?php echo lang('Bank Account');?></h3>
    </div>
    <div class="box-body">

        <div class="table-responsive">
           <table style="width: 100%" class="display compact table table-bordered table-striped" id="bankaccounts-table">
                <thead>
                    <tr>
                        <th><?php echo lang('name');?></th>
                        <th><?php echo lang('Notes');?></th>
                        <th><?php echo lang('Opening Amount');?></th>
                        <th><?php echo lang('Total');?></th>
                        <th style="width: 90px !important"><?php echo lang('actions');?></th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th><?php echo lang('name');?></th>
                        <th><?php echo lang('Notes');?></th>
                        <th></th>
                        <th></th>
                        <th style="width: 90px !important"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
</div>


<script type="text/javascript">
$(document).ready(function () {

    oTable = $('#bankaccounts-table').dataTable({
      autoWidth: false,
        "aaSorting": [[1, "asc"]],
        "aLengthMenu": [[10, 15, 20, 25, 50, 100, -1], [10, 15, 20, 25, 50, 100, "All"]],
        "iDisplayLength": <?php echo escapeStr($settings->rows_per_page); ?>,
        'bProcessing': true, 'bServerSide': true,
        'sAjaxSource': '<?php echo site_url('panel/accounts/getAllBankAccounts/'); ?>',
        'fnServerData': function (sSource, aoData, fnCallback) {
            aoData.push({
                "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                "value": "<?php echo $this->security->get_csrf_hash() ?>"
            });
            $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
        },
        "aoColumns": [
            {"bSortable": false},
            null,
            {"mRender": currencyFormat},
            null,
            null,
        ],

        "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
            var total = 0; var total1 = 0; var total2 = 0;
            for (var i = 0; i < aaData.length; i++) {
                total   +=  parseFloat(aaData[aiDisplay[i]]['2']);
                total1   +=  parseFloat(aaData[aiDisplay[i]]['3']);
            }
            var nCells = nRow.getElementsByTagName('th');
            nCells[2].innerHTML = currencyFormat(total);
            nCells[3].innerHTML = currencyFormat(total1);
        }
    });
});



// 
// 
 jQuery(document).on("click", "#delete_bank", function () {
        var num = jQuery(this).data("num");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/accounts/delete_bank",
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
                if (data.success) {
                    toastr['success'](data.msg);
                }else{
                    toastr['warning'](data.msg);
                }
                $('#bankaccounts-table').DataTable().ajax.reload();

            }
        });
    });

    jQuery(document).on("click", ".add_bank", function (e) {
        $('#bankmodal').modal('show');
        $('#bank_form').trigger("reset");
        $('#bank_form').parsley().reset();
        jQuery('#bankmodalheader').html("<?php echo lang('Add Bank Account');?>");
        jQuery('#bankfooter').html('<button data-dismiss="modal" class="btn-icon btn btn-goback" type="button"><i class="fa fa-reply img-circle text-muted"></i> <?php echo lang("go_back") ?></button><button id="submit_bank" role="button" form="bank_form" class="btn-icon btn btn-success" data-mode="add"><i class="fa fa-plus img-circle text-success"></i> <?php echo lang("add"); ?></button>');
    });

    jQuery(document).on("click", "#edit_bank", function () {
        $('#bank_form')[0].reset();
        $('#bank_form').find("select").val("").trigger('change');
        var num = $(this).data('num');
        jQuery('#fundmodalheader').html("<?php echo lang('Edit Bank Account');?>");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/accounts/getBankByID",
            data: "id=" + encodeURI(num) + "&token=<?php echo $_SESSION['token'];?>",
            cache: false,
            dataType: "json",
            success: function (data) {
                jQuery('#bank_name').val(data.name);
                jQuery('#bank_title').val(data.title);
                jQuery('#bank_description').val(data.description);
                jQuery('#bank_account_number').val(data.account_number);
                jQuery('#bank_contact_person').val(data.contact_person);
                jQuery('#bank_phone').val(data.phone);
                jQuery('#opening_balance').val(data.opening_balance);

                jQuery('#bankfooter').html('<button data-dismiss="modal" class="btn-icon btn btn-goback" type="button"><i class="fa fa-reply img-circle text-muted"></i> <?php echo lang("go_back") ?></button><button role="button" id="submit_bank" form="bank_form" class="btn-icon btn btn-success" data-mode="modify" data-num="' + encodeURI(num) + '"><i class="fa fa-plus img-circle text-success"></i> <?php echo lang("save"); ?></button>');
            }
        });
    });


     $(function () {
      $('#bank_form').parsley({

        successClass: "has-success",
        errorClass: "has-error",
        classHandler: function (el) {
            return el.$element.closest(".form-group");
        },
        errorsContainer: function (el) {
            return el.$element.closest(".form-group");
        },
        errorsWrapper: "<span class='help-block'></span>",
        errorTemplate: "<span></span>"
      }).on('form:submit', function(event) {
        var mode = jQuery('#submit_bank').data("mode");
        var id = jQuery('#submit_bank').data("num");
        var url = "";
        var dataString = new FormData($('#bank_form')[0]);
        if (mode == "add") {
            url = base_url + "panel/accounts/add_bank";
            $.ajax({
                url: url,
                type: "POST",
                data:  dataString,
                contentType:false,
                cache: false,
                processData:false,
                success: function (result) {
                    if (result.success) {
                        toastr['success']("<?php echo lang('bank account successfully added');?>");
                        setTimeout(function () {
                            $('#bankmodal').modal('hide');
                            $('#bankaccounts-table').DataTable().ajax.reload();
                        }, 500);
                    }else{
                        toastr['error'](result.error);
                    }
                }
            });
        } else {
            url = base_url + "panel/accounts/edit_bank";
            dataString.append('id',id);
            $.ajax({
                url: url,
                type: "POST",
                data:  dataString,
                contentType:false,
                cache: false,
                processData:false,
                success: function (result) {

                    if (result.success) {
                        toastr['success']("<?php echo lang('bank account successfully edited');?>");
                        setTimeout(function () {
                            $('#bankmodal').modal('hide');
                            $('#bankaccounts-table').DataTable().ajax.reload();
                        }, 500);
                    }else{
                        toastr['error'](result.error);
                    }
                    
                }
            });
        }
        return false;
    });
    });

</script>



<!-- Model Add -->
<div class="modal fade" id="bankmodal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="bankmodalheader"></h4>
            </div>
            <div class="modal-body">
                <form id="bank_form" class="col s12">
                    <div class="row">
                        <div class="col-md-12 input-field">
                            <div class="form-group">
                                <label><?php echo lang('Title');?></label>
                                <input type="text" name="title" id="bank_title" class="form-control">
                            </div>
                        </div>

                         <div class="col-md-12 input-field">
                            <div class="form-group">
                                <label><?php echo lang('Description');?></label>
                                <textarea name="description" class="form-control" id="bank_description"></textarea>
                            </div>
                        </div>

                        <div class="col-md-12 input-field">
                            <div class="form-group">
                                <label><?php echo lang('Account Number');?></label>
                                <input type="text" name="account_number" id="bank_account_number" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12 input-field">
                            <div class="form-group">
                                <label><?php echo lang('Contact Person');?></label>
                                <input type="text" name="contact_person" id="bank_contact_person" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12 input-field">
                            <div class="form-group">
                                <label><?php echo lang('Phone');?></label>
                                <input type="text" name="phone" id="bank_phone" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-12 input-field">
                            <div class="form-group">
                                <label><?php echo lang('Opening Balance');?></label>
                                <input type="text" name="opening_balance" id="opening_balance" class="form-control">
                            </div>
                        </div>

                       
                    </div>
                </form>
            </div>
            <div class="modal-footer" id="bankfooter">
                  <!--    -->
            </div>
        </div>
    </div>
</div>