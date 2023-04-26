<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title text-left"><?php echo escapeStr($note->subject); ?></h4>
    </div>
    <div class="modal-body text-left">
        <textarea class="form-control" id="note" name="note" required="required" rows="6" readonly=""><?php echo escapeStr($note->note); ?></textarea>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-modal" data-dismiss="modal"><?php echo lang('Close');?></button>
    </div>
</div>