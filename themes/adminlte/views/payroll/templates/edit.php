    <div class="box box-primary">
        <?php echo form_open('panel/payroll/edit_template/'. $template->id); ?>
        <div class="box-body">
            <p><?php echo lang('you can edit the template by changing the fields and adding or deleting rows.');?></p>

            <div class="row">
                <div class="col-md-6">
                    <div class="box box-solid box-primary">
                        <div class="box-header">
                            <h3 class="box-title"><?php echo lang('Additions');?></h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                
                            <?php foreach($bottom_left as $key): ?>
                                <div class="col-md-6">
                                    <div class="form-group" id="<?php echo $key->id;?>">
                                        <div class="input-group">
                                            <input type="text" name="<?php echo $key->id;?>" value="<?php echo escapeStr($key->name);?>" class="form-control">
                                            <?php if($key->is_default==0): ?>
                                                <div class="input-group-addon">
                                                    <a href="<?php echo base_url();?>panel/payroll/delete_template_meta/<?php echo $key->id;?>"
                                                   class="deleteMeta text-danger" div-id="<?php echo $key->id;?>"><i class="fa fa-trash"></i> </a>
                                                </div>
                                            <?php endif;?>
                                        </div>
                                    </div>
                                </div>

                                
                            <?php endforeach; ?>

                            </div>

                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary margin btn-icon  pull-right" data-toggle="modal" data-target="#addRow" data-position="bottom_left"><i class="fa fa-plus img-circle text-info"></i> <?php echo lang('Add');?></button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="box box-solid box-danger">
                        <div class="box-header">
                            <h3 class="box-title"><?php echo lang('Deductions');?></h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                            <?php foreach($bottom_right as $key): ?>
                                <div class="col-md-6">
                                    <div class="form-group" id="<?php echo $key->id;?>">
                                        <div class="input-group">
                                        <input type="text" name="<?php echo $key->id;?>" value="<?php echo escapeStr($key->name);?>" class="form-control">
                                            <?php if($key->is_default==0): ?>
                                                <div class="input-group-addon">
                                                    <a href="<?php echo base_url();?>panel/payroll/delete_template_meta/<?php echo $key->id;?>"
                                                   class="deleteMeta text-danger" div-id="<?php echo $key->id;?>"><i class="fa fa-trash"></i> </a>
                                                </div>
                                            <?php endif;?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            </div>

                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="form-group">
                                    <button type="button" class="btn btn-danger margin btn-icon pull-right" data-toggle="modal" data-target="#addRow" data-position="bottom_right"><i class="fa fa-plus img-circle text-danger"></i> <?php echo lang('Add');?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer" style="text-align: right;">
            <button type="submit" class="btn btn-default  btn-icon "><i class="fa fa-reply img-circle text-muted"></i><?php echo lang('Go Back');?></button>
            <a class="btn btn-primary btn-icon" href="<?php echo base_url('panel/payroll/change_logo/').$template->id ?>" data-toggle="modal" data-target="#myModal"><i class="fa fa-upload img-circle text-muted"></i> <?php echo lang('Upload Logo');?></a>
            <button type="submit" class="btn btn-success  btn-icon "><i class="fa fa-save img-circle text-success"></i> <?php echo lang('Save');?></button>
        </div>
        <?php echo form_close(); ?>
    </div>
    <!-- /.box -->
    <div class="modal fade" id="addRow">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">*</span></button>
                    <h4 class="modal-title"><?php echo lang('Add Row');?></h4>
                </div>
                <?php echo form_open('panel/payroll/add_template_row/'. $template->id); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" style="">
                            <input name="position" value="" type="hidden" id="position">

                            <div class="form-group">
                                <label><?php echo lang('Name');?></label>
                                <input type="text" name="name" id="amount" required="" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                        <a class="btn btn-goback btn-icon" href="<?php echo base_url();?>panel/payroll/templates"><i class="fa fa-reply img-circle text-muted"></i> <?php echo lang('Close');?></a>

                        <button type="submit" class="btn btn-success btn-icon"><i class="fa fa-save img-circle text-success"></i> <?php echo lang('Submit');?></button>
                </div>
                <?php echo form_close(); ?>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <script>
        $(document).ready(function () {
            $('.deleteMeta').on('click', function (e) {
                e.preventDefault();
                var href = $(this).attr('href');
                var div_id = $(this).attr('div-id');

                bootbox.confirm("<?php echo lang('r_u_sure');?>", function(result){ 
                    if (result) {
                         $.ajax({
                            type: 'GET',
                            url: href,
                            success: function (data) {
                                $('#' + div_id).hide();
                                toastr.success("<?php echo lang('Field has been deleted');?>");
                            }
                        });
                    }
                });
            });
        });
        $('#addRow').on('shown.bs.modal', function (e) {
            var position = $(e.relatedTarget).data('position');
            $(e.currentTarget).find("#position").val(position);
        })
    </script>