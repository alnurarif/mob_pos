<script type="text/javascript">
    $(document).ready(function () {
        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[1, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo base_url(); ?>panel/pos_inventory/getStockData/<?php echo $type;?>/<?php echo $id;?>',
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
                <?php if($is_serialized): ?>
                null,
                <?php endif; ?>
            ],
           
        });
              
    });

    jQuery(document).on("click", "#delete", function () {
        var num = jQuery(this).data("num");
        $('#delete_form input[name=id]').val(num);
        $('#deletemodal').modal('show');
    });
   jQuery(document).on("submit", "#delete_form", function (event) {
        event.preventDefault();
        form = $(this);

        console.log(form.serialize());
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/pos_inventory/delete",
            data: form.serialize(),
            cache: false,
            dataType: "json",
            success: function (data) {
                toastr['success']("<?php echo lang('deleted'); ?>: ", "<?php echo lang('Stock Item Deleted');?>");
                $('#dynamic-table').DataTable().ajax.reload();

                $('#delete_form').trigger('reset');
                $('#deletemodal').modal('hide');
            }
        });
    });

</script>





<div class="row">
    <?php echo validation_errors(); ?>
    <div class="col-lg-4">
        <div class="box box-primary ">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo lang('Add Inventory');?> - <?=($record['name']);?></h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-lg-12">
                   <form role="form" method="post">
                        <div class="form-group">
                            <label><?php echo lang('Cost of Goods');?></label>
                            <input type="number" step="any" class="form-control" name="price_cost" placeholder="Cost of item(s)" value="<?php echo set_value('price_cost'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="quantity"><?php echo lang('Quantity');?></label>
                            <input type="number" max="99" class="form-control" name="quantity" placeholder="Quantity"  value="<?php echo ($is_serialized) ? 1 : set_value('quantity'); ?>" required <?php echo ($is_serialized) ? "readonly" : '' ?>>
                        </div>
                        <?php if($is_serialized){ ?>
                            <div class="form-group">
                                <label for="serial_number"><?php echo lang('Serial Number');?></label>
                                <input type="text" class="form-control" name="serial_number" placeholder="<?php echo lang('Serial Number');?>" value="<?php echo set_value('serial_number'); ?>" required>
                            </div>
                        <?php } ?>
                        <input type="submit" class="btn btn-sm btn-default" value="Add Inventory">
                        <a class="btn btn-danger btn-sm" href="javascript:window.history.go(-1);"><?php echo lang('go_back');?></a>
                    </form>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
          </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="box box-primary ">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo lang('Current Inventory');?></h3>
            </div>
            <div class="box-body">
                <div class="adv-table">
                        <table class=" compact table table-bordered table-striped" id="dynamic-table" width="100%">
                            <thead>
                                <tr>
                                    <?php if($is_serialized): ?>
                                        <th><?php echo lang('Serial Number');?></th>
                                    <?php endif; ?>
                                    <th><?php echo lang('Cost');?></th>
                                    <th><?php echo lang('Date');?></th>
                                    <th><?php echo lang('Action');?></th>
                                </tr>
                            </thead>
                            
                            <tfoot>
                                <tr>
                                    <?php if($is_serialized): ?>
                                        <th><?php echo lang('Serial Number');?></th>
                                    <?php endif; ?>
                                    <th><?php echo lang('Cost');?></th>
                                    <th><?php echo lang('Date');?></th>
                                    <th><?php echo lang('Action');?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
            </div>
        </div>
    </div>



<!-- ============= MODAL MODIFICA CLIENTI ============= -->
<div class="modal fade" id="othermodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="titclienti"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <p class="tips custip"></p>
                    <div class="row">
                        <form id="modify_price" method="post">
                            <div class="col-md-12 col-lg-12 input-field">
                                <div class="form-group">
                                    <label><?php echo lang('Price');?></label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-money"></i>
                                        </div>
                                        <input id="it_price" name="it_price" type="number" class="validate form-control" required>
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

<script type="text/javascript">
     
       jQuery(document).on("click", "#modify", function () {
        jQuery('#titclienti').html('<?php echo lang('edit'); ?> <?php echo lang('Price');?>');
        
        var num = jQuery(this).data("num");
            jQuery.ajax({
                type: "POST",
                url: base_url + "panel/pos_inventory/getProductByID",
                data: "id=" + encodeURI(num) + "&token=<?php echo $_SESSION['token'];?>",
                cache: false,
                dataType: "json",
                success: function (data) {
                    jQuery('#titclienti').html("<?php echo lang('edit'); ?> <?php echo lang('Price');?>");
                    jQuery('#it_price').val(data.price);
                  
                    jQuery('#footerClient1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button type="submit" id="submit" class="btn btn-success" data-mode="modify" form="modify_price" value="Submit" data-num="' + encodeURI(num) + '"><i class="fas fa-save"></i> <?php echo lang('Submit');?></button>')

                }
            });
        });
        // process the form
        $('#modify_price').on( "submit", function(event) {
            event.preventDefault();
            var mode = jQuery('#submit').data("mode");
            var id = jQuery('#submit').data("num");
            console.log(mode);
            console.log(id);
            //validate
            var valid = true;
            if (valid) {
                var url = "";
                var dataString = "";
                url = base_url + "panel/pos_inventory/edit";
                dataString = $('form').serialize() + "&id=" + encodeURI(id);
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        toastr['success']("<?php echo lang('edit');?>",  name + "<?php echo lang('updated');?>");
                        setTimeout(function () {
                            $('#othermodal').modal('hide');
                            $('#dynamic-table').DataTable().ajax.reload();
                        }, 500);
                    }
                });
            }
            return false;
        });

</script>



<!-- ============= MODAL MODIFY CLIENTI ============= -->
<div class="modal fade" id="deletemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=lang('delete_stock_item');?></h4>
            </div>
            <div class="modal-body">
                <form id="delete_form" data-parsley-validate>
                    <input type="hidden" value="" name="id" />
                    <div class="form-group">
                        <label><?php echo lang('reason');?><font color="#FF0017"> *</font></label>
                        <textarea name="reason" required class="form-control"></textarea>
                    </div>
                     <div class="form-group">
                        <label><?php echo lang('User');?></label><font color="#FF0017"> *</font>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fas  fa-folder"></i>
                            </div>
                            <select required name="user_id" data-num="1" class="form-control m-bot15" style="width: 100%">
                                <option selected disabled></option>
                                <?php
                                    foreach ($all_users as $user) :
                                    echo '<option value="'.$user->id.'">'.$user->first_name.' '.$user->last_name.'</option>';
                                    endforeach;
                                ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                  <button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fa fa-reply"></i> <?= lang("go_back"); ?></button>
                  <button role="button" form="delete_form" id="delete_submit" class="btn btn-success"><?= lang("delete"); ?></button>
            </div>
        </div>
    </div>
</div>
