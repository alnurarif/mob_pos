<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Setting
 *
 *
 * @package		Repair
 * @category	Controller
 * @author		Usman Sher
*/

class Settings extends Auth_Controller
{
	// THE CONSTRUCTOR //
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Settings_model');
        $this->digital_upload_path = 'assets/uploads/csv/';
        // $this->lang->load('global', $this->Main_model->language());
    }

	// SHOW THE SETTINGS PAGE //
    public function index()
    {
        $q = $this->db->query("SHOW TABLE STATUS LIKE 'sales'");

        $this->data['auto_increment_value'] = $q->row()->Auto_increment;
        $this->data['tax_rates'] = $this->settings_model->getTaxRates();
        $this->data['date_formats'] = $this->settings_model->getDateFormats();
            $this->data['bank_accounts'] = $this->settings_model->getAllBankAccountsDP();

        $this->render('settings');
    }


    // AJAX LOGO UPLOAD //
    public function upload_image()
    {
        $status = "";
        $msg = "";
        $this->load->library('upload');
        $this->upload_path = 'assets/uploads/logos';
        $this->upload_path_favicon = 'assets/uploads/logos/favicons';
        $this->image_types = 'jpg|jpeg|png|gif';
        $this->allowed_file_size = 190 * 53;

        $config['upload_path'] = $this->upload_path;
        $config['allowed_types'] = $this->image_types;
        $config['max_size'] = $this->allowed_file_size;
        $config['overwrite'] = FALSE;
        $config['max_filename'] = 25;
        $config['encrypt_name'] = TRUE;
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('logo_upload')) {
            $error = $this->upload->display_errors();
            $status = 'error';
            echo $msg = $this->upload->display_errors('', '');
            echo 'false1';
        }else{
            $data = $this->upload->data();
            if($data)
            {
                // if ($data['image_height'] > 90) {
                //     $config['image_library']    = 'gd2';
                //     $config['source_image']     = $data['full_path'];
                //     $config['create_thumb']     = FALSE;
                //     $config['maintain_ratio']   = TRUE;
                //     $config['width']            = 90;
                //     $this->load->library('image_lib', $config);
                //     $this->image_lib->resize();
                // }
                $name = $this->upload->file_name;
                $this->settings_model->update_logo($name);
                echo $name;
                
            }
            else
            {
                unlink($data['full_path']);
                echo 'false';
            }

        }

    }


	// SAVE THE SETTING //
    public function save_settings()
    {


        if ($this->Admin || $this->GP['settings-order_repairs_edit']) {
            // START order_repairs
            $categories = implode(',', $this->input->post('category'));
            $custom_fields = implode(',', $this->input->post('custom_fields'));
            $repair_custom_checkbox = implode(',', $this->input->post('repair_custom_checkbox'));
            $repair_custom_toggles = implode(',', $this->input->post('repair_custom_toggles'));
            // END order_repairs
        }

        $data = array();
        if ($this->Admin || $this->GP['settings-general_settings_edit']) {
            // START GENERAL
            $data['title']              = $this->input->post('title');
            $data['language']           = $this->input->post('language');
            $data['currency']           = $this->input->post('currency');
            $data['reference_format']   = $this->input->post('reference_format');
            $data['random_admin']       = $this->input->post('random_admin');
            $data['disable_labor']      = $this->input->post('disable_labor');
            $data['google_api_key']     = $this->input->post('google_api_key');
            $data['dateformat']     = $this->input->post('dateformat');
            

            $data['sales_prefix']    = $this->input->post('sales_prefix');
            $data['payment_prefix']    = $this->input->post('payment_prefix');
            $data['return_prefix']    = $this->input->post('return_prefix');
            $data['purchase_prefix']    = $this->input->post('purchase_prefix');
            $data['repair_prefix']    = $this->input->post('repair_prefix');



            $data['thousands_sep']     = $this->input->post('thousands_sep');
            $data['decimals_sep']     = $this->input->post('decimals_sep');
            
            $data['use_defects_input_dropdown']     = $this->input->post('use_defects_input_dropdown');
            $data['use_models_input_dropdown']     = $this->input->post('use_models_input_dropdown');
            $data['use_rtl']     = $this->input->post('use_rtl');
            $data['display_symbol']     = $this->input->post('display_symbol');

            $data['invoice_template'] = $this->input->post('invoice_template');
            $data['report_template'] = $this->input->post('report_template');

            $data['require_clockin'] = $this->input->post('require_clockin');
            $data['auto_clockout'] = $this->input->post('auto_clockout');
            
            $data['universal_clients']  = $this->input->post('universal_clients');
            if ($this->input->post('universal_clients')) {
                $this->db->update('clients', array('universal'=>1));
            }
            $data['universal_accessories']  = $this->input->post('universal_accessories');
            if ($this->input->post('universal_accessories')) {
                $this->db->update('accessory', array('universal'=>1));
            }
            $data['universal_plans']  = $this->input->post('universal_plans');
            if ($this->input->post('universal_plans')) {
                $this->db->update('plans', array('universal'=>1));
            }
            $data['universal_others']  = $this->input->post('universal_others');
            if ($this->input->post('universal_others')) {
                $this->db->update('other', array('universal'=>1));
            }
            $data['universal_manufacturers']  = $this->input->post('universal_manufacturers');
            if ($this->input->post('universal_manufacturers')) {
                $this->db->update('manufacturers', array('universal'=>1));
            }
            $data['universal_carriers']  = $this->input->post('universal_carriers');
            if ($this->input->post('universal_carriers')) {
                $this->db->update('carriers', array('universal'=>1));
            }
            $data['universal_suppliers']  = $this->input->post('universal_suppliers');
            if ($this->input->post('universal_suppliers')) {
                $this->db->update('suppliers', array('universal'=>1));
            }
            // END GENERAL
        }


        // START order_repairs
        if ($this->Admin || $this->GP['settings-order_repairs_edit']) {
            $data['category']               = $categories;
            $data['custom_fields']          = $custom_fields;
            $data['repair_custom_checkbox'] = $repair_custom_checkbox;
            $data['repair_custom_toggles'] = $repair_custom_toggles;
        }
        // END order_repairs

        // START settings-quote
        if ($this->Admin || $this->GP['settings-quote_edit']) {

            $data['invoice_name'] = $this->input->post('invoice_name');
            $data['invoice_mail'] = $this->input->post('invoice_mail');
            $data['address'] = $this->input->post('invoice_address');
            $data['phone'] = $this->input->post('invoice_phone');
            $data['vat'] = $this->input->post('invoice_vat');
            $data['disclaimer'] = $this->input->post('disclaimer');
            $data['city'] = $this->input->post('city');
            $data['state'] = $this->input->post('state');
            $data['zipcode'] = $this->input->post('zip');
        }
        // END settings-quote

        // START SMS
        if ($this->Admin || $this->GP['settings-sms_edit']) {
            $data['usesms'] = $this->input->post('usesms');
            $data['nexmo_api_key'] = $this->input->post('n_api_key');
            $data['nexmo_api_secret'] = $this->input->post('n_api_secret');
            $data['twilio_mode'] = $this->input->post('t_mode');
            $data['twilio_account_sid'] = $this->input->post('t_account_sid');
            $data['twilio_auth_token'] = $this->input->post('t_token');
            $data['twilio_number'] = $this->input->post('t_number');
            $data['smtp_host'] = $this->input->post('smtp_host');
            $data['smtp_user'] = $this->input->post('smtp_user');
            $data['smtp_pass'] = $this->input->post('smtp_pass');
            $data['smtp_port'] = $this->input->post('smtp_port');
            $data['smtp_crypto'] = $this->input->post('smtp_crypto');
                
            $data['smsgateway_token'] = $this->input->post('smsgateway_token');
            $data['smsgateway_device_id'] = $this->input->post('smsgateway_device_id'); 
            $data['default_http_api'] = $this->input->post('default_http_api'); 
            
            $this->load->helper('file');
            $customer_purchase = $this->input->post('customer_purchase');
            write_file(FCPATH.'themes/'.$this->theme.'/email_templates/customer_purchase.html', $customer_purchase);
        }


        // END SMS

        if ($this->Admin || $this->GP['settings-pos_configuration_edit']) {
            $data['drawer_amount'] = $this->input->post('drawer_amount');
            $data['max_drawer_amount'] = $this->input->post('max_drawer_amount');
            $data['auto_close_drawer'] = $this->input->post('auto_close_drawer');
            $data['sell_repair_parts'] = $this->input->post('sell_repair_parts');
            $data['accept_cash'] = $this->input->post('accept_cash');
            $data['accept_cheque'] = $this->input->post('accept_cheque');
            $data['accept_cc'] = $this->input->post('accept_cc');
            $data['accept_paypal'] = $this->input->post('accept_paypal');
            $data['disclaimer_sale'] = $this->input->post('disclaimer_sale');
            
            $data['accept_authorize'] = $this->input->post('accept_authorize');
            $data['authorize_login_id'] = $this->input->post('authorize_login_id');
            $data['authorize_transaction_id'] = $this->input->post('authorize_transaction_id');
            $data['authorize_client_key'] = $this->input->post('authorize_client_key');
        }

            $data['after_sale_page'] = $this->input->post('after_sale_page');
            $data['pos_bank_id'] = $this->input->post('pos_bank_id');
            $data['purchase_bank_id'] = $this->input->post('purchase_bank_id');

        $data['due_bill_notify_before'] = $this->input->post('due_bill_notify_before');
        $data['rows_per_page'] = $this->input->post('rows_per_page');
        $data['due_bill_notify_when'] = $this->input->post('due_bill_notify_when');
        $data['due_bill_message'] = trim($this->input->post('due_bill_message'));
        
        $data['notify_sales'] = $this->input->post('notify_sales') ? implode(',', $this->input->post('notify_sales')) : '';
        $data['notify_refund'] = $this->input->post('notify_refund') ? implode(',', $this->input->post('notify_refund')) : '';
        $data['notify_repair'] = $this->input->post('notify_repair') ? implode(',', $this->input->post('notify_repair')) : '';
        $data['notify_porder'] = $this->input->post('notify_porder') ? implode(',', $this->input->post('notify_porder')) : '';
        $data['notify_preceive'] = $this->input->post('notify_preceive') ? implode(',', $this->input->post('notify_preceive')) : '';
        $data['notify_cpurchase'] = $this->input->post('notify_cpurchase') ? implode(',', $this->input->post('notify_cpurchase')) : '';
        $sale_start_number= (int)$this->input->post('sale_start_number');
        $auto_increment_value = (int)$this->db->query("SHOW TABLE STATUS LIKE 'sales'")->row()->Auto_increment;
        if ($sale_start_number > $auto_increment_value) {
            $q = $this->db->query("ALTER TABLE sales AUTO_INCREMENT = ".$sale_start_number);
        }
		$data = $this->Settings_model->update_settings($data);


        if ($this->input->post('use_defects_input_dropdown') == 1) {
            $this->repairer->updateDefectsTable();
        }
        // if ($this->input->post('use_models_input_dropdown') == 1) {
        //     $this->repairer->updateModelsTable();
        // }
        echo json_encode($data);
    }

    // SHOW THE SETTINGS PAGE //
    public function tax_rates($action = NULL)
    {
        if (!$action or $action == 'index') {
            $this->repairer->checkPermissions();
            $this->render('tax_rates');
        }
        if ($action == 'getAll') {
            $this->load->library('datatables');

            if ($this->uri->segment(5)) {
                $a = $this->uri->segment(5);
                if ($a == 'disabled') {
                    $this->datatables->where('disable', 1);
                }elseif($a == 'enabled'){
                    $this->datatables->where('disable', 0);
                }
            }
            $this->datatables
                ->select('id, name, code, rate, type, disable')
                ->from('tax_rates');
            $this->datatables->add_column('actions', "$1___$2", 'id, disable');
            $this->datatables->unset_column('id');
            $this->datatables->unset_column('disable');
            echo $this->datatables->generate();
        }elseif ($action == 'toggle') {
            $toggle = $this->input->post('toggle');
            if ($toggle == 'enable') {
                $data = array(
                    'disable' => 0,
                );
                $a = lang('enabled');
            }else{
                $data = array(
                    'disable' => 1,
                );
                $a = lang('disabled');
            }
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('tax_rates', $data);
            echo json_encode(array('ret' => 'true', 'toggle' => $a));
        }elseif ($action == 'byID') {
            $data = array();
            $query = $this->db->get_where('tax_rates', array('id' => $this->input->post('id')));
            if ($query->num_rows() > 0) {
                $data = $query->row_array();
            }
            echo  json_encode($data);
        }elseif ($action == 'add') {
            $this->repairer->checkPermissions('add', FALSE,'tax_rates');

            $data = array(
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
                'rate' => $this->input->post('rate'),
                'type' => $this->input->post('type'),
            );
            $this->db->insert('tax_rates', $data);
            echo $this->db->insert_id();
        }elseif ($action == 'edit') {
            $this->repairer->checkPermissions('edit', FALSE,'tax_rates');
            $data = array(
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
                'rate' => $this->input->post('rate'),
                'type' => $this->input->post('type'),
            );
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('tax_rates', $data);
        }
    }

    // SHOW THE SETTINGS PAGE //
    public function carriers($action = NULL)
    {
        if (!$action or $action == 'index') {
            $this->repairer->checkPermissions();
            $this->render('carriers');
        }
        if ($action == 'getAll') {
            $this->load->library('datatables');

            if ($this->uri->segment(5)) {
                $a = $this->uri->segment(5);
                if ($a == 'disabled') {
                    $this->datatables->where('disable', 1);
                }elseif($a == 'enabled'){
                    $this->datatables->where('disable', 0);
                }
            }
            $this->datatables->where('(universal=1 OR store_id='.$this->activeStore.')' , NULL, FALSE);

            $this->datatables
                ->select('id, name, disable')
                ->from('carriers');
            $this->datatables->add_column('actions', "$1___$2", 'id, disable');
            $this->datatables->unset_column('id');
            $this->datatables->unset_column('disable');
            echo $this->datatables->generate();
        }elseif ($action == 'toggle') {
            $toggle = $this->input->post('toggle');
            if ($toggle == 'enable') {
                $data = array(
                    'disable' => 0,
                );
                $a = lang('enabled');
            }else{
                $data = array(
                    'disable' => 1,
                );
                $a = lang('disabled');
            }
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('carriers', $data);
            echo json_encode(array('ret' => 'true', 'toggle' => $a));
        }elseif ($action == 'byID') {
            $data = array();
            $query = $this->db->get_where('carriers', array('id' => $this->input->post('id')));
            if ($query->num_rows() > 0) {
                $data = $query->row_array();
            }
            echo  json_encode($data);
        }elseif ($action == 'add') {
            $this->repairer->checkPermissions('add', FALSE, 'carriers');
            $data = array(
                'name' => $this->input->post('name'),
                'universal' => $this->input->post('universal') ? $this->input->post('universal') : $this->mSettings->universal_carriers,
                'store_id' => $this->activeStore,
            );
            $this->db->insert('carriers', $data);
            echo $this->db->insert_id();
        }elseif ($action == 'edit') {
            $this->repairer->checkPermissions('edit', FALSE, 'carriers');

            $data = array(
                'name' => $this->input->post('name'),
                'universal' => $this->input->post('universal') ? $this->input->post('universal') : $this->mSettings->universal_carriers,
            );
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('carriers', $data);
        }
    }
    // SHOW THE SETTINGS PAGE //
    public function manufacturers($action = NULL)
    {
        if (!$action or $action == 'index') {
            $this->repairer->checkPermissions();

            $this->render('manufacturers');
        }
        if ($action == 'getAll') {
            $this->load->library('datatables');
            if ($this->uri->segment(5)) {
                $a = $this->uri->segment(5);
                if ($a == 'disabled') {
                    $this->datatables->where('disable', 1);
                }elseif($a == 'enabled'){
                    $this->datatables->where('disable', 0);
                }
            }
            $this->datatables->where('(universal=1 OR store_id='.$this->activeStore.')' , NULL, FALSE);

            $this->datatables
                ->select('id, name, disable')
                ->where('parent_id', null)
                ->from('manufacturers');
            $this->datatables->add_column('actions', "$1___$2", 'id, disable');
            $this->datatables->unset_column('id');
            $this->datatables->unset_column('disable');
            echo $this->datatables->generate();
        }elseif ($action == 'toggle') {
            $toggle = $this->input->post('toggle');
            if ($toggle == 'enable') {
                $data = array(
                    'disable' => 0,
                );
                $a = lang('enabled');
            }else{
                $data = array(
                    'disable' => 1,
                );
                $a = lang('disabled');
            }
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('manufacturers', $data);
            echo json_encode(array('ret' => 'true', 'toggle' => $a));
        }elseif ($action == 'byID') {
            $data = array();
            $query = $this->db->get_where('manufacturers', array('id' => $this->input->post('id')));
            if ($query->num_rows() > 0) {
                $data = $query->row_array();
            }
            echo  json_encode($data);
        }elseif ($action == 'add') {
            $this->repairer->checkPermissions('add', FALSE, 'manufacturers');

            $data = array(
                'name' => $this->input->post('name'),
                'universal' => $this->input->post('universal') ? $this->input->post('universal') : $this->mSettings->universal_manufacturers,
                'store_id' => $this->activeStore,
            );
            $this->db->insert('manufacturers', $data);
            echo $this->db->insert_id();
        }elseif ($action == 'edit') {
            $this->repairer->checkPermissions('edit', FALSE, 'manufacturers');

            $data = array(
                'name' => $this->input->post('name'),
                'universal' => $this->input->post('universal') ? $this->input->post('universal') : $this->mSettings->universal_manufacturers,
            );
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('manufacturers', $data);
        }
    }


    // SHOW THE SETTINGS PAGE //
    public function models($action = NULL)
    {
        if (!$action or $action == 'index') {
            $this->repairer->checkPermissions();

            $this->render('models');
        } elseif ($action == 'getAjax') {
            $term = $this->input->get('q');
            if ($term) {
                $this->db->where("manufacturers.name LIKE '%" . $term . "%'");
            }

            $this->db->select('id, name');
            $this->db->where('disable', 0);
            $this->db->where('parent_id', $this->input->get('manufacturer'));
            $q = $this->db->get('manufacturers');

            $data = array();
            if ($q->num_rows() > 0) {
                foreach ($q->result() as $model) {
                    $data[] = array('id' => $model->id, 'text' => "$model->name ");
                }
            }

            echo $this->repairer->send_json($data);
        } elseif ($action == 'getAll') {
            $this->load->library('datatables');
            if ($this->uri->segment(5)) {
                $a = $this->uri->segment(5);
                if ($a == 'disabled') {
                    $this->datatables->where('disable', 1);
                }elseif($a == 'enabled'){
                    $this->datatables->where('disable', 0);
                }
            }
            $this->datatables->where('(universal=1 OR store_id='.$this->activeStore.')' , NULL, FALSE);

            $this->datatables
                ->select('id, name, disable')
                ->where('parent_id !=',  null)
                ->from('manufacturers');
            $this->datatables->add_column('actions', "$1___$2", 'id, disable');
            $this->datatables->unset_column('id');
            $this->datatables->unset_column('disable');
            echo $this->datatables->generate();
        }elseif ($action == 'toggle') {
            $toggle = $this->input->post('toggle');
            if ($toggle == 'enable') {
                $data = array(
                    'disable' => 0,
                );
                $a = lang('enabled');
            }else{
                $data = array(
                    'disable' => 1,
                );
                $a = lang('disabled');
            }
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('manufacturers', $data);
            echo json_encode(array('ret' => 'true', 'toggle' => $a));
        }elseif ($action == 'byID') {
            $data = array();
            $query = $this->db->get_where('manufacturers', array('id' => $this->input->post('id')));
            if ($query->num_rows() > 0) {
                $data = $query->row_array();
            }
            echo  json_encode($data);
        }elseif ($action == 'add') {
            $this->repairer->checkPermissions('add', FALSE, 'manufacturers');

            $data = array(
                'name' => $this->input->post('name'),
                'parent_id' => $this->input->post('parent_id') ?? null,
                'universal' => $this->input->post('universal') ? $this->input->post('universal') : $this->mSettings->universal_manufacturers,
                'store_id' => $this->activeStore,
            );
            $this->db->insert('manufacturers', $data);
            echo $this->db->insert_id();
        }elseif ($action == 'edit') {
            $this->repairer->checkPermissions('edit', FALSE, 'manufacturers');

            $data = array(
                'name' => $this->input->post('name'),
                'parent_id' => $this->input->post('parent_id') ?? null,
                'universal' => $this->input->post('universal') ? $this->input->post('universal') : $this->mSettings->universal_manufacturers,
            );
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('manufacturers', $data);
        }
    }
   

    // PRINT A SUPPLIERS PAGE //
    public function suppliers()
    {

        $this->render('inventory/suppliers_index');
    }

    // PRINT A Import Function PAGE //
    public function import($type = NULL)
    {
        $this->repairer->checkPermissions();

        $this->load->helper('security');
        $this->form_validation->set_rules('customers', lang("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (isset($_FILES["customers"])) {
                $this->load->library('upload');

                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv|text/csv';
                $config['max_size'] = 99999;
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload($type)) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect('panel/settings/import/'.$type);
                }


                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys = array('first_name', 'last_name', 'company', 'telephone', 'address', 'city', 'state', 'postal_code', 'email', 'vat', 'cf', 'comment', 'tax_exempt', 'universal');
                $final = array();
                if (count($arrResult) > 0 && count($keys) == count($arrResult[0])) {
                        foreach ($arrResult as $key => $value) {
                            $final[] = array_combine($keys, $value);
                        }
                } else {
                    $this->session->set_flashdata('error', lang('CSV Format Error'));
                    redirect('panel/settings/import/'.$type);
                }
                $rw = 2;
                foreach ($final as $csv_pr) {
                    $c_first_name[] = trim($csv_pr['first_name']);
                    $c_last_name[] = trim($csv_pr['last_name']);
                    $c_company[] = trim($csv_pr['company']);
                    $c_telephone[] = trim(preg_replace('/\D+/', '', $csv_pr['telephone']));
                    $c_address[] = trim($csv_pr['address']);
                    $c_city[] = trim($csv_pr['city']);
                    $c_state[] = trim($csv_pr['state']);
                    $c_postal_code[] = trim($csv_pr['postal_code']);
                    $c_email[] = trim($csv_pr['email']);
                    $c_vat[] = trim($csv_pr['vat']);
                    $c_cf[] = trim($csv_pr['cf']);
                    $c_date[] = date('Y-m-d');
                    $c_comment[] = trim($csv_pr['comment']);
                    $c_tax_exempt[] = trim($csv_pr['tax_exempt']);
                    $c_universal[] = trim($csv_pr['universal']);
                    $c_store_id[] = trim($this->activeStore);
                    $rw++;
                }

                $ikeys = array('first_name', 'last_name', 'company', 'telephone', 'address', 'city', 'state', 'postal_code', 'email', 'vat', 'cf', 'date' , 'comment', 'tax_exempt', 'universal', 'store_id');
                $items = array();
                foreach (array_map(null, $c_first_name, $c_last_name, $c_company, $c_telephone, $c_address, $c_city, $c_state, $c_postal_code, $c_email, $c_vat, $c_cf, $c_date, $c_comment, $c_tax_exempt, $c_universal, $c_store_id) as $ikey => $value) {
                    $items[] = array_combine($ikeys, $value);
                }

            }
        }

        if ($this->form_validation->run() == true) {
            if ($this->settings_model->import($items, $type)) {
                $this->session->set_flashdata('message', ucfirst($type)." ".lang('Added Successfully'));
                redirect('panel/settings/import/'.$type);
            }else{
                $this->session->set_flashdata('error', lang('Error Adding')." ".ucfirst($type));
                redirect('panel/settings/import/'.$type);
            }
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->render('import');
        }
    }

    // Multi Store Setup //
    public function store($action = NULL)
    {
        if (!$action or $action == 'index') {
            $this->repairer->checkPermissions('index', FALSE, 'store');
            $this->data['tax_rates'] = $this->settings_model->getTaxRates();
            $this->data['timezones'] = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
            $this->render('store');
        }
        if ($action == 'getAll') {
            $this->load->library('datatables');
            if ($this->uri->segment(5)) {
                $a = $this->uri->segment(5);
                if ($a == 'locked') {
                    $this->datatables->where('locked', 1);
                }elseif($a == 'available'){
                    $this->datatables->where('locked', 0);
                }
            }
            $this->datatables->where('deleted', 0);
            $this->datatables
                ->select('id, name, locked')
                ->from('store');
            $this->datatables->add_column('actions', "$1___$2", 'id, locked');
            $this->datatables->unset_column('id');
            $this->datatables->unset_column('locked');
            echo $this->datatables->generate();
        }elseif ($action == 'toggle') {
            $this->repairer->checkPermissions('disable', FALSE, 'store');

            $toggle = $this->input->post('toggle');
            if ($toggle == 'enable') {
                $data = array(
                    'locked' => 0,
                );
                $a = lang('Unlocked');
            }else{
                $data = array(
                    'locked' => 1,
                );
                $a = lang('Locked');
            }
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('store', $data);
            echo json_encode(array('ret' => 'true', 'toggle' => $a));
        }elseif ($action == 'delete') {
            $this->repairer->checkPermissions('delete', FALSE, 'store');

            $this->db->where('id', $this->input->post('id'));
            $this->db->update('store', array('deleted'=>1));
            echo "true";
        }elseif ($action == 'byID') {
            $data = array();
            $query = $this->db->get_where('store', array('id' => $this->input->post('id')));
            if ($query->num_rows() > 0) {
                $data = $query->row_array();
            }
            echo  json_encode($data);
        }elseif ($action == 'add') {
            $this->repairer->checkPermissions('add', FALSE, 'store');
            $data = array(
                'name' => $this->input->post('name'),
                'address' => $this->input->post('address'),
                'invoice_mail' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'timezone' => $this->input->post('timezone'),
            );
            $data['city'] = $this->input->post('city');
            $data['state'] = $this->input->post('state');
            $data['zipcode'] = $this->input->post('zip');
            $new_phone_tax = implode(',', $this->input->post('new_phone_tax'));
            $used_phone_tax = implode(',', $this->input->post('used_phone_tax'));
            $accessories_tax = implode(',', $this->input->post('accessories_tax'));
            $repair_items_tax = implode(',', $this->input->post('repair_items_tax'));
            $other_items_tax = implode(',', $this->input->post('other_items_tax'));
            $plans_tax = implode(',', $this->input->post('plans_tax'));
            $data['new_phone_tax']      = $new_phone_tax;
            $data['used_phone_tax']     = $used_phone_tax;
            $data['accessories_tax']    = $accessories_tax;
            $data['repair_items_tax']   = $repair_items_tax;
            $data['other_items_tax']    = $other_items_tax;
            $data['plans_tax']          = $plans_tax;

            $this->db->insert('store', $data);
            echo $this->db->insert_id();
        }elseif ($action == 'edit') {
            $this->repairer->checkPermissions('edit', FALSE, 'store');
            $data = array(
                'name' => $this->input->post('name'),
                'address' => $this->input->post('address'),
                'invoice_mail' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'timezone' => $this->input->post('timezone'),
            );
            $data['city'] = $this->input->post('city');
            $data['state'] = $this->input->post('state');
            $data['zipcode'] = $this->input->post('zip');
            // $data['store_wise_reference'] = $this->input->post('store_wise_reference');

            
            $new_phone_tax = implode(',', $this->input->post('new_phone_tax'));
            $used_phone_tax = implode(',', $this->input->post('used_phone_tax'));
            $accessories_tax = implode(',', $this->input->post('accessories_tax'));
            $repair_items_tax = implode(',', $this->input->post('repair_items_tax'));
            $other_items_tax = implode(',', $this->input->post('other_items_tax'));
            $plans_tax = implode(',', $this->input->post('plans_tax'));
            $data['new_phone_tax']      = $new_phone_tax;
            $data['used_phone_tax']     = $used_phone_tax;
            $data['accessories_tax']    = $accessories_tax;
            $data['repair_items_tax']   = $repair_items_tax;
            $data['other_items_tax']    = $other_items_tax;
            $data['plans_tax']          = $plans_tax;
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('store', $data);
        }
    }
  


     public function getActivitiesAjax($show_parent = TRUE){

        $term = $this->input->get('q');
        if ($term) {
            $this->db->where("activities.name LIKE '%" . $term . "%'");
        }
        $this->db->select('id, name');
        $this->db->where('disable', 0);
        if ($show_parent) {
            $this->db->where('sub_id IS NULL', NULL, FALSE);
        }else{
            $this->db->where('sub_id IS NOT NULL', NULL, FALSE);
            if ($this->input->get('activity_id')) {
                $this->db->where('sub_id', $this->input->get('activity_id'));
            }
        }
        $q = $this->db->get('activities');

        $data = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $client) {
                $data[] = array('id' => $client->id, 'text' => "$client->name ");
            }
        }

        echo json_encode($data);
    }
    public function activate() {
        $this->form_validation->set_rules('current_account', lang('current_account'), 'required');
        if ($this->form_validation->run() == TRUE) {
            $current_account = $this->input->post('current_account');
            $this->session->set_userdata('active_store', $current_account);
            redirect('panel');
        }else{
            $this->render('activate');
        }
    }

    function toggle_activities() {
        $toggle = $this->input->post('toggle');
        if ($toggle == 'enable') {
            $data = array('disable' => 0);
            $a = lang('enabled');
        } else {
            $data = array('disable' => 1);
            $a = lang('disabled');
        }
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('activities', $data);
        echo json_encode(array('ret' => 'true', 'toggle' => $a));
    }

    public function delete_activities() {
        $id = $this->input->post('id');
        $this->db->where('id', $id)->update('activities', array('disable' => 1 ));
        echo "true";
    }

    public function activities($type = NULL) {

        $this->repairer->checkPermissions();

        if ($type === 'disabled' || $type === 'enabled') {
            $this->data['toggle_type'] = $type;
        }else{
            $this->data['toggle_type'] = NULL;
        }
        $this->mPageTitle = "Activities";
        $this->render("activities/index");
    }

    // GENERATE THE AJAX TABLE CONTENT //
    public function getAllActivities($type = NULL)
    {
        $this->repairer->checkPermissions('activities');

        $this->load->library('datatables');
        if ($this->uri->segment(4)) {
            $a = $this->uri->segment(4);
            if ($a == 'disabled') {
                $this->datatables->where('activities.disable', 1);
            }elseif($a == 'enabled'){
                $this->datatables->where('activities.disable', 0);
            }
        }

        $this->datatables
            ->select('activities.id as id, activities.name, activities.disable as disable')
            ->where('sub_id IS NULL', NULL, FALSE)
            ->from('activities');
        $this->datatables->add_column('actions', "$1___$2", 'id, disable');
        $this->datatables->unset_column('id');
        $this->datatables->unset_column('disable');
        echo $this->datatables->generate();
    }

    public function addmore_activities() {

        $this->load->view($this->theme."activities/add_item");
    }
    public function add_activities()
    {
        $this->repairer->checkPermissions('add', FALSE, 'activities');

        $this->mPageTitle = lang('Add Activity');
        $this->form_validation->set_rules('activity_name',lang('Activity Name'), 'trim|required');
        $this->form_validation->set_rules('name[]', lang('Sub Activity Name'), 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->render('activities/add');
        }else{
            $data = array(
                'name' => $this->input->post('activity_name'),
                'sub_id' => NULL,
                'disable' => 0,
            );
            $this->db->insert('activities', $data);
            $activity_id = $this->db->insert_id();
            if ($this->input->post('name')) {
                $i = sizeof($this->input->post('name'));
                for ($r = 0; $r < $i; $r++) {
                    $name = $this->input->post('name')[$r];
                    $subs[] = array(
                        'sub_id' => $activity_id,
                        'name' => $name,
                        'disable' => 0,
                    );
                }
            }
            $this->db->insert_batch('activities', $subs);
            $this->session->set_flashdata('message', lang('Activity added successfully'));
            redirect('panel/settings/activities');
        }
    }
    public function edit_activities($id)
    {
        $this->repairer->checkPermissions('edit', FALSE, 'activities');


        $this->mPageTitle = lang('Edit Activity');
        $this->form_validation->set_rules('activity_name', lang('Activity Name'), 'trim|required');
        $this->form_validation->set_rules('name[]', lang('Sub Activity Name'), 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->data['activity'] = $this->db->where('id', $id)->get('activities')->row();
            $this->data['sub_activities'] = $this->db->where('sub_id', $id)->get('activities')->result();
            $this->render('activities/edit');
        }else{
            $this->db->update('activities', array('name' => $this->input->post('activity_name') ), array('id'=> $id));

            if ($this->input->post('name')) {
                $i = sizeof($this->input->post('name'));
                for ($r = 0; $r < $i; $r++) {
                    $name = $this->input->post('name')[$r];
                    $disable = $this->input->post('disable')[$r];
                    $sub_old = (int)$this->input->post('sub_old')[$r];
                    $subs = array(
                        'sub_id' => $id,
                        'name' => $name,
                        'disable' => $disable,
                    );
                    if ($sub_old == 1) {
                        $sub_id = (int)$_POST['sub_id'][$r];
                        $this->db->where('id', $sub_id)->update('activities', $subs);
                    }else{
                        $this->db->insert('activities', $subs);
                    }
                }
            }
            $this->session->set_flashdata('message', lang('Activity edited successfully'));
            redirect('panel/settings/activities');
        }
    }


     public function getCategoriesAjax($show_parent = TRUE){

        $term = $this->input->get('r');
        if ($term) {
            $this->db->where("categories.name LIKE '%" . $term . "%'");
        }
        $this->db->select('id, name');
        $this->db->where('disable', 0);
        if ($show_parent) {
            $this->db->where('sub_id IS NULL', NULL, FALSE);
        }else{
            $this->db->where('sub_id IS NOT NULL', NULL, FALSE);
            if ($this->input->get('category_id')) {
                $this->db->where('sub_id', $this->input->get('category_id'));
            }
        }
        $q = $this->db->get('categories');

        $data = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $client) {
                $data[] = array('id' => $client->id, 'text' => "$client->name ");
            }
        }

        echo json_encode($data);
    }

    function toggle_categories() {
        $toggle = $this->input->post('toggle');
        if ($toggle == 'enable') {
            $data = array('disable' => 0);
            $a = lang('enabled');
        } else {
            $data = array('disable' => 1);
            $a = lang('disabled');
        }
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('categories', $data);
        echo json_encode(array('ret' => 'true', 'toggle' => $a));
    }

    public function delete_categories() {
        $id = $this->input->post('id');
        $this->db->where('id', $id)->update('categories', array('disable' => 1 ));
        echo "true";
    }

    public function categories($type = NULL) {

        $this->repairer->checkPermissions();

        if ($type === 'disabled' || $type === 'enabled') {
            $this->data['toggle_type'] = $type;
        }else{
            $this->data['toggle_type'] = NULL;
        }
        $this->mPageTitle = "Categories";
        $this->render("categories/index");
    }

    // GENERATE THE AJAX TABLE CONTENT //
    public function getAllCategories($type = NULL)
    {
        $this->repairer->checkPermissions('categories');

        $this->load->library('datatables');
        if ($this->uri->segment(4)) {
            $a = $this->uri->segment(4);
            if ($a == 'disabled') {
                $this->datatables->where('categories.disable', 1);
            }elseif($a == 'enabled'){
                $this->datatables->where('categories.disable', 0);
            }
        }

        $this->datatables
            ->select('categories.id as id, categories.name, categories.disable as disable')
            ->where('sub_id IS NULL', NULL, FALSE)
            ->from('categories');
        $this->datatables->add_column('actions', "$1___$2", 'id, disable');
        $this->datatables->unset_column('id');
        $this->datatables->unset_column('disable');
        echo $this->datatables->generate();
    }

    public function addmore_categories() {

        $this->load->view($this->theme."categories/add_item");
    }
    public function add_categories()
    {
        $this->repairer->checkPermissions('add', FALSE, 'categories');

        $this->mPageTitle = lang('Add Category');
        $this->form_validation->set_rules('category_name', lang('Name'), 'trim|required');
        $this->form_validation->set_rules('name[]', 'cubcategory', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->render('categories/add');
        }else{
            $data = array(
                'name' => $this->input->post('category_name'),
                'sub_id' => NULL,
                'disable' => 0,
            );
            $this->db->insert('categories', $data);
            $category_id = $this->db->insert_id();
            if (isset($_POST['name']) && $_POST['name'] !== null) {
                $i = sizeof($_POST['name']);
                for ($r = 0; $r < $i; $r++) {
                    $name = $this->input->post('name')[$r];
                    $subs[] = array(
                        'sub_id' => $category_id,
                        'name' => $name,
                        'disable' => 0,
                    );
                }
            }
            $this->db->insert_batch('categories', $subs);
            $this->session->set_flashdata('message', lang('Category added successfully'));
            redirect('panel/settings/categories');
        }
    }
    public function edit_categories($id)
    {
        $this->repairer->checkPermissions('edit', FALSE, 'categories');


        $this->mPageTitle = "Edit Category";
        $this->form_validation->set_rules('category_name', lang('Name'), 'trim|required');
        $this->form_validation->set_rules('name[]', lang('subcategory'), 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->data['category'] = $this->db->where('id', $id)->get('categories')->row();
            $this->data['sub_categories'] = $this->db->where('sub_id', $id)->get('categories')->result();
            $this->render('categories/edit');
        }else{
            $this->db->update('categories', array('name' => $this->input->post('category_name') ), array('id'=> $id));
            if (isset($_POST['name']) && $_POST['name'] !== null) {
                $i = sizeof($_POST['name']);
                for ($r = 0; $r < $i; $r++) {
                    $name = $this->input->post('name')[$r];
                    $disable = $this->input->post('disable')[$r];
                    $sub_old = (int)$_POST['sub_old'][$r];
                    $subs = array(
                        'sub_id' => $id,
                        'name' => $name,
                        'disable' => $disable,
                    );
                    if ($sub_old == 1) {
                        $sub_id = (int)$_POST['sub_id'][$r];
                        $this->db->where('id', $sub_id)->update('categories', $subs);
                    }else{
                        $this->db->insert('categories', $subs);
                    }
                }
            }
            $this->session->set_flashdata('message', lang('Category edited successfully'));
            redirect('panel/settings/categories');
        }
    }

    public function mandatory_fields() {
        $this->lang->load('mand', $this->mSettings->language);

        $result = $this->db->get('frm_priv')->result();
        $data = array();
        foreach($result as $row){
            $data[$row->form][$row->name] = $row->required;
            $this->form_validation->set_rules($row->form.'___'.$row->name, lang($row->name), 'trim');
        }
        if ($this->form_validation->run() == FALSE) {
            $this->data['mand_fields'] = $data;
            $this->render('mand_fields');
        }else{
            foreach ($_POST as $key => $required) {
                $key = explode('___', $key);
                $form = $key[0];
                $field = $key[1];
                $this->db->where('form', $form)->where('name', $field)->update('frm_priv', array('required' => $required));
            }
            $this->session->set_flashdata('message', lang('updated'));
            redirect('panel/settings/mandatory_fields');
        }
    }


    // Warranty Plans CRUD //
    public function warranties($type = NULL)
    {
        if ($type === 'disabled' || $type === 'enabled') {
            $this->data['toggle_type'] = $type;
        }else{
            $this->data['toggle_type'] = NULL;
        }
        $this->render('warranties');
    }


    public function warranty_add()
    {
        $data = array(
            'warranty_duration' => $this->input->post('duration'),
            'warranty_duration_type' => $this->input->post('duration_type'),
            'details' => $this->input->post('details'),
        );
        $this->db->insert('warranties', $data);
        echo "true";
    }
    public function warranty_edit()
    {
        $id = $this->input->post('id');
        $data = array(
            'warranty_duration' => $this->input->post('duration'),
            'warranty_duration_type' => $this->input->post('duration_type'),
            'details' => $this->input->post('details'),
        );
        $this->db->update('warranties', $data, array('id'=>$id));
        echo "true";
    }

    public function getWarrantyByID()
    {
        $id = $this->input->post('id');
        $q = $this->db->get_where('warranties', array('id'=>$id));
        if ($q->num_rows() > 0) {
            echo json_encode(array('success' => true, 'data'=>$q->row() ));
        }else{
            echo json_encode(array('success' => false));
        }
    }

    public function warranty_toggle()
    {
        $toggle = $this->input->post('toggle');
            if ($toggle == 'enable') {
                $data = array(
                    'disable' => 0,
                );
                $a = lang('enabled');
            }else{
                $data = array(
                    'disable' => 1,
                );
                $a = lang('disabled');
            }
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('warranties', $data);
            echo json_encode(array('ret' => 'true', 'toggle' => $a));
    }
    // SHOW THE SETTINGS PAGE //
    public function getAllWarranties($type = '')
    {
        $this->load->library('datatables');
        if ($type == 'disabled') {
            $this->datatables->where('disable', 1);
        }elseif($type == 'enabled'){
            $this->datatables->where('disable', 0);
        }
        $this->datatables
            ->select('warranties.id as id, warranty_duration, warranty_duration_type, details, warranties.disable as disable')
            ->from('warranties');
        $this->datatables->add_column('actions', "$1___$2", 'id, disable');
        $this->datatables->unset_column('id');
        $this->datatables->unset_column('disable');
        echo $this->datatables->generate();
    }
    


    // Activations Plans CRUD //
    public function activation_plans($type = NULL)
    {
        if ($type === 'disabled' || $type === 'enabled') {
            $this->data['toggle_type'] = $type;
        }else{
            $this->data['toggle_type'] = NULL;
        }
        $this->render('activation_plans');
    }

    public function getAllAPlans($type = '')
    {
        $this->load->library('datatables');
        if ($type == 'disabled') {
            $this->datatables->where('disable', 1);
        }elseif($type == 'enabled'){
            $this->datatables->where('disable', 0);
        }
        $this->datatables
            ->select('id, name, items, disable')
            ->from('activation_plans');
        $this->datatables->add_column('actions', "$1___$2", 'id, disable');
        $this->datatables->unset_column('id');
        $this->datatables->unset_column('disable');
        echo $this->datatables->generate();
    }

    public function getAPlanByID(){
        $id = $this->input->post('id');
        $q = $this->db->get_where('activation_plans', array('id'=>$id));
        if ($q->num_rows() > 0) {
            $data = $q->row();
            $data->items = json_decode($data->items);
            echo json_encode(array('success' => true, 'data'=>$data));
        }else{
            echo json_encode(array('success' => false));
        }
    }
    public function aplan_add(){
        $data = array(
            'name' => $this->input->post('name'),
            'universal' => $this->input->post('universal'),
            'store_id' => $this->activeStore,
        );

        $items = array();
        if (isset($_POST['product_name']) && $_POST['product_name'] !== null) {
            $i = sizeof($_POST['product_name']);
            for ($r = 0; $r < $i; $r++) {
                $name = $_POST['product_name'][$r];
                $type = $_POST['product_type'][$r];
                $id = $_POST['product_id'][$r];
                $code = $_POST['product'][$r];
                $items[] = array(
                    'name' => $name,
                    'code' => $code,
                    'type' => $type,
                    'id'   => $id,
                );
            }
        }
        $data['items'] = json_encode($items);
        
        $this->db->insert('activation_plans', $data);
        echo "true";
    }

    public function aplan_edit(){
        $data = array(
            'name' => $this->input->post('name'),
            'universal' => $this->input->post('universal'),
            'store_id' => $this->activeStore,
        );

        $items = array();
        if (isset($_POST['product_name']) && $_POST['product_name'] !== null) {
            $i = sizeof($_POST['product_name']);
            for ($r = 0; $r < $i; $r++) {
                $name = $_POST['product_name'][$r];
                $type = $_POST['product_type'][$r];
                $id = $_POST['product_id'][$r];
                $code = $_POST['product'][$r];
                $items[] = array(
                    'name' => $name,
                    'code' => $code,
                    'type' => $type,
                    'id'   => $id,
                );
            }
        }
        $data['items'] = json_encode($items);
        $this->db->update('activation_plans', $data, array('id'=>$this->input->post('id')));
        echo "true";
    }

    public function discount_codes($action = NULL)
    {
        if (!$action or $action == 'index') {
            $this->render('discount_codes');
        } if ($action == 'getAll') {
            $this->load->library('datatables');
            $this->datatables
                ->select('code, type, CONCAT(IFNULL(used_for, ""),"____" ,IFNULL(used_for_name, "")) as used_for, used_on as date, CONCAT(users.first_name, " ", users.last_name) as name, sale_number, used_on as status, discount_codes.id as actions')
                ->join('users', 'users.id=discount_codes.used_by', 'left')
                ->from('discount_codes');
            echo $this->datatables->generate();
        }elseif ($action == 'add') {
            $q = $this->db->get_where('discount_codes', array('code'=>$this->input->post('code')));
            if ($q->num_rows() > 0) {
                $this->repairer->send_json((array('success' => false, 'message' => lang('This Code already exists. Please try another code'))));
            }
            $type = $this->input->post('type');
            $used_for = $this->input->post('used_for');
            $used_for_id = NULL;
            $used_for_name = NULL;
            if ($type == 'product') {
                $product = explode('____', $used_for);
                $used_for = $product[1];
                $used_for_id = $product[0];
                $used_for_name = $product[2];
            }
            $data = array(
                'code'          => $this->input->post('code'),
                'type'          => $this->input->post('type'),
                'used_for'      => $used_for,
                'used_for_id'   => $used_for_id,
                'used_for_name' => $used_for_name,
            );
            $this->db->insert('discount_codes', $data);
            $this->repairer->send_json((array('success' => true, 'id' => $this->db->insert_id())));
        }elseif ($action == 'json_sort') {
            $term = $this->input->post('id', true);
            $rows = array();
            $q = NULL;
            if ($term == 'product') {
                $this->load->model('pos_model');
                $new_phones     = $this->pos_model->getNewPhones();
                $used_phones    = $this->pos_model->getUsedPhones();
                $others         = $this->pos_model->getOthers();
                $accessories    = $this->pos_model->getAccessoryNames();
                $plans          = $this->pos_model->getAllPlans();
                $repair_items   = $this->pos_model->getProductNames();
                $rows = array_merge((array)$repair_items, (array)$accessories, (array)$new_phones, (array)$used_phones,(array)$others,(array)$plans);
                unset($new_phones, $used_phones, $others, $accessories, $plans, $repair_items);
                foreach (array_filter($rows) as $row) {
                    $pr[] = array('id' => $row->id.'____'.$row->type.'____'.$row->name, 'text' => $row->name.' ('.humanize($row->type).')');
                }
            }elseif($term == 'category'){
                $pr[] = array('id' => 'other', 'text' => lang('Other Products'));
                $pr[] = array('id' => 'repair', 'text' => lang('Repair Parts'));
                $pr[] = array('id' => 'new_phone', 'text' => lang('New Phones'));
                $pr[] = array('id' => 'used_phone', 'text' => lang('Used Phones'));
                $pr[] = array('id' => 'accessory', 'text' => lang('Accessories'));
                $pr[] = array('id' => 'plans', 'text' => lang('Cellular Plans'));
            }
            if (!empty($pr)) {
                $this->repairer->send_json($pr);
            }else {
                $this->repairer->send_json((array('id' => 0, 'text' => lang('no_match_found'))));
            }            
        }elseif($action == 'delete'){
            $id = $this->input->post('id');
            $this->db->delete('discount_codes', array('id'=>$id));
            echo json_encode(array('success'=>true));
        }
    }



    public function repair_statuses() {
        $this->mPageTitle = lang('repair_statuses');
        
        $this->data['statuses'] = $this->settings_model->getRepairStatuses();
        $this->render('repair_statuses');
    }   
     
    public function updatePosition() {
        $i = 1;
        foreach ($_GET['id'] as $item):
            $this->db
                ->where('id', $item)
                ->update('status', array('position' => $i));
            $i++;
        endforeach;
    }    

    public function status_add() {
        $data = array(
            'label' => $this->input->post('label'),
            'bg_color' => $this->input->post('bg_color'),
            'fg_color' => $this->input->post('fg_color'),
            'send_email' => $this->input->post('send_email') ? 1 : 0,
            'send_sms' => $this->input->post('send_sms') ? 1 : 0,
            'email_text' => $this->input->post('send_email') ? $this->input->post('email_text') : NULL,
            'sms_text' => $this->input->post('send_sms') ? $this->input->post('sms_text') : NULL,
            'position' => $this->settings_model->countRepairStatuses(),
            'completed' => $this->input->post('completed') ? $this->input->post('completed') : 0,
            'show_in_default' => $this->input->post('show_in_default') ? $this->input->post('show_in_default') : 0,
        );

        $this->db->insert('status', $data);
        $id = $this->db->insert_id();

        $this->settings_model->addLog('add', 'status', $id, json_encode(array(
            'data' => $data,
        )));
        echo $this->repairer->send_json(array('success'=>true));
    }    

    public function status_edit() {
        header("Content-Type: text/html; charset=utf-8");
        $id = $this->input->post('id');


        $data = array(
            'label' => $this->input->post('label'),
            'bg_color' => $this->input->post('bg_color'),
            'fg_color' => $this->input->post('fg_color'),
            'send_email' => $this->input->post('send_email') ? 1 : 0,
            'send_sms' => $this->input->post('send_sms') ? 1 : 0,
            'email_text' => $this->input->post('send_email') ? $this->input->post('email_text', false) : NULL,
            'sms_text' => $this->input->post('send_sms') ? $this->input->post('sms_text', false) : NULL,
            'completed' => $this->input->post('completed') ? $this->input->post('completed') : 0,
            'show_in_default' => $this->input->post('show_in_default') ? $this->input->post('show_in_default') : 0,
        );
        $this->db->update('status', $data, array('id'=>$id));
        $this->settings_model->addLog('update', 'status', $id, json_encode(array(
            'data' => $data,
        )));
        echo $this->repairer->send_json(array('success'=>true));
    }    
    
    public function statusDelete() {
        $id = $this->input->post('id');
        if ($this->settings_model->verifyStatusDelete($id)) {
            $this->db->delete('status', array('id'=>$id));
            echo $this->repairer->send_json(array('success'=>true));
            $this->settings_model->addLog('delete', 'status', $id, json_encode(array(
                
            )));
            die();
        }
        echo $this->repairer->send_json(array('success'=>false));

    }    
    
    // GET CUSTOMER AND SEND TO AJAX FOR SHOW IT //
    public function getStatusByID()
    {
        $id = $this->security->xss_clean($this->input->post('id', true));
        $data = $this->db->get_where('status', array('id'=>$id));
        if ($data->num_rows() > 0) {
            return $this->repairer->send_json(array('status' => true,'data'=>$data->row()));
        }
        return $this->repairer->send_json(array('status' => false));

    }


    
    public function sms_gateways() {
        $this->render('sms_gateways');
    }


    public function add_smsgateway()
    {
        $postdata = $this->input->post('postdata');
        $data = array(
            'name' => $this->input->post('name'),
            'user_id' => $this->mUser->id,
            'url' => $this->input->post('url'),
            'to_name' => $this->input->post('to_name'),
            // 'from_name' => $this->input->post('from_name'),
            'message_name' => $this->input->post('message_name'),
            'notes' => $this->input->post('notes'),
            'postdata' => $this->input->post('postdata') ? json_encode(array_combine($postdata['name'], $postdata['value'])) : '',
        );

        $this->db->insert('sms_gateways', $data);
        $id = $this->db->insert_id();
        $this->settings_model->addLog('add', 'sms_gateways', $id, json_encode(array(
            'data' => $data
        )));
    }


    public function edit_smsgateway($id = null)
    {
        $id = $this->input->post('id');
        $postdata = $this->input->post('postdata');
        $data = array(
            'name' => $this->input->post('name'),
            'user_id' => $this->mUser->id,
            'url' => $this->input->post('url'),
            'to_name' => $this->input->post('to_name'),
            // 'from_name' => $this->input->post('from_name'),
            'message_name' => $this->input->post('message_name'),
            'notes' => $this->input->post('notes'),
            'postdata' => $this->input->post('postdata') ? json_encode(array_combine($postdata['name'], $postdata['value'])) : '',
        );

        $this->db->where('id', $id)->update('sms_gateways', $data);
        $id = $this->db->insert_id();
        $this->settings_model->addLog('edit', 'sms_gateways', $id, json_encode(array(
            'data' => $data
        )));
    }


    public function delete_smsgateway($id = null)
    {
        $id = $this->input->post('id');
        $this->db->where('id', $id)->delete('sms_gateways');
        $this->settings_model->addLog('delete', 'sms_gateways', $id, json_encode(array(
        )));

        echo $this->repairer->send_json(['success'=>true]);

    }

    public function getSMSGateways()
    {
        $this->repairer->checkPermissions('index', NULL, 'tax_rates');
        $this->load->library('datatables');
        $this->datatables
            ->select('id, name, notes')
            ->from('sms_gateways');
        
        $actions = "";
        if ($this->Admin || $this->GP['sms_gateways-edit']) {
            $actions .= "<a  data-dismiss='modal' id='modify' href='#smsgatewaymodal' data-toggle='modal' data-num='$1'><button class='btn btn-primary btn-xs'><i class='fas fa-edit'></i></button></a>";
        }
        if ($this->Admin || $this->GP['sms_gateways-delete']) {
            $actions .= "<a id='delete' data-num='$1'><button class='btn btn-danger btn-xs'><i class='fas fa-trash'></i></button></a>";
        }

        $this->datatables->add_column('actions', $actions, 'id');
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }


    public function get_smsgateway_id($id = null)
    {
        $id = $this->input->post('id');
        $q = $this->db->where('id', $id)->get('sms_gateways');
        if ($q->num_rows() > 0) {
            echo $this->repairer->send_json(['success'=>true, 'data'=>$q->row()]);
        }
        echo $this->repairer->send_json(['success'=>false]);
        
    }

    public function repair_statuses_pos($id = null)
    {


        $data = [];
        $data['repair_deposit'] = $this->input->post('repair_deposit');
        $data['repair_completed'] = $this->input->post('repair_completed');
        $this->db->update('settings', $data);
        redirect('panel/settings/repair_statuses/');
    }


    

}




