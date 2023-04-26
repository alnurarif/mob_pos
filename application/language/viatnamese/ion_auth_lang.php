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
$lang['account_creation_successful']            = 'Tài Khoản được tạo thành công';
$lang['account_creation_unsuccessful']          = 'Không thể tạo tài khoản';
$lang['account_creation_duplicate_email']       = 'Email đã được sử dụng hoặc không đúng';
$lang['account_creation_duplicate_identity']    = 'ID đã được sử dụng hoặc không đúng';
$lang['account_creation_missing_default_group'] = 'Chưa chọn Nhóm';
$lang['account_creation_invalid_default_group'] = 'Tên nhóm không có';


// Password
$lang['password_change_successful']          = 'Thay đổi mật khẩu thành công';
$lang['password_change_unsuccessful']        = 'Không thể thay đổi mật khẩu';
$lang['forgot_password_successful']          = 'Đã gửi email đặt lại mật khẩu';
$lang['forgot_password_unsuccessful']        = 'Không thể đặt lại mật khẩu';

// Activation
$lang['activate_successful']                 = 'Tài Khoản Được Kích Hoạt';
$lang['activate_unsuccessful']               = 'Không thể kích hoạt tài khoản';
$lang['deactivate_successful']               = 'Vô Hiệu Hoá Tài Khoản';
$lang['deactivate_unsuccessful']             = 'Không thể vô hiệu hoá tài khoản';
$lang['activation_email_successful']         = 'Đã gửi email kích hoạt';
$lang['activation_email_unsuccessful']       = 'Không thể gửi email kích hoạt';

// Login / Logout
$lang['login_successful']                    = 'Đăng nhập thành công';
$lang['login_unsuccessful']                  = 'Thông tin đăng nhập không chính xác';
$lang['login_unsuccessful_not_active']       = 'Tài khoản không hoạt động';
$lang['login_timeout']                       = 'Tạm thời bị khoá.  Vui lòng thử lại sau.';
$lang['logout_successful']                   = 'Đăng xuất thành công';

// Account Changes
$lang['update_successful']                   = 'Cập nhật thành công thông tin tài khoản';
$lang['update_unsuccessful']                 = 'Không thể cập nhật thông tin tài khoản';
$lang['delete_successful']                   = 'Đã xoá User';
$lang['delete_unsuccessful']                 = 'Không thể xoá User';

// Groups
$lang['group_creation_successful']           = 'Tạo Nhóm thành công';
$lang['group_already_exists']                = 'Tên Nhóm đã có';
$lang['group_update_successful']             = 'Chi tiết Nhóm được cập nhật';
$lang['group_delete_successful']             = 'Đã xoá Nhóm';
$lang['group_delete_unsuccessful']           = 'Không thể xoá nhóm';
$lang['group_delete_notallowed']             = 'Không thể xoá Nhóm Quản Trị';
$lang['group_name_required']                 = 'Phải nhập Tên Nhóm';
$lang['group_name_admin_not_alter']          = 'Tên Nhóm Admin không thể thay đổi';

// Activation Email
$lang['email_activation_subject']            = 'Kích Hoạt Tài Khoản';
$lang['email_activate_heading']              = 'Kích hoạt tài khoản cho %s';
$lang['email_activate_subheading']           = 'Vui lòng click vào liên kết để %s.';
$lang['email_activate_link']                 = 'Kích Hoạt Tài Khoản';

// Forgot Password Email
$lang['email_forgotten_password_subject']    = 'Xác Minh Quên Mật Khẩu';
$lang['email_forgot_password_heading']       = 'Đặt lại Mật Khẩu cho %s';
$lang['email_forgot_password_subheading']    = 'Vui lòng click vào liên kết để %s.';
$lang['email_forgot_password_link']          = 'Đặt Lại Mật Khẩu';

// New Password Email
$lang['email_new_password_subject']          = 'Mật Khẩu Mới';
$lang['email_new_password_heading']          = 'Mật Khẩu Mới cho %s';
$lang['email_new_password_subheading']       = 'Mật khẩu đã được đặt lại cho: %s';
