<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Purchases_model extends CI_Model
{

    function __construct() {
        parent::__construct();
    }


    public function getAllCompanies($group_name) {
        $this->db->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
        $q = $this->db->get('suppliers');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCompanyByID($id) {
        $q = $this->db->get_where('suppliers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTaxRateByID($id) {
        $q = $this->db->get_where('tax_rates', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    public function getUser($id = NULL) {
        if (!$id) {
            $id = $this->session->userdata('user_id');
        }
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $q = $this->db->get_where('users', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    
    public function getPurchaseCount()
    {
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $this->db->from('purchases');
        return $this->db->count_all_results()+1;
    }
    
     public function getReference() {
        
        $prefix = $this->controller->mSettings->purchase_prefix;
        $ref_no = (!empty($prefix)) ? $prefix . '/' : '';
        $seq_number = $this->getPurchaseCount();

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

    public function getSupplierSuggestions($term, $limit = 10)
    {
        $this->db->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
        $this->db->select("id, (CASE WHEN company = '-' THEN name ELSE CONCAT(company, ' (', name, ')') END) as text", FALSE);
        $this->db->where(" (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR company LIKE '%" . $term . "%' OR email LIKE '%" . $term . "%' OR phone LIKE '%" . $term . "%') ");
        $q = $this->db->get('suppliers', $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getAllTaxRates() {
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }


    public function getAVCOcost($item_type = 'repair', $product_id = null) {
        $q = $this->db
                ->where('item_type', $item_type)
                ->where('product_id', $product_id)
                ->get('sale_items');
        $sum = 0;
        $total = 0;
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $sum += $row->unit_cost;
                $total += 1;
            }
            return $sum/$total;
        }

        $q = $this->db
                ->where('inventory_type', $item_type)
                ->where('inventory_id', $product_id)
                ->get('stock');
        $sum = 0;
        $total = 0;
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $sum += $row->price;
                $total += 1;
            }
            return $sum/$total;
        }


        return FALSE;
    }

    public function getProductNames($term, $limit = 5)
    {


        // $this->db->where("(name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");
        $search = explode(' ', $term);
        $where = array();
        foreach ($search as $val) {
            $where[] = "(name LIKE '%" . $val . "%' )";
        }
        $where = implode(' AND ', $where);

        $this->db->order_by('name');



        $this->db->where($where);

        $this->db->limit($limit);
        $this->db->select('id as id, name as name, code as code, taxable, tax_rate as tax_rates, is_serialized');
        $this->db->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
        $q = $this->db->where('isDeleted != ', 1)->get('inventory');

        // AVCO
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $row->type = 'repair';
                $cost = $this->getAVCOcost($row->type, $row->id) ?? 0;
                $row->cost = $cost;
                $row->unit_cost = $cost;
                $row->discount = 0;
                $row->qty = 1;

                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getAccessoryNames($term, $limit = 5)
    {

        $search = explode(' ', $term);
        $where = array();
        foreach ($search as $val) {
            $where[] = "(name LIKE '%" . $val . "%' )";
        }
        $where = implode(' AND ', $where);
        $this->db->where($where);
        $this->db->order_by('name');

        // $this->db->where("(name LIKE '%" . $term . "%' OR upc_code LIKE '%" . $term . "%' OR  concat(name, ' (', upc_code, ')') LIKE '%" . $term . "%')");
        $this->db->limit($limit);
        $this->db->select('id as id, name as name, upc_code as code, taxable, tax_id as tax_rates, is_serialized');
        $this->db->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
        $q = $this->db->where('deleted != ', 1)->get('accessory');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $row->type = 'accessory';

                $cost = $this->getAVCOcost($row->type, $row->id) ?? 0;
                $row->cost = $cost;
                $row->unit_cost = $cost;

                $row->discount = 0;
                $row->qty = 1;

                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getNewPhones($term, $limit = 5)
    {
        // $this->db->where("(phone_name LIKE '%" . $term . "%' OR model_name LIKE '%" . $term . "%' OR  concat(phone_name, ' (', model_name, ')') LIKE '%" . $term . "%')");

        $search = explode(' ', $term);
        $where = array();
        foreach ($search as $val) {
            $where[] = "(phone_name LIKE '%" . $val . "%' )";
        }
        $where = implode(' AND ', $where);
        $this->db->where($where);

        $this->db->order_by('phone_name');


        $this->db->where('phones.sold', 0);
        $this->db->limit($limit);
        $this->db->select('id as id, phone_name as name, model_name as code, taxable, tax_id as tax_rates');
        $q = $this->db->get('phones');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $row->type = 'new_phone';

                $cost = $this->getAVCOcost('phones', $row->id) ?? 0;
                $row->cost = $cost;
                $row->unit_cost = $cost;

                $row->discount = 0;
                $row->qty = 1;
                $row->is_serialized = 1;

                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getOthers($term, $limit = 5)
    {
        // $this->db->where("(name LIKE '%" . $term . "%' OR upc_code LIKE '%" . $term . "%' OR  concat(name, ' (', upc_code, ')') LIKE '%" . $term . "%')");
        $search = explode(' ', $term);
        $where = array();
        foreach ($search as $val) {
            $where[] = "(name LIKE '%" . $val . "%' )";
        }
        $where = implode(' AND ', $where);
        $this->db->where($where);
        $this->db->order_by('name');

        $this->db->limit($limit);
        $this->db->select('id as id, name as name, upc_code as code, taxable, is_serialized');
        $this->db->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
        $q = $this->db->where('deleted != ', 1)->get('other');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $row->type = 'other';

                $cost = $this->getAVCOcost($row->type, $row->id) ?? 0;
                $row->cost = $cost;
                $row->unit_cost = $cost;


                $row->discount = 0;
                $row->qty = 1;

                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getPurchaseProductNames($term, $limit = 10){
        $repairs = $this->getProductNames($term, $limit);
        $accessory = $this->getAccessoryNames($term, $limit);
        $others = $this->getOthers($term, $limit);
        $phones = $this->getNewPhones($term, $limit);

        $data = array();
        $data = array_merge((array)$repairs, (array)$accessory, (array)$others, (array)$phones);
        return $data;
    }
    public function getAllProducts()
    {
        $this->db->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
        $q = $this->db->where('isDeleted != ', 1)->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getProductByID($id, $type) {
        $data = NULL;
        if ($type == 'repair') {
            $this->db->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
            $q = $this->db->get_where('inventory', array('id' => $id, 'isDeleted' => 0), 1);
            if ($q->num_rows() > 0) {
                $data = $q->row();
            }
        }elseif ($type == 'accessory') {
            $this->db->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
            $q = $this->db->select('*, upc_code as code')->get_where('accessory', array('id' => $id, 'deleted' => 0), 1);
            if ($q->num_rows() > 0) {
                $data = $q->row();
            }
        }elseif ($type == 'new_phone') {
            $q = $this->db->select('*, phone_name as name, model_name as code')->get_where('phones', array('id' => $id), 1);
            if ($q->num_rows() > 0) {
                $data = $q->row();
            }
        }elseif ($type == 'used_phone') {
            $q = $this->db->select('*, phone_name as name, model_name as code')->get_where('phones', array('id' => $id), 1);
            if ($q->num_rows() > 0) {
                $data = $q->row();
            }
        }elseif ($type == 'other') {
            $this->db->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
            $q = $this->db->select('*, upc_code as code')->get_where('other', array('id' => $id, 'deleted' => 0), 1);
            if ($q->num_rows() > 0) {
                $data = $q->row();
            }
        }
        if ($data) {
            $data->type = $type;
            return $data;
        }
        return FALSE;
        
    }


 

    public function getProductByCode($code)
    {
        $q = $this->db->get_where('inventory', array('code' => $code, 'isDeleted' => 0, 'store_id'=>(int)$this->session->userdata('active_store')), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

 

    public function getAllPurchases()
    {
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getAllPurchaseItems($purchase_id)
    {
        $this->db->select('purchase_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, inventory.details as details, stock_type, stock.id as stock_id, phone_items.id as usedphone_id')
            ->join('stock', 'purchase_items.received_stock_id=stock.id', 'left')
            ->join('phone_items', 'purchase_items.received_stock_id=phone_items.id', 'left')
            ->join('inventory', 'inventory.id=purchase_items.product_id', 'left')
            ->join('tax_rates', 'tax_rates.id=purchase_items.tax_rate_id', 'left')
            ->group_by('purchase_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('purchase_items', array('purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }


    public function getAllPurchaseItems2($purchase_id)
    {
        $this->db->select('purchase_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, inventory.details as details, stock_type, stock.id as stock_id, phone_items.id as usedphone_id, SUM(purchase_items.quantity) as quantity, SUM(purchase_items.item_tax) as item_tax, SUM(purchase_items.discount) as discount, SUM(purchase_items.subtotal) as subtotal')
            ->join('stock', 'purchase_items.received_stock_id=stock.id', 'left')
            ->join('phone_items', 'purchase_items.received_stock_id=phone_items.id', 'left')
            ->join('inventory', 'inventory.id=purchase_items.product_id', 'left')
            ->join('tax_rates', 'tax_rates.id=purchase_items.tax_rate_id', 'left')
            ->group_by('purchase_items.product_id, purchase_items.stock_type')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('purchase_items', array('purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getItemByID($id)
    {
        $q = $this->db->get_where('purchase_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTaxRateByName($name)
    {
        $q = $this->db->get_where('tax_rates', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    
    public function getPurchaseByID($id)
    {
        $q = $this->db->select('*, date, updated_at')->get_where('purchases', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getCustomerPurchaseByID($id)
    {
        $q = $this->db->get_where('customer_purchases', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
     public function getAllCustomerPurchaseItems($id)
    {
        $q = $this->db->get_where('customer_purchase_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function deleteCustomerPurchase($id)
    {
        $this->db->delete('customer_purchases', array('id' => $id, ));
        $this->db->delete('customer_purchase_items', array('purchase_id' => $id, ));
        return TRUE;
    }
    
    public function getProductQtyByID($id)
    {
        $q = $this->db->get_where('inventory', array('id' => $id, 'isDeleted' => 0), 1);
        if ($q->num_rows() > 0) {
            return $q->row()->quantity;
        }
        return FALSE;
    }
    public function addPurchase($data, $items)
    {   
        $settings = $this->settings_model->getSettings();
        

        $status = $data['status'];
        if ($this->db->insert('purchases', $data)) {
            $purchase_id = $this->db->insert_id();

            if ($this->repairer->getReference('po') == $data['reference_no']) {
                $this->repairer->updateReference('po');
            }
            foreach ($items as $item) {
                $item['purchase_id'] = $purchase_id;
                if ($status == 'received') {
                    if ($item['stock_type'] == 'new_phone' || $item['stock_type'] == 'used_phone') {
                        $cost = ($item['cost']);
                        $price = ($item['price']);
                        $imei = ($item['imei']);
                        $phone_id = ($item['phone_id']);
                        
                        unset($item['price']);
                        unset($item['cost']);
                        unset($item['imei']);
                        unset($item['phone_id']);
                    } 
                }
                if ($data['status'] == 'returned') {
                    if ($item['stock_type'] == 'repair' ||
                        $item['stock_type'] == 'accessory' ||
                        $item['stock_type'] == 'other'
                    ) {
                        for($i = 0; $i < abs($item['quantity']); $i++) {
                            $this->db
                                ->where('inventory_type', $item['stock_type'] )
                                ->where('inventory_id', $item['product_id'])
                                ->where('in_state_of_transfer', 0)
                                ->where('store_id', (int)$this->session->userdata('active_store'))
                                ->order_by('modified_date', 'DESC')
                                ->limit(1);

                            $q = $this->db->get('stock');
                            if ($q->num_rows() > 0) {
                                $id = $q->row()->id;
                                $this->db->delete('stock', array('id' => $id));
                            }
                        }
                    }
                    if ($item['stock_type'] == 'new_phone')
                    {
                        $this->db
                            ->where('inventory_type', 'phones' )
                            ->where('inventory_id', $item['product_id'])
                            ->where('serial_number', $item['product_code'])
                            ->order_by('modified_date', 'DESC')
                            ->limit(1);
                        $q = $this->db->get('stock');
                        if ($q->num_rows() > 0) {
                            $id = $q->row()->id;
                            $this->db->delete('stock', array('id' => $id));
                        }
                    }
                    if ($item['stock_type'] == 'used_phone')
                    {
                        $this->db->delete('phones', array('id'=>$item['product_id']));
                        $this->db->delete('phone_items', array('phone_id'=>$item['product_id']));
                    }
                    $this->db->update('purchases', array('return_purchase_ref' => $data['return_purchase_ref'], 'surcharge' => $data['surcharge'],'return_purchase_total' => $data['grand_total'], 'return_id' => $purchase_id), array('id' => $data['purchase_id']));
                }

                $this->db->insert('purchase_items', $item);
                if ($status == 'received') {
                    if ($item['stock_type'] == 'new_phone' || $item['stock_type'] == 'used_phone') {
                        $item['cost'] = $cost;
                        $item['price'] = $price;
                        $item['imei'] = $imei;
                        $item['phone_id'] = $phone_id;
                    }
                    if ($item['stock_type'] == 'repair') {
                        for($i = 0; $i < $item['quantity']; $i++) {
                            $data = array(
                                'inventory_id' => $item['product_id'],
                                'inventory_type' => 'repair',
                                'price' => $item['unit_cost'],
                                'modified_date' => date('Y-m-d H:i:s'),
                                'store_id' => (int)$this->session->userdata('active_store'),
                                'in_state_of_transfer' => 0,
                            );
                            $this->db->insert('stock', $data);
                        }
                    }
                    if ($item['stock_type'] == 'accessory') {
                        for($i = 0; $i < $item['quantity']; $i++) {
                             $data = array(
                                'inventory_id' => $item['product_id'],
                                'inventory_type' => 'accessory',
                                'price' => $item['unit_cost'],
                                'modified_date' => date('Y-m-d H:i:s'),
                                'store_id' => (int)$this->session->userdata('active_store'),
                                'in_state_of_transfer' => 0,
                            );
                            $this->db->insert('stock', $data);
                        }
                    }
                    if ($item['stock_type'] == 'new_phone') {
                         $data = array(
                            'inventory_id' => $item['product_id'],
                            'inventory_type' => 'phones',
                            'price' => $item['cost'],
                            'modified_date' => date('Y-m-d H:i:s'),
                            'store_id' => (int)$this->session->userdata('active_store'),
                            'in_state_of_transfer' => 0,
                            'serial_number' => $item['imei'],
                        );
                        $this->db->insert('stock', $data);
                    }

                    if ($item['stock_type'] == 'used_phone') {
                        $data = array(
                            'phone_id' => $item['product_id'],
                            'cost' => $item['cost'],
                            'price' => $item['price'],
                            'imei' => $item['imei'],
                        );
                        $this->db->insert('phone_items', $data);
                    }
                    
                    if ($item['stock_type'] == 'other') {
                        for($i = 0; $i < $item['quantity']; $i++) {
                            $data = array(
                                'inventory_id' => $item['product_id'],
                                'inventory_type' => 'other',
                                'price' => $item['unit_cost'],
                                'modified_date' => date('Y-m-d H:i:s'),
                                'store_id' => (int)$this->session->userdata('active_store'),
                                'in_state_of_transfer' => 0,
                            );
                            $this->db->insert('stock', $data);
                        }
                    }
                }
            }

            
            $account_data = array(
                'type' => 'expense',
                // 'type' => $data['status'] == 'returned' ? 'deposit' : 'expense',
                'type_id' => 3,
                'amount' => $data['grand_total'],
                'date' => $data['date'],
                'recurring' => 0,
                'notes' => '',
                'user_id' =>$this->session->userdata('user_id'),
                'bank_id' => $settings->purchase_bank_id,
                'fund_type' => '',
                'to_from_id' => $data['supplier_id'],
                'to_from_name' => $data['supplier'],
                'created_at' => date('Y-m-d H:i:s'),
                'store_id' => (int)$this->session->userdata('active_store'),
                'sale_id' =>$purchase_id,
            );
            $this->db->insert('account_entries', $account_data);



            return $purchase_id;
        }
        return false;
    }

    public function updatePurchase($id, $data, $items = array())
    {
        $settings = $this->settings_model->getSettings();

        $opurchase = $this->getPurchaseByID($id);
        $oitems = $this->getAllPurchaseItems($id);
        $status = $data['status'];

        if ($this->db->update('purchases', $data, array('id' => $id)) && $this->db->delete('purchase_items', array('purchase_id' => $id))) {
            $purchase_id = $id;
            foreach ($items as $item) {
                $item['purchase_id'] = $purchase_id;
                if ($status == 'received') {
                    if ($item['stock_type'] == 'new_phone' || $item['stock_type'] == 'used_phone') {
                        $cost = ($item['cost']);
                        $price = ($item['price']);
                        $imei = ($item['imei']);
                        $phone_id = ($item['phone_id']);

                        unset($item['cost']);
                        unset($item['price']);
                        unset($item['imei']);
                        unset($item['phone_id']);
                    }
                }
                $this->db->insert('purchase_items', $item);
                if ($status == 'received') {
                    if ( $item['stock_type'] == 'new_phone' || $item['stock_type'] == 'used_phone' ) {
                        $item['cost'] = $cost;
                        $item['price'] = $price;
                        $item['imei'] = $imei;
                        $item['phone_id'] = $phone_id;
                    }
                }
                if ($status == 'received') {
                    if ($item['stock_type'] == 'repair') {
                        for($i = 0; $i < $item['quantity']; $i++) {
                            $stock_data = array(
                                'inventory_id'      => $item['product_id'],
                                'inventory_type'    => 'repair',
                                'price'             => $item['unit_cost'],
                                'modified_date'     => date('Y-m-d H:i:s'),
                                'store_id' => (int)$this->session->userdata('active_store'),
                                'in_state_of_transfer' => 0,
                            );
                            $this->db->insert('stock', $stock_data);
                        }
                    }
                    if ($item['stock_type'] == 'accessory') {
                        for($i = 0; $i < $item['quantity']; $i++) {
                             $stock_data = array(
                                'inventory_id'      => $item['product_id'],
                                'inventory_type'    => 'accessory',
                                'price'             => $item['unit_cost'],
                                'modified_date'     => date('Y-m-d H:i:s'),
                                'store_id' => (int)$this->session->userdata('active_store'),
                                'in_state_of_transfer' => 0,

                            );
                            $this->db->insert('stock', $stock_data);
                        }
                    }
                    if ($item['stock_type'] == 'new_phone') {
                         $data = array(
                            'inventory_id' => $item['product_id'],
                            'inventory_type' => 'phones',
                            'price' => $item['cost'],
                            'modified_date' => date('Y-m-d H:i:s'),
                            'store_id' => (int)$this->session->userdata('active_store'),
                            'in_state_of_transfer' => 0,
                            'serial_number' => $item['imei'],
                        );
                        $this->db->insert('stock', $data);
                    }
                    if ($item['stock_type'] == 'used_phone') {
                        $data = array(
                            'phone_id' => $item['product_id'],
                            'cost' => $item['cost'],
                            'price' => $item['price'],
                            'imei' => $item['imei'],
                        );
                        $this->db->insert('phone_items', $data);
                       
                    }
                    
                    if ($item['stock_type'] == 'other') {
                        for($i = 0; $i < $item['quantity']; $i++) {
                            $stock_data = array(
                                'inventory_id'      => $item['product_id'],
                                'inventory_type'    => 'other',
                                'price'             => $item['unit_cost'],
                                'modified_date'     => date('Y-m-d H:i:s'),
                                'store_id' => (int)$this->session->userdata('active_store'),
                                'in_state_of_transfer' => 0,
                                
                            );
                            $this->db->insert('stock', $stock_data);
                        }
                    }
                }

            }


            $this->db->where('sale_id', $id)->delete('account_entries');
            $account_data = array(
                'type' => 'expense',
                'type_id' => 3,
                'amount' => $data['grand_total'],
                'date' => $data['date'],
                'recurring' => 0,
                'notes' => '',
                'user_id' =>$this->session->userdata('user_id'),
                'bank_id' => $settings->purchase_bank_id,
                'fund_type' => '',
                'to_from_id' => $data['supplier_id'],
                'to_from_name' => $data['supplier'],
                'created_at' => date('Y-m-d H:i:s'),
                'store_id' => (int)$this->session->userdata('active_store'),
                'sale_id' =>$id,
            );
            $this->db->insert('account_entries', $account_data);

            return true;
        }
        return false;
    }

    public function deletePurchase($id)
    {
        $purchase = $this->getPurchaseByID($id);
        $purchase_items = $this->getAllPurchaseItems($id);
        if ($this->db->delete('purchase_items', array('purchase_id' => $id)) && $this->db->delete('purchases', array('id' => $id))) {
            if ($purchase->status == 'received') {
                redirect('panel/purchases');
            }
            return true;
        }
        return FALSE;
    }

    public function getPurchaseItemByID($id) {
        $q = $this->db->get_where('purchase_items', array('id'=>$id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function purchaseStatusChange($id)
    {
        $data = array(); 
        $this->db->where('purchase_id', $id);
        $pending = $this->db
                ->select('COUNT(id)')
                ->where('recieved', 0)
                ->get('purchase_items');
        $data[0] = $pending->row();
        $this->db->where('purchase_id', $id);
        $recieved = $this->db
                ->select('COUNT(id)')
                ->where('recieved', 1)
                ->get('purchase_items');
        $data[1] = $recieved->row();
    }
}
