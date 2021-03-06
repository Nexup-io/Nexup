<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'task';
$route['listing/index'] = 'Listing/index';
$route['lists'] = 'Listing/index';
$route['lock_list'] = 'Listing/lock_list';
$route['lock_nexup_list'] = 'Listing/lock_nexup_list';
$route['update_listType'] = 'Listing/update_listType';
$route['change_listType'] = 'Listing/change_listType';
$route['lists/search_result'] = 'Listing/search_result';
$route['lists/cache'] = 'Listing/reload_cache';
$route['get_list_details'] = 'Listing/get_list_details';
$route['get_list_config'] = 'Listing/get_list_config';
$route['get_list_tab'] = 'Listing/get_list_tab';
$route['get_list_log'] = 'Listing/get_list_log';
$route['lists/(:any)'] = 'Listing/index';
$route['searchlist'] = 'Listing/search';
$route['sharelist'] = 'Listing/share';
$route['copy_list'] = 'Listing/copy_list';
$route['return_list_tab'] = 'Listing/return_list_tab';
$route['copy_list_summary'] = 'Listing/copy_list_summary';
$route['copy_list_summary_details'] = 'Listing/copy_list_summary_details';
$route['reset_attendance_list'] = 'Listing/reset_attendance_list';
$route['update_config'] = 'Listing/update_config';
$route['save_config'] = 'Listing/save_config';
$route['new_list_cron'] = 'Listing/new_list_cron';
$route['activate/(:any)'] = 'user/activate';
$route['404_override'] = '';
$route['order_change'] = 'task/order_change';
$route['item/get_bulk_data'] = 'task/get_bulk_data';
$route['item/preview'] = 'task/generate_preview';
$route['getShared'] = 'task/getShared';
$route['item/save_bulk_data'] = 'task/save_bulk_data';
$route['change_column_order'] = 'task/change_column_order';
$route['listing/push'] = 'listing/edit';
$route['item_order'] = 'task/order';
$route['get_list_desc'] = 'task/get_list_desc';
$route['update_list_desc'] = 'task/update_list_desc';
$route['export_log/(:any)'] = 'task/export_log';
$route['help_add_bulk'] = 'task/help_add_bulk';
$route['item'] = 'task/index';
$route['item/add'] = 'task/add';
$route['item/push'] = 'task/edit';
$route['item/update'] = 'task/update';
$route['item/get_task_data'] = 'task/get_task_data';
$route['item/complete'] = 'task/complete';
$route['item/present'] = 'task/present';
$route['item/delete'] = 'task/delete';
$route['item/remove'] = 'task/remove';
$route['item/update_url'] = 'task/update_url';
$route['delete_column'] = 'task/delete_column';
$route['get_next_task'] = 'task/get_next_task';
$route['next_task'] = 'task/next_task';
$route['next_item'] = 'task/next_item';
$route['next_item_random'] = 'task/next_item_random';
$route['undo_nexup'] = 'task/undo_nexup';
$route['item/update_column_name'] = 'task/update_column_name';
$route['list'] = 'task/index';
$route['item'] = 'task/index';
$route['list/(:any)'] = 'task/index';
$route['list2/(:any)'] = 'task/index2';
$route['item/(:any)'] = 'task/index';
$route['login'] = 'user/login';
$route['inflo_login'] = 'user/inflologin';
$route['profile'] = 'user/profile';
$route['register'] = 'user/register';
$route['logout'] = 'user/logout';
$route['change_avatar'] = 'user/change_avatar';
$route['social/facebook_callback'] = 'sociallogin/facebook_callback';
$route['history'] = 'user/operation_history';
$route['change_password'] = 'user/change_password';
$route['login/facebook'] = 'sociallogin/fb_redirect';
$route['facebooklogin'] = 'user/facebooklogin';
$route['save_ref'] = 'user/save_ref';
$route['get_nexup_box'] = 'Listing/get_nexup_box';
$route['translate_uri_dashes'] = FALSE;