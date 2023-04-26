<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - English
*
* Author: Ben Edmunds
*         ben.edmunds@gmail.com
*         @benedmunds
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  03.14.2010
*
* Description:  English language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful']            = 'Account creato con successo';
$lang['account_creation_unsuccessful']          = 'Impossibile creare account';
$lang['account_creation_duplicate_email']       = 'Email gia in uso o non valida';
$lang['account_creation_duplicate_identity']    = 'Identita gia in uso o non valida';
$lang['account_creation_missing_default_group'] = 'Gruppo predefinito non impostato';
$lang['account_creation_invalid_default_group'] = 'Non gruppo predefinito non valido';


// Password
$lang['password_change_successful']          = 'Password cambiata con successo';
$lang['password_change_unsuccessful']        = 'Impossibile cambiare la Password';
$lang['forgot_password_successful']          = 'Email reset password mandata';
$lang['forgot_password_unsuccessful']        = 'Impossibile Resettare Password';

// Activation
$lang['activate_successful']                 = 'Account Attivato';
$lang['activate_unsuccessful']               = 'Impossibile attivare Account';
$lang['deactivate_successful']               = 'Account Disattivato';
$lang['deactivate_unsuccessful']             = 'Impossibile disattivare Account';
$lang['activation_email_successful']         = 'Email attivazione mandata';
$lang['activation_email_unsuccessful']       = 'Impossibile mandare email attivazione';

// Login / Logout
$lang['login_successful']                    = 'Loggato con successo';
$lang['login_unsuccessful']                  = 'Login non corretto';
$lang['login_unsuccessful_not_active']       = 'Account inattivo';
$lang['login_timeout']                       = 'Chiuso fuori temporaneamente.  Riprova piu tardi.';
$lang['logout_successful']                   = 'Sei uscito con successo';

// Account Changes
$lang['update_successful']                   = 'Informazioni Account aggiornati con successo';
$lang['update_unsuccessful']                 = 'Impossibile aggiornare informazioni Account';
$lang['delete_successful']                   = 'Utente cancellato';
$lang['delete_unsuccessful']                 = 'Impossibile cancellare Utente';

// Groups
$lang['group_creation_successful']           = 'Gruppo creato con successo';
$lang['group_already_exists']                = 'Nome Gruppo gia in uso';
$lang['group_update_successful']             = 'Dettagli gruppo aggiornati';
$lang['group_delete_successful']             = 'Gruppo cancellato';
$lang['group_delete_unsuccessful']           = 'Impossibile cancellare gruppo';
$lang['group_delete_notallowed']             = 'Non si puo cancellare l\'amministratore del gruppo';
$lang['group_name_required']                 = 'Nome del gruppo e un campo obbligatprio';
$lang['group_name_admin_not_alter']          = 'Il nome del admin del gruppo non puo essere cambiato';

// Activation Email
$lang['email_activation_subject']            = 'Attivazione Account';
$lang['email_activate_heading']              = 'Attiva account';
$lang['email_activate_subheading']           = 'Cliccare il link seguente %s.';
$lang['email_activate_link']                 = 'Attiva il tuo Account';

// Forgot Password Email
$lang['email_forgotten_password_subject']    = 'Verifica Password dimenticata';
$lang['email_forgot_password_heading']       = 'Resetta Password';
$lang['email_forgot_password_subheading']    = 'Per favore clicca questo link %s.';
$lang['email_forgot_password_link']          = 'Resetta la tua Password';

// New Password Email
$lang['email_new_password_subject']          = 'Nuova Password';
$lang['email_new_password_heading']          = 'Nuova Password per %s';
$lang['email_new_password_subheading']       = 'La tua password è stata mandata a: %s';