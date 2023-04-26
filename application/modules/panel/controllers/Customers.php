<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Customers
 *
 *
 * @package		Repairer
 * @category	Controller
 * @author		Usman Sher
*/

// Includes all customers controller

class Customers extends Auth_Controller
{
	// THE CONSTRUCTOR //
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Customers_model');
    }

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
        $this->db->update('clients', $data);
        echo json_encode(array('ret' => 'true', 'toggle' => $a));
    }

	// PRINT A CUSTOMERS PAGE //
    public function index($type = NULL)
    {
        $this->repairer->checkPermissions();

        if ($type === 'disabled' || $type === 'enabled') {
            $this->data['toggle_type'] = $type;
        }else{
            $this->data['toggle_type'] = NULL;
        }
        $this->render('clients/index');
    }

	// GENERATE THE AJAX TABLE CONTENT //
    public function getAllCustomers($type = NULL)
    {
        $this->load->library('datatables');
        $this->datatables
            // ->where('id !=', 1)
            ->select('id, CONCAT(first_name, " ", last_name ) as name, company, address, email, telephone, disable')
            ->from('clients');
        // if ($type === 'disabled') {
        //     $this->datatables->where('disable', 1);
        // } elseif($type === 'enabled') {
            $this->datatables->where('disable', 0);
        // }
        $this->datatables->where('(universal=1 OR store_id='.$this->activeStore.')', NUll, FALSE);

        $this->datatables->add_column('actions', "$1___$2", 'id, disable');
        $this->datatables->unset_column('id');
        $this->datatables->unset_column('disable');
        echo $this->datatables->generate();
    }
	
	// ADD A CUSTOMER //
    public function add()
    {

        $this->repairer->checkPermissions();

        $first_name = $this->input->post('first_name');
        $last_name = $this->input->post('last_name');
        $company = $this->input->post('company');
        $address = $this->input->post('address');
        $city = $this->input->post('city');
		$state = $this->input->post('state');
		$postal_code = $this->input->post('postal_code');
        $telephone = $this->input->post('telephone');
        $email = $this->input->post('email');
        $comment = $this->input->post('comment');
        $vat = $this->input->post('vat');
        $cf = $this->input->post('cf');
		$token = $this->input->post('token');
		
        $data = array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'company' => $company,
            'telephone' => $telephone,
            'address' => $address,
            'city' => $city,
			'state' => $state,
			'postal_code' => $postal_code,
            'email' => $email,
            'date' => date('Y-m-d H:i:s'),
            'comment' => $comment,
            'vat' => $vat,
            'cf' => $cf,
            'tax_exempt' => $this->input->post('tax_exempt', true),
            'universal' => $this->input->post('universal') ? $this->input->post('universal') : $this->mSettings->universal_clients,
            'store_id' => $this->activeStore,
        );

        $id = $this->Customers_model->insert_client($data);

        $this->repairer->send_json([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'company' => $company,
            'id' => $id,
        ]);

    }

    
    // EDIT CUSTOMER //
    public function editAjax()
    {   
        $this->form_validation->set_rules('first_name', lang('first_client_name'), 'required');
        if ($this->form_validation->run() == TRUE) {
            $id = $this->input->post('id', true);
            $first_name = $this->input->post('first_name', true);
            $last_name = $this->input->post('last_name', true);
            $company = $this->input->post('company', true);
            $address = $this->input->post('address', true);
            $city = $this->input->post('city', true);
            $state = $this->input->post('state', true);
            $postal_code = $this->input->post('postal_code', true);
            $telephone = $this->input->post('telephone', true);
            $email = $this->input->post('email', true);
            $comment = $this->input->post('comment', true);
            $vat = $this->input->post('vat', true);
            $cf = $this->input->post('cf', true);
            $telephone = preg_replace("/[^0-9]/", "", $telephone);

            $data = array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'company' => $company,
                'telephone' => $telephone,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'postal_code' => $postal_code,
                'email' => $email,
                'comment' => $comment,
                'vat' => $vat,
                'cf' => $cf,
                'tax_exempt' => $this->input->post('tax_exempt', true),
                'universal' => $this->input->post('universal') ? $this->input->post('universal') : $this->mSettings->universal_clients,
            );
            if ($this->Customers_model->edit_client($id, $data)) {
                echo $this->repairer->send_json(['data'=>$data, 'success'=>true, 'msg'=>lang('Client edited successfully')]);
            }else{
                echo $this->repairer->send_json(['data'=>$data, 'success'=>false, 'msg'=>lang('Error occured while editing Client')]);
            }

       }
    }

	// EDIT CUSTOMER //
    public function edit($id)
    {   
        $this->showPageTitle = false;
        if (!$this->Admin) {
            if (!$this->GP['customers-edit'] && !$this->GP['customers-view'] && !$this->GP['customers-internal_notes'] && !$this->GP['customers-activities'] && !$this->GP['customers-documents'] && !$this->GP['customers-purchase_history']) {
                    $this->repairer->checkPermissions();
            }
        }
        
        $client = $this->Customers_model->find_customer($id);
        if (!$client) {
            redirect('panel/customers');
        }
        $this->form_validation->set_rules('first_name', lang('first_client_name'), 'required');
        if ($this->form_validation->run() == TRUE) {
            $first_name = $this->input->post('first_name', true);
            $last_name = $this->input->post('last_name', true);
            $company = $this->input->post('company', true);
            $address = $this->input->post('address', true);
            $city = $this->input->post('city', true);
            $state = $this->input->post('state', true);
            $postal_code = $this->input->post('postal_code', true);
            $telephone = $this->input->post('telephone', true);
            $email = $this->input->post('email', true);
            $comment = $this->input->post('comment', true);
            $vat = $this->input->post('vat', true);
            $cf = $this->input->post('cf', true);
            $telephone = preg_replace("/[^0-9]/", "", $telephone);

            $data = array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'company' => $company,
                'telephone' => $telephone,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'postal_code' => $postal_code,
                'email' => $email,
                'comment' => $comment,
                'vat' => $vat,
                'cf' => $cf,
                'tax_exempt' => $this->input->post('tax_exempt', true),
                'universal' => $this->input->post('universal') ? $this->input->post('universal') : $this->mSettings->universal_clients,
                // 'store_id'  => $this->activeStore,
            );
            if ($this->Customers_model->edit_client($id, $data)) {
                $this->session->set_flashdata('message', lang('Client edited successfully'));
            }else{
                $this->session->set_flashdata('message', lang('Error occured while editing Client'));
            }
            redirect('panel/customers/edit/'.$id);

       }else{
            $this->data['activities'] = $this->settings_model->getAllActivities();
            $this->data['client'] = $client;
            $this->data['documents'] = $this->Customers_model->getDocuments($id);
            $this->render('clients/edit');
       }
    }

	// DELETE CUSTOMER 
    public function delete()
    {
		$id = $this->security->xss_clean($this->input->post('id', true));

        $data = $this->Customers_model->delete_clients($id);
        echo json_encode($data);
    }

	// GET CUSTOMER AND SEND TO AJAX FOR SHOW IT //
    public function getCustomerByID()
    {
        $id = $this->security->xss_clean($this->input->post('id', true));
		$data = $this->Customers_model->find_customer($id);
		$token = $this->input->post('token', true);
		// if($_SESSION['token'] != $token) die('CSRF Attempts');
        echo json_encode($data);
    }

    public function getAjax($show_walk = 'show'){

        $term = $this->input->get('q');
        if ($term) {
            $this->db->where("CONCAT(first_name, ' ', last_name) LIKE '%" . $term . "%' OR telephone LIKE '%" . $term . "%' OR  concat(first_name, ' ', last_name, ' ', telephone) LIKE '%" . $term . "%'");
        }
        $this->db->where('(universal=1 OR store_id='.$this->activeStore.')', NUll, FALSE);

        $this->db->select('id, CONCAT(first_name, " ", last_name) as name, telephone');
        $q = $this->db->get('clients');

        $data = array(); 
        if ($q->num_rows() > 0) {
           
            foreach ($q->result() as $client) {
                $data[] = array('id' => $client->id, 'text' => "$client->name ". preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $client->telephone) );              
            } 
        } else {
           $data[] = array('id' => '0', 'text' => 'No Client Found');
        }
       
        echo json_encode($data);
    }

    /******************************************************************************/
    /******************************************************************************/
    /******************************************************************************/


    public function getReturns($id)
    {
        $detail_link = anchor('panel/sales/modal_view/$1', '<i class="fas fa-file-text-o"></i> ' . "Sale Details", 'data-toggle="modal" data-target="#myModal"');
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
            <ul class="dropdown-menu pull-right" role="menu">
                <li>' . $detail_link . '</li>
            </ul>
        </div></div>';

        $this->load->library('datatables');
        $this->datatables->where('customer_id', $id);
        $this->datatables
            ->select("date, return_sale_ref, reference_no, biller, customer, surcharge, grand_total, id")
            ->from('sales');
        $this->datatables->where('sale_status', 'returned');
        $this->datatables->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
    }

    /******************************************************************************/

     function getAllSales($id)
    {
        $this->load->library('datatables');
        $this->load->library('repairer');

        $this->datatables->where('sales.customer_id', $id)->select("sales.id as id,LPAD(sales.id, 4, '0') as sale_id, date as date, customer, (SELECT 
                CASE
                    WHEN item_type = 'crepairs' THEN GROUP_CONCAT(CONCAT(product_name,' (Deposit)'))
                    WHEN item_type = 'drepairs' THEN GROUP_CONCAT(CONCAT(product_name,' (Repair Pickup)'))
                    WHEN item_type IN ('new_phone', 'used_phone') THEN GROUP_CONCAT(CONCAT(product_name,' (Phone Sold)'))
                    ELSE GROUP_CONCAT(product_name)
                END
            FROM sale_items WHERE sale_items.sale_id = sales.id) as name, (grand_total-total_tax) as total, total_tax, (grand_total)")
            ->from('sales')
            ->where('sale_status', 'completed')
            ->group_by('sales.id');

        $detail_link = anchor('panel/sales/modal_view/$1', '<i class="fas fa-file-text-o"></i> ' . "Sale Details", 'data-toggle="modal" data-target="#myModal"');
        $bill_link = '<a href="'.base_url('panel/pos/view/$1').'" >'.lang('View Sale').'</a>';
        $refund_link = '<a href="'.base_url('panel/sales/refund/$1').'" >'.lang('Refund').'</a>';
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
            <ul class="dropdown-menu pull-right" role="menu">
                <li>' . $detail_link . '</li>
                <li>' . $bill_link . '</li>
                <li>' . $refund_link . '</li>
            </ul>
        </div></div>';
        $this->datatables->add_column('actions', $action, 'id');
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }
    
    /******************************************************************************/

    public function getCustomerPurchases($id)
    {


        $user = $this->ion_auth->get_user_id();
        $edit_link = anchor('panel/purchases/customer_edit/$1', '<i class="fas fa-edit"></i> ' . lang('edit_purchase'));
        $delete_link = "<a href='#' class='po' title='<b>" . lang('delete_purchase') . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('panel/purchases/customer_delete/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fas fa-trash\"></i> "
        . lang('delete_purchase') . "</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . "Actions" . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $edit_link . '</li>
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';
        $this->load->library('datatables');
        $this->datatables->where('customer_id', $id);
        $this->datatables
            ->select("id, date as date, customer, status, grand_total")
            ->from('customer_purchases');
        $this->datatables->add_column("Actions", '$1___$2', "id, status");
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }

    /******************************************************************************/
    /******************************************************************************/
    /******************************************************************************/

    public function add_note()
    {
        $data = array(
            'client_id' => $this->input->post('client_id'),
            'subject' => $this->input->post('subject'),
            'note' => $this->input->post('note'),
            'user_id' => $this->ion_auth->user()->row()->id,
            'date' => date('Y-m-d H:i:s'),
        );
        $this->db->insert('client_notes', $data);
    }

    
    public function getAllNotes($id)
    {
        $this->load->library('datatables');
        $this->datatables->where('client_id', $id);
        $this->datatables
            ->select("date as date, CONCAT(users.first_name, ' ', users.last_name) as name, subject, client_notes.id as id")
            ->from('client_notes')
            ->join('users', 'users.id=client_notes.user_id', 'left');
        echo $this->datatables->generate();
    }

    public function view_note($id, $is_activity = FALSE)
    {
        if ($is_activity) {
            $q = $this->db->select('*, details as note, "'.lang('Activity Details').'" as subject')->get_where('client_activity', array('id'=>$id));
            if ($q->num_rows() > 0) {
                $data['note'] = $q->row();
                $this->load->view($this->theme.'clients/view_note', $data);
            }
        }else{
            $q = $this->db->get_where('client_notes', array('id'=>$id));
            if ($q->num_rows() > 0) {
                $data['note'] = $q->row();
                $this->load->view($this->theme.'clients/view_note', $data);
            }
        }
    }

    //
    public function add_document($client_id) {
        $this->load->library('upload');
        $this->upload_path = 'assets/uploads/documents';
        $this->image_types = 'png|jpg|gif|xls|xlsx|doc|docx|pdf';
        $this->allowed_file_size = 0;

        $config['upload_path'] = $this->upload_path;
        $config['allowed_types'] = $this->image_types;
        $config['max_size'] = $this->allowed_file_size;
        $config['overwrite'] = FALSE;
        $config['max_filename'] = 25;
        $config['encrypt_name'] = FALSE;
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('document')) {
            $error = $this->upload->display_errors();
            // echo '{"status":"error"}';
            show_404();
            exit;
        }else{
            $data = $this->upload->data();
            $idata = array(
                'client_id' => $client_id,
                'file_name' => $data['orig_name'],
            );
            $this->db->insert('client_documents', $idata);
            $iid = $this->db->insert_id();
            unset($idata);
            echo '{"status":"success", "file_name": "'.$data['orig_name'].'", "id": "'.$iid.'"}';
            exit;
        }
    }

    //
    public function delete_doc() {
        $id = $this->input->post('id');
        $record = $this->db->get_where('client_documents', array('id'=>$id))->row();
        $this->db->delete('client_documents', array('id'=>$id));
        $path = FCPATH.'assets/uploads/documents/'.$record->file_name;
        @unlink($path);
        echo true;
    }
    

    public function getAllActivities($id) {
        $this->load->library('datatables');
        $this->datatables->where('client_id', $id);
        $this->datatables
            ->select("a1.name as activity, a2.name as sub_activity, store.name as sname, client_activity.remind_date as remind_date, client_activity.priority as priority, client_activity.status as status, client_activity.id as id")
            ->join('activities as a1', 'client_activity.activity_id=a1.id')
            ->join('activities as a2', 'client_activity.subactivity_id=a2.id')
            ->join('store', 'client_activity.locations=store.id')
            ->from('client_activity');
        $this->datatables->edit_column("status", '$1___$2', "id, status");

        echo $this->datatables->generate();
    }
    public function activity_add($client_id)
    {
        $data = array(
            'client_id' => $client_id,
            'activity_id' => $this->input->post('activity_id'),
            'subactivity_id' => $this->input->post('sub_activity'),
            'locations' => $this->activeStore,
            'remind_date' => $this->repairer->fld(trim($this->input->post('remind_date'))),
            'priority' => $this->input->post('priority'),
            'status' => 'open',
            'details' => $this->input->post('activity_details') ? $this->input->post('activity_details') : '',
            'created_by' => $this->ion_auth->user()->row()->id,
            'created_at' => date('Y-m-d H:i:s'),
        );
        $this->db->insert('client_activity', $data);
        echo "true";
    }

    public function closeActivity()
    {
        if($this->input->post('id')) {
            $data = array(
                'status' => 'closed',
                'closed_at' => date('Y-m-d H:i:s'),
            );
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('client_activity', $data);
            echo "true";
        }else{
            echo "false";
        }
    }

    public function upload_attachments()
    {
        // 'images' refers to your file input name attribute
        if (empty($_FILES['upload_manager'])) {
            echo json_encode(['error'=>lang('upload_no_file')]); 
            // or you can throw an exception 
            return; // terminate
        }
        // get user id posted
        $client_id = $this->input->post('id') ? $this->input->post('id') : NULL;

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
                'upload_path'   => 'files/clients',
                'allowed_types' => 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt|mov',
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
                    'client_id' => $client_id,
                );
                $this->db->insert('attachments', $data);
                $uploaded_ids[] = $this->db->insert_id();
            }
            $output = ["success"=> true, 'data'=>json_encode($uploaded_ids)];
        } elseif ($success === false) {
            $output = ['error'=>lang('error_Contant_Admin')];
            foreach ($paths as $file) {
                unlink('files/clients/'.$file);
            }
        } else {
            $output = ['error'=>lang('error_proccess_upload')];
        }

        echo json_encode(array_unique($output));
    }
    public function getAttachments()
    {
        $id = $this->input->post('id');
        $q = $this->db->get_where('attachments', array('client_id'=>$id));

        $urls = array();
        $previews = array();
        if ($q->num_rows() > 0) {
            $result = $q->result();
            foreach ($result as $row) {
                $url = base_url().'files/clients/'.$row->filename;
                $burl = FCPATH.'files/clients/'.$row->filename;
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
            unlink(FCPATH.'/files/clients/'.$row->filename);
            $this->repairer->send_json(array('success'=>true));
            return true;
        }
        $this->repairer->send_json(array('success'=>false));
        return false;

   }

    // GENERATE THE AJAX TABLE CONTENT //
    public function getAllRepairs($client_id = NULL, $type=null)
    {
        $default_statuses = $this->settings_model->getRepairStatusesDefault();
        $this->load->library('datatables');
        $this->datatables
            ->select('disable, repair.id as id, serial_number, name, telephone, defect, model_name, date_opening as date_opening, if(status > 0, CONCAT(status.label, "____", status.bg_color, "____", status.fg_color, "____", status.id, "____" ,repair.id), "cancelled") as status, code, grand_total, paid, if(pos_sold = 1, "paid", "pending") as payment_status, CONCAT(warranty,"____",IFNULL(date_closing, 0)) as warranty')
            ->join('status', 'status.id=repair.status', 'left')
            ->from('repair');
        if ($type === 'disabled') {
            $this->datatables->where('disable', 1);
        } elseif($type === 'enabled') {
            $this->datatables->where('disable', 0);
        }elseif(is_numeric($type)) {
            $this->datatables->where('status', $type);
        }elseif($type === 'default' && $default_statuses) {
            $this->datatables->where_in('status', array_column($default_statuses, 'id'));
        }elseif($type === 'default' && !$default_statuses) {
            $this->datatables->where('status', false);
        }
        if($client_id) {
            $this->datatables->where('client_id', $client_id);

        }
        $this->datatables->where('store_id', $this->activeStore);
        $this->datatables->add_column('actions', "$1___$2", 'id, disable');
        $this->datatables->unset_column('id');
        $this->datatables->unset_column('disable');
        echo $this->datatables->generate();
    }


    
    function export_csv() {

        $q = $this->db
            ->select('id, CONCAT(first_name, " ", last_name) as name, company, address, email, telephone, city, postal_code, vat, comment')
            ->from('clients')->get();

        $customers = array();
        if ($q->num_rows() > 0) {
            $customers = $q->result();
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Customers');
        $sheet->SetCellValue('A1', lang('client_company'));
        $sheet->SetCellValue('B1', lang('name'));
        $sheet->SetCellValue('C1', lang('client_telephone'));
        $sheet->SetCellValue('D1', lang('client_email'));
        $sheet->SetCellValue('E1', lang('client_address'));
        $sheet->SetCellValue('F1', lang('client_city'));
        $sheet->SetCellValue('G1', lang('client_postal_code'));
        $sheet->SetCellValue('H1', lang('client_vat'));
        $sheet->SetCellValue('I1', lang('client_comment'));


        $row = 2;

        foreach ($customers as $customer) {
            $sheet->SetCellValue('A' . $row, $customer->company);
            $sheet->SetCellValue('B' . $row, $customer->name);
            $sheet->SetCellValue('C' . $row, $customer->telephone);
            $sheet->SetCellValue('D' . $row, $customer->email);
            $sheet->SetCellValue('E' . $row, $customer->address);
            $sheet->SetCellValue('F' . $row, $customer->city);
            $sheet->SetCellValue('G' . $row, $customer->postal_code);
            $sheet->SetCellValue('H' . $row, $customer->vat);
            $sheet->SetCellValue('I' . $row, $customer->comment);
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $filename = 'customers' . date('Y_m_d_H_i_s');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.csv"');
        header('Cache-Control: max-age=0');

        $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Csv');
        $writer->save('php://output');
        exit();

    }


}