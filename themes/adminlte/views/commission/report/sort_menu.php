<script type="text/javascript">
	$("#second_d").select2({
        placeholder: "<?php echo lang('please_select_srt_commission');?>",
        ajax: {
          	url: '<?php echo base_url();?>panel/commission/json_sort',
          	type: 'POST',
          	data: function (params) {
	            return {
	              	id: $("#sort_by").val(),
	              	search: params.term,
	              	<?php if($groups):?>
	              		group: "<?php echo $groups; ?>",
	              	<?php endif;?>

	            }
          	},
          	processResults: function (data) {
	            return {
	              	results: $.map(data, function (item) {
	                	return {
	                        text: item.text,
	                        id: item.id
	                    }
	                })
	            };
          	}
        }
    });
	$(function () {
	    jQuery(document).on("change", "#sort_by", function () {
	       	$('.sort_with_div').slideDown();
	        $('#sort_with_label').html($(this).val());
	        if ($(this).val() == 'all') {
	        	$('.sort_with_div').slideUp();
	        	$('.sort_with_div').disabled = true;
	        }
	    });
    });

	var dateToday = new Date();
</script>

<script type="text/javascript">
	$(function () {
		
        $('.datetimepicker').datetimepicker({
        	format:'MM-DD-YYYY HH:mm:ss',
        });
        $(".derp").daterangepicker({
		    datepickerOptions : {
		        numberOfMonths : 2
		    },
		    dateFormat: 'mm-dd-yy',
            ranges: {
               "<?php echo lang('Today');?>": [moment(), moment()],
               "<?php echo lang('Yesterday');?>": [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               "<?php echo lang('Last 7 Days');?>": [moment().subtract(6, 'days'), moment()],
               "<?php echo lang('Last 30 Days');?>": [moment().subtract(29, 'days'), moment()],
               "<?php echo lang('This Month');?>": [moment().startOf('month'), moment().endOf('month')],
               "<?php echo lang('Last Month');?>": [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            "alwaysShowCalendars": true,
		});
        $('.derp').on('apply.daterangepicker', function(ev, picker) {
            var start = (picker.startDate.format('YYYY-MM-DD'));
            var end = (picker.endDate.format('YYYY-MM-DD'));
            var dateObject = {
                "start": start,
                "end": end
            };

            jsonString = JSON.stringify(dateObject);
            $('#date_range').val(jsonString);
        });
    });
</script>
<?php echo form_open('panel/commission/report'); ?>
	<br>
	<input type="hidden" name="pin_code" id="pincode" value="<?php echo escapeStr($pin_code); ?>">
	<div class="form-group">
		<label><?php echo lang('Sort By');?></label>
		<select class="form-control" id="sort_by" name="sort_by">
			<option value="user"><?php echo lang('Individual');?></option>
			<option value="group"><?php echo lang('Group');?></option>
			<?php if($admin || $perms['commission-view_all']) : ?><option value="all"><?php echo lang('View All');?></option><?php endif; ?>
		</select>
	</div>
	
	<div class="form-group sort_with_div">
		<label id="sort_with_label"><?php echo lang('Individual');?></label>
		<select class="form-control" name="sort_with" id="second_d"></select>
	</div>

	<div class="row" id="edit_div">
		<div class="form-group col-md-6">
			<label><?php echo lang('Date Range');?></label>
			<input class="form-control derp btn btn-primary" name="date_range_o">
            <input type="hidden" name="date_range" class="date_range" id="date_range" value='<?php echo (isset($_POST['date_range']) ? htmlspecialchars($_POST['date_range']) : "");?>'>
		</div>
	</div>
	<input type="submit" class="btn btn-primary" name="submit" value="<?php echo lang('Submit');?>">
</form>


