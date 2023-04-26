<?php echo form_open('panel/timeclock/view', array('name'=> 'timeclock')); ?>
	<input type="hidden" name="pin_code" id="pincode" value="<?php echo $this->ion_auth->user()->row()->pin_code; ?>">
	<input type="hidden" name="sort_by" id="sort_by" value="user">
	<input type="hidden" name="sort_with" id="sort_with" value="<?php echo $this->ion_auth->user()->row()->id; ?>">
	<input type="hidden" name="show_form"  value="1">
<?php echo form_close(); ?>
<script type="text/javascript">
	window.onload = function(){
	  document.forms['timeclock'].submit()
	}
</script>