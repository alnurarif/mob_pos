<?php

/**
 * Base controllers for different purposes
 * 	- MY_Controller: for Frontend Website
 * 	- Admin_Controller: for Admin Panel (require login), extends from MY_Controller
 * 	- API_Controller: for API Site, extends from REST_Controller
 */
class MY_Controller extends MX_Controller {
	// Values to be obtained automatically from router
	protected $mModule = '';			// module name (empty = Frontend Website)
	public $mCtrler = 'home';		// current controller
	public $mAction = 'index';		// controller function being called
	protected $mMethod = 'GET';			// HTTP request method

	// Config values from config/ci_bootstrap.php
	protected $mConfig = array();
	protected $mBaseUrl = array();
	protected $mSiteName = '';
	protected $mMetaData = array();
	protected $mScripts = array();
	public 	  $mSettings = NULL;
	protected $mStylesheets = array();

	// Values and objects to be overrided or accessible from child controllers
	protected $mPageTitlePrefix = '';
	protected $mPageTitle = '';
	protected $showPageTitle = false;
	protected $mBodyClass = '';
	protected $mMenu = array();
	protected $mBreadcrumb = array();

	// Multilingual
	protected $mMultilingual = FALSE;
	protected $mLanguage = 'en';
	protected $mAvailableLanguages = array();

	// Data to pass into views
	protected $data = array();

	// Login user
	protected $mPageAuth = array();
	protected $mUser = NULL;
	protected $mUserGroups = array();
	protected $mUserMainGroup;

	public 	  $cancelled = TRUE;
	public 	  $mData = NULL;
	public 	  $mStores = array();
	public 	  $activeStore = NULL;
	public 	  $activeStoreData = NULL;

	// Constructor
	public function __construct()
	{
		parent::__construct();


		
		if (file_exists(FCPATH.'RMS_PRO')) {
			redirect('http://' . $_SERVER['SERVER_NAME'] . '/install/index.php');
		}
		
		$this->load->model('settings_model');
		$this->load->model('pos_model');
		$this->mSettings = $this->settings_model->getSettings();


		// router info
		$this->mCtrler = $this->router->fetch_class();
		$this->mAction = $this->router->fetch_method();
		$this->mMethod = $this->input->server('REQUEST_METHOD');
		$this->load->model('main_model');

		if ( $this->ion_auth->logged_in() ) {
			
			/*
			 * --------------------------------------------------------------------
			 * SET YOUR TIMEZONE
			 * --------------------------------------------------------------------
			 *
			 * Find your timezone here
			 * http://php.net/manual/en/timezones.php
			 */
            if ($this->activeStoreData && $this->activeStoreData->timezone) {
				if(function_exists('date_default_timezone_set')) date_default_timezone_set($this->activeStoreData->timezone);
                $now = new DateTime();
				$mins = $now->getOffset() / 60;
				$sgn = ($mins < 0 ? -1 : 1);
				$mins = abs($mins);
				$hrs = floor($mins / 60);
				$mins -= $hrs * 60;
				$offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);
                $this->db->query("SET time_zone='".$offset."'");
                unset($now, $mins, $sgn, $mins, $hrs, $mins, $offset);
            }
        }
		$this->lang->load('main_lang', $this->mSettings->language);
		$this->lang->load('pos_lang', $this->mSettings->language);
		$this->lang->load('plugins_lang', $this->mSettings->language);
		$this->lang->load('calendar_lang', $this->mSettings->language);
		
		$this->load->library('repairer');


		$this->main_model->gen_token();
		// initial setup
		$this->_setup();
		
		if ( $this->ion_auth->logged_in() ) {
			if (!($this->session->userdata('active_store'))) {
				if (!($this->mCtrler === 'settings' && $this->mAction === 'activate')) {
					$this->session->set_flashdata('error', 'Please activate a store');
					redirect('panel/settings/activate');
				}
			}
			$this->activeStore = (int)$this->session->userdata('active_store');
			$this->activeStoreData = $this->settings_model->getStoreByID($this->activeStore);
			
		}

	}

	// Setup values from file: config/ci_bootstrap.php
	private function _setup()
	{
		$config = $this->config->item('my_config');
        	
		// load default values
		$this->mBaseUrl = empty($this->mModule) ? base_url() : base_url($this->mModule).'/';
		$this->mSiteName = empty($config['site_name']) ? '' : $config['site_name'];
		$this->mPageTitlePrefix = empty($config['page_title_prefix']) ? '' : $config['page_title_prefix'];
		$this->mPageTitle = empty($config['page_title']) ? '' : $config['page_title'];
		$this->mBodyClass = empty($config['body_class']) ? '' : $config['body_class'];
		$this->mMenu = empty($config['menu']) ? array() : $config['menu'];
		$this->mMetaData = empty($config['meta_data']) ? array() : $config['meta_data'];
		$this->mScripts = empty($config['scripts']) ? array() : $config['scripts'];
		$this->mStylesheets = empty($config['stylesheets']) ? array() : $config['stylesheets'];
		$this->mPageAuth = empty($config['page_auth']) ? array() : $config['page_auth'];
        $this->loggedIn         = $this->repairer->logged_in();
		


		$this->mTRates = (array)$this->settings_model->getTaxRates();

		$this->theme = $this->mSettings->theme.'/views/';
        if(is_dir(VIEWPATH.$this->mSettings->theme.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR)) {
            $this->assets = base_url() . 'themes/' . $this->mSettings->theme . '/assets/';
        } else {
            $this->assets = base_url() . 'themes/adminlte/assets/';
        }

        $this->data['assets'] = $this->assets;
        $this->data['theme'] = $this->theme;
        $this->data['info']                = $this->settings_model->getNotifications();

		$this->mStores = $this->settings_model->getAllStores();
		if (!empty($config['menu']) && sizeof($this->mStores) > 1 && isset($this->mMenu['inventory,phones,accessory,other,plans'])) {
			$this->mMenu['inventory,phones,accessory,other,plans']['children']['Tranfer Stock'] = [
				'name'=>'transfers/index',
				'url' => 'transfers/index'
			];

		}
		// restrict pages
		$uri = ($this->mAction=='index') ? $this->mCtrler : $this->mCtrler.'/'.$this->mAction;

		// push first entry to breadcrumb
		if ($this->mCtrler!='home')
		{
			$page = $this->mMultilingual ? lang('home') : 'Home';
			$this->push_breadcrumb($page, '');
		}
        $this->data['dateFormats'] = null;

        if($sd = $this->settings_model->getDateFormat($this->mSettings->dateformat)) {
            $dateFormats = array(
                'js_sdate' => $sd->js,
                'php_sdate' => $sd->php,
                'mysq_sdate' => $sd->sql,
                'js_ldate' => $sd->js . ' hh:ii',
                'php_ldate' => $sd->php . ' H:i',
                'mysql_ldate' => $sd->sql . ' %H:%i'
                );
        } else {
            $dateFormats = array(
                'js_sdate' => 'mm-dd-yyyy',
                'php_sdate' => 'm-d-Y',
                'mysq_sdate' => '%m-%d-%Y',
                'js_ldate' => 'mm-dd-yyyy hh:ii:ss',
                'php_ldate' => 'm-d-Y H:i:s',
                'mysql_ldate' => '%m-%d-%Y %T'
                );
        }

		$this->dateFormats = $dateFormats;
        $this->data['dateFormats'] = $dateFormats;
  		
  		$this->data['dp_lang']      = json_encode(['days' => [lang('cal_sunday'), lang('cal_monday'), lang('cal_tuesday'), lang('cal_wednesday'), lang('cal_thursday'), lang('cal_friday'), lang('cal_saturday'), lang('cal_sunday')], 'daysShort' => [lang('cal_sun'), lang('cal_mon'), lang('cal_tue'), lang('cal_wed'), lang('cal_thu'), lang('cal_fri'), lang('cal_sat'), lang('cal_sun')], 'daysMin' => [lang('cal_su'), lang('cal_mo'), lang('cal_tu'), lang('cal_we'), lang('cal_th'), lang('cal_fr'), lang('cal_sa'), lang('cal_su')], 'months' => [lang('cal_january'), lang('cal_february'), lang('cal_march'), lang('cal_april'), lang('cal_may'), lang('cal_june'), lang('cal_july'), lang('cal_august'), lang('cal_september'), lang('cal_october'), lang('cal_november'), lang('cal_december')], 'monthsShort' => [lang('cal_jan'), lang('cal_feb'), lang('cal_mar'), lang('cal_apr'), lang('cal_may'), lang('cal_jun'), lang('cal_jul'), lang('cal_aug'), lang('cal_sep'), lang('cal_oct'), lang('cal_nov'), lang('cal_dec')], 'today' => lang('today'), 'suffix' => [], 'meridiem' => []]);

		if ( $this->ion_auth->logged_in() ) {
			$this->mUser = $this->ion_auth->user()->row();
			$this->mUser->ClockedIn = $this->settings_model->isClockedIn($this->mUser->id, $this->session->userdata('active_store'));
			$this->mUser->stores = json_decode($this->mUser->stores);
			$this->Admin = $this->repairer->in_group('admin') ? TRUE : FALSE;
            $this->data['Admin'] = $this->Admin;
			
			$this->data['frm_priv_inventory'] = $this->settings_model->getMandatory('repair_items');
            $this->data['categories'] = $this->settings_model->getAllCategories();
            $this->data['subcategories'] = $this->settings_model->getAllCategories(FALSE);
            // $this->data['product'] = $id ? $this->inventory_model->getProductByID($id) : NULL;
			// $this->data['variants'] = $id ? $this->inventory_model->getProductVariantsByID($id) : NULL;
			
            $this->data['plans'] = $this->pos_model->getAllPlans();

            
            if(!$this->Admin) {
                $gp	= $this->settings_model->checkPermissions();
	            $this->GP = $gp[0];
	            $this->data['GP'] = $gp[0];
	        } else {
	            $this->data['GP'] = NULL;
	        }
            $this->load->language('calendar_lang',$this->mSettings->language);

		}

		if ($this->ion_auth->logged_in() && $this->mSettings->require_clockin && !$this->mUser->ClockedIn) {
			$this->session->set_flashdata('error', 'Please Clock In!');
			if (
				!(
					($this->mCtrler == 'reports') || 
					($this->mCtrler == 'welcome') || 
					($this->mCtrler == 'timeclock') || 
					($this->mCtrler == 'sales' && $this->mAction == 'return_sales') ||
					($this->mCtrler == 'sales' && $this->mAction == 'modal_view') ||
					($this->mCtrler == 'sales' && $this->mAction == 'getReturns') ||
					($this->mCtrler == 'purchases' && $this->mAction == 'modal_view') ||
					($this->mCtrler == 'pos' && $this->mAction == 'view') ||  
					($this->mCtrler == 'auth' && $this->mAction == 'logout') ||
					($this->mCtrler == 'settings' && $this->mAction == 'activate') ||
					($this->mCtrler == 'login')
				)
			) {
				redirect('panel', 'refresh');
			}
		}

		if ($this->ion_auth->logged_in() && $this->mUser->ClockedIn) {
			$clock_in_date = $this->mUser->ClockedIn->clock_in;
			$clock_out_date = array_map('intval', explode(':', $this->mSettings->auto_clockout));
			$clock_out_date = "+".$clock_out_date[0]." hour +".$clock_out_date[1]." minutes +".$clock_out_date[2]." seconds";
			$clock_out_date = date('Y-m-d H:i:s',strtotime($clock_out_date, strtotime($clock_in_date)));
			if (time() > strtotime($clock_out_date)) {
				$this->db->where('user_id', $this->mUser->id)
				->update('timeclock', array('clock_out'=>$clock_out_date));
				redirect('panel');
			}
		}

		$this->mConfig = $config;
	}


	// Verify user login (regardless of user group)
	protected function verify_login($redirect_url = NULL)
	{
		if ( !$this->ion_auth->logged_in() )
		{
			if ( $redirect_url==NULL )
				$redirect_url = $this->mConfig['login_url'];

			redirect($redirect_url);
		}
	}

	// Verify user authentication
	// $group parameter can be name, ID, name array, ID array, or mixed array
	// Reference: http://benedmunds.com/ion_auth/#in_group
	protected function verify_auth($group = 'members', $redirect_url = NULL)
	{
		if ( !$this->ion_auth->logged_in() || !$this->ion_auth->in_group($group) )
		{
			if ( $redirect_url==NULL )
				$redirect_url = $this->mConfig['login_url'];
			
			redirect($redirect_url);
		}
	}

	// Add script files, either append or prepend to $this->mScripts array
	// ($files can be string or string array)
	protected function add_script($files, $append = TRUE, $position = 'foot')
	{
		$files = is_string($files) ? array($files) : $files;
		$position = ($position==='head' || $position==='foot') ? $position : 'foot';

		if ($append)
			$this->mScripts[$position] = array_merge($this->mScripts[$position], $files);
		else
			$this->mScripts[$position] = array_merge($files, $this->mScripts[$position]);
	}

	// Add stylesheet files, either append or prepend to $this->mStylesheets array
	// ($files can be string or string array)
	protected function add_stylesheet($files, $append = TRUE, $media = 'screen')
	{
		$files = is_string($files) ? array($files) : $files;

		if ($append)
			$this->mStylesheets[$media] = array_merge($this->mStylesheets[$media], $files);
		else
			$this->mStylesheets[$media] = array_merge($files, $this->mStylesheets[$media]);
	}

	// Render template
	protected function render($view_file, $layout = 'default')
	{
		// automatically generate page title
		if ( empty($this->mPageTitle) )
		{
			if ($this->mAction=='index')
				$this->mPageTitle = humanize($this->mCtrler);
			else
				$this->mPageTitle = humanize($this->mAction);
		}
		$this->data['all_funds'] = [
			'cash' => lang('Cash'),
			'CC' => lang('Credit Card'),
			'Cheque' => lang('Cheque'),
			'ppp' => lang('PayPal'),
			'other' => lang('Other'),
			'authorize' => lang('Authorize.Net'),
		];
		$this->data['module'] = $this->mModule;
		$this->data['ctrler'] = $this->mCtrler;
		$this->data['action'] = $this->mAction;
		$this->data['settings'] = $this->mSettings;
		$this->data['taxRates'] = $this->mTRates;

		$this->data['site_name'] = $this->mSiteName;
		$this->data['page_title'] = $this->mPageTitlePrefix.$this->mPageTitle;
		$this->data['show_page_title'] = $this->showPageTitle;
		$this->data['current_uri'] = empty($this->mModule) ? uri_string(): str_replace($this->mModule.'/', '', uri_string());
		$this->data['meta_data'] = $this->mMetaData;
		$this->data['scripts'] = $this->mScripts;
		$this->data['stylesheets'] = $this->mStylesheets;
		$this->data['page_auth'] = $this->mPageAuth;

		$this->data['base_url'] = $this->mBaseUrl;
		$this->data['menu'] = $this->mMenu;
		$this->data['user'] = $this->mUser;
		$this->data['ga_id'] = empty($this->mConfig['ga_id']) ? '' : $this->mConfig['ga_id'];
		$this->data['frm_priv_client'] = $this->settings_model->getMandatory('client');
        $this->data['frm_priv_repairs'] = $this->settings_model->getMandatory('repair');


		$this->data['defects'] = $this->settings_model->getAllDefects();
		$this->data['manufacturers'] = $this->settings_model->getManufacturers();

		$this->data['manufacturers'] = $this->settings_model->getOnlyManufacturers();


        $this->data['warranty_plans'] = $this->settings_model->getAllWarranties();
		$this->data['body_class'] = $this->mBodyClass;
        
        $this->data['suppliers'] = $this->settings_model->getAllSuppliers();
        $this->data['statuses'] = $this->settings_model->getRepairStatuses();
		$this->data['clients'] = $this->settings_model->getAllClients();
		
        $this->data['all_users'] = $this->settings_model->getAllUsers();
        $this->data['technicians'] = $this->settings_model->getAllTechnicians();

		// Multi Store
		$this->data['stores'] = $this->mStores;
		$this->data['active_store'] = $this->activeStore;

		$this->data['due_activities'] = $this->settings_model->getDueActivities($this->activeStore);
        $this->data['qty_alert_num'] = $this->settings_model->get_total_qty_alerts();

		// automatically push current page to last record of breadcrumb
		$this->push_breadcrumb($this->mPageTitle);
		$this->data['breadcrumb'] = $this->mBreadcrumb;

		$this->data['inner_view'] = $view_file;
		$this->load->view($this->theme . '_base/head', $this->data);
		$this->load->view($this->theme . '_layouts/'.$layout, $this->data);
		$this->load->view($this->theme . '_base/foot', $this->data);
	}

	// Output JSON string
	protected function render_json($data, $code = 200)
	{
		$this->output
			->set_status_header($code)
			->set_content_type('application/json')
			->set_output(json_encode($data));
			
		// force output immediately and interrupt other scripts
		global $OUT;
		$OUT->_display();
		exit;
	}

	// Add breadcrumb entry
	// (Link will be disabled when it is the last entry, or URL set as '#')
	protected function push_breadcrumb($name, $url = '#', $append = TRUE)
	{
		$entry = array('name' => $name, 'url' => $url);

		if ($append)
			$this->mBreadcrumb[] = $entry;
		else
			array_unshift($this->mBreadcrumb, $entry);
	}
	
}

// include base controllers
require APPPATH."core/Auth_Controller.php";
