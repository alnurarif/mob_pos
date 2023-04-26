<?php
$id = uniqid();
?>
<tr id="<?php echo $id; ?>">
<td>
<div class="form-group">
<input type="text" class="form-control" name="imei[]" placeholder="<?php echo lang('Serial/IMEI number');?>" value="<?php echo set_value('imei');?>"/>
<span class="help-block text-red"></i></span>
</div>
</td>
<td>
<input  type="text" class="form-control" name="purchase_price[]" placeholder="<?php echo lang('Price Cost');?>" value="<?php echo set_value('purchase_price');?>">
<span class="help-block text-red"></i></span>
</td>
<td>
<input  type="text" class="form-control" name="list_price[]" placeholder="<?php echo lang('List Price');?>" value="<?php echo set_value('list_price');?>">
<span class="help-block text-red"></i></span>
</td>
<td><button type="button" class="btn btn-danger btn-xs delete imei_delete_item" data-id="<?php echo escapeStr($id);?>"><i class="fas fa-trash"></i></button></td>
</tr>