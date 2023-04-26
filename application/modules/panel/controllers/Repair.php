<?php if (!defined('BASEPATH')) { exit('No direct script access allowed');}
/**
 * Customers
 *
 *
 * @package     Reparer
 * @category    Controller
 * @author      Usman Sher
*/

class Repair extends Auth_Controller
{
    // THE CONSTRUCTOR //
    public function __construct()
    {
        parent::__construct();
        $this->load->model('repair_model');
    }

    public function index($type = NULL)
    {
        $this->repairer->checkPermissions();
        $this->mPageTitle = lang('Repairs');
        if ($type === 'disabled' || $type === 'enabled'
            || is_numeric($type) || $type === 'cancelled' || $type === 'default' || $type === 'pending' || $type === 'completed') {
            $this->data['toggle_type'] = $type;
        }else{
            $this->data['toggle_type'] = NULL;
        }
        $this->data['manufacturers'] = $this->settings_model->getManufacturers();
        $this->data['clients'] = $this->repair_model->getAllClients();
        $this->data['users'] = $this->db->where('active', 1)->where('hidden', 0)->get('users')->result();
        $this->data['frm_priv'] = $this->settings_model->getMandatory('repair');

        $this->render('repair/index');
    }

    // GENERATE THE AJAX TABLE CONTENT //
    public function getAllRepairs($type = NULL)
    {


        $default_statuses = $this->settings_model->getRepairStatusesDefault();
        $completed_status = $this->settings_model->getActiveStatuses(1);
        $active_status = $this->settings_model->getActiveStatuses(0);

        $this->load->library('datatables');
        
        if ($this->repairer->in_group('Repair')) {
            $this->datatables->where('assigned_to', $this->mUser->id);
        }
        $this->datatables
            ->select('disable, repair.id as id, repair.serial_number as serial_number, repair.name as name, repair.telephone as telephone, repair.defect as defect, repair.model_name as model_name, repair.date_opening as date_opening, if(status > 0, CONCAT(status.label, "____", status.bg_color, "____", status.fg_color, "____", status.id, "____" ,repair.id), "cancelled") as status, CONCAT(users.first_name, " ", users.last_name) as assigned_to, repair.code as rid, grand_total, CONCAT(repair.warranty,"____",IFNULL(date_closing, 0)) as warranty, CONCAT(deposit_collected, "___", advance) as c1, pos_sold as c2, (SELECT sale_items.sale_id from sale_items WHERE sale_items.product_id=repair.id and item_type="drepairs") as sale_id')
            ->join('status', 'status.id=repair.status', 'left')
            ->join('users', 'users.id=repair.assigned_to', 'left')
            ->from('repair');

        if ($type === 'disabled') {
            $this->datatables->where('disable', 1);
        }elseif($type === 'enabled') {
            $this->datatables->where('disable', 0);
        }elseif(is_numeric($type)) {
            $this->datatables->where('status', $type);
        }elseif($type === 'default' && $default_statuses) {
            $this->datatables->where_in('status', array_column($default_statuses, 'id'));
        }elseif($type === 'default' && !$default_statuses) {
            $this->datatables->where('status', false);
        }elseif($type === 'completed') {
            $this->datatables->where_in('status', $completed_status);
        }elseif($type === 'pending') {
            $this->datatables->where_in('status', $active_status);
        }

        $this->datatables->where('repair.store_id', $this->activeStore);
        $this->datatables->add_column('actions', "$1___$2___$3___$4", 'id, disable, c2, sale_id');
        $this->datatables->add_column('id_', "$1", 'id');
        $this->datatables->add_column('deposit', "$1", 'c1');
        $this->datatables->add_column('pos_sold', "$1", 'c2');
        // $this->datatables->unset_column('id');
        $this->datatables->unset_column('sale_id');
        $this->datatables->unset_column('c1');
        $this->datatables->unset_column('c2');
        $this->datatables->unset_column('disable');
        echo $this->datatables->generate();
    }

    
    public function add(){
        $this->repairer->checkPermissions();
        $this->form_validation->set_rules('client_name', lang("client_name"), 'required');
        if ($this->form_validation->run() == true) {
            $user_id = $this->ion_auth->get_user_id();
            $custom_fields = explode(',', $this->mSettings->custom_fields);
            $custom_checmarks = explode(',', $this->mSettings->repair_custom_checkbox);
            $custom_toggles = explode(',', $this->mSettings->repair_custom_toggles);
            $cust = array();
            $custcheck = array();
            $custtoggles = array();
            foreach ($_POST as $key => $var) {
                if (substr($key, 0, 7) === 'custom_' ) {
                    $cust[(substr($key, 7))] = $var;
                }
                if (substr($key, 0, 12) === 'checkcustom_' ) {
                    $custcheck[(substr($key, 12))] = $var;
                }
                if (substr($key, 0, 12) === 'checktoggle_' ) {
                    $custtoggles[(substr($key, 12))] = $var;
                }
            }
            $cust = (json_encode($cust));
            $custcheck = (json_encode($custcheck));
            $custtoggles = (json_encode($custtoggles));


            $client_details = $this->repair_model->getClientNameByID($this->input->post('client_name'));
            if ($this->input->post('category_select') === 'other') {
                $category = $this->input->post('category_input');
            }else{
                $category = $this->input->post('category_select');
            }
            $warranty_plan = $this->input->post('warranty_id');
            $warranty_plan = json_encode($this->settings_model->getWarrantyByID($warranty_plan));

            $data = array(
                'client_id' => $this->input->post('client_name'),
                'name' => $client_details->name,
                'telephone' => $client_details->telephone,
                'email' => $this->input->post('email'),
                
                'defect' => $this->input->post('defect'),
                'defect_id' => $this->input->post('defect_id'),
                'category' => $category,
                'manufacturer_id' => $this->input->post('manufacturer'),
                'model_id' => $this->input->post('model_id'),
                'model_name' => $this->input->post('model'),
                'advance' => $this->input->post('advance'),
                'date_opening' => date('Y-m-d H:i:s'),
                'service_charges' => $this->input->post('service_charges'),
                'comment' => $this->input->post('comment'),
                'status' => $this->input->post('status'),
                'code' => $this->input->post('code') ,
                'sms' => 0,
                'custom_field' => $cust,
                'custom_checkboxes' => $custcheck,
                'created_by' => $user_id,
                'assigned_to' => $this->input->post('assigned_to'),
                'serial_number' => $this->input->post('serial_number'),
                // pre repair items
                'custom_toggles' => $custtoggles,
                'pin_code' => $this->input->post('cust_pin_code'),
                'pattern' => $this->input->post('patternlock'),
                'tax_id' => $this->input->post('tax_id'),

                'store_id' => $this->activeStore,
                'warranty' => $warranty_plan,
            );
            
            if($this->repair_model->repair_code_exists($data['code'])){
                $data['code'] = $this->repairer->getReference('repair');
            }

            $data['reference_no'] = $this->repairer->getReference('repair');
            // if((int)$data['status'] == 2) {
            //     $data['date_closing'] = date('Y-m-d H:i:s');
            // }


            if ($this->mSettings->use_defects_input_dropdown && $defect_ = $this->settings_model->getDefectByID($this->input->post('defect_id'))) {
                $data['defect'] = $defect_->name;
            }

            if ($this->mSettings->use_models_input_dropdown && $model_ = $this->settings_model->getModelByID($this->input->post('model_id'))) {
                $data['model_name'] = $model_->name;
            }


            if (isset($_POST['sms']) && $_POST['sms'] !== null) {
                $data['sms'] = 1;
            }
            // if ($_POST['code'] == null) {
            //     $data['code'] = time();
            // }
            $products = array();
            $subtotal = 0;
            $total_tax = 0;
            $total_discount = 0;
            $total = 0;
            $gtotal = 0;
            $date = date('Y-m-d');
            if (isset($_POST['item_id']) && $_POST['item_id'] !== null) {
                $i = sizeof($_POST['item_id']);
                for ($r = 0; $r < $i; $r++) {
                    $item_id = $_POST['item_id'][$r];
                    $item_name = $_POST['item_name'][$r];
                    $item_code = $_POST['item_code'][$r];
                    $item_price = $_POST['item_price'][$r];
                    $item_cost = $_POST['item_cost'][$r];
                    $item_tax = $_POST['item_tax'][$r];
                    $item_tax_id = $_POST['item_tax_id'][$r];
                    $item_discount = $_POST['item_discount'][$r];
                    $item_details = $_POST['item_details'][$r];
                    $item_serial = $_POST['item_serial'][$r];
                    $item_type = $_POST['item_type'][$r];
                    $refund_item = $_POST['refund_item'][$r];
                    $add_to_stock = $_POST['add_to_stock'][$r];
                    $items_restock = $_POST['items_restock'][$r];
                    $phone_classification = $_POST['phone_classification'][$r];
                    $used_phone_vals = $_POST['used_phone_vals'][$r];
                    $variant = $_POST['product_option'][$r] !== 'null' ? $_POST['product_option'][$r] : NULL;
                    $phone_number   = $_POST['phone_number'][$r];
                    $set_reminder   = $_POST['set_reminder'][$r];
                    $warranty_id    = $_POST['product_warranty'][$r];
                    $warranty = $this->settings_model->getWarrantyByID($warranty_id);
                    $warranties = $warranty;
                    $warranties_ids[] = $warranty ? $warranty->id : NULL;
                    $warranty = json_encode($warranty);
                    $disount_code = $_POST['disount_code'][$r];
                    $activation_spiff = $_POST['activation_spiff'][$r];
                    $products[] = array(
                        'store_id'      => $this->activeStore,
                        'product_id'    => $item_id,
                        'unit_cost'     => $item_cost,
                        'product_name'  => $item_name,
                        'product_code'  => $item_code,
                        'quantity'      => 1,
                        'unit_price'    => $item_price,
                        'taxable'       => ($item_tax > 0) ? 1 : 0,
                        'tax'           => $item_tax,
                        'tax_rate'      => urldecode($item_tax_id),
                        'subtotal'      => ($item_price + $item_tax) - $item_discount,
                        'option_id'     => $variant,
                        'discount'      => $item_discount,
                        'item_type'     => $item_type,
                        'serial_number' => ($item_serial == 'null' || $item_serial == 'undefined') ? NULL : $item_serial,
                        'date'          => $date,
                        'refund_item'   => $refund_item,
                        'add_to_stock'  => $add_to_stock,
                        'sale_item_id'  => NULL,
                        'real_store_id' => $this->activeStore,
                        'store_id'      => $this->activeStore,
                        'phone_number'  => $phone_number,
                        'set_reminder'  => $set_reminder,
                        'warranty'      => $warranty,
                        'activation_spiff' => $activation_spiff,
                        'item_details' => $item_details,
                    );

                    $subtotal += ($item_price + $item_tax);
                    $total_tax += $item_tax;
                    $total_discount += $item_discount;
                }
            }

            $service_charges = (float)$this->input->post('service_charges');
            $tax_rate = $this->settings_model->getTaxRateByID($this->input->post('tax_id'));
            $sc_tax = 0;
            if ($tax_rate) {
                if ($tax_rate->type == 2) {
                    $sc_tax = ($tax_rate->rate);
                }
                if ($tax_rate->type == 1) {
                    $sc_tax = ((($service_charges) * $tax_rate->rate) / 100);
                }
            }
            $total_tax += $sc_tax;

            $gtotal = ($subtotal - $total_discount);
            $gtotal = $service_charges + $sc_tax + $gtotal;
            $data['tax'] = $total_tax;
            $data['total'] = $total;
            $data['grand_total'] = $gtotal;
            

            $attachment_data = $this->input->post('attachment_data') ? $this->input->post('attachment_data') : NULL;
            $result = $insert_id = $this->repair_model->add_repair($data, $products, $attachment_data);

            $repair_id = $result['id'];
            if ($this->input->post('sign_id')) {
                $data = $this->input->post('sign_id');
                $name = $repair_id.'__'.time().'.png';
                $this->repairer->base30_to_jpeg($data, FCPATH.'assets/uploads/signs/repair_'.$name);
                $this->db->where('id', $repair_id);

                $sign_name = $this->input->post('sign_name');
                $this->db->update('repair', array('repair_sign' => $name, 'repair_sign_name' => $sign_name));
            }

            if ($result) {
              $emails = $this->settings_model->getUsersByID($this->mSettings->notify_repair);
              if ($emails) {
                $user = $this->ion_auth->user()->row();
                $message = file_get_contents(FCPATH.'themes/'.$this->theme.'/email_templates/notify_repair.html');

                $search  = array('{code}', '{user}', '{grand_total}', '{logo}', '{customer}');
                $replace = array($data['code'], $user->first_name.' '.$user->last_name,$this->mSettings->currency.$gtotal, '<img src="' . base_url() . 'assets/uploads/logos/' . $this->mSettings->logo . '" alt="' . $this->mSettings->title . '"/>',$client_details->name);
                $message = str_replace($search, $replace, $message);
                $this->repairer->send_email($emails, lang('Notification for New Repair'), $message);
              }
            }
            echo json_encode($result);
        }else{
            $this->repairer->send_json(['success'=>false, 'error'=>validation_errors()]);
        }
    }

    public function edit($id = NULL){
        $this->repairer->checkPermissions();
        // $id = $this->input->post('id');
        // if (!$id && !is_numeric($id)) {
        //     redirect('panel/repair');
        // }
        // $repair_d = $this->getRepairByID($id);
        // if ($repair_d['pos_sold'] == 1) {
        //     $this->repairer->send_json(['success'=>false, 'msg'=>lang('You cannot edit a completed repair')]);
        // }
        $this->form_validation->set_rules('client_name', lang("client_name"), 'required');
        if ($this->form_validation->run() == true) {
            
            $custom_fields = explode(',', $this->mSettings->custom_fields);
            $custom_checmarks = explode(',', $this->mSettings->repair_custom_checkbox);
            $custom_toggles = explode(',', $this->mSettings->repair_custom_toggles);
            $cust = array();
            $custcheck = array();
            $custtoggles = array();
            foreach ($_POST as $key => $var) {
                if (substr($key, 0, 7) === 'custom_' ) {
                    $cust[(substr($key, 7))] = $var;
                }
                if (substr($key, 0, 12) === 'checkcustom_' ) {
                    $custcheck[(substr($key, 12))] = $var;
                }
                if (substr($key, 0, 12) === 'checktoggle_' ) {
                    $custtoggles[(substr($key, 12))] = $var;
                }
            }
            $cust = (json_encode($cust));
            $custcheck = (json_encode($custcheck));
            $custtoggles = (json_encode($custtoggles));

            $client_details = $this->repair_model->getClientNameByID($this->input->post('client_name'));
            $warranty_plan = $this->input->post('warranty_id');
            $warranty_plan = json_encode($this->settings_model->getWarrantyByID($warranty_plan));
            $data = array(
                'client_id' => $this->input->post('client_name'),
                'name' => $client_details->name,
                'telephone' => $client_details->telephone,
                'defect' => $this->input->post('defect'),
                'defect_id' => $this->input->post('defect_id'),
                'category' => $this->input->post('category_select'),
                'manufacturer_id' => $this->input->post('manufacturer'),
                'model_id' => $this->input->post('model_id'),
                'model_name' => $this->input->post('model'),
                'advance' => $this->input->post('advance'),
                'service_charges' => $this->input->post('service_charges'),
                'tax_id' => $this->input->post('tax_id'),
                'comment' => $this->input->post('comment'),
                'status' => $this->input->post('status'),
                'code' => $this->input->post('code'),
                'custom_field' => $cust,
                'custom_checkboxes' => $custcheck,
                'updated_by' => $this->ion_auth->get_user_id(),
                'assigned_to' => $this->input->post('assigned_to'),
                'email' => $this->input->post('email'),
                'serial_number' => $this->input->post('serial_number'),
                // pre repair items
                'custom_toggles' => $custtoggles,
                'pin_code' => $this->input->post('cust_pin_code'),
                'pattern' => $this->input->post('patternlock'),
                'warranty' => $warranty_plan,
            );
            // if((int)$data['status'] == 2) {
            //     $data['date_closing'] = date('Y-m-d H:i:s');
            // }


            if ($this->mSettings->use_defects_input_dropdown && $defect_ = $this->settings_model->getDefectByID($this->input->post('defect_id'))) {
                $data['defect'] = $defect_->name;
            }

            if ($this->mSettings->use_models_input_dropdown && $model_ = $this->settings_model->getModelByID($this->input->post('model_id'))) {
                $data['model_name'] = $model_->name;
            }


            if (isset($_POST['sms']) && $_POST['sms'] !== null) {
                $data['sms'] = 1;
            }
            if ($_POST['code'] == null) {
                $data['code'] = time();
            }
            $products = array();
            $subtotal = 0;
            $total_tax = 0;
            $total_discount = 0;
            $total = 0;
            $gtotal = 0;
            $date = date('Y-m-d');
            if (isset($_POST['item_id']) && $_POST['item_id'] !== null) {
                $i = sizeof($_POST['item_id']);
                for ($r = 0; $r < $i; $r++) {
                    $item_id = $_POST['item_id'][$r];
                    $item_name = $_POST['item_name'][$r];
                    $item_code = $_POST['item_code'][$r];
                    $item_price = $_POST['item_price'][$r];
                    $item_cost = $_POST['item_cost'][$r];
                    $item_tax = $_POST['item_tax'][$r];
                    $item_tax_id = $_POST['item_tax_id'][$r];
                    $item_discount = $_POST['item_discount'][$r];
                    $item_details = $_POST['item_details'][$r];
                    $item_serial = $_POST['item_serial'][$r];
                    $item_type = $_POST['item_type'][$r];
                    $refund_item = $_POST['refund_item'][$r];
                    $add_to_stock = $_POST['add_to_stock'][$r];
                    $items_restock = $_POST['items_restock'][$r];
                    $phone_classification = $_POST['phone_classification'][$r];
                    $used_phone_vals = $_POST['used_phone_vals'][$r];
                    $variant = $_POST['product_option'][$r] !== 'null' ? $_POST['product_option'][$r] : NULL;
                    $phone_number   = $_POST['phone_number'][$r];
                    $set_reminder   = $_POST['set_reminder'][$r];
                    $warranty_id    = $_POST['product_warranty'][$r];
                    $warranty = $this->settings_model->getWarrantyByID($warranty_id);
                    $warranties = $warranty;
                    $warranties_ids[] = $warranty ? $warranty->id : NULL;
                    $warranty = json_encode($warranty);
                    $disount_code = $_POST['disount_code'][$r];
                    $activation_spiff = $_POST['activation_spiff'][$r];
                    $products[] = array(
                        'store_id'      => $this->activeStore,
                        'product_id'    => $item_id,
                        'unit_cost'     => $item_cost,
                        'product_name'  => $item_name,
                        'product_code'  => $item_code,
                        'quantity'      => 1,
                        'unit_price'    => $item_price,
                        'taxable'       => ($item_tax > 0) ? 1 : 0,
                        'tax'           => $item_tax,
                        'tax_rate'      => urldecode($item_tax_id),
                        'subtotal'      => ($item_price + $item_tax) - $item_discount,
                        'option_id'     => $variant,
                        'discount'      => $item_discount,
                        'item_type'     => $item_type,
                        'serial_number' => ($item_serial == 'null' || $item_serial == 'undefined') ? NULL : $item_serial,
                        'date'          => $date,
                        'refund_item'   => $refund_item,
                        'add_to_stock'  => $add_to_stock,
                        'sale_item_id'  => NULL,
                        'real_store_id' => $this->activeStore,
                        'store_id'      => $this->activeStore,
                        'phone_number'  => $phone_number,
                        'set_reminder'  => $set_reminder,
                        'warranty'      => $warranty,
                        'activation_spiff' => $activation_spiff,
                        'item_details' => $item_details,
                    );

                    $subtotal += ($item_price + $item_tax);
                    $total_tax += $item_tax;
                    $total_discount += $item_discount;
                }
            }


            // http_response_code(400);
            // print_r($products);die();


            $service_charges = (float)$this->input->post('service_charges');
            $tax_rate = $this->settings_model->getTaxRateByID($this->input->post('tax_id'));
            $sc_tax = 0;
            if ($tax_rate) {
                if ($tax_rate->type == 2) {
                    $sc_tax = ($tax_rate->rate);
                }
                if ($tax_rate->type == 1) {
                    $sc_tax = ((($service_charges) * $tax_rate->rate) / 100);
                }
            }
            $total_tax += $sc_tax;

            $gtotal = ($subtotal - $total_discount);
            $gtotal = $service_charges + $sc_tax + $gtotal;
            $data['tax'] = $total_tax;
            $data['total'] = $total;
            $data['grand_total'] = $gtotal;


            $this->repair_model->edit_repair($id, $data, $products);
        }else{
            $this->repairer->send_json(['success'=>false, 'error'=>validation_errors()]);
        }
    }
    public function delete($id = null){
        if($this->input->post('id')){
            $id = $this->input->post('id');
        }
        $repair = $this->repair_model->getRepairByID($id);
        if((int) $repair['pos_sold'] == 1) {
            if($this->input->is_ajax_request()){
                echo $this->repairer->send_json(['success'=>false, 'message'=>'pos invoiced']);die();
            }else{
                $this->session->set_flashdata('error', $repair['code'] . ": pos invoiced");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }

        $add_to_stock = (string)$this->input->post('add_to_stock');
        $sale_items = $this->repair_model->getAllRepairItems($id);

        if ($add_to_stock == 'true') {
            foreach ($sale_items as $item) {
                if ($item->item_type == 'repair' || $item->item_type == 'other' || $item->item_type == 'accessory') {
                    $keep_stock = 1;
                    if ($item->item_type == 'other') {
                         $qother = $this->db->get_where('other', array('id'=>$item->product_id));
                        if ($qother->num_rows > 0) {
                            $keep_stock = $qother->row()->keep_stock;
                        }
                    }
                    if ((int)$keep_stock == 1) {
                        $stock_data = array(
                            'price'             => $item->unit_cost,
                            'inventory_type'    => $item->item_type == 'new_phone' ? 'phones' : $item->item_type,
                            'inventory_id'      => $item->product_id,
                            'modified_date'     => date('Y-m-d H:i:s'),
                            'store_id'          => (int)$this->session->userdata('active_store'),
                            'in_state_of_transfer'  => 0,
                        );
                        if ($item->serial_number !== '') {
                            $stock_data['serial_number'] = $item->serial_number;
                        }
                        $this->db->insert('stock', $stock_data);
                    }
                }
            }
        }
        
        $this->db->where('id', $id);
        $this->db->delete('repair');
        
        if($this->input->is_ajax_request()){
            echo $this->repairer->send_json(['success'=>true]);
        }else{
            $this->session->set_flashdata('message', lang('deleted'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    public function getRepairByID($id = NULL){

        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }

        $data = $this->repair_model->getRepairByID($id);

        if ($this->input->post('id')) {
            echo json_encode($data);
        }else{
            return $data;
        }
        
    }

    public function view($id)
    {
        $this->data['settings'] = $this->mSettings;
        $this->data['repair'] = $this->getRepairByID($id);
        $this->data['repair']['invoice'] = $this->repair_model->getRepairPosInvoice($id);


        $this->load->view($this->theme.'repair/view', $this->data);
    }


    public function status_toggle($status){
        if ($status == 'complete') {
            $id = $this->input->post('id', true);
            $token = $this->input->post('token', true);


            $data = $this->repair_model->complete_repair($id);
            echo json_encode($data);
        }
        if ($status == 'approve') {
            $id = $this->input->post('id', true);
            $token = $this->input->post('token', true);


            $data = $this->repair_model->approved_repair($id);
            echo json_encode($data);
        }
        if ($status == 'tobedeliver') {
            $id = $this->input->post('id', true);
            $token = $this->input->post('token', true);


            $data = $this->repair_model->tobedeliver_repair($id);
            echo json_encode($data);
        }
    }

    
        // SEND A SMS DIRECT //
    public function send_sms()
    {
        $text = $this->input->post('text', true);
        $number = $this->input->post('number', true);
        $return = $this->repair_model->send_sms($number, $text);
        $status = false;

        if($return) { $status = true; }
        else { if($return->IsError != true) $status = true; }

        echo json_encode(array('status' => $status));
    }



    // SEND A SMS DIRECT //
    public function updateComment()
    {
        $comment = $this->input->post('comment', true);
        $id = $this->input->post('id', true);

        $data = array(
            'comment' => $comment,
        );
        if($this->db->where('id', $id)->update('repair',$data)){
            echo json_encode(array('success' => true));
        }else{
            echo json_encode(array('success' => false));
        }


    }
   
     public function invoice($id,$type)
    {
        $this->data['settings'] = $this->mSettings;
        $this->data['db'] = $this->repair_model->findRepairByID($id);
        $this->data['items'] = $this->repair_model->getAllRepairItems($id);
        $this->data['tax_rate'] = $this->settings_model->getTaxRateByID($this->data['db']['tax_id']);
        $this->data['client'] = $this->repair_model->getClientNameByID($this->repair_model->id_from_name($this->data['db']['name']));
        $this->data['currency'] = $this->mSettings->currency;
        $this->data['language'] = $this->mSettings->language;
        $this->data['status'] = $this->settings_model->getStatusByID($this->data['db']['status']);
        $this->data['payments'] = [];


        $this->data['tax_rate'] = $this->settings_model->getTaxRateByID($this->data['db']['tax_id']);
        $this->data['manufacturer'] = $this->settings_model->getModelByID($this->data['db']['manufacturer_id']);

        $this->data['user'] = $this->mUser;
        $this->data['two_copies'] = 0;
        $this->data['is_a4'] = 0;
        

        if($type == 1) {
           
            $this->mPageTitle = lang('invoice_title');
            if (in_array($this->mSettings->invoice_template, array(1,2,3,4))) {
                $this->load->view($this->theme . 'template/invoice_template'.$this->mSettings->invoice_template, $this->data);
            }else{
                $this->load->view($this->theme . 'template/invoice_template1', $this->data);
            }
        } else {
            
            $this->mPageTitle = lang('report');
            if (in_array($this->mSettings->report_template, array(1,2,3,4))) {
                $this->load->view($this->theme . 'template/report_template'.$this->mSettings->report_template, $this->data);
            }else{
                $this->load->view($this->theme . 'template/report_template1', $this->data);
            }
        };
    }

    public function save_signature() {
        $id = $this->input->post('id');
        $data = $this->input->post('data');
        $name = $id.'__'.time().'.png';
        $this->base30_to_jpeg($data, FCPATH.'assets/uploads/signs/'.$name);
        $this->db->where('id', $id);
        $this->db->update('repair', array('sign' => $name));
        echo "true";
    }

    function base30_to_jpeg($base30_string, $output_file) {
        require APPPATH.'libraries/jSignature.php';
        $data = str_replace('image/jsignature;base30,', '', $base30_string);
        $converter = new jSignature();
        $raw = $converter->Base64ToNative($data);
        //Calculate dimensions
        $width = 0;
        $height = 0;
        foreach($raw as $line) {
            if (max($line['x']) > $width) $width = max($line['x']);
            if (max($line['y']) > $height) $height = max($line['y']);
        }

        // Create an image
        $im = imagecreatetruecolor($width+20,$height+20);
        // Save transparency for PNG
        imagesavealpha($im, true);
        // Fill background with transparency
        $trans_colour = imagecolorallocatealpha($im, 255, 255, 255, 127);
        imagefill($im, 0, 0, $trans_colour);
        // Set pen thickness
        imagesetthickness($im, 2);
        // Set pen color to black
        $black = imagecolorallocate($im, 0, 0, 0);
        // Loop through array pairs from each signature word
        for ($i = 0; $i < count($raw); $i++)
        {
            // Loop through each pair in a word
            for ($j = 0; $j < count($raw[$i]['x']); $j++)
            {
                // Make sure we are not on the last coordinate in the array
                if ( ! isset($raw[$i]['x'][$j]))
                    break;
                if ( ! isset($raw[$i]['x'][$j+1]))
                // Draw the dot for the coordinate
                    imagesetpixel ( $im, $raw[$i]['x'][$j], $raw[$i]['y'][$j], $black);
                else
                // Draw the line for the coordinate pair
                imageline($im, $raw[$i]['x'][$j], $raw[$i]['y'][$j], $raw[$i]['x'][$j+1], $raw[$i]['y'][$j+1], $black);
            }
        }

        //Create Image
        $ifp = fopen($output_file, "wb");
        imagepng($im, $output_file);
        fclose($ifp);
        imagedestroy($im);
        return true;
    }



    public function email($id = null) {
        $email = $this->input->post('email');
        if ($this->input->post('id')) {
        $id = $this->input->post('id');
        }


        $repair = $this->repair_model->findRepairByID($id);
        $customer = $this->repair_model->getClientNameByID($this->repair_model->id_from_name($repair['name']));

        $this->data['page_title'] = 'Invoice';
        $this->data['settings'] = $this->mSettings;


        $this->load->library('parser');
        $message = file_get_contents(FCPATH.'themes/'.$this->theme.'email_templates/invoice.html');



        $parse_data = array(
            'stylesheet' => '<link rel="stylesheet" href="'.$this->assets.'assets/vendor/bootstrap/css/bootstrap.css" />',
            'name' => $customer->company && $customer->company != '-' ? $customer->company :  $customer->name,
            'email' => $customer->email,
            'heading' => 'Invoice'.'<hr>',
            'msg' => $message,
            'site_link' => base_url(),
            'site_name' => $this->mSettings->title,
            'logo' => '<img src="' . base_url('assets/uploads/logos/' . $this->mSettings->logo) . '" alt="' . $this->mSettings->title . '"/>',
            'email_footer' => '',
        );

        $msg = file_get_contents(FCPATH.'themes/'.$this->theme.'email_templates/email_con.html');
        $message = $this->parser->parse_string($msg, $parse_data, TRUE);
        $subject = 'Invoice' . ' - ' . $this->mSettings->title;
                    

        $repair = $this->repair_model->findRepairByID($id);
        $this->data['db'] = $repair;
        $this->data['items'] = $this->repair_model->getAllRepairItems($id);
        $this->data['tax_rate'] = $this->settings_model->getTaxRateByID($this->data['db']['tax_id']);
        $this->data['client'] = $this->repair_model->getClientNameByID($this->repair_model->id_from_name($this->data['db']['name']));
        $this->data['currency'] = $this->mSettings->currency;
        $this->data['language'] = $this->mSettings->language;
        $this->data['status'] = $this->settings_model->getStatusByID($this->data['db']['status']);
        $this->data['payments'] = array();
        $this->data['user'] = $this->mUser;
        $this->data['two_copies'] = 0;
        $this->data['is_a4'] = 0;
        $this->data['pdf'] = true;
        $this->data['settings'] = $this->mSettings;



        $name = lang("repair") . "_" . str_replace('/', '_', $repair['code']) . ".pdf";
        

        if (in_array($this->mSettings->invoice_template, array(1,2,3,4))) {
            $html = $this->load->view($this->theme . 'template/invoice_template'.$this->mSettings->invoice_template, $this->data, true);
        }else{
            $html = $this->load->view($this->theme . 'template/invoice_template1', $this->data, true);
        }


        $pdf = $this->repairer->generate_pdf($html, lang('invoice').'.pdf', 'S', null, null, null, null, 'P', ($this->data['db']['payment_status'] =='paid' ? base_url().'assets/images/paid_mark_en.png' : null));



        if ($this->repairer->send_email($email, $subject, $message, null, null, $pdf)) {
            $this->repairer->send_json(array('msg' => lang("email_sent")));
        } else {
            $this->repairer->send_json(array('msg' => lang("email_failed")));
        }
    }
    // Toggles
    function toggle() {
        $toggle = $this->input->post('toggle');
        if ($toggle == 'enable') {
            $data = array('disable' => 0);
            $a = lang('enabled');
        } else {
            $data = array('disable' => 1);
            $a = lang('disabled');
        }
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('repair', $data);
        echo json_encode(array('ret' => 'true', 'toggle' => $a));
    }



     // Reparation Payments
      public function payments($id = null)
    {
        $this->data['payments'] = $this->repair_model->getRepairPayments($id);
        $this->data['inv'] = $this->repair_model->getTRepairByID($id);
        $this->load->view($this->theme.'/repair/payments', $this->data);
    }


    public function delete_payment($id = null) {
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }


        if ($this->repair_model->deletePayment($id)) {
            $this->session->set_flashdata('message', lang("payment_deleted"));
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }


    public function add_payment($id = NULL)
    {
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        if ($this->input->post('sale_id')) {
            $id = $this->input->post('sale_id');
        }

        $sale = $this->repair_model->getTRepairByID($id);
        if ($sale->payment_status == 'paid' && $sale->grand_total == $sale->paid) {
            $this->session->set_flashdata('message', lang('sale_already_paid'));
            $this->repairer->md();
        }

        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == TRUE) {
            $date = date('Y-m-d H:i:s');
            $payment = array(
                'date'         => $date,
                'repair_id'      => $this->input->post('sale_id'),
                'reference_no' => $this->repairer->getReference('pay'),
                'amount'       => $this->input->post('amount-paid'),
                'paid_by'      => $this->input->post('paid_by'),
                'cheque_no'    => $this->input->post('cheque_no'),
                'cc_no'        => $this->input->post('paid_by') == 'voucher' ? $this->input->post('voucher_no') : $this->input->post('pcc_no'),
                'cc_holder'    => $this->input->post('pcc_holder'),
                'cc_month'     => $this->input->post('pcc_month'),
                'cc_year'      => $this->input->post('pcc_year'),
                'cc_type'      => $this->input->post('pcc_type'),
                'cc_cvv2'      => $this->input->post('pcc_ccv'),
                'note'         => $this->input->post('note'),
                'created_by'   => $this->session->userdata('user_id'),
                'type'         => 'received',
            );

           

        } elseif ($this->input->post('add_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == TRUE && $msg = $this->repair_model->addPayment($payment)) {
            if ($msg) {
                $this->session->set_flashdata('message', lang("payment_added"));
                $success = true;
            } else {
                $this->session->set_flashdata('error', lang("payment_failed"));
                $success = false;
            }
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $sale = $this->repair_model->getTRepairByID($id);
            $this->data['inv'] = $sale;
            $this->data['payment_ref'] = $this->repairer->getReference('pay');
            $this->load->view($this->theme.'/repair/add_payment', $this->data);
        }
    }

    public function edit_payment($id = null, $repair_id = null)
    {
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $payment = $this->repair_model->getPaymentByID($id);
        $sale = $this->repair_model->getTRepairByID($payment->repair_id);
        if ($sale->payment_status == 'paid' && $sale->grand_total == $sale->paid) {
            $this->session->set_flashdata('message', lang('sale_already_paid'));
            $this->repairer->md();
        }

        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            $date = date('Y-m-d H:i:s');

            $payment = array(
                'date' => $date,
                'repair_id' => $this->input->post('repair_id'),
                'reference_no' => $this->repairer->getReference('pay'),
                'amount' => $this->input->post('amount-paid'),
                'paid_by' => $this->input->post('paid_by'),
                'cheque_no' => $this->input->post('cheque_no'),
                'cc_no'        => $this->input->post('paid_by') == 'voucher' ? $this->input->post('voucher_no') : $this->input->post('pcc_no'),
                'cc_holder' => $this->input->post('pcc_holder'),
                'cc_month' => $this->input->post('pcc_month'),
                'cc_year' => $this->input->post('pcc_year'),
                'cc_type' => $this->input->post('pcc_type'),
                'note'         => $this->input->post('note'),
                'created_by' => $this->session->userdata('user_id'),
            );

        } elseif ($this->input->post('edit_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->repair_model->updatePayment($id, $payment)) {
            $this->session->set_flashdata('error', lang('payment_updated'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['payment'] = $payment;
            $this->data['inv'] = $sale;
            $this->load->view($this->theme . '/repair/edit_payment', $this->data);
        }
    }




    function print_barcodes($repair_id = NULL)
    {
        $this->mPageTitle = lang('print_barcode');
        $this->form_validation->set_rules('style', lang("style"), 'required');

        if ($this->form_validation->run() == true) {
            $style = $this->input->post('style');
            $bci_size = ($style == 10 || $style == 12 ? 50 : ($style == 14 || $style == 18 ? 30 : 20));
            if ($style == 50) {
                $bci_size = 30;
            }
            $this->data['bci_size'] = $bci_size;
            $s = isset($_POST['product']) ? sizeof($_POST['product']) : 0;
            if ($s < 1) {
                $this->session->set_flashdata('error', lang('no_product_selected'));
                redirect("panel/repair/print_barcodes");
            }
            for ($m = 0; $m < $s; $m++) {
                $pid = $_POST['product'][$m];
                $quantity = $_POST['quantity'][$m];
                $product = $this->repair_model->getRepairByID($pid);
                $barcodes[] = array(
                    'site' => $this->input->post('site_name') ? $this->mSettings->title : FALSE,
                    'name' => $this->input->post('client_name') ? $product['name'] : FALSE,
                    'model' => $this->input->post('model') ? $product['model_name'] : FALSE,
                    'serial_number' => $this->input->post('serial_number') ? $product['serial_number'] : FALSE,
                    'price' => $this->input->post('price') ? number_format($product['grand_total'], 0, '', '') : FALSE,
                    'barcode' => ($product['code']),
                    'quantity' => $quantity,
                    'defect' => $product['defect'],
                    'telephone' => $this->input->post('telephone') ? $product['telephone'] : FALSE,
                    'repair_id' => $this->input->post('repair_id') ? $product['id'] : FALSE,

                );
            }
            $this->data['barcodes'] = $barcodes;
            $this->data['style'] = $style;
            $this->data['items'] = false;
            
            $this->render('repair/print_barcodes');
        } else {
            if ($repair_id) {
                if ($row = $this->repair_model->getRepairByID($repair_id)) {
                    $pr[$row['id']] = array(
                        'id' => $row['id'], 
                        'label' => $row['name'] . " (" . $row['model_name'] . ")", 
                        'name' => $row['name'], 
                        'serial_number' => $row['serial_number'], 
                        'model' => $row['model_name'], 
                        'qty' => 1
                    );
                    $this->session->set_flashdata('message',  lang('product_added_to_list'));
                }
            }
            $this->data['items'] = isset($pr) ? json_encode($pr) : false;
            $this->render('repair/print_barcodes');
        }
    }

    public function syncRepairPayments($id)
    {
        $this->repair_model->syncRepairPayments($id);
    }

    // Upload Attachments Plugin
    public function upload_attachments()
    {
        // upload.php
        // 'images' refers to your file input name attribute
        if (empty($_FILES['upload_manager'])) {
            echo json_encode(['error'=>lang('upload_no_file')]); 
            // or you can throw an exception 
            return; // terminate
        }
        // get user id posted
        $repair_id = $this->input->post('id') ? $this->input->post('id') : NULL;

        // a flag to see if everything is ok
        $success = null;

        // file paths to store
        $paths = [];

        // loop and process files
        $this->load->library('upload');
        $number_of_files_uploaded = count($_FILES['upload_manager']['name']);
        for ($i = 0; $i < $number_of_files_uploaded; $i++) {
            $_FILES['userfile']['name']     = $_FILES['upload_manager']['name'][$i];
            $_FILES['userfile']['type']     = $_FILES['upload_manager']['type'][$i];
            $_FILES['userfile']['tmp_name'] = $_FILES['upload_manager']['tmp_name'][$i];
            $_FILES['userfile']['error']    = $_FILES['upload_manager']['error'][$i];
            $_FILES['userfile']['size']     = $_FILES['upload_manager']['size'][$i];
            $config = array(
                'upload_path'   => 'files/',
                'allowed_types' => 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt',
                'max_size'      => 204800,
            );
            $this->upload->initialize($config);
            if ( ! $this->upload->do_upload('userfile')){
                $success = false;
                break;
            }else{
                $success = true;
                $paths[] = $this->upload->file_name;
            }
        }

        // check and process based on successful status 
        if ($success === true) {
            $uploaded_ids = array();
            foreach ($paths as $file) {
                $label = explode('.', $file);
                $data = array(
                    'label' => $label[0],
                    'filename' => $file,
                    'added_date' => date('Y-m-d H:i:s'),
                    'reparation_id' => $repair_id,
                );
                $this->db->insert('attachments', $data);
                $uploaded_ids[] = $this->db->insert_id();
            }
            $output = ["success"=> true, 'data'=>json_encode($uploaded_ids)];
        } elseif ($success === false) {
            $output = ['error'=>lang('error_Contant_support')];
            foreach ($paths as $file) {
                unlink('files/'.$file);
            }
        } else {
            $output = ['error'=>lang('error_proccess_upload')];
        }

        echo json_encode(array_unique($output));
    }
    public function getAttachments()
    {
        $id = $this->input->post('id');
        $q = $this->db->get_where('attachments', array('reparation_id'=>$id));

        $urls = array();
        $previews = array();
        if ($q->num_rows() > 0) {
            $result = $q->result();
            foreach ($result as $row) {
                $url = base_url().'files/'.$row->filename;
                $burl = FCPATH.'files/'.$row->filename;
                if (file_exists($burl)) {
                    list($width) = getimagesize($burl);
                    $size = filesize($burl);
                    $extension = (explode('.', $row->filename));
                    $extension = $extension[count($extension) - 1];
                    if (in_array($extension, explode('|', 'doc|docx|xls|xlsx|ppt|pptx'))) {
                        $type = 'office';
                    }elseif (in_array($extension, explode('|', 'pdf'))) {
                        $type = 'pdf';

                    }elseif (in_array($extension, explode('|', 'htm|html'))) {
                        $type = 'html';
                    }elseif (in_array($extension, explode('|', 'txt|ini|csv|java|php|js|css'))) {
                        $type = 'text';
                    }elseif (in_array($extension, explode('|', 'avi|mpg|mkv|mov|mp4|3gp|webm|wmv'))) {
                        $type = 'video';
                    }elseif (in_array($extension, explode('|', 'mp3|wav'))) {
                        $type = 'audio';
                    }
                    elseif (in_array($extension, explode('|', 'doc|docx|xls|xlsx|ppt|pptx'))) {
                        $type = 'office';
                    }
                    elseif (in_array($extension, explode('|', 'png|gif|jpg|jpeg|tif'))) {
                        $type = 'image';
                    }else{
                        $type = 'other';
                    }
            
                    $previews[] = array(
                        'caption' => $row->label,
                        'filename' => $row->filename,
                        'downloadUrl' => $url,
                        'size' => $width,
                        'width' => (string)$width.'px',
                        'key'=>$row->id,
                        'filetype' => mime_content_type($burl),
                        'type'=>$type,
                    );
                    $urls[] = $url;
                }
                
            }
        }
        echo $this->repairer->send_json(array(
            'show_data' => !empty($urls) ? TRUE : FALSE,
            'previews' => $previews,
            'urls' => $urls,
        ));
    }
    public function delete_attachment()
    {
        $id = $this->input->post('key');
        $q = $this->db->get_where('attachments', array('id'=>$id));
        if ($q->num_rows() > 0) {
            $row = $q->row();
            $this->db->delete('attachments', array('id'=>$id));
            unlink(FCPATH.'/files/'.$row->filename);
            $this->repairer->send_json(array('success'=>true));

            $this->settings_model->addLog('delete-attachment', 'repair_id', $row->reparation_id, json_encode(array(
                'filename' => $row->filename,
            )));

            return true;
        }
        $this->repairer->send_json(array('success'=>false));
        return false;

    }


    public function view_pdf($id, $save_bufffer = false)
    {
        $this->load->model('repair_model');
        
        if (!$id) {
            redirect('panel/repair');
        }
       
        $repair = $this->repair_model->findRepairByID($id);
        $this->data['db'] = $repair;
        $this->data['items'] = $this->repair_model->getAllRepairItems($id);
        $this->data['tax_rate'] = $this->settings_model->getTaxRateByID($this->data['db']['tax_id']);
        $this->data['client'] = $this->repair_model->getClientNameByID($this->repair_model->id_from_name($this->data['db']['name']));
        $this->data['currency'] = $this->mSettings->currency;
        $this->data['language'] = $this->mSettings->language;
        $this->data['status'] = $this->settings_model->getStatusByID($this->data['db']['status']);
        $this->data['payments'] = array();
        $this->data['user'] = $this->mUser;
        $this->data['two_copies'] = 0;
        $this->data['is_a4'] = 0;
        $this->data['pdf'] = true;
        $this->data['settings'] = $this->mSettings;



        $name = lang("repair") . "_" . str_replace('/', '_', $repair['code']) . ".pdf";
        $html = $this->load->view($this->theme.'repair/pdf/index', $this->data, true);
        $footer = $this->load->view($this->theme.'repair/pdf/footer', $this->data, true);

        if ($save_bufffer) {
            return $this->repairer->generate_pdf($html, lang('invoice').'.pdf', 'S', $footer, 10, null, 5);
        }
        $this->repairer->generate_pdf($html, $name, 'I', $footer, 10, null, 5);

    }

     public function invoice_pdf($id = null, $quote = false) {
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }

        $repair = $this->repair_model->findRepairByID($id);
        $customer = $this->repair_model->getClientNameByID($repair['client_id']);

        $repair = $this->repair_model->findRepairByID($id);
        $this->data['db'] = $repair;
        $this->data['items'] = $this->repair_model->getAllRepairItems($id);
        $this->data['tax_rate'] = $this->settings_model->getTaxRateByID($this->data['db']['tax_id']);
        $this->data['client'] = $this->repair_model->getClientNameByID($this->data['db']['client_id']);
        $this->data['currency'] = $this->mSettings->currency;
        $this->data['language'] = $this->mSettings->language;
        $this->data['status'] = $this->settings_model->getStatusByID($this->data['db']['status']);
        $this->data['payments'] = array();
        $this->data['user'] = $this->mUser;
        $this->data['two_copies'] = 0;
        $this->data['is_a4'] = 0;
        $this->data['pdf'] = true;
        $this->data['settings'] = $this->mSettings;
        $name = lang("repair") . "_" . str_replace('/', '_', $repair['code']) . ".pdf";




        if ($quote) {
            if (in_array($this->mSettings->invoice_template, array(1,2,3,4))) {
                $html = $this->load->view($this->theme . 'template/report_template'.$this->mSettings->invoice_template, $this->data, true);
            }else{
                $html = $this->load->view($this->theme . 'template/report_template1', $this->data, true);
            }

            $pdf = $this->repairer->generate_pdf($html, lang('quote').'.pdf', 'I', null, null, null, null, 'P', ($this->data['db']['payment_status'] =='paid' ? base_url().'assets/images/paid_mark_en.png' : null));
        }else{
            if (in_array($this->mSettings->invoice_template, array(1,2,3,4))) {
                $html = $this->load->view($this->theme . 'template/invoice_template'.$this->mSettings->invoice_template, $this->data, true);
            }else{
                $html = $this->load->view($this->theme . 'template/invoice_template1', $this->data, true);
            }
            $pdf = $this->repairer->generate_pdf($html, lang('invoice').'.pdf', 'I', null, null, null, null, 'P', ($this->data['db']['payment_status'] =='paid' ? base_url().'assets/images/paid_mark_en.png' : null));
        }

    }




    public function actions()
    {
        if (!$this->Admin) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->delete($id);
                    }
                    $this->session->set_flashdata('message', lang('users_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->setTitle(lang('Repairs'));
                    $sheet->SetCellValue('A1', lang('Serial Number'));
                    $sheet->SetCellValue('B1', lang('repair_name'));
                    $sheet->SetCellValue('C1', lang('client_telephone'));
                    $sheet->SetCellValue('D1', lang('repair_defect'));
                    $sheet->SetCellValue('E1', lang('repair_model'));
                    $sheet->SetCellValue('F1', lang('repair_opened_at'));
                    $sheet->SetCellValue('G1', lang('repair_status'));
                    $sheet->SetCellValue('H1', lang('assigned_to'));
                    $sheet->SetCellValue('I1', lang('repair_code'));
                    $sheet->SetCellValue('J1', lang('grand_total'));
                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $repair = $this->repair_model->getRepairByID($id);
                        $user = $this->settings_model->getUser($repair['assigned_to']);
                        $status = $this->settings_model->getStatusByID($repair['status']);
                        $sheet->SetCellValue('A' . $row, $repair['serial_number']);
                        $sheet->SetCellValue('B' . $row, $repair['name']);
                        $sheet->SetCellValue('C' . $row, $repair['telephone']);
                        $sheet->SetCellValue('D' . $row, $repair['defect']);
                        $sheet->SetCellValue('E' . $row, $repair['model_name']);
                        $sheet->SetCellValue('F' . $row, $repair['date_opening']);
                        $sheet->SetCellValue('G' . $row, $status ? $status->label : '');
                        $sheet->SetCellValue('H' . $row, $user ? $user->first_name . ' ' . $user->last_name : '');
                        $sheet->SetCellValue('I' . $row, $repair['code']);
                        $sheet->SetCellValue('J' . $row, $repair['grand_total']);
                        $row++;
                    }

                    $sheet->getColumnDimension('A')->setWidth(10);
                    $sheet->getColumnDimension('B')->setWidth(30);
                    $sheet->getColumnDimension('C')->setWidth(25);
                    $sheet->getColumnDimension('D')->setWidth(45);
                    $sheet->getColumnDimension('E')->setWidth(15);
                    $sheet->getColumnDimension('F')->setWidth(15);

                    $sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    $filename = 'repairs_' . date('Y_m_d_H_i_s');


                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
                    header('Cache-Control: max-age=0');
                    $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
                    $writer->save('php://output');
                    exit();
                }
            } else {
                $this->session->set_flashdata('error', lang('no_user_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

}