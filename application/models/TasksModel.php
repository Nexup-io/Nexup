<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class TasksModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /*
     * Add multiple tasks togather
     * @author SG
     */

    public function add_task($data) {
        $this->db->insert_batch('list_data', $data);
        return $this->db->affected_rows();
    }

    /*
     * Update task of list
     * @author SG
     */

    public function update_task($task_id, $data) {
        $condition = array('id' => $task_id);
        $this->db->where($condition);
        return $this->db->update('list_data', $data);
    }

    /*
     * Add single task
     * @author SG
     */

    public function add_single_task($data) {
        $this->db->insert('list_data', $data);
        return $this->db->insert_id();
    }

    /**
     * Get list name from slug
     * @author SG
     */
    public function find_list_id($id) {
        $condition = array('id' => $id);
        $rst = $this->db->select('*');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        return $query->row_array();
    }

    /**
     * Get task list
     * @author SG
     */
    public function get_tasks2($list_id) {
        $condition = array('list_data.list_id' => $list_id, 'list_data.is_deleted' => 0);
        $rst = $this->db->select('list_data.id as TaskId, value as TaskName, is_completed as IsCompleted, is_present as IsPresent, list_data.order as order, column_id, list_columns.order as col_order');
        $this->db->join('list_columns', 'list_columns.id = list_data.column_id', 'left');
        $this->db->where($condition);
        $this->db->order_by('order asc, list_columns.order asc, column_id asc');
        $query = $this->db->get('list_data');
        return $query->result_array();
    }
    /**
     * Get task list
     * @author SG
     */
    public function get_tasks($list_id) {
        $condition = array('list_data.list_id' => $list_id, 'list_data.is_deleted' => 0);
        $rst = $this->db->select('list_data.id as TaskId, value as TaskName, is_completed as IsCompleted, is_present as IsPresent, list_data.order as order, column_id, list_columns.order as col_order, attendance_data.id as attendance_id, attendance_data.item_ids, attendance_data.comment, attendance_data.check_date');
        $this->db->join('list_columns', 'list_columns.id = list_data.column_id', 'left');
        $this->db->join('attendance_data', 'attendance_data.is_deleted = 0 AND FIND_IN_SET(list_data.id,attendance_data.item_ids) !=0', 'left');
        $this->db->where($condition);
            $this->db->order_by('order asc, list_columns.order asc, column_id asc');
        $query = $this->db->get('list_data');
        return $query->result_array();
    }
    
    public function get_tasks_2($list_id, $sort = NULL) {
        $query = "SELECT `list_data`.`id` as `TaskId`, `list_data`.`user_id` as `UserId`, `value` as `TaskName`, list_columns.type, list_columns.height, `is_completed` as `IsCompleted`, `is_present` as `IsPresent`, `list_data`.preview_meta as preview_meta, `list_data`.`order` as `order`, `column_id`, `list_columns`.`order` as `col_order`, `attendance_data`.`id` as `attendance_id`, `attendance_data`.`item_ids`, `attendance_data`.`comment`, `attendance_data`.`check_date` FROM `list_data` LEFT JOIN `list_columns` ON `list_columns`.`id` = `list_data`.`column_id` LEFT JOIN `attendance_data` ON `attendance_data`.`is_deleted` = 0 AND FIND_IN_SET(list_data.id,attendance_data.item_ids) !=0 WHERE `list_data`.`list_id` = '" . $list_id . "' AND `list_data`.`is_deleted` =0 ORDER BY " . $sort;
        return $this->db->query($query)->result_array();
    }
    
    
    public function get_tasks_for_calendar($list_id, $column_ids, $limit = 0) {
        $add_limit = '';
        if($limit > 0){
            $add_limit = ' LIMIT ' . $limit;
        }
        $query = "SELECT `list_data`.`id` as `TaskId`, `list_data`.`user_id` as `UserId`, `value` as `TaskName`, list_columns.type, list_columns.height, `is_completed` as `IsCompleted`, `is_present` as `IsPresent`, `list_data`.preview_meta as preview_meta, `list_data`.`order` as `order`, `column_id`, `list_columns`.`order` as `col_order`, `attendance_data`.`id` as `attendance_id`, `attendance_data`.`item_ids`, `attendance_data`.`comment`, `attendance_data`.`check_date` FROM `list_data` LEFT JOIN `list_columns` ON `list_columns`.`id` = `list_data`.`column_id` LEFT JOIN `attendance_data` ON `attendance_data`.`is_deleted` = 0 AND FIND_IN_SET(list_data.id,attendance_data.item_ids) !=0 WHERE `list_data`.`list_id` = '" . $list_id . "' AND `list_data`.`is_deleted` =0 AND `list_data`.`column_id` IN (" . implode(',', $column_ids) . ")" . $add_limit;
        return $this->db->query($query)->result_array();
    }
    
    

    /*
     * Get task name
     * @author SG
     */

    public function getTaskByTaskId($taks_id) {
        $condition = array('list_data.id' => $taks_id, 'list_data.is_deleted' => 0);
        $rst = $this->db->select('value as TaskName');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        $task = $query->row_array();
        return $task['TaskName'];
//        p($task); exit;
    }

    /*
     * Get list types
     * @author SG
     */

    public function getListTypes() {
        $rst = $this->db->select('id as ListTypeId, type_name as ListTypeName, icon, is_active, is_actionable');
        $this->db->order_by('order asc, is_actionable asc, is_active desc');
        $query = $this->db->get('list_types');
        return $query->result_array();
    }

    /*
     * Update task data
     * @author SG
     */

    public function update_task_data($list_id, $task_id, $task_data) {
        $condition = array('list_id' => $list_id, 'id' => $task_id);
        $this->db->where($condition);
        return $this->db->update('list_data', $task_data);
    }

    /*
     * Remove task data
     * @author SG
     */

    public function remove_task_data($task_id) {
        $condition = array('task_inflo_id' => $task_id);
        $this->db->where($condition);
        $task_data['is_deleted'] = 1;
        return $this->db->update('list_data', $task_data);
    }

    /*
     * Remove multimple task data
     * @author SG
     */

    public function remove_task_data_bulk($task_ids) {
        $this->db->where_in('id', $task_ids);
        $task_data['is_deleted'] = 1;
        $task_data['order'] = 0;
        return $this->db->update('list_data', $task_data);
    }

    /*
     * Remove from nexup list
     * @author SG
     */

    public function remove_data_nexup($task_ids) {
        $this->db->where_in('new_order', $task_ids);
        $task_data['is_undone'] = 1;
        return $this->db->update('cycle_history', $task_data);
    }

    public function get_last_order_of_item($list_id) {
        $condition = array('list_id' => $list_id, 'is_deleted' => 0);
        $this->db->select_max('order');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        $res = $query->row_array();
        return $res['order'];
    }

    /*
     * Save history of cycle through list items
     * @author SG
     */

    public function save_history($data) {
        $this->db->insert('cycle_history', $data);
        return $this->db->insert_id();
    }

    /*
     * Find log of advance of list items by list id for round robbin list
     * @author SG
     */

    public function find_log($list_id) {
        $condition = array('cycle_history.list_id' => $list_id, 'nexup_type' => 1);
        $rst = $this->db->select('cycle_history.list_id as list_id, cycle_history.user_id as user_id, cycle_history.user_name as user_name, old_order, new_order, comment, user_ip, cycle_history.created as created, list_data.value');
        $this->db->join('list_data', 'cycle_history.new_order = list_data.id', 'left');
        $this->db->where($condition);
        $this->db->order_by('cycle_history.id', 'desc');
        $query = $this->db->get('cycle_history');
        return $query->result_array();
    }
    
    
    /*
     * Find log of advance of list items by list id for random list
     * @author SG
     */

    public function find_log_random($list_id) {
        $condition = array('cycle_history.list_id' => $list_id, 'nexup_type' => 3);
        $rst = $this->db->select('cycle_history.list_id as list_id, cycle_history.user_id as user_id, cycle_history.user_name as user_name, old_order, new_order, comment, user_ip, cycle_history.created as created, list_data.value');
        $this->db->join('list_data', 'cycle_history.new_order = list_data.id', 'left');
        $this->db->where($condition);
        $this->db->order_by('cycle_history.id', 'desc');
        $query = $this->db->get('cycle_history');
        return $query->result_array();
    }
    

    /*
     * Find last log of advance of list items by list id
     * @author SG
     */

    public function find_last_log($list_id) {
        $condition = array('list_id' => $list_id, 'is_undone' => 0, 'is_undo' => 0);
        $rst = $this->db->select('id,list_id, user_id, old_order, new_order, comment, user_ip, created');
        $this->db->where($condition);
        $this->db->limit(1);
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('cycle_history');
        return $query->result_array();
    }

    /*
     * Update log
     * @author SG
     */

    public function update_log($id, $data) {
        $condition = array('id' => $id);
        $this->db->where($condition);
        return $this->db->update('cycle_history', $data);
    }

    /*
     * Get current order of items
     * @author SG
     */

    public function get_current_item_order($list_id) {
        $condition = array('list_id' => $list_id,);
        $rst = $this->db->select('id');
        $this->db->where($condition);
        $this->db->order_by('order', 'asc');
        $query = $this->db->get('list_data');
        return $query->result_array();
    }

    /*
     * Add new column in list
     * @author SG
     */

    public function add_new_colum($data) {
        $this->db->insert('list_columns', $data);
        return $this->db->insert_id();
    }

    /*
     * Find highest order of column
     * @author SG
     */

    public function get_max_column_order($list_id) {
        $condition = array('list_id' => $list_id, 'is_deleted' => 0);
        $this->db->select_max('order');
        $this->db->where($condition);
        $query = $this->db->get('list_columns');
        $res = $query->row_array();
        return $res['order'];
    }

    /*
     * Get column wise tasks
     * @author SG
     */

    public function get_tasks_by_columns_order($list_id) {
        $condition = array('list_data.list_id' => $list_id, 'list_data.is_deleted' => 0, 'list_columns.is_deleted' => 0);
        $rst = $this->db->select('value as TaskName, list_data.id as TaskId, is_completed as IsCompleted, list_data.order as order, column_id, list_columns.column_name as column_name');
        $this->db->join('list_columns', 'list_columns.id = list_data.column_id', 'left');
        $this->db->where($condition);
        $this->db->order_by('list_data.order', 'asc');
        $query = $this->db->get('list_data');
        return $query->result_array();
    }

    /*
     * Get max ordered column for a list
     * @author SG
     */

    public function FindColumnMaxOrder($list_id) {
        $condition = array('list_id' => $list_id, 'is_deleted' => 0);
        $this->db->select('IF(MAX(`order`) IS NULL,0,MAX(`order`)) AS `order`');
        $this->db->where($condition);
        $query = $this->db->get('list_columns');
        $res = $query->row_array();
        return $res['order'];
    }

    /*
     * Update column order for list when there is single column list and user add new column
     * @author SG
     */

    public function UpdateColumnOrder($list_id, $col_id) {
        $condition = array('list_id' => $list_id, 'column_id' => 0);
        $this->db->where($condition);
        $data['column_id'] = $col_id;
        return $this->db->update('list_data', $data);
    }

    /*
     * Get columns for a list
     * @author SG
     */

    public function getColumns($list_id) {
        $condition = array('list_id' => $list_id, 'is_deleted' => 0);
        $this->db->select('id, column_name, order, type, height');
        $this->db->where($condition);
        $this->db->order_by('order', 'asc');
        $query = $this->db->get('list_columns');
        return $query->result_array();
    }

    /*
     * Get column name from id
     * @author SG
     */

    public function getColumnNameById($list_id, $col_id) {
        $condition = array('list_id' => $list_id, 'id' => $col_id, 'is_deleted' => 0);
        $this->db->select('column_name');
        $this->db->where($condition);
        $query = $this->db->get('list_columns');
        $data = $query->row_array();
        return $data['column_name'];
    }

    /*
     * Update the column name
     * @author: SG
     */

    public function updateColumnName($list_id, $col_id, $update_data) {
        $condition = array('id' => $col_id, 'list_id' => $list_id);
        $this->db->where($condition);
        return $this->db->update('list_columns', $update_data);
    }

    /*
     * Update column order for multi column list
     * @author SG
     */

    public function update_column_data($list_id, $col_id, $col_data) {
        $condition = array('list_id' => $list_id, 'id' => $col_id);
        $this->db->where($condition);
        return $this->db->update('list_columns', $col_data);
    }

    /*
     * Delete Column
     * @author SG
     */

    public function delete_column($list_id, $column_id) {
        $condition = array('list_id' => $list_id, 'id' => $column_id);
        $del_data['is_deleted'] = 1;
        $this->db->where($condition);
        return $this->db->update('list_columns', $del_data);
    }
    
    /*
     * Delete task related to column which is deleted
     * @author SG
     */

    public function remove_items($list_id, $column_id) {
        $condition = array('list_id' => $list_id, 'id' => $column_id);
        $del_data['is_deleted'] = 1;
        $this->db->where($condition);
        return $this->db->update('list_data', $del_data);
    }

    /*
     * Remove item column wise
     * @authos SG
     */

    public function remove_items_column($list_id, $column_id) {
        $condition = array('list_id' => $list_id, 'column_id' => $column_id);
        $del_data['is_deleted'] = 1;
        $this->db->where($condition);
        return $this->db->update('list_data', $del_data);
    }

    /*
     * Get tasks by order
     * @author SG
     */

    public function get_tasks_by_order($order, $list_id) {
        $condition = array('order' => $order, 'list_id' => $list_id);
        $rst = $this->db->select('id, order');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        return $query->result_array();
    }

    /*
     * Find task order and list id by it's id
     * @author SG
     */

    public function get_task_order($task_id) {
        $condition = array('id' => $task_id);
        $rst = $this->db->select('id, task_inflo_id, order, list_id, list_inflo_id');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        return $query->row_array();
    }

    /*
     * Find tasks which has same order as the task for which id is passed
     * @author SG
     */

    public function get_similar_tasks($list_id, $order) {
        $condition = array('list_id' => $list_id, 'order' => $order, 'is_deleted' => 0);
        $rst = $this->db->select('task_inflo_id, id');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        return $query->result_array();
    }

    /*
     * Find Last Cycle History
     * @author SG
     */

    public function get_log_last($list_id, $nexup_type = NULL) {
        if($nexup_type == NULL){
            $nexup_type = 1;
        }
        $condition = array('list_id' => $list_id, 'is_undo' => 0, 'is_undone' => 0, 'nexup_type' => $nexup_type);
        $rst = $this->db->select('new_order');
        $this->db->where($condition);
        $this->db->limit(1);
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('cycle_history');
        return $query->row_array();
    }

    /*
     * Find Last Cycle History
     * @author SG
     */

    public function get_log_last_details($list_id) {
        $condition = array('list_id' => $list_id, 'is_undo' => 0, 'is_undone' => 0);
        $rst = $this->db->select('new_order, old_order');
        $this->db->where($condition);
        $this->db->limit(1);
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('cycle_history');
        return $query->row_array();
    }

    /*
     * Find next item to store in log
     * @author: SG
     */

    public function find_next_for_log($order, $list_id) {
        $condition = array('list_data.order >' => $order, 'list_data.is_deleted' => 0, 'list_data.list_id' => $list_id, 'list_data.id >' => 0);
        $rst = $this->db->select('list_data.id');
        $this->db->join('list_columns', 'list_columns.id = list_data.column_id', 'left');
        $this->db->where($condition);
        $this->db->limit(1);
        $this->db->order_by('list_columns.order asc, list_data.order asc, column_id asc');
//        $this->db->order_by('column_id asc');
        $query = $this->db->get('list_data');
        return $query->row_array();
//        echo $this->db->last_query(); exit;
    }
    
    /*
     * Find next random item to store in log
     * @author: SG
     */

    public function find_next_random_for_log($order, $list_id) {
        $condition = array('list_data.order !=' => $order, 'list_data.is_deleted' => 0, 'list_data.list_id' => $list_id, 'list_data.id >' => 0);
        $rst = $this->db->select('list_data.id');
        $this->db->join('list_columns', 'list_columns.id = list_data.column_id', 'left');
        $this->db->where($condition);
        $this->db->order_by('list_columns.order asc, list_data.order asc, column_id asc');
        $query = $this->db->get('list_data');
        $res = $query->result_array();
        $resp = array_column($res, 'id');
        shuffle($resp);
        return $resp[0];
    }

    /*
     * Get order of item
     * @author: SG
     */

    public function get_item_order($item_id) {
        $condition = array('id' => $item_id);
        $rst = $this->db->select('order');
        $this->db->where($condition);
        $this->db->limit(1);
        $this->db->order_by('order', 'asc');
        $query = $this->db->get('list_data');
        return $query->row_array();
    }

    /*
     * Complete the task
     * @author SG
     */

    public function complete_task($task_ids, $list_id, $mark_status) {
        $condition = 'id IN ' . $task_ids . ' AND is_deleted = 0';
//         $this->db->where_in('task_inflo_id', $task_ids);
        $task_data['is_completed'] = $mark_status;
        $this->db->where($condition);
        return $this->db->update('list_data', $task_data);
//        echo $this->db->last_query(); exit;
    }

    /*
     * Check if item is deleted or not
     * @author SG
     */

    public function get_task_details($task_id, $list_id) {
        $condition = array('id' => $task_id, 'list_id' => $list_id);
        $rst = $this->db->select('is_deleted');
        $this->db->where($condition);
        $this->db->limit(1);
        $this->db->order_by('order', 'asc');
        $query = $this->db->get('list_data');
        $data = $query->row_array();
        return $data['is_deleted'];
    }

    /*
     * Get item similar order to log item which was deleted
     * @author SG
     */

    public function get_similar_item($order, $list_id) {
        $condition = array('list_data.order' => $order, 'list_data.is_deleted' => 0, 'list_columns.is_deleted' => 0, 'list_data.list_id' => $list_id);
        $rst = $this->db->select('list_data.id as TaskId');
        $this->db->join('list_columns', 'list_columns.id = list_data.column_id', 'left');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get('list_data');
        $this->db->order_by('list_columns.order asc');
        return $query->row_array();
    }

    /*
     * Mark task as present
     * @author SG
     */

    public function present_task($task_ids, $list_id, $mark_status, $date_today) {
        $this->db->where('id IN (' . $task_ids . ')');
//        $this->db->where_in('id', $task_ids);
        $task_data['is_present'] = $mark_status;
//        $task_data['modified'] = $date_today;
        return $this->db->update('list_data', $task_data);
    }

    /*
     * Find Last Cycle History
     * @author SG
     */

    public function get_present($list_id) {
        $condition = array('list_id' => $list_id);
        $rst = $this->db->select('id, is_present');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        return $query->result_array();
    }

    /*
     * Update SLUG of a list
     */

    public function update_slug($list_id, $new_slug) {
        $this->db->where('id', $list_id);
        $update_slug['slug'] = $new_slug;
        $update_slug['url'] = '/' . $new_slug;
        return $this->db->update('lists', $update_slug);
    }

    /*
     * Find first column of list
     * @author SG
     */

    public function find_first_column($list_id) {
        $condition = array('list_id' => $list_id, 'is_deleted' => 0);
        $rst = $this->db->select('id');
        $this->db->where($condition);
        $this->db->limit(1);
        $this->db->order_by('order asc');
        $query = $this->db->get('list_columns');
        $result = $query->row_array();
        return $result['id'];
    }
    
    /*
     * Find second column of list
     * @author SG
     */

    public function find_second_column($list_id) {
        $condition = array('list_id' => $list_id, 'is_deleted' => 0, 'order' => 2);
        $rst = $this->db->select('id');
        $this->db->where($condition);
        $this->db->limit(1);
        $this->db->order_by('order asc');
        $query = $this->db->get('list_columns');
        $result = $query->row_array();
        return $result['id'];
    }

    /*
     * Find order for items logged in cycle history
     * @author SG
     */

    public function find_orders_log($list_id, $log_ids) {
        $rst = $this->db->select('list_data.order');

        $this->db->join('list_data as lt', 'lt.task_inflo_id = list_data.task_inflo_id', 'left');
        $condition = array('list_data.list_id' => $list_id, 'lt.list_id' => 'list_data.id');

        $this->db->where($condition);
        $this->db->where_in('list_data.id', $log_ids);
        $logs = implode(',', $log_ids);
        $this->db->order_by('FIELD ( list_data.id, ' . $logs . ' )');
        $query = $this->db->get('list_data');
        return $query->result_array();
    }

    /*
     * Find log of advance of list items by list id to export in excell
     * @author SG
     */

    public function find_log_export($list_id, $orders, $first_column_id) {
        $condition = array('list_id' => $list_id, 'column_id' => $first_column_id);
        $rst = $this->db->select('id, value');
        $this->db->where($condition);
        $this->db->where_in('order', $orders);
        $orders_str = implode(',', $orders);
        $this->db->order_by('FIELD ( `order`, ' . $orders_str . ' )');
        $query = $this->db->get('list_data');
        return $query->result_array();
    }

    /*
     * Find order for single item
     * @author SG
     */

    public function find_order_log_single($list_id, $log_id) {
        $condition = array('list_id' => $list_id, 'id' => $log_id);
        $rst = $this->db->select('list_data.order');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        $resp = $query->row_array();
        return $resp['order'];
    }

    /*
     * Find first item in row
     * @author SG
     */

    public function find_item_first($list_id, $order_current_item, $first_column_id) {
        $condition = array('list_id' => $list_id, 'order' => $order_current_item, 'column_id' => $first_column_id);
        $rst = $this->db->select('value');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        $resp = $query->row_array();
        return $resp['value'];
    }

    /*
     * Get List id from task id
     * @author SG
     */

    public function getListIdByTaskId($task_id) {
        $condition = array('list_data.id' => $task_id, 'list_data.is_deleted' => 0);
        $rst = $this->db->select('list_id');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        $task = $query->row_array();
        return $task['list_id'];
    }

    /*
     * Get List user id of a list
     * @author SG
     */

    public function getListUserId($list_id) {
        $condition = array('id' => $list_id, 'is_deleted' => 0);
        $rst = $this->db->select('user_id');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $list = $query->row_array();
        return $list['user_id'];
    }

    /*
     * Get List user id of a list by slug
     * @author SG
     */

    public function getListUserIdBySlug($list_slug) {
        $condition = array('slug' => $list_slug, 'is_deleted' => 0);
        $rst = $this->db->select('user_id');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $list = $query->row_array();
        return $list['user_id'];
    }

    /*
     * Get lock status of list
     * @author SG
     */

    public function get_lock_list_status($list_id) {
        $condition = array('id' => $list_id);
        $rst = $this->db->select('is_locked, user_id');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        return $query->row_array();
    }

    /*
     * Get all items of list
     * @author SG
     */

    public function get_all_items($list_id) {
        $condition = array('list_data.list_id' => $list_id, 'list_data.is_deleted' => 0);
        $rst = $this->db->select('value as TaskName, list_data.id as TaskId, is_completed as IsCompleted, is_present as IsPresent, list_data.order as order, column_id, list_columns.order as col_order');
        $this->db->join('list_columns', 'list_columns.id = list_data.column_id', 'left');
        $this->db->where($condition);
        $this->db->order_by('order asc, list_columns.order asc, column_id asc');
        $query = $this->db->get('list_data');
        return $query->result_array();
    }
    
    /*
     * Get all items of list sort by order
     * @author SG
     */

    public function get_all_items_ordered($list_id) {
        $condition = array('list_data.list_id' => $list_id, 'list_data.is_deleted' => 0);
        $rst = $this->db->select('value as TaskName, is_completed as IsCompleted, is_present as IsPresent, list_data.order as order, list_columns.order as col_order');
        $this->db->join('list_columns', 'list_columns.id = list_data.column_id', 'left');
        $this->db->where($condition);
        $this->db->order_by('order asc');
        $query = $this->db->get('list_data');
        return $query->result_array();
    }

    /*
     * Delete all dtaa of list
     * @author SG
     */

    public function delete_task_bulk($list_id) {
        $condition = array('list_id' => $list_id);
        $this->db->where($condition);
        $data['is_deleted'] = 1;
        return $this->db->update('list_data', $data);
    }

    /*
     * Delete all columns of list
     * @author SG
     */

    public function delete_columns_bulk($list_id) {
        $condition = array('list_id' => $list_id);
        $this->db->where($condition);
        $data['is_deleted'] = 1;
        return $this->db->update('list_columns', $data);
    }

    /*
     * Reorder columns after deleting one column
     * @author SG
     */

    public function reorder_cols($cols) {
        return $this->db->update_batch('list_columns', $cols, 'id');
    }

    /*
     * Get last log of nexup for a list
     * @author SG
     */

    public function get_last_log($list_id) {
        $condition = array('list_id' => $list_id, 'is_undo' => 0, 'is_undone' => 0, 'nexup_type' => 1);
        $rst = $this->db->select('new_order');
        $this->db->where($condition);
        $this->db->order_by('id desc');
        $this->db->limit(1);
        $query = $this->db->get('cycle_history');
        $resp = $query->row_array();
        return $resp['new_order'];
    }

    /*
     * Get inflo id for column
     * @author SG
     */

    public function get_col_inflo_id($col_id) {
        $condition = array('is_deleted' => 0, 'id' => $col_id);
        $rst = $this->db->select('col_inflo_id');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get('list_columns');
        $resp = $query->row_array();
        return $resp['col_inflo_id'];
    }

    /*
     * Get tasks by order
     * @author SG
     */

    public function get_task_ids_by_order($order, $list_id) {
        $condition = array('order' => $order, 'list_id' => $list_id);
        $rst = $this->db->select('id');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        return $query->result_array();
    }

    /*
     * Update task orders
     * @author SG
     */

    public function update_task_orders($list_id, $task_id, $order) {
        $condition = 'list_id = ' . $list_id . ' AND id IN(' . $task_id . ') AND is_deleted = 0';
        $this->db->where($condition);
        $task_data['order'] = $order;
        return $this->db->update('list_data', $task_data);
//        echo $this->db->last_query();
    }

    /*
     * Get task id from inflo id
     * @author SG
     */

    public function get_task_id_from_task_inflo_id($task_inflo_id) {
        $condition = array('task_inflo_id' => $task_inflo_id);
        $this->db->select('id');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        $response = $query->row_array();
        return $response['id'];
    }

    /*
     * Get task inflo id from task id
     * @author SG
     */

    public function get_task_inflo_id_from_task_id($task_id) {
        $condition = array('id' => $task_id);
        $this->db->select('task_inflo_id');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        $response = $query->row_array();
        return $response['task_inflo_id'];
    }

    /*
     * Reset attendance list
     * @author SG
     */

    public function reset_attendance_list($list_id) {
        $condition = array('list_id' => $list_id);
        $this->db->where($condition);
        $task_data['is_present'] = 0;
        return $this->db->update('list_data', $task_data);
    }

    /*
     * Reset attendance list
     * @author SG
     */

    public function reset_attendance_data($list_id) {
        $condition = array('list_id' => $list_id, 'is_deleted' => 0);
        $this->db->where($condition);
        $task_data['comment'] = '';
        $task_data['check_date'] = null;
        return $this->db->update('attendance_data', $task_data);
    }

    /*
     * Find count of total yes/no/maybe/blank
     * @author SG
     */

    public function find_count_present($first_col_id = null,$flag_present, $list_id) {
        $condition = array('list_id' => $list_id, 'is_present' => $flag_present, 'is_deleted' => 0);
        if(!empty($first_col_id)){
            $condition['column_id'] = $first_col_id;
        }
        $this->db->select('count(`id`) as total');
//        $this->db->distinct();
//        $this->db->group_by('order');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        $response = $query->row_array();
        return $response['total'];
    }

    /*
     * Get number of columns
     * @author SG
     */

    public function count_col($list_id) {
        $condition = array('list_id' => $list_id, 'is_deleted' => 0);
        $this->db->select('count(`id`) as total');
        $this->db->where($condition);
        $query = $this->db->get('list_columns');
        $response = $query->row_array();
        return $response['total'];
    }

    /*
     * Add entry in comments table attendance_data (for attendance list only)
     * @author SG
     */

    public function add_attendance_data($list_id, $time, $item_ids) {
        $today = date('Y:m:d H:i:s');
        $data['list_id'] = $list_id;
        $data['item_ids'] = $item_ids;
        $data['check_date'] = $time;
        $data['created'] = $today;
        $data['modified'] = $today;
        $this->db->insert('attendance_data', $data);
        return $this->db->insert_id();
    }

    /**
     * Get comments and date of last chec mark for attendance list
     * @author SG
     */
    public function get_attendance_extra($list_id) {
        $condition = array('attendance_data.list_id' => $list_id, 'attendance_data.is_deleted' => 0);
        $rst = $this->db->select('attendance_data.item_ids, attendance_data.comment, attendance_data.check_date');
        $this->db->where($condition);
        $query = $this->db->get('attendance_data');
        return $query->result_array();
    }

    /**
     * Get comments and date of last chec mark for attendance list
     * @author SG
     */
    public function update_maybe_remove($list_id) {
        $condition = array('list_id' => $list_id, 'is_present' => 2);
        $this->db->where($condition);
        $data['is_present'] = 0;
        return $this->db->update('list_data', $data);
    }

    /*
     * Find data from attendance_data
     * @author SG
     */

    public function get_data_extra($list_id, $item_id) {
        $condition = 'attendance_data.list_id = ' . $list_id . ' AND attendance_data.is_deleted = 0 AND FIND_IN_SET(' . $item_id . ',item_ids) !=0';
        $rst = $this->db->select('attendance_data.id , attendance_data.item_ids, attendance_data.comment, attendance_data.check_date');
        $this->db->where($condition);
        $query = $this->db->get('attendance_data');
        return $query->result_array();
    }
    
    /*
     * Add item id in attendance_data when new column is added
     * @author SG
     */
    public function update_extra($list_id, $extra_id, $data){
        $condition = array('list_id' => $list_id, 'id' => $extra_id);
        $this->db->where($condition);
        return $this->db->update('attendance_data', $data);
    }
    
    /*
     * Get all data from attendance_data related to list
     * @author SG
     */
    public function get_all_extra($list_id){
        $condition = 'attendance_data.list_id = ' . $list_id . ' AND is_deleted = 0';
        $rst = $this->db->select('attendance_data.id , attendance_data.item_ids, attendance_data.comment, attendance_data.check_date');
        $this->db->where($condition);
        $query = $this->db->get('attendance_data');
        return $query->result_array();
    }
    
    /*
     * Get comment and date of last checked by attendance data id
     * @author SG
     */
    public function get_attendance_extra_by_id($attend_id,$list_id){
        $condition = array('list_id' => $list_id, 'id' => $attend_id);
        $rst = $this->db->select('attendance_data.id, attendance_data.comment, attendance_data.check_date');
        $this->db->where($condition);
        $query = $this->db->get('attendance_data');
        return $query->row_array();
    }
    
    /*
     * Update comment attendance data
     * @author SG
     */
    public function update_attendance_extra_comment($comment_id,$list_id, $comment){
        $condition = array('list_id' => $list_id, 'id' => $comment_id);
        $data['comment'] = $comment;
        $this->db->where($condition);
        return $this->db->update('attendance_data', $data);
    }
    
    /*
     * Update check time attendance data
     * @author SG
     */
    public function update_check_time($comment_id,$list_id, $date){
        $condition = array('list_id' => $list_id, 'id' => $comment_id);
        $data['check_date'] = $date;
        $this->db->where($condition);
        return $this->db->update('attendance_data', $data);
    }
    
    /*
     * Get time when items were checked last time
     * @author SG
     */
    public function get_last_checked($comment_id,$list_id){
        
        
        $condition = array('list_id' => $list_id, 'id' => $comment_id);
        $rst = $this->db->select('attendance_data.id, attendance_data.check_date');
        $this->db->where($condition);
        $query = $this->db->get('attendance_data');
        return $query->result_array();
        
    }
    
    
    /*
     * Delete all from attendance data
     * @author SG
     */
    public function delete_attendance_data($list_id){
        $condition = array('list_id' => $list_id);
        $data['is_deleted'] = 1;
        $this->db->where($condition);
        return $this->db->update('attendance_data', $data);
    }
    
    /*
     * Find list in user's visited list
     * @author SG
     */
    public function find_visit($user_id, $list_id){
        $condition = array('user_id' => $user_id, 'list_id' => $list_id);
        $rst = $this->db->select('*');
        $this->db->where($condition);
        $query = $this->db->get('visited_lists');
        return $query->row_array();
    }
    
    
    /*
     * Record list in user's visited list
     * @author SG
     */
    public function record_visit($data){
        $this->db->insert('visited_lists', $data);
        return $this->db->insert_id();
    }
    
    /*
     * Find all list visited by user
     * @author SG
     */
    public function find_visited_all($user_id){
        $condition = array('user_id' => $user_id, 'is_deleted' => 0);
        $rst = $this->db->select('DISTINCT(list_id)');
        $this->db->where($condition);
        $query = $this->db->get('visited_lists');
        $this->db->order_by('lists.modified desc');
        return $query->result_array();
    }
    
    /*
     * Delete nexup data
     * @author SG
     */

    public function delete_nexup_data($list_id) {
        $condition = array('list_id' => $list_id);
        $this->db->where($condition);
        return $this->db->delete('cycle_history');
    }
    
    /*
     * Count visit on the list
     * @author SG
     */
    public function count_list_visitors($list_id){
        $condition = array('list_id' => $list_id);
        $this->db->where($condition);
        $rst = $this->db->select('COUNT(id) as total_visit');
        $query = $this->db->get('visited_lists');
        $found_visit = $query->row_array();
        return $found_visit['total_visit'];
    }
    /*
     * Count visit on the list by user
     * @author SG
     */
    public function find_user_visit_count($user_id,$list_id){
        $condition = array('list_id' => $list_id, 'user_id' => $user_id);
        $this->db->where($condition);
        $rst = $this->db->select('COUNT(id) as total_visit');
        $query = $this->db->get('visited_lists');
        $found_visit = $query->row_array();
        return $found_visit['total_visit'];
    }
    
    /*
     * Add visitor to table when any user visit list
     * @author SG
     */
    public function add_visitor($user_id, $list_id){
        $now = date('Y-m-d H:i:s');
        $add_visitor['user_id'] = $user_id;
        $add_visitor['list_id'] = $list_id;
        $add_visitor['date_visited'] = $now;
        $add_visitor['created'] = $now;
        $add_visitor['modified'] = $now;
        $this->db->insert('visited_lists', $add_visitor);
        return $this->db->insert_id();
    }
    
    /*
     * Find list items sort by orders
     * @author SG
     */
    public function find_all_tasks_sort_by_order($list_id){
        $condition = array('list_id' => $list_id, 'is_deleted' => 0);
        $this->db->where($condition);
        $rst = $this->db->select('id, order');
        $this->db->order_by('order asc');
        $query = $this->db->get('list_data');
        return $query->result_array();
    }
    
    /*
     * Find all column ids for the list
     * @author SG
     */
    public function find_all_col_ids($list_id){
        $condition = array('list_id' => $list_id, 'is_deleted' => 0);
        $this->db->where($condition);
        $rst = $this->db->select('id');
        $this->db->order_by('order asc');
        $query = $this->db->get('list_columns');
        return $query->result_array();
    }
    
    /**
     * Get task list for first column
     * @author SG
     */
    public function get_tasks_first_col($list_id, $col_id) {
        $condition = array('list_data.list_id' => $list_id, 'list_data.is_deleted' => 0, 'column_id' => $col_id);
        $rst = $this->db->select('list_data.id as TaskId, value as TaskName, is_completed as IsCompleted, is_present as IsPresent, list_data.order as order, column_id, list_columns.order as col_order, attendance_data.id as attendance_id, attendance_data.item_ids, attendance_data.comment, attendance_data.check_date');
        $this->db->join('list_columns', 'list_columns.id = list_data.column_id', 'left');
        $this->db->join('attendance_data', 'attendance_data.is_deleted = 0 AND FIND_IN_SET(list_data.id,attendance_data.item_ids) !=0', 'left');
        $this->db->where($condition);
            $this->db->order_by('order asc, list_columns.order asc, column_id asc');
        $query = $this->db->get('list_data');
        return $query->result_array();
    }
    
    /**
     * Get list slug by list id
     * @author SG
     */
    public function find_list_slug($id) {
        $condition = array('id' => $id);
        $rst = $this->db->select('slug');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $slug = $query->row_array();
        return $slug['slug'];
    }
    
    /*
     * Get number of columns
     * @author SG
     */

    public function count_items($list_id) {
        $condition = array('list_id' => $list_id, 'is_deleted' => 0);
        $this->db->select('count(`id`) as total');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        $response = $query->row_array();
        return $response['total'];
    }
    
    /*
     * Get order of items marked as yes for attendance list
     * @author SG
     */
    public function get_yes_orders($list_id){
        $condition = array('list_data.list_id' => $list_id, 'is_present' => 1, 'is_deleted' => 0, 'order >' => 0);
        $rst = $this->db->select('distinct(`order`)');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        return $query->result_array();
    }
    
    /*
     * Get order of items marked as maybe for attendance list
     * @author SG
     */
    public function get_maybe_orders($list_id){
        $condition = array('list_data.list_id' => $list_id, 'is_present' => 2, 'is_deleted' => 0, 'order >' => 0);
        $rst = $this->db->select('distinct(`order`)');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        return $query->result_array();
    }
    
    /*
     * Get order of items marked as no for attendance list
     * @author SG
     */
    public function get_no_orders($list_id){
        $condition = array('list_data.list_id' => $list_id, 'is_present' => 3, 'is_deleted' => 0, 'order >' => 0);
        $rst = $this->db->select('distinct(`order`)');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        return $query->result_array();
    }
    
    
    /*
     * Get order of items marked as blank for attendance list
     * @author SG
     */
    public function get_blank_orders($list_id){
        $condition = array('list_data.list_id' => $list_id, 'is_present' => 0, 'is_deleted' => 0, 'order >' => 0);
        $rst = $this->db->select('distinct(`order`)');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        return $query->result_array();
    }
    
    /*
     * Get order of items marked as completed for todo list
     * @author SG
     */
    public function get_completed_orders($list_id){
        $condition = array('list_data.list_id' => $list_id, 'is_completed' => 1, 'is_deleted' => 0, 'order >' => 0);
        $rst = $this->db->select('distinct(`order`)');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        return $query->result_array();
    }
    
    /*
     * Get completed items of first column from list
     * @author SG
     */
    public function get_completed_items($list_id, $column_id, $status = 1){
        $conditions = array('list_id' => $list_id, 'column_id' => $column_id, 'is_completed' => $status, 'is_deleted' => 0);
        $rst = $this->db->select('value');
        $this->db->where($conditions);
        $this->db->limit(5);
        $query = $this->db->get('list_data');
        return $query->result_array();
    }
    
    /*
     * Function to get user id who has created item
     * @author: SG
     */
    public function get_task_owner($task_id){
        $conditions = array('id' => $task_id);
        $rst = $this->db->select('user_id');
        $this->db->where($conditions);
        $query = $this->db->get('list_data');
        return $query->row_array();
    }
    
    /*
     * Function to get values count for single column inside list
     * @author: SG
     */
    public function find_item_count_in_col($list_id, $first_column_id){
        $conditions = array('list_id' => $list_id, 'is_deleted' => 0, 'column_id' => $first_column_id);
        $rst = $this->db->select('*');
        $this->db->where($conditions);
        $query = $this->db->get('list_data');
        return $query->num_rows();
    }
    
    
    /*
     * Function to update list id for columns
     * @author: SG
     * creation date: 10-04-2018
    */
    public function update_column_data_all($list_id, $col_data) {
        $condition = array('list_id' => $list_id);
        $this->db->where($condition);
        return $this->db->update('list_columns', $col_data);
    }
    
    /*
     * Function to update list ids of tasks
     * @author: SG
     * creation date: 10-04-2018
     */
    public function update_list_id_for_task($list_id, $data) {
        $condition = array('list_id' => $list_id);
        $this->db->where($condition);
        return $this->db->update('list_data', $data);
    }
    
    
    /*
     * Function to update list id for log
     * @author: SG
     * creation date: 10-04-2018
     */
    public function update_list_id_for_log($list_id, $data) {
        $condition = array('list_id' => $list_id);
        $this->db->where($condition);
        return $this->db->update('cycle_history', $data);
    }
    
    /*
     * Function to update list_id for attendance_data
     * @author: SG
     * creation date: 10-04-2018
     */
    public function update_list_id_for_attendance_extra($list_id, $data){
        $condition = array('list_id' => $list_id);
        $this->db->where($condition);
        return $this->db->update('attendance_data', $data);
    }
    
    /*
     * Function to get tasks for a column
     * @author: SG
     * creation date: 24-05-2018
     */
    public function getTaskByColumnId($list_id, $col_id) {
        $condition = array('list_data.column_id' => $col_id, 'list_data.list_id' => $list_id, 'list_data.is_deleted' => 0);
        $rst = $this->db->select('id, value as TaskName');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        return $query->result_array();
    }
    
    public function get_column_type($col_id){
        $condition = array('id' => $col_id, 'is_deleted' => 0);
        $rst = $this->db->select('type');
        $this->db->where($condition);
        $query = $this->db->get('list_columns');
        return $query->row_array();
    }
    
    
}
