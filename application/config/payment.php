<?php defined('BASEPATH') OR exit('No direct script access allowed');

/** 
 * Sandbox / Test Mode
 * -------------------------
 * TRUE means you'll be hitting PayPal's sandbox /Stripe test mode. FALSE means you'll be hitting the live servers.
 */
$config['TestMode'] = FALSE;

/* ***************** Stripe Keys ***************** */
/* 
 * Stripe API Keys
 * ------------------ 
 * You may obtain these by visiting account settings link and then API keys at https://dashboard.stripe.com/login
 */
$config['stripe_secret_key']			= $config['TestMode'] ? '' : 'sk_test_48By8pGwGXWnaiGvDmaXpYEW'; 
$config['stripe_publishable_key']		= $config['TestMode'] ? '' : 'pk_test_WsU27ND5poN7kvwP9vbEK2BL'; 

