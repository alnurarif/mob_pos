<style type="text/css">
    .default_label {
        font-size: 13px;
        position: absolute;
        top: 5px;
        right: 20px;
        vertical-align: top;
        line-height: 15px;
        padding: 2px 5px;
        border-radius: 2px;
    }

    .template_tools {
        font-size: 13px;
        position: absolute;
        bottom: 5px;
        right: 20px;
        vertical-align: top;
        line-height: 15px;
        padding: 2px 5px;
        border-radius: 2px;
    }

    .template_tool {
        font-size: 13px;
        position: absolute;
        bottom: 5px;
        left: 20px;
        vertical-align: top;
        line-height: 15px;
        padding: 2px 5px;
        border-radius: 2px;
    }
</style>
<?php if($this->Admin || $GP['payroll-setDefaultTemplate']): ?>
<script type="text/javascript">
    jQuery(document).on("click", "#setDefaultTemplate", function () {
        id = $(this).data('num');
        $.post("<?php echo site_url('panel/payroll/setDefaultTemplate'); ?>", {
            id: id,
        },
        function (data) {
            if (data.success) {
                toastr.success("<?php echo lang('Template is set to default');?>");
                window.location.reload();
            }else{
                toastr.error("<?php echo lang('an error occured');?>");
            }
        });
    });
</script>
<?php endif; ?>
<?php if($this->Admin || $GP['payroll-add_template']): ?>
<a class="btn-icon btn btn-primary btn-icon add_template" ><i class="fa fa-plus img-circle text-primary"></i> Add Template</a>
<br>
<br>
<?php endif; ?>
<div class="row">
    <?php foreach ($templates as $template): ?>
        <div class="col-md-4">
            <div class="img-thumbnail text-center">
                <?php if($template->id == $settings->payroll_template): ?>
                    <span class="label label-success default_label"><?php echo lang('Default');?></span>
                <?php endif; ?>
                <?php if($this->Admin || $GP['payroll-template']): ?>
                    <a href="<?php echo base_url();?>panel/payroll/template/<?php echo $template->id;?>">
                <?php else: ?>
                    <a href="#">
                <?php endif; ?>
                    <img src="<?php echo base_url();?>assets/uploads/default_payroll_template.png" class="img-responsive"/>
                    <h4><?php echo $template->name; ?></h4>
                    <p><?php echo $template->notes; ?></p>
                </a>
                <div class="template_tools">
                    <?php if($this->Admin || $GP['payroll-setDefaultTemplate']): ?>
                        <?php if($template->id !== $settings->payroll_template): ?>
                            <button class="btn btn-primary btn-xs " id="setDefaultTemplate" data-num="<?php echo $template->id;?>">  <?php echo lang('Make Default');?></button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="template_tool">
                    <?php if($this->Admin || $GP['payroll-delete_template']): ?>
                        <?php if($template->id !== $settings->payroll_template): ?>
                            <a class="btn btn-danger btn-xs" href="<?php echo base_url();?>panel/payroll/delete_template/<?php echo $template->id;?>" data-num="<?php echo $template->id;?>">  <?php echo lang('Delete Template');?></a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>


<!-- Model Add -->
<div class="modal fade" id="templatemodal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="templatemodalheader"></h4>
            </div>
            <div class="modal-body">
                <form id="template_form" class="col s12">
                    <div class="row">
                        <div class="col-md-12 input-field">
                            <div class="form-group">
                                <label><?php echo lang('Name');?></label>
                                <input type="text" name="name" id="template_name" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-12 input-field">
                            <div class="form-group">
                                <label><?php echo lang('Notes');?></label>
                                <input type="text" name="notes" id="template_notes" class="form-control">
                            </div>
                        </div>
                        
                    </div>
                </form>
            </div>
            <div class="modal-footer" id="templatefooter">
                  <!--    -->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).on("click", ".add_template", function (e) {
        $('#templatemodal').modal('show');
        $('#template_form').trigger("reset");
        $('#template_form').parsley().reset();
        jQuery('#templatemodalheader').html("<?php echo lang('Add Payroll Template');?>");
        jQuery('#templatefooter').html('<button data-dismiss="modal" class="btn-icon btn btn-goback" type="button"><i class="fa fa-reply img-circle text-muted"></i> <?php echo lang("go_back") ?></button><button id="submit_template" role="button" form="template_form" class="btn-icon btn btn-success" data-mode="add"><i class="fa fa-plus img-circle text-success"></i> <?php echo lang("add"); ?></button>');
    });

    $(function () {
      $('#template_form').parsley({
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
        var mode = jQuery('#submit_template').data("mode");
        var id = jQuery('#submit_template').data("num");
        var url = "";
        var dataString = new FormData($('#template_form')[0]);
        url = base_url + "panel/payroll/add_template";
        $.ajax({
            url: url,
            type: "POST",
            data:  dataString,
            contentType:false,
            cache: false,
            processData:false,
            success: function (result) {
                toastr['success']("<?php echo lang('template successfully added');?>");
                setTimeout(function () {
                    window.location.href="<?php echo base_url();?>panel/payroll/template/"+result.id;
                }, 500);
            }
        });
        return false;
    });
    });


</script>