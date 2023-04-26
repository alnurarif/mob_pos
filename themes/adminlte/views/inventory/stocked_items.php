<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-header no-print">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        <i class="fas fa-2x">&times;</i>
    </button>
    <h4 class="modal-title" id="myModalLabel"><?php echo lang('View Details');?></h4>
</div>
<div class="modal-body">
	<div class="table-responsive">
		<table class="table table-striped">
			<thead>
                <tr>
                    <th><?php echo lang('Name');?></th>
                    <th><?php echo lang('Type');?></th>
                    <th><?php echo lang('Category');?></th>
                    <th><?php echo lang('Subcategory');?></th>
                    <th><?php echo lang('UPC Code');?></th>
                    <th><?php echo lang('Serial Number');?></th>
                    <th><?php echo lang('Counted Quantity');?></th>
                    <th><?php echo lang('Expected Quantity');?></th>
                    <th><?php echo lang('Difference');?></th>
                    <th><?php echo lang('Expected Cost');?></th>
                    <th><?php echo lang('Counted Cost');?></th>
                    <th><?php echo lang('Difference');?></th>
                </tr>
            </thead>
            <tbody>
            	<?php foreach ($result as $row): ?>
	            	<tr>
	                    <th><?php echo escapeStr($row->product_name); ?></th>
	                    <th><?php echo humanize($row->product_type); ?></th>
	                    <th><?php echo escapeStr($row->category); ?></th>
	                    <th><?php echo escapeStr($row->sub_category); ?></th>
	                    <th><?php echo escapeStr($row->product_code); ?></th>
	                    <th><?php echo $row->serial ? escapeStr($row->serial) : lang('Non-Serialed Product'); ?></th>
	                    <th><?php echo $row->counted_qty; ?></th>
	                    <th><?php echo $row->total_qty; ?></th>
	                    <th><?php echo $row->difference; ?></th>
	                    <th><?php echo $row->total_cost; ?></th>
	                    <th><?php echo $row->cost_selected; ?></th>
	                    <th><?php echo number_format($row->total_cost-$row->cost_selected, 2); ?></th>
	                </tr>
            	<?php endforeach; ?>
            </tbody>
		</table>
	</div>
</div>
