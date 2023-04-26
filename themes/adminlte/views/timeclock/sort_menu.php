<script type="text/javascript">
    <?php if($admin || $perms['timeclock-addentry']) : ?>
    $('#timeclock_add_form').on( "submit", function(event) {
        event.preventDefault();
        var id = jQuery('#submit').data("num");
        var valid = true;
        if (valid) {
            var url = base_url + "panel/timeclock/add_mEntry";
            dataString = $('#timeclock_add_form').serialize() + "&id=" + encodeURI(id);
            jQuery.ajax({
                type: "POST",
                url: url,
                data: dataString,
                cache: false,
                success: function (data) {
                    toastr['success']("<?php echo lang('edit');?>", "Timeclock: Added");
                    setTimeout(function () {
                        $('#timeclockmodal').modal('hide');
                    }, 500);
                }
            });

        }
        return false;
    });
    <?php endif; ?>

	$("#second_d").select2({
        placeholder: "<?php echo lang('please_select_srt_commission');?>",
        ajax: {
          	url: '<?php echo base_url();?>panel/timeclock/json_sort',
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
        <?php if($admin || $perms['timeclock-addentry']) : ?>
    		jQuery(document).on("click", "#add_entry", function (e) {
    	        event.preventDefault();
            	$('#timeclockmodal').modal('show');
    	    });
        <?php endif; ?>
		
        $('.datetimepicker').datetimepicker({
        	format:'MM-DD-YYYY HH:mm:ss',
        });

        $(".derp").daterangepicker({
		    datepickerOptions : {
		        numberOfMonths : 2
		    },
		    dateFormat: 'mm-dd-yy',
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(29, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
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
<?php echo form_open('panel/timeclock/view'); ?>
	<br>
	<input type="hidden" name="pin_code" id="pincode" value="<?php echo escapeStr($pin_code); ?>">
	
	<div class="form-group">
		<label><?php echo lang('Sort By');?></label>
		<select class="form-control" id="sort_by" name="sort_by">
			<option value="user"><?php echo lang('Individual');?></option>
			<option value="group"><?php echo lang('Group');?></option>
			<?php if($admin || $perms['timeclock-view_all']) : ?><option value="all"><?php echo lang('View All');?></option><?php endif; ?>
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
    <?php if($admin || $perms['timeclock-addentry']) : ?>
        <button id="add_entry" class="btn btn-primary pull-right"><i class="fas fa-plus-circle"></i> <?php echo lang('Add an Entry');?></button>
    <?php endif; ?>


	<input type="submit" class="btn btn-primary" name="submit" value="Submit">
</form>


<?php if($admin || $perms['timeclock-addentry']) : ?>
<!-- ============= MODAL MODIFICA CLIENTI ============= -->
<div class="modal fade" id="timeclockmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo lang('Add Time Clock Entry');?></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <form id="timeclock_add_form" method="post">
                        <input type="hidden" name="pin_code" value="<?php echo $pin_code; ?>">
                        <div class="col-md-12 col-lg-4 input-field">
                            <div class="form-group">
	                            <label><?php echo lang('User');?></label>
					            <?php
					            $dp = array();
					            foreach ($users as $user) {
					                $dp[$user->id] = $user->name;
					            }
					            unset($users);
					            echo form_dropdown('meuser', $dp, set_value('meuser'), 'class="form-control"');
					            ?>
				        	</div>
				        </div>
                        <div class="col-md-12 col-lg-4 input-field">
                            <div class="form-group">
                                <label><?php echo lang('Clock In');?></label>
                                <input id="clock_in" name="clock_in" type="text" required class="validate form-control datetimepicker">
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-4 input-field">
                            <div class="form-group">
                                <label><?php echo lang('Clock Out');?></label>
                                <input id="clock_out" name="clock_out" type="text" required class="validate form-control datetimepicker">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" id="footerClient1">
                <button role="submit" class="btn btn-primary" form="timeclock_add_form"><?php echo lang('Add Entry');?></button>
            </div>
        </div>
    </div>
</div>
</div>
<?php endif; ?>


