<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// NOTE: this controller inherits from MY_Controller instead of Admin_Controller,
// since no authentication is required
class Login extends MY_Controller {

	public function __construct()
	{
		parent::__construct();

		if ($this->ion_auth->logged_in()) {
			redirect('panel/welcome');
		}

	}
	/**
	 * Login page and submission
	 */
	// log the user in
	public function index()
	{

		$this->data['title'] = $this->lang->line('login_heading');
		$this->mBodyClass = 'hold-transition login-page';
		//validate form input
		$this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('email')), 'required');
		$this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('password')), 'required');

		if ($this->form_validation->run() == true)
		{
			// check to see if the user is logging in
			// check for "remember me"
			$remember = (bool) $this->input->post('remember');

			if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
			{
				// Set User Store Session
				$user = ($this->ion_auth->user()->row());
				$stores = json_decode($user->stores);
				$all_stores = array();
				foreach ($this->mStores as $store) {
					$all_stores[] = $store['id'];
				}
				
				$accessable_stores = array_intersect((array)$stores, (array)$all_stores);
				if($user->all_stores && sizeof($all_stores) == 1){
					$this->session->set_userdata('active_store', end($all_stores));
				}elseif ($user->all_stores || sizeof($accessable_stores) > 1) {
					$this->session->set_flashdata('message', 'Please activate the store you want to use. ');
					redirect('panel/settings/activate');
				}elseif (sizeof($accessable_stores) < 1) {
					$this->ion_auth->logout();
					$this->session->set_flashdata('error', 'Sorry! You can\'t sign in. The Store assigned to you maybe is disabled. Please contact your store owner.');
					redirect('panel/login');
				}else{
					$this->session->set_userdata('active_store', $accessable_stores[0]);
				}
				// END
				//if the login is successful
				//redirect them back to the home page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect('panel/welcome', 'refresh');
			}
			else
			{
				// if the login was un-successful
				// redirect them back to the login page
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('panel/', 'refresh'); // use redirects instead of loading views for compatibility with MY_Controller libraries
			}
		}
		else
		{
			// the user is not logging in so display the login page
			// set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->data['identity'] = array('name' => 'identity',
				'id'    => 'identity',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('identity'),
				'placeholder' => lang('email')
			);
			$this->data['password'] = array('name' => 'password',
				'id'   => 'password',
				'placeholder' => lang('password'),
				'type' => 'password',
			);

			$this->render('auth/login', 'empty');
		}
	}

	// forgot password
	public function forgot_password()
	{
		// setting validation rules by checking whether identity is username or email
		if($this->config->item('identity', 'ion_auth') != 'email' )
		{
		   $this->form_validation->set_rules('identity', $this->lang->line('forgot_password_identity_label'), 'required');
		}
		else
		{
		   $this->form_validation->set_rules('identity', $this->lang->line('forgot_password_validation_email_label'), 'required|valid_email');
		}


		if ($this->form_validation->run() == false)
		{
			$this->data['type'] = $this->config->item('identity','ion_auth');
			// setup the input
			$this->data['identity'] = array('name' => 'identity',
				'id' => 'identity',
			);

			if ( $this->config->item('identity', 'ion_auth') != 'email' ){
				$this->data['identity_label'] = $this->lang->line('forgot_password_identity_label');
			}
			else
			{
				$this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
			}

			// set any errors and display the form
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			$this->render('auth/forgot_password', 'empty');
		}
		else
		{
			$identity_column = $this->config->item('identity','ion_auth');
			$identity = $this->ion_auth->where($identity_column, $this->input->post('identity'))->users()->row();

			if(empty($identity)) {

	            		if($this->config->item('identity', 'ion_auth') != 'email')
		            	{
		            		$this->ion_auth->set_error('forgot_password_identity_not_found');
		            	}
		            	else
		            	{
		            	   $this->ion_auth->set_error('forgot_password_email_not_found');
		            	}

		                $this->session->set_flashdata('message', $this->ion_auth->errors());
                		redirect("panel/login/forgot_password", 'refresh');
            		}

			// run the forgotten password method to email an activation code to the user
			$forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

			if ($forgotten)
			{
				// if there were no errors
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect("panel/login", 'refresh'); //we should display a confirmation page here instead of the login page
			}
			else
			{
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect("panel/login/forgot_password", 'refresh');
			}
		}
	}


	// reset password - final step for forgotten password
	public function reset_password($code = NULL)
	{
		if (!$code)
		{
			show_404();
		}

		$user = $this->ion_auth->forgotten_password_check($code);

		if ($user)
		{
			// if the code is valid then display the password reset form

			$this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
			$this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

			if ($this->form_validation->run() == false)
			{
				// display the form

				// set the flash data error message if there is one
				$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

				$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
				$this->data['new_password'] = array(
					'name' => 'new',
					'id'   => 'new',
					'type' => 'password',
					'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
					'class' => 'form-control',
				);
				$this->data['new_password_confirm'] = array(
					'name'    => 'new_confirm',
					'id'      => 'new_confirm',
					'type'    => 'password',
					'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
					'class' => 'form-control',
				);
				$this->data['user_id'] = array(
					'name'  => 'user_id',
					'id'    => 'user_id',
					'type'  => 'hidden',
					'value' => $user->id,
					'class' => 'form-control',
				);
				$this->data['csrf'] = null;
				$this->data['code'] = $code;

				// render
				$this->render('auth/reset_password', 'empty');
			}
			else
			{
				// do we have a valid request?
				if ( $user->id != $this->input->post('user_id'))
				{

					// something fishy might be up
					$this->ion_auth->clear_forgotten_password_code($code);

					show_error($this->lang->line('error_csrf'));

				}
				else
				{
					// finally change the password
					$identity = $user->{$this->config->item('identity', 'ion_auth')};

					$change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

					if ($change)
					{
						// if the password was successfully changed
						$this->session->set_flashdata('message', $this->ion_auth->messages());
						redirect("panel//", 'refresh');
					}
					else
					{
						$this->session->set_flashdata('message', $this->ion_auth->errors());
						redirect('panel/login/reset_password/' . $code, 'refresh');
					}
				}
			}
		}
		else
		{
			// if the code is invalid then send them back to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("panel/login/forgot_password", 'refresh');
		}
	}


	
    public function activate($id, $code)
    {
        if ($code) {
            if ($activation = $this->ion_auth->activate($id, $code)) {
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect('login');
            }
        } else {
            $this->session->set_flashdata('error', $this->ion_auth->errors());
            redirect('login');
        }
    }

}
?>
