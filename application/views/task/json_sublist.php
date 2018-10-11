<?php
$locked_list = '';
$collipsable_div = '';
if($list_details['is_locked'] == 1){
    $locked_list = ' locked_list';
    if($list_details['start_collapsed'] == 1){
        $collipsable_div = ' collapsible_div';
    }
}
?>
<div class="my_table my_scroll_table my_calendar_table table_one_page<?php echo $locked_list . $collipsable_div; ?>">
    <div class="caleneder_div_wrapper">
        <div class="container week_view_container">
            <div class="div_btn_name">
                <div class="btn_box_add">
                    <?php
                    $date_active = ' active';
                    $today_active = '';
                    if(date('m/j/Y', strtotime($list_details['list_name'])) == date('m/j/Y')){
                        $date_active = '';
                        $today_active = ' active';
                    }
                    ?>
                    <a class="btn-today<?php echo $today_active; ?>">Today</a>
                    <a class="btn-day<?php echo $date_active; ?>">Day</a>
                    <a class="btn-week">Week</a>
                    <a class="btn-month">Month</a>
                </div>
            </div>
            <div class="div_full_data calender_div_inner_box">
                <div class="left_div">
                    <div class="day_calendar">

                    </div>
                </div>
                <div class="div_new_data">

                    <div class="head_custom head_custom_sublist">
                        <div class="add-data-head sub_list_title_head">
                            <h2 id="listname_<?php echo $list_details['list_id']; ?>" class="listname_<?php echo $list_details['list_id']; ?> edit_list_task_sub" style="" data-id="<?php echo $list_details['list_id']; ?>" data-slug="<?php echo $list_details['list_slug']; ?>" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="<?php echo $list_details['list_name']; ?>">
                                <a class="prev_day day-arrow" data-date="<?php echo date('m/j/Y', strtotime($list_details['list_name'] . ' -1 day')); ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                                
                                <span class="sub_list_name_span_calendar" data-listname="<?php echo $list_details['list_name']; ?>"><?php echo $list_details['list_name']; ?></span>
                                <a class="next_day day-arrow" data-date="<?php echo date('m/j/Y', strtotime($list_details['list_name'] . ' +1 day')); ?>"><i class="fa fa-chevron-right" aria-hidden="true"></i></a>
                            </h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="count_div"> 
                            <span id="count_visit_span" data-totalvisits="<?php echo $visit_history; ?>"><?php echo $visit_history; ?></span>
                        </div>
                    </div>
                    <?php
                    if(isset($list_details['show_author']) && $list_details['show_author'] != 0){
                        $list_author = 'Anonymous';
                        if($list_details['created_user_name'] != 'Anonymous' && $list_details['created_user_name'] != '')
                        $list_author = $list_details['created_user_name'];
                    ?>
                    <div class="list_author_cls list_author_cls_calendar"><?php echo $list_author; ?></div>
                    <?php
                    }
                    ?>


                    <div class="my_table my_sub_table my_scroll_table">
                        <table id="test_table_<?php echo $list_details['list_id']; ?>" class="table test_table">

                            <thead>
                                <tr class="td_add_tr ui-sortable">

                            <th class="noDrag nodrag_action_heading"></th>
                            <?php
                            $hide_rearrange_class = ' hidden_rearrange';
                            if($list_details['allow_move'] == 1){
                                $hide_rearrange_class = '';
                            }
                            ?>

                            <?php
                            foreach ($columns as $ids => $col):
                                $placeholder = 'Add ' . $list_details['list_name'];
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
                            <th class="heading_items_col_add" data-listid="<?php echo $list_details['list_id']; ?>" data-colid="<?php echo $col['id']; ?>">
                                <div class=" add-data-input">
                                    <?php if($col['type'] != 'memo' && $col['type'] != 'number'){ ?>
                                    <input type="text" name="task_name" id="task_name" class="task_sub_name" data-listid="<?php echo $list_details['list_id']; ?>" data-colid="<?php echo $col['id']; ?>" placeholder="<?php echo $placeholder; ?>"  data-type="<?php echo $col['type']; ?>" <?php if($col['type'] == 'checkbox' || $col['type'] == 'timestamp'){ echo 'style="display: none;"'; } ?>/>
                                    <?php }elseif($col['type'] == 'number'){ ?>
                                    <input type="number" name="task_name" id="task_name" class="task_sub_name" data-listid="<?php echo $list_details['list_id']; ?>" data-colid="<?php echo $col['id']; ?>" placeholder="<?php echo $placeholder; ?>"  data-type="<?php echo $col['type']; ?>" <?php if($col['type'] == 'checkbox' || $col['type'] == 'timestamp'){ echo 'style="display: none;"'; } ?>/>
                                    <?php } else{ ?>
                                    <textarea name="task_name" id="task_name" class="task_sub_name" data-listid="<?php echo $list_details['list_id']; ?>" data-colid="<?php echo $col['id']; ?>" placeholder="<?php echo $placeholder; ?>"  data-type="<?php echo $col['type']; ?>" data-gramm_editor="false" rows="5" <?php if($col['type'] == 'checkbox' || $col['type'] == 'timestamp'){ echo 'style="display: none;"'; } ?>></textarea>
                                    <?php } ?>
                                    <span class="span_enter"><img src="/assets/img/enter.png" class="enter_img"></span>
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
                            <th class="noDrag nodrag_time hidden_nodrag"></th>
                            <th class="noDrag nodrag_comment hidden_nodrag"></th>
                            </tr>
                            <tr class="td_arrange_tr ui-sortable">
                                <th class="noDrag nodrag_actions"><div class="add-data-title-nodrag hidden_nodrag status-column"></div></th>
                                <?php
                                $hide_col_class = '';
                                if(count($columns) == 1){
                                    $hide_col_class = ' hidden_heading';
                                }
                                foreach ($columns as $ids => $col):
                                    if ($ids <= 0) {
                                        $task_ul_id = 'TaskList';
                                    } else {
                                        $task_ul_id = 'TaskList' . $ids;
                                    }
                                ?>
                                <th class="heading_items_col<?php echo $hide_col_class; ?>" data-listid="<?php echo $list_details['list_id']; ?>" data-colid="<?php echo $col['id']; ?>">
                                    <div class="add-data-title-r">
                                        <a class="icon-more-h move_sub_col <?php echo $hide_rearrange_class; ?>" id="dropdownMenu0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="visibility: hidden;"></a>
                                        <a class="remove_sub_col remove_col custom_cursor icon-cross-out" data-colid="<?php echo $col['id']; ?>" data-listid="<?php echo $list_details['list_id']; ?>" style="visibility: hidden;"></a>

                                    </div>
                                    <div class="add-data-title" data-colid="<?php echo $col['id']; ?>" data-listid="<?php echo $list_details['list_id']; ?>" data-toggle="tooltip" data-placement="bottom" title="" data-type="<?php echo $col['type']; ?>" data-original-title="<?php echo $col['column_name']; ?>">
                                        <span class="column_name_class" id="col_name_<?php echo $col['id']; ?>"><?php echo $col['column_name']; ?></span>

                                    </div>
                                    <a class="icon-more-o icon_listing_table"></a>
                                    <div class="div_option_wrap">
                                        <ul class="ul_table_option" data-listid="<?php echo $list_details['list_id']; ?>">
                                            <li><div class="custom_radio_class"><input class="radio-col-type" type="radio" id="text_<?php echo $col['id']; ?>" name="radio-group-<?php echo $col['id']; ?>" value="text" data-col_id="<?php echo $col['id']; ?>"  <?php if($col['type'] == 'text'){ echo ' checked'; } ?>><label class="col_type_lbl" for="text_<?php echo $col['id']; ?>">Text</label></div></li>
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
                            </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($task_data as $task_key => $task_val):
                                ?>
                                <tr class="">
                                    <td class="icon-more-holder" data-order="<?php echo $task_val[0]['order']; ?>" data-listid="<?php echo $list_details['list_id']; ?>" data-taskname="<?php echo $task_val[0]['TaskName']; ?>">
                                        <span class="icon-more <?php echo $hide_rearrange_class; ?>"></span>
                                        <a href="javascript:void(0)" class="icon-cross-out delete_task custom_cursor" data-id="<?php echo $task_val[0]['TaskId']; ?>" data-listid="<?php echo $list_details['list_id']; ?>"></a>
                                    </td>

                                    <?php
                                    foreach ($task_val as $t_key => $t_val):
                                    ?>
                                    <td class="list-table-view">
                                        
                                        <?php
                                        $print_title = strip_tags(htmlspecialchars_decode(htmlspecialchars_decode($t_val['TaskName'])));

                                        $t_val['TaskName'] = html_entity_decode($t_val['TaskName']);
//                                                        $reg_exUrl = "/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
//                                                        $regex_email = '/^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i';
                                        $regex_email = '/([a-zA-Z0-9_\-\.]*@\\S+\\.\\w+)/';
                                        $reg_exUrl = "#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#";
                                        $reg_exUrl2 = "^([a-zA-Z0-9]+(\.[a-zA-Z0-9]{2,3}+)+.*)$^";
                                        if($t_val['type'] == 'text' || $t_val['type'] == 'memo'){
                                            if (preg_match($regex_email, trim($t_val['TaskName']), $eml)) {
                                                $print_srt_name = preg_replace($regex_email, "<a class='mail_url' href='mailto:" . $eml[0] . "'>" . $eml[0] . "</a>", trim($t_val['TaskName']));
                                            }elseif (preg_match($reg_exUrl, $t_val['TaskName'], $url)) {
                                                if(empty($url) && empty($url[0])){
                                                    if (preg_match($reg_exUrl, $t_val['TaskName'], $url)) {
                                                        $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href=" . $url[0] . ">" . $url[0] . "</a>", trim($t_val['TaskName']));
                                                    }
                                                }else{
                                                    $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href='http://" . $url[0] . "'>" . $url[0] . "</a>", trim($t_val['TaskName']));
                                                }
                                            } else {
                                                $print_srt_name = trim($t_val['TaskName']);
                                            }
                                        }
                                        ?>
                                        
                                        
                                        
                                        <?php
                                        $div_css = '';
                                        if($t_val['type'] == 'memo'){
                                            $div_css .= 'padding: 11px 20px;';
                                            if($t_val['height'] == 1){
                                                $div_css .= 'height: 50px;';
                                            }elseif($t_val['height'] == 2){
                                                $div_css .= 'height: 80px;';
                                            }elseif($t_val['height'] == 3){
                                                $div_css .= 'height: 100px;';
                                            }else{
                                                $height = 110 + (30 * ($t_val['height'] - 3)) . 'px';
                                                $div_css .= 'height: ' . $height;
                                            }
                                        }
                                        $print_title = strip_tags(htmlspecialchars_decode(htmlspecialchars_decode($t_val['TaskName'])));
                                        ?>
                                        
                                        <div class="add-data-div edit_task" data-id="<?php echo $t_val['TaskId']; ?>" data-task="<?php echo $t_val['TaskName']; ?>" data-listid="<?php echo $list_details['list_id']; ?>" data-toggle="tooltip" data-placement="bottom" data-type="<?php echo $t_val['type']; ?>" title="<?php echo $print_title; ?>" style="padding: 11px 20px;<?php echo $div_css; ?>" data-original-title="<?php echo $t_val['TaskName']; ?>">
                                            <?php
                                            $span_css = '';
                                            if($t_val['type'] == 'memo'){
                                                $span_css .= 'line-height: 30px;';
                                            }
                                            ?>
                                            <span id="span_task_<?php echo $t_val['TaskId']; ?>" class="task_name_span" style="<?php echo $span_css; ?>">
                                                <?php
                                                $print_srt_name = $t_val['TaskName'];
                                                $regex_email = '/([a-zA-Z0-9_\-\.]*@\\S+\\.\\w+)/';
                                                $reg_exUrl = "#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#";
                                                $reg_exUrl2 = "^([a-zA-Z0-9]+(\.[a-zA-Z0-9]{2,3}+)+.*)$^";
                                                $task_item = $t_val['TaskName'];
                                                if($t_val['type'] == 'text' || $t_val['type'] == 'memo'){
                                                    if (preg_match($regex_email, trim($t_val['TaskName']), $eml)) {
                                                        $print_srt_name = preg_replace($regex_email, "<a class='mail_url' href='mailto:" . $eml[0] . "'>" . $eml[0] . "</a>", trim($t_val['TaskName']));
                                                    }elseif (preg_match($reg_exUrl, $t_val['TaskName'], $url)) {
                                                        if(empty($url) || empty($url[0])){
                                                            if (preg_match($reg_exUrl, $t_val['TaskName'], $url)) {
                                                                $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href=" . $url[0] . ">" . $url[0] . "</a>", trim($t_val['TaskName']));
                                                            }
                                                        }else{
                                                            $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href='" . $url[0] . "'>" . $url[0] . "</a>", trim($t_val['TaskName']));
                                                        }
                                                    }elseif (preg_match($reg_exUrl2, $t_val['TaskName'], $url)) {
                                                        $match_url=substr($url[0], 0, strrpos($url[0], ' '));
                                                        if($match_url == '' && $url[0] != ''){
                                                            $match_url = $url[0];
                                                        }
                                                        $task_item = str_replace($match_url, '|url|', html_entity_decode($task_item));
                                                        $anchor = "<a class='link_clickable' href='http://" . $match_url . "'>" . $match_url . '</a>';
                                                        $print_srt_name = str_replace('|url|', $anchor, $task_item);
                                                    } else {
                                                        $print_srt_name = html_entity_decode($t_val['TaskName']);
                                                    }
                                                }
                                                if($t_val['type'] == 'currency'){
                                                    if($print_srt_name != ''){
                                                        echo '$ ';
                                                    }
                                                    if($t_val['TaskName'] != ''){
                                                        if(filter_var($t_val['TaskName'], FILTER_VALIDATE_INT)){
                                                            $print_srt_name = $t_val['TaskName'] . '.00';
                                                        }else{
                                                            $print_srt_name = number_format((float)$t_val['TaskName'], 2, '.', '');
                                                        }
                                                    }else{
                                                        $print_srt_name = '';
                                                    }
                                                }
                                                if($t_val['type'] == 'email'){
                                                    $print_srt_name = '<a class="mail_url" href="mailto:' . trim($t_val['TaskName']) . '">' . trim($t_val['TaskName']) . '</a>';
                                                }
                                                if($t_val['type'] == 'text'){
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
                                    <td class="list-table-view-attend hidden_nodrag">
                                        <div class="add-date-div check_date" data-id="13702" data-listid="<?php echo $list_details['list_id']; ?>" data-toggle="tooltip" data-placement="top" title="" data-original-title="">
                                            <span id="span_time_13702" class="time_name_span">&nbsp;</span>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                endforeach;
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>