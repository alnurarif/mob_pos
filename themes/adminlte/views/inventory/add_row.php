<?php $id = uniqid(); ?>
<tr id="variant_<?php echo $id; ?>">
    <td>
        <input type="text" class="form-control" name="variant_name[]"
                   placeholder="<?php echo lang('Name');?>"
                 required/>
    </td>
    <td>
        <input type="text" class="form-control" name="variant_price[]"
               placeholder="<?php echo lang('Price');?>"
                required>
    </td>
    <td><button type="button" class="btn btn-danger btn-xs delete delete_row" data-id="<?php echo escapeStr($id);?>"><i class="fas fa-trash"></i></button></td>
</tr>   