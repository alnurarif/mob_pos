<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('Assign Commission');?></h3>
      <div class="box-tools pull-right">
      </div>
  </div>
  <div class="box-body">
     <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
        <?php echo form_open('panel/commission/edit_product/'.$row->id, 'id="assign_plan"'); ?>
            <div class="form-group">
                <label><?php echo lang('Category');?></label>
                <?php 
                $category = array(
                    'repair_parts' => lang('Repair Parts'),
                    'new_phones' => lang('New Phones'),
                    'used_phones' => lang('Used Phones'),
                    'accessories' => lang('Accessories'),
                    'other' => lang('Other'),
                    'plans' => lang('Cellular Plans'),
                );
                echo form_dropdown('category', $category, set_value('category', $row->category), 'class="form-control" required id="category"' );
                ?>
            </div>
            <div class="form-group" id="product-div">
                <label><?php echo lang('Product');?></label>
                <?php
                $product = array();
                foreach ($products as $prod) {
                    $product[$prod->id] = $prod->name;
                }
                echo form_dropdown('product', $product, set_value('product', $row->product_id), 'class="form-control" required id="product"' );
                ?>
            </div>
            <hr>

            <div class="form-group">
                <label><?php echo lang('Commission Plan');?></label>
                <?php 
                    echo form_dropdown('plan', $plans, set_value('plan', $row->plan_id), 'class="form-control" required id="plan"' );
                ?>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-md-1 control-label"><?php echo lang('Groups');?></label>
                <div class="col-md-9 ui-sortable">
                     <?php foreach ($this->ion_auth->groups()->result() as $group): ?>
                        <div class="checkbox-styled checkbox-inline">
                            <input id="group_<?php echo bin2hex($group->id); ?>" <?php echo (($this->input->post('groups') && in_array($group->id, $this->input->post('groups'))) || in_array($group->id, explode(',', $row->groups))) ? 'checked' : ''; ?> type="checkbox" class="skip" value="<?php echo $group->id; ?>" name="groups[]">
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
    $('#category').on('change', function (e) {
        $('#product').val('').trigger('change');
        $('#product').empty();
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
    checked = $("input[type=checkbox]:checked").length;
    if(!checked) {
        bootbox.alert("<?php echo lang('You must check at least one checkbox');?>");
        return false;
    }
    dataString = $('#assign_plan').serialize()  + '&type=product';
    jQuery.ajax({
        type: "POST",
        url: "<?php echo base_url(); ?>panel/commission/checkData/<?php echo $row->id;?>",
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
});
</script>