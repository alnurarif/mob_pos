<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Deposits extends Auth_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('pos_inventory_model');
        $this->load->model('deposits_model');

    }

    public function index() {
        $this->repairer->checkPermissions();
        $this->render('accounts/deposits/index');
    }

    public function getAllDeposits() {
        $this->repairer->checkPermissions('index');

        $user = $this->ion_auth->get_user_id();


        $delete_link = '';
        $edit_link = '';
        if($this->Admin || $this->GP['deposit-edit']){
            $edit_link = '<li>' . anchor('panel/deposits/edit/$1', '<i class="fas fa-edit"></i> ' . lang('edit')) . '</li>' ;
        }
        if($this->Admin || $this->GP['deposit-delete']){
            $delete_link = "<li><a href='#' class='po' title='<b>" . lang('delete') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger btn-icon po-delete' href='" . site_url('panel/deposits/delete/$1') . "'> <i class='fa fa-trash img-circle text-danger'></i> "
            . lang('i_m_sure') . "</a> <button class='btn po-close btn-primary btn-icon'><i class='fa fa-trash img-circle text-muted'></i> " . lang('no') . "</button>\"  rel='popover'><i class=\"fas fa-trash\"></i> "
            . lang('delete') . "</a></li>";
        }

        
           
            $action = '<div class="text-center"><div class="btn-group text-left">'
                . '<button type="button" class="btn btn-info btn-round dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
                . lang('actions') . ' <span class="caret"></span></button>
            <ul class="dropdown-menu " role="menu">'.
                  $edit_link .
                  $delete_link  .
            '</ul>
        </div></div>';

      
        $this->load->library('datatables');


        $start_date = null;
        $end_date = false;
        if ($this->input->post('start_date')) {
            $start_date = $this->input->post('start_date');
        }
        if ($this->input->post('end_date')) {
            $end_date = $this->input->post('end_date');
        }
        
        if ($start_date) {
            $this->datatables->where('account_entries.created_at >=', $start_date);
        }
        if ($end_date) {
            $this->datatables->where('account_entries.created_at <=', $end_date);
        }

        $this->datatables->where('account_entrytypes.store_id', (int)$this->session->userdata('active_store'));
        $this->datatables
            ->select("account_entrytypes.name as deposit_type, account_entries.amount, account_entries.date, account_entries.notes,accounts.title, account_entries.fund_type, (SELECT GROUP_CONCAT(CONCAT(entry_attachments.id, '___', entry_attachments.label, '___', entry_attachments.filename), ',')  FROM entry_attachments WHERE entry_attachments.entry_id=account_entries.id) as files, account_entries.id as actions")
            ->where('account_entries.type', 'deposit')
            ->join("account_entrytypes", 'account_entries.type_id=account_entrytypes.id', 'left')
            ->join("accounts", 'accounts.id=account_entries.bank_id', 'left')
            ->from('account_entries');
        $this->datatables->edit_column("actions", $action, "actions");
        $this->datatables->edit_column("files", '$1', "unserialize_files(files)");
        
        echo $this->datatables->generate();
    }



    public function add() {
        $this->repairer->checkPermissions();

        $this->form_validation->set_rules('type_id', $this->lang->line("deposit_type"), 'required');

        if ($this->form_validation->run() == true) {
            $name = '';
            if ($customer = $this->settings_model->getCustomerByID($this->input->post('customer'))) {
                $name = $customer->first_name . ' ' . $customer->last_name;
            }

            $date = $this->repairer->fsd(trim($this->input->post('date')));
            $data = array(
                'type' => 'deposit',
                'type_id' => $this->input->post('type_id'),
                'amount' => $this->input->post('amount'),
                'date' => date('Y-m-d', strtotime($date)),
                'recurring' => $this->input->post('recurring'),
                'notes' => $this->input->post('notes'),
                'user_id' => $this->mUser->id,
                'bank_id' => $this->input->post('bank_account'),
                'fund_type' => $this->input->post('fund_type'),
                'to_from_id' => $this->input->post('customer'),
                'to_from_name' => $name,
                'created_at' => date('Y-m-d H:i:s'),
                'store_id' => (int)$this->session->userdata('active_store'),
            );

            if ($data['recurring'] == 1) {
                $data['recur_frequency'] = $this->input->post('recur_frequency');

                $recur_start_date = $this->repairer->fsd(trim($this->input->post('recur_start_date')));
                $recur_start_date = date('Y-m-d', strtotime($recur_start_date));
                $data['recur_start_date'] = $recur_start_date;

                if (!empty($this->input->post('recur_end_date'))) {
                    $recur_end_date = $this->repairer->fsd(trim($this->input->post('recur_end_date')));
                    $recur_end_date = date('Y-m-d', strtotime($recur_end_date));
                    $data['recur_end_date'] = $recur_end_date;
                }
                $data['recur_next_date'] = date_format(date_add(date_create($recur_start_date),
                date_interval_create_from_date_string($this->input->post('recur_frequency') . ' ' . $this->input->post('recur_type') . 's')),
                'Y-m-d');
                $data['recur_type'] = $this->input->post('recur_type');
            }


            $this->db->insert('account_entries', $data);
            $id = $this->db->insert_id();

            $attachments = $this->input->post('attachment_data') ? $this->input->post('attachment_data') : NULL;
            if ($attachments) {
                $attachments = explode(',', $attachments);
                $this->db
                    ->where_in('id', $attachments)
                    ->update('entry_attachments', array('entry_id'=>$id));
            }


            $this->settings_model->addLog('add', 'deposit', $id, json_encode(array(
                'data' => $data,
                'attachments' => $attachments,
            )));
            $this->session->set_flashdata('message', lang('deposit successfully added'));
            redirect('deposits/index');

        }else{

            $this->data['bank_accounts'] = $this->settings_model->getAllBankAccountsDP();
            $this->data['deposit_types'] = $this->deposits_model->getAllTypes();
            $this->render('accounts/deposits/add');
        }
    }

    public function edit($id) {
        $this->repairer->checkPermissions();

        $this->form_validation->set_rules('type_id', $this->lang->line("deposit_type"), 'required');
        $deposit = $this->deposits_model->getDepositByID($id);

        if ($this->form_validation->run() == true) {
            $name = '';
            if ($customer = $this->settings_model->getCustomerByID($this->input->post('customer'))) {
                $name = $customer->first_name . ' ' . $customer->last_name;
            }

            $date = $this->repairer->fsd(trim($this->input->post('date')));
            $data = array(
                'type' => 'deposit',
                'type_id' => $this->input->post('type_id'),
                'amount' => $this->input->post('amount'),
                'date' => date('Y-m-d', strtotime($date)),
                'recurring' => $this->input->post('recurring'),
                'fund_type' => $this->input->post('fund_type'),
                'notes' => $this->input->post('notes'),
                'bank_id' => $this->input->post('bank_account'),
                'to_from_id' => $this->input->post('customer'),
                'to_from_name' => $name,
            );

            if ($data['recurring'] == 1) {

                $data['recur_frequency'] = $this->input->post('recur_frequency');

                $recur_start_date = $this->repairer->fsd(trim($this->input->post('recur_start_date')));
                $recur_start_date = date('Y-m-d', strtotime($recur_start_date));
                $data['recur_start_date'] = $recur_start_date;

                if (!empty($this->input->post('recur_end_date'))) {
                    $recur_end_date = $this->repairer->fsd(trim($this->input->post('recur_end_date')));
                    $recur_end_date = date('Y-m-d', strtotime($recur_end_date));
                    $data['recur_end_date'] = $recur_end_date;
                }
                $data['recur_next_date'] = date_format(date_add(date_create($recur_start_date),
                date_interval_create_from_date_string($this->input->post('recur_frequency') . ' ' . $this->input->post('recur_type') . 's')),
                'Y-m-d');
                $data['recur_type'] = $this->input->post('recur_type');
            }


            $this->db->where('id', $id)->update('account_entries', $data);
            $this->settings_model->addLog('edit', 'deposit', $id, json_encode(array(
                'data' => $data,
            )));
            $this->session->set_flashdata('message', lang('deposit successfully updated'));
        }else{
            $this->data['bank_accounts'] = $this->settings_model->getAllBankAccountsDP();
            $this->data['deposit'] = $this->deposits_model->getDepositByID($id);
            $this->data['deposit_types'] = $this->deposits_model->getAllTypes();

            if($this->data['deposit']->sale_id){
                $this->session->set_flashdata('message', lang('cannot edit sale/purchase linked entry'));
            }
            $this->render('accounts/deposits/edit');
        }
        
    }

    public function delete($id)
    {
        $this->repairer->checkPermissions();

        $this->deposits_model->deleteDeposit($id);
        $this->settings_model->addLog('delete', 'deposit', $id, json_encode(array(
                
        )));
        $this->session->set_flashdata('message', lang('deposit successfully deleted'));
        redirect('panel/deposits','refresh');
    }

    public function types() {
        $this->repairer->checkPermissions('index', null, 'deposit_type');
        $this->render('accounts/deposits/entrytypes');
    }


    public function getAllEntryTypes() {
        $this->repairer->checkPermissions('index', null, 'deposit_type');

        if ($this->Admin || $this->GP['deposit_type-edit']) {
            $edit_link = "<a  data-dismiss='modal' id='edit_entrytype' href='#entrytypemodal' data-toggle='modal' data-num='$1'><i class='fas fa-edit'></i> ".lang('edit')."</a>";
        }
        if ($this->Admin || $this->GP['deposit_type-delete']) {
            $delete_link = "<a href='#' class='po' title='<b>" . lang('delete') . "</b>' data-content=\"<p>"
                . lang('r_u_sure') . "</p><button class='btn btn-danger btn-icon' id='delete_entrytype' data-num='$1'><i class='fa fa-trash img-circle text-danger'></i> "
                . lang('i_m_sure') . "</button> <button class='btn btn-icon btn-default po-close'><i class='fa fa-trash img-circle text-muted'></i> " . lang('no') . "</button>\"  rel='popover'><i class=\"fas fa-trash\"></i> "
                . lang('delete') . "</a>";
        }

            $action = '<div class="text-center"><div class="btn-group text-left">'
                . '<button type="button" class="btn btn-info btn-round dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
                . lang('actions') . ' <span class="caret"></span></button>
            <ul class="dropdown-menu " role="menu">'.
                '<li>' . $edit_link . '</li>
                <li>' . $delete_link . '</li>
            </ul>
        </div></div>';


        $start_date = null;
        $end_date = null;
      
       
        if ($this->input->post('start_date')) {
            $start_date = $this->input->post('start_date');
        }
        if ($this->input->post('end_date')) {
            $end_date = $this->input->post('end_date');
        }
   
        $this->load->library('datatables');

        $this->datatables
            ->where('store_id', $this->activeStore)
            ->where('type', 'deposit')
            ->select("name, description,id as actions", 'left')
            ->from('account_entrytypes');

        $this->datatables->edit_column("actions", $action, "actions");


        echo $this->datatables->generate();
    }




    public function getEntryTypeByID($id = null)
    {
        if (!$id) {
            $id = $this->input->post('id');
        }

        $q = $this->db->where('id', $id)->get('account_entrytypes');
        if ($q->num_rows() > 0) {
            echo $this->repairer->send_json($q->row());
        }else{
            echo $this->repairer->send_json(false);
        }
    }


    public function add_entrytype() {
        $this->repairer->checkPermissions('add', null, 'deposit_type');

        $this->form_validation->set_rules('name', lang('Name'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('description', lang('Description'), 'trim|xss_clean');
        if ($this->form_validation->run() == TRUE) {
                
            $data = array(
                'user_id' => $this->mUser->id,
                'created_at' => date('Y-m-d H:i:s'),
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'store_id' => $this->activeStore,
                'type' => 'deposit',
            );
            $this->db->insert('account_entrytypes', $data);
            $id = $this->db->insert_id();
            echo $this->repairer->send_json(array('success'=>true, 'msg'=>lang('entry type successfully added')));
        }else{
            echo $this->repairer->send_json(array('success'=>false, 'error'=>validation_errors()));
        }

    }

    public function edit_entrytype() {
        $this->repairer->checkPermissions('edit', null, 'deposit_type');

        $this->form_validation->set_rules('name', lang('Name'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('description', lang('Description'), 'trim|xss_clean');
        if ($this->form_validation->run() == TRUE) {
            $id = $this->input->post('id');
            $data = array(
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
            $this->db->where('id', $id)->update('account_entrytypes', $data);

            echo $this->repairer->send_json(array('success'=>true, 'msg'=>lang('entry type successfully updated')));
        }else{
            echo $this->repairer->send_json(array('success'=>false, 'error'=>validation_errors()));
        }
        
    }

    public function delete_entrytype() {

        $this->repairer->checkPermissions('delete', null, 'deposit_type');

        $id = $this->input->post('id');

        if ($id < 4) {
            echo $this->repairer->send_json(array('success'=>false, 'msg'=>lang('entry type not deleted')));
        }
        if($this->db->where('id', $id)->delete('account_entrytypes')){
            echo $this->repairer->send_json(array('success'=>true, 'msg'=>lang('entry type successfully deleted')));
        }else{
            echo $this->repairer->send_json(array('success'=>false, 'msg'=>lang('entry type not deleted')));

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
        $entry_id = $this->input->post('id') ? $this->input->post('id') : NULL;

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
            if (!$this->upload->do_upload('userfile')){
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
                    'entry_id' => $entry_id,
                );
                $this->db->insert('entry_attachments', $data);
                $uploaded_ids[] = $this->db->insert_id();
            }
            $output = ["success"=> true, 'data'=>json_encode($uploaded_ids)];
        } elseif ($success === false) {
            $output = ['error'=>lang('error_Contant_Admin')];
            foreach ($paths as $file) {
                unlink('files/'.$file);
            }
        } else {
            $output = ['error'=>lang('error_proccess_upload')];
        }

        $this->settings_model->addLog('upload-attachments', 'expenses', $entry_id, json_encode(array(
            'data' => $data,
            'output' => $output,
        )));
        echo json_encode(array_unique($output));
    }
    public function getAttachments()
    {
        $id = $this->input->post('id');
        $q = $this->db->get_where('entry_attachments', array('entry_id'=>$id));

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
        $q = $this->db->get_where('entry_attachments', array('id'=>$id));
        if ($q->num_rows() > 0) {
            $row = $q->row();
            $this->db->delete('entry_attachments', array('id'=>$id));
            unlink(FCPATH.'/files/'.$row->filename);
            $this->repairer->send_json(array('success'=>true));
            return true;
        }
        $this->repairer->send_json(array('success'=>false));
        return false;
    }

}