<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Commission extends Auth_Controller {

	public function __construct()
	{
		parent::__construct();
        $this->load->model('commission_model');
	}

	// redirect if needed, otherwise display the user list
	public function index($type = NULL)
	{
        $this->repairer->checkPermissions();

		if ($type === 'disabled' || $type === 'enabled') {
        	$this->data['toggle_type'] = $type;
		}else{
        	$this->data['toggle_type'] = NULL;
		}
		$this->render('commission/index');
	}


	public function getAllCommission($type = NULL)
    {
        $this->repairer->checkPermissions('index');

    	$this->load->library('datatables');
        $this->datatables
            ->select('id, label, disable') 
            ->from('commission');
        if ($type === 'disabled') {
        	$this->datatables->where('disable', 1);
		} elseif($type === 'enabled') {
        	$this->datatables->where('disable', 0);
		}
		$this->datatables->where('(universal=1 OR store_id='.$this->activeStore.')',NULL, FALSE);
        $this->datatables->add_column('actions', "$1__$2", 'id, disable');
        $this->datatables->unset_column('id');
        $this->datatables->unset_column('disable');
        echo $this->datatables->generate();
    }

    function toggle() {
        $this->repairer->checkPermissions('disable');

        $toggle = $this->input->post('toggle');
        if ($toggle == 'enable') {
            $data = array('disable' => 0);
            $a = lang('enabled');
        } else {
            $data = array('disable' => 1);
            $a = lang('disabled');
        }
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('commission', $data);
        echo json_encode(array('ret' => 'true', 'toggle' => $a));
    }

    public function add()
    {
        $this->repairer->checkPermissions();

    	$this->form_validation->set_rules('label', lang('Label'), 'required');
        $this->form_validation->set_rules('value', lang('Value'), 'required');
        $this->form_validation->set_rules('type', lang('Type'), 'required');

		if ($this->form_validation->run() == FALSE) {
    		$this->render('commission/add');
    	}else{
    		$data = array(
    			'label' => $this->input->post('label'),
    			'universal' => $this->input->post('universal') ? $this->input->post('universal') : 0,
			    'value' => $this->input->post('value'),
                'type' => $this->input->post('type'),
    			'store_id' => $this->activeStore,
    			'created_at' => date('Y-m-d H:i:s'),
    			'disable' => 0,
    		);
    		$this->db->insert('commission', $data);
    		$this->session->set_flashdata('message', lang('Commission Plan Added'));
    		redirect('panel/commission');
    	}
    }
    public function edit($id)
    {
        $this->repairer->checkPermissions();

        $this->form_validation->set_rules('label',lang('Label'), 'required');
        $this->form_validation->set_rules('value',lang('Value'), 'required');
        $this->form_validation->set_rules('type',lang('Type'), 'required');

        if ($this->form_validation->run() == FALSE) {

            $this->db->where('(universal=1 OR store_id='.$this->activeStore.')',NULL, FALSE);
            $q = $this->db->get_where('commission', array('id'=>$id));
            if ($q->num_rows() > 0) {
                $this->data['plan'] = $q->row();
                $this->render('commission/edit');
            }else{
                $this->session->set_flashdata('message', lang('Plan Not Found'));
                redirect('panel/commission');
            }
        }else{
            $data = array(
                'label' => $this->input->post('label'),
                'universal' => $this->input->post('universal') ? $this->input->post('universal') : 0,
                'value' => $this->input->post('value'),
                'type' => $this->input->post('type'),
                'store_id' => $this->activeStore,
                'created_at' => date('Y-m-d H:i:s'),
                'disable' => 0,
            );
            $this->db->where('id', $id);
            $this->db->update('commission', $data);
            $this->session->set_flashdata('message', lang('Commission Plan Edited'));
            redirect('panel/commission');
        }
    }

    public function assign()
    {
        $this->repairer->checkPermissions();

        $this->form_validation->set_rules('type', lang('Type'), 'required');
        $this->form_validation->set_rules('category', lang('Category'), 'required');
        if ($this->input->post('type') == 'product') {
            $this->form_validation->set_rules('product', lang('Product'), 'required');
        }
        $this->form_validation->set_rules('groups[]', lang('Groups'), 'required');
        $this->form_validation->set_rules('plan', lang('Plan'), 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->data['plans'] = $this->commission_model->getCommisionPlans();
            $this->render('commission/assign');
        }else{
            $type = $this->input->post('type');
            $category = $this->input->post('category');
            $product = $this->input->post('product');
            $groups = $this->input->post('groups');
            $verify = $this->commission_model->verifyAssigning($type, $category, $product, $groups); 
            unset($category,$product,$groups);
            $verify = json_decode($verify);
            if ($verify->success) {
                $table = 'category_commission';
                $plan=$this->input->post('plan');
                $plan = $this->db->get_where('commission', array('id'=> $plan))->row();

                $data = array(
                    'category' => $this->input->post('category'),
                    'groups' => implode(',', $this->input->post('groups')),
                    'amount' => $plan->value,
                    'type' => $plan->type,
                    'plan_id' => $plan->id,
                );
                if ($type == 'product') {
                    $data['product_id'] = $this->input->post('product');
                    $table = 'product_commission';
                }
                $this->db->insert($table, $data);
                $this->session->set_flashdata('message', lang('Successfully assigned the plan'));
                $type = $type == 'group' ? 'category' : $type;
                redirect('panel/commission/'.$type);
            }else{
                $this->session->set_flashdata('error', ($verify->msg ? implode('<br>', $verify->msg) : lang('Something has gone wrong. Please try again.')));
                redirect('panel/commission/assign');
            }
        }
        
    }




    
    public function getProductsAjax()
    {
        $term = $this->input->get('q');
        $type = $this->input->get('type');
        $data = array();
        if ($type == 'repair_parts') {
            $data = $this->commission_model->getRepairPartsNames($term);
        }elseif ($type == 'new_phones') {
            $data = $this->commission_model->getNewPhones($term);
        }elseif ($type == 'used_phones') {
            $data = $this->commission_model->getUsedPhones($term);
        }elseif ($type == 'accessories') {
            $data = $this->commission_model->getAccessoryNames($term);
        }elseif ($type == 'other') {
            $data = $this->commission_model->getOthers($term);
        }elseif ($type == 'plans') {
            $data = $this->commission_model->getAllPlans($term);
        }
        echo json_encode($data);
    }


    public function checkData($id = NULL)
    {
        $type = $this->input->post('type');
        $category = $this->input->post('category');
        $product = $this->input->post('product');
        $groups = $this->input->post('groups');
        echo $this->commission_model->verifyAssigning($type, $category, $product, $groups, $id); 
    }
    public function product($type = NULL)
    {
        $this->repairer->checkPermissions();

        $this->render('commission/product');
    }
    public function delete_product()
    {
        $this->repairer->checkPermissions();

        $this->db->where('id', $this->input->post('id'));
        $this->db->delete('product_commission');
        echo('true');
    }
     public function edit_product($id)
    {
        $this->repairer->checkPermissions();

        if (!empty($_POST)) {
            $category = $this->input->post('category');
            $product = $this->input->post('product');
            $groups = $this->input->post('groups');
            $verify = $this->commission_model->verifyAssigning('product', $category, $product, $groups, $id); 
            unset($category,$product,$groups);
            $verify = json_decode($verify);
            if ($verify->success) {
                $table = 'product_commission';
                $plan=$this->input->post('plan');
                $plan = $this->db->get_where('commission', array('id'=> $plan))->row();

                $data = array(
                    'category' => $this->input->post('category'),
                    'groups' => implode(',', $this->input->post('groups')),
                    'amount' => $plan->value,
                    'type' => $plan->type,
                    'plan_id' => $plan->id,

                );
                $data['product_id'] = $this->input->post('product');
                $this->db->where('id', $id);
                $this->db->update($table, $data);
                $this->session->set_flashdata('message', lang('Successfully updated the plan'));
                redirect('panel/commission/product');
            }else{
                $this->session->set_flashdata('error', ($verify->msg ? implode('<br>', $verify->msg) : lang('Something has gone wrong. Please try again.')));
                redirect('panel/commission/edit_product/'.$id);
            }
        }
        $this->db->where('id', $id);
        $q = $this->db->from('product_commission')->get();
        if ($q->num_rows() > 0) {
            $row = $q->row();
        }else{
            redirect('panel/commission/category');
        }
        $this->data['plans'] = $this->commission_model->getCommisionPlans();
        $this->data['products'] = $this->commission_model->getProductByIDAndType(NULL, $row->category);
        $this->data['row'] = $row;

        $this->render('commission/edit_product');
        
    }
    public function getAllProductCommission($type = NULL)
    {
        $this->repairer->checkPermissions('product');

        $this->load->library('datatables');
        $this->datatables
            ->select('id as id, (CASE WHEN category = "repair_parts" THEN (SELECT name FROM inventory WHERE inventory.id=product_commission.product_id) WHEN category = "new_phones" THEN (SELECT phone_name FROM phones WHERE phones.id=product_commission.product_id) WHEN category = "used_phones" THEN (SELECT phone_name FROM phones WHERE phones.id=product_commission.product_id) WHEN category = "accessories" THEN (SELECT name FROM accessory WHERE accessory.id=product_commission.product_id) WHEN category = "other" THEN (SELECT name FROM other WHERE other.id=product_commission.product_id) WHEN category = "plans" THEN (SELECT carriers.name as name FROM plans  LEFT JOIN carriers ON carriers.id = plans.carrier_id WHERE plans.id=product_commission.product_id)ELSE "Nothing" END) AS product, category, CONCAT(amount, "____",type) as plan, groups') 
            ->from('product_commission');
        $actions = "";
        if($this->Admin || $this->GP['commission-edit_product']){
            $actions .= '<a class="btn btn-primary" href="'.base_url().'panel/commission/edit_product/$1">'.lang('Edit').'</a>';
        }
        if($this->Admin || $this->GP['commission-delete_product']){
            $actions .= '<a class="btn btn-danger" id="delete" data-num="$1">'.lang('Delete').'</a>';
        }
        $this->datatables->add_column('actions', $actions, 'id');
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    public function category($type = NULL)
    {
        $this->repairer->checkPermissions();

        $this->render('commission/category');
    }
    public function delete_category()
    {
        $this->repairer->checkPermissions();

        $this->db->where('id', $this->input->post('id'));
        $this->db->delete('category_commission');
        echo('true');
    }


    public function edit_category($id)
    {
        $this->repairer->checkPermissions();

        if (!empty($_POST)) {
            $category = $this->input->post('category');
            $product = $this->input->post('product');
            $groups = $this->input->post('groups');
            $verify = $this->commission_model->verifyAssigning('group', $category, $product, $groups, $id); 
            unset($category,$product,$groups);
            $verify = json_decode($verify);
            if ($verify->success) {
                $table = 'category_commission';
                $plan=$this->input->post('plan');
                $plan = $this->db->get_where('commission', array('id'=> $plan))->row();

                $data = array(
                    'category' => $this->input->post('category'),
                    'groups' => implode(',', $this->input->post('groups')),
                    'amount' => $plan->value,
                    'type' => $plan->type,
                    'plan_id' => $plan->id,
                );
                $this->db->where('id', $id);
                $this->db->update($table, $data);
                $this->session->set_flashdata('message', lang('Successfully updated the plan'));
                redirect('panel/commission/category');
            }else{
                $this->session->set_flashdata('error', ($verify->msg ? implode('<br>', $verify->msg) : lang('Something has gone wrong. Please try again.')));
                redirect('panel/commission/edit_category/'.$id);
            }
        }
        $this->db->where('id', $id);
        $q = $this->db->from('category_commission')->get();
        if ($q->num_rows() > 0) {
            $row = $q->row();
        }else{
            redirect('panel/commission/category');
        }
        $this->data['plans'] = $this->commission_model->getCommisionPlans();
        $this->data['row'] = $row;
        $this->render('commission/edit_category');
        
    }
    public function getAllCategoryCommission($type = NULL)
    {
        $this->repairer->checkPermissions('category');

        $this->load->library('datatables');
        $this->datatables
            ->select('id, category, CONCAT(amount, "____",type) as plan, groups') 
            ->from('category_commission');

        $actions = "";
        if($this->Admin || $this->GP['commission-edit_category']){
            $actions .= '<a class="btn btn-primary" href="'.base_url().'panel/commission/edit_category/$1">'.lang('Edit').'</a>';
        }
        if($this->Admin || $this->GP['commission-delete_category']){
            $actions .= '<a class="btn btn-danger" id="delete" data-num="$1">'.lang('Delete').'</a>';
        }
        $this->datatables->add_column('actions', $actions, 'id');
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }


    public function report_index()
    {
        $this->mPageTitle = lang('Commission Report');
        $this->render('commission/report/index');
    }

    public function getSortMenu()
    {
        $this->data['perms'] = NULL;
        $this->data['groups'] = NULL;
        $final = array();
        $pin_code = $this->input->post('pin_code');
        $q = $this->db->get_where('users', array('pin_code' => $pin_code));
        if ($q->num_rows() > 0) {
            $user = $q->row();
            $this->data['pin_code'] = $pin_code;
            $this->data['admin'] = FALSE;
            $GP = $this->settings_model->getGroupPermissions($user->id);
            if (!$GP) {
                $this->data['admin'] = TRUE;
            }else{
                $perms = $GP[0];
                $groups = array_filter(explode(',', $perms['commission-groups']));
                if (empty($groups)) {
                    $final['success'] = true;
                    $final['user_id'] = $user->id;
                    $final['html'] = NULL;
                    echo json_encode($final);
                    die();
                }
                $this->data['perms'] = $perms;
                $this->data['groups'] = implode(',', $groups);
            }
            $html = $this->load->view($this->theme.'commission/report/sort_menu', $this->data, TRUE);
            $final['success'] = true;
            $final['html'] = $html;
        }else{
            $final['success'] = false;
        }
        echo json_encode($final);
    }

 
    public function json_sort()
    {
        $this->load->library('repairer');
        $term = $this->input->post('id', true);
        
        $rows = array();
        $q = NULL;
        if ($term == 'user') {
            if ($this->input->post('group')) {
                $groups = explode(',', $this->input->post('group'));
                foreach ($groups as $group) {
                    foreach ($this->ion_auth->users($group)->result() as $user) {
                        if ($user->hidden) continue;
                        $data = array(
                            'id' => $user->id,
                            'name' => $user->first_name . " " . $user->last_name,
                        );
                        $rows[] = $data;
                    }
                }
            }else{
                foreach ($this->ion_auth->users()->result() as $user) {
                    if ($user->hidden) continue;
                    $data = array(
                        'id' => $user->id,
                        'name' => $user->first_name . " " . $user->last_name,
                    );
                    $rows[] = $data;
                }
            }
        }elseif($term == 'group'){
            if ($this->input->post('group')) {
                $groups = $this->input->post('group');
                $this->db->where_in('id', explode(',', $groups));
            }
            $q = $this->db->get('groups');
            if ($q->num_rows() > 0) {
                $rows = $q->result_array();
            }
        }
            
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $pr[] = array('id' => $row['id'], 'text' => $row['name']);
            }
            $this->repairer->send_json($pr);
        }else {
            $this->repairer->send_json((array('id' => 0, 'text' => lang('no_match_found'))));
        }
    }
    
    public function report()
    {
        if (!$this->input->post('pin_code')) {
            $this->session->set_flashdata('warning', lang('Enter your Pin Code'));
            redirect('panel/commission/report_index');
        }
       
        $this->data['pin_code']     = $this->input->post('pin_code');
        $this->data['sort_by']      = $this->input->post('sort_by');
        $this->data['sort_with']    = $this->input->post('sort_with');
        $this->data['from_date']    = urlencode(date('Y-m-d').' 00:00:00');
        $this->data['to_date']    = urlencode(date('Y-m-d').' 23:59:59');
        if ($this->input->post('date_range')) {
            $date_range = json_decode($this->input->post('date_range'));
            $this->data['from_date']    = urlencode($date_range->start.' 00:00:00');
            $this->data['to_date']      = urlencode($date_range->end.' 23:59:59');
        }
        $q = $this->db->get_where('users', array('pin_code' => $this->input->post('pin_code')));
        if ($q->num_rows() > 0) {
            $user = $q->row();
            $check_id = $user->id;
            $GP = $this->settings_model->getGroupPermissions($user->id);
            if ($GP) {
                $perms = $GP[0];
            }
        }
        if ($this->input->post('sort_by') == 'group') {
            if (isset($perms)) {
                if ($perms['commission-ownstore']) {
                    $this->db->where('sales.store_id', $this->activeStore);
                }else{
                    $this->db->where_in('sales.store_id', explode(',', $perms['commission-stores']));
                }
            }
            $this->db->where("date BETWEEN '".urldecode($this->data['from_date'])."' AND '".urldecode($this->data['to_date'])."'");
            $this->data['users'] = $this->db
                                        ->select('biller_id, biller')
                                        ->group_by('biller_id')
                                        ->where('groups.id', $this->input->post('sort_with'))
                                        ->join('groups', 'groups.id=sales.biller_id', 'left')
                                        ->get('sales')
                                        ->result();

        }elseif ($this->input->post('sort_by') == 'all') {
             if (isset($perms)) {
                if ($perms['commission-ownstore']) {
                    $this->db->where('sales.store_id', $this->activeStore);
                }else{
                    $this->db->where_in('sales.store_id', explode(',', $perms['commission-stores']));
                }
            }
            $this->db->where("date BETWEEN '".urldecode($this->data['from_date'])."' AND '".urldecode($this->data['to_date'])."'");
            $this->data['users'] = $this->db
                                    ->select('biller_id, biller')
                                    ->group_by('biller_id')
                                    ->get('sales')
                                    ->result();
        }else{
            $this->data['users'] = NULL;
        }

        $this->render('commission/report/view');
    }
     public function getAllRecords($id = NULL)
    {
        $pin_code   = $this->input->post('pin_code');
        $this->load->library('datatables');

        $perms = NULL;
        $q = $this->db->get_where('users', array('pin_code' => $pin_code));
        if ($q->num_rows() > 0) {
            $user = $q->row();
            $check_id = $user->id;
            $GP = $this->settings_model->getGroupPermissions($user->id);
            $perms = $GP[0];
           
            if ($perms) {
                if ($perms['commission-ownstore']) {
                    $this->datatables->where('sale_items.store_id', $this->activeStore);
                }else{
                    $this->db->where_in('sale_items.store_id', explode(',', $perms['commission-stores']));
                }
            }
        }

        $sort_by    = $this->input->post('sort_by');
        $sort_with  = $this->input->post('sort_with');
        $from_date  = $this->input->post('from_date');
        $to_date    = $this->input->post('to_date');

        if ($from_date && $to_date) {
            $from_date = urldecode($from_date);
            $to_date = urldecode($to_date);
            $this->datatables->where("sales.date BETWEEN '$from_date' AND '$to_date'");
        }

        if ($sort_by == 'user') {
            $this->datatables->where('sales.biller_id', $sort_with);
        }elseif($sort_by == 'group'){
            $this->datatables->where('users.group_id', $sort_with);
        }
        if ($id) {
            $this->datatables->where('sales.biller_id', $id);
        }
        $this->datatables->where('sale_items.item_type !=', 'crepairs');
        $this->datatables->where('sale_items.item_type !=', 'cp');
        $this->datatables->select('LPAD(sale_items.sale_id, 4, "0") as sale_id, sales.date as date, if(sale_items.item_type="drepairs", repair_items.product_name, sale_items.product_name) as product_name,store.name as store_name, (SELECT `groups`.`description` FROM `users` LEFT JOIN `groups` ON `users`.`group_id` = `groups`.`id` WHERE sales.biller_id=users.id LIMIT 1) as user_group,sales.biller as biller, if(sale_items.item_type="drepairs", repair_items.unit_cost, sale_items.unit_cost) as unit_cost, if(sale_items.item_type="drepairs", repair_items.unit_price, sale_items.unit_price) as unit_price,if(sale_items.item_type="drepairs", (repair_items.subtotal-repair_items.tax), (sale_items.subtotal - sale_items.tax)) as subtotal,if(sales.sale_id IS NOT NULL, (0- if(sale_items.item_type="crepairs", if(add_to_stock, ABS(repair_items.unit_price)-repair_items.unit_cost, ABS(repair_items.unit_price) ) , if(add_to_stock, ABS(sale_items.unit_price)-sale_items.unit_cost, ABS(sale_items.unit_price) ) ) ) + sales.surcharge ,if(sale_items.item_type="drepairs", repair_items.unit_price, sale_items.unit_price-sale_items.unit_cost) + (SELECT SUM(activation_spiff) FROM sale_items WHERE sale_items.sale_id=sales.id)) as profit,  commission.label as commission_label, if(sale_items.item_type="drepairs", repair_items.commission, sale_items.commission) as commission')
        ->join('sales', 'sales.id=sale_items.sale_id')
        ->join('repair_items', 'repair_items.repair_id=sale_items.product_id', 'left')
        ->join('commission', 'commission.id=sale_items.plan_id', 'left')
        ->join('store', 'store.id=sales.store_id', 'left')
        ->join('users', 'users.id=sales.biller_id')
        ->from('sale_items');
        echo $this->datatables->generate();
    }


  
}