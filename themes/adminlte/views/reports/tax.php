<script type="text/javascript" src="<?php echo $assets;?>/plugins/datatables/ext/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="<?php echo $assets;?>/plugins/datatables/ext/jszip.min.js"></script>
<script type="text/javascript" src="<?php echo $assets;?>/plugins/datatables/ext/pdfmake.min.js"></script>
<script type="text/javascript" src="<?php echo $assets;?>/plugins/datatables/ext/vfs_fonts.js"></script>
<script type="text/javascript" src="<?php echo $assets;?>/plugins/datatables/ext/buttons.html5.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $assets;?>/plugins/datatables/ext/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo $assets;?>/plugins/datatables/ext/buttons.dataTables.min.css">
<style type="text/css">
	.dt-right{
		font-align:right;
	}
</style>

<?php 
if ($this->input->post('date_range')) {
	$date_range = json_decode($this->input->post('date_range')); 
	$date_range = date('m.d.Y',strtotime( $date_range->start)). ' - '.date('m.d.Y', strtotime($date_range->end));
}
?>

<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('reports/tax');?></h3>
      <div class="box-tools pull-right">
      </div>
  </div>
  <div class="box-body">
  	<?php if(validation_errors()) print(validation_errors()); ?>
		<?php echo form_open("panel/reports/tax"); ?>
		<div class="col-md-6">
			<label><?php echo lang('Date Range');?></label>
			<div class="form-group">
			<?php echo form_input('date_range_o', (isset($_POST['date_range_o']) ? $_POST['date_range_o'] : ""), 'class="form-control derp" id="daterange"'); ?>
            <input type="hidden" name="date_range" class="date_range" id="date_range" value='<?php echo (isset($_POST['date_range']) ? htmlspecialchars($_POST['date_range']) : "");?>'>
            </div>
		</div>
      
		<div class="col-md-6">
        	<label><?php echo lang('Taxes');?></label>
            <?php foreach ($taxRates as $rate): ?>
        		<div class="checkbox-styled">
                    <input <?php echo ($this->input->post('taxes') !== NULL && in_array($rate->id, $_POST['taxes'])) ? 'checked' : ''; ?> name="taxes[]" id="checkboxes-<?php echo $rate->id; ?>" value="<?php echo $rate->id; ?>" type="checkbox">
                    <label for="checkboxes-<?php echo $rate->id; ?>"><?php echo escapeStr($rate->name); ?></label>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="clearfix"></div>
        <div class="form-group">
            <div class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
        </div>
        <?php echo form_close(); ?>
    	<?php if ($tax_followed_items): ?>
    		<hr>
    		<?php foreach ($tax_followed_items as $tax => $items): ?>
    			
        		<fieldset>
        			<legend><?php echo $tax; ?></legend>
        			<div class="table-responsive">
	                    <table class=" compact table table-bordered table-striped" id="dynamic-table_<?php echo preg_replace('/\s+/', '', $tax); ?>" class="datatable">
	                        <thead>
	                            <tr>
	                                <th><?php echo lang('Sale ID');?></th>
	                                <th><?php echo lang('Date');?></th>
	                                <th><?php echo lang('Product Name');?></th>
	                                <th><?php echo lang('Unit Price');?></th>
	                                <th><?php echo lang('Tax');?></th>
	                            </tr>
	                        </thead>
	                        <tbody>
	                        	<?php 
	                        		$tprice = 0; $ttax = 0; foreach ($items as $item): 
	                        		$tprice += $item['unit_price'];
	                        		$ttax += $item['tax'];
	                        		?>
	                        		<tr>
	                        			<td><a href="<?php echo base_url();?>panel/pos/view/<?php echo $item['sale_id']; ?>" target="_blank"><?php echo $item['sale_id'];?></a></td>
	                        			<td><?php echo date('m.d.Y H:i:s' ,strtotime($item['date'])); ?></td>
	                        			<td><?php echo $item['product_name']; ?></td>
	                        			<td><span class=""><?php echo escapeStr($settings->currency).number_format($item['unit_price'], 2); ?></span></td>
	                        			<td><span class=""><?php echo escapeStr($settings->currency).number_format($item['tax'], 2); ?></span></td>
	                        		</tr>
    							<?php endforeach; ?>
	                        </tbody>
	                        <tfoot>
	                        	<tr>
	                        		<th><?php echo lang('Sale ID');?></th>
	                                <th><?php echo lang('Date');?></th>
	                        		<th><?php echo lang('Product Name');?></th>
	                        		<th><span class=""><?php echo escapeStr($settings->currency).number_format($tprice, 2); ?></span></th>
	                        		<th><span class=""><?php echo escapeStr($settings->currency).number_format($ttax, 2); ?></span></th>
	                        	</tr>
	                        </tfoot>
	                    </table>
	                </div>
        		</fieldset>
        		<script type="text/javascript">
				$(document).ready(function () {
					
					var tbl = '#dynamic-table_<?php echo preg_replace('/\s+/', '', $tax); ?>';
					$('#dynamic-table_<?php echo preg_replace('/\s+/', '', $tax); ?>').dataTable({
						
						dom: 'Bfrtip',
                    	buttons: [
						    {
						    	title: '<?php echo $tax; ?> | <?php echo $date_range; ?> | <?php echo $site_name; ?>',
						        extend:'pdfHtml5',
						        text:"<?php echo lang('PDF');?>",
						        orientation:'landscape',
						        customize : function(doc){
						            var colCount = new Array();
						            $(tbl).find('tbody tr:first-child td').each(function(){
						                if($(this).attr('colspan')){
						                    for(var i=1;i<=$(this).attr('colspan');$i++){
						                        colCount.push('*');
						                    }
						                }else{ colCount.push('*'); }
						            });
						            doc.content[1].table.widths = colCount;
						        },
                                footer: true,
						    },
						    {
						    	title: '<?php echo $tax; ?> | <?php echo $date_range; ?> | My Tech POS',
                                extend: 'csv',
                                'sButtonText': "<?php echo lang('CSV');?>",
                                footer: true,
                           	},
                           	{
						    	title: '<?php echo $tax; ?> | <?php echo $date_range; ?> | My Tech POS',
                               	extend: 'excel',
                               	'sButtonText': "<?php echo lang('Excel');?>",
                               	footer: true,
                           	}   
						],
                       	"columnDefs": [
							{	 "type": "num-fmt", "targets": [3,4], "className": "text-right" }
						],


					});
				});
				</script>
    		<?php endforeach; ?>
    	<?php endif; ?>
  </div>
</div>
