<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Pos extends Auth_Controller {

	public function __construct()
	{
		parent::__construct();
        $this->load->model('repair_model');
		$this->load->model('pos_model');
	}
  
	/* ----------------------- */

    public function index($sid = NULL)
    {

        $this->showPageTitle = false;
        $this->repairer->checkPermissions();
        if ($register = $this->pos_model->registerData($this->session->userdata('user_id'))) {
            $register_data = array('register_id' => $register->id, 'cash_in_hand' => $register->cash_in_hand, 'register_open_time' => $register->date);
            $this->session->set_userdata($register_data);
        } else {
            $this->session->set_flashdata('error', "Register not open");
            redirect('panel/pos/open_register');
        }


        //validate form input
        $this->form_validation->set_rules('client_name', lang('Customer'), 'trim|required');
        $this->form_validation->set_rules('biller', lang('Biller'), 'required');
        if ($this->form_validation->run() == TRUE) {
/*
	        echo "<pre>";
	        print_r($_POST);
	        die();
*/
            $sale_type = $this->input->post('sale_type');
            $date = date('Y-m-d H:i:s');
            $customer_id = $this->input->post('client_name');
            $note = $this->input->post('note');
            if ($customer_id == -1) {
                $customer = lang('walk_in');
            }else{
                $customer_details = $this->pos_model->getClientByID($customer_id);
                $customer = $customer_details->first_name . ' ' . $customer_details->last_name;
            }
            
           
            $reference = $this->pos_model->getReference();

            $products = array(); 
            $warranties = ''; 
            $warranties_ids = array(); 
            $subtotal = 0;
            $total_tax = 0;
            $total_discount = 0;
            $total = 0;
            $gtotal = 0;
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
                        'sale_item_id'  => ($sale_type == 'refund') ? $_POST['sale_item_id'][$r] : NULL,
                        'items_restock'  => $items_restock,
                        'phone_classification'  => $phone_classification,
                        'used_phone_vals'  => $used_phone_vals,
                        'real_store_id' => $this->activeStore,
                        'store_id'  => $this->input->get('refund') ? $this->pos_model->getStoreIDBySaleID($this->input->get('refund')) : $this->activeStore,
                        'phone_number'  => $phone_number,
                        'set_reminder'  => $set_reminder,
                        'warranty'      => $warranty,
                        'discount_code_used' => $disount_code,
                        'activation_spiff' => $activation_spiff,
                        'item_details' => $item_details,
                    );

                    $subtotal += ($item_price + $item_tax);
                    $total_tax += $item_tax;
                    $total_discount += $item_discount;
                }
            }


            $gtotal = $subtotal - $total_discount;

            $biller_id = $this->input->post('biller');
            $biller = $this->ion_auth->user($biller_id)->row();
            $biller = $biller->first_name.' '.$biller->last_name;
            $created_by = $this->session->userdata('user_id');
            
            if ($this->input->post('pin_code')) {
                $q = $this->db->get_where('users', array('pin_code'=>$this->input->post('pin_code')));
                if ($q->num_rows() > 0) {
                    $biller = $q->row();
                    $biller_id = $q->row()->id;
                    $biller = $biller->first_name.' '.$biller->last_name;
                }
            }elseif($this->Admin){
                $q = $this->db->get_where('users', array('id'=>$this->input->post('biller_id')));
                if ($q->num_rows() > 0) {
                    $biller = $q->row();
                    $biller_id = $q->row()->id;
                    $biller = $biller->first_name.' '.$biller->last_name;
                }
            }

            // // Check all Warranties
            if ($warranties && count(array_unique($warranties_ids)) === 1) {
                $warranties->success = true;
            }else{
                $warranties = array('success'=>false);
            }
          
            $payment_status = 'due';

            $data = array(
                'date'              => $date,
                'reference_no'      => $reference,
                'customer_id'       => $customer_id,
                'customer'          => $customer,
                'biller_id'         => $biller_id,
                'biller'            => $biller,
                'total_discount'    => $total_discount,
                'total_tax'         => $total_tax,
                'grand_total'       => $gtotal + (($sale_type == 'refund')?$this->input->post('surcharge'):0),
                'total_items'       => sizeof($_POST['item_id']),
                'paid'              => 0,
                'created_by'        => $created_by,
                'sale_status'       => 'completed',
                'real_store_id'     => $this->activeStore,
                'store_id'  => $this->input->get('refund') ? $this->pos_model->getStoreIDBySaleID($this->input->get('refund')) : $this->activeStore,
                'warranties' =>  json_encode($warranties),
                'payment_status' =>  $payment_status,
            );
            
            if ($sale_type == 'refund') {
	            
                $data['sale_id'] = $this->input->get('refund');
                $data['note'] = $this->input->post('renote');
                $data['surcharge'] = $this->input->post('surcharge');
                $data['return_sale_ref'] = $reference;
                $data['sale_status'] = 'returned';
            }
            
            $authorize = false;
           
            $p = isset($_POST['amount']) ? sizeof($_POST['amount']) : 0;
            for ($r = 0; $r < $p; $r++) {
	            
                if (isset($_POST['amount'][$r]) && 
                    !empty($_POST['amount'][$r]) && 
                    isset($_POST['paid_by'][$r]) && 
                    !empty($_POST['paid_by'][$r])
                ) {
	                
                    $amount = ($_POST['balance_amount'][$r] > 0 ? $_POST['amount'][$r] - $_POST['balance_amount'][$r] : $_POST['amount'][$r]);
                    if($_POST['paid_by'][$r] == 'authorize'){
		                $authorize = array('amount'=> $amount,'number'=>$_POST['cc_no'][$r], 'exp'=>$_POST['cc_year'][$r].'-'.$_POST['cc_month'][$r]);
	            	}
                    $payment[] = array(
                        'date'         => $date,
                        'amount'       => $amount,
                        'paid_by'      => $_POST['paid_by'][$r],
                        'cheque_no'    => $_POST['cheque_no'][$r],
                        'cc_no'        => $_POST['cc_no'][$r],
                        'cc_holder'    => $_POST['cc_holder'][$r],
                        'cc_month'     => $_POST['cc_month'][$r],
                        'cc_year'      => $_POST['cc_year'][$r],
                        'cc_type'      => $_POST['cc_type'][$r],
                        'cc_cvv2'      => $_POST['cc_cvv2'][$r],
                        'created_by'   => $this->session->userdata('user_id'),
                        'type'         => ($sale_type == 'refund') ? 'returned' : 'received',
                        'note'         => $_POST['payment_note'][$r],
                        'pos_paid'     => $_POST['amount'][$r],
                        'pos_balance'  => $_POST['balance_amount'][$r],
                        'store_id'  => $this->activeStore,
                    );
                    $pp[] = $amount;
                }
            }
            if (!empty($pp)) {
                $paid = array_sum($pp);
            } else {
                $paid = 0;
            }
            if (!isset($payment) || empty($payment)) {
                $payment = array();
            }
            $data['paid'] = $paid;
            
            if(is_array($authorize)){
	            $this->load->library('authorize');
	            if ($sale_type == 'refund') {
	            	$this->authorize->refundTransaction($authorize['amount'], array('number'=>$authorize['number'], 'exp'=>$authorize['exp']));
	            }else{
		            $nounce = array();
			        $nounce['desc'] = $this->input->post('dataDesc');
			        $nounce['value'] = $this->input->post('dataValue');
		            $result = $this->authorize->createAnAcceptPaymentTransaction($authorize['amount'], $nounce);
		            if(!$result){
			            $this->session->set_flashdata('error', 'Error occurred while processing Authorize.Net Payment. The Sale was not added. ');
			            redirect('panel/pos/');
			            exit();
		            }
	            }
	        }
        } 

        if ($this->form_validation->run() == TRUE && !empty($products) && !empty($data)) {
            if ($sale = $this->pos_model->addSale($data, $products, $payment, ($sale_type == 'refund' ? TRUE : FALSE))) {
                $this->admin_email($sale);
                $_SESSION['remove_posls'] = TRUE;
                $this->session->set_userdata('remove_posls', TRUE);
                $msg = "Sale Added";
                $this->session->set_flashdata('message', $msg);

                $redirect_to = $this->mSettings->after_sale_page ? 'panel/pos' :'panel/pos/view/'.$sale;
                redirect($redirect_to);
            }
        } else {
            $this->data['remove_posls'] = $this->session->userdata('remove_posls');
            
            $this->session->set_userdata('remove_posls', FALSE);
        	$this->load->helper('text');
        	$this->load->model('repair_model');
        	$this->data['customers'] = $this->repair_model->getAllClients();
            $this->data['reference_note'] = NULL;
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['message'] = isset($this->data['message']) ? $this->data['message'] : $this->session->flashdata('message');
       		$this->data['tax_rates'] = $this->settings_model->getTaxRates();
            $this->data['user'] = $this->ion_auth->user()->row();


		    $register_open_time = $this->session->userdata('register_open_time');
            $cashsales = $this->pos_model->getRegisterCashSales($register_open_time);
            $tosafetranfers = $this->pos_model->getSafeTotals($register_open_time);
            $todrawertranfers = $this->pos_model->getDrawerTotal($register_open_time);
            $total_cash = $cashsales->paid ? ((($cashsales->paid + ($this->session->userdata('cash_in_hand'))) + $todrawertranfers->total) - $tosafetranfers->total) : (((($this->session->userdata('cash_in_hand'))) + $todrawertranfers->total) - $tosafetranfers->total);


            $this->data['max_drawer_lock'] = $total_cash > $this->mSettings->max_drawer_amount ? TRUE : FALSE;
            $this->data['users'] = $this->db->where('active', 1)->where('hidden', 0)->get('users')->result();
            
            // All Products
            $this->data['new_phones'] = $this->pos_model->getNewPhones();
            $this->data['used_phones'] = $this->pos_model->getUsedPhones();
            $this->data['others'] = $this->pos_model->getOthers();
            $this->data['accessories'] = $this->pos_model->getAccessoryNames();
            $this->data['plans'] = $this->pos_model->getAllPlans();
            $this->data['repair_items'] = $this->pos_model->getProductNames();
            $this->data['crepairs'] = $this->pos_model->getCheckedIn();
            $this->data['drepairs'] = $this->pos_model->getDeliverables();
            $this->data['cp'] = $this->pos_model->getCustomerPurchases();

            $this->render('pos/add');
        }
    }

   

	public function open_register()
    {
        $this->repairer->checkPermissions('index');

        $q = $this->db->get_where('pos_register', array('user_id'=>$this->session->userdata('user_id'), 'status'=>'open', 'store_id'=>$this->activeStore));
        if ($q->num_rows() > 0) {
            $this->session->set_flashdata('error', lang('Register open'));
            redirect('panel/pos');
        }

        $this->form_validation->set_rules('cash_in_hand', lang('cash_in_hand'), 'trim|required|numeric');
        if ($this->form_validation->run() == TRUE) {
            extract($_POST);


            $cash_data = [];
            $currency_sets = $this->repairer->returnOpenRegisterSets();
            foreach($currency_sets as $input => $name){
                $cash_data['n'.$name]=$this->input->post('n'.$name);
            }
            $cash_data = json_encode($cash_data);

            $data = array(
                'date'          => date('Y-m-d H:i:s'),
                'cash_in_hand'  => $this->input->post('cash_in_hand'),
                'user_id'       => $this->session->userdata('user_id'),
                'status'        => 'open',
                'cash_data'     => $cash_data,
                'store_id'     => $this->activeStore,

            );
        }
        
        if ($this->form_validation->run() == TRUE) {
        	$this->db->insert('pos_register', $data);
            $this->session->set_flashdata('message', lang('Drawer successfully opened'));
            redirect("panel/pos");
        } else {
            echo validation_errors();
           	$this->render('pos/open_register');
        }
    }

    public function suggestions()
    {
    	$this->load->library('repairer');
        $is_repair = $this->input->get('is_repair', true);
        $term = $this->input->get('term', true);
        $type = $this->input->get('type');
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . site_url('welcome') . "'; }, 10);</script>");
        }
       
        $rows = $this->pos_model->getAllProductNames($term, 5, $this->mSettings->sell_repair_parts, $is_repair);
        
        $rows = array_filter((array)$rows);
        sort_array_of_array($rows, 'name');

        if ($rows) {
            $c = str_replace(".", "", microtime(true));
            $r = 0;
            foreach ($rows as $row) {
                if ($row->taxable) {
                    if ($row->type == 'repair') {
                        $row->tax_rates = $this->activeStoreData->repair_items_tax; 
                    }elseif ($row->type == 'accessory') {
                        $row->tax_rates = $this->activeStoreData->accessories_tax; 
                    }elseif ($row->type == 'other') {
                        $row->tax_rates = $this->activeStoreData->other_items_tax; 
                    }elseif ($row->type == 'new_phone') {
                        $row->tax_rates = $this->activeStoreData->new_phone_tax; 
                    }elseif ($row->type == 'used_phone') {
                        $row->tax_rates = $this->activeStoreData->used_phone_tax; 
                    }elseif ($row->type == 'plans') {
                        $row->tax_rates = $this->activeStoreData->plans_tax; 
                    }else{
                       $row->taxable = 0; 
                    }
                }
            	if ($row->taxable) {
                    $tax_rates = explode(',', $row->tax_rates);
                    $o_taxes = array();
                    foreach ($tax_rates as $taxrate) {
                        $o_taxes[] = $this->db->get_where('tax_rates', array('id' => (int)$taxrate))->row();
                    }
                }else{
                    $o_taxes = NULL;
                }
                if ($row->type == 'crepairs' or $row->type == 'drepairs') {
                    $item_id = $row->type.$row->id;
                    $row_id = $row->type.$row->id;
                }else{
                    $item_id = $row->type.($c + $r);
                    $row_id = $row->type.time();
                }

                $label =  $row->name . " (" . $row->code . ")";
                if ($this->Admin){
                    $label = $row->name . " (" . $row->code . ")" .'| '.lang('price').':' .$row->price . ($row->cost ? ' '.lang('cost').':' .$row->cost : '') .' '.lang('quantity').':' .$row->qty;
                }

                $pr[] = array(
                    'row_id' => $row_id,
                    'item_id' => $item_id,
                    'label' => $label, 
                    'code' => $row->code, 
                    'name' => $row->name, 
                    'price' => $row->price, 
                    'qty' => $row->qty, 
                    'type' => $row->type, 
                    'cost'=>$row->cost, 
                    'stock_id'=>$row->stock_id, 
                    'product_id'=>$row->id,
                    'taxable'=>$row->taxable,
                    'pr_tax' => $o_taxes,
                    'variants' => $row->variants ? TRUE : FALSE,
                    'option_selected' => FALSE,
                    'options' => $row->variants,
                    'option' => NULL,
                    'item_details' => '',
                    'row' => $row,
                    'discount' => 0,
                    'is_serialized' => (int)$row->is_serialized,
                    'serialed' => (int)$row->is_serialized ? (isset($row->serial_number) ? TRUE : FALSE) : TRUE,
                    'serial_number' => isset($row->serial_number) ? $row->serial_number : NULL,
                    'used_phone_vals' => NULL,
                    'serial_search' => (isset($row->serial_number) || in_array($row->type, array('repair', 'other', 'accessory', 'new_phone'))) ? TRUE : FALSE,
                    'phone_number' => NULL,
                    'set_reminder' => NULL,
                    'activation_items' => ((isset($row->s_activation_plan) && $row->s_activation_plan) && ($row->type == 'new_phone' OR $row->type == 'used_phone')) ? $this->settings_model->getSAPItemsByID($row->s_activation_plan) : NULL,
                    'discount_code_used' => NULL,
                    'activation_spiff' => 0,
                    'purchase_type' => false,
                );
                $r++;
            }
            $this->repairer->send_json($pr);
        } else {
            $this->repairer->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }
    public function getProductDataByTypeAndID(){
        $this->load->library('repairer');
        $term = $this->input->get('code');
        $type = $this->input->get('type');

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
        if ($type == 'plan' || $type == 'plans') {
            $rows = $this->pos_model->getAllPlansByID($term);
        }
        if ($type == 'repair') {
            $rows = $this->pos_model->getProductNamesByID($term);
        }
        if ($type == 'crepair') {
            $rows = $this->pos_model->getCheckedInByID($term);
        }
        if ($type == 'drepair') {
            $rows = $this->pos_model->getDeliverablesByID($term);
        }
         if ($type == 'cp') {
            $rows = $this->pos_model->getCustomerPurchasesByID($term);
        }
        $rows = array_filter((array)$rows);
        if ($rows) {
            $c = str_replace(".", "", microtime(true));
            $r = 0;
            foreach ($rows as $row) {
                if ($row->taxable) {
                    if ($row->type == 'repair') {
                        $row->tax_rates = $this->activeStoreData->repair_items_tax; 
                    }elseif ($row->type == 'accessory') {
                        $row->tax_rates = $this->activeStoreData->accessories_tax; 
                    }elseif ($row->type == 'other') {
                        $row->tax_rates = $this->activeStoreData->other_items_tax; 
                    }elseif ($row->type == 'new_phone') {
                        $row->tax_rates = $this->activeStoreData->new_phone_tax; 
                    }elseif ($row->type == 'used_phone') {
                        $row->tax_rates = $this->activeStoreData->used_phone_tax; 
                    }elseif ($row->type == 'plans') {
                        $row->tax_rates = $this->activeStoreData->plans_tax; 
                    }else{
                       $row->taxable = 0; 
                    }
                }
                if ($row->taxable) {
                    $tax_rates = explode(',', $row->tax_rates);
                    $o_taxes = array();
                    foreach ($tax_rates as $taxrate) {
                        $o_taxes[] = $this->db->get_where('tax_rates', array('id' => (int)$taxrate))->row();
                    }
                }else{
                    $o_taxes = NULL;
                }
                if ($row->type == 'crepairs' or $row->type == 'drepairs') {
                    $item_id = $row->type.$row->id;
                    $row_id = $row->type.$row->id;
                }else{
                    $item_id = $row->type.($c + $r);
                    $row_id = $row->type.time();
                }
                $pr[] = array(
                    'row_id' => $row_id,
                    'item_id' => $item_id, 
                    'label' => $row->name . " (" . $row->code . ")", 
                    'code' => $row->code, 
                    'name' => $row->name, 
                    'price' => $row->price, 
                    'qty' => $row->qty, 
                    'type' => $row->type, 
                    'cost'=>$row->cost, 
                    'stock_id'=>$row->stock_id, 
                    'product_id'=>$row->id,
                    'taxable'=>$row->taxable,
                    'pr_tax' => $o_taxes,
                    'variants' => $row->variants ? TRUE : FALSE,
                    'option_selected' => FALSE,
                    'options'   => $row->variants ? $row->variants : FALSE,
                    'option'    => NULL,
                    'item_details' => '',
                    'row' => $row,
                    'discount' => 0,
                    'is_serialized' => (int)$row->is_serialized,
                    'serialed' => (int)$row->is_serialized ? FALSE : TRUE,
                    'serial_number' => NULL,
                    'serial_search' => (isset($row->serial_number) || in_array($row->type, array('repair', 'other', 'accessory', 'new_phone'))) ? TRUE : FALSE,
                    'phone_number' => NULL,
                    'set_reminder' => NULL,
                    'activation_items' => ((isset($row->s_activation_plan) && $row->s_activation_plan) && ($row->type == 'new_phone' OR $row->type == 'used_phone')) ? $this->settings_model->getSAPItemsByID($row->s_activation_plan) : NULL,
                    'discount_code_used' => NULL,
                    'activation_spiff' => 0,
                    'purchase_type' => false,

                );
                $r++;
            }
            $this->repairer->send_json($pr);

        } else {
            $this->repairer->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    // SHOW A INVOICE TEMPLATE //
    public function view($sale_id)
    {
        if (!$sale_id) {
            redirect('panel/pos');
        }
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['message'] = $this->session->flashdata('message');

        $this->data['rows'] = $this->pos_model->getAllInvoiceItems($sale_id, 1);

        $inv = $this->pos_model->getInvoiceByID($sale_id);
        $biller_id = $inv->biller_id;
        $customer_id = $inv->customer_id;

        $this->data['biller'] = $this->pos_model->getBillerByID($biller_id);
        $customer = $this->pos_model->getCustomerByID($customer_id);
        $this->data['customer']  = $customer;
        $this->data['payments'] = $this->pos_model->getInvoicePayments($sale_id);
        $this->data['barcode'] = $this->barcode($inv->reference_no, 'code128', 30);
        $this->data['inv'] = $inv;
        $this->data['sid'] = $sale_id;
        $this->data['logo'] = $this->mSettings->logo;
        $this->data['created_by'] = $this->pos_model->getBillerByID($inv->created_by);
        $this->data['pdf'] = true;
        $this->data['settings'] = $this->mSettings;
        $this->data['pos'] = $this->pos_model->getSetting();
        $this->data['page_title'] = $this->lang->line("invoice");
        $html = $this->load->view($this->theme.'pos/view', $this->data);
    }

    public function view_pdf($sale_id, $save_bufffer = false)
    {
        $this->load->model('repair_model');
        
        if (!$sale_id) {
            redirect('panel/pos');
        }
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['message'] = $this->session->flashdata('message');

        $this->data['rows'] = $this->pos_model->getAllInvoiceItems($sale_id, 1);


        $inv = $this->pos_model->getInvoiceByID($sale_id);
        $biller_id = $inv->biller_id;
        $customer_id = $inv->customer_id;

        $this->data['biller'] = $this->pos_model->getBillerByID($biller_id);
        $customer = $this->pos_model->getCustomerByID($customer_id);
        $this->data['customer']  = $customer;
        $this->data['payments'] = $this->pos_model->getInvoicePayments($sale_id);
        $this->data['barcode'] = $this->barcode($inv->reference_no, 'code128', 30);
        $this->data['inv'] = $inv;
        $this->data['sid'] = $sale_id;
        $this->data['logo'] = $this->mSettings->logo;
        $this->data['created_by'] = $this->pos_model->getBillerByID($inv->created_by);
        $this->data['pdf'] = true;
        $this->data['settings'] = $this->mSettings;
        $this->data['pos'] = $this->pos_model->getSetting();
        $this->data['page_title'] = $this->lang->line("invoice");
        // $html = $this->load->view($this->theme.'pos/a4', $this->data);

        

        $name = lang("sale") . "_" . str_replace('/', '_', $inv->reference_no) . ".pdf";
        $html = $this->load->view($this->theme.'pos/pdf/index', $this->data, true);
        $footer = $this->load->view($this->theme.'pos/pdf/footer', $this->data, true);


        if ($save_bufffer) {
            return $this->repairer->generate_pdf($html, lang('invoice').'.pdf', 'S', $footer, 10, null, 5);
        }
        $this->repairer->generate_pdf($html, $name, 'I', $footer, 10, null, 5);

    }
    public function register_details()
    {
        $this->repairer->checkPermissions('index');
        $this->load->library('repairer');

        $register_open_time = $this->session->userdata('register_open_time');
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['ccsales'] = $this->pos_model->getRegisterCCSales($register_open_time);
        $this->data['cashsales'] = $this->pos_model->getRegisterCashSales($register_open_time);
        $this->data['chsales'] = $this->pos_model->getRegisterChSales($register_open_time);
        $this->data['pppsales'] = $this->pos_model->getRegisterPPPSales($register_open_time);
        $this->data['othersales'] = $this->pos_model->getRegisterOtherSales($register_open_time);
        $this->data['totalsales'] = $this->pos_model->getRegisterSales($register_open_time);
        $this->data['tosafetranfers'] = $this->pos_model->getSafeTotals($register_open_time);
        $this->data['todrawertranfers'] = $this->pos_model->getDrawerTotal($register_open_time);
        $this->data['refunds'] = $this->pos_model->getRegisterRefunds($register_open_time);
        
        $this->load->view($this->theme.'pos/register_details', $this->data);
    }




    public function close_register($user_id = NULL)
    {
        $this->repairer->checkPermissions('index');

        $this->load->library('repairer');

        $user_id = $this->session->userdata('user_id');
        $this->form_validation->set_rules('total_cash', lang('total_cash'), 'trim|required|numeric');
        if ($this->form_validation->run() == TRUE) {
            
            $rid = $this->session->userdata('register_id');
            $user_id = $this->session->userdata('user_id');

            $cash_data = [];
            $currency_sets = $this->repairer->returnOpenRegisterSets();
            foreach($currency_sets as $input => $name){
                $cash_data['n'.$name]=$this->input->post('n'.$name);
            }
            $cash_data = json_encode($cash_data);

           
            if ($this->input->post('pin_close')) {
                $created_by = $this->db->get_where('users', array('pin_code'=>$this->input->post('pin_close')))->row()->id;
            }else{
                $created_by = $this->session->userdata('user_id');
            }
            $data = array(
                'closed_at'                => date('Y-m-d H:i:s'),
                'total_cash_submitted'     => $this->input->post('total_cash_submitted'),
                'total_cheques_submitted'  => $this->input->post('total_cheques_submitted'),
                'total_cc_submitted'       => $this->input->post('total_cc_submitted'),
                'total_ppp_submitted'      => $this->input->post('total_ppp_submitted'),
                'total_others_submitted'   => $this->input->post('total_others_submitted'),
                'total_cash'               => $this->input->post('total_cash'),
                'total_cheques'            => $this->input->post('total_cheques'),
                'total_cc'                 => $this->input->post('total_cc'),
                'total_others'             => $this->input->post('total_others'),
                'total_ppp'                => $this->input->post('total_ppp'),
                'total_cash_qty'           => $this->input->post('total_cash_qty'),
                'total_cheques_qty'        => $this->input->post('total_cheques_qty'),
                'total_cc_qty'             => $this->input->post('total_cc_qty'),
                'total_others_qty'         => $this->input->post('total_others_qty'),
                'total_ppp_qty'            => $this->input->post('total_ppp_qty'),
                'total_cash_submitted_data'=> $cash_data,
                'note'                     => $this->input->post('note'),
                'tosafetranfers'           => $this->input->post('tosafetranfers'),
                'todrawertranfers'         => $this->input->post('todrawertranfers'),
                'count_note'               => $this->input->post('count_note'),
                'status'                   => 'close',
                'closed_by'                => $created_by,
            );
        } elseif ($this->input->post('close_register')) {
            $this->session->set_flashdata('error', (validation_errors() ? validation_errors() : $this->session->flashdata('error')));
            redirect("pos");
        }

        if ($this->form_validation->run() == TRUE && $this->pos_model->closeRegister($rid, $user_id, $data)) {
            $this->session->set_flashdata('message', lang('register_closed'));
            redirect("panel");
        } else {
            $register_open_time = $this->session->userdata('register_open_time');
            $this->data['cash_in_hand'] = NULL;
            $this->data['register_open_time'] = NULL;
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['ccsales'] = $this->pos_model->getRegisterCCSales($register_open_time, $user_id);
            $this->data['cashsales'] = $this->pos_model->getRegisterCashSales($register_open_time, $user_id);
            $this->data['chsales'] = $this->pos_model->getRegisterChSales($register_open_time, $user_id);
            $this->data['pppsales'] = $this->pos_model->getRegisterPPPSales($register_open_time, $user_id);
            $this->data['othersales'] = $this->pos_model->getRegisterOtherSales($register_open_time);
            $this->data['totalsales'] = $this->pos_model->getRegisterSales($register_open_time, $user_id);
            $this->data['tosafetranfers'] = $this->pos_model->getSafeTotals($register_open_time, $user_id);
            $this->data['todrawertranfers'] = $this->pos_model->getDrawerTotal($register_open_time, $user_id);
            $this->data['refunds'] = $this->pos_model->getRegisterRefunds($register_open_time);
            $this->data['user_id'] = $user_id;
            $this->render('pos/close');
        }
    }

    public function verifyPin()
    {
        $pin_code = $this->input->post('pin_code');
        $q = $this->db->get_where('users', array('pin_code'=>$pin_code));
        if ($q->num_rows() > 0) {
            echo 'true';
        }else{
            echo 'false';
        }
    }
    
    public function addToSafe(){
        
        if ($this->input->post('type') == 'safe') {
            $message = lang('Money successfully deposited to the safe');
            $amount = $this->input->post('amount');
        }elseif($this->input->post('type') == 'drawer'){
            $message = lang('Money successfully added to the drawer');
            $amount = 0-$this->input->post('amount');
        }else{
            $this->session->set_flashdata('warning', lang('Something has gone wrong. Please try again.'));
            redirect('panel/pos');
        }

        $data = array(
            'amount'    => $amount,
            'date'      => date('Y-m-d H:i:s'),
            'created_by' => $this->session->userdata('user_id'),
            'store_id' => $this->activeStore,
        );
        $this->db->insert('pos_safe_transfers', $data);

        $this->session->set_flashdata('message', $message);
        redirect('panel/pos');
    }
    public function moveTo($x){
        if ($x === 'safe' or $x === 'drawer') {
            $register_open_time = $this->session->userdata('register_open_time');
            
            $cashsales = $this->pos_model->getRegisterCashSales($register_open_time);
            $tosafetranfers = $this->pos_model->getSafeTotals($register_open_time);
            $todrawertranfers = $this->pos_model->getDrawerTotal($register_open_time);
            $total_cash = $cashsales->paid ? ((($cashsales->paid + ($this->session->userdata('cash_in_hand'))) + $todrawertranfers->total) - $tosafetranfers->total) : (((($this->session->userdata('cash_in_hand'))) + $todrawertranfers->total) - $tosafetranfers->total);
            $this->data['max_drawer_lock'] = $total_cash > $this->mSettings->max_drawer_amount ? TRUE : FALSE;
            $this->data['tcash'] = $total_cash;
            $this->data['type'] = $x;
            $this->load->view($this->theme.'pos/moveto', $this->data);
        }
    }
    public function getProductSerials(){
        $this->load->library('repairer');
        $type = $this->input->post('type');
        $id = $this->input->post('id');
        $term = $this->input->post('term');
        if ($type == 'new_phone') {
            $type = 'phones';
        }
        $results = $this->pos_model->getSerials($type, $id, $term);
        if ($results) {
            foreach ($results as $row) {
                $pr[] = $row->serial_number;
            }
            $this->repairer->send_json($pr);
        }else{
            $this->repairer->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }
    public function verifyProductSerial(){
        $this->load->library('repairer');
        $term = $this->input->post('term');
        $type = $this->input->post('type');
        $id = $this->input->post('id');
        if ($type == 'new_phone') {
            $type = 'phones';
        }
        $results = $this->pos_model->getSerials($type, $id, $term, TRUE);
        if ($results) {
            $this->repairer->send_json(true);
        }else{
            $this->repairer->send_json(false);
        }
    }
    
    public function pdf($id = null, $view = null, $save_bufffer = null)
    {
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->pos_model->getInvoiceByID($id);
        
        $this->data['settings'] = ($this->mSettings);
        $this->data['customer'] = ($inv->customer);
        $this->data['biller'] = ($inv->biller);
        $this->data['created_by'] = ($inv->created_by);
        $this->data['inv'] = $inv;
        $this->data['rows'] = $this->pos_model->getAllInvoiceItems($id);
        $this->data['return_sale'] = $inv->return_id ? $this->pos_model->getInvoiceByID($inv->return_id) : NULL;
        $this->data['return_rows'] = $inv->return_id ? $this->pos_model->getAllInvoiceItems($inv->return_id) : NULL;
        $this->data['client'] = $this->pos_model->getClientByID((int)$inv->customer_id);
        $this->load->view($this->theme.'sales/pdf', $this->data);
        $name = lang("sale") . "_" . str_replace('/', '_', $inv->reference_no) . ".pdf";
        $html = $this->load->view($this->theme.'sales/pdf', $this->data, true);
        if ($view) {
            $this->load->view($this->theme.'sales/pdf', $this->data);
        } elseif ($save_bufffer) {
            return $this->repairer->generate_pdf($html, $name, $save_bufffer, $this->data['biller']);
        } else {
            $this->repairer->generate_pdf($html, $name, false, $this->data['biller']);
        }
    }
    public function email($id = null, $view = null, $save_bufffer = null)
    {
        if ($this->input->get('id')) {
            $id = $this->input->get('id');

        }
        if ($this->input->post('id')) {
            $sale_id = $this->input->post('id');
        } 

     

        if (!$sale_id) {
            die('No sale selected.');
        }
        if ($this->input->post('email')) {
            $to = $this->input->post('email');
        }
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['message'] = $this->session->flashdata('message');

        $this->data['rows'] = $this->pos_model->getAllInvoiceItems($sale_id);
        $inv = $this->pos_model->getInvoiceByID($sale_id);
        $biller_id = $inv->biller_id;
        $customer_id = $inv->customer_id;
        $this->data['biller'] = $inv->biller;
        $this->data['settings'] = $this->mSettings;
        $this->data['customer'] = $this->pos_model->getClientByID((int)$customer_id);

        $this->data['payments'] = $this->pos_model->getInvoicePayments($sale_id);
        $this->data['barcode'] = $this->barcode($inv->reference_no, 'code128', 30);
        $this->data['inv'] = $inv;
        $this->data['sid'] = $sale_id;
        $this->data['page_title'] = $this->lang->line("invoice");

        if (!$to) {
            $to = $this->data['customer']->email;
        }
        if (!$to) {
            $this->repairer->send_json(array('msg' => $this->lang->line("no_meil_provided")));
        }
        // $receipt = $this->load->view($this->theme.'pos/email_receipt', $this->data);
        $receipt = $this->load->view($this->theme.'pos/email_receipt', $this->data, TRUE);
        if ($this->repairer->send_email($to, 'Receipt from ' . $this->data['biller'], $receipt)) {
            $this->repairer->send_json(array('msg' => lang('Email Sent')));
        } else {
            $this->repairer->send_json(array('msg' => lang('Failed')));
        }

    }

    public function barcode($text = NULL, $bcs = 'code128', $height = 50)
    {
        return base_url('panel/inventory/gen_barcode/' . $text . '/' . $bcs . '/' . $height);
    }

    public function admin_email($id = null) {
        $sale_id = $id;
        if (!$sale_id) {
            die('No sale selected.');
        }
        $this->data['rows'] = $this->pos_model->getAllInvoiceItems($sale_id);
        $inv = $this->pos_model->getInvoiceByID($sale_id);

        $biller_id = $inv->biller_id;
        $customer_id = $inv->customer_id;
        $this->data['biller'] = $inv->biller;
        $this->data['settings'] = $this->mSettings;
        $this->data['customer'] = $this->pos_model->getClientByID((int)$customer_id);

        $this->data['payments'] = $this->pos_model->getInvoicePayments($sale_id);
        $this->data['barcode'] = $this->barcode($inv->reference_no, 'code128', 30);
        $this->data['inv'] = $inv;
        $this->data['sid'] = $sale_id;
        $this->data['page_title'] = $this->lang->line("invoice");
        
        if($inv->sale_status == 'completed'){
            $to = $this->settings_model->getUsersByID($this->mSettings->notify_sales);
        }elseif($inv->sale_status == 'returned'){
            $to = $this->settings_model->getUsersByID($this->mSettings->notify_repair);
        }else{
            return FALSE;
        }
        if (!$to) {
            return FALSE;
        }
        
        $receipt = $this->load->view($this->theme.'email_templates/notify_sale', $this->data, TRUE);

        if ($this->repairer->send_email($to, sprintf(lang('notification_sale_by'), $this->data['biller']), $receipt)) {
            return TRUE;
        }
        return FALSE;
    }

    public function verifyDiscountCode() {
        $code = $this->input->post('code');
        $type = $this->input->post('type');
        $id   = $this->input->post('id');

        $q = $this->db
            ->where('used_on IS NULL', NULL, FALSE)
            ->where('code', $code)
            ->get('discount_codes');
        if ($q->num_rows() > 0) {
            $data = $q->row();
            if ($data->type == 'master') {
                $this->repairer->send_json(array('success'=>true, 'data'=>$q->row()));
            }elseif($data->type == 'category'){
                if ($data->used_for && $data->used_for == $type) {
                    $this->repairer->send_json(array('success'=>true, 'data'=>$q->row()));
                }else{
                    $this->repairer->send_json(array('success'=>false, 'message'=> 'You cannot use the code on this category. this code is only viable for "'. humanize($data->used_for).'" category.'));
                }
            }elseif($data->type == 'product'){
                if ($data->used_for && $data->used_for == $type && $data->used_for_id && $data->used_for_id == $id) {
                    $this->repairer->send_json(array('success'=>true, 'data'=>$q->row()));
                }else{
                    $this->repairer->send_json(array('success'=>false, 'message'=> lang('You cannot use the code on this product.')));
                }
            }else{
                $this->repairer->send_json(array('success'=>false, 'message'=> lang('An Error Occured. Please contact system admin.')));
            }
        }
        $this->repairer->send_json(array('success'=>false, 'message'=> lang('Error Ocurred.')));
    }



    public function email_receipt($sale_id = NULL, $view = null)
    {
        if ($this->input->post('id')) {
            $sale_id = $this->input->post('id');
        }
        if ( ! $sale_id) {
            die('No sale selected.');
        }
        $to = '';
        if ($this->input->post('email')) {
            $to = $this->input->post('email');
        }

        $inv = $this->pos_model->getInvoiceByID($sale_id);
        $biller_id = $inv->biller_id;
        $customer_id = $inv->customer_id;
        $customer = $this->pos_model->getCustomerByID($customer_id);
        $pdf = $this->view_pdf($sale_id, true);

        $this->load->library('parser');
        $message = $this->mSettings->sale_email_text;

        $parse_data = array(
            'stylesheet' => '<link rel="stylesheet" href="'.$this->assets.'assets/vendor/bootstrap/css/bootstrap.css" />',
            'name' => $customer ? ($customer->company && $customer->company != '-' ? $customer->company :  $customer->first_name) : lang('walk_in'),
            'email' => $to,
            'heading' => lang('invoice').'<hr>',
            'msg' => $message,
            'site_link' => base_url(),
            'site_name' => $this->mSettings->title,
            'logo' => '<img src="' . base_url('assets/uploads/logos/' . $this->mSettings->logo) . '" alt="' . $this->mSettings->title . '"/>',
            'email_footer' => '',
        );

        $msg = file_get_contents(FCPATH.'themes/'.$this->theme.'email_templates/email_con.html');
        $receipt = $this->parser->parse_string($msg, $parse_data, TRUE);

        if ($view) {
            echo $receipt;
            die();
        }

        if (!$to) {
            $this->repairer->send_json(array('msg' => $this->lang->line("no_meil_provided")));
        }

        try {
            if ($this->repairer->send_email($to, lang('sale_reciept'), $receipt, null, null, $pdf)) {
                $this->repairer->send_json(array('msg' => lang('email_sent')));
            } else {
                $this->repairer->send_json(array('msg' => $this->lang->line("email_failed")));
            }
        } catch (Exception $e) {
            $this->repairer->send_json(array('msg' => $e->getMessage()));
        }

    }
}