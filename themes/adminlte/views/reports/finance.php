<?php 
	switch ($month) {
		case 1:
		case 3:
		case 5:
		case 7:
		case 8:
		case 10:
		case 12:
			$m = 31;
			break;
		case 4:
		case 6:
		case 9:
		case 11:
			$m = 30;
			break;
		case 2:
			$m = 28;
			break;
		default:
			$m = 30;
			break;
	}
	$total = 0;
	for ($i = 1; $i <= $m; ++$i) {
	    $total += $list[$i];
	}
	?>
<div class="box box-primary ">
	<div class="box-header with-border">
		<h3 class="box-title">Filter Result</h3>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<?php echo lang('choose_month', 'choose_month');?>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fas fa-calendar"></i>
						</div>
						<input type="text" readonly="" value="<?php echo $list[32].'/'.$list[33]; ?>" id="date" size="16" class="form-control">
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<?php echo lang('User', 'assigned_to');?>

					<?php  
					$u = [''=>lang('All')];
					foreach ($all_users as $user) {
						$u[$user->id] = $user->first_name.' '.$user->last_name;
					}
					?>
					<?= form_dropdown('assigned_to', $u, $this->input->get('assigned_to'), 'id="assigned_to" class="form-control" style="width: 100%"'); ?>
				</div>
			</div>
			<div class="col-md-12">
				<a class="submit_date btn btn-primary btn-primary"><i class="fas fa-refresh"></i> <?php echo $this->lang->line('update');?></a> 
			</div>
		</div>
	</div>
</div>

<div class="clearfix"></div>
<div class="box box-primary ">
	<div class="box-header with-border">
		<h3 class="box-title"><?php echo $this->lang->line('months_earning_report');?>: <i><?php echo $list[32].'/'.$list[33]; ?></h3>
		<div class="box-tools pull-right">
		</div>
	</div>
	<div class="box-body">
		<div class="col-md-12">
			<div id="hero-area" style="height: 400px" class="graph"></div>
		</div>
		<div class="col-md-4">
			<!-- small box -->
			<div class="small-box bg-red">
				<div class="inner">
					<h3><?php echo $this->repairer->formatMoney ($total);?></h3>
					<p><?php echo $this->lang->line('this_month');?></p>
				</div>
				<div class="icon">
					<i class="ion ion-pie-graph"></i>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var Script = function() {
	
		jQuery(function() {
			window.m = Morris.Area({
				element: 'hero-area',
				data: [
				<?php if (count($list) <= 1): ?>
					  {period: '01', earnings: '0'}
				<?php else: ?>
				    <?php for ($i = 1; $i <= $m; ++$i):  ?>
				       {period: "<?php echo $list[33].'-'.$list[32].'-'.$i ?>", earnings: "<?php echo $list[$i]; ?>"},
				    <?php endfor; ?>
				<?php endif; ?>
	            
				],
				xkey: 'period',
				ykeys: ['earnings'],
				labels: [
					'<?php echo $this->lang->line('earnings_graph ');?>'
				],
				hideHover: 'auto',
				lineWidth: 1,
				pointSize: 2,
				lineColors: ['#33df33'],
				fillOpacity: 0.5,
				smooth: true,
				xLabelAngle: 0,
				xLabels: 'day',
			   	resize: true,
	  			redraw: true,
				xLabelFormat: function(x) {
					return x.getUTCDate();
				},
				yLabelFormat: function(y) {
	                      return "<?php echo $currency; ?>" + y.toString();
				}
	
			});
		});
	
	}();
	
	jQuery(document).ready(function () {
		jQuery('.submit_date').on( "click", function (e) {
			var url = jQuery("#date").val();
			var vars = '?assigned_to='+jQuery("#assigned_to").val();
			window.location = base_url+"panel/reports/finance/" + url + vars;
		});
	});
	$(function(){
	    $('#date').datetimepicker({
  			format: 'mm/yyyy', 
  			fontAwesome: true, 
  			language: 'sma', 
  			weekStart: 1, 
  			todayBtn: 1, 
  			autoclose: 1, 
  			todayHighlight: 1, 
  			startView: 3, 
  			maxView: 4, 
  			minView: 3, 
  			forceParse: 0
  		});
	});
	
	$(window).on('resize', function() {
	window.m.redraw();
	});
</script>