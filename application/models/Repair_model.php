<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Repair_model extends CI_Model
{

	public function __construct()
    {
        parent::__construct();
        $this->load->model('settings_model');
    }



     public function getAllRepairs($start = null, $end = null)
    {

        if ($start) {
            $this->db->where('date_opening >', $start);
        }

        if ($end) {
            $this->db->where('date_opening <', $end);
        }

        $q = $this->db
            ->select('repair.*, status.id as status_id, fg_color, bg_color')
            ->join('status', 'status.id=repair.status', 'left')
            ->get('repair');
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return array();
    }


    public function getAllClients()
    {
        $this->db->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')' , NULL, FALSE);
        $q = $this->db->get_where('clients', array('disable'=> 0));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getProductNameByRepairID($id) {
        $this->db->select('inventory.*, repair_items.tax_rate, (SELECT price from stock where stock.inventory_id=inventory.id AND stock.inventory_type=\'repair\'  and selected = 0 AND in_state_of_transfer = 0 AND store_id='.(int)$this->session->userdata('active_store').' ORDER BY modified_date ASC LIMIT 1) as cost, (SELECT id from stock where stock.inventory_id=inventory.id AND stock.inventory_type=\'repair\'  and selected = 0 AND in_state_of_transfer = 0 AND store_id='.(int)$this->session->userdata('active_store').' ORDER BY modified_date ASC LIMIT 1) as stock_id, option_id, repair_items.serial_number as serial_number, repair_items.discount as discount, item_type , item_details')
            ->join('inventory', 'inventory.id = repair_items.product_id', 'left')
            ->where('isDeleted !=', 1)
            ->where('repair_items.item_type', 'repair')
            ->where('repair_items.repair_id', $id)
            ->where('repair_items.store_id', (int)$this->session->userdata('active_store'));
        $q = $this->db->get('repair_items');
        $repair_items = $q->result();

        $this->db->select('other.*,upc_code as code, repair_items.tax_rate, (SELECT price from stock where stock.inventory_id=other.id AND stock.inventory_type=\'other\'  and selected = 0 AND in_state_of_transfer = 0 AND store_id='.(int)$this->session->userdata('active_store').' ORDER BY modified_date ASC LIMIT 1) as cost, (SELECT id from stock where stock.inventory_id=other.id AND stock.inventory_type=\'other\'  and selected = 0 AND in_state_of_transfer = 0 AND store_id='.(int)$this->session->userdata('active_store').' ORDER BY modified_date ASC LIMIT 1) as stock_id, option_id, repair_items.serial_number as serial_number, repair_items.discount as discount, item_type , item_details')
            ->join('other', 'other.id = repair_items.product_id', 'left')
            ->where('repair_items.item_type', 'other')
            ->where('repair_items.repair_id', $id)
            ->where('repair_items.store_id', (int)$this->session->userdata('active_store'));
        $q = $this->db->get('repair_items');
        $other_items = $q->result();

        $this->db->select('accessory.*,upc_code as code, repair_items.tax_rate, (SELECT price from stock where stock.inventory_id=accessory.id AND stock.inventory_type=\'accessory\'  and selected = 0 AND in_state_of_transfer = 0 AND store_id='.(int)$this->session->userdata('active_store').' ORDER BY modified_date ASC LIMIT 1) as cost, (SELECT id from stock where stock.inventory_id=accessory.id AND stock.inventory_type=\'accessory\'  and selected = 0 AND in_state_of_transfer = 0 AND store_id='.(int)$this->session->userdata('active_store').' ORDER BY modified_date ASC LIMIT 1) as stock_id, option_id, repair_items.serial_number as serial_number, repair_items.discount as discount, item_type , item_details')
            ->join('accessory', 'accessory.id = repair_items.product_id', 'left')
            ->where('repair_items.item_type', 'accessory')
            ->where('repair_items.repair_id', $id)
            ->where('repair_items.store_id', (int)$this->session->userdata('active_store'));
        $q = $this->db->get('repair_items');
        $accessory_items = $q->result();

        return array_merge($repair_items, $other_items, $accessory_items);
        // return false;
    }
    public function getAllRepairItems($id)
    {
        $q = $this->db->get_where('sale_items', array('repair_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }



    public function getClientNameByID($id)
    {
        $q = $this->db->select('*, CONCAT(first_name, " " , last_name) as name')->get_where('clients', array('id' => $id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function id_from_name($name)
    {
        $value = $this->db->escape_like_str($name);

        $data = array();

        $this->db->from('clients');
        $this->db->where("CONCAT(first_name, ' ', last_name, ' ', company) LIKE '%".$value."%'", null, false);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $data = $query->row_array();
            return $data['id'];
        } else {
            return false;
        }
    }

	public function repair_code_exists($code) {
        return $this->db
            ->where('code', $code)
            ->count_all_results('repair') > 0 ? true : false;
    }

	public function add_repair($data, $items, $attachments) {
        $this->repairer->updateReference('repair');

        // if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        //     $email = $data['email'];
        // }else{
        //     $email = FALSE;
        // }
        // unset($data['email']);
        $this->db->insert('repair', $data);
        $id = $repair_id = $this->db->insert_id();

        if ($attachments) {
            $attachments = explode(',', $attachments);
            $this->db
                ->where_in('id', $attachments)
                ->update('attachments', array('repair_id'=>$id));
        }

        $this->syncRepairPayments($repair_id);
        
        if (!empty($items)) {
            foreach ($items as $item) {
                if ($item['item_type'] == 'repair' || $item['item_type'] == 'other' || $item['item_type'] == 'accessory' ) {
                    $serialized = false;
                    if ($item['serial_number'] !== null) {
                        $serialized = true;
                        $this->db
                            ->where('inventory_type', $item['item_type'] == 'new_phone' ? 'phones' : $item['item_type'])
                            ->where('inventory_id', $item['product_id'])
                            ->where('serial_number', $item['serial_number'])
                            ->where('store_id', (int)$this->session->userdata('active_store'))
                            ->where('in_state_of_transfer', 0)
                            ->order_by('modified_date', 'DESC')
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
                            ->order_by('modified_date', 'DESC')
                            ->limit(1);
                        $q = $this->db->get('stock');
                        if ($q->num_rows() > 0) {
                            $item['unit_cost'] = $q->row()->price;
                            $id = $q->row()->id;
                            $this->db->delete('stock', array('id' => $id));
                        }
                    }
                }

                $item['repair_id'] = $repair_id;
                $this->db->insert('sale_items', $item);
            }
        }


        $array = array();
        $array['id'] = $repair_id;
        $settings = $this->settings_model->getSettings();
        $sms_result = $this->change_status($repair_id, $data['status']);

        unset($settings);
        return $array;
    }

    public function edit_repair($repair_id, $data, $items) {

        $cancelled = FALSE;

        $this->db->where('id', $repair_id);
        $this->db->update('repair', $data);

        $pitems = $this->getAllRepairItems($repair_id);
        $sms_result = $this->change_status($repair_id, $data['status']);
        $this->syncRepairPayments($repair_id);

        if ((int)$data['status'] === 0) {
            $cancelled = TRUE;
        }

        $this->db->where('repair_id', $repair_id)->delete('sale_items');
        if ($pitems) {
            $i = sizeof($pitems);
            for ($r = 0; $r < $i; $r++) {
                if ($pitems[$r]->unit_cost > 0) {
                    $data = array(
                        'inventory_id'      => $pitems[$r]->product_id,
                        'inventory_type'    => $pitems[$r]->item_type,
                        'price'             => $pitems[$r]->unit_cost,
                        'modified_date'     => $pitems[$r]->date,
                        'serial_number'     => $pitems[$r]->serial_number == 'null' ? null : $pitems[$r]->serial_number,
                        'store_id'          =>  (int)$this->session->userdata('active_store'),
                    );
                    $this->db->insert('stock' ,$data);
                }
            }
        }


        if (!empty($items)) {
            foreach ($items as $item) {

                if ($item['item_type'] == 'repair' || $item['item_type'] == 'other' || $item['item_type'] == 'accessory' ) {
                    $serialized = false;
                    if ($item['serial_number'] !== null) {
                        $serialized = true;
                        $this->db
                            ->where('inventory_type', $item['item_type'] == 'new_phone' ? 'phones' : $item['item_type'])
                            ->where('inventory_id', $item['product_id'])
                            ->where('serial_number', $item['serial_number'])
                            ->where('store_id', (int)$this->session->userdata('active_store'))
                            ->where('in_state_of_transfer', 0)
                            ->order_by('modified_date', 'DESC')
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
                            ->order_by('modified_date', 'DESC')
                            ->limit(1);
                        $q = $this->db->get('stock');
                        if ($q->num_rows() > 0) {
                            $item['unit_cost'] = $q->row()->price;
                            $id = $q->row()->id;
                            $this->db->delete('stock', array('id' => $id));
                        }
                    }
                }
                 $item['repair_id'] = $repair_id;
                $this->db->insert('sale_items', $item); 
            }
           
        }



        return true;
    }
    public function getRepairByID($id) {
        $this->activeStoreData = $this->settings_model->getStoreByID((int)$this->session->userdata('active_store'));

        $this->load->model('pos_model');
        $this->db->where('repair.id', $id);
        $this->db->join('manufacturers', 'manufacturers.id=repair.manufacturer_id', 'left');
        $this->db->join('users', 'users.id=repair.assigned_to', 'left');
        $this->db->select('repair.*, manufacturers.name as manufacturer_name, repair.id as id, CONCAT(users.first_name, " ", users.first_name) as assigned_to_name');
        $q = $this->db->get('repair');

        $data = array();
        $items = array();
        if ($q->num_rows() > 0) {
            $data = $q->row_array();
            $q = $this->db->get_where('sale_items', array('repair_id' => $id));
                
            $c = str_replace(".", "", microtime(true));
            $r = 0;
            foreach (($q->result()) as $item) {
                $row = $this->pos_model->getProductByTypeAndID($item->product_id, $item->item_type);
                if($item->item_type == 'manual') {
                        $item->product_id = 'manual'.uniqid();
                        $items[$item->product_id] = array(
                        'row_id' => $item->product_id,
                        'item_id' => $item->product_id,
                        'label' => $item->product_name, 
                        'code' => $item->product_code, 
                        'name' => $item->product_name, 
                        'price' => $item->unit_price, 
                        'qty' => $item->quantity, 
                        'type' => $item->item_type, 
                        'cost'=>$item->unit_cost, 
                        'stock_id'=> null,
                        'product_id'=> $item->product_id,
                        'taxable'=> 0,
                        'pr_tax' => null,
                        'variants' => FALSE,
                        'option_selected' => TRUE,
                        'options' => null,
                        'option' => TRUE,
                        'item_details' => $item->item_details,
                        'row' => [
                            'alert_quantity' => "0",
                            'category' => "12",
                            'code' => $item->product_id,
                            'cost' => "0",
                            'date_created' => "2021-04-14 18:59:39",
                            'delivery_note_number' => "",
                            'details' => "",
                            'discount_type' => "1",
                            'id' => $item->product_id,
                            'isDeleted' => "0",
                            'is_serialized' => "0",
                            'manufacturer_id' => "13",
                            'max_discount' => "0.00",
                            'model_name' => "after2",
                            'name' => "",
                            'price' => $item->unit_price, 
                            'qty' => "1",
                            'quantity' => "0.00",
                            'quick_sale' => "1",
                            'stock_id' => null,
                            'store_id' => "1",
                            'sub_category' => "13",
                            'tax_rate' => null,
                            'tax_rates' => null,
                            'taxable' => "0",
                            'type' => "manual",
                            'unit' => null,
                            'universal' => "0",
                            'variants' => false,
                            'warranty_id' => "0",

                        ],
                        'discount' => $item->discount ?? 0,
                        'is_serialized' => 0,
                        'serialed' => TRUE,
                        'serial_number' => NULL,
                        'used_phone_vals' => NULL,
                        'serial_search' => FALSE,
                        'phone_number' => NULL,
                        'set_reminder' => NULL,
                        'activation_items' => NULL,
                        'discount_code_used' => NULL,
                        'activation_spiff' => 0,
                        'purchase_type' => false,
                        'previous_item' => true,
                    );
                }else{

                    if ($row->taxable) {
                        if ($row->type == 'repair') {
                            $row->tax_rates = $this->activeStoreData->repair_items_tax; 
                        }elseif ($row->type == 'accessory') {
                            $row->tax_rates = $this->activeStoreData->accessories_tax; 
                        }elseif ($row->type == 'other') {
                            $row->tax_rates = $this->activeStoreData->other_items_tax; 
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

                    $item_id = $row->type.($c + $r);
                    $row_id = $row->type.time().$r;

                    $row->variable_price = 0;
                    $items[$row_id] = array(
                        'row_id' => $row_id,
                        'item_id' => $item_id,
                        'label' => $row->name . " (" . $row->code . ")", 
                        'code' => $row->code, 
                        'name' => $row->name, 
                        'price' => $item->unit_price, 
                        'qty' => $item->quantity, 
                        'type' => $row->type, 
                        'cost'=>$item->unit_cost, 
                        'stock_id'=>$row->stock_id, 
                        'product_id'=>$row->id,
                        'taxable'=>$row->taxable,
                        'pr_tax' => $o_taxes,
                        'variants' => $row->variants ? TRUE : FALSE,
                        'option_selected' => $item->option_id ? true : false,
                        'options' => $row->variants,
                        'option' => $item->option_id,
                        'item_details' => $item->item_details,
                        'row' => $row,
                        'discount' => $item->discount ?? 0,
                        // 'discount' => $row->discount,
                        'is_serialized' => (int)$row->is_serialized,
                        'serialed' => (int)$row->is_serialized ? (isset($row->serial_number) ? TRUE : FALSE) : TRUE,
                        'serial_number' => isset($row->serial_number) ? $row->serial_number : NULL,
                        'used_phone_vals' => NULL,
                        'serial_search' => (isset($row->serial_number) || in_array($row->type, array('repair', 'other', 'accessory', 'new_phone'))) ? TRUE : FALSE,
                        'phone_number' => NULL,
                        'set_reminder' => NULL,
                        'activation_items' => NULL,
                        'discount_code_used' => NULL,
                        'activation_spiff' => 0,
                        'purchase_type' => false,
                        'previous_item' => true,
                    );
                }
                $r++;
            }
            // echo "<pre>";
            // print_r($items);
            // die();
            $data['items'] = $items;
            return $data;
        }
        return false;
    }

     /*
    |--------------------------------------------------------------------------
    | SET THE ORDER STATUS  TO: CLOSED
    | @param Order ID
    |--------------------------------------------------------------------------
    */
    public function complete_repair($id)
    {
        $data = array(
            'status' => 0,
            'date_closing' => date('Y-m-d H:i:s'),
        );
        $this->db->where('id', $id);
        $this->db->update('repair', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | SET THE ORDER STATUS  TO: APPROVED
    | @param Order ID
    |--------------------------------------------------------------------------
    */
    public function approved_repair($id)
    {
        $data = array(
            'status' => 1,
        );
        $this->db->where('id', $id);
        $this->db->update('repair', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | SET THE ORDER STATUS TO: TO DELIVER
    | @param Order ID
    |--------------------------------------------------------------------------
    */
    public function tobedeliver_repair($id)
    {
        $data = array(
            'status' => 2,
            'date_closing' => date('Y-m-d H:i:s'),
        );
        $this->db->where('id', $id);
        $this->db->update('repair', $data);

        $repair = $this->findRepairByID($id);

        if ($repair['sms'] == 1)
        {
            $settings = $this->settings_model->getSettings();
            $this->send_sms($repair['telephone'], $settings->r_closing, $repair['name'], $repair['model_name'], $repair['code'], $id);
        }
    }

     /*
    |--------------------------------------------------------------------------
    | FIND ORDER/REPARATION
    | @param The ID
    |--------------------------------------------------------------------------
    */
    public function findRepairByID($id)
    {
        $data = array();
        $query = $this->db->get_where('repair', array('id' => $id));
        if ($query->num_rows() > 0) {
            $data = $query->row_array();
        }

        return $data;
    }


    /*
    |--------------------------------------------------------------------------
    | SEND THE SMS TO CUSTOMER
    |--------------------------------------------------------------------------
    */
    public function send_sms($number, $text, $name = '', $model = '', $code = '', $id = '', $manufacturer = '', $grand_total = '')
    {
        $settings = $this->settings_model->getSettings();
        $search  = array('%businessname%', '%customer%', '%model%', '%site_url%', '%statuscode%', '%id%', '%manufacturer%', '%grand_total%');
        $replace = array($settings->title, $name, $model, site_url(), $code, $id, $manufacturer, $grand_total);
        $text = str_replace($search, $replace, $text);

        if($settings->usesms == 1) {
            // IF THAT IS NEXMO //
            try {
                $client = new Nexmo\Client(new Nexmo\Client\Credentials\Basic($settings->nexmo_api_key, $settings->nexmo_api_secret));
                $message = $client->message()->send([
                    'to' => $number,
                    'from' => $settings->phone,
                    'text' => $text,
                ]);
                if ($message['status'] == 0) {
                    return true;
                } else {
                    return false;
                }
            } catch (Exception $e) {
                return FALSE;
            }

        } elseif($settings->usesms == 2) {

            try {
                $client = new Twilio\Rest\Client($settings->twilio_account_sid, $settings->twilio_auth_token);
                $message = $client->messages->create(
                    $number,
                    array(
                        'from' => $settings->twilio_number,
                        'body' => $text,
                    )
                );
            } catch (Exception $e) {
                return FALSE;
            }
            if($message->sid){
                return TRUE;
            }
        } elseif($settings->usesms == 3) {

            try {

                // Configure client
                $config = SMSGatewayMe\Client\Configuration::getDefaultConfiguration();
                $config->setApiKey('Authorization', $settings->smsgateway_token);
                $apiClient = new SMSGatewayMe\Client\ApiClient($config);
                $messageClient = new SMSGatewayMe\Client\Api\MessageApi($apiClient);

                // Sending a SMS Message
                $sendMessageRequest1 = new SMSGatewayMe\Client\Model\SendMessageRequest([
                    'phoneNumber' => $number,
                    'message' => $text,
                    'deviceId' => $settings->smsgateway_device_id
                ]);
               
                $sendMessages = $messageClient->sendMessages([
                    $sendMessageRequest1,
                ]);
                return TRUE;
            } catch (Exception $e) {
                return FALSE;
            }

        } else {

            $api = $this->settings_model->getSMSGatewayByID($settings->default_http_api);

            if ($api) {
                $append = "?";
                $append .= $api->to_name . "=" . $number;
                $append .= "&" . $api->message_name . "=" . $text;

                $postdata = [];
                try {
                    $postdata = @json_decode($api->postdata);
                } catch (Exception $e) {
                    
                }
                foreach ($postdata as $key => $value) {
                    $append .= "&" . $key . "=" . $value;
                }

                $url = $api->url . $append;
                //send sms here
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                $curl_scraped_page = curl_exec($ch);
                curl_close($ch);
                return $curl_scraped_page;
            }
            return false;
        }
    }


    public function change_status($id, $to_status) {
        $sms_result = FALSE;
        $email_result = FALSE;

        if ($to_status < 1) {
            $this->db->update('repair', array('status' => 0), array('id'=>$id)); 
            $returnData = array();
            $returnData['sms_sent'] = $sms_result;
            $returnData['email_sent'] = $email_result;
            $returnData['label'] = 'Cancelled';
            return $returnData;
        }
        
        $repair = $this->findRepairByID($id);
        $manufacturer = $this->settings_model->getModelByID($repair['manufacturer_id']);
        $status_Data = $this->settings_model->getStatusByID($to_status);
        if ($repair['sms'] && $status_Data->send_sms) {
            $msg = $status_Data->sms_text;
            $sms_result = $this->send_sms($repair['telephone'], $msg, $repair['name'], $repair['model_name'], $repair['code'], $id, ($manufacturer ? $manufacturer->name : ''), $repair['grand_total']);
        }
        if ($repair['email'] && $status_Data->send_email) {
            $email = $status_Data->email_text;
            $client_details = $this->getClientNameByID($repair['client_id']);
            $email_result = $this->email_message($client_details->email, sprintf(lang('status_change_email_subject'), $status_Data->label), $email, $repair['name'], $repair['model_name'], $repair['code'], $id,  ($manufacturer ? $manufacturer->name : ''), $this->repairer->formatMoney($repair['grand_total']));
        }
        $data = array(
            'status' => $status_Data->id,
        );
        if ($this->isCompletedStatus($status_Data->id)) {
            $data['date_closing'] = date('Y-m-d H:i:s');
        }else{
            $data['date_closing'] = null;
        }

        $this->db->update('repair', $data, array('id'=>$id)); 
        $returnData = array();
        $returnData['sms_sent'] = $sms_result;
        $returnData['email_sent'] = $email_result;
        $returnData['label'] = $status_Data->label;
        return $returnData;
    }


    public function isCompletedStatus($id) {
        $this->db->where('id', $id);
        $q = $this->db->get('status');
        if ($q->num_rows() > 0) {
            return $q->row()->completed ? true : false;
        }
        return false;
    }


    public function email_message($to, $subject, $text, $name = '', $model = '', $code = '', $id = '', $manufacturer = '', $grand_total = '')
    {
        $settings = $this->settings_model->getSettings();
        $search  = array('%businessname%', '%customer%', '%model%', '%site_url%', '%statuscode%', '%businesscontact%', '%id%', '%manufacturer%', '%grand_total%');
        $replace = array($settings->title, $name, $model, site_url(), $code, $settings->phone,  $id, $manufacturer, $grand_total);
        $text = str_replace($search, $replace, $text);
        return $this->repairer->send_email($to, $subject, $text, $settings->invoice_mail, $settings->title);
    }


    public function getRepairPayments($repair_id)
    {
        $q = $this->db->get_where("payments", array('repair_id' => $repair_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }
    public function syncRepairPayments($id) {
        $sale = $this->getTRepairByID($id);
        if ($payments = $this->getRepairPayments($id)) {
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

            if ($this->db->update('repair', array('paid' => $paid, 'payment_status' => $payment_status), array('id' => $id))) {
                return true;
            }
        } else {
            $payment_status = 'pending';
            if ($this->db->update('repair', array('paid' => 0, 'payment_status' => $payment_status), array('id' => $id))) {
                return true;
            }
        }
        return FALSE;
    }

    public function getPaymentByID($id)
    {
        $q = $this->db->get_where('payments', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPaymentByRepairID($id)
    {
        $q = $this->db->get_where('payments', array('repair_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPaymentsByRepairID($id) {
        $q = $this->db->get_where('payments', array('repair_id' => $id));
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
    }

    
    public function deletePayment($id)
    {
        $opay = $this->getPaymentByID($id);
        if ($this->db->delete('payments', array('id' => $id))) {
            $repair = $this->getTRepairByID($data['repair_id']);
            $this->syncRepairPayments($opay->repair_id);
            $this->settings_model->addLog('delete-payment', 'reparation', $opay->repair_id, json_encode(array(
                'data'=>$repair,
            )));
            return true;
        }
        return FALSE;
    }

    public function addPayment($data = array())
    {
        unset($data['cc_cvv2']);
        if ($this->db->insert('payments', $data)) {
            $payment_id = $this->db->insert_id();
            $repair = $this->getTRepairByID($data['repair_id']);
            if ($this->repairer->getReference('pay') == $data['reference_no']) {
                $this->repairer->updateReference('pay');
            }
            $this->syncRepairPayments($data['repair_id']);

            $this->settings_model->addLog('add-payment', 'repair', $data['repair_id'], json_encode(array(
                'data'=>$repair,
            )));

            return true;
        }
        return false;
    }

    public function updatePayment($id, $data = array())
    {
        $opay = $this->getPaymentByID($id);
        if ($this->db->update('payments', $data, array('id' => $id))) {
            $repair = $this->getTRepairByID($data['repair_id']);
            $this->syncRepairPayments($data['repair_id']);

            $this->settings_model->addLog('update-payment', 'repair', $data['repair_id'], json_encode(array(
                'data'=>$repair,
            )));

            return true;
        }
        return false;
    }


    public function getTRepairByID($id)
    {
        $data = array();
        $query = $this->db->get_where('repair', array('id' => $id));
        if ($query->num_rows() > 0) {
            $data = $query->row();
        }

        return $data;
    }


    public function getRepairPosInvoice($id)
    {
        $data = array();
        $query = $this->db->get_where('sale_items', array('product_id' => $id, 'item_type'=>'drepairs'));
        if ($query->num_rows() > 0) {
            return $data = $query->row();
        }

        return false;
    }
}
