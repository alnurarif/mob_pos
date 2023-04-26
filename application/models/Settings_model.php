<?php

/*
|--------------------------------------------------------------------------
| Setting model file
|--------------------------------------------------------------------------
| 
*/

class Settings_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

	/*------------------------------------------------------------------------
	| GET THE LANGUAGE
	| @return Language slug
	|--------------------------------------------------------------------------*/
    public function get_language()
    {
        $data = array();
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function isClockedIn($id, $store_id) {
        $get_record = $this->db->get_where('timeclock', array('user_id' => $id, 'clock_out'=>NULL, 'store_id' => $store_id));
        if ($get_record->num_rows() > 0) {
            return $get_record->row();
        }else{
            return FALSE;
        }
    }
    /*------------------------------------------------------------------------
    | Check User Group
    | @return GroupRow
    |--------------------------------------------------------------------------*/
    public function checkGroupUsers($id)
    {
        $q = $this->db->get_where('users', ['group_id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    /*------------------------------------------------------------------------
    | Delete User Group
    | @return true/false
    |--------------------------------------------------------------------------*/
    public function deleteGroup($id)
    {
        if ($this->db->delete('groups', array('id' => $id))) {
            $this->db->delete('permissions', array('group_id'=>$id));
            return true;
        }
        return FALSE;
    }
    /*------------------------------------------------------------------------
    | Get User Groups
    | @return GroupResult
    |--------------------------------------------------------------------------*/
    public function getGroups()
    {
        $this->db->where('id >', 1);
        $q = $this->db->get('groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }


    public function getTaxRates($getAll = FALSE)
    {
        if ($getAll) {
            $this->db->where(array('disable' => 0));
        }

        $data = array();
        $query = $this->db->get('tax_rates');
        if ($query->num_rows() > 0) {
            $data = $query->result();
        }

        return $data;
    }
    public function getManufacturers($getAll = FALSE)
    {
        if ($getAll) {
            $this->db->where(array('disable' => 0));
        }
        $this->db->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);

        $data = array();
        $query = $this->db->get('manufacturers');
        if ($query->num_rows() > 0) {
            $data = $query->result();
        }

        return $data;
    }
    
    public function getOnlyManufacturers($getAll = FALSE)
    {
        if ($getAll) {
            $this->db->where(array('disable' => 0));
        }

        $this->db->where('(parent_id <= 0 OR parent_id IS NULL)' , NULL, FALSE);
        $this->db->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);

        $data = array();
        $query = $this->db->get('manufacturers');
        if ($query->num_rows() > 0) {
            $data = $query->result();
        }

        return $data;
    }
    public function getCarriers($getAll = FALSE)
    {
        if ($getAll) {
            $this->db->where(array('disable' => 0));
        }
        $this->db->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);

        $data = array();
        $query = $this->db->get('carriers');
        if ($query->num_rows() > 0) {
            $data = $query->result();
        }

        return $data;
    }



    public function getDueActivities($store_id)
    {
        $this->db
                ->join('clients', 'clients.id=client_activity.client_id')
                ->join('activities as a1', 'client_activity.activity_id=a1.id')
                ->join('activities as a2', 'client_activity.subactivity_id=a2.id')
                ->where('status', 'open')
                ->where('remind_date <= NOW()', NULL, FALSE)
                ->order_by("FIELD(priority, 'high', 'normal', 'low')", NULL, FALSE);

        $data = array();
        $query = $this->db->select('CONCAT(clients.first_name, " ", clients.last_name) as name, client_activity.client_id, a1.name as activity, a2.name as subactivity, locations')->get('client_activity');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $locations = explode(',', $row->locations);
                if (in_array($store_id, $locations)) {
                    $data[] = $row;
                }
            }
        }
        return $data;
    }

    public function getDuePlanReminders()
    {
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        $this->db->where('set_reminder', 1);
        $this->db->join('plan_items', 'plan_items.id=sale_items.option_id', 'left');
        $data = array();
        $query = $this->db->select('*')->get('sale_items');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
        }
        return $data;
    }
    
    public function getTaxRateByID($id) {
        $q = $this->db->get_where('tax_rates', array('id' => $id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getStoreByID($id) {
        $q = $this->db->get_where('store', array('id' => $id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllStores($getLocked = FALSE, $getDeleted = FALSE)
    {
        if (!$getLocked) {
            $this->db->where('locked', 0);
        }
        if (!$getDeleted) {
            $this->db->where('deleted', 0);
        }
        $data = array();
        $query = $this->db->get('store');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $data[$row['id']] = $row;
            }
        }
        return $data;
    }

    public function getAllActivities($only_parent = TRUE)
    {
        $this->db->where(array('disable' => 0));
        if ($only_parent) {
            $this->db->where('sub_id is NULL', NULL, FALSE);
        }else {
            $this->db->where('sub_id is NOT NULL', NULL, FALSE);
        }
        $data = array();
        $query = $this->db->get('activities');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getAllCategories($only_parent = TRUE)
    {
        $this->db->where(array('disable' => 0));
        if ($only_parent) {
            $this->db->where('sub_id is NULL', NULL, FALSE);
        }else {
            $this->db->where('sub_id is NOT NULL', NULL, FALSE);
        }
        $data = array();
        $data[] =  array('id'=>'', 'name'=>lang('select_placeholder'));
        $query = $this->db->get('categories');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $data[] = $row;
            }
        }
        return $data;
    }   

    public function getAllWarranties()
    {
        $this->db->where(array('disable' => 0));
        $data = array();
        $this->db->select('warranty_duration, warranty_duration_type, id as id');
        $query = $this->db->get('warranties');
        if ($query->num_rows() > 0) {
            $data[] =  array('id'=> 0, 'name'=>lang('No Warranty'));
            foreach ($query->result_array() as $row) {
                $data[] = [
                    'id' => $row['id'],
                    'name' => $row['warranty_duration'] . " " . lang($row['warranty_duration_type']),
                ];
            }
        }else{
            $data[] =  array('id'=> 0, 'name'=>lang('No Warranty Plans Created'));
        }
        return $data;
    }   
    

	/*------------------------------------------------------------------------
	| GET SETTING LIST
	| @return Variable with setting
	|--------------------------------------------------------------------------*/
    public function getSettings()
    {
        $data = array();
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

	/*------------------------------------------------------------------------
	| UPDATE SETTING
	| @param title, lang, disclaimer, admin username, admin password, sms services used, skebby username, skebby password, skebby name, skebby method, showcredit [1/0],
	| currency, invoice name, invoice mail, invoice address, invoice phone, invoice VAT, invoice type [EU/US], tax amount, category
	|--------------------------------------------------------------------------*/
    public function update_settings($data = NULL)
    {
        if ($data) {
            $this->db->update('settings', $data);
            return true;
        }
    }
	
	
	
  /*------------------------------------------------------------------------
	| SAVE THE LOGO IN THE DB
	-------------------------------------------------------------------------*/
    public function update_logo($logo)	
    {
        $data = array(
            'logo' => $logo,
        );
        $this->db->update('settings', $data);
    }


    public function getGroupPermissions($id)
    {
        $q = $this->db->get_where('permissions', array('group_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
    }


    public function getGroupPermissionsByGroupID($id)
    {
        $q = $this->db->get_where('permissions', array('group_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updatePermissions($id, $data = array())
    {
        $this->db->where(array('group_id' => $id));
        if ($this->db->update('permissions', $data)) {
            return true;
        }
        return false;
    }

    public function getGroupByID($id)
    {
        $q = $this->db->get_where('groups', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function import($items, $type)
    {
        if ($type == 'customers') {
            $this->db->insert_batch('clients', $items);
            return TRUE;
        }
        return FALSE;
    }
    
    public function getUsersByID($id) {
        $emails = array();
        $ids = explode(',', $id);
        $q = $this->db->where_in('id', $ids)->from('users')->get();
        if ($q->num_rows() > 0) {
            $rows = $q->result();
            foreach ($rows as $row) {
                if (filter_var($row->email, FILTER_VALIDATE_EMAIL)) {
                    $emails[] = $row->email;
                }
            }
        }
        if (!empty(array_filter($emails))) {
            return $emails;
        }else{
            return FALSE;
        }
    }

    public function getMandatory($form) {
        $this->db->where('form', $form);
        $q = $this->db->get('frm_priv');
        $data = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $field) {
                $data[$field->name] = $field->required;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCategories($id = NULL) {
        // $this->db->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
        $this->db
            ->select('categories.id as id, categories.name as name');
        if ($id) {
            $this->db->where('sub_id IS NOT NULL', NULL, FALSE);
            $this->db->where('sub_id', $id);
        }else{
            $this->db->where('sub_id IS NULL', NULL, FALSE);
        }
        $q = $this->db->get('categories');
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;

    }
    public function getCategoriesTree() {
        $categories = $this->getCategories();
        if ($categories) {
            $data = array();
            foreach ($categories as $category) {
                $sub = $this->getCategories($category->id);
                $children = NULL;
                if ($sub) {
                    $children = json_decode(json_encode($sub), True);
                }
                $data[] = array(
                    'name' => $category->name,
                    'id'=> $category->id,
                    'children'=> $children,
                );
            }
            return $data;
        }
        return FALSE;
    }

    
    public function getWarrantyByID($id) {
        $q = $this->db->get_where('warranties', array('id' => $id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function UPCCodeExists($code) {
        $q = $this->db->select('id, name as name, upc_code as code, "other" AS type')->get_where('other', array('upc_code' => $code));
        if ($q->num_rows() > 0) {
            return $q->row();
        }else{
            $q = $this->db->select('id, name as name, upc_code as code, "accessory" AS type')->get_where('accessory', array('upc_code' => $code));
            if ($q->num_rows() > 0) {
                return $q->row();
            }else{
                $q = $this->db->select('id, name as name, code as code, "repair_part" AS type')->get_where('inventory', array('code' => $code));
                if ($q->num_rows() > 0) {
                    return $q->row();
                } else {
                    return FALSE;
                }
            }
        }
        return FALSE;
    }

    public function getByTypeAndID($type, $id) {
        $q = $this->db->get_where($type, array('id' => $id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


    public function getAllUsers() {
        $users = array();
        $q = $this->db
                ->where('active', 1)
                ->where('hidden', 0)
                ->get('users');
            
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $k => $user)
            {   
                if ($user->all_stores || in_array((int)$this->session->userdata('active_store'), json_decode($user->stores))) {
                    $users[] = $user;
                }
            }
            return $users;
        }
        return [];
    }


    public function getAllTechnicians() {
        $users = array();
        $q = $this->db
                ->where('group_id !=', 1)
                ->where('active', 1)
                ->where('hidden', 0)
                ->get('users');
            
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $k => $user)
            {   
                if ($user->all_stores || in_array((int)$this->session->userdata('active_store'), json_decode($user->stores))) {
                    $users[] = $user;
                }
            }
            return $users;
        }
        return [];
    }


    public function getAllUsers_() {
        $users = array();
        $q = $this->db
                // ->where('store_id', (int)$this->session->userdata('active_store'))
                ->where('active', 1)
                ->where('hidden', 0)
                ->get('users');
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $k => $user) {   
                if ($user->all_stores || in_array((int)$this->session->userdata('active_store'), json_decode($user->stores))) {
                    $users[] = $user;
                }
            }
            return $users;
        }
        return [];
    }


    public function getAllSAPs() {
        $data = array();
        
        $this->db->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
        $query = $this->db->get('activation_plans');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $data[] = $row;
            }
        }
        return $data;
    }   

    public function getSAPItemsByID($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('activation_plans');
        if ($query->num_rows() > 0) {
            return $query->row()->items;
        }
        return FALSE;
    }   

    
     /*------------------------------------------------------------------------
    | getRepairStatuses
    |--------------------------------------------------------------------------*/
    public function getRepairStatuses()
    {
        $q = $this->db->order_by('position', 'ASC')->get('status');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getRepairStatusesCompleted()
    {
        $q = $this->db->order_by('position', 'ASC')->where('completed', 1)->get('status');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row->id;
            }
            return $data;
        }
        return FALSE;
    }

    public function getRepairStatusesPending()
    {
        $q = $this->db->order_by('position', 'ASC')->where('completed', 0)->get('status');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row->id;
            }
            return $data;
        }
        return FALSE;
    }

     /*------------------------------------------------------------------------
    | getRepairStatuses
    |--------------------------------------------------------------------------*/
    public function getRepairStatusesDefault()
    {
        $q = $this->db->where('show_in_default', 1)->order_by('position', 'ASC')->get('status');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function countRepairStatuses()
    {
        $count = $this->db->count_all_results('status');
        return $count+1;
    }

    public function verifyStatusDelete($id)
    {
        $this->db->where('status', $id);
        $q = $this->db->get('repair');
        if ($q->num_rows() > 0) {
            return FALSE;
        }
        return TRUE;
    }
    public function getStatusByID($id) {
        $this->db->where('id', $id);
        $q = $this->db->select('*')->get('status');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }   

      public function getSMSGatewaysDP()
    {
        $data = array();
        $q = $this->db->get('sms_gateways');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[$row->id] = $row->name;
            }
        }
        return $data;
    }



    public function getSMSGatewayByID($id)
    {
        $data = array();
        $q = $this->db->where('id', $id)->get('sms_gateways');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return False;
    }

    public function getActiveStatuses($completed)
    {
        $this->db->reset_query();
        
        $status = array();
        $q2 = $this->db
            ->where('completed', $completed)
            ->get('status');
        if ($q2->num_rows() > 0) {
            foreach ($q2->result() as $row) {
                $status[] = $row->id;
            }
        }
        return $status;
    }


    public function addLog($action = null, $model = null, $item_id = null, $details = null, $amount = null) {
        $data = array(
            'action' => $action,
            'model' => $model,
            'link_id' => $item_id,
            'user_id' => $this->session->userdata( 'user_id' ) ? $this->session->userdata( 'user_id' ) : 0,
            'date' => date('Y-m-d H:i:s'),
            'ip_addr' => $this->input->ip_address(),
            'details' => $details,
            'amount' => $amount,
        );
        $this->db->insert('activity_log', $data);
        return $this->db->insert_id();
    }

       public function get_total_qty_alerts() {
        $this->db->where('quantity < alert_quantity', NULL, FALSE)->where('isDeleted != ', 1)->where('alert_quantity >', 0);
        return $this->db->count_all_results('inventory');
    }

    public function getAllSuppliers() {
        // add store_id

        $q = $this->db->get("suppliers");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }


    public function getAllClients() {

        // add store_id
        $data = array();
        $this->db->order_by('id', 'desc');
        $q = $this->db->select('*, concat(first_name, " ", last_name) as name')->where('id >', 1)->get('clients');
        $no_customer = $this->db->select('*, concat(first_name, " ", last_name) as name')->where('id', 1)->get('clients');

        if ($no_customer->num_rows() > 0) {
            foreach ($no_customer->result() as $client) {
                $data[] = $client;
            }
        }

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $client) {
                $data[] = $client;
            }
        }
        return $data;
    }

    public function getAllBankAccounts() {

        // add store_id
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));
        
        $data = array();
        $this->db->order_by('id', 'desc');
        $q = $this->db->select('*')->get('accounts');

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $account) {
                $data[] = $account;
            }
        }
        return $data;
    }
    public function getAllBankAccountsDP() {

        // add store_id
        $this->db->where('store_id', (int)$this->session->userdata('active_store'));

        $data = array();
        $this->db->order_by('id', 'desc');
        $q = $this->db->select('*')->get('accounts');

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $account) {
                $data[$account->id] = $account->title;
            }
        }
        return $data;
    }


    public function getCustomerByID($id)
    {
        $data = array();
        $query = $this->db->get_where('clients', array('id' => $id));
        if ($query->num_rows() > 0) {
            $data = $query->row();
        }

        return $data;
    }

     public function getAllActions()
    {
        $actions = array(
            'add',
            'change-status',
            'delete',
            'edit',
            'email-payout',
            'email-receipt',
            'return-sale',
            'update',
        );
        sort($actions, SORT_NUMERIC);
        return $actions;
    }

    public function getAllLogModels()
    {
        $models =  array(
            'category',
            'client',
            'custom-field',
            'model',
            'payment',
            'pos-sale',
            'printer',
            'product',
            'repair',
            'status',
            'tax-rate',
            'user',
        );
        sort($models, SORT_NUMERIC);
        return $models;
    }
     public function getDateFormats()
    {
        $q = $this->db->get('date_format');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getDateFormat($id) {
        $q = $this->db->get_where('date_format', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getUserByID($id) {
        $q = $this->db->get_where('users', array('id' => $id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
     public function getUser($id = null)
    {
        if (!$id) {
            $id = $this->session->userdata('user_id');
        }
        $q = $this->db->get_where('users', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getUserGroup($user_id = false)
    {
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $group_id = $this->getUserGroupID($user_id);
        $q        = $this->db->get_where('groups', ['id' => $group_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getUserGroupID($user_id = false)
    {
        $user = $this->getUser($user_id);
        return $user->group_id;
    }

     public function checkPermissions()
    {
        $q = $this->db->get_where('permissions', ['group_id' => $this->session->userdata('group_id')], 1);
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return false;
    }

     public function getNotifications()
    {
        $date = date('Y-m-d H:i:s', time());
        $this->db->where('from_date <=', $date);
        $this->db->where('till_date >=', $date);
        $q = $this->db->get('notifications');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }


     public function getAllDefects()
    {
        $q = $this->db->get('defects');
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return [];
    }


     public function getModelByID($id) {
        $this->db->where('id', $id);
        $q = $this->db->select('*')->get('manufacturers');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }   
     public function getDefectByID($id) {
        $this->db->where('id', $id);
        $q = $this->db->select('*')->get('defects');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }   

    public function getAllRepairDefects() {
        $defects = $this->db
            ->group_by('defect')
            ->select('defect, GROUP_CONCAT(id) as ids')
            ->get('repair');
        if ($defects->num_rows() > 0) {
            return $defects->result();
        }
        return [];
    }   

    public function checkIfDefectExists($name) {
        $this->db->where('name', $name);
        $q = $this->db->select('*')->get('defects');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }   



    // public function getAllRepairModels() {
    //     $defects = $this->db
    //         ->group_by('model_name, manufacturer')
    //         ->select('model_name, GROUP_CONCAT(id) as ids')
    //         ->get('repair');
    //     if ($defects->num_rows() > 0) {
    //         return $defects->result();
    //     }
    //     return [];
    // }   

    // public function checkIfDefectExists($name) {
    //     $this->db->where('name', $name);
    //     $q = $this->db->select('*')->get('defects');
    //     if ($q->num_rows() > 0) {
    //         return $q->row();
    //     }
    //     return false;
    // }   
   
}
