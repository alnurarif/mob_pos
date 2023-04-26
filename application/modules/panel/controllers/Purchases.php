<?php defined('BASEPATH') or exit('No direct script access allowed');

class Purchases extends Auth_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('repairer');
        $this->load->model('purchases_model');
        $this->digital_upload_path = 'files/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->data['logo'] = true;
    }

    /* ------------------------------------------------------------------------- */

    public function index($status = '')
    {
        $this->repairer->checkPermissions();
        if ($status == 'ordered' || $status == 'pending' || $status == 'received') {
            $this->data['status_view'] = $status;
        }else{
            $this->data['status_view'] = '';
        }
        $this->render('purchases/index');
    }
    public function recieve($id)
    {
        if (!is_numeric($id)) {
            redirect('purchases');
        }
        $this->data['inv'] = $this->purchases_model->getPurchaseByID($id);
        $this->data['inv_items'] = $this->purchases_model->getAllPurchaseItems($id);
        $this->render('purchases/recieve');
    }
    public function recieved($is_serialized)
    {
        $item_id = $this->input->post('id');
        $purchase_id = $this->input->post('purchase_id');
        $serial_number = $this->input->post('serial_number');
        $cost = $this->input->post('cost');
        $price = $this->input->post('price');
        $item = $this->purchases_model->getPurchaseItemByID($item_id);
        
        if ($item->stock_type == 'used_phone') {
            $data = array(
                'phone_id' => $item->product_id,
                'cost' => $cost,
                'price' => $price,
                'imei' => (int)$is_serialized == 1 ? $serial_number : NULL,
            );
            $this->db->insert('phone_items', $data);
        }else{
            $data = array(
                'inventory_id' => $item->product_id,
                'inventory_type' => $item->stock_type == 'new_phone' ? 'phones' : $item->stock_type,
                'price' => $item->unit_cost,
                'modified_date' => date('Y-m-d H:i:s'),
                'store_id' => (int)$this->session->userdata('active_store'),
                'serial_number'=>(int)$is_serialized == 1 ? $serial_number : NULL,
                'in_state_of_transfer' => 0,
            );
            $this->db->insert('stock', $data);
        }

        $id = json_encode($this->db->insert_id());
        $this->db->update('purchase_items', array('recieved'=>1, 'received_stock_id'=>$id, 'date'=> date('Y-m-d H:i:s')), array('id'=>$item_id));

        $item_statuses = $this->purchases_model->purchaseStatusChange($purchase_id, TRUE);
        $status = 'ordered';
        if ($item_statuses[0] == 0) {
            $status = 'received';
        }
        if ($item_statuses[1] > 0) {
            $status = 'pending';
        }
        // Update Status of the Purchase
        $this->db->update(
            'purchases',
            array(
                'status'=>$status
            ),
            array(
                'id'=>$purchase_id
            )
        );

        echo "true";
    }
    
    public function getPurchases($status = '')
    {

        $user = $this->ion_auth->get_user_id();
        $email_link = anchor('panel/purchases/email/$1', '<i class="fas fa-envelope"></i> ' . lang('email_purchase'), 'data-toggle="modal" data-target="#myModal"');
        $return_link = '';
        if ($this->Admin || $this->GP['purchases-return_purchase']) {
            $return_link = anchor('panel/purchases/return_purchase/$1', '<i class="fas fa-angle-double-left"></i>' . lang('Return Purchase'));
        }
        $view_link = anchor('panel/purchases/view/$1', '<i class="fas fa-eye"></i> ' .
            lang('View Purchase'));
        $edit_link = '';
        $recieve_purchase = anchor('panel/purchases/recieve/$1', '<i class="fas fa-edit"></i> ' . lang('Recieve purchase'));

        if ($this->Admin || $this->GP['purchases-edit']) {
            $edit_link = anchor('panel/purchases/edit/$1', '<i class="fas fa-edit"></i> ' . lang('edit_purchase'));
        }
        $pdf_link = anchor('panel/purchases/pdf/$1', '<i class="fas fa-file-pdf"></i> ' . lang('Download PDF'));
        $print_barcode = anchor('panel/inventory/print_barcodes/?purchase=$1', '<i class="fas fa-print"></i>' . lang('print_barcode_label'));
        $delete_link = '';
        if ($this->Admin || $this->GP['purchases-delete']) {
            $delete_link = "<a href='#' class='po' title='<b>" . lang('delete_purchase') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('panel/purchases/delete/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fas fa-trash\"></i> "
            . lang('delete_purchase') . "</a>";
        }

        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . "Actions" . ' <span class="caret"></span></button>
            <ul class="dropdown-menu pull-right" role="menu">
                <li>' . $view_link . '</li>
                <li>' . $edit_link . '</li>
                <li>' . $recieve_purchase . '</li>
                <li>' . $return_link . '</li>
                <li>' . $pdf_link . '</li>
                <li>' . $delete_link . '</li>
                <li>' . $email_link . '</li>
            </ul>
        </div></div>';
        $this->load->library('datatables');
        $this->datatables
            ->select("id, track_code, CONCAT(UPPER(provider),' - ', UPPER(track_code)) as trackit ,date as date, reference_no, supplier, CONCAT(status, '__', IFNULL(return_status, '')), grand_total, attachment")
            ->edit_column('trackit', '$1____$2', 'trackit, track_code')
            ->where('store_id', $this->activeStore)
            ->from('purchases');

        if ($status == 'ordered' || $status == 'pending' || $status == 'received') {
            $this->datatables->where('status', $status);
        }
        $this->datatables->add_column("Actions", $action, "id");
        $this->datatables->unset_column("track_code");
        echo $this->datatables->generate();
    }

    public function email($purchase_id = null)
        {
            if ($this->input->get('id')) {
                $purchase_id = $this->input->get('id');
            }
            $inv = $this->purchases_model->getPurchaseByID($purchase_id);
            $this->form_validation->set_rules('to', $this->lang->line("to") . " " . $this->lang->line("email"), 'trim|required|valid_email');
            $this->form_validation->set_rules('subject', $this->lang->line("subject"), 'trim|required');
            $this->form_validation->set_rules('cc', $this->lang->line("cc"), 'trim|valid_emails');
            $this->form_validation->set_rules('bcc', $this->lang->line("bcc"), 'trim|valid_emails');
            $this->form_validation->set_rules('note', $this->lang->line("message"), 'trim');

            if ($this->form_validation->run() == true) {
                $to = $this->input->post('to');
                $subject = $this->input->post('subject');
                if ($this->input->post('cc')) {
                    $cc = $this->input->post('cc');
                } else {
                    $cc = null;
                }
                if ($this->input->post('bcc')) {
                    $bcc = $this->input->post('bcc');
                } else {
                    $bcc = null;
                }
                $supplier = $this->purchases_model->getCompanyByID($inv->supplier_id);
                $this->load->library('parser');
                $parse_data = array(
                    'reference_number' => $inv->reference_no,
                    'contact_person' => $supplier->name,
                    'company' => $supplier->company,
                    'site_link' => base_url(),
                    'site_name' => $this->mSettings->title,
                    'logo' => '<img src="' . base_url() . 'assets/uploads/logos/' . $this->mSettings->logo . '" alt="' . $this->mSettings->title . '"/>',
                );
                $msg = $this->input->post('note');
                $message = $this->parser->parse_string($msg, $parse_data);
                $attachment = $this->pdf($purchase_id, null, 'S');
            } elseif ($this->input->post('send_email')) {
                $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                $this->session->set_flashdata('error', $this->data['error']);
                redirect($_SERVER["HTTP_REFERER"]);
            }
            if ($this->form_validation->run() == true && $this->repairer->send_email($to, $subject, $message, null, null, $attachment, $cc, $bcc)) {
                delete_files($attachment);
                $this->db->update('purchases', array('status' => 'ordered'), array('id' => $purchase_id));
                $this->session->set_flashdata('message', lang('email_sent'));
                redirect("panel/purchases");
            } else {
                $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                if (file_exists(APPPATH.'views/email_templates/purchase.html')) {
                    $purchase_temp = file_get_contents(APPPATH.'views/email_templates/purchase.html');
                } else {
                    $purchase_temp = '<h3>{logo}</h3><h4>Purchase Details</h4><p>Hello {contact_person} ({company}),</p><p>Please find the attachment for our purchase order ({reference_number}) details.</p><p>Best regards,<br>{site_name}</p>';
                }
                $this->data['subject'] = array('name' => 'subject',
                    'id' => 'subject',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('subject', lang('purchase_order').' (' . $inv->reference_no . ') '.lang('from').' ' . $this->mSettings->title),
                );
                $this->data['note'] = array('name' => 'note',
                    'id' => 'note',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('note', $purchase_temp),
                );
                $this->data['supplier'] = $this->purchases_model->getCompanyByID($inv->supplier_id);
                $this->data['id'] = $purchase_id;
                $this->load->view($this->theme.'purchases/email', $this->data);
            }
        }
    /* ----------------------------------------------------------------------------- */

    public function modal_view($purchase_id = null)
    {
        $this->repairer->checkPermissions('index');

        if ($this->input->get('id')) {
            $purchase_id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->purchases_model->getPurchaseByID($purchase_id);

        $this->data['rows'] = $this->purchases_model->getAllPurchaseItems2($purchase_id);
        $this->data['supplier'] = $this->purchases_model->getCompanyByID($inv->supplier_id);
        $this->data['inv'] = $inv;
        $this->data['created_by'] = $this->purchases_model->getUser($inv->created_by);
        $this->data['updated_by'] = $inv->updated_by ? $this->purchases_model->getUser($inv->updated_by) : null;
        $this->data['Settings'] = $this->mSettings;
        $this->data['return_purchase'] = $inv->return_id ? $this->purchases_model->getPurchaseByID($inv->return_id) : NULL;
        $this->data['return_rows'] = $inv->return_id ? $this->purchases_model->getAllPurchaseItems($inv->return_id) : NULL;
        $this->load->view($this->theme.'purchases/modal_view', $this->data);

    }


    public function customer_modal_view($id = null)
    {
        $this->load->model('repair_model');
        $this->repairer->checkPermissions('index');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $inv = $this->getCustomerPurchaseByID($id);
        $this->data['created_by'] = $this->purchases_model->getUser($inv->created_by);
        $this->data['Settings'] = $this->mSettings;
        $this->data['manufacturers'] = $this->settings_model->getManufacturers();
        $this->data['carriers'] = $this->settings_model->getCarriers();
        $this->data['client'] = $this->settings_model->getCustomerByID($inv->customer_id);
        $this->data['tax_rates'] = $this->settings_model->getTaxRates();
        $this->data['inv'] = $inv;
        $this->data['inv_items'] = $this->getCustomerPurchaseItemsByID($id);
        $this->load->view($this->theme.'purchases/customer/modal_view', $this->data);

    }


    //generate pdf and force to download
    public function customer_pdf($id = null, $view = null, $save_bufffer = null)
    {

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        
        $inv = $this->getCustomerPurchaseByID($id);
        $this->data['created_by'] = $this->purchases_model->getUser($inv->created_by);
        $this->data['Settings'] = $this->mSettings;
        $this->data['manufacturers'] = $this->settings_model->getManufacturers();
        $this->data['carriers'] = $this->settings_model->getCarriers();
        $this->data['client'] = $this->settings_model->getCustomerByID($inv->customer_id);
        $this->data['tax_rates'] = $this->settings_model->getTaxRates();
        $this->data['inv'] = $inv;
        $this->data['inv_items'] = $this->getCustomerPurchaseItemsByID($id);


        $name = $this->lang->line("purchase") . "_" . str_replace('/', '_', $inv->reference_no) . ".pdf";
        $html = $this->load->view($this->theme.'purchases/customer/pdf', $this->data, true);
        $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        if ($view) {
            $this->load->view($this->theme.'purchases/customer/pdf', $this->data);
        } elseif ($save_bufffer) {
            return $this->repairer->generate_pdf($html, $name, $save_bufffer);
        } else {
            $this->repairer->generate_pdf($html, $name);
        }

    }


    /* ----------------------------------------------------------------------------- */

    //generate pdf and force to download
    public function pdf($purchase_id = null, $view = null, $save_bufffer = null)
    {

        if ($this->input->get('id')) {
            $purchase_id = $this->input->get('id');
        }
        $inv = $this->purchases_model->getPurchaseByID($purchase_id);

        $this->data['rows'] = $this->purchases_model->getAllPurchaseItems($purchase_id);
        $this->data['supplier'] = $this->purchases_model->getCompanyByID($inv->supplier_id);
        $this->data['inv'] = $inv;
        $this->data['created_by'] = $this->purchases_model->getUser($inv->created_by);
        $this->data['updated_by'] = $inv->updated_by ? $this->purchases_model->getUser($inv->updated_by) : null;
        $this->data['Settings'] = $this->mSettings;
        $this->data['return_purchase'] = $inv->return_id ? $this->purchases_model->getPurchaseByID($inv->return_id) : NULL;
        $this->data['return_rows'] = $inv->return_id ? $this->purchases_model->getAllPurchaseItems($inv->return_id) : NULL;
        $name = $this->lang->line("purchase") . "_" . str_replace('/', '_', $inv->reference_no) . ".pdf";
        $html = $this->load->view($this->theme.'purchases/pdf', $this->data, true);
        $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        if ($view) {
            $this->load->view($this->theme.'purchases/pdf', $this->data);
        } elseif ($save_bufffer) {
            return $this->repairer->generate_pdf($html, $name, $save_bufffer);
        } else {
            $this->repairer->generate_pdf($html, $name);
        }

    }
    public function view($purchase_id = null)
    {
        $this->repairer->checkPermissions('index');

        if ($this->input->get('id')) {
            $purchase_id = $this->input->get('id');
        }
        $inv = $this->purchases_model->getPurchaseByID($purchase_id);

        $this->data['rows'] = $this->purchases_model->getAllPurchaseItems2($purchase_id);
        $this->data['supplier'] = $this->purchases_model->getCompanyByID($inv->supplier_id);
        $this->data['inv'] = $inv;
        $this->data['created_by'] = $this->purchases_model->getUser($inv->created_by);
        $this->data['updated_by'] = $inv->updated_by ? $this->purchases_model->getUser($inv->updated_by) : null;
        $this->data['Settings'] = $this->mSettings;
        $this->data['return_purchase'] = $inv->return_id ? $this->purchases_model->getPurchaseByID($inv->return_id) : NULL;
        $this->data['return_rows'] = $inv->return_id ? $this->purchases_model->getAllPurchaseItems($inv->return_id) : NULL;

        $this->render('purchases/view');
    }

    /* -------------------------------------------------------------------------------------------------------------------------------- */

    public function add()
    {
        $this->repairer->checkPermissions();

        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
        $this->form_validation->set_rules('posupplier', $this->lang->line("supplier"), 'required');

        $this->session->unset_userdata('csrf_token');
        if ($this->form_validation->run() == true) {

            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') :  $this->repairer->getReference('po');


            $date = $this->repairer->fld(trim($this->input->post('date')));
            $supplier_id = $this->input->post('posupplier');
            $status = $this->input->post('status');

            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $supplier_details = $this->purchases_model->getCompanyByID($supplier_id);
            $supplier = $supplier_details->company != '-'  ? $supplier_details->company : $supplier_details->name;
            $note = $this->repairer->clear_tags($this->input->post('note'));
            $track_code = $this->input->post('track_code');
            $localStorage = $this->input->post('poitems');
            $phone_items = $this->input->post('phone_items');
            if ($this->input->post('shipping_provider') === 'other') {
                $provider = $this->input->post('provider_input');
            }else{
                $provider = $this->input->post('shipping_provider');
            }
            $total = 0;
            $product_tax = 0;
            $order_tax = 0;
            $product_discount = 0;
            $order_discount = 0;
            $percentage = '%';
            $i = sizeof($_POST['product']);
            for ($r = 0; $r < $i; $r++) {
                $item_code = $_POST['product'][$r];
                $item_net_cost = ($_POST['net_cost'][$r]);
                $unit_cost = ($_POST['unit_cost'][$r]);
                $item_tax_rate = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : null;
                $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : null;
                $item_quantity = $_POST['quantity'][$r];
                $item_id = $_POST['product_id'][$r];
                $item_type = $_POST['product_type'][$r];
                $is_serialized = $_POST['is_serialized'][$r];

                if (isset($item_code) && isset($unit_cost) && isset($item_quantity)) {
                    $product_details = $this->purchases_model->getProductByID($item_id, $item_type);
                    // print_r($product_details);
                    $pr_discount = 0;
                    if (isset($item_discount)) {
                        $discount = $item_discount;
                        $dpos = strpos($discount, $percentage);
                        if ($dpos !== false) {
                            $pds = explode("%", $discount);
                            $pr_discount = ((($unit_cost) * (Float) ($pds[0])) / 100);
                        } else {
                            $pr_discount = ($discount);
                        }
                    }

                    $unit_cost = ($unit_cost - $pr_discount);
                    $item_net_cost = $unit_cost;
                    $pr_item_discount = ($pr_discount * $item_quantity);
                    $product_discount += $pr_item_discount;
                    $pr_tax = 0;
                    $pr_item_tax = 0;
                    $item_tax = 0;
                    $tax = '';
                    $item_tax_rate = json_decode(urldecode($item_tax_rate));
                    $product_tax += $pr_item_tax;
                    $subtotal = (($item_net_cost * $item_quantity) + $pr_item_tax);
                    for ($q=0; $q < $item_quantity; $q++) { 
                        $products[] = array(
                            'product_id' => $product_details->id,
                            'product_code' => $item_code,
                            'product_name' => $product_details->name,
                            'net_unit_cost' => $item_net_cost,
                            'unit_cost' => ($item_net_cost + $item_tax),
                            'quantity' => 1,
                            'quantity_balance' => 1,
                            'item_tax' => $pr_item_tax,
                            'tax_rate_id' => $pr_tax,
                            'tax' => $tax,
                            'discount' => $item_discount,
                            'item_discount' => $item_discount,
                            'subtotal' => ($item_net_cost),
                            'date' => date('Y-m-d', strtotime($date)),
                            'status' => $status,
                            'stock_type' => $item_type,
                            'store_id' => $this->activeStore,
                            'is_serialized' => $is_serialized,
                        );
                    }
                   
                    $total += (($item_net_cost * $item_quantity));
                }
               
            }
            
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }

            if ($this->input->post('discount')) {
                $order_discount_id = $this->input->post('discount');
                $opos = strpos($order_discount_id, $percentage);
                if ($opos !== false) {
                    $ods = explode("%", $order_discount_id);
                    $order_discount = (((($total + $product_tax) * (Float) ($ods[0])) / 100));

                } else {
                    $order_discount = ($order_discount_id);
                }
            } else {
                $order_discount_id = null;
            }
            $total_discount = ($order_discount + $product_discount);

         
            if ($this->input->post('order_tax')) {
                $order_tax_value = $this->input->post('order_tax');
                $opot = strpos($order_tax_value, '%');
                if ($opot !== false) {
                    $odt = explode("%", $order_tax_value);
                    $order_tax = (((($total + $product_tax - $order_discount) * (Float)($odt[0])) / 100));
                } else {
                    $order_tax = ($order_tax_value);
                }
            }
            $order_tax_id = null;


            $total_tax = (($product_tax + $order_tax));
            $grand_total = (($total + $total_tax + $this->repairer->formatDecimal($shipping) - $order_discount));
            $data = array('reference_no' => $reference,
                'date' => $date,
                'supplier_id' => $supplier_id,
                'supplier' => $supplier,
                'note' => $note,
                'total' => $total,
                'product_discount' => $product_discount,
                'order_discount_id' => $order_discount_id,
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $product_tax,
                'order_tax_id' => $order_tax_id,
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'shipping' => ($shipping),
                'grand_total' => $grand_total,
                'status' => $status,
                'created_by' => $this->session->userdata('user_id'),
                'localstorage' => $localStorage,
                'track_code' => $track_code,
                'provider' => $provider,
                'date_recieved' => NULL,
                'date_verified' => NULL,
                'date_ordered' => NULL,
                'store_id' => $this->activeStore,
            );
            // ordered - pending - recieved
            if ($status == 'ordered') {
                $data['date_ordered'] = date('Y-m-d H:i:s');
            }elseif ($status == 'pending') {
                $data['date_recieved'] = date('Y-m-d H:i:s');
            }elseif($status == 'received') {
                $data['date_verified'] = date('Y-m-d H:i:s');
            }

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = false;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }


        }

        if ($this->form_validation->run() == true && $p = $this->purchases_model->addPurchase($data, $products)) {
            echo validation_errors();
            $this->admin_email($p);
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', lang("purchase_added"));
            redirect('panel/purchases');
        }else{
            $this->data['ponumber'] =  $this->repairer->getReference('po');

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['suppliers'] = $this->purchases_model->getAllCompanies('supplier');
            $this->data['tax_rates'] = $this->purchases_model->getAllTaxRates();
            $this->data['manufacturers'] = $this->settings_model->getManufacturers();
            $this->data['carriers'] = $this->settings_model->getCarriers();
            $this->data['categories'] = $this->settings_model->getAllCategories();
            $this->data['subcategories'] = $this->settings_model->getAllCategories(FALSE);
            
            $this->data['mand_inventory'] = $this->settings_model->getMandatory('repair_items');
            $this->data['mand_nphone'] = $this->settings_model->getMandatory('new_phones');
            $this->data['mand_uphone'] = $this->settings_model->getMandatory('used_phones');
            $this->data['mand_acc'] = $this->settings_model->getMandatory('accessory');
            $this->data['warranty_plans'] = $this->settings_model->getAllWarranties();

            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $this->render('purchases/add');
        }
    }

    public function ship_complete() {
        $id = $this->input->post('id');
        $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
        $track_code = $this->input->post('track_code');
        if ($this->input->post('shipping_provider') === 'other') {
            $provider = $this->input->post('provider_input');
        }else{
            $provider = $this->input->post('shipping_provider');
        }
        $data = array(
            'shipping' => $shipping,
            'track_code' => $track_code,
            'provider' => $provider,
            'return_status' => 2,
        );
        $this->db->where('id', $id);
        $this->db->update('purchases', $data);
        echo json_encode(array('success'=>true));
    }
    public function return_complete() {
        $id = $this->input->post('id');
        $data = array(
            'return_status' => 3,
        );
        $this->db->where('id', $id);
        $this->db->update('purchases', $data);
        echo json_encode(array('success'=>true));
    }

    /* ------------------------------------------------------------------------------------- */

    public function edit($id = null)
    {
        $this->repairer->checkPermissions();
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $q = $this->db->get_where(
            'purchase_items', 
            array(
                'purchase_id'   => $id, 
                'recieved'      => 1
            )
        );
        if($q->num_rows() > 0){
            $this->session->set_flashdata('error', lang('received_purchase_non_editable'));
            redirect('panel/purchases');
        }

        $inv = $this->purchases_model->getPurchaseByID($id);
        if ($inv->status == 'received') {
            $this->session->set_flashdata('error', lang('received_purchase_non_editable'));
            redirect('panel/purchases');
        }
        if ($inv->status == 'returned') {
            $this->session->set_flashdata('error', lang('returned_purchase_non_editable'));
            redirect('panel/purchases');
        }
        $p_status = $inv->status;
        $this->form_validation->set_message('is_natural_no_zero', lang("no_zero_required"));
        $this->form_validation->set_rules('reference_no', lang("ref_no"), 'required');
        $this->form_validation->set_rules('posupplier', lang("supplier"), 'required');

        $this->session->unset_userdata('csrf_token');
        if ($this->form_validation->run() == true) {

            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') :  $this->repairer->getReference('po');
            $date = $this->repairer->fld(trim($this->input->post('date')));

            $supplier_id = $this->input->post('posupplier');
            $status = $this->input->post('status');
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $supplier_details = $this->purchases_model->getCompanyByID($supplier_id);
            $supplier = $supplier_details->company != '-'  ? $supplier_details->company : $supplier_details->name;
            $note = $this->repairer->clear_tags($this->input->post('note'));
            $track_code = $this->input->post('track_code');
            $localStorage = $this->input->post('poitems');
            $phone_items = $this->input->post('phone_items');
            if ($this->input->post('shipping_provider') === 'other') {
                $provider = $this->input->post('provider_input');
            }else{
                $provider = $this->input->post('shipping_provider');
            }

            $total = 0;
            $product_tax = 0;
            $order_tax = 0;
            $product_discount = 0;
            $order_discount = 0;
            $percentage = '%';
            $i = sizeof($_POST['product']);
            for ($r = 0; $r < $i; $r++) {

                $item_code = $_POST['product'][$r];
                $item_net_cost = ($_POST['net_cost'][$r]);
                $unit_cost = ($_POST['unit_cost'][$r]);
                $item_tax_rate = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : null;
                $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : null;
                $item_quantity = $_POST['quantity'][$r];
                $item_id = $_POST['product_id'][$r];
                $item_type = $_POST['product_type'][$r];
                $is_serialized = $_POST['is_serialized'][$r];

                if (isset($item_code) && isset($unit_cost) && isset($item_quantity)) {
                    $product_details = $this->purchases_model->getProductByID($item_id, $item_type);
                    if (!$product_details) {
                        $this->session->set_flashdata('warning', "Item in the purchase with Code".$item_code." is deleted. Please enable it or remove it from the purchase.");
                        redirect('panel/purchases/edit/'.$id);

                    }
                    $pr_discount = 0;
                    if (isset($item_discount)) {
                        $discount = $item_discount;
                        $dpos = strpos($discount, $percentage);
                        if ($dpos !== false) {
                            $pds = explode("%", $discount);
                            $pr_discount = ((($unit_cost) * (Float) ($pds[0])) / 100);
                        } else {
                            $pr_discount = ($discount);
                        }
                    }

                    $unit_cost = ($unit_cost - $pr_discount);
                    $item_net_cost = $unit_cost;
                    $pr_item_discount = ($pr_discount * $item_quantity);
                    $product_discount += $pr_item_discount;
                    $pr_tax = 0;
                    $pr_item_tax = 0;
                    $item_tax = 0;
                    $tax = '';
                    $item_tax_rate = json_decode(urldecode($item_tax_rate));
                    $product_tax += $pr_item_tax;
                    $subtotal = (($item_net_cost * $item_quantity) + $pr_item_tax);
                    for ($q=0; $q < $item_quantity; $q++) { 
                        $products[] = array(
                            'product_id' => $product_details->id,
                            'product_code' => $item_code,
                            'product_name' => $product_details->name,
                            'net_unit_cost' => $item_net_cost,
                            'unit_cost' => ($item_net_cost + $item_tax),
                            'quantity' => 1,
                            'quantity_balance' => 1,
                            'item_tax' => $pr_item_tax,
                            'tax_rate_id' => $pr_tax,
                            'tax' => $tax,
                            'discount' => $item_discount,
                            'item_discount' => $item_discount,
                            'subtotal' => ($item_net_cost),
                            'date' => date('Y-m-d', strtotime($date)),
                            'status' => $status,
                            'stock_type' => $item_type,
                            'store_id' => $this->activeStore,
                            'is_serialized' => $is_serialized,
                        );
                    }
                    $total += (($item_net_cost * $item_quantity));
                }
            }

            if (empty($products)) {
                $this->form_validation->set_rules('product', ("order_items"), 'required');
            } else {
                krsort($products);
            }

            if ($this->input->post('discount')) {
                $order_discount_id = $this->input->post('discount');
                $opos = strpos($order_discount_id, $percentage);
                if ($opos !== false) {
                    $ods = explode("%", $order_discount_id);
                    $order_discount = (((($total + $product_tax) * (Float) ($ods[0])) / 100));

                } else {
                    $order_discount = ($order_discount_id);
                }
            } else {
                $order_discount_id = null;
            }
            $total_discount = ($order_discount + $product_discount);


            if ($this->input->post('order_tax')) {
                $order_tax_value = $this->input->post('order_tax');
                $opot = strpos($order_tax_value, '%');
                if ($opot !== false) {
                    $odt = explode("%", $order_tax_value);
                    $order_tax = (((($total + $product_tax - $order_discount) * (Float)($odt[0])) / 100));
                } else {
                    $order_tax = ($order_tax_value);
                }
            }
            $order_tax_value = null;


            $total_tax = (($product_tax + $order_tax));
            $grand_total = (($total + $total_tax + $this->repairer->formatDecimal($shipping) - $order_discount));
            $data = array('reference_no' => $reference,
                'date' => $date,
                'supplier_id' => $supplier_id,
                'supplier' => $supplier,
                'note' => $note,
                'total' => $total,
                'product_discount' => $product_discount,
                'order_discount_id' => $order_discount_id,
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $product_tax,
                'order_tax_id' => null,
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'shipping' => ($shipping),
                'grand_total' => $grand_total,
                'status' => $status,
                'created_by' => $this->session->userdata('user_id'),
                'localstorage' => $localStorage,
                'provider' => $provider,
                'track_code' => $track_code,
                'store_id' => $this->activeStore,

            );
            // ordered - pending - recieved
            if (!($p_status == $status)) {
                if($status == 'ordered') {
                    $data['date_ordered'] = date('Y-m-d H:i:s');
                }elseif ($status == 'pending') {
                    $data['date_recieved'] = date('Y-m-d H:i:s');
                }elseif($status == 'received') {
                    $data['date_verified'] = date('Y-m-d H:i:s');
                }else{
                    $data['date_recieved']  = NULL;
                    $data['date_verified']  = NULL;
                    $data['date_ordered']   = NULL;
                }
            }

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = false;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }
        }

        if ($this->form_validation->run() == true && $this->purchases_model->updatePurchase($id, $data, $products)) {
            $this->admin_email($id);
            $this->session->set_userdata('remove_pols', 1);
            $this->session->set_flashdata('message', lang('purchase_updated'));
            redirect('panel/purchases');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['inv'] = $inv;

            $this->data['suppliers'] = $this->purchases_model->getAllCompanies('supplier');
            $this->data['tax_rates'] = $this->purchases_model->getAllTaxRates();
            $this->data['manufacturers'] = $this->settings_model->getManufacturers();
            $this->data['carriers'] = $this->settings_model->getCarriers();
            $this->data['categories'] = $this->settings_model->getAllCategories();
            $this->data['subcategories'] = $this->settings_model->getAllCategories(FALSE);
            $this->data['mand_inventory'] = $this->settings_model->getMandatory('repair_items');
            $this->data['mand_nphone'] = $this->settings_model->getMandatory('new_phones');
            $this->data['mand_uphone'] = $this->settings_model->getMandatory('used_phones');
            $this->data['mand_acc'] = $this->settings_model->getMandatory('accessory');
            $this->data['warranty_plans'] = $this->settings_model->getAllWarranties();

            $this->data['id'] = $id;
            $this->data['inv_items'] = $this->data['inv']->localstorage;
            $this->data['purchase'] = $this->purchases_model->getPurchaseByID($id);
            $this->load->helper('string');
            $value = random_string('alnum', 20);
            $this->session->set_userdata('user_csrf', $value);
            $this->session->set_userdata('remove_pols', 1);
            $this->data['csrf'] = $this->session->userdata('user_csrf');
            $this->render('purchases/edit');
        }
    }

    /* --------------------------------------------------------------------------- */

    public function delete($id = null)
    {
        $this->repairer->checkPermissions();

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->purchases_model->deletePurchase($id)) {
            if ($this->input->is_ajax_request()) {
                echo 'purchase deleted';die();
            }
            $this->session->set_flashdata('message', lang('purchase_deleted'));
            redirect('panel/purchases');
        }
    }

    /* --------------------------------------------------------------------------- */
    function getSupplier($id = NULL)
    {
        $this->db->select("id, (CASE WHEN company = '-' THEN name ELSE CONCAT(company, ' (', name, ')') END) as text", FALSE);
        $this->db->where('(universal=1 OR store_id='.$this->activeStore.')', NULL, FALSE);
        $q = $this->db->get_where('suppliers', array('id' => $id));
        $row = $q->row();
        $this->repairer->send_json((array('id' => $row->id, 'text' => $row->text)));
    }

    function supplier_suggestions($term = NULL, $limit = NULL)
    {
        if ($this->input->get('term')) {
            $term = $this->input->get('term', TRUE);
            $term = $term['term'];
        }
        $limit = $this->input->get('limit', TRUE);
        $rows['results'] = $this->purchases_model->getSupplierSuggestions($term, $limit);
        $this->repairer->send_json($rows);
    }

    public function suggestions()
    {
        $term = $this->input->get('term', true);
        $supplier_id = $this->input->get('supplier_id', true);

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . site_url('welcome') . "'; }, 10);</script>");
        }
        $rows = $this->purchases_model->getPurchaseProductNames($term);
        $rows = array_filter((array)$rows);
        if ($rows) {
            $c = str_replace(".", "", microtime(true));
            $r = 0;

            foreach ($rows as $row) {
                $pr[] = array(
                    'item_id' => ($c + $r),
                    'label' => $row->name . " (" . $row->code . ")",
                    'row' => $row,
                    'pr_tax' => NULL,
                );
                $r++;
            }
            $this->repairer->send_json($pr);
        } else {
            $this->repairer->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    /* -------------------------------------------------------------------------------- */

    public function purchase_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {

                    foreach ($_POST['val'] as $id) {
                        $this->purchases_model->deletePurchase($id);
                    }

                    $this->session->set_flashdata('message', $this->lang->line("purchases_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();

                    $sheet->setTitle(lang("Vendor Purchases Report"));
                    $sheet->SetCellValue('A2', lang("date"));
                    $sheet->SetCellValue('B2', lang('reference_no'));
                    $sheet->SetCellValue('C2', lang('Supplier'));
                    $sheet->SetCellValue('D2', lang('status'));
                    $sheet->SetCellValue('E2', lang('grand_total'));
                    

                    $sheet->SetCellValue('A1', "Vendor Purchases report");
                    $sheet->mergeCells('A1:E1');
                    $row = 3;
                    $total = 0;
                    foreach ($_POST['val'] as $id) {
                        $ir = $row + 1;
                        if ($ir % 2 == 0) {
                            $style_header = array(                  
                                'fill' => array(
                                    'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                    'color' => array('rgb'=>'CCCCCC'),
                                ),
                            );
                            $sheet->getStyle("A$row:E$row")->applyFromArray( $style_header );
                        }
                        $purchase = $this->purchases_model->getPurchaseByID($id);
                        $sheet->SetCellValue('A' . $row, $this->repairer->hrld($purchase->date));
                        $sheet->SetCellValue('B' . $row, $purchase->reference_no);
                        $sheet->SetCellValue('C' . $row, $purchase->supplier);
                        $sheet->SetCellValue('D' . $row, $purchase->status);
                        $sheet->SetCellValue('E' . $row, $this->repairer->formatMoney($purchase->grand_total));
                        $total += $purchase->grand_total;

                        $row++;
                    }
                    $sheet->getColumnDimension('A')->setWidth(30);
                    $sheet->getColumnDimension('B')->setWidth(20);
                    $sheet->getColumnDimension('C')->setWidth(25);
                    $sheet->getColumnDimension('D')->setWidth(25);
                    $sheet->getColumnDimension('E')->setWidth(15);
                    

                     $header = 'A1:E1';
                    $sheet->getStyle($header)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('94ce58');
                    $style = array(
                        'font' => array('bold' => true,),
                        'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,),
                    );
                    $sheet->getStyle($header)->applyFromArray($style);
                    

                    $header = 'A2:E2';
                    $sheet->getStyle($header)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('fdbf2d');
                    $style = array(
                        'font' => array('bold' => true,),
                        'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_GENERAL,),
                    );
                    $sheet->getStyle($header)->applyFromArray($style);


                    $header = 'A'.$row.':E'.$row;
                    $sheet->getStyle($header)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('fdbf2d');
                    $style = array(
                        'font' => array('bold' => true,),
                        'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_GENERAL,),
                    );
                    $sheet->getStyle($header)->applyFromArray($style);
                    
                    $sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $filename = 'purchases_' . date('Y_m_d_H_i_s');
                    

                        
                        if ($this->input->post('form_action') == 'export_excel') {
                            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                            header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
                            header('Cache-Control: max-age=0');

                            $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
                            $writer->save('php://output');
                            exit();
                        }

                        if ($this->input->post('form_action') == 'export_pdf') {
                            $styleArray = [
                                'borders' => [
                                    'allBorders' => [
                                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                        'color' => ['argb' => 'FFFF0000'],
                                    ],
                                ],
                            ];
                            $sheet->getStyle('A0:E'.($row-1))->applyFromArray($styleArray);
                            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                            header('Content-Type: application/pdf');
                            header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
                            header('Cache-Control: max-age=0');
                            $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');
                            $writer->save('php://output');
                        }

                    redirect($_SERVER["HTTP_REFERER"]);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_purchase_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    /* ------------------------------------------------------------------------- */

    public function Customer($status = '')
    {
        $this->repairer->checkPermissions();

        if ($status == 'ready') {
            $this->data['status_view'] = 1;
        }elseif($status == 'purchased'){
            $this->data['status_view'] = 2;
        }else{
            $this->data['status_view'] = '';
        }
        $this->mPageTitle = "Customer Purchases";
        $this->render('purchases/customer/index');
    }

    public function getCustomerPurchases($status = NULL)
    {

        $user = $this->ion_auth->get_user_id();
        $edit_link = anchor('panel/purchases/customer_edit/$1', '<i class="fas fa-edit"></i> ' . lang('edit_purchase'));
        
        $detail_link = anchor('panel/purchases/customer_modal_view/$1', '<i class="fas fa-file-text"></i> ' . lang('View Purchase'), 'data-toggle="modal" data-target="#myModal"');

        $delete_link = "<a href='#' class='po' title='<b>" . lang('delete_purchase') . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('panel/purchases/customer_delete/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fas fa-trash\"></i> "
        . lang('delete_purchase') . "</a>";
    

        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . "Actions" . ' <span class="caret"></span></button>
            <ul class="dropdown-menu pull-right" role="menu">
                <li>' . $detail_link . '</li>
                <li>' . $edit_link . '</li>
                <li>' . $delete_link . '</li>
                <li>' . '<button class="btn btn-xs btn-default" id="email" data-num="$1"></button>' . '</li>
            </ul>
        </div></div>';

        $this->load->library('datatables');
        $this->datatables
            ->select("customer_purchases.id as id, customer_purchases.date, customer_purchases.customer, customer_purchases.status, customer_purchase_items.imei as imei, grand_total")
            ->where('(universal=1 OR store_id='.$this->activeStore.')', NULL, FALSE)
            ->join('customer_purchase_items', 'customer_purchase_items.purchase_id=customer_purchases.id', 'left')
            ->from('customer_purchases');
        if ( $status == 1 || $status == 2 ) {
            $this->datatables->where('status', $status);
        }
        // $this->datatables->where('created_by', $this->session->userdata('user_id'));
        $this->datatables->add_column("Actions", '$1___$2', "id, status");
        echo $this->datatables->generate();
    }

    public function customer_email()
    {


        $text = file_get_contents(FCPATH.'themes/'.$this->theme.'/email_templates/customer_purchase.html');
        $id = $this->input->post('id');
        $email = $this->input->post('email');
        $data = $this->db->get_where('customer_purchases', array('id'=>$id))->row_array();
        $search  = array('%businessname%', '%customer%', '%phone%', '%site_url%');
        $replace = array($this->mSettings->title, $data['customer'], $data['phone_name'], site_url());
        $text = str_replace($search, $replace, $text);
        if($this->repairer->send_email($email, 'Receipt from ' . $this->mSettings->title, $text)){
            $this->repairer->send_json(array('msg' => 'Email Sent'));
        } else {
            $this->repairer->send_json(array('msg' => 'Failed'));
        }
    }
    /* ----------------------------------------------------------------------------- */
    public function customer_add()
    {
        $this->repairer->checkPermissions();

        $this->mPageTitle = lang('Add Customer Purchase');

        $this->load->model('repair_model');
        $this->form_validation->set_rules('phone_name', lang('phone_name'), 'trim|required|trim');
        $this->form_validation->set_rules('model', lang('repair_model'), 'required|trim');
        $this->form_validation->set_rules('manufacturer', lang('p_manufacturer'), 'required|trim');
        $this->form_validation->set_rules('discount_type', lang('Discount Type'), 'required|trim');
        $this->form_validation->set_rules('description', lang('description'), 'trim');
        $this->form_validation->set_rules('carrier', lang('Carrier'), 'required|trim');
        $this->form_validation->set_rules('max_discount', lang('Max Discount'), 'trim');

        if ($this->form_validation->run() == FALSE) {
            $this->data['manufacturers'] = $this->settings_model->getManufacturers();
            $this->data['carriers'] = $this->settings_model->getCarriers();
            $this->data['clients'] = $this->repair_model->getAllClients();
            $this->data['tax_rates'] = $this->settings_model->getTaxRates();
            $this->render('purchases/customer/add');
        } else {
            $data = array();
            $client = $this->db->get_where('clients', array('id'=> $this->input->post('client_name')))->row();
            $data['status']             = 1;
            $data['customer_id']        = $this->input->post('client_name');
            $data['customer']           = $client->first_name.' '.$client->last_name;
            $data['phone_name']         = $this->input->post('phone_name');
            $data['model_name']         = $this->input->post('model');
            $data['manufacturer_id']    = $this->input->post('manufacturer');
            $data['carrier_id']         = $this->input->post('carrier');
            $data['description']        = $this->input->post('description');
            $data['max_discount']       = $this->input->post('max_discount');
            $data['discount_type']      = $this->input->post('discount_type');
            $data['taxable']            = $this->input->post('taxable');
            if ($data['taxable'] == 1) {
                $data['tax_id']         = $this->mSettings->used_phone_tax;
            }else{
                $data['tax_id']         = NULL;
            }
            $data['date_added']         = date('Y-m-d H:i:s');
            $data['cosmetic_condition'] = $this->input->post('cosmetic_condition');
            $data['operational_condition'] = $this->input->post('operational_condition');
            $data['date']               = date('Y-m-d');
            $data['used_status']        = $this->input->post('phone_status');
            $data['unlocked']           = $this->input->post('unlock_status');
            $data['created_by']         = $this->ion_auth->get_user_id();
            $data['store_id']           = $this->activeStore;



            if (isset($_POST['imei']) && $_POST['imei'] !== null) {
                $i = sizeof($_POST['imei']);
                for ($r = 0; $r < $i; $r++) {
                    $cost = $_POST['purchase_price'][$r];
                }
            }

            $order_tax_id = null;
            $data['grand_total']= $cost;

            $this->db->insert('customer_purchases', $data);
            $id = $this->db->insert_id();


            $phones = array();
            if ($this->input->post('imei')) {
                $i = sizeof($this->input->post('imei'));
                for ($r = 0; $r < $i; $r++) {
                    $imei = $this->security->xss_clean($this->input->post('imei')[$r]);
                    $cost = $this->security->xss_clean($this->input->post('purchase_price')[$r]);
                    $price = $this->security->xss_clean($this->input->post('list_price')[$r]);
                    $phones[] = array(
                        'purchase_id' => $id,
                        'imei' => $this->security->xss_clean($imei),
                        'cost' => $this->security->xss_clean($cost),
                        'price' =>  $this->security->xss_clean($price),
                    );
                }
            }
            $this->db->insert_batch('customer_purchase_items', $phones);


            $account_data = array(
                'type' => 'expense',
                'type_id' => 2,
                'amount' => $data['grand_total'],
                'date' => $data['date'],
                'recurring' => 0,
                'notes' => '',
                'user_id' =>$this->session->userdata('user_id'),
                'bank_id' => $this->mSettings->purchase_bank_id,
                'fund_type' => '',
                'to_from_id' => $data['customer_id'],
                'to_from_name' => $data['customer'],
                'created_at' => date('Y-m-d H:i:s'),
                'store_id' => (int)$this->session->userdata('active_store'),
                'sale_id' => $id,
            );
            $this->db->insert('account_entries', $account_data);


            $this->session->set_flashdata('message', 'Customer Purchase added successfully');

            // SEND EMAIL
            if ($client->email) {
                $text = file_get_contents(FCPATH.'themes/'.$this->theme.'/email_templates/customer_purchase.html');
                $search  = array('%businessname%', '%customer%', '%phone%', '%site_url%');
                $replace = array($this->mSettings->title, $data['customer'], $data['phone_name'], site_url());
                $text = str_replace($search, $replace, $text);
                @$this->repairer->send_email($client->email, sprintf(lang('reciept_from'), $this->mSettings->title), $text);
                $emails = $this->settings_model->getUsersByID($this->mSettings->notify_cpurchase);
                if ($emails) {
                  $this->load->library('parser');
                  $user = $this->ion_auth->user()->row();
                  $parse_data = array(
                      'user' => $user->first_name.' '.$user->last_name,
                      'grand_total' => $this->mSettings->currency.$data['grand_total'],
                      'logo' => '<img src="' . base_url() . 'assets/uploads/logos/' . $this->mSettings->logo . '" alt="' . $this->mSettings->title . '"/>',
                  );
                  $message = file_get_contents(FCPATH.'themes/'.$this->theme.'/email_templates/notify_cpurchase.php');
                  $message = $this->parser->parse_string($message, $parse_data);
                  $this->repairer->send_email($emails, lang('Notification for Customer Purchase'), $message);
                }
            }
            
            redirect('panel/purchases/customer/');
        }
    }
    public function getCustomerPurchaseByID($id)
    {
        $q = $this->db->get_where('customer_purchases', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getCustomerPurchaseItemsByID($id)
    {
        $q = $this->db->get_where('customer_purchase_items', array('purchase_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
    }
    /* ----------------------------------------------------------------------------- */
    public function customer_edit($id = NULL)
    {
        $this->repairer->checkPermissions();

        if(!$id){
            redirect('panel/purchases/customer');
        }
        $this->mPageTitle = lang('Edit Customer Purchase');

        $this->load->model('repair_model');
         $this->form_validation->set_rules('phone_name', lang('phone_name'), 'trim|required|trim');
        $this->form_validation->set_rules('model', lang('repair_model'), 'required|trim');
        $this->form_validation->set_rules('manufacturer', lang('p_manufacturer'), 'required|trim');
        $this->form_validation->set_rules('discount_type', lang('Discount Type'), 'required|trim');
        $this->form_validation->set_rules('description', lang('description'), 'trim');
        $this->form_validation->set_rules('carrier', lang('Carrier'), 'required|trim');
        $this->form_validation->set_rules('max_discount', lang('Max Discount'), 'trim');

        if ($this->form_validation->run() == FALSE) {
            $this->data['manufacturers'] = $this->settings_model->getManufacturers();
            $this->data['carriers'] = $this->settings_model->getCarriers();
            $this->data['clients'] = $this->repair_model->getAllClients();
            $this->data['tax_rates'] = $this->settings_model->getTaxRates();
            $this->data['inv'] = $this->getCustomerPurchaseByID($id);
            $this->data['inv_items'] = $this->getCustomerPurchaseItemsByID($id);
            $this->render('purchases/customer/edit');
        } else {
            $data = array();
            $client = $this->db->get_where('clients', array('id'=> $this->input->post('client_name')))->row();

            $data['status']             = 1;
            $data['customer_id']        = $this->input->post('client_name');
            $data['customer']           = $client->first_name.' '.$client->last_name;
            $data['phone_name']         = $this->input->post('phone_name');
            $data['model_name']         = $this->input->post('model');
            $data['manufacturer_id']    = $this->input->post('manufacturer');
            $data['carrier_id']         = $this->input->post('carrier');
            $data['description']        = $this->input->post('description');
            $data['max_discount']       = $this->input->post('max_discount');
            $data['discount_type']      = $this->input->post('discount_type');
            $data['taxable']            = $this->input->post('taxable');
            if ($data['taxable'] == 1) {
                $data['tax_id']         = $this->mSettings->used_phone_tax;
            }else{
                $data['tax_id']         = NULL;
            }
            $data['date_added']         = date('Y-m-d H:i:s');
            $data['cosmetic_condition'] = $this->input->post('cosmetic_condition');
            $data['operational_condition'] = $this->input->post('operational_condition');
            $data['date']               = date('Y-m-d');
            $data['used_status']        = $this->input->post('phone_status');
            $data['unlocked']           = $this->input->post('unlock_status');
            $data['created_by']         = $this->ion_auth->get_user_id();

            $order_tax = 0;
            $cost = 0;
            if (isset($_POST['imei']) && $_POST['imei'] !== null) {
                $i = sizeof($_POST['imei']);
                for ($r = 0; $r < $i; $r++) {
                    $cost = $_POST['purchase_price'][$r];
                    if ($this->input->post('order_tax')) {
                        $order_tax_value = $this->input->post('order_tax');
                        $opot = strpos($order_tax_value, '%');
                        if ($opot !== false) {
                            $odt = explode("%", $order_tax_value);
                            $order_tax = (((($cost) * (Float)($odt[0])) / 100));
                        } else {
                            $order_tax = ($order_tax_value);
                        }
                    }
                    $order_tax_id = null;
                }
            }

            $data['order_tax_rate'] = $this->input->post('order_tax');
            $data['order_tax']  = $order_tax;
            $data['grand_total']= $cost + $order_tax;
            $this->db->where('id', $id);
            // update purchase
            $this->db->update('customer_purchases', $data);
            // delete previous items
            $this->db->delete('customer_purchase_items', array('purchase_id'=>$id));
            $phones = array();
            if ($this->input->post('imei')) {
                $i = sizeof($this->input->post('imei'));
                for ($r = 0; $r < $i; $r++) {
                    $imei = $this->security->xss_clean($this->input->post('imei')[$r]);
                    $cost = $this->security->xss_clean($this->input->post('purchase_price')[$r]);
                    $price = $this->security->xss_clean($this->input->post('list_price')[$r]);
                    $phones[] = array(
                        'purchase_id' => $id,
                        'imei' => $this->security->xss_clean($imei),
                        'cost' => $this->security->xss_clean($cost),
                        'price' =>  $this->security->xss_clean($price),
                    );
                }
            }
            $this->db->insert_batch('customer_purchase_items', $phones);



            $this->db->where('sale_id', $id)->delete('account_entries');
            $account_data = array(
                'type' => 'expense',
                'type_id' => 2,
                'amount' => $data['grand_total'],
                'date' => $data['date'],
                'recurring' => 0,
                'notes' => '',
                'user_id' =>$this->session->userdata('user_id'),
                'bank_id' => $this->mSettings->purchase_bank_id,
                'fund_type' => '',
                'to_from_id' => $data['customer_id'],
                'to_from_name' => $data['customer'],
                'created_at' => date('Y-m-d H:i:s'),
                'store_id' => (int)$this->session->userdata('active_store'),
                'sale_id' => $id,
            );
            $this->db->insert('account_entries', $account_data);



            $this->session->set_flashdata('message', lang('Customer Purchase Edited successfully'));
            redirect('panel/purchases/customer/');
        }
    }
     /* -------------------------------------------------------------------------------- */

    public function customer_purchase_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {

                    foreach ($_POST['val'] as $id) {
                        $this->purchases_model->deleteCustomerPurchase($id);
                    }

                    $this->session->set_flashdata('message', lang('Customer Purchases Deleted'));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {


                    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();

                    $sheet->setTitle(lang("Customer Purchases"));
                    $sheet->SetCellValue('A2', lang("date"));
                    $sheet->SetCellValue('B2', lang('Customer'));
                    $sheet->SetCellValue('C2', lang('status'));
                    $sheet->SetCellValue('D2', lang('grand_total'));
                    

                    $sheet->SetCellValue('A1', lang("Customer Purchases"));
                    $sheet->mergeCells('A1:D1');

                    $row = 3;
                    $total = 0;

                    foreach ($_POST['val'] as $id) {
                        $ir = $row + 1;
                        if ($ir % 2 == 0) {
                            $style_header = array(                  
                                'fill' => array(
                                    'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                    'color' => array('rgb'=>'CCCCCC'),
                                ),
                            );
                            $sheet->getStyle("A$row:D$row")->applyFromArray( $style_header );
                        }

                        $purchase = $this->purchases_model->getCustomerPurchaseByID($id);
                        $status = $purchase->status==1?'Ready to Purchase':'Purchased';
                        $sheet->SetCellValue('A' . $row, $this->repairer->hrld($purchase->date));
                        $sheet->SetCellValue('B' . $row, $purchase->customer);
                        $sheet->SetCellValue('C' . $row, $status);
                        $sheet->SetCellValue('D' . $row, $this->repairer->formatMoney($purchase->grand_total));
                        $total += $purchase->grand_total;
                        $row++;

                    }


                    $style_header = array(      
                        'fill' => array(
                            'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => array('rgb'=>'fdbf2d'),
                        ),
                    );
                    $sheet->getStyle("A$row:D$row")->applyFromArray( $style_header );
                    $sheet->SetCellValue('D' . $row, $total);

            }                


                $sheet->getColumnDimension('A')->setWidth(30);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(25);

                $filename = 'customer_purchases_report';

                
                 $header = 'A1:D1';
                $sheet->getStyle($header)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('94ce58');
                $style = array(
                    'font' => array('bold' => true,),
                    'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,),
                );
                $sheet->getStyle($header)->applyFromArray($style);
                

                $header = 'A2:D2';
                $sheet->getStyle($header)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('fdbf2d');
                $style = array(
                    'font' => array('bold' => true,),
                    'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_GENERAL,),
                );
                $sheet->getStyle($header)->applyFromArray($style);


                $header = 'A'.$row.':D'.$row;
                $sheet->getStyle($header)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('fdbf2d');
                $style = array(
                    'font' => array('bold' => true,),
                    'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_GENERAL,),
                );
                $sheet->getStyle($header)->applyFromArray($style);
                if ($this->input->post('form_action') == 'export_pdf') {
                    $styleArray = [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                'color' => ['argb' => 'FFFF0000'],
                            ],
                        ],
                    ];
                    $sheet->getStyle('A0:D'.($row))->applyFromArray($styleArray);
                    $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                    header('Content-Type: application/pdf');
                    header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
                    header('Cache-Control: max-age=0');
                    $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');
                    $writer->save('php://output');
                }
                if ($this->input->post('form_action') == 'export_excel') {
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
                    header('Cache-Control: max-age=0');

                    $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
                    $writer->save('php://output');
                    exit();
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_purchase_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    /* ------------------------------------------------------------------------- */
    /* --------------------------------------------------------------------------- */

    public function customer_delete($id = null)
    {
        $this->repairer->checkPermissions();

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->purchases_model->deleteCustomerPurchase($id)) {
            if ($this->input->is_ajax_request()) {
                echo 'purchase deleted';die();
            }
            $this->session->set_flashdata('message', lang('Customer Purchase Deleted'));
            redirect('panel/purchases');
        }
    }

    public function return_purchase($id = null)
    {
        $this->repairer->checkPermissions();
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $purchase = $this->purchases_model->getPurchaseByID($id);
        if ($purchase->return_id) {
            $this->session->set_flashdata('error', lang('Purchase Already Returned'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if ($purchase->status != 'received') {
            $this->session->set_flashdata('error', lang('Purchase status is not received'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('return_surcharge', lang("return_surcharge"), 'required');

        if ($this->form_validation->run() == true) {
            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') :  $this->repairer->getReference('rpo');
            $date = $this->repairer->mdytoymd2($this->input->post('date'));
            // $date = preg_replace('#(\d{2})/(\d{2})/(\d{4})\s(.*)#', '$3-$2-$1 $4', $date);
            // $date = date("Y-m-d H:i:s", strtotime($date));
            $return_surcharge = $this->input->post('return_surcharge') ? $this->input->post('return_surcharge') : 0;
            $rma_number = $this->input->post('rma_number');
            $note = $this->repairer->clear_tags($this->input->post('note'));

            $total = 0;
            $product_tax = 0;
            $order_tax = 0;
            $product_discount = 0;
            $order_discount = 0;
            $percentage = '%';
            $i = isset($_POST['product']) ? sizeof($_POST['product']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $item_id = $_POST['product_id'][$r];
                $item_code = $_POST['product'][$r];
                $purchase_item_id = $_POST['purchase_item_id'][$r];
                $real_unit_cost = $this->repairer->formatDecimal($_POST['real_unit_cost'][$r]);
                $unit_cost = $this->repairer->formatDecimal($_POST['unit_cost'][$r]);
                $item_unit_quantity = (0-$_POST['quantity'][$r]);
                $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : null;
                $item_unit = $_POST['product_unit'][$r];
                $item_quantity = (0-$_POST['product_base_quantity'][$r]);
                $product_type = $_POST['product_type'][$r];

                if (isset($item_code) && isset($real_unit_cost) && isset($unit_cost) && isset($item_quantity)) {

                    $product_details = $this->purchases_model->getProductByID($item_id, $product_type );

                    $item_type = $product_details->type;
                    $item_name = $product_details->name;

                    if (isset($item_discount)) {
                        $discount = $item_discount;
                        $dpos = strpos($discount, $percentage);
                        if ($dpos !== false) {
                            $pds = explode("%", $discount);
                            $pr_discount = $this->repairer->formatDecimal(((($this->sma->formatDecimal($unit_cost)) * (Float) ($pds[0])) / 100), 4);
                        } else {
                            $pr_discount = $this->repairer->formatDecimal($discount);
                        }
                    } else {
                        $pr_discount = 0;
                    }
                    $pr_item_discount = $this->repairer->formatDecimal(($pr_discount * $item_unit_quantity), 4);
                    $product_discount += $pr_item_discount;


                    $item_tax = 0;
                    $pr_tax = 0;
                    $pr_item_tax = 0;
                    $tax = "";

                    $item_net_cost = ($unit_cost - $pr_discount);
                    $product_tax += $pr_item_tax;
                    $subtotal = $this->repairer->formatDecimal((($item_net_cost * $item_unit_quantity) + $pr_item_tax), 4);
                    $products[] = array(
                        'product_id' => $item_id,
                        'product_code' => $item_code,
                        'product_name' => $item_name,
                        'net_unit_cost' => $item_net_cost,
                        'unit_cost' => $this->repairer->formatDecimal($item_net_cost + $item_tax),
                        'quantity' => $item_quantity,
                        'quantity_balance' => $item_quantity,
                        'item_tax' => $pr_item_tax,
                        'tax_rate_id' => $pr_tax,
                        'tax' => $tax,
                        'discount' => $item_discount,
                        'item_discount' => $pr_item_discount,
                        'subtotal' => $this->repairer->formatDecimal($subtotal),
                        'purchase_item_id' => $purchase_item_id,
                        'stock_type' => $product_type,
                        'store_id' => $this->activeStore,
                    );

                    $total += $this->repairer->formatDecimal(($item_net_cost * $item_unit_quantity), 4);
                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }
            if ($this->input->post('discount')) {
                $order_discount_id = $this->input->post('discount');
                $opos = strpos($order_discount_id, $percentage);
                if ($opos !== false) {
                    $ods = explode("%", $order_discount_id);
                    $order_discount = (((($total + $product_tax) * (Float) ($ods[0])) / 100));

                } else {
                    $order_discount = ($order_discount_id);
                }
            } else {
                $order_discount_id = null;
            }
            $total_discount = ($order_discount + $product_discount);

            if ($this->input->post('order_tax')) {
                $order_tax_value = $this->input->post('order_tax');
                $opot = strpos($order_tax_value, '%');
                if ($opot !== false) {
                    $odt = explode("%", $order_tax_value);
                    $order_tax = (((($total + $product_tax - $order_discount) * (Float)($odt[0])) / 100));
                } else {
                    $order_tax = ($order_tax_value);
                }
            }
            $order_tax_id = null;


            $total_tax = $this->repairer->formatDecimal(($product_tax + $order_tax), 4);
            $grand_total = $this->repairer->formatDecimal(($total + $total_tax + $this->repairer->formatDecimal($return_surcharge) - $order_discount), 4);
            $data = array('date' => $date,
                'purchase_id' => $id,
                'reference_no' => $purchase->reference_no,
                'supplier_id' => $purchase->supplier_id,
                'supplier' => $purchase->supplier,
                'note' => $note,
                'total' => $total,
                'product_discount' => $product_discount,
                'order_discount_id' => $order_discount_id,
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $product_tax,
                'order_tax_id' => $order_tax_id,
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'surcharge' => $this->repairer->formatDecimal($return_surcharge),
                'grand_total' => $grand_total,
                'created_by' => $this->session->userdata('user_id'),
                'return_purchase_ref' => $reference,
                'status' => 'returned',
                'store_id' => $this->activeStore,
                'rma_number' => $rma_number,
                'return_status' => 1,
            );

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = false;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }
        }

        if ($this->form_validation->run() == true && $this->purchases_model->addPurchase($data, $products)) {
            $this->session->set_flashdata('message', lang('Return Purchase Added'));
            redirect("panel/purchases");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['inv'] = $purchase;
            

            $inv_items = $this->purchases_model->getAllPurchaseItems($id);
           
            krsort($inv_items);
            $c = rand(100000, 9999999);
            $this->load->model('inventory_model');
            foreach ($inv_items as $item) {
                $row = $this->purchases_model->getProductByID($item->product_id, $item->stock_type);
                if (!$row) {
                    $pr[$c] = NULL;
                    continue;
                }
                if ($item->stock_type == 'used_phone') {
                    if (!$item->usedphone_id) {
                        continue;
                    }
                }else{
                    if (!$item->stock_id) {
                        continue;
                    }
                }
                $row->unit = 'unit';
                $row->base_quantity = $item->quantity;
                $row->base_unit_cost = $item->net_unit_cost;
                $row->unit = $row->unit;
                $row->qty = $item->quantity;
                $row->oqty = $item->quantity;
                $row->purchase_item_id = $item->id;
                $row->received = $item->quantity;
                $row->quantity_balance = $item->quantity_balance;
                $row->discount = $item->discount ? $item->discount : '0';
                $row->type = $item->stock_type;
                $row->code = $item->product_code;
                $row->cost = $this->repairer->formatDecimal($item->net_unit_cost + ($item->item_discount / $item->quantity));
                unset($row->details, $row->product_details, $row->price, $row->file, $row->product_group_id);

                $pr[$c] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'tax_rate' => NULL);

                $c++;
            }

            $this->data['inv_items'] = json_encode($pr);
            $this->data['id'] = $id;
            $this->data['reference'] = '';
            $this->data['tax_rates'] = $this->settings_model->getTaxRates();
            $this->render('purchases/return_purchase');
        }
    }

    public function admin_email($purchase_id = null) {
        if ($this->input->get('id')) {
            $purchase_id = $this->input->get('id');
        }
        $inv = $this->purchases_model->getPurchaseByID($purchase_id);

        if ($inv->status == 'ordered') {
          $to = $this->settings_model->getUsersByID($this->mSettings->notify_porder);
        }elseif ($inv->status == 'received') {
          $to = $this->settings_model->getUsersByID($this->mSettings->notify_preceive);
        }else{
            return FALSE;
        }
        if (!$to) {
            return FALSE;
        }
        $subject = lang('Notification for Purchase');
        $supplier = $this->purchases_model->getCompanyByID($inv->supplier_id);
        $this->load->library('parser');
        $user = $this->ion_auth->user($inv->created_by)->row();
        $parse_data = array(
            'reference_number' => $inv->reference_no,
            'user' => $user->first_name.' '.$user->last_name,
            'status' => ucfirst($inv->status),
            'grand_total' => $this->mSettings->currency.$inv->grand_total,
            'site_link' => base_url(),
            'site_name' => $this->mSettings->title,
            'logo' => '<img src="' . base_url() . 'assets/uploads/logos/' . $this->mSettings->logo . '" alt="' . $this->mSettings->title . '"/>',
        );
        $msg = file_get_contents(FCPATH.'themes/'.$this->theme.'/email_templates/notify_purchase.php');
        $message = $this->parser->parse_string($msg, $parse_data);
        $attachment = $this->pdf($purchase_id, null, 'S');
       
        $this->repairer->send_email($to, $subject, $message, null, null, $attachment);
        delete_files($attachment);
        return TRUE;
    }
}
