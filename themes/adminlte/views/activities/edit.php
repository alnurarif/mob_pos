<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('Activity');?></h3>
      <div class="box-tools pull-right">
      </div>
  </div>
  <div class="box-body">
    <?php echo validation_errors(); ?>
            <?php echo form_open('', 'name="plan"'); ?>
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                        <div class="form-group all">
                            <label><?php echo lang('Name');?></label>
                            <input type="text"  value="<?php echo escapeStr($activity->name); ?>" name="activity_name" class="form-control">
                        </div>
                        <table id='mup' class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th><?php echo lang('Name');?></th>
                                <th><?php echo lang('disable');?></th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 0;
                                   foreach ($sub_activities as $item):
                                ?>
                                    <tr>

                                    <input type="hidden" class="" name="sub_old[<?php echo $i;?>]" value="1">
                                    <input type="hidden" class="" name="sub_id[<?php echo $i;?>]" value="<?php echo $item->id; ?>">
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="activity_name form-control" name="name[<?php echo $i;?>]"
                                                   placeholder="<?php echo lang('Sub Activity Name');?>"
                                                   value="<?php echo escapeStr($item->name) ?>"/>
                                        </div>
                                    </td>
                                    <td><input type="hidden" class="" name="disable[<?php echo $i;?>]" value="0">
                                    <input type="checkbox" class="checkbox" name="disable[<?php echo $i;?>]" value="1" <?php echo ($item->disable == 1) ? 'checked' : ''?>></td>
                                </tr>
                                <?php
                                    $i++;
                                    endforeach;
                                ?>
                            </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">
                                    <a class="btn btn-default btn-xs" href="javascript:void(0);"
                                       id="addup">
                                        <i class="fas fa-plus"></i>
                                        <?php echo lang('Add More...');?>
                                    </a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="col-md-12 row">
                <?php echo form_submit('submit', lang('submit'), 'class="btn btn-primary"'); ?>
            </div>
        </div>
        <?php echo form_close(); ?>
  </div>
</div>

    
<script type="text/javascript">
    $("#addup").on( "click", function(){
        var all = (document.querySelectorAll(".activity_name").length);
        $.post("<?php echo base_url("panel/settings/addmore_activities");?>",
        {
            id: all,
        },
        function(data, status){
            $("#mup tbody").append(data);
        });
    });

    $( document ).ready(function() {
        $('.checkbox').iCheck({
            checkboxClass: 'icheckbox_minimal-grey',
            radioClass: 'iradio_minimal-grey'
        });
    });
</script>