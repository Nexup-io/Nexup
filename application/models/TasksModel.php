<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class TasksModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function add_task($data) {
        $this->db->insert('list_data', $data);
        return $this->db->insert_id();
    }

    public function update_task($task_id, $data) {
        $condition = array('id' => $task_id);
        $this->db->where($condition);
        return $this->db->update('list_data', $data);
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
    public function get_tasks($list_id) {
        $condition = array('list_data.list_inflo_id' => $list_id, 'list_data.is_deleted' => 0);
        $rst = $this->db->select('value as TaskName, task_inflo_id as TaskId, is_completed as IsCompleted, order, column_id');
        $this->db->where($condition);
        $this->db->order_by('list_data.order', 'asc');
        $query = $this->db->get('list_data');
        return $query->result_array();
    }

    /*
     * Get task name
     * @author SG
     */

    public function getTaskByTaskId($taks_id) {
        $condition = array('list_data.task_inflo_id' => $taks_id, 'list_data.is_deleted' => 0);
        $rst = $this->db->select('value as TaskName');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        $task = $query->row_array();
        return $task['TaskName'];
    }

    /*
     * Get list types
     * @author SG
     */

    public function getListTypes() {
        $rst = $this->db->select('id as ListTypeId, type_name as ListTypeName');
        $query = $this->db->get('list_types');
        return $query->result_array();
    }

    /*
     * Update task data
     * @author SG
     */

    public function update_task_data($list_id, $task_id, $task_data) {
        $condition = array('list_inflo_id' => $list_id, 'task_inflo_id' => $task_id);
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

    public function get_last_order_of_item($list_id, $col_id) {
        $condition = array('list_inflo_id' => $list_id, 'column_id' => $col_id);
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
    public function save_history($data){
        $this->db->insert('cycle_history', $data);
        return $this->db->insert_id();
    }
    
    /*
     * Find log of advance of list items by list id
     * @author SG
     */
    public function find_log($list_id){
        $condition = array('list_inflo_id' => $list_id, 'nexup_type' => 1);
        $rst = $this->db->select('list_inflo_id, user_id, old_order, new_order, comment, user_ip, created');
        $this->db->where($condition);
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('cycle_history');
        return $query->result_array();
    }
    
    /*
     * Find last log of advance of list items by list id
     * @author SG
     */
    public function find_last_log($list_id){
        $condition = array('list_inflo_id' => $list_id, 'is_undone' => 0, 'is_undo' => 0);
        $rst = $this->db->select('id,list_inflo_id, user_id, old_order, new_order, comment, user_ip, created');
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
     public function update_log($id, $data){
         $condition = array('id' => $id);
        $this->db->where($condition);
        return $this->db->update('cycle_history', $data);
     }

     /*
     * Get current order of items
     * @author SG
     */
     public function get_current_item_order($list_id){
         $condition = array('list_inflo_id' => $list_id,);
        $rst = $this->db->select('task_inflo_id');
        $this->db->where($condition);
        $this->db->order_by('order', 'asc');
        $query = $this->db->get('list_data');
        return $query->result_array();
     }
    
     
    /*
     * Add new column in list
     * @author SG
     */
    public function add_new_colum($data){
        $this->db->insert('list_columns', $data);
        return $this->db->insert_id();
    }
    
    /*
     * Find highest order of column
     * @author SG
     */
    public function get_max_column_order($list_id){
        $condition = array('list_inflo_id' => $list_id, 'is_deleted' => 0);
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
        $condition = array('list_data.list_inflo_id' => $list_id, 'list_data.is_deleted' => 0, 'list_columns.is_deleted' => 0);
        $rst = $this->db->select('value as TaskName, task_inflo_id as TaskId, is_completed as IsCompleted, list_data.order as order, column_id, list_columns.column_name as column_name');
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
    public function FindColumnMaxOrder($list_id){
        $condition = array('list_inflo_id' => $list_id, 'is_deleted' => 0);
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
    public function UpdateColumnOrder($list_id, $col_id){
        $condition = array('list_inflo_id' => $list_id);
        $this->db->where($condition);
        $data['column_id'] = $col_id;
        return $this->db->update('list_data', $data);
    }
    
    
    /*
     * Get columns for a list
     * @author SG
     */
    public function getColumns($list_id){
        $condition = array('list_inflo_id' => $list_id, 'is_deleted' => 0);
        $this->db->select('id, column_name, order');
        $this->db->where($condition);
        $this->db->order_by('order', 'asc');
        $query = $this->db->get('list_columns');
        return $query->result_array();
    }
    
    /*
     * Get column name from id
     * @author SG
     */
    public function getColumnNameById($list_id, $col_id){
        $condition = array('list_inflo_id' => $list_id, 'id' => $col_id, 'is_deleted' => 0);
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
    public function updateColumnName($list_id, $col_id, $update_data){
        $condition = array('id' => $col_id, 'list_inflo_id' => $list_id);
        $this->db->where($condition);
        return $this->db->update('list_columns', $update_data);
    }
    
    
    /*
     * Update column order for multi column list
     * @author SG
     */

    public function update_column_data($list_id, $col_id, $col_data) {
        $condition = array('list_inflo_id' => $list_id, 'id' => $col_id);
        $this->db->where($condition);
        return $this->db->update('list_columns', $col_data);
    }
    
    
    /*
     * Delete Column
     * @author SG
     */
    
    public function delete_column($list_id, $column_id){
        $condition = array('list_inflo_id' => $list_id, 'id' => $column_id);
        $del_data['is_deleted'] = 1;
        $this->db->where($condition);
        return $this->db->update('list_columns', $del_data);
    }
    
    
    /*
     * Delete task related to column which is deleted
     * @author SG
     */
    
    public function remove_items($list_id, $column_id){
        $condition = array('list_inflo_id' => $list_id, 'id' => $column_id);
        $del_data['is_deleted'] = 1;
        $this->db->where($condition);
        return $this->db->update('list_data', $del_data);
    }
    

}
