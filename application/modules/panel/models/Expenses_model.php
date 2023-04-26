<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Expenses_model extends CI_Model
{
   

    public function getExpensesSum() {
        $settings = $this->settings_model->getSettings();
        $start_date = dmtoymd($settings->accounts_year_start);
        $end_date = false;
        if ($this->input->post('start_date')) {
            $start_date = $this->input->post('start_date');
        }
        if ($this->input->post('end_date')) {
            $end_date = $this->input->post('end_date');
        }
        
        if ($start_date) {
            $this->db->where('account_entries.created_at >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('account_entries.created_at <=', $end_date);
        }

        $q = $this->db->where('type', 'expense')->select('SUM(amount) as amount')->get('account_entries');
        if ($q->num_rows() > 0) {
            return $q->row()->amount ? $q->row()->amount : 0;
        }
        return 0;
    }

    public function getIncomesSum() {
        $settings = $this->settings_model->getSettings();
        $start_date = dmtoymd($settings->accounts_year_start);
        $end_date = false;
        if ($this->input->post('start_date')) {
            $start_date = $this->input->post('start_date');
        }
        if ($this->input->post('end_date')) {
            $end_date = $this->input->post('end_date');
        }
        
        if ($start_date) {
            $this->db->where('incomes.created_at >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('incomes.created_at <=', $end_date);
        }

        $q = $this->db->select('SUM(amount) as amount')->get('incomes');
        if ($q->num_rows() > 0) {
            return $q->row()->amount ? $q->row()->amount : 0;
        }
        return 0;
    }

    public function getPaypalSum() {
        $settings = $this->settings_model->getSettings();
        $start_date = dmtoymd($settings->accounts_year_start);
        $end_date = false;
        if ($this->input->post('start_date')) {
            $start_date = $this->input->post('start_date');
        }
        if ($this->input->post('end_date')) {
            $end_date = $this->input->post('end_date');
        }
        
        if ($start_date) {
            $this->db->where('date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('date <=', $end_date);
        }


        $q = $this->db->select('net')->get('paypal_account_entries');
        if ($q->num_rows() > 0) {
            $total_amount = 0;
            foreach ($q->result() as $key => $value) {
                $total_amount += $value->net;
            }
            return $total_amount;
        }
        return 0;
    }

    public function getOtherIncomesSum() {
        $settings = $this->settings_model->getSettings();
        $start_date = dmtoymd($settings->accounts_year_start);
        $end_date = false;
        if ($this->input->post('start_date')) {
            $start_date = $this->input->post('start_date');
        }
        if ($this->input->post('end_date')) {
            $end_date = $this->input->post('end_date');
        }
        
        if ($start_date) {
            $this->db->where('account_entries.created_at >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('account_entries.created_at <=', $end_date);
        }



        $q = $this->db->where('type', 'deposit')->select('SUM(amount) as amount')->get('account_entries');
        if ($q->num_rows() > 0) {
            return $q->row()->amount ? $q->row()->amount : 0;
        }
        return 0;
    }

    public function getRecurringaccount_entriesSum() {
        $settings = $this->settings_model->getSettings();
        $start_date = dmtoymd($settings->accounts_year_start);
        $end_date = false;
        if ($this->input->post('start_date')) {
            $start_date = $this->input->post('start_date');
        }
        if ($this->input->post('end_date')) {
            $end_date = $this->input->post('end_date');
        }
        
        if ($start_date) {
            $this->db->where('account_entries.created_at >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('account_entries.created_at <=', $end_date);
        }


        $q = $this->db->where('recurring', 1)->select("if(account_entries.type='expense', (0 - account_entries.amount), account_entries.amount) as amount")->get('account_entries');
        if ($q->num_rows() > 0) {
            $total_amount = 0;
            foreach ($q->result() as $key => $value) {
                $total_amount += $value->amount;
            }
            return $total_amount;
        }
        return 0;
    }


    public function getAllFundsSUM() {
        $settings = $this->settings_model->getSettings();
        $start_date = dmtoymd($settings->accounts_year_start);
        $end_date = false;
        if ($this->input->post('start_date')) {
            $start_date = $this->input->post('start_date');
        }
        if ($this->input->post('end_date')) {
            $end_date = $this->input->post('end_date');
        }


        $total_paypal_amount = 0;
        if ($start_date && $end_date) {
            $q = $this->db->where('paypal_account_entries.date >=', $start_date)->select('SUM(net) as total_paypal_amount')->where('paypal_account_entries.date <=', $end_date)->get('paypal_account_entries');
        }else{
            $q = $this->db->select('SUM(net) as total_paypal_amount')->get('paypal_account_entries');
        }
        if ($q->num_rows() > 0) {
            $total_paypal_amount = $q->row()->total_paypal_amount ? $q->row()->total_paypal_amount : 0;
        }


        $q1 = $this->db
            ->select("funds.id as actions, funds.name, funds.notes, bank_accounts.name as bankname, if(funds.id = 4, IFNULL((SELECT SUM(amount) FROM account_entries WHERE account_entries.fund_id = funds.id ".($start_date && $end_date ? "AND (account_entries.created_at BETWEEN '$start_date' AND '$end_date')" : '')."), 0) + IFNULL((SELECT SUM(amount) FROM incomes WHERE incomes.fund_id = funds.id ".($start_date && $end_date ? "AND (DATE(incomes.created_at) BETWEEN '$start_date' AND '$end_date')" : '')."), 0) + $total_paypal_amount, IFNULL((SELECT SUM(amount) FROM account_entries WHERE account_entries.fund_id = funds.id ".($start_date && $end_date ? "AND (account_entries.created_at BETWEEN '$start_date' AND '$end_date')" : '')."), 0) + IFNULL((SELECT SUM(amount) FROM incomes WHERE incomes.fund_id = funds.id ".($start_date && $end_date ? "AND (DATE(incomes.created_at) BETWEEN '$start_date' AND '$end_date')" : '')."), 0)) as a")
            ->join('bank_accounts', 'bank_accounts.id=funds.bank_id', 'left')
            ->from('funds')->get();

        $total_amount = 0;
        if ($q1->num_rows() > 0) {
            foreach ($q1->result() as $key => $row) {
                $total_amount += (float) $row->a;
            }
        }
        return $total_amount;
    }



    public function getAllBankAccountsSUM() {
         $settings = $this->settings_model->getSettings();
        $start_date = dmtoymd($settings->accounts_year_start);
        $end_date = false;
        if ($this->input->post('start_date')) {
            $start_date = $this->input->post('start_date');
        }
        if ($this->input->post('end_date')) {
            $end_date = $this->input->post('end_date');
        }


        $total_paypal_amount = 0;
        if ($start_date && $end_date) {
            $q = $this->db->where('paypal_account_entries.date >=', $start_date)->select('SUM(net) as total_paypal_amount')->where('paypal_account_entries.date <=', $end_date)->get('paypal_account_entries');
        }else{
            $q = $this->db->select('SUM(net) as total_paypal_amount')->get('paypal_account_entries');
        }
        if ($q->num_rows() > 0) {
            $total_paypal_amount = $q->row()->total_paypal_amount ? $q->row()->total_paypal_amount : 0;
        }

        $q1 = $this->db
            ->select("bank_accounts.id as actions, bank_accounts.name, bank_accounts.notes, bank_accounts.opening_amount, 
                if(bank_accounts.id = 3, IFNULL((SELECT SUM(amount) FROM account_entries WHERE account_entries.fund_id = funds.id ".($start_date && $end_date ? "AND (account_entries.created_at BETWEEN '$start_date' AND '$end_date')" : '')."), 0) + IFNULL((SELECT SUM(amount) FROM incomes WHERE incomes.fund_id = funds.id ".($start_date && $end_date ? "AND (DATE(incomes.created_at) BETWEEN '$start_date' AND '$end_date')" : '')."), 0) + $total_paypal_amount, IFNULL((SELECT SUM(amount) FROM account_entries WHERE account_entries.fund_id = funds.id ".($start_date && $end_date ? "AND (account_entries.created_at BETWEEN '$start_date' AND '$end_date')" : '')."), 0) + IFNULL((SELECT SUM(amount) FROM incomes WHERE incomes.fund_id = funds.id ".($start_date && $end_date ? "AND (DATE(incomes.created_at) BETWEEN '$start_date' AND '$end_date')" : '')."), 0)) as a, if(bank_accounts.id = 3, IFNULL((SELECT SUM(amount) FROM account_entries WHERE account_entries.fund_id = funds.id ".($start_date && $end_date ? "AND (account_entries.created_at BETWEEN '$start_date' AND '$end_date')" : '')."), 0)  + bank_accounts.opening_amount + IFNULL((SELECT SUM(amount) FROM incomes WHERE incomes.fund_id = funds.id ".($start_date && $end_date ? "AND (DATE(incomes.created_at) BETWEEN '$start_date' AND '$end_date')" : '')."), 0) + $total_paypal_amount, IFNULL((SELECT SUM(amount) FROM account_entries WHERE account_entries.fund_id = funds.id ".($start_date && $end_date ? "AND (account_entries.created_at BETWEEN '$start_date' AND '$end_date')" : '')."), 0) + bank_accounts.opening_amount + IFNULL((SELECT SUM(amount) FROM incomes WHERE incomes.fund_id = funds.id ".($start_date && $end_date ? "AND (DATE(incomes.created_at) BETWEEN '$start_date' AND '$end_date')" : '')."), 0)) as b", 'left')
            ->group_by('bank_accounts.id')
            ->join('funds', 'funds.bank_id=bank_accounts.id', 'left')
            ->from('bank_accounts')->get();

        $total_amount = 0;
        if ($q1->num_rows() > 0) {
            foreach ($q1->result() as $key => $row) {
                $total_amount += (float) $row->b;
            }
        }
        return $total_amount;
    }


    public function getAllSuppliers() {
        $q = $this->db->get('suppliers');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[$row->id] = $row->company;
            }
            return $data;
        }
        return FALSE;
    }

   
    public function getAllTypes()
    {
        $data = array();
        $q = $this->db->get_where('account_entrytypes', array('type'=>'expense'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getAllCategories()
    {
        $data = array();
        $q = $this->db->get('expense_categories');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[$row->id] = $row->name;
            }
        }
        return $data;
    }


    public function getAllExpenses()
    {
        $data = array();
        $q = $this->db
        ->select('account_entries.*, account_entrytypes.name as etname, funds.name as fname, suppliers.name as sname')
        ->where('account_entries.type', 'expense')
        ->join('account_entrytypes', 'account_entries.type_id=account_entrytypes.id', 'left')
        ->join('funds', 'account_entries.fund_id=funds.id', 'left')
        ->join('suppliers', 'account_entries.to_from_id=suppliers.id', 'left')
        ->get('account_entries');
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return $data;
    }


    public function getAllFunds()
    {
        $data = array();
        $q = $this->db->get('funds');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[$row->id] = $row->name;
            }
        }
        return $data;
    }

    public function getExpenseByID($id)
    {
        $q = $this->db->where('id', $id)->get('account_entries');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function getSupplierByID($id)
    {
        $q = $this->db->where('id', $id)->get('suppliers');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

     public function getExpenseTypeByID($id)
    {
        $q = $this->db->where('id', $id)->get('account_entrytypes');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function getExpenseTypeByCategory($expense_category_id)
    {
        $q = $this->db->where('expense_category_id', $expense_category_id)->get('account_entrytypes');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getExpenseCategoryByID($id)
    {
        $q = $this->db->where('id', $id)->get('expense_categories');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function deleteExpense($id)
    {
        $this->db->where('id', $id)->delete('account_entries');
        return true;
    }

    
}