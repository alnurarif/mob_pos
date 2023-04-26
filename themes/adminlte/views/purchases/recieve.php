
<div class="box">
    <div class="box-header">
        <div class="title-title"><?php echo lang('Recieve purchase');?>: <?php echo escapeStr($inv->reference_no); ?></div>
    </div>
    <div class="box-body">
        <?php echo form_open('panel/purchases/multi_add', 'id="stock_multi"'); ?>
        <button class="btn btn-primary"><?php echo lang('Add to Stock');?></button>

        <table id="table_re" class="table table-striped table-bordered" style="width: 100%;">
        	<thead>
        		<tr>
        			<th style="min-width:30px; width: 30px; text-align: center;">
                        <input class="checkbox checkft" type="checkbox" name="check"/>
                    </th>			
                    <th>#</th>
                    <th><?php echo lang('Product Name');?></th>
        			<th><?php echo lang('Serial Number');?></th>
                    <th><?php echo lang('Unit Cost');?></th>
                    <th><?php echo lang('Unit Price');?></th>
        			<th><?php echo lang('Status');?></th>
        		</tr>
        	</thead>
        	<tbody>
        		<?php $i = 1; foreach ($inv_items as $item): ?>
                    <input type="hidden" class="form-control" name="is_serialized[]" value="<?php echo $item->is_serialized; ?>">

        			<tr>
                        <td id="treeC_<?php echo $item->id; ?>">
                            <?php if(!$item->recieved): ?>
                                <div class="text-center">
                                    <input data-is_serialized="<?php echo $item->is_serialized ? 'true' : 'false'; ?>" class="checkbox multi-select" type="checkbox" name="val[]" value="<?php echo $item->id; ?>"  />
                                </div>
                            <?php endif; ?>
                        </td>
        				<td><?php echo $i; ?></td>
                        <td><?php echo escapeStr($item->product_name);?> (<?php echo escapeStr($item->product_code);?>)</td>
        				<td>
                            <?php if($item->is_serialized && !$item->recieved): ?>
                                <input type="text" class="form-control" name="serial_number[]" id="serial_number">
                            <?php else:?>
                                <input type="hidden" class="form-control" name="serial_number[]">
                            <?php endif;?>
                        </td>
                        <td>
                            <?php if($item->stock_type == 'used_phone' && !$item->recieved): ?>
                                <input type="text" class="form-control" name="unit_cost[]" id="unit_cost" value="">
                            <?php else:?>
                                <input type="hidden" class="form-control" name="unit_cost[]">
                                <?php echo $item->unit_cost; ?>
                            <?php endif;?>
                        </td>
                        <td>
                            <?php if($item->stock_type == 'used_phone' && !$item->recieved): ?>
                                <input type="text" class="form-control" name="price[]" id="price" value="">
                            <?php else:?>
                                <input type="hidden" class="form-control" name="price[]" id="price" value="">
                            <?php endif;?>
                        </td>
        				<td id="tree_<?php echo $item->id; ?>">
                            <?php if(!$item->recieved): ?>
                            <button data-is_serialized="<?php echo $item->is_serialized ? 'true' : 'false'; ?>" data-type="<?php echo $item->stock_type; ?>"  data-num="<?php echo $item->id; ?>" class="add_to_stock btn btn-primary btn-xs">Add to Stock</button>
                            <?php else: ?>
                                <span class="label label-success">Added</span>
                            <?php endif; ?>
                        </td>
        			</tr>
        		<?php $i+=1; endforeach; ?>
        	</tbody>
        </table>
        <?php echo form_close();?>
    </div>
</div>
<script type="text/javascript">
    $('#table_re').DataTable();
    
	var purchase_id_ = <?php echo $inv->id; ?>;
    $('#stock_multi').on( "submit", function(event) {
       	event.preventDefault();
    	checked = $("input[type=checkbox]:checked");
      	if(!checked.length) {
        	bootbox.alert("<?php echo lang('You must check at least one checkbox');?>");
        	return false;
      	}
        $.each(checked, function(){
            var a = $(this).closest('tr').find('.add_to_stock');
            a.click();
        });
    });

    $(document).on('click', '.add_to_stock', function (event) {
        event.preventDefault();
        var id = $(this).data('num');
        var is_serialized    = $(this).data('is_serialized');
        var type    = $(this).data('type');
        var serial_number    = $(this).closest('tr').find('#serial_number');
        var cost             = $(this).closest('tr').find('#unit_cost');
        var price            = $(this).closest('tr').find('#price');

        if (is_serialized && serial_number.val() == '') {
            bootbox.alert("<?php echo lang('Please enter a serial number for this product');?>"); 
            return false;
        }
        if (is_serialized && type == 'used_phone') {
            if (cost.val() == '') {
                bootbox.alert("<?php echo lang('Please enter a cost for this phone');?>"); 
                return false;
            }
            if (price.val() == '') {
                bootbox.alert("<?php echo lang('Please enter a price for this product');?>"); 
                return false;
            }
        }

        var url = base_url + "panel/purchases/recieved/0";
        if (is_serialized) {
            var url = base_url + "panel/purchases/recieved/1";
        }

        jQuery.ajax({
            type: "POST",
            url: url,
            data: "id=" + encodeURI(id) + "&serial_number=" + encodeURI(serial_number.val()) + "&cost=" + encodeURI(cost.val()) + "&price=" + encodeURI(price.val()) + "&purchase_id=" + encodeURI(purchase_id_) ,
            cache: false,
            dataType: "json",
            success: function (data) {
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "progressBar": true,
                    "positionClass": "toast-bottom-right",
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                }
                toastr['success']("<?php echo lang('Added to Stock');?>");
                $('#tree_'+id).html('<span class="label label-success">'+"<?php echo lang('Added to Stock');?>"+'</span>');
                $('#treeC_'+id).html('');
                $('#dynamic-table').DataTable().ajax.reload();
                if (serial_number) {
                    serial_number.remove();
                }
                if (cost) {
                    cost.remove();
                }
                if (price) {
                    price.remove();
                }
            }
        });
    });

    $(document).on('ifChecked', '.checkth, .checkft', function(event) {
        event.preventDefault();
        $('.checkth, .checkft').iCheck('check');
        $('.multi-select').each(function() {
            $(this).iCheck('check');
            $(this).closest('tr').find('#serial_number').attr('required', true);
        });
    });
    $(document).on('ifUnchecked', '.checkth, .checkft', function(event) {
        event.preventDefault();
        $('.checkth, .checkft').iCheck('uncheck');
        $('.multi-select').each(function() {
            $(this).iCheck('uncheck');
            $(this).closest('tr').find('#serial_number').attr('required', false);
        });
    });
    $(document).on('ifChecked', '.multi-select', function(event) {
        event.preventDefault();
        $(this).closest('tr').find('#serial_number').attr('required', true);
        $(this).iCheck('check');
    });
    $(document).on('ifUnchecked', '.multi-select', function(event) {
        $(this).closest('tr').find('#serial_number').attr('required', false);
        $(this).iCheck('uncheck');
    });

    $(document).on('ifUnchecked', '.multi-select', function(event) {
        event.preventDefault();
        $('.checkth, .checkft').attr('checked', false);
        $('.checkth, .checkft').iCheck('update');
    });
</script>