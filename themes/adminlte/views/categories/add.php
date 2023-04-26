<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('Category');?></h3>
      <div class="box-tools pull-right">
      </div>
  </div>
  <div class="box-body">
    <?php echo validation_errors(); ?>
        <?php echo form_open(); ?>
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                        <div class="form-group all">
                            <label><?php echo lang('Name');?></label>
                            <input type="text" name="category_name" class="form-control">
                        </div>
                        
                        <table id='mup' class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th><?php echo lang('Name');?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($this->input->post('name')): ?>
                                    <?php 
                                    $i = sizeof($_POST['name']);
                                    for ($r = 0; $r < $i; $r++) {
                                        $name = $_POST['name'][$r];
                                    ?>
                                     <tr>
                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="name[]"
                                                       placeholder="<?php echo lang('Sub Category Name');?>"
                                                       value="<?php echo escapeStr($name); ?>"/>
                                            </div>
                                        </td>
                                        <input type="hidden" name="disable[]" value="0">
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                <?php else: ?>
                                     <tr>
                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="name[]"
                                                       placeholder="<?php echo lang('Sub Category Name');?>"
                                                       value=""/>
                                            </div>
                                        </td>
                                        <input type="hidden" name="disable[]" value="0">
                                   </tr>
                                <?php endif; ?>
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
  $("#addup").on("click", function () {
        var url = "<?php echo base_url("panel/settings/addmore_categories");?>";
        $.get(url, {}, function (data) {
            $("#mup tbody").append(data);
        });
    });
</script>

