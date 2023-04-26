<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa">&times;</i>
    </button>
    <h4 class="modal-title" id="myMoveModalLabel"><?php echo sprintf(lang('title_move_modal'), lang($type));?></h4>
</div>
<div class="modal-body" style="padding: 15px">
		<?php echo form_open('panel/pos/addToSafe'); ?>
		<input type="hidden" name="type" value="<?php echo $type; ?>">
		<div class="form-group">
            <label><?php echo lang('Amount');?></label>
    		<input type="number" required <?php echo ($type == 'safe') ? 'max="'.$tcash.'"' : ''; ?> name="amount" class="form-control">
    	</div>
    	<input type="submit" name="submit" value="<?=lang('submit');?>" class="btn btn-primary">
    	<?php echo form_close(); ?>
</div>
