<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ListsModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Find list created without login by users
     * @author SG
     */
    public function find_public_lists() {
        $condition = array('lists.user_id' => NULL);
        $rst = $this->db->select('lists.*, count(list_data.id) as items');
        $this->db->join('list_data', 'lists.id = list_data.list_id', 'left');
        $this->db->where($condition);
        $this->db->group_by('lists.id');
        $query = $this->db->get('lists');
        return $query->result_array();
    }

    /**
     * Find list created after login by users
     * @author SG
     */
    public function find_user_lists($user_id) {
        $condition = array('lists.user_id' => $user_id, 'lists.is_deleted' => 0, 'list_data.is_deleted' => 0);
        $rst = $this->db->select('lists.list_inflo_id as ListId, lists.slug as ListSlug, lists.name as ListName, count(list_data.id) as total_items');
        $this->db->join('list_data', 'lists.list_inflo_id = list_data.list_inflo_id', 'left');
        $this->db->where($condition);
        $this->db->group_by('lists.id');
        $query = $this->db->get('lists');

        return $query->result_array();
    }
    
    /**
     * Find total items added by user
     * @author SG
     */
    public function find_total_user_lists($user_id) {
        $condition = array('list_data.user_id' => $user_id, 'list_data.is_deleted' => 0);
        $rst = $this->db->select('count(list_data.id) as total_items');
        $this->db->where($condition);
        $this->db->group_by('list_data.list_inflo_id');
        $query = $this->db->get('list_data');

        $total = $query->row_array();
        return $total['total_items'];
    }
    
    /**
     * Add new list
     * @author SG
     */
    public function add_list($data) {
        $this->db->insert('lists', $data);
        return $this->db->insert_id();
    }

    
    /**
     * Find count of tasks from all lists created by logged in user
     * @author SG
     */
    public function find_all_user_list_item_count($user_id) {
        $condition = array('lists.user_id' => $user_id, 'lists.is_deleted' => 0);
        $this->db->select('count(list_data.id) as items');
        $this->db->join('list_data', 'lists.id = list_data.list_id', 'left');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        return $query->row_array();
    }

    
    /**
     * Find count of tasks from all lists created by guest users without login
     * @author SG
     */
    public function find_all_public_list_item_count() {
        $condition = array('lists.user_id' => NULL);
        $this->db->select('count(list_data.id) as items');
        $this->db->join('list_data', 'lists.id = list_data.list_id', 'left');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        return $query->row_array();
    }

    
    /**
     * Get list details
     * @author SG
     */
    public function get_list_details($list_id, $user_id) {
        $condition = array('lists.user_id' => $user_id, 'lists.id' => $list_id);
        $this->db->select('lists.name');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        return $query->row_array();
    }

    /**
     * Find list name
     * @author SG
     */
    public function find_list_by_slug($slug, $user_id) {
        $condition = array('lists.slug' => $slug, 'lists.user_id' => $user_id);
        $this->db->select('lists.name');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $list_name =  $query->row_array();
//        echo $list_name['name']; exit;
        return $list_name['name'];
    }
    
    public function find_list_details_by_slug($slug) {
        $condition = array('lists.slug' => $slug);
        $this->db->select('lists.name as list_name, lists.list_inflo_id as list_id, lists.slug as list_slug, lists.list_type_id as type_id, lists.show_completed as show_completed, lists.allow_move as allow_move, lists.user_id as list_owner_id, lists.is_locked as is_locked');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $list_details =  $query->row_array();
        return $list_details;
    }
    
    /**
     * Update list
     * @author SG
     */
    public function change_list_type($list_id, $list_data) {
        $condition = array('list_inflo_id' => $list_id);
        $this->db->where($condition);
        return $this->db->update('lists', $list_data);
    }
    
    /**
     * Update list
     * @author SG
     */
    public function update_list_data($list_id, $list_data) {
        $condition = array('list_inflo_id' => $list_id);
        $this->db->where($condition);
        return $this->db->update('lists', $list_data);
    }
    
    
    /**
     * Delete list
     * @author SG
     */
    public function delete_list($list_id){
        $condition = array('list_inflo_id' => $list_id);
        $this->db->where($condition);
        $list_data['is_deleted'] = 1;
        return $this->db->update('lists', $list_data);
    }
    
    public function find_user_lists_by_ids($list_ids){
        
        $condition = 'lists.list_inflo_id IN' . $list_ids . ' AND lists.is_deleted = 0';
        $this->db->select('lists.list_inflo_id as ListId, lists.slug as ListSlug, lists.name as ListName, count(list_data.id) as total_items');
        $this->db->join('list_data', 'lists.list_inflo_id = list_data.list_inflo_id', 'left');
        $this->db->where($condition);
        $this->db->group_by('lists.id');
        $query = $this->db->get('lists');
        return $query->result_array();
    }
    
    public function find_list_slug_by_id($list_id){
         $condition = array('list_inflo_id' => $list_id);
         $this->db->select('lists.slug as list_slug');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $result = $query->row_array();
       return $result['list_slug'];
    }

}
