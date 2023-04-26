<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Inventory
 *
 *
 * @package     Repairer
 * @category    Controller
 * @author      Usman Sher
*/

// Includes all customers controller

class Inventory extends Auth_Controller
{
    // THE CONSTRUCTOR //
    public function __construct()
    {
        parent::__construct();
        $this->load->model('inventory_model');
        
        $this->digital_upload_path = 'files/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->popup_attributes = array('width' => '900', 'height' => '600', 'window_name' => 'sma_popup', 'menubar' => 'yes', 'scrollbars' => 'yes', 'status' => 'no', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
        $this->mPageTitle = "Repair Parts";
    }

    public function update_qs() {
        $id = $this->input->post('row_id');
        $val = $this->input->post('val1');
        $this->db->where('id', $id);
        $this->db->update('inventory', array('quick_sale' => $val));
        echo "true";
    }

    public function addmore() {
        $this->load->view($this->theme."inventory/add_row");
    }
    function addByAjax()
    {
        $upc = $this->settings_model->UPCCodeExists($this->input->post('code'));
        if ($upc) {
            $this->repairer->send_json(array('msg'=>'error', 'message'=>lang('code_already_exists', $upc->name, humanize($upc->type))));
        }
        $data = array(
            'code'              => $this->input->post('code'),
            'name'              => $this->input->post('name'),
            'manufacturer_id'   => $this->input->post('manufacturer'),
            'model_name'        => $this->input->post('model'),
            'price'             => ($this->input->post('price')),
            'unit'              => NULL,
            'taxable'           => 1,
            'alert_quantity'    => $this->input->post('alert_quantity'),
            'details'           => $this->input->post('details'),
            'date_created'      => date("Y-m-d H:i:s"),
            'is_serialized'     => $this->input->post('is_serialized'),
            'universal'         => $this->input->post('universal'),
            'store_id'          => $this->activeStore,
            'category'          => $this->input->post('category_id'),
            'sub_category'      => $this->input->post('sub_category'),  
            'max_discount'      => $this->input->post('max_discount'),
            'discount_type'     => $this->input->post('discount_type'),
            'warranty_id'       => $this->input->post('warranty_id'),

        );
        
        if ($row = $this->addAjaxProduct($data)) {
            $row->type = 'repair';
            $row->cost = 1;
            $row->unit_cost = 1;
            $row->discount = 0;
            $row->qty = 1;
            $pr = array(
                'item_id' => uniqid(), 
                'id'    => (int)$row->id,
                'label' => $row->name . " (" . $row->code . ")", 
                'row' => $row,
                'pr_tax' => null,
            );
            $this->repairer->send_json(array('msg' => 'success', 'result' => $pr));
        } else {
            exit(json_encode(array('msg' => ('failed_to_add_product'))));
        }
        
    }
    
    public function addAjaxProduct($data) {
        if ($this->db->insert('inventory', $data)) {
            $product_id = $this->db->insert_id();
            return $this->db->select('id as id, name as name, code as code, is_serialized')->where('id', $product_id)->get('inventory')->row();
        }
        return false;
    }

        
    function index($type = NULL)
    {
        $this->repairer->checkPermissions();
        if ($type === 'disabled' || $type === 'enabled') {
            $this->data['toggle_type'] = $type;
        }else{
            $this->data['toggle_type'] = NULL;
        }
        $this->data['cat_filter'] = $this->settings_model->getCategoriesTree();
        $this->render('inventory/index');
    }
    function toggle() {
        $toggle = $this->input->post('toggle');
        if ($toggle == 'enable') {
            $data = array('isDeleted' => 0);
            $a = lang('enabled');
        } else {
            $data = array('isDeleted' => 1);
            $a = lang('disabled');
        }

        $this->db->where('id', $this->input->post('id'));
        $this->db->update('inventory', $data);
        echo json_encode(array('ret' => 'true', 'toggle' => $a));
    }
    function getProducts($type = NULL) {

        $this->load->library('datatables');
        $this->datatables->where('(universal=1 OR store_id='.$this->activeStore.')', NULL, FALSE);
        $this->datatables
            ->select($this->db->dbprefix('inventory') . ".id as productid,  {$this->db->dbprefix('inventory')}.code as code, {$this->db->dbprefix('inventory')}.name as name, price as price, (SELECT COUNT(id) FROM stock WHERE stock.inventory_id = inventory.id AND stock.inventory_type = 'repair') as count, isDeleted, quick_sale", FALSE)
            ->from('inventory')
            ->group_by("inventory.id");
        
        $this->datatables->where('isDeleted', 0);
        if ($this->input->get('cat_id')) {
            $this->datatables->where('category', $this->input->get('cat_id'));
        }
        if ($this->input->get('sub_id')) {
            $this->datatables->where('sub_category', $this->input->get('sub_id'));
        }

        $this->datatables->add_column('quick_sale', '$1__$2', 'productid, quick_sale');
        $this->datatables->add_column('manage_stock', '$1', 'productid');
        $this->datatables->add_column("Actions", "$1___$2___$3___$4___$5", "productid, isDeleted, image, code, name");
        $this->datatables->unset_column('quick_sale');
        $this->datatables->unset_column('isDeleted');
        echo $this->datatables->generate();
    }
    /* ------------------------------------------------------- */

    function add($id = NULL)
    {
        $this->showPageTitle = false;
        $this->repairer->checkPermissions();

        $this->form_validation->set_error_delimiters('', '');

        $upc = $this->settings_model->UPCCodeExists($this->input->post('code'));
        if ($upc) {
            $this->form_validation->set_rules('codea', lang('code'), 'required',
                array('required' => lang('code_already_exists', $upc->name, humanize($upc->type)))
            );
        }


        $this->form_validation->set_rules('name', lang("name"), 'required');

        if ($this->form_validation->run() == true) {
            $data = array(
                'code'              => $this->input->post('code'),
                'name'              => $this->input->post('name'),
                'manufacturer_id'   => $this->input->post('manufacturer'),
                'model_name'        => $this->input->post('model'),
                'price'             => $this->input->post('price'),
                'unit'              => NULL,
                'taxable'           => $this->input->post('taxable'),
                'alert_quantity'    => (int)$this->input->post('alert_quantity'),
                'details'           => $this->input->post('details'),
                'date_created'      => date("Y-m-d H:i:s"),
                'is_serialized'     => $this->input->post('is_serialized'),
                'universal'         => $this->input->post('universal'),
                'store_id'          => $this->activeStore,
                'category'          => $this->input->post('category_id'),
                'sub_category'      => $this->input->post('sub_category'),  
                'max_discount'      => $this->input->post('max_discount'),
                'discount_type'     => $this->input->post('discount_type'),
                'warranty_id'       => $this->input->post('warranty_id'),
                'delivery_note_number'       => $this->input->post('delivery_note_number'),
                'quick_sale'              => 1,
            );

            $variants = NULL;
            if ($this->input->post('variants')) {
                $variants = array();
                $i = sizeof($_POST['variant_name']);
                for ($r = 0; $r < $i; $r++) {
                    $name = $_POST['variant_name'][$r];
                    $price = $_POST['variant_price'][$r];
                    $variants[] = array(
                        'variant_name' => $name,
                        'price' => $price,
                    );
                }
            }


        }

        if ($this->form_validation->run() == true && $this->inventory_model->addProduct($data, $variants)) {
            $this->session->set_flashdata('message', lang("product_added"));
            redirect('panel/inventory');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['tax_rates'] = $this->settings_model->getTaxRates();
            $this->data['manufacturers'] = $this->settings_model->getManufacturers();
            $this->data['product'] = $id ? $this->inventory_model->getProductByID($id) : NULL;
            $this->data['frm_priv'] = $this->settings_model->getMandatory('repair_items');
            $this->data['variants'] = $id ? $this->inventory_model->getProductVariantsByID($id) : NULL;
            $this->data['warranty_plans'] = $this->settings_model->getAllWarranties();
            $this->data['categories'] = $this->settings_model->getAllCategories();
            $this->data['subcategories'] = $this->settings_model->getAllCategories(FALSE);
            $this->render('inventory/add');
        }
    }
    function product_barcode($product_code = NULL, $bcs = 'code128', $height = 60)
    {
       return "<img src='" . site_url('panel/misc/barcode/' . $product_code . '/' . $bcs . '/' . $height) . "' alt='{$product_code}' class='bcimg' />";
    }

   
    public function barcode_suggestions()
    {
        $this->load->library('repairer');
        $term = $this->input->get('term', true);
        $type = $this->input->get('type');
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . site_url('welcome') . "'; }, 10);</script>");
        }
        $this->load->model('pos_model');
        $rows = $this->pos_model->getProductBarcodes($term, 5);
        $rows = array_filter((array)$rows);
        if ($rows) {
            $c = str_replace(".", "", microtime(true));
            $r = 0;
            foreach ($rows as $row) {
                $item_id = $row->type.($c + $r);
                $row_id = $row->type.time();
                $pr[] = array(
                    'row_id' => $row_id,
                    'item_id' => $item_id,
                    'label' => $row->name . " (" . $row->code . ")", 
                    'code' => $row->code, 
                    'name' => $row->name, 
                    'qty' => $row->qty, 
                    'type' => $row->type, 
                    'product_id'=>$row->id,
                );
                $r++;
            }
            $this->repairer->send_json($pr);
        } else {
            $this->repairer->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }
    function print_barcodes($product_id = NULL, $type = null)
    {
        $this->form_validation->set_rules('style', lang("style"), 'required');
        $this->load->model('pos_model');
        if ($this->form_validation->run() == true) {
            $style = $this->input->post('style');
            $bci_size = ($style == 10 || $style == 12 ? 50 : ($style == 14 || $style == 18 ? 30 : 20));
            $s = isset($_POST['product']) ? sizeof($_POST['product']) : 0;
            if ($s < 1) {
                $this->session->set_flashdata('error', lang('no_product_selected'));
                redirect("panel/inventory/print_barcodes");
            }
            $barcodes = [];
            for ($m = 0; $m < $s; $m++) {
                $pid = $_POST['product'][$m];
                $quantity = $_POST['quantity'][$m];
                $type = $_POST['type'][$m];


                $product = $this->pos_model->getProductBarcodesByTypeAndID($pid, $type);
                $barcodes[] = array(
                    'site' => $this->input->post('site_name') ? $this->mSettings->title : FALSE,
                    'name' => $this->input->post('product_name') ? $product['name'] : FALSE,
                    'barcode' => $product['code'],
                    'price' => $this->input->post('price') ? $product['price'] : FALSE,
                    'quantity' => $quantity
                );
                
            }
            $this->data['barcodes'] = $barcodes;
            $this->data['style'] = $style;
            $this->data['bci_size'] = $bci_size;
            $this->data['items'] = false;
            
            $this->render('inventory/print_barcodes');
        } else {

            if ($product_id) {
                if ($row = $this->pos_model->getProductByTypeAndID($product_id, $type)) {

                    $c = str_replace(".", "", microtime(true));
                    $r = 0;
                    $item_id = $type.($c + $r);
                    $row_id = $type.time();
                    $pr[] = array(
                        'row_id' => $row_id,
                        'item_id' => $item_id,
                        'label' => $row->name . " (" . $row->code . ")", 
                        'code' => $row->code, 
                        'name' => $row->name, 
                        'qty' => $row->quantity, 
                        'type' => $type, 
                        'product_id'=>$row->id,
                    );
                    $this->session->set_flashdata('message',  lang('product_added_to_list'));
                }
            }
            $this->data['items'] = isset($pr) ? json_encode($pr) : false;
            $this->render('inventory/print_barcodes');

        }
    }
     /* -------------------------------------------------------- */

    function edit($id = NULL)
    {
        $this->showPageTitle = false;
        
        $this->repairer->checkPermissions();
        $this->load->helper('security');
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }
        $product = $this->inventory_model->getProductByID($id);

        if (!$id || !$product) {
            $this->session->set_flashdata('error', ('prduct_not_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
       
        $this->form_validation->set_rules('code', lang("product_code"), 'alpha_dash');
        
        if ($this->input->post('barcode_symbology') == 'ean13') {
            $this->form_validation->set_rules('code', lang("product_code"), 'min_length[13]|max_length[13]');
        }
        $date = date('Y-m-d H:i:s', strtotime($product->date_created) + 3600);
        $date_now = date('Y-m-d H:i:s', time());
        if ($date < $date_now) {
            if ($this->input->post('code') !== $product->code) {
                $this->form_validation->set_message('required', lang('upc_edit_one_hour'));
                $this->form_validation->set_rules('Code', "UPC CODE", 'required');
            }
        }
        $upc = $this->settings_model->UPCCodeExists($this->input->post('code'));
        if ($this->input->post('code') !== $product->code && $upc) {
            $this->form_validation->set_rules('codea', 'Code', 'required',
                array('required' => lang('code_already_exists', $upc->name, humanize($upc->type)))
            );
        }

        if ($this->form_validation->run() == true) {

            $data = array(
                'code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                'manufacturer_id' => $this->input->post('manufacturer'),
                'model_name' => $this->input->post('model'),
                'price' => ($this->input->post('price')),
                'unit' => NULL,
                'taxable' => $this->input->post('taxable'),
                'alert_quantity' => $this->input->post('alert_quantity'),
                'details' => $this->input->post('details'),
                'is_serialized' => $this->input->post('is_serialized'),
                'universal'     => $this->input->post('universal'),
                'category'      => $this->input->post('category_id'),
                'sub_category'  => $this->input->post('sub_category'),  
                'max_discount'      => $this->input->post('max_discount'),
                'discount_type'     => $this->input->post('discount_type'),
                'warranty_id'       => $this->input->post('warranty_id'),
                'delivery_note_number'       => $this->input->post('delivery_note_number'),

            );
           
            $variants = NULL;
            if ($this->input->post('variants')) {
                $variants = array();
                $i = sizeof($_POST['variant_name']);
                for ($r = 0; $r < $i; $r++) {
                    $name = $_POST['variant_name'][$r];
                    if ($name !== '') {
                        $price = $_POST['variant_price'][$r];
                        $variants[] = array(
                            'inventory_id' => $id,
                            'variant_name' => $name,
                            'price' => $price,
                        );
                    }
                }
            }

            
        }

        if ($this->form_validation->run() == true && $this->inventory_model->updateProduct($id, $data, $variants)) {
            $this->session->set_flashdata('message', lang("product_updated"));
            redirect('panel/inventory');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['tax_rates'] = $this->settings_model->getTaxRates();
            $this->data['manufacturers'] = $this->settings_model->getManufacturers();

            $this->data['product'] = $product;
            $this->data['variants'] = $id ? $this->inventory_model->getProductVariantsByID($id) : NULL;
            $this->data['frm_priv'] = $this->settings_model->getMandatory('repair_items');
            $this->data['categories'] = $this->settings_model->getAllCategories();
            $this->data['subcategories'] = $this->settings_model->getAllCategories(FALSE);
            $this->data['warranty_plans'] = $this->settings_model->getAllWarranties();

            $this->render('inventory/edit');
        }
    }
    function delete($id = NULL)
    {

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->inventory_model->deleteProduct($id)) {
            if($this->input->is_ajax_request()) {
                echo lang("product_deleted"); die();
            }
            $this->session->set_flashdata('message', lang('product_deleted'));
            redirect('welcome');
        }

    }
    function modal_view($id = NULL)
    {
        $pr_details = $this->inventory_model->getProductByID($id);
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            $this->repairer->md();
        }
        $this->data['barcode'] = "<img src='" . site_url('panel/inventory/gen_barcode/' . $pr_details->code . '/' . 'code128' . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        $this->data['product'] = $pr_details;
        $tax_rates = explode(',' ,$pr_details->tax_rate);
        foreach ($tax_rates as $tax_rate) {
            $this->data['tax_rate'][] = ($this->settings_model->getTaxRateByID($tax_rate)) ?$this->settings_model->getTaxRateByID($tax_rate)->name : NULL;
        }
        $this->data['tax_rate'] = array_filter($this->data['tax_rate']);
        $this->data['Settings'] = $this->mSettings;
        $this->load->view($this->theme.'inventory/modal_view', $this->data);
    }


    // GENERATE THE AJAX TABLE CONTENT //
    public function getAllSuppliers()
    {
        $this->load->library('datatables');
        $this->datatables
            ->where('(universal=1 OR store_id='.$this->activeStore.')', NULL, FALSE)
            ->select('id, name, company, phone, email, city, country, vat_no')
            ->from('suppliers');

        $actions = "<a data-dismiss='modal' class='view' href='#view_supplier' data-toggle='modal' data-num='$1'><button class='btn btn-success btn-xs'><i class='fas fa-check'></i></button></a>";

        if ($this->Admin || $this->GP['suppliers-edit']){
            $actions .= "<a  data-dismiss='modal' id='modify' href='#suppliermodal' data-toggle='modal' data-num='$1'><button class='btn btn-primary btn-xs'><i class='fas fa-edit'></i></button></a>";
        }
        if ($this->Admin || $this->GP['suppliers-delete']){
            $actions .= "<a id='delete' data-num='$1'><button class='btn btn-danger btn-xs'><i class='fas fa-trash'></i></button></a>";
        }
        $this->datatables->add_column('actions', $actions, 'id');
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    
    // ADD A CUSTOMER //
    public function add_supplier()
    {
        $this->repairer->checkPermissions('add', FALSE, 'suppliers');
        $data = array(
            'name'      => $this->input->post('name', true),
            'company'   => $this->input->post('company', true),
            'address'   => $this->input->post('address', true),
            'city'      => $this->input->post('city', true),
            'country'   => $this->input->post('country', true),
            'state'     => $this->input->post('state', true),
            'postal_code'  => $this->input->post('postal_code', true),
            'phone'         => $this->input->post('phone', true),
            'email'         => $this->input->post('email', true),
            'vat_no'        => $this->input->post('vat_no', true),
            'url'           => $this->input->post('url', true),
            'universal' => $this->input->post('universal') ? $this->input->post('universal') : $this->mSettings->universal_suppliers,
            'store_id'          => $this->activeStore,
        );

        echo $this->inventory_model->insert_supplier($data);
    }

    // EDIT CUSTOMER //
    public function edit_supplier()
    {
        $this->repairer->checkPermissions('edit', FALSE, 'suppliers');

        $id = $this->input->post('id', true);
        $data = array(
            'name'      => $this->input->post('name', true),
            'company'   => $this->input->post('company', true),
            'address'   => $this->input->post('address', true),
            'city'      => $this->input->post('city', true),
            'country'   => $this->input->post('country', true),
            'state'     => $this->input->post('state', true),
            'postal_code'   => $this->input->post('postal_code', true),
            'phone'     => $this->input->post('phone', true),
            'email'     => $this->input->post('email', true),
            'vat_no'    => $this->input->post('vat_no', true),
            'url'       => $this->input->post('url', true),
            'universal' => $this->input->post('universal') ? $this->input->post('universal') : $this->mSettings->universal_suppliers,
        );
        $token = $this->input->post('token', true);
        echo $this->inventory_model->edit_supplier($id, $data);
       
    }

    // DELETE CUSTOMER 
    public function delete_supplier()
    {
        $this->repairer->checkPermissions('delete', FALSE, 'suppliers');
        $id = $this->security->xss_clean($this->input->post('id', true));
        $data = $this->inventory_model->delete_supplier($id);
        echo json_encode($data);
    }

    // GET CUSTOMER AND SEND TO AJAX FOR SHOW IT //
    public function getSupplierByID()
    {
        $id = $this->security->xss_clean($this->input->post('id', true));
        $data = $this->inventory_model->find_supplier($id);
        $token = $this->input->post('token', true);
        echo json_encode($data);
    }

    function suggestions()
    {
        $term = $this->input->get('term', TRUE);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . site_url('welcome') . "'; }, 10);</script>");
        }
        $this->load->model('pos_model');
        $repairs = $this->pos_model->getProductNames($term);
        $others = $this->pos_model->getOthers($term);
        $accessory = $this->pos_model->getAccessoryNames($term);
        $rows = array_merge((array)$repairs, (array)$accessory,(array)$others);
        $pr = [];
        // echo "<pre>";print_r($rows);die();
        if ($rows) {
            foreach ($rows as $row) {
                if (!$row) {
                    continue;
                }
                $variants = $this->inventory_model->getProductVariantsByID($row->id);
                $quantity = $this->db->where(array('inventory_id'=> $row->id, 'inventory_type' => 'repair'))->select('COUNT(id) as count')->get('stock')->row()->count;

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


                $pr[] = array(
                    'type' => $row->type, 
                    'row_id' => time(),
                    'label' => $row->name . " (" . $row->code . ")", 
                    'code' => $row->code, 
                    'name' => $row->name, 
                    'price' => $row->price, 
                    'qty' => 1, 
                    'available_now' => $quantity, 
                    'total_qty' => $quantity, 
                    'cost'=>$row->cost, 
                    'row' => $row,
                    'stock_id'=>$row->stock_id, 
                    'product_id'=>$row->id,
                    'taxable'=>$row->taxable,
                    'pr_tax' => $o_taxes,
                    'variants' => $variants ? TRUE : FALSE,
                    'option_selected' => FALSE,
                    'options' => $variants,
                    'option' => NULL,
                    'is_serialized' => (int)$row->is_serialized,
                    'serialed' => (int)$row->is_serialized ? FALSE : TRUE,
                    'serial_number' => NULL,
                    'discount' => 0,
                    'max_discount' => $row->max_discount,
                    'discount_type' => $row->discount_type,
                    'discount' => 0,
                    'item_details' => '',
                );
            }
            $this->repairer->send_json($pr);
        } else {
            $this->repairer->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }
    public function setSelected()
    {
        $id = $this->input->post('stock_id');
        $this->db->where(array('id' => $id));
        $this->db->update('stock', array('selected'=> 1, 'selected_user_id'=>$this->ion_auth->get_user_id()));
        echo "true";
    }
    public function removeSelected()
    {
        $this->db->where(array('selected_user_id' => $this->ion_auth->get_user_id()));
        $this->db->update('stock', array('selected'=> 0, 'selected_user_id'=> NULL));
    }

    public function removeSelectedAll()
    {
        $this->db->update('stock', array('selected'=> 0, 'selected_user_id'=> NULL));
    }
    public function removeSelectedByInventoryID()
    {
        $id = $this->input->post('id');
        $this->db->where(array('inventory_id' => $id));
        $this->db->update('stock', array('selected'=> 0));
    }
    
    public function removeSelectedByStockID()
    {
        $id = $this->input->post('id');
        $this->db->where('id', $id);
        $this->db->update('stock', array('selected'=> 0, 'selected_user_id'=> NULL));
        echo "true";
    }

    function product_actions($wh = NULL)
    {
       
        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->inventory_model->deleteProduct($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("products_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'labels') {
                    foreach ($_POST['val'] as $id) {
                        $row = $this->inventory_model->getProductByID($id);
                        $pr[$row->id] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity);

                    }
                    $this->data['items'] = isset($pr) ? json_encode($pr) : false;
                    $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                    $this->render('inventory/print_barcodes');

                } elseif ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->setTitle(lang('products'));
                    $sheet->SetCellValue('A1', lang('name'));
                    $sheet->SetCellValue('B1', lang('code'));
                    $sheet->SetCellValue('C1', lang('model'));
                    $sheet->SetCellValue('D1', lang('cost'));
                    $sheet->SetCellValue('E1', lang('price'));
                    $sheet->SetCellValue('F1', lang('alert_quantity'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $product = $this->inventory_model->getProductByID($id);
                        $tax_rate = $this->settings_model->getTaxRateByID($product->tax_rate);
                        $quantity = $product->quantity;

                        $sheet->SetCellValue('A' . $row, $product->name);
                        $sheet->SetCellValue('B' . $row, $product->code);
                        $sheet->SetCellValue('C' . $row, ($product->model_name));
                        $sheet->SetCellValue('D' . $row, $this->repairer->formatDecimal($product->cost));
                        $sheet->SetCellValue('E' . $row, $product->price);
                        $sheet->SetCellValue('F' . $row, $product->alert_quantity);
                        $row++;
                    }


                    $sheet->getColumnDimension('A')->setWidth(30);
                    $sheet->getColumnDimension('B')->setWidth(20);
                    $sheet->getColumnDimension('C')->setWidth(35);
                    $sheet->getColumnDimension('D')->setWidth(10);
                    $sheet->getColumnDimension('E')->setWidth(10);
                    $sheet->getColumnDimension('F')->setWidth(10);
                    $sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    $filename = 'products_' . date('Y_m_d_H_i_s');
                    
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
                        $sheet->getStyle('A0:F'.($row-1))->applyFromArray($styleArray);
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
                $this->session->set_flashdata('error', $this->lang->line("no_product_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
     
    public function count_stock(){
        $this->mPageTitle = 'Count Stock';
        if ($this->input->post('type')) {
            $this->load->model('Countstock_model');
            $type = $this->input->post('type');
            $rows = $this->Countstock_model->getAllProductNames($type);

            $items = array();
            foreach ($rows as $row) {
                $id = $row->id;
                $type = $row->type;
                $row = $this->Countstock_model->getProductDataByTypeAndID($type, $id);
                if (!empty($row)) {
                    foreach ($this->Countstock_model->getProductDataByTypeAndID($type, $id) as $row) {
                        $row['humanized_type'] = humanize($row['type']);
                        $items[$row['item_id']] = $row;
                    }
                }
            }
            echo '
                    <script>
                        localStorage.setItem("countitems", JSON.stringify('.json_encode($items).'));
                        localStorage.setItem("count_start", false);
                        window.location.href = "'.base_url('panel/inventory/count_initiate').'";
                    </script>
                ';
            die();
        }
        
        $this->render('inventory/count_stock');
    }
    public function count_initiate(){
        $this->mPageTitle = 'Count Stock';
        $this->render('inventory/count_initiate');
    }

    public function count_save(){
        $this->mPageTitle = 'Count Save';
        if (isset($_POST['wrong_upc_name'])) {
            $i = sizeof($_POST['wrong_upc_name']);
            for ($r = 0; $r < $i; $r++) {
                $wrong_upc_name = $_POST['wrong_upc_name'][$r];
                $wrong_upc_explanation = $_POST['wrong_upc_explanation'][$r];
                $wrong_upcs[] = array(
                    'name' => $wrong_upc_name,
                    'explanation' => $wrong_upc_explanation,
                );
            }
        }
        $data = array(
            'date' => date('Y-m-d H:i:s'),
            'wrong_upcs' => !empty($wrong_upcs) ? json_encode($wrong_upcs) : NULL,
        );
        $this->db->insert('count_stock', $data);
        $count_id = $this->db->insert_id();
        $items = array();
        $i = sizeof($_POST['product_id']);
        for ($r = 0; $r < $i; $r++) {
            $product_id = $_POST['product_id'][$r];
            $product_name = $_POST['product_name'][$r];
            $product_type = $_POST['product_type'][$r];
            $product_code = $_POST['code'][$r];
            $total_cost = $_POST['total_cost'][$r];
            $selected_cost = $_POST['selected_cost'][$r];
            $category = $_POST['category'][$r];
            $sub_category = $_POST['sub_category'][$r];
            $serial = $_POST['serial'][$r];
            $counted_qty = $_POST['counted_qty'][$r];
            $item_qty = $_POST['item_qty'][$r];

            $items[] = array(
                'count_stock_id' => $count_id,
                'product_id' => $product_id,
                'product_name' => $product_name,
                'product_type' => $product_type,
                'product_code' => $product_code,
                'total_cost' => $total_cost,
                'cost_selected' => $selected_cost,
                'category' => $category,
                'sub_category' => $sub_category,
                'serial' => ($serial == NULL OR $serial == 'null') ? NULL : $serial,
                'counted_qty' => $counted_qty,
                'total_qty' => $item_qty,
                'difference' => $item_qty-$counted_qty,
            );
        }

        $this->db->insert_batch('count_stock_items', $items);
        redirect('panel/inventory/counted_stock?reset=true');
    }
    public function counted_stock(){
        $this->render('inventory/counted_stock');
    }


    public function getCountedStock(){
        $this->load->library('datatables');
        $this->datatables
            ->select("date, wrong_upcs, id")
            ->from('count_stock');
        echo $this->datatables->generate();
    }

    public function stocked_items($id){
        $this->db->group_by('product_id, product_type');
        $q = $this->db->select('*, GROUP_CONCAT(serial  SEPARATOR " ,") as serial, SUM(total_qty) as total_qty, SUM(counted_qty) as counted_qty, SUM(total_qty)-SUM(counted_qty) as difference')->get_where('count_stock_items', array('count_stock_id'=> $id));
        $data['result'] = $q->result();
        $this->load->view($this->theme.'inventory/stocked_items', $data);
    }

    public function getModels($term = null)
    {
        if ($term) {
            $this->db->like('model_name', $term);
        }
        $q = $this->db->select('*, model_name as name')->get('repair');
        $names = array();
        if ($q->num_rows() > 0) {
            $names = $q->result_array();
        }
        echo $this->repairer->send_json($names);
    }
    

	public function getInventoryByID()
	{
		$id = $this->input->post('id');
        $row = $this->db->where('id', $id)->get('inventory')->row();
        $row->variants = $this->inventory_model->getProductVariantsByID($id);

		echo $this->repairer->send_json($row);
	}

}