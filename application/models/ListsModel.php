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
//    public function find_user_lists_1($user_id) {
//        $condition = array('lists.user_id' => $user_id, 'lists.is_deleted' => 0, 'lists.id >' => '0', 'parent_id' => 0);
//        $rst = $this->db->select('lists.id as ListId, lists.list_inflo_id as ListInfloId, lists.slug as ListSlug, lists.list_type_id, lists.name as ListName, count(list_data.id) as total_items, lists.created_user_name, lists.show_author');
//        $this->db->join('list_data', 'lists.id = list_data.list_id AND list_data.is_deleted = 0', 'left');
//        $this->db->where($condition);
//        $this->db->group_by('lists.id');
//        $query = $this->db->get('lists');
//        return $query->result_array();
//    }
    
    public function find_user_lists($user_id) {
	$condition = array('lists.user_id' => $user_id, 'lists.is_deleted' => 0, 'lists.id >' => '0', 'parent_id' => 0);

	$rst = $this->db->select('lists.id as ListId, lists.list_inflo_id as ListInfloId, lists.slug as ListSlug, lists.list_type_id, lists.name as ListName, total_items, total_child, lists.created_user_name, lists.show_author, list_types.icon', FALSE);

	$this->db->join('(select count(*) total_items, list_id last_data_id, ld.is_deleted ld_is_deleted from list_data ld WHERE ld.is_deleted = 0 group by last_data_id) items_count', 'lists.id=last_data_id AND ld_is_deleted = 0', 'left');

	$this->db->join('(select count(*) total_child, child.parent_id child_parent_id , child.is_deleted child_is_deleted from lists child WHERE child.is_deleted = 0 group by child_parent_id) tabs_count', 'lists.id=child_parent_id AND child_is_deleted = 0', 'left');
        
        $this->db->join('list_types', 'lists.list_type_id = list_types.id', 'left');

	$this->db->where($condition);
        $this->db->order_by('lists.modified desc');
	$this->db->group_by('lists.id');
	$query = $this->db->get('lists');
	return $query->result_array();
    }

    /*
     * Find Lists when user search any list from search box
     */

    public function search_user_lists($user_id, $search_params) {
        $condition = 'lists.parent_id = 0 AND lists.user_id = ' . $user_id . ' AND lists.is_deleted = 0 AND lists.name LIKE "%' . $search_params . '%"';
        $rst = $this->db->select('lists.id as ListId, lists.list_inflo_id as ListInfloId, lists.slug as ListSlug, lists.list_type_id, lists.name as ListName, count(list_data.id) as total_items, lists.user_id, lists.created_user_name, lists.show_author, lists.visible_in_search');
        $this->db->join('list_data', 'lists.id = list_data.list_id AND list_data.is_deleted = 0', 'left');
        $this->db->where($condition);
        $this->db->group_by('lists.id');
        $this->db->order_by("lists.modified", "desc");
        $query = $this->db->get('lists');

        return $query->result_array();
    }
    
    
    /*
     * Find Lists when user search any list from search box which belongs to other users
     */

    public function search_public_lists_other_users($user_id, $search_params) {
        $condition = 'lists.parent_id = 0 AND lists.user_id != ' . $user_id . ' AND lists.is_deleted = 0 AND lists.name LIKE "%' . $search_params . '%" AND lists.is_private = 0';
        $rst = $this->db->select('lists.id as ListId, lists.list_inflo_id as ListInfloId, lists.slug as ListSlug, lists.name as ListName, count(list_data.id) as total_items, lists.user_id, lists.created_user_name, lists.show_author, lists.visible_in_search');
        $this->db->join('list_data', 'lists.id = list_data.list_id AND list_data.is_deleted = 0', 'left');
        $this->db->where($condition);
        $this->db->group_by('lists.id');
        $this->db->order_by("lists.created", "desc");
        $query = $this->db->get('lists');

        return $query->result_array();
    }
    
    /*
     * Find Lists when public search any list from search box
     */

    public function search_public_lists($search_params) {
        $condition = 'lists.parent_id = 0 AND lists.is_deleted = 0 AND lists.name LIKE "%' . $search_params . '%" AND lists.is_private=0';
        $rst = $this->db->select('lists.id as ListId, lists.slug as ListSlug, lists.name as ListName, count(list_data.id) as total_items, lists.user_id, lists.visible_in_search');
        $this->db->join('list_data', 'lists.id = list_data.list_id AND list_data.is_deleted = 0', 'left');
        $this->db->where($condition);
        $this->db->group_by('lists.id');
        $query = $this->db->get('lists');

        return $query->result_array();
    }
    
    /*
     * Find list type image
     */
    public function find_list_type_image($type_id) {
        $condition = array('id' => $type_id);
        $rst = $this->db->select('icon');
        $this->db->where($condition);
        $query = $this->db->get('list_types');
        return $query->row_array();
    }
    
    
    /*
     * Find Lists by name
     */

    public function search_lists_by_name($search_params) {
        $condition = 'lists.is_deleted = 0 AND lists.name IN (' . $search_params . ')';
        $rst = $this->db->select('lists.id as ListId, lists.slug as ListSlug, lists.name as ListName, count(list_data.id) as total_items, lists.user_id');
        $this->db->join('list_data', 'lists.id = list_data.list_id AND list_data.is_deleted = 0', 'left');
        $this->db->where($condition);
        $this->db->group_by('lists.id');
        $query = $this->db->get('lists');

        return $query->result_array();
    }
    
    /*
     * Find Lists by inflo id
     */

    public function search_lists_by_inflo_id($search_params) {
        $condition = 'lists.parent_id = 0 AND lists.is_deleted = 0 AND lists.list_inflo_id IN (' . $search_params . ')';
        $rst = $this->db->select('lists.id as ListId, lists.slug as ListSlug, lists.name as ListName, count(list_data.id) as total_items, lists.user_id, lists.visible_in_search');
        $this->db->join('list_data', 'lists.id = list_data.list_id AND list_data.is_deleted = 0', 'left');
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
        $condition = array('list_data.is_deleted' => 0, 'lists.is_deleted' => 0, 'lists.user_id' => $user_id);
        $rst = $this->db->select('count(list_data.id) as total_items');
        $this->db->join('lists', 'list_data.list_id = lists.id AND lists.is_deleted = 0', 'left');
        $this->db->where($condition);
//        $this->db->group_by('list_data.list_inflo_id');
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
    public function find_list_by_slug($slug) {
        $condition = array('lists.slug' => $slug);
        $this->db->select('lists.name');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $list_name = $query->row_array();
//        echo $list_name['name']; exit;
        return $list_name['name'];
    }

    /*
     * Find List Details By Slug
     * @author SG
     */

    public function find_list_details_by_slug($slug) {
        $condition = array('lists.slug' => $slug);
        $this->db->select('lists.name as list_name, lists.id as list_id, lists.parent_id, lists.slug as list_slug, lists.list_type_id as type_id, lists.show_completed as show_completed, lists.allow_move as allow_move, lists.allow_undo as allow_undo, lists.visible_in_search, lists.allow_maybe as allow_maybe, lists.show_time as show_time, lists.show_preview as show_preview, lists.enable_comment as enable_comment, lists.enable_attendance_comment as enable_attendance_comment, lists.allow_append_locked, lists.start_collapsed, lists.user_id as list_owner_id, lists.is_locked as is_locked, lists.has_password, lists.password, lists.modification_password, lists.salt, lists.is_deleted as is_deleted, lists.user_id as user_id, lists.created_user_name, lists.show_author, lists.list_inflo_id, lists.unshared_with');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $list_details = $query->row_array();
        return $list_details;
    }

    /*
     * Find List Details By List Id
     * @author SG
     */

    public function find_list_details_by_id($list_id) {
        $condition = array('lists.id' => $list_id);
        $this->db->select('lists.name as list_name, lists.id as list_id, lists.parent_id, lists.list_inflo_id as list_inflo_id, lists.slug as list_slug, lists.list_type_id as type_id, lists.show_completed as show_completed, lists.allow_move as allow_move, lists.allow_undo as allow_undo, , lists.visible_in_search, lists.allow_maybe as allow_maybe, lists.show_time as show_time, lists.show_author, lists.enable_comment as enable_comment, lists.enable_attendance_comment as enable_attendance_comment, lists.user_id as list_owner_id, lists.is_locked as is_locked, lists.show_preview as show_preview, lists.allow_append_locked, lists.start_collapsed, lists.is_deleted as is_deleted, lists.user_id as user_id, lists.created_user_name');
        
//        $this->db->select('lists.name as list_name, lists.id as list_id, lists.slug as list_slug, lists.list_type_id as type_id, lists.show_completed as show_completed, lists.allow_move as allow_move, lists.allow_undo as allow_undo, lists.user_id as list_owner_id, lists.is_locked as is_locked, lists.is_deleted as is_deleted');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $list_details = $query->row_array();
        return $list_details;
    }

    /**
     * Update list
     * @author SG
     */
    public function change_list_type($list_id, $list_data) {
        $condition = array('id' => $list_id);
        $this->db->where($condition);
        return $this->db->update('lists', $list_data);
    }

    /**
     * Update list
     * @author SG
     */
    public function update_list_data($list_id, $list_data) {
        $condition = array('id' => $list_id);
        $this->db->where($condition);
        return $this->db->update('lists', $list_data);
    }

    /*
     * Update list after creating new list on inflo
     */

    public function update_list_data_from_inflo($list_id, $list_data) {
        $condition = array('id' => $list_id);
        $this->db->where($condition);
        return $this->db->update('lists', $list_data);
    }

    /**
     * Delete list
     * @author SG
     */
    public function delete_list($list_id) {
        $condition = array('id' => $list_id);
        $this->db->where($condition);
        $list_data['is_deleted'] = 1;
        return $this->db->update('lists', $list_data);
    }
    
    /**
     * Delete list from directory
     * @author SG
     */
    public function delete_list_directory($list_id, $del_user_id) {
        $condition = array('list_id' => $list_id, 'user_id' => $del_user_id);
        $this->db->where($condition);
        $list_data['is_deleted'] = 1;
        return $this->db->update('visited_lists', $list_data);
    }
    
    
    /**
     * Find local unsharing
     * @author SG
     */
    public function list_unshared_local_users($list_id) {
        $condition = array('id' => $list_id);
        $this->db->select('lists.unshared_with');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $list_details = $query->row_array();
        return $list_details['unshared_with'];
    }
    
    
    /**
     * Delete shared list from directory
     * @author SG
     */
    public function delete_list_local_sharing($list_id, $del_user_id) {
        $condition = array('id' => $list_id);
        $this->db->where($condition);
        $list_data['unshared_with'] = $del_user_id;
        return $this->db->update('lists', $list_data);
    }

    /*
     * Find user lists by list ids
     * @author SG
     */

//    public function find_user_lists_by_ids_1($list_ids) {
//
//        $condition = 'lists.id IN' . $list_ids . ' AND lists.is_deleted = 0';
//        $this->db->select('lists.id as ListId, lists.list_inflo_id as ListInfloId, lists.slug as ListSlug, lists.list_type_id, lists.name as ListName, count(list_data.id) as total_items, lists.user_id as user_id, lists.created_user_name, lists.show_author');
//        $this->db->select('if(list_data.is_deleted = 0 , count(list_data.id),0) as total_items', FALSE);
//        $this->db->join('list_data', 'lists.id = list_data.list_id AND list_data.is_deleted = 0', 'left');
//        $this->db->where($condition);
//        $this->db->group_by('lists.id');
//        $query = $this->db->get('lists');
//        return $query->result_array();
//    }
    
    
    public function find_user_lists_by_ids($list_ids) {

        $condition = 'lists.id IN' . $list_ids . ' AND lists.is_deleted = 0';
        $this->db->select('lists.id as ListId, lists.list_inflo_id as ListInfloId, lists.slug as ListSlug, lists.list_type_id, lists.name as ListName, total_items, total_child, lists.user_id as user_id, lists.created_user_name, lists.show_author, list_types.icon', FALSE);
        
        $this->db->join('(select count(*) total_items, list_id last_data_id, ld.is_deleted ld_is_deleted from list_data ld WHERE ld.is_deleted = 0 group by last_data_id) items_count', 'lists.id=last_data_id AND ld_is_deleted = 0', 'left');

	$this->db->join('(select count(*) total_child, child.parent_id child_parent_id , child.is_deleted child_is_deleted from lists child WHERE child.is_deleted = 0 group by child_parent_id) tabs_count', 'lists.id=child_parent_id AND child_is_deleted = 0', 'left');
        
        $this->db->join('list_types', 'lists.list_type_id = list_types.id', 'left');
        
        $this->db->where($condition);
        $this->db->group_by('lists.id');
        $query = $this->db->get('lists');
        return $query->result_array();
    }

    /*
     * Find list slug for given list id
     * @author SG
     */

    public function find_list_slug_by_id($list_id) {
        $condition = array('id' => $list_id);
        $this->db->select('lists.slug as list_slug');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $result = $query->row_array();
        return $result['list_slug'];
    }

    /*
     * Find list name by list id
     * @author SG
     */

    public function find_list_name_by_id($list_id) {
        $condition = array('id' => $list_id);
        $this->db->select('name');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $list_details = $query->row_array();
        return $list_details['name'];
    }

    /*
     * Find list owner details by list id
     * @author SG
     */

    public function get_list_author_by_id($list_id) {
        $condition = array('id' => $list_id);
        $this->db->select('user_id');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $list_details = $query->row_array();
        return $list_details['user_id'];
    }

    /*
     * Find if list with slug already exist
     * @author SG
     */

    public function find_exist_slug($slug, $list_id) {
        $condition = array('slug' => $slug, 'id !=' => $list_id);
        $this->db->select('lists.slug as list_slug');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $result = $query->row_array();
        return $result['list_slug'];
    }

    /*
     * Find if list type
     * @author SG
     */

    public function find_list_type($list_id) {
        $condition = array('id !=' => $list_id);
        $this->db->select('list_type_id');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $result = $query->row_array();
        return $result['list_type_id'];
    }

    /*
     * Find public list more than 1 month older
     * @author SG
     */

    public function find_old_list($date) {
        $condition = array('user_id' => 0, 'is_deleted' => 0, 'created <' => $date);
        $this->db->select('id');
        $this->db->where($condition);
        $this->db->order_by('created desc');
        $query = $this->db->get('lists');
        return $query->result_array();
    }

    /*
     * Delete public list more than 1 month older
     * @author SG
     */

    public function remove_old_list($list_ids, $date) {
        $condition = array('user_id' => 0, 'is_deleted' => 0, 'created <=' => $date);
        $this->db->where($condition);
        $this->db->where_in('id', $list_ids);
        $list_data['is_deleted'] = 1;
        return $this->db->update('lists', $list_data);
    }

    /*
     * Find list shared by user
     * @author SG
     */

//    public function find_shared_lists($list_ids){
//        
//        $condition = 'lists.list_inflo_id IN' . $list_ids . ' AND lists.is_deleted = 0';
//        $this->db->select('lists.list_inflo_id as ListId, lists.slug as ListSlug, lists.name as ListName, lists.user_id as user_id');
//        $this->db->select('if(list_data.is_deleted =0 , count(list_data.id),0) as total_items', FALSE);
//        $this->db->join('list_data', 'lists.list_inflo_id = list_data.list_inflo_id AND list_data.is_deleted = 0', 'left');
//        $this->db->where($condition);
//        $this->db->group_by('lists.id');
//        $query = $this->db->get('lists');
//        return $query->result_array();
//    }


//    public function find_shared_lists_1($list_ids, $user_id) {
//        $condition = 'lists.list_inflo_id IN' . $list_ids . ' AND lists.is_deleted = 0 AND find_in_set(' . $user_id . ',unshared_with) = 0';
//        $this->db->select('lists.id as ListId, lists.list_inflo_id as ListInfloId, lists.slug as ListSlug, lists.list_type_id, lists.name as ListName, lists.user_id as user_id, lists.created_user_name, lists.show_author');
//        $this->db->select('if(list_data.is_deleted = 0 , count(list_data.id),0) as total_items', FALSE);
//        $this->db->join('list_data', 'lists.id = list_data.list_id AND list_data.is_deleted = 0', 'left');
//        $this->db->where($condition);
//        $this->db->group_by('lists.id');
//        $query = $this->db->get('lists');
//        return $query->result_array();
//    }
    
    public function find_shared_lists($list_ids, $user_id) {
        $condition = 'lists.list_inflo_id IN' . $list_ids . ' AND lists.is_deleted = 0 AND find_in_set(' . $user_id . ',unshared_with) = 0 AND lists.parent_id = 0';
        $this->db->select('lists.id as ListId, lists.list_inflo_id as ListInfloId, lists.slug as ListSlug, lists.list_type_id, lists.name as ListName, total_items, total_child, lists.user_id as user_id, lists.created_user_name, lists.show_author, list_types.icon', FALSE);
        
        $this->db->join('(select count(*) total_items, list_id last_data_id, ld.is_deleted ld_is_deleted from list_data ld WHERE ld.is_deleted = 0 group by last_data_id) items_count', 'lists.id=last_data_id AND ld_is_deleted = 0', 'left');

	$this->db->join('(select count(*) total_child, child.parent_id child_parent_id , child.is_deleted child_is_deleted from lists child WHERE child.is_deleted = 0 group by child_parent_id) tabs_count', 'lists.id=child_parent_id AND child_is_deleted = 0', 'left');
        
        $this->db->join('list_types', 'lists.list_type_id = list_types.id', 'left');
        
        $this->db->where($condition);
        $this->db->order_by('lists.modified desc');
        $this->db->group_by('lists.id');
        $query = $this->db->get('lists');
        return $query->result_array();
    }

    /*
     * Find lists visited by user
     * @uthor SG
     */

//    public function find_my_visited_lists_1($list_ids, $user_id) {
//
//        $condition = 'lists.id IN ' . $list_ids . ' AND lists.is_deleted = 0 and lists.user_id != ' . $user_id;
//        $this->db->select('lists.id as ListId, lists.list_inflo_id as ListInfloId, lists.slug as ListSlug, lists.list_type_id, lists.name as ListName, lists.user_id as user_id, lists.created_user_name, lists.show_author');
//        $this->db->select('if(list_data.is_deleted = 0 , count(list_data.id),0) as total_items', FALSE);
//        $this->db->join('list_data', 'lists.id = list_data.list_id AND list_data.is_deleted = 0', 'left');
//        $this->db->where($condition);
//        $this->db->group_by('lists.id');
//        $query = $this->db->get('lists');
//        return $query->result_array();
//    }
    
    public function find_my_visited_lists($list_ids, $user_id) {

        $condition = 'lists.id IN ' . $list_ids . ' AND lists.is_deleted = 0 and lists.user_id != ' . $user_id;
        $this->db->select('lists.id as ListId, lists.list_inflo_id as ListInfloId, lists.slug as ListSlug, lists.list_type_id, lists.name as ListName, total_items, total_child, lists.user_id as user_id, lists.created_user_name, lists.show_author, list_types.icon', FALSE);
        
        $this->db->join('(select count(*) total_items, list_id last_data_id, ld.is_deleted ld_is_deleted from list_data ld WHERE ld.is_deleted = 0 group by last_data_id) items_count', 'lists.id=last_data_id AND ld_is_deleted = 0', 'left');

	$this->db->join('(select count(*) total_child, child.parent_id child_parent_id , child.is_deleted child_is_deleted from lists child WHERE child.is_deleted = 0 group by child_parent_id) tabs_count', 'lists.id=child_parent_id AND child_is_deleted = 0', 'left');
        
        $this->db->join('list_types', 'lists.list_type_id = list_types.id', 'left');
        
        $this->db->where($condition);
        $this->db->order_by('lists.modified desc');
        $this->db->group_by('lists.id');
        $query = $this->db->get('lists');
        return $query->result_array();
    }

    /*
     * Save list description
     * @author SG
     */

    public function save_list_desc($desc_str, $list_id) {
        $condition = array('is_deleted' => 0, 'id' => $list_id);
        $this->db->where($condition);
        $list_update['description'] = $desc_str;
        return $this->db->update('lists', $list_update);
    }

    /*
     * Get list description
     * @author SG
     */

    public function list_desc_get($list_id) {
        $condition = array('is_deleted' => 0, 'id' => $list_id);
        $this->db->select('lists.description as desc');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $response = $query->row_array();
        return $response['desc'];
    }

    /*
     * Get List inflo id
     * @author SG
     */

    public function get_list_inflo_id_from_list_id($list_id) {
        $condition = array('id' => $list_id);
        $this->db->select('list_inflo_id');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $response = $query->row_array();
        return $response['list_inflo_id'];
    }
    
    /*
     * Get List inflo id from slug
     * @author SG
     */

    public function get_list_inflo_id_from_list_slug($list_slug) {
        $condition = array('slug' => $list_slug);
        $this->db->select('list_inflo_id');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $response = $query->row_array();
        return $response['list_inflo_id'];
    }

    /*
     * Get list type from list id
     * @author SG
     */

    public function getListType($list_id) {
        $condition = array('id' => $list_id);
        $this->db->select('list_type_id');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $response = $query->row_array();
        return $response['list_type_id'];
    }

    /*
     * Get list owner from list id
     * @author SG
     */

    public function getListOwner($list_id) {
        $condition = array('id' => $list_id);
        $this->db->select('user_id');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $response = $query->row_array();
        return $response['user_id'];
    }
    
    /*
     * Get list name
     * @author SG
     */
    public function get_list_data_for_copy($list_id){
        $condition = array('id' => $list_id);
        $this->db->select('name, list_type_id, description, is_locked, 	show_completed, allow_move, allow_undo, allow_maybe, show_time, enable_comment, ');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        return $query->row_array();
    }
    
     /**
     * Find list by name
     * @author SG
     */
    public function find_list_by_name($name, $user_id) {
        $condition = array('user_id' => $user_id);
        $this->db->like('lists.name', $name . ' Copy');
        $this->db->select('COUNT(*) as count');
        $this->db->where($condition);
        $this->db->order_by('created', 'desc');
        $query = $this->db->get('lists');
        $list_name = $query->row_array();
//        echo $list_name['name']; exit;
        return $list_name['count'];
    }
    
    /*
     * Find list id by name
     * @author: SG
     */
    public function find_list_id_by_name($list_name, $parent_id){
        $condition = array('name' => $list_name, 'parent_id' => $parent_id);
        $this->db->select('id');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $list_name = $query->row_array();
        return $list_name['id'];
    }


    /*
     * Find list owner
     * @author SG
     */
    public function get_list_owner($list_id){
        $condition = array('id' => $list_id);
        $this->db->select('created_user_name as owner');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $owner_name = $query->row_array();
        return $owner_name['owner'];
    }
    
    /*
     * Find type id
     * @author SG
     */
    public function get_type_id($list_id){
        $condition = array('lists.id' => $list_id);
        $rst = $this->db->select('lists.list_type_id');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $res = $query->row_array();
        return $res['list_type_id'];
    }
    
    /*
     * Find count of yes, no, maybe and unresponded for attendance list
     * @author SG
     */
    public function get_summary_attendance($list_id, $col_id){
        $query = 'select sum(case when is_present = 1 then 1 else 0 end) as yes_total, sum(case when is_present = 2 then 1 else 0 end) as maybe_total, sum(case when is_present = 3 then 1 else 0 end) as no_total, sum(case when is_present = 0 then 1 else 0 end) as unresponded_total from list_data where is_deleted = 0 AND list_id = ' . $list_id . ' AND column_id = ' . $col_id;
        return $this->db->query($query)->row_array();
    }
    
    
    /*
     * Get list data to copy summary
     * @author SG
     */
    public function get_list_data_for_copy_summary($list_id){
        $condition = array('id' => $list_id);
        $this->db->select('name, list_type_id, description, is_locked, show_completed, allow_move, allow_undo, allow_maybe, show_time, enable_comment, slug, show_time');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        return $query->row_array();
    }
    
    /*
     * Get items with yes flag from first column
     * @author SG
     */
    public function get_present_items($list_id, $col_id, $present_flag, $sort){
        $query = 'select value, `attendance_data`.`id` as `attendance_id`, `attendance_data`.`item_ids`, `attendance_data`.`comment`, `attendance_data`.`check_date` from list_data LEFT JOIN `attendance_data` ON `attendance_data`.`is_deleted` = 0 AND FIND_IN_SET(list_data.id,attendance_data.item_ids) !=0 where list_data.list_id = ' . $list_id . ' AND list_data.column_id = ' . $col_id . ' AND list_data.is_present = ' . $present_flag . ' AND list_data.is_deleted = 0 ORDER BY ' . $sort;
        return $this->db->query($query)->result_array();
    }
    /*
     * Get items with yes flag from first column
     * @author SG
     */
    public function get_present_items_with_comments($list_id, $col_id, $present_flag, $sort){
        $query = 'select value, `attendance_data`.`comment` from list_data LEFT JOIN `attendance_data` ON `attendance_data`.`is_deleted` = 0 AND FIND_IN_SET(list_data.id,attendance_data.item_ids) !=0 where list_data.list_id = ' . $list_id . ' AND list_data.column_id = ' . $col_id . ' AND list_data.is_present != 0 AND list_data.is_deleted = 0 ORDER BY ' . $sort;
        return $this->db->query($query)->result_array();
    }
    
    /*
     * Get list details for sharing
     * @author: SG
     */
    public function get_list_details_for_share($list_id) {
        $condition = array('lists.is_deleted' => 0, 'lists.id' => $list_id);
        $this->db->select('name, slug');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        return $query->row_array();
    }
    
    /*
     * Find Child List
     * @author: SG
     */
    public function find_sublists($list_id) {
        $condition = array('lists.parent_id' => $list_id, 'is_deleted' => 0);
        $this->db->select('lists.name as list_name, lists.id as list_id, lists.description, lists.slug as list_slug, lists.list_type_id as type_id, lists.show_completed as show_completed, lists.allow_move as allow_move, lists.allow_undo as allow_undo, lists.allow_maybe as allow_maybe, lists.show_time as show_time, lists.show_preview as show_preview, lists.enable_comment as enable_comment, lists.allow_append_locked, lists.user_id as list_owner_id, lists.is_locked as is_locked, lists.is_deleted as is_deleted, lists.user_id as user_id, lists.created_user_name, lists.show_author, lists.list_inflo_id, lists.unshared_with');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $list_details = $query->result_array();
        return $list_details;
    }
    
    /*
     * Function to find count of total lists
     * @author: SG
     */
    public function count_lists($date_today = null){
        $this->db->select('id');
        if(!empty($date_today)){
            $conditions = array('created LIKE' => $date_today . '%');
            $this->db->where($conditions);
        }
        $query = $this->db->get('lists');
        return $query->num_rows();
    }
    
    /*
     * Function to find names of newly created lists with their owners
     * @author: SG
     */
    public function find_new_created_lists($date_today){
        $this->db->select('name, created_user_name');
        $conditions = array('created LIKE' => $date_today . '%');
        $this->db->where($conditions);
        $query = $this->db->get('lists');
        return $query->result_array();
    }
    
    /*
     * Function to find active unique users
     * @author: SG
     */
    public function find_active_users(){
        $this->db->select('DISTINCT(created_user_name)');
        $conditions = array('created_user_name <>' => '');
        $this->db->where($conditions);
        $query = $this->db->get('lists');
        return $query->result_array();
    }
    
    /*
     * Function to get list for copy as child list
     * @author: SG
     */
    public function get_list_data_for_copy_child($list_id){
        $condition = array('id' => $list_id);
        $this->db->select('name, list_type_id, user_id, description, is_locked, show_completed, allow_move, allow_undo, allow_maybe, show_time, show_preview, allow_append_locked, is_private, enable_comment, show_author, created_user_name');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        return $query->row_array();
    }
    
    /*
     * Function to get lists to move list list
     * @author: SG
     * @input: int user_id
     * @input: int list_id
     * @output: array lists
     */
    public function find_lists_to_move($user_id, $list_id){
        $condition = array('user_id' => $user_id,'id <>' => $list_id, 'parent_id' => 0, 'is_deleted' => 0);
        $this->db->select('id, name');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        return $query->result_array();
    }
    
    /*
     * Function to find count of child lists
     * @author: SG
     */
    public function find_list_tabs_count($list_id){
        $condition = array('parent_id ' => $list_id, 'is_deleted' => 0);
        $this->db->select('count(*) as tabs');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        return $query->row_array();
    }
    
    /*
     * Function to get password of list
     * @author: SG
     */
    public function get_list_pass($list_id){
        $condition = array('lists.id' => $list_id);
        $this->db->select('has_password, password, modification_password, salt');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $list_details = $query->row_array();
        return $list_details;
    }
    
    /*
     * Function to get list id from list name
     * @author: SG
     */
    public function get_child_list_id($parent_list_id, $list_name){
        $condition = array('lists.parent_id' => $parent_list_id, 'name' => $list_name);
        $this->db->select('id');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $list_details = $query->row_array();
        return $list_details['id'];
    }
    /*
     * Function to get list id from list name
     * @author: SG
     */
    public function get_child_list_id_for_cal($parent_list_id, $list_name){
        $condition = array('lists.parent_id' => $parent_list_id, 'name' => $list_name);
        $this->db->select('id, list_inflo_id');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        $list_details = $query->row_array();
        return $list_details;
    }

}
