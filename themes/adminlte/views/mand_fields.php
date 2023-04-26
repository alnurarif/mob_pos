    <?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
    .table td:first-child {
        font-weight: bold;
    }

    label {
        margin-right: 10px;
    }
    
</style>

<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('settings/mandatory_fields');?></h3>
      <div class="box-tools pull-right">
      </div>
  </div>
  <div class="box-body">
    <?php
                    echo form_open("panel/settings/mandatory_fields/"); 
                ?>
                <div class="table-responsive">
                    <table id="mand_fields" class="table table-bordered table-hover table-striped">

                        <thead>
                        <tr>
                            <th class="text-center"><?php echo lang('Form');?></th>
                            <th class="text-center"><?php echo lang('Field');?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mand_fields as $form => $fields): ?>
                                <tr>
                                    <td><?php echo lang($form) ? lang($form) : (lang(humanize($form)) ? lang(humanize($form)) : humanize($form)); ?></td>
                                    <td>
                                        <?php foreach ($fields as $name => $required): ?>
                                            <input type="hidden" value="0" class="checkbox" name="<?php echo $form.'___'.$name; ?>">
                                            <input type="checkbox" value="1" id="<?php echo $form.$name; ?>" class="checkbox" name="<?php echo $form.'___'.$name; ?>" <?php echo $required ? "checked" : ''; ?>>
                                            <label for="<?php echo $form.$name; ?>" class="padding05"><?php echo lang($name) ? lang($name) : (lang(humanize($name)) ? lang(humanize($name)) : humanize($name)); ?></label>
                                        <?php endforeach; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><?php echo lang('update')?></button>
                </div>
                <?php echo form_close(); ?>
  </div>
</div>

<script src="<?php echo base_url(); ?>assets/plugins/floatThead/jquery.floatThead.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var $table = $('#mand_fields');
        $table.floatThead({
            top:55,
            responsiveContainer: function($table){
                return $table.closest('.table-responsive');
            },
        });
    });
</script>
<script type="text/javascript">
$(document).ready(function(){
    $('input[type=checkbox]').iCheck({
        checkboxClass: 'icheckbox_flat',
        radioClass: 'iradio_flat'
    });
});

</script>