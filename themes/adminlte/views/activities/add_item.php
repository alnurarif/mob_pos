<?php $id = $this->input->post('id'); ?>
<tr>
  	<td>
      	<div class="form-group">
          	<input type="text" class="form-control activity_name" name="name[<?php echo $id; ?>]" placeholder="<?php echo lang('Sub Activity Name');?>" value=""/>
      	</div>
 	</td>
	<input type="hidden" class="" name="sub_old[<?php echo $id;?>]" value="0">
  	<input type="hidden" class="form-control"  name="disable[<?php echo $id; ?>]"  value="0">
</tr>