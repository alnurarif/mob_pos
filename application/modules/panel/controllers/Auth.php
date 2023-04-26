<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends Auth_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

		$this->upload_path = 'assets/uploads/members';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '10720';
        $this->load->library('upload');

	}


	public function index()
    {
        if (!$this->loggedIn) {
            redirect('panel');
        }
        if (!$this->Admin) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'panel/welcome');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $this->render('auth/index');
    }


    public function getUsers()
    {
        if (!$this->Admin) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            $this->repairer->md();
        }

        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('users') . '.id as id, first_name, last_name, email, company, ' . $this->db->dbprefix('groups') . '.name, active')
            ->from('users')
            ->join('groups', 'users.group_id=groups.id', 'left')
            ->edit_column('active', '$1__$2', 'active, id')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . base_url('panel/auth/edit_user/$1') . "' class='tip' title='" . lang('edit_user') . "'><i class=\"fa fa-edit\"></i></a></div>", 'id');

        if (!$this->Admin) {
            $this->datatables->unset_column('id');
        }
        echo $this->datatables->generate();
    }

	// log the user out
	public function logout()
	{
		$this->data['title'] = "Logout";

		// log the user out
		$logout = $this->ion_auth->logout();

		// redirect them to the login page
		$this->session->set_flashdata('message', $this->ion_auth->messages());
		redirect('panel/login', 'refresh');
	}

	// change password
	public function change_password()
	{
		$this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
		$this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
		$this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');

		if (!$this->ion_auth->logged_in())
		{
			redirect('panel/login', 'refresh');
		}

		$user = $this->ion_auth->user()->row();

		if ($this->form_validation->run() == false)
		{
			// display the form
			// set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
			$this->data['old_password'] = array(
				'name' => 'old',
				'id'   => 'old',
				'type' => 'password',
				'class' => 'form-control'
			);
			$this->data['new_password'] = array(
				'name'    => 'new',
				'id'      => 'new',
				'type'    => 'password',
				'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
				'class' => 'form-control'
			);
			$this->data['new_password_confirm'] = array(
				'name'    => 'new_confirm',
				'id'      => 'new_confirm',
				'type'    => 'password',
				'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
				'class' => 'form-control'
			);
			$this->data['user_id'] = array(
				'name'  => 'user_id',
				'id'    => 'user_id',
				'type'  => 'hidden',
				'value' => $user->id,
				'class' => 'form-control'
			);

			// render
			$this->_render_page('auth/change_password', $this->data);
		}
		else
		{
			$identity = $this->session->userdata('identity');

			$change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

			if ($change)
			{
				//if the password was successfully changed
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				$this->logout();
			}
			else
			{
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('panel/auth/change_password', 'refresh');
			}
		}
	}

	// activate the user
  	public function activate($id, $code = false)
    {
        if ($code !== false) {
            $activation = $this->ion_auth->activate($id, $code);
        } elseif ($this->Admin) {
            $activation = $this->ion_auth->activate($id);
        }

        if ($activation) {
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            if ($this->Admin) {
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                redirect('panel/auth/login');
            }
        } else {
            $this->session->set_flashdata('error', $this->ion_auth->errors());
            redirect('panel/login/forgot_password');
        }
    }

	public function delete_user($id)
	{
		$this->repairer->checkPermissions();
		if($this->ion_auth->delete_user($id)){
			$this->session->set_flashdata('message', $this->ion_auth->messages());
		}else{
			$this->session->set_flashdata('message', $this->ion_auth->errors());
		}

		redirect('panel/auth');
	}
	
	public function deactivate($id = null)
    {
        $this->repairer->checkPermissions('users', true);
        $id = $this->config->item('use_mongodb', 'ion_auth') ? (string)$id : (int)$id;
        $this->form_validation->set_rules('confirm', lang('confirm'), 'required');

        if ($this->form_validation->run() == false) {
            if ($this->input->post('deactivate')) {
                $this->session->set_flashdata('error', validation_errors());
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                $this->data['csrf']     = $this->_get_csrf_nonce();
                $this->data['user']     = $this->ion_auth->user($id)->row();
                $this->load->view($this->theme . 'auth/deactivate_user', $this->data);
            }
        } else {
            if ($this->input->post('confirm') == 'yes') {
                if ($id != $this->input->post('id')) {
                    show_error(lang('error_csrf'));
                }

                if ($this->ion_auth->logged_in() && $this->Admin) {
                    $this->ion_auth->deactivate($id);
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                }
            }

            redirect($_SERVER['HTTP_REFERER']);
        }
    }

	// create a new user
	public function create_user()
    {
		$this->repairer->checkPermissions();

        $this->data['title'] = $this->lang->line('create_user_heading');


        $tables = $this->config->item('tables','ion_auth');
        $identity_column = $this->config->item('identity','ion_auth');
        $this->data['identity_column'] = $identity_column;

        // validate form input
        $this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required');
        $this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required');
        if($identity_column!=='email')
        {
            $this->form_validation->set_rules('identity',$this->lang->line('create_user_validation_identity_label'),'required|is_unique['.$tables['users'].'.'.$identity_column.']');
            $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email');
        }
        else
        {
            $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email|is_unique[' . $tables['users'] . '.email]');
        }
        // $this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'trim');
        $this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'trim');
        $this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');
		$this->form_validation->set_rules('pin_code', 'Pin Code', 'required|is_unique[users.pin_code]');


        if ($this->form_validation->run() == true)
        {
            $email    = strtolower($this->input->post('email'));
            $identity = ($identity_column==='email') ? $email : $this->input->post('identity');
            $password = $this->input->post('password');
			$phone = $this->input->post('phone');
			$phone = preg_replace('/\D+/', '', $phone);
    		$stores_json = json_encode($this->input->post('stores'));

            $additional_data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name'  => $this->input->post('last_name'),
                'company'    => $this->input->post('company'),
                'phone'      => $phone,
				'pin_code'   => $this->input->post('pin_code'),
				'image'		 => 'no_image.png',
				'stores'	 => $stores_json,
            	'all_stores' => $this->input->post('all_stores'),
            	'group_id' 	=> $this->input->post('group'),
            	'store_id' 	=> $this->activeStore,
            );
            if (isset($_FILES['user_image'])) {
                if ($_FILES['user_image']['size'] > 0) {
                    $config['upload_path'] = $this->upload_path;
                    $config['allowed_types'] = $this->image_types;
                    $config['max_size'] = $this->allowed_file_size;
                    $config['overwrite'] = FALSE;
                    $config['max_filename'] = 25;
                    $config['encrypt_name'] = TRUE;
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('user_image')) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        redirect("panel/auth/add");
                    }else{
                        $photo = $this->upload->file_name;
                        $additional_data['image'] = $photo;
                        $config = NULL;
                    }
                }
            }
        }
        if ($this->form_validation->run() == true && $this->ion_auth->register($identity, $password, $email, $additional_data))
        {
            // check to see if we are creating the user
            // redirect them back to the admin page
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect("panel/auth", 'refresh');
        }
        else
        {
            // display the create user form
            // set the flash data error message if there is one
            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

        	$frm_priv = $this->settings_model->getMandatory('user');
        	$this->data['frm_priv'] = $frm_priv;

            $this->data['first_name'] = array(
                'name'  => 'first_name',
                'id'    => 'first_name',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('first_name'),
                'class' => 'form-control',
                'required' => 'required',

            );

            $this->data['last_name'] = array(
                'name'  => 'last_name',
                'id'    => 'last_name',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('last_name'),
                'class' => 'form-control',
                'required' => 'required',

            );

            $this->data['identity'] = array(
                'name'  => 'identity',
                'id'    => 'identity',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('identity'),
                'class' => 'form-control',
                'required' => 'required',
            );
            $this->data['email'] = array(
                'name'  => 'email',
                'id'    => 'email',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('email'),
                'class' => 'form-control',
                'required' => 'required',
            );


            $this->data['company'] = array(
                'name'  => 'company',
                'id'    => 'company',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('company'),
                'class' => 'form-control'

            );
            
            $this->data['pin_code'] = array(
                'name'  => 'pin_code',
                'id'    => 'pin_code',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('pin_code'),
                'class' => 'form-control',
                'required' => 'required',
                'minlength' => 4,
                'maxlength' => 8,


            );

            $this->data['password'] = array(
                'name'  => 'password',
                'id'    => 'password',
                'type'  => 'password',
                'value' => $this->form_validation->set_value('password'),
                'class' => 'form-control',
                'required' => 'required',

            );
            $this->data['password_confirm'] = array(
                'name'  => 'password_confirm',
                'id'    => 'password_confirm',
                'type'  => 'password',
                'value' => $this->form_validation->set_value('password_confirm'),
                'class' => 'form-control',
                'required' => 'required',
                'data-parsley-equalto'=>"#password",
            );

			if ($frm_priv['company']) {
	        	$this->data['company']['required'] = 'required';
			}
        
            $groups=$this->ion_auth->groups()->result_array();
			$this->data['groups'] = $groups;

            $this->_render_page('auth/create_user', $this->data);
        }
    }

	// edit a user
	public function edit_user($id)
	{
		$this->repairer->checkPermissions();

		$this->data['title'] = $this->lang->line('edit_user_heading');

		$user = $this->ion_auth->user($id)->row();
		$groups=$this->ion_auth->groups()->result_array();

		// validate form input
		$this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'required');
		$this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'required');
		// $this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'required');
		$this->form_validation->set_rules('company', $this->lang->line('edit_user_validation_company_label'), 'required');
		if((int)$this->input->post('pin_code') != (int)$user->pin_code) {
	       $is_unique =  '|is_unique[users.pin_code]';
	    } else {
	       $is_unique =  '';
	    }
		$this->form_validation->set_rules('pin_code', 'Pin Code', 'required'.$is_unique);

		if (isset($_POST) && !empty($_POST))
		{
			
			// update the password if it was posted
			if ($this->input->post('password'))
			{
				$this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
				$this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
			}

			if ($this->form_validation->run() === TRUE)
			{
				$phone = $this->input->post('phone');
				$phone = preg_replace('/\D+/', '', $phone);
        		$stores_json = json_encode($this->input->post('stores'));
				$data = array(
					'first_name' => $this->input->post('first_name'),
					'last_name'  => $this->input->post('last_name'),
					'company'    => $this->input->post('company'),
					'phone'      => $phone,
					'pin_code'   => $this->input->post('pin_code'),
	            );
	            if ($this->Admin) {
                    $data['group_id'] = $this->input->post('group');
	            	$data['stores'] = $stores_json;
	            	$data['all_stores'] = $this->input->post('all_stores');
	            }
	            if (isset($_FILES['user_image'])) {
	                if ($_FILES['user_image']['size'] > 0) {
	                    $config['upload_path'] = $this->upload_path;
	                    $config['allowed_types'] = $this->image_types;
	                    $config['max_size'] = $this->allowed_file_size;
	                    $config['overwrite'] = FALSE;
	                    $config['max_filename'] = 25;
	                    $config['encrypt_name'] = TRUE;
	                    $this->upload->initialize($config);
	                    if (!$this->upload->do_upload('user_image')) {

	                        $error = $this->upload->display_errors();
	                        $this->session->set_flashdata('error', $error);
	                        redirect("panel/auth/edit_user/".$id);
	                    }else{
	                        $photo = $this->upload->file_name;
	                        $data['image'] = $photo;
	                        $config = NULL;

	                    }
	                }
	            }

				// update the password if it was posted
				if ($this->input->post('password'))
				{
					$data['password'] = $this->input->post('password');
				}

				// //Update the groups user belongs to
				// $groupData = $this->input->post('groups');

				// if (isset($groupData) && !empty($groupData)) {

				// 	$this->ion_auth->remove_from_group('', $id);

				// 	foreach ($groupData as $grp) {
				// 		$this->ion_auth->add_to_group($grp, $id);
				// 	}

				// }

				// check to see if we are updating the user
			   if($this->ion_auth->update($user->id, $data))
			    {
			    	// redirect them back to the admin page if admin, or to the base url if non admin
				    $this->session->set_flashdata('message', $this->ion_auth->messages() );
					redirect('panel/auth', 'refresh');
			    }
			    else
			    {
			    	// redirect them back to the admin page if admin, or to the base url if non admin
				    $this->session->set_flashdata('error', $this->ion_auth->errors() );
					redirect('panel/auth', 'refresh');
			    }

			}
		}

		// display the edit user form
		$this->data['csrf'] = $this->_get_csrf_nonce();

		// set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

		// pass the user to the view
		$user->stores = json_decode($user->stores);
		$this->data['edit_user'] = $user;
		$this->data['groups'] = $groups;
		$frm_priv = $this->settings_model->getMandatory('user');
    	$this->data['frm_priv'] = $frm_priv;
		
		$this->data['first_name'] = array(
			'name'  => 'first_name',
			'id'    => 'first_name',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('first_name', $user->first_name),
            'class' => 'form-control',
			'required' => 'required',
		);
		$this->data['last_name'] = array(
			'name'  => 'last_name',
			'id'    => 'last_name',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('last_name', $user->last_name),
            'class' => 'form-control',
			'required' => 'required',
		);
		$this->data['pin_code'] = array(
            'name'  => 'pin_code',
            'id'    => 'pin_code',
            'type'  => 'text',
            'value' => $this->form_validation->set_value('pin_code', $user->pin_code),
            'class' => 'form-control',
            'required' => 'required',
            'minlength' => 4,
            'maxlength' => 8,
        );
		$this->data['company'] = array(
			'name'  => 'company',
			'id'    => 'company',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('company', $user->company),
            'class' => 'form-control',

		);
		if ($frm_priv['company']) {
        	$this->data['company']['required'] = 'required';
		}
       
		$this->data['phone'] = set_value('phone', $user->phone);
		$this->data['password'] = array(
			'name' => 'password',
			'id'   => 'password',
			'type' => 'password',
            'class' => 'form-control',
		);
		$this->data['password_confirm'] = array(
			'name' => 'password_confirm',
			'id'   => 'password_confirm',
			'type' => 'password',
			'class' => 'form-control',
            'data-parsley-equalto'=>"#password",
		);

		$this->data['image'] = $user->image;
		$this->_render_page('auth/edit_user', $this->data);
	}

	function user_groups()
    {
		$this->repairer->checkPermissions();

		$this->data['group_name'] = array(
			'name'  => 'group_name',
			'id'    => 'group_name',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('group_name'),
			'class' => 'form-control'
		);
		$this->data['description'] = array(
			'name'  => 'description',
			'id'    => 'description',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('description'),
			'class' => 'form-control'
		);
		
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['groups'] = $this->settings_model->getGroups();
		$this->render('auth/user_groups');
    }

    public function delete_group($id = null)
    {
        if ($this->settings_model->checkGroupUsers($id)) {
            $this->session->set_flashdata('error', lang('group_x_b_deleted'));
            admin_redirect('system_settings/user_groups');
        }

        if ($this->settings_model->deleteGroup($id)) {
            $this->session->set_flashdata('message', lang('group_deleted'));
            admin_redirect('system_settings/user_groups');
        }
    }


	// create a new group
	public function create_group()
	{
		$this->repairer->checkPermissions();
		$this->form_validation->set_rules('group_name', $this->lang->line('create_group_validation_name_label'), 'required');
		if ($this->form_validation->run() == TRUE)
		{
			$new_group_id = $this->ion_auth->create_group($this->input->post('group_name'), $this->input->post('description'));
			if($new_group_id)
			{
	            $this->db->insert('permissions', array('group_id' => $new_group_id));
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect("panel/auth/permissions/".$new_group_id, 'refresh');
			}
		}else{
			$this->session->set_flashdata('warning', validation_errors());
			redirect('panel/auth/user_groups');
		}
	}

	// edit a group
	public function edit_group($id)
	{
		$this->repairer->checkPermissions();

		// bail if no group id given
		if(!$id || empty($id))
		{
			redirect('panel/auth', 'refresh');
		}

		$this->data['title'] = $this->lang->line('edit_group_title');

		
		$group = $this->ion_auth->group($id)->row();

		// validate form input
		$this->form_validation->set_rules('group_name', $this->lang->line('edit_group_validation_name_label'), 'required');

		if (isset($_POST) && !empty($_POST))
		{
			if ($this->form_validation->run() === TRUE)
			{
				$group_update = $this->ion_auth->update_group($id, $_POST['group_name'], $_POST['group_description']);

				if($group_update)
				{
					$this->session->set_flashdata('message', $this->lang->line('edit_group_saved'));
				}
				else
				{
					$this->session->set_flashdata('message', $this->ion_auth->errors());
				}
				redirect("panel/auth/user_groups", 'refresh');
			}
		}

		if (validation_errors()) {
			$this->session->set_flashdata('warning', validation_errors());
			redirect('panel/auth/user_groups');
		}
		// set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

		// pass the user to the view
		$this->data['group'] = $group;

		$readonly = $this->config->item('admin_group', 'ion_auth') === $group->name ? 'readonly' : '';

		$this->data['group_name'] = array(
			'name'    => 'group_name',
			'id'      => 'group_name',
			'type'    => 'text',
			'value'   => $this->form_validation->set_value('group_name', $group->name),
			'class'   => 'form-control',
			$readonly => $readonly,
		);
		$this->data['group_description'] = array(
			'name'  => 'group_description',
			'id'    => 'group_description',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('group_description', $group->description),
			'class' => 'form-control',
		);

		$this->load->view($this->theme.'auth/edit_group', $this->data);
	}


	public function _get_csrf_nonce()
	{
		$this->load->helper('string');
		$key   = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return array($key => $value);
	}

	public function _valid_csrf_nonce()
	{
		if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
			$this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue'))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function _render_page($view, $data=null, $returnhtml=false)//I think this makes more sense
	{

		$this->viewdata = (empty($data)) ? $this->data: $data;

		$view_html = $this->render($view);

		if ($returnhtml) return $view_html;//This will return html on 3rd argument being true
	}

	function permissions($id = NULL)
    {

        $this->form_validation->set_rules('group', 'Group', 'is_natural_no_zero');
        if ($this->form_validation->run() == true) {
            $data = array(
				'repair-index' => $this->input->post('repair-index'),
				'repair-add' => $this->input->post('repair-add'),
				'repair-edit' => $this->input->post('repair-edit'),
				'customers-index' => $this->input->post('customers-index'),
				'customers-add' => $this->input->post('customers-add'),
				'customers-edit' => $this->input->post('customers-edit'),
				'pos-index' => $this->input->post('pos-index'),
				'pos-add_discounts' => $this->input->post('pos-add_discounts'),
				'pos-checkout_negative' => $this->input->post('pos-checkout_negative'),
				'pos-purchase_phones' => $this->input->post('pos-purchase_phones'),
				'inventory-index' => $this->input->post('inventory-index'),
				'inventory-add' => $this->input->post('inventory-add'),
				'inventory-edit' => $this->input->post('inventory-edit'),
				'inventory-manage_stock' => $this->input->post('inventory-manage_stock'),
				'phones-add_new' => $this->input->post('phones-add_new'),
				'phones-edit_new' => $this->input->post('phones-edit_new'),
				'phones-add_used' => $this->input->post('phones-add_used'),
				'phones-edit_used' => $this->input->post('phones-edit_used'),
				'accessory-add' => $this->input->post('accessory-add'),
				'accessory-edit' => $this->input->post('accessory-edit'),
				'accessory-manage_stock' => $this->input->post('accessory-manage_stock'),
				'plans-add' => $this->input->post('plans-add'),
				'plans-edit' => $this->input->post('plans-edit'),
				'other-add' => $this->input->post('other-add'),
				'other-edit' => $this->input->post('other-edit'),
				'other-manage_stock' => $this->input->post('other-manage_stock'),
				'purchases-index' => $this->input->post('purchases-index'),
				'purchases-add' => $this->input->post('purchases-add'),
				'purchases-edit' => $this->input->post('purchases-edit'),
				'purchases-delete' => $this->input->post('purchases-delete'),
				'purchases-return_purchase' => $this->input->post('purchases-return_purchase'),
				'purchases-customer' => $this->input->post('purchases-customer'),
				'purchases-customer_add' => $this->input->post('purchases-customer_add'),
				'purchases-customer_edit' => $this->input->post('purchases-customer_edit'),
				'purchases-customer_delete' => $this->input->post('purchases-customer_delete'),
				'reports-stock' => $this->input->post('reports-stock'),
				'reports-finance' => $this->input->post('reports-finance'),
				'reports-sales' => $this->input->post('reports-sales'),
				'reports-profit' => $this->input->post('reports-profit'),
				'reports-tax' => $this->input->post('reports-tax'),
				'reports-vendor_purchases' => $this->input->post('reports-vendor_purchases'),
				'reports-customer_purchases' => $this->input->post('reports-customer_purchases'),
				'reports-drawer' => $this->input->post('reports-drawer'),
				'reports-gl' => $this->input->post('reports-gl'),
				'sales-refund' => $this->input->post('sales-refund'),
				'sales-return_sales' => $this->input->post('sales-return_sales'),
				'settings-general_settings' => $this->input->post('settings-general_settings'),
				'settings-order_repairs' => $this->input->post('settings-order_repairs'),
				'settings-quote' => $this->input->post('settings-quote'),
				'settings-sms' => $this->input->post('settings-sms'),
				'settings-pos_configuration' => $this->input->post('settings-pos_configuration'),
				'settings-general_settings_edit' => $this->input->post('settings-general_settings_edit'),
				'settings-order_repairs_edit' => $this->input->post('settings-order_repairs_edit'),
				'settings-quote_edit' => $this->input->post('settings-quote_edit'),
				'settings-sms_edit' => $this->input->post('settings-sms_edit'),
				'settings-pos_configuration_edit' => $this->input->post('settings-pos_configuration_edit'),
				'settings-tax_rates' => $this->input->post('settings-tax_rates'),
				'tax_rates-edit' => $this->input->post('tax_rates-edit'),
				'tax_rates-add' => $this->input->post('tax_rates-add'),
				'settings-carriers' => $this->input->post('settings-carriers'),
				'carriers-add' => $this->input->post('carriers-add'),
				'carriers-edit' => $this->input->post('carriers-edit'),
				'settings-manufacturers' => $this->input->post('settings-manufacturers'),
				'settings-import' => $this->input->post('settings-import'),
				'manufacturers-add' => $this->input->post('manufacturers-add'),
				'manufacturers-edit' => $this->input->post('manufacturers-edit'),
				'settings-suppliers' => $this->input->post('settings-suppliers'),
				'suppliers-add' => $this->input->post('suppliers-add'),
				'suppliers-edit' => $this->input->post('suppliers-edit'),
				'suppliers-delete' => $this->input->post('suppliers-delete'),
				'auth-index' => $this->input->post('auth-index'),
				'auth-deactivate' => $this->input->post('auth-deactivate'),
				'auth-create_user' => $this->input->post('auth-create_user'),
				'auth-edit_user' => $this->input->post('auth-edit_user'),
				'auth-delete_user' => $this->input->post('auth-delete_user'),
				'auth-user_groups' => $this->input->post('auth-user_groups'),
				'auth-create_group' => $this->input->post('auth-create_group'),
				'auth-edit_group' => $this->input->post('auth-edit_group'),
				'auth-delete_group' => $this->input->post('auth-delete_group'),
				'auth-permissions' => $this->input->post('auth-permissions'),
			
				'store-index' 		=> $this->input->post('store-index'),
				'store-add' 		=> $this->input->post('store-add'),
				'store-edit'		=> $this->input->post('store-edit'),
				'store-delete'		=> $this->input->post('store-delete'),
				'store-disable'		=> $this->input->post('store-disable'),
				'welcome-pendingrepairs' => $this->input->post('welcome-pendingrepairs'),
				'welcome-completedseven' => $this->input->post('welcome-completedseven'),
				'welcome-completedthirty' => $this->input->post('welcome-completedthirty'),
				'welcome-revenuechart' => $this->input->post('welcome-revenuechart'),
				'welcome-stockchart' => $this->input->post('welcome-stockchart'),
				'welcome-quickmail' => $this->input->post('welcome-quickmail'),

				'customers-internal_notes' => $this->input->post('customers-internal_notes'),
				'customers-activities' => $this->input->post('customers-activities'),
				'customers-documents' => $this->input->post('customers-documents'),
				'customers-purchase_history' => $this->input->post('customers-purchase_history'),


				'timeclock-addentry' => $this->input->post('timeclock-addentry'),
				'timeclock-delete' => $this->input->post('timeclock-delete'),
				'timeclock-edit' => $this->input->post('timeclock-edit'),
				'timeclock-view_all' => $this->input->post('timeclock-view_all'),
				'timeclock-groups' => $this->input->post('timeclock-groups') ? implode(',', $this->input->post('timeclock-groups')) : NULL,
				'timeclock-stores' =>  $this->input->post('timeclock-stores') ? implode(',', $this->input->post('timeclock-stores')) : NULL,

				'commission-view_all' => $this->input->post('commission-view_all'),
				'commission-groups' => $this->input->post('commission-groups') ? implode(',', $this->input->post('commission-groups')) : NULL,
				'commission-stores' =>  $this->input->post('commission-stores') ? implode(',', $this->input->post('commission-stores')) : NULL,
				
				'timeclock-ownstore' =>  $this->input->post('timeclock-ownstore'),
				'commission-ownstore' =>  $this->input->post('commission-ownstore'),

				'commission-index' => $this->input->post('commission-index'),
				'commission-add' => $this->input->post('commission-add'),
				'commission-edit' => $this->input->post('commission-edit'),
				'commission-disable' => $this->input->post('commission-disable'),
				'commission-assign' => $this->input->post('commission-assign'),
				'commission-category' => $this->input->post('commission-category'),
				'commission-edit_category' => $this->input->post('commission-edit_category'),
				'commission-delete_category' => $this->input->post('commission-delete_category'),
				'commission-product' => $this->input->post('commission-product'),
				'commission-edit_product' => $this->input->post('commission-edit_product'),
				'commission-delete_product' => $this->input->post('commission-delete_product'),

				'settings-activities' => $this->input->post('settings-activities'),
				'activities-add' => $this->input->post('activities-add'),
				'activities-edit' => $this->input->post('activities-edit'),
				'activities-disable' => $this->input->post('activities-disable'),
				'reports-activities' => $this->input->post('reports-activities'),
				'settings-categories' => $this->input->post('settings-categories'),				
				'categories-add' => $this->input->post('categories-add'),
				'categories-edit' => $this->input->post('categories-edit'),
				'categories-disable' => $this->input->post('categories-disable'),
				'reports-activities' => $this->input->post('reports-activities'),

				'recent_sales-viewall' => $this->input->post('recent_sales-viewall'),
				'recent_sales-ownstore' => $this->input->post('recent_sales-ownstore'),
				'recent_sales-stores' =>  $this->input->post('recent_sales-stores') ? implode(',', $this->input->post('recent_sales-stores')) : NULL,

				'welcome-lookup_repair' => $this->input->post('welcome-lookup_repair'),
				'welcome-lookup_sale' => $this->input->post('welcome-lookup_sale'),
				'welcome-lookup_customer' => $this->input->post('welcome-lookup_customer'),
				'welcome-calculator' => $this->input->post('welcome-calculator'),
				'welcome-commission_today' => $this->input->post('welcome-commission_today'),
				'welcome-commission_week' => $this->input->post('welcome-commission_week'),
				'welcome-commission_month' => $this->input->post('welcome-commission_month'),
                'payroll-index' => $this->input->post('payroll-index'),
                'payroll-add' => $this->input->post('payroll-add'),
                'payroll-edit' => $this->input->post('payroll-edit'),
                'payroll-view' => $this->input->post('payroll-view'),
                'payroll-delete' => $this->input->post('payroll-delete'),
                'payroll-payslip' => $this->input->post('payroll-payslip'),
                'payroll-templates' => $this->input->post('payroll-templates'),
                'payroll-template' => $this->input->post('payroll-template'),
                'payroll-add_template' => $this->input->post('payroll-add_template'),
                'payroll-edit_template' => $this->input->post('payroll-template'),
                'payroll-delete_template' => $this->input->post('payroll-delete_template'),
                'payroll-setDefaultTemplate' => $this->input->post('payroll-setDefaultTemplate'),
                // 
                'accounts-index' => $this->input->post('accounts-index'),
                'accounts-add_bank' => $this->input->post('accounts-add_bank'),
                'accounts-edit_bank' => $this->input->post('accounts-edit_bank'),
                'accounts-delete_bank' => $this->input->post('accounts-delete_bank'),
                'expense-add' => $this->input->post('expense-add'),
                'expense-edit' => $this->input->post('expense-edit'),
                'expense-delete' => $this->input->post('expense-delete'),
                'expense-index' => $this->input->post('expense-index'),
                'deposits-add' => $this->input->post('deposits-add'),
                'deposits-index' => $this->input->post('deposits-index'),
                'deposits-edit' => $this->input->post('deposits-edit'),
                'deposits-delete' => $this->input->post('deposits-delete'),
                'expense_type-index' => $this->input->post('expense_type-index'),
                'expense_type-add' => $this->input->post('expense_type-add'),
                'expense_type-edit' => $this->input->post('expense_type-edit'),
                'expense_type-delete' => $this->input->post('expense_type-delete'),
                'deposit_type-add' => $this->input->post('deposit_type-add'),
                'deposit_type-edit' => $this->input->post('deposit_type-edit'),
                'deposit_type-delete' => $this->input->post('deposit_type-delete'),
                'deposit_type-index' => $this->input->post('deposit_type-index'),


                'repair-delete' => $this->input->post('repair-delete'),
                'customers-delete' => $this->input->post('customers-delete'),
                'inventory-delete' => $this->input->post('inventory-delete'),
                'phones-delete_new' => $this->input->post('phones-delete_new'),
                'phones-delete_used' => $this->input->post('phones-delete_used'),
                'accessory-delete' => $this->input->post('accessory-delete'),
                'plans-delete' => $this->input->post('plans-delete'),
                'other-delete' => $this->input->post('other-delete'),
            );

        }

        if ($this->form_validation->run() == true && $this->settings_model->updatePermissions($id, $data)) {
            $this->session->set_flashdata('message', lang('Group permissions successfully updated'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->data['id'] = $id;
            $this->data['p'] = $this->settings_model->getGroupPermissionsByGroupID($id);
            $this->data['group'] = $this->settings_model->getGroupByID($id);
            $this->render('auth/permissions');
        }

    }


    public function user_actions()
    {
        if (!$this->Admin) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        if ($id != $this->session->userdata('user_id')) {
                            $this->auth_model->delete_user($id);
                        }
                    }
                    $this->session->set_flashdata('message', lang('users_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {


                	$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
	                $sheet = $spreadsheet->getActiveSheet();

	              


	                $sheet->setTitle(lang('users'));
	                $sheet->SetCellValue('A1', lang('first_name'));
	                $sheet->SetCellValue('B1', lang('last_name'));
	                $sheet->SetCellValue('C1', lang('email'));
	                $sheet->SetCellValue('D1', lang('company'));
	                $sheet->SetCellValue('E1', lang('group'));
	                $sheet->SetCellValue('F1', lang('status'));


                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $user = $this->settings_model->getUser($id);
                        $sheet->SetCellValue('A' . $row, $user->first_name);
                        $sheet->SetCellValue('B' . $row, $user->last_name);
                        $sheet->SetCellValue('C' . $row, $user->email);
                        $sheet->SetCellValue('D' . $row, $user->company);
                        $sheet->SetCellValue('E' . $row, $user->group_id);
                        $sheet->SetCellValue('F' . $row, $user->active);
                        $row++;
                    }

                    $sheet->getColumnDimension('A')->setWidth(10);
	                $sheet->getColumnDimension('B')->setWidth(30);
	                $sheet->getColumnDimension('C')->setWidth(25);
	                $sheet->getColumnDimension('D')->setWidth(45);
	                $sheet->getColumnDimension('E')->setWidth(15);
	                $sheet->getColumnDimension('F')->setWidth(15);

                	$sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    $filename = 'users_' . date('Y_m_d_H_i_s');


                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
                    header('Cache-Control: max-age=0');
                    $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
                    $writer->save('php://output');
                    exit();
                }
            } else {
                $this->session->set_flashdata('error', lang('no_user_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }


}
