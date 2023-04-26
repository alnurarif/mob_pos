    <div class="box" id="dripicons-iconz">
        <div class="box-header">
            <div class="box-title"><?php echo lang('Customer Purchase');?> </div>
        </div>
        <div class="box-body">
                <?php echo validation_errors(); ?>
                <?php echo form_open(); ?>
                    <div class="col-md-12">
                        <div class="row">
                           
                            
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <?php echo lang('client_title', 'client_name');?>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-user"></i>
                                        </div>
                                        <?php
                                        $client_dd = array();
                                        foreach ($clients as $client) {
                                            $client_dd[$client->id] =$client->first_name.' '.$client->last_name." ".$client->company;
                                        }
                                        echo form_dropdown('client_name', $client_dd, set_value('client_name', $inv->customer_id), 'class="form-control m-bot15" style="width: 100%"');
                                        ?>
                                        <a class="add_c btn input-group-addon"><i class="fas fa-user-plus"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                       <fieldset>
                           <legend><?php echo lang('Phone Information');?></legend>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <?php echo lang('phone_name', 'phone_name'); ?>
                                    <input type="text" class="form-control" value="<?php echo set_value('phone_name', $inv->phone_name); ?>" name="phone_name">
                                </div>
                            </div>
                               
                                <div class="col-lg-4">
                                    <div class="form-group">
                                         <?php
                                            $cosmetic = array(
                                                '1' => '*',
                                                '2' => '**',
                                                '3' => '***',
                                                '4' => '****',
                                                '5' => '*****',
                                            ); 
                                        ?>
                                        <div class="form-group">
                                            <label><?php echo lang('Cosmetic Condition');?></label>
                                            <?php echo form_dropdown('cosmetic_condition', $cosmetic, set_value('cosmetic_condition', $inv->cosmetic_condition), 'class="form-control" id="cosmetic_condition"'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">

                                    <div class="form-group">
                                        <?php
                                            $operational = array(
                                                '1' => '*',
                                                '2' => '**',
                                                '3' => '***',
                                                '4' => '****',
                                                '5' => '*****',
                                            ); 
                                        ?>
                                        <div class="form-group">
                                            <label><?php echo lang('Operational Condition');?></label>
                                            <?php echo form_dropdown('operational_condition', $cosmetic, set_value('operational_condition', $inv->operational_condition), 'class="form-control" id="operational_condition"'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    
                                    <div class="form-group">
                                        <div class="form-group">
                                            <?php echo lang('p_manufacturer', 'manufacturer');?>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fas  fa-folder"></i>
                                                </div>
                                                <?php
                                                    $models_dd = array(); 
                                                    foreach ($manufacturers as $manufacturer) {
                                                        $models_dd[$manufacturer->id] = $manufacturer->name;
                                                    }
                                                ?>
                                                <?php echo form_dropdown('manufacturer', $models_dd, set_value('manufacturer', $inv->manufacturer_id), 'class="form-control" id="manufacturer"'); ?>
                                                <a class="add_manufacturer btn input-group-addon"><i class="fas fa-plus"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <?php echo lang('p_model', 'p_model'); ?>
                                        <input type="text" class="form-control" value="<?php echo set_value('model', $inv->model_name); ?>" name="model">
                                    </div>
                                </div>
                                
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <?php echo lang('p_max_discount', 'p_max_discount');?>
                                        <div class="input-group">
                                            <input id="max_discount" name="max_discount" value="<?php echo set_value('max_discount', $inv->max_discount); ?>" type="text" class="validate form-control">
                                            <div class="input-group-addon">
                                                <?php
                                                    $dts = array(
                                                        '1' => '%',
                                                        '2' => 'Fixed',
                                                    ); 
                                                ?>
                                                <?php echo form_dropdown('discount_type', $dts, set_value('discount_type', $inv->discount_type), 'class="skip" id="discount_type"'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">

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
                                                <?php echo form_dropdown('carrier', $carriers_dd, set_value('carrier', $inv->carrier_id), 'class="form-control" id="carrier"'); ?>
                                                <a class="add_carrier btn input-group-addon"><i class="fas fa-plus"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <?php
                                        $used_status = array(
                                            '1' => lang('Ready to Sale'),
                                            '2' => lang('Needs Repair'),
                                        ); 
                                    ?>
                                    <div class="form-group">
                                        <label><?php echo lang('Select Phone Status');?></label>
                                        <?php echo form_dropdown('phone_status', $used_status, set_value('phone_status', $inv->used_status), 'class="form-control" id="phone_status"'); ?>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <?php
                                        $unlock_status = array(
                                            '0' => lang('no'),
                                            '1' => lang('yes'),
                                        ); 
                                    ?>
                                    <div class="form-group">
                                        <label><?php echo lang('Unlocked');?></label>
                                        <?php echo form_dropdown('unlock_status', $unlock_status, set_value('unlock_status', $inv->unlocked), 'class="form-control" id="unlock_status"'); ?>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <input type="hidden" name="taxable" value="1">
                                        
                                    </div>
                                   
                                   <div class="form-group all">
                                        <?php
                                            $tax_rates_dd = array(); 
                                            foreach ($tax_rates as $tax) {
                                                $tax_rates_dd[$tax->id] = $tax->name;
                                            }
                                        ?>
                                        <?php echo lang('taxrate_title', 'phone_tax');?>
                                        <?php echo form_dropdown('phone_tax[]', $tax_rates_dd, set_value('phone_tax', explode(',',$inv->tax_id)), 'class="skip form-control" id="phone_tax" multiple'); ?>
                                    </div>
                                </div>
                              
                                <div class="col-lg-8">
                            
                                    <table id='mup' class="table table-striped table-bordered table-hover">
                                                <thead>
                                                <tr>
                                                    <th><?php echo lang('Serial/IMEI Number(s)');?></th>
                                                    <th><?php echo lang('Price Cost');?></th>
                                                    <th colspan="2"><?php echo lang('List Price');?></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php if ($this->input->post('imei')): ?>
                                                    <?php 
                                                    $i = sizeof($_POST['imei']);
                                                    for ($r = 0; $r < $i; $r++) {
                                                        $imei = escapeStr($_POST['imei'][$r]);
                                                        $cost = escapeStr($_POST['purchase_price'][$r]);
                                                        $price = escapeStr($_POST['list_price'][$r]);
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="imei[]"
                                                                       placeholder="<?php echo lang('Serial/IMEI number');?>"
                                                                       value="<?php echo $imei; ?>"/>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="purchase_price[]" placeholder="<?php echo lang('Price Cost');?>"
                                                                   value="<?php echo $cost; ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="list_price[]"
                                                                   placeholder="<?php echo lang('List Price');?>"
                                                                   value="<?php echo $price; ?>">
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                    <?php
                                                    }
                                                    ?>
                                                <?php else: ?>
                                                    <?php 
                                                        if($inv_items):
                                                       foreach ($inv_items as $item):
                                                    ?>
                                                        <tr>
                                                            <td>
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" name="imei[]"
                                                                           placeholder="<?php echo lang('Serial/IMEI number');?>"
                                                                           value="<?php echo escapeStr($item->imei); ?>"/>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                       name="purchase_price[]" placeholder="<?php echo lang('Price Cost');?>"
                                                                       value="<?php echo escapeStr($item->cost); ?>">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name="list_price[]"
                                                                       placeholder="<?php echo lang('List Price');?>"
                                                                       value="<?php echo escapeStr($item->price); ?>">
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    <?php
                                                        endforeach;
                                                        endif;
                                                    ?>
                                                <?php endif; ?>
                                                
                                                </tbody>
                                                <tfoot>
                                                
                                                </tfoot>
                                            </table>
                                </div>
                                
                       </fieldset>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo lang('Note');?></label>
                            <textarea name="description" class="form-control" rows="5"><?php echo set_value('description', $inv->description); ?></textarea>
                        </div>
                        <?php echo form_submit('submit', lang('Submit'), 'class="btn btn-primary"'); ?>
                    </div>
                </div>
                
                <?php echo form_close(); ?>
            </div>
        </div>        

<script type="text/javascript">
    $("#addupimei").on("click", function () {
            var url = "<?php echo base_url("panel/phones/addmore");?>";
            $.get(url, {}, function (data) {
                $("#mup tbody").append(data);
            });
        });
        $(document).on('click', '.imei_delete_item', function (e) {
            var id = $(this).data("id");
            $("#" + id).remove();
        });
    $('select').not('.skip').select2();
    jQuery('.inp_cat').hide();
    jQuery("#p_carrier").on("select2:select", function (e) {
        var selected = jQuery("#p_carrier").val();
        if(selected === 'other') {
            jQuery('.select_cat').hide();
            jQuery('.inp_cat').show();
            jQuery('#carrier_input').val('');
            jQuery('#carrier_input').focus();
        }
        else
        {
            jQuery('#p_carrier').val(selected);
        }
    });
</script>

