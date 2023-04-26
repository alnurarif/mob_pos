<?php $id = $this->input->post('id'); ?>
<tr>
  <td>
      <div class="form-group">
          <input type="text" class="form-control plan_name" name="name[<?php echo $id; ?>]"
                 placeholder="<?php echo lang('Plan Name');?>"
                 value=""/>
      </div>
  </td>
  <td>
      <input type="text" class="form-control"
             name="cost[<?php echo $id; ?>]" placeholder="<?php echo lang('Cost');?>"
             value="">
  </td>
  <td>
      <input type="text" class="form-control"
             name="price[<?php echo $id; ?>]" placeholder="<?php echo lang('Price');?>"
             value="">
  </td>
  <td>
    <div class="col-md-9 row">
       <input type="text" class="form-control" name="duration[<?php echo $id; ?>]" placeholder="<?php echo lang('Duration');?>"
             value="">
    </div>
    <div class="col-md-3 row">
    <?php
    $plan_duration = array(
        'days' => lang('Days'),
        'months' => lang('Months'),
        'years' => lang('Years'),
    ); 
    ?>
    <?php 
    echo form_dropdown('plan_duration_type['.$id.']', $plan_duration, set_value('plan_duration_type'),'class="form-control"'); 
    ?>
    </div>
  </td>
  <td>
      <input type="number" step="any" class="form-control" name="activation_spiff[<?php echo $id; ?>]" placeholder="<?php echo lang('Activation Spiff');?>" value="">
  </td>
  <td>
    <input type="hidden" class="form-control"
             name="disable[<?php echo $id; ?>]" placeholder="<?php echo lang('Disable');?>"
             value="0">
  </td>

</tr>