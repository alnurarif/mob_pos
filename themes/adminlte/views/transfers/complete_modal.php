<h3><?php echo $transfer->product_name; ?></h3>
<?php echo form_open('panel/transfers/multi_add', 'id="stock_multi"'); ?>
<button class="btn btn-primary"><?php echo lang('Add to Stock');?></button>
<table id="table_re" class="table table-striped table-bordered" style="width: 100%;">
	<thead>
		<tr>
			<th style="min-width:30px; width: 30px; text-align: center;">
                <input class="checkbox checkft" type="checkbox" name="check"/>
            </th>			
            <th>#</th>
			<th><?php echo lang('Serial Number');?></th>
			<th><?php echo lang('Cost');?></th>
			<th><?php echo lang('Status');?></th>
		</tr>
	</thead>
	<tbody>
		<?php $i = 1; foreach ($items as $item): ?>
			<tr>
                <?php if($item['in_state_of_transfer'] == 1 && $item['transfer_id'] == $transfer->id): ?>
					<td id="treeC_<?php echo $item['id']; ?>">
    					<div class="text-center"><input class="checkbox multi-select" type="checkbox" name="val[]" value="<?php echo $item['id']; ?>" /></div>
    				</td>
				<?php else: ?>
					<td></td>
				<?php endif; ?>
				
				<td><?php echo $i; ?></td>
				<td><?php echo $item['serial_number']?escapeStr($item['serial_number']):'Not Available'; ?></td>
				<td><?php echo $item['price']; ?></td>
				<?php if($item['in_state_of_transfer'] == 1 && $item['transfer_id'] == $transfer->id): ?>
					<td id="tree_<?php echo $item['id']; ?>"><button data-num="<?php echo $item['id']; ?>" data-transfer_id="<?php echo $transfer->id; ?>"  class="add_to_stock btn btn-primary btn-xs"><?php echo lang('Add to Stock');?></button></td>
				<?php else: ?>
					<td><span class="label label-success"><?php echo lang('Added');?></span></td>
				<?php endif; ?>
			</tr>
		<?php $i+=1; endforeach; ?>
	</tbody>
</table>
<?php echo form_close();?>
<script type="text/javascript">
	$('#table_re').DataTable();
	var transfer_id_ = <?php echo $transfer->id; ?>;
    $('#stock_multi').on( "submit", function(event) {
       	event.preventDefault();
    	checked = $("input[type=checkbox]:checked").length;
      	if(!checked) {
        	bootbox.alert("<?php echo lang('You must check at least one checkbox');?>");
        	return false;
      	}
       	jQuery.ajax({
            type: "POST",
            url: base_url + "panel/transfers/multi_add",
            data: $(this).serialize() + '&transfer_id=' +transfer_id_ ,
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
        		$.each(data, function () {
        			$('#tree_'+this).html('<span class="label label-success">'+"<?php echo lang('Added');?>"+'</span>');
                	$('#treeC_'+this).html('');
				});
                $('#dynamic-table').DataTable().ajax.reload();
            }
        });
        
    });
</script>