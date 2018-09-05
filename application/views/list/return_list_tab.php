<?php
if ($allowed_access == 0 && $list_id != 0) {
    ?>

    <div class="listing_not_view">
        <div class="listing_not_view_inner_content">
            <div class="img_lock"><img src="/assets/img/file_lock.png"/></div>  
            <p>This list is <span>private</span></p>
        </div>
    </div>
    <?php
} else {
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
        if ($config['allow_append_locked'] == 1) {
            $no_hover_table = '';
            $no_hover_data = ' no_hover_table';
        }
        $hide_add_row = ' hidden_add_row';
        $hide_add_col = ' hidden_add_col';

        if (isset($_SESSION['id']) && $_SESSION['id'] == $list_user_id) {
            $hide_add_item_cls = '';
            $no_hover_table = '';
            $hide_add_row = '';
            $hide_add_col = '';
        }
    }
    if ($is_locked == 2) {
        $collapsable_div = ' collapse_div';
        if ($config['allow_append_locked'] == 0) {
            $hide_add_row = ' hidden_add_row';
        }
        $hide_add_col = ' hidden_add_col';
        $no_hover_table = ' no_hover_table';
        $no_hover_heading = ' no_hover_table';
        if ($config['allow_append_locked'] == 1) {
            $no_hover_table = '';
            $no_hover_data = ' no_hover_table';
        }
    }
    ?>
    <section id="content" class="content content_sublist">

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
        <div class="head_custom head_custom_sublist">
            <div class="add-data-head sub_list_title_head">
                <?php
                $hide_list = '';
                $show_add_column = '';
                $style_add_col = '';

                if ($multi_col == 0 && empty($tasks)) {
                    $style_add_col = 'display: none;';
                }
                ?>
                <h2 id="listname_<?php echo $list_id; ?>" class="listname_<?php echo $list_id; ?> edit_list_task_sub<?php echo $no_hover_heading; ?>" style="<?php echo $hide_list; ?>" data-id="<?php echo $list_id; ?>" data-slug="<?php echo $list_slug; ?>" id="edit_list_task_page" data-toggle="tooltip" data-placement="bottom" title="<?php echo html_entity_decode($list_name); ?>"><?php echo html_entity_decode($list_name); ?></h2>
<!--                <a data-toggle="modal" data-target="#share-contact-sublist" id="share_sub_list" data-toggle="tooltip" data-placement="bottom" title="Share" class="icon-share custom_cursor" style="<?php echo $hide_list; ?>" data-keyboard="false" data-listid="<?php echo $list_id; ?>"> </a>-->
                <div class="clearfix"></div>
                <?php if ($config['show_author'] == 1) { ?>
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
        if($is_locked > 0){
            $locked_desc = ' no_hover_table';
        }
        ?>
        <div class="sub_list_desc_div<?php echo $hide_desc . $locked_desc; ?>">
            <?php
            $list_desc = preg_replace('$(\s|^)(https?://[a-z0-9_./?=&-]+)(?![^<>]*>)$i', ' <a href="$2" target="_blank">$2</a> ', nl2br($list_desc));
            $list_desc = preg_replace('$(\s|^)(www\.[a-z0-9_./?=&-]+)(?![^<>]*>)$i', ' <a href="$2" target="_blank">$2</a> ', nl2br($list_desc));
            ?>
            <span id="sublist_desc_text" class="sublist_desc_text" data-listid="<?php echo $list_id; ?>"><?php echo $list_desc; ?></span>
            <span id="sub_list_desc_span" style="display: none;">
                <textarea cols="10" rows="3" id="list_sub_desc" data-listid="<?php echo $list_id; ?>" style="resize: none;">
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
        if ($allowed_access == 2) {
            $access_class = ' no-pointer';
            $tooltip_locked = 'This list is locked by it\'s owner!';
        }
        $pointer_class = '';
        if ($list_id == 0) {
            $pointer_class = ' no-pointer-icons';
        }
        ?>
        <span class="config_icons hide_data config_icons_sub<?php echo $pointer_class; ?>" id="config_icons" data-toggle="tooltip" title="<?php echo $tooltip_locked; ?>" data-placement="bottom">
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
            if ($config['enable_comment'] == 1) {
                $show_nexup_cmnt = 1;
            }
            ?>
            <a class="add_sub_column_url custom_cursor icon-add" data-toggle="tooltip" title="New Column" data-listid="<?php echo $list_id; ?>">
                <img src="<?php echo base_url(); ?>assets/img/add_col_icon.png">
            </a>
            <a data-toggle="modal" data-target="#listConfig" id="listConfig_lnk_<?php echo $list_id; ?>" class="icon-wrench custom_cursor sub_listConfig_lnk<?php echo $access_class; ?>" data-moveallow="<?php echo $allow_move_config; ?>" data-showcompleted="<?php echo $show_completed_config; ?>" data-allowcmnt="<?php echo $show_nexup_cmnt; ?>" data-allowundo="<?php echo $allow_undo_config; ?>" data-allowmaybe="<?php echo $allow_maybe_config; ?>" data-showtime="<?php echo $show_time; ?>" data-toggle="tooltip" title="Settings" data-placement="bottom" data-listid="<?php echo $list_id; ?>" data-allowappendLocked="<?php echo $config['allow_append_locked']; ?>" data-allowedattendancecomment="<?php echo $config['enable_attendance_comment']; ?>" data-collapsed="<?php echo $config['start_collapsed']; ?>"> </a>
            <div class="ddl_lt">

                <a id="listTypes_lnk" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" class="icon-list custom_cursor<?php echo $access_class; ?>" title="List Types" data-placement="bottom"> </a>
                <ul class="dropdown-menu" id="list_subType_dd" aria-labelledby="listTypes_lnk">
                    <li class="list-sub-type-header">Fixed Lists</li>
                    <?php
                    foreach ($list_types as $listType):
                        if ($listType['is_actionable'] == 0) {
                            $inactive_class = '';
                            if ($listType['is_active'] == 0) {
                                $inactive_class = ' inactive_list_type';
                            }
                            if($listType['ListTypeId'] != 12){
                            ?>
                            <li id="listType_<?php echo $listType['ListTypeId']; ?>" class="list_sub_type_cls custom_cursor<?php echo $inactive_class; ?>" data-typeId="<?php echo $listType['ListTypeId']; ?>" data-listid="<?php echo $list_id; ?>">
                                <span class="list_type_img"><img class="list_type_icon" src="<?php echo base_url(); ?>assets/img/<?php echo $listType['icon'] ?>"></span>
                                <span class="list_type_name"><?php echo $listType['ListTypeName']; ?></span>
                            </li>
                            <?php
                            }
                        }
                    endforeach;
                    ?>
                    <li class="list-sub-type-header">Action Lists</li>
                    <?php
                    foreach ($list_types as $listType):
                        if ($listType['is_actionable'] == 1) {
                            $inactive_class = '';
                            if ($listType['is_active'] == 0) {
                                $inactive_class = ' inactive_list_type';
                            }
                            if($listType['ListTypeId'] != 12){
                            ?>
                            <li id="listType_<?php echo $listType['ListTypeId']; ?>" class="list_sub_type_cls custom_cursor<?php echo $inactive_class; ?>" data-typeId="<?php echo $listType['ListTypeId']; ?>" data-listid="<?php echo $list_id; ?>">
                                <span class="list_type_img"><img class="list_type_icon" src="<?php echo base_url(); ?>assets/img/<?php echo $listType['icon'] ?>"></span>
                                <span class="list_type_name"><?php echo $listType['ListTypeName']; ?></span>
                            </li>
                            <?php
                            }
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

            <a class="bulk_add_cls bulk_sub_add_cls custom_cursor<?php echo $hide_bulk_cls . $access_class; ?>" id="add_bulk_sub_data" data-id="<?php echo $list_id; ?>" data-slug="<?php echo $list_slug; ?>" data-toggle="tooltip" title="Bulk data entry" data-placement="bottom"></a>


            <?php
            $class_lock = '';
            if (!$this->session->userdata('logged_in')) {
                $class_lock = 'lock_hide';
            }
            if ($is_locked == 0) {
                ?>
                <a class="icon-lock-open2 custom_cursor <?php echo $class_lock . $access_class; ?>" id="listsub_Lock_lnk" data-id="<?php echo $list_id; ?>" data-slug="<?php echo $list_slug; ?>" data-toggle="tooltip" title="Lock List" data-placement="bottom"></a>
                <?php
            } else {
                ?>
                <a class="icon-lock2 custom_cursor <?php echo $class_lock . $access_class; ?>" id="listSub_Unlock_lnk" data-id="<?php echo $list_id; ?>" data-slug="<?php echo $list_slug; ?>" data-toggle="tooltip" title="Unlock List" data-placement="bottom"></a>
                <?php
            }
            ?>

            <?php
            $hide_desc_cls = '';
            if ($list_id == 0 || $allowed_access == 0) {
                $hide_desc_cls = ' hdn_desc';
            }
            ?>
            <a class="add_data_desc custom_cursor<?php echo $hide_desc_cls . $access_class; ?>" id="add_sub_data_desc" data-id="<?php echo $list_id; ?>" data-slug="<?php echo $list_slug; ?>" data-toggle="tooltip" title="Add list description" data-placement="bottom">
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
                <a class="reset_sub_list btn btn-sm bth-default<?php echo $click_alert; ?>" id="reset_list" data-listid="<?php echo $list_id; ?>" data-toggle="tooltip" data-placement="bottom" title="Reset List">
                    <img src="/assets/img/rotate-left.png">
                </a>
                <?php
            }
            ?>
            <a id="delete_sub_list_builder" class="delete_sub_list_builder custom_cursor<?php echo $disable_delete . $access_class; ?>" data-id="<?php echo $list_id; ?>" data-slug="<?php echo $list_slug; ?>" data-toggle="tooltip" data-placement="bottom" title="Delete List" style="<?php echo $hide_list; ?>"><img src="/assets/img/rubbish-bin.png"></a>
            <?php
            $disabled_copy = '';
            if (!isset($_SESSION['logged_in'])) {
                $disabled_copy = '';
            }
            ?>
            <a id="copy_sub_list_btn" class="copy-list-btn copy-list-btn-items-page custom_cursor<?php echo $access_class; ?>" data-id="<?php echo $list_id; ?>" data-slug="<?php echo $list_slug; ?>" data-toggle="tooltip" data-placement="bottom" title="Copy List" style="<?php echo $disabled_copy; ?>"><img src="/assets/img/copy.png"></a>

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
                            if ($type_id == 2) {
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
                            } elseif ($type_id == 8) {
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
                                    if ($config['enable_comment'] == 0) {
                                        $hide_comment_box = ' hide_box';
                                    }
                                    ?>
                                    <span class="add-data-div add_comment_box<?php echo $hide_comment_box; ?>" id="nexup_cmnt_span">
                                        <input type="text" id="nexup_comment" class="nexup_comment" placeholder="Comment...">
                                    </span>
                                </div>
                                <a class="undo-btn prev_btn custom_cursor<?php echo $undo_class; ?>" data-listid="<?php echo $list_id; ?>" data-toggle="tooltip" data-placement="top" title="Backup"><span id="undo_icon" class=""><img class="defult_arrow" src="/assets/img/prev-arrow.png"><img class="hover_arrow" src="/assets/img/prev-arrow-white.png"><img class="grey_arrow" src="/assets/img/prev-arrow-grey.png"></span></a>
                                <div class="h-nav dropdown log_icon_btn">
                                    <a title="Log" class="custom_cursor" id="dropdownMenuLog_sub" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-listid="<?php echo $list_id; ?>"><img class="defult_arrow" src="/assets/img/log_icon.png"><img class="hover_arrow" src="/assets/img/log_icon-white.png"></a>

                                    <?php
                                    $i = 0;
                                    if (!empty($log_list)) {
                                        ?>
                                        <ul class="dropdown-menu" id="log_dd" aria-labelledby="dropdownMenuLog">
                                            <?php
                                            foreach ($log_list as $key_log => $log):
                                                $comment_class = '';
                                                if ($log['comment'] == '') {
                                                    $comment_class = ' no-comt-class';
                                                }
                                                if ($i > 5) {
                                                    break;
                                                }
                                                $hourdiff = round((strtotime(date('Y-m-d H:i:s')) - strtotime($log['created'])) / 3600, 1);
                                                $cmt = '<span class="cmt_val' . $comment_class . '">' . $log['comment'] . '</span><span class="comment-span">(' . $log['created'] . ')</span>';
                                                if ($log['comment'] == '') {
                                                    $cmt = '<span class="cmt_val' . $comment_class . '">no comment</span><span class="comment-span">(' . $log['created'] . ')</span>';
                                                }
                                                if ($hourdiff > 1 && $hourdiff < 24) {
                                                    if (floor($hourdiff) > 1) {
                                                        $hrs = ' hours';
                                                    } else {
                                                        $hrs = ' hour';
                                                    }
                                                    $cmt = '<span class="cmt_val' . $comment_class . '">' . $log['comment'] . '</span><span class="comment-span">(' . floor($hourdiff) . $hrs . ' ago)</span>';
                                                    if ($log['comment'] == '') {
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
                                                        if ($log['comment'] == '') {
                                                            $cmt = '<span class="cmt_val' . $comment_class . '">no comment</span><span class="comment-span">(' . floor($min_dif) . $minutes . ' ago)</span>';
                                                        }
                                                    } else {
                                                        $cmt = '<span class="cmt_val' . $comment_class . '">' . $log['comment'] . '</span><span class="comment-span">(Just Now)</span>';
                                                        if ($log['comment'] == '') {
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
                            <li class="copy_summary_li" onclick="copySummaryToClipboard(this);">Copy</li>
                            <li class="copy_summary_details_li" onclick="copySummaryDetailsToClipboard(this);">Copy w/details</li>
                            <li class="copy_summary_details_comments_li" onclick="copySummaryDetailsToClipboard(this);">Copy w/details & comments</li>
                            <textarea id="hdn_summary_sub" name="hdn_summary_sub" style="position: absolute;left: -10000px;"></textarea>
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
            <a class="icon-settings-sub custom_cursor<?php echo $class_hide_settings; ?>" id="config_lnk_sub" <?php
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
            <div class="added_div added_sub_div add-data-left" id="added_div">

                <div id="addSubTaskDiv" class="item-add-div multi-column-lists">
                    <h3 id="TaskSubListHead"></h3>
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
                        if ($type_id == 11) {
                            $attendance_class = ' attendance_list_class';
                            $table_checkbox_class = ' check_box_table';
                        }
                        if($type_id == 5){
                            $table_checkbox_class = ' check_box_table';
                        }
                        ?>
                        <div class="my_table my_sub_table my_scroll_table">
                            <table id="test_table_<?php echo $list_id; ?>" class="table test_table<?php echo $no_hover_table . $table_checkbox_class; ?>">
                                <?php
                                if ($multi_col == 0) {
                                    ?>
                                    <thead>
                                        <?php
                                        $heading_hidden_class = '';
                                        $nodrag_hidden_class = '';
                                        $nodrag_hidden_comment_class = '';
                                        $nodrag_hidden_row_class = '';
                                        if ($type_id != 11) {
                                            $heading_hidden_class = ' hidden_heading';
                                            $nodrag_hidden_comment_class = ' hidden_nodrag';
                                            $nodrag_hidden_class = ' hidden_nodrag';
                                            $nodrag_hidden_row_class = '';
                                        }else{
                                            if($config['enable_attendance_comment'] == 0){
                                                $nodrag_hidden_comment_class = ' hidden_nodrag';
                                            }
                                        }
                                        if ($type_id == 5) {
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
                                            if($columns[0]['type'] == 'text' || $columns[0]['type'] == 'memo'){
                                                if (!empty($columns)) {
                                                    $placeholder = 'Add ' . $columns[0]['column_name'];
                                                } else {
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
                                            ?>

                                            <th class="heading_items_col_add<?php echo $hide_add_item_cls; echo $hide_add_row; ?>" data-listid="<?php echo $list_id; ?>" data-colid="<?php echo $columns[0]['id']; ?>">
                                    <div class="add-data-input">
                                        <?php if(isset($columns) && $columns[0]['type'] == 'memo'){ ?>
                                            <textarea name="task_name" id="task_name" class="task_sub_name" data-listid="<?php echo $list_id; ?>" data-colid="<?php echo $columns[0]['id']; ?>" placeholder="<?php echo $placeholder; ?>" data-type="<?php echo $columns[0]['type']; ?>" data-gramm_editor="false" rows="5" <?php if(isset ($columns[0]) && ($columns[0]['type'] == 'checkbox' || $columns[0]['type'] == 'timestamp')){ echo 'style="display: none;"'; } ?>></textarea>
                                        <?php } elseif(isset($columns) && $columns[0]['type'] == 'number'){ ?>
                                            <input type="number" name="task_name" id="task_name" class="task_sub_name" data-listid="<?php echo $list_id; ?>" data-colid="<?php if (isset($columns) && !empty($columns)) { echo $columns[0]['id']; } else { echo '0'; } ?>" placeholder="<?php echo $placeholder; ?>" data-type="<?php if (isset($columns) && !empty($columns)) { echo $columns[0]['type']; } ?>"  <?php if(isset ($columns[0]) && ($columns[0]['type'] == 'checkbox' || $columns[0]['type'] == 'timestamp')){ echo 'style="display: none;"'; } ?>/>
                                        <?php } else { ?>
                                        <input type="text" name="task_name" id="task_name" class="task_sub_name" data-listid="<?php echo $list_id; ?>" data-colid="<?php if (isset($columns) && !empty($columns)) { echo $columns[0]['id']; } else { echo '0'; } ?>" placeholder="<?php echo $placeholder; ?>" data-type="<?php if (isset($columns) && !empty($columns)) { echo $columns[0]['type']; } ?>"  <?php if(isset ($columns[0]) && ($columns[0]['type'] == 'checkbox' || $columns[0]['type'] == 'timestamp')){ echo 'style="display: none;"'; } ?>/>
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
                                        if ($type_id != 11) {
                                            $heading_hidden_class = ' hidden_heading';
                                            $nodrag_hidden_class = ' hidden_nodrag';
                                            $nodrag_hidden_comment_class = ' hidden_nodrag';
                                            $nodrag_hidden_attendee = ' hidden_nodrag';
                                        }else{
                                            if($config['enable_attendance_comment'] == 0){
                                                $nodrag_hidden_comment_class = ' hidden_nodrag';
                                            }
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
                                                <a href="" class="icon-more-h move_sub_col ui-sortable-handle<?php echo $hide_rearrange_class; ?>" id="dropdownMenu0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="visibility: hidden;"></a>
                                                <a class="remove_sub_col custom_cursor icon-cross-out" data-colid="<?php echo $col['id']; ?>" data-listid="<?php echo $list_id; ?>" style="visibility: hidden;"></a>
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
                                                <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="link_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="link" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'link'){ echo ' checked'; } ?>><label class="col_type_lbl" for="link_<?php echo $col['id']; ?>">Link</label></div></li>
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
//                                                            $regex_email = '/^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i';
                                                            $regex_email = '/([a-zA-Z0-9_\-\.]*@\\S+\\.\\w+)/';
                                                            $reg_exUrl = "#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#";
                                                            $reg_exUrl2 = "^([a-zA-Z0-9]+(\.[a-zA-Z0-9]{2,3}+)+.*)$^";
                                                            
                                                            if (preg_match($regex_email, trim($tsk['TaskName']), $eml)) {
                                                                $print_srt_name = preg_replace($regex_email, "<a class='mail_url' href='mailto:" . $eml[0] . "'>" . $eml[0] . "</a>", trim($tsk['TaskName']));
                                                            }elseif (preg_match($reg_exUrl, $tsk['TaskName'], $url)) {
                                                                if (empty($url) && empty($url[0])) {
                                                                    if (preg_match($reg_exUrl, $tsk['TaskName'], $url)) {
                                                                        $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href=" . $url[0] . ">" . $url[0] . "</a>", trim($tsk['TaskName']));
                                                                    }
                                                                } else {
                                                                    $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href='http://" . $url[0] . "'>" . $url[0] . "</a>", trim($tsk['TaskName']));
                                                                }
                                                            } else {
                                                                $print_srt_name = trim($tsk['TaskName']);
                                                            }
                                                            ?>
                                                            <?php
                                                            $div_css = 'padding: 11px 20px;';
                                                            if($tsk['type'] == 'memo'){
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
                                                            <div class="add-data-div edit_task<?php
                                                            if (isset($_SESSION['id']) && $tsk['UserId'] != $_SESSION['id']) {
                                                                echo $no_hover_data;
                                                            }
                                                            if ($tsk['IsCompleted']) {
                                                                echo ' completed_task';
                                                            }
                                                            ?>" data-id="<?php echo $tsk['TaskId'] ?>" data-task="<?php echo $tsk['TaskName']; ?>" data-listid="<?php echo $list_id; ?>" data-type="<?php echo $tsk['type']; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $print_title; ?>" style="<?php echo $div_css; ?>">
                                                                <!--<span class="icon-more"></span>-->
                                                                <?php
                                                                $span_css = '';
                                                                if($tsk['type'] == 'memo'){
                                                                    $span_css .= 'line-height: 30px;';
                                                                }
                                                                ?>
                                                                <span id="span_task_<?php echo $tsk['TaskId']; ?>" class="task_name_span" style="<?php echo $span_css; ?>">
                                                                    <?php
//                                                                    $regex_email = '/^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i';
                                                                    $regex_email = '/([a-zA-Z0-9_\-\.]*@\\S+\\.\\w+)/';
                                                                    $reg_exUrl = "#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#";
                                                                    $reg_exUrl2 = "^([a-zA-Z0-9]+(\.[a-zA-Z0-9]{2,3}+)+.*)$^";
                                                                    $task_item = $tsk['TaskName'];
                                                                    if (preg_match($regex_email, trim($tsk['TaskName']), $eml)) {
                                                                        $print_srt_name = preg_replace($regex_email, "<a class='mail_url' href='mailto:" . $eml[0] . "'>" . $eml[0] . "</a>", trim($tsk['TaskName']));
                                                                    }elseif (preg_match($reg_exUrl, $tsk['TaskName'], $url)) {
                                                                        if (empty($url) || empty($url[0])) {
                                                                            if (preg_match($reg_exUrl, $tsk['TaskName'], $url)) {
                                                                                $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href=" . $url[0] . ">" . $url[0] . "</a>", trim($tsk['TaskName']));
                                                                            }
                                                                        } else {
                                                                            $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href='" . $url[0] . "'>" . $url[0] . "</a>", trim($tsk['TaskName']));
                                                                        }
                                                                    } elseif (preg_match($reg_exUrl2, $tsk['TaskName'], $url)) {
                                                                        $match_url = substr($url[0], 0, strrpos($url[0], ' '));
                                                                        if ($match_url == '' && $url[0] != '') {
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
//                                                                    echo nl2br(trim($print_srt_name));
                                                                    ?>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <?php
                                                    endforeach;
                                                    ?>

                                                    <td class="list-table-view-attend<?php echo $nodrag_hidden_comment_class; ?>">
                                                        <div class="add-comment-div edit_comment" data-id="<?php echo $a_id; ?>" data-listid="<?php echo $list_id; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo $a_cmnt; ?>">
                                                            <span id="span_comment_<?php echo $a_id; ?>" class="comment_name_span"><?php
                                                                if (!empty($a_cmnt)) {
                                                                    echo $a_cmnt;
                                                                } else {
                                                                    echo '&nbsp';
                                                                }
                                                                ?></span>
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
                                            if ($type_id != 11) {
                                                $heading_hidden_class = ' hidden_heading';
                                                $nodrag_hidden_class = ' hidden_nodrag';
                                                $nodrag_hidden_comment_class = ' hidden_nodrag';
                                                $nodrag_hidden_attendee = ' hidden_nodrag';
                                                $nodrag_hidden_row_class = '';
                                            }else{
                                                if($config['enable_attendance_comment'] == 0){
                                                    $nodrag_hidden_comment_class = ' hidden_nodrag';
                                                }
                                            }
                                            if ($type_id == 5) {
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
                                                        if (!empty($col)) {
                                                            $placeholder = 'Add ' . $col['column_name'];
                                                        } else {
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
                                                    <th class="heading_items_col_add<?php
                                                    echo $hide_add_item_cls;
                                                    echo $hide_add_row;
                                                    ?>" data-listid="<?php echo $list_id; ?>" data-colid="<?php echo $col['id']; ?>">
                                            <div class=" add-data-input">
                                                <?php if($col['type'] != 'memo' && $col['type'] != 'number'){ ?>
                                                <input type="text" name="task_name" id="task_name" class="task_sub_name" data-listid="<?php echo $list_id; ?>" data-colid="<?php echo $col['id']; ?>" placeholder="<?php echo $placeholder; ?>" data-type="<?php echo $col['type']; ?>" <?php if($col['type'] == 'checkbox' || $col['type'] == 'timestamp'){ echo 'style="display: none;"'; } ?>/>
                                                <?php }elseif($col['type'] == 'number'){ ?>
                                                <input type="number" name="task_name" id="task_name" class="task_sub_name" data-listid="<?php echo $list_id; ?>" data-colid="<?php echo $col['id']; ?>" placeholder="<?php echo $placeholder; ?>" data-type="<?php echo $col['type']; ?>" <?php if($col['type'] == 'checkbox' || $col['type'] == 'timestamp'){ echo 'style="display: none;"'; } ?>/>
                                                <?php }else{ ?>
                                                <textarea name="task_name" id="task_name" class="task_sub_name" data-listid="<?php echo $list_id; ?>" data-colid="<?php echo $col['id']; ?>" placeholder="<?php echo $placeholder; ?>"  data-type="<?php echo $col['type']; ?>" data-gramm_editor="false" rows="5" <?php if($col['type'] == 'checkbox' || $col['type'] == 'timestamp'){ echo 'style="display: none;"'; } ?>></textarea>
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
                                        <th class="noDrag nodrag_time<?php echo $nodrag_hidden_class; ?>"></th>
                                        <th class="noDrag nodrag_comment<?php echo $nodrag_hidden_comment_class; ?>"></th>
                                        </tr>
                                        <tr class="td_arrange_tr">



                                            <th class="noDrag nodrag_actions">
                                                <?php
                                                if ($type_id == 3) {
                                                    ?>
                                                <th class="noDrag rank_th_head"></th>
                                                <?php
                                            }
                                            ?>
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
                                                <a href="" class="icon-more-h move_sub_col ui-sortable-handle<?php echo $hide_rearrange_class; ?>" id="dropdownMenu0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="visibility: hidden;"></a>
                                                <a class="remove_sub_col custom_cursor icon-cross-out" data-colid="<?php echo $col['id']; ?>" data-listid="<?php echo $list_id; ?>" style="visibility: hidden;"></a>
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
                                                <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="link_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="link" data-col_id="<?php echo $col['id']; ?>" <?php if($col['type'] == 'link'){ echo ' checked'; } ?>><label class="col_type_lbl" for="link_<?php echo $col['id']; ?>">Link</label></div></li>

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
                                                            $div_css = 'padding: 11px 20px;';
                                                            if($tsk['type'] == 'memo'){
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
                                                            <div class="add-data-div edit_task<?php
                                                            if (isset($_SESSION['id']) && $tsk['UserId'] != $_SESSION['id']) {
                                                                echo $no_hover_data;
                                                            }
                                                            if ($tsk['IsCompleted']) {
                                                                echo ' completed_task';
                                                            }
                                                            ?>" data-id="<?php echo $tsk['TaskId']; ?>" data-task="<?php echo $tsk['TaskName']; ?>" data-listid="<?php echo $list_id; ?>" data-type="<?php echo $tsk['type']; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $print_title; ?>" style="<?php echo $div_css; ?>">
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
//                                                                    $regex_email = '/^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i';
                                                                    $regex_email = '/([a-zA-Z0-9_\-\.]*@\\S+\\.\\w+)/';
                                                                    $reg_exUrl = "#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#";
                                                                    $reg_exUrl2 = "^([a-zA-Z0-9]+(\.[a-zA-Z0-9]{2,3}+)+.*)$^";
                                                                    $task_item = $tsk['TaskName'];
                                                                    if (preg_match($regex_email, trim($tsk['TaskName']), $eml)) {
                                                                        $print_srt_name = preg_replace($regex_email, "<a class='mail_url' href='mailto:" . $eml[0] . "'>" . $eml[0] . "</a>", trim($tsk['TaskName']));
                                                                    }elseif (preg_match($reg_exUrl, $tsk['TaskName'], $url)) {
                                                                        if (empty($url) || empty($url[0])) {
                                                                            if (preg_match($reg_exUrl, $tsk['TaskName'], $url)) {
                                                                                $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href='" . $url[0] . "'>" . $url[0] . "</a>", trim($tsk['TaskName']));
                                                                            }
                                                                        } else {
                                                                            $href_url = "http://" . $url[0];
                                                                            if (strpos($url[0], 'http') >= 0) {
                                                                                $href_url = $url[0];
                                                                            }
                                                                            $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href='" . $href_url . "'>" . $url[0] . "</a>", trim($tsk['TaskName']));
                                                                        }
                                                                    } elseif (preg_match($reg_exUrl2, $tsk['TaskName'], $url)) {
                                                                        $match_url = substr($url[0], 0, strrpos($url[0], ' '));
                                                                        if ($match_url == '' && $url[0] != '') {
                                                                            $match_url = $url[0];
                                                                        }
                                                                        $task_item = str_replace($match_url, '|url|', html_entity_decode($task_item));
                                                                        $anchor = "<a class='link_clickable' href='http://" . $match_url . "'>" . $match_url . '</a>';
                                                                        $print_srt_name = str_replace('|url|', $anchor, $task_item);
                                                                    } else {
                                                                        $print_srt_name = html_entity_decode($tsk['TaskName']);
                                                                    }
                                                                    if($tsk['type'] == 'text'){
                                                                        echo trim(preg_replace("/[\n\r]/","",$print_srt_name));
                                                                    }else{
                                                                        echo nl2br(trim($print_srt_name));
                                                                    }
//                                                                    echo $print_srt_name;
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
                                                            <span id="span_comment_<?php echo $a_id; ?>" class="comment_name_span"><?php
                                                                if (!empty($a_cmnt)) {
                                                                    echo $a_cmnt;
                                                                } else {
                                                                    echo '&nbsp';
                                                                }
                                                                ?></span>
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
<!--                <div class="add-data-head-r<?php
                echo $show_add_column;
                echo $hide_add_col;
                ?>" style="<?php echo $style_add_col; ?>">
                    <a class="add_sub_column_url custom_cursor icon-add" data-toggle="tooltip" title="New Column" data-listid="<?php echo $list_id; ?>">
                        <img src="http://test.nexup.io/assets/img/add_col_icon.png">
                    </a>
                </div>-->
            </div>


        </div>
    </section>

    <style>
        .edit-list-class{width: auto;position:relative;}
        input#edit_list_name { border: 1px solid #f5f3f3;}
    </style>
    <?php
}
?>

<script>
    var prev_index = 0;
    var new_index = 0;
    var list_id = <?php echo $list_id; ?>;
    console.log(list_id);

    $('#test_table_<?php echo $list_id ?>').find('tbody').sortable({
        handle: '.icon-more',
        connectWith: ".add-data-div.edit_task",
        axis: "y",
        tolerance: 'pointer',
        scroll: true,
        animation: 100,
        revert: 100,
        stop: function (event, ui) {
            $('#test_table_<?php echo $list_id ?>').parent().removeClass('my_scroll_table_no_overflow');
            $('#test_table_<?php echo $list_id ?>').parent().addClass('my_scroll_table');
        },
        update: function (event, ui) {
            if ($('#test_table_<?php echo $list_id ?>').find('.rank_th').length > 0) {
                var rnk_val = 1;
                $('#test_table_<?php echo $list_id ?>').find('.rank_th').each(function () {
                    $(this).text(rnk_val);
                    rnk_val++;
                });
            }
            var tasks_orders = [];
            $('#test_table_<?php echo $list_id ?>').find('tbody tr td.icon-more-holder').each(function (e) {
                var orders = $(this).attr('data-order');
                tasks_orders.push(orders);
            });

//                console.log($(ui.item).children('td.list-table-view:eq(0)').find('div.edit_task').attr('data-id'));

            var task_id = $(ui.item).children('td.list-table-view:eq(0)').find('div.edit_task').attr('data-id');
            var list_id = '';
            list_id = $(ui.item).children('td.icon-more-holder').attr('data-listid');
            var user_ip = "<?php echo $_SERVER['REMOTE_ADDR']; ?>";
            $.ajax({
                url: '<?php echo base_url() . 'order_change' ?>',
                type: 'POST',
                data: {
                    OrderId: ui.item.index() + 1,
                    TaskOrders: JSON.stringify(tasks_orders),
                    ListId: list_id,
                    user_ip: user_ip
                },
                success: function (res) {
                    if (res != 'fail') {
                        if ($('#test_table_<?php echo $list_id ?>').find('.icon-more-holder').length > 0) {
                            var ord_val = 1;
                            $('#test_table_<?php echo $list_id ?>').find('.icon-more-holder').each(function () {
                                $(this).attr('data-order', ord_val);
                                ord_val++;
                            });
                        }

                        var total_rows = $('#test_table_<?php echo $list_id ?>').find('tbody tr').length;
                        $.ajax({
                            url: '<?php echo base_url() . 'item_order' ?>',
                            type: 'POST',
                            data: {
                                OrderId: ui.item.index() + 1,
                                Taskid: task_id
                            },
                            success: function (res) {
                            }
                        });
                    } else {
                        $('#test_table_<?php echo $list_id ?>').find("tbody").sortable('cancel')
//                                return false;
                    }
                }
            });
        }
    });




    $('#test_table_<?php echo $list_id ?>').find('thead tr').sortable({
        handle: '.move_sub_col',
        cancel: '.noDrag',
        connectWith: 'thead tr.td_arrange_tr',
        tolerance: "pointer",
        items: "th:not(.noDrag)",
        helper: function (e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function (index) {
                $(this).width($originals.eq(index).width())
            });
            return $helper;
        },
        start: function (e, ui) {
            prev_index = ui.item.index();
        },
        update: function (event, ui) {
            var next_class = $('#test_table_<?php echo $list_id ?>').find('thead .td_arrange_tr th:nth-child(2)').attr('class');
            if ($('.icon-settings').attr('data-typeid') == 3) {
                next_class = $('#test_table_<?php echo $list_id ?>').find('thead .td_arrange_tr th:nth-child(3)').attr('class');
            }
            if (next_class == 'noDrag') {
                event.preventDefault();
            }

            var total_rows = $('#test_table_<?php echo $list_id ?>').find('tbody tr').length;

            new_index = ui.item.index();
            var col_ids = [];
            var list_id = 0;
            $('#test_table_<?php echo $list_id ?>').find('.heading_items_col .add-data-title').each(function () {
                var ids = $(this).attr('data-colid');
                col_ids.push(ids);
                list_id = $(this).attr('data-listid');

            });

            $.ajax({
                url: '<?php echo base_url() . 'change_column_order' ?>',
                type: 'POST',
                data: {
                    column_ids: JSON.stringify(col_ids),
                    list_id: list_id
                },
                success: function (res) {
                    if (res != 'fail') {
                        var resp = JSON.parse(res);
                        var old_pos = prev_index;
                        var new_pos = new_index;

                        if ($('#tabs-<?php echo $list_id ?>').find('.icon-settings').attr('data-typeid') == 3) {
                            if (new_pos < 2) {
                                new_pos = 2;
                                $('#test_table_<?php echo $list_id ?>').find('thead tr.td_arrange_tr').each(function () {
                                    var cols = $(this).children('th');
                                    cols.eq(old_pos).detach().insertBefore(cols.eq(new_pos));
                                });
                            }
                        } else {
                            if (new_pos < 1) {
                                new_pos = 1;
                                $('#test_table_<?php echo $list_id ?>').find('thead tr.td_arrange_tr').each(function () {
                                    var cols = $(this).children('th');
                                    cols.eq(old_pos).detach().insertBefore(cols.eq(new_pos));
                                });
                            }

                        }
                        $('#test_table_<?php echo $list_id ?>').find('thead tr.td_add_tr').each(function () {
                            var cols = $(this).children('th');

                            if (new_pos > old_pos) {
                                cols.eq(old_pos).detach().insertAfter(cols.eq(new_pos));
                            } else {
                                cols.eq(old_pos).detach().insertBefore(cols.eq(new_pos));
                            }
                        });
                        $('#test_table_<?php echo $list_id ?>').find('tbody tr').each(function () {
                            var cols = $(this).children('td');

                            if (new_pos > old_pos) {
                                cols.eq(old_pos).detach().insertAfter(cols.eq(new_pos));
                            } else {
                                cols.eq(old_pos).detach().insertBefore(cols.eq(new_pos));
                            }
                        });
                        if (total_rows > 0) {
                            if (resp.length > 0) {
                                if ($('#tabs-<?php echo $list_id ?>').find('.icon-settings-sub').attr('data-typeid') == 2 || $('#tabs-<?php echo $list_id ?>').find('.icon-settings-sub').attr('data-typeid') == 8) {
                                    $.ajax({
                                        url: '<?php echo base_url(); ?>get_nexup_box',
                                        type: 'POST',
                                        data: {
                                            'list_id': list_id,
                                        },
                                        success: function (res) {
                                            if (res != '') {
                                                var row_indx = $('#test_table_<?php echo $list_id ?>').find('#span_task_' + res).parent().parent().parent().index();
                                                if ($('#tabs-<?php echo $list_id ?>').find('.icon-settings-sub').attr('data-typeid') == 2) {
                                                    $('#tabs-<?php echo $list_id ?>').find('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table_<?php echo $list_id ?>').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                                    
                                                    $('#tabs-<?php echo $list_id ?>').find('.nexup-sub-group-one').attr('data-original-title', $.trim($('#test_table_<?php echo $list_id ?>').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                                    var item_list_nexup_data = '<tr><td>' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                                    var grp_two = '';
                                                    for (s = 1; s < 4; s++) {
                                                        if ($('#test_table_<?php echo $list_id ?>').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').length == 1) {
                                                            grp_two += '<span data-toggle="tooltip" title="" data-original-title="' + $.trim($('#test_table_<?php echo $list_id ?>').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '">' + $.trim($('#test_table_<?php echo $list_id ?>').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</span>';
                                                            item_list_nexup_data += '<tr><td>' + $.trim($('#test_table_<?php echo $list_id ?>').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                                        }
                                                    }
                                                    grp_two += '<p data-toggle="modal" data-target="#items-list" id="items_model_p" data-rowid="' + (row_indx + 1) + '" data-placement="top" title="Show all items"><img src="<?php echo base_url(); ?>assets/img/information-button-icon-23.png"></p>';
                                                    if (grp_two != '') {
                                                        $('#tabs-<?php echo $list_id ?>').find('.button-outer').find('.nexup-sub-group-two').html(grp_two);
                                                    }
                                                    
                                                    

                                                } else if ($('#tabs-<?php echo $list_id ?>').find('.icon-settings-sub').attr('data-typeid') == 8) {
                                                    $('#tabs-<?php echo $list_id ?>').find('.nexup-sub-group-single').find('#next_task_name').text($.trim($('#test_table_<?php echo $list_id ?>').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                                    
                                                    var next_task_random = $('#tabs-<?php echo $list_id ?>').find('.nexup-sub-group-single').find('#next_task_name');
                                                    var numWords_random = $.trim(next_task_random.text()).length;
                                                    if ((numWords_random >= 1) && (numWords_random < 10)) {
                                                        next_task_random.css("font-size", "50px");
                                                    }
                                                    else if ((numWords_random >= 10) && (numWords_random < 20)) {
                                                        next_task_random.css("font-size", "36px");
                                                    }
                                                    else if ((numWords_random >= 20) && (numWords_random < 30)) {
                                                        next_task_random.css("font-size", "30px");
                                                    }
                                                    else if ((numWords_random >= 30) && (numWords_random < 40)) {
                                                        next_task_random.css("font-size", "26px");
                                                    }
                                                    else {
                                                        next_task_random.css("font-size", "20px");
                                                    }
                                                    
                                                    
                                                }
                                                if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                                                    $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                                        event.preventDefault()
                                                    }).tooltip();
                                                }
                                                $(document).on('click', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                                                    $('.tooltip').remove()
                                                });
                                            }
                                            var nexup_sun_grp_2_len = $('#tabs-<?php echo $list_id ?>').find('.button-outer').find('.nexup-sub-group-two').find('span').length;
                                            if (nexup_sun_grp_2_len == 1) {
                                                $('#test_table_<?php echo $list_id ?>').find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '18px');
                                            } else if (nexup_sun_grp_2_len == 2) {
                                                $('#test_table_<?php echo $list_id ?>').find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '16px');
                                            } else {
                                                $('#test_table_<?php echo $list_id ?>').find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '13px');
                                            }
                                            set_nexup_box_data();
                                        }
                                    });
                                }
                            }
                        }

                        var current_id = $('#tabs-<?php echo $list_id ?>').find('.tasks_lists_display.ui-sortable').attr('id');
                        $('#' + current_id).sortable('destroy');
                        var first_id = $('#test_table_<?php echo $list_id ?>').find('tbody tr:nth-child(2)').attr('id');
                        $("#" + first_id).sortable({
                            handle: '.icon-more',
                            cancel: '.heading_col',
                            update: function (event, ui) {
                                if ($('#test_table_<?php echo $list_id ?>').find('.rank_th').length > 0) {
                                    var rnk_val = 1;
                                    $('#test_table_<?php echo $list_id ?>').find('.rank_th').each(function () {
                                        $(this).text(rnk_val);
                                        rnk_val++;
                                    });
                                }
                                var tasks_ids = [];
                                $('#tabs-<?php echo $list_id ?>').find('.tasks_lists_display li').each(function (e) {
                                    var ids = $(this).attr('data-id');
                                    tasks_ids.push(ids);
                                });

                                var task_id = $(ui.item).attr('data-id');
                                var list_id = $(ui.item).children().attr('data-listid');
                                var user_ip = "<?php echo $_SERVER['REMOTE_ADDR']; ?>";
                                $.ajax({
                                    url: '<?php echo base_url() . 'order_change' ?>',
                                    type: 'POST',
                                    data: {
                                        OrderId: ui.item.index() + 1,
                                        Taskid: JSON.stringify(tasks_ids),
                                        ListId: list_id,
                                        user_ip: user_ip
                                    },
                                    success: function (res) {
                                        if (res != 'fail') {
                                            if ($('#test_table_<?php echo $list_id ?>').find('.icon-more-holder').length > 0) {
                                                var ord_val = 1;
                                                $('#test_table_<?php echo $list_id ?>').find('.icon-more-holder').each(function () {
                                                    $(this).attr('data-order', ord_val);
                                                    ord_val++;
                                                });
                                            }
                                            var total_rows = $('#test_table_<?php echo $list_id ?>').find('tbody tr').length;
                                            $.ajax({
                                                url: '<?php echo base_url() . 'item_order' ?>',
                                                type: 'POST',
                                                data: {
                                                    OrderId: ui.item.index() + 1,
                                                    Taskid: task_id
                                                },
                                                success: function (res) {
                                                }
                                            });
                                        }
                                    }
                                });
                            }
                        });
                        $('#test_table_<?php echo $list_id ?>').find('thead tr').sortable('refresh');
                    }
                }
            });

        }
    });



    var next_task = $('#tabs-<?php echo $list_id ?>').find(".whoisnext-div .nexup-group .nexup-group-two .button-outer .nexup-sub-group-one #next_task_name");
    var numWords = $.trim(next_task.text()).length;
    if ((numWords >= 1) && (numWords < 10)) {
        next_task.css("font-size", "50px");
    }
    else if ((numWords >= 10) && (numWords < 20)) {
        next_task.css("font-size", "36px");
    }
    else if ((numWords >= 20) && (numWords < 30)) {
        next_task.css("font-size", "30px");
    }
    else if ((numWords >= 30) && (numWords < 40)) {
        next_task.css("font-size", "26px");
    }
    else {
        next_task.css("font-size", "20px");
    }

    var nexup_sun_grp_2_len = $('#tabs-<?php echo $list_id ?>').find('.button-outer').find('.nexup-sub-group-two').find('span').length;
    if (nexup_sun_grp_2_len == 1) {
        $('#tabs-<?php echo $list_id ?>').find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '18px');
    } else if (nexup_sun_grp_2_len == 2) {
        $('#tabs-<?php echo $list_id ?>').find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '16px');
    } else {
        $('#tabs-<?php echo $list_id ?>').find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '13px');
    }
    
    var next_task_random = $('#tabs-<?php echo $list_id ?>').find('.nexup-sub-group-single').find('#next_task_name');
    var numWords_random = $.trim(next_task_random.text()).length;
    if ((numWords_random >= 1) && (numWords_random < 10)) {
        next_task_random.css("font-size", "50px");
    }
    else if ((numWords_random >= 10) && (numWords_random < 20)) {
        next_task_random.css("font-size", "36px");
    }
    else if ((numWords_random >= 20) && (numWords_random < 30)) {
        next_task_random.css("font-size", "30px");
    }
    else if ((numWords_random >= 30) && (numWords_random < 40)) {
        next_task_random.css("font-size", "26px");
    }
    else {
        next_task_random.css("font-size", "20px");
    }


    setInterval(function () {
        var extra_ids = [];
        $('#tabs-<?php echo $list_id ?>').find('.check_date').each(function () {
            extra_ids.push($(this).attr('data-id'));
        });
        var attendance_data_ids = extra_ids.join(',');
        var list_id = '<?php echo $list_id ?>';

        if ($('#test_table_' + list_id).find('tbody').find('tr').length > 0) {
            $.ajax({
                url: '<?php echo base_url() . 'task/get_check_time' ?>',
                type: 'POST',
                data: {
                    'attendance_data_ids': attendance_data_ids,
                    'list_id': list_id
                },
                success: function (res) {
                    if (res != 'fail' && res != 'empty') {
                        var json_res = JSON.parse(res);
                        var arr_len = json_res.length;

                        for (var i = 0; i < arr_len; i++) {
                            $('#test_table_<?php echo $list_id ?>').find('#span_time_' + json_res[i]['id']).text(json_res[i]['val']);
                            $('#test_table_<?php echo $list_id ?>').find('#span_time_' + json_res[i]['id']).closest('div.check_date').attr('data-original-title', json_res[i]['val']);
                        }

                    }
                }
            });
        }


    }, 30000);

    if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
        $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').tooltip('destroy');
    } else {
        $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
            event.preventDefault()
        }).tooltip();
    }
    
    var isTouchDevice = 'ontouchstart' in document.documentElement;
            if (isTouchDevice) {
                if ($(window).width() < 1367) {
                    $('.enable-move').removeClass('hide_move_btn');
//                    alert('enable');
                }
            } else {
                $('.enable-move').removeClass('hide_move_btn');
                $('.enable-move').addClass('hide_move_btn');
//                $('.enable-move').hide();
                $(document).on('mouseover', '.tasks_lists_display.ui-sortable .task_li', function () {
                    $(this).find('.icon-more').css({'visibility': 'visible'});
                });

                //Hide re-order handle when mouse out of task
                $(document).on('mouseout', '.task_li', function () {
                    $(this).find('.icon-more').css({'visibility': 'hidden'});
                });


                $(document).on('mouseover', '.heading_items_col', function () {
                    $(this).children().children().children('a.icon-more-h.move_col').css({'visibility': 'visible'});
                });

                $(document).on('mouseout', '.heading_items_col', function () {
                    $(this).children().children().children('a.icon-more-h.move_col').css({'visibility': 'hidden'});
                });
            }
            
            if ($('#tabs-<?php echo $list_id; ?>').find('.collapse_div').length > 0) {
                if('<?php echo $config['start_collapsed'] ?>' != 1){
                    $('#tabs-<?php echo $list_id; ?>').find('#addSubTaskDiv').accordion({
                        collapsible: true,
                        heightStyle: "content",
                    });
                }else{
                    $('#tabs-<?php echo $list_id; ?>').find('#addSubTaskDiv').accordion({
                        collapsible: true,
                        heightStyle: "content",
                        active: false
                    });
                }
                
            }
            
            if ($('#tabs-<?php echo $list_id; ?>').find('#listSub_Unlock_lnk').length == 1) {
                $('#tabs-<?php echo $list_id; ?>').find('.add-data-head-r').addClass('hide_add');
            }
            
            $(document).on('click', '#tabs-<?php echo $list_id; ?> #added_div h3#TaskListHead span.ui-icon-triangle-1-e', function () {
                $('#tabs-<?php echo $list_id; ?>').find('.add-data-head-r').addClass('hide_add');
            });
            $(document).on('click', '#tabs-<?php echo $list_id; ?> #added_div h3#TaskListHead span.ui-icon-triangle-1-s', function () {
                $('#tabs-<?php echo $list_id; ?>').find('.add-data-head-r').removeClass('hide_add');
                $('#tabs-<?php echo $list_id; ?>').find('#addTaskDiv').css('display', 'inline-block');
            });
</script>