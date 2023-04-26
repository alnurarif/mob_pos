<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('Assign Commission');?> </h3>
      <div class="box-tools pull-right">
      </div>
  </div>
  <div class="box-body">
       <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
        <?php echo form_open('panel/commission/assign', 'id="assign_plan"'); ?>
            <div class="form-group">
                <label><?php echo lang('Type of Commission');?></label>
                <?php 
                $types = array(
                    'product' => lang('Product Commission'),
                    'group' => lang('Category Commission'),
                );
                echo form_dropdown('type', $types, set_value('type'), 'class="form-control" required id="type"' );
                ?>
                <span class="help-block" id="type_span"><?php echo lang('please select a value');?></span>
            </div>
            <hr>
            <div class="form-group">
                <label><?php echo lang('Category');?></label>
                <?php 
                $category = array(
                    'repair_parts' => 'Repair Parts',
                    'new_phones' => 'New Phones',
                    'used_phones' => 'Used Phones',
                    'accessories' => 'Accessories',
                    'other' => 'Other',
                    'plans' => 'Cellular Plans',
                );
                echo form_dropdown('category', $category, set_value('category'), 'class="form-control" required id="category"' );
                ?>
                <span class="help-block" id="category_span"><?php echo lang('please select a value');?></span>
            </div>
            <div class="form-group" id="product-div">
                <label><?php echo lang('Product');?></label>
                <select name="product" id="product" class="form-control" style="width: 100%" required=""></select>
                <span class="help-block" id="product_span"><?php echo lang('please select a value');?></span>
            </div>
            <hr>
            <div class="form-group">
                <label><?php echo lang('Commission Plan');?></label>
                <?php 
                    echo form_dropdown('plan', $plans, set_value('plan'), 'class="form-control" required id="plan"' );
                ?>
                <span class="help-block" id="plan_span"><?php echo lang('please select a value');?></span>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-md-1 control-label"><?php echo lang('Groups');?></label>
                <div class="col-md-9 ui-sortable">
                     <?php foreach ($this->ion_auth->groups()->result() as $group): ?>
                        <div class="checkbox-styled checkbox-inline">
                            <input id="group_<?php echo bin2hex($group->id); ?>" <?php echo ($this->input->post('groups') && in_array($group->id, $this->input->post('groups'))) ? 'checked' : ''; ?> type="checkbox" class="skip" value="<?php echo $group->id; ?>" name="groups[]">
                            <label for="group_<?php echo bin2hex($group->id); ?>"><?php echo escapeStr($group->name); ?> </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php echo form_close(); ?>

            <div class="row col-md-12">
                <button id="submit" class="btn btn-primary"><?php echo lang('Submit');?></button>
            </div>
  </div>
</div>

<script type="text/javascript">
jQuery(document).ready( function($) {
    $('.help-block').hide();
    $('#type').on('change', function (e) {
        var val = $(this).val();
        console.log(val);
        if (val == 'group') {
            $('#product-div').slideUp();
        } else {
            $('#product-div').slideDown();
        }
    });

    $('#category').on('change', function (e) {
        $('#product').val('').trigger('change');
    });

    $( "#product" ).select2({    
        ajax: {
            placeholder: "<?php echo lang('Select a Product');?>",
            url: "<?php echo base_url(); ?>panel/commission/getProductsAjax",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    type: $('#category').val(),
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
    });
});

 // process the form
 $("#submit").on( "click", function() {
    event.preventDefault();

    var type = $('#type').val();
    var category = $('#category').val();
    var product = $('#product').val();
    var plan = $('#plan').val();
    if (type == "") {
        $("span#type_span").show();
        $("input#name").focus();
        return false;
    }
    if (category == "") {
        $("span#category_span").show();
        $("input#category").focus();
        return false;
    }
    if (type == 'product' && (product == null || product == "")) {
        $("#product-div").addClass("has-error");
        $("span#product_span").show();
        $("input#product").focus();
        return false;
    }
    if (plan == "") {
        $("span#plan_span").show();
        $("input#plan").focus();
        return false;
    }

    checked = $("input[type=checkbox]:checked").length;
    if(!checked) {
        bootbox.alert("<?php echo lang('You must check at least one checkbox');?>");
        return false;
    }

    dataString = $('#assign_plan').serialize();
    jQuery.ajax({
        type: "POST",
        url: "<?php echo base_url(); ?>panel/commission/checkData",
        data: dataString,
        cache: false,
        success: function (data) {
            data = JSON.parse(data);
            if (data.success) {
                $('#assign_plan').submit();
            } else{
                if (data.msg) {
                    bootbox.alert((data.msg).join('<br>'));
                } 
            }
            return;
        }
    });
    return false;
});

</script>