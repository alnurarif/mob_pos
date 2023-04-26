<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| CI Bootstrap 3 Configuration
| -------------------------------------------------------------------------
| This file lets you define default values to be passed into views 
| when calling MY_Controller's render() function. 
| 
| See example and detailed explanation from:
| 	/application/config/ci_bootstrap_example.php
*/

$config['my_config'] = array(
	// Site name
	'site_name' => 'My Tech POS',
	// Default page title prefix
	'page_title_prefix' => '',
	// Default page title
	'page_title' => '',
	// Default meta data
	'meta_data'	=> array(
		'author'		=> '',
		'description'	=> '',
		'keywords'		=> ''
	),

	// Default CSS class for <body> tag
	'body_class' => 'hold-transition sidebar-mini',

	// Menu items
	'menu' => array(
		'home' => array(
			'name'		=> 'welcome/index',
			'url'		=> '',
			'icon'		=> 'fas fa-home',

		),
		'repair' => array(
			'name'		=> 'repair/index',
			'url'		=> 'repair/index/default',
			'icon'		=> 'glyphicon glyphicon-wrench',
		),

		'customers' => array(
			'name'		=> 'customers/index',
			'url'		=> 'customers/index/enabled',
			'icon'		=> 'fas fa-users',
		),

		'pos' => array(
			'name'		=> 'pos/index',
			'url'		=> 'pos',
			'icon'		=> 'fas fa-desktop',
		),

		'inventory,phones,accessory,other,plans' => array(
			'name'		=> 'Stock/Inventory',
			'url'		=> 'inventory',
			'icon'		=> 'fas fa-tasks',
			'children'  => array(
				'Repair Parts' => array(
					'name'		=> 'inventory/index',
					'url'		=> 'inventory/index/enabled',
				),

				'New Phones' => array(
					'name'		=> 'phones/new',
					'url'		=> 'phones/view/new/enabled',
				),

				'Used Phones' => array(
					'name'		=> 'phones/used',
					'url'		=> 'phones/view/used/enabled',
				),

				'Accessoriess' => array(
					'name'		=> 'accessory/index',
					'url'		=> 'accessory/index',
				),

				'Cellular Plans' => array(
					'name'		=> 'plans/index',
					'url'		=> 'plans/index/enabled',
				),

				'Other Products' => array(
					'name'		=> 'other/index',
					'url'		=> 'other/index/enabled',
				),

				'Print Barcodes' => array(
					'name'		=> 'inventory/print_barcodes',
					'url'		=> 'inventory/print_barcodes',
				),

				'Count Stock' => array(
					'name'		=> 'inventory/count_stock',
					'url'		=> 'inventory/count_stock',
				),

				'Transfers' => array(
					'name'		=> 'transfers/index',
					'url'		=> 'transfers/index',
				),

			),
		),
                


		'purchases' => array(
			'name'		=> 'purchases/main',
			'url'		=> 'purchases',
			'icon'		=> 'fa fa-store-alt',
			'children'  => array(

				'Vendor Purchases' => array(
					'name'		=> 'purchases/index',
					'url'		=> 'purchases/index',
				),
				'Customer Purchases' => array(
					'name'		=> 'purchases/customer',
					'url'		=> 'purchases/customer',
				),
			),
		),

		'accounts,deposits,expenses' => array(
			'name'		=> 'accounts/main',
			'url'		=> 'accounts',
			'icon'		=> 'fa fa-file-invoice-dollar',
			'children'  => array(
				'List Accounts' => array(
					'name'		=> 'accounts/index',
					'url'		=> 'accounts/index',
				),
				'Expenses' => array(
					'name'		=> 'expenses/index',
					'url'		=> 'expenses/index',
				),
				'Expense Types' => array(
					'name'		=> 'expenses/types',
					'url'		=> 'expenses/types',
				),
				'Deposits' => array(
					'name'		=> 'deposits/index',
					'url'		=> 'deposits/index',
				),
				'Deposit Types' => array(
					'name'		=> 'deposits/types',
					'url'		=> 'deposits/types',
				),
			),
		),

		'payroll,timeclock' => array(
			'name'		=> 'hrm/index',
			'url'		=> 'hrm',
			'icon'		=> 'fa fa-street-view',
			'children'  => array(
				'Time Clock' => array(
					'name'		=> 'timeclock/index',
					'url'		=> 'timeclock/index',
				),
				'Payroll' => array(
					'name'		=> 'payroll/index',
					'url'		=> 'payroll/index',
				),
			),
		),

		'reports,sales' => array(
			'name'		=> 'reports/index',
			'url'		=> 'reports',
			'icon'		=> 'fas fa-chart-pie',
			'children'  => array(
				'Stock Chart'		=> array(
					'name' => 'reports/stock',
					'url' => 'reports/stock',
				),
				'Finance Chart'		=> array(
					'name' => 'reports/finance',
					'url' => 'reports/finance',
				),
				'Sales Report'  	=> array(
					'name' => 'reports/sales',
					'url' => 'reports/sales',
				),
				'View Refunds' 		=> array(
					'name' => 'sales/return_sales',
					'url' => 'sales/return_sales',
				),
				'Profit Report' 		=> array(
					'name' => 'reports/profit',
					'url' => 'reports/profit',
				),
				'Tax Report' 		=> array(
					'name' => 'reports/tax',
					'url' => 'reports/tax',
				),
				'Vendor Purchases' 		=> array(
					'name' => 'reports/vendor_purchases',
					'url' => 'reports/vendor_purchases',
				),
				'Customer Purchases' 	=> array(
					'name' => 'reports/customer_purchases',
					'url' => 'reports/customer_purchases',
				),
				'Drawer Report' 	=> array(
					'name' => 'reports/drawer',
					'url' => 'reports/drawer',
				),
				'G/L Report' 	=> array(
					'name' => 'reports/gl',
					'url' => 'reports/gl',
				),
				'Activities Due' 	=> array(
					'name' => 'reports/activities',
					'url' => 'reports/activities',
				),
				'Commission Report'	=> array(
					'name' => 'commission/report_index',
					'url' => 'commission/report_index',
				),
				'Quantity Alerts'	=> array(
					'name' => 'reports/quantity_alerts',
					'url' => 'reports/quantity_alerts',
				),
				'Counted Stock'	=> array(
					'name' => 'inventory/counted_stock',
					'url' => 'inventory/counted_stock',
				),
			),
		),

		'settings' => array(
			'name'		=> 'settings/index',
			'url'		=> 'settings',
			'icon'		=> 'fas fa-cog',
			'children'  => array(
				'System Setting'	=> array(
					'name' => 'settings/index',
					'url' => 'settings/index',
				),
				'Tax Rates'			=> array(
					'name' => 'settings/tax_rates',
					'url' => 'settings/tax_rates',
				),
				'Manufacturers'		=> array(
					'name' => 'settings/manufacturers',
					'url' => 'settings/manufacturers',
				),

				'Models'		=> array(
					'name' => 'settings/models',
					'url' => 'settings/models',
				),

				'Defects'		=> array(
					'name' => 'defects/index',
					'url' => 'defects/index',
				),
				'Carriers'			=> array(
					'name' => 'settings/carriers',
					'url' => 'settings/carriers',
				),
				'Suppliers'			=> array(
					'name' => 'settings/suppliers',
					'url' => 'settings/suppliers',
				),
				'Importer'			=> array(
					'name' => 'settings/import',
					'url' => 'settings/import',
				),
				'Set Up Stores'		=> array(
					'name' => 'settings/store',
					'url' => 'settings/store',
				),
				'Activities'		=> array(
					'name' => 'settings/activities',
					'url' => 'settings/activities',
				),
				'Categories'		=> array(
					'name' => 'settings/categories',
					'url' => 'settings/categories',
				),
				'Mandatory Fields'	=> array(
					'name' => 'settings/mandatory_fields',
					'url' => 'settings/mandatory_fields',
				),
				'Warranties'		=> array(
					'name' => 'settings/warranties',
					'url' => 'settings/warranties',
				),
				'Activation Plans'	=> array(
					'name' => 'settings/activation_plans',
					'url' => 'settings/activation_plans',
				),
				'Discount Codes'	=> array(
					'name' => 'settings/discount_codes',
					'url' => 'settings/discount_codes',
				),
				'SMS Gateways'		=> array(
					'name' => 'settings/sms_gateways',
					'url' => 'settings/sms_gateways',
				),
				'Repair Status'		=> array(
					'name' => 'settings/repair_statuses',
					'url' => 'settings/repair_statuses',
				),
			),
		),

		'commission' => array(
			'name'		=> 'commission/main',
			'url'		=> 'commission',
			'icon'		=> 'fas fa-file',
			'children'  => array(
				'Commissions'	=> array(
					'name' => 'commission/index',
					'url' => 'commission/index',
				),
				'Category Commissions'	=> array(
					'name' => 'commission/category',
					'url' => 'commission/category',
				),
				'Product Commissions'	=> array(
					'name' => 'commission/product',
					'url' => 'commission/product',
				),
				'Assign Commissions'	=> array(
					'name' => 'commission/assign',
					'url' => 'commission/assign',
				),
			),
		),

		'auth' => array(
			'name'		=> 'users/index',
			'url'		=> 'auth',
			'icon'		=> 'fas fa-user',
			'children'  => array(
				'Users'	=> array(
					'name' => 'auth/index',
					'url' => 'auth/index',
				),
				'Create User'	=> array(
					'name' => 'auth/create_user',
					'url' => 'auth/create_user',
				),
				'Groups'	=> array(
					'name' => 'auth/user_groups',
					'url' => 'auth/user_groups',
				),
			),
		),


		'tools' => array(
			'name'		=> 'tools/index',
			'url'		=> 'tools',
			'icon'		=> 'fas fa-cog',
			'children'  => array(
				'Notifications'	=> array(
					'name' => 'notifications/index',
					'url' => 'notifications/index',
				),
				/*'Calendar'	=> array(
					'name' => 'calendar/index',
					'url' => 'calendar/index',
				),*/
				'MEID Converter'	=> array(
					'name' => 'tools/meid_convert',
					'url' => 'tools/meid_convert',
				),
				'Send Email'	=> array(
					'name' => 'tools/email',
					'url' => 'tools/email',
				),
				'Log'	=> array(
					'name' => 'log/index',
					'url' => 'log/index',
				),
			),
		),

	),


	// Login page
	'login_url' => 'panel/login',

	// AdminLTE settings
	'adminlte' => array(
		'body_class' 	=> array(
			'admin'	=> 'skin-blue',
		)
	),


	// Debug tools
	'debug' => array(
		'view_data'	=> FALSE,
		'profiler'	=> FALSE

	),

);



/*
| -------------------------------------------------------------------------
| Override values from /application/config/config.php
| -------------------------------------------------------------------------
*/
$config['sess_cookie_name'] = 'ci_session_admin';