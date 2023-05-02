
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
        </button>
        <h4 class="modal-title" id="myModalLabel"><strong><?php echo lang('Repair History'); ?></strong></h4>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="compact table table-bordered table-striped" width="100%">
                    <thead>
                        <tr>
                            
                            <th><?php echo lang('update_at'); ?></th>
                            <th><?php echo lang('ref'); ?></th>
                            <th><?php echo lang('Customer'); ?></th>
                            <th><?php echo lang('Status'); ?></th>
                            <th><?php echo lang('Comment'); ?></th>
                            <th><?php echo lang('updated_by'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($repair_history_data) > 0){ ?> 
                            <?php foreach($repair_history_data as $single_history){ ?> 
                                <tr>
                                    <td><?=  $single_history->created_at; ?></td>
                                    <td><?= $repair['reference_no']; ?></td>
                                    <td><?= $repair['name']; ?></td>
                                    <td><?= $single_history->status_name; ?></td>
                                    <td><?= $single_history->repair_note; ?></td>
                                    <td><?= $single_history->created_by_name; ?></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>