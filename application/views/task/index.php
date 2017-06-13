<section id="content" class="content">
    <?php
    if ($type_id == 2) {
        ?>
        <div class="whoisnext-div">
            <div class="nexup-group">
                <?php
                $btn_cls = '';
                if (empty($tasks)) {
                    $btn_cls = ' whosnext_img_bg';
                }
                ?>
                <div class="button-outer custom_cursor<?php echo $btn_cls; ?>" data-toggle="tooltip" title="<?php
                if (!empty($tasks)) {
                    echo $tasks[0]['TaskName'];
                }
                ?>">
                    <span id="next_task_name"><?php
                        if (!empty($tasks)) {
                            echo $tasks[0]['TaskName'];
                        }
                        ?></span>
                    <!--<span class="tooltiptext-task-next"></span>-->
                </div>
                <div id="nexup_btns">
                    <?php
                    $undo_class = '';
                    if ($config['allow_undo'] == 0) {
                        $undo_class = ' disabled_undo';
                    }
                    ?>
                    <a class="undo-btn custom_cursor<?php echo $undo_class; ?>" data-listid="<?php echo $list_id; ?>"><span id="undo_icon" class="icon-undo2"> </span> Back up</a>
                    <div class="cmnt-btn-div">
                        <a class="whoisnext-btn-cmnt custom_cursor"><span id="nexup_icon_cmnt" class="icon-redo2"> </span> Nexup</a>
                        <span class="add-data-div hide_box" id="nexup_cmnt_span">
                            <input type="text" id="nexup_comment" class="nexup_comment" placeholder="Comment...">
                        </span>
                    </div>

                </div>
            </div>
        </div>
        <?php
    }
    ?>
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
    <div class="add-data-head">
        <?php
        $hide_list = '';
        $show_add_column = '';
        if ($list_id == 0) {
            $show_add_column = ' hidden_add_column_btn';
            ?>
            <input name="edit_list_name" id="edit_list_name" class="edit-list-class" data-id="<?php echo $list_id; ?>" value="<?php echo $list_name; ?>" placeholder="List" type="text">
            <?php
            $hide_list = 'display: none;';
        }
        ?>
        <h2 id="listname_<?php echo $list_id; ?>" class="listname_<?php echo $list_id; ?> edit_list_task" style="<?php echo $hide_list; ?>" data-id="<?php echo $list_id; ?>" data-slug="<?php echo $list_slug; ?>" id="edit_list_task_page"><?php echo $list_name; ?></h2>
        <a data-toggle="modal" data-target="#share-contact" id="share_list" class="icon-share custom_cursor" style="<?php echo $hide_list; ?>"> </a>
        <div class="plus-category">
            <?php
            $class_hide_settings = '';
            $calss_hide_lock = '';
            if (isset($_SESSION['logged_in']) && $_SESSION['id'] != $list_owner_id) {
                if ($list_owner_id > 0) {
                    $class_hide_settings = ' hide_config';
                }
            } elseif (!isset($_SESSION['logged_in']) && $is_locked == 1) {
                $class_hide_settings = ' hide_config';
            } else {
                $calss_hide_lock = ' hide_lock';
            }
            if ($is_locked == 1) {
                ?>
                <a class="icon-lock2 custom_cursor<?php echo $calss_hide_lock; ?>" id="config_lcoked" <?php
                if ($is_locked == 1) {
                    echo 'data-locked="1"';
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
            } else {
                echo 'data-locked="0"';
            }
            ?>></a>
        </div>
        <span class="config_icons hide_data" id="config_icons">
            <a data-toggle="modal" data-target="#listConfig" id="listConfig_lnk" class="icon-wrench custom_cursor"> </a>
            <div class="ddl_lt">
                <a id="listTypes_lnk" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" class="icon-list custom_cursor"> </a>
                <ul class="dropdown-menu" id="listType_dd" aria-labelledby="listTypes_lnk">
                    <?php
                    foreach ($list_types as $listType):
                        ?>
                        <li id="listType_<?php echo $listType['ListTypeId']; ?>" class="list_type_cls custom_cursor" data-typeId="<?php echo $listType['ListTypeId']; ?>" data-listid="<?php echo $list_id; ?>"><?php echo $listType['ListTypeName']; ?></li>
                        <?php
                    endforeach;
                    ?>
                </ul>
            </div>
            <?php
            if ($this->session->userdata('logged_in')) {
                $class_lock = '';
                if ($list_owner_id != $_SESSION['id']) {
                    $class_lock = 'lock_hide';
                }
                if ($is_locked == 0) {
                    ?>
                    <a class="icon-lock2 custom_cursor <?php echo $class_lock; ?>" id="listLock_lnk" data-id="<?php echo $list_id; ?>" data-slug="<?php echo $list_slug; ?>"></a>
                    <?php
                } else {
                    ?>
                    <a class="icon-lock-open2 custom_cursor <?php echo $class_lock; ?>" id="listUnlock_lnk" data-id="<?php echo $list_id; ?>" data-slug="<?php echo $list_slug; ?>"></a>
                    <?php
                }
            }
            ?>
        </span>
        <div class="add-data-head-r<?php echo $show_add_column; ?>">
            <a class="add_column_url custom_cursor icon-add"></a>
        </div>
    </div>

    <?php
    $hide_add_item_cls = '';
    $collapsable_div = '';
    if ($is_locked == 1) {
        $hide_add_item_cls = ' hide_add_item';
        $collapsable_div = ' collapse_div';
    }
    ?>
    <div class="add-data-body<?php echo $collapsable_div; ?>">
        <div id="addTaskDiv" class="item-add-div multi-column-lists">
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
            if(!empty($tasks)){
                $task_size = count($tasks);
                if($multi_col == 1){
                    if($task_size == 2){
                        $task_class = ' column-2';
                    }elseif($task_size == 3){
                        $task_class = ' column-3';
                    }elseif($task_size > 3){
                        $task_class = ' column-4';
                    }
                    $task_list_div_class= ' task_multi_col_div';
                }
                
            }
            ?>
            
            <div id="TaskListDiv" class="column-css<?php echo $task_class . $task_list_div_class; ?>">
                <h3 id="TaskListHead"></h3>
                <?php
                if ($multi_col == 0) {
                    ?>
                    <ul class="add-data-body-ul <?php echo $sort_class; ?>" id="TaskList">
                        <li class="heading_col add_item_input"><div class="<?php echo $hide_add_item_cls; ?> add-data-input"><input type="text" name="task_name" id="task_name" class="task_name" data-listid="<?php echo $list_id; ?>" data-colid="0" placeholder="Item" /></div></li>
                        <?php
                        if (!empty($tasks)) {
                            foreach ($tasks as $task):
                                ?>
                                <li id="task_<?php echo $task['TaskId']; ?>" class="task_li" data-id="<?php echo $task['TaskId']; ?>">

                                    <div class="add-data-div edit_task <?php
                                    if ($task['IsCompleted']) {
                                        echo 'completed_task';
                                    }
                                    ?>" data-id="<?php echo $task['TaskId'] ?>" data-task="<?php echo $task['TaskName']; ?>" data-listid="<?php echo $list_id; ?>">
                                        <span class="icon-more"></span>
                                        <span id="span_task_<?php echo $task['TaskId']; ?>" class="task_name_span"><?php echo $task['TaskName']; ?></span>
                                        <div class="opertaions pull-right">
                                            <a href="javascript:void(0)" class="icon-cross-out delete_task custom_cursor" data-id="<?php echo $task['TaskId']; ?>" data-task="<?php echo $task['TaskName']; ?>" data-listid="<?php echo $list_id; ?>"></a>
                                            <?php
                                            if ($type_id == 5) {
                                                ?>
                                                <a href="javascript:void(0)" class="icon-checked complete_task custom_cursor" data-id="<?php echo $task['TaskId']; ?>" data-task="<?php echo $task['TaskName']; ?>" data-listid="<?php echo $list_id; ?>"></a>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>

                                </li>
                                <?php
                            endforeach;
                            ?>
                            <?php
                        }
                        ?>
                    </ul>
                    <?php
                } else {
                    foreach ($tasks as $ids => $task):
                        if($ids <= 0){
                            $task_ul_id = 'TaskList';
                        }else{
                            $task_ul_id = 'TaskList' . $ids;
                        }
                        ?>
                        <ul class="add-data-body-ul <?php echo $sort_class; ?>" id="<?php echo $task_ul_id; ?>">
                            <li class="heading_col add_item_input">
                                <div class="<?php echo $hide_add_item_cls; ?> add-data-input"><input type="text" name="task_name" id="task_name" class="task_name" data-listid="<?php echo $list_id; ?>" data-colid="<?php echo $task['column_id']; ?>" placeholder="Item" /></div>
                                <!--<div class="add-data-input"><input type="text" name="" /></div>-->
                            </li>
                            <li class="heading_col heading_items_col">
                                <div class="add-data-title"><?php echo $task['column_name'] ?>
                                    <div class="add-data-title-r">
                                        <a href="" class="icon-more-h" id="dropdownMenu<?php echo $ids; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></a>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu<?php echo $ids; ?>">
                                            <li><a class="remove_col" data-colid="<?php echo $task['column_id']; ?>">Remove</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            <?php
                                foreach ($task['tasks'] as $t):
                            ?>
                            <li id="task_<?php echo $t['TaskId']; ?>" class="task_li" data-id="<?php echo $t['TaskId']; ?>">
                                <div class="add-data-div edit_task <?php if ($t['IsCompleted']) { echo 'completed_task'; } ?>" data-id="<?php echo $t['TaskId'] ?>" data-task="<?php echo $t['TaskName']; ?>" data-listid="<?php echo $list_id; ?>">
                                    <span class="icon-more"></span>
                                    <span id="span_task_<?php echo $t['TaskId']; ?>" class="task_name_span"><?php echo $t['TaskName']; ?></span>
                                    <div class="opertaions pull-right">
                                        <a href="javascript:void(0)" class="icon-cross-out delete_task custom_cursor" data-id="<?php echo $t['TaskId']; ?>" data-task="<?php echo $t['TaskName']; ?>" data-listid="<?php echo $list_id; ?>"></a>
                                        <?php
                                        if ($type_id == 5) {
                                            ?>
                                            <a href="javascript:void(0)" class="icon-checked complete_task custom_cursor" data-id="<?php echo $t['TaskId']; ?>" data-task="<?php echo $t['TaskName']; ?>" data-listid="<?php echo $list_id; ?>"></a>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </li>
                            <?php
                                endforeach;
                            ?>
                        </ul>
                        <?php
                    endforeach;
                }
                ?>
            </div>
        </div>


    </div>
</section>

<div class="modal fade share-modal" id="share-contact" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="sharemodal-head">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon-cross-out"></span></button>
                <h2>Share <span><?php echo $list_name; ?></span> list:</h2>
            </div>

            <div class="sharemodal-body auth_share">
                <div id="share_msg" class="alert no-border" style="display: none;"></div>
                <div class="input-outer">
                    <label>Share Url</label>
                    <?php
                    if ($this->uri->segment(2) != '') {
                        $slug = $this->uri->segment(2);
                    } elseif (isset($_SESSION['last_slug']) && $_SESSION['last_slug'] != '') {
                        $slug = $_SESSION['last_slug'];
                    } else {
                        $slug = '';
                    }
                    ?>
                    <a id="import_contacts" href="<?php echo base_url() . 'item/' . $slug; ?>" target="_blank"><?php echo base_url() . 'item/' . $slug; ?></a>
                    <a class="btn btn-success btn-sm copy-btn" id="copy_btn" onclick="copyToClipboard('import_contacts')">Copy</a>
                    <input type="text" id="hdn_copy_url" name="hdn_copy_url" style="height:1px; width:1px;position: absolute;left: -100px;">
                </div>
                <?php
                if ($this->session->userdata('logged_in')) {
                    ?>
                    <div class="input-outer">
                        <label>Your Friend's Email</label>
                        <input type="text" id="share_email" name="share_email" placeholder="johndoe@yourmail.com" />
                    </div>
                    <div class="input-textarea">
                        <textarea id="message_share" name="message_share" placeholder="Add Message to all friends"></textarea>
                    </div>

                    <?php
                }
                ?>
                <div class="button-outer">
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
                    if ($this->session->userdata('logged_in')) {
                        ?>
                        <button type="submit" name="invite_btn" id="invite_btn" data-link="<?php echo base_url() . 'item/' . $slug; ?>" data-listid="<?php echo $list_id; ?>">Send Invitations</button>
                        <?php
                    }
                    ?>
                    <button type="submit" name="close_share" id="close_share" class="close_share" data-dismiss="modal">Close</button>
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
                <h2>Configuration</h2>
            </div>
            <div class="configmodal-body">

                <div id="config_msg" class="alert no-border" style="display: none;">

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
                        <label for="move_item">Allow items to move</label>
                    </div>	
                </div>

                <div class="checkbox-outer">
                    <div class="checkbox">
                        <?php
                        $show_completed = 'checked="checked"';
                        if ($config['show_completed'] == 'False') {
                            $show_completed = '';
                        }
                        ?>
                        <input id="show_completed_item" type="checkbox" name="show_completed_item" value="True" <?php echo $show_completed; ?>>
                        <label for="show_completed_item">Show Completed Items</label>
                    </div>	
                </div>

                <div class="checkbox-outer">
                    <div class="checkbox">
                        <?php
                        $undo_items = 'checked="checked"';
                        if ($config['allow_undo'] == 0) {
                            $undo_items = '';
                        }
                        ?>
                        <input id="undo_item" type="checkbox" name="undo_item" value="True" <?php echo $undo_items; ?>>
                        <label for="undo_item">Allow users to undo</label>
                    </div>	
                </div>

                <div class="button-outer" id="config_btn_div">
                    <button type="submit" name="save_config" id="save_config" data-listid="<?php echo $list_id; ?>">Save Configuration</button>
                    <button type="submit" name="close_config" id="close_config" class="close_config" data-dismiss="modal">Close</button>
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
                <h2>Log</h2>
            </div>
            <div class="logmodal-body">
                <div id="log_div" class="log_div">
                    <!--<ul class="dropdown-menu2" id="log_dd2" aria-labelledby="dropdownMenuLog2">-->
                    <table class="table table-striped table-responsive">
                        <?php
                        foreach ($log_list as $key_log => $log):
                            $hourdiff = round((strtotime(date('Y-m-d H:i:s')) - strtotime($log['created'])) / 3600, 1);
                            $cmt = 'Traversed on ' . $log['created'];

                            if ($hourdiff > 1 && $hourdiff < 24) {
                                if (floor($hourdiff) > 1) {
                                    $hrs = ' hours';
                                } else {
                                    $hrs = ' hour';
                                }
                                $cmt = 'Traversed on ' . floor($hourdiff) . $hrs . ' ago';
                            } elseif ($hourdiff <= 1) {
                                $min_dif = $hourdiff * 60;
                                if ($min_dif > 1) {
                                    if (floor($min_dif) > 1) {
                                        $minutes = ' minutes';
                                    } else {
                                        $minutes = ' minute';
                                    }
                                    $cmt = 'Traversed on ' . floor($min_dif) . $minutes . ' ago';
                                } else {
                                    $cmt = 'Traversed Just Now';
                                }
                            }

                            if (isset($_SESSION['logged_in'])) {
                                if ($log['user_id'] == $_SESSION['id']) {
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
                                                $cmt = $log['comment'] . ' (' . floor($min_dif) . $minutes . ' ago)';
                                            } else {
                                                $cmt = $log['comment'] . ' (Just Now)';
                                            }
                                        }
                                    }
                                    ?>
                                    <tr><td><?php echo $cmt; ?></td></tr>
                                    <!--<li class='log_options'><?php echo $cmt; ?></li>-->
                                    <?php
                                }
                            } else {

                                if ($log['user_ip'] == $_SERVER['REMOTE_ADDR'] && $log['user_id'] == 0) {
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
                                                $cmt = $log['comment'] . ' (' . floor($min_dif) . $minutes . ' ago)';
                                            } else {
                                                $cmt = $log['comment'] . ' (Just Now)';
                                            }
                                        }
                                    }
                                    ?>
                                    <tr><td><?php echo $cmt; ?></td></tr>
                                    <!--<li class='log_options'><?php echo $cmt; ?></li>-->          
                                    <?php
                                }
                            }
                        endforeach;
                        ?>
                    </table>
                    <!--</ul>-->
                </div>
                <div class="button-outer" id="config_btn_div">
                    <button type="submit" name="close_config" id="close_config" class="close_config" data-dismiss="modal">Close</button>
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
                    <span class="add_coll_span" id="add_coll_span">
                        <input type="text" id="nexup_column" class="nexup_column" placeholder="Column Name">
                    </span>
                </div>
                <div class="button-outer" id="config_btn_div">
                    <button type="submit" name="save_col" id="save_col" class="save_col" data-listid="<?php echo $list_id; ?>">Save</button>
                    <button type="submit" name="close_col" id="close_col" class="close_col close_config" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .edit-list-class{width: auto;position:relative;}
    input#edit_list_name { border: 1px solid #f5f3f3;}
</style>