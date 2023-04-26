<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Countstock_model extends CI_Model {
    public function getProductNames() {
        $this->db->where('isDeleted', 0);
        $this->db->where('(inventory.universal=1 OR inventory.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);
        $this->db->select('*, id as id, name as name, code as code, taxable, tax_rate as tax_rates, (SELECT price from stock where stock.inventory_id=inventory.id AND stock.inventory_type=\'repair\' AND in_state_of_transfer = 0  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date ASC LIMIT 1) as cost, (SELECT id from stock where stock.inventory_id=inventory.id AND stock.inventory_type=\'repair\' AND in_state_of_transfer = 0  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date ASC LIMIT 1) as stock_id');
        $q = $this->db->get('inventory');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $row->type = 'repair';
                $row->qty = $this->countStock('repair', $row->id);
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }
   
    public function getAccessoryNames($term = NULL, $limit = 5)
    {
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

    public function getOthers($term = NULL, $limit = 5)
    {
        $data = array();
        $this->db->where('(other.universal=1 OR other.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);
        $this->db->where('other.keep_stock', 1);
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
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('type', 'used');
        $this->db->where('used_status', 1);
        $this->db->where('disable', 0);
        $this->db->where('phones.sold', 0);

        $this->db->select('*, phones.id as id, phone_name as name, taxable, tax_id as tax_rates, (SELECT imei from phone_items where phones.id=phone_items.phone_id LIMIT 1) as code')
        ->join('phone_items', 'phone_items.phone_id=phones.id', 'left');

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

    public function countStock($type, $id) {
        $q = $this->db
            ->select('COUNT(id) as count')
            ->where(array('inventory_type' => $type, 'inventory_id'=>$id, 'store_id'=>(int)$this->session->userdata('active_store'), 'selected'=>0))
            ->get('stock');
        return $q->row()->count;
    }
    public function getAllProductNames($products = 'all'){
        $repairs        = ($products == 'all' or $products == 'repair_parts') ? $this->getProductNames() : array();
        $others         = ($products == 'all' or $products == 'others') ? $this->getOthers() : array();
        $accessory      = ($products == 'all' or $products == 'accessories') ? $this->getAccessoryNames() : array();
        $new_phones     = ($products == 'all' or $products == 'new_phones') ? $this->getNewPhones() : array();
        $used_phones    = ($products == 'all' or $products == 'used_phones') ? $this->getUsedPhones() : array();
        $data = array();
        $data = array_filter(array_merge((array)$repairs, (array)$accessory, (array)$new_phones, (array)$used_phones, (array)$others));
        return $data;
    }

    // Products By ID
    public function getProductNameByID($id, $serialed = FALSE)
    {
        $qty = $this->countStock('repair', $id);
        if ($qty > 0) {
            $this->db->where('inventory.id', $id);
            $this->db->where('isDeleted', 0);
            $this->db->where('(inventory.universal=1 OR inventory.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);
            $this->db->select('inventory.*, inventory.id as id, name as name, code as code, is_serialized, category, sub_category, (SELECT GROUP_CONCAT(CONCAT(id, "____", price)) from stock where stock.inventory_id=inventory.id AND stock.inventory_type=\'repair\'  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date DESC) as stock_data, inventory.price as price');
            if ($serialed) {
                $this->db->select('inventory.*, stock.*, inventory.id as id, name as name, code as code, is_serialized, category, sub_category, (SELECT GROUP_CONCAT(CONCAT(id, "____", price)) from stock where stock.inventory_id=inventory.id AND stock.inventory_type=\'repair\'  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date DESC) as stock_data, inventory.price as price, stock.price as item_cost');
                $this->db->join('stock', 'stock.inventory_id=inventory.id AND stock.inventory_type=\'repair\' AND in_state_of_transfer = 0  AND stock.store_id='.(int)$this->session->userdata('active_store').' and selected = 0', 'left');
            }
            $q = $this->db->get('inventory');
            if ($q->num_rows() > 0) {
                if ($serialed) {
                    return $q->result();
                }
                return $q->row();
            }
            return FALSE;
        }
    }
    public function getAccessoryByID($id, $serialed = FALSE)
    {
        $qty = $this->countStock('accessory', $id);
        if ($qty > 0) {
            $this->db->where('accessory.id', $id);
            $this->db->where('(accessory.universal=1 OR accessory.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);
            $this->db->select('accessory.*, accessory.id as id, name as name, upc_code as code, is_serialized, category, sub_category, (SELECT GROUP_CONCAT(CONCAT(id, "____", price)) from stock where stock.inventory_id=accessory.id AND stock.inventory_type=\'accessory\'  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date DESC) as stock_data, accessory.price as price');
            if ($serialed) {
                $this->db->select('accessory.*, stock.*,accessory.id as id, name as name, upc_code as code, is_serialized, category, sub_category, (SELECT GROUP_CONCAT(CONCAT(id, "____", price)) from stock where stock.inventory_id=accessory.id AND stock.inventory_type=\'accessory\'  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date DESC) as stock_data, accessory.price as price, stock.price as item_cost');

                $this->db->join('stock', 'stock.inventory_id=accessory.id AND stock.inventory_type=\'accessory\' AND in_state_of_transfer = 0  AND stock.store_id='.(int)$this->session->userdata('active_store').' and selected = 0', 'left');
            }
            $q = $this->db->where('deleted != ', 1)->get('accessory');
            if ($q->num_rows() > 0) {
                if ($serialed) {
                    return $q->result();
                }
                return $q->row();
            }
        }
        return FALSE;
    }
     public function getOtherByID($id, $serialed = FALSE)
    {
        $qty = $this->countStock('other', $id);
        if ($qty > 0) {
            $this->db->where('other.id', $id);
            $this->db->where('other.keep_stock', 1);
            $this->db->where('(other.universal=1 OR other.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);
            $this->db->select('other.*, other.id as id, name as name, upc_code as code, is_serialized, category, sub_category, (SELECT GROUP_CONCAT(CONCAT(id, "____", price)) from stock where stock.inventory_id=other.id AND stock.inventory_type=\'other\'  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date DESC) as stock_data, other.price as price');
            if ($serialed) {
                $this->db->select('other.*, stock.*, other.id as id, name as name, upc_code as code, is_serialized, category, sub_category, (SELECT GROUP_CONCAT(CONCAT(id, "____", price)) from stock where stock.inventory_id=other.id AND stock.inventory_type=\'other\'  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date DESC) as stock_data, other.price as price, stock.price as item_cost');
                $this->db->join('stock', 'stock.inventory_id=other.id AND stock.inventory_type=\'other\' AND in_state_of_transfer = 0  AND stock.store_id='.(int)$this->session->userdata('active_store').' and selected = 0', 'left');
            }
            $q = $this->db->where('deleted != ', 1)->get('other');
            if ($q->num_rows() > 0) {
                if ($serialed) {
                    return $q->result();
                }
                return $q->row();
            }
        }
        return FALSE;
    }
    public function getProductNamesByID($id)
    {
        $row = $this->getProductNameByID($id);
        if ($row) {
            if ($row->is_serialized) {
                $rows = $this->getProductNameByID($id, TRUE);
                if ($rows) {
                    foreach ($rows as $nrow) {
                        $nrow->is_serialized = 1;
                        $nrow->type = 'repair';
                        $nrow->qty = 1;
                        $data[] = $nrow;
                    }
                }
            }else{
                $row->type = 'repair';
                $row->qty = $this->countStock('repair', $row->id);
                $data[] = $row;
            }
            return ($data);
        }
        return FALSE;
    }

    // Accessories By ID

    public function getAccessoryNamesByID($id)
    {
        $row = $this->getAccessoryByID($id);
        if ($row) {
            if ($row->is_serialized) {
                $rows = $this->getAccessoryByID($id, TRUE);
                if ($rows) {
                    foreach ($rows as $nrow) {
                        $nrow->is_serialized = 1;
                        $nrow->type = 'accessory';
                        $nrow->qty = 1;
                        $data[] = $nrow;
                    }
                }
            }else{
                $row->type = 'accessory';
                $row->qty = $this->countStock('accessory', $row->id);
                $data[] = $row;
            }
            return ($data);
        }
        return FALSE;
    }
    
   
    public function getOthersByID($id)
    {
        $row = $this->getOtherByID($id);
        if ($row) {
            if ($row->is_serialized) {
                $rows = $this->getOtherByID($id, TRUE);
                if ($rows) {
                    foreach ($rows as $nrow) {
                        $nrow->is_serialized = 1;
                        $nrow->type = 'other';
                        $nrow->qty = 1;
                        $data[] = $nrow;
                    }
                }
            }else{
                $row->type = 'other';
                $row->qty = $this->countStock('other', $row->id);
                $data[] = $row;
            }
            return ($data);
        }
        return FALSE;
    }



    public function getNewPhonesByID($id)
    {
        $stock = $this->countStock('phones', $id);
        if ($stock > 0) {
            $this->db->where('phones.id', $id);
            $this->db->where('type', 'new');
            $this->db->where('phones.store_id', (int)$this->session->userdata('active_store'));
            $this->db->select('phones.id as id, phone_name as name, model_name as code, serial_number, category, sub_category, (SELECT GROUP_CONCAT(CONCAT(id, "____", price)) from stock where stock.inventory_id=phones.id AND stock.inventory_type=\'phones\'  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date DESC) as stock_data, phones.price as price, stock.price as item_cost')
            ->join('stock', 'stock.inventory_id=phones.id AND stock.inventory_type=\'phones\' AND in_state_of_transfer = 0  AND stock.store_id='.(int)$this->session->userdata('active_store').' and selected = 0', 'left');
            $q = $this->db->where('disable', 0)->get('phones');
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $row->is_serialized = 1;
                    $row->type = 'new_phone';
                    $row->qty = 1;
                    $data[] = $row;
                }
                return $data;
            }
            return FALSE;
        }
        return FALSE;
    }

    public function getUsedPhonesByID($id)
    {
        $this->db->where('phones.id', $id);
        $this->db->where('type', 'used');
        $this->db->where('disable', 0);
        $this->db->where('phones.sold', 0);
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $this->db->select('*, phones.id as id, phone_name as name, model_name as code, category, sub_category');
        $q = $this->db->get('phones');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $item = $this->db->select('imei,id,cost,price')->get_where('phone_items', array('phone_id'=>$row->id));
                if ($item->num_rows() > 0) {
                    $row->stock_data = $item->row()->id."____".$item->row()->cost;
                    $row->item_cost = $item->row()->cost;
                    $row->type = 'used_phone';
                    $row->qty = 1;
                    $row->serial_number = $item->row()->imei;
                    $row->price = $item->row()->price;
                    $row->is_serialized = 1;
                    $data[] = $row;
                }else{
                    return FALSE;
                }
            }
            return $data;
        }
        return FALSE;
    }

    public function getProductDataByTypeAndID($type, $term){
       
        if ($type == 'new_phone') {
            $rows = $this->getNewPhonesByID($term);
        }
        if ($type == 'used_phone') {
            $rows = $this->getUsedPhonesByID($term);
        }
        if ($type == 'accessory') {
            $rows = $this->getAccessoryNamesByID($term);
        }
        if ($type == 'other') {
            $rows = $this->getOthersByID($term);
        }
        if ($type == 'repair') {
            $rows = $this->getProductNamesByID($term);
        }
        $rows = array_filter((array)$rows);
        if ($rows) {
            $c = str_replace(".", "", microtime(true));
            $r = 0;
            foreach ($rows as $row) {
                $item_id = $row->type.($c + $r);
                $row_id = $row->type.time();
                $pr[$item_id] = array(
                    'row_id'        => $row_id,
                    'item_id'       => $item_id, 
                    'label'         => $row->name . " (" . $row->code . ")", 
                    'code'          => $row->code, 
                    'name'          => $row->name, 
                    'qty'           => $row->qty, 
                    'type'          => $row->type, 
                    'product_id'    => $row->id,
                    'serial'        => $row->is_serialized ? $row->serial_number : NULL,
                    'cost'          => $row->is_serialized ? $row->item_cost : NULL,
                    'is_serialized' => $row->is_serialized,
                    'price'         => $row->price,
                    'stock_data'    => $row->stock_data,
                    'category'      => $this->getCategoryName($row->category),
                    'sub_category'  => $this->getCategoryName($row->sub_category),
                    'counted_qty'   => 0,
                );
                $r++;
            }
            return array_filter($pr);
        } 
    }

    public function getCategoryName($id) {
        $q = $this->db->where('id', $id)->select('name')->get('categories');
        if ($q->num_rows() > 0) {
            return $q->row()->name;
        }
        return 'No Category';
    }
    
}
