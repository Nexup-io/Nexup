<?php
$current_user = 0;
$stored_pass = array();
if(isset($_SESSION['stored_pass'])){
    $stored_pass = $_SESSION['stored_pass'];
}
$stored_modify_pass = array();
if(isset($_SESSION['modification_pass'])){
    $stored_modify_pass = $_SESSION['modification_pass'];
}
if(isset($_SESSION['id'])){
    $current_user = $_SESSION['id'];
}
if ($allowed_access == 0 && $list_id != 0) {
    
?>

<div class="listing_not_view">
    <div class="listing_not_view_inner_content">
        <div class="img_lock"><img src="/assets/img/file_lock.png"/></div>  
        <p>This list is <span>private</span></p>
    </div>
</div>
<?php
} elseif($is_locked == 1 && $config['has_password'] == 1 && $list_owner_id != $current_user && !in_array($list_id, $stored_pass) && !in_array($list_id, $stored_modify_pass)){
?>
<div class="listing_pass_view">
    <div class="listing_pass_view_inner_content">
        <div class="img_lock"><img src="/assets/img/file_lock.png"/></div>  
        <p><span>password protected</span></p>
        <span class="enter_pass_span" id="enter_pass_span">
            <input type="password" id="password_list_show" class="password_list_show" placeholder="Password">
            
        </span>
        <button type="submit" name="unlock_list_btn" id="unlock_list_btn" class="unlock_list_btn" data-listid="<?php echo $list_id; ?>">Unlock</button>
        <div class="clearfix"></div>
        <span class="error_msg_pwd_enter" style="display: none;"></span>
        <!--<p>Click here to enter <a id="password_popup" data-toggle="modal" data-target="#unlock_list">password</a>.</p>-->
    </div>
</div>
<?php
}else{
    $hide_add_item_cls = '';
    $collapsable_div = '';
    $no_hover_table = '';
    $no_hover_heading = '';
    $hide_add_col = '';
    $hide_add_row = '';
    $no_hover_data = '';
    $hide_rearrange_class = '';
    if($config['allow_move'] == 'False'){
        $hide_rearrange_class = ' hidden_rearrange';
    }
    if ($is_locked == 1) {
        $collapsable_div = ' collapse_div';
        $no_hover_table = ' no_hover_table';
        $no_hover_heading = ' no_hover_table';
        $hide_add_row = ' hidden_add_row';
        if($config['allow_append_locked'] == 1){
            $no_hover_table = '';
            $no_hover_data = ' no_hover_table';
            $hide_add_row = '';
        }
        $hide_add_col = ' hidden_add_col';
        
        if(isset($_SESSION['id']) && $_SESSION['id'] == $list_user_id){
            $hide_add_item_cls = '';
            $no_hover_table = '';
            $hide_add_row = '';
            $hide_add_col = '';
            $no_hover_heading = '';
        }
    }
    if($is_locked == 2){
        $collapsable_div = ' collapse_div';
        if($config['allow_append_locked'] == 0){
            $hide_add_row = ' hidden_add_row';
        }
        $hide_add_col = ' hidden_add_col';
        $no_hover_table = ' no_hover_table';
        $no_hover_heading = ' no_hover_table';
        if($config['allow_append_locked'] == 1){
            $no_hover_table = '';
            $no_hover_data = ' no_hover_table';
        }
    }
    
    if(!empty($stored_modify_pass) && in_array($list_id, $stored_modify_pass)){
        $no_hover_table = '';
        $no_hover_heading = '';
        $no_hover_data = '';
    }
    
    if($modification_password != ''){
        if(!empty($stored_pass) && in_array($list_id, $stored_pass)){
            $no_hover_table = ' modify_pass_needed';
            $no_hover_heading = ' modify_pass_needed';
            $no_hover_data = ' modify_pass_needed';
        }
    }
    if($password == '' && $modification_password != ''){
        $no_hover_table = ' modify_pass_needed';
        $no_hover_heading = ' modify_pass_needed';
        if(!empty($stored_modify_pass) && in_array($list_id, $stored_modify_pass)){
            $no_hover_table = '';
            $no_hover_heading = '';
            $no_hover_data = '';
        }
    }
    
?>
<section id="content" class="content">

    <?php
    if ($this->session->flashdata('success') != '') {
        ?>
        <div class="alert alert-success no-border">
            <?php echo $this->session->flashdata('success'); ?>
        </div>
        <?php
    }
    ?>
    <?php
    if ($this->session->flashdata('error') != '') {
        ?>
        <div class="alert alert-danger no-border">
            <?php
            echo $this->session->flashdata('error');
            ?>
        </div>
        <?php
    }
    ?>
    <div class="head_custom">
    <div class="add-data-head list_title_head">
        <?php
        $hide_list = '';
        $show_add_column = '';
        $style_add_col='';
        
        if ($list_id == 0) {
        $show_add_column = ' hidden_add_column_btn';
        $style_add_col='display: none;';
        ?>
            <input name="edit_list_name" id="edit_list_name" class="edit-list-class" data-id="<?php echo $list_id; ?>" value="<?php echo $list_name; ?>" placeholder="What is your List's name?" type="text">
            <div class="edit_list_cls" style="display: none;"></div>
            <?php
            $hide_list = 'display: none;';
        }
        if($multi_col == 0 && empty($tasks)){
            $style_add_col='display: none;';
        }
        ?>
        <h2 id="listname_<?php echo $list_id; ?>" class="listname_<?php echo $list_id; ?> edit_list_task<?php echo $no_hover_heading; ?>" style="<?php echo $hide_list; ?>" data-id="<?php echo $list_id; ?>" data-slug="<?php echo $list_slug; ?>" id="edit_list_task_page" data-toggle="tooltip" data-placement="bottom" title="<?php echo html_entity_decode($list_name); ?>"><?php echo html_entity_decode($list_name); ?></h2>
        <a data-toggle="modal" data-target="#share-contact" id="share_list" data-toggle="tooltip" data-placement="bottom" title="Share" class="icon-share custom_cursor" style="<?php echo $hide_list; ?>" data-keyboard="false"> </a>
        <!--<span class="visit_site"><img src="/assets/img/visit.png"/>-->
            
        <!--</span>-->
        <div class="clearfix"></div>
        <?php if($config['show_author'] == 1){ ?>
            <div class="list_author_cls"><?php echo $list_author; ?></div>
        <?php } ?>
    </div>
        <div class="count_div"> 
            <span id="count_visit_span" data-totalvisits="<?php echo $total_visits_long; ?>">
                <?php
                echo $total_visits;
                ?>
            </span>
            <?php if($total_visits_long > 999){ ?>
            <span id="visit_actual_count"><?php echo $total_visits_long; ?></span>
            <?php } ?>
        </div>
        </div>
    
        <?php
        $hide_desc = '';
        $locked_desc = '';
        if($list_desc == ''){
            $hide_desc = ' hiden_desc';
        }
        if($is_locked > 0 && $current_user != $list_owner_id){
            $locked_desc = ' no_hover_table';
        }
        
        ?>
        <div class="list_desc_div<?php echo $hide_desc . $locked_desc; ?>">
            <?php
            $list_desc = preg_replace('$(\s|^)(https?://[a-z0-9_./?=&-]+)(?![^<>]*>)$i', ' <a href="$2" target="_blank">$2</a> ', nl2br($list_desc));
            $list_desc = preg_replace('$(\s|^)(www\.[a-z0-9_./?=&-]+)(?![^<>]*>)$i', ' <a href="$2" target="_blank">$2</a> ', nl2br($list_desc));
            ?>
            <span id="list_desc_text" class="list_desc_text" data-listid="<?php echo $list_id; ?>"><?php echo $list_desc; ?></span>
            <span id="list_desc_span" style="display: none;">
                <textarea cols="10" rows="3" id="list_desc" data-listid="<?php echo $list_id; ?>" style="resize: none;">
                    <?php
                    if ($list_desc != '') {
                        echo $list_desc;
                    } else {
                        echo 'Click here to add description.';
                    }
                    ?>
                </textarea>
            </span>


        </div>
    
    
    <?php
    $access_class = '';
    $tooltip_locked = '';
    if($allowed_access == 2){
        $access_class = ' no-pointer';
        $tooltip_locked = 'This list is locked by it\'s owner!';
    }
    $pointer_class = '';
    if($list_id == 0){
        $pointer_class = ' no-pointer-icons';
    }
    $style = '';
    if(!isset($_SESSION['logged_in']) && ($is_locked == 2 || $is_locked == 1)){
        $style = 'display: none;';
        if(in_array($list_id, $stored_modify_pass)){
            $style = '';
        }
    }
    ?>
    <span class="config_icons hide_data<?php echo $pointer_class . $no_hover_heading; ?>" id="config_icons" data-toggle="tooltip" title="<?php echo $tooltip_locked; ?>" data-placement="bottom" style="<?php echo $style; ?>">
        <?php
        $allow_move_config = 0;
        if ($config['allow_move'] == 'True') {
            $allow_move_config = 1;
        }
        $show_completed_config = 0;
        if ($config['show_completed'] == 'True') {
            $show_completed_config = 1;
        }
        $allow_undo_config = 0;
        if ($config['allow_undo'] == 1) {
            $allow_undo_config = 1;
        }
        $allow_maybe_config = 0;
        if ($config['allow_maybe'] == 1) {
            $allow_maybe_config = 1;
        }
        $show_time = 0;
        if ($config['show_time'] == 1) {
            $show_time = 1;
        }
        $show_nexup_cmnt = 0;
        if($config['enable_comment'] == 1){
            $show_nexup_cmnt = 1;
        }
        ?>
        <a class="add_column_url custom_cursor icon-add" data-toggle="tooltip" title="" data-original-title="New Column"><img src="<?php echo base_url(); ?>assets/img/add_col_icon.png"></a>
        <a data-toggle="modal" data-target="#listConfig" id="listConfig_lnk" class="icon-wrench custom_cursor<?php echo $access_class; ?>" data-moveallow="<?php echo $allow_move_config; ?>" data-showcompleted="<?php echo $show_completed_config; ?>" data-allowcmnt="<?php echo $show_nexup_cmnt; ?>" data-allowundo="<?php echo $allow_undo_config; ?>" data-allowmaybe="<?php echo $allow_maybe_config; ?>" data-showtime="<?php echo $show_time; ?>" data-visiblesearch="<?php echo $config['visible_in_search']; ?>" data-toggle="tooltip" title="Settings" data-placement="bottom" data-allowappendLocked="<?php echo $config['allow_append_locked']; ?>" data-allowedAttendanceComment="<?php echo $config['enable_attendance_comment'] ?>" data-haspass="<?php echo $config['has_password']; ?>" data-collapsed="<?php echo $config['start_collapsed']; ?>"> </a>
        <div class="ddl_lt">

            <a id="listTypes_lnk" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" class="icon-list custom_cursor<?php echo $access_class; ?>" title="List Types" data-placement="bottom"> </a>
            <ul class="dropdown-menu" id="listType_dd" aria-labelledby="listTypes_lnk">
                <li class="list-type-header">Fixed Lists</li>
                <?php
                foreach ($list_types as $listType):
                    if ($listType['is_actionable'] == 0) {
                        $inactive_class = '';
                        if ($listType['is_active'] == 0) {
                            $inactive_class = ' inactive_list_type';
                        }
                        ?>
                        <li id="listType_<?php echo $listType['ListTypeId']; ?>" class="list_type_cls custom_cursor<?php echo $inactive_class; ?>" data-typeId="<?php echo $listType['ListTypeId']; ?>" data-listid="<?php echo $list_id; ?>">
                            <img class="list_type_icon" src="<?php echo base_url(); ?>assets/img/<?php echo $listType['icon'] ?>">
                            <span class="list_type_name"><?php echo $listType['ListTypeName']; ?></span>
                            <?php // echo $listType['ListTypeName']; ?>
                        </li>
                        <?php
                    }
                endforeach;
                ?>
                <li class="list-type-header">Action Lists</li>
                <?php
                foreach ($list_types as $listType):
                    if ($listType['is_actionable'] == 1) {
                        $inactive_class = '';
                        if ($listType['is_active'] == 0) {
                            $inactive_class = ' inactive_list_type';
                        }
                        ?>
                        <li id="listType_<?php echo $listType['ListTypeId']; ?>" class="list_type_cls custom_cursor<?php echo $inactive_class; ?>" data-typeId="<?php echo $listType['ListTypeId']; ?>" data-listid="<?php echo $list_id; ?>">
                            <img class="list_type_icon" src="<?php echo base_url(); ?>assets/img/<?php echo $listType['icon'] ?>">
                            <span class="list_type_name"><?php echo $listType['ListTypeName']; ?></span>
                            <?php // echo $listType['ListTypeName']; ?>
                        </li>
                        <?php
                    }
                endforeach;
                ?>
            </ul>
        </div>

        <?php
        $hide_bulk_cls = '';
        if ($list_id == 0) {
            $hide_bulk_cls = ' hdn_bulk';
        }
        ?>

        <a class="bulk_add_cls custom_cursor<?php echo $hide_bulk_cls . $access_class; ?>" id="add_bulk_data" data-id="<?php echo $list_id; ?>" data-slug="<?php echo $list_slug; ?>" data-toggle="tooltip" title="Bulk data entry" data-placement="bottom"></a>


        <?php
        $class_lock = '';
        if (!$this->session->userdata('logged_in')) {
            $class_lock = 'lock_hide';
        }
        if ($is_locked == 0) {
            ?>
            <a class="icon-lock-open2 custom_cursor <?php echo $class_lock . $access_class; ?>" id="listLock_lnk" data-id="<?php echo $list_id; ?>" data-slug="<?php echo $list_slug; ?>" data-toggle="tooltip" title="Lock List" data-placement="bottom"></a>
            <?php
        } else {
            ?>
            <a class="icon-lock2 custom_cursor <?php echo $class_lock . $access_class; ?>" id="listUnlock_lnk" data-id="<?php echo $list_id; ?>" data-slug="<?php echo $list_slug; ?>" data-toggle="tooltip" title="Unlock List" data-placement="bottom"></a>
            <?php
        }
        ?>
            
        <?php
        $hide_desc_cls = '';
        if ($list_id == 0 || $allowed_access == 0) {
            $hide_desc_cls = ' hdn_desc';
        }
        ?>
        <a class="add_data_desc custom_cursor<?php echo $hide_desc_cls . $access_class; ?>" id="add_data_desc" data-id="<?php echo $list_id; ?>" data-slug="<?php echo $list_slug; ?>" data-toggle="tooltip" title="Add list description" data-placement="bottom">
            <img src="/assets/img/pencil.png">
        </a>
            
        <?php
        $disable_delete = '';
        if ((!isset($_SESSION['logged_in']) && $list_owner_id > 0) || ($allowed_access == 0)) {
            $disable_delete = ' disabled_btn';
        }
        ?>
        <?php
        if ($type_id == 11) {
            $disable_reset = '';
            $click_alert = '';
            if ((!isset($_SESSION['logged_in']) && $list_owner_id > 0) || ($allowed_access == 0)) {
                $click_alert = ' login_prompt"';
                $disable_reset = ' disabled_btn';
            }
            ?>
            <a class="reset_list btn btn-sm bth-default<?php echo $click_alert; ?>" id="reset_list" data-listid="<?php echo $list_id; ?>" data-toggle="tooltip" data-placement="bottom" title="Reset List">
                <img src="/assets/img/rotate-left.png">
            </a>
            <?php
        }
        ?>
        <a id="delete_list_builder" class="delete_list_builder custom_cursor<?php echo $disable_delete . $access_class; ?>" data-id="<?php echo $list_id; ?>" data-slug="<?php echo $list_slug; ?>" data-toggle="tooltip" data-placement="bottom" title="Delete List" style="<?php echo $hide_list; ?>"><img src="/assets/img/rubbish-bin.png"></a>
        <?php
        $disabled_copy = '';
        if(!isset($_SESSION['logged_in'])){
            $disabled_copy = '';
        }
        ?>
        <a id="copy_list_btn" class="copy-list-btn copy-list-btn-items-page custom_cursor<?php echo $access_class; ?>" data-id="<?php echo $list_id; ?>" data-slug="<?php echo $list_slug; ?>" data-toggle="tooltip" data-placement="bottom" title="Copy List" style="<?php echo $disabled_copy; ?>"><img src="/assets/img/copy.png"></a>
        <button type="button" class="btn btn-default enable-move hide_move_btn" data-toggle="tooltip" data-placement="bottom" data-title="Rearrange"><img src="/assets/img/move.png"></button>
    </span>


    <?php
    $first_key = 0;
    if (!empty($tasks)) {
        $first_key = key($tasks);
    }
    if ($type_id == 2 || $type_id == 8) {
        ?>
        <div class="whoisnext-div">
            <div class="nexup-group">
                <?php
                $btn_cls = '';
                if (empty($tasks)) {
                    $btn_cls = ' whosnext_img_bg';
                }
                ?>
                <div class="nexup-group-two">
                    <div class="button-outer custom_cursor<?php echo $btn_cls; ?>">
                        <?php
                        if($type_id == 2){
                        ?>
                        <div class="nexup-sub-group nexup-sub-group-one" data-toggle="tooltip" title="<?php
                        if (!empty($tasks)) {
                            if (!empty($last_log)) {
                                $last_log_arr = explode(',', $last_log);
                                $data_print = $last_log_arr[0];
                                foreach ($tasks as $tid => $tdt):
                                    if ($tdt[0]['TaskId'] == $data_print) {
                                        echo $tdt[0]['TaskName'];
                                    }
                                endforeach;
                            } else {
                                echo $tasks[$first_key][0]['TaskName'];
                            }
                        }
                        ?>">
                            <span id="next_task_name"><?php
                                if (!empty($tasks)) {
                                    if (!empty($last_log)) {
                                        $f_key = 1;
                                        $last_log_arr = explode(',', $last_log);
//                                    p($last_log_arr); exit;
                                        $data_print = $last_log_arr[0];
//                                    echo $data_print; exit;
//                                    p($tasks); exit;
                                        foreach ($tasks as $t_key => $t_det):

//                                        p($t_det);
                                            foreach ($t_det as $t):
                                                if ($t['TaskId'] == $data_print) {
                                                    $f_key = $t_key;
                                                }
                                            endforeach;
                                        endforeach;
//                            echo $f_key; exit;
//                                    p($f_key); exit;
                                        echo $tasks[$f_key][0]['TaskName'];
                                    } else {
                                        echo $tasks[$first_key][0]['TaskName'];
                                    }
                                }
                                ?></span>
                            <!--<span class="tooltiptext-task-next"></span>-->
                        </div>
                        <?php if ($multi_col == 1) { ?>
                            <div class="nexup-sub-group nexup-sub-group-two">
                                <?php
                                $row_id = 1;
                                if (!empty($last_log)) {
//                                    $last_log_arr = explode(',', $last_log);
                                    $did = $last_log;
                                    $cnt_row_ids = 1;
                                    foreach ($tasks as $task_key => $tdet):
                                        foreach ($tdet as $t):
                                            if ($t['TaskId'] == $did) {
                                                $first_key = $task_key;
                                                $row_id = $cnt_row_ids;
                                            }
                                        endforeach;
                                        $cnt_row_ids++;
                                    endforeach;
                                }
                                if (isset($tasks[$first_key])) {
                                    $t_cnt = 1;
                                    foreach ($tasks[$first_key] as $id_t => $t):
                                        if ($t_cnt > 3) {
                                            break;
                                        }
                                        if ($id_t > 0) {
                                            ?>
                                            <span data-toggle="tooltip" title="<?php echo trim($t['TaskName']); ?>"><?php echo $t['TaskName']; ?></span>
                                            <?php
                                            $t_cnt++;
                                        }
                                    endforeach;
                                    ?>
                                    <?php
                                }
                                ?>
                                <p data-toggle="modal" data-target="#items-list" id="items_model_p" data-rowid='<?php echo $row_id; ?>' data-toggle="tooltip" data-placement="top" title="Show all items">
                                    <img src="<?php echo base_url() . 'assets/img/information-button-icon-23.png'; ?>">
                                </p>
                            </div>
                        <?php
                                        }
                        }elseif($type_id == 8){
                        ?>
                        <div class="nexup-sub-group nexup-sub-group-single" data-toggle="tooltip" title="<?php
                        if (!empty($tasks)) {
                            if (!empty($last_log)) {
                                $last_log_arr = explode(',', $last_log);
                                $data_print = $last_log_arr[0];
                                foreach ($tasks as $tid => $tdt):
                                    if ($tdt[0]['TaskId'] == $data_print) {
                                        echo trim($tdt[0]['TaskName']);
                                    }
                                endforeach;
                            } else {
                                echo trim($tasks[$first_key][0]['TaskName']);
                            }
                        }
                        ?>">
                            <span id="next_task_name"><?php
                                if (!empty($tasks)) {
                                    if (!empty($last_log)) {
                                        $f_key = 1;
                                        $last_log_arr = explode(',', $last_log);
//                                    p($last_log_arr); exit;
                                        $data_print = $last_log_arr[0];
//                                    echo $data_print; exit;
//                                    p($tasks); exit;
                                        foreach ($tasks as $t_key => $t_det):

//                                        p($t_det);
                                            foreach ($t_det as $t):
                                                if ($t['TaskId'] == $data_print) {
                                                    $f_key = $t_key;
                                                }
                                            endforeach;
                                        endforeach;
//                            echo $f_key; exit;
//                                    p($f_key); exit;
                                        echo $tasks[$f_key][0]['TaskName'];
                                    } else {
                                        echo $tasks[$first_key][0]['TaskName'];
                                    }
                                }
                                ?></span>
                            <!--<span class="tooltiptext-task-next"></span>-->
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                    <div id="nexup_btns">
                        <?php
                        $undo_class = '';
                        if ($config['allow_undo'] == 0) {
                            $undo_class = ' disabled_undo';
                        }
                        ?>
                        <div class="cmnt-btn-div cmnt-btn-div_change">
                            <div class="next_btn_div">
                                <a class="whoisnext-btn-cmnt custom_cursor next_btn" data-toggle="tooltip" data-placement="top" title="Nexup"><span id="nexup_icon_cmnt" class="">
                                    <img class="defult_arrow" src="/assets/img/next-arrow.png"><img class="hover_arrow" src="/assets/img/next-arrow-white.png"></span>
                                </a>
                                <?php
                                $hide_comment_box = '';
                                if($config['enable_comment'] == 0){
                                    $hide_comment_box = ' hide_box';
                                }
                                ?>
                                <span class="add-data-div add_comment_box<?php echo $hide_comment_box; ?>" id="nexup_cmnt_span">
                                    <input type="text" id="nexup_comment" class="nexup_comment" placeholder="Comment...">
                                </span>
                            </div>
                            <a class="undo-btn prev_btn custom_cursor<?php echo $undo_class; ?>" data-listid="<?php echo $list_id; ?>" data-toggle="tooltip" data-placement="top" title="Backup"><span id="undo_icon" class=""><img class="defult_arrow" src="/assets/img/prev-arrow.png"><img class="hover_arrow" src="/assets/img/prev-arrow-white.png"><img class="grey_arrow" src="/assets/img/prev-arrow-grey.png"></span></a>
                            <div class="h-nav dropdown log_icon_btn">
                                <a title="Log" class="custom_cursor" id="dropdownMenuLog" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><img class="defult_arrow" src="/assets/img/log_icon.png"><img class="hover_arrow" src="/assets/img/log_icon-white.png"></a>
                                
                                    <?php
                                    $i = 0;
                                    if (!empty($log_list)) {
                                    ?>
                                    <ul class="dropdown-menu" id="log_dd" aria-labelledby="dropdownMenuLog">
                                    <?php
                                        foreach ($log_list as $key_log => $log):
                                            $comment_class = '';
                                            if($log['comment'] == ''){
                                                $comment_class = ' no-comt-class';
                                            }
                                            if ($i > 5) {
                                                break;
                                            }
                                            $hourdiff = round((strtotime(date('Y-m-d H:i:s')) - strtotime($log['created'])) / 3600, 1);
                                            $cmt = '<span class="cmt_val' . $comment_class . '">' . $log['comment'] . '</span><span class="comment-span">(' . $log['created'] . ')</span>';
                                            if($log['comment'] == ''){
                                                $cmt = '<span class="cmt_val' . $comment_class . '">no comment</span><span class="comment-span">(' . $log['created'] . ')</span>';
                                            }
                                            if ($hourdiff > 1 && $hourdiff < 24) {
                                                if (floor($hourdiff) > 1) {
                                                    $hrs = ' hours';
                                                } else {
                                                    $hrs = ' hour';
                                                }
                                                $cmt = '<span class="cmt_val' . $comment_class . '">' . $log['comment'] . '</span><span class="comment-span">(' . floor($hourdiff) . $hrs . ' ago)</span>';
                                                if($log['comment'] == ''){
                                                    $cmt = '<span class="cmt_val' . $comment_class . '">no comment</span><span class="comment-span">(' . floor($hourdiff) . $hrs . ' ago)</span>';
                                                }
                                            } elseif ($hourdiff <= 1) {
                                                $min_dif = $hourdiff * 60;
                                                if (floor($min_dif) > 1) {
                                                    $minutes = ' minutes';
                                                } else {
                                                    $minutes = ' minute';
                                                }
                                                if ($min_dif > 0) {
                                                    $cmt = '<span class="cmt_val' . $comment_class . '">' . $log['comment'] . '</span><span class="comment-span">(' . floor($min_dif) . $minutes . ' ago)</span>';
                                                    if($log['comment'] == ''){
                                                        $cmt = '<span class="cmt_val' . $comment_class . '">no comment</span><span class="comment-span">(' . floor($min_dif) . $minutes . ' ago)</span>';
                                                    }
                                                } else {
                                                    $cmt = '<span class="cmt_val' . $comment_class . '">' . $log['comment'] . '</span><span class="comment-span">(Just Now)</span>';
                                                    if($log['comment'] == ''){
                                                        $cmt = '<span class="cmt_val' . $comment_class . '">no comment</span><span class="comment-span">(Just Now)</span>';
                                                    }
                                                }
                                            }

                                            if (isset($_SESSION['logged_in'])) {
//                                                if ($log['user_id'] == $_SESSION['id']) {
                                                if (!empty($log['comment'])) {
                                                    $cmt = '<span class="cmt_val' . $comment_class . '">' . $log['comment'] . '</span><span class="comment-span">(' . $log['created'] . ')</span>';
                                                    if ($hourdiff > 1 && $hourdiff < 24) {
                                                        if (floor($hourdiff) > 1) {
                                                            $hrs = ' hours';
                                                        } else {
                                                            $hrs = ' hour';
                                                        }
                                                        $cmt = '<span class="cmt_val' . $comment_class . '">' . $log['comment'] . '</span><span class="comment-span">(' . floor($hourdiff) . $hrs . ' ago)</span>';
                                                    } elseif ($hourdiff <= 1) {
                                                        $min_dif = $hourdiff * 60;
                                                        if ($min_dif > 0) {
                                                            if (floor($min_dif) > 1) {
                                                                $minutes = ' minutes';
                                                            } else {
                                                                $minutes = ' minute';
                                                            }
                                                            $cmt = '<span class="cmt_val' . $comment_class . '">' . $log['comment'] . '</span><span class="comment-span">(' . floor($min_dif) . $minutes . ' ago)</span>';
                                                        } else {
                                                            $cmt = '<span class="cmt_val' . $comment_class . '">' . $log['comment'] . '</span><span class="comment-span">(Just Now)</span>';
                                                        }
                                                    }
                                                }
                                                ?>
                                                <li class='log_options'><?php echo $cmt; ?></li>
                                                <?php
//                                                }
                                            } else {

                                                if ($allowed_access == 1) {
                                                    if (!empty($log['comment'])) {
                                                        $cmt = '<span class="cmt_val' . $comment_class . '">' . $log['comment'] . '</span><span class="comment-span">(' . $log['created'] . ')</span>';
                                                        if ($hourdiff > 1 && $hourdiff < 24) {
                                                            if (floor($hourdiff) > 1) {
                                                                $hrs = ' hours';
                                                            } else {
                                                                $hrs = ' hour';
                                                            }
                                                            $cmt = '<span class="cmt_val' . $comment_class . '">' . $log['comment'] . '</span><span class="comment-span">(' . floor($hourdiff) . $hrs . ' ago)</span>';
                                                        } elseif ($hourdiff <= 1) {
                                                            $min_dif = $hourdiff * 60;
                                                            if ($min_dif > 0) {
                                                                if (floor($min_dif) > 1) {
                                                                    $minutes = ' minutes';
                                                                } else {
                                                                    $minutes = ' minute';
                                                                }
                                                                $cmt = '<span class="cmt_val' . $comment_class . '">' . $log['comment'] . '</span><span class="comment-span">(' . floor($min_dif) . $minutes . ' ago)</span>';
                                                            } else {
                                                                $cmt = '<span class="cmt_val' . $comment_class . '">' . $log['comment'] . '</span><span class="comment-span">(Just Now)</span>';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <li class='log_options'><?php echo $cmt; ?></li>          
                                                    <?php
                                                }
                                            }
                                            $i++;
                                        endforeach;
                                        ?>
                                    </ul>
                                    <?php
                                    }
                                    ?>
                                
                            </div>
                            
                        </div>


                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    if ($type_id == 11) {
        ?>

        <div class="count_box">
            <div class="full_hover_ef">
            <ul>
                <li class="green_box">Yes <span id="yes_cnt"><?php echo $total_yes; ?></span></li>
                <?php
                $style_attr = '';
                if($config['allow_maybe'] == 0){
                    $style_attr = 'display:none;';
                }
                ?>
                <li class="yellow_box" style="<?php echo $style_attr; ?>">Maybe <span id="maybe_cnt"><?php echo $total_maybe ?></span></li>
                <li class="red_box">No <span id="no_cnt"><?php echo $total_no; ?></span></li>
                <li class="white_Box">Unresponded <span id="blank_cnt"><?php echo $total_blank; ?></span></li>
            </ul>
            <div class="drop_copy_summary">
                <a class="icon-more" id="copy_summary_dd" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></a>
                <ul class="dropdown-menu" id="summary_dd" aria-labelledby="summary_lnk">
                    <li class="copy_summary_li" onclick="copySummaryToClipboard();">Copy</li>
                    <li class="copy_summary_details_li" onclick="copySummaryDetailsToClipboard();">Copy w/details</li>
                    <li class="copy_summary_details_comments_li" onclick="copySummaryDetailsToClipboard('comments');">Copy w/details & comments</li>
                    <textarea id="hdn_summary" name="hdn_summary" style="position: absolute;left: -10000px;"></textarea>
                </ul>
            </div>
        </div>
    </div>

        <?php
    }
    ?>
    
    
    <div class="plus-category" data-access="<?php echo $allowed_access; ?>">

        <?php
        $class_hide_settings = '';
        $calss_hide_lock = '';
//        if (isset($_SESSION['logged_in']) && $_SESSION['id'] != $list_owner_id) {
        if (isset($_SESSION['logged_in']) && $is_locked == 2) {
            if ($list_owner_id > 0) {
                $class_hide_settings = ' hide_config';
            }
        } elseif (!isset($_SESSION['logged_in']) && ($is_locked == 1)) {
            $class_hide_settings = ' hide_config';
        } else {
            $calss_hide_lock = ' hide_lock';
        }
        if ($is_locked == 1 || $is_locked == 2) {
            ?>
            <a class="icon-lock2 custom_cursor<?php echo $calss_hide_lock; ?>" id="config_lcoked" <?php
            if ($is_locked == 1) {
                echo 'data-locked="1"';
            } elseif ($is_locked == 2) {
                echo 'data-locked="2"';
            } else {
                echo 'data-locked="0"';
            }
            ?>></a>
               <?php
           }
           ?>
        <a class="icon-settings custom_cursor<?php echo $class_hide_settings; ?>" id="config_lnk" <?php
        if ($is_locked == 1) {
            echo 'data-locked="1"';
        } elseif ($is_locked == 2) {
                echo 'data-locked="2"';
            } else {
            echo 'data-locked="0"';
        }
        ?> data-typeid="<?php echo $type_id; ?>"  data-toggle="tooltip" title="Configuration" data-placement="bottom" data-showprev="<?php echo $config['show_preview']; ?>" data-showowner="<?php echo $config['show_author']; ?>"></a>
    </div>

    <div class="add-data-body add-data-body-new <?php echo $collapsable_div; ?>">
        <div class="added_div" id="added_div">
            
            <div id="addTaskDiv" class="item-add-div multi-column-lists">
                <h3 id="TaskListHead"></h3>
                <?php
                $sort_class = '';
                $move_btn_cls = '';
                if ($config['allow_move'] == 'True') {
                    $sort_class = 'tasks_lists_display';
                }
                if (empty($tasks) || $config['allow_move'] != 'True') {
                    $move_btn_cls = ' hide_move_btn';
                }
                $task_class = '';
                $task_list_div_class = '';
                if (!empty($tasks)) {
                    $task_size = count($tasks[$first_key]);
                    if ($multi_col == 1) {
                        if ($task_size == 2) {
                            $task_class = ' column-2';
                        } elseif ($task_size == 3) {
                            $task_class = ' column-3';
                        } elseif ($task_size > 3) {
                            $task_class = ' column-4';
                        }
                        if ($task_size > 2) {
                            $task_list_div_class = ' task_multi_col_div';
                        }
                    }
                }
                ?>

                <!--<div class="button_wrapper move_btn_wrapper"></div>-->
                <div id="TaskListDiv" class="column-css new_add_col_custom<?php echo $task_class . $task_list_div_class; ?>">
                    <?php
                    $table_checkbox_class = '';
                    $locked_my_div = '';
                    if ($is_locked == 1) {
                        $locked_my_div = 'style="display: initial;"';
                    }
                    $attendance_class = '';
                    if($type_id == 11){
                        $attendance_class = ' attendance_list_class';
                        $table_checkbox_class = ' check_box_table';
                    }
                    if($type_id == 5){
                        $table_checkbox_class = ' check_box_table';
                    }
                    ?>
                    <div class="my_table my_scroll_table">
                        <table id="test_table" class="table<?php echo $no_hover_table . $table_checkbox_class; ?>">
                            <?php
                            if ($multi_col == 0) {
                                ?>
                                <thead>
                                    <?php
                                    $heading_hidden_class = '';
                                    $nodrag_hidden_class = '';
                                    $nodrag_hidden_comment_class = '';
                                    $nodrag_hidden_row_class = '';
                                    if($type_id == 11 && $config['enable_attendance_comment'] == 0){
                                        $nodrag_hidden_comment_class = ' hidden_nodrag';
                                    }
                                    if ($type_id != 11) {
                                        $heading_hidden_class = ' hidden_heading';
                                        $nodrag_hidden_comment_class = ' hidden_nodrag';
                                        $nodrag_hidden_class = ' hidden_nodrag';
                                        $nodrag_hidden_row_class = '';
                                    }
                                    if($type_id == 5){
                                        $nodrag_hidden_row_class = ' hidden_nodrag';
                                    }
                                    if ($config['show_time'] == 0) {
                                        $heading_hidden_class = ' hidden_heading';
                                        $nodrag_hidden_class = ' hidden_nodrag';
                                    }
                                    ?>
                                    <tr class="td_add_tr">
                                        
                                        <th class="noDrag nodrag_action_heading"></th>
                                        <?php
                                        if ($type_id == 3) {
                                            ?>
                                            <th class="noDrag rank_th_head"></th>
                                            <?php
                                        }
                                        ?>
                                        <?php
//                                        if($type_id == 11){
                                        ?>

                                        <?php
//                                        }
                                        $placeholder = 'Add What is your List\'s name?';
                                        if(isset($columns)){
                                            if($columns[0]['type'] == 'text' || $columns[0]['type'] == 'memo'){
                                                if(!empty($columns)){
                                                    $placeholder = 'Add ' . $columns[0]['column_name'];
                                                }else{
                                                    $placeholder = 'item';
                                                }
                                            } elseif($columns[0]['type'] == 'datetime'){
                                                $placeholder = 'MM/DD/YYYY HH:MM';
                                            } elseif($columns[0]['type'] == 'date'){
                                                $placeholder = 'MM/DD/YYYY';
                                            } elseif($columns[0]['type'] == 'time'){
                                                $placeholder = 'HH:MM';
                                            } elseif($columns[0]['type'] == 'currency'){
                                                $placeholder = '$';
                                            } elseif($columns[0]['type'] == 'number'){
                                                $placeholder = 'Add ' . $columns[0]['column_name'];
                                            } elseif($columns[0]['type'] == 'email'){
                                                $placeholder = 'username@domain.com';
                                            } elseif($columns[0]['type'] == 'link'){
                                                $placeholder = 'http://example.com';
                                            }
                                        }
                                        ?>

                                        <th class="heading_items_col_add<?php echo $hide_add_item_cls; echo $hide_add_row; ?>" data-listid="<?php echo $list_id; ?>" data-colid="<?php if(isset($columns[0]['id'])){ echo $columns[0]['id']; } else { echo 0; } ?>">
                                <div class="add-data-input">
                                    <?php if(isset($columns) && $columns[0]['type'] == 'memo'){ ?>
                                    <textarea name="task_name" id="task_name" class="task_name" data-listid="<?php echo $list_id; ?>" data-colid="<?php echo $columns[0]['id']; ?>" placeholder="<?php echo $placeholder; ?>" data-type="<?php echo $columns[0]['type']; ?>" data-gramm_editor="false" rows="5" <?php if(isset ($columns[0]) && ($columns[0]['type'] == 'checkbox' || $columns[0]['type'] == 'timestamp')){ echo 'style="display: none;"'; } ?>></textarea>
                                    <?php } elseif(isset($columns) && $columns[0]['type'] == 'number'){ ?>
                                    <input type="number" name="task_name" id="task_name" class="task_name" data-listid="<?php echo $list_id; ?>" data-colid="<?php if (isset($columns) && !empty($columns)) { echo $columns[0]['id']; } else { echo '0'; } ?>" placeholder="<?php echo $placeholder; ?>" data-type="<?php if (isset($columns) && !empty($columns)) { echo $columns[0]['type']; } ?>"  <?php if(isset ($columns[0]) && ($columns[0]['type'] == 'checkbox' || $columns[0]['type'] == 'timestamp')){ echo 'style="display: none;"'; } ?>/>
                                    <?php } else { ?>
                                    <input type="text" name="task_name" id="task_name" class="task_name" data-listid="<?php echo $list_id; ?>" data-colid="<?php if (isset($columns) && !empty($columns)) { echo $columns[0]['id']; } else { echo '0'; } ?>" placeholder="<?php echo $placeholder; ?>" data-type="<?php if (isset($columns) && !empty($columns)) { echo $columns[0]['type']; } ?>"  <?php if(isset ($columns[0]) && ($columns[0]['type'] == 'checkbox' || $columns[0]['type'] == 'timestamp')){ echo 'style="display: none;"'; } ?>/>
                                    <?php } ?>
                                    <span class="span_enter"><img src="/assets/img/enter.png" class="enter_img"/></span>
                                    <?php if(isset($columns) && $columns[0]['type'] == 'checkbox'){ ?>
                                    <input type="checkbox" id="check_all_<?php if (isset($columns) && !empty($columns)) { echo $columns[0]['id']; } ?>" class="check_all_check_box" data-colid="<?php if (isset($col) && !empty($col)) { echo $col['id']; } ?>">
                                    <?php }elseif(isset($columns) && $columns[0]['type'] == 'timestamp'){ ?>
                                    <a class="btn btn-default timestamp-all-btn" id="timestamp_<?php if (isset($columns) && !empty($columns)) { echo $columns[0]['id']; } ?>" data-colid="<?php if (isset($columns) && !empty($columns)) { echo $columns[0]['id']; } ?>">Timestamp</a>
                                    <?php } ?>
                                </div>
                                </th>
                                <th class="noDrag nodrag_comment<?php echo $nodrag_hidden_comment_class; ?>"></th>
                                <th class="noDrag nodrag_time<?php echo $nodrag_hidden_class; ?>"></th>
                                </tr>
                                <tr class="td_arrange_tr">
                                    <?php
                                    $heading_hidden_class = '';
                                    $nodrag_hidden_class = '';
                                    $nodrag_hidden_comment_class = '';
                                    $nodrag_hidden_attendee = '';
                                    if($type_id == 11 && $config['enable_attendance_comment'] == 0){
                                        $nodrag_hidden_comment_class = ' hidden_nodrag';
                                    }
                                    if ($type_id != 11) {
                                        $heading_hidden_class = ' hidden_heading';
                                        $nodrag_hidden_class = ' hidden_nodrag';
                                        $nodrag_hidden_comment_class = ' hidden_nodrag';
                                        $nodrag_hidden_attendee = ' hidden_nodrag';
                                    }
                                    if ($config['show_time'] == 0) {
//                                        $heading_hidden_class = ' hidden_heading';
                                        $nodrag_hidden_class = ' hidden_nodrag';
                                    }
                                    ?>
                                    
                                    <th class="noDrag nodrag_actions">
                                    
    <?php
//                                        if($type_id == 11){
    ?>
                                <div class="add-data-title-nodrag<?php echo $nodrag_hidden_attendee; ?> status-column">
                                    <!--<span class="column_name_class" id="col_name_fixed">Status</span>-->
                                </div>
                                <?php
//                                        }
                                ?>
                                </th>
                                <?php
                                    if ($type_id == 3) {
                                        ?>
                                            <th class="noDrag rank_th_head"></th>
                                        <?php
                                    }
                                    ?>

                                <?php
                                if (!empty($columns)) {
                                    foreach ($columns as $ids => $col):
                                        if ($ids <= 0) {
                                            $task_ul_id = 'TaskList';
                                        } else {
                                            $task_ul_id = 'TaskList' . $ids;
                                        }
                                        ?>
                                        <th class="heading_items_col<?php echo $heading_hidden_class; ?>" data-listid="<?php echo $list_id; ?>" data-colid="<?php echo $col['id']; ?>">
                                        <div class="add-data-title-r">
                                            <a href="" class="icon-more-h move_col ui-sortable-handle<?php echo $hide_rearrange_class; ?>" id="dropdownMenu0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="visibility: hidden;"></a>
                                            <a class="remove_col custom_cursor icon-cross-out" data-colid="<?php echo $col['id']; ?>" data-listid="<?php echo $list_id; ?>" style="visibility: hidden;"></a>
                                            <!--                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu0">
                                                                                                <li><a class="remove_col custom_cursor" data-colid="<?php echo $col['id']; ?>" data-listid="<?php echo $list_id; ?>">Remove</a></li>
                                                                                            </ul>-->
                                        </div>
                                        <div class="add-data-title" data-colid="<?php echo $col['id']; ?>" data-listid="<?php echo $list_id; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $col['column_name']; ?>" data-type="<?php echo $col['type']; ?>">
                                            <span class="column_name_class" id="col_name_<?php echo $col['id']; ?>"><?php echo $col['column_name']; ?></span>
                                        </div>
                                        <a class="icon-more-o icon_listing_table"></a>
                                        <div class="div_option_wrap">  
                                        <ul class="ul_table_option" data-listid="<?php echo $list_id; ?>">
                                            <li><div class="custom_radio_class"><input type="radio" id="text_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="text" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'text'){ echo ' checked'; } ?>><label class="col_type_lbl" for="text_<?php echo $col['id']; ?>">Text</label></div></li>
                                            <li><div class="custom_radio_class"><input type="radio" id="memo_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="memo" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'memo'){ echo ' checked'; } ?>><label class="col_type_lbl" for="memo_<?php echo $col['id']; ?>">Memo</label></div>
                                                <div class="plus_minus_wrap"><span>Height</span><a class="minus_a">-</a><input id="number_rows" type="number" min="1" value="<?php echo $col['height']; ?>"><a class="plus_a">+</a></div>
                                                 </li>
                                            <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="checkbox_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="checkbox" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'checkbox'){ echo ' checked'; } ?>><label class="col_type_lbl" for="checkbox_<?php echo $col['id']; ?>">Check Box</label></div></li>
                                           <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="number_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="number" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'number'){ echo ' checked'; } ?>><label class="col_type_lbl" for="number_<?php echo $col['id']; ?>">Number</label></div></li>
                                           <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="currency_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="currency" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'currency'){ echo ' checked'; } ?>><label class="col_type_lbl" for="currency_<?php echo $col['id']; ?>">Dollar</label></div></li>

                                            <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="datetime_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="datetime" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'datetime'){ echo ' checked'; } ?>><label class="col_type_lbl" for="datetime_<?php echo $col['id']; ?>">Date Time</label></div></li>
                                            <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="date_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="date" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'date'){ echo ' checked'; } ?>><label class="col_type_lbl" for="date_<?php echo $col['id']; ?>">Date</label></div></li>
                                            <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="time_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="time" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'time'){ echo ' checked'; } ?>><label class="col_type_lbl" for="time_<?php echo $col['id']; ?>">Time</label></div></li>
                                           <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="timestamp_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="timestamp" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'timestamp'){ echo ' checked'; } ?>><label class="col_type_lbl" for="timestamp_<?php echo $col['id']; ?>">Time Stamp</label></div></li>
                                           <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="link_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="link" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'link'){ echo ' checked'; } ?>><label class="col_type_lbl" for="link_<?php echo $col['id']; ?>">Link</label></div></li>
                                           <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="email_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="email" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'email'){ echo ' checked'; } ?>><label class="col_type_lbl" for="email_<?php echo $col['id']; ?>" style="font-style: italic;">Email</label></div></li>
                                            <li class="disabled-radio-class"><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="inflo_ob_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="infloobject" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'infloobject'){ echo ' checked'; } ?> disabled="disabled"><label class="col_type_lbl" for="inflo_ob_<?php echo $col['id']; ?>" style="font-style: italic;">Inflo Object</label></div></li>
                                             </ul>
                                            </div>

                                        </th>
            <?php
        endforeach;
    }
    ?>
                                <th class="noDrag nodrag_comment<?php echo $nodrag_hidden_comment_class; ?>">
                                <div class="add-data-title-nodrag" data-toggle="tooltip" data-placement="bottom" data-original-title="Comment">
                                    <span class="column_name_class" id="col_name_fixed2">Comment</span>
                                </div>
                                </th>
                                <th class="noDrag nodrag_time<?php echo $nodrag_hidden_class; ?>">
                                <div class="add-data-title-nodrag" data-toggle="tooltip" data-placement="bottom" data-original-title="Time">
                                    <span class="column_name_class" id="col_name_checked">Time</span>
                                </div>
                                </th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (isset($tasks) && !empty($tasks)) {
                                        $rnk = 1;
                                        foreach ($tasks as $ids => $task):
                                            $completed_class = '';
                                            if ($type_id == 5 && $task[0]['IsCompleted'] == 1) {
                                                $completed_class = 'completed strikeout';
                                            } elseif ($type_id != 5 && $task[0]['IsCompleted'] == 1) {
                                                $completed_class = 'completed';
                                            }
                                            $hidden_tr_class = '';
                                            if ($config['show_completed'] == 'False' && $task[0]['IsCompleted'] == 1) {
                                                $hidden_tr_class = ' hidden_tbl_row';
                                            }
                                            ?>
                                            <tr class="<?php echo $completed_class . $hidden_tr_class; ?>">
                                                    
                                                <td class="icon-more-holder" data-order="<?php echo $task[0]['order']; ?>" data-listid="<?php echo $list_id; ?>" data-taskname="<?php echo $task[0]['TaskName']; ?>">
                                                    <span class="icon-more<?php echo $attendance_class; ?><?php echo $hide_rearrange_class; ?>"></span>
                                                    <a href="javascript:void(0)" class="icon-cross-out delete_task custom_cursor" data-id="<?php echo $task[0]['TaskId']; ?>" data-task="<?php echo $task[0]['TaskName']; ?>" data-listid="<?php echo $list_id; ?>"></a>
                                                    <?php
                                                           if ($type_id == 5) {
                                                               ?>
                                                        <input type="checkbox" class="complete_task custom_cursor" id="complete_<?php echo $task[0]['TaskId']; ?>" data-id="<?php echo $task[0]['TaskId']; ?>" data-listid="<?php echo $list_id; ?>"<?php
                                                        if ($task[0]['IsCompleted'] == 1) {
                                                            echo ' checked="checked"';
                                                        }
                                                        ?>>
                                                        <label for="complete_<?php echo $task[0]['TaskId']; ?>" class="complete_lbl"> </label>
                                                        <?php
                                                    }

                                                    if ($type_id == 11) {
                                                        ?>
                                                        <input type="checkbox" class="present_task custom_cursor" id="present_<?php echo $task[0]['TaskId']; ?>" data-id="<?php echo $task[0]['TaskId']; ?>" data-listid="<?php echo $list_id; ?>">
                                                        <?php
                                                        $task_class = '';
                                                        if ($task[0]['IsPresent'] == 1) {
                                                            $task_class = ' green_label';
                                                        } elseif ($task[0]['IsPresent'] == 3) {
                                                            $task_class = ' red_label';
                                                        } elseif ($task[0]['IsPresent'] == 2) {
                                                            $task_class = ' yellow_label';
                                                        }
                                                        ?>
                                                        <label for="present_<?php echo $task[0]['TaskId']; ?>" class="present_lbl<?php echo $task_class; ?>"> </label>
                                                    <?php
                                                }
                                                ?>
                                                </td>
                                                <?php
                                                if ($type_id == 3) {
                                                    ?>
                                                <td class="rank_th">
                                                <?php
                                                echo $rnk;
                                                $rnk++;
                                                ?>
                                                </td>
                                                <?php
                                                }
                                                ?>

                                                <?php
                                                if (!empty($attendance_data)) {
                                                    $corder = 0;
                                                    foreach ($task as $tsid => $tsks):

                                                        foreach ($attendance_data as $aid => $adata):
                                                            
                                                            if (preg_match('(,' . $tsks['TaskId'] . '|' . $tsks['TaskId'] . ',|' . $tsks['TaskId'] . ')', $adata['item_ids']) === 1) {
                                                                $a_id = $adata['id'];
                                                                $a_cmnt = $adata['comment'];
                                                                if ($corder != $tsks['order']) {


                                                                    if (!empty($adata['check_date'])) {
                                                                        $hourdiff = round((strtotime(date('Y-m-d H:i:s')) - strtotime($adata['check_date'])) / 3600, 1);
                                                                        $time_checked = $adata['check_date'];

                                                                        if ($hourdiff > 1 && $hourdiff < 24) {
                                                                            if (floor($hourdiff) > 1) {
                                                                                $hrs = ' hours';
                                                                            } else {
                                                                                $hrs = ' hour';
                                                                            }
                                                                            $time_checked = floor($hourdiff) . $hrs . ' ago';
                                                                        } elseif ($hourdiff <= 1) {
                                                                            $min_dif = $hourdiff * 60;
                                                                            if ($min_dif > 1) {
                                                                                if (floor($min_dif) > 1) {
                                                                                    $minutes = ' minutes';
                                                                                } else {
                                                                                    $minutes = ' minute';
                                                                                }
                                                                                $time_checked = floor($min_dif) . $minutes . ' ago';
                                                                            } else {
                                                                                $time_checked = 'Just Now';
                                                                            }
                                                                        }
                                                                        $time_checked_tootltip = $time_checked;
                                                                    } else {
                                                                        $time_checked = '&nbsp';
                                                                        $time_checked_tootltip = '';
                                                                    }
                                                                    ?>

                                                                    <?php
                                                                    $corder = $tsks['order'];
                                                                }
                                                            }
                                                        endforeach;
                                                    endforeach;
                                                }
                                                ?>


                                                    <?php
                                                    foreach ($task as $tsid => $tsk):
                                                        ?>
                                                    <td class="list-table-view">
                                                        <?php
//                                                        $print_title = strip_tags(html_entity_decode($tsk['TaskName']));
                                                        $print_title = strip_tags(htmlspecialchars_decode(htmlspecialchars_decode($tsk['TaskName'])));
                                                        
                                                        $tsk['TaskName'] = html_entity_decode($tsk['TaskName']);
//                                                        $reg_exUrl = "/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
//                                                        $regex_email = '/^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i';
                                                        $regex_email = '/([a-zA-Z0-9_\-\.]*@\\S+\\.\\w+)/';
                                                        $reg_exUrl = "#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#";
                                                        $reg_exUrl2 = "^([a-zA-Z0-9]+(\.[a-zA-Z0-9]{2,3}+)+.*)$^";
                                                        if($tsk['type'] == 'text' || $tsk['type'] == 'memo'){
                                                            if (preg_match($regex_email, trim($tsk['TaskName']), $eml)) {
                                                                $print_srt_name = preg_replace($regex_email, "<a class='mail_url' href='mailto:" . $eml[0] . "'>" . $eml[0] . "</a>", trim($tsk['TaskName']));
                                                            }elseif (preg_match($reg_exUrl, $tsk['TaskName'], $url)) {
                                                                if(empty($url) && empty($url[0])){
                                                                    if (preg_match($reg_exUrl, $tsk['TaskName'], $url)) {
                                                                        $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href=" . $url[0] . ">" . $url[0] . "</a>", trim($tsk['TaskName']));
                                                                    }
                                                                }else{
                                                                    $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href='http://" . $url[0] . "'>" . $url[0] . "</a>", trim($tsk['TaskName']));
                                                                }
                                                            } else {
                                                                $print_srt_name = trim($tsk['TaskName']);
                                                            }
                                                        }
                                                        ?>
                                                        <?php
                                                        $div_css = '';
                                                        if($tsk['type'] == 'memo'){
                                                            $div_css .= 'padding: 11px 20px;';
                                                            if($tsk['height'] == 1){
                                                                $div_css .= 'height: 50px;';
                                                            }elseif($tsk['height'] == 2){
                                                                $div_css .= 'height: 80px;';
                                                            }elseif($tsk['height'] == 3){
                                                                $div_css .= 'height: 100px;';
                                                            }else{
                                                                $height = 110 + (30 * ($tsk['height'] - 3)) . 'px';
                                                                $div_css .= 'height: ' . $height;
                                                            }
                                                        }
                                                        ?>
                                                        <div class="add-data-div edit_task<?php if(!isset($_SESSION['id']) || isset($_SESSION['id']) && $tsk['UserId'] != $_SESSION['id']) { echo $no_hover_data; }
                                                        if ($tsk['IsCompleted']) {
                                                            echo ' completed_task';
                                                        }
                                                        ?>" data-id="<?php echo $tsk['TaskId'] ?>" data-task="<?php echo $tsk['TaskName']; ?>" data-listid="<?php echo $list_id; ?>" data-toggle="tooltip" data-placement="bottom" data-type="<?php echo $tsk['type']; ?>" title="<?php echo $print_title; ?>" style="<?php echo $div_css; ?>">
                                                            <!--<span class="icon-more"></span>-->
                                                            <?php
                                                            $span_css = '';
                                                            if($tsk['type'] == 'memo'){
                                                                $span_css .= 'line-height: 30px;';
                                                            }
                                                            ?>
                                                            <span id="span_task_<?php echo $tsk['TaskId']; ?>" class="task_name_span" style="<?php echo $span_css; ?>">
                                                                <?php
//                                                                $regex_email = '/^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i';
                                                                $regex_email = '/([a-zA-Z0-9_\-\.]*@\\S+\\.\\w+)/';
                                                                $reg_exUrl = "#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#";
                                                                $reg_exUrl2 = "^([a-zA-Z0-9]+(\.[a-zA-Z0-9]{2,3}+)+.*)$^";
                                                                $task_item = $tsk['TaskName'];
                                                                if($tsk['type'] == 'text' || $tsk['type'] == 'memo'){
                                                                    if (preg_match($regex_email, trim($tsk['TaskName']), $eml)) {
                                                                        $print_srt_name = preg_replace($regex_email, "<a class='mail_url' href='mailto:" . $eml[0] . "'>" . $eml[0] . "</a>", trim($tsk['TaskName']));
                                                                    }elseif (preg_match($reg_exUrl, $tsk['TaskName'], $url)) {
                                                                        if(empty($url) || empty($url[0])){
                                                                            if (preg_match($reg_exUrl, $tsk['TaskName'], $url)) {
                                                                                $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href=" . $url[0] . ">" . $url[0] . "</a>", trim($tsk['TaskName']));
                                                                            }
                                                                        }else{
                                                                            $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href='" . $url[0] . "'>" . $url[0] . "</a>", trim($tsk['TaskName']));
                                                                        }
                                                                    }elseif (preg_match($reg_exUrl2, $tsk['TaskName'], $url)) {
                                                                        $match_url=substr($url[0], 0, strrpos($url[0], ' '));
                                                                        if($match_url == '' && $url[0] != ''){
                                                                            $match_url = $url[0];
                                                                        }
                                                                        $task_item = str_replace($match_url, '|url|', html_entity_decode($task_item));
                                                                        $anchor = "<a class='link_clickable' href='http://" . $match_url . "'>" . $match_url . '</a>';
                                                                        $print_srt_name = str_replace('|url|', $anchor, $task_item);
                                                                    } else {
                                                                        $print_srt_name = html_entity_decode($tsk['TaskName']);
                                                                    }
                                                                }
                                                                if($tsk['type'] == 'currency'){
                                                                    if($print_srt_name != ''){
                                                                        echo '$ ';
                                                                    }
                                                                    if($tsk['TaskName'] != ''){
                                                                        if(filter_var($tsk['TaskName'], FILTER_VALIDATE_INT)){
                                                                            $print_srt_name = $tsk['TaskName'];
                                                                        }else{
                                                                            $print_srt_name = number_format((float)$tsk['TaskName'], 2, '.', '');
                                                                        }
                                                                    }else{
                                                                        $print_srt_name = '';
                                                                    }
                                                                }
                                                                if($tsk['type'] == 'email'){
                                                                    $print_srt_name = '<a class="mail_url" href="mailto:' . trim($tsk['TaskName']) . '">' . trim($tsk['TaskName']) . '</a>';
                                                                }
                                                                if($tsk['type'] == 'text'){
                                                                    echo trim(preg_replace("/[\n\r]/","",$print_srt_name));
                                                                }else{
                                                                    echo nl2br(trim($print_srt_name));
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </td>
                                                <?php
                                                endforeach;
                                                ?>
                                                
                                                <td class="list-table-view-attend<?php echo $nodrag_hidden_comment_class; ?>">
                                                    <div class="add-comment-div edit_comment" data-id="<?php echo $a_id; ?>" data-listid="<?php echo $list_id; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo $a_cmnt; ?>">
                                                        <span id="span_comment_<?php echo $a_id; ?>" class="comment_name_span"><?php if (!empty($a_cmnt)) {
                                    echo $a_cmnt;
                                } else {
                                    echo '&nbsp;';
                                } ?></span>
                                                    </div>
                                                </td>
                                                <td class="list-table-view-attend<?php echo $nodrag_hidden_class; ?>">
                                                    <div class="add-date-div check_date" data-id="<?php echo $a_id; ?>" data-listid="<?php echo $list_id; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo $time_checked_tootltip; ?>">
                                                        <span id="span_time_<?php echo $a_id; ?>" class="time_name_span"><?php echo $time_checked; ?></span>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php
                                    endforeach;
                                }
                                ?>
                                </tbody>

                                <?php
                            } else {
                                $max_key = 0;
                                foreach ($tasks as $tsks):
//                        p($tsks);
                                    if (!empty($tsks['tasks'])) {
                                        if (max(array_keys($tsks['tasks'])) > $max_key) {
                                            $max_key = max(array_keys($tsks['tasks']));
                                        }
                                    }
                                endforeach;
                                if (!empty($columns)) {
                                    $max_key = 0;
                                    if (max(array_keys($columns)) > $max_key) {
                                        $max_key = max(array_keys($columns));
                                    }
                                    ?>

                                    <thead>
                                        <?php
                                        $heading_hidden_class = '';
                                        $nodrag_hidden_class = '';
                                        $nodrag_hidden_comment_class = '';
                                        $nodrag_hidden_attendee = '';
                                        $nodrag_hidden_row_class = '';
                                        if($type_id == 11 && $config['enable_attendance_comment'] == 0){
                                            $nodrag_hidden_comment_class = ' hidden_nodrag';
                                        }
                                        if ($type_id != 11) {
                                            $heading_hidden_class = ' hidden_heading';
                                            $nodrag_hidden_class = ' hidden_nodrag';
                                            $nodrag_hidden_comment_class = ' hidden_nodrag';
                                            $nodrag_hidden_attendee = ' hidden_nodrag';
                                            $nodrag_hidden_row_class = '';
                                        }
                                        if($type_id == 5){
                                            $nodrag_hidden_row_class = ' hidden_nodrag';
                                        }
                                        if ($config['show_time'] == 0) {
//                                            $heading_hidden_class = ' hidden_heading';
                                            $nodrag_hidden_class = ' hidden_nodrag';
                                        }
                                        ?>
                                        <tr class="td_add_tr">
                                            
                                            <th class="noDrag nodrag_action_heading"></th>
                                            <?php
                                            if ($type_id == 3) {
                                                ?>
                                                <th class="noDrag rank_th_head"></th>
                                                <?php
                                            }
                                            ?>
                                            <?php
//                                            if($type_id == 11){
                                            ?>

                                    <?php
//                                            }
                                    ?>

        <?php
        foreach ($columns as $ids => $col):
            $placeholder = 'Add What is your List\'s name?';
            if($col['type'] == 'text' || $col['type'] == 'memo'){
                if(!empty($columns)){
                    $placeholder = 'Add ' . $col['column_name'];
                }else{
                    $placeholder = 'item';
                }
            } elseif($col['type'] == 'datetime'){
                $placeholder = 'MM/DD/YYYY HH:MM';
            } elseif($col['type'] == 'date'){
                $placeholder = 'MM/DD/YYYY';
            } elseif($col['type'] == 'time'){
                $placeholder = 'HH:MM';
            } elseif($col['type'] == 'currency'){
                $placeholder = '$';
            } elseif($col['type'] == 'number'){
                $placeholder = 'Add ' . $col['column_name'];
            } elseif($col['type'] == 'email'){
                $placeholder = 'username@domain.com';
            } elseif($col['type'] == 'link'){
                $placeholder = 'http://example.com';
            }
            
            ?>
                                                <th class="heading_items_col_add<?php echo $hide_add_item_cls; echo $hide_add_row; ?>" data-listid="<?php echo $list_id; ?>" data-colid="<?php echo $col['id']; ?>">
                                        <div class=" add-data-input">
                                            <?php if($col['type'] != 'memo' && $col['type'] != 'number'){ ?>
                                            <input type="text" name="task_name" id="task_name" class="task_name" data-listid="<?php echo $list_id; ?>" data-colid="<?php echo $col['id']; ?>" placeholder="<?php echo $placeholder; ?>"  data-type="<?php echo $col['type']; ?>" <?php if($col['type'] == 'checkbox' || $col['type'] == 'timestamp'){ echo 'style="display: none;"'; } ?>/>
                                            <?php }elseif($col['type'] == 'number'){ ?>
                                            <input type="number" name="task_name" id="task_name" class="task_name" data-listid="<?php echo $list_id; ?>" data-colid="<?php echo $col['id']; ?>" placeholder="<?php echo $placeholder; ?>"  data-type="<?php echo $col['type']; ?>" <?php if($col['type'] == 'checkbox' || $col['type'] == 'timestamp'){ echo 'style="display: none;"'; } ?>/>
                                            <?php } else{ ?>
                                            <textarea name="task_name" id="task_name" class="task_name" data-listid="<?php echo $list_id; ?>" data-colid="<?php echo $col['id']; ?>" placeholder="<?php echo $placeholder; ?>"  data-type="<?php echo $col['type']; ?>" data-gramm_editor="false" rows="5" <?php if($col['type'] == 'checkbox' || $col['type'] == 'timestamp'){ echo 'style="display: none;"'; } ?>></textarea>
                                            <?php } ?>
                                            <span class="span_enter"><img src="/assets/img/enter.png" class="enter_img"/></span>
                                            <?php if(isset($col) && $col['type'] == 'checkbox'){ ?>
                                            <input type="checkbox" id="check_all_<?php if (isset($col) && !empty($col)) { echo $col['id']; } ?>" class="check_all_check_box" data-colid="<?php if (isset($col) && !empty($col)) { echo $col['id']; } ?>">
                                            <?php }elseif(isset($col) && $col['type'] == 'timestamp'){ ?>
                                            <a class="btn btn-default timestamp-all-btn" id="timestamp_<?php if (isset($col) && !empty($col)) { echo $col['id']; } ?>" data-colid="<?php if (isset($col) && !empty($col)) { echo $col['id']; } ?>">Timestamp</a>
                                            <?php } ?>
                                        </div>
                                        </th>
            <?php
            endforeach;
            ?>
                                    <th class="noDrag nodrag_comment<?php echo $nodrag_hidden_comment_class; ?>"></th>
                                    <th class="noDrag nodrag_time<?php echo $nodrag_hidden_class; ?>"></th>
                                    </tr>
                                    <tr class="td_arrange_tr">
                                    


                                        
                                    <?php
                                    if ($type_id == 3) {
                                        ?>
                                            <th class="noDrag rank_th_head"></th>
                                        <?php
                                    }
                                    ?>
                                    <th class="noDrag nodrag_actions">
                                    <div class="add-data-title-nodrag<?php echo $nodrag_hidden_attendee; ?> status-column">
                                        <!--<span class="column_name_class" id="col_name_fixed">Status</span>-->
                                    </div>
                                    </th>

                                    <?php
//                                            }else{
                                    ?>
        <!--<th class="noDrag"></th>-->
                                    <?php
//                                            }
                                    ?>

        <?php
        foreach ($columns as $ids => $col):
            if ($ids <= 0) {
                $task_ul_id = 'TaskList';
            } else {
                $task_ul_id = 'TaskList' . $ids;
            }
            ?>
                                        <th class="heading_items_col" data-listid="<?php echo $list_id; ?>" data-colid="<?php echo $col['id']; ?>">
                                        <div class="add-data-title-r">
                                            <a href="" class="icon-more-h move_col ui-sortable-handle<?php echo $hide_rearrange_class; ?>" id="dropdownMenu0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="visibility: hidden;"></a>
                                            <a class="remove_col custom_cursor icon-cross-out" data-colid="<?php echo $col['id']; ?>" data-listid="<?php echo $list_id; ?>" style="visibility: hidden;"></a>
                                            <!--                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu0">
                                                                                                <li><a class="remove_col custom_cursor" data-colid="<?php echo $col['id']; ?>" data-listid="<?php echo $list_id; ?>">Remove</a></li>
                                                                                            </ul>-->
                                        </div>
                                        <div class="add-data-title" data-colid="<?php echo $col['id']; ?>" data-listid="<?php echo $list_id; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $col['column_name']; ?>" data-type="<?php echo $col['type']; ?>">
                                            <span class="column_name_class" id="col_name_<?php echo $col['id']; ?>"><?php echo $col['column_name']; ?></span>
                                            
                                        </div>
                                        <a class="icon-more-o icon_listing_table"></a>
                                        <div class="div_option_wrap">   
                                        <ul class="ul_table_option" data-listid="<?php echo $list_id; ?>">
                                            <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="text_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="text" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'text'){ echo ' checked'; } ?>><label class="col_type_lbl" for="text_<?php echo $col['id']; ?>">Text</label></div></li>
                                            <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="memo_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="memo" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'memo'){ echo ' checked'; } ?>><label class="col_type_lbl" for="memo_<?php echo $col['id']; ?>">Memo</label></div>
                                                <div class="plus_minus_wrap"><span>Height</span><a class="minus_a">-</a><input id="number_rows" type="number" min="1" value="<?php echo $col['height']; ?>"><a class="plus_a">+</a></div>
                                                 </li>
                                                 <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="checkbox_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="checkbox" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'checkbox'){ echo ' checked'; } ?>><label class="col_type_lbl" for="checkbox_<?php echo $col['id']; ?>">Check Box</label></div></li>
                                                <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="number_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="number" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'number'){ echo ' checked'; } ?>><label class="col_type_lbl" for="number_<?php echo $col['id']; ?>">Number</label></div></li>
                                                <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="currency_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="currency" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'currency'){ echo ' checked'; } ?>><label class="col_type_lbl" for="currency_<?php echo $col['id']; ?>">Dollar</label></div></li>
												 
                                                 <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="datetime_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="datetime" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'datetime'){ echo ' checked'; } ?>><label class="col_type_lbl" for="datetime_<?php echo $col['id']; ?>">Date Time</label></div></li>
                                                 <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="date_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="date" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'date'){ echo ' checked'; } ?>><label class="col_type_lbl" for="date_<?php echo $col['id']; ?>">Date</label></div></li>
                                                 <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="time_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="time" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'time'){ echo ' checked'; } ?>><label class="col_type_lbl" for="time_<?php echo $col['id']; ?>">Time</label></div></li>
                                                 <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="timestamp_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="timestamp" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'timestamp'){ echo ' checked'; } ?>><label class="col_type_lbl" for="timestamp_<?php echo $col['id']; ?>">Time Stamp</label></div></li>
                                                 <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="email_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="email" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'email'){ echo ' checked'; } ?>><label class="col_type_lbl" for="email_<?php echo $col['id']; ?>">Email</label></div></li>
                                                 <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="link_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="link" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'link'){ echo ' checked'; } ?>><label class="col_type_lbl" for="link_<?php echo $col['id']; ?>" style="font-style: italic;">Link</label></div></li>
                                                 <li class="disabled-radio-class"><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="inflo_ob_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="infloobject" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'infloobject'){ echo ' checked'; } ?> disabled="disabled"><label class="col_type_lbl" for="inflo_ob_<?php echo $col['id']; ?>" style="font-style: italic;">Inflo Object</label></div></li>
                                             </ul>
                                            </div>
                                        </th>
            <?php
        endforeach;
        ?>

        <?php
        if (!empty($attendance_data)) {
            ?>
                                        
                                        <th class="noDrag nodrag_comment<?php echo $nodrag_hidden_comment_class; ?>">
                                        <div class="add-data-title-nodrag" data-toggle="tooltip" data-placement="bottom" data-original-title="Comment">
                                            <span class="column_name_class" id="col_name_fixed2">Comment</span>
                                        </div>
                                        </th>
                                        <th class="noDrag nodrag_time<?php echo $nodrag_hidden_class; ?>">
                                        <div class="add-data-title-nodrag" data-toggle="tooltip" data-placement="bottom" data-original-title="Time">
                                            <span class="column_name_class" id="col_name_checked">Time</span>
                                        </div>
                                        </th>

            <?php
        }
        ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php
//                                    for ($i = 0; $i < $max_order; $i++) {
                                        $cnt = 1;
                                        ?>


                                        <?php
                                        $rnks = 1;
                                        foreach ($tasks as $ids => $task):
                                            $completed_class = '';
                                            if ($type_id == 5 && $task[0]['IsCompleted'] == 1) {
                                                $completed_class = 'completed strikeout';
                                            } elseif ($type_id != 5 && $task[0]['IsCompleted'] == 1) {
                                                $completed_class = 'completed';
                                            }
                                            $hidden_tr_class = '';
                                            if ($config['show_completed'] == 'False' && $task[0]['IsCompleted'] == 1) {
                                                $hidden_tr_class = ' hidden_tbl_row';
                                            }
                                            ?>
                                            <tr class="<?php echo $completed_class . $hidden_tr_class; ?>">
                                                
                                                <td class="icon-more-holder" data-order="<?php echo $task[0]['order']; ?>" data-listid="<?php echo $list_id; ?>" data-taskname="<?php echo $task[0]['TaskName']; ?>">
                                                    <span class="icon-more<?php echo $attendance_class; ?><?php echo $hide_rearrange_class; ?>"></span>
                                                    <a href="javascript:void(0)" class="icon-cross-out delete_task custom_cursor" data-id="<?php echo $task[0]['TaskId']; ?>" data-listid="<?php echo $list_id; ?>"></a>
                                                    <?php
                                                    if ($type_id == 5) {
                                                        ?>
                                                        <input type="checkbox" class="complete_task custom_cursor" id="complete_<?php echo $task[0]['TaskId']; ?>" data-id="<?php echo $task[0]['TaskId']; ?>" data-listid="<?php echo $list_id; ?>"<?php
                                                        if ($task[0]['IsCompleted'] == 1) {
                                                            echo ' checked="checked"';
                                                        }
                                                        ?>>
                                                          <!--<input type="checkbox" id="test1" />-->
                                                        <label for="complete_<?php echo $task[0]['TaskId']; ?>" class="complete_lbl"> </label>
                                                        <?php
                                                    }
                                                    if ($type_id == 11) {
                                                        ?>
                                                        <input type="checkbox" class="present_task custom_cursor" id="present_<?php echo $task[0]['TaskId']; ?>" data-id="<?php echo $task[0]['TaskId']; ?>" data-listid="<?php echo $list_id; ?>">
                                                        <?php
                                                        $task_class = '';
                                                        if ($task[0]['IsPresent'] == 1) {
                                                            $task_class = ' green_label';
                                                        } elseif ($task[0]['IsPresent'] == 3) {
                                                            $task_class = ' red_label';
                                                        } elseif ($task[0]['IsPresent'] == 2) {
                                                            $task_class = ' yellow_label';
                                                        }
                                                        ?>
                                                        <label for="present_<?php echo $task[0]['TaskId']; ?>" class="present_lbl<?php echo $task_class; ?>"> </label>
                                                    <?php
                                                }
                                                ?>
                                                </td>
                                                <?php
                                                if ($type_id == 3) {
                                                    ?>
                                                    <td class="rank_th">
                                                        <?php
                                                        echo $rnks;
                                                        $rnks++;
                                                        ?>
                                                    </td>
                                                <?php
                                                }
                                                ?>

                                                <?php
                                                if (!empty($attendance_data)) {
                                                    $corder = 0;
                                                    foreach ($task as $tsid => $tsks):
//                                                        p($tsks);
                                                        foreach ($attendance_data as $aid => $adata):
                                                            if (preg_match('(,' . $tsks['TaskId'] . '|' . $tsks['TaskId'] . ',)', $adata['item_ids']) === 1) {
                                                                $a_id = $adata['id'];
                                                                $a_cmnt = $adata['comment'];
                                                                if ($corder != $tsks['order']) {
                                                                    if (!empty($adata['check_date'])) {
                                                                        $hourdiff = round((strtotime(date('Y-m-d H:i:s')) - strtotime($adata['check_date'])) / 3600, 1);
                                                                        $time_checked = $adata['check_date'];

                                                                        if ($hourdiff > 1 && $hourdiff < 24) {
                                                                            if (floor($hourdiff) > 1) {
                                                                                $hrs = ' hours';
                                                                            } else {
                                                                                $hrs = ' hour';
                                                                            }
                                                                            $time_checked = floor($hourdiff) . $hrs . ' ago';
                                                                        } elseif ($hourdiff <= 1) {
                                                                            $min_dif = $hourdiff * 60;
                                                                            if ($min_dif > 1) {
                                                                                if (floor($min_dif) > 1) {
                                                                                    $minutes = ' minutes';
                                                                                } else {
                                                                                    $minutes = ' minute';
                                                                                }
                                                                                $time_checked = floor($min_dif) . $minutes . ' ago';
                                                                            } else {
                                                                                $time_checked = 'Just Now';
                                                                            }
                                                                        }
                                                                        $time_checked_tootltip = $time_checked;
                                                                    } else {
                                                                        $time_checked = '&nbsp';
                                                                        $time_checked_tootltip = '';
                                                                    }
                                                                    ?>

                                                                    <?php
                                                                    $corder = $tsks['order'];
                                                                }
                                                            }
                                                        endforeach;
                                                    endforeach;
                                                }
                                                ?>

                                                    <?php
                                                    foreach ($task as $tsid => $tsk):
                                                        $print_title = strip_tags(htmlspecialchars_decode(htmlspecialchars_decode($tsk['TaskName'])));
//                                                    echo 'title' . strip_tags($print_title) . ',        ';
                                                        $tsk['TaskName'] = html_entity_decode($tsk['TaskName']);
//                                                        $reg_exUrl = "/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
                                                        $reg_exUrl = "^([a-zA-Z0-9]+(\.[a-zA-Z0-9]{2,3}+)+.*)$^";
                                                        if (preg_match($reg_exUrl, $tsk['TaskName'], $url)) {
                                                            $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href=http://" . $url[0] . ">" . $url[0] . "</a>", html_entity_decode($tsk['TaskName']));
                                                        } else {
                                                            $print_srt_name = html_entity_decode($tsk['TaskName']);
                                                        }
                                                        ?>
                                                    <td class="list-table-view">
                                                        <?php
                                                        $div_css = '';
                                                        if($tsk['type'] == 'memo'){
                                                        $div_css .= 'padding: 11px 20px;';
                                                            if($tsk['height'] == 1){
                                                                $div_css .= 'height: 60px;';
                                                            }elseif($tsk['height'] == 2){
                                                                $div_css .= 'height: 80px;';
                                                            }elseif($tsk['height'] == 3){
                                                                $div_css .= 'height: 110px;';
                                                            }else{
                                                                $height = 110 + (30 * ($tsk['height'] - 3)) . 'px';
                                                                $div_css .= 'height: ' . $height;
                                                            }
                                                        }
                                                        ?>
                                                        <div class="add-data-div edit_task<?php if(!isset($_SESSION['id']) || isset($_SESSION['id']) && $tsk['UserId'] != $_SESSION['id']) { echo $no_hover_data; } if ($tsk['IsCompleted']) { echo ' completed_task'; } ?>" data-id="<?php echo $tsk['TaskId']; ?>" data-task="<?php echo $tsk['TaskName']; ?>" data-listid="<?php echo $list_id; ?>" data-toggle="tooltip" data-placement="bottom" data-type="<?php echo $tsk['type']; ?>" title="<?php echo $print_title; ?>" style="<?php echo $div_css; ?>">
                                                            <!--<span class="icon-more"></span>-->
                                                            <?php
                                                            $span_css = '';
                                                            if($tsk['type'] == 'memo'){
                                                                $span_css .= 'line-height: 30px;';
                                                            }
                                                            ?>
                                                            <span id="span_task_<?php echo $tsk['TaskId']; ?>" class="task_name_span" style="<?php echo $span_css; ?>">
                                                            <?php
//                                                    $reg_exUrl = "/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
//                                                                $regex_email = '/^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i';
                                                                $regex_email = '/([a-zA-Z0-9_\-\.]*@\\S+\\.\\w+)/';
                                                                $reg_exUrl = "#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#";
                                                                $reg_exUrl2 = "^([a-zA-Z0-9]+(\.[a-zA-Z0-9]{2,3}+)+.*)$^";
                                                                $task_item = $tsk['TaskName'];
                                                                if (preg_match($regex_email, trim($tsk['TaskName']), $eml)) {
                                                                    $print_srt_name = preg_replace($regex_email, "<a class='mail_url' href='mailto:" . $eml[0] . "'>" . $eml[0] . "</a>", trim($tsk['TaskName']));
                                                                }elseif (preg_match($reg_exUrl, $tsk['TaskName'], $url)) {
                                                                    if(empty($url) || empty($url[0])){
                                                                        if (preg_match($reg_exUrl, $tsk['TaskName'], $url)) {
                                                                            $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href='" . $url[0] . "'>" . $url[0] . "</a>", trim($tsk['TaskName']));
                                                                        }
                                                                    }else{
                                                                        $href_url = "http://" . $url[0];
                                                                        if(strpos($url[0], 'http') >= 0){
                                                                            $href_url = $url[0];
                                                                        }
                                                                        $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href='" . $href_url . "'>" . $url[0] . "</a>", trim($tsk['TaskName']));
                                                                    }
                                                                }elseif (preg_match($reg_exUrl2, $tsk['TaskName'], $url)) {
                                                                    $match_url=substr($url[0], 0, strrpos($url[0], ' '));
                                                                    if($match_url == '' && $url[0] != ''){
                                                                        $match_url = $url[0];
                                                                    }
                                                                    $task_item = str_replace($match_url, '|url|', html_entity_decode($task_item));
                                                                    $anchor = "<a class='link_clickable' href='http://" . $match_url . "'>" . $match_url . '</a>';
                                                                    $print_srt_name = str_replace('|url|', $anchor, $task_item);
                                                                } else {
                                                                    $print_srt_name = html_entity_decode($tsk['TaskName']);
                                                                }
                                                                if($tsk['type'] == 'currency'){
                                                                    if($print_srt_name != ''){
                                                                        echo '$ ';
                                                                    }
                                                                    if($tsk['TaskName'] != ''){
                                                                        if(filter_var($tsk['TaskName'], FILTER_VALIDATE_INT)){
                                                                            $print_srt_name = $tsk['TaskName'];
                                                                        }else{
                                                                            $print_srt_name = number_format((float)$tsk['TaskName'], 2, '.', '');
                                                                        }
                                                                    }else{
                                                                        $print_srt_name = '';
                                                                    }
                                                                }
                                                                
                                                                if($tsk['type'] == 'email'){
                                                                    $print_srt_name = '<a class="mail_url" href="mailto:' . trim($tsk['TaskName']) . '">' . trim($tsk['TaskName']) . '</a>';
                                                                }
                                                                if($tsk['type'] == 'text'){
                                                                    echo trim(preg_replace("/[\n\r]/","",$print_srt_name));
                                                                    
                                                                }else{
                                                                    echo nl2br(trim($print_srt_name));
                                                                }
                                                    ?>
                                                        
                                                            </span>
                                                            
                                                        </div>
                                                    </td>
                                                    <?php
                                                    $cnt++;
                                                    endforeach;
                                                    ?>
                                                
                                                <td class="list-table-view-attend<?php echo $nodrag_hidden_comment_class; ?>">
                                                    <div class="add-comment-div edit_comment" data-id="<?php echo $a_id; ?>" data-listid="<?php echo $list_id; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo $a_cmnt; ?>">
                                                        <span id="span_comment_<?php echo $a_id; ?>" class="comment_name_span"><?php if (!empty($a_cmnt)) {
                                    echo $a_cmnt;
                                } else {
                                    echo '&nbsp;';
                                } ?></span>
                                                    </div>
                                                </td>
                                                <td class="list-table-view-attend<?php echo $nodrag_hidden_class; ?>">
                                                    <div class="add-date-div check_date" data-id="<?php echo $a_id; ?>" data-listid="<?php echo $list_id; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo $time_checked_tootltip; ?>">
                                                        <span id="span_time_<?php echo $a_id; ?>" class="time_name_span"><?php echo $time_checked; ?></span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        endforeach;
                                        ?>

        <?php
//                                    }
        ?>
                                    </tbody>

        <?php
    }
}
?>
                        </table>
                    </div>
                </div>
            </div>
<!--            <div class="add-data-head-r<?php echo $show_add_column; echo $hide_add_col; ?>" style="<?php echo $style_add_col; ?>">
                <a class="add_column_url custom_cursor icon-add" data-toggle="tooltip" title="New Column">
                <img src="<?php echo base_url(); ?>assets/img/add_col_icon.png">
                </a>
            </div>-->
        </div>


    </div>
</section>

<div class="modal fade share-modal" id="share-contact" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="sharemodal-head">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon-cross-out"></span></button>
                <h2 class="share_heading_h2">
                    Share:
                    <span class="share_heading_span"><?php echo html_entity_decode($list_name); ?></span>
                </h2>
<!--                <h2 class="center_list_name"><span><?php echo html_entity_decode($list_name); ?></span></h2>-->
                <span class="custom_slug_err alert-danger" style="display: none;"></span>
            </div>

            <div class="sharemodal-body auth_share">
                <div id="copy_msg_share"></div>
                <div id="share_msg" class="alert no-border" style="display: none;"></div>
                <div class="input-outer inner-outer-first-child">
                    <label>Share Link</label>
                    <?php
                    if ($this->uri->segment(2) != '') {
                        $slug = $this->uri->segment(2);
                    } elseif (isset($_SESSION['last_slug']) && $_SESSION['last_slug'] != '') {
                        $slug = $_SESSION['last_slug'];
                    } else {
                        $slug = '';
                    }
                    ?>
                    <a id="import_contacts" href="<?php echo base_url() . 'list/' . $slug; ?>" target="_blank"><?php echo base_url() . 'list/' . $slug; ?></a>
                    <?php
                    $hide_customize = '';
                    if ($allowed_access != 1 || !isset($_SESSION['logged_in'])) {
                        $hide_customize = ' hide_custom_btn';
                    }
                    ?>
                    <a class="btn btn-primary btn-sm customize-btn<?php echo $hide_customize; ?>" id="customize_btn">Customize</a>
                    <a class="btn btn-success btn-sm copy-btn" id="copy_btn" onclick="copyToClipboard('import_contacts')">Copy</a>

                    <!--<input type="text" id="hdn_copy_url" name="hdn_copy_url" style="height:1px; width:1px;position: absolute;left: -100px;">-->
                    <textarea id="hdn_copy_url" name="hdn_copy_url" style="height:1px; width:1px;position: absolute;left: -1000px;">
                        
                    </textarea>
                    <!--<input type="text" id="hdn_copy_url" name="hdn_copy_url" style="height:1px; width:1px;position: absolute;left: -100px;">-->

                </div>
                <?php if(isset($_SESSION['id']) && $list_owner_id == $_SESSION['id']){ ?>
                <div class="input-outer lock_outer">
                    <span class="inflo_share_group_head">Lock:</span>
                    <div class="checkbox-outer" style='float: right;'>
                        <div class="checkbox">
                            <input type="checkbox" value="<?php echo $is_locked; ?>" name="is_locked_list" id="is_locked_list" class="is_locked_list_class" <?php if ($is_locked > 0) { echo 'checked'; } ?>>
                            <label for="is_locked_list">&nbsp;</label>
                        </div>
                    </div>
                    <div class="input-outer object_outer input-sub-outer-cls">
                        
                        <?php
                        $pass_val = '';
                        $modify_pass = '';
                        if($config['has_password'] == 1){
                            if($password != ''){
                                $pass_val = $password;
                            }
                        }
                        if($config['has_modify_password'] == 1){
                            if($modification_password != ''){
                                $modify_pass = $modification_password;
                            }
                        }
                        ?>
                        <div class="share_pass_div">
                            <div class="share_input_wrap">
                                <label>Password to view list:</label>
                                <input type="password" name="password_list" id="password_list" class="password_list_class" placeholder="View Password" value="<?php echo trim($pass_val); ?>">
                            </div>
                            <div class="share_input_wrap">
                                <label>Password to modify list data:</label>
                                <input type="password" name="password_list_modify" id="password_list_modify" class="password_list_class" placeholder="Modification Password" value="<?php echo trim($modify_pass); ?>">
                            </div>
                            <div class="share_btn_wrap">
                                <a class="btn btn-default save_pass_btn" id="save_pass_btn">Save</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                }else if(isset($_SESSION['id']) && $_SESSION['id'] != $list_owner_id){
                    if ($config['has_password'] == 1 && $is_locked != 0 && $allowed_access != 0 && $list_id != 0) {
                ?>
                <div class="input-outer lock_outer">
                    <span class="inflo_share_group_head">Lock:</span>
                    <div class="input-outer object_outer input-sub-outer-cls">
                        <?php
                        $pass_val = '';
                        $modify_pass = '';
                        if($config['has_password'] == 1){
                            if($password != ''){
                                $pass_val = $password;
                            }
                            if($modification_password != ''){
                                $modify_pass = $modification_password;
                            }
                        }
                        if(trim($password) != ''){
                        ?>
                        <!--<input type="text" name="password_list" id="password_list" class="password_list_class" placeholder="View Password" value="<?php echo trim($pass_val); ?>" disabled>-->
                        <span class="view_password_span">
                            <label>View Password:</label>
                            <p><?php echo trim($password); ?></p>
                        </span>
                        <?php
                        }
                        if(trim($modify_pass) != ''){
                        ?>
                        <!--<input type="text" name="password_list_modify" id="password_list_modify" class="password_list_class" placeholder="Modification Password" value="<?php echo trim($modify_pass); ?>" disabled>-->
                        <span class="modify_password_span">
                            <label>Modification Password:</label>
                            <p><?php echo trim($modify_pass); ?></p>
                        </span>
                        <?php } ?>
                    </div>
                </div>
                <?php
                    }
                }
                ?>
                
                <div class="input-outer object_outer share_outer">
                    <span class="inflo_share_group_head">Share with others:</span>
                    <?php
                    if ($this->uri->segment(2) != '') {
                        $slug = $this->uri->segment(2);
                    } elseif (isset($_SESSION['last_slug']) && $_SESSION['last_slug'] != '') {
                        $slug = $_SESSION['last_slug'];
                    } else {
                        $slug = '';
                    }
                    ?>

                        <?php
//                        $disable_class = ' noevents';
//                        if ($this->session->userdata('logged_in')) {
//                            $disable_class = '';
//                        }
                        ?>
                    <a class="btn btn-sm share-btn" id="share_btn" data-id="<?php echo $list_inflo_id; ?>">
                        <img class="inflo-icon" src="<?php echo base_url(); ?>/assets/img/inflo-alpha.png" alt=""> Share
                    </a>

                                <?php
//                    }
                                ?>
                    <div class="input-outer object_outer input-sub-outer-cls">
                                    <?php
                                    if (!empty($list_share_data)) {
                                        if (!empty($list_share_data['MyConnections']) || !empty($list_share_data['MyGroups']) || !empty($list_share_data['SharedGroups']) || !empty($list_share_data['SmartGroups'])) {
                                            ?>
                                        <?php
                                        foreach ($list_share_data as $lnm => $list):
                                            if (!empty($list)) {
                                                ?>

                                        <div class="input-inner">
                                            <span class="inflo_share_group_name"><?php echo $lnm; ?></span>
                                            <div class="inflo_share_group_details">
                                        <?php
                                        foreach ($list as $l):
                                            ?>
                                                    <span class="inflo_share_group_details_span">
                                            <?php
                                            echo $l->Name;
                                            ?>
                                                    </span>
                                            <?php
                                        endforeach;
                                        ?>
                                            </div>
                                        </div>


                <?php
            }
        endforeach;
        ?>
        <?php
    }else {
        echo 'Public';
    }
} else {
    echo 'Public';
}
?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<div class="modal fade config-modal" id="config-list" tabindex="-2" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="configmodal-head">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon-cross-out"></span></button>
                <h2>List Configuration</h2>
            </div>
            <div class="configmodal-body">

                <div id="config_msg" class="alert no-border" style="display: none;">

                </div>
                
                <?php
                $hidden_maybe_class = ' hidden_checkbox';
                if ($type_id == 11) {
                    $hidden_maybe_class = '';
                }
                ?>
                <div class="checkbox-outer<?php echo $hidden_maybe_class; ?>">
                    <div class="checkbox">
                <?php
                $maybe_allow = 'checked="checked"';
                if ($config['allow_maybe'] == 0) {
                    $maybe_allow = '';
                }
                ?>
                        <input id="maybe_allowed" type="checkbox" name="maybe_allowed" value="1" <?php echo $maybe_allow; ?>>
                        <label for="maybe_allowed">Allow maybe</label>
                    </div>	
                </div>
                
                <?php
                $hidden_time_class = ' hidden_checkbox';
                if ($type_id == 11) {
                    $hidden_time_class = '';
                }
                ?>

                <div class="checkbox-outer<?php echo $hidden_time_class; ?>">
                    <div class="checkbox">
                <?php
                $show_time = 'checked="checked"';
                if ($config['show_time'] == 0) {
                    $show_time = '';
                }
                ?>
                        <input id="show_time" type="checkbox" name="show_time" value="1" <?php echo $show_time; ?>>
                        <label for="show_time">Show timestamp</label>
                    </div>	
                </div>

                <div class="checkbox-outer">
                    <div class="checkbox">
                    <?php
                    $move_items = 'checked="checked"';
                    if ($config['allow_move'] == 'False') {
                        $move_items = '';
                    }
                    ?>
                        <input id="move_item" type="checkbox" name="move_item" value="True" <?php echo $move_items; ?>>
                        <label for="move_item">Allow rearrange</label>
                    </div>	
                </div>
                
                <?php
                if(isset($_SESSION['id'])){
                    $hide_author = '';
                    if ($config['show_author'] == 0) {
                        $hide_author = ' hide_author';
                    }
                    if($list_user_id == $_SESSION['id'] || $allowed_access == 1){
                ?>
                        <div class="checkbox-outer">
                            <div class="checkbox">
                            <?php
                            $show_author = 'checked="checked"';
                            if ($config['show_author'] == 0) {
                                $show_author = '';
                            }
                            ?>
                                <input id="show_author" type="checkbox" name="show_author" value="True" <?php echo $show_author; ?>>
                                <label for="show_author">Show owner</label>
                            </div>
                        </div>
                <?php
                    }
                }
                ?>
                
<!--                <div class="checkbox-outer">
                    <div class="checkbox">
                    <?php
                    $show_preview = 'checked="checked"';
                    if ($config['show_preview'] == 0) {
                        $show_preview = '';
                    }
                    ?>
                        <input id="show_preview" type="checkbox" name="show_preview" value="True" <?php echo $show_preview; ?>>
                        <label for="show_preview">Show preview</label>
                    </div>	
                </div>-->

                <?php
                $hidden_cb_class = ' hidden_checkbox';
                if ($type_id == 5) {
                    $hidden_cb_class = '';
                }
                ?>

                <div class="checkbox-outer<?php echo $hidden_cb_class; ?>">
                    <div class="checkbox">
                <?php
                $show_completed = 'checked="checked"';
                if ($config['show_completed'] == 'False') {
                    $show_completed = '';
                }
                ?>
                        <input id="show_completed_item" type="checkbox" name="show_completed_item" value="True" <?php echo $show_completed; ?>>
                        <label id="show_completed_item_lbl" for="show_completed_item">Show completed items</label>
                    </div>	
                </div>

                <?php
                $hidden_undo_class = ' hidden_checkbox';
                if ($type_id == 2 || $type_id == 8) {
                    $hidden_undo_class = '';
                }
                ?>

                <div class="checkbox-outer<?php echo $hidden_undo_class; ?>">
                    <div class="checkbox">
                <?php
                $undo_items = 'checked="checked"';
                if ($config['allow_undo'] == 0) {
                    $undo_items = '';
                }
                ?>
                        <input id="undo_item" type="checkbox" name="undo_item" value="True" <?php echo $undo_items; ?>>
                        <label for="undo_item">Allow Backup</label>
                    </div>	
                </div>
                
                
                <?php
                $hidden_comment_box_class = ' hidden_checkbox';
                if ($type_id == 2 || $type_id == 8) {
                    $hidden_comment_box_class = '';
                }
                ?>
                
                <div class="checkbox-outer<?php echo $hidden_comment_box_class; ?>">
                    <div class="checkbox">
                <?php
                $comment_nexup_visible = 'checked="checked"';
                //p($config);
                if ($config['enable_comment'] == 0) {
                    $comment_nexup_visible = '';
                }
                ?>
                        <input id="visible_comment" type="checkbox" name="visible_comment" value="True" <?php echo $comment_nexup_visible; ?>>
                        <label for="visible_comment">Show Comment</label>
                    </div>	
                </div>


                <div class="checkbox-outer">
                    <div class="checkbox">
                <?php
                $append_nexup_visible = 'checked="checked"';
                //p($config);
                if ($config['allow_append_locked'] == 0) {
                    $append_nexup_visible = '';
                }
                ?>
                        <input id="allow_append_locked" type="checkbox" name="allow_append_locked" value="True" <?php echo $append_nexup_visible; ?>>
                        <label for="allow_append_locked">Allow to append on lock</label>
                    </div>	
                </div>

                <?php
                $hidden_comment_config_class = ' hidden_checkbox';
                if ($type_id == 11) {
                    $hidden_comment_config_class = ' ';
                }
                ?>
                <div class="checkbox-outer<?php echo $hidden_comment_config_class; ?>">
                    <div class="checkbox">
                <?php
                $attendance_comment_allow = 'checked="checked"';
                if ($config['enable_attendance_comment'] == 0) {
                    $attendance_comment_allow = '';
                }
                ?>
                        <input id="enable_attendance_comment" type="checkbox" name="enable_attendance_comment" value="1" <?php echo $attendance_comment_allow; ?>>
                        <label for="enable_attendance_comment">Enable Comment</label>
                    </div>	
                </div>

                <?php
                if (isset($_SESSION['id']) && $_SESSION['id'] > 0) {
                ?>
                <div class="checkbox-outer">
                    <div class="checkbox">
                <?php
                $visible_in_search = 'checked="checked"';
                if ($config['visible_in_search'] == 0) {
                    $visible_in_search = '';
                }
                ?>
                        <input id="visible_in_search" type="checkbox" name="visible_in_search" value="True" <?php echo $visible_in_search; ?>>
                        <label for="visible_in_search">Hide From Search</label>
                    </div>	
                </div>
                <?php
                }
                ?>

                <?php
                $hdn_collapsed = '';
//                if($type_id != 2){
//                    $hdn_collapsed = ' hidden_checkbox';
//                }
                ?>
                <div class="checkbox-outer<?php echo $hdn_collapsed; ?>">
                    <div class="checkbox">
                <?php
                $start_collapsed = 'checked="checked"';
                if ($config['start_collapsed'] == 0) {
                    $start_collapsed = '';
                }
                ?>
                        <input id="start_collapsed" type="checkbox" name="start_collapsed" value="True" <?php echo $start_collapsed; ?>>
                        <label for="start_collapsed">Start Collapsed</label>
                    </div>	
                </div>


                <div class="button-outer" id="config_btn_div">
                    <button type="submit" name="save_config" id="save_config" data-listid="<?php echo $list_id; ?>">Save</button>
                    <button type="submit" name="close_config" id="close_config" class="close_config" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bulk_data_modal" id="bulk_data_modal" tabindex="-3" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="bulk_data_modal-head">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon-cross-out"></span></button>
                <h2>Bulk data</h2>
            </div>
            <div class="bulk_data_modal-data-body">
                <div id="copy_msg"></div>
                <div id="data_msg" class="alert no-border">
                </div>
                <div class="help-btn-div">
                    <img src="/assets/img/help-button.png" data-href="<?php echo base_url() . 'help_add_bulk'; ?>">
                    <div class="hover_help">Click here to see how this works.</div>
                </div>

                <div class="col-sm-12 bulk_data_div">
                    <div class="bulk_options_wrapper">
                        <div class="options_sel">
                            <!--<label class="option_list_type"><input type="radio" name="type_option" id="type_option_normal" class="list_type_opt" value="normal" checked="checked"> Delimited Lists</label>-->
                            
                            <select class="spearator_options form-control" id="spearator_options">
                                <option value="comma">Comma separated</option>
                                <option value="tab" selected="">Tab separated</option>
                            </select>
                        </div>
                        <div class="checkbox-outer">
                            <div class="checkbox">
                                <input id="include_header" type="checkbox" name="include_header" value="True" checked="checked">
                                <label for="include_header">Header</label>
                            </div>	
                        </div>
                    </div>
                    <div class="col-sm-12 separator_div_import_export">
                        <span class="cpoy_btn">
                            <span class="import_bulk_btn import_bulk_wrapper">
                                <a class="btn btn-sm import-btn" id="import_bulk_btn">Import</a>
                                <input type="file" name="bulk_import" id="bulk_import" class="bulk_import">
                            </span>
                            <span class="export_bulk_btn">
                                <a class="btn btn-primary btn-sm export-btn" id="export_bulk_btn">Export</a>
                            </span>
                        </span>
                    </div>
                    <!--<textarea id="values_items"></textarea>-->
                    <!--<div id="values_items" contenteditable="true" data-gramm_editor="false"></div>-->
                    <textarea id="values_items" data-gramm_editor="false" rows="10" style="resize: none;"></textarea>
                    <div id="init_value_items" style="display: none;"></div>
                    <textarea id="hdn_values_items" style="height:1px; width:1px;position: absolute;left: -100px;"></textarea>
                    
                </div>
                <div class="col-sm-12 separator_div">
                    <span class="bulk_loader hiden_img" style="display: none;">
                        <img src="/assets/img/loader.gif">
                    </span>
                    <span class="cpoy_btn">
                        <!--<label class="option_list_type pull-left">-->
                            <div class="checkbox-outer">
                                <div class="checkbox">
                                    <input type="checkbox" name="type_option_cb" id="type_option_cb_email" class="list_type_opt" value="email">
                                    <label for="type_option_cb_email">E-mail list</label>
                                </div>
                            </div>
                            <!--<input type="checkbox" name="type_option_cb" id="type_option_cb_email" class="list_type_opt" value="email"> This list contains E-mails-->
                        <!--</label>-->
                        <a class="btn btn-success btn-sm copy-btn" id="copy_bulk_btn" onclick="copyDataToClipboard('values_items')">Copy</a>
                    </span>
                    
                </div>


                <div class="button-outer" id="config_btn_div">
                    <button type="submit" name="save_bulk" id="save_bulk" data-listid="<?php echo $list_id; ?>">Ok</button>
                    <button type="submit" name="close_bulk" id="close_bulk" class="close_bulk" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade log-modal" id="log-list" tabindex="-2" role="dialog" aria-labelledby="LogModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="logmodal-head">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon-cross-out"></span></button>
                <h2>Action Log</h2>
            </div>
            <div class="logmodal-body">
                <div class="export_button_div">
            <?php
            $disabled = 'disabled="disabled" style="pointer-events: none;" data-toggle="tooltip" title="Login to continue"';
            if (isset($_SESSION['id']) && $_SESSION['id'] > 0) {
                $disabled = '';
            }
            ?>
                    <a class="btn btn-success pull-right export_to_csv_btn" href="<?php echo base_url() . 'export_log/' . $list_id; ?>" <?php echo $disabled; ?>>Export</a>
                </div>
                <div id="log_div" class="log_div">
                    <!--<ul class="dropdown-menu2" id="log_dd2" aria-labelledby="dropdownMenuLog2">-->
                    <table class="table table-striped table-responsive log_table_popup">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Comment</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($log_list)) {
                                foreach ($log_list as $key_log => $log):
                                    $hourdiff = round((strtotime(date('Y-m-d H:i:s')) - strtotime($log['created'])) / 3600, 1);
                                    $cmt = $log['created'];

                                    if ($hourdiff > 1 && $hourdiff < 24) {
                                        if (floor($hourdiff) > 1) {
                                            $hrs = ' hours';
                                        } else {
                                            $hrs = ' hour';
                                        }
                                        $cmt = floor($hourdiff) . $hrs . ' ago';
                                    } elseif ($hourdiff <= 1) {
                                        $min_dif = $hourdiff * 60;
                                        if ($min_dif > 1) {
                                            if (floor($min_dif) > 1) {
                                                $minutes = ' minutes';
                                            } else {
                                                $minutes = ' minute';
                                            }
                                            $cmt = floor($min_dif) . $minutes . ' ago';
                                        } else {
                                            $cmt = 'Just Now';
                                        }
                                    }

                                    if (isset($_SESSION['logged_in'])) {
//                                        if ($log['user_id'] == $_SESSION['id']) {
                                        if (!empty($log['comment'])) {
                                            $cmt = $log['comment'] . ' (' . $log['created'] . ')';
                                            if ($hourdiff > 1 && $hourdiff < 24) {
                                                if (floor($hourdiff) > 1) {
                                                    $hrs = ' hours';
                                                } else {
                                                    $hrs = ' hour';
                                                }
                                                $cmt = $log['comment'] . ' (' . floor($hourdiff) . $hrs . ' ago)';
                                            } elseif ($hourdiff <= 1) {
                                                $min_dif = $hourdiff * 60;
                                                if (floor($min_dif) > 1) {
                                                    $minutes = ' minutes';
                                                } else {
                                                    $minutes = ' minute';
                                                }
                                                if ($min_dif > 0) {
                                                    $cmt = floor($min_dif) . $minutes . ' ago';
                                                } else {
                                                    $cmt = 'Just Now';
                                                }
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td><?php echo $log['value']; ?></td>
                                            <td><?php echo $log['comment']; ?></td>
                                            <td><?php echo $cmt; ?></td>
                                        </tr>
                                        <!--<tr><td><?php echo $cmt; ?></td></tr>-->
                                        <!--<li class='log_options'><?php echo $cmt; ?></li>-->
                                        <?php
//                                        }
                                    } else {

                                        if ($allowed_access == 1) {
                                            if (!empty($log['comment'])) {
                                                $cmt = $log['comment'] . ' (' . $log['created'] . ')';
                                                if ($hourdiff > 1 && $hourdiff < 24) {
                                                    if (floor($hourdiff) > 1) {
                                                        $hrs = ' hours';
                                                    } else {
                                                        $hrs = ' hour';
                                                    }
                                                    $cmt = floor($hourdiff) . $hrs . ' ago';
                                                } elseif ($hourdiff <= 1) {
                                                    $min_dif = $hourdiff * 60;
                                                    if (floor($min_dif) > 1) {
                                                        $minutes = ' minutes';
                                                    } else {
                                                        $minutes = ' minute';
                                                    }
                                                    if ($min_dif > 0) {
                                                        $cmt = floor($min_dif) . $minutes . ' ago';
                                                    } else {
                                                        $cmt = '(Just Now)';
                                                    }
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td><?php echo $log['value']; ?></td>
                                                <td><?php echo $log['comment']; ?></td>
                                                <td><?php echo $cmt; ?></td>
                                            </tr>
                                            <!--<tr><td><?php echo $cmt; ?></td></tr>-->
                <?php
            }
        }
    endforeach;
}
?>
                        </tbody>
                    </table>
                    <!--</ul>-->
                </div>
                <div class="button-outer" id="config_btn_div">
                    <button type="submit" name="close_config" id="close_config" class="close_config" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade col-modal" id="col_list" tabindex="-3" role="dialog" aria-labelledby="colModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="colmodal-head">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon-cross-out"></span></button>
                <h2>Column</h2>
            </div>
            <div class="colmodal-body">
                <div id="col_div" class="col_div">
                    <span class="col_msg" style="display: none;">

                    </span>
                    <span class="add_coll_span" id="add_coll_span">
                        <input type="text" id="nexup_column" class="nexup_column" placeholder="Column Name">
                    </span>
                </div>
                <div class="button-outer" id="config_btn_div">
                    <button type="submit" name="save_col" id="save_col" class="save_col" data-listid="<?php echo $list_id; ?>">Save</button>
                    <button type="submit" name="close_col" id="close_col" class="close_col close_config" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade modify-pass-modal" id="modify_pass_modal" tabindex="-3" role="dialog" aria-labelledby="colModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="colmodal-head">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon-cross-out"></span></button>
                <h2>Modify Password</h2>
                <span class="error_msg_pwd_enter_modify" style="display: none;"></span>
            </div>
            <div class="colmodal-body">
                <div id="col_div" class="col_div">
                    <span class="col_msg" style="display: none;">

                    </span>
                    <span class="modify_pass_span" id="modify_pass_span">
                        <input type="password" id="modify_pass" class="modify_pass" placeholder="Modify Password">
                    </span>
                </div>
                <div class="button-outer" id="config_btn_div">
                    <button type="submit" name="apply_pass" id="apply_pass" class="apply_pass" data-listid="<?php echo $list_id; ?>">Save</button>
                    <button type="submit" name="close_col" id="close_pass" class="close_col close_config" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade items-modal" id="items-list" tabindex="-2" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="items-modal-head">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon-cross-out"></span></button>
                <h2>Nexup</h2>
            </div>
            <div class="items-modal-body">
                <div class="nexup-group">
                    <div class="nexup-group-two">
                        <div class="nexup-sub-group nexup-sub-group-two">
                            <table class="table table-striped table-responsive" id="item_list_table_nexup">
                            <?php
                            foreach ($tasks as $tsks):
                            ?>
                                    <tr><td><?php $tsks[0]['name']; ?></td></tr>
                            <?php
                            endforeach;
                            ?>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="button-outer" id="config_btn_div">
                    <button type="submit" name="close_config" id="close_config" class="close_config" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .edit-list-class{width: auto;position:relative;}
    input#edit_list_name { border: 1px solid #f5f3f3;}
</style>
<?php
}
?>

