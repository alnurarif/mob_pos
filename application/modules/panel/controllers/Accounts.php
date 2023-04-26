<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Accounts extends Auth_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('pos_inventory_model');

    }
    public function index() {
        $this->repairer->checkPermissions();
        $this->render('accounts/index');
    }


    public function getAllBankAccounts() {
        $this->repairer->checkPermissions('index');

        $edit_link = "<a  data-dismiss='modal' id='edit_bank' href='#bankmodal' data-toggle='modal' data-num='$1'><i class='fas fa-edit'></i> ".lang('edit')."</a>";
        $delete_link = "<a href='#' class='po' title='<b>" . lang('delete') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><button class='btn btn-danger btn-icon' id='delete_bank' data-num='$1'><i class='fa fa-trash img-circle text-danger'></i> "
            . lang('i_m_sure') . "</button> <button class='btn btn-icon btn-default po-close'><i class='fa fa-trash img-circle text-muted'></i> " . lang('no') . "</button>\"  rel='popover'><i class=\"fas fa-trash\"></i> "
            . lang('delete') . "</a>";

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
        $this->datatables->where('store_id', (int)$this->session->userdata('active_store'));
        $this->datatables
            ->select("accounts.title, accounts.description, accounts.opening_balance, (SELECT SUM(IF(type='deposit', amount, 0-amount)) FROM account_entries) + accounts.opening_balance  as total, accounts.id as actions", 'left')
            ->from('accounts');

        $this->datatables->edit_column("actions", $action, "actions");


        echo $this->datatables->generate();
    }




    public function getBankByID($id = null)
    {
        if (!$id) {
            $id = $this->input->post('id');
        }

        $q = $this->db->where('id', $id)->get('accounts');
        if ($q->num_rows() > 0) {
            echo $this->repairer->send_json($q->row());
        }else{
            echo $this->repairer->send_json(false);
        }
    }


    public function add_bank() {
        $this->repairer->checkPermissions();

        $this->form_validation->set_rules('title', lang('Title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('account_number', lang('Account Number'), 'trim|xss_clean');
        $this->form_validation->set_rules('contact_person', lang('Contact Person'), 'trim|xss_clean');
        $this->form_validation->set_rules('phone', lang('Phone'), 'trim|xss_clean');
        $this->form_validation->set_rules('opening_balance', lang('Opening Balance'), 'trim|xss_clean');
        if ($this->form_validation->run() == TRUE) {
            $data = array(
                'user_id' => $this->mUser->id,
                'created_at' => date('Y-m-d H:i:s'),
                'title' => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'account_number' => $this->input->post('account_number'),
                'contact_person' => $this->input->post('contact_person'),
                'phone' => $this->input->post('phone'),
                'opening_balance' => $this->input->post('opening_balance'),
                'store_id' => (int)$this->session->userdata('active_store'),
            );
            $this->db->insert('accounts', $data);
            $id = $this->db->insert_id();
            echo $this->repairer->send_json(array('success'=>true, 'msg'=>lang('bank account successfully added')));
        }else{
            echo $this->repairer->send_json(array('success'=>false, 'error'=>validation_errors()));
        }
    }

    public function edit_bank() {
        $this->repairer->checkPermissions();
        
        $this->form_validation->set_rules('id', lang('id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('title', lang('title'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('account_number', lang('account_number'), 'trim|xss_clean');
        $this->form_validation->set_rules('contact_person', lang('contact_person'), 'trim|xss_clean');
        $this->form_validation->set_rules('phone', lang('phone'), 'trim|xss_clean');
        $this->form_validation->set_rules('opening_balance', lang('opening_balance'), 'trim|xss_clean');
        
        if ($this->form_validation->run() == TRUE) {
            $id = $this->input->post('id');
            $data = array(
                'user_id' => $this->mUser->id,
                'title' => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'account_number' => $this->input->post('account_number'),
                'contact_person' => $this->input->post('contact_person'),
                'phone' => $this->input->post('phone'),
                'opening_balance' => $this->input->post('opening_balance'),
                'updated_at' => date('Y-m-d H:i:s'),
                'store_id' => (int)$this->session->userdata('active_store'),
            );
            $this->db->where('id', $id)->update('accounts', $data);
            echo $this->repairer->send_json(array('success'=>true, 'msg'=>lang('bank account successfully updated')));
        }else{
            echo $this->repairer->send_json(array('success'=>false, 'error'=>validation_errors()));
        }
    }

    public function delete_bank() {
        $this->repairer->checkPermissions();
        $id = $this->input->post('id');
        if ($this->db->where('id', $id)->delete('accounts')) {
            echo $this->repairer->send_json(array('success'=>true, 'msg'=>lang('bank account successfully deleted')));
        }else{
            echo $this->repairer->send_json(array('success'=>false, 'msg'=>lang('bank account not deleted')));
        }
    }


    public function funds() {
            $this->data['bank_accounts'] = $this->settings_model->getAllBankAccountsDP();

        $this->render('accounts/funds');
    }


    public function getFundByID($id = null)
    {
        if (!$id) {
            $id = $this->input->post('id');
        }

        $q = $this->db->where('id', $id)->get('funds');
        if ($q->num_rows() > 0) {
            echo $this->repairer->send_json($q->row());
        }else{
            echo $this->repairer->send_json(false);
        }
        // $data = $this->expenses_model->getExpenseTypeByID($id);
    }


    public function getAllFunds() {

        $edit_link = "<a data-dismiss='modal' id='edit_fund' href='#fundmodal' data-toggle='modal' data-num='$1'><i class='fas fa-edit'></i> ".lang('edit')."</a>";
        $delete_link = "<a href='#' class='po' title='<b>" . lang('delete') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><button class='btn btn-icon btn-danger' id='delete_fund' data-num='$1'><i class='fa fa-trash img-circle text-danger'></i> "
            . lang('i_m_sure') . "</button> <button class='btn btn-icon btn-default po-close'><i class='fa fa-reply img-circle text-muted'></i>" . lang('no') . "</button>\"  rel='popover'><i class=\"fas fa-trash\"></i> "
            . lang('delete') . "</a>";
           
            $action = '<div class="text-center"><div class="btn-group text-left">'
                . '<button type="button" class="btn btn-info btn-round dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
                . lang('actions') . ' <span class="caret"></span></button>
            <ul class="dropdown-menu " role="menu">'.
                '<li>' . $edit_link . '</li>
                <li>' . $delete_link . '</li>
            </ul>
        </div></div>';


        $start_date = false;
        $end_date = false;
        if ($this->input->post('start_date')) {
            $start_date = $this->input->post('start_date');
        }
        if ($this->input->post('end_date')) {
            $end_date = $this->input->post('end_date');
        }

       

        $this->load->library('datatables');
       

        $this->datatables
            ->select("funds.id as fund_id, funds.name, funds.notes, accounts.title as bankname, if(funds.id = 4, IFNULL((SELECT SUM(amount) FROM account_entries WHERE account_entries.fund_id = funds.id ".($start_date && $end_date ? "AND (account_entries.date BETWEEN '$start_date' AND '$end_date')" : '')."), 0), IFNULL((SELECT SUM(amount) FROM account_entries WHERE account_entries.fund_id = funds.id ".($start_date && $end_date ? "AND (account_entries.date BETWEEN '$start_date' AND '$end_date')" : '')."), 0)) as a")
            ->join('accounts', 'accounts.id=funds.bank_id', 'left')
            ->from('funds');
        $this->datatables->add_column("actions", $action, "fund_id");
        $this->datatables->unset_column("fund_id");
        echo $this->datatables->generate();
   }



    public function add_fund() {
        $data = array(
            'user_id' => $this->mUser->id,
            'name' => $this->input->post('name'),
            'notes' => $this->input->post('notes'),
            'bank_id' => $this->input->post('bank_id'),
            'created_at' => date('Y-m-d H:i:s'),
        );
        $this->db->insert('funds', $data);
        $id = $this->db->insert_id();

        $this->settings_model->addLog('add', 'fund', $id, json_encode(array(
            'data'=>$data,
        )));


        echo $this->repairer->send_json(array('success'=>true, 'msg'=>'fund successfully added'));
    }

    public function edit_fund() {
        $id = $this->input->post('id');
        $data = array(
            'user_id' => $this->mUser->id,
            'name' => $this->input->post('name'),
            'notes' => $this->input->post('notes'),
            'bank_id' => $this->input->post('bank_id'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        $this->db->where('id', $id)->update('funds', $data);
        $this->settings_model->addLog('update', 'fund', $id, json_encode(array(
            'data'=>$data,
        )));
        echo $this->repairer->send_json(array('success'=>true, 'msg'=>'fund successfully updated'));
    }

    public function delete_fund() {

        $id = $this->input->post('id');
        if ((int) $id > 7) {
            $fund = null;
            $q = $this->db->where('id', $id)->get('funds');
            if ($q->num_rows() > 0) {
                $fund = $q->row();
            }
            $this->db->where('id', $id)->delete('funds');
            $this->settings_model->addLog('delete', 'fund', $id, json_encode(array(
                'data'=>$fund,
            )));
            echo $this->repairer->send_json(array('success'=>true, 'msg'=>'fund successfully deleted'));
        }else{
            echo $this->repairer->send_json(array('success'=>false, 'msg'=>'fund cannot be deleted'));
        }
        
    }

}