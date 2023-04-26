<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pos_model extends CI_Model
{
    
    public function registerData($user_id)
    {
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $q = $this->db->get_where('pos_register', array('user_id' => $user_id, 'status' => 'open', 'store_id'=>(int)$this->session->userdata('active_store')), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    public function getProductVariantsByID($id)
    {
        $q = $this->db->select('*, variant_name as name, price as price')->get_where('inventory_variants', array('inventory_id' => $id));
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
    }
    
    public function findSaleByID($id)
    {
        $q = $this->db->get_where('sales', array('id' => $id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getAllSaleItems($id)
    {
        $q = $this->db->get_where('sale_items', array('sale_id' => $id));
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
    }
    public function getPlanVariantsByID($id)
    {
        $q = $this->db->select('*, plan_name as name, plan_price as price, plan_cost as cost')->get_where('plan_items', array('plan_id' => $id, 'disable'=>0));
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
    }
    public function getSalesCount()
    {
        $this->db->where('store_id', (int)$this->session->userdata('active_store'))->from('sales');
        return $this->db->count_all_results()+1;
    }
     public function getReference($which = NULL) {
        $prefix = 'SALE';
        if ($which == 're') {
            $prefix = 'SALERETURN';
        }
        $ref_no = (!empty($prefix)) ? $prefix . '/' : '';
        $seq_number = $this->getSalesCount();

        if ($this->controller->mSettings->reference_format == 1) {
            $ref_no .= date('Y') . "/" . sprintf("%04s", $seq_number);
        } elseif ($this->controller->mSettings->reference_format == 2) {
            $ref_no .= date('Y') . "/" . date('m') . "/" . sprintf("%04s", $seq_number);
        } elseif ($this->controller->mSettings->reference_format == 3) {
            $ref_no .= sprintf("%04s", $seq_number);
        } else {
            $ref_no .= sprintf("%04s", $seq_number);
        }

        return $ref_no;
    }

    public function getClientByID($id)
    {
        $q = $this->db->get_where('clients', array('id'=>$id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getInvoicePayments($sale_id)
    {
        $q = $this->db->get_where("payments", array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }

        return FALSE;
    }
    public function getStoreIDBySaleID($id) {
        $q = $this->db->get_where('sales', array('id' => $id));
        if ($q->num_rows() > 0) {
            return $q->row()->store_id;
        }else{
            return false;
        }
    }

    public function updateCodeUsed($code, $sale_id, $user_id) {
        $this->db
            ->update(
                'discount_codes', 
                array(
                    'used_on' => date('Y-m-d H:i:s'), 
                    'used_by' => $user_id,
                    'sale_number' => $sale_id
                ), 
                array(
                    'code'=>$code
                )
            );
        return TRUE;
    }


    public function addSale($data = array(), $items = array(), $payments = array(), $refund = FALSE) {
        $data_sale = $data;
        $types_of = array(
            'repair',
            'other',
            'accessory',
            'plans',
            'new_phone',
            'used_phone',
        );

        $settings = $this->settings_model->getSettings();
        $user = $this->settings_model->getUserByID($data['biller_id']);

        if ($this->db->insert('sales', $data)) {
            $sale_id = $this->db->insert_id();
            if ($refund) {
                $this->db->update('sales', array('return_sale_ref' => $data['return_sale_ref'], 'surcharge' => $data['surcharge'],'return_sale_total' => abs($data['grand_total']), 'return_id' => $sale_id), array('id' => $data['sale_id']));
            }

            foreach ($items as $item) {
                $discount_code_used = $item['discount_code_used'];
                unset($item['discount_code_used']);
                @$this->updateCodeUsed($discount_code_used, $sale_id, $data['biller_id']);
                if ($item['refund_item']) {
                    $item['commission'] = 0;
                    if (in_array($item['item_type'], $types_of)) {
                        $cdata = $this->getCommission($item['product_id'], $item['item_type'], $user->group_id);
                        if ($cdata['type'] == 'sales') {
                            $subtotal = 0-(abs($item['subtotal']) - abs($item['tax']));
                            $item['commission'] = ($subtotal/100) * $cdata['amount'];
                        }elseif ($cdata['type'] == 'profit') {
                            $profit = 0-($item['add_to_stock'] ? (abs($item['unit_price']) - ($item['unit_cost'])) : abs($item['unit_price']));
                            $profit += $item['activation_spiff'];

                            $item['commission'] = ($profit/100) * $cdata['amount'];
                        }elseif ($cdata['type'] == 'flat') {
                            $item['commission'] = 0-$cdata['amount'];
                        }
                        if ($cdata['plan_id']) {
                            $item['plan_id'] = $cdata['plan_id'];
                        }
                    }

                    if ($item['add_to_stock']) {
                        $active_store_id = (int)$this->session->userdata('active_store');
                        $sale_store_id = $this->getStoreIDBySaleID($data['sale_id']);
                        $same_store = ($active_store_id == $sale_store_id);
                        if (!$same_store) {
                            $transfer_data = array(
                                'date' => date('Y-m-d H:i:s'),
                                'sending_store' => $sale_store_id,
                                'receiving_store' => $active_store_id,
                                'product_id' => $item['product_id'],
                                'product_name' => $item['product_name'],
                                'product_type' => $item['item_type'],
                                'quantity' => 1,
                                'total_cost' => abs($item['unit_cost']),
                                'status' => 'received',
                                'is_refund' => TRUE,
                                'shipping_provider' => 'Refund Transaction',
                            );
                            $this->db->insert('transfers', $transfer_data);
                        }
                        unset($active_store_id);
                        unset($sale_store_id);
                        unset($same_store);

                        if ($item['item_type'] == 'repair' || $item['item_type'] == 'other' || $item['item_type'] == 'accessory'|| $item['item_type'] == 'new_phone') {
                            $keep_stock = 1;
                            if ($item['item_type'] == 'other') {
                                 $qother = $this->db->get_where('other', array('id'=>$item['product_id']));
                                if ($qother->num_rows > 0) {
                                    $keep_stock = $qother->row()->keep_stock;
                                }
                            }
                            if ((int)$keep_stock == 1) {
                                $stock_data = array(
                                    'price'             => $item['unit_cost'],
                                    'inventory_type'    => $item['item_type'] == 'new_phone' ? 'phones' : $item['item_type'],
                                    'inventory_id'      => $item['product_id'],
                                    'modified_date'     => date('Y-m-d H:i:s'),
                                    'store_id'          =>   (int)$this->session->userdata('active_store'),
                                    'in_state_of_transfer'  => 0,
                                );
                                if ($item['serial_number'] !== '') {
                                    $stock_data['serial_number'] = $item['serial_number'];
                                }
                                $this->db->insert('stock', $stock_data);
                            }
                        }
                        if ($item['item_type'] == 'used_phone') {
                            $ndata = array();
                            if ($item['item_type'] == 'new_phone' && $item['phone_classification'] == 'used'
                                ) {
                                $ndata['type'] = 'used';
                                $used_phone_vals = explode(',', $item['used_phone_vals']);
                                $ndata['cosmetic_condition'] = $used_phone_vals[0];
                                $ndata['operational_condition'] = $used_phone_vals[1];
                                $ndata['used_status'] = $used_phone_vals[2];
                                $ndata['unlocked'] = $used_phone_vals[3];
                                $ndata['store_id'] = (int)$this->session->userdata('active_store');
                                unset($used_phone_vals);
                            }
                            $ndata['sold'] = 0;
                            $this->db->where(array('id'=>$item['product_id']));
                            $this->db->update('phones', $ndata);
                        }
                        if ($item['item_type'] == 'crepairs' || $item['item_type'] == 'drepairs') {

                            if ($item['items_restock'] == 1) {
                                $q = $this->db->get_where('repair_items', array('repair_id'=>$item['product_id']));
                                if ($q->num_rows() > 0) {
                                    $ritems = ($q->result());
                                    foreach ($ritems as $key => $ritem) {
                                        $stock_data = array(
                                            'price'             => $ritem->unit_cost,
                                            'serial_number'     => ($ritem->serial_number !== '') ? $ritem->serial_number : NULL,
                                            'inventory_type'    => 'repair',
                                            'inventory_id'      => $ritem->product_id,
                                            'modified_date'     => date('Y-m-d H:i:s'),
                                            'store_id'          => (int)$this->session->userdata('active_store'),
                                            'in_state_of_transfer'  => 0,
                                        );
                                        $this->db->insert('stock', $stock_data);
                                    }
                                }
                            }
                            // $this->db->where('id', $item['product_id'])->update('repair', array('status'=>5));
                        }
                    }
                } else { 
                    $item['commission'] = 0;
                    if (in_array($item['item_type'], $types_of)) {
                        $cdata = $this->getCommission($item['product_id'], $item['item_type'], $user->group_id);
                        if ($cdata['type'] == 'sales') {
                            $subtotal = $item['subtotal'] - $item['tax'];
                            $item['commission'] = ($subtotal/100) * $cdata['amount'];
                        }elseif ($cdata['type'] == 'profit') {
                            $profit = $item['unit_price'] - $item['unit_cost'];
                            $profit += $item['activation_spiff'];

                            $item['commission'] = ($profit/100) * $cdata['amount'];
                        }elseif ($cdata['type'] == 'flat') {
                            $item['commission'] = $cdata['amount'];
                        }
                        if ($cdata['plan_id']) {
                            $item['plan_id'] = $cdata['plan_id'];
                        }
                    }
                    if ($item['item_type'] == 'drepairs') {
                        $q = $this->db->get_where('repair_items', array('repair_id'=>$item['product_id']));
                        if ($q->num_rows() > 0) {
                            $rows = $q->result();
                            foreach ($rows as $row) {
                                $cdata = $this->getCommission($row->product_id, 'repair', $user->group_id);
                                if ($cdata['type'] == 'sales') {
                                    $subtotal = $row->subtotal - $row->tax;
                                    $commission = ($subtotal/100) * $cdata['amount'];
                                }elseif ($cdata['type'] == 'profit') {
                                    $profit = $row->unit_price;
                                    $profit += $item['activation_spiff'];

                                    $commission = ($profit/100) * $cdata['amount'];
                                }elseif ($cdata['type'] == 'flat') {
                                    $commission = $cdata['amount'];
                                }
                                $this->db->update('repair_items', array('commission'=>$commission), array('id'=>$row->id));
                                $item['commission'] += $commission;
                                if ($cdata['plan_id']) {
                                    $item['plan_id'] = $cdata['plan_id'];
                                }
                            }
                        }
                    }

                    if ($item['item_type'] == 'repair' || $item['item_type'] == 'other' || $item['item_type'] == 'accessory' || $item['item_type'] == 'new_phone') {
                        $serialized = false;
                        if ($item['serial_number'] !== null) {
                            $serialized = true;
                            $this->db
                                ->where('inventory_type', $item['item_type'] == 'new_phone' ? 'phones' : $item['item_type'])
                                ->where('inventory_id', $item['product_id'])
                                ->where('serial_number', $item['serial_number'])
                                ->where('store_id', (int)$this->session->userdata('active_store'))
                                ->where('in_state_of_transfer', 0)
                                ->order_by('modified_date', 'ASC')
                                ->limit(1);
                                
                            $q = $this->db->get('stock');
                            if ($q->num_rows() > 0) {
                                $id = $q->row()->id;
                                $item['unit_cost'] = $q->row()->price;
                                $this->db->delete('stock', array('id' => $id));
                            }else{
                                $serialized = false;
                            }
                        }
                        if (!$serialized) {
                            $this->db
                                ->where('inventory_type', $item['item_type'])
                                ->where('inventory_id', $item['product_id'])
                                ->where('in_state_of_transfer', 0)
                                ->where('store_id', (int)$this->session->userdata('active_store'))
                                ->order_by('modified_date', 'ASC')
                                ->limit(1);
                            $q = $this->db->get('stock');
                            if ($q->num_rows() > 0) {
                                $item['unit_cost'] = $q->row()->price;
                                $id = $q->row()->id;
                                $this->db->delete('stock', array('id' => $id));
                            }
                        }
                    }

                    if ($item['item_type'] == 'used_phone') {
                        $this->db->where(
                            array(
                                    'id'=>$item['product_id'],
                                    'store_id' => (int)$this->session->userdata('active_store'),
                                )
                        );
                        $this->db->update('phones', array('sold'=>1));
                    }

                    if ($item['item_type'] == 'crepairs') {
                        $this->db->where(
                            array(
                                    'id'=>$item['product_id'],
                                    'store_id' => (int)$this->session->userdata('active_store'),
                                )
                        );

                        $data = ['deposit_collected'=>1];
                        if ((int)$settings->repair_deposit > 0) {
                            $data['status'] = $settings->repair_deposit;
                        }
                        $this->db->update('repair', $data);
                         // get cost of all items
                        $rrgt = $this->getRepairItemsTotals($item['product_id']);
                        $item['unit_cost'] = $rrgt->cost ?? 0;

                    }

                    if ($item['item_type'] == 'drepairs') {
                        $this->db->where(
                            array(
                                'id'=>$item['product_id'],
                                'store_id' => (int)$this->session->userdata('active_store'),
                            )
                        );
                        $data = ['date_closing'=>date('Y-m-d H:i:s'), 'pos_sold'=>1];
                        if ((int)$settings->repair_completed > 0) {
                            $data['status'] = $settings->repair_completed;
                        }
                        $this->db->update('repair', $data);


                        // get cost of all items
                        $rrgt = $this->getRepairItemsTotals($item['product_id']);
                        $item['unit_cost'] = $rrgt->cost ?? 0;

                    }


                    if ($item['item_type'] == 'cp') {
                        $purchase_data = $this->db
                            ->select('phone_name, manufacturer_id, model_name, carrier_id, description, max_discount, tax_id, discount_type, cosmetic_condition, operational_condition, used_status, unlocked, date as date_acquired, taxable')
                            ->get_where('customer_purchases', array('id' => $item['product_id']))
                            ->row_array();
                        $purchase_data['date_added'] = date('Y-m-d H:i:s');
                        $purchase_data['type'] = 'used';
                        $purchase_data['store_id'] = (int)$this->session->userdata('active_store');
                        $this->db->insert('phones', $purchase_data);
                        $pid = $this->db->insert_id();
                        $purchase_data_items = $this->db
                            ->select('imei, cost, price')
                            ->get_where('customer_purchase_items', array('purchase_id' => $item['product_id']))
                            ->row_array();
                        $purchase_data_items['phone_id'] = $pid;
                        $this->db->insert('phone_items', $purchase_data_items);

                        $this->db->where('id', $item['product_id']);
                        $this->db->update('customer_purchases', array('status' => 2));
                    }
                }
                unset($item['items_restock']);
                unset($item['phone_classification']);
                unset($item['used_phone_vals']);
                $item['sale_id'] = $sale_id;
                $this->db->insert('sale_items', $item);
            }

            foreach ($payments as $payment) {
                if($settings->pos_bank_id) {
                    $account_data = array(
                        'type' => $refund ? 'expense' : 'deposit',
                        'type_id' => 1,
                        'amount' => $payment['amount'],
                        'date' => date('Y-m-d'),
                        'recurring' => 0,
                        'sale_id' => $sale_id,
                        'user_id' => $this->session->userdata('user_id'),
                        'bank_id' => $settings->pos_bank_id,
                        'fund_type' => $payment['paid_by'],
                        'to_from_id' => $data_sale['customer_id'],
                        'to_from_name' => $data_sale['customer'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'store_id' => (int)$this->session->userdata('active_store'),
                    );
                    $this->db->insert('account_entries', $account_data);
                }

                $payment['sale_id'] = $sale_id;
                $this->db->insert('payments', $payment);
            }

            $this->syncSalePayments($sale_id);






            return $sale_id;
        }
    }
     public function getRepairItemsTotals($id) {
        $q = $this->db
            ->select('SUM(unit_cost * quantity) as cost, SUM(unit_price * quantity) as price')
            ->where('repair_id', $id)
            ->get('sale_items');
        if ($q->num_rows() > 0) {
            return $q->row();
        }else{
            return (object) [
                'cost' => 0,
                'price' => 0,
            ];
        }

     }

     public function syncSalePayments($id) {
        $sale = $this->getSaleByID($id);
        if ($payments = $this->getSalePayments($id)) {
            $paid = 0;
            $grand_total = $sale->grand_total;
            foreach ($payments as $payment) {
                $paid += $payment->amount;
            }
            $payment_status = $paid == 0 ? 'pending' : $sale->payment_status;
            if ($this->repairer->formatDecimal($grand_total) == $this->repairer->formatDecimal($paid)) {
                $payment_status = 'paid';
            } elseif ($paid != 0) {
                $payment_status = 'partial';
            }

            if ($this->db->update('sales', array('paid' => $paid, 'payment_status' => $payment_status), array('id' => $id))) {
                return true;
            }
        }else{
            if ($this->db->update('sales', array('paid' => 0, 'payment_status' => 'pending'), array('id' => $id))) {
                return true;
            }
        }
        return FALSE;
    }

    public function getCommission($product_id = NULL, $type = NULL, $group=NULL) {
        if ($type == 'drepairs' || $type == 'repair') {
            $type = 'repair_parts';
        }elseif ($type == 'accessory') {
            $type = 'accessories';
        }elseif ($type == 'new_phone') {
            $type = 'new_phones';
        }elseif ($type == 'used_phone') {
            $type = 'used_phones';
        }
        $q = $this->db
                ->select('groups, amount, type, plan_id')
                 ->where('product_id', $product_id)
                 ->where('category', $type)
                 ->get('product_commission');
        if ($q->num_rows() > 0) {
            $rows = $q->result_array();
            foreach ($rows as $row) {
                $row['groups'] = explode(',', $row['groups']);
                if (in_array($group, $row['groups'])) {
                    unset($row['groups']);
                    return $row;
                }
            }
            return array('amount'=>0, 'type'=>'flat', 'plan_id'=>NULL);
        }else{
            $q = $this->db
                    ->select('groups, amount, type, plan_id')
                    ->where('category', $type)
                    ->get('category_commission');
            if ($q->num_rows() > 0) {
                $rows = $q->result_array();
                foreach ($rows as $row) {
                    $row['groups'] = explode(',', $row['groups']);
                    if (in_array($group, $row['groups'])) {
                        unset($row['groups']);
                        return $row;
                    }
                }
                return array('amount'=>0, 'type'=>'flat', 'plan_id'=>NULL);
            }else{
                return array('amount'=>0, 'type'=>'flat', 'plan_id'=>NULL);
            }
        }
    }

    public function countStock($type, $id)
    {
        $q = $this->db
            ->select('COUNT(id) as count')
            ->where(array('inventory_type' => $type, 'inventory_id'=>$id, 'store_id'=>(int)$this->session->userdata('active_store')))
            ->get('stock');
        return $q->row()->count;
    }
    
    public function getDeliverables($term = NULL, $limit = 5) {
        $completed_statuses = $this->settings_model->getRepairStatusesCompleted();
        if ($term) {
            $this->db->where("(name LIKE '%" . $term . "%' OR telephone LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR model_name LIKE '%" . $term . "%' OR  concat(name, ' (', model_name, ')') LIKE '%" . $term . "%')");
            $this->db->limit($limit);
        }

        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('(disable = 0)');
        $this->db->where('(pos_sold = 0)');
        $this->db->where_in('status', $completed_statuses);

        $this->db->where('((advance=0) OR (deposit_collected=1))');
        $this->db->select('*, id as id, CONCAT(name," - ",defect, " - ", model_name) as name, (telephone) as code, ((grand_total-advance) - tax) as price, (SELECT SUM(unit_cost) FROM repair_items WHERE repair.id = repair_items.repair_id) as cost');
        
        $q = $this->db->get('repair');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $row->type = 'drepairs';
                if (!$row->cost) {
                    $row->cost = 0;
                }
                $row->taxable = 0;
                $row->qty = -1;
                $row->variants = NULL;
                $row->stock_id = NULL;
                $row->is_serialized = 0;
                $row->warranty = json_decode($row->warranty);
                $row->warranty_id = $row->warranty ? $row->warranty->id : null;

                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getCheckedIn($term = NULL, $limit = 5) {
        $pending_statuses = $this->settings_model->getRepairStatusesPending();

        if ($term) {
            $this->db->where("(name LIKE '%" . $term . "%' OR model_name LIKE '%" . $term . "%' OR  concat(name, ' (', model_name, ')') LIKE '%" . $term . "%')");
            $this->db->limit($limit);
        }


        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        // $this->db->where('(status = 1)');
        $this->db->where_in('status', $pending_statuses);

        $this->db->where('(disable = 0)');
        $this->db->where('((advance!=0) AND (deposit_collected=0))');
        $this->db->select('*, id as id, CONCAT(name," - ",defect, " - ", model_name) as name, (telephone) as code, (advance) as price');
        
        $q = $this->db->get('repair');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $row->tax = 0;
                $row->cost = 0;
                $row->type = 'crepairs';
                $row->taxable = 0;
                $row->qty = -1;
                $row->variants = NULL;
                $row->stock_id = NULL;
                $row->is_serialized = 0;   
                $row->warranty = json_decode($row->warranty);
                $row->warranty_id = $row->warranty ? $row->warranty->id : null;
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }



    public function getProductNames($term = NULL, $limit = 20) {
        if ($term) {

            $search = explode(' ', $term);
            $where = array();
            foreach ($search as $val) {
                $where[] = "(name LIKE '%" . $val . "%' OR code LIKE '%" . $val . "%')";
            }
            $where = implode(' AND ', $where);
            $this->db->where($where);
            $this->db->limit($limit);
        }else{
            $this->db->where('quick_sale', 1);
        }

        $this->db->order_by('name');
        $this->db->where('isDeleted', 0);
        $this->db->where('(inventory.universal=1 OR inventory.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);
        $this->db->select('*, id as id, name as name, code as code, taxable, tax_rate as tax_rates, (SELECT price from stock where stock.inventory_id=inventory.id AND stock.inventory_type=\'repair\' AND in_state_of_transfer = 0  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date ASC LIMIT 1) as cost, (SELECT id from stock where stock.inventory_id=inventory.id AND stock.inventory_type=\'repair\' AND in_state_of_transfer = 0  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date ASC LIMIT 1) as stock_id');
        $q = $this->db->get('inventory');
        if ($q->num_rows() > 0) {
            // print_r($q->result());die();

            foreach (($q->result()) as $row) {
                $row->type = 'repair';
                $row->qty = $this->countStock('repair', $row->id);
                $row->variants = $this->getProductVariantsByID($row->id);
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }
   
    public function getAccessoryNames($term = NULL, $limit = 5)
    {
        if ($term) {
            $search = explode(' ', $term);
            $where = array();
            foreach ($search as $val) {
                $where[] = "(name LIKE '%" . $val . "%' OR upc_code LIKE '%" . $val . "%')";
            }
            $where = implode(' AND ', $where);
            $this->db->where($where);

            // $this->db->where("(name LIKE '%" . $term . "%' OR upc_code LIKE '%" . $term . "%' OR  concat(name, ' (', upc_code, ')') LIKE '%" . $term . "%')");
            $this->db->limit($limit);
        }else{
            $this->db->where('quick_sale', 1);
        }
        
        $this->db->order_by('name');
        $this->db->where('(accessory.universal=1 OR accessory.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);

        $this->db->select('*, id as id, name as name, upc_code as code, taxable, tax_id as tax_rates, (SELECT price from stock where stock.inventory_id=accessory.id AND stock.inventory_type=\'accessory\' AND in_state_of_transfer = 0  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date ASC LIMIT 1) as cost, (SELECT id from stock where stock.inventory_id=accessory.id AND stock.inventory_type=\'accessory\' AND in_state_of_transfer = 0  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date ASC LIMIT 1) as stock_id');
        $q = $this->db->where('deleted != ', 1)->order_by('accessory.name', 'ASC')->get('accessory');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $row->variants = NULL;
                $row->type = 'accessory';
                $row->qty = $this->countStock('accessory', $row->id);
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getOthers($term = NULL, $limit = 5)
    {
        $data = array();
        if ($term) {
            $this->db->where('inventory_type', 'other');
            $this->db->where('in_state_of_transfer', 0);
            $this->db->where('selected', 0);
            $this->db->where('store_id', (int)$this->session->userdata('active_store'));
                $this->db->where('serial_number', trim($term));
            $q = $this->db->get('stock');
            if ($q->num_rows() > 0) {
                $stock = $q->row();
                $this->db->where('id', $stock->inventory_id);
                $this->db->where('(other.universal=1 OR other.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);
                $this->db->select('*,IF(cash_out = 0, price, price * -1) as price, other.cost as no_stock_cost, id as id, name as name, upc_code as code, taxable, tax_id as tax_rates');
                $q = $this->db->where('deleted != ', 1)->order_by('other.name', 'ASC')->get('other');
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $row->type = 'other';
                        $row->qty = $this->countStock('other', $row->id);
                        $row->variants = NULL;
                        $row->serial_number = $stock->serial_number;
                        $row->cost = $stock->price;
                        $row->stock_id = $stock->id;
                        $data[] = $row;
                    }
                    return $data;
                }
                die();
            }
        }
    

        if ($term) {
            $search = explode(' ', $term);
            $where = array();
            foreach ($search as $val) {
                $where[] = "(name LIKE '%" . $val . "%' OR upc_code LIKE '%" . $val . "%')";
            }
            $where = implode(' AND ', $where);
            $this->db->where($where);

            // $this->db->where("(name LIKE '%" . $term . "%' OR upc_code LIKE '%" . $term . "%' OR  concat(name, ' (', upc_code, ')') LIKE '%" . $term . "%')");
            $this->db->limit($limit);
        }else{
            $this->db->where('quick_sale', 1);
        }

        $this->db->order_by('name');
        $this->db->where('(other.universal=1 OR other.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);
       
        $this->db->select('*,IF(cash_out = 0, price, price * -1) as price, other.cost as no_stock_cost, id as id, name as name, upc_code as code, taxable, tax_id as tax_rates, (SELECT price from stock where stock.inventory_id=other.id AND stock.inventory_type=\'other\' AND store_id='.(int)$this->session->userdata('active_store').' AND in_state_of_transfer = 0  and selected = 0 ORDER BY modified_date ASC LIMIT 1) as cost, (SELECT id from stock where stock.inventory_id=other.id AND stock.inventory_type=\'other\' AND in_state_of_transfer = 0  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date ASC LIMIT 1) as stock_id');
        
        $q = $this->db->where('deleted != ', 1)->get('other');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $row->type = 'other';
                $row->qty = $this->countStock('other', $row->id);
                $row->variants = NULL;
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getNewPhones($term = NULL, $limit = 5)
    {
        $data = array();
        if ($term) {
            $this->db->where('inventory_type', 'phones');
            $this->db->where('in_state_of_transfer', 0);
            $this->db->where('selected', 0);
            $this->db->where('store_id', (int)$this->session->userdata('active_store'));
            $this->db->where('serial_number', trim($term));
            $q = $this->db->get('stock');
            if ($q->num_rows() > 0) {
                $stock = $q->row();
                $this->db->where('id', $stock->inventory_id);
                $this->db->where('store_id', (int)$this->session->userdata('active_store'));
                $this->db->select('*, price, id as id, phone_name as name, model_name as code, taxable, tax_id as tax_rates');
                $q = $this->db->where('disable', 0)->order_by('phones.phone_name', 'ASC')->get('phones');
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $row->type = 'new_phone';
                        $row->qty = $this->countStock('phones', $row->id);
                        $row->variants = NULL;
                        $row->is_serialized = 1;
                        $row->serial_number = $stock->serial_number;
                        $row->cost = $stock->price;
                        $row->stock_id = $stock->id;
                        $data[] = $row;
                    }
                    return $data;
                }
                die();
            }
        }
        if ($term) {

            $search = explode(' ', $term);
            $where = array();
            foreach ($search as $val) {
                $where[] = "(phone_name LIKE '%" . $val . "%' )";
            }
            $where = implode(' AND ', $where);
            $this->db->where($where);

            
            // $this->db->where("(phone_name LIKE '%" . $term . "%' OR model_name LIKE '%" . $term . "%' OR  concat(phone_name, ' (', model_name, ')') LIKE '%" . $term . "%')");
            $this->db->limit($limit);
        }else{
            $this->db->where('quick_sale', 1);
        }
        $this->db->order_by('phone_name');
        
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('type', 'new');
        $this->db->select('*,price as price, id as id, phone_name as name, model_name as code, taxable, tax_id as tax_rates, (SELECT price from stock where stock.inventory_id=phones.id AND stock.inventory_type=\'phones\' AND store_id='.(int)$this->session->userdata('active_store').' AND in_state_of_transfer = 0  and selected = 0 ORDER BY modified_date ASC LIMIT 1) as cost, (SELECT id from stock where stock.inventory_id=phones.id AND stock.inventory_type=\'phones\' AND in_state_of_transfer = 0  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date ASC LIMIT 1) as stock_id');
        $q = $this->db->where('disable', 0)->from('phones')->get();
        if ($q->num_rows() > 0) {
            $rows = $q->result();
            foreach ($rows as $row) {
                $row->type = 'new_phone';
                $row->qty = $this->countStock('phones', $row->id);
                $row->variants = NULL;
                $row->is_serialized = 1;
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getUsedPhones($term = NULL, $limit = 5)
    {
        if ($term) {
            $this->db->where("(phone_name LIKE '%" . $term . "%' OR imei LIKE '%" . $term . "%' OR  concat(phone_name, ' (', model_name, ')') LIKE '%" . $term . "%')");
            $this->db->limit($limit);
        }else{
            $this->db->where('quick_sale', 1);
        }
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('type', 'used');
        $this->db->where('used_status', 1);
        $this->db->where('disable', 0);
        $this->db->where('phones.sold', 0);

        $this->db->select('*, phones.id as id, phone_name as name, taxable, tax_id as tax_rates, (SELECT imei from phone_items where phones.id=phone_items.phone_id LIMIT 1) as code')
        ->join('phone_items', 'phone_items.phone_id=phones.id', 'left');

        $q = $this->db->order_by('phones.phone_name', 'ASC')->get('phones');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $item = $this->db->select('*, imei as name, price as price, cost as cost')->get_where('phone_items', array('phone_id'=>$row->id));
                    $row->type = 'used_phone';
                    $row->price = 0;
                    $row->variants = NULL;
                    $row->qty = 1;
                    $row->cost = 1;
                    $row->stock_id = NULL;
                    $row->is_serialized = 0;
                    if ($q->num_rows() > 0) {
                        $row->variants = $item->result();
                    }else{
                        return FALSE;
                    }
                    $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCustomerPurchases($term = NULL, $limit = 5)
    {
        if ($term) {
            $this->db->where("(phone_name LIKE '%" . $term . "%' OR imei LIKE '%" . $term . "%' OR  concat(phone_name, ' (', model_name, ')') LIKE '%" . $term . "%')");
            $this->db->limit($limit);
        }
        $this->db->where('(customer_purchases.universal=1 OR customer_purchases.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);

        $this->db->where('status', 1);
        $this->db->where_in("(SELECT trans_id FROM myTable WHERE code = 'B')");

        $this->db->select('*, customer_purchases.id as id, phone_name as name, taxable, tax_id as tax_rates, (SELECT imei from customer_purchase_items where customer_purchases.id=customer_purchase_items.purchase_id LIMIT 1) as code')
        ->join('customer_purchase_items', 'customer_purchase_items.purchase_id=customer_purchases.id', 'left');

        $q = $this->db->get('customer_purchases');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $item = $this->db->select('*, imei as name, price as price, cost as cost')->get_where('customer_purchase_items', array('purchase_id'=>$row->id));
                    $row->type = 'cp';
                    $row->price = 0;
                    $row->variants = NULL;
                    $row->qty = 1;
                    $row->cost = 1;
                    $row->stock_id = NULL;
                    $row->taxable = 0;
                    $row->is_serialized = 0;
                    $row->warranty_id = null;

                    if ($q->num_rows() > 0) {
                        $row->variants = $item->result();
                    }else{
                        return FALSE;
                    }
                    $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

     public function getAllPlans($term = NULL, $limit=5)
    {
        if ($term) {
            $this->db->where("name LIKE '%" . $term . "%'");
            $this->db->limit($limit);
        }
        $this->db->where('(plans.universal=1 OR plans.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);

        $this->db->join('carriers', 'carriers.id=plans.carrier_id', 'left');
        $this->db->select('plans.id as id, carriers.name as name, carriers.name as code, plans.taxable, plans.tax_rate as tax_rates');
        $q = $this->db->where(array('plans.disable' => 0))->get('plans');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) 
            {
                $row->variants = $this->getPlanVariantsByID($row->id);
                $row->type = 'plans';
                $row->cost = 0;
                $row->price = 1;
                $row->qty = -1;
                $row->stock_id = NULL;
                $row->is_serialized = 0;
                $row->warranty_id = null;

                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getAllProductNames($term, $limit = 5, $sell_repair_parts = NULL, $is_repair){
        if ($sell_repair_parts || $is_repair) {
            $repairs = $this->getProductNames($term);
        }else{
            $repairs = FALSE;
        }
        $others = $this->getOthers($term);
        $accessory = $this->getAccessoryNames($term);

        if (!$is_repair) {
            $new_phones = $this->getNewPhones($term);
            $used_phones = $this->getUsedPhones($term);
            $plans = $this->getAllPlans($term);
            $checked_in = $this->getCheckedIn($term);
            $deliverables = $this->getDeliverables($term);
            $data = array();
            $data = array_merge((array)$repairs, (array)$accessory, (array)$new_phones, (array)$used_phones, (array)$plans, (array)$others, (array)$checked_in, (array)$deliverables);
            return $data;
        }
        $data = array();
        $data = array_merge((array)$repairs, (array)$accessory, (array)$others);
        return $data;
    }

    // Products By ID
    public function getProductNamesByID($id) {
        if ($id) {
            $this->db->where('inventory.id', $id);
        }
        $this->db->where('isDeleted', 0);
        $this->db->where('(inventory.universal=1 OR inventory.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);

        $this->db->select('*, id as id, name as name, code as code, taxable, tax_rate as tax_rates, (SELECT price from stock where stock.inventory_id=inventory.id AND stock.inventory_type="repair" AND in_state_of_transfer = 0  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date ASC LIMIT 1) as cost, (SELECT id from stock where stock.inventory_id=inventory.id AND stock.inventory_type="repair" AND in_state_of_transfer = 0  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date ASC LIMIT 1) as stock_id');
        $q = $this->db->order_by('inventory.name', 'ASC')->get('inventory');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $row->type = 'repair';
                $row->qty = $this->countStock('repair', $row->id);
                $row->variants = $this->getProductVariantsByID($row->id);
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }
   
    public function getAccessoryNamesByID($id)
    {
        if ($id) {
            $this->db->where('accessory.id', $id);
        }
        $this->db->where('(accessory.universal=1 OR accessory.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);
        $this->db->select('*, id as id, name as name, upc_code as code, taxable, tax_id as tax_rates, (SELECT price from stock where stock.inventory_id=accessory.id AND stock.inventory_type=\'accessory\' AND in_state_of_transfer = 0  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date ASC LIMIT 1) as cost, (SELECT id from stock where stock.inventory_id=accessory.id AND stock.inventory_type=\'accessory\' AND in_state_of_transfer = 0  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date ASC LIMIT 1) as stock_id');
        $q = $this->db->where('deleted != ', 1)->get('accessory');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $row->variants = NULL;
                $row->type = 'accessory';
                $row->qty = $this->countStock('accessory', $row->id);
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getOthersByID($id)
    {
        if ($id) {
            $this->db->where('other.id', $id);
        }
        $this->db->where('(other.universal=1 OR other.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);

        $this->db->select('*,IF(cash_out = 0, price, price * -1) as price, id as id, name as name, upc_code as code, taxable, tax_id as tax_rates, (SELECT price from stock where stock.inventory_id=other.id AND stock.inventory_type=\'other\' AND in_state_of_transfer = 0  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date ASC LIMIT 1) as cost, (SELECT id from stock where stock.inventory_id=other.id AND stock.inventory_type=\'other\' AND in_state_of_transfer = 0  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date ASC LIMIT 1) as stock_id, other.cost as no_stock_cost');
        $q = $this->db->where('deleted != ', 1)->get('other');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {

                $row->type = 'other';
                $row->qty = $this->countStock('other', $row->id);
                $row->variants = NULL;
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getNewPhonesByID($id)
    {

        if ($id) {
            $this->db->where('phones.id', $id);
        }
        $this->db->where('type', 'new');
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));

        $this->db->select('*, id as id, phone_name as name, model_name as code, taxable, tax_id as tax_rates, (SELECT price from stock where stock.inventory_id=phones.id AND stock.inventory_type=\'phones\' AND in_state_of_transfer = 0  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date ASC LIMIT 1) as cost, (SELECT id from stock where stock.inventory_id=phones.id AND stock.inventory_type=\'phones\' AND in_state_of_transfer = 0  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date ASC LIMIT 1) as stock_id');
        $q = $this->db->where('disable', 0)->get('phones');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $row->variants = NULL;
                $row->type = 'new_phone';
                $row->qty = $this->countStock('phones', $row->id);
                $row->is_serialized = 1;
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getUsedPhonesByID($id)
    {
        if ($id) {
            $this->db->where('phones.id', $id);
        }
        $this->db->where('type', 'used');
        $this->db->where('disable', 0);
        $this->db->where('phones.sold', 0);
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));


        $this->db->select('*, id as id, phone_name as name, (SELECT imei from phone_items where phones.id=phone_items.phone_id LIMIT 1) as code, taxable, tax_id as tax_rates,');
        $q = $this->db->get('phones');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $item = $this->db->select('*, imei as name, price as price, cost as cost')->get_where('phone_items', array('phone_id'=>$row->id));
                    $row->type = 'used_phone';
                    $row->price = 0;
                    $row->variants = NULL;
                    $row->qty = 1;
                    $row->cost = 1;
                    $row->stock_id = NULL;
                    $row->is_serialized = 0;


                    if ($q->num_rows() > 0) {
                        $row->variants = $item->result();
                    }else{
                        return FALSE;
                    }
                    $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
     public function getAllPlansByID($id)
    {
        if ($id) {
            $this->db->where('plans.id', $id);
        }
        
        // $this->db->where('(plans.universal=1 OR plans.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);
        $this->db->join('carriers', 'carriers.id=plans.carrier_id', 'left');
        $this->db->select('plans.id as id, carriers.name as name, carriers.name as code, plans.taxable, plans.tax_rate as tax_rates');
        $q = $this->db->where(array('plans.disable' => 0))->order_by('carriers.name', 'ASC')->get('plans');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) 
            {
                $row->variants = $this->getPlanVariantsByID($row->id);
                $row->type = 'plans';
                $row->cost = 0;
                $row->price = 1;
                $row->qty = -1;
                $row->stock_id = NULL;
                $row->is_serialized = 0;
                $row->warranty_id = null;
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
     public function getDeliverablesByID($id = NULL) {
        $completed_statuses = $this->settings_model->getRepairStatusesCompleted();

        if ($id) {
            $this->db->where('repair.id', $id);
        }
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('(disable = 0)');
        $this->db->where('(pos_sold = 0)');
        // $this->db->where('(status = 2)');
        $this->db->where_in('status', $completed_statuses);

        $this->db->where('((advance=0) OR (deposit_collected=1))');
        $this->db->select('*, id as id, CONCAT(name," - ",defect, " - ", model_name) as name, telephone as code, ((grand_total-advance) - tax) as price, (SELECT SUM(unit_cost) FROM repair_items WHERE repair.id = repair_items.repair_id) as cost');
        
        $q = $this->db->get('repair');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $row->type = 'drepairs';
                if (!$row->cost) {
                    $row->cost = 0;
                }
                $row->taxable = 0;
                $row->qty = -1;
                $row->variants = NULL;
                $row->stock_id = NULL;
                $row->is_serialized = 0;
                $row->warranty = json_decode($row->warranty);
                $row->warranty_id = $row->warranty ? $row->warranty->id : null;
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }
    public function getCheckedInByID($id = NULL) {
        $completed_statuses = $this->settings_model->getRepairStatusesCompleted();

        if ($id) {
            $this->db->where('repair.id', $id);
        }
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $this->db->where_not_in('status', $completed_statuses);
        // $this->db->where('(status != 0)');
        $this->db->where('(disable = 0)');
        $this->db->where('((advance!=0) AND (deposit_collected = 0))');
        $this->db->select('*, id as id, CONCAT(name," - ",defect, " - ", model_name) as name, telephone as code, advance as price');
        
        $q = $this->db->get('repair');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $row->tax = 0;
                $row->cost = 0;
                $row->type = 'crepairs';
                $row->taxable = 0;
                $row->qty = -1;
                $row->variants = NULL;
                $row->stock_id = NULL;
                $row->is_serialized = 0;
                $row->warranty = json_decode($row->warranty);
                $row->warranty_id = $row->warranty ? $row->warranty->id : null;

                $data[] = $row;
            }
            return $data;
        }
        return false;
    }
  public function getCustomerPurchasesByID($id)
    {
        if ($id) {
            $this->db->where("id", $id);
        }

        $this->db->where('status', 1);
        $this->db->where('(customer_purchases.universal=1 OR customer_purchases.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);

        $this->db->select('*, id as id, phone_name as name, taxable, tax_id as tax_rates, (SELECT imei from customer_purchase_items where customer_purchases.id=customer_purchase_items.purchase_id LIMIT 1) as code');

        $q = $this->db->get('customer_purchases');

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $item = $this->db->select('*, imei as name, price as price, cost as cost')->get_where('customer_purchase_items', array('purchase_id'=>$row->id));
                    $row->type = 'cp';
                    $row->price = 0;
                    $row->variants = NULL;
                    $row->qty = 1;
                    $row->cost = 1;
                    $row->stock_id = NULL;
                    $row->is_serialized = 0;
                    $row->taxable = 0;
                    $row->warranty_id = null;
                    if ($q->num_rows() > 0) {
                        $row->variants = $item->result();
                    }else{
                        return FALSE;
                    }
                    $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getRegisterCCSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cc_slips, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'CC');
        $this->db->where('payments.created_by', $user_id);
        $this->db->where('payments.store_id', (int)$this->session->userdata('active_store'));

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
     public function getRegisterCashSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cash_qty, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'cash');
        $this->db->where('payments.created_by', $user_id);
        $this->db->where('payments.store_id', (int)$this->session->userdata('active_store'));


        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
     public function getRegisterChSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'Cheque');
        $this->db->where('payments.created_by', $user_id);
        $this->db->where('payments.store_id', (int)$this->session->userdata('active_store'));

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    public function getRegisterOtherSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_others, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'other');
        $this->db->where('payments.created_by', $user_id);
        $this->db->where('payments.store_id', (int)$this->session->userdata('active_store'));


        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterPPPSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date)->where('paid_by', 'ppp');
        $this->db->where('payments.created_by', $user_id);

        $this->db->where('payments.store_id', (int)$this->session->userdata('active_store'));

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    public function getRegisterSales($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('type', 'received')->where('payments.date >', $date);
        $this->db->where('payments.created_by', $user_id);
        $this->db->where('payments.store_id', (int)$this->session->userdata('active_store'));


        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function closeRegister($rid, $user_id, $data)
    {
        if (!$rid) {
            $rid = $this->session->userdata('register_id');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }

        if ($this->db->update('pos_register', $data, array('id' => $rid, 'user_id' => $user_id))) {
            return true;
        }
        return FALSE;
    }
    public function getDrawerTotal($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db
            ->select('SUM(ABS(amount)) as total', FALSE)
            ->where('amount <', 0)
            ->where("date >", $date);
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('created_by', $user_id);

        $q = $this->db->get('pos_safe_transfers');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    public function getSafeTotals($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db
            ->select('SUM(amount) as total', FALSE)
            ->where('amount >', 0)
            ->where("date >", $date);
        $this->db->where('created_by', $user_id);
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));

        $q = $this->db->get('pos_safe_transfers');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSerials($type, $id = NULL, $term, $verify = FALSE)
    {
        if ($type == 'repair' || $type == 'other' || $type == 'accessory'|| $type == 'phones') {
            $this->db
                ->select('serial_number')
                ->where('serial_number != ', NULL)
                ->where("inventory_type", $type)
                ->where("inventory_id", $id)
                ->where('store_id', (int)$this->session->userdata('active_store'));

            if ($verify) {
                $this->db->where("serial_number", $term);
            }else{
                $this->db->like("serial_number", $term);
            }

            $q = $this->db->get('stock');
            if ($q->num_rows() > 0) {
                return $q->result();
            }
        }else{
            return FALSE;
        }
       
    }


    /////// SALES ///////
       public function getInvoiceByID($id)
    {
        $q = $this->db->get_where('sales', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    

     public function getRepairItemsByID($id)
    {
        $q = $this->db->get_where('sale_items', array('repair_id' => $id));
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return [];
    }

     
    public function getAllInvoiceItems($sale_id, $repair_items_as_rows = false)
    {
        $this->db->select('*, item_type as type, product_name as name, unit_price as price, unit_cost as cost ')
            ->group_by('sale_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('sale_items', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                
                $row->is_item = false;
                $row->has_items = false;
                $row->ritems = false;
                if ($row->type == 'crepairs' || $row->type == 'drepairs') {
                    $row->cost = 0;
                    $repair_items = $this->getRepairItemsByID($row->product_id);
                    $row->ritems = $repair_items;
                    foreach ($repair_items as $repair_item) {
                        $row->cost += $repair_item->unit_cost;
                    }

                    if ($repair_items_as_rows && count($row->ritems) > 1) {
                        $row->has_items = true;
                    }

                }


                $row->variants = NULL;
                $row->variable_price = FALSE;
                $row->items_restock = 0;
                $data[] = $row;

                if ($repair_items_as_rows && $row->ritems && count($row->ritems) > 1) {
                    foreach ($row->ritems as $ritem) {
                        $ritem->type = $ritem->item_type;
                        $ritem->name = $ritem->product_name;
                        $ritem->price = $ritem->unit_price;
                        $ritem->cost = $ritem->unit_cost;
                        $ritem->has_items = false;
                        $ritem->is_item = true;
                        
                        $data[] = $ritem;
                    }
                }
                

            }
            return $data;
        }
        return FALSE;
    }
    public function getRegisterRefunds($date, $user_id = NULL)
    {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( amount, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', FALSE)
            ->where('type', 'returned')->where('payments.date >', $date);
        $this->db->where('payments.created_by', $user_id);
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getProductBarcodesByTypeAndID($term, $type){
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . site_url('welcome') . "'; }, 10);</script>");
        }
        $rows = [];
        if ($type == 'new_phone') {
            $rows = $this->pos_model->getNewPhonesByID($term);
        }
        if ($type == 'used_phone') {
            $rows = $this->pos_model->getUsedPhonesByID($term);
        }
        if ($type == 'accessory') {
            $rows = $this->pos_model->getAccessoryNamesByID($term);
        }
        if ($type == 'other') {
            $rows = $this->pos_model->getOthersByID($term);
        }
        if ($type == 'repair') {
            $rows = $this->pos_model->getProductNamesByID($term);
        }
        $rows = array_filter((array)$rows);
        if ($rows) {
            $c = str_replace(".", "", microtime(true));
            $r = 0;
            foreach ($rows as $row) {
                if ($row->type == 'crepairs' or $row->type == 'drepairs') {
                    $item_id = $row->type.$row->id;
                    $row_id = $row->type.$row->id;
                }else{
                    $item_id = $row->type.($c + $r);
                    $row_id = $row->type.time();
                }
                $pr = array(
                    'row_id' => $row_id,
                    'item_id' => $item_id, 
                    'label' => $row->name . " (" . $row->code . ")", 
                    'code' => $row->code, 
                    'name' => $row->name, 
                    'qty' => $row->qty, 
                    'type' => $row->type, 
                    'price' => $row->price, 
                );
                $r++;
            }
            return $pr;
        } else {
            return FALSE;
        }
    }



    public function getProductByTypeAndID($term, $type){
        $rows = [];
        if ($type == 'new_phone') {
            $rows = $this->pos_model->getNewPhonesByID($term);
        }
        if ($type == 'used_phone') {
            $rows = $this->pos_model->getUsedPhonesByID($term);
        }
        if ($type == 'accessory') {
            $rows = $this->pos_model->getAccessoryNamesByID($term);
        }
        if ($type == 'other') {
            $rows = $this->pos_model->getOthersByID($term);
        }
        if ($type == 'repair') {
            $rows = $this->pos_model->getProductNamesByID($term);
        }
        $rows = array_filter((array)$rows);
        if (!empty($rows)) {
            return $rows[0];
        }
        return false;
    }
    public function getProductBarcodes($term, $limit = 5){
        $repairs = $this->getProductNames($term);
        $others = $this->getOthers($term);
        $accessory = $this->getAccessoryNames($term);
        $new_phones = $this->getNewPhones($term);
        $used_phones = $this->getUsedPhones($term);
        $data = array();
        $data = array_merge((array)$repairs, (array)$accessory, (array)$new_phones, (array)$used_phones,(array)$others);
        return $data;
    }


    
    public function getPaymentByID($id)
    {
        $q = $this->db->get_where('payments', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function deletePayment($id)
    {
        $opay = $this->getPaymentByID($id);

        if ($this->db->delete('payments', array('id' => $id))) {
            $this->syncSalePayments($opay->sale_id);

            $this->settings_model->addLog('delete-payment', 'pos-sale', $opay->sale_id, json_encode(array(
                'payment'=>$opay,
            )));

            return true;
        }
        return FALSE;
    }

    public function addPayment($data = array())
    {
        $settings = $this->settings_model->getSettings();
        unset($data['cc_cvv2']);
        if ($this->db->insert('payments', $data)) {
            $payment_id = $this->db->insert_id();
            $sale = $this->getSaleByID($data['sale_id']);
            if ($this->repairer->getReference('pay') == $data['reference_no']) {
                $this->repairer->updateReference('pay');
            }
            $this->syncSalePayments($data['sale_id']);

            $this->settings_model->addLog('add-payment', 'pos-sale', $data['sale_id'], json_encode(array(
                'sale'=>$sale,
            )));
            return true;
        }

        return false;
    }

    public function updatePayment($id, $data = array())
    {
        $settings = $this->settings_model->getSettings();
        $opay = $this->getPaymentByID($id);
        if ($this->db->update('payments', $data, array('id' => $id))) {
            $sale = $this->getSaleByID($data['sale_id']);
            
            $this->syncSalePayments($data['sale_id']);

            $this->settings_model->addLog('update-payment', 'pos-sale', $data['sale_id'], json_encode(array(
                'sale'=>$sale,
            )));
            return true;
        }
        return false;
    }


    public function getSaleByID($id) {
        $q = $this->db->get_where('sales', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getCustomerByID($id) {
        $q = $this->db->get_where('clients', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getSalePayments($sale_id) {
        $q = $this->db->get_where('payments', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return [];
    }

    public function getBillerByID($id) {
        $q = $this->db->get_where('users', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


    function getSetting()
    {
        $q = $this->db->get('pos_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    function updateSetting($data)
    {
        $q = $this->db->update('pos_settings', $data);
        return TRUE;
    }

}
