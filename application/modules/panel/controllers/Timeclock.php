<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }
/**
 * Time Clock
 *
 *
 * @package     Repair
 * @category    Controller
 * @author      Usman Sher
*/

class Timeclock extends Auth_Controller
{
    // THE CONSTRUCTOR //
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->render('timeclock/index');
    }

    public function verifyTimeLogging()
    {
        $pin_code = $this->input->post('entry_pin_code');
        $row = $this->db->get_where('users', array('pin_code' => $pin_code));
        if ($row->num_rows() > 0) {
            $user = $row->row();
            $get_record = $this->db->get_where('timeclock', array('user_id' => $user->id, 'clock_out'=>NULL, 'store_id'=> $this->activeStore));
            if ($get_record->num_rows() > 0) {
                echo 'clock_out';
            }else{
                echo 'clock_in';
            }
        }else{
            echo "pin_incorrect";
        }
           
    }

    public function view()
    {
        if (!$this->input->post('pin_code')) {
            $this->session->set_flashdata('warning', lang('Please re-enter your pin number'));
            redirect('panel/timeclock');
        }
       
        $this->data['pin_code']     = $this->input->post('pin_code');
        $this->data['sort_by']      = $this->input->post('sort_by');
        $this->data['sort_with']    = $this->input->post('sort_with');
        $this->data['from_date']    = urlencode(date('Y-m-d').' 00:00:00');
        $this->data['to_date']    = urlencode(date('Y-m-d').' 23:59:59');
        if ($this->input->post('date_range')) {
            $date_range = json_decode($this->input->post('date_range'));
            $this->data['from_date']    = urlencode($date_range->start.' 00:00:00');
            $this->data['to_date']      = urlencode($date_range->end.' 23:59:59');
        }
        $q = $this->db->get_where('users', array('pin_code' => $this->input->post('pin_code')));
        if ($q->num_rows() > 0) {
            $user = $q->row();
            $check_id = $user->id;
            $GP = $this->settings_model->getGroupPermissions($user->id);
            if ($GP) {
                $perms = $GP[0];
            }
        }
        if ($this->input->post('sort_by') == 'group') {
             if (isset($perms)) {
                if ($perms['timeclock-ownstore']) {
                    $this->db->where('timeclock.store_id', $this->activeStore);
                }else{
                    $this->db->where_in('timeclock.store_id', explode(',', $perms['timeclock-stores']));
                }
            }
            
            $this->db->where("clock_in BETWEEN '".urldecode($this->data['from_date'])."' AND '".urldecode($this->data['to_date'])."'");
            $this->data['users'] = $this->db->select('user_id')->group_by('user_id')->where('group_id', $this->input->post('sort_with'))->get('timeclock')->result();

        }elseif ($this->input->post('sort_by') == 'all') {
            if (isset($perms)) {
                if ($perms['timeclock-ownstore']) {
                    $this->db->where('timeclock.store_id', $this->activeStore);
                }else{
                    $this->db->where_in('timeclock.store_id', explode(',', $perms['timeclock-stores']));
                }
            }

            $this->db->where("clock_in BETWEEN '".urldecode($this->data['from_date'])."' AND '".urldecode($this->data['to_date'])."'");
            $this->data['users'] = $this->db->select('user_id')->group_by('user_id')->get('timeclock')->result();
        }else{
            $this->data['users'] = NULL;
        }

        $this->render('timeclock/view');
    }

    public function add_entry()
    {
        $pin_code = $this->input->post('entry_pin_code');
        $row = $this->db->get_where('users', array('pin_code' => $pin_code));
        if ($row->num_rows() > 0) {
            $user = $row->row();
            $get_record = $this->db->get_where('timeclock', array('user_id' => $user->id, 'clock_out'=>NULL, 'store_id' => $this->activeStore));
            if ($get_record->num_rows() > 0) {
                $clock_out = date("Y-m-d H:i:s");
                $data = array(
                    'clock_out' => $clock_out,
                );
                $this->db->where('id', $get_record->row()->id);
                $this->db->update('timeclock', $data);
            }else{
                $clock_in = date("Y-m-d H:i:s");
                $clock_out = NULL;
                $data = array(
                    'user_id'   => $user->id,
                    'group_id'  => $user->group_id,
                    'name'      => $user->first_name.' '.$user->last_name,
                    'clock_in'  => $clock_in,
                    'clock_out' => $clock_out,
                    'store_id'  => $this->activeStore,
                );
                $this->db->insert('timeclock', $data);
            }
            redirect();
        }else{
            echo "pin_incorrect";
        }

    }
    public function add_mEntry() {
        $user_id = $this->input->post('meuser');
        $row = $this->db->get_where('users', array('id' => $user_id));
        $user = $row->row();
        $clock_in = $this->repairer->mdytoymd2($this->input->post('clock_in'));
        $clock_out = $this->repairer->mdytoymd2($this->input->post('clock_out'));
        // $group_id = $this->settings_model->getGroupByID($user->group_id);
        
        $data = array(
            'user_id'   => $user_id,
            'group_id'  => $user->group_id,
            'name'      => $user->first_name.' '.$user->last_name,
            'clock_in'  => $clock_in,
            'clock_out' => $clock_out,
            'store_id'  => $this->activeStore,
        );
        $this->db->insert('timeclock', $data);
    }

    public function getAllRecords($id = NULL)
    {
        $pin_code   = $this->input->post('pin_code');
        $this->load->library('datatables');

        $perms = NULL;
        $q = $this->db->get_where('users', array('pin_code' => $pin_code));
        if ($q->num_rows() > 0) {
            $user = $q->row();
            $check_id = $user->id;
            $GP = $this->settings_model->getGroupPermissions($user->id);
            $perms = $GP[0];
            if ($perms) {
                if ($perms['timeclock-ownstore']) {
                    $this->db->where('timeclock.store_id', $this->activeStore);
                }else{
                    $this->db->where_in('timeclock.store_id', explode(',', $perms['timeclock-stores']));
                }
            }
        }

        $sort_by    = $this->input->post('sort_by');
        $sort_with  = $this->input->post('sort_with');
        $from_date  = $this->input->post('from_date');
        $to_date    = $this->input->post('to_date');

        if ($from_date && $to_date) {
            $from_date = urldecode($from_date);
            $to_date = urldecode($to_date);
            $this->datatables->where("clock_in BETWEEN '$from_date' AND '$to_date'");
        }

        if ($sort_by == 'user') {
            $this->datatables->where('user_id', $sort_with);
        }elseif($sort_by == 'group'){
            $this->datatables->where('users.group_id', $sort_with);
        }
        if ($id) {
            $this->datatables->where('user_id', $id);
        }

        $q = $this->datatables->select('name as name, timeclock.id as id, 
            timeclock.clock_in as clock_in,
            timeclock.clock_out as clock_out,
            IFNULL((time_to_sec(timediff(timeclock.clock_out, timeclock.clock_in)) / 3600), 0) as total_hours')
            ->join('users', 'users.id=timeclock.user_id', 'left')
            ->from('timeclock');

        $edit = '-';
        $delete = '-';
        if (($perms && $perms['timeclock-edit'])) {
            $edit = '<button class="modify btn btn-warning" data-num="$1"><i class="fas fa-edit"></i> '.lang('edit').'</button>';
        }
        if (($perms && $perms['timeclock-delete'])) {
            $delete = '<button class="delete btn btn-danger" data-num="$1"><i class="fas fa-trash"></i> '.lang('delete').'</button>';
        }

        $this->datatables->add_column('actions', '<div class="btn-group">'.$edit.$delete.'</div>', 'id');
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }

    public function getRecordByID()
    {
        $this->db->where('id', $this->input->post('id'));
        $q = $this->db->select('*, clock_in, clock_out')->get('timeclock');
        echo json_encode($q->row());
    }
    public function edit_entry()
    {
        $this->load->library('repairer');
        $pin_code    = $this->input->post('pin_code');
        $editor_id = $this->db->get_where('users', array('pin_code' => $pin_code))->row()->id;

        $clock_in = $this->repairer->mdytoymd2($this->input->post('clock_in'));
        $clock_out = $this->repairer->mdytoymd2($this->input->post('clock_out'));
        $data = array(
            'editor_id' => $editor_id,
            'edit_time' => date("Y-m-d H:i:s"),
            'clock_in' => $clock_in,
            'clock_out' => $clock_out,
        );
        $this->db->where('id', $this->input->post('id'));
        $q = $this->db->update('timeclock', $data);
        echo 'true';
    }
    public function getSortMenu()
    {
        $this->data['perms'] = NULL;
        $this->data['groups'] = NULL;
        $final = array();
        $pin_code = $this->input->post('pin_code');
        $q = $this->db->get_where('users', array('pin_code' => $pin_code));
        if ($q->num_rows() > 0) {
            $user = $q->row();
            $this->data['pin_code'] = $pin_code;
            $this->data['admin'] = FALSE;
            $GP = $this->settings_model->getGroupPermissions($user->id);
            if (!$GP) {
                $this->data['admin'] = TRUE;
            }else{
                $perms = $GP[0];
                $groups = array_filter(explode(',', $perms['timeclock-groups']));
                if (empty($groups)) {
                    $final['success'] = true;
                    $final['user_id'] = $user->id;
                    $final['html'] = NULL;
                    echo json_encode($final);
                    die();
                }
                $this->data['perms'] = $perms;
                $this->data['groups'] = implode(',', $groups);
            }
            $this->data['users'] = $this->db->select('CONCAT(first_name, " ", last_name) as name, id')->where('hidden', 0)->where('active', 1)->get('users')->result();
            $html = $this->load->view($this->theme.'timeclock/sort_menu', $this->data, TRUE);
            $final['success'] = true;
            $final['html'] = $html;
        }else{
            $final['success'] = false;
        }
        echo json_encode($final);

    }
    public function delete_entry()
    {
        $id = $this->input->post('id');
        if ($id) {
            $this->db->where('id', $id);
            $this->db->delete('timeclock');
            echo "true";
        }else{
            echo "false";
        }
       
    }
   
    public function json_sort()
    {
        $term = $this->input->post('id', true);
        $rows = array();
        $q = NULL;
        if ($term == 'user') {
            if ($this->input->post('group')) {
                $groups = explode(',', $this->input->post('group'));
                foreach ($groups as $group) {
                    foreach ($this->ion_auth->users($group)->result() as $user) {
                        if ($user->hidden) continue;
                        $data = array(
                            'id' => $user->id,
                            'name' => $user->first_name . " " . $user->last_name,
                        );
                        $rows[] = $data;
                    }
                }
            }else{
                foreach ($this->ion_auth->users()->result() as $user) {
                    if ($user->hidden) continue;
                    $data = array(
                        'id' => $user->id,
                        'name' => $user->first_name . " " . $user->last_name,
                    );
                    $rows[] = $data;
                }
            }
        }elseif($term == 'group'){
            if ($this->input->post('group')) {
                $groups = $this->input->post('group');
                $this->db->where_in('id', explode(',', $groups));
            }
            $q = $this->db->get('groups');
            if ($q->num_rows() > 0) {
                $rows = $q->result_array();
            }
        }
            
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $pr[] = array('id' => $row['id'], 'text' => $row['name']);
            }
            $this->repairer->send_json($pr);
        }else {
            $this->repairer->send_json((array('id' => 0, 'text' => lang('no_match_found'))));
        }
    }
    
}

