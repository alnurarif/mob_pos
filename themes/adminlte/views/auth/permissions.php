<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
    .table td:first-child {
        font-weight: bold;
    }

    label {
        margin-right: 10px;
    }
    
</style>


<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('set_permissions');?></h3>
      <div class="box-tools pull-right">
      </div>
  </div>
  <div class="box-body">
    
            <?php if (!empty($p)) {
                if ($p->group_id != 1) {
                echo form_open("panel/auth/permissions/" . $id); 
            ?>
                <div class="table-responsive">
                    <table id="perms" class="table table-bordered table-hover table-striped">

                        <thead>
                        <tr>
                            <th colspan="6"
                                class="text-center"><?php echo escapeStr($group->description ). ' ( ' . escapeStr($group->name) . ' ) ' . $this->lang->line("group_permissions"); ?></th>
                        </tr>
                        
                        <tr>
                            <th class="text-center">Activity</th>
                            <th class="text-center"><?php echo lang("view"); ?></th>
                            <th class="text-center"><?php echo lang("add"); ?></th>
                            <th class="text-center"><?php echo lang("edit"); ?></th>
                            <th class="text-center"><?php echo lang("delete"); ?></th>
                            <th class="text-center"><?php echo lang("misc"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                           <tr>
                               
                                <td colspan="6"><center><h3><?php echo lang('General Permissions');?></h3></center></td>
                            </tr>
                            <tr>
                                <td><?php echo lang('Repairs');?></td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="repair-index" <?php echo $p->{'repair-index'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="repair-add" <?php echo $p->{'repair-add'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="repair-edit" <?php echo $p->{'repair-edit'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                  <input type="checkbox" value="1" class="checkbox" name="repair-delete" <?php echo $p->{'repair-delete'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                    
                                </td>
                            </tr>

                            <tr>
                                <td><?php echo lang('Customers');?></td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="customers-index" <?php echo $p->{'customers-index'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="customers-add" <?php echo $p->{'customers-add'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="customers-edit" <?php echo $p->{'customers-edit'} ? "checked" : ''; ?>>
                                </td>
                                
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" name="customers-delete" <?php echo $p->{'customers-delete'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                    <input type="checkbox" value="1" id="customers-internal_notes" class="checkbox" name="customers-internal_notes" <?php echo $p->{'customers-internal_notes'} ? "checked" : ''; ?>>
                                    <label for="customers-internal_notes" class="padding05"><?php echo lang('Internal Notes');?></label>

                                    <input type="checkbox" value="1" id="customers-activities" class="checkbox" name="customers-activities" <?php echo $p->{'customers-activities'} ? "checked" : ''; ?>>
                                    <label for="customers-activities" class="padding05"><?php echo lang('Activities');?></label>

                                    <input type="checkbox" value="1" id="customers-documents" class="checkbox" name="customers-documents" <?php echo $p->{'customers-documents'} ? "checked" : ''; ?>>
                                    <label for="customers-documents" class="padding05"><?php echo lang('Documents');?></label>

                                    <input type="checkbox" value="1" id="customers-purchase_history" class="checkbox" name="customers-purchase_history" <?php echo $p->{'customers-purchase_history'} ? "checked" : ''; ?>>
                                    <label for="customers-purchase_history" class="padding05"><?php echo lang('Purchase History');?></label>
                                </td>
                            </tr>

                            <tr>
                                <td><?php echo lang('POS');?></td>
                                <td class="text-center">
                                </td>
                                <td class="text-center">
                                </td>
                                <td class="text-center">
                                </td>
                                <td class="text-center">
                                </td>
                                <td>

                                    <input type="checkbox" value="1" id="pos-index" class="checkbox" name="pos-index" <?php echo $p->{'pos-index'} ? "checked" : ''; ?>>
                                    <label for="pos-index" class="padding05"><?php echo lang('Use POS');?></label>


                                    <input type="checkbox" value="1" id="pos-add_discounts" class="checkbox" name="pos-add_discounts" <?php echo $p->{'pos-add_discounts'} ? "checked" : ''; ?>>
                                    <label for="pos-add_discounts" class="padding05"><?php echo lang('Do Discounts');?></label>


                                    <input type="checkbox" value="1" id="pos-checkout_negative" class="checkbox" name="pos-checkout_negative" <?php echo $p->{'pos-checkout_negative'} ? "checked" : ''; ?>>
                                    <label for="pos-checkout_negative" class="padding05"><?php echo lang('Checkout Negative Totals');?></label>

                                    <input type="checkbox" value="1" id="pos-purchase_phones" class="checkbox" name="pos-purchase_phones" <?php echo $p->{'pos-purchase_phones'} ? "checked" : ''; ?>>
                                    <label for="pos-purchase_phones" class="padding05"> <?php echo lang('Purchase Phones');?></label>

                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="sales-refund" name="sales-refund" <?php echo $p->{'sales-refund'} ? "checked" : ''; ?>>
                                        <label for="sales-refund" class="padding05"><?php echo lang('Refund Sales');?></label>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo lang('Repair Parts');?></td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="inventory-index" <?php echo $p->{'inventory-index'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="inventory-add" <?php echo $p->{'inventory-add'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="inventory-edit" <?php echo $p->{'inventory-edit'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="inventory-delete" <?php echo $p->{'inventory-delete'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                    <input type="checkbox" value="1" id="inventory-manage_stock" class="checkbox" name="inventory-manage_stock" <?php echo $p->{'inventory-manage_stock'} ? "checked" : ''; ?>>
                                    <label for="inventory-manage_stock" class="padding05"><?php echo lang('Manage Stock');?></label>
                                </td>
                            </tr>

                            <tr>
                                <td><?php echo lang('New Phones');?></td>
                                <td class="text-center">
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="phones-add_new" <?php echo $p->{'phones-add_new'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="phones-edit_new" <?php echo $p->{'phones-edit_new'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="phones-delete_new" <?php echo $p->{'phones-delete_new'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                   
                                </td>
                            </tr>

                            <tr>
                                <td><?php echo lang('Used Phones');?></td>
                                <td class="text-center">
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="phones-add_used" <?php echo $p->{'phones-add_used'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="phones-edit_used" <?php echo $p->{'phones-edit_used'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="phones-delete_used" <?php echo $p->{'phones-delete_used'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                   
                                </td>
                            </tr>

                             <tr>
                                <td><?php echo lang('Accessory');?></td>
                                <td class="text-center">
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="accessory-add" <?php echo $p->{'accessory-add'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="accessory-edit" <?php echo $p->{'accessory-edit'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="accessory-delete" <?php echo $p->{'accessory-delete'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                    <input type="checkbox" value="1" id="accessory-manage_stock" class="checkbox" name="accessory-manage_stock" <?php echo $p->{'accessory-manage_stock'} ? "checked" : ''; ?>>
                                    <label for="accessory-manage_stock" class="padding05"><?php echo lang('Manage Stock');?></label>
                                </td>
                            </tr>

                            <tr>
                                <td><?php echo lang('Plans');?></td>
                                <td class="text-center">
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="plans-add" <?php echo $p->{'plans-add'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="plans-edit" <?php echo $p->{'plans-edit'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                     <input type="checkbox" value="1" class="checkbox" name="plans-delete" <?php echo $p->{'plans-delete'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                   
                                </td>
                            </tr>


                            <tr>
                                <td><?php echo lang('Other Products');?></td>
                                <td class="text-center">
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="other-add" <?php echo $p->{'other-add'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="other-edit" <?php echo $p->{'other-edit'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="other-delete" <?php echo $p->{'other-delete'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                    <input type="checkbox" value="1" id="other-manage_stock" class="checkbox" name="other-manage_stock" <?php echo $p->{'other-manage_stock'} ? "checked" : ''; ?>>
                                    <label for="other-manage_stock" class="padding05"><?php echo lang('Manage Stock');?></label>
                                </td>
                            </tr>


                            <tr>
                                <td><?php echo lang('Purchases');?></td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="purchases-index" <?php echo $p->{'purchases-index'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="purchases-add" <?php echo $p->{'purchases-add'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="purchases-edit" <?php echo $p->{'purchases-edit'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="purchases-delete" <?php echo $p->{'purchases-delete'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                    <input type="checkbox" value="1" id="purchases-return_purchase" class="checkbox" name="purchases-return_purchase" <?php echo $p->{'purchases-return_purchase'} ? "checked" : ''; ?>>
                                    <label for="purchases-return_purchase" class="padding05"><?php echo lang('Return Purchases (RMA)');?></label>
                                </td>
                            </tr>


                             <tr>
                                <td><?php echo lang('Customer Purchases');?></td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="purchases-customer" <?php echo $p->{'purchases-customer'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="purchases-customer_add" <?php echo $p->{'purchases-customer_add'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="purchases-customer_edit" <?php echo $p->{'purchases-customer_edit'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="purchases-customer_delete" <?php echo $p->{'purchases-customer_delete'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                </td>
                            </tr>


                            
                           
                             
                            <tr>
                                <td><?php echo lang('Suppliers');?></td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="settings-suppliers" <?php echo $p->{'settings-suppliers'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="suppliers-add" <?php echo $p->{'suppliers-add'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="suppliers-edit" <?php echo $p->{'suppliers-edit'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="suppliers-delete" <?php echo $p->{'suppliers-delete'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                </td>
                            </tr>

                            <tr>
                                <td><?php echo lang('Tax Rates');?></td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="settings-tax_rates" <?php echo $p->{'settings-tax_rates'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="tax_rates-add" <?php echo $p->{'tax_rates-add'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="tax_rates-edit" <?php echo $p->{'tax_rates-edit'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    
                                </td>
                                <td>
                                </td>
                            </tr>

                            <tr>
                                <td><?php echo lang('Manufacturers');?></td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="settings-manufacturers" <?php echo $p->{'settings-manufacturers'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="manufacturers-add" <?php echo $p->{'manufacturers-add'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="manufacturers-edit" <?php echo $p->{'manufacturers-edit'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                </td>
                                <td>
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo lang('Carriers');?></td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="settings-carriers" <?php echo $p->{'settings-carriers'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="carriers-add" <?php echo $p->{'carriers-add'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="carriers-edit" <?php echo $p->{'carriers-edit'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                </td>
                                <td>
                                </td>
                            </tr>

                            <tr>
                                <td><?php echo lang('Users');?></td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="auth-index" <?php echo $p->{'auth-index'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="auth-create_user" <?php echo $p->{'auth-create_user'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="auth-edit_user" <?php echo $p->{'auth-edit_user'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="auth-delete_user" <?php echo $p->{'auth-delete_user'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                </td>
                            </tr>

                            <tr>
                                <td><?php echo lang('Groups');?></td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="auth-user_groups" <?php echo $p->{'auth-user_groups'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="auth-create_group" <?php echo $p->{'auth-create_group'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="auth-edit_group" <?php echo $p->{'auth-edit_group'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="auth-delete_group" <?php echo $p->{'auth-delete_group'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                    <input type="checkbox" value="1" id="auth-permissions" class="checkbox" name="auth-permissions" <?php echo $p->{'auth-permissions'} ? "checked" : ''; ?>>
                                    <label for="auth-permissions" class="padding05"><?php echo lang('Edit Permissions');?></label>
                                </td>
                            </tr>
                            
                           <thead>
                            <tr>
                               
                                <th colspan="6"><center><h3><?php echo lang('System Settings Permissions');?></h3></center></th>
                               </tr></thead>

                            <tr>
                                <td><?php echo lang('General Settings');?></td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="general_settings" name="settings-general_settings" <?php echo $p->{'settings-general_settings'} ? "checked" : ''; ?>>
                                </td>
                                <td></td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="general_settings_edit" name="settings-general_settings_edit" <?php echo $p->{'settings-general_settings_edit'} ? "checked" : ''; ?>>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>

                           <!--  <tr>
                                <td>Default Taxes</td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="default_taxes" name="settings-default_taxes" <?php echo $p->{'settings-default_taxes'} ? "checked" : ''; ?>>
                                </td>
                                <td></td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="default_taxes_edit" name="settings-default_taxes_edit" <?php echo $p->{'settings-default_taxes_edit'} ? "checked" : ''; ?>>
                                </td>
                                <td></td>
                                <td></td>
                            </tr> -->

                            <tr>
                                <td><?php echo lang('Orders &amp; Repairs');?></td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="order_repairs" name="settings-order_repairs" <?php echo $p->{'settings-order_repairs'} ? "checked" : ''; ?>>
                                </td>
                                <td></td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="order_repairs_edit" name="settings-order_repairs_edit" <?php echo $p->{'settings-order_repairs_edit'} ? "checked" : ''; ?>>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td><?php echo lang('Quote');?></td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="quote" name="settings-quote" <?php echo $p->{'settings-quote'} ? "checked" : ''; ?>>
                                </td>
                                <td></td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="quote_edit" name="settings-quote_edit" <?php echo $p->{'settings-quote_edit'} ? "checked" : ''; ?>>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td><?php echo lang('SMS');?></td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="sms" name="settings-sms" <?php echo $p->{'settings-sms'} ? "checked" : ''; ?>>
                                </td>
                                <td></td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="sms_edit" name="settings-sms_edit" <?php echo $p->{'settings-sms_edit'} ? "checked" : ''; ?>>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td><?php echo lang('POS Configuration');?></td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="pos_configuration" name="settings-pos_configuration" <?php echo $p->{'settings-pos_configuration'} ? "checked" : ''; ?>>
                                </td>
                                <td></td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="pos_configuration_edit" name="settings-pos_configuration_edit" <?php echo $p->{'settings-pos_configuration_edit'} ? "checked" : ''; ?>>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td><?php echo lang('Importer');?></td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="import" name="settings-import" <?php echo $p->{'settings-import'} ? "checked" : ''; ?>>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>



                            <tr>
                                <td><?php echo lang('Stores');?></td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="store_index" name="store-index" <?php echo $p->{'store-index'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="store_add" name="store-add" <?php echo $p->{'store-add'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="store_edit" name="store-edit" <?php echo $p->{'store-edit'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="store_edit" name="store-delete" <?php echo $p->{'store-delete'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="store-disable" name="store-disable" <?php echo $p->{'store-disable'} ? "checked" : ''; ?>>
                                        <label for="store-disable" class="padding05"><?php echo lang('Lock/Unlock Store');?></label>
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td><?php echo lang('Activities');?></td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="activities-activities" name="settings-activities" <?php echo $p->{'settings-activities'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="activities-add" name="activities-add" <?php echo $p->{'activities-add'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="activities-edit" name="activities-edit" <?php echo $p->{'activities-edit'} ? "checked" : ''; ?>>
                                </td>
                                <td></td>
                                <td>
                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="activities-disable" name="activities-disable" <?php echo $p->{'activities-disable'} ? "checked" : ''; ?>>
                                        <label for="activities-disable" class="padding05"><?php echo lang('Disable/Enable Activities');?></label>
                                    </span>
                                     <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="reports-activities" name="reports-activities" <?php echo $p->{'reports-activities'} ? "checked" : ''; ?>>
                                        <label for="reports-activities" class="padding05"><?php echo lang('Activities Viewer');?></label>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo lang('Categories');?></td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="categories-categories" name="settings-categories" <?php echo $p->{'settings-categories'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="categories-add" name="categories-add" <?php echo $p->{'categories-add'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                    <input type="checkbox" value="1" class="checkbox" id="categories-edit" name="categories-edit" <?php echo $p->{'categories-edit'} ? "checked" : ''; ?>>
                                </td>
                                <td></td>
                                <td>
                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="categories-disable" name="categories-disable" <?php echo $p->{'categories-disable'} ? "checked" : ''; ?>>
                                        <label for="categories-disable" class="padding05"><?php echo lang('Disable/Enable Categories');?></label>
                                    </span>
                                     <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="reports-categories" name="reports-categories" <?php echo $p->{'reports-categories'} ? "checked" : ''; ?>>
                                        <label for="reports-activities" class="padding05"><?php echo lang('Categories Viewer');?></label>
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td><?php echo lang('payroll');?></td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="payroll-index" <?php echo $p->{'payroll-index'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="payroll-add" <?php echo $p->{'payroll-add'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="payroll-edit" <?php echo $p->{'payroll-edit'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="payroll-delete" <?php echo $p->{'payroll-delete'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="payroll-view" name="payroll-view" <?php echo $p->{'payroll-view'} ? "checked" : ''; ?>>
                                        <label for="payroll-view" class="padding05">View Payroll</label>
                                    </span>
                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="payroll-payslip" name="payroll-payslip" <?php echo $p->{'payroll-payslip'} ? "checked" : ''; ?>>
                                        <label for="payroll-payslip" class="padding05">Print Payslip</label>
                                    </span>
                                </td>
                            </tr>


                            <tr>
                                <td><?php echo lang('payroll_templates');?></td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="payroll-templates" <?php echo $p->{'payroll-templates'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="payroll-add_template" <?php echo $p->{'payroll-add_template'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="payroll-template" <?php echo $p->{'payroll-template'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="payroll-delete_template" <?php echo $p->{'payroll-delete_template'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                    
                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="payroll-setDefaultTemplate" name="payroll-setDefaultTemplate" <?php echo $p->{'payroll-setDefaultTemplate'} ? "checked" : ''; ?>>
                                        <label for="payroll-setDefaultTemplate" class="padding05">Set Default Template</label>
                                    </span>
                                </td>
                            </tr>


                            <tr>
                                <td><?php echo lang('Bank Account');?></td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="accounts-index" <?php echo $p->{'accounts-index'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="accounts-add_bank" <?php echo $p->{'accounts-add_bank'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="accounts-edit_bank" <?php echo $p->{'accounts-edit_bank'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="accounts-delete_bank" <?php echo $p->{'accounts-delete_bank'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                   
                                </td>
                            </tr>

                            <tr>
                                <td><?php echo lang('Expenses');?></td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="expense-index" <?php echo $p->{'expense-index'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="expense-add" <?php echo $p->{'expense-add'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="expense-edit" <?php echo $p->{'expense-edit'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="expense-delete" <?php echo $p->{'expense-delete'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                   
                                </td>
                            </tr>

                            <tr>
                                <td><?php echo lang('Deposits');?></td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="deposits-index" <?php echo $p->{'deposits-index'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="deposits-add" <?php echo $p->{'deposits-add'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="deposits-edit" <?php echo $p->{'deposits-edit'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="deposits-delete" <?php echo $p->{'deposits-delete'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                   
                                </td>
                            </tr>

                            <tr>
                                <td><?php echo lang('expense_type');?></td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="expense_type-index" <?php echo $p->{'expense_type-index'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="expense_type-add" <?php echo $p->{'expense_type-add'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="expense_type-edit" <?php echo $p->{'expense_type-edit'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="expense_type-delete" <?php echo $p->{'expense_type-delete'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                   
                                </td>
                            </tr>

                            <tr>
                                <td><?php echo lang('deposit_type');?></td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="deposit_type-index" <?php echo $p->{'deposit_type-index'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="deposit_type-add" <?php echo $p->{'deposit_type-add'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="deposit_type-edit" <?php echo $p->{'deposit_type-edit'} ? "checked" : ''; ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" value="1" class="checkbox" name="deposit_type-delete" <?php echo $p->{'deposit_type-delete'} ? "checked" : ''; ?>>
                                </td>
                                <td>
                                   
                                </td>
                            </tr>

                            
                            <thead>
                            <tr>
                               
                                <th colspan="6"><center>
                                  <h3><?php echo lang('Reporting Payments &amp; Dashboard');?></h3></center></th>
                                </tr></thead>
                            <tr>
                                <td><?php echo lang('View Reports');?></td>
                                <td colspan="5">
                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="stock" name="reports-stock" <?php echo $p->{'reports-stock'} ? "checked" : ''; ?>>
                                        <label for="stock" class="padding05"><?php echo lang('Stock Chart');?></label>
                                    </span>

                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="finance" name="reports-finance" <?php echo $p->{'reports-finance'} ? "checked" : ''; ?>>
                                        <label for="finance" class="padding05"><?php echo lang('Finance Chart');?></label>
                                    </span>

                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="sales" name="reports-sales" <?php echo $p->{'reports-sales'} ? "checked" : ''; ?>>
                                        <label for="sales" class="padding05"><?php echo lang('Sales Report');?></label>
                                    </span>

                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="sales-return_sales" name="sales-return_sales" <?php echo $p->{'sales-return_sales'} ? "checked" : ''; ?>>
                                        <label for="sales-return_sales" class="padding05"><?php echo lang('View Refunds');?></label>
                                    </span>

                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="profit" name="reports-profit" <?php echo $p->{'reports-profit'} ? "checked" : ''; ?>>
                                        <label for="profit" class="padding05"><?php echo lang('Profit Report');?></label>
                                    </span>

                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="tax" name="reports-tax" <?php echo $p->{'reports-tax'} ? "checked" : ''; ?>>
                                        <label for="tax" class="padding05"><?php echo lang('Tax Report');?></label>
                                    </span>

                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="reports_vendor_purchases" name="reports-vendor_purchases" <?php echo $p->{'reports-vendor_purchases'} ? "checked" : ''; ?>>
                                        <label for="reports_vendor_purchases" class="padding05"><?php echo lang('Vendor Purchases Report');?></label>
                                    </span>

                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="reports_customer_purchases" name="reports-customer_purchases" <?php echo $p->{'reports-customer_purchases'} ? "checked" : ''; ?>>
                                        <label for="reports_customer_purchases" class="padding05"><?php echo lang('Customer Purchases Report');?></label>
                                    </span>

                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="reports_drawer" name="reports-drawer" <?php echo $p->{'reports-drawer'} ? "checked" : ''; ?>>
                                        <label for="reports_drawer" class="padding05"><?php echo lang('Drawer Report');?></label>
                                    </span>

                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="gl" name="reports-gl" <?php echo $p->{'reports-gl'} ? "checked" : ''; ?>>
                                        <label for="gl" class="padding05"><?php echo lang('G/L Report');?></label>
                                    </span>

                                   
                                </td>
                            </tr>

                            
                             <tr>
                                <td><?php echo lang('Dashboard Widgets');?></td>
                                <td colspan="5">
                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="welcome-pendingrepairs" name="welcome-pendingrepairs" <?php echo $p->{'welcome-pendingrepairs'} ? "checked" : ''; ?>>
                                        <label for="welcome-pendingrepairs" class="padding05"><?php echo lang('Pending Repairs');?></label>
                                    </span>

                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="welcome-completedseven" name="welcome-completedseven" <?php echo $p->{'welcome-completedseven'} ? "checked" : ''; ?>>
                                        <label for="welcome-completedseven" class="padding05"><?php echo lang('Repairs Completed in Last 7 Days');?></label>
                                    </span>

                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="welcome-completedthirty" name="welcome-completedthirty" <?php echo $p->{'welcome-completedthirty'} ? "checked" : ''; ?>>
                                        <label for="welcome-completedthirty" class="padding05"><?php echo lang('Repairs Completed in Last 30 Days');?></label>
                                    </span>

                                      <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="welcome-revenuechart" name="welcome-revenuechart" <?php echo $p->{'welcome-revenuechart'} ? "checked" : ''; ?>>
                                        <label for="welcome-revenuechart" class="padding05"><?php echo lang('Revenue Chart');?></label>
                                    </span>

                                      <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="welcome-stockchart" name="welcome-stockchart" <?php echo $p->{'welcome-stockchart'} ? "checked" : ''; ?>>
                                        <label for="welcome-stockchart" class="padding05"><?php echo lang('Stock Chart');?></label>
                                    </span>

                                     <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="welcome-quickmail" name="welcome-quickmail" <?php echo $p->{'welcome-quickmail'} ? "checked" : ''; ?>>
                                        <label for="welcome-quickmail" class="padding05"><?php echo lang('Quick Email');?></label>
                                    </span>

                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="welcome-lookup_repair" name="welcome-lookup_repair" <?php echo $p->{'welcome-lookup_repair'} ? "checked" : ''; ?>>
                                        <label for="welcome-lookup_repair" class="padding05"><?php echo lang('Repair Lookup');?></label>
                                    </span>

                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="welcome-lookup_sale" name="welcome-lookup_sale" <?php echo $p->{'welcome-lookup_sale'} ? "checked" : ''; ?>>
                                        <label for="welcome-lookup_sale" class="padding05"><?php echo lang('Sale Lookup');?></label>
                                    </span>
                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="welcome-lookup_customer" name="welcome-lookup_customer" <?php echo $p->{'welcome-lookup_customer'} ? "checked" : ''; ?>>
                                        <label for="welcome-lookup_customer" class="padding05"><?php echo lang('Customer Lookup');?></label>
                                    </span>
                                    
                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="welcome-commission_today" name="welcome-commission_today" <?php echo $p->{'welcome-commission_today'} ? "checked" : ''; ?>>
                                        <label for="welcome-commission_today" class="padding05"><?php echo lang('Commission Today');?></label>
                                    </span>
                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="welcome-commission_week" name="welcome-commission_week" <?php echo $p->{'welcome-commission_week'} ? "checked" : ''; ?>>
                                        <label for="welcome-commission_week" class="padding05"><?php echo lang('Commission - this week');?></label>
                                    </span>
                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="welcome-commission_month" name="welcome-commission_month" <?php echo $p->{'welcome-commission_month'} ? "checked" : ''; ?>>
                                        <label for="welcome-commission_month" class="padding05"><?php echo lang('Commission - this month');?></label>
                                    </span>
                                    <span style="inline-block">
                                        <input type="checkbox" value="1" class="checkbox" id="welcome-calculator" name="welcome-calculator" <?php echo $p->{'welcome-calculator'} ? "checked" : ''; ?>>
                                        <label for="welcome-calculator" class="padding05"><?php echo lang('Calculator');?></label>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo lang('Recent Sales');?></td>
                                <td colspan="5">
                                    <div class="col-lg-12">
                                        <div class="col-lg-12">
                                            <span style="inline-block">
                                                <input type="checkbox" <?php echo $p->{'recent_sales-viewall'} ? "checked" : ''; ?> value="1" class="checkbox" id="recent_sales-viewall" name="recent_sales-viewall">
                                                <label for="recent_sales-viewall" class="padding05"><?php echo lang('View All Stores Sales');?></label>
                                            </div>
                                        </div>
                                        <div style="<?php echo $p->{'recent_sales-viewall'} ? "display:none" : ''; ?>" class="col-lg-6" id="stores_rsales_div">
                                            <div class="well well-sm">
                                                
                                            <label for="recent_sales-stores" class="padding05"><?php echo sprintf(lang('What stores recent sales should (%s) be able to view.'), $group->name);?></label><br>
                                            <div class="inline-block">
                                                <label for="recent_sales-ownstore" class="padding05"><?php echo lang('Always View Store Logged Into');?></label>
                                                <input type="checkbox" value="1" class="checkbox" id="recent_sales-ownstore" name="recent_sales-ownstore" <?php echo $p->{'recent_sales-ownstore'} ? "checked" : ''; ?>>
                                            </div>
                                            <div style="<?php echo $p->{'recent_sales-ownstore'} ? "display:none" : ''; ?>" id="stores_rsales">
                                                <?php foreach ($this->mStores as $store): ?>
                                                    <div class="inline-block">
                                                         <label for="recent_sales-stores<?php echo $store['id']; ?>" class="padding05"><?php echo $store['name']; ?></label>
                                                        <input type="checkbox" value="<?php echo $store['id']; ?>" class="checkbox" id="recent_sales-stores<?php echo $store['id']; ?>" name="recent_sales-stores[]" <?php echo in_array($store['id'], explode(',', $p->{'recent_sales-stores'})) ? "checked" : ''; ?>>
                                                    </div>                           
                                                <?php endforeach; ?>
                                            </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <thead>

                            <tr>
                               
                                <th colspan="6"><center><h3><?php echo lang('Commission Settings');?></h3></center></th>
                                </tr></thead>
                          <tr>
                                <td><?php echo lang('Commission Plans');?></td>

                                <td colspan="5">
                                    <div class="col-lg-12">
                                        <div class="col-lg-12">
                                            <span style="inline-block">
                                                    <input type="checkbox" <?php echo $p->{'commission-index'} ? "checked" : ''; ?> value="1" class="checkbox" id="commission-index" name="commission-index">
                                                    <label for="commission-index" class="padding05"><?php echo lang('View Commission Plans');?></label>
                                            </span>
                                            <span style="inline-block">
                                                    <input type="checkbox" <?php echo $p->{'commission-add'} ? "checked" : ''; ?> value="1" class="checkbox" id="commission-add" name="commission-add">
                                                    <label for="commission-add" class="padding05"><?php echo lang('Add Plans');?></label>
                                            </span>
                                             <span style="inline-block">
                                                    <input type="checkbox" <?php echo $p->{'commission-edit'} ? "checked" : ''; ?> value="1" class="checkbox" id="commission-edit" name="commission-edit">
                                                    <label for="commission-edit" class="padding05"><?php echo lang('Edit Plans');?></label>
                                            </span>
                                            <span style="inline-block">
                                                    <input type="checkbox" <?php echo $p->{'commission-disable'} ? "checked" : ''; ?> value="1" class="checkbox" id="commission-disable" name="commission-disable">
                                                    <label for="commission-disable" class="padding05"><?php echo lang('Disable Plans');?></label>
                                            </span>
                                            <span style="inline-block">
                                                    <input type="checkbox" <?php echo $p->{'commission-assign'} ? "checked" : ''; ?> value="1" class="checkbox" id="commission-assign" name="commission-assign">
                                                    <label for="commission-assign" class="padding05"><?php echo lang('Assign Commission Plan');?></label>
                                            </span>
                                        </div></div></td></tr>
                                           <tr>
                                <td><?php echo lang('Product Commissions');?></td>

                                <td colspan="5">
                                    <div class="col-lg-12">
                                        <div class="col-lg-12">
                                            <span style="inline-block">
                                                    <input type="checkbox" <?php echo $p->{'commission-product'} ? "checked" : ''; ?> value="1" class="checkbox" id="commission-product" name="commission-product">
                                                    <label for="commission-product" class="padding05"><?php echo lang('View Product Level Commissions');?></label>
                                            </span>
                                             <span style="inline-block">
                                                    <input type="checkbox" <?php echo $p->{'commission-edit_product'} ? "checked" : ''; ?> value="1" class="checkbox" id="commission-edit_product" name="commission-edit_product">
                                                    <label for="commission-edit_product" class="padding05"><?php echo lang('Edit Product Commissions');?></label>
                                            </span>
                                             <span style="inline-block">
                                                    <input type="checkbox" <?php echo $p->{'commission-delete_product'} ? "checked" : ''; ?> value="1" class="checkbox" id="commission-delete_product" name="commission-delete_product">
                                                    <label for="commission-delete_product" class="padding05"><?php echo lang('Delete Product Commissions');?></label>
                                            </span>
                                            </div></div></td></tr>
                                           <tr>
                                <td><?php echo lang('Category Commissions');?></td>

                                <td colspan="5">
                                    <div class="col-lg-12">
                                        <div class="col-lg-12">
                                            <span style="inline-block">
                                                    <input type="checkbox" <?php echo $p->{'commission-category'} ? "checked" : ''; ?> value="1" class="checkbox" id="commission-category" name="commission-category">
                                                    <label for="commission-category" class="padding05"><?php echo lang('View Category Commissions');?></label>
                                            </span>
                                             <span style="inline-block">
                                                    <input type="checkbox" <?php echo $p->{'commission-edit_category'} ? "checked" : ''; ?> value="1" class="checkbox" id="commission-edit_category" name="commission-edit_category">
                                                    <label for="commission-edit_category" class="padding05"><?php echo lang('Edit Category Commissions');?></label>
                                            </span>
                                             <span style="inline-block">
                                                    <input type="checkbox" <?php echo $p->{'commission-delete_category'} ? "checked" : ''; ?> value="1" class="checkbox" id="commission-delete_category" name="commission-delete_category">
                                                    <label for="commission-delete_category" class="padding05"><?php echo lang('Delete Category Commissions');?></label>
                                            </span>
                                            </div></div></td></tr>
                                           <tr>
                                <td><?php echo lang('Commission Reports');?></td>

                                <td colspan="5">
                                    <div class="col-lg-12">
                                        <div class="col-lg-12">

                                            <span style="inline-block">
                                                <input type="checkbox" value="<?php echo $p->{'group_id'}; ?>" class="checkbox" id="commission-groups" name="commission-groups[]" <?php echo in_array($p->{'group_id'}, explode(',', $p->{'commission-groups'})) ? "checked" : ''; ?>>
                                                <label for="commission-groups" class="padding05"><?php echo sprintf(lang('Can View Others Within (%s) Group'), escapeStr($group->name));?></label>
                                            </span>
                                            <span style="inline-block">
                                                    <input type="checkbox" <?php echo $p->{'commission-view_all'} ? "checked" : ''; ?> value="1" class="checkbox" id="commission-view_all" name="commission-view_all">
                                                    <label for="commission-view_all" class="padding05"><?php echo lang('View All Records');?></label>
                                      </div>
                                            </span>
                                  </div>
                                            
                                        <div class="col-lg-6">
                                            <div class="well well-sm">
                                            <label for="commission-groups" class="padding05"><?php echo lang('What other user groups can they see?');?></label>
                                            <?php 
                                                $groups = $this->ion_auth->groups()->result();
                                                foreach ($groups as $group_): 
                                            ?>
                                                <?php if($p->{'group_id'} == $group_->id) continue; ?>
                                                     <div class="inline-block">
                                                         <label for="commission-groups<?php echo $group_->id; ?>" class="padding05"><?php echo escapeStr($group_->description); ?></label>
                                                        <input type="checkbox" value="<?php echo $group_->id; ?>" class="checkbox" id="commission-groups<?php echo $group_->id; ?>" name="commission-groups[]" <?php echo in_array($group_->id, explode(',', $p->{'commission-groups'})) ? "checked" : ''; ?>>
                                                    </div> 
                                            <?php endforeach; ?>
                                            </div>
                                            
                                        </div>
                                        

                                        <div class="col-lg-6">
                                            <div class="well well-sm">
                                                
                                            <label for="commission-stores" class="padding05"><?php echo sprintf(lang('What stores commissions reports should (%s) be able to view'),$group->name);?></label><br>
                                            <div class="inline-block">
                                                <label for="commission-ownstore" class="padding05"><?php echo lang('Always View Store Logged Into');?></label>
                                                <input type="checkbox" value="1" class="checkbox" id="commission-ownstore" name="commission-ownstore"  <?php echo $p->{'commission-ownstore'} ? "checked" : ''; ?>>
                                            </div>
                                            <div style="
                                            <?php echo $p->{'commission-ownstore'} ? 'display:none' : ''; ?>" id="stores_commission">
                                            <?php foreach ($this->mStores as $store): ?>
                                                <div class="inline-block">
                                                     <label for="commission-stores<?php echo $store['id']; ?>" class="padding05"><?php echo escapeStr($store['name']); ?></label>
                                                    <input type="checkbox" value="<?php echo $store['id']; ?>" class="checkbox" id="commission-stores<?php echo $store['id']; ?>" name="commission-stores[]" <?php echo in_array($store['id'], explode(',', $p->{'commission-stores'})) ? "checked" : ''; ?>>
                                                </div>      
                                            <?php endforeach; ?>
                                            </div>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                            <thead>
                                <tr>
                                    <th colspan="6">
                                        <center>    
                                            <h3><?php echo lang('Timeclock Settings');?></h3>
                                        </center>
                                    </th>
                                </tr>
                            </thead>
                            <tr>
                                <td><?php echo lang('Timeclock');?></td>
                                <td colspan="5">
                                    <div class="col-lg-12">
                                        <div class="col-lg-12">

                                          <span style="inline-block">
                                                <input type="checkbox" value="1" class="checkbox" name="timeclock-edit" <?php echo $p->{'timeclock-edit'} ? "checked" : ''; ?>>
                                                <label for="timeclock-edit" class="padding05"><?php echo lang('Edit Entries');?></label>
                                            </span>
                                            <span style="inline-block">
                                                <input type="checkbox" id="timeclock-addentry" value="1" class="checkbox" name="timeclock-addentry" <?php echo $p->{'timeclock-addentry'} ? "checked" : ''; ?>>
                                                <label for="timeclock-addentry" class="padding05"><?php echo lang('Add Entries');?></label>
                                            </span>
                                          <span style="inline-block">
                                                <input type="checkbox" value="1" class="checkbox" name="timeclock-delete" <?php echo $p->{'timeclock-delete'} ? "checked" : ''; ?>>
                                                <label for="timeclock-delete" class="padding05"><?php echo lang('Delete Entries');?></label>
                                            <span style="inline-block">
                                                <input type="checkbox" value="<?php echo $p->{'group_id'}; ?>" class="checkbox" id="timeclock-groups" name="timeclock-groups[]" <?php echo in_array($p->{'group_id'}, explode(',', $p->{'timeclock-groups'})) ? "checked" : ''; ?>>
                                                <label for="timeclock-groups" class="padding05"><?php echo sprintf(lang('Can View Others Within (%s) Group'), $group->name);?></label>
                                            
                                            <span style="inline-block">
                                                <input type="checkbox" <?php echo $p->{'timeclock-view_all'} ? "checked" : ''; ?> value="1" class="checkbox" id="timeclock-view_all" name="timeclock-view_all">
                                                <label for="timeclock-view_all" class="padding05"><?php echo lang('View All Records');?></label>
                                            </div></div>
                                        <div class="col-lg-6">
                                            <div class="well well-sm">
                                            <label for="timeclock-groups" class="padding05"><?php echo lang('What other user groups can they see?');?></label>
                                            <?php 
                                                $groups = $this->ion_auth->groups()->result();
                                                foreach ($groups as $group_): 
                                            ?>
                                                <?php if($p->{'group_id'} == $group_->id) continue; ?>
                                                     <div class="inline-block">
                                                         <label for="timeclock-groups<?php echo $group_->id; ?>" class="padding05"><?php echo $group_->description; ?></label>
                                                        <input type="checkbox" value="<?php echo $group_->id; ?>" class="checkbox" id="timeclock-groups<?php echo $group_->id; ?>" name="timeclock-groups[]" <?php echo in_array($group_->id, explode(',', $p->{'timeclock-groups'})) ? "checked" : ''; ?>>
                                                    </div> 
                                            <?php endforeach; ?>
                                            </div>
                                            
                                        </div>
                                        

                                        <div class="col-lg-6">
                                            <div class="well well-sm">
                                            <label for="timeclock-stores" class="padding05"><?php echo sprintf(lang('What stores timeclock reports should (%s) be able to view'),$group->name);?></label><br>
                                                
                                            <div class="inline-block">
                                                <label for="timeclock-ownstore" class="padding05"><?php echo lang('Always View Store Logged Into');?></label>
                                                <input type="checkbox" value="1" class="checkbox" id="timeclock-ownstore" name="timeclock-ownstore" <?php echo $p->{'timeclock-ownstore'} ? "checked" : ''; ?>>
                                            </div>
                                            <div style="<?php echo $p->{'timeclock-ownstore'} ? "display:none" : ''; ?>" id="stores_timeclock">
                                                <?php foreach ($this->mStores as $store): ?>
                                                    <div class="inline-block">
                                                         <label for="timeclock-stores<?php echo $store['id']; ?>" class="padding05"><?php echo $store['name']; ?></label>
                                                        <input type="checkbox" value="<?php echo $store['id']; ?>" class="checkbox" id="timeclock-stores<?php echo $store['id']; ?>" name="timeclock-stores[]" <?php echo in_array($store['id'], explode(',', $p->{'timeclock-stores'})) ? "checked" : ''; ?>>
                                                    </div>                                     
                                                <?php endforeach; ?>
                                            </div>
                                            </div>
                                            
                                        </div>
                                    </div>

                                </td>
                            </tr>


                        </tbody>
                    </table>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><?php echo lang('update')?></button>
                </div>
                <?php echo form_close();
            } else {
                echo $this->lang->line("group_x_allowed");
            }
        } else {
            echo $this->lang->line("group_x_allowed");
        } ?>
  </div>
</div>

<script src="<?php echo base_url(); ?>assets/plugins/floatThead/jquery.floatThead.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var $table = $('#perms');
        $table.floatThead({
            top:55,
            responsiveContainer: function($table){
                return $table.closest('.table-responsive');
            },
        });
        $('input').on('ifChanged', function (event) { $(event.target).trigger('change'); });

        $('#timeclock-ownstore').on("change", function(){
            if (!this.checked) {
                $('#stores_timeclock').fadeIn('slow');
            }
            else {
                $('#stores_timeclock').fadeOut('slow');
            }                   
        });
        $('#commission-ownstore').on("change", function(){
            if (!this.checked) {
                $('#stores_commission').fadeIn('slow');
            }
            else {
                $('#stores_commission').fadeOut('slow');
            }                   
        });

        $('#recent_sales-ownstore').on("change", function(){
            if (!this.checked) {
                $('#stores_rsales').slideDown('slow');
            }
            else {
                $('#stores_rsales').slideUp('slow');
            }                   
        });

        $('#recent_sales-viewall').on("change", function(){
            if (!this.checked) {
                $('#stores_rsales_div').slideDown('slow');
            }
            else {
                $('#stores_rsales_div').slideUp('slow');
            }                   
        });
    });
       
</script>
<script type="text/javascript">
$(document).ready(function(){
    $('input[type=checkbox]').iCheck({
        checkboxClass: 'icheckbox_flat',
        radioClass: 'iradio_flat'
    });
});

</script>