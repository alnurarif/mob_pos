<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


// Account Creation
$lang['account_creation_successful']            = 'Compte crée avec succès';
$lang['account_creation_unsuccessful']          = 'Échec lors de la création du compte';
$lang['account_creation_duplicate_email']       = 'Email déjà utilisé ou invalide';
$lang['account_creation_duplicate_identity']    = 'Identité déjà utilisée ou invalide';
$lang['account_creation_missing_default_group'] = 'Groupe par défaut non renseigné';
$lang['account_creation_invalid_default_group'] = 'Nom du groupe par défaut invalide';


// Password
$lang['password_change_successful']          = 'Mot de passe changé avec succès';
$lang['password_change_unsuccessful']        = 'Impossible de changer le mot de passe';
$lang['forgot_password_successful']          = 'Mot de passe réinitialisé, émail envoyé';
$lang['forgot_password_unsuccessful']        = 'Impossible de réinitialiser le mot de passe';

// Activation
$lang['activate_successful']                 = 'Compte activé';
$lang['activate_unsuccessful']               = 'Impossible d\'activer le compte';
$lang['deactivate_successful']               = 'Compte désactivé';
$lang['deactivate_unsuccessful']             = 'Impossible de désactiver le compte';
$lang['activation_email_successful']         = 'Courriel d\'activation envoyé';
$lang['activation_email_unsuccessful']       = 'Impossible d\'envoyer un émail d\'activation';

// Login / Logout
$lang['login_successful']                    = 'Connexion réussie';
$lang['login_unsuccessful']                  = 'Login Incorrect';
$lang['login_unsuccessful_not_active']       = 'le compte est désactivé';
$lang['login_timeout']                       = 'Bloqué temporairement. Essayez plus tard.';
$lang['logout_successful']                   = 'Déconnexion réussie';

// Account Changes
$lang['update_successful']                   = 'Informations du compte mises à jours avec succès';
$lang['update_unsuccessful']                 = 'Impossible de mettre à jour les informations du compte';
$lang['delete_successful']                   = 'Utilisateur supprimé';
$lang['delete_unsuccessful']                 = 'Impossible de supprimer l\'utilisateur';

// Groups
$lang['group_creation_successful']           = 'Groupe créé avec succès';
$lang['group_already_exists']                = 'Ce nom de groupe est déjà utilisé';
$lang['group_update_successful']             = 'Détails de groupe mis à jour';
$lang['group_delete_successful']             = 'Groupe supprimé';
$lang['group_delete_unsuccessful']           = 'Impossible de supprimer ce groupe';
$lang['group_delete_notallowed']             = 'Impossible de supprimer le groupe de l\'administrateur';
$lang['group_name_required']                 = 'Le nom du groupe est requis';
$lang['group_name_admin_not_alter']          = 'Le nom du groupe Admin ne peut être changé';

// Activation Email
$lang['email_activation_subject']            = 'Activation du compte';
$lang['email_activate_heading']              = 'Activer le compte pour %s';
$lang['email_activate_subheading']           = 'Cliquer ce lien pour %s.';
$lang['email_activate_link']                 = 'Activer votre compter';

// Forgot Password Email
$lang['email_forgotten_password_subject']    = 'Vérification du mot de passe oublié';
$lang['email_forgot_password_heading']       = 'Réinitialiser le mot de passe pour %s';
$lang['email_forgot_password_subheading']    = 'Veuillez cliquer ce lien pour %s.';
$lang['email_forgot_password_link']          = 'Réinitialiser votre mot de passe';

// New Password Email
$lang['email_new_password_subject']          = 'Nouveau mot de passe';
$lang['email_new_password_heading']          = 'Nouveau mot de passe pour %s';
$lang['email_new_password_subheading']       = 'Votre mot de passe a été réinitialisé pour: %s';
