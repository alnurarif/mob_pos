<script>
    $(document).ready(function () {
        $('body').on('click','#submit-update',function(){
            form = $(this);
            var id = $('#repair_id_edit').val();
            let uid = <?= $this->session->userdata('user_id') ?>;
    
                    var url = base_url + "panel/repair/update_status";
                    var dataString = $('#rpair_status_update_form').serialize()+ '&uid=' + uid;
                    // var dataString = $('#rpair_status_update_form').serialize() + '&status=' + 1;
                    // newWindow = window.open("", "_blank");
    
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: dataString,
                        cache: false,
                        success: function (data) {
                            result = (JSON.parse(data));
                            
                            setTimeout(function () {
                                if (result.success) {
                                    if ($.fn.DataTable.isDataTable('#dynamic-table') ) {
                                        $('#dynamic-table').DataTable().ajax.reload();
                                    }
                                    toastr['success']("<?php echo lang('Updated Succesfully'); ?>");
                                    $('#myModal').modal('hide');
                                }
                            }, 500);
                        }
                    });
                
            return false;
        });
        $('#rpair_status_update_form').on("submit", function(event) {
        });
    });
</script>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
        </button>
        <h4 class="modal-title" id="myModalLabel"><strong><?php echo lang('status'); ?></strong></h4>
    </div>
    <div class="modal-body">
        <p><?= lang('enter_info'); ?></p>
        <form id="rpair_status_update_form" method="post">
            <input type="hidden" name="repair_id_edit" id="repair_id_edit" value="<?= $repair['id']; ?>">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><?= lang('Details'); ?></strong>
                </div>
                <div class="panel-body">
                    <table class="table table-condensed table-striped table-borderless" style="margin-bottom:0;">
                        <tbody>
                        <tr>
                            <td><?= lang('ref'); ?></td>
                            <td><?= $repair['reference_no']; ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('customer'); ?></td>
                            <td><?= $repair['name']; ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('status'); ?></td>
                            <td><strong><?= $repair['status_label']; ?></strong></td>
                        </tr>
                        <tr>
                            <td><?= lang('payment_status'); ?></td>
                            <td id="payment_status"><?= lang($repair['payment_status']); ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('assigned_to'); ?></td>
                            <td><?= $repair['assigned_to_name']; ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if($repair['status_label'] != 'Delivered'){ ?>
            <div class="form-group">
                    
                <strong><?= lang('status'); ?></strong>
                <select name="status" class="form-control" id="status" required="required" style="width:100%;">
                    <?php foreach($statuses as $repair_status){ ?>
                    <option value="<?= $repair_status->id; ?>" <?php echo ($repair_status->id == $repair['status_id']) ? 'selected': ''; ?>><?= $repair_status->label; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <strong><?= lang('note'); ?></strong>
                <textarea 
                name="repair_note" 
                cols="40" 
                rows="3" 
                class="form-control" 
                id="repair_note" 
                required="required" 
                data-bv-field="repair_note" 
                dir="ltr"></textarea>
            </div>
            <?php } ?>
            
            <div class="modal-footer">
                <?php if($repair['status_label'] != 'Delivered'){ ?>
                    <input type="button" value="Update" id="submit-update" class="btn btn-primary">
                <?php } ?>
            </div>
        </form>
    </div>
</div>
