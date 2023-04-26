<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Notifications extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

      
        if (!$this->Admin) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->lang->load('notifications', $this->mSettings->language);
        $this->load->library('form_validation');
        $this->load->model('cmt_model');
    }

    public function add()
    {
        $this->form_validation->set_rules('comment', lang('comment'), 'required|min_length[3]');

        if ($this->form_validation->run() == true) {
            $data = [
                'comment'   => $this->input->post('comment'),
                'from_date' => $this->input->post('from_date') ? $this->repairer->fld($this->input->post('from_date')) : null,
                'till_date' => $this->input->post('to_date') ? $this->repairer->fld($this->input->post('to_date')) : null,
            ];
        } elseif ($this->input->post('submit')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('panel/notifications');
        }

        if ($this->form_validation->run() == true && $this->cmt_model->addNotification($data)) {
            $this->session->set_flashdata('message', lang('notification_added'));
            redirect('panel/notifications');
        } else {
            $this->data['comment'] = ['name' => 'comment',
                'id'                         => 'comment',
                'type'                       => 'textarea',
                'class'                      => 'form-control',
                'required'                   => 'required',
                'value'                      => $this->form_validation->set_value('comment'),
            ];
            $this->data['error']    = validation_errors();
            $this->load->view($this->theme . 'notifications/add', $this->data);
        }
    }

    public function delete($id = null)
    {
        if (!$this->Admin) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->cmt_model->deleteComment($id)) {
            if ($this->input->is_ajax_request()) {
                $this->repairer->send_json(['error' => 0, 'msg' => lang('notifications_deleted')]);
            }else{
                $this->session->set_flashdata('warning', lang('notifications_deleted'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function edit($id = null)
    {
        if (!$this->Admin) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }

        $this->form_validation->set_rules('comment', lang('notifications'), 'required|min_length[3]');

        if ($this->form_validation->run() == true) {
            $data = [
                'comment'   => $this->input->post('comment'),
                'from_date' => $this->input->post('from_date') ? $this->repairer->fld($this->input->post('from_date')) : null,
                'till_date' => $this->input->post('to_date') ? $this->repairer->fld($this->input->post('to_date')) : null,
            ];
        } elseif ($this->input->post('submit')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('panel/notifications');
        }

        if ($this->form_validation->run() == true && $this->cmt_model->updateNotification($id, $data)) {
            $this->session->set_flashdata('message', lang('notification_updated'));
            redirect('panel/notifications');
        } else {
            $comment = $this->cmt_model->getCommentByID($id);

            $this->data['comment'] = ['name' => 'comment',
                'id'                         => 'comment',
                'type'                       => 'textarea',
                'class'                      => 'form-control',
                'required'                   => 'required',
                'value'                      => $this->form_validation->set_value('comment', $comment->comment),
            ];

            $this->data['notification'] = $comment;
            $this->data['id']           = $id;
            $this->data['error']        = validation_errors();
            $this->load->view($this->theme . 'notifications/edit', $this->data);
        }
    }

    public function getNotifications()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, comment, date, from_date, till_date')
            ->from('notifications')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . base_url('panel/notifications/edit/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_notification') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . $this->lang->line('delete_notification') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . base_url('panel/notifications/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fas fa-trash\"></i></a></div>", 'id');
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }

    public function index()
    {
        if (!$this->Admin) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->render('notifications/index');
    }
}
