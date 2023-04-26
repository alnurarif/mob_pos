<div class="box box-primary ">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo lang('Edit Plan');?></h3>
    </div>
    <div class="box-body">
        <?php echo validation_errors(); ?>
            <?php echo form_open('', 'name="plan"'); ?>
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                     <div class="form-group">
                        <div class="form-group">
                            <?php echo lang('p_carrier', 'carrier');?>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas  fa-folder"></i>
                                </div>
                                <?php
                                    $carriers_dd = array(); 
                                    foreach ($carriers as $carrier) {
                                        $carriers_dd[$carrier->id] = $carrier->name;
                                    }
                                ?>
                                <?php echo form_dropdown('carrier', $carriers_dd, set_value('carrier', $plan->carrier_id), 'class="form-control" id="carrier"'); ?>
                                <a class="add_carrier btn input-group-addon"><i class="fas fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                       
                        <?php if(!$settings->universal_plans): ?>
                        <div class="form-group all">
                            <div class="checkbox-styled checkbox-inline">
                                <input type="hidden"  name="universal" value="0">
                                <input type="checkbox" <?php echo $plan->universal ? 'checked' : ''?>  id="universal" name="universal" value="1">
                                <label for="universal"><?php echo lang('is_universal'); ?></label>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                       
                        <div class="form-group all">
                            <div class="checkbox-styled checkbox-inline">
                                <input type="hidden"  name="taxable" value="0">

                                <input type="checkbox" id="taxable" name="taxable" <?php echo $plan->taxable ? 'checked' : ''?> value="1">
                                <label for="taxable"><?php echo lang('is_taxable'); ?></label>
                            </div>
                        </div>

                    
                        <table id='mup' class="table table-striped table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th class="col-md-3"><?php echo lang('Plan');?></th>
                                            <th class="col-md-1"><?php echo lang('Cost');?></th>
                                            <th class="col-md-1"><?php echo lang('Price');?></th>
                                            <th class="col-md-4"><?php echo lang('Duration');?></th>
                                            <th class="col-md-2"><?php echo lang('Activation Spiff');?></th>
                                            <th class="col-md-1"><?php echo lang('Disable');?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if ($this->input->post('name')): ?>
                                            <?php 
                                            $i = sizeof($_POST['name']);
                                            for ($r = 0; $r < $i; $r++) {
                                                $name = escapeStr($_POST['name'][$r]);
                                                $cost = escapeStr($_POST['cost'][$r]);
                                                $price = escapeStr($_POST['price'][$r]);
                                                $duration = escapeStr($_POST['duration'][$r]);
                                                $plan_duration_type = escapeStr($_POST['plan_duration_type'][$r]);
                                                $disable = escapeStr($_POST['disable'][$r]);
                                                $activation_spiff = escapeStr($_POST['activation_spiff'][$r]);

                                            ?>
                                             <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="name[<?php echo $r; ?>]"
                                                               placeholder="<?php echo lang('Plan Name');?>"
                                                               value="<?php echo $name; ?>"/>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                           name="cost[<?php echo $r; ?>]" placeholder="<?php echo lang('Cost');?>"
                                                           value="<?php echo $cost; ?>">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                           name="price[<?php echo $r; ?>]" placeholder="<?php echo lang('Price');?>"
                                                           value="<?php echo $price; ?>">
                                                </td>
                                                <td>
                                                	<div class="col-md-9 row">
                                                		 <input type="text" class="form-control" name="duration[<?php echo $r; ?>]" placeholder="<?php echo lang('Duration');?>"
                                                           value="<?php echo $duration; ?>">
                                                	</div>
                                                	<div class="col-md-3 row">
                                                        <?php
					                                        $plan_duration = array(
					                                        	'days' => lang('Days'),
					                                        	'months' => lang('Months'),
					                                        	'years' => lang('Years'),
					                                        ); 
					                                    ?>
					                                    <?php 
					                                        echo form_dropdown('plan_duration_type['.$r.']', $plan_duration, set_value('plan_duration_type', ($plan_duration_type)),'class="form-control"'); 
					                                    ?>
				                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number" step="any" class="form-control"
                                                           name="activation_spiff[]" placeholder="<?php echo lang('Activation Spiff');?>"
                                                           value="<?php echo $activation_spiff; ?>">
                                                </td>
                                                <td><input type="hidden" class="" name="disable[<?php echo $r;?>]" value="0">
                                                <input type="checkbox" class="checkbox" name="disable[<?php echo $r;?>]" value="1" <?php echo ($disable == 1) ? 'checked' : ''?>></td>
                                            </tr>
                                            <?php
                                            }
                                            ?>
                                        <?php else: ?>
                                            <?php 
                                            $i = 0;
                                               foreach ($plan_items as $item):
                                            ?>
                                                <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" class="plan_name form-control" name="name[<?php echo $i;?>]"
                                                               placeholder="<?php echo lang('Plan Name');?>"
                                                               value="<?php echo escapeStr($item->plan_name) ?>"/>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                           name="cost[<?php echo $i;?>]" placeholder="<?php echo lang('Cost');?>"
                                                           value="<?php echo escapeStr($item->plan_cost) ?>">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                           name="price[<?php echo $i;?>]" placeholder="<?php echo lang('Price');?>"
                                                           value="<?php echo escapeStr($item->plan_price) ?>">
                                                </td>
                                                <td>
                                                    <div class="col-md-9 row">
                                                         <input type="text" class="form-control" name="duration[<?php echo $i; ?>]" placeholder="<?php echo lang('Duration');?>"
                                                           value="<?php echo escapeStr($item->plan_duration) ?>">
                                                    </div>
                                                    <div class="col-md-3 row">
                                                        <?php
                                                            $plan_duration = array(
                                                                'days' => lang('Days'),
                                                                'months' => lang('Months'),
                                                                'years' => lang('Years'),
                                                            ); 
                                                        ?>
                                                        <?php 
                                                            echo form_dropdown('plan_duration_type['.$i.']', $plan_duration, set_value('plan_duration_type', escapeStr($item->plan_duration_type)),'class="form-control"'); 
                                                        ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number" step="any" class="form-control"
                                                           name="activation_spiff[<?php echo $i; ?>]" placeholder="<?php echo lang('Activation Spiff');?>"
                                                           value="<?php echo escapeStr($item->activation_spiff); ?>">
                                                </td>
                                                <td><input type="hidden" class="" name="disable[<?php echo $i;?>]" value="0">
                                                <input type="checkbox" class="checkbox" name="disable[<?php echo $i;?>]" value="1" <?php echo ($item->disable == 1) ? 'checked' : ''?>></td>
                                            </tr>
                                            <?php
                                                $i++;
                                                endforeach;
                                            ?>
                                        <?php endif; ?>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="5">
                                                <a class="btn btn-default btn-xs" href="javascript:void(0);"
                                                   id="addup">
                                                    <i class="fas fa-plus"></i>
                                                    <?php echo lang('Add More...');?>
                                                </a>
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
            </div>
            <div class="col-md-12 row">
                <?php echo form_submit('submit', lang('submit'), 'class="btn btn-primary"'); ?>
            </div>
        </div>
        <?php echo form_close(); ?>
      </div>
</div>

    
<script type="text/javascript">
    $("#addup").on( "click", function(){
        var all = (document.querySelectorAll(".plan_name").length);

        $.post("<?php echo base_url("panel/plans/addmore");?>",
        {
            id: all,
        },
        function(data, status){
            $("#mup tbody").append(data);
        });
    });
    $( document ).ready(function() {
        $('.checkbox').iCheck({
            checkboxClass: 'icheckbox_minimal-grey',
            radioClass: 'iradio_minimal-grey'
        });
    });
</script>

