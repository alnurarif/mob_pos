<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Commission_model extends CI_Model
{
	
    public function getRepairPartByID($id = NULL) {
        if($id){        
            $this->db->where('inventory.id', $id);
        }

        $this->db->where('isDeleted', 0);
        $this->db->where('(inventory.universal=1 OR inventory.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);
        $this->db->select('*, id as id, name as name, code as code');
        $q = $this->db->get('inventory');
        if ($q->num_rows() > 0) {
             if($id){
                return $q->row();
            }
            return $q->result();
        }
        return false;
    }
   
    public function getAccessoryByID($id = NULL)
    {
        if($id){
            $this->db->where('accessory.id', $id);
        }
        $this->db->where('(accessory.universal=1 OR accessory.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);

        $this->db->select('*, id as id, name as name, upc_code as code');
        $q = $this->db->where('deleted != ', 1)->get('accessory');
        if ($q->num_rows() > 0) {
             if($id){
                return $q->row();
            }
            return $q->result();
        }
        return FALSE;
    }


    public function getOtherByID($id = NULL)
    {
       if($id){
            $this->db->where('other.id', $id);
       }
        $this->db->where('(other.universal=1 OR other.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);
        $this->db->select('id as id, name as name, upc_code as code');
        $q = $this->db->where('deleted != ', 1)->get('other');
        if ($q->num_rows() > 0) {
             if($id){
                return $q->row();
            }
            return $q->result();
        }
        return FALSE;
    }

    public function getNewPhoneByID($id = NULL)
    {

        if($id){
            $this->db->where('phones.id', $id);
        }
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('type', 'new');
        $this->db->where('disable', 0);
        $this->db->where('phones.sold', 0);

        $this->db->select('*, phones.id as id, phone_name as name, (SELECT imei from phone_items where phones.id=phone_items.phone_id LIMIT 1) as code')
        ->join('phone_items', 'phone_items.phone_id=phones.id', 'left');

        $q = $this->db->get('phones');
        if ($q->num_rows() > 0) {
             if($id){
                return $q->row();
            }
            return $q->result();
        }
        return FALSE;
    }

    public function getUsedPhoneByID($id = NULL)
    {
        if($id){
            $this->db->where('phones.id', $id);
        }
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('type', 'used');
        $this->db->where('used_status', 1);
        $this->db->where('disable', 0);
        $this->db->where('phones.sold', 0);

        $this->db->select('*, phones.id as id, phone_name as name, (SELECT imei from phone_items where phones.id=phone_items.phone_id LIMIT 1) as code')
        ->join('phone_items', 'phone_items.phone_id=phones.id', 'left');

        $q = $this->db->get('phones');
        if ($q->num_rows() > 0) {
             if($id){
                return $q->row();
            }
            return $q->result();
        }
        return FALSE;
    }
    
    public function getAllPlanByID($id = NULL)
    {
        if($id){
            $this->db->where('plans.id', $id);
        }
        $this->db->where('(plans.universal=1 OR plans.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);
        $this->db->join('carriers', 'carriers.id=plans.carrier_id', 'left');
        $this->db->select('plans.id as id, carriers.name as name, carriers.name as code');
        $q = $this->db->where(array('plans.disable' => 0))->get('plans');
        if ($q->num_rows() > 0) {
            if($id){
                return $q->row();
            }
            return $q->result();

        }
        return FALSE;
    }
	
     public function getProductByIDAndType($id, $type)
    {
        if ($type == 'repair_parts') {
            $data = $this->getRepairPartByID($id);
        }elseif ($type == 'new_phones') {
            $data = $this->getNewPhoneByID($id);
        }elseif ($type == 'used_phones') {
            $data = $this->getUsedPhoneByID($id);
        }elseif ($type == 'accessories') {
            $data = $this->getAccessoryByID($id);
        }elseif ($type == 'other') {
            $data = $this->getOtherByID($id);
        }elseif ($type == 'plans') {
            $data = $this->getAllPlanByID($id);
        }
        return $data;
    }

     public function getRepairPartsNames($term = NULL, $limit = 5) {
        if ($term) {
            $this->db->where("(name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");
        }

        $this->db->where('isDeleted', 0);
        $this->db->where('(inventory.universal=1 OR inventory.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);
        $this->db->select('*, id as id, name as name, code as code');
        $q = $this->db->get('inventory');
        if ($q->num_rows() > 0) {

            foreach (($q->result()) as $row) {
                $data[] = array('id' => $row->id, 'text' => "$row->name  ($row->code)");              
            }
            return $data;
        }
        return false;
    }
   
    public function getAccessoryNames($term = NULL, $limit = 5)
    {
        if ($term) {
            $this->db->where("(name LIKE '%" . $term . "%' OR upc_code LIKE '%" . $term . "%' OR  concat(name, ' (', upc_code, ')') LIKE '%" . $term . "%')");
        }
        
        $this->db->where('(accessory.universal=1 OR accessory.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);

        $this->db->select('*, id as id, name as name, upc_code as code');
        $q = $this->db->where('deleted != ', 1)->get('accessory');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = array('id' => $row->id, 'text' => "$row->name  ($row->code)");              
            }
            return $data;
        }
        return FALSE;
    }


    public function getOthers($term = NULL, $limit = 5)
    {
        if ($term) {
            $this->db->where("(name LIKE '%" . $term . "%' OR upc_code LIKE '%" . $term . "%' OR  concat(name, ' (', upc_code, ')') LIKE '%" . $term . "%')");
        }

        $this->db->where('(other.universal=1 OR other.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);
        $this->db->select('id as id, name as name, upc_code as code');
        $q = $this->db->where('deleted != ', 1)->get('other');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = array('id' => $row->id, 'text' => "$row->name  ($row->code)");              
            }
            return $data;
        }
        return FALSE;
    }

    public function getNewPhones($term = NULL, $limit = 5)
    {

        if ($term) {
            $this->db->where("(imei LIKE '%" . $term . "%' OR phone_name LIKE '%" . $term . "%' OR model_name LIKE '%" . $term . "%' OR  concat(phone_name, ' (', model_name, ')') LIKE '%" . $term . "%')");
        }

        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('type', 'new');
        $this->db->where('disable', 0);
        $this->db->where('phones.sold', 0);

        $this->db->select('*, phones.id as id, phone_name as name, (SELECT imei from phone_items where phones.id=phone_items.phone_id LIMIT 1) as code')
        ->join('phone_items', 'phone_items.phone_id=phones.id', 'left');

        $q = $this->db->get('phones');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = array('id' => $row->id, 'text' => "$row->name  ($row->code)");              
            }
            return $data;
        }
        return FALSE;
    }

    public function getUsedPhones($term = NULL, $limit = 5)
    {
        if ($term) {
            $this->db->where("(phone_name LIKE '%" . $term . "%' OR imei LIKE '%" . $term . "%' OR  concat(phone_name, ' (', model_name, ')') LIKE '%" . $term . "%')");
        }

        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('type', 'used');
        $this->db->where('used_status', 1);
        $this->db->where('disable', 0);
        $this->db->where('phones.sold', 0);

        $this->db->select('*, phones.id as id, phone_name as name, (SELECT imei from phone_items where phones.id=phone_items.phone_id LIMIT 1) as code')
        ->join('phone_items', 'phone_items.phone_id=phones.id', 'left');

        $q = $this->db->get('phones');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = array('id' => $row->id, 'text' => "$row->name  ($row->code)");              
            }
            return $data;
        }
        return FALSE;
    }
    
    public function getAllPlans($term = NULL, $limit=5)
    {
        if ($term) {
            $this->db->where("name LIKE '%" . $term . "%'");
        }
        $this->db->where('(plans.universal=1 OR plans.store_id='.(int)$this->session->userdata('active_store').')',NULL, FALSE);
        $this->db->join('carriers', 'carriers.id=plans.carrier_id', 'left');
        $this->db->select('plans.id as id, carriers.name as name, carriers.name as code');
        $q = $this->db->where(array('plans.disable' => 0))->get('plans');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) 
            {
                $data[] = array('id' => $row->id, 'text' => "$row->name");              
            }
            return $data;
        }
        return FALSE;
    }
    

    public function getCommisionPlans()
    {
        $q = $this->db
            ->select('id, label') 
            ->from('commission')
            ->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[$row->id] = $row->label;       
            }
            return $data;
        }
        return FALSE;
    }
    public function verifyAssigning($type = NULL, $category = NULL, $product = NULL, $groups = NULL, $id = NULL)
    {
        if (!$type && !$category && !$groups) {
            die();
        }
         if ($type == 'product') {
            $q = $this->db
                ->select('GROUP_CONCAT(groups) as groups')
                ->where('category', $category)
                ->where('id !=', $id)
                ->where('product_id', $product)
                ->get('product_commission');
            if ($q->num_rows() > 0) {
                $match = array();
                $result = $q->row_array();
                $all_groups = explode(',',$result['groups']);
                $match = array_intersect($all_groups, $groups);
                if (!empty($match)) {
                    $message = array();
                    foreach ($match as $group) {
                        $group_name = $this->settings_model->getGroupByID($group);
                        $group_name = $group_name ? $group_name->name : NULL;

                        $product_name = $this->commission_model->getProductByIDAndType($product, $category);
                        $product_name = $product_name ? $product_name->name : NULL;

                        if ($group_name !== NULL) {
                            $message = sprintf(lang('commission_structure_not_accepted'), $group_name, $product_name);
                            $message[] = $message;
                        }
                    }
                    return json_encode(array(
                        'success' => false,
                        'msg' => $message,
                    ));
                }else{
                     return json_encode(array(
                        'success' => true,
                        'msg' => null,
                    ));
                }
                die();
            }else{
                return json_encode(array(
                    'success' => true,
                    'msg' => null,
                ));
                die();
            }
        }elseif($type == 'group'){
            $q = $this->db
                ->select('groups')
                ->where('category', $category)
                ->where('id !=', $id)
                ->get('category_commission');

            if ($q->num_rows() > 0) {
                $match = array();
                $result = $q->row_array();
                $all_groups = explode(',',$result['groups']);
                $match = array_intersect($all_groups, $groups);
                if (!empty($match)) {
                    $message = array();
                    foreach ($match as $group) {
                        $group_name = $this->settings_model->getGroupByID($group);
                        $group_name = $group_name ? $group_name->name : NULL;
                        if ($group_name !== NULL) {
                            $message = sprintf(lang('commission_structure_not_accepted_category'), $group_name, $category);
                            $message[] = $message;
                        }
                    }
                    return json_encode(array(
                        'success' => false,
                        'msg' => $message,
                    ));
                }else{
                     return json_encode(array(
                        'success' => true,
                        'msg' => null,
                    ));
                }
                die();
            }else{
                return json_encode(array(
                    'success' => true,
                    'msg' => null,
                ));
                die();
            }
        }else{
            return json_encode(array(
                'success' => false,
                'msg' => null,
            ));
            die();
        }
    }

    public function getLabel($type)
    {
        if ($type == 'repair_parts') {
            return lang('Repair Parts');
        }elseif ($type == 'new_phones') {
            return lang('New Phones');
        }elseif ($type == 'used_phones') {
            return lang('Used Phones');
        }elseif ($type == 'accessories') {
            return lang('Accessories');
        }elseif ($type == 'other') {
            return lang('Other Products');
        }elseif ($type == 'plans') {
            return lang('Cellular Plans');
        }
        return $data;
    }
}
