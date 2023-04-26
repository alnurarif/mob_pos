<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('Commission');?></h3>
      <div class="box-tools pull-right">
      </div>
  </div>
  <div class="box-body">
    <?php echo validation_errors(); ?>
            <?php echo form_open('panel/commission/edit/'.$plan->id); ?>
                
                <div class="col-md-12">
                    <div class="col-md-12 col-lg-4 input-field">
                        <div class="form-group">
                            <label><?php echo lang('Commission Label');?></label>
                            <input type="text" name="label" value="<?php echo set_value('label', $plan->label); ?>" class="form-control" data-parsley-required="true" required="">
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-12 input-field">
                      <div class="form-group all">
                          <div class="checkbox-styled checkbox-inline">
                              <input type="hidden"  name="universal" value="0">
                              <input type="checkbox" <?php echo $plan->universal ? 'checked' : ''; ?> id="universal" name="universal" value="1">
                              <label for="universal"><?php echo lang('is_universal'); ?></label>
                          </div>
                      </div>
                    </div>
                    <div class="col-md-12 col-lg-4 input-field">
                      <div class="form-group">
                          <label><?php echo lang('Value');?></label>
                          <input value="<?php echo set_value('value', $plan->value); ?>"  name="value" class="form-control" type="text" placeholder="0" data-parsley-required="true" data-parsley-type="number"  />
                      </div>
                    </div>
                     <div class="col-md-12 col-lg-4 input-field">
                      <div class="form-group">
                          <label><?php echo lang('Type');?></label>
                          <?php $types = array(
                            'sales' => lang('% of Sales'),
                            'profit' => lang('% of Profit'),
                            'flat' => lang('Flat Rate'),
                          ); 
                          echo form_dropdown('type', $types, set_value('type', $plan->type), 'class="form-control"');
                          ?>
                      </div>
                    </div>
                </div>
                <div class="col-md-12">
                  <?php echo form_submit('submit',lang('Edit Plan'), 'class="btn btn-primary"' ); ?>
                </div>
            <?php echo form_close(); ?>
  </div>
</div>


<script type="text/javascript">
$(function () {
  $('form').parsley();
});
</script>
