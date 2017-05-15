<section id="content" class="content">
    <?php
    if ($type_id == 2) {
        ?>
        <div class="whoisnext-div">
            <?php
            $btn_cls = '';
            if (empty($tasks)) {
                $btn_cls = ' whosnext_img_bg';
            }
            ?>
            <div class="button-outer<?php echo $btn_cls; ?>" data-toggle="tooltip" title="<?php
            if (!empty($tasks)) {
                echo $tasks[0]->TaskName;
            }
            ?>">
                <span id="next_task_name"><?php
                    if (!empty($tasks)) {
                        echo $tasks[0]->TaskName;
                    }
                    ?></span>
                <!--<span class="tooltiptext-task-next"></span>-->
            </div>
            <a class="whoisnext-btn custom_cursor">Who's Next</a>
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
        if ($list_id == 0) {
            ?>
            <input name="edit_list_name" id="edit_list_name" class="edit-list-class" data-id="<?php echo $list_id; ?>" value="<?php echo $list_name; ?>" placeholder="List" type="text">
            <?php
            $hide_list = 'display: none;';
        }
        ?>
        <h2 id="listname_<?php echo $list_id; ?>" class="listname_<?php echo $list_id; ?> edit_list_task" style="<?php echo $hide_list; ?>" data-id="<?php echo $list_id; ?>" data-slug="<?php echo $list_slug; ?>" id="edit_list_task_page"><?php echo $list_name; ?></h2>
        <!--<a class="icon-edit custom_cursor edit_list_task" data-id="<?php echo $list_id; ?>" data-slug="<?php echo $list_slug; ?>" id="edit_list_task_page" style="<?php echo $hide_list; ?>"></a>-->
        <a data-toggle="modal" data-target="#share-contact" id="share_list" class="icon-share custom_cursor" style="<?php echo $hide_list; ?>"> </a>
        <div class="plus-category">
            <a class="icon-settings custom_cursor" id="config_lnk" <?php
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
                        <li id="listType_<?php echo $listType->ListTypeId; ?>" class="list_type_cls custom_cursor" data-typeId="<?php echo $listType->ListTypeId; ?>" data-listid="<?php echo $list_id; ?>"><?php echo $listType->ListTypeName; ?></li>
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
    </div>

    <div class="add-data-body">
        <ul class="add-data-body-ul" id="TaskAdd">
            <li id="add_task_li">
                <div class="add-data-div"><input type="text" name="task_name" id="task_name" data-listid="<?php echo $list_id; ?>" placeholder="Item" /></div>
                <div class="add_task_cls" style="display: none;"></div>
            </li>
        </ul>
        <?php
        $sort_class = '';
        $move_btn_cls ='';
        if ($config['allow_move'] == 'True') {
            $sort_class = 'tasks_lists_display';
        }
        if(empty($tasks) || $config['allow_move'] != 'True'){
            $move_btn_cls =' hide_move_btn';
        }
        ?>
        <button type="button" class="btn btn-default enable-move<?php echo $move_btn_cls; ?>">Enable Rearrange Items</button>
        <ul class="add-data-body-ul <?php echo $sort_class; ?>" id="TaskList">
            <?php
            if (!empty($tasks)) {
                foreach ($tasks as $task):
                    ?>
                    <li id="task_<?php echo $task->TaskId; ?>" class="task_li" data-id="<?php echo $task->TaskId; ?>">

                        <div class="add-data-div edit_task <?php
                        if ($task->IsCompleted) {
                            echo 'completed_task';
                        }
                        ?>" data-id="<?php echo $task->TaskId ?>" data-task="<?php echo $task->TaskName; ?>" data-listid="<?php echo $list_id; ?>">
                            <span class="icon-more"></span>
                            <span id="span_task_<?php echo $task->TaskId; ?>" class="task_name_span"><?php echo $task->TaskName; ?></span>
                            <div class="opertaions pull-right">
                                <a href="javascript:void(0)" class="icon-cross-out delete_task custom_cursor" data-id="<?php echo $task->TaskId ?>" data-task="<?php echo $task->TaskName; ?>" data-listid="<?php echo $list_id; ?>"></a>
                                <?php
                                if ($type_id == 5) {
                                    ?>
                                    <a href="javascript:void(0)" class="icon-checked complete_task custom_cursor" data-id="<?php echo $task->TaskId ?>" data-task="<?php echo $task->TaskName; ?>" data-listid="<?php echo $list_id; ?>"></a>
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
    </div>
</section>

<div class="modal fade share-modal" id="share-contact" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="sharemodal-head">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon-cancel"></span></button>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon-cancel"></span></button>
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

                <div class="button-outer" id="config_btn_div">
                    <button type="submit" name="save_config" id="save_config" data-listid="<?php echo $list_id; ?>">Save Configuration</button>
                    <button type="submit" name="close_config" id="close_config" class="close_config" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade listType-modal" id="listTypesModal" tabindex="-3" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="listTypesmodal-head">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon-cancel"></span></button>
                <h2>Select List Type</h2>
            </div>
            <div class="listTypemodal-body">

                <div id="ListType_msg" class="alert no-border" style="display: none;">

                </div>

                <?php
                foreach ($list_types as $listType):
                    $radio_checked = '';
                    if ($type_id == $listType->ListTypeId) {
                        $radio_checked = 'checked="checked"';
                    }
                    ?>
                    <div class="radio-outer">
                        <div class="radio">
                            <input id="listType_<?php echo $listType->ListTypeId; ?>" class="list_type_cls" type="radio" name="listType" value="True" data-typeId="<?php echo $listType->ListTypeId; ?>" <?php echo $radio_checked; ?>>
                            <label for="listType_<?php echo $listType->ListTypeId; ?>"><?php echo $listType->ListTypeName; ?></label>
                        </div>
                    </div>
                    <?php
                endforeach;
                ?>

                <div class="button-outer">
                    <button type="submit" name="update_listType_btn" id="update_listType_btn" data-listid="<?php echo $list_id; ?>">Update List Type</button>
                </div>
            </div>
        </div>
    </div>
</div>




<style>
    .edit-list-class{width: auto;position:relative;}
    input#edit_list_name { border: 1px solid #f5f3f3;}
</style>