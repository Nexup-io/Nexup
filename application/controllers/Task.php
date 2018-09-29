<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Task extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array('TasksModel', 'ListsModel'));
//        include APPPATH . 'third_party/fb_link_preview/php/classes/LinkPreview.php';
    }

    /**
     * Display all tasks for a list
     * @author SG
     */
    public function index() {
        if (($this->uri->segment(1) == 'list' || $this->uri->segment(1) == 'item') && ($this->uri->segment(2) == '' || $this->uri->segment(2) == NULL)) {
            $this->session->set_flashdata('error', 'The list you are looking for does not exist!');
            redirect(base_url() . 'lists', 'refresh');
        }
        $data['password'] = '';
        $data['modification_password'] = '';
        $data['title'] = 'Nexup';
        $data['list_id'] = 0;
        $data['list_name'] = '';
        $data['list_slug'] = '';
        $data['config']['show_completed'] = 'True';
        $data['config']['allow_move'] = 'True';
        $data['config']['allow_undo'] = 0;
        $data['config']['allow_maybe'] = 0;
        $data['config']['show_time'] = 0;
        $data['config']['enable_comment'] = 0;
        $data['config']['enable_attendance_comment'] = 1;
        $data['config']['show_preview'] = 1;
        $data['config']['show_author'] = 0;
        $data['config']['allow_append_locked'] = 0;
        $data['config']['visible_in_search'] = 0;
        $data['config']['has_password'] = 0;
        $data['config']['has_modify_password'] = 0;
        $data['config']['start_collapsed'] = 0;
        $data['list_author'] = 'Anonymous';
        $data['list_user_id'] = 0;
        if (isset($_SESSION['id'])) {
            $data['list_user_id'] = $_SESSION['id'];
        }
        $data['type_id'] = 1;
        $data['is_locked'] = 0;

        $slug = '';
        if ($this->uri->segment(2) != null) {
            $slug = $this->uri->segment(2);
        } elseif (isset($_SESSION['last_slug']) && $_SESSION['last_slug'] != '') {
            $slug = $_SESSION['last_slug'];
        }
        if ($slug == '' && $this->uri->segment(1) == 'item') {
            redirect(base_url() . 'lists', 'refresh');
        }
        $data['list_owner_id'] = 0;
        $data['multi_col'] = 0;
        $data['list_desc'] = '';
        $total_visit_count = 0;
        $total_visit_count_long = 0;
        $list_id = 0;
        if ($slug != '') {

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }


            $list = $this->ListsModel->find_list_details_by_slug($slug);
            $list_id = $list['list_id'];
//            echo decrypt(base64_decode($list['password']), $list['salt']);
//            p($list); exit;
            if($list['parent_id'] > 0){
                $this->session->set_flashdata('error', 'The list you are looking for does not exist!');
                redirect(base_url() . 'lists', 'refresh');
            }
            
            if($list['type_id'] == 12){
                $sub_lists = $this->ListsModel->find_sublists($list['list_id']);
                $data['sublists'] = $sub_lists;
            }
            if ($list['unshared_with'] != '') {
                $unshared_array = explode(',', $list['unshared_with']);
                if (isset($_SESSION['id'])) {
                    $pos = array_search($_SESSION['id'], $unshared_array);
                    if ($pos >= 0) {
                        unset($unshared_array[$pos]);
                    }
                    $unshared_str = implode(',', $unshared_array);

                    $update_data_share['unshared_with'] = $unshared_str;
//                    p($update_data_share); exit;
                    $update_unshared = $this->ListsModel->update_list_data($list['list_id'], $update_data_share);
                }
            }
            if (empty($list)) {
                redirect(base_url() . 'lists', 'refresh');
            }
            $visit_user_id = 0;
            if (isset($_SESSION['id'])) {
                $visit_user_id = $_SESSION['id'];
            }
            $visit_list_id = $list['list_id'];

            $today_date = date('Y-m-d H:i:s');
            $visit_data['user_id'] = $visit_user_id;
            $visit_data['list_id'] = $list['list_id'];
            $visit_data['date_visited'] = $today_date;
            $visit_data['created'] = $today_date;
            $visit_data['modified'] = $today_date;
            $visit_data['user_ip'] = $_SERVER['REMOTE_ADDR'];
            $record_history = $this->TasksModel->record_visit($visit_data);

            $total_visit_count_long = $this->TasksModel->count_list_visitors($visit_list_id);
            $total_visit_count = number_format_short($total_visit_count_long);
            

            if (isset($_SESSION['id']) && $_SESSION['id'] > 0) {

                $data_get['Apikey'] = API_KEY;
                $data_get['SearchText'] = '';

                $data_get = json_encode($data_get);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, API_URL . "account/GetListsSharedWithLoggedInUser");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_get);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $server_output = curl_exec($ch);
                $response = (array) json_decode($server_output);

                $shared_list_ids = array();
                if (isset($response['success']) && $response['success'] == 1) {
                    $shared_cnt = 0;
                    foreach ($response['data'] as $rid => $res):
                        array_push($shared_list_ids, $res->ListId);
                    endforeach;
                }
            }

            if (!empty($list)) {
                $extra_attendance = $this->TasksModel->get_all_extra($list['list_id']);
                $data['attendance_data'] = $extra_attendance;
                $list_inflo_id = $list['list_inflo_id'];
            }

//            $list_extra = $this->TasksModel->get_attendance_extra($list['list_id']);
//            p($_SESSION); exit;
            if ($list['user_id'] == 0) {

                if (isset($_SESSION['id']) && $_SESSION['id'] > 0) {
//                    p($list); exit;
                    $updt_list['user_id'] = $_SESSION['id'];
                    $user_name = '';
                    if (isset($_SESSION['first_name']) && !empty($_SESSION['first_name'])) {
                        $user_name .= $_SESSION['first_name'];
                    }
                    if (isset($_SESSION['last_name']) && !empty($_SESSION['last_name'])) {
                        $user_name .= ' ' . $_SESSION['last_name'];
                    }
                    if (empty($_SESSION['first_name']) && empty($_SESSION['last_name'])) {
                        $user_name = $_SESSION['email'];
                    }
                    $updt_list['user_id'] = $_SESSION['id'];
                    $updt_list['created_user_name'] = $user_name;
                    $list['created_user_name'] = $user_name;
                    $this->ListsModel->update_list_data($list['list_id'], $updt_list);
                    $data_send['Apikey'] = API_KEY;
                    $data_send['Listid'] = $list_inflo_id;
                    $data_send['CreatedByUserId'] = $_SESSION['id'];
//                    p($data_send); exit;

                    $data_send = json_encode($data_send);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, API_URL . "account/UpdateList");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_send);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $server_output = curl_exec($ch);
                    $response_update = (array) json_decode($server_output);
                    if (isset($response_update['success']) && $response_update['success'] == 1) {
                        $update_list['created_user_name'] = $response_update['data']->CreateByUserFullName;
                        $this->ListsModel->update_list_data($list['list_id'], $update_list);
                    }
                }
            }

            if (empty($list)) {
                $this->session->set_flashdata('error', 'The list you are looking for does not exist!');
                redirect(base_url() . 'lists', 'refresh');
            }
            if ($list['is_deleted'] == 1) {
                $this->session->set_flashdata('error', 'The list you are looking for does not exist!');
                redirect(base_url() . 'lists', 'refresh');
            }
            $nexup_type = '1';
            if ($list['type_id'] == 8) {
                $nexup_type = '3';
            }
            $last_log = $this->TasksModel->get_log_last($list['list_id'], $nexup_type);
            $deleted_item = $this->TasksModel->get_task_details($last_log['new_order'], $list['list_id']);
            if ($deleted_item == 1) {
                $order_item = $this->TasksModel->get_item_order($last_log['new_order']);
                $log_data = $this->TasksModel->get_similar_item($order_item['order'], $list['list_id']);
            }
            $data['last_log'] = array();
            if (!empty($last_log)) {
                if ($deleted_item == 0) {
                    $data['last_log'] = $last_log['new_order'];
                } else {
                    $data['last_log'] = $log_data['TaskId'];
                }
            }
            $sort = 'list_columns.order asc';
            if ($list['type_id'] == 11) {
                if ($list['show_time'] == 1) {
                    $sort .= ', list_data.is_present=0,list_data.is_present, attendance_data.check_date desc, list_data.value, list_data.order asc';
                } else {
                    $sort .= ', list_data.is_present=0,list_data.is_present, list_data.value, list_data.order asc';
                }
            } elseif ($list['type_id'] == 5) {
//                $sort .= ', list_data.is_completed, list_data.modified, list_data.order asc';
                $sort .= ', list_data.is_completed, list_data.order asc';
            } else {
                $sort .= ', list_data.order asc';
            }

            $tasks = $this->TasksModel->get_tasks_2($list['list_id'], $sort);
            $task_data = array();
            $c_index = 1;

            foreach ($tasks as $tid => $tdata):

                if ($tdata['order'] != $c_index) {
                    $c_index = $tdata['order'];
                }
                $task_data[$c_index][] = $tdata;
            endforeach;
//            p($task_data); exit;

            $columns = $this->TasksModel->getColumns($list['list_id']);
            $data['list_name'] = $list['list_name'];
            $data['list_id'] = $list['list_id'];
            $data['list_slug'] = $list['list_slug'];
            $data['show_list_slug'] = $list['list_slug'];
            $data['list_author'] = $list['created_user_name'];
            $data['list_user_id'] = $list['user_id'];

            if (!empty($list['custom_slug'])) {
                $data['show_list_slug'] = $list['custom_slug'];
            }
            $data['type_id'] = $list['type_id'];
            if ($list['show_completed'] == 0) {
                $show_completed = 'False';
            } else {
                $show_completed = 'True';
            }
            $data['config']['show_completed'] = $show_completed;
            if ($list['allow_move'] == 0) {
                $allow_move = 'False';
            } else {
                $allow_move = 'True';
            }
            $data['config']['allow_move'] = $allow_move;
            $data['config']['allow_undo'] = $list['allow_undo'];
            $data['list_owner_id'] = $list['list_owner_id'];
            $data['is_locked'] = $list['is_locked'];
            if (isset($_SESSION['id']) && $list['is_locked'] == 1) {
                if ($list['user_id'] != $_SESSION['id']) {
                    $data['is_locked'] = 2;
                }
            }
            $data['config']['allow_maybe'] = $list['allow_maybe'];
            $data['config']['show_time'] = $list['show_time'];
            $data['config']['show_preview'] = $list['show_preview'];
            $data['config']['enable_comment'] = $list['enable_comment'];
            $data['config']['enable_attendance_comment'] = $list['enable_attendance_comment'];
            $data['config']['show_author'] = $list['show_author'];
            $data['config']['allow_append_locked'] = $list['allow_append_locked'];
            $data['config']['visible_in_search'] = $list['visible_in_search'];
            if($list['password'] != ''){
                $data['config']['has_password'] = 1;
            }
            if($list['modification_password'] != ''){
                $data['config']['has_modify_password'] = 1;
            }
            $data['config']['start_collapsed'] = $list['start_collapsed'];
            
            if($data['config']['has_password'] == 1 && $list['password'] != ''){
                $data['password'] = decrypt(base64_decode($list['password']), $list['salt']);
            }
            if($data['config']['has_modify_password'] == 1 && $list['modification_password'] != ''){
                $data['modification_password'] = decrypt(base64_decode($list['modification_password']), $list['salt']);
            }
            $data['salt'] = $list['salt'];

            if (!empty($columns)) {
//            if(count($columns) > 1){
                $data['columns'] = $columns;
            }
            if (count($columns) > 1) {
                $data['multi_col'] = 1;
            }
            $data['tasks'] = $task_data;
            $data['list_desc'] = $this->ListsModel->list_desc_get($list['list_id']);

            if ($list['type_id'] == 11) {
                if ($list['show_time'] != 1) {
                    $task_for_sort = $data['tasks'];
                    $temp_arr_sorted = array();
                    $temp_arr_yes = array();
                    $temp_arr_maybe = array();
                    $temp_arr_no = array();
                    $temp_arr_blank = array();
                    foreach ($task_for_sort as $tf_id => $tf_data):
                        if ($tf_data[0]['IsPresent'] == 1) {
                            $temp_arr_yes[$tf_id] = $tf_data[0]['TaskName'];
                        } elseif ($tf_data[0]['IsPresent'] == 2) {
                            $temp_arr_maybe[$tf_id] = $tf_data[0]['TaskName'];
                        } elseif ($tf_data[0]['IsPresent'] == 3) {
                            $temp_arr_no[$tf_id] = $tf_data[0]['TaskName'];
                        } else {
                            $temp_arr_blank[$tf_id] = $tf_data[0]['TaskName'];
                        }
                    endforeach;
                    sort($temp_arr_yes);
                    sort($temp_arr_maybe);
                    sort($temp_arr_no);
                    sort($temp_arr_blank);


                    foreach ($temp_arr_yes as $yes):
                        foreach ($task_for_sort as $sort_id => $sort_task):
                            if ($yes == $sort_task[0]['TaskName']) {
                                $temp_arr_sorted[$sort_id] = $sort_task;
                            }
                        endforeach;
                    endforeach;

                    foreach ($temp_arr_maybe as $maybe):
                        foreach ($task_for_sort as $sort_id => $sort_task):
                            if ($maybe == $sort_task[0]['TaskName']) {
                                $temp_arr_sorted[$sort_id] = $sort_task;
                            }
                        endforeach;
                    endforeach;

                    foreach ($temp_arr_no as $no):
                        foreach ($task_for_sort as $sort_id => $sort_task):
                            if ($no == $sort_task[0]['TaskName']) {
                                $temp_arr_sorted[$sort_id] = $sort_task;
                            }
                        endforeach;
                    endforeach;

                    foreach ($temp_arr_blank as $blank):
                        foreach ($task_for_sort as $sort_id => $sort_task):
                            if ($blank == $sort_task[0]['TaskName']) {
                                $temp_arr_sorted[$sort_id] = $sort_task;
                            }
                        endforeach;
                    endforeach;
//                        p($temp_arr_sorted);
//                    exit;

                    $data['tasks'] = $temp_arr_sorted;

//                    p($temp_arr_sorted); exit;
                }
            }

//            p($data['tasks']); exit;


            if (!empty($data)) {

                if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                    if (isset($_SESSION['auth_visit']) && $_SESSION['auth_visit'] != null) {
                        $visited_arr = $_SESSION['auth_visit'];
                        if (in_array($data['list_id'], $visited_arr['list_id'])) {
                            $index = array_search($data['list_id'], $visited_arr['list_id']);
                            unset($visited_arr['list_id'][$index]);
                            unset($visited_arr['list_name'][$index]);
                            unset($visited_arr['list_slug'][$index]);
                        }

                        $visited_arr['list_id'][] = $data['list_id'];
                        $visited_arr['list_name'][] = $data['list_name'];
                        $visited_arr['list_slug'][] = $data['list_slug'];
                        $_SESSION['auth_visit'] = $visited_arr;
                    } else {
                        $visited_arr['list_id'][] = $data['list_id'];
                        $visited_arr['list_name'][] = $data['list_name'];
                        $visited_arr['list_slug'][] = $data['list_slug'];
                        $_SESSION['auth_visit'] = $visited_arr;
                    }
                } else {
                    if (isset($_SESSION['unauth_visit']) && $_SESSION['unauth_visit'] != null) {
                        $visited_arr = $_SESSION['unauth_visit'];

                        if (in_array($data['list_id'], $visited_arr['list_id'])) {
                            $index = array_search($data['list_id'], $visited_arr['list_id']);
                            unset($visited_arr['list_id'][$index]);
                            unset($visited_arr['list_name'][$index]);
                            unset($visited_arr['list_slug'][$index]);
                        }

                        $visited_arr['list_id'][] = $data['list_id'];
                        $visited_arr['list_name'][] = $data['list_name'];
                        $visited_arr['list_slug'][] = $data['list_slug'];
                        $_SESSION['unauth_visit'] = $visited_arr;
                    } else {
                        $visited_arr['list_id'][] = $data['list_id'];
                        $visited_arr['list_name'][] = $data['list_name'];
                        $visited_arr['list_slug'][] = $data['list_slug'];
                        $_SESSION['unauth_visit'] = $visited_arr;
                    }
                }
                $log = $this->TasksModel->find_log($list['list_id']);
                if ($list['type_id'] == 8) {
                    $log = $this->TasksModel->find_log_random($list['list_id']);
                }
//                echo $this->db->last_query(); exit;

                $first_column_id = $this->TasksModel->find_first_column($list['list_id']);
//                p($first_column_id); exit;
                $log_print = $log;
                foreach ($log as $lgid => $lgval):
                    $order_current_item = $this->TasksModel->find_order_log_single($list['list_id'], $lgval['new_order']);
                    $first_item_current = $this->TasksModel->find_item_first($list['list_id'], $order_current_item, $first_column_id);
                    $log_print[$lgid]['value'] = $first_item_current;
                endforeach;
//                p($log_print); exit;
//                p($log); exit;
                if (!empty($log)) {
                    $data['log_list'] = $log_print;
                }
            } else {
                redirect($this->agent->referrer(), 'refresh');
            }
        }
        $data['total_visits'] = $total_visit_count;
        $data['total_visits_long'] = $total_visit_count_long;

        $list_types = $this->TasksModel->getListTypes();
        $data['list_types'] = $list_types;
        $max_order = 0;
        if (!empty($list)) {
            $max_order = $this->TasksModel->get_last_order_of_item($list['list_id']);
        }
        $data['max_order'] = $max_order;

        $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($data['list_id']);

        $header = array('Content-Type: application/json');
        if (isset($_SESSION['xauthtoken'])) {
            $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
            array_push($header, $val);
        }

        $data_send_check['Apikey'] = API_KEY;
//        $data_send['Listid'] = 1;
        $data_send_check['Listid'] = $list_inflo_id;

        $data_send_check = json_encode($data_send_check);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_send_check);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $server_output = curl_exec($ch);
        $response = (array) json_decode($server_output);

        $list_share_data = array();
        if (isset($response['success']) && $response['success'] == 1) {
            $list_share_data = (array) $response['data'];
        }
        $data['list_share_data'] = $list_share_data;

        $list_owner = $this->ListsModel->getListOwner($data['list_id']);

        $allowed_access = 1;

        $owner_find = 0;
        if (isset($_SESSION['id'])) {
            $owner_find = $_SESSION['id'];
        }
        if ($list_owner != $owner_find) {
            $priviladge_get['Apikey'] = API_KEY;
            $priviladge_get['Listid'] = $list_inflo_id;
            $priviladge_get['userid'] = 0;
            if (isset($_SESSION['id'])) {
                $priviladge_get['userid'] = $_SESSION['id'];
            }
            $priviladge_get = json_encode($priviladge_get);

            $ch_priviledge = curl_init();
            curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
            curl_setopt($ch_priviledge, CURLOPT_POST, 1);
            curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
            curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
            $server_output_priviledge = curl_exec($ch_priviledge);
            $response_priviledge = (array) json_decode($server_output_priviledge);
//            p($response_priviledge); exit;
            if (isset($response_priviledge['success'])) {
                if ($response_priviledge['success'] == 1) {
                    $allowed_access = 1;
                }elseif(isset($_SESSION['modification_pass']) && in_array($list_id, $_SESSION['modification_pass'])){
                    $allowed_access = 1;
                } else {
                    $allowed_access = 0;
                    if (isset($response_priviledge['data'])) {
                        if ($response_priviledge['data']->IsPublic == 'True') {
                            if ($response_priviledge['data']->IsLocked == 'True') {
                                $allowed_access = 2;
                            }
                        }
                    }
//                    echo $allowed_access; exit;
                }
            }
        }


        $data['allowed_access'] = $allowed_access;
        $data['list_inflo_id'] = $list_inflo_id;

        $blank_flag = 0;
        $yes_flag = 1;
        $no_flag = 3;
        $maybe_flag = 2;
        $data['total_yes'] = 0;
        $data['total_no'] = 0;
        $data['total_maybe'] = 0;
        $data['total_blank'] = 0;
        if (isset($list['type_id']) && $list['type_id'] == 11) {
            $total_cols = $this->TasksModel->count_col($list['list_id']);
            
            $first_col_id = $this->TasksModel->find_first_column($list['list_id']);
            
            $yes = $this->TasksModel->find_count_present($first_col_id, $yes_flag, $list['list_id']);
            if ($total_cols > 0) {
                $data['total_yes'] = $yes;
            }
            $no = $this->TasksModel->find_count_present($first_col_id, $no_flag, $list['list_id']);
            if ($total_cols > 0) {
                $data['total_no'] = $no;
            }

            $maybe = $this->TasksModel->find_count_present($first_col_id, $maybe_flag, $list['list_id']);
            if ($total_cols > 0) {
                $data['total_maybe'] = $maybe;
            }

            $blank = $this->TasksModel->find_count_present($first_col_id, $blank_flag, $list['list_id']);
            if ($total_cols > 0) {
                $data['total_blank'] = $blank;
            }
        }
        if(isset($list) && $list['type_id'] == 12){
            $this->template->load('default_template2', 'task/index2', $data);
        }elseif(isset($list) && $list['type_id'] == 6){
            $data['week_start_end'] = $this->x_week_range(date('m/j/Y'));
            $start_date = $data['week_start_end'][0];
            $end_date = date_add( new DateTime($data['week_start_end'][1]) , new DateInterval('P1D') );
            $end_date = $end_date->format('Y-m-d H:i:s');
                    
            $period = new DatePeriod(
                new DateTime($start_date),
                new DateInterval('P1D'),
                new DateTime($end_date)
           );
            $dates_list_names = array();
            $child_list_data = array();
            foreach ($period as $key => $value) {
                $child_list_id = $this->ListsModel->find_list_id_by_name($value->format('m/j/Y'), $list_id);
                
                $dates_list_names[$value->format('m/j/Y')] = $child_list_id;
                $child_list_data[$value->format('m/j/Y')] = array();
                
                if(!empty($child_list_id)){
                    $first_column_id = $this->TasksModel->find_first_column($child_list_id);
                    $second_column_id = $this->TasksModel->find_second_column($child_list_id);
                    
                    $array_col_ids = array();
                    if(!empty($first_column_id)){
                        array_push($array_col_ids, $first_column_id);
                    }
                    
                    if(!empty($second_column_id)){
                        array_push($array_col_ids, $second_column_id);
                    }
                    
                    if(!empty($first_column_id)){
                        $child_list_data[$value->format('m/j/Y')] = array_chunk($this->TasksModel->get_tasks_for_calendar($child_list_id, $array_col_ids), 2);
                    }
                }
            }
//            p($child_list_data); exit;
            $data['child_list_data'] = $child_list_data;
            $data['date_list_names'] = $dates_list_names;
            
            $this->template->load('default_template_calendar', 'task/week_index', $data);
        }else{
            $this->template->load('default_template', 'task/index', $data);
        }
    }
    
    function x_week_range($date) {
        $ts = strtotime($date);
        $start = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
        return array(date('Y-m-d', $start),
        date('Y-m-d', strtotime('next saturday', $start)));
    }
    
    /*
     * Function to get custom week data
     * @author: SG
     */
    public function get_week_data(){
        if($this->input->post()){
            $list_id = $this->input->post('list_id');
            $week_start_end = $this->x_week_range(date('m/j/Y', strtotime($this->input->post('startDate'))));
            $start_date = $week_start_end[0];
            $end_date = date_add( new DateTime($week_start_end[1]) , new DateInterval('P1D') );
            $end_date = $end_date->format('m/j/Y');
            $period = new DatePeriod(
                new DateTime($start_date),
                new DateInterval('P1D'),
                new DateTime($end_date)
            );
            $dates_list_names = array();
            $child_list_data = array();
            foreach ($period as $key => $value) {
                $child_list_id = $this->ListsModel->find_list_id_by_name($value->format('m/j/Y'), $list_id);
                $dates_list_names[$value->format('m/j/Y')] = $child_list_id;
                $child_list_data[$value->format('m/j/Y')] = array();
                
                if(!empty($child_list_id)){
                    $first_column_id = $this->TasksModel->find_first_column($child_list_id);
                    $second_column_id = $this->TasksModel->find_second_column($child_list_id);
                    
                    $array_col_ids = array();
                    if(!empty($first_column_id)){
                        array_push($array_col_ids, $first_column_id);
                    }
                    
                    if(!empty($second_column_id)){
                        array_push($array_col_ids, $second_column_id);
                    }
                    
                    if(!empty($first_column_id)){
                        $child_list_data[$value->format('m/j/Y')] = array_chunk($this->TasksModel->get_tasks_for_calendar($child_list_id, $array_col_ids), 2);
                    }
                }
            }
//            p($child_list_id);
            
            $res = '';
            $res .= '<div class="head_row_week">';
            $count_date = 0;
            $current_month = date('m', strtotime(key($dates_list_names)));
            foreach ($dates_list_names as $date_key => $date_val):
                if($date_val != '') {
                    $print_date_id = $date_val;
                } else {
                    $print_date_id = 0;
                }
                $res .= '<div class="date_Detail_week" data-date="' . $date_key . '" data-listid="' . $print_date_id . '">';
                $res .= '<h2>';
                $day = '';
                if(date('D', strtotime($date_key)) != 'Sun'){
                    $day = date('D', strtotime($date_key));
                            
                }
                $res .= '<div class="day_w day_sun">' . $day . '</div>';
                $res .= '<div class="day_date">';
                    $this_month = date('m', strtotime($date_key));
                    if ($count_date == 0) {
                        $res .= date('M', strtotime($date_key)) . ' ';
                    } elseif ($this_month > $current_month) {
                        $current_month = $this_month;
                        $res .= date('M', strtotime($date_key)) . ' ';
                    }
                    $res .= date('d', strtotime($date_key));
                    $count_date++;
                $res .= '</div>';
                $res .= '</h2>';
                $res .= '</div>';
            endforeach;
            $res .= '</div>';
            $res .= '<div class="body_week">';
            foreach ($child_list_data as $data_key => $data_val):
                $data_list_id = 0;
            if($dates_list_names[$data_key] != ''){
                $data_list_id = $dates_list_names[$data_key];
            }
                $res .= '<div class="day_content" data-date="' . $data_key . '" data-listid="' . $data_list_id . '">';
                foreach ($data_val as $key_d => $val_d):
                    $res .= '<div class="data_content_w">';
                    $res .= '<div class="data_name">';
                    $res .= trim($val_d[0]['TaskName']);;
                    $res .= '</div>';
                    $res .= '<div class="time_display">';
                    $res .= trim($val_d[1]['TaskName']);;
                    $res .= '</div>';
                    $res .= '</div>';
                endforeach;
                $res .= '</div>';
            endforeach;
            $res .= '</div>';
//            exit;
            
            echo $res; exit;
            
            p($child_list_data); exit;
        }
    }
    
    /*
     * Get monthly list
     * @author: SG
     */
    public function get_month_list(){
        if($this->input->post()){
            $list_id = $this->input->post('list_id');
            if($this->input->post('start_date') != ''){
                $start_date = date('m/j/Y', strtotime($this->input->post('start_date')));
            }else{
                $start_date = date('m/01/Y'); // hard-coded '01' for first day
            }
            if($this->input->post('end_date') != ''){
                $end_date = date('m/j/Y', strtotime($this->input->post('end_date')));
            }else{
                $end_date  = date('m/t/Y');
            }
            
            
            $firstdayofmonth = date('w', strtotime($start_date));
            $lastdayofmonth = date('w', strtotime($end_date));
            $month_start_diff = $this->x_week_range(date('m/j/Y', strtotime($start_date)));
            $month_end_diff = $this->x_week_range(date('m/j/Y', strtotime($end_date)));
            $month_end_date = date_add( new DateTime($month_end_diff[1]) , new DateInterval('P1D') );
            $month_end_date = $month_end_date->format('Y-m-d H:i:s');
            $period = new DatePeriod(
                new DateTime($month_start_diff[0]),
                new DateInterval('P1D'),
                new DateTime($month_end_date)
            );
            
            
            $dates_list_names = array();
            foreach ($period as $pkey=>$pval):
                $child_list_id = $this->ListsModel->find_list_id_by_name($pval->format('m/j/Y'), $list_id);
                $dates_list_names[$pval->format('m/j/Y')] = $child_list_id;
                $child_list_data[$pval->format('m/j/Y')] = array();
                if(!empty($child_list_id)){
                    $first_column_id = $this->TasksModel->find_first_column($child_list_id);
                    $second_column_id = $this->TasksModel->find_second_column($child_list_id);
                    $array_col_ids = array();
                    if(!empty($first_column_id)){
                        array_push($array_col_ids, $first_column_id);
                    }
                    
                    if(!empty($second_column_id)){
                        array_push($array_col_ids, $second_column_id);
                    }
                    
                    if(!empty($first_column_id)){
                        $child_list_data[$pval->format('m/j/Y')] = array_chunk($this->TasksModel->get_tasks_for_calendar($child_list_id, $array_col_ids, 6), 2);
                    }
                }
            endforeach;
            
            $data['dates_list_names'] = $dates_list_names;
            $data['child_list_data'] = $child_list_data;
            
            
            $list_names_array = array_chunk($dates_list_names, 7, true);
            
            $res = '';
            if($this->input->post('start_date') == ''){
                $res .= '<div class="caleneder_div_wrapper">';
                $res .= '<div class="container month_view_container">';
                $res .= '<div class="div_btn_name">';
                $res .= '<div class="btn_box_add">';
                $res .= '<a class="btn-today">Today</a>';
                $res .= '<a class="btn-day">Day</a>';
                $res .= '<a class="btn-week">Week</a>';
                $res .= '<a class="active btn-month">Month</a>';
                $res .= '</div>';
                $res .= '</div>';
                $res .= '<div class="calender_div_inner_box">';
                $res .= '<div class="div_left_one">';
                $res .= '<div class="month_calendar"></div>';
                $res .= '</div>';
                $res .= '<div class="div_right_one month_view_class">';
            }
                
            
            
            
            
            $res .= '<div class="div_celender_detail">';
            $res .= '<div class="row_date">';
            $res .= '<div class="day_name"><span>Sun</span></div>';
            $res .= '<div class="day_name"><span>Mon</span></div>';
            $res .= '<div class="day_name"><span>Tue</span></div>';
            $res .= '<div class="day_name"><span>Wed</span></div>';
            $res .= '<div class="day_name"><span>Thu</span></div>';
            $res .= '<div class="day_name"><span>Fri</span></div>';
            $res .= '<div class="day_name"><span>Sat</span></div>';
            $res .= '</div>';
            $res .= '<div class="presentation">';
            $count_date = 0;
            $current_month = date('m', strtotime(key($child_list_data)));
            
            foreach ($list_names_array as $list_names_key => $list_name_data):
                
            
                $res .= '<div class="row_detail">';
                $res .= '<div class="date_wrap">';
                foreach ($list_name_data as $name_key => $name_data):
                    $dlist_id = 0;
                    if($name_data != ''){
                        $dlist_id = $name_data;
                    }
                    $this_month = date('m', strtotime($name_key));
                    $res .= '<div class="day_number" data-listid="' . $dlist_id . '">';
                    $res .= '<h2 class="date_h2">';
                    if ($count_date == 0) {
                        $date_show = date('M j, Y', strtotime($name_key)) . ' ';
                    } elseif ($this_month > $current_month) {
                        $current_month = $this_month;
                        $date_show = date('M j', strtotime($name_key)) . ' ';
                    }else{
                        $date_show = date('j', strtotime($name_key));
                    }
                    $res .=  $date_show;
                    $res .= '</h2>';
                    $res .= '</div>';
                    $count_date ++;
                endforeach;
                $res .= '</div>';
                $res .= '<div class="content_wrap">';
                 foreach ($list_name_data as $names_key => $names_data):
                    $res .= '<div class="day_data" data-listid="' . $names_data . '">';
                    foreach ($child_list_data[$names_key] as $c_key => $c_data):
                        $res .= '<h2 class="h2_content">';
                        if(!empty($c_data)){
                            $res .= $c_data[0]['TaskName'];
                        }else{
                            $res .= '&nbsp;';
                        }
                        $res .= '</h2>';
                    endforeach;
                    
                    $res .= '</div>';
                 endforeach;
                    
                $res .= '</div>';
                $res .= '</div>';
            endforeach;
            
            $res .= '</div>';
            $res .= '</div>';
            
            
            if($this->input->post('start_date') == ''){
                $res .= '</div>';
                $res .= '</div>';
                $res .= '</div>';
                $res .= '</div>';
            }
                
            echo $res; exit;
            
            
            $this->template->load('default_template_calendar', 'task/month_index', $data);
            
        }
    }
    
    /*
     * Get weekly list
     * @author: SG
     */
    
    public function get_week_list(){
        if($this->input->post()){
            $res = '';
            $list_id = $this->input->post('list_id');
            $data['week_start_end'] = $this->x_week_range(date('m/j/Y'));
            
            if($this->input->post('start_date') && $this->input->post('start_date') != ''){
                $start_date = $this->input->post('start_date');
            }else{
                $start_date = $data['week_start_end'][0];
            }
            
            if($this->input->post('end_date') && $this->input->post('start_date') != ''){
                $end_date = $this->input->post('start_date');
                $end_date = date_add( new DateTime($end_date) , new DateInterval('P1D') );
                $end_date = $end_date->format('Y-m-d H:i:s');
            }else{
                $end_date = $data['week_start_end'][0];
                $end_date = date_add( new DateTime($data['week_start_end'][1]) , new DateInterval('P1D') );
                $end_date = $end_date->format('Y-m-d H:i:s');
            }
            
            
                    
            $period = new DatePeriod(
                new DateTime($start_date),
                new DateInterval('P1D'),
                new DateTime($end_date)
            );   
            $dates_list_names = array();
            $child_list_data = array();
            foreach ($period as $key => $value) {
                $child_list_id = $this->ListsModel->find_list_id_by_name($value->format('m/j/Y'), $list_id);
                
                $dates_list_names[$value->format('m/j/Y')] = $child_list_id;
                $child_list_data[$value->format('m/j/Y')] = array();
                
                if(!empty($child_list_id)){
                    $first_column_id = $this->TasksModel->find_first_column($child_list_id);
                    $second_column_id = $this->TasksModel->find_second_column($child_list_id);
                    
                    $array_col_ids = array();
                    if(!empty($first_column_id)){
                        array_push($array_col_ids, $first_column_id);
                    }
                    
                    if(!empty($second_column_id)){
                        array_push($array_col_ids, $second_column_id);
                    }
                    
                    if(!empty($first_column_id)){
                        $child_list_data[$value->format('m/j/Y')] = array_chunk($this->TasksModel->get_tasks_for_calendar($child_list_id, $array_col_ids), 2);
                    }
                }
            }
//            p($child_list_data); exit;
            
            $res .='<div class="my_table my_scroll_table my_calendar_table">';
            $res .= '<div class="caleneder_div_wrapper">';
            $res .= '<div class="container week_view_container">';
            $res .= '<div class="div_btn_name"><div class="btn_box_add"><a class="btn-today">Today</a><a class="btn-day">Day</a><a class="active btn-week">Week</a><a class="btn-month">Month</a></div></div>';
            $res .= '<div class="calender_div_inner_box">';
            $res .= '<div class="div_left_one"><div class="week_calendar"></div></div>';
            $res .= '<div class="div_right_one">';
            $res .= '<div class="week_view_c">';
            $res .= '<div class="container">';
            $res .= '<div class="week_view">';
            $res .= '<div class="head_row_week">';
            $count_date = 0;
            $current_month = date('m', strtotime(key($dates_list_names)));
            foreach ($dates_list_names as $date_key => $date_val):
                if($date_val != '') { $my_list_id = $date_val; } else { $my_list_id = 0; }
                $res .= '<div class="date_Detail_week" data-date="' . $date_key . '" data-listid="' . $my_list_id . '">';
                $res .= '<h2>';
                $res .= '<div class="day_w day_sun">';
                $res .= '</div>';
                $res .= '<div class="day_date">';
                $this_month = date('m', strtotime($date_key));
                if ($count_date == 0) {
                    $res .= date('M', strtotime($date_key)) . ' ';
                } elseif ($this_month > $current_month) {
                    $current_month = $this_month;
                    $res .= date('M', strtotime($date_key)) . ' ';
                }
                $res .= date('d', strtotime($date_key));
                $count_date++;
                $res .= '</div>';
                $res .= '</h2>';
                $res .= '</div>';
            endforeach;
            
            $res .= '</div>';
            $res .= '<div class="body_week">';
            foreach ($child_list_data as $data_key => $data_val):
                if($dates_list_names[$data_key] != '') { $data_sub_list_id =  $dates_list_names[$data_key]; } else { $data_sub_list_id = 0; }
                $res .= '<div class="day_content" data-date="' . $data_key . '" data-listid="' . $data_sub_list_id . '">';
                foreach ($data_val as $key_d => $val_d):
                    $res .= '<div class="data_content_w">';
                    $res .= '<div class="data_name">';
                    $res .= trim($val_d[0]['TaskName']);
                    $res .= '</div>';
                    $res .= '<div class="time_display">';
                    $res .= '<span>';
                    $res .= trim($val_d[1]['TaskName']);
                    $res .= '</span>';
                    $res .= '</div>';
                    $res .= '</div>';
                endforeach;
                $res .= '</div>';
            endforeach;
                
            $res .= '</div>';
            $res .= '</div>';
            $res .= '</div>';
            $res .= '</div>';
            $res .= '</div>';
            $res .= '</div>';
            $res .= '</div>';
            $res .= '</div>';
            $res .= '</div>';
            
            echo $res;
            exit;
            
        }
    }

    /**
     * Add task for a list
     * @author SG
     */
    public function add() {
        if ($this->input->post()) {
            $task_received = $this->input->post('task_data');
            $task_data = array();
            foreach ($task_received as $tsk):
                $task_data[$tsk['column']] = $tsk['val'];
            endforeach;
            
            if (empty($this->input->post('task_data'))) {
                echo 'empty';
                exit;
            }

            $date_add = date('Y-m-d H:i:s');

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }


            $data['Apikey'] = API_KEY;

            if ($this->input->post('list_id') == 0) {

                $send_data['Apikey'] = API_KEY;
                if ($this->input->post('list_name') == '') {
                    $send_data['Listname'] = 'Untitled';
                } else {
                    $send_data['Listname'] = htmlentities($this->security->xss_clean($this->input->post('list_name')));
                }

                $store['name'] = $send_data['Listname'];
                $store['list_type_id'] = 1;
                if (isset($_SESSION['logged_in'])) {
                    $store['user_id'] = $_SESSION['id'];
                } else {
                    $store['user_id'] = 0;
                }
                $store['created'] = $date_add;
                $store['modified'] = $date_add;
                $addList = $this->ListsModel->add_list($store);
                if ($addList == 0) {
                    $error_msg = 'Add list (from add items): ' . implode(',', $store);
                    $myfile = file_put_contents('./assets/logs.txt', $error_msg . PHP_EOL, FILE_APPEND | LOCK_EX);
                    echo 'fail';
                    exit;
                }

                $send_data = json_encode($send_data);

                $ch1 = curl_init();
                curl_setopt($ch1, CURLOPT_URL, API_URL . "account/CreateList");
                curl_setopt($ch1, CURLOPT_POST, 1);
                curl_setopt($ch1, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch1, CURLOPT_POSTFIELDS, $send_data);
                curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
                $server_output1 = curl_exec($ch1);
                $response1 = (array) json_decode($server_output1);
                if (isset($response1['success']) && $response1['success'] == 1) {
                    $list_inflo_id = $response1['data']->ListId;
                    $visited_arr = array();
                    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                        if (isset($_SESSION['auth_visit']) && $_SESSION['auth_visit'] != null) {
                            $visited_arr = $_SESSION['auth_visit'];

                            array_push($visited_arr['list_id'], $addList);
                            array_push($visited_arr['list_name'], $response1['data']->ListName);
                            array_push($visited_arr['list_slug'], $response1['data']->ListSlug);

                            $_SESSION['auth_visit'] = $visited_arr;
                        } else {
                            $visited_arr['list_id'][] = $addList;
                            $visited_arr['list_name'][] = $response1['data']->ListName;
                            $visited_arr['list_slug'][] = $response1['data']->ListSlug;
                            $_SESSION['auth_visit'] = $visited_arr;
                        }
                    } else {
                        if (isset($_SESSION['unauth_visit']) && $_SESSION['unauth_visit'] != null) {
                            $visited_arr = $_SESSION['unauth_visit'];

                            array_push($visited_arr['list_id'], $addList);
                            array_push($visited_arr['list_name'], $response1['data']->ListName);
                            array_push($visited_arr['list_slug'], $response1['data']->ListSlug);

                            $_SESSION['unauth_visit'] = $visited_arr;
                        } else {
                            $visited_arr['list_id'][] = $addList;
                            $visited_arr['list_name'][] = $response1['data']->ListName;
                            $visited_arr['list_slug'][] = $response1['data']->ListSlug;
                            $_SESSION['unauth_visit'] = $visited_arr;
                        }
                    }


                    $_SESSION['last_slug'] = $response1['data']->ListSlug;
                    $data['Listid'] = $addList;
                    $data['list_inflo_id'] = $response1['data']->ListId;

                    $update_list_local['slug'] = $response1['data']->ListSlug;
                    $update_list_local['url'] = '/' . $response1['data']->ListSlug;
                    $update_list_local['list_inflo_id'] = $response1['data']->ListId;
                    $update_list_local['created_user_name'] = $response1['data']->CreateByUserFullName;

//                    echo $addList . '<br>';
//                    p($addList) . '<br>'; exit;

                    $this->ListsModel->update_list_data_from_inflo($addList, $update_list_local);

                    $today = date('Y-m-d H:i:s');
                    $add_first_col['list_inflo_id'] = $response1['data']->ListId;
                    $add_first_col['list_id'] = $addList;
                    $add_first_col['column_name'] = $this->security->xss_clean($response1['data']->ListName);
                    $add_first_col['order'] = 1;
                    $add_first_col['created'] = $today;
                    $add_first_col['modified'] = $today;
                    $add_col_first = $this->TasksModel->add_new_colum($add_first_col);

                    if ($add_col_first > 0) {
                        $list_inflo_id_col = $this->ListsModel->get_list_inflo_id_from_list_id($addList);
                        $api_caol_add['Apikey'] = API_KEY;
                        $api_caol_add['Listid'] = $list_inflo_id_col;
                        $api_caol_add['ListColumnName'] = $add_first_col['column_name'];
                        $post_col_data = json_encode($api_caol_add);
                        $ch_col = curl_init();
                        curl_setopt($ch_col, CURLOPT_URL, API_URL . "account/CreateListColumn");
                        curl_setopt($ch_col, CURLOPT_POST, 1);
                        curl_setopt($ch_col, CURLOPT_HTTPHEADER, $header);
                        curl_setopt($ch_col, CURLOPT_POSTFIELDS, $post_col_data);
                        curl_setopt($ch_col, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch_col, CURLOPT_SSL_VERIFYPEER, false);
                        $server_output_col = curl_exec($ch_col);
                        $response_col = (array) json_decode($server_output_col);
                        //            p($response_col); exit;
                        $col_inflo_id = 0;
                        if (isset($response_col['success']) && $response_col['success'] == 1) {
                            $col_inflo_id = $response_col['data']->ColumnId;
                            $col_data['col_inflo_id'] = $col_inflo_id;
                            $this->TasksModel->update_column_data($addList, $add_col_first, $col_data);
                        }
                    }

                    if (!$this->session->userdata('logged_in')) {
                        if ($this->session->userdata('list_id') != null) {
                            $list_arr = $this->session->userdata('list_id');
                        } else {
                            $list_arr = array();
                        }
                        array_push($list_arr, $addList);
                        $_SESSION['list_id'] = $list_arr;
                    }
                } else {
                    $error_msg = 'Add list (from add items, ' . $addList . '): ' . implode(',', $store);
                    $myfile = file_put_contents('./assets/logs.txt', $error_msg . PHP_EOL, FILE_APPEND | LOCK_EX);
                    echo 'fail';
                    exit;
                }
            } else {
                $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($this->input->post('list_id'));
                $list_owner = $this->ListsModel->getListOwner($this->input->post('list_id'));

                $owner_find = 0;
                if (isset($_SESSION['id'])) {
                    $owner_find = $_SESSION['id'];
                }
                $list_id = $this->input->post('list_id');
                if($this->input->post('list_id') == 0){
                    $list_id = $addList;
                }
                $list = $this->ListsModel->find_list_details_by_id($list_id);
                if($list['is_locked'] == 1 && $list['allow_append_locked'] == 0){
                    if ($list_owner != $owner_find) {
                        $priviladge_get['Apikey'] = API_KEY;
                        $priviladge_get['Listid'] = $list_inflo_id;
                        $priviladge_get['userid'] = 0;
                        if (isset($_SESSION['id'])) {
                            $priviladge_get['userid'] = $_SESSION['id'];
                        }
    //                    p($priviladge_get); exit;

                        $priviladge_get = json_encode($priviladge_get);
                        $ch_priviledge = curl_init();
                        curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
                        curl_setopt($ch_priviledge, CURLOPT_POST, 1);
                        curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
                        curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
                        curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
                        $server_output_priviledge = curl_exec($ch_priviledge);
                        $response_priviledge = (array) json_decode($server_output_priviledge);
    //                    p($response_priviledge); exit;
                        if (isset($response_priviledge['success']) && $response_priviledge['success'] == 0) {
                            echo 'not allowed';
                            exit;
                        } elseif (!isset($response_priviledge['success'])) {
                            echo 'not allowed';
                            exit;
                        }
                    }
                }
                


//                $data['Listid'] = trim($this->input->post('list_id'));
                $data['Listid'] = $this->input->post('list_id');
            }
            $last_order = $this->TasksModel->get_last_order_of_item($data['Listid']);


            if (isset($_SESSION['logged_in'])) {
                $user_id = $_SESSION['id'];
            } else {
                $user_id = 0;
            }

            $item_cnt = 0;
            foreach ($task_data as $tid => $tname):
                $data['Taskname'] = htmlentities(trim($this->input->post('task_name')));
                $send_data_inflo['Apikey'] = API_KEY;
                $send_data_inflo['Taskname'] = $data['Taskname'];
                $send_data_inflo['Listid'] = $list_inflo_id;
                $post_data = json_encode($send_data_inflo);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, API_URL . "account/CreateTask");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $server_output = curl_exec($ch);
                $response = (array) json_decode($server_output);
                if (!$this->session->userdata('logged_in')) {
                    if ($this->session->userdata('task_id') != null) {
                        $task_arr = $this->session->userdata('task_id');
                    } else {
                        $task_arr = array();
                    }
                    if (isset($response['data']) && !empty($response['data'])) {
                        array_push($task_arr, $response['data']->TaskId);
                        $_SESSION['task_id'] = $task_arr;
                    }
                }
                $task_id = 0;
                if (isset($response['success']) && $response['success'] == 1) {
                    $task_id = $response['data']->TaskId;
                } else {
                    $error_msg = 'Add item: (' . $data['Listid'] . ')' . $server_output;
                    $myfile = file_put_contents('./assets/logs.txt', $error_msg . PHP_EOL, FILE_APPEND | LOCK_EX);
                }
                curl_close($ch);

                $add_task[$item_cnt]['user_id'] = $user_id;
                $add_task[$item_cnt]['list_inflo_id'] = $list_inflo_id;
                $add_task[$item_cnt]['list_id'] = $data['Listid'];
                $add_task[$item_cnt]['task_inflo_id'] = $task_id;
                $add_task[$item_cnt]['column_id'] = $tid;
                if ($this->input->post('list_id') == 0) {
                    $add_task[$item_cnt]['column_id'] = $add_col_first;
                }
                $add_task[$item_cnt]['order'] = $last_order + 1;
                $add_task[$item_cnt]['value'] = htmlentities($this->security->xss_clean($tname));
                $add_task[$item_cnt]['created'] = $date_add;
                $add_task[$item_cnt]['modified'] = $date_add;
                $add_task[$item_cnt]['preview_meta'] = '';

                $get_tags_str = htmlspecialchars_decode(htmlspecialchars_decode($tname));
                preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $get_tags_str, $match);
                if (empty($match) || empty($match[0])) {
                    preg_match_all('^([a-zA-Z0-9]+(\.[a-zA-Z0-9]+)+.[a-zA-Z][a-zA-Z]*)$^', $get_tags_str, $match);
                }

                if (!empty($match)) {
                    if (!empty($match[0])) {
                        $match_url = substr($match[0][0], 0, strrpos($match[0][0], ' '));
                        if ($match_url == '') {
                            $match_url = $match[0][0];
                        }
                        if (!preg_match("~^(?:f|ht)tps?://~i", $match_url)) {
                            $match_url = "http://" . $match_url;
                        }
                    }
                }
                $item_cnt++;
            endforeach;

            $insert_ids = array();
            foreach ($add_task as $add_id => $add_data):
                $added = $this->TasksModel->add_single_task($add_data);
                if($added > 0){
                    $col_id = $add_data['column_id'];
                    $column_type = $this->TasksModel->get_column_type($col_id);
                    $update_data['value'] = '';
                    if($column_type['type'] == 'checkbox'){
                        $checked_box_check = '';
                        if($add_data['value'] == 'checked'){
                            $checked_box_check = 'checked';
                        }
                        $update_data['value'] = "<input type='checkbox' name='value_cb_" . $added . "' data-id='" . $added . "' class='my_data_checkbox'" . $checked_box_check . ">";
                    } elseif($column_type['type'] == 'timestamp'){
                        $update_data['value'] = "<a class='btn btn-default timestamp-btn' data-id='" . $added . "'>Timestamp</a>";
                    }
                    if($update_data['value'] != ''){
                        $this->TasksModel->update_task($added,$update_data);
                    }
                }
                array_push($insert_ids, $added);
            endforeach;

//            $task_add = $this->TasksModel->add_task($add_task);
//            p($task_add); exit;
            if (empty($insert_ids)) {
//            if ($task_add == 0) {
                $error_msg = 'Add item: (' . $data['Listid'] . ')' . json_encode($this->input->post());
                $myfile = file_put_contents('./assets/logs.txt', $error_msg . PHP_EOL, FILE_APPEND | LOCK_EX);
                echo 'fail';
                exit;
            }
//            $items_added_str = array();
            $previews = array();
//            foreach ($add_task as $added_items) {
//                $task_id = $this->TasksModel->get_task_id_from_task_inflo_id($added_items['task_inflo_id']);
//                array_push($items_added_str, $task_id);
////                $previews[$task_id] = $added_items['preview_meta'];
//            }

            $task_ids_for_comments = implode(',', $insert_ids);
//             echo $task_ids_for_comments; exit;

            $add_task_present_data = $this->TasksModel->add_attendance_data($data['Listid'], null, $task_ids_for_comments);

            $task_success = array();
//            echo $task_add;
//            array_push($task_success, $insert_ids);
            $max_order = $this->TasksModel->get_last_order_of_item($data['Listid']);
            array_push($task_success, $max_order);
            array_push($task_success, json_encode($insert_ids));
            array_push($task_success, $data['Listid']);
            if (isset($update_list_local)) {
                array_push($task_success, $update_list_local['slug']);
            }
            if ($this->input->post('list_id') == 0) {
                $task_success['col_id'] = $add_col_first;
            }
            $task_success['list_inflo_id'] = $list_inflo_id;
            $task_success['extra_id'] = $add_task_present_data;

            $blank_flag = 0;
            $yes_flag = 1;
            $maybe_flag = 2;
            $no_flag = 3;
            $total_cols = $this->TasksModel->count_col($data['Listid']);
            
            $first_col_id = $this->TasksModel->find_first_column($data['Listid']);
            
            $yes = $this->TasksModel->find_count_present($first_col_id, $yes_flag, $data['Listid']);
            $task_success['total_yes'] = $yes;
            $no = $this->TasksModel->find_count_present($first_col_id, $no_flag, $data['Listid']);
            $task_success['total_no'] = $no;

            $maybe = $this->TasksModel->find_count_present($first_col_id, $maybe_flag, $data['Listid']);
            $task_success['total_maybe'] = $maybe;

            $blank = $this->TasksModel->find_count_present($first_col_id, $blank_flag, $data['Listid']);
            $task_success['total_blank'] = $blank;
            $task_success['previews'] = $previews;
//            $task_success['col_id'] = $add_col_first;
            
            if(!empty($task_success)){
                $data_list['modified'] = date('Y-m-d H:i:s');
                $update_list = $this->ListsModel->update_list_data($data['Listid'], $data_list);
            }

            echo json_encode($task_success);

            if (empty($task_success)) {
                echo 'fail';
            }


//             else {
//                $ret = 'fail';
//            }
//            echo $ret;
            exit;
        }
    }

    /**
     * Get Task Details
     * @author SG
     */
    public function get_task_data() {
        if ($this->input->post()) {

            $task_id = $this->input->post('task_id');
            $task_owner = $this->TasksModel->get_task_owner($task_id);

            $list_id = $this->TasksModel->getListIdByTaskId($task_id);

            $list = $this->ListsModel->find_list_details_by_id($list_id);
            if ($list['is_locked'] == 1) {
                if(!isset($_SESSION['modification_pass']) || (isset($_SESSION['modification_pass']) && !in_array($list_id, $_SESSION['modification_pass']))){
                    if($list['allow_append_locked'] == 1){
                        if (isset($_SESSION['id']) && $_SESSION['id'] != $task_owner['user_id']) {
                            echo 'not allowed';
                        }
                    }else{
                        if (isset($_SESSION['id']) && $_SESSION['id'] != $list['user_id']) {
                            echo 'not allowed';
                            exit;
                        } elseif (!isset($_SESSION['id'])) {
                            echo 'not allowed';
                            exit;
                        }
                    }
                }
                    
            }

            $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($list_id);
//            echo $task_id . ' : ' . $list_inflo_id; exit;


            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $list_owner = $this->ListsModel->getListOwner($list_id);
            $owner_find = 0;
            if (isset($_SESSION['id'])) {
                $owner_find = $_SESSION['id'];
            }
            if($list['is_locked'] == 1 && $list['allow_append_locked'] == 0){
                if ($list_owner != $owner_find) {
                    $priviladge_get['Apikey'] = API_KEY;
                    $priviladge_get['Listid'] = $list_inflo_id;
                    $priviladge_get['userid'] = 0;
                    if (isset($_SESSION['id'])) {
                        $priviladge_get['userid'] = $_SESSION['id'];
                    }
                    $priviladge_get = json_encode($priviladge_get);
                    $ch_priviledge = curl_init();
                    curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
                    curl_setopt($ch_priviledge, CURLOPT_POST, 1);
                    curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
                    curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
                    $server_output_priviledge = curl_exec($ch_priviledge);
                    $response_priviledge = (array) json_decode($server_output_priviledge);
                    //        p($response_priviledge); exit;
                    if (!isset($response_priviledge['success'])) {
                        echo 'unauthorised';
                        exit;
                    }
                    if(!isset($_SESSION['modification_pass']) || (isset($_SESSION['modification_pass']) && !in_array($list_id, $_SESSION['modification_pass']))){
                        if (isset($response_priviledge['success']) && $response_priviledge['success'] == 0) {
                            echo 'unauthorised';
                            exit;
                        }
                    }
                }
            }


            $list_user_id = $this->TasksModel->getListUserId($list_id);

            $task_name = $this->TasksModel->getTaskByTaskId($task_id);
            p(html_entity_decode(htmlspecialchars_decode($task_name)));
            exit;

            echo html_entity_decode($this->security->xss_clean($task_name));
            exit;
        }
    }

    /**
     * Edit task of a list on local database
     * @author SG
     */
    public function update() {
        if ($this->input->post()) {

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }
            $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($this->input->post('ListId'));

            $list_owner = $this->ListsModel->getListOwner($this->input->post('ListId'));

            $owner_find = 0;
            if (isset($_SESSION['id'])) {
                $owner_find = $_SESSION['id'];
            }
            if ($list_owner != $owner_find) {
                $priviladge_get['Apikey'] = API_KEY;
                $priviladge_get['Listid'] = $list_inflo_id;
                $priviladge_get['userid'] = 0;
                if (isset($_SESSION['id'])) {
                    $priviladge_get['userid'] = $_SESSION['id'];
                }
                $priviladge_get = json_encode($priviladge_get);
                $ch_priviledge = curl_init();
                curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
                curl_setopt($ch_priviledge, CURLOPT_POST, 1);
                curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
                curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
                $server_output_priviledge = curl_exec($ch_priviledge);
                $response_priviledge = (array) json_decode($server_output_priviledge);
                if (isset($response_priviledge['success']) && $response_priviledge['success'] == 0) {
                    echo 'not allowed';
                    exit;
                }
            }




            $data_task['value'] = htmlentities(trim($this->security->xss_clean($this->input->post('Taskname'))));
            $name = str_replace(array('http://', 'https://'), array('', ''), $this->input->post('Taskname'));



            $get_tags_str = htmlspecialchars_decode(htmlspecialchars_decode($this->input->post('Taskname')));
            preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $get_tags_str, $match);
            if (empty($match) || empty($match[0])) {
                preg_match_all('^([a-zA-Z0-9]+(\.[a-zA-Z0-9]+)+.*)$^', $get_tags_str, $match);
            }
//            if (!empty($match)) {
//                if (!empty($match[0])) {
//                    $match_url = substr($match[0][0], 0, strrpos($match[0][0], ' '));
//                    if ($match_url == '') {
//                        $match_url = $match[0][0];
//                    }
//                    if (!preg_match("~^(?:f|ht)tps?://~i", $match_url)) {
//                        $match_url = "http://" . $match_url;
//                    }
//                    $googlePagespeedData = file_get_contents("https://www.googleapis.com/pagespeedonline/v2/runPagespeed?url=$match_url&screenshot=true");
//                    $googlePagespeedData = json_decode($googlePagespeedData, true);
//                    $screenshot = $googlePagespeedData['screenshot']['data'];
//                    $screenshot = str_replace(array('_', '-'), array('/', '+'), $screenshot);
//                    $data_task['preview_meta'] = $screenshot;
//                } else {
//                    $data_task['preview_meta'] = '';
//                }
//            } else {
//                $data_task['preview_meta'] = '';
//            }
//                p($data_task); exit;
            $update_local = $this->TasksModel->update_task_data($this->input->post('ListId'), $this->input->post('TaskId'), $data_task);
            if ($update_local) {
                $data_list['modified'] = date('Y-m-d H:i:s');
                $update_list = $this->ListsModel->update_list_data($this->input->post('ListId'), $data_list);
                echo 'success';
//                if(isset($data_task['preview_meta'])){
//                    echo $data_task['preview_meta'];
//                }else{
//                    echo 'success';
//                }
            } else {
                echo 'fail';
            }
            exit;
        }
    }

    /**
     * Edit task of a list
     * @author SG
     */
    public function edit() {
        if ($this->input->post()) {
            if (empty($this->input->post('Taskname'))) {
                echo 'empty';
                exit;
            }

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }
            $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($this->input->post('ListId'));
            $task_inflo_id = $this->TasksModel->get_task_inflo_id_from_task_id($this->input->post('TaskId'));

            $list_owner = $this->ListsModel->getListOwner($this->input->post('ListId'));

            $owner_find = 0;
            if (isset($_SESSION['id'])) {
                $owner_find = $_SESSION['id'];
            }
            if ($list_owner != $owner_find) {
                $priviladge_get['Apikey'] = API_KEY;
                $priviladge_get['Listid'] = $list_inflo_id;
                $priviladge_get['userid'] = 0;
                if (isset($_SESSION['id'])) {
                    $priviladge_get['userid'] = $_SESSION['id'];
                }
                $priviladge_get = json_encode($priviladge_get);
                $ch_priviledge = curl_init();
                curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
                curl_setopt($ch_priviledge, CURLOPT_POST, 1);
                curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
                curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
                $server_output_priviledge = curl_exec($ch_priviledge);
                $response_priviledge = (array) json_decode($server_output_priviledge);
                if ($response_priviledge['success'] == 0) {
                    echo 'not allowed';
                    exit;
                }
            }



            $data['apikey'] = API_KEY;
            $data['Taskname'] = htmlentities(trim($this->input->post('Taskname')));
            $data['Listid'] = $list_inflo_id;
            $data['TaskId'] = $task_inflo_id;
            $post_data = json_encode($data);



            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, API_URL . "account/UpdateTask");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $server_output = curl_exec($ch);
            $response = (array) json_decode($server_output);
//            p($response); exit;
            if (isset($response['success']) && $response['success'] == 1) {
                echo 'success';
            } else {
                echo 'fail';
            }
            exit;
        }
        exit;
    }

    /*
     * Delete item of a list from nexup database
     * @author SG
     */

    public function remove() {
        if (empty($this->input->post('TaskId'))) {
            echo 'empty';
            exit;
        }

        $item_details = $this->TasksModel->get_task_order($this->input->post('TaskId')); //Get order and list id of task



        $header = array('Content-Type: application/json');
        if (isset($_SESSION['xauthtoken'])) {
            $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
            array_push($header, $val);
        }

        $list_owner = $this->ListsModel->getListOwner($this->input->post('ListId'));

        $list = $this->ListsModel->find_list_details_by_id($this->input->post('ListId'));
        
        $task_owner = $this->TasksModel->get_task_owner($this->input->post('TaskId'));
        
        
        if ($list['is_locked'] == 1) {
            if($list['allow_append_locked'] == 1){
                if (isset($_SESSION['id']) && $_SESSION['id'] != $task_owner['user_id']) {
                    echo 'not allowed';
                }
            }else{
                if (isset($_SESSION['id']) && $_SESSION['id'] != $list_owner) {
                    echo 'not allowed';
                    exit;
                } elseif (!isset($_SESSION['id'])) {
                    echo 'not allowed';
                    exit;
                }
            }
        }

        $owner_find = 0;
        if (isset($_SESSION['id'])) {
            $owner_find = $_SESSION['id'];
        }
        
        if($list['is_locked'] == 1 && $list['allow_append_locked'] == 0){
            if ($list_owner != $owner_find) {
                $priviladge_get['Apikey'] = API_KEY;
                $priviladge_get['Listid'] = $item_details['list_inflo_id'];
                $priviladge_get['userid'] = 0;
                if (isset($_SESSION['id'])) {
                    $priviladge_get['userid'] = $_SESSION['id'];
                }
                $priviladge_get = json_encode($priviladge_get);
                $ch_priviledge = curl_init();
                curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
                curl_setopt($ch_priviledge, CURLOPT_POST, 1);
                curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
                curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
                $server_output_priviledge = curl_exec($ch_priviledge);
                $response_priviledge = (array) json_decode($server_output_priviledge);
                if ($response_priviledge['success'] == 0) {
                    echo 'not allowed';
                    exit;
                }
            }
        }



        $found_items = $this->TasksModel->get_similar_tasks($item_details['list_id'], $item_details['order']); //Find tasks of same order from list to delete
        $remove = array_column($found_items, 'id');

//        $fp = fopen($_SERVER["DOCUMENT_ROOT"] . '/assets/inputfile.txt', 'a');
//        fwrite($fp, implode(',', $remove) . PHP_EOL);
//        fclose($fp);
//            
//        if(empty($remove)){
//            $remove[0] = $this->input->post('TaskId');
//        }

        if (!empty($remove)) {
            $remove_local = $this->TasksModel->remove_task_data_bulk($remove);
        } else {
            $remove_local = 1;
        }
        if ($remove_local) {
            if (!empty($remove)) {
                $remove_local = $this->TasksModel->remove_data_nexup($remove);
            } else {
                $remove_local = 1;
            }
//            $remove_local = $this->TasksModel->remove_data_nexup($remove);

            $all_tasks = $this->TasksModel->find_all_tasks_sort_by_order($item_details['list_id']);
//            p($all_tasks); exit;
            if (!empty($all_tasks)) {
                $c_order = $all_tasks[0]['order'];
                $new_order = 1;
                foreach ($all_tasks as $t_id => $t_data):

                    $updt = $this->TasksModel->update_task_orders($item_details['list_id'], $t_data['id'], $new_order);
                    if (isset($all_tasks[$t_id + 1])) {
                        if ($all_tasks[$t_id + 1]['order'] != $c_order) {
                            $new_order = $new_order + 1;
                            $c_order = $all_tasks[$t_id + 1]['order'];
                        }
                    }

                endforeach;
            }


            $blank_flag = 0;
            $yes_flag = 1;
            $maybe_flag = 2;
            $no_flag = 3;
            $total_cols = $this->TasksModel->count_col($item_details['list_id']);
            
            $first_col_id = $this->TasksModel->find_first_column($item_details['list_id']);
            
            $yes = $this->TasksModel->find_count_present($first_col_id, $yes_flag, $item_details['list_id']);
            $task_success['total_yes'] = $yes;
            $no = $this->TasksModel->find_count_present($first_col_id, $no_flag, $item_details['list_id']);
            $task_success['total_no'] = $no;

            $maybe = $this->TasksModel->find_count_present($first_col_id, $maybe_flag, $item_details['list_id']);
            $task_success['total_maybe'] = $maybe;

            $blank = $this->TasksModel->find_count_present($first_col_id, $blank_flag, $item_details['list_id']);
            $task_success['total_blank'] = $blank;

            $task_success['remove'] = implode(',', $remove);

            $last_log = $this->TasksModel->get_last_log($this->input->post('ListId'));
            if (empty($last_log)) {
                $last_log = 'no log';
            }
            $task_success['last_log'] = $last_log;

            $data_list['modified'] = date('Y-m-d H:i:s');
            $update_list = $this->ListsModel->update_list_data($this->input->post('ListId'), $data_list);
            echo json_encode($task_success);
        } else {
            echo 'fail';
        }
        exit;
    }

    /**
     * Delete task of a list
     * @author SG
     */
    public function delete() {
        if ($this->input->post()) {
//            p(explode(',',$this->input->post('TaskId'))); exit;
            if (empty($this->input->post('TaskId'))) {
                echo 'empty';
                exit;
            }

            $item_details = $this->TasksModel->get_task_order($this->input->post('TaskId'));

            $list_owner = $this->ListsModel->getListOwner($this->input->post('ListId'));

            $list = $this->ListsModel->find_list_details_by_id($this->input->post('ListId'));
            $task_owner = $this->TasksModel->get_task_owner($this->input->post('TaskId'));
            if ($list['is_locked'] == 1) {
                if($list['allow_append_locked'] == 1){
                    if (isset($_SESSION['id']) && $_SESSION['id'] != $task_owner['user_id']) {
                        echo 'not allowed';
                        exit;
                    }
                }else{
                    if (isset($_SESSION['id']) && $_SESSION['id'] != $list_owner) {
                        echo 'not allowed';
                        exit;
                    } elseif (!isset($_SESSION['id'])) {
                        echo 'not allowed';
                        exit;
                    }
                }
            }

            $owner_find = 0;
            if (isset($_SESSION['id'])) {
                $owner_find = $_SESSION['id'];
            }
            if($list['is_locked'] == 1 && $list['allow_append_locked'] == 0){
                if ($list_owner != $owner_find) {
                    $header = array('Content-Type: application/json');
                    if (isset($_SESSION['xauthtoken'])) {
                        $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                        array_push($header, $val);
                    }

                    $priviladge_get['Apikey'] = API_KEY;
                    $priviladge_get['Listid'] = $item_details['list_inflo_id'];
                    $priviladge_get['userid'] = 0;
                    if (isset($_SESSION['id'])) {
                        $priviladge_get['userid'] = $_SESSION['id'];
                    }
                    $priviladge_get = json_encode($priviladge_get);
                    $ch_priviledge = curl_init();
                    curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
                    curl_setopt($ch_priviledge, CURLOPT_POST, 1);
                    curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
                    curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
                    $server_output_priviledge = curl_exec($ch_priviledge);
                    $response_priviledge = (array) json_decode($server_output_priviledge);
                    if ($response_priviledge['success'] == 0) {
                        echo 'not allowed';
                        exit;
                    }
                }
            }



            $found_items = $this->TasksModel->get_similar_tasks($item_details['list_id'], $item_details['order']); //Find tasks of same order from list to delete
            $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($item_details['list_id']);
            $remove = array_column($found_items, 'task_inflo_id');

            if (empty($remove)) {
                $remove[0] = $this->input->post('TaskId');
            }

            $datas['Apikey'] = API_KEY;
            $datas['Listid'] = $list_inflo_id;
            $datas['TaskIdList'] = $remove;
            $post_data = json_encode($datas);

            $header = array('Content-Type: application/json');

            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, API_URL . "account/Delete");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $server_output = curl_exec($ch);
            $response = (array) json_decode($server_output);

            if (isset($response['success']) && $response['success'] == 1) {
                echo 'success';
            } else {
                echo 'fail';
            }
        }
    }

    /**
     * Delete task of a list
     * @author SG
     */
    public function complete() {
        if ($this->input->post()) {

            $task_arrs = explode(',', $this->input->post('TaskId'));
            $item_details = $this->TasksModel->get_task_order($task_arrs[0]);

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $list_owner = $this->ListsModel->getListOwner($item_details['list_id']);

            $owner_find = 0;
            if (isset($_SESSION['id'])) {
                $owner_find = $_SESSION['id'];
            }
            if ($list_owner != $owner_find) {
                $priviladge_get['Apikey'] = API_KEY;
                $priviladge_get['Listid'] = $item_details['list_inflo_id'];
                $priviladge_get['userid'] = 0;
                if (isset($_SESSION['id'])) {
                    $priviladge_get['userid'] = $_SESSION['id'];
                }
                $priviladge_get = json_encode($priviladge_get);
                $ch_priviledge = curl_init();
                curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
                curl_setopt($ch_priviledge, CURLOPT_POST, 1);
                curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
                curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
                $server_output_priviledge = curl_exec($ch_priviledge);
                $response_priviledge = (array) json_decode($server_output_priviledge);
                $allowed_access = 0;
                if (isset($response_priviledge['success'])) {
                    if ($response_priviledge['success'] == 1) {
                        $allowed_access = 1;
                    }
                    if (isset($response_priviledge['data']) && ($response_priviledge['data']->IsPublic == TRUE && $response_priviledge['data']->IsLocked == TRUE)) {
                        $allowed_access = 2;
                    }

                    if ($allowed_access == 0) {
                        echo 'not allowed';
                        exit;
                    }
                }
            }



            $found_items = $this->TasksModel->get_similar_tasks($item_details['id'], $item_details['order']);
//            p($found_items); exit;
            $mark_value = array_column($found_items, 'id');
            array_push($mark_value, $this->input->post('TaskId'));
            $mark_value = '(' . implode(',', $mark_value) . ')';
//            echo $mark_value; exit;

            $mark_items = $this->TasksModel->complete_task($mark_value, $this->input->post('TaskId'), $this->input->post('task_status'));

            if ($mark_items > 0) {
                $task_orders = json_decode($this->input->post('tasks_orders'));
                $list_id = $this->input->post('ListId');
                $lists = $this->TasksModel->get_tasks($list_id);
                $update_cnt = 0;
                
                $update_list['modified'] = date('Y-m-d H:i:s');
                $this->ListsModel->update_list_data($this->input->post('ListId'), $update_list);

//                $found_data = array();
//                foreach ($task_orders as $order):
//                    $found_items = $this->TasksModel->get_tasks_by_order($order, $list_id);
////                    p($found_items);
//                    array_push($found_data, $found_items);
//                endforeach;
//                foreach ($found_data as $id => $data_found):
//                    $updt_itm['order'] = $id + 1;
//                    foreach ($data_found as $did => $df):
//                        $this->TasksModel->update_task_data($list_id, $df['id'], $updt_itm);
//                    endforeach;
//                endforeach;

                echo 'success';
            } else {
                echo 'fail';
            }
        }
    }

    /*
     * Change order of task on nexup database
     * @author SG
     */

    public function order_change() {
        if ($this->input->post()) {

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }
            $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($this->input->post('ListId'));

            $priviladge_get['Apikey'] = API_KEY;
            $priviladge_get['Listid'] = $list_inflo_id;
            $priviladge_get['userid'] = 0;
            if (isset($_SESSION['id'])) {
                $priviladge_get['userid'] = $_SESSION['id'];
            }
            $priviladge_get = json_encode($priviladge_get);
            $ch_priviledge = curl_init();
            curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
            curl_setopt($ch_priviledge, CURLOPT_POST, 1);
            curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
            curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
            $server_output_priviledge = curl_exec($ch_priviledge);
            $response_priviledge = (array) json_decode($server_output_priviledge);
            if ($response_priviledge['success'] == 0) {
                echo 'not allowed';
                exit;
            }



            $today_date = date('Y-m-d H:i:s');
            $order_id = $this->input->post('OrderId');
            $task_orders = json_decode($this->input->post('TaskOrders'));
            $list_id = $this->input->post('ListId');
            $lists = $this->TasksModel->get_tasks($list_id);
            $update_cnt = 0;

            $list_locked = $this->TasksModel->get_lock_list_status($list_id);

            $current_order = $this->TasksModel->get_current_item_order($list_id);

            $exist_order = implode(',', array_column($current_order, 'task_inflo_id'));
            $new_order = implode(',', $task_orders);

            $found_data = array();
            foreach ($task_orders as $order):
                $found_items = $this->TasksModel->get_tasks_by_order($order, $list_id);
                array_push($found_data, $found_items);
            endforeach;
            $updt = 0;
            $update_cnt_items = 0;

            foreach ($found_data as $id => $data_found):
                $updt_itm['order'] = $id + 1;
                foreach ($data_found as $did => $df):
                    $updt = $this->TasksModel->update_task_data($list_id, $df['id'], $updt_itm);
                    if($updt){
                        $update_cnt_items++;
                    }
                endforeach;
            endforeach;
            if($update_cnt_items > 0){
                $update_list['modified'] = date('Y-m-d H:i:s');
                $this->ListsModel->update_list_data($list_id, $update_list);
            }

//            echo $updt; exit;

            p($found_data);
            exit;
//            p($new_order);
//            exit;

            foreach ($task_id as $tid => $task):
                $update_order['order'] = $tid + 1;
                $order_update = $this->TasksModel->update_task_data($list_id, $task, $update_order);
                $update_cnt++;
            endforeach;

            $save_history['list_inflo_id'] = $list_id;
            if (isset($_SESSION['logged_in'])) {
                $save_history['user_id'] = $_SESSION['id'];
            }
            $save_history['old_order'] = $exist_order;
            $save_history['new_order'] = $new_order;
            $save_history['nexup_type'] = 2;
            $save_history['created'] = $today_date;
            $save_history['modified'] = $today_date;
            if ($this->input->post('user_ip')) {
                $save_history['user_ip'] = $this->input->post('user_ip');
            }
//            $store_history = $this->TasksModel->save_history($save_history);

            if ($update_cnt > 0) {
                echo 'success';
            } else {
                echo 'fail';
            }
        }
        exit;
    }

    /**
     * Change order of tasks
     * @author SG
     */
    public function order() {
        if ($this->input->post()) {
            $curl_data['apikey'] = API_KEY;
            $curl_data['OrderId'] = $this->input->post('OrderId');
            $task_inflo_id = $this->TasksModel->get_task_inflo_id_from_task_id($this->input->post('Taskid'));
            $curl_data['Taskid'] = $task_inflo_id;
//            p($this->input->post('Taskid')); exit;

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, API_URL . 'account/UpdateTaskOrder');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curl_data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $server_output = curl_exec($ch);
            $response = (array) json_decode($server_output);
//            p($response); exit;
            if (isset($response['success']) && $response['success'] == 1) {
                echo 'success';
            } else {
                echo 'fail';
            }
        }
        exit;
    }

    /**
     * Get List Types
     * @author SG
     */
    public function get_list_types() {
        $data_get['Apikey'] = API_KEY;
        $post_get = json_encode($data_get);
        $header = array('Content-Type: application/json');
        if (isset($_SESSION['xauthtoken'])) {
            $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
            array_push($header, $val);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, API_URL . "account/GetListTypes");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_get);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $server_output = curl_exec($ch);
        $response = (array) json_decode($server_output);
        if (isset($response['success']) && $response['success'] == 1) {
            return json_encode($response['data']->ListTytpe);
        }
    }

    /**
     * Get next item in list
     * @author SG
     */
    public function get_next_task() {
        if ($this->input->post()) {
            $data_send['Apikey'] = API_KEY;
            $data_send['Listid'] = $this->input->post('Listid');
            $data_send['Taskid'] = $this->input->post('Taskid');
            $post_send = json_encode($data_send);
            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, API_URL . "Account/NextTask");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_send);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $server_output = curl_exec($ch);
            $response = (array) json_decode($server_output);
//                p($response); exit;
            if (isset($response['success']) && $response['success'] == 1) {
                $next_arr = (array) $response['data']->NextTask;
                echo $next_arr['TaskName'];
            } else {
                echo 'fail';
            }
            exit;
        }
    }

    /**
     * Update list with next item in list (traverse in round robin list) on nexup database
     * @author SG
     */
    public function next_item() {
//        p($_SESSION); exit;
        if ($this->input->post()) {
            $today_date = date('Y-m-d H:i:s');
            $items = $this->TasksModel->get_tasks($this->input->post('Listid'));

            $last_updated_order = 0;
            $item_size = sizeof($items);
            $current_order_store = array();
            $new_order_store = array();
            array_push($current_order_store, $items[0]['TaskId']);
            $log_user = 'anonymous';
            if (isset($_SESSION['first_name'])) {
                $log_user = $_SESSION['first_name'];
                if (isset($_SESSION['last_name'])) {
                    $log_user .= ' ' . $_SESSION['last_name'];
                }
            }

            for ($i = 1; $i < $item_size; $i++) {
                array_push($current_order_store, $items[$i]['TaskId']);
                $item_id = $items[$i]['TaskId'];
                $new_ord['order'] = $i;
//                $updated = $this->TasksModel->update_task_data($this->input->post('Listid'), $item_id, $new_ord);
                $last_updated_order = $i;
                array_push($new_order_store, $items[$i]['TaskId']);
            }
            array_push($new_order_store, $items[0]['TaskId']);

//            $save_history['list_inflo_id'] = $this->input->post('Listid');
            $save_history['list_id'] = $this->input->post('Listid');
            if (isset($_SESSION['logged_in'])) {
                $save_history['user_id'] = $_SESSION['id'];
            }
            $save_history['user_name'] = $log_user;
            $last_log = $this->TasksModel->get_log_last_details($this->input->post('Listid'));
//            p($last_log); exit;
            if (!empty($last_log)) {
                $item_order = $this->TasksModel->get_item_order($last_log['new_order']);
                $save_history['old_order'] = $last_log['new_order'];
                $next_item = $this->TasksModel->find_next_for_log($item_order['order'], $this->input->post('Listid'));
//                p($next_item); exit;
                if (empty($next_item)) {
                    $next_item['id'] = $items[0]['TaskId'];
                }
                $save_history['new_order'] = $next_item['id'];
            } else {
                $save_history['old_order'] = $items[0]['TaskId'];
                foreach ($items as $it):
                    if ($it['order'] > $items[0]['order']) {
                        $save_history['new_order'] = $it['TaskId'];
                        break;
                    }
                endforeach;
            }

            if ($this->input->post('comment')) {
                $save_history['comment'] = $this->input->post('comment');
            }
            $save_history['nexup_type'] = 1;
            $save_history['created'] = $today_date;
            $save_history['modified'] = $today_date;
            if ($this->input->post('user_ip')) {
                $save_history['user_ip'] = $this->input->post('user_ip');
            }


            $store_history = $this->TasksModel->save_history($save_history);

//            $new_order['order'] = $last_updated_order + 1;
//            $updated_final = $this->TasksModel->update_task_data($this->input->post('Listid'), $this->input->post('Taskid'), $new_order);
            if ($store_history > 0) {
                $update_list['modified'] = date('Y-m-d H:i:s');
                $this->ListsModel->update_list_data($this->input->post('Listid'), $update_list);
                
                $save_history_array = $save_history['new_order'];
                echo $save_history_array;
            } else {
                echo 'fail';
            }
            exit;
        }
    }

    /**
     * Update list with next item in list (traverse in round robin list)
     * @author SG
     */
    public function next_task() {
        if ($this->input->post()) {
            if (!isset($_SESSION['logged_in'])) {
                echo 'not allowed';
                exit;
            }
            $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($this->input->post('Listid'));
            $task_inflo_id = $this->TasksModel->get_task_inflo_id_from_task_id($this->input->post('Taskid'));
            $data_send['Apikey'] = API_KEY;
            $data_send['Listid'] = $list_inflo_id;
            $data_send['Taskid'] = $task_inflo_id;
            $data_send['IsUpdate'] = 1;
            $post_send = json_encode($data_send);
            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, API_URL . "Account/NextTask");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_send);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $server_output = curl_exec($ch);
            $response = (array) json_decode($server_output);
//                p($response); exit;
            if (isset($response['success']) && $response['success'] == 1) {
                echo 'success';
            } else {
                echo 'fail';
            }
            exit;
        }
    }

    /*
     * Function for undo the last traverse
     * @author SG
     */

    public function undo_nexup() {
        if ($this->input->post()) {

            $items = $this->TasksModel->get_tasks($this->input->post('list_id'));
            $item_size = sizeof($items);

            $last_log = $this->TasksModel->find_last_log($this->input->post('list_id'));
//            p($last_log); exit;

            if (empty($last_log)) {
                echo 'fail';
                exit;
            }

            $log_order = $last_log[0]['old_order'];
//            echo $this->db->last_query();

            $new_order_last = $last_log[0]['new_order'];

            $today_date = date('Y-m-d H:i:s');

//            $new_history['list_inflo_id'] = $this->input->post('list_id');
            $new_history['list_id'] = $this->input->post('list_id');
            $new_history['user_id'] = 0;
            if (isset($_SESSION['logged_in'])) {
                $new_history['user_id'] = $_SESSION['id'];
            }
            $log_user = 'anonymous';
            if (isset($_SESSION['first_name'])) {
                $log_user = $_SESSION['first_name'];
                if (isset($_SESSION['last_name'])) {
                    $log_user .= ' ' . $_SESSION['last_name'];
                }
            }
            $new_history['user_name'] = $log_user;
            $new_history['old_order'] = $new_order_last;
            $new_history['new_order'] = $log_order;
            $new_history['user_ip'] = $_SERVER['REMOTE_ADDR'];
            $new_history['is_undo'] = 1;
            $new_history['is_undone'] = 0;
            $new_history['created'] = $today_date;
            $new_history['modified'] = $today_date;

            $this->TasksModel->save_history($new_history);

            $log_update['is_undone'] = 1;
            $log_update['modified'] = date('Y-m-d H:i:s');
            $this->TasksModel->update_log($last_log[0]['id'], $log_update);
//            echo $new_order_last; exit;

            $deleted_item = $this->TasksModel->get_task_details($new_order_last, $this->input->post('list_id'));
            if ($deleted_item == 1) {
                $order_item = $this->TasksModel->get_item_order($new_order_last);
                $log_data = $this->TasksModel->get_similar_item($order_item['order'], $this->input->post('list_id'));

                $log_order = $log_data['task_inflo_id'];
            }
            
            $update_list['modified'] = date('Y-m-d H:i:s');
            $this->ListsModel->update_list_data($this->input->post('list_id'), $update_list);

            echo $log_order;
            exit;
        }
    }

    /*
     * function to add column for a list
     * @author SG
     */

    public function add_column() {
        if ($this->input->post()) {
            $list_details = $this->ListsModel->find_list_details_by_id($this->input->post('list_id'));
            if (trim($this->input->post('col_name')) == '') {
                echo 'empty';
                exit;
            }
            if (empty($this->input->post('col_name'))) {
                echo 'empty';
                exit;
            }
            $first_column_id = $this->TasksModel->find_first_column($this->input->post('list_id'));
            $cells_cnt = $this->TasksModel->find_item_count_in_col($this->input->post('list_id'), $first_column_id);
            $yes_itms = $this->TasksModel->get_yes_orders($this->input->post('list_id'));
            $yes_itms = array_column($yes_itms, 'order');

            $maybe_itms = $this->TasksModel->get_maybe_orders($this->input->post('list_id'));
            $maybe_itms = array_column($maybe_itms, 'order');

            $no_itms = $this->TasksModel->get_no_orders($this->input->post('list_id'));
            $no_itms = array_column($no_itms, 'order');

            $empty_itms = $this->TasksModel->get_blank_orders($this->input->post('list_id'));
            $complete_itms = $this->TasksModel->get_completed_orders($this->input->post('list_id'));
            $complete_itms = array_column($complete_itms, 'order');


            $list_owner = $this->ListsModel->getListOwner($this->input->post('list_id'));

            $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($this->input->post('list_id'));

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $allowed_access = 1;

            $owner_find = 0;
            if (isset($_SESSION['id'])) {
                $owner_find = $_SESSION['id'];
            }
            if ($list_owner != $owner_find) {
                $priviladge_get['Apikey'] = API_KEY;
                $priviladge_get['Listid'] = $list_inflo_id;
                $priviladge_get['userid'] = 0;
                if (isset($_SESSION['id'])) {
                    $priviladge_get['userid'] = $_SESSION['id'];
                }
                $priviladge_get = json_encode($priviladge_get);

                $ch_priviledge = curl_init();
                curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
                curl_setopt($ch_priviledge, CURLOPT_POST, 1);
                curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
                curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
                $server_output_priviledge = curl_exec($ch_priviledge);
                $response_priviledge = (array) json_decode($server_output_priviledge);
//            p($response_priviledge); exit;

                if (isset($response_priviledge['success'])) {
                    if ($response_priviledge['success'] == 1) {
                        $allowed_access = 1;
                    } else {
                        $allowed_access = 0;
                        if (isset($response_priviledge['data'])) {
                            if ($response_priviledge['data']->IsPublic == 'True') {
                                if ($response_priviledge['data']->IsLocked == 'True') {
                                    $allowed_access = 2;
                                }
                            }
                        }
                    }
                }
                if ($allowed_access != 1) {
                    echo 'not_allowed';
                    exit;
                }
            }


            $completed_items = array();
            if ($this->input->post('completed_items') != '') {
                $completed_items = explode(',', $this->input->post('completed_items'));
            }

            $yes_items = array();
            if ($this->input->post('yes_items') != '') {
                $yes_items = explode(',', $this->input->post('yes_items'));
            }
            $no_items = array();
            if ($this->input->post('no_items') != '') {
                $no_items = explode(',', $this->input->post('no_items'));
            }
            $maybe_items = array();
            if ($this->input->post('maybe_items') != '') {
                $maybe_items = explode(',', $this->input->post('maybe_items'));
            }



            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($this->input->post('list_id'));

            $api_caol_add['Apikey'] = API_KEY;
            $api_caol_add['Listid'] = $list_inflo_id;
            $api_caol_add['ListColumnName'] = htmlentities(trim($this->input->post('col_name')));
            $post_col_data = json_encode($api_caol_add);
            $ch_col = curl_init();
            curl_setopt($ch_col, CURLOPT_URL, API_URL . "account/CreateListColumn");
            curl_setopt($ch_col, CURLOPT_POST, 1);
            curl_setopt($ch_col, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch_col, CURLOPT_POSTFIELDS, $post_col_data);
            curl_setopt($ch_col, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_col, CURLOPT_SSL_VERIFYPEER, false);
            $server_output_col = curl_exec($ch_col);
            $response_col = (array) json_decode($server_output_col);
//            p($response_col); exit;
            $col_inflo_id = 0;
            if (isset($response_col['success']) && $response_col['success'] == 1) {
                $col_inflo_id = $response_col['data']->ColumnId;
            }



            $resp = array();
            $today = date('Y-m-d H:i:s');
            $new_col['list_inflo_id'] = $list_inflo_id;
            $new_col['list_id'] = $this->input->post('list_id');
            $new_col['column_name'] = htmlentities(trim($this->security->xss_clean($this->input->post('col_name'))));
            $new_col['col_inflo_id'] = $col_inflo_id;
//            $cells_cnt = $this->input->post('cells_cnt');
            $col_order = $this->TasksModel->FindColumnMaxOrder($this->input->post('list_id'));
//            echo $col_order; exit;
            $add_col_first = 0;

            if ($col_order == 0) {
                $current_list_name = $this->ListsModel->find_list_name_by_id($this->input->post('list_id'));
                $col_order = $col_order + 1;
                $add_first_col['list_inflo_id'] = $list_inflo_id;
                $add_first_col['list_id'] = $this->input->post('list_id');
                $add_first_col['column_name'] = $current_list_name;
                $add_first_col['order'] = $col_order;
                $add_first_col['created'] = $today;
                $add_first_col['modified'] = $today;
                $add_col_first = $this->TasksModel->add_new_colum($add_first_col);
                $col_order_add = $this->TasksModel->UpdateColumnOrder($this->input->post('list_id'), $add_col_first);
                $first_col_resp = '<th class="heading_items_col" data-listid="' . $this->input->post('list_id') . '"  data-colid="' . $add_col_first . '">';
                $first_col_resp .= '<div class="add-data-title-r">';
                $first_col_resp .= '<a href="" class="icon-more-h move_col ui-sortable-handle" id="dropdownMenu0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="visibility: hidden;"></a>';
                if($this->input->post('type_column_list') && $this->input->post('type_column_list') == 'child'){
                    $first_col_resp .= '<a class="remove_sub_col custom_cursor" data-colid="' . $add_col_first . '" data-listid="' . $this->input->post('list_id') . '">Remove</a>';
                }else{
                    $first_col_resp .= '<a class="remove_col custom_cursor" data-colid="' . $add_col_first . '" data-listid="' . $this->input->post('list_id') . '">Remove</a>';
                }
                $first_col_resp .= '</div>';
                $first_col_resp .= '<div class="add-data-title" data-colid="' . $add_col_first . '" data-listid="' . $this->input->post('list_id') . '" data-toggle="tooltip" data-placement="bottom" title="' . $add_first_col['column_name'] . '">';
                $first_col_resp .= '<span class="column_name_class" id="col_name_' . $add_col_first . '">' . $current_list_name . '</span>';
                
                $first_col_resp .= '</div>';
                
                $first_col_resp .= '<a href="" class="icon-more-o icon_listing_table"></a>';
                $first_col_resp .= '<div class="div_option_wrap">';
                $first_col_resp .= '<ul class="ul_table_option" data-listid="' . $this->input->post('list_id') . '">';
                $first_col_resp .= '<li><div class="custom_radio_class"><input type="radio" id="text_'. $add_col_first . '" name="radio-group-' . $add_col_first . '" value="text" data-col_id="' . $add_col_first . '"  checked><label for="text_' . $add_col_first . '">Text</label></div></li>';
                $first_col_resp .= '<li><div class="custom_radio_class"><input type="radio" id="memo_' . $add_col_first . '" name="radio-group-' . $add_col_first . '" value="memo" data-col_id="' . $add_col_first . '"><label for="memo_' . $add_col_first . '">Memo</label></div><div class="plus_minus_wrap"><span>Height</span><a class="minus_a">-</a><input id="number_rows" type="number" min="1" value="1"><a class="plus_a">+</a></div></li>';
                $first_col_resp .= '<li><div class="custom_radio_class"><input type="radio" id="checkbox_' . $add_col_first . '" name="radio-group-' . $add_col_first . '" value="checkbox" data-col_id="'. $add_col_first . '"><label for="checkbox_' . $add_col_first . '">Check Box</label></div></li>';
                $first_col_resp .= '<li><div class="custom_radio_class"><input type="radio" id="number_' . $add_col_first . '" name="radio-group-' . $add_col_first . '" value="number" data-col_id="' . $add_col_first . '"><label for="number_' . $add_col_first . '">Number</label></div></li>';
                $first_col_resp .= '<li><div class="custom_radio_class"><input type="radio" id="currency_' . $add_col_first . '" name="radio-group-' . $add_col_first . '" value="currency" data-col_id="' . $add_col_first . '"><label for="currency_' . $add_col_first . '">Dollar</label></div></li>';
                $first_col_resp .= '<li><div class="custom_radio_class"><input type="radio" id="datetime_' . $add_col_first . '" name="radio-group-' . $add_col_first . '" value="datetime" data-col_id="' . $add_col_first . '"><label for="datetime_' . $add_col_first . '">Date Time</label></div></li>';
                $first_col_resp .= '<li><div class="custom_radio_class"><input type="radio" id="date_' . $add_col_first . '" name="radio-group-' . $add_col_first . '" value="date" data-col_id="' . $add_col_first . '"><label for="date_' . $add_col_first . '">Date</label></div></li>';
                $first_col_resp .= '<li><div class="custom_radio_class"><input type="radio" id="time_' . $add_col_first . '" name="radio-group-' . $add_col_first . '" value="time" data-col_id="' . $add_col_first . '"><label for="time_' . $add_col_first . '">Time</label></div></li>';
                $first_col_resp .= '<li><div class="custom_radio_class"><input type="radio" id="timestamp_' . $add_col_first . '" name="radio-group-' . $add_col_first . '" value="timestamp" data-col_id="' . $add_col_first . '"><label for="timestamp_' . $add_col_first . '">Time Stamp</label></div></li>';
                $first_col_resp .= '<li><div class="custom_radio_class"><input type="radio" id="inflo_ob_' . $add_col_first . '" name="radio-group-' . $add_col_first . '" value="infloobject" data-col_id="' . $add_col_first . '" disabled="disabled"><label for="inflo_ob_' . $add_col_first . '" style="font-style: italic;">Inflo Object</label></div></li>';
                $first_col_resp .= '</ul>';
                $first_col_resp .= '</div>';
                $first_col_resp .= '</th>';

                $resp['first_col'] = $first_col_resp;
            } elseif ($col_order == 1) {
                $columns = $this->TasksModel->getColumns($this->input->post('list_id'));
                $first_col_resp = '<th class="heading_items_col" data-listid="' . $this->input->post('list_id') . '"  data-colid="' . $columns[0]['id'] . '">';
                $first_col_resp .= '<div class="add-data-title-r">';
                $first_col_resp .= '<a href="" class="icon-more-h move_col ui-sortable-handle" id="dropdownMenu0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="visibility: hidden;"></a>';
                
                if($this->input->post('type_column_list') && $this->input->post('type_column_list') == 'child'){
                    $first_col_resp .= '<a class="remove_sub_col custom_cursor icon-cross-out" data-colid="' . $columns[0]['id'] . '" data-listid="' . $this->input->post('list_id') . '" style="visibility: hidden;"></a>';
                }else{
                    $first_col_resp .= '<a class="remove_col custom_cursor icon-cross-out" data-colid="' . $columns[0]['id'] . '" data-listid="' . $this->input->post('list_id') . '" style="visibility: hidden;"></a>';
                }
                $first_col_resp .= '</div>';
                $first_col_resp .= '<div class="add-data-title" data-colid="' . $columns[0]['id'] . '" data-listid="' . $this->input->post('list_id') . '" data-toggle="tooltip" data-placement="bottom" title="' . $columns[0]['column_name'] . '">';
                $first_col_resp .= '<span class="column_name_class" id="col_name_' . $columns[0]['id'] . '">' . $columns[0]['column_name'] . '</span>';

                $first_col_resp .= '</div>';
                $first_col_resp .= '<a href="" class="icon-more-o icon_listing_table"></a>';
                $first_col_resp .= '<div class="div_option_wrap">';
                $first_col_resp .= '<ul class="ul_table_option" data-listid="' . $this->input->post('list_id') . '">';
                $first_col_resp .= '<li><div class="custom_radio_class"><input type="radio" id="text_'. $columns[0]['id'] . '" name="radio-group-' . $columns[0]['id'] . '" value="text" data-col_id="' . $columns[0]['id'] . '"  checked><label for="text_' . $columns[0]['id'] . '">Text</label></div></li>';
                $first_col_resp .= '<li><div class="custom_radio_class"><input type="radio" id="memo_' . $columns[0]['id'] . '" name="radio-group-' . $columns[0]['id'] . '" value="memo" data-col_id="' . $columns[0]['id'] . '"><label for="memo_' . $columns[0]['id'] . '">Memo</label></div><div class="plus_minus_wrap"><span>Height</span><a class="minus_a">-</a><input id="number_rows" type="number" min="1" value="3"><a class="plus_a">+</a></div></li>';
                $first_col_resp .= '<li><div class="custom_radio_class"><input type="radio" id="checkbox_' . $columns[0]['id'] . '" name="radio-group-' . $columns[0]['id'] . '" value="checkbox" data-col_id="'. $columns[0]['id'] . '"><label for="checkbox_' . $columns[0]['id'] . '">Check Box</label></div></li>';
                $first_col_resp .= '<li><div class="custom_radio_class"><input type="radio" id="number_' . $columns[0]['id'] . '" name="radio-group-' . $columns[0]['id'] . '" value="number" data-col_id="' . $columns[0]['id'] . '"><label for="number_' . $columns[0]['id'] . '">Number</label></div></li>';
                $first_col_resp .= '<li><div class="custom_radio_class"><input type="radio" id="currency_' . $columns[0]['id'] . '" name="radio-group-' . $columns[0]['id'] . '" value="currency" data-col_id="' . $columns[0]['id'] . '"><label for="currency_' . $columns[0]['id'] . '">Dollar</label></div></li>';
                $first_col_resp .= '<li><div class="custom_radio_class"><input type="radio" id="datetime_' . $columns[0]['id'] . '" name="radio-group-' . $columns[0]['id'] . '" value="datetime" data-col_id="' . $columns[0]['id'] . '"><label for="datetime_' . $columns[0]['id'] . '">Date Time</label></div></li>';
                $first_col_resp .= '<li><div class="custom_radio_class"><input type="radio" id="date_' . $columns[0]['id'] . '" name="radio-group-' . $columns[0]['id'] . '" value="date" data-col_id="' . $columns[0]['id'] . '"><label for="date_' . $columns[0]['id'] . '">Date</label></div></li>';
                $first_col_resp .= '<li><div class="custom_radio_class"><input type="radio" id="time_' . $columns[0]['id'] . '" name="radio-group-' . $columns[0]['id'] . '" value="time" data-col_id="' . $columns[0]['id'] . '"><label for="time_' . $columns[0]['id'] . '">Time</label></div></li>';
                $first_col_resp .= '<li><div class="custom_radio_class"><input type="radio" id="timestamp_' . $columns[0]['id'] . '" name="radio-group-' . $columns[0]['id'] . '" value="timestamp" data-col_id="' . $columns[0]['id'] . '"><label for="timestamp_' . $columns[0]['id'] . '">Time Stamp</label></div></li>';
                $first_col_resp .= '<li><div class="custom_radio_class"><input type="radio" id="inflo_ob_' . $columns[0]['id'] . '" name="radio-group-' . $columns[0]['id'] . '" value="infloobject" data-col_id="' . $columns[0]['id'] . '" disabled="disabled"><label for="inflo_ob_' . $columns[0]['id'] . '" style="font-style: italic;">Inflo Object</label></div></li>';
                $first_col_resp .= '</ul>';
                $first_col_resp .= '</div>';
                $first_col_resp .= '</th>';
                $resp['first_col'] = $first_col_resp;
            }

            $new_col['order'] = $col_order + 1;
            $new_col['created'] = $today;
            $new_col['modified'] = $today;
            $add_col = $this->TasksModel->add_new_colum($new_col);
            if ($add_col > 0) {
                $update_list['modified'] = date('Y-m-d H:i:s');
                $this->ListsModel->update_list_data($this->input->post('list_id'), $update_list);
                $col_res_id = $col_order + 1;
                $resp_str = '<th class="heading_items_col" data-listid="' . $this->input->post('list_id') . '"  data-colid="' . $add_col . '">';
                $resp_str .= '<div class="add-data-title-r">';
                if($list_details['parent_id'] == 0){
                    $move_class = 'move_col';
                }else{
                    $move_class = 'move_sub_col';
                }
                $resp_str .= '<a href="" class="icon-more-h ' . $move_class . ' ui-sortable-handle" id="dropdownMenu0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="visibility: hidden;"></a>';
                
                if($this->input->post('type_column_list') && $this->input->post('type_column_list') == 'child'){
                    $resp_str .= '<a class="remove_sub_col custom_cursor icon-cross-out" data-colid="' . $add_col . '" data-listid="' . $this->input->post('list_id') . '" style="visibility: hidden;"></a>';
                }else{
                    $resp_str .= '<a class="remove_col custom_cursor icon-cross-out" data-colid="' . $add_col . '" data-listid="' . $this->input->post('list_id') . '" style="visibility: hidden;"></a>';
                }
                $resp_str .= '</div>';
                $resp_str .= '<div class="add-data-title" data-colid="' . $add_col . '" data-listid="' . $this->input->post('list_id') . '" data-toggle="tooltip" data-placement="bottom" title="' . $new_col['column_name'] . '">';
                $resp_str .= '<span class="column_name_class" id="col_name_' . $add_col . '">' . $new_col['column_name'] . '</span>';

                $resp_str .= '</div>';
                
                $resp_str .= '<a href="" class="icon-more-o icon_listing_table"></a>';
                $resp_str .= '<div class="div_option_wrap">';
                $resp_str .= '<ul class="ul_table_option" data-listid="' . $this->input->post('list_id') . '">';
                $resp_str .= '<li><div class="custom_radio_class"><input type="radio" id="text_'. $add_col . '" name="radio-group-' . $add_col . '" value="text" data-col_id="' . $add_col . '"  checked><label for="text_' . $add_col . '">Text</label></div></li>';
                $resp_str .= '<li><div class="custom_radio_class"><input type="radio" id="memo_' . $add_col . '" name="radio-group-' . $add_col . '" value="memo" data-col_id="' . $add_col . '"><label for="memo_' . $add_col . '">Memo</label></div><div class="plus_minus_wrap"><span>Height</span><a class="minus_a">-</a><input id="number_rows" type="number" min="1" value="3"><a class="plus_a">+</a></div></li>';
                $resp_str .= '<li><div class="custom_radio_class"><input type="radio" id="checkbox_' . $add_col . '" name="radio-group-' . $add_col . '" value="checkbox" data-col_id="'. $add_col . '"><label for="checkbox_' . $add_col . '">Check Box</label></div></li>';
                $resp_str .= '<li><div class="custom_radio_class"><input type="radio" id="number_' . $add_col . '" name="radio-group-' . $add_col . '" value="number" data-col_id="' . $add_col . '"><label for="number_' . $add_col . '">Number</label></div></li>';
                $resp_str .= '<li><div class="custom_radio_class"><input type="radio" id="currency_' . $add_col . '" name="radio-group-' . $add_col . '" value="currency" data-col_id="' . $add_col . '"><label for="currency_' . $add_col . '">Dollar</label></div></li>';
                $resp_str .= '<li><div class="custom_radio_class"><input type="radio" id="datetime_' . $add_col . '" name="radio-group-' . $add_col . '" value="datetime" data-col_id="' . $add_col . '"><label for="datetime_' . $add_col . '">Date Time</label></div></li>';
                $resp_str .= '<li><div class="custom_radio_class"><input type="radio" id="date_' . $add_col . '" name="radio-group-' . $add_col . '" value="date" data-col_id="' . $add_col . '"><label for="date_' . $add_col . '">Date</label></div></li>';
                $resp_str .= '<li><div class="custom_radio_class"><input type="radio" id="time_' . $add_col . '" name="radio-group-' . $add_col . '" value="time" data-col_id="' . $add_col . '"><label for="time_' . $add_col . '">Time</label></div></li>';
                $resp_str .= '<li><div class="custom_radio_class"><input type="radio" id="timestamp_' . $add_col . '" name="radio-group-' . $add_col . '" value="timestamp" data-col_id="' . $add_col . '"><label for="timestamp_' . $add_col . '">Time Stamp</label></div></li>';
                $resp_str .= '<li><div class="custom_radio_class"><input type="radio" id="inflo_ob_' . $add_col . '" name="radio-group-' . $add_col . '" value="infloobject" data-col_id="' . $add_col . '" disabled="disabled"><label for="inflo_ob_' . $add_col . '" style="font-style: italic;">Inflo Object</label></div></li>';
                $resp_str .= '</ul>';
                $resp_str .= '</div>';
                
                $resp_str .= '</th>';

                $resp['new_col'] = $resp_str;

                $resp_input = '<th class="heading_items_col_add" data-listid="' . $this->input->post('list_id') . '" data-colid="' . $add_col . '">';
                $resp_input .= '<div class="add-data-input">';
                if($this->input->post('type_column_list') && $this->input->post('type_column_list') == 'child'){
                    $resp_input .= '<input type="text" name="task_name" id="task_name" class="task_sub_name" data-listid="' . $this->input->post('list_id') . '" data-colid="' . $add_col . '" placeholder="Add ' . $new_col['column_name'] . '">';
                }else{
                    $resp_input .= '<input type="text" name="task_name" id="task_name" class="task_name" data-listid="' . $this->input->post('list_id') . '" data-colid="' . $add_col . '" placeholder="Add ' . $new_col['column_name'] . '">';
                }
                $resp_input .= '</div>';
                $resp_input .= '</th>';

                $resp['new_col_input'] = $resp_input;

                $date_add = date('Y-m-d H:i:s');


//                $header = array('Content-Type: application/json');
//                if (isset($_SESSION['xauthtoken'])) {
//                    $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
//                    array_push($header, $val);
//                }

                $add_task_data['Apikey'] = API_KEY;
                $add_task_data['Listid'] = $list_inflo_id;
                $resp_td_str = array();
                for ($i = 0; $i < $cells_cnt; $i++) {

                    $add_task_data['Taskname'] = '';
                    $post_data = json_encode($add_task_data);

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, API_URL . "account/CreateTask");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $server_output = curl_exec($ch);
                    $response = (array) json_decode($server_output);
                    if (!$this->session->userdata('logged_in')) {
                        if ($this->session->userdata('task_id') != null) {
                            $task_arr = $this->session->userdata('task_id');
                        } else {
                            $task_arr = array();
                        }
                    }
                    $task_id = 0;
                    if (isset($response['success']) && $response['success'] == 1) {
                        $task_id = $response['data']->TaskId;
                        if (!$this->session->userdata('logged_in')) {
                            array_push($task_arr, $response['data']->TaskId);
                            $_SESSION['task_id'] = $task_arr;
                        }
                    }
                    curl_close($ch);
                    $user_id = NULL;
                    if (isset($_SESSION['id'])) {
                        $user_id = $_SESSION['id'];
                    }
                    $add_task['user_id'] = $user_id;
                    $add_task['list_id'] = $this->input->post('list_id');
                    $add_task['list_inflo_id'] = $list_inflo_id;
                    $add_task['task_inflo_id'] = $task_id;
                    $add_task['column_id'] = $add_col;
                    $add_task['order'] = $i + 1;
                    if (!empty($complete_itms)) {
                        if (in_array($add_task['order'], $complete_itms)) {
                            $add_task['is_completed'] = 1;
//                            p('Here' . $i);
                        } else {
                            $add_task['is_completed'] = 0;
                        }
//                        p(in_array(($i + 1), $completed_items) . ' : ' . ($i+1)) . '<br>';
                    }
                    $add_task['is_present'] = 0;
                    if (!empty($yes_itms)) {
                        if (in_array($add_task['order'], $yes_itms)) {
                            $add_task['is_present'] = 1;
                        }
                    }
                    if (!empty($maybe_itms)) {
                        if (in_array($add_task['order'], $maybe_itms)) {
                            $add_task['is_present'] = 2;
                        }
                    }
                    if (!empty($no_itms)) {
                        if (in_array($add_task['order'], $no_itms)) {
                            $add_task['is_present'] = 3;
                        }
                    }
                    $add_task['value'] = '';
                    $add_task['created'] = $date_add;
                    $add_task['modified'] = $date_add;
                    $task_add = $this->TasksModel->add_single_task($add_task);
//                    p($add_task);

                    $similar_tasks = $this->TasksModel->get_similar_tasks($this->input->post('list_id'), $add_task['order']);
//                    p($similar_tasks);
                    if (!empty($similar_tasks)) {
                        $find_task = $similar_tasks[0]['id'];
                        $find_data_extra = $this->TasksModel->get_data_extra($this->input->post('list_id'), $find_task);
                        if (!empty($find_data_extra)) {
                            $updt_data['item_ids'] = $find_data_extra[0]['item_ids'] . ',' . $task_add;
                            $updated_data = $this->TasksModel->update_extra($this->input->post('list_id'), $find_data_extra[0]['id'], $updt_data);
                        }
                    }

                    $resp_td_str[$i] = '<td class="list-table-view">';
                    $resp_td_str[$i] .= '<div class="add-data-div edit_task" data-id="' . $task_add . '" data-task="" data-listid="' . $this->input->post('list_id') . '">';
                    $resp_td_str[$i] .= '<span id="span_task_' . $task_add . '" class="task_name_span"></span>';
                    $resp_td_str[$i] .= '</div>';
                    $resp_td_str[$i] .= '</td>';
                }

//                exit;
                $resp['new_col_data'] = $resp_td_str;
                $resp['last_log'] = 0;

                $last_log = $this->TasksModel->get_last_log($this->input->post('list_id'));
                if (!empty($last_log)) {
                    $resp['last_log'] = $last_log;
                }

                echo json_encode($resp);
            } else {
                echo 'fail';
            }
            exit;
        }
    }

    /*
     * Get column name from column id
     * @author SG
     */

    public function get_column_name() {
        if ($this->input->post()) {

            $list_id = 0;
            if (isset($_POST['list_id'])) {
                $list_id = $this->input->post('list_id');
            }
            $col_id = 0;
            if (isset($_POST['column_id'])) {
                $col_id = $this->input->post('column_id');
            }

            $list = $this->ListsModel->find_list_details_by_id($list_id);
            if ($list['is_locked'] == 1) {
                if(!isset($_SESSION['modification_pass']) || (isset($_SESSION['modification_pass']) && !in_array($list_id, $_SESSION['modification_pass']))){
                    if (isset($_SESSION['id']) && $_SESSION['id'] != $list['user_id']) {
                        echo 'unauthorised';
                        exit;
                    } elseif (!isset($_SESSION['id'])) {
                        echo 'unauthorised';
                        exit;
                    }
                }
            }

//            if (!isset($_SESSION['id']) || $_SESSION['id'] < 1) {
//                echo 'empty';
//                exit;
//            } else {
            $list_user_id = $this->TasksModel->getListUserId($list_id);
            $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($list_id);


            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $list_owner = $this->ListsModel->getListOwner($this->input->post('list_id'));

            $owner_find = 0;
            if (isset($_SESSION['id'])) {
                $owner_find = $_SESSION['id'];
            }

            if ($list_owner != $owner_find) {
                $priviladge_get['Apikey'] = API_KEY;
                $priviladge_get['Listid'] = $list_inflo_id;
                $priviladge_get['userid'] = 0;
                if (isset($_SESSION['id'])) {
                    $priviladge_get['userid'] = $_SESSION['id'];
                }
                $priviladge_get = json_encode($priviladge_get);

                $ch_priviledge = curl_init();
                curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
                curl_setopt($ch_priviledge, CURLOPT_POST, 1);
                curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
                curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
                $server_output_priviledge = curl_exec($ch_priviledge);
                $response_priviledge = (array) json_decode($server_output_priviledge);
                $allowed_access = 0;
                if (isset($response_priviledge['success']) && $response_priviledge['success'] == 1) {
                    $allowed_access = 1;
                }elseif(isset($_SESSION['modification_pass']) && in_array($list_id, $_SESSION['modification_pass'])){
                    $allowed_access = 1;
                }

                if ($allowed_access == 0) {
                    echo 'unauthorised';
                    exit;
                }
            }
            $column_name = $this->TasksModel->getColumnNameById($list_id, $col_id);
            echo htmlspecialchars_decode(htmlspecialchars_decode($this->security->xss_clean($column_name)));
        }
        exit;
    }

    /*
     * Update the name of columns
     * @author SG
     */

    public function update_column_name() {
        if ($this->input->post()) {
            $col_name = $this->security->xss_clean($this->input->post('column_name'));
            $col_id = $this->input->post('column_id');
            $list_id = $this->input->post('list_id');
            $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($list_id);

            $list = $this->ListsModel->find_list_details_by_id($list_id);
            if ($list['is_locked'] == 1) {
                if(!isset($_SESSION['modification_pass']) || (isset($_SESSION['modification_pass']) && !in_array($list_id, $_SESSION['modification_pass']))){
                    if (isset($_SESSION['id']) && $_SESSION['id'] != $list['user_id']) {
                        echo 'unauthorised';
                        exit;
                    } elseif (!isset($_SESSION['id'])) {
                        echo 'unauthorised';
                        exit;
                    }
                }
            }

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $priviladge_get['Apikey'] = API_KEY;
            $priviladge_get['Listid'] = $list_inflo_id;
            $priviladge_get['userid'] = 0;
            if (isset($_SESSION['id'])) {
                $priviladge_get['userid'] = $_SESSION['id'];
            }
            $priviladge_get = json_encode($priviladge_get);

            $ch_priviledge = curl_init();
            curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
            curl_setopt($ch_priviledge, CURLOPT_POST, 1);
            curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
            curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
            $server_output_priviledge = curl_exec($ch_priviledge);
            $response_priviledge = (array) json_decode($server_output_priviledge);
            $allowed_access = 0;
            if ($response_priviledge['success'] == 1) {
                $allowed_access = 1;
            }elseif(isset($_SESSION['modification_pass']) && in_array($list_id, $_SESSION['modification_pass'])){
                $allowed_access = 1;
            }

            if ($allowed_access == 0) {
                echo 'unauthorised';
                exit;
            }



            if ($col_name == '') {
                echo 'empty';
                exit;
            }

            $col_inflo_id = $this->TasksModel->get_col_inflo_id($col_id);

            if ($col_inflo_id > 0) {


                $update_col['Apikey'] = API_KEY;
                $update_col['ListColumnid'] = $col_inflo_id;
                $update_col['ListColumnname'] = $col_name;

                $update_col = json_encode($update_col);
//            p($update_col); exit;

                $ch_update_col = curl_init();
                curl_setopt($ch_update_col, CURLOPT_URL, API_URL . "Account/UpdateListColumn");
                curl_setopt($ch_update_col, CURLOPT_POST, 1);
                curl_setopt($ch_update_col, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch_update_col, CURLOPT_POSTFIELDS, $update_col);
                curl_setopt($ch_update_col, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_update_col, CURLOPT_SSL_VERIFYPEER, false);
                $server_output_update_col = curl_exec($ch_update_col);
            }
            $update_data['column_name'] = $col_name;


            $update = $this->TasksModel->updateColumnName($list_id, $col_id, $update_data);
            if ($update == 1) {
                $update_list['modified'] = date('Y-m-d H:i:s');
                $this->ListsModel->update_list_data($list_id, $update_list);
                echo 'success';
            } else {
                echo 'fail';
            }
        }
    }

    /*
     * Change order of coluns
     * @author SG
     */

    public function change_column_order() {
        if ($this->input->post()) {
            $column_ids = json_decode($this->input->post('column_ids'));
            $list_id = $this->input->post('list_id');
            $update_col_count = 0;
            foreach ($column_ids as $c_id => $c_data):
                $new_order['order'] = $c_id + 1;
                $updated = $this->TasksModel->update_column_data($list_id, $c_data, $new_order);
                if($updated){
                    $update_col_count++;
                }
            endforeach;
            $last_log = $this->TasksModel->get_log_last($this->input->post('list_id'));
            $order = 1;
            if (!empty($last_log)) {
                $order = $this->TasksModel->find_order_log_single($this->input->post('list_id'), $last_log['new_order']);
            }

            $tasks = $this->TasksModel->get_tasks($this->input->post('list_id'));
            $task_data = array();
            $c_index = 1;
            $order_arr = array();
            $cur_order = 0;

            foreach ($tasks as $tid => $tdata):
                if ($tdata['order'] > $cur_order) {
                    $cur_order = $tdata['order'];
                    array_push($order_arr, $cur_order);
                }
                if ($tdata['order'] != $c_index) {
                    $c_index = $tdata['order'];
                }
                $task_data[$c_index][] = $tdata;
            endforeach;

            $first_key = 0;
            $resp = array();
            if (!empty($task_data)) {
                $first_key = min($order_arr);
                foreach ($task_data[$first_key] as $task_items):
                    array_push($resp, $task_items['TaskName']);
                endforeach;
            }


            if($update_col_count > 0){
                $update_list['modified'] = date('Y-m-d H:i:s');
                $this->ListsModel->update_list_data($this->input->post('list_id'), $update_list);
            }
            echo json_encode($resp);
//            p($resp); exit;
//            echo 'success';
            exit;
        }
    }

    /*
     * Delete column
     * @author SG
     */

    public function delete_column() {
        if ($this->input->post()) {

            $list_owner = $this->ListsModel->getListOwner($this->input->post('list_id'));

            $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($this->input->post('list_id'));

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $allowed_access = 1;

            $owner_find = 0;
            if (isset($_SESSION['id'])) {
                $owner_find = $_SESSION['id'];
            }
            if ($list_owner != $owner_find) {
                $priviladge_get['Apikey'] = API_KEY;
                $priviladge_get['Listid'] = $list_inflo_id;
                $priviladge_get['userid'] = 0;
                if (isset($_SESSION['id'])) {
                    $priviladge_get['userid'] = $_SESSION['id'];
                }
                $priviladge_get = json_encode($priviladge_get);

                $ch_priviledge = curl_init();
                curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
                curl_setopt($ch_priviledge, CURLOPT_POST, 1);
                curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
                curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
                $server_output_priviledge = curl_exec($ch_priviledge);
                $response_priviledge = (array) json_decode($server_output_priviledge);
//            p($response_priviledge); exit;

                if (isset($response_priviledge['success'])) {
                    if ($response_priviledge['success'] == 1) {
                        $allowed_access = 1;
                    } else {
                        $allowed_access = 0;
                        if (isset($response_priviledge['data'])) {
                            if ($response_priviledge['data']->IsPublic == 'True') {
                                if ($response_priviledge['data']->IsLocked == 'True') {
                                    $allowed_access = 2;
                                }
                            }
                        }
                    }
                }
                if ($allowed_access != 1) {
                    echo 'not allowed';
                    exit;
                }
            }


            $col_count = $this->TasksModel->count_col($this->input->post('list_id'));
            if ($col_count > 1) {
                $delete_col = $this->TasksModel->delete_column($this->input->post('list_id'), $this->input->post('column_id'));
                $cols_after_delete = $this->TasksModel->getColumns($this->input->post('list_id'));

                $reorder_col = array();
                $col_cnt = 0;
                foreach ($cols_after_delete as $cid => $col):
                    $reorder_col[$col_cnt]['id'] = $col['id'];
                    $reorder_col[$col_cnt]['order'] = $col_cnt + 1;
                    $col_cnt++;
                endforeach;
                if (!empty($reorder_cols)) {
                    $reordered = $this->TasksModel->reorder_cols($reorder_col);
                }

                if ($delete_col) {
                    $delete_items = $this->TasksModel->remove_items_column($this->input->post('list_id'), $this->input->post('column_id'));
                    
                $update_list['modified'] = date('Y-m-d H:i:s');
                $this->ListsModel->update_list_data($this->input->post('list_id'), $update_list);
                    
                    echo 'success';
                } else {
                    echo 'fail';
                }
                exit;
            } else {
                echo 'not allowed';
            }
        }
    }

    public function index2() {
        if (($this->uri->segment(1) == 'list' || $this->uri->segment(1) == 'item') && ($this->uri->segment(2) == '' || $this->uri->segment(2) == NULL)) {
            $this->session->set_flashdata('error', 'The list you are looking for does not exist!');
            redirect(base_url() . 'lists', 'refresh');
        }
        $data['title'] = 'Nexup';
        $data['list_id'] = 0;
        $data['list_name'] = 'What is your List\'s name?';
        $data['list_slug'] = '';
        $data['config']['show_completed'] = 'True';
        $data['config']['allow_move'] = 'True';
        $data['config']['allow_undo'] = 0;
        $data['config']['allow_maybe'] = 0;
        $data['config']['show_time'] = 0;
        $data['config']['enable_comment'] = 0;
        $data['config']['enable_attendance_comment'] = 0;
        $data['config']['show_preview'] = 1;
        $data['config']['show_author'] = 0;
        $data['config']['allow_append_locked'] = 0;
        $data['list_author'] = 'Anonymous';
        $data['list_user_id'] = 0;
        if (isset($_SESSION['id'])) {
            $data['list_user_id'] = $_SESSION['id'];
        }
        $data['type_id'] = 1;
        $data['is_locked'] = 0;

        $slug = '';
        if ($this->uri->segment(2) != null) {
            $slug = $this->uri->segment(2);
        } elseif (isset($_SESSION['last_slug']) && $_SESSION['last_slug'] != '') {
            $slug = $_SESSION['last_slug'];
        }
        if ($slug == '' && $this->uri->segment(1) == 'item') {
            redirect(base_url() . 'lists', 'refresh');
        }
        $data['list_owner_id'] = 0;
        $data['multi_col'] = 0;
        $data['list_desc'] = '';
        $total_visit_count = 0;
        $total_visit_count_long = 0;

        if ($slug != '') {

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }


            $list = $this->ListsModel->find_list_details_by_slug($slug);
            $sub_lists = $this->ListsModel->find_sublists($list['list_id']);
            $data['sublists'] = $sub_lists;
            if ($list['unshared_with'] != '') {
                $unshared_array = explode(',', $list['unshared_with']);
                if (isset($_SESSION['id'])) {
                    $pos = array_search($_SESSION['id'], $unshared_array);
                    if ($pos >= 0) {
                        unset($unshared_array[$pos]);
                    }
                    $unshared_str = implode(',', $unshared_array);

                    $update_data_share['unshared_with'] = $unshared_str;
//                    p($update_data_share); exit;
                    $update_unshared = $this->ListsModel->update_list_data($list['list_id'], $update_data_share);
                }
            }
            if (empty($list)) {
                redirect(base_url() . 'lists', 'refresh');
            }
            $visit_user_id = 0;
            if (isset($_SESSION['id'])) {
                $visit_user_id = $_SESSION['id'];
            }
            $visit_list_id = $list['list_id'];

            $today_date = date('Y-m-d H:i:s');
            $visit_data['user_id'] = $visit_user_id;
            $visit_data['list_id'] = $list['list_id'];
            $visit_data['date_visited'] = $today_date;
            $visit_data['created'] = $today_date;
            $visit_data['modified'] = $today_date;
            $visit_data['user_ip'] = $_SERVER['REMOTE_ADDR'];
            $record_history = $this->TasksModel->record_visit($visit_data);


            $total_visit_count_long = $this->TasksModel->count_list_visitors($visit_list_id);
            $total_visit_count = number_format_short($total_visit_count_long);



            if (isset($_SESSION['id']) && $_SESSION['id'] > 0) {

                $data_get['Apikey'] = API_KEY;
                $data_get['SearchText'] = '';

                $data_get = json_encode($data_get);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, API_URL . "account/GetListsSharedWithLoggedInUser");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_get);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $server_output = curl_exec($ch);
                $response = (array) json_decode($server_output);

                $shared_list_ids = array();
                if (isset($response['success']) && $response['success'] == 1) {
                    $shared_cnt = 0;
                    foreach ($response['data'] as $rid => $res):
                        array_push($shared_list_ids, $res->ListId);
                    endforeach;
                }
            }

            if (!empty($list)) {
                $extra_attendance = $this->TasksModel->get_all_extra($list['list_id']);
                $data['attendance_data'] = $extra_attendance;
                $list_inflo_id = $list['list_inflo_id'];
            }

//            $list_extra = $this->TasksModel->get_attendance_extra($list['list_id']);
//            p($_SESSION); exit;
            if ($list['user_id'] == 0) {

                if (isset($_SESSION['id']) && $_SESSION['id'] > 0) {
//                    p($list); exit;
                    $updt_list['user_id'] = $_SESSION['id'];
                    $user_name = '';
                    if (isset($_SESSION['first_name']) && !empty($_SESSION['first_name'])) {
                        $user_name .= $_SESSION['first_name'];
                    }
                    if (isset($_SESSION['last_name']) && !empty($_SESSION['last_name'])) {
                        $user_name .= ' ' . $_SESSION['last_name'];
                    }
                    if (empty($_SESSION['first_name']) && empty($_SESSION['last_name'])) {
                        $user_name = $_SESSION['email'];
                    }
                    $updt_list['user_id'] = $_SESSION['id'];
                    $updt_list['created_user_name'] = $user_name;
                    $list['created_user_name'] = $user_name;
                    $this->ListsModel->update_list_data($list['list_id'], $updt_list);
                    $data_send['Apikey'] = API_KEY;
                    $data_send['Listid'] = $list_inflo_id;
                    $data_send['CreatedByUserId'] = $_SESSION['id'];
//                    p($data_send); exit;

                    $data_send = json_encode($data_send);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, API_URL . "account/UpdateList");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_send);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $server_output = curl_exec($ch);
                    $response_update = (array) json_decode($server_output);
                    if (isset($response_update['success']) && $response_update['success'] == 1) {
                        $update_list['created_user_name'] = $response_update['data']->CreateByUserFullName;
                        $this->ListsModel->update_list_data($list['list_id'], $update_list);
                    }
                }
            }

            if (empty($list)) {
                $this->session->set_flashdata('error', 'The list you are looking for does not exist!');
                redirect(base_url() . 'lists', 'refresh');
            }
            if ($list['is_deleted'] == 1) {
                $this->session->set_flashdata('error', 'The list you are looking for does not exist!');
                redirect(base_url() . 'lists', 'refresh');
            }
            $nexup_type = '1';
            if ($list['type_id'] == 8) {
                $nexup_type = '3';
            }
            $last_log = $this->TasksModel->get_log_last($list['list_id'], $nexup_type);
            $deleted_item = $this->TasksModel->get_task_details($last_log['new_order'], $list['list_id']);
            if ($deleted_item == 1) {
                $order_item = $this->TasksModel->get_item_order($last_log['new_order']);
                $log_data = $this->TasksModel->get_similar_item($order_item['order'], $list['list_id']);
            }
            $data['last_log'] = array();
            if (!empty($last_log)) {
                if ($deleted_item == 0) {
                    $data['last_log'] = $last_log['new_order'];
                } else {
                    $data['last_log'] = $log_data['TaskId'];
                }
            }
            $sort = 'list_columns.order asc';
            if ($list['type_id'] == 11) {
                if ($list['show_time'] == 1) {
                    $sort .= ', list_data.is_present=0,list_data.is_present, attendance_data.check_date desc, list_data.value, list_data.order asc';
                } else {
                    $sort .= ', list_data.is_present=0,list_data.is_present, list_data.value, list_data.order asc';
                }
            } elseif ($list['type_id'] == 5) {
//                $sort .= ', list_data.is_completed, list_data.modified, list_data.order asc';
                $sort .= ', list_data.is_completed, list_data.order asc';
            } else {
                $sort .= ', list_data.order asc';
            }

            $tasks = $this->TasksModel->get_tasks_2($list['list_id'], $sort);

            $task_data = array();
            $c_index = 1;

            foreach ($tasks as $tid => $tdata):

                if ($tdata['order'] != $c_index) {
                    $c_index = $tdata['order'];
                }
                $task_data[$c_index][] = $tdata;
            endforeach;
//            p($task_data); exit;

            $columns = $this->TasksModel->getColumns($list['list_id']);

            $data['list_name'] = $list['list_name'];
            $data['list_id'] = $list['list_id'];
            $data['list_slug'] = $list['list_slug'];
            $data['show_list_slug'] = $list['list_slug'];
            $data['list_author'] = $list['created_user_name'];
            $data['list_user_id'] = $list['user_id'];

            if (!empty($list['custom_slug'])) {
                $data['show_list_slug'] = $list['custom_slug'];
            }
            $data['type_id'] = $list['type_id'];
            if ($list['show_completed'] == 0) {
                $show_completed = 'False';
            } else {
                $show_completed = 'True';
            }
            $data['config']['show_completed'] = $show_completed;
            if ($list['allow_move'] == 0) {
                $allow_move = 'False';
            } else {
                $allow_move = 'True';
            }
            $data['config']['allow_move'] = $allow_move;
            $data['config']['allow_undo'] = $list['allow_undo'];
            $data['list_owner_id'] = $list['list_owner_id'];
            $data['is_locked'] = $list['is_locked'];
            if (isset($_SESSION['id']) && $list['is_locked'] == 1) {
                if ($list['user_id'] != $_SESSION['id']) {
                    $data['is_locked'] = 2;
                }
            }
            $data['config']['allow_maybe'] = $list['allow_maybe'];
            $data['config']['show_time'] = $list['show_time'];
            $data['config']['show_preview'] = $list['show_preview'];
            $data['config']['enable_comment'] = $list['enable_comment'];
            $data['config']['enable_attendance_comment'] = $list['enable_attendance_comment'];
            $data['config']['show_author'] = $list['show_author'];
            $data['config']['allow_append_locked'] = $list['allow_append_locked'];

            if (!empty($columns)) {
//            if(count($columns) > 1){
                $data['columns'] = $columns;
            }
            if (count($columns) > 1) {
                $data['multi_col'] = 1;
            }
            $data['tasks'] = $task_data;
            $data['list_desc'] = $this->ListsModel->list_desc_get($list['list_id']);

            if ($list['type_id'] == 11) {
                if ($list['show_time'] != 1) {
                    $task_for_sort = $data['tasks'];
                    $temp_arr_sorted = array();
                    $temp_arr_yes = array();
                    $temp_arr_maybe = array();
                    $temp_arr_no = array();
                    $temp_arr_blank = array();
                    foreach ($task_for_sort as $tf_id => $tf_data):
                        if ($tf_data[0]['IsPresent'] == 1) {
                            $temp_arr_yes[$tf_id] = $tf_data[0]['TaskName'];
                        } elseif ($tf_data[0]['IsPresent'] == 2) {
                            $temp_arr_maybe[$tf_id] = $tf_data[0]['TaskName'];
                        } elseif ($tf_data[0]['IsPresent'] == 3) {
                            $temp_arr_no[$tf_id] = $tf_data[0]['TaskName'];
                        } else {
                            $temp_arr_blank[$tf_id] = $tf_data[0]['TaskName'];
                        }
                    endforeach;
                    sort($temp_arr_yes);
                    sort($temp_arr_maybe);
                    sort($temp_arr_no);
                    sort($temp_arr_blank);


                    foreach ($temp_arr_yes as $yes):
                        foreach ($task_for_sort as $sort_id => $sort_task):
                            if ($yes == $sort_task[0]['TaskName']) {
                                $temp_arr_sorted[$sort_id] = $sort_task;
                            }
                        endforeach;
                    endforeach;

                    foreach ($temp_arr_maybe as $maybe):
                        foreach ($task_for_sort as $sort_id => $sort_task):
                            if ($maybe == $sort_task[0]['TaskName']) {
                                $temp_arr_sorted[$sort_id] = $sort_task;
                            }
                        endforeach;
                    endforeach;

                    foreach ($temp_arr_no as $no):
                        foreach ($task_for_sort as $sort_id => $sort_task):
                            if ($no == $sort_task[0]['TaskName']) {
                                $temp_arr_sorted[$sort_id] = $sort_task;
                            }
                        endforeach;
                    endforeach;

                    foreach ($temp_arr_blank as $blank):
                        foreach ($task_for_sort as $sort_id => $sort_task):
                            if ($blank == $sort_task[0]['TaskName']) {
                                $temp_arr_sorted[$sort_id] = $sort_task;
                            }
                        endforeach;
                    endforeach;
//                        p($temp_arr_sorted);
//                    exit;

                    $data['tasks'] = $temp_arr_sorted;

//                    p($temp_arr_sorted); exit;
                }
            }

//            p($data['tasks']); exit;


            if (!empty($data)) {

                if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                    if (isset($_SESSION['auth_visit']) && $_SESSION['auth_visit'] != null) {
                        $visited_arr = $_SESSION['auth_visit'];
                        if (in_array($data['list_id'], $visited_arr['list_id'])) {
                            $index = array_search($data['list_id'], $visited_arr['list_id']);
                            unset($visited_arr['list_id'][$index]);
                            unset($visited_arr['list_name'][$index]);
                            unset($visited_arr['list_slug'][$index]);
                        }

                        $visited_arr['list_id'][] = $data['list_id'];
                        $visited_arr['list_name'][] = $data['list_name'];
                        $visited_arr['list_slug'][] = $data['list_slug'];
                        $_SESSION['auth_visit'] = $visited_arr;
                    } else {
                        $visited_arr['list_id'][] = $data['list_id'];
                        $visited_arr['list_name'][] = $data['list_name'];
                        $visited_arr['list_slug'][] = $data['list_slug'];
                        $_SESSION['auth_visit'] = $visited_arr;
                    }
                } else {
                    if (isset($_SESSION['unauth_visit']) && $_SESSION['unauth_visit'] != null) {
                        $visited_arr = $_SESSION['unauth_visit'];

                        if (in_array($data['list_id'], $visited_arr['list_id'])) {
                            $index = array_search($data['list_id'], $visited_arr['list_id']);
                            unset($visited_arr['list_id'][$index]);
                            unset($visited_arr['list_name'][$index]);
                            unset($visited_arr['list_slug'][$index]);
                        }

                        $visited_arr['list_id'][] = $data['list_id'];
                        $visited_arr['list_name'][] = $data['list_name'];
                        $visited_arr['list_slug'][] = $data['list_slug'];
                        $_SESSION['unauth_visit'] = $visited_arr;
                    } else {
                        $visited_arr['list_id'][] = $data['list_id'];
                        $visited_arr['list_name'][] = $data['list_name'];
                        $visited_arr['list_slug'][] = $data['list_slug'];
                        $_SESSION['unauth_visit'] = $visited_arr;
                    }
                }
                $log = $this->TasksModel->find_log($list['list_id']);
                if ($list['type_id'] == 8) {
                    $log = $this->TasksModel->find_log_random($list['list_id']);
                }
//                echo $this->db->last_query(); exit;

                $first_column_id = $this->TasksModel->find_first_column($list['list_id']);
//                p($first_column_id); exit;
                $log_print = $log;
                foreach ($log as $lgid => $lgval):
                    $order_current_item = $this->TasksModel->find_order_log_single($list['list_id'], $lgval['new_order']);
                    $first_item_current = $this->TasksModel->find_item_first($list['list_id'], $order_current_item, $first_column_id);
                    $log_print[$lgid]['value'] = $first_item_current;
                endforeach;
//                p($log_print); exit;
//                p($log); exit;
                if (!empty($log)) {
                    $data['log_list'] = $log_print;
                }
            } else {
                redirect($this->agent->referrer(), 'refresh');
            }
        }
        $data['total_visits'] = $total_visit_count;
        $data['total_visits_long'] = $total_visit_count_long;

        $list_types = $this->TasksModel->getListTypes();
        $data['list_types'] = $list_types;
        $max_order = 0;
        if (!empty($list)) {
            $max_order = $this->TasksModel->get_last_order_of_item($list['list_id']);
        }
        $data['max_order'] = $max_order;

        $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($data['list_id']);

        $header = array('Content-Type: application/json');
        if (isset($_SESSION['xauthtoken'])) {
            $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
            array_push($header, $val);
        }

        $data_send_check['Apikey'] = API_KEY;
//        $data_send['Listid'] = 1;
        $data_send_check['Listid'] = $list_inflo_id;

        $data_send_check = json_encode($data_send_check);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_send_check);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $server_output = curl_exec($ch);
        $response = (array) json_decode($server_output);

        $list_share_data = array();
        if (isset($response['success']) && $response['success'] == 1) {
            $list_share_data = (array) $response['data'];
        }
        $data['list_share_data'] = $list_share_data;

        $list_owner = $this->ListsModel->getListOwner($data['list_id']);

        $allowed_access = 1;

        $owner_find = 0;
        if (isset($_SESSION['id'])) {
            $owner_find = $_SESSION['id'];
        }
        if ($list_owner != $owner_find) {
            $priviladge_get['Apikey'] = API_KEY;
            $priviladge_get['Listid'] = $list_inflo_id;
            $priviladge_get['userid'] = 0;
            if (isset($_SESSION['id'])) {
                $priviladge_get['userid'] = $_SESSION['id'];
            }
            $priviladge_get = json_encode($priviladge_get);

            $ch_priviledge = curl_init();
            curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
            curl_setopt($ch_priviledge, CURLOPT_POST, 1);
            curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
            curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
            $server_output_priviledge = curl_exec($ch_priviledge);
            $response_priviledge = (array) json_decode($server_output_priviledge);

            if (isset($response_priviledge['success'])) {
                if ($response_priviledge['success'] == 1) {
                    $allowed_access = 1;
                } else {
                    $allowed_access = 0;
                    if (isset($response_priviledge['data'])) {
                        if ($response_priviledge['data']->IsPublic == 'True') {
                            if ($response_priviledge['data']->IsLocked == 'True') {
                                $allowed_access = 2;
                            }
                        }
                    }
//                    echo $allowed_access; exit;
                }
            }
        }


        $data['allowed_access'] = $allowed_access;
        $data['list_inflo_id'] = $list_inflo_id;

        $blank_flag = 0;
        $yes_flag = 1;
        $no_flag = 3;
        $maybe_flag = 2;
        $data['total_yes'] = 0;
        $data['total_no'] = 0;
        $data['total_maybe'] = 0;
        $data['total_blank'] = 0;
        if (isset($list['type_id']) && $list['type_id'] == 11) {
            $total_cols = $this->TasksModel->count_col($list['list_id']);
            
            $first_col_id = $this->TasksModel->find_first_column($list['list_id']);

            $yes = $this->TasksModel->find_count_present($first_col_id, $yes_flag, $list['list_id']);
            if ($total_cols > 0) {
                $data['total_yes'] = $yes;
            }
            $no = $this->TasksModel->find_count_present($first_col_id, $no_flag, $list['list_id']);
            if ($total_cols > 0) {
                $data['total_no'] = $no;
            }

            $maybe = $this->TasksModel->find_count_present($first_col_id, $maybe_flag, $list['list_id']);
            if ($total_cols > 0) {
                $data['total_maybe'] = $maybe;
            }

            $blank = $this->TasksModel->find_count_present($first_col_id, $blank_flag, $list['list_id']);
            if ($total_cols > 0) {
                $data['total_blank'] = $blank;
            }
        }
//        p($data); exit;

        $this->template->load('default_template2', 'task/index2', $data);
    }

    /*
     * Mark as present
     * @author SG
     */

    public function present() {
        if ($this->input->post()) {
            $item_details = $this->TasksModel->get_task_order($this->input->post('TaskId'));
            $found_items = $this->TasksModel->get_similar_tasks($item_details['list_id'], $item_details['order']);

            $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($item_details['list_id']);

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $list_owner = $this->ListsModel->getListOwner($item_details['list_id']);

            $list = $this->ListsModel->find_list_details_by_id($item_details['list_id']);

            $owner_find = 0;
            if (isset($_SESSION['id'])) {
                $owner_find = $_SESSION['id'];
            }
            if ($list_owner != $owner_find) {
                $priviladge_get['Apikey'] = API_KEY;
                $priviladge_get['Listid'] = $list_inflo_id;
                $priviladge_get['userid'] = 0;
                if (isset($_SESSION['id'])) {
                    $priviladge_get['userid'] = $_SESSION['id'];
                }
                $priviladge_get = json_encode($priviladge_get);

                $ch_priviledge = curl_init();
                curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
                curl_setopt($ch_priviledge, CURLOPT_POST, 1);
                curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
                curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
                $server_output_priviledge = curl_exec($ch_priviledge);
                $response_priviledge = (array) json_decode($server_output_priviledge);
                $allowed_access = 0;
                if (isset($response_priviledge['success'])) {
                    if ($response_priviledge['success'] == 1) {
                        $allowed_access = 1;
                    }
                    if (isset($response_priviledge['data']) && ($response_priviledge['data']->IsPublic == TRUE && $response_priviledge['data']->IsLocked == TRUE)) {
                        $allowed_access = 2;
                    }
                }

                if ($allowed_access == 0) {
                    echo 'unauthorized';
                    exit;
                }
            }
            $mark_value = $this->input->post('TaskId');

            $today = date('Y:m:d H:i:s');
            $mark_items = $this->TasksModel->present_task($mark_value, $item_details['id'], $this->input->post('task_status'), $today);
            if ($this->input->post('time_id') > 0) {
                $update_time = $this->TasksModel->update_check_time($this->input->post('time_id'), $this->input->post('ListId'), $today);
            }
            if ($mark_items > 0) {
                
                $update_list['modified'] = date('Y-m-d H:i:s');
                $this->ListsModel->update_list_data($this->input->post('ListId'), $update_list);
                
                $blank_flag = 0;
                $yes_flag = 1;
                $no_flag = 3;
                $maybe_flag = 2;
                $total_cols = $this->TasksModel->count_col($this->input->post('ListId'));

                $first_col_id = $this->TasksModel->find_first_column($list['list_id']);
                
                $yes = $this->TasksModel->find_count_present($first_col_id, $yes_flag, $this->input->post('ListId'));
                $total['yes'] = $yes;
                $no = $this->TasksModel->find_count_present($first_col_id, $no_flag, $this->input->post('ListId'));
                $total['no'] = $no;

                $maybe = $this->TasksModel->find_count_present($first_col_id, $maybe_flag, $this->input->post('ListId'));
                $total['maybe'] = $maybe;

                $blank = $this->TasksModel->find_count_present($first_col_id, $blank_flag, $this->input->post('ListId'));
                $total['blank'] = $blank;
//                echo $this->db->last_query(); exit;
                echo json_encode($total);
            } else {
                echo 'fail';
            }
        }
        exit;
    }

    /*
     * Customize URL
     * @uthor SG
     */

    public function update_url() {
        if ($this->input->post()) {

            if (!empty($this->input->post('new_slug'))) {
                if (trim($this->input->post('new_slug')) == '') {
                    echo 'empty';
                    exit;
                }
                if (!ctype_alnum($this->input->post('new_slug'))) {
                    echo 'bad string';
                    exit;
                }
                $find = $this->ListsModel->find_exist_slug(trim($this->input->post('new_slug')), $this->input->post('list_id'));
                if (empty($find)) {
                    $update = $this->TasksModel->update_slug($this->input->post('list_id'), trim($this->input->post('new_slug')));
                    if ($update > 0) {
                        $update_list['modified'] = date('Y-m-d H:i:s');
                        $this->ListsModel->update_list_data($this->input->post('list_id'), $update_list);
                
                        if (isset($_SESSION['auth_visit'])) {
                            if (in_array($this->input->post('list_id'), $_SESSION['auth_visit']['list_id'])) {
                                $key = array_search($this->input->post('list_id'), $_SESSION['auth_visit']['list_id']);
                                $_SESSION['auth_visit']['list_slug'][$key] = trim($this->input->post('new_slug'));
                            }
                        }
                        if (isset($_SESSION['unauth_visit']) && !empty($_SESSION['unauth_visit'])) {
                            if (in_array($this->input->post('list_id'), $_SESSION['unauth_visit']['list_id'])) {
                                $key = array_search($this->input->post('list_id'), $_SESSION['unauth_visit']['list_id']);
                                $_SESSION['unauth_visit']['list_slug'][$key] = trim($this->input->post('new_slug'));
                            }
                        }

                        $header = array('Content-Type: application/json');
                        if (isset($_SESSION['xauthtoken'])) {
                            $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                            array_push($header, $val);
                        }

                        $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($this->input->post('list_id'));

                        $api_update_slug['Apikey'] = API_KEY;
                        $api_update_slug['Listslug'] = trim($this->input->post('new_slug'));
                        $api_update_slug['Listid'] = $list_inflo_id;
                        $post_data = json_encode($api_update_slug);
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, API_URL . "account/UpdateListSlug");
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        $server_output = curl_exec($ch);
                        $response = (array) json_decode($server_output);

                        p($response);

                        echo 'success';
                    } else {
                        echo 'fail';
                    }
                } else {
                    echo 'existing';
                }
            } else {
                echo 'not found';
            }
            exit;
        }
    }

    /*
     * Export log to csv
     * @uthor SG
     */

    public function export_log() {
        if (isset($_SESSION['id']) && $_SESSION['id'] > 0) {
            $list_id = $this->uri->segment(2);

            $list_details = $this->ListsModel->find_list_details_by_id($list_id);

            $nexup_type = 1;
            if ($list_details['type_id'] == 8) {
                $nexup_type = 3;
            }

            $log = $this->TasksModel->find_log($list_id);
            if ($list_details['type_id'] == 8) {
                $log = $this->TasksModel->find_log_random($list_id);
            }

            $first_column_id = $this->TasksModel->find_first_column($list_id);
            $log_print = $log;

            foreach ($log as $lgid => $lgval):
                $order_current_item = $this->TasksModel->find_order_log_single($list_id, $lgval['new_order']);
                $first_item_current = $this->TasksModel->find_item_first($list_id, $order_current_item, $first_column_id);
                $log_print[$lgid]['value'] = $first_item_current;
            endforeach;

            $file_export = 'Item,user,Comment,Date' . PHP_EOL;
            foreach ($log_print as $lid => $ldata):
                $cmt = " ";
                if (!empty($ldata['comment'])) {
                    $cmt = $ldata['comment'];
                }
                $ldata['value'] = htmlspecialchars_decode($ldata['value'], ENT_QUOTES);
                if (strpos($ldata['value'], '"') != false) {
                    $ldata['value'] = str_replace('"', "”", htmlspecialchars_decode($ldata['value'], ENT_QUOTES));
                }
                if (strpos($ldata['value'], ',') !== false) {
                    $ldata['value'] = '"' . $ldata['value'] . '"';
                }
                if (strpos($cmt, '"') !== false) {
                    $cmt = str_replace('"', "”", $cmt);
                }
                if (strpos($cmt, ',') !== false) {
                    $cmt = '"' . $cmt . '"';
                }
                if ($ldata['user_name'] == '') {
                    $ldata['user_name'] = 'anonymous';
                }
                $file_export .= $ldata['value'] . ',' . $ldata['user_name'] . ',' . $cmt . ',' . $ldata['created'] . PHP_EOL;
            endforeach;
            $file_export = iconv("windows-1254", "utf8", $file_export);
            $filename = 'ActionLog';
            header('Content-type: text/csv; charset: UTF-8');
            header('Content-Encoding: UTF-8');
            header("Content-Disposition: attachment; filename=" . $filename . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo($file_export);
        }
    }

    /*
     * Get all the items of list
     * @author SG
     */

    public function get_bulk_data() {
        if ($this->input->post()) {
            $list_id = $this->input->post('list_id');
            $list_cols = $this->TasksModel->getColumns($list_id);
            $list_cols_arr = array_column($list_cols, 'column_name');
            
            $list_type_array = array();
            foreach ($list_cols as $clKey => $lcData):
                $list_type_array[$lcData['id']] = $lcData['type'];
            endforeach;

            $list_column_str = implode("\t", $list_cols_arr);


            $list_data = $this->TasksModel->get_all_items($list_id);

            $list_data_complete_arr = array();
            foreach ($list_data as $lid => $ldata):
                if($list_type_array[$ldata['column_id']] == 'checkbox'){
                    if (strpos($ldata['TaskName'], 'checked') !== false) {
                        $task_name = ' true';
                    }else{
                        $task_name = 'false';
                    }
                }elseif($list_type_array[$ldata['column_id']] == 'timestamp'){
                    $task_name = time();
                }else{
                    $task_name = $ldata['TaskName'];
                }
                $list_data_complete_arr[$ldata['order']][] = htmlspecialchars_decode($this->security->xss_clean($task_name));
//                $list_data_complete_arr[$ldata['order']][] = $this->security->xss_clean($ldata['TaskName']);
            endforeach;

            
            $list_item_arr = array();
            foreach ($list_data_complete_arr as $lists_id => $lists_data):
                $list_item_arr[$lists_id] = implode("\t", $lists_data);
            endforeach;

            $list_item_text = implode("\n", $list_item_arr);
            if (trim(empty($list_column_str)) && trim(empty($list_item_text))) {
                echo '';
            } else {
                echo $list_column_str . "\n" . $list_item_text;
            }
            exit;

//            p($list_item_text); exit;
        }
    }

    public function save_bulk_data() {
        if ($this->input->post()) {
            $list_data = json_decode($this->input->post('list_data'));
            $list_id = $this->input->post('list_id');
            $separator = $this->input->post('separator');
            if ($separator == 'tab') {
                $list_data = preg_replace('/\t/', '|;|', $list_data);
            }
            $list_data = str_replace('"', '', $list_data);

            $data_arr = explode(PHP_EOL, $list_data);

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $api_add['Apikey'] = API_KEY;


            if ($separator == 'tab') {
                $reg_ex = "/\t/";
            }

            $updt_arr = array();
            foreach ($data_arr as $rid => $rows):
                if ($separator == 'tab') {
                    $rows = preg_replace($reg_ex, '|;|', $rows);
                    $updt_arr[$rid] = explode('|;|', $rows);
                } else {
                    $updt_arr[$rid] = explode(',', $rows);
                }

            endforeach;
            $largest_arr_size = 0;
            foreach ($updt_arr as $arid => $arnm):
                if (sizeof($arnm) > $largest_arr_size) {
                    $largest_arr_size = sizeof($arnm);
                }
            endforeach;

            foreach ($updt_arr as $updtid => $updtnm):
                if (sizeof($updtnm) < $largest_arr_size) {
                    $new_elements_needed = ($largest_arr_size - sizeof($updtnm));
                    for ($i = 0; $i < $new_elements_needed; $i++) {
                        array_push($updt_arr[$updtid], "");
                    }
                }
            endforeach;

            function count_array_size($a) {
                return count($a);
            }

            $all_array_size = array_map("count_array_size", $updt_arr);

            
            $min_size = $all_array_size[0];
            $max_size = $all_array_size[0];

            foreach ($all_array_size as $aid => $asize):
                if ($asize > $max_size) {
                    $max_size = $asize;
                }
                if ($asize < $min_size) {
                    $min_size = $asize;
                }
            endforeach;

            if ($min_size != $max_size) {
                echo 'mismatch';
                exit;
            }
//            if($this->input->post('include_header') == 1){
            $del_cols = $this->TasksModel->delete_columns_bulk($list_id);
//            }

            $del_tasks = $this->TasksModel->delete_task_bulk($list_id);
            $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($list_id);

//            if($this->input->post('include_header') == 1){
            $header_data = $updt_arr[0];
            if ($this->input->post('include_header') == 1) {
                unset($updt_arr[0]);
            }
            $col_ids = array();
            $cols = array();
            $col_order = 1;
            $today = date('Y-m-d H:i:s');
            foreach ($header_data as $hid => $hname):
                $api_caol_add['Apikey'] = API_KEY;
                $api_caol_add['Listid'] = $list_inflo_id;
                $api_caol_add['ListColumnName'] = htmlentities($hname);
                $post_col_data = json_encode($api_caol_add);
                $ch_col = curl_init();
                curl_setopt($ch_col, CURLOPT_URL, API_URL . "account/CreateListColumn");
                curl_setopt($ch_col, CURLOPT_POST, 1);
                curl_setopt($ch_col, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch_col, CURLOPT_POSTFIELDS, $post_col_data);
                curl_setopt($ch_col, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_col, CURLOPT_SSL_VERIFYPEER, false);
                $server_output_col = curl_exec($ch_col);
                $response_col = (array) json_decode($server_output_col);
                //                p($response_col);
                if (isset($response_col['success']) && $response_col['success'] == 1) {
                    $cols[$hid]['col_inflo_id'] = $response_col['data']->ColumnId;
                }


                $cols[$hid]['list_id'] = $list_id;
                $cols[$hid]['list_inflo_id'] = $list_inflo_id;
                $cols[$hid]['column_name'] = htmlentities($this->security->xss_clean($hname));
                $cols[$hid]['order'] = $col_order;
                $col_order++;
                $cols[$hid]['is_deleted'] = 0;
                $cols[$hid]['created'] = $today;
                $cols[$hid]['modified'] = $today;
                $col_add = $this->TasksModel->add_new_colum($cols[$hid]);
                array_push($col_ids, $col_add);
            endforeach;
//            }else{
//                $col_ids = $this->TasksModel->find_all_col_ids($list_id);
//                $col_ids = array_column($col_ids, 'id');
//            }

            $uid = 0;
            if (isset($_SESSION['id'])) {
                $uid = $_SESSION['id'];
            }

            $vals_save = array();
            $vals_cnt = 0;
            $item_id_data = 0;

            if (!empty($col_ids) && !empty($updt_arr)) {
                foreach ($updt_arr as $upid => $up_data):
                    foreach ($up_data as $ui => $ud):
                        $api_add['Taskname'] = htmlentities($ud);
                        $api_add['Listid'] = $list_inflo_id;
                        $post_data = json_encode($api_add);
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, API_URL . "account/CreateTask");
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        $server_output = curl_exec($ch);
                        $response = (array) json_decode($server_output);
//                        p($response);

                        if (!$this->session->userdata('logged_in')) {
                            if ($this->session->userdata('task_id') != null) {
                                $task_arr = $this->session->userdata('task_id');
                            } else {
                                $task_arr = array();
                            }
                            array_push($task_arr, $response['data']->TaskId);
                            $_SESSION['task_id'] = $task_arr;
                        }
                        $task_id = 0;
                        if (isset($response['success']) && $response['success'] == 1) {
                            $task_id = $response['data']->TaskId;
                        }
                        curl_close($ch);


                        $vals_save[$vals_cnt]['user_id'] = $uid;
                        $vals_save[$vals_cnt]['list_id'] = $list_id;
                        $vals_save[$vals_cnt]['list_inflo_id'] = $list_inflo_id;
                        $vals_save[$vals_cnt]['task_inflo_id'] = $task_id;
                        $vals_save[$vals_cnt]['value'] = htmlentities($this->security->xss_clean($ud));
                        $vals_save[$vals_cnt]['is_completed'] = 0;
                        $vals_save[$vals_cnt]['is_present'] = 0;
                        $vals_save[$vals_cnt]['is_deleted'] = 0;
                        $vals_save[$vals_cnt]['order'] = $item_id_data + 1;
                        $vals_save[$vals_cnt]['preview_meta'] = '';
                        $get_tags_str = htmlspecialchars_decode(htmlspecialchars_decode($ud));
                        preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $get_tags_str, $match);
                        if (!empty($match)) {
                            if (!empty($match[0])) {
                                $match_url = $match[0][0];
                                $googlePagespeedData = file_get_contents("https://www.googleapis.com/pagespeedonline/v2/runPagespeed?url=$match_url&screenshot=true");
                                $googlePagespeedData = json_decode($googlePagespeedData, true);
                                $screenshot = $googlePagespeedData['screenshot']['data'];
                                $screenshot = str_replace(array('_', '-'), array('/', '+'), $screenshot);
                                $vals_save[$vals_cnt]['preview_meta'] = $screenshot;
                            }
                        }


                        $vals_save[$vals_cnt]['column_id'] = $col_ids[$ui];
                        $vals_save[$vals_cnt]['created'] = $today;
                        $vals_save[$vals_cnt]['modified'] = $today;
                        $vals_cnt++;
                    endforeach;
                    $item_id_data++;
                endforeach;
//                p($vals_save); exit;

                $task_add = $this->TasksModel->add_task($vals_save);

                $find_all_tasks = $this->TasksModel->get_tasks($list_id);

                $delete_data = $this->TasksModel->delete_attendance_data($list_id);

                $tasks_arr = array();

                foreach ($find_all_tasks as $all_tasks):
                    $tasks_arr[$all_tasks['order']][] = $all_tasks['TaskId'];
                endforeach;

                if (!empty($tasks_arr)) {
                    foreach ($tasks_arr as $task_data):
                        $save_data = implode(',', $task_data);
                        $add_task_present_data = $this->TasksModel->add_attendance_data($list_id, null, $save_data);
                    endforeach;
                }
                
                $data_list['modified'] = date('Y-m-d H:i:s');
                $update_list = $this->ListsModel->update_list_data($list_id, $data_list);
                
                $this->TasksModel->delete_nexup_data($list_id);
                exit;
            }

            if (empty($col_ids) && empty($updt_arr)) {
                echo 'error';
            } else {
                //REsponse Table
                $c_index = 1;
                $list_data = $this->TasksModel->get_tasks($list_id);
                $type = $this->ListsModel->find_list_type($list_id);
                foreach ($list_data as $tid => $tdata):
                    if ($tdata['order'] != $c_index) {
                        $c_index = $tdata['order'];
                    }
                    $task_data[$c_index - 1][] = $tdata;
                endforeach;

                $columns = $this->TasksModel->getColumns($list_id);


                $res_table = '<table id="test_table" class="table">';
                $res_table .= '<thead>';
                $res_table .= '<tr class="td_add_tr ui-sortable">';
                if ($type == 3) {
                    $res_table .= '<th class="noDrag rank_th_head"></th>';
                }
                $res_table .= '<th class="noDrag"></th>';
                foreach ($columns as $colId => $colData):
                    $res_table .= '<th class="heading_items_col_add" data-listid="' . $list_id . '" data-colid="' . $colData['id'] . '">';
                    $res_table .= '<div class=" add-data-input"><input type="text" name="task_name" id="task_name" class="task_name" data-listid="2004" data-colid="769" placeholder="item"></div>';
                    $res_table .= '</th>';
                endforeach;
                $res_table .= '</tr>';


                if (count($columns) > 1) {
                    $res_table .= '<tr class="td_add_tr ui-sortable">';
                    if ($type == 3) {
                        $res_table .= '<th class="noDrag rank_th_head"></th>';
                    }
                    $res_table .= '<th class="noDrag"></th>';
                    foreach ($columns as $colId => $colData):
                        $res_table .= '<th class="heading_items_col" data-listid="' . $list_id . '" data-colid="' . $colData['id'] . ' " data-toggle="tooltip" data-placement="top" title="' . $colData['column_name'] . '">';
                        $res_table .= '<div class="add-data-title-r">';
                        $res_table .= '<a href="" class="icon-more-h move_col ui-sortable-handle" id="dropdownMenu0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="visibility: hidden;"></a>';
                        $res_table .= '<a class="remove_col custom_cursor icon-cross-out" data-colid="' . $colData['id'] . '" data-listid="' . $list_id . '" style="visibility: hidden;"></a>';
                        $res_table .= '</div>';
                        $res_table .= '<div class="add-data-title" data-colid="' . $colData['id'] . '" data-listid="' . $list_id . '">';
                        $res_table .= '<span class="column_name_class" id="col_name_' . $colData['id'] . '">' . $colData['column_name'] . '</span>';
                        $res_table .= '</div>';
                        $res_table .= '</th>';

                        $res_table .= '</th>';
                    endforeach;
                    $res_table .= '</tr>';
                }
                $res_table .= '</thead>';
                $res_table .= '<tbody>';
                if (!empty($task_data)) {
                    if ($type == 3) {
                        $rank_cnt = 1;
                    }
                    foreach ($task_data as $upid => $up_data):
                        $res_table .= '<tr>';
                        if ($type == 3) {
                            $res_table .= '<td class="rank_th">' . $rank_cnt . '</td>';
                            $rank_cnt ++;
                        }
                        $res_table .= '<td class="icon-more-holder" data-order="' . $up_data[0]['order'] . '" data-listid="' . $list_id . '" data-taskname="' . $up_data[0]['TaskName'] . '">';
                        $res_table .= '<span class="icon-more ui-sortable-handle"></span>';
                        $res_table .= '<a href="javascript:void(0)" class="icon-cross-out delete_task custom_cursor" data-id="' . $up_data[0]['TaskId'] . '" data-listid="' . $list_id . '"></a>';
                        $res_table .= '</td>';

                        foreach ($up_data as $ui => $ud):
                            $res_table .= '<td class="list-table-view">';
                            $res_table .= '<div class="add-data-div edit_task " data-id="' . $ud['TaskId'] . '" data-task="' . $up_data[0]['TaskName'] . '" data-listid="2111">';
                            $res_table .= '<span id="span_task_' . $ud['TaskId'] . '" class="task_name_span">' . $up_data[0]['TaskName'] . '</span>';
                            $res_table .= '</div>';
                            $res_table .= '</td>';
                        endforeach;
                        $res_table .= '</tr>';
                    endforeach;
                }
                $res_table .= '</tbody>';
                $res_table .= '</table>';
                //REsponse Table End

                echo 'success';
            }

            exit;
        }
    }

    /*
     * Get list of connections, groups, smart listing with whom list is shared
     * @author SG
     */

    public function getShared() {
        $list_id = $this->input->post('list_id');
        $header = array('Content-Type: application/json');
        if (isset($_SESSION['xauthtoken'])) {
            $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
            array_push($header, $val);
        }
        $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($list_id);

        $api_get['Apikey'] = API_KEY;
        $api_get['Listid'] = $list_inflo_id;

        $post_data = json_encode($api_get);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $server_output = curl_exec($ch);
        $response = (array) json_decode($server_output);
//p($api_get); exit;
        $res = '';
        if (isset($response['success']) && $response['success'] == 1) {
            $list_update['is_private'] = 0;
            $res_data = $response['data'];
            if (empty($res_data->MyConnections) && empty($res_data->MyGroups) && empty($res_data->SharedGroups) && empty($res_data->SmartGroups)) {
                $res = 'Public';
            } else {
                $list_update['is_private'] = 1;
                foreach ($res_data as $rid => $rdata):
                    if (!empty($rdata)) {
                        $res .= '<div class="input-inner">';
                        $res .= '<span class="inflo_share_group_name">' . $rid . '</span>';
                        $res .= '<div class="inflo_share_group_details">';
                        foreach ($rdata as $rd):
                            $res .= '<span class="inflo_share_group_details_span">';
                            $res .= $rd->Name;
                            $res .= '</span>';
                        endforeach;
                        $res .= '</div>';
                        $res .= '</div>';
                    }
                endforeach;
//                     p($response);
            }
            $public_update = $this->ListsModel->update_list_data($list_id, $list_update);
        }
        echo $res;
//            p($response);
        exit;
    }

    /*
     * Get list description
     * @author SG
     */

    public function get_list_desc() {
        if ($this->input->post()) {
            if ($this->input->post('list_id') && $this->input->post('list_id') > 0) {
                $desc = $this->ListsModel->list_desc_get($this->input->post('list_id'));
                if($this->input->post('get_type') && $this->input->post('get_type') == 'get'){
                    $desc = preg_replace('$(\s|^)(https?://[a-z0-9_./?=&-]+)(?![^<>]*>)$i', ' <a href="$2" target="_blank">$2</a> ', nl2br($desc));
                    $desc = preg_replace('$(\s|^)(www\.[a-z0-9_./?=&-]+)(?![^<>]*>)$i', ' <a href="$2" target="_blank">$2</a> ', nl2br($desc));
                }
            } else {
                $desc = 'error';
            }
            echo $desc;
        }
        exit;
    }

    /*
     * Update List Description
     * @author: SG
     */

    public function update_list_desc() {
        if ($this->input->post()) {
            if ($this->input->post('list_id') == 0) {
                echo 'not found';
                exit;
            }
            if ($this->input->post('list_desc') == '') {
                echo 'empty';
                exit;
            }
            $desc_update = $this->ListsModel->save_list_desc($this->input->post('list_desc'), $this->input->post('list_id'));

            if ($desc_update == 1) {
                $update_list['modified'] = date('Y-m-d H:i:s');
                $this->ListsModel->update_list_data($this->input->post('list_id'), $update_list);
                
                $list_desc = $this->input->post('list_desc');
                $list_desc = preg_replace('$(\s|^)(https?://[a-z0-9_./?=&-]+)(?![^<>]*>)$i', ' <a href="$2" target="_blank">$2</a> ', nl2br($list_desc));
                $list_desc = preg_replace('$(\s|^)(www\.[a-z0-9_./?=&-]+)(?![^<>]*>)$i', ' <a href="$2" target="_blank">$2</a> ', nl2br($list_desc));
                echo htmlentities($this->security->xss_clean($list_desc));
            } else {
                echo 'fail';
            }
        }
        exit;
    }

    /**
     * Get Comment Details
     * @author SG
     */
    public function get_comment_data() {
        if ($this->input->post()) {
            if (empty($this->input->post('comment_id')) || empty($this->input->post('list_id'))) {
                echo 'empty';
                exit;
            }

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $list_owner = $this->ListsModel->getListOwner($this->input->post('list_id'));
            $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($this->input->post('list_id'));

            $owner_find = 0;
            if (isset($_SESSION['id'])) {
                $owner_find = $_SESSION['id'];
            }
            if ($list_owner != $owner_find) {
                $priviladge_get['Apikey'] = API_KEY;
                $priviladge_get['Listid'] = $list_inflo_id;
                $priviladge_get['userid'] = 0;
                if (isset($_SESSION['id'])) {
                    $priviladge_get['userid'] = $_SESSION['id'];
                }
                $priviladge_get = json_encode($priviladge_get);

                $ch_priviledge = curl_init();
                curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
                curl_setopt($ch_priviledge, CURLOPT_POST, 1);
                curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
                curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
                $server_output_priviledge = curl_exec($ch_priviledge);
                $response_priviledge = (array) json_decode($server_output_priviledge);
                //        p($response_priviledge); exit;
                $allowed_access = 0;
                if (isset($response_priviledge['success'])) {
                    if ($response_priviledge['success'] == 1) {
                        $allowed_access = 1;
                    }
                    if (isset($response_priviledge['data']) && ($response_priviledge['data']->IsPublic == TRUE && $response_priviledge['data']->IsLocked == TRUE)) {
                        $allowed_access = 2;
                    }
                }

                if ($allowed_access == 0) {
                    echo 'unauthorized';
                    exit;
                }
            }


            $comment_id = $this->input->post('comment_id');
            $list_id = $this->input->post('list_id');
            $found_data = $this->TasksModel->get_attendance_extra_by_id($comment_id, $list_id);
            if (!empty($found_data)) {
                echo json_encode($found_data);
            } else {
                echo 'not found';
            }
        }
        exit;
    }

    /**
     * Update Comment Details
     * @author SG
     */
    public function update_comment_data() {
        if ($this->input->post()) {
            if (empty($this->input->post('comment_id')) || empty($this->input->post('list_id'))) {
                echo 'empty';
                exit;
            }
            $comment_id = $this->input->post('comment_id');
            $list_id = $this->input->post('list_id');
            $comment = $this->input->post('comment');
            $save_data = $this->TasksModel->update_attendance_extra_comment($comment_id, $list_id, $comment);
            if ($save_data > 0) {
                echo $comment;
            } else {
                echo 'not found';
            }
        }
        exit;
    }

    /**
     * Get last check time Details
     * @author SG
     */
    public function get_check_time() {
        if ($this->input->post()) {
            if (empty($this->input->post('attendance_data_ids')) || empty($this->input->post('list_id'))) {
                echo 'empty';
                exit;
            }
            $attendance_data_ids = $this->input->post('attendance_data_ids');
            $list_id = $this->input->post('list_id');

            $get_data = $this->TasksModel->get_last_checked($attendance_data_ids, $list_id);

            $res_data = array();
            if (!empty($get_data)) {
                $indx = 0;
                foreach ($get_data as $data):
                    if (!empty($data['check_date'])) {
                        $hourdiff = round((strtotime(date('Y-m-d H:i:s')) - strtotime($data['check_date'])) / 3600, 1);
                        $time = $data['check_date'];
                        if ($hourdiff > 1 && $hourdiff < 24) {
                            if (floor($hourdiff) > 1) {
                                $hrs = ' hours';
                            } else {
                                $hrs = ' hour';
                            }
                            $time = floor($hourdiff) . $hrs . ' ago';
                        } elseif ($hourdiff <= 1) {
                            $min_dif = $hourdiff * 60;
                            if ($min_dif > 1) {
                                if (floor($min_dif) > 1) {
                                    $minutes = ' minutes';
                                } else {
                                    $minutes = ' minute';
                                }
                                $time = floor($min_dif) . $minutes . ' ago';
                            } else {
                                $time = 'Just Now';
                            }
                        }
                        $res_data[$indx]['id'] = $data['id'];
                        $res_data[$indx]['val'] = $time;
                        $indx++;
                    }
                endforeach;
            }

            echo json_encode($res_data);
            exit;

//            if($save_data > 0){
//                echo $comment;
//            }else{
//                echo 'not found';
//            }
        }
        exit;
    }

    /*
     * Helo page for bulk data entry
     * @author SG
     */

    public function help_add_bulk() {
        $data['title'] = 'Add Bulk Data Help';
        $data['config']['allow_move'] = 'False';
        $this->template->load('default_template', 'task/help_add_bulk', $data);
    }

    /*
     * Get random items from list for Random list
     * @author SG
     */

    public function next_item_random() {
        if ($this->input->post()) {
            $first_col = $this->TasksModel->find_first_column($this->input->post('Listid'));
            $today_date = date('Y-m-d H:i:s');
            $items = $this->TasksModel->get_tasks_first_col($this->input->post('Listid'), $first_col);
            $last_updated_order = 0;
            $item_size = sizeof($items);
            $current_order_store = array();
            $new_order_store = array();
            array_push($current_order_store, $items[0]['TaskId']);
//            p($current_order_store); exit;

            for ($i = 1; $i < $item_size; $i++) {
                array_push($current_order_store, $items[$i]['TaskId']);
                $item_id = $items[$i]['TaskId'];
                $new_ord['order'] = $i;
//                $updated = $this->TasksModel->update_task_data($this->input->post('Listid'), $item_id, $new_ord);
                $last_updated_order = $i;
                array_push($new_order_store, $items[$i]['TaskId']);
            }
            array_push($new_order_store, $items[0]['TaskId']);

//            $save_history['list_inflo_id'] = $this->input->post('Listid');
            $save_history['list_id'] = $this->input->post('Listid');
            if (isset($_SESSION['logged_in'])) {
                $save_history['user_id'] = $_SESSION['id'];
            }
            $log_user = 'anonymous';
            if (isset($_SESSION['first_name'])) {
                $log_user = $_SESSION['first_name'];
                if (isset($_SESSION['last_name'])) {
                    $log_user .= ' ' . $_SESSION['last_name'];
                }
            }
            $save_history['user_name'] = $log_user;
            $last_log = $this->TasksModel->get_log_last_details($this->input->post('Listid'));
            $temp_items = $items;

            if (!empty($last_log)) {
                $item_order = $this->TasksModel->get_item_order($last_log['new_order']);
                foreach ($temp_items as $tid => $titem):
                    if ($titem['order'] == $item_order['order']) {
                        unset($temp_items[$tid]);
                    }
                endforeach;
                $save_history['old_order'] = $last_log['new_order'];
            } else {
                $save_history['old_order'] = $temp_items[0]['TaskId'];
                unset($temp_items[0]);
            }
            $k = array_rand($temp_items);
            $save_history['new_order'] = $temp_items[$k]['TaskId'];


//            p($temp_items); exit;
//            if (!empty($last_log)) {
//                $item_order = $this->TasksModel->get_item_order($last_log['new_order']);
//                $save_history['old_order'] = $last_log['new_order'];
//                $next_item = $this->TasksModel->find_next_random_for_log($item_order['order'], $this->input->post('Listid'));
//                p($next_item); exit;
//                if (empty($next_item)) {
//                    $next_item['id'] = $items[0]['TaskId'];
//                }
//                
//                $save_history['new_order'] = $next_item['id'];
//            } else {
//                $save_history['old_order'] = $items[0]['TaskId'];
//                $temp_items = $items;
//                unset($temp_items[0]);
//                $k = array_rand($temp_items);
//                $save_history['new_order'] = $temp_items[$k]['TaskId'];
//            }
//            p($save_history); exit;

            if ($this->input->post('comment')) {
                $save_history['comment'] = $this->input->post('comment');
            }
            $save_history['nexup_type'] = 3;
            $save_history['created'] = $today_date;
            $save_history['modified'] = $today_date;
            if ($this->input->post('user_ip')) {
                $save_history['user_ip'] = $this->input->post('user_ip');
            }


            $store_history = $this->TasksModel->save_history($save_history);

//            $new_order['order'] = $last_updated_order + 1;
//            $updated_final = $this->TasksModel->update_task_data($this->input->post('Listid'), $this->input->post('Taskid'), $new_order);
            if ($store_history > 0) {
                $update_list['modified'] = date('Y-m-d H:i:s');
                $this->ListsModel->update_list_data($this->input->post('Listid'), $update_list);
                $save_history_array = $save_history['new_order'];
                echo $save_history_array;
            } else {
                echo 'fail';
            }
            exit;
        }
    }

    /*
     * Generate Preview
     * @author SG
     */

    public function generate_preview() {
        SetUp::init();
        $text = $_POST["text"];
        $imageQuantity = 1;
        $text = " " . str_replace("\n", " ", $text);
        $header = "";

        $linkPreview = new LinkPreview();
        $answer = $linkPreview->crawl($text, $imageQuantity, $header);

        echo $answer;

        SetUp::finish();
    }
    
    /*
     * Function to export bulk data as csv
     * @author: SG
     */
    public function export_bulk(){
//        if($this->input->post()){
            $data_to_export = $_GET['export_data'];
            $export_data = base64_decode($data_to_export);
            $export_data = preg_replace('/\n/', PHP_EOL, $export_data);
            $file_export = iconv("windows-1254", "utf8", $export_data);
            $filename = 'BulkData';
            header('Content-type: text/csv; charset: UTF-8');
            header('Content-Encoding: UTF-8');
            header("Content-Disposition: attachment; filename=" . $filename . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo($file_export);
//        }
    }
    
    public function test_index(){
        echo html_entity_decode($this->security->xss_clean('Hiren&lt;hda@narola.email&gt;;Baber&lt;drghauri@gmail.com&gt;;Nikita&lt;ng@narola.email&gt;;Suresh&lt;sd@narola.email&gt;\nHiren&lt;hda@narola.email&gt;;Baber&lt;drghauri@gmail.com&gt;;Nikita&lt;ng@narola.email&gt;;Suresh&lt;sd@narola.email&gt;')); exit;
        
    }

}
