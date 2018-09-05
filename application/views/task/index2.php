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
}else{
    $hide_add_item_cls = '';
    $collapsable_div = '';
    $no_hover_table = '';
    $no_hover_heading = '';
    $hide_add_col = '';
    $hide_add_row = '';
    $no_hover_data = '';
    if ($is_locked == 1) {
        $collapsable_div = ' collapse_div';
        $no_hover_table = ' no_hover_table';
        $no_hover_heading = ' no_hover_table';
        if($config['allow_append_locked'] == 1){
            $no_hover_table = '';
            $no_hover_data = ' no_hover_table';
        }
        $hide_add_row = ' hidden_add_row';
        $hide_add_col = ' hidden_add_col';
        
        if(isset($_SESSION['id']) && $_SESSION['id'] == $list_user_id){
            $hide_add_item_cls = '';
            $no_hover_table = '';
            $hide_add_row = '';
            $hide_add_col = '';
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
    
?>
<div id="tabs" class="tab_new_custom_wrapper">
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
        <a id="delete_list_builder" class="delete_list_builder delete_list_tabbed custom_cursor" data-id="<?php echo $list_id ?>" data-slug="<?php echo $list_slug ?>" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Delete List" style="opacity: 0;"><img src="/assets/img/rubbish-bin.png"></a>
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
        <div class="list_desc_div hiden_desc">
            <span id="list_desc_text" class="list_desc_text" data-listid="<?php echo $list_id; ?>"><?php echo nl2br($list_desc); ?></span>
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
    
        <ul id="tab_ul" class="tab_ul tab_custom_head">
            <?php
            if(!empty($sublists)){
                foreach ($sublists as $sublistKeyTab=>$sublistDataTab):
            ?>
            <li class="custom_tab">
                <a href="#tabs-<?php echo $sublistDataTab['list_id']; ?>" class="custom_tab_anchor" data-listid="<?php echo $sublistDataTab['list_id']; ?>"><?php echo $sublistDataTab['list_name']; ?></a>
            </li>
            <?php
                endforeach;
            }
            ?>
            <li id="add_tab_li" class="add_tab_li"><a id="add_tab" class="custom_cursor icon-add"></a></li>
        </ul>
    <?php
    if(!empty($sublists)){
        foreach ($sublists as $sublistKey=>$sublistData):
    ?>
    <div id="tabs-<?php echo $sublistData['list_id']; ?>">
        <img src="<?php echo base_url() . 'assets/img/preloader.gif'; ?>" class="loader_img">
    </div>
    <?php
        endforeach;
    }
    ?>
</section>
</div>

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
                    <a class="btn btn-success btn-sm copy-btn" id="copy_btn" onclick="copyToClipboard('import_contacts', 'hdn_copy_url')">Copy</a>

                    <!--<input type="text" id="hdn_copy_url" name="hdn_copy_url" style="height:1px; width:1px;position: absolute;left: -100px;">-->
                    <textarea id="hdn_copy_url" name="hdn_copy_url" style="height:1px; width:1px;position: absolute;left: -1000px;">
                        
                    </textarea>
                    <!--<input type="text" id="hdn_copy_url" name="hdn_copy_url" style="height:1px; width:1px;position: absolute;left: -100px;">-->

                </div>
                <div class="input-outer object_outer">
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




<div class="modal fade share-modal" id="share-contact-sublist" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="sharemodal-sublist-head">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon-cross-out"></span></button>
                <h2 class="share_heading_h2">
                    Share:
                    <span class="share_heading_span share_heading_span_sublist"></span>
                </h2>
                <span class="custom_slug_err alert-danger" style="display: none;"></span>
            </div>
            <div class="sharemodal-body sharemodal-sublist-body auth_share">
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
                        <label for="move_item">Allow data rearrange</label>
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
                if ($type_id == 11) {
                    $hidden_comment_config_class = '';
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
                }
                ?>


                <div class="button-outer" id="config_btn_div">
                    <button type="submit" name="save_config" id="save_config" data-listid="<?php echo $list_id; ?>">Save</button>
                    <button type="submit" name="close_config" id="close_config" class="close_config" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade config-modal-sub-list" id="config_sub_list" tabindex="-2" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="configmodal-head">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon-cross-out"></span></button>
                <h2>List Configuration</h2>
            </div>
            <div class="configmodal-body config-submodal-body">

            </div>
        </div>
    </div>
</div>



<div class="modal fade bulk_data_modal bulk_data_sub_modal" id="bulk_data_sub_modal" tabindex="-3" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="bulk_data_modal-head bulk_data_sub_modal-head">
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
                            <!--<label class="option_list_type"><input type="radio" name="type_option" id="type_option_email" class="list_type_opt" value="email"> E-mails</label>-->
                            <select class="spearator_options form-control spearator_sub_options" id="spearator_sub_options">
                                <option value="comma">Comma separated</option>
                                <option value="tab">Tab separated</option>
                            </select>
                        </div>
                        <div class="checkbox-outer">
                            <div class="checkbox">
                                <input id="include_sub_header" type="checkbox" name="include_sub_header" value="True" checked="checked">
                                <label for="include_sub_header">Header</label>
                            </div>	
                        </div>
                    </div>
                    
                    <div class="col-sm-12 separator_div_import_export">
                        <span class="cpoy_btn">
                            <span class="import_bulk_btn import_bulk_wrapper">
                                <a class="btn btn-sm import-btn" id="import_sub_bulk_btn">Import</a>
                                <input type="file" name="bulk_sub_import" id="bulk_sub_import" class="bulk_import">
                            </span>
                            <span class="export_bulk_btn">
                                <a class="btn btn-primary btn-sm export-btn" id="export_sub_bulk_btn" download="BulkData.csv">Export</a>
                            </span>
                        </span>
                    </div>
                    
                    <!--<textarea id="values_sub_items"></textarea>-->
                    <!--<div id="values_sub_items" contenteditable="true" data-gramm_editor="false"></div>-->
                    <!--<div id="values_sub_items" contenteditable="true" data-gramm_editor="false"></div>-->
                    <textarea id="values_sub_items" data-gramm_editor="false" rows="10" style="resize: none;"></textarea>
                    <div id="init_value_sub_items" style="display: none;"></div>
                    <textarea id="hdn_values_sub_items" style="height:1px; width:1px;position: absolute;left: -100px;"></textarea>
                    
                </div>
                <div class="col-sm-12 separator_div">
                    <span class="bulk_loader hiden_img" style="display: none;">
                        <img src="/assets/img/loader.gif">
                    </span>
                    <span class="cpoy_btn">
                        <div class="checkbox-outer">
                            <div class="checkbox">
                                <input type="checkbox" name="type_option_cb" id="type_option_cb_email" class="list_type_opt" value="email">
                                <label for="type_option_cb_email">E-mail list</label>
                            </div>
                        </div>
                        <a class="btn btn-success btn-sm copy-btn" id="copy_bulk_btn" onclick="copyDataToClipboard('values_sub_items', 'hdn_values_sub_items')">Copy</a>
                    </span>
                </div>
                <div class="button-outer" id="config_btn_div">
                    <button type="submit" name="save_sub_bulk" id="save_sub_bulk">Ok</button>
                    <button type="submit" name="close_sub_bulk" id="close_sub_bulk" class="close_bulk" data-dismiss="modal">Cancel</button>
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


<div class="modal fade log-modal" id="log-list-sub" tabindex="-2" role="dialog" aria-labelledby="LogModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="logmodal-head">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon-cross-out"></span></button>
                <h2>Action Log</h2>
            </div>
            <div class="logmodal-body-sub">
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
                    <table class="table table-striped table-responsive log_sub_table_popup" id="log_sub_table_popup">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Comment</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        </tbody>
                    </table>
                    <!--</ul>-->
                </div>
                <div class="button-outer" id="config_btn_div">
                    <button type="submit" name="close_log_sub" id="close_log_sub" class="close_config" data-dismiss="modal">Cancel</button>
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

<div class="modal fade col-modal" id="col_sub_list" tabindex="-3" role="dialog" aria-labelledby="colModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="col-sub-modal-head">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon-cross-out"></span></button>
                <h2>Column</h2>
            </div>
            <div class="col-sub-modal-body">
                <div id="col_div" class="col_div">
                    <span class="col_sub_msg" style="display: none;">

                    </span>
                    <span class="add_sub_coll_span" id="add_coll_span">
                        <input type="text" id="nexup_sub_column" class="nexup_sub_column" placeholder="Column Name">
                    </span>
                </div>
                <div class="button-outer" id="config_btn_div">
                    <button type="submit" name="save_sub_col" id="save_sub_col" class="save_sub_col" data-listid="">Save</button>
                    <button type="submit" name="close_sub_col" id="close_sub_col" class="close_sub_col close_config" data-dismiss="modal">Cancel</button>
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

