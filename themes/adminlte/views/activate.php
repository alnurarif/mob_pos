
<div class="box">
  <div class="box-header">
    <h2 class="box-title"><?php echo lang('Select store to activate');?></h2>
  </div>
  <div class="box-body">
  <p><strong> <?php echo sprintf(lang('Currently active store'), (isset($_SESSION['active_store'])) ? $stores[$_SESSION['active_store']]['name'] : 'NONE');?></strong></p>
    <?php echo form_open(); ?>
    <div class="">
      <select class="form-control" name="current_account" required>
          <option disabled>(NONE)</option>
          <?php if($stores): ?>
              <?php foreach($stores as $store): ?>
                <option <?php echo $store['id'] == $this->activeStore ? 'selected' : ''; ?> value="<?php echo $store['id']; ?>"><?php echo escapeStr($store['name']); ?></option>
              <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
    <br>
    <button class="btn btn-primary"><?php echo lang('Activate');?></button>
  <?php echo form_close(); ?>
  </div>
  <div class="box-footer">
  <?php echo lang('store_info_msg');?>
  </div>
</div>
