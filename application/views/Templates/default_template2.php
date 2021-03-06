<?php
$data['Apikey'] = '4C4BB187-5913-46F3-AFAA-45E8AAA81B96';
$post_data = json_encode($data);
$header = array('Content-Type: application/json');
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, API_URL . "account/EncryptApiKey");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$server_output = curl_exec($ch);
$response = (array) json_decode($server_output);
$api_key_encrypted = '';
if(isset($response['success']) && $response['success'] == 1){
    $api_key_encrypted = $response['data']->ApiKey;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <meta property="og:site_name" content="Nexup"/>
        <?php if($_SERVER['REQUEST_URI'] == '/lists' || $_SERVER['REQUEST_URI'] == '/'){ ?>
        <meta property="og:title" content="Nexup"/>
        <?php
        } else{
            if(isset($list_name)){
        ?>
        <meta property="og:title" content="<?php echo $list_name; ?>"/>
        <?php
            }else{
        ?>
        <meta property="og:title" content="Nexup"/>
        <?php
            }
        }
        ?>
        <meta property="og:type" content="website" />
        <?php
        $list_desc = 'Nexup is a list sharing platform.';
        if($_SERVER['REQUEST_URI'] != '/lists' || $_SERVER['REQUEST_URI'] != '/'){
            if (isset($tasks) && !empty($tasks)) {
                $max_loop = count($tasks);
                if(count($tasks) > 5){
                    $max_loop = 4;
                }
                $display_task = '';
                for($i = 1; $i < $max_loop; $i++){
                    if(isset($type_id) && $type_id == 3){
                        $display_task .= $i . ' - ';
                    }
                    if(isset($tasks[$i][0]['TaskName'])){
                        if($tasks[$i][0]['TaskName'] != ''){
                            $display_task .= $tasks[$i][0]['TaskName'];
                        }else{
                            $display_task .= 'null';
                        }
                    }
                    if(isset($tasks[$i][1]['TaskName']) && count($tasks[$i] > 1)){
                        if($tasks[$i][1]['TaskName'] != ''){
                            $display_task .= ', ' . $tasks[$i][1]['TaskName'];
                        }else{
                            $display_task .= ', null';
                        }
                    }
                    if(isset($tasks[$i][2]['TaskName']) && count($tasks[$i] > 2)){
                        if($tasks[$i][2]['TaskName'] != ''){
                            $display_task .= ', ' . $tasks[$i][2]['TaskName'];
                        }else{
                            $display_task .= ', null';
                        }
                    }
                    $display_task .= '\n';
                }
                
                
                if(isset($type_id) && $type_id == 11){
                    $display_task = $total_yes . ' - YES, ';
                    $display_task .= $total_maybe . ' - MAYBE, ';
                    $display_task .= $total_no . ' - NO, ';
                    $display_task .= $total_blank . ' - UNRESPONDED';
                }elseif (isset($type_id) && $type_id == 2) {
                    $order_arrs = array();
                        $cur_order = 0;
                        foreach ($tasks as $tid => $tdata):
                            if($tdata[0]['order'] > $cur_order){
                                $cur_order = $tdata[0]['order'];
                                array_push($order_arrs, $cur_order);
                            }
                        endforeach;

                        $first_key = 0;
                        if (!empty($tasks)) {
                            $first_key = min($order_arrs);
                        }
                    if (!empty($tasks)) {
                        $display_data = '';
                        if (!empty($last_log)) {
                            $last_log_arr = explode(',', $last_log);
                            $data_print = $last_log_arr[0];
                            foreach ($tasks as $tid => $tdt):
                                if ($tdt[0]['TaskId'] == $data_print) {
                                    $display_data = $tdt[0]['TaskName'];
                                }
                            endforeach;
                            if ($multi_col == 1) {
                                $row_id = 1;
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
                                if (isset($tasks[$first_key])) {
                                    $t_cnt = 1;
                                    foreach ($tasks[$first_key] as $id_t => $t):
                                        if ($t_cnt > 3) {
                                            break;
                                        }
                                        if ($id_t > 0) {
                                            $display_data .=  ', ' . trim($t['TaskName']);
                                            $t_cnt++;
                                        }
                                    endforeach;
                                }
                            }
                        } else {
                            $display_data = $tasks[$first_key][0]['TaskName'];
                        }
                        $display_task = $display_data;
                    }
                }elseif (isset($type_id) && $type_id == 8) {
                    if (!empty($tasks)) {
                        $display_data = '';
                        $order_arrs = array();
                        $cur_order = 0;
                        foreach ($tasks as $tid => $tdata):
                            if($tdata[0]['order'] > $cur_order){
                                $cur_order = $tdata[0]['order'];
                                array_push($order_arrs, $cur_order);
                            }
                        endforeach;

                        $first_key = 0;
                        if (!empty($tasks)) {
                            $first_key = min($order_arrs);
                        }
                        
                        if (!empty($last_log)) {
                            $last_log_arr = explode(',', $last_log);
                            $data_print = $last_log_arr[0];
                            foreach ($tasks as $tid => $tdt):
                                if ($tdt[0]['TaskId'] == $data_print) {
                                    $display_data = trim($tdt[0]['TaskName']);
                                }
                            endforeach;
                        } else {
                            $display_data = trim($tasks[$first_key][0]['TaskName']);
                        }
                    }
                    $display_task = $display_data;
                }elseif(isset($type_id) && $type_id == 5) {
                    $first_column_id = $this->TasksModel->find_first_column($list_id);
                    $completed = $this->TasksModel->get_completed_items($list_id, $first_column_id, 1);
                    $in_complete = $this->TasksModel->get_completed_items($list_id, $first_column_id, 0);
                    
                    $completed = array_column($completed, 'value');
                    $in_complete = array_column($in_complete, 'value');
                    
                    $display_task = 'Incomplete: ' . implode(', ', $in_complete) . ', ...';
                    $display_task .= '  Completed: ' . implode(', ', $completed) . ', ...';
                }
                
                $list_desc = $display_task;
            }
        }
        ?>
        <meta property="og:description" content="<?php echo $list_desc; ?>"/>
        <?php $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>
        <meta property="og:url" content="<?php echo $actual_link; ?>"/>
        <title><?php echo $title; ?></title>

        <link href="https://fonts.googleapis.com/css?family=Poppins:300,700|Roboto:300,400" rel="stylesheet">
        <link href="<?php echo base_url() . 'assets/css/bootstrap.min.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/bootstrap-datetimepicker.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/icomoon.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/bootstrap-select.css'; ?>" rel="stylesheet" />
        <!--<link href="<?php echo base_url() . 'assets/css/font-awesome.min.css'; ?>" rel="stylesheet" />-->
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link href="<?php echo base_url() . 'assets/css/jquery-ui.min.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/token-input.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/token-input-facebook.css'; ?>" rel="stylesheet" />
        <link href="<?php // echo base_url() . 'assets/css/jquery.mCustomScrollbar.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/style.css?v=' . time(); ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/responsive.css?v=' . time(); ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/tabledragdrop.css'; ?>" rel="stylesheet" />
        <link href="<?php // echo base_url() . 'assets/css/bootstrap-responsive-tabs.css'; ?>" rel="stylesheet" />
        <link rel="icon" href="<?php echo base_url(); ?>assets/img/favicon.ico" type="image/ico">
    </head>
    <body>
        <div class="wrapper">
            <header id="header" class="header">
                <div class="header-l">
                    <!--<div class="logo"><a href="<?php echo base_url() . 'lists'; ?>"><img src="<?php echo base_url() . 'assets/img/logo_second.svg'; ?>" alt="" /></a></div>-->
                    <div class="logo">
                        <div class="logo_main_contcept">
                            <a href="<?php echo base_url() . 'lists'; ?>">
                                <img class="ft"  src="<?php echo base_url() . 'assets/img/logo_first.svg'; ?>"/>
                                <div class="div_two_logos">
                                    <img class="top" src="<?php echo base_url() . 'assets/img/logo-2.jpg'; ?>"/>
                                    <img class="bottom" src="<?php echo base_url() . 'assets/img/lg.jpg'; ?>"/>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div id="header-r-accordian">
                    <div></div>
                    <div class="header-r">
                        <div class="search">
                            <?php
                            $serach_box_text = 'Search';
                            if (!isset($_SESSION['id'])) {
                                $serach_box_text = 'Search Public list';
                            }
                            ?>
                            <input type="text" name="search_list" id="search_list" placeholder="<?php echo $serach_box_text; ?>" value="<?php if (isset($find_param)) {
                                echo $find_param;
                            } ?>"/>
                            <?php
                            if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != 1) {
                                $search_title = 'Please login for full search';
                            } else {
                                $search_title = 'Search';
                            }
                            ?>
                            <button class="icon-search" type="submit"  data-toggle="tooltip" data-placement="bottom" title="<?php echo $search_title; ?>"> </button>
                            <div class="loader_div">
                                <a class="loader_inline" id="loader_search" style="display:none;"><img src="<?php echo base_url() . 'assets/img/loader.gif' ?>"></a>
                            </div>
                        </div>
                        <div class="nav-view">
                            <a class="icon-nine-circles-button custom_cursor" href="<?php echo base_url() . 'lists'; ?>" data-toggle="tooltip" data-placement="bottom" title="Directory"> </a>
                        </div>
                                <?php if (isset($_SESSION['unauth_visit']) && !empty($_SESSION['unauth_visit']) || isset($_SESSION['auth_visit']) && !empty($_SESSION['auth_visit'])) { ?>
                            <div class="h-nav dropdown h-nav_history">
                                <a class="icon-history custom_cursor" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <img src="/assets/img/history.png">
                                </a>
                                <ul class="dropdown-menu" id="history_dd" aria-labelledby="dropdownMenu2">

                                    <?php
                                    $visited_lists_show = array();
                                    if (isset($_SESSION['auth_visit']) && $_SESSION['auth_visit'] != null) {
                                        $auth_ssn_ids = $_SESSION['auth_visit']['list_id'];
                                        krsort($auth_ssn_ids);
                                        $auth_ssn_slugs = $_SESSION['auth_visit']['list_slug'];
                                        krsort($auth_ssn_slugs);
                                        $auth_ssn_names = $_SESSION['auth_visit']['list_name'];
                                        krsort($auth_ssn_names);

                                        foreach ($auth_ssn_ids as $key => $val):
                                            if (!empty($val)) {
                                                if (isset($_SESSION['auth_visit']['deleted'][$key])) {
                                                    echo "<li class='history_options'><a class='removed_list'>" . $auth_ssn_names[$key] . "</a></li>";
                                                } else {
                                                    echo "<li class='history_options' value='" . base_url() . "list/" . $auth_ssn_slugs[$key] . "' data-slug='" . $auth_ssn_slugs[$key] . "'><a href='" . base_url() . "list/" . $auth_ssn_slugs[$key] . "'>" . $auth_ssn_names[$key] . "</a></li>";
                                                }
                                            }
                                        endforeach;
                                    } else {
                                        $unauth_ssn_ids = $_SESSION['unauth_visit']['list_id'];
                                        krsort($unauth_ssn_ids);
                                        $unauth_ssn_slugs = $_SESSION['unauth_visit']['list_slug'];
                                        krsort($unauth_ssn_slugs);
                                        $unauth_ssn_names = $_SESSION['unauth_visit']['list_name'];
                                        krsort($unauth_ssn_names);

                                        foreach ($unauth_ssn_ids as $key => $val):
                                            if (!empty($val)) {
                                                if (isset($_SESSION['unauth_visit']['deleted'][$key])) {
                                                    echo "<li class='history_options'><a class='removed_list'>" . $unauth_ssn_names[$key] . "</a></li>";
                                                } else {
                                                    echo "<li class='history_options' value='" . base_url() . "list/" . $unauth_ssn_slugs[$key] . "'><a href='" . base_url() . "list/" . $unauth_ssn_slugs[$key] . "'>" . $unauth_ssn_names[$key] . "</a></li>";
                                                }
                                            }
                                        endforeach;
                                    }
                                    ?>
                                </ul>
                            </div>
    <?php
}
?>

                                <?php if ($this->session->userdata('logged_in')) { ?>
                            <div class="h-nav dropdown h-nav_with_login">
                                <!--<a class="icon-more custom_cursor" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> </a>-->
                                <a id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" class="avatar_menu custom_cursor"><img src="<?php
                                if (@getimagesize($_SESSION['image'])) {
                                    echo $_SESSION['image'];
                                } else {
                                    echo 'https://demo.inflo.io/Images/profile_thum.jpg';
                                }
                                ?>" alt="Avatar" id="imgpreview"></a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <li><a><?php echo $this->session->userdata('first_name') . ' ' . $this->session->userdata('last_name'); ?></a></li>
                                    <li><a href="<?php echo base_url() . 'profile'; ?>" data-toggle="tooltip" data-placement="bottom" title="Profile">Profile</a></li>
                                    <li><a href="<?php echo base_url() . 'logout'; ?>" data-toggle="tooltip" data-placement="bottom" title="Logout">Logout</a></li>
                                </ul>
                            </div>
                        <?php } ?>

<?php if (!$this->session->userdata('logged_in')) { ?>

                            <div class="h-nav nav-inflo-login">
                                <div class="social-login"><a class="inflologinlink btn btn-inflo"><img class="inflo-icon" src="<?php echo base_url(); ?>/assets/img/inflo-alpha.png" alt=""> Login</a></div>
                            </div>
            <?php } ?>
                    </div>
                </div>
            </header>
            <?php
            echo $body;
            $task_list_id = 0;
            if (isset($list_id)) {
                $task_list_id = $list_id;
            }
            ?>
            <div class="push"></div>
        </div>
        <input type="hidden" id="hndautoid" value="<?php echo $api_key_encrypted; ?>"/>
        <input type="hidden" id="hdnuserid"/>
        <input type="hidden" id="hdnaccesstoken"/>
        <div id="copy_msg_summary"></div>
        <input type="hidden" id="hidden_share_click" value="0">
        <footer id="footer">
            <p>© <?php echo date('Y'); ?> Copyright - All Rights Reserved</p>
        </footer>


        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery.min.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-ui.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/bootstrap.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/moment-with-locales.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/bootstrap-datetimepicker.min.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/bootstrap-select.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/validate.min.js'; ?>"></script>
        <!--<script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-ui.min.js'; ?>"></script>-->
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-ui-touch-punch.min.js'; ?>"></script>
        <!--<script type="text/javascript" src="https://developer.inflo.io/Scripts/InfloAPIScript.js"></script>-->
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/InfloAPIScript.js?v=' . time(); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery.tokeninput.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/clipboard.min.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery.mCustomScrollbar.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/tabledragdrop.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/import_email.js?v=' . time(); ?>"></script>


        <noscript><div class="no-script-enabled">It seems you have not enabled Java script! Please enable java script to perform any operation on NEXUP.</div></noscript>
        <script>
            
            function set_nexup_box_data(){
                    var screen_size = $( window ).width();
                    $('.whoisnext-div').find('.button-outer').find('.nexup-sub-group-two').find('span').each(function (e){
                        var my_len = $(this).text().length;
                        if(screen_size <= 414){
                            var my_sub_str = $(this).text();
                            if(my_len > 68){
                                my_sub_str = $(this).text().substring(0,68) + '...';
                            }
                            $(this).text(my_sub_str);
                        }else if(screen_size > 414 && screen_size <= 447){
                            var my_sub_str = $(this).text();
                            if(my_len > 88){
                                my_sub_str = $(this).text().substring(0,88) + '...';
                            }
                            $(this).text(my_sub_str);
                        }else if(screen_size > 447 && screen_size <= 499){
                            var my_sub_str = $(this).text();
                            if(my_len > 94){
                                my_sub_str = $(this).text().substring(0,94) + '...';
                            }
                            $(this).text(my_sub_str);
                        }else if(screen_size > 499 && screen_size <= 709){
                            var my_sub_str = $(this).text();
                            if(my_len > 106){
                                my_sub_str = $(this).text().substring(0,106) + '...';
                            }
                            $(this).text(my_sub_str);
                        }else if(screen_size > 709 && screen_size <= 767){
                            var my_sub_str = $(this).text();
                            if(my_len > 110){
                                my_sub_str = $(this).text().substring(0,110) + '...';
                            }
                            $(this).text(my_sub_str);
                        }else if(screen_size > 767 && screen_size <= 1024){
                            var my_sub_str = $(this).text();
                            if(my_len > 73){
                                my_sub_str = $(this).text().substring(0,73) + '...';
                            }
                            $(this).text(my_sub_str);
                        } else if(screen_size > 1024 && screen_size <= 1199){
                            var my_sub_str = $(this).text();
                            if(my_len > 118){
                                my_sub_str = $(this).text().substring(0,118) + '...';
                            }
                            $(this).text(my_sub_str);
                        } else if(screen_size > 1199){
                            var my_sub_str = $(this).text();
                            if(my_len > 125){
                                my_sub_str = $(this).text().substring(0,125) + '...';
                            }
                            $(this).text(my_sub_str);
                        }
                    });
                }
            $(document).ready(function () {
                
                if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                    $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').tooltip('destroy');
                }
                
                var list_id = $('#content').find('#tab_ul').find('li.custom_tab:eq(0)').find('a.custom_tab_anchor').attr('data-listid');
                    $.ajax({
                        url:'/return_list_tab',
                        type: 'POST',
                        context: this,
                        data:{list_id: list_id},
                        success: function (res) {
                            if(res != 'fail' || res != '' || res != 'unauthorised'){
                                $(document).find('#tabs-' + list_id).html(res);
                                if($('#tabs-' + list_id).find('.edit_list_task_sub').attr('data-id') > 0){
                                    if($('#tabs-' + list_id).find('#listsub_Lock_lnk').length == 1){
                                        if($('#tabs-' + list_id).find('#listsub_Lock_lnk').hasClass('lock_hide')){
                                            $('#tabs-' + list_id).find('#config_icons').append($('#tabs-' + list_id).find('#listsub_Lock_lnk.lock_hide'));
                                        }
                                    }
                                    if($('#tabs-' + list_id).find('#listSub_Unlock_lnk').length == 1){
                                        if($('#tabs-' + list_id).find('#listSub_Unlock_lnk').hasClass('lock_hide')){
                                            $('#tabs-' + list_id).find('#config_icons').append($('#tabs-' + list_id).find('#listSub_Unlock_lnk.lock_hide'));
                                        }
                                    }
                                    if($('#tabs-' + list_id).find('.reset_sub_list').length == 1){
                                        if($('#tabs-' + list_id).find('.reset_sub_list').hasClass('login_prompt')){
                                            $('#tabs-' + list_id).find('#config_icons').append($('#tabs-' + list_id).find('.reset_sub_list.login_prompt'));
                                        }
                                    }
                                    if($('#tabs-' + list_id).find('#delete_sub_list_builder').length == 1){
                                        if($('#tabs-' + list_id).find('#delete_sub_list_builder').hasClass('disabled_btn')){
                                            $('#tabs-' + list_id).find('#config_icons').append($('#tabs-' + list_id).find('#delete_sub_list_builder.disabled_btn'));
                                        }
                                    }
                                    if($('#tabs-' + list_id).find('.sub_listConfig_lnk').attr('data-allowappendlocked') == 1){
                                        if($('#tabs-' + list_id).find('.ui-icon-triangle-1-e').length == 1){
                                            $('.ui-icon-triangle-1-e').trigger('click');
                                        }
                                    }
                                }
                                $('#tabs-' + list_id).find('.task_sub_name[data-type="datetime"]').datetimepicker({
                                    format: 'MM/DD/YYYY HH:mm',
                                    widgetPositioning: {
                                        horizontal: 'auto',
                                        vertical: 'bottom'
                                    }
                                });
                                $('#tabs-' + list_id).find('.task_sub_name[data-type="date"]').datetimepicker({
                                    format: 'MM/DD/YYYY',
                                    widgetPositioning: {
                                        horizontal: 'auto',
                                        vertical: 'bottom'
                                    }
                                });
                                $('#tabs-' + list_id).find('.task_sub_name[data-type="time"]').datetimepicker({
                                    format: 'HH:mm',
                                    widgetPositioning: {
                                        horizontal: 'auto',
                                        vertical: 'bottom'
                                    }
                                });
                                
                            }
                        }
                    });
                
                
                if($('#config_lcoked').attr('data-locked') == 2){
                    $('.edit_task').each(function (){
                        if($(this).hasClass('no_hover_table')){
                            $(this).parent().parent().find('.icon-more-holder').find('.delete_task').css('visibility', 'hidden');
                            $(this).parent().parent().find('.icon-more-holder').find('.icon-more').css('visibility', 'hidden');
                        }
                    });
//                    $('.icon-more-holder').find('.delete_task').css('display', 'none');
//                    $('.icon-more-holder').find('.icon-more').css('display', 'none');
                    $('.move_col').css('display', 'none');
                    $('.remove_col').css('display', 'none');
                }

                $('.added_div .add-data-head-r span.ui-accordion-header-icon.ui-icon.ui-icon-triangle-1-e').css('display', 'none');
                if ($('#listUnlock_lnk').length == 1) {
                    $('.add-data-head-r').addClass('hide_add');
                }
//                GetEncryptedAPIKey('974CB208-48DD-41D4-99C1-53599EB107DA');
                
                $(document).on('click touchstart', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                    $('.tooltip').remove();
                });
                var isTouchDevice = 'ontouchstart' in document.documentElement;
                if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                    $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                        event.preventDefault();
                    }).tooltip({delay: { "hide": 100 }});
                }

                $(document).on('click touch', '#customize_btn', function () {
                    var url_current = $('#import_contacts').text();
                    var url_arr = url_current.split('/');
                    var url_arr_len = url_arr.length;
                    var url_slug = url_arr[url_arr_len - 1];
                    var import_contacts_cpy = '<span class="edit_url_span"><a id="import_contacts_copy"><?php echo base_url(); ?>list/</a>';
                    var custom_url_box = '<input type="text" name="custom_url" id="custom_url" class="custom-url-txt" value="' + url_slug + '" onCopy="return false" onDrag="return false" onDrop="return false" onPaste="return false" autocomplete=off></span>';
                    $('.edit_url_span').remove();
                    $('#import_contacts').after(import_contacts_cpy + custom_url_box);
                    $('#import_contacts').hide();
                    $('#custom_url').focus();
//                    $('#custom_url').select();
                    $('#custom_url').get(0).setSelectionRange(0,9999);
//                    $(this).hide();
                    $('#copy_btn').addClass('block-copy-btn');
                });
                
                if ($('.plus-category').attr('data-access') == 0) {
                    $('.icon-more-holder .icon-more').hide();
                    $('.icon-more-holder .delete_task').hide();
                    $('.icon-more-holder .complete_task').hide();
                    $('.icon-more-holder .present_task').hide();
                    $('.add-data-title-r .move_col').hide();
                    $('.add-data-title-r .remove_col').hide();
                }
                var screen_size = $(window).width();
                if (screen_size < 521) {
                    $("#header-r-accordian").accordion({
                        collapsible: true
                    });
                }
                if ($('.column-4').length > 0) {
                    var list_count = $('ul.tasks_lists_display').length;
                    var div_width = (list_count * 400);
                    $('.column-4').width(div_width);
                }
                $('div#TaskListDiv').css('width', $('#test_table').width());
                var total_cell = $('#test_table tbody tr:first-child td').length;


                $(document).on('click touchstart', '#add_data_desc', function () {
                    if ($('.plus-category').attr('data-access') == 1) {
                        if ($('.list_desc_div').hasClass('hiden_desc')) {
                            var list_id = $(this).attr('data-id');
                            var get_type = 'get';
                            $.ajax({
                                url: '<?php echo base_url() . 'get_list_desc' ?>',
                                type: 'POST',
                                data: {list_id: list_id, get_type: get_type},
                                success: function (res) {
                                    var resp_data = $.parseHTML(decodeURI(res));
                                    var resp = res;
                                    if (resp_data != null) {
                                        resp = resp_data[0]['data'];
                                    } else {
                                        if (resp == '') {
                                            resp = 'Click here to add description.';
                                        }
                                    }
                                    document.getElementById("list_desc_text").innerText = resp;
                                    $('#list_desc_text').html(res);
                                }
                            });

                            $('#list_desc_span').hide();
                            $('#list_desc_text').show();
                            $('.list_desc_div').removeClass('hiden_desc');
                            $('#list_desc_text').css('display', 'block');
                        } else {
                            $('.list_desc_div').addClass('hiden_desc');
                        }
                    }
                });
                
                $(document).on('click touchstart', '#add_sub_data_desc', function () {
                    var list_id = $(this).attr('data-id');
                    var get_type = 'get';
                    if ($('#tabs-' + list_id).find('.plus-category').attr('data-access') == 1) {
                        if ($('#tabs-' + list_id).find('.sub_list_desc_div').hasClass('hiden_desc')) {
                            $.ajax({
                                url: '<?php echo base_url() . 'get_list_desc' ?>',
                                type: 'POST',
                                data: {list_id: list_id, get_type: get_type},
                                success: function (res) {
                                    var resp_data = $.parseHTML(decodeURI(res));
                                    var resp = res;
                                    if (resp_data != null) {
                                        resp = resp_data[0]['data'];
                                    }
                                    if (res == '') {
                                        res = 'Click here to add description.';
                                    }
                                    $('#tabs-' + list_id).find('.sub_list_desc_div').find("#sublist_desc_text").html(resp);
                                    $('#tabs-' + list_id).find('#sublist_desc_text').html(res);
                                    $('#tabs-' + list_id).find('#sub_list_desc_div').removeClass('hiden_desc');
                                }
                            });

                            $('#tabs-' + list_id).find('.sub_list_desc_div').find('#sub_list_desc_span').hide();
                            $('#tabs-' + list_id).find('.sub_list_desc_div').find('#sublist_desc_text').show();
                            $('#tabs-' + list_id).find('.sub_list_desc_div').removeClass('hiden_desc');
                            $('#tabs-' + list_id).find('.sub_list_desc_div').find('#sublist_desc_text').css('display', 'block');
                        } else {
                            $('#tabs-' + list_id).find('.sub_list_desc_div').addClass('hiden_desc');
                        }
                    }
                });
                
                $(document).on('click', '#reset_list', function () {
                    if($(this).hasClass('login_prompt')){
                        var cnf = confirm('This function requires authentication, please login and try again.');
                        if(cnf){
                            $('.inflologinlink').trigger('click');
                        }
                        return false;
                    }
                    var list_id = $(this).attr('data-listid');
                    $.ajax({
                        url: '<?php echo base_url() . 'reset_attendance_list' ?>',
                        type: 'POST',
                        data: {list_id: list_id},
                        success: function (res) {
                            if (res == 'empty') {
                                alert('something went wrong. Please try again!');
                            } else if (res == 'wrong') {
                                alert('You can not resert this list!');
                            } else if (res == 'not allowed') {
                                alert('You are not allowed to resert this list!');
                            } else if (res == 'success') {
                                if ($('.present_lbl').hasClass('green_label')) {
                                    $('.present_lbl').removeClass('green_label');
                                }
                                if ($('.present_lbl').hasClass('yellow_label')) {
                                    $('.present_lbl').removeClass('yellow_label');
                                }
                                if ($('.present_lbl').hasClass('red_label')) {
                                    $('.present_lbl').removeClass('red_label');
                                }

                                $('#tabs-' + list_id).find('#yes_cnt').text('0');
                                $('#tabs-' + list_id).find('#maybe_cnt').text('0');
                                $('#tabs-' + list_id).find('#no_cnt').text('0');
                                $('#tabs-' + list_id).find('#blank_cnt').text($('#tabs-' + list_id).find('.present_task').length);

                                $('.present_task').attr('checked', false);
                                $('.list-table-view-attend .check_date .time_name_span').html('&nbsp;');
                                $('.list-table-view-attend .edit_comment .comment_name_span').html('&nbsp;');
                            }

                        }
                    });
                });

                $(document).on('click touchstart', '#list_desc_text', function () {
                    $('#list_desc').text('');
                    var list_id = $(this).attr('data-listid');
                    var get_type = 'edit';
                    $.ajax({
                        url: '<?php echo base_url() . 'get_list_desc' ?>',
                        type: 'POST',
                        context: this,
                        data: {list_id: list_id, get_type: get_type},
                        success: function (res) {

                            var resp_data = $.parseHTML(decodeURI(res));
                            var resp = res;
                            if (resp_data != null) {
                                resp = resp_data[0]['data'];
                            }
                            $('#list_desc').val(resp);
                            $(this).hide();
                            $('#list_desc_span').show();
                            $('#list_desc').focus();
                        }
                    });
                });
                
                $(document).on('click touchstart', '#sublist_desc_text', function () {
                    $('#tabs-' + list_id).find('#list_sub_desc').text('');
                    var list_id = $(this).attr('data-listid');
                    var get_type = 'edit';
                    $.ajax({
                        url: '<?php echo base_url() . 'get_list_desc' ?>',
                        type: 'POST',
                        context: this,
                        data: {list_id: list_id, get_type: get_type},
                        success: function (res) {

                            var resp_data = $.parseHTML(decodeURI(res));
                            var resp = res;
                            if (resp_data != null) {
                                resp = resp_data[0]['data'];
                            }


                            $('#tabs-' + list_id).find('#list_sub_desc').val(resp);
                            $('#tabs-' + list_id).find(this).hide();
                            $('#tabs-' + list_id).find('#sub_list_desc_span').show();
                            $('#tabs-' + list_id).find('#list_sub_desc').focus();
                        }
                    });
                });


                $(document).on('keydown', '#list_desc', function (evt) {
                    var key_code = evt.keyCode;
                    if (key_code == 27) {
                        $('#list_desc_span').hide();
                        $('#list_desc_text').show();
                    }
                });
                $(document).on('keydown', '#list_sub_desc', function (evt) {
                    var list_id = $(this).attr('data-listid');
                    var key_code = evt.keyCode;
                    if (key_code == 27) {
                        $('#tabs-' + list_id).find('#sub_list_desc_span').hide();
                        $('#tabs-' + list_id).find('#sublist_desc_text').show();
                    }
                });

                $(document).on('blur', '#list_desc', function () {
                    $('#list_desc_msg').remove();
                    var loader = '<img id="loader_save_desc" src="/assets/img/loader.gif" style="width: 15px;margin-left: 10px;">';
                    $(this).append(loader);
                    var list_id = $(this).attr('data-listid');
                    var list_desc = $('#list_desc').val();
                    if (list_desc == '') {
                        var error_msg_box = "<div class='alert alert-danger' id='list_desc_msg'>List description can not be left empty!</div>";
                        $('#list_desc_span').append(error_msg_box);
                        $('#loader_save_desc').remove();
                        return false;
                    }
                    $.ajax({
                        url: '<?php echo base_url() . 'update_list_desc' ?>',
                        type: 'POST',
                        context: this,
                        data: {
                            list_id: list_id,
                            list_desc: list_desc.replace(/</g, "&lt;").replace(/>/g, "&gt;")
                        },
                        success: function (res) {
                            if (res != 'fail') {
                                if (res == 'empty') {
                                    var error_msg_box = "<div class='alert alert-danger' id='list_desc_msg'>List description can not be left empty!</div>";
                                    $('#list_desc_span').append(error_msg_box);
                                    $('#loader_save_desc').remove();
                                    return false;
                                }
                                var resp_data = $.parseHTML(decodeURI(res));
                                var resp = res;
                                if (resp_data != null) {
                                    resp = resp_data[0]['data'];
                                }
//                                $('#list_desc_text').text(resp);
                                document.getElementById("list_desc_text").innerHtml = res;
//                                $('#list_desc_text').textContent = resp;
                                $('#list_desc').val(resp);
                            }
                            $('#list_desc_span').hide();
                            $('#list_desc_text').show();
                            $('#loader_save_desc').remove();
                        }
                    });
                });
                
                $(document).on('blur', '#list_sub_desc', function () {
                    $('#list_desc_msg').remove();
                    var list_id = $(this).attr('data-listid');
                    var list_desc = $(this).val();
                    var loader = '<img id="loader_save_desc_' + list_id + '" src="/assets/img/loader.gif" style="width: 15px;margin-left: 10px;">';
                    $(this).append(loader);
                    if (list_desc == '') {
                        var error_msg_box = "<div class='alert alert-danger' id='list_desc_msg'>List description can not be left empty!</div>";
                        $('#tabs-' + list_id).find('#list_desc_span').append(error_msg_box);
                        $('#tabs-' + list_id).find('#loader_save_desc').remove();
                        return false;
                    }
                    $.ajax({
                        url: '<?php echo base_url() . 'update_list_desc' ?>',
                        type: 'POST',
                        context: this,
                        data: {
                            list_id: list_id,
                            list_desc: list_desc.replace(/</g, "&lt;").replace(/>/g, "&gt;")
                        },
                        success: function (res) {
                            if (res != 'fail') {
                                if (res == 'empty') {
                                    var error_msg_box = "<div class='alert alert-danger' id='list_desc_msg'>List description can not be left empty!</div>";
                                    $('#tabs-' + list_id).find('#list_desc_span').append(error_msg_box);
                                    $('#tabs-' + list_id).find('#loader_save_desc').remove();
                                    return false;
                                }
                                var resp_data = $.parseHTML(decodeURI(res));
                                var resp = res;
                                if (resp_data != null) {
                                    resp = resp_data[0]['data'];
                                }
                                $('#tabs-' + list_id).find("#sublist_desc_text").html(resp);
                                $('#tabs-' + list_id).find('#list_sub_desc').val(resp);
                            }
                            $('#tabs-' + list_id).find('#sub_list_desc_span').hide();
                            $('#tabs-' + list_id).find('#sublist_desc_text').show();
                            $('#tabs-' + list_id).find('#loader_save_desc').remove();
                        }
                    });
                });

//                if($('.icon-settings').attr('data-typeid') == 11){
//                setInterval(function () {
//                    var extra_ids = [];
//                    $('.check_date').each(function () {
//                        extra_ids.push($(this).attr('data-id'));
//                    });
//                    var attendance_data_ids = extra_ids.join(',');
//                    var list_id = $('.edit_list_task').attr('data-id');
//
//                    $.ajax({
//                        url: '<?php echo base_url() . 'task/get_check_time' ?>',
//                        type: 'POST',
//                        data: {
//                            'attendance_data_ids': attendance_data_ids,
//                            'list_id': list_id
//                        },
//                        success: function (res) {
//                            if (res != 'fail' && res != 'empty') {
//                                var json_res = JSON.parse(res);
//                                var arr_len = json_res.length;
//
//                                for (var i = 0; i < arr_len; i++) {
//                                    $('#span_time_' + json_res[i]['id']).text(json_res[i]['val']);
//                                    $('#span_time_' + json_res[i]['id']).closest('div.check_date').attr('data-original-title', json_res[i]['val']);
//                                }
//
//                            }
//                        }
//                    });
//
//                }, 30000);
//                }


                if ($('#config_lnk').attr('data-typeid') != 11) {
                    if ($(window).width() >= 1350) {
                        if ($('.task_name').length <= 3) {
                            if (!$('#added_div').hasClass('add-data-left')) {
                                $('#added_div').addClass('add-data-left');
                                $('.my_table').addClass('table_one_page');
                            }
                        } else {
                            if ($('#added_div').hasClass('add-data-left')) {
                                $('#added_div').removeClass('add-data-left');
                                $('.my_table').removeClass('table_one_page');
                            }
                        }
                    } else if ($(window).width() >= 1200 && $(window).width() < 1350) {
                        if ($('.task_name').length <= 2) {
                            if (!$('#added_div').hasClass('add-data-left')) {
                                $('#added_div').addClass('add-data-left');
                                $('.my_table').addClass('table_one_page');
                            }
                        } else {
                            if ($('#added_div').hasClass('add-data-left')) {
                                $('#added_div').removeClass('add-data-left');
                                $('.my_table').removeClass('table_one_page');
                            }
                        }
                    }
                } else {
                    if ($('#added_div').hasClass('add-data-left')) {
                        $('#added_div').removeClass('add-data-left');
                        $('.my_table').removeClass('table_one_page');
                    }
                }
                
                
                var nexup_sun_grp_2_len = $('.button-outer').find('.nexup-sub-group-two').find('span').length;
                if(nexup_sun_grp_2_len == 1){
                    $('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '18px');
                }else if(nexup_sun_grp_2_len == 2){
                    $('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '16px');
                }else{
                    $('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '13px');
                }
                
                set_nexup_box_data();
                
                if($('#config_lnk').attr('data-showprev') == 1){
                    $('.list-table-view').find('.edit_task').find('.task_name_span').each(function (e){
                        if($(this).find('.link_clickable').length > 0){
                        }
                    });
                }
                
            });

            $(document).on('click', 'ul.nav.nav-tabs  a', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });

            //Show/hide search box when search button is clicked
            $('.search button').click(function () {
                if ($('.search').hasClass('search-open'))
                {
                    $('.search').removeClass('search-open');
                }
                else
                {
                    $('.search').addClass('search-open');
                    $('#search_list').focus();
                }
            });


            //Show hide actions on list when button clicked which appears on list while mouse over it.
            $(document).on('click', '.list-body-dropdown > a', function (e) {
                if($(this).attr('id') != 'menu_directory'){
                    $('#edit_list_name').remove();
                    $('.list-body-box a big').show();
                    $('.edit_list_cls').remove();
    //                e.preventDefault();
    //                $(this).parent('.list-body-dropdown').parent('.list-body-box').addClass('action-show');
                    if ($(this).parent('.list-body-dropdown').parent('.list-body-box').hasClass('action-show')) {
                        $(this).parent('.list-body-dropdown').parent('.list-body-box').removeClass('action-show');
                    } else {
                        $('.list-body-box').removeClass('action-show');
                        $(this).parent('.list-body-dropdown').parent('.list-body-box').addClass('action-show');
    //                    $(document).find(this).parents('.list-body-box').attr('class', 'list-body-box custom_cursor action-show');
                    }
                }
            });

            //Display icon for options when mouse is over list
//            $(document).on('mouseover', '.list-body-box', function () {
//                $(this).find("a.icon-more").css('display', 'block');
//            });

            //Hide icon for options when mouse out of list
//            $(document).on('mouseout', '.list-body-box', function () {
//                $(this).find("a.icon-more").css('display', 'none');
//            });
        </script>

        <!-- Autocomplete javascript for search box -->
        <script>

            //Search list auto complere
            
            var page_index = 0;

            jQuery("#search_list").autocomplete({
                source: function (request, response) {
                    var list_name = $("#search_list").val();
                    $('#loader_search').show();
                    $.ajax({
                        url: '<?php echo base_url() . 'searchlist' ?>',
                        type: 'POST',
                        data: {list_name: list_name},
                        success: function (res) {
                            if(res != ''){
                                $('.ui-menu.ui-widget.ui-widget-content.ui-autocomplete.ui-front').scrollTop(0);
                            }
                            page_index = 0;
                            if (JSON.parse(res)[0] != '') {
                                if (JSON.parse(res) == '0') {
                                    response(['Please login to search list']);
                                } else {
                                    response(JSON.parse(res));
                                }
                            } else {
                                response();
                            }

                            $('#loader_search').hide();
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                    if (ui.item.value == 'Please login to search list') {
                        ui.item.value = '';
                    } else {
                        window.location.href = ui.item.url;
                    }
                }
            });

            $.extend($.ui.autocomplete.prototype, {
                _renderMenu: function (ul, items) {
                    //remove scroll event to prevent attaching multiple scroll events to one container element
                    $(ul).unbind("scroll");

                    var self = this;
                    self._scrollMenu(ul, items);
                },
                _scrollMenu: function (ul, items) {
                    var self = this;
                    var maxShow = 10;
                    var results = [];
                    var pages = Math.ceil(items.length / maxShow);
                    results = items.slice(0, maxShow);

                    if (pages > 1) {
                        $(ul).scroll(function () {
                            if (isScrollbarBottom($(ul))) {
                                ++page_index;
                                if (page_index >= pages)
                                    return;
                                if (page_index < pages){
                                    results = items.slice(page_index * maxShow, page_index * maxShow + maxShow);
                                    //append item to ul
                                    $.each(results, function (index, item) {
                                        self._renderItemData(ul, item);
                                    });
                                    //refresh menu
//                                    if (page_index >= pages){
//                                        self.menu.deactivate();
//                                    }
                                    self.menu.refresh();
                                    // size and position menu
                                    ul.show();
                                    self._resizeMenu();
                                    ul.position($.extend({
                                        of: self.element
                                    }, self.options.position));
                                    if (self.options.autoFocus) {
                                        self.menu.next(new $.Event("mouseover"));
                                    }
                                }
                                    
                            }
                        });
                    }

                    $.each(results, function (index, item) {
                        self._renderItemData(ul, item);
                    });
                }
            });

            function isScrollbarBottom(container) {
                var height = container.outerHeight();
                var scrollHeight = container[0].scrollHeight;
                var scrollTop = container.scrollTop();
                if (scrollTop >= scrollHeight - height) {
                    return true;
                }
                return false;
            }
            ;

            //Re-order tasks
            
            $(document).on('mousedown', 'span.icon-more.ui-sortable-handle', function (){
                $('.my_table').addClass('my_scroll_table_no_overflow');
                $('.my_table').removeClass('my_scroll_table');
            });
            $(document).on('mouseup', 'span.icon-more.ui-sortable-handle', function (){
                $('.my_table').removeClass('my_scroll_table_no_overflow');
                $('.my_table').addClass('my_scroll_table');
            });

            $("#test_table tbody").sortable({
                handle: '.icon-more',
                connectWith: ".add-data-div.edit_task",
                axis: "y",
                tolerance: 'pointer',
                scroll: true,
                animation: 100,
                revert: 100,
                stop:function( event, ui ) {
                    $('.my_table').removeClass('my_scroll_table_no_overflow');
                    $('.my_table').addClass('my_scroll_table');
                },
                update: function (event, ui) {
                    if ($('.rank_th').length > 0) {
                        var rnk_val = 1;
                        $('.rank_th').each(function () {
                            $(this).text(rnk_val);
                            rnk_val++;
                        });
                    }
                    var tasks_orders = [];
                    $('#test_table tbody tr td.icon-more-holder').each(function (e) {
                        var orders = $(this).attr('data-order');
                        tasks_orders.push(orders);
                    });


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
                                if ($('.icon-more-holder').length > 0) {
                                    var ord_val = 1;
                                    $('.icon-more-holder').each(function () {
                                        $(this).attr('data-order', ord_val);
                                        ord_val++;
                                    });
                                }

                                var total_rows = $('#test_table tbody tr').length;
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
                                $("#test_table tbody").sortable('cancel')
//                                return false;
                            }
                        }
                    });
                }
            });
            $("#test_table").disableSelection();


            if ($('#config_lnk').attr('data-typeid') == 11) {
                $("#test_table tbody").sortable("disable");
            }

            if ($("#test_table").length > 0) {
                if ('<?php echo $config['allow_move'] ?>' == 'False') {
                    $("#test_table tbody").sortable("disable");
                }
            }

            //Autocomplete for share list
            $("#share_email").tokenInput('<?php echo base_url() . 'sharelist' ?>', {theme: "facebook", preventDuplicates: true});


            //Save configuration for list
            
            $(document).on('click', '.save_sublist_config', function () {
                $(this).css('pointer-events', 'none');
                var list_id = $(this).attr('data-listid');
                
                if ($('#content_' + list_id).find('.plus-category').attr('data-access') == 0) {
                    alert('You are not allowed to change configuration!');
                    return false;
                }
                
                 var allow_move = 'False';
                if ($('.config-submodal-body').find('#move_item_sub').is(':checked')) {
                    allow_move = 'True';
                }
                var show_completed = 'False';
                if ($('.config-submodal-body').find('#show_sub_completed_item').is(':checked')) {
                    show_completed = 'True';
                }
                var allow_undo = 'False';
                if ($('.config-submodal-body').find('#undo_item_sub').is(':checked')) {
                    allow_undo = 'True';
                }
                var allow_maybe = '0';
                if ($('.config-submodal-body').find('#maybe_allowed_sub').is(':checked')) {
                    allow_maybe = '1';
                }
                var show_time = '0';
                if ($('.config-submodal-body').find('#show_time_sub').is(':checked')) {
                    show_time = '1';
                }
                var enable_comment = '0';
                if ($('.config-submodal-body').find('#visible_comment_sub').is(':checked')) {
                    enable_comment = '1';
                }
                var show_preview = '0';
                if ($('.config-submodal-body').find('#show_preview_sub').is(':checked')) {
                    show_preview = '1';
                }
                var show_author = '0';
                if ($('.config-submodal-body').find('#show_author_sub').is(':checked')) {
                    show_author = '1';
                }
                var allow_append_locked = '0';
                if($('.config-submodal-body').find('#allow_append_locked_sub').is(':checked')){
                    allow_append_locked = '1';
                }
                
                $.ajax({
                    url: '<?php echo base_url() . 'update_config' ?>',
                    type: 'POST',
                    data: {
                        'list_id': list_id,
                        'allow_move': allow_move,
                        'show_completed': show_completed,
                        'allow_undo': allow_undo,
                        'allow_maybe': allow_maybe,
                        'show_time': show_time,
                        'enable_comment': enable_comment,
                        'show_preview': show_preview,
                        'show_author': show_author,
                        'allow_append_locked': allow_append_locked
                    },
                    success: function (res) {
                        if (res == 'success') {
                            $('#config_sub_msg').html('Configurations updated successfully.');
                            $('#config_sub_msg').removeClass('alert-danger');
                            $('#config_sub_msg').addClass('alert-success');
                            $('#config_sub_msg').show();
                            if (allow_undo == 'False') {
                                $('#tabs-' + list_id).find('#nexup_btns .undo-btn').removeClass('disabled_undo');
                                $('#tabs-' + list_id).find('#nexup_btns .undo-btn').addClass('disabled_undo');
                                $('#tabs-' + list_id).find('#listConfig_lnk').attr('data-allowundo', '0');
                            } else {
                                $('#tabs-' + list_id).find('#nexup_btns .undo-btn').removeClass('disabled_undo');
                                $('#tabs-' + list_id).find('#listConfig_lnk').attr('data-allowundo', '1');
                            }
                            if (allow_move == 'False') {
                                $('#tabs-' + list_id).find("#test_table tbody").sortable("disable");
                                $('#tabs-' + list_id).find('#listConfig_lnk').attr('data-moveallow', '0');
                            } else {
                                $('#listConfig_lnk_' + list_id).attr('data-moveallow', '1');
                                if ($('#tabs-' + list_id).find('#config_lnk_sub').attr('data-typeid') != 11) {
                                    if ($('#tabs-' + list_id).find('#test_table tbody').hasClass('ui-sortable')) {
                                        $('#tabs-' + list_id).find("#test_table tbody").sortable("enable");
                                    } else {
                                        //                                    $("#TaskList").addClass('tasks_lists_display');
                                        $('#tabs-' + list_id).find("#test_table tbody").sortable({
                                            handle: '.icon-more',
                                            connectWith: ".tasks_lists_display",
                                            animation: 100,
                                            revert: 100,
                                            stop:function( event, ui ) {
                                                $('#tabs-' + list_id).find('.my_table').removeClass('my_scroll_table_no_overflow');
                                                $('#tabs-' + list_id).find('.my_table').addClass('my_scroll_table');
                                            },
                                            update: function (event, ui) {
                                                if ($('#tabs-' + list_id).find('.rank_th').length > 0) {
                                                    var rnk_val = 1;
                                                    $('#tabs-' + list_id).find('.rank_th').each(function () {
                                                        $(this).text(rnk_val);
                                                        rnk_val++;
                                                    });
                                                }
                                                var tasks_ids = [];
                                                $('#tabs-' + list_id).find('#test_table tbody tr td.icon-more-holder').each(function (e) {
                                                    var ids = $(this).attr('dta-taskid');
                                                    tasks_ids.push(ids);
                                                });

                                                var task_id = $(ui.item).children('th').attr('dta-taskid');
                                                var list_id = $(ui.item).children('th').attr('data-listid');
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
                                                            if ($('#tabs-' + list_id).find('.icon-more-holder').length > 0) {
                                                                var ord_val = 1;
                                                                $('#tabs-' + list_id).find('.icon-more-holder').each(function () {
                                                                    $(this).attr('data-order', ord_val);
                                                                    ord_val++;
                                                                });
                                                            }

                                                            var total_rows = $('#tabs-' + list_id).find('#test_table tbody tr').length;
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
                                    }
                                }

                            }
                            
                            if (show_completed == 'False') {
                                $('#listConfig_lnk_' + list_id).attr('data-showcompleted', '0');
                                $('#tabs-' + list_id).find('.completed').addClass('hidden_tbl_row');
                            } else {
                                $('#listConfig_lnk_' + list_id).attr('data-showcompleted', '1');
                                $('#tabs-' + list_id).find('.completed').removeClass('hidden_tbl_row');
                            }
                            
                            if (allow_maybe == 0) {
                                $('#listConfig_lnk_' + list_id).attr('data-allowmaybe', '0');
                                $('#tabs-' + list_id).find('.present_lbl').each(function () {
                                    if ($(this).hasClass('yellow_label')) {
                                        $(this).removeClass('yellow_label');
                                    }
                                });
                                $('#tabs-' + list_id).find
                                
                            } else {
                                $('#listConfig_lnk_' + list_id).attr('data-allowmaybe', '1');
                            }
                            
                            if (show_time == 0) {
                                $('#listConfig_lnk_' + list_id).attr('data-showtime', '0');

                                if (!$('#tabs-' + list_id).find('.nodrag_time').hasClass('hidden_nodrag')) {
                                    $('#tabs-' + list_id).find('.nodrag_time').addClass('hidden_nodrag');
                                }
                                $('#tabs-' + list_id).find('.check_date').each(function () {
                                    if (!$(this).parent().hasClass('hidden_nodrag')) {
                                        $(this).parent().addClass('hidden_nodrag');
                                    }
                                });


                                $('#tabs-' + list_id).find('.check_date').closest('td.list-table-view-attend').addClass('hidden_tbl_row');
                                if (!$('#tabs-' + list_id).find('.check_date').closest('td.list-table-view-attend').hasClass('hidden_nodrag')) {
                                    $('#tabs-' + list_id).find('.check_date').closest('td.list-table-view-attend').addClass('hidden_nodrag');
                                }
                            } else {
                                $('#listConfig_lnk_' + list_id).attr('data-showtime', '1');
                                $('#tabs-' + list_id).find('.check_date').closest('td.list-table-view-attend').removeClass('hidden_tbl_row');
                                if ($('#tabs-' + list_id).find('.icon-settings').attr('data-typeid') == 11) {
                                    if ($('#tabs-' + list_id).find('.nodrag_time').hasClass('hidden_nodrag')) {
                                        $('#tabs-' + list_id).find('.nodrag_time').removeClass('hidden_nodrag');
                                    }
                                    $('#tabs-' + list_id).find('.check_date').each(function () {
                                        if ($(this).parent().hasClass('hidden_nodrag')) {
                                            $(this).parent().removeClass('hidden_nodrag');
                                        }
                                    });
                                }
                            }
                            
                            if (show_preview == 1) {
                                $('#tabs-' + list_id).find('#config_lnk').attr('data-showprev', '1');
                            } else {
                                $('#tabs-' + list_id).find('#config_lnk').attr('data-showprev', '0');
                            }
                            
                            if(show_author == 1){
                                $('#tabs-' + list_id).find('#config_lnk').attr('data-showowner', '1');
                            }else{
                                $('#tabs-' + list_id).find('#config_lnk').attr('data-showowner', '0');
                            }
                            
                            if(show_author == 1){
                                $('#tabs-' + list_id).find('#config_lnk_sub').attr('data-showowner', 1);
                            }else{
                                $('#tabs-' + list_id).find('#config_lnk_sub').attr('data-showowner', 0);
                            }
                            
                            
                            if ($('#tabs-' + list_id).find('.count_box').length == 1) {
                                var total_lbl = $('#tabs-' + list_id).find('.present_lbl').length;
                                var yes_cnt = $('#tabs-' + list_id).find('.green_label').length;
                                var maybe_cnt = $('#tabs-' + list_id).find('.yellow_label').length;
                                var no_cnt = $('#tabs-' + list_id).find('.red_label').length;
                                var blank_cnt = total_lbl - (yes_cnt + maybe_cnt + no_cnt);

                                $('#tabs-' + list_id).find('#yes_cnt').text(yes_cnt);
                                $('#tabs-' + list_id).find('#maybe_cnt').text(maybe_cnt);
                                $('#tabs-' + list_id).find('#no_cnt').text(no_cnt);
                                $('#tabs-' + list_id).find('#blank_cnt').text(blank_cnt);
                            }
                            
                            $.ajax({
                                url: '<?php echo base_url() . 'save_config' ?>',
                                type: 'POST',
                                data: {
                                    'list_id': list_id,
                                    'allow_move': allow_move,
                                    'show_completed': show_completed
                                },
                                success: function (res) {
                                }
                            });
                            
                            $.ajax({
                                url: '<?php echo base_url() . 'listing/get_list_body'; ?>',
                                type: 'POST',
                                data: {
                                    'Listid': list_id,
                                },
                                success: function (res) {
                                    var resp = JSON.parse(res);
                                    $('#tabs-' + list_id).find('.test_table tbody').html(resp['body']);
                                    if ($('#tabs-' + list_id).find('#config_lnk').attr('data-typeid') == 11) {
                                        $('#tabs-' + list_id).find("#test_table tbody").sortable("disable");
                                    } else {
                                        $('#tabs-' + list_id).find("#test_table tbody").sortable("enable");
                                    }
                                    if($('#tabs-' + list_id).find('#config_lnk_sub').attr('data-showowner') == 1){
                                        if($('#tabs-' + list_id).find('.sub_list_title_head').find('.sub_list_author_cls').length == 1){
                                            $('#tabs-' + list_id).find('.sub_list_author_cls').html(resp['owner']);
                                        }else{
                                            $('#tabs-' + list_id).find('.sub_list_title_head').append('<div class="sub_list_author_cls">' + resp['owner'] + '</div>');
                                        }
                                    }else{
                                        $('#tabs-' + list_id).find('.sub_list_author_cls').remove();
                                    }
                                    
                                    if($('#listConfig_lnk_' + list_id).attr('data-moveallow') == 0){
                                        $('#tabs-' + list_id).find("#test_table tbody").sortable("disable");
                                    }

                                }
                            });
                            
                            if (enable_comment == 0) {
                                if (!$('#tabs-' + list_id).find('#nexup_cmnt_span').hasClass('hide_box')) {
                                    $('#tabs-' + list_id).find('#nexup_cmnt_span').addClass('hide_box');
                                }
                            } else {
                                if ($('#tabs-' + list_id).find('#nexup_cmnt_span').hasClass('hide_box')) {
                                    $('#tabs-' + list_id).find('#nexup_cmnt_span').removeClass('hide_box');
                                }
                            }
                            
                            $('#config_sub_list').modal('toggle');
                            $('#tabs-' + list_id).find('#listConfig_lnk').attr('data-allowcmnt', enable_comment);
                            $('#tabs-' + list_id).find('.config-modal').modal('toggle');
                            $('#tabs-' + list_id).find('#save_config').css('pointer-events', 'all');
                            
                            if(show_author == 0){
                                if(!$('#tabs-' + list_id).find('.sub_list_author_cls').hasClass('hide_author')){
                                    $('#tabs-' + list_id).find('.sub_list_author_cls').addClass('hide_author');
                                }
                            }else{
                                if($('#tabs-' + list_id).find('.sub_list_author_cls').hasClass('hide_author')){
                                    $('#tabs-' + list_id).find('.sub_list_author_cls').removeClass('hide_author');
                                }
                            }
                        } else if (res == 'not allowed') {
                            $('#config_sub_msg').html('You have not created list. Please create list to proceed!');
                            $('#config_sub_msg').addClass('alert-danger');
                            $('#config_sub_msg').removeClass('alert-success');
                            $('#config_sub_msg').show();
                        } else {
                            $('#config_sub_msg').html('Something went wrong. Configuration was not updated. Please try again!');
                            $('#config_sub_msg').addClass('alert-danger');
                            $('#config_sub_msg').removeClass('alert-success');
                            $('#config_sub_msg').show();
                        }
                    }
                    
                }).done(function(){
                    $('#config_sub_msg').delay(5000).fadeOut('fast');
                    $('.save_sublist_config').css('pointer-events', 'all');
                });
                
                
            });

            var isTouchDevice = 'ontouchstart' in document.documentElement;
            if (isTouchDevice) {
                if ($(window).width() < 1367) {
                    $('.enable-move').removeClass('hide_move_btn');
                    $('.delete_list_tabbed').css('opacity', '1');
                }
            } else {
                $('.enable-move').removeClass('hide_move_btn');
                $('.enable-move').addClass('hide_move_btn');
//                $('.enable-move').hide();
                $(document).on('mouseover', '.tasks_lists_display.ui-sortable .task_li', function () {
//                    var did = $(this).attr('data-id');
//                    $('#task_' + did + ' .icon-more').css({'visibility': 'visible'});
                    $(this).find('.icon-more').css({'visibility': 'visible'});
                });

                //Hide re-order handle when mouse out of task
                $(document).on('mouseout', '.task_li', function () {
//                    var did = $(this).attr('data-id');
//                    $('#task_' + did + ' .icon-more').css({'visibility': 'hidden'});
                    $(this).find('.icon-more').css({'visibility': 'hidden'});
                });


                $(document).on('mouseover', '.heading_items_col', function () {
                    $(this).children().children().children('a.icon-more-h.move_col').css({'visibility': 'visible'});
                });

                $(document).on('mouseout', '.heading_items_col', function () {
                    $(this).children().children().children('a.icon-more-h.move_col').css({'visibility': 'hidden'});
                });


            }
            var prev_index = 0;
            var new_index = 0;
            $("#my_tbl thead tr").sortable({
                update: function (event, ui) {
                    $(this).children().each(function (index) {
                        $(this).find('td').last().html(index + 1)
                    });
                }
            });


            $('#test_table thead tr').sortable({
                handle: '.move_col',
                cancel: '.noDrag',
                connectWith: 'tbody thead tr.td_arrange_tr',
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
                    var next_class = $('#test_table thead .td_arrange_tr th:nth-child(2)').attr('class');
                    if ($('.icon-settings').attr('data-typeid') == 3) {
                        next_class = $('#test_table thead .td_arrange_tr th:nth-child(3)').attr('class');
                    }
                    if (next_class == 'noDrag') {
                        event.preventDefault();
                    }

                    var total_rows = $('#test_table tbody tr').length;

                    new_index = ui.item.index();
                    var col_ids = [];
                    var list_id = 0;
                    $('.heading_items_col .add-data-title').each(function () {
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

                                if ($('.icon-settings').attr('data-typeid') == 3) {
                                    if (new_pos < 2) {
                                        new_pos = 2;
                                        $('#test_table thead tr.td_arrange_tr').each(function () {
                                            var cols = $(this).children('th');
                                            cols.eq(old_pos).detach().insertBefore(cols.eq(new_pos));
                                        });
                                    }

                                } else {
                                    if (new_pos < 1) {
                                        new_pos = 1;
                                        $('#test_table thead tr.td_arrange_tr').each(function () {
                                            var cols = $(this).children('th');
                                            cols.eq(old_pos).detach().insertBefore(cols.eq(new_pos));
                                        });
                                    }

                                }

                                $('#test_table thead tr.td_add_tr').each(function () {
                                    var cols = $(this).children('th');

                                    if (new_pos > old_pos) {
                                        cols.eq(old_pos).detach().insertAfter(cols.eq(new_pos));
                                    } else {
                                        cols.eq(old_pos).detach().insertBefore(cols.eq(new_pos));
                                    }
                                });

                                $('#test_table tbody tr').each(function () {
                                    var cols = $(this).children('td');

                                    if (new_pos > old_pos) {
                                        cols.eq(old_pos).detach().insertAfter(cols.eq(new_pos));
                                    } else {
                                        cols.eq(old_pos).detach().insertBefore(cols.eq(new_pos));
                                    }
                                });

                                if (total_rows > 0) {

                                    if (resp.length > 0) {
                                        
                                        if($('.icon-settings').attr('data-typeid') == 2 || $('.icon-settings').attr('data-typeid') == 8){
                                            $.ajax({
                                               url: '<?php echo base_url(); ?>get_nexup_box',
                                               type: 'POST',
                                               data: {
                                                   'list_id': list_id,
                                               },
                                               success: function (res) {
                                                   if(res != ''){
                                                       var row_indx = $('#span_task_' + res).parent().parent().parent().index();
                                                       if($('.icon-settings').attr('data-typeid') == 2){
                                                           $('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                                                                                       $('.nexup-sub-group-one').attr('data-original-title', $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                                           var item_list_nexup_data = '<tr><td>' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                                           var grp_two = '';
                                                           for(s = 1; s < 4; s++){
                                                               if($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').length == 1){
                                                                   grp_two += '<span data-toggle="tooltip" title="" data-original-title="' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) +'">' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</span>';
                                                                   item_list_nexup_data += '<tr><td>' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                                               }
                                                           }
                                                           grp_two += '<p data-toggle="modal" data-target="#items-list" id="items_model_p" data-rowid="' + (row_indx + 1) +'" data-placement="top" title="Show all items"><img src="<?php echo base_url(); ?>assets/img/information-button-icon-23.png"></p>';
                                                           if(grp_two != ''){
                                                               $('.button-outer').find('.nexup-sub-group-two').html(grp_two);
                                                           }

                                                       }else if($('.icon-settings').attr('data-typeid') == 8){
                                                            $('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                                       }
                                                       if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                                                            $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                                               event.preventDefault()
                                                           }).tooltip({delay: { "hide": 100 }});
                                                       }
                                                       $(document).on('click', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                                                           $('.tooltip').remove()
                                                       });
                                                   }
                                                    var nexup_sun_grp_2_len = $('.button-outer').find('.nexup-sub-group-two').find('span').length;
                                                    if(nexup_sun_grp_2_len == 1){
                                                        $('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '18px');
                                                    }else if(nexup_sun_grp_2_len == 2){
                                                        $('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '16px');
                                                    }else{
                                                        $('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '13px');
                                                    }
                                                    set_nexup_box_data();
                                               }
                                           });
                                        }
                                    }
                                }

                                var current_id = $('.tasks_lists_display.ui-sortable').attr('id');
                                $('#' + current_id).sortable('destroy');
                                var first_id = $('#test_table tbody tr:nth-child(2)').attr('id');
                                $("#" + first_id).sortable({
                                    handle: '.icon-more',
                                    cancel: '.heading_col',
                                    update: function (event, ui) {
                                        if ($('.rank_th').length > 0) {
                                            var rnk_val = 1;
                                            $('.rank_th').each(function () {
                                                $(this).text(rnk_val);
                                                rnk_val++;
                                            });
                                        }
                                        var tasks_ids = [];
                                        $('.tasks_lists_display li').each(function (e) {
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
                                                    if ($('.icon-more-holder').length > 0) {
                                                        var ord_val = 1;
                                                        $('.icon-more-holder').each(function () {
                                                            $(this).attr('data-order', ord_val);
                                                            ord_val++;
                                                        });
                                                    }
                                                    var total_rows = $('#test_table tbody tr').length;
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
                                $('#test_table thead tr').sortable('refresh');
                            }
                        }
                    });

                }
            });

        </script>

        <script>
            $('#LoginForm').validate({
                errorClass: 'custom_form_error',
                rules: {
                    user_name: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true,
                    },
                },
                messages: {
                    user_name: {
                        required: 'Please enter email!',
                        email: 'Please enter valid email!'
                    },
                    password: {
                        required: 'Please enter password!',
                    },
                }
            });
            $('#profileForm').validate({
                errorClass: 'custom_form_error',
                rules: {
                    update_first_name: {
                        required: true,
                    },
                    update_last_name: {
                        required: true,
                    },
                },
                messages: {
                    update_first_name: {
                        required: 'Please enter first name!',
                    },
                    update_last_name: {
                        required: 'Please enter last name!',
                    },
                }
            });
            
            $('#passwordForm').validate({
                errorClass: 'custom_form_error',
                rules: {
                    password: {
                        required: true,
                        minlength: 8
                    },
                    confirmpassword: {
                        required: true,
                        minlength: 8,
                        equalTo: "#password"
                    },
                },
                messages: {
                    password: {
                        required: 'Please enter password!',
                        minlength: 'Minimum 8 characters are required!'
                    },
                    confirmpassword: {
                        required: 'Please confirm password!',
                        minlength: 'Minimum 8 characters are required!',
                        equalTo: 'Password does not match!'
                    },
                }
            });
        </script>

        <script>
            //Hide success/error message after 5 seconds of display
            setTimeout(function () {
                $('.alert-danger').fadeOut('fast');
                $('.alert-success').fadeOut('fast');
            }, 5000);
            $(document).on('click', '#add_list_top_btn', function () {
                $('.list-body-plus a.icon-add').trigger('click');
            });
            //Add new list
            $(document).on('keydown', '#list_name', function (evt) {
                var key_code = evt.keyCode;
                if (key_code == 27) {
                    $(this).val('');
                    $(this).hide();
                    $('.add_list_cls').text('');
                    $('.add_list_cls').hide();
                } else if (key_code == 13) {
                    var list_name = $(this).val();
                    $('.add_list_cls').text('');
                    $('.add_list_cls').hide();
                    $(this).removeClass('list-error');
                    $.ajax({
                        url: "<?php echo base_url() . 'listing/add' ?>",
                        type: 'POST',
                        data: {
                            'list_name': list_name
                        },
                        success: function (res) {
                            if (res == 'fail') {
                                $('#list_name').val('');
                                $('.add_list_cls').text('Something went wrong. Your list was not added. Please try again later!');
                                $('.add_list_cls').show();
                                $('#list_name').removeClass('list-error');
                                $('#list_name').addClass('list-error');
                            } else if (res == 'existing') {
                                $('.add_list_cls').text('This list already exist. Please try different name!');
                                $('.add_list_cls').show();
                                $('#list_name').removeClass('list-error');
                                $('#list_name').addClass('list-error');
                            } else if (res == 'empty') {
                                $('.add_list_cls').text('Please enter list name!');
                                $('.add_list_cls').show();
                                $('#list_name').removeClass('list-error');
                                $('#list_name').addClass('list-error');
                            } else {
                                $('#config_icons').removeClass('no-pointer-icons');
                                $('.plus-category').attr('data-access', '1');
                                if ($('.add-data-head-r').hasClass('hidden_add_column_btn')) {
                                    $('.add-data-head-r').removeClass('hidden_add_column_btn')
                                }
                                $("#add_item_li").before(res);
                                $('#list_name').val('');
                                $('.add_list_cls').text('');
                                $('.add_list_cls').hide();
                                $('#list_name').removeClass('list-error');
                                $('.add-data-head-r').removeClass('hidden_add_column_btn');
                            }
                        }
                    });
                } else {
                    $('.edit_list_cls').hide();
                }
            });

            //Display Edit list text box on list page
            $(document).on('click', '.edit_list', function () {
                if (window.location.pathname == '/lists') {
                    var err_box = '<span class="edit_list_cls">Please enter list name!</span>';
                    $('.edit_list_cls').remove();
                }
                var did = $(this).attr('data-id');
                var dslug = $(this).attr('data-slug');
                $.ajax({
                    url: "<?php echo base_url() . 'listing/get_list_data'; ?>",
                    type: 'POST',
                    data: {
                        'list_slug': dslug
                    },
                    success: function (res) {
                        if (res == 'not found') {
                            alert('List you are looking for does not exist!');
                        } else if (res == 'unauthorised') {
                            alert('You are not allowed to edit this list!');
                        } else {
                            $('#list_name').val('');
                            $('#list_name').hide();
                            var txtbx = '<input type="text" name="edit_list_name" id="edit_list_name" class="edit-list-class" data-id="' + did + '" value="' + res + '" placeholder="What is your List\'s name?" />';
                            var err_msg_list = '<div class="edit_list_cls" style="display: none;"></div>';
                            $('#list_' + did + ' .list-body-box .dropdown-action').before(txtbx);
                            $('#share_list').after(err_msg_list);
                            var el = $("#edit_list_name").get(0);
                            var elemLen = el.value.length;
                            el.selectionStart = elemLen;
                            el.selectionEnd = elemLen;
                            $('#edit_list_name').focus();
                            $('#listname_' + did).hide();
                            $('#list_' + did + ' .list-body-box .dropdown-action').hide();
                            $('#list_' + did + ' .list-body-box .list-body-box-link').css('pointer-events', 'none');
                        }
                    }
                });
            });

            //Display Edit list text box on tasks page
            $(document).on('click touchstart', '.edit_list_task', function () {
                if ($('.plus-category').attr('data-access') == 0) {
                    alert('Nope...\nThis list is not shared with you.');
                    return false;
                }
                $(this).css('pointer-events', 'none');

                var did = $(this).attr('data-id');
                var dslug = $(this).attr('data-slug');
                $.ajax({
                    url: "<?php echo base_url() . 'listing/get_list_data'; ?>",
                    type: 'POST',
                    context: this,
                    data: {
                        'list_slug': dslug
                    },
                    success: function (res) {
                        if (res == null || res == '') {
                            alert('Please login to perform action!');
                            $(this).css('pointer-events', 'auto');
                            return false;
                        } else if (res == 'unauthorised') {
                            alert('You are not authorised to edit this list!');
                            $(this).css('pointer-events', 'auto');
                            return false;
                        }
                        
                        if (res == 'not found') {
                            alert('List you are looking for does not exist!');
                        } else if (res == 'not allowed') {
                            alert('You are not allowed to edit this list!');
                        } else {
                            var txtbx = '<input type="text" name="edit_list_name" id="edit_list_name" class="edit-list-class" data-id="' + did + '" value="' + res + '" placeholder="I want to..." />';
                            var err_msg_list = '<div class="edit_list_cls" style="display: none;"></div>';
                            $('.add-data-head #listname_' + did).hide();
                            $('.add-data-head #listname_' + did).before(txtbx);
                            $('#share_list').after(err_msg_list);
                            var el = $("#edit_list_name").get(0);
                            var elemLen = el.value.length;
                            el.selectionStart = elemLen;
                            el.selectionEnd = elemLen;
                            $('#edit_list_name').focus();
                            $('#listname_' + did).hide();
                            $('#edit_list_task_page').hide();
                        }
                        $(this).css('pointer-events', 'auto');
                    }
                });
            });
            
            
            
            $(document).on('click touchstart', '.edit_list_task_sub', function () {
                var did = $(this).attr('data-id');
                if ($('#test_table_' + did).find('.plus-category').attr('data-access') == 0) {
                    alert('Nope...\nThis list is not shared with you.');
                    return false;
                }
                $(this).css('pointer-events', 'none');
                var dslug = $(this).attr('data-slug');
                $.ajax({
                    url: "<?php echo base_url() . 'listing/get_list_data'; ?>",
                    type: 'POST',
                    context: this,
                    data: {
                        'list_slug': dslug
                    },
                    success: function (res) {
                        if (res == null || res == '') {
                            alert('Please login to perform action!');
                            $(this).css('pointer-events', 'auto');
                            return false;
                        } else if (res == 'unauthorised') {
                            alert('You are not authorised to edit this list!');
                            $(this).css('pointer-events', 'auto');
                            return false;
                        }
//                      
                        if (res == 'not found') {
                            alert('List you are looking for does not exist!');
                        } else if (res == 'not allowed') {
                            alert('You are not allowed to edit this list!');
                        } else {
                            var txtbx = '<input type="text" name="edit_list_name_sub" id="edit_list_name_sub_' + did + '" class="edit-list-class edit-list-class-sub edit_list_name_sub" data-id="' + did + '" value="' + res + '" placeholder="What is your List\'s name?" />';
                            var err_msg_list = '<div class="edit_list_sub_cls" style="display: none;"></div>';
                            $('.add-data-head #listname_' + did).hide();
                            $('.add-data-head #listname_' + did).before(txtbx + err_msg_list);
//                            $('#share_sub_list[data-listid="' + did + '"]').after(err_msg_list);
                            var el = $('#tabs-' + did).find("#edit_list_name_sub_" + did).get(0);
                            var elemLen = el.value.length;
                            el.selectionStart = elemLen;
                            el.selectionEnd = elemLen;
                            $('#edit_list_name_sub_' + did).focus();
                            $('#listname_' + did).hide();
                            $('#edit_list_task_page').hide();
                        }
                        $(this).css('pointer-events', 'auto');
                    }
                });
            });
            
            $(document).on('focus', '.edit-list-class-sub', function () {
//                $(this).setSelectionRange(0,9999);
                $(this).select();
            });
            
            $(document).on('blur', '.edit_list_name_sub', function (evt) {
                if (window.location.pathname == '/lists') {
                    $('.edit_list_sub_cls').remove();
                }
                var did_edit = $(this).attr('data-id');
                var list_txt = $(this).val();
                edit_list_names_sub(did_edit, list_txt);
            });
            
            $(document).on('keydown', '.edit_list_name_sub', function(evt){
                var key_code = evt.keyCode;
                var did_edit = $(this).attr('data-id');
                var list_txt = $(this).val();
                
                if (key_code == 27) {
                    if (did_edit > 0) {
                        $(this).remove();
//                        $('.edit_list_sub_cls').remove();
                        $('#edit_list_name_sub_' + did_edit).remove();
                        $('#tabs-' + did_edit).find('.edit_list_sub_cls').remove();
                        
                        $('#listname_' + did_edit).show();
                        $('#list_' + did_edit + ' .list-body-box .dropdown-action').show();
                        $('#list_' + did_edit + ' .list-body-box .list-body-box-link').removeAttr('style');
                        $('#edit_list_task_page').show();
                    }
                } else if (key_code == 13) {
                    $(this).trigger('blur');
                } else {
                    $('.edit_list_cls').hide();
                }
            });
            

            $(document).on('focus', '#edit_list_name', function () {
                $('#edit_list_name').select();
            });
            $(document).on('focus', '#edit_task_name', function (e) {
                e.preventDefault();
                $('#edit_task_name').select();
                return false;
            });

            function edit_list_names(did_edit, list_txt) {
                var path_name = window.location.pathname;
                if (list_txt.trim() == '') {
                    if (path_name == '/lists') {
                        var err_box = '<span class="edit_list_cls">Please enter list name!</span>';
                        $('#list_' + did_edit + ' .list-body-box #edit_list_name').after(err_box);
                    } else {
                        $('.edit_list_cls').text('Please enter list name!');
                        $('.edit_list_cls').show();
                    }
                    return false;
                } else {
                    if (path_name == '/lists') {
                        $('.edit_list_cls').remove();
                    }
                    $('.edit_list_cls').text('');
                    $('.edit_list_cls').hide();
                }
                var operation = '';
                var data_send = {};
                if (did_edit > 0) {
                    operation = 'edit';
                    data_send.list_id = did_edit;
                    data_send.edit_list_name = list_txt.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                    call_url = "<?php echo base_url() . 'listing/update'; ?>"
                } else {
                    operation = 'add';
                    data_send.list_name = list_txt.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                    var call_url = "<?php echo base_url() . 'listing/add'; ?>";
                }
                $.ajax({
                    url: call_url,
                    type: 'POST',
                    data: data_send,
                    success: function (res) {
                        if (res == 'existing') {
                            if (path_name == '/lists') {
                                var err_box = '<span class="edit_list_cls">This list already exist. Please try different name!</span>';
                                $('#list_' + did_edit + ' .list-body-box #edit_list_name').after(err_box);
                            } else {
                                $('.edit_list_cls').text('This list already exist. Please try different name!');
                                $('.edit_list_cls').show();
                                $('#edit_list_name').removeClass('list-error');
                                $('#edit_list_name').addClass('list-error');
                                $('#edit_list_name').focus();
                            }
                        } else if (res == 'fail') {
//                            if (operation == 'add') {
//                                $('#edit_list_name').val('');
//                            }
                            if (path_name == '/lists') {
                                var err_box = '<span class="edit_list_cls">Something went wrong. Your list was not updated. Please try again later!</span>';
                                $('#list_' + did_edit + ' .list-body-box #edit_list_name').after(err_box);
                            } else {
                                $('.edit_list_cls').text('Something went wrong. Your list was not updated. Please try again later!');
                                $('.edit_list_cls').show();
                                $('#edit_list_name').removeClass('list-error');
                                $('#edit_list_name').addClass('list-error');
                                $('#edit_list_name').focus();
                            }
                        } else if (res == 'empty') {
                            if (path_name == '/lists') {
                                var err_box = '<span class="edit_list_cls">Please enter list name!</span>';
                                $('#list_' + did_edit + ' .list-body-box #edit_list_name').after(err_box);
                            } else {
                                $('.edit_list_cls').text('Please enter list name!');
                                $('#edit_list_name').focus();
                                $('.edit_list_cls').show();
                                $('#edit_list_name').removeClass('list-error');
                                $('#edit_list_name').addClass('list-error');
                            }
                        } else {
                            $('#delete_list_builder').show();
                            $('#delete_list_builder').removeClass('disabled_btn');
                            $('.plus-category').attr('data-access', 1);
                            if (path_name == '/lists') {
                                $('.edit_list_cls').remove();
                            }
                            var resp = JSON.parse(res);
                            $('.export_to_csv_btn').attr('href', '<?php echo base_url(); ?>export_log/' + resp[0]);
                            if (operation == 'add') {
                                if($('#config_icons').hasClass('no-pointer-icons')){
                                    $('#config_icons').removeClass('no-pointer-icons');
                                }
                                if ($("#history_dd").length == 0) {
                                    var ddl_history = '<div class="h-nav dropdown">';
                                    ddl_history += '<a class="icon-history custom_cursor" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> </a>';
                                    ddl_history += '<ul class="dropdown-menu" id="history_dd" aria-labelledby="dropdownMenu2">';
                                    ddl_history += '<li class="history_options" value="<?php echo base_url() . 'list/' ?>' + resp[1] + '" data-slug="' + resp[1] + '"><a href="<?php echo base_url() . 'list/' ?>' + resp[1] + '">' + list_txt.replace(/</g, "&lt;").replace(/>/g, "&gt;") + '</a></li>';
                                    ddl_history += '</ul>';
                                    ddl_history += '</div>';
//                                        alert(ddl_history);
//                                        $('.history_div').html(ddl_history);
                                    $('.nav-view').after(ddl_history);
                                } else {
                                    var ddl_history_option = '<li class="history_options" value="<?php echo base_url() . 'list/' ?>' + resp[1] + '" data-slug="' + resp[1] + '"><a href="<?php echo base_url() . 'list/' ?>' + resp[1] + '">' + list_txt.replace(/</g, "&lt;").replace(/>/g, "&gt;") + '</a></li>';
                                    $('#history_dd').prepend(ddl_history_option);
                                    $('#history_dd').val('<?php echo base_url() . 'list/' ?>' + resp[1]);
                                }
                                if ($('.inflologinlink').length == 0) {
                                    $('#customize_btn').removeClass('hide_custom_btn');
                                }
                                $('#save_config').attr('ddata-listid', resp[0]);
                                $('#save_col').attr('data-listid', resp[0]);
                                $('.add-data-head-r').removeClass('hidden_add_column_btn');
                                $('.edit_list_task').attr('data-id', resp[0]);
                                $('#share_btn').attr('data-id', resp['list_inflo_id']);
                                $('#task_name').attr('data-listid', resp[0]);
                                $('#save_config').attr('data-listid', resp[0]);
                                $('.edit_list_task').attr('data-slug', resp[1]);
                                $('#add_bulk_data').attr('data-id', resp[0]);
                                $('#copy_list_btn').attr('data-id', resp[0]);
                                $('.plus-category').attr('data-access', '1');
                                if ($('#add_bulk_data').hasClass('hdn_bulk')) {
                                    $('#add_bulk_data').removeClass('hdn_bulk');
                                }
                                if ($('#add_data_desc').hasClass('hdn_desc')) {
                                    $('#add_data_desc').removeClass('hdn_desc');
                                }
                                $('#add_data_desc').attr('data-id', resp[0]);
                                $('#list_desc_text').attr('data-listid', resp[0]);
                                $('#list_desc').attr('data-listid', resp[0]);
                                $('#update_desc_btn').attr('data-listid', resp[0]);
                                $('.add_icon_holder').attr('data-listid', resp[0]);
                                $('.delete_list_builder').attr('data-id', resp[0]);
                                $('.delete_list_builder').attr('data-slug', resp[1]);
                                $('.delete_list_builder').show();
                                var append_td = '<th class="heading_items_col hidden_heading" data-listid="' + resp[0] + '" data-colid="' + resp['col_id'] + '" style="">';
                                append_td += '<div class="add-data-title-r">';
                                append_td += '<a href="" class="icon-more-h move_col ui-sortable-handle" id="dropdownMenu0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="visibility: hidden;"></a>';
                                append_td += '<a class="remove_col custom_cursor icon-cross-out" data-colid="' + resp['col_id'] + '" data-listid="' + resp[0] + '" style="visibility: hidden;"></a>';
                                append_td += '</div>';
                                append_td += '<div class="add-data-title" data-colid="' + resp['col_id'] + '" data-listid="' + resp[0] + '" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="' + list_txt + '">';
                                append_td += '<span class="column_name_class" id="col_name_' + resp['col_id'] + '">' + list_txt + '</span>';
                                append_td += '</div>';
                                append_td += '</th>';
                                $('#test_table thead tr.td_arrange_tr .nodrag_actions').after(append_td);
                                $('.heading_items_col_add:eq(0)').attr('data-listid', resp[0]);
                                $('.heading_items_col_add:eq(0)').attr('data-colid', resp[2]);
                            } else if (operation == 'edit') {
                                $('#history_dd li[value="<?php echo base_url() . 'list/' ?>' + resp[1] + '"] a').html(list_txt.replace(/</g, "&lt;").replace(/>/g, "&gt;"));
                                $.ajax({
                                    url: "<?php echo base_url() . 'listing/push'; ?>",
                                    type: 'POST',
                                    data: data_send,
                                    success: function (resp) {

                                    }
                                });
                            }
                            $('#edit_list_name').remove();
                            if ($('#task_name').length == 1) {
                                $('#task_name').focus();
                            }
                            $('.edit_list_cls').hide();
                            $('#edit_list_name').removeClass('list-error');
                            $('.listname_' + did_edit).html(list_txt.replace(/</g, "&lt;").replace(/>/g, "&gt;"));
                            $('.listname_' + did_edit).show();
                            $('#list_' + did_edit + ' .list-body-box .dropdown-action').show();
                            $('#list_' + did_edit + ' .list-body-box .list-body-box-link').removeAttr('style');
                            $('#edit_list_task_page').show();
                            $('.edit_list_task').attr('data-original-title', list_txt.replace(/</g, "&lt;").replace(/>/g, "&gt;"));

                            $('.sharemodal-head h2 span').html(list_txt.replace(/</g, "&lt;").replace(/>/g, "&gt;"));
                            $('#edit_list_task_page').attr('data-id', resp[0]);
                            $('.list_type_cls').attr('data-listid', resp[0]);
                            if (resp[1] != '') {
                                $('#edit_list_task_page').attr('data-slug', resp[1]);
                                $('#share_list').show();
                                $('#import_contacts').attr('href', '<?php echo base_url() . 'list/' ?>' + resp[1]);
                                $('#import_contacts').text('<?php echo base_url() . 'list/' ?>' + resp[1]);
                            }
                            $('#task_name').attr('data-listid', resp[0]);
                            $('#add_task_li #name').attr('data-listid', resp[0]);
                            $('#listLock_lnk').attr('data-id', resp[0]);
                            $('#listLock_lnk').attr('data-slug', resp[1]);
                            $('.add-data-head h2').attr('id', 'listname_' + resp[0]);
                            $('.add-data-head h2').removeClass('listname_0');
                            $('.add-data-head h2').addClass('listname_' + resp[0]);
                            if (resp[2] != 'undefined') {
                                $('#task_name:eq(0)').attr('data-colid', resp[2]);
                            }
                            
                            $('#tab_ul').find('li.custom_tab[aria-controls="tabs-' + did_edit + '"]').find('a.custom_tab_anchor[href="#tabs-' + did_edit + '"]').html(list_txt.replace(/</g, "&lt;").replace(/>/g, "&gt;"));
                            
                        }
                        if (operation == 'add') {
                            $('#task_name').attr('placeholder', 'Add ' + list_txt);
                        }
                    }
                });
            }
            
            
            function edit_list_names_sub(did_edit, list_txt) {
                var path_name = window.location.pathname;
                if (list_txt.trim() == '') {
                        $('#tabs-' + did_edit).find('.edit_list_sub_cls').text('Please enter list name!');
                        $('#tabs-' + did_edit).find('.edit_list_sub_cls').show();
                        $('#edit_list_name_sub_' + did_edit).focus();
                    return false;
                } else {
                    $('#tabs-' + did_edit).find('.edit_list_sub_cls').text('');
                    $('#tabs-' + did_edit).find('.edit_list_sub_cls').hide();
                }
                var operation = '';
                var data_send = {};
                
                    operation = 'edit';
                    data_send.list_id = did_edit;
                    data_send.edit_list_name = list_txt.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                    call_url = "<?php echo base_url() . 'listing/update'; ?>"
                
                $.ajax({
                    url: call_url,
                    type: 'POST',
                    data: data_send,
                    success: function (res) {
                        if (res == 'existing') {
                                $('#tabs-' + did_edit).find('.edit_list_sub_cls').text('This list already exist. Please try different name!');
                                $('#tabs-' + did_edit).find('.edit_list_sub_cls').show();
                                $('#edit_list_name_sub_' + did_edit).focus();
                                $('#edit_list_name_sub_' + did_edit).removeClass('list-error');
                                $('#edit_list_name_sub_' + did_edit).addClass('list-error');
                                $('#edit_list_name_sub_' + did_edit).focus();
                        } else if (res == 'fail') {
                                $('#tabs-' + did_edit).find('.edit_list_sub_cls').text('Something went wrong. Your list was not updated. Please try again later!');
                                $('#tabs-' + did_edit).find('.edit_list_sub_cls').show();
                                $('#edit_list_name_sub_' + did_edit).focus();
                                $('#edit_list_name_sub_' + did_edit).removeClass('list-error');
                                $('#edit_list_name_sub_' + did_edit).addClass('list-error');
                                $('#edit_list_name_sub_' + did_edit).focus();
                        } else if (res == 'empty') {
                                $('#tabs-' + did_edit).find('.edit_list_sub_cls').text('Please enter list name!');
                                $('#tabs-' + did_edit).find('.edit_list_sub_cls').show();
                                $('#edit_list_name_sub_' + did_edit).focus();
                                
                                $('#edit_list_name_sub_' + did_edit).focus();
                                $('#edit_list_name_sub_' + did_edit).removeClass('list-error');
                                $('#edit_list_name_sub_' + did_edit).addClass('list-error');
                        } else {
                            $('#tabs-' + did_edit).find('#delete_sub_list_builder').show();
                            $('#tabs-' + did_edit).find('#delete_sub_list_builder').removeClass('disabled_btn');
                            $('#tabs-' + did_edit).find('.plus-category').attr('data-access', 1);
                            var resp = JSON.parse(res);
                            $('#tabs-' + did_edit).find('.export_to_csv_btn').attr('href', '<?php echo base_url(); ?>export_log/' + resp[0]);
                                $.ajax({
                                    url: "<?php echo base_url() . 'listing/push'; ?>",
                                    type: 'POST',
                                    data: data_send,
                                    success: function (resp) {

                                    }
                                });
                            $('#edit_list_name_sub_' + did_edit).remove();
                            if ($('#tabs-' + did_edit).find('#task_name').length == 1) {
                                $('#tabs-' + did_edit).find('#task_name').focus();
                            }
                            $('#tabs-' + did_edit).find('.edit_list_sub_cls').remove();
                            $('#edit_list_name_sub_' + did_edit).removeClass('list-error');
                            $('.listname_' + did_edit).html(list_txt.replace(/</g, "&lt;").replace(/>/g, "&gt;"));
                            $('.listname_' + did_edit).show();
                            $('#list_' + did_edit + ' .list-body-box .dropdown-action').show();
                            $('#list_' + did_edit + ' .list-body-box .list-body-box-link').removeAttr('style');
                            $('.edit_list_task').attr('data-original-title', list_txt.replace(/</g, "&lt;").replace(/>/g, "&gt;"));

                            $('#tabs-' + did_edit).find('.list_type_cls').attr('data-listid', resp[0]);
                            if (resp[1] != '') {
                                $('#content_' + did_edit).find('#share_sub_list').show();
                            }
                            $('#tabs-' + did_edit).find('.task_sub_name').attr('data-listid', resp[0]);
                            $('#tabs-' + did_edit).find('#listsub_Lock_lnk').attr('data-id', resp[0]);
                            $('#tabs-' + did_edit).find('#listsub_Lock_lnk').attr('data-slug', resp[1]);
                            $('#tabs-' + did_edit).find('.add-data-head h2').attr('id', 'listname_' + resp[0]);
                            
                            $('#tab_ul').find('li.custom_tab[aria-controls="tabs-' + did_edit + '"]').find('a.custom_tab_anchor[href="#tabs-' + did_edit + '"]').html(list_txt.replace(/</g, "&lt;").replace(/>/g, "&gt;"));
                            
                        }
                    }
                });
            }

            $(document).on('blur', '#edit_list_name', function (evt) {
                if (window.location.pathname == '/lists') {
                    $('.edit_list_cls').remove();
                }
                var did_edit = $(this).attr('data-id');
                var list_txt = $(this).val();
                edit_list_names(did_edit, list_txt);
            });

            //Edit List
            $(document).on('keydown', '#edit_list_name', function (evt) {
                var key_code = evt.keyCode;
                var did_edit = $(this).attr('data-id');
                var list_txt = $(this).val();

                if (key_code == 27) {
                    if (did_edit > 0) {
                        $(this).remove();
                        $('.edit_list_cls').remove();
                        $('#listname_' + did_edit).show();
                        $('#list_' + did_edit + ' .list-body-box .dropdown-action').show();
                        $('#list_' + did_edit + ' .list-body-box .list-body-box-link').removeAttr('style');
                        $('#edit_list_task_page').show();
                    }
                } else if (key_code == 13) {
                    $(this).trigger('blur');
                } else {
                    $('.edit_list_cls').hide();
                }
            });

            //Delete list
            $(document).on('click', '.delete_list, .delete_list_builder', function () {
                var cnf = confirm('If you delete parent list, you will loose all your child lists of this list. Are you sure want to delete this list?');
                var slg = $(this).attr('data-slug');
                if (cnf) {
                    var did_del = $(this).attr('data-id');
                    $.ajax({
                        url: "<?php echo base_url(); ?>listing/remove",
                        type: 'POST',
                        context: this,
                        data: {
                            'list_id': did_del,
                        },
                        success: function (res) {
                            if (res == 'success') {
                                var parent_li = $(this).closest('li').attr('id');
                                var del_item_count = $('#' + parent_li + ' .list_item_count').text();
                                var total_item_count = $('.all_items_count').text();
                                var new_total_items = (total_item_count - del_item_count);
                                $('.all_items_count').html(new_total_items);

                                $('#list_' + did_del).remove();
                                $.ajax({
                                    url: "listing/delete",
                                    type: 'POST',
                                    data: {
                                        'list_id': did_del,
                                    },
                                    success: function (resp) {
                                    }
                                });
                                if ($(this).hasClass('delete_list_builder')) {
                                    window.location.href = '<?php echo base_url() . 'lists'; ?>';
                                }
                                $('.history_options').each(function (e) {
                                    if ($(this).attr('data-slug') == slg) {
                                        var url_txt = $(this).children('a').text();
                                        $(this).removeAttr('data-slug');
                                        $(this).removeAttr('value');
                                        $(this).html('<a class="removed_list">' + url_txt + '<a>');
                                    }
                                });

                            } else if (res == 'fail') {
                                alert('Something went wrong. List was not deleted. Please try again!');
                            } else if (res == 'unauthorized') {
                                alert('You are not allowed to delete this list. Please contact list admin to get access!');
                            }
                        }
                    });
                }

            });
            
            
            $(document).on('click', '.delete_sub_list_builder', function () {
                var cnf = confirm('Are you sure want to delete this list?');
                var slg = $(this).attr('data-slug');
                if (cnf) {
                    var did_del = $(this).attr('data-id');
                    $.ajax({
                        url: "<?php echo base_url(); ?>listing/remove",
                        type: 'POST',
                        context: this,
                        data: {
                            'list_id': did_del,
                        },
                        success: function (res) {
                            if (res == 'success') {
                                $.ajax({
                                    url: "<?php echo base_url(); ?>/listing/delete",
                                    type: 'POST',
                                    data: {
                                        'list_id': did_del,
                                    },
                                    success: function (resp) {
                                    }
                                });
                                $('#tab_ul').find('li.custom_tab[aria-controls="tabs-' + did_del + '"]').remove();
                                var tab_num = $('#tabs-'+ did_del).index();
                                $('#tabs-'+ did_del).remove();
                                tabs.tabs( "refresh" );

                            } else if (res == 'fail') {
                                alert('Something went wrong. List was not deleted. Please try again!');
                            } else if (res == 'unauthorized') {
                                alert('You are not allowed to delete this list. Please contact list admin to get access!');
                            }
                        }
                    });
                }

            });


            //Remove visited list from directory page
            $(document).on('click', '.remove_list', function () {
                var cnf = confirm('Are you sure want to remove this list from your directory?');
                var slg = $(this).attr('data-slug');
                if (cnf) {
                    var did_del = $(this).attr('data-id');
                    $.ajax({
                        url: "<?php echo base_url(); ?>listing/remove_directory",
                        type: 'POST',
                        context: this,
                        data: {
                            'list_id': did_del,
                        },
                        success: function (res) {
                            if (res == 'success') {
                                var parent_li = $(this).closest('li').attr('id');
                                var del_item_count = $('#' + parent_li + ' .list_item_count').text();
                                var total_item_count = $('.all_items_count').text();
                                var new_total_items = (total_item_count - del_item_count);
                                $('.all_items_count').html(new_total_items);

                                $('#list_' + did_del).remove();
                                if ($(this).hasClass('delete_list_builder')) {
                                    window.location.href = '<?php echo base_url() . 'lists'; ?>';
                                }
                                $('.history_options').each(function (e) {
                                    if ($(this).attr('data-slug') == slg) {
                                        var url_txt = $(this).children('a').text();
                                        $(this).removeAttr('data-slug');
                                        $(this).removeAttr('value');
                                        $(this).html('<a class="removed_list">' + url_txt + '<a>');
                                    }
                                });

                            } else if (res == 'fail') {
                                alert('Something went wrong. List was not deleted. Please try again!');
                            } else if (res == 'unauthorized') {
                                alert('You are not allowed to delete this list. Please contact list admin to get access!');
                            }
                        }
                    });
                }

            });
            
            
            //Remove shared list from directory page
            $(document).on('click', '.remove_list_local_share', function () {
                var cnf = confirm('Are you sure want to remove this list from your directory?');
                var slg = $(this).attr('data-slug');
                if (cnf) {
                    var did_del = $(this).attr('data-id');
                    $.ajax({
                        url: "<?php echo base_url(); ?>listing/remove_local_share",
                        type: 'POST',
                        context: this,
                        data: {
                            'list_id': did_del,
                        },
                        success: function (res) {
                            if (res == 'success') {
                                $(this).parent().parent().parent().remove();
                            } else if (res == 'fail') {
                                alert('Something went wrong. List was not deleted. Please try again!');
                            } else if (res == 'unauthorized') {
                                alert('You are not allowed to remove this list. Please contact list admin to get access!');
                            }
                        }
                    });
                }

            });


            //Update configurations of list (like allow re-ordering of list or not, Display completed tasks or not
            $(document).on('click', '#config_lnk', function () {
                if ($(this).hasClass('active-setting')) {
                    $(this).removeClass('active-setting');
                } else {
                    $(this).addClass('active-setting');
                }
                if ($(this).parent().parent().find('#config_icons').hasClass('hide_data'))
                {
                    $(this).parent().parent().find('#config_icons').fadeIn(1000);
                    $(this).parent().parent().find('#config_icons').removeClass('hide_data');
                }
                else
                {
                    $(this).parent().parent().find('#config_icons').fadeOut(1000);
                    $(this).parent().parent().find('#config_icons').addClass('hide_data');
                }
//                if ($('#config_icons').hasClass('hide_data'))
//                {
//                    $('#config_icons').fadeIn(1000);
//                    $('#config_icons').removeClass('hide_data');
//                    $('#config_icons2').fadeIn(1000).css("display", "block");
//                    ;
//                    $('#config_icons2').removeClass('hide_data');
//                }
//                else
//                {
//                    $('#config_icons').fadeOut(1000);
//                    $('#config_icons').addClass('hide_data');
//                    $('#config_icons2').fadeOut(1000);
//                    $('#config_icons2').addClass('hide_data');
//                }
            });

            //Display configuration modal
            
            $(document).on('click', '.sub_listConfig_lnk', function (e) {
                 $('#config_sub_list').modal('show');
            });
            
            
            $(document).on('click', '#listConfig_lnk', function () {
                $('#save_config').css('pointer-events', 'all');
                
                var movable = $(this).attr('data-moveallow');
                $('#move_item').prop('checked', false);
                if (movable == 1) {
                    $('#move_item').prop('checked', true);
                }

                var show_completed_items = $(this).attr('data-showcompleted');
                $('#show_completed_item').prop('checked', false);
                if (show_completed_items == 1) {
                    $('#show_completed_item').prop('checked', true);
                }

                var backup_allow = $(this).attr('data-allowundo');
                $('#undo_item').prop('checked', false);
                if (backup_allow == 1) {
                    $('#undo_item').prop('checked', true);
                }
                
                var allow_maybe = $(this).attr('data-allowmaybe');
                $('#maybe_allowed').prop('checked', false);
                if (allow_maybe == 1) {
                    $('#maybe_allowed').prop('checked', true);
                }
                
                var show_time_stamp = $(this).attr('data-showtime');
                $('#show_time').prop('checked', false);
                if (show_time_stamp == 1) {
                    $('#show_time').prop('checked', true);
                }
                
                var show_owner = $('#config_lnk').attr('data-showowner');
                $('#show_author').prop('checked', false);
                if (show_owner == 1) {
                    $('#show_author').prop('checked', true);
                }
                
                var show_comment = $(this).attr('data-allowcmnt');
                $('#visible_comment').prop('checked', false);
                if (show_comment == 1) {
                    $('#visible_comment').prop('checked', true);
                }

                $('#config-list').modal('show');
            });
            
            //Share list with email address
            $(document).on('click', '#invite_btn', function () {
                $('.sharemodal-body .input-outer').removeClass('alert-danger');
                $('.sharemodal-body .input-textarea textarea').removeClass('alert-danger');

                var msg_share = $('#message_share').val();
                var selectedValues = $('#share_email').tokenInput("get");
                var list_url = $(this).attr('data-link');
                var email_arr = [];
                for (i = 0; i < selectedValues.length; i++) {
                    email_arr.push(selectedValues[i].name);
                }
                var emails = '';
                if (email_arr.length > 0) {
                    emails = JSON.stringify(email_arr);
                }

                var list_id = $(this).attr('data-listid');

                $.ajax({
                    url: "<?php echo base_url() . 'sharelist'; ?>",
                    type: 'POST',
                    data: {
                        'email': emails,
                        'Listid': list_id,
                        'msg_share': msg_share,
                        'list_url': list_url
                    },
                    success: function (res) {
                        if (res == 'empty both') {
                            $('.sharemodal-body .input-outer').addClass('alert-danger');
                            $('.sharemodal-body .input-textarea textarea').addClass('alert-danger');
                        } else if (res == 'empty email') {
                            $('.sharemodal-body .input-outer').addClass('alert-danger');
                            $('.sharemodal-body .input-textarea textarea').removeClass('alert-danger');
                        } else if (res == 'empty msg') {
                            $('.sharemodal-body .input-outer').removeClass('alert-danger');
                            $('.sharemodal-body .input-textarea textarea').addClass('alert-danger');
                        } else if (res == 'success') {
                            $('.sharemodal-body .input-outer').removeClass('alert-danger');
                            $('.sharemodal-body .input-textarea textarea').removeClass('alert-danger');
                            $('#share_msg').html('Your list was shared successfully.');
                            $('#share_msg').removeClass('alert-danger');
                            $('#share_msg').addClass('alert-success');
                            $('#share_msg').show();
                            $('#message_share').val("");
                            $('#share_email').tokenInput("clear");
                        } else {
                            $('#share_msg').html('Something went wrong. Your list was not shared. please try again!');
                            $('#share_msg').removeClass('alert-success');
                            $('#share_msg').addClass('alert-danger');
                            $('#share_msg').show();
                        }
                        $('.alert-danger').delay(5000).fadeOut('fast');
                        $('.alert-success').delay(5000).fadeOut('fast');
                    }
                });

            });


            function add_item_new(list_id, list_name, task_data, col_id) {
                $('.delete_task').prop('disabled', true);
                $.ajax({
                    url: "<?php echo base_url() . 'item/add'; ?>",
                    async : false,
                    type: 'POST',
                    context: this,
                    data: {
                        'list_id': list_id,
                        'list_name': list_name,
                        'task_data': task_data,
//                            'task_name': $(this).val(),
                        'col_id': col_id
                    },
                    success: function (res) {
                        $('.task_name').prop('disabled', false);
                        if (res == 'existing') {
                            $('.add_task_cls').text('This task already exist. Please try different name!');
                            $('.add_task_cls').show();
                            $('#add_task_li').removeClass('list-error');
                            $('#add_task_li').addClass('list-error');
                            $('.task_name').val('');
                            $('.task_name').prop('disabled', false);
                        } else if (res == 'fail') {
                            $('#task_name').val('');
                            $('.add_task_cls').text('Something went wrong. Your task was not added. Please try again later!');
                            $('.add_task_cls').show();
                            $('#add_task_li').removeClass('list-error');
                            $('#add_task_li').addClass('list-error');
                            $('.task_name').val('');
                            $('.task_name').prop('disabled', false);
                        } else if (res == 'empty') {
                            $('.add_task_cls').text('Please enter task name!');
                            $('.add_task_cls').show();
                            $('#add_task_li').removeClass('list-error');
                            $('#add_task_li').addClass('list-error');
                            $('.task_name').val('');
                            $('.task_name').prop('disabled', false);
                        } else if (res == 'not allowed') {
                            alert('You are not allowed to add items in this list!');
                            $('.task_name').val('');
                            $('.task_name').prop('disabled', false);
                        } else {
                            var opct = '';
                            var opct_cnt = 0;
                            $('#test_table').find('tbody').find('.icon-more-holder').find('.icon-more ').each(function (){
                                if($(this).css('opacity') == 1) {
                                    opct_cnt++;
                                    
                                }
                            });
                            if(opct_cnt > 0){
                                opct = 'opacity: 1;';
                            }
                            var resp = JSON.parse(res);
                            var task_ids = JSON.parse(resp[1]);
                            var val_store = $('.task_name').first().val();
                            var url = val_store.match(/(((ftp|https?):\/\/)[\-\w@:%_\+.~#?,&\/\/=]+)|((mailto:)?[_.\w-]+@([\w][\w\-]+\.)+[a-zA-Z]{2,3})/g);
                            var val_url = '';
                            var val_txt = '';
                            if (isAnchor(val_store.replace(/&lt;/g, "<").replace(/&gt;/g, ">"))) {
                                val_store = val_store.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
                                val_url = '';
                                val_txt = val_store.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
                            } else {
                                if (url != null) {
                                    $.each(url, function (i, v) {
                                        val_url = v;
                                        val_txt = val_store.replace(v, '');
                                        val_store = val_store.replace(v, '<a class="link_clickable" href="' + v + '">' + v + '</a>');
                                    });
                                }
                            }
                            var attend_class= '';
                            if($('#config_lnk').attr('data-typeid') == 11){
                                attend_class = ' attendance_list_class';
                            }
                            var td_val = '<tr>';
                            var total_th = $('.rank_th').length;
                            $('#delete_list_builder').attr('data-id', resp[2]);
                            $('#copy_list_btn').attr('data-id', resp[2]);
                            
                            td_val += '<td class="icon-more-holder add_icon_holder" data-order="' + resp[0] + '" data-listid="' + resp[2] + '" data-taskname="' + val_txt + val_url + '"><span class="icon-more ui-sortable-handle' + attend_class + '" style="' + opct + '"></span>';
                            td_val += '<a href="javascript:void(0)" class="icon-cross-out delete_task custom_cursor" data-id="' + task_ids[0] + '" data-task="' + val_txt + val_url + '" data-listid="' + resp[2] + '" style="' + opct +'"></a>';
                            if ($('.icon-settings').attr('data-typeid') == 5) {
                                td_val += '<input type="checkbox" class="complete_task custom_cursor" id="complete_' + task_ids[0] + '" data-id="' + task_ids[0] + '" data-listid="' + resp[2] + '" />';
                                td_val += ' <label for="complete_' + task_ids[0] + '" class="complete_lbl"> </label>';
                            } else if ($('.icon-settings').attr('data-typeid') == 11) {
                                td_val += '<input type="checkbox" class="present_task custom_cursor" id="present_' + task_ids[0] + '" data-id="' + task_ids[0] + '" data-listid="' + resp[2] + '" />';
                                td_val += ' <label for="present_' + task_ids[0] + '" class="present_lbl"> </label>';
                            }
                            td_val += '</td>';
                            if ($('.rank_th_head').length > 0) {
                                td_val += '<td class="rank_th">' + (total_th + 1) + '</td>'
                            }
                            var hidden_time_class = '';
                            var hidden_comment_class = '';
                            if ($('.icon-settings').attr('data-typeid') == 11) {
                                if ($('#listConfig_lnk').attr('data-showtime') == 0) {
                                    hidden_time_class = ' hidden_nodrag';
                                }
                            } else {
                                hidden_time_class = ' hidden_nodrag';
                                hidden_comment_class = ' hidden_nodrag';
                            }

                            var json_cnt = 0;


                            $('.task_name').each(function () {
                                var valuse_store = $(this).val();
                                var display_val = $(this).val();
                                display_val = display_val.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                                url = display_val.match(/(((ftp|https?):\/\/)[\-\w@:%_\+.~#?,&\/\/=]+)|((mailto:)?[_.\w-]+@([\w][\w\-]+\.)+[a-zA-Z]{2,3})/g);
//                                url = display_val.match(/(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)/g);
                                if (url == 'null' || url == null || url == 'undefined') {
                                    url = display_val.match(/([a-zA-Z0-9]+(\.[a-zA-Z0-9]{2,3})+.*)$/g);
                                }
                                var display_url = '';
                                var display_txt = '';
                                var title_text = '';

                                if (isAnchor(display_val.replace(/&lt;/g, "<").replace(/&gt;/g, ">"))) {
                                    display_val = display_val.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
                                    display_url = '';
                                    display_txt = display_val.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
                                } else {
                                    if (url != null && url != 'null' && url != 'undefined' && url != '') {
                                        $.each(url, function (i, v) {
                                            v = v.split(' ')[0];
                                            display_url = v;
                                            
                                            title_text = display_val.replace(v, '|url|');
                                            display_txt = display_val.replace(v, '');
                                            var url_http = v;
                                            if(v.indexOf('http://') != 0){
                                                if(v.indexOf('https://') != 0){
                                                    url_http = 'http://' + v;
                                                }
                                            }
                                            display_val = display_val.replace(v, '<a class="link_clickable" href="' + url_http + '">' + v + '</a>');
                                            title_text = title_text.replace('|url|', v);
                                        });
                                    }
                                }

                                td_val += '<td class="list-table-view">';
                                td_val += '<div class="add-data-div edit_task" data-id="' + task_ids[json_cnt] + '" data-task="' + display_txt + display_url + '" data-listid="' + resp[2] + '" data-toggle="tooltip" data-placement="top" title="' + title_text + '">';

                                td_val += '<span id="span_task_' + task_ids[json_cnt] + '" class="task_name_span">' + display_val + '</span>';

                                td_val += '<div class="opertaions pull-right">';

                                td_val += '</div>';
                                td_val += '</div>';
//                                if (url != null && url != 'null' && url != 'undefined' && url != '') {
//                                    td_val += '<a class="link_clickable" href="' + display_url + '" target="__blank">';
//                                    td_val += '<img class="preview_image" src="data:image/jpeg;base64,' + resp['previews'][task_ids[json_cnt]] + '">';
//                                    td_val += '</a>';
//                                }
                                td_val += '</td>';
                                json_cnt++;
                            });
                            td_val += '<td class="list-table-view-attend' + hidden_comment_class + '">';
                            td_val += '<div class="add-comment-div edit_comment" data-id="' + resp['extra_id'] + '" data-listid="' + resp[2] + '" data-toggle="tooltip" data-placement="top" title="">';
                            td_val += '<span id="span_comment_' + resp['extra_id'] + '" class="comment_name_span">&nbsp;</span>';
                            td_val += '</div>';
                            td_val += '</td>';

                            td_val += '<td class="list-table-view-attend' + hidden_time_class + '">';
                            td_val += '<div class="add-date-div check_date" data-id="' + resp['extra_id'] + '" data-listid="' + resp[3] + '" data-toggle="tooltip" data-placement="top" title="">';
                            td_val += '<span id="span_time_' + resp['extra_id'] + '" class="time_name_span">&nbsp;</span>';
                            td_val += '</div>';
                            td_val += '</td>';

                            td_val += '</tr>';
                            if ($('.icon-settings').attr('data-typeid') == 5) {
                                if ($('.complete_task:checked').length > 0) {
                                    $('.complete_task:checked:first').parent().parent().before(td_val);
                                } else {
                                    $('#test_table tbody').append(td_val);
                                }
                            } else {
                                $('#test_table tbody').append(td_val);
                            }


                            if ($('#test_table tbody tr').length == 1) {

                                $('.whoisnext-div .nexup-group .nexup-group-two .button-outer').removeClass('whosnext_img_bg');
                                var next_elem_find = $('#test_table tbody tr:eq(0) td.list-table-view:eq(0) .add-data-div .task_name_span').text();

                                var first_elem = '';
                                if ($('#config_lnk').attr('data-typeid') == 2) {
                                    first_elem += '<div class="nexup-sub-group nexup-sub-group-one" data-toggle="tooltip" title="' + $.trim(next_elem_find) + '">';
                                    first_elem += '<span id="next_task_name">' + $.trim(next_elem_find) + '</span>';
                                    first_elem += '</div>';
                                } else if ($('#config_lnk').attr('data-typeid') == 8) {
                                    first_elem += '<div class="nexup-sub-group nexup-sub-group-single" data-toggle="tooltip" title="' + $.trim(next_elem_find) + '">';
                                    first_elem += '<span id="next_task_name">' + $.trim(next_elem_find) + '</span>';
                                    first_elem += '</div>';
                                }


                                var total_cols = $('#test_table tbody tr:first-child td .add-data-div').length;
                                if (total_cols >= 1) {

                                    var index_row = $(next_elem_find).parent('td').parent().index();
                                    var loop_max = (total_cols - 1);
                                    if ($('#config_lnk').attr('data-typeid') == 3) {
                                        var loop_max = total_cols;
                                    }
                                    if (total_cols > 3) {
                                        var loop_max = 3;
                                    }
                                    if ($('#test_table tbody tr:first-child td .edit_task').length > 1) {

                                        first_elem += '<div class="nexup-sub-group nexup-sub-group-two">';
                                        for (j = 0; j < loop_max; j++) {
                                            if (index_row < 0) {
                                                index_row = 0;
                                            }
                                            first_elem += '<span data-toggle="tooltip" title="' + $.trim($('#test_table tbody tr:nth-child(' + (index_row + 1) + ') td:eq(' + (j + 2) + ') .add-data-div .task_name_span').text()) + '">';
                                            first_elem += $.trim($('#test_table tbody tr:nth-child(' + (index_row + 1) + ') td:eq(' + (j + 2) + ') .add-data-div .task_name_span').text());
                                            first_elem += '</span>';
                                        }
                                        first_elem += '<p data-toggle="modal" data-target="#items-list" id="items_model_p" data-rowid="' + (index_row + 1) + '"><img src="<?php echo base_url() . 'assets/img/information-button-icon-23.png'; ?>"></p>';
                                        first_elem += '</div>';
                                    }
                                }


                                $('.whoisnext-div .nexup-group .nexup-group-two .button-outer').html(first_elem);

                                var next_task = $(".whoisnext-div .nexup-group .nexup-group-two .button-outer .nexup-sub-group-one #next_task_name");
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
                            }

                            if (resp[3] != 'undefined') {
                                $('#delete_list_builder').show();
                                $('#delete_list_builder').removeClass('disabled_btn');
                                $('.plus-category').attr('data-access', 1);
                            }

                            var list_added_name = $('#edit_list_name').val();
                            $('#edit_list_name').remove();
                            $('#listname_0').attr('data-id', resp[2]);
                            $('#listname_0').attr('data-slug', resp[3]);
                            $('#listname_0').addClass('listname_' + resp[2]);
                            $('#listname_0').removeClass('listname_0');
                            $('#listname_0').show();
                            $('#listname_0').attr('id', 'listname_' + resp[2]);
                            $('#share_list').show();
                            if (typeof resp[3] != 'undefined') {
                                var share_url = '<?php echo base_url() . 'list/'; ?>' + resp[3];
                                $('#import_contacts').text(share_url);
                                $('#import_contacts').attr('href', share_url);
                            }
                            $('#share_btn').attr('data-id', resp['list_inflo_id']);
                            $('#task_name').attr('data-listid', resp[2]);
                            $('.list_type_cls').attr('data-listid', resp[2]);
                            $('#listLock_lnk').attr('data-listid', resp[2]);
                            $('#save_config').attr('data-listid', resp[2]);
                            $('.edit_list_cls').hide();
                            if ($('.inflologinlink').length == 0) {
                                $('#customize_btn').removeClass('hide_custom_btn');
                            }

                            if (list_id == '0') {
                                $('.plus-category').attr('data-access', '1');
                                if($('#config_icons').hasClass('no-pointer-icons')){
                                    $('#config_icons').removeClass('no-pointer-icons');
                                }
                                list_id = resp[2];
                                $('#save_col').attr('data-listid', resp[2]);
                            }
                            if ($('.add-data-head-r').hasClass('hidden_add_column_btn')) {
                                $('.add-data-head-r').removeClass('hidden_add_column_btn')
                            }
                            if ($('#add_bulk_data').hasClass('hdn_bulk')) {
                                $('#add_bulk_data').removeClass('hdn_bulk');
                            }
                            if ($('#add_data_desc').hasClass('hdn_desc')) {
                                $('#add_data_desc').removeClass('hdn_desc');
                            }
                            if ($('.icon-settings').attr('data-typeid') == 11) {
                                $('#yes_cnt').text(resp['total_yes']);
                                $('#maybe_cnt').text(resp['total_maybe']);
                                $('#no_cnt').text(resp['total_no']);
                                $('#blank_cnt').text(resp['total_blank']);
                            }
                            if (list_id == 0) {
                                $('.heading_items_col_add:eq(0)').attr('data-listid', resp[2]);
                                $('.heading_items_col_add:eq(0)').attr('data-colid', resp['col_id']);
                                $('#save_col').attr('data-listid', resp[2]);
                            }
                            if ($('.edit_task').length > 0) {
                                $('.add-data-head-r').show();
                            }
                            if (list_id == 0) {
                                $('#task_name').attr('placeholder', 'Add ' + list_name);
                            }
                            var type_id = $('#config_lnk').attr('data-typeid');
                            
                            $.ajax({
                                url: '<?php echo base_url() . 'listing/get_list_body'; ?>',
                                type: 'POST',
                                data: {
                                    'Listid': list_id,
                                },
                                success: function (res) {
                                    var resp = JSON.parse(res);
                                    $('#test_table tbody').html(resp['body']);
//                                    $('#test_table tbody').append(resp);

                                    if (type_id == 11) {
                                        $("#test_table tbody").sortable("disable");
                                    } else {
                                        $("#test_table tbody").sortable("enable");
                                    }
//                                    $("#test_table tbody").sortable("disable");
//                                    $("#test_table tbody").sortable("enable");
                                    if($('#listConfig_lnk').attr('data-moveallow') == 0){
                                        $("#test_table tbody").sortable("disable");
                                    }
                                }
                            });
                        }

                        $('.task_name:eq(0)').focus();
                        var total_nexup_span = $('.whoisnext-div .nexup-group .nexup-group-two .nexup-sub-group-two span').length;
//                        if (total_nexup_span < 3) {
//                            var task_span = '<span>' + $('.task_name:first-child').val() + '</span>';
//                            $('#items_model_p').before(task_span);
//                        }
                        $('.task_name').val('');
                        if (resp['col_id'] != 'undefined') {
                            $('#task_name:eq(0)').attr('data-colid', resp['col_id']);
                        }
//                        if ($('#test_table').width() > 1200) {
//                            $('#TaskListDiv .my_table').removeClass('my_scroll_table');
//                            $('#addTaskDiv').mCustomScrollbar("destroy");
//                            $("#addTaskDiv").mCustomScrollbar({
//                                axis: "x",
//                                scrollButtons: {enable: true},
//                                theme: "3d",
//                                scrollbarPosition: "outside",
//                                advanced: {
//                                    updateOnContentResize: true,
//                                    updateOnBrowserResize: true,
//                                    autoExpandHorizontalScroll: true,
//                                },
//                                mouseWheel:{enable: false},
//                            });
//                        } else {
//                            $('#my_table').mCustomScrollbar("destroy");
//                            $('#TaskListDiv .my_table').addClass('my_scroll_table');
//                        }
                        if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                            $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                event.preventDefault()
                            }).tooltip({delay: { "hide": 100 }});
                        }
                        $(document).on('click', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                            $('.tooltip').remove();
                        })
                    },
                    error: function (textStatus, errorThrown) {
                        alert('something went wrong. Items were not added. Plese try again!');
                        $('.task_name').prop('disabled', false);
                        $('.delete_task').prop('disabled', false);
                    },
                    complete: function (data) {
                        $('.task_name').prop('disabled', false);
                        $('.delete_task').prop('disabled', false);
                    }
                });
                setTimeout(function () {
                    $('.delete_task').prop('disabled', false);
                }, 5000);
            }
            
            function add_item_new_sub(list_id, list_name, task_data, col_id) {
                $('#test_table_' + list_id).find('.delete_task').prop('disabled', true);
                $.ajax({
                    url: "<?php echo base_url() . 'item/add'; ?>",
                    async : false,
                    type: 'POST',
                    context: this,
                    data: {
                        'list_id': list_id,
                        'list_name': list_name,
                        'task_data': task_data,
//                            'task_name': $(this).val(),
                        'col_id': col_id
                    },
                    success: function (res) {
                        $('.task_name').prop('disabled', false);
                        if (res == 'existing') {
                            $('.add_task_cls').text('This task already exist. Please try different name!');
                            $('.add_task_cls').show();
                            $('#add_task_li').removeClass('list-error');
                            $('#add_task_li').addClass('list-error');
                            $('.task_name').val('');
                            $('.task_name').prop('disabled', false);
                        } else if (res == 'fail') {
                            $('#task_name').val('');
                            $('.add_task_cls').text('Something went wrong. Your task was not added. Please try again later!');
                            $('.add_task_cls').show();
                            $('#add_task_li').removeClass('list-error');
                            $('#add_task_li').addClass('list-error');
                            $('.task_name').val('');
                            $('.task_name').prop('disabled', false);
                        } else if (res == 'empty') {
                            $('.add_task_cls').text('Please enter task name!');
                            $('.add_task_cls').show();
                            $('#add_task_li').removeClass('list-error');
                            $('#add_task_li').addClass('list-error');
                            $('.task_name').val('');
                            $('.task_name').prop('disabled', false);
                        } else if (res == 'not allowed') {
                            alert('You are not allowed to add items in this list!');
                            $('#test_table_' + list_id).find('.task_name').val('');
                            $('#test_table_' + list_id).find('.task_name').prop('disabled', false);
                        } else {
                            
                            $('#tabs-' + list_id).find('.add-data-head-r').removeAttr('style');
                            var opct = '';
                            var opct_cnt = 0;
                            $('#test_table_' + list_id).find('tbody').find('.icon-more-holder').find('.icon-more ').each(function (){
                                if($(this).css('opacity') == 1) {
                                    opct_cnt++;
                                    
                                }
                            });
                            if(opct_cnt > 0){
                                opct = 'opacity: 1;';
                            }
                            var resp = JSON.parse(res);
                            var task_ids = JSON.parse(resp[1]);
                            var val_store = $('#test_table_' + list_id).find('.task_sub_name').first().val();
                            var url = val_store.match(/(((ftp|https?):\/\/)[\-\w@:%_\+.~#?,&\/\/=]+)|((mailto:)?[_.\w-]+@([\w][\w\-]+\.)+[a-zA-Z]{2,3})/g);
                            var val_url = '';
                            var val_txt = '';
                            if (isAnchor(val_store.replace(/&lt;/g, "<").replace(/&gt;/g, ">"))) {
                                val_store = val_store.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
                                val_url = '';
                                val_txt = val_store.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
                            } else {
                                if (url != null) {
                                    $.each(url, function (i, v) {
                                        val_url = v;
                                        val_txt = val_store.replace(v, '');
                                        val_store = val_store.replace(v, '<a class="link_clickable" href="' + v + '">' + v + '</a>');
                                    });
                                }
                            }
                            
                            var attend_class= '';
                            if($('#tabs-' + list_id).find('#config_lnk_sub').attr('data-typeid') == 11){
                                attend_class = ' attendance_list_class';
                            }
                            var td_val = '<tr>';
                            var total_th = $('#test_table_' + list_id).find('.rank_th').length;
                            $('#test_table_' + list_id).find('#delete_sub_list_builder').attr('data-id', resp[2]);
                            $('#test_table_' + list_id).find('#copy_sub_list_btn').attr('data-id', resp[2]);
                            
                            td_val += '<td class="icon-more-holder add_icon_holder" data-order="' + resp[0] + '" data-listid="' + resp[2] + '" data-taskname="' + val_txt + val_url + '"><span class="icon-more ui-sortable-handle' + attend_class + '" style="' + opct + '"></span>';
                            td_val += '<a href="javascript:void(0)" class="icon-cross-out delete_task custom_cursor" data-id="' + task_ids[0] + '" data-task="' + val_txt + val_url + '" data-listid="' + resp[2] + '" style="' + opct +'"></a>';
                            if ($('#tabs-' + list_id).find('.icon-settings-sub').attr('data-typeid') == 5) {
                                td_val += '<input type="checkbox" class="complete_task custom_cursor" id="complete_' + task_ids[0] + '" data-id="' + task_ids[0] + '" data-listid="' + resp[2] + '" />';
                                td_val += ' <label for="complete_' + task_ids[0] + '" class="complete_lbl"> </label>';
                            } else if ($('#content_' + list_id).find('.icon-settings-sub').attr('data-typeid') == 11) {
                                td_val += '<input type="checkbox" class="present_task custom_cursor" id="present_' + task_ids[0] + '" data-id="' + task_ids[0] + '" data-listid="' + resp[2] + '" />';
                                td_val += ' <label for="present_' + task_ids[0] + '" class="present_lbl"> </label>';
                            }
                            td_val += '</td>';
                            if ($('#test_table_' + list_id).find('.rank_th_head').length > 0) {
                                td_val += '<td class="rank_th">' + (total_th + 1) + '</td>'
                            }
                            var hidden_time_class = '';
                            var hidden_comment_class = '';
                            if ($('#tabs-' + list_id).find('.icon-settings-sub').attr('data-typeid') == 11) {
                                if ($('#tabs-' + list_id).find('#listConfig_lnk_' + list_id).attr('data-showtime') == 0) {
                                    hidden_time_class = ' hidden_nodrag';
                                }
                            } else {
                                hidden_time_class = ' hidden_nodrag';
                                hidden_comment_class = ' hidden_nodrag';
                            }

                            var json_cnt = 0;


//                            $('#test_table_' + list_id).find('.task_sub_name').each(function () {
//                                var valuse_store = $(this).val();
//                                var display_val = $(this).val();
//                                display_val = display_val.replace(/</g, "&lt;").replace(/>/g, "&gt;");
//                                url = display_val.match(/(((ftp|https?):\/\/)[\-\w@:%_\+.~#?,&\/\/=]+)|((mailto:)?[_.\w-]+@([\w][\w\-]+\.)+[a-zA-Z]{2,3})/g);
//                                if (url == 'null' || url == null || url == 'undefined') {
//                                    url = display_val.match(/([a-zA-Z0-9]+(\.[a-zA-Z0-9]{2,3})+.*)$/g);
//                                }
//                                var display_url = '';
//                                var display_txt = '';
//                                var title_text = '';
//
//                                if (isAnchor(display_val.replace(/&lt;/g, "<").replace(/&gt;/g, ">"))) {
//                                    display_val = display_val.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
//                                    display_url = '';
//                                    display_txt = display_val.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
//                                } else {
//                                    if (url != null && url != 'null' && url != 'undefined' && url != '') {
//                                        $.each(url, function (i, v) {
//                                            v = v.split(' ')[0];
//                                            display_url = v;
//                                            
//                                            title_text = display_val.replace(v, '|url|');
//                                            display_txt = display_val.replace(v, '');
//                                            var url_http = v;
//                                            if(v.indexOf('http://') != 0){
//                                                if(v.indexOf('https://') != 0){
//                                                    url_http = 'http://' + v;
//                                                }
//                                            }
//                                            display_val = display_val.replace(v, '<a class="link_clickable" href="' + url_http + '">' + v + '</a>');
//                                            title_text = title_text.replace('|url|', v);
//                                        });
//                                    }
//                                }
//                                            
//
//                                td_val += '<td class="list-table-view">';
//                                td_val += '<div class="add-data-div edit_task" data-id="' + task_ids[json_cnt] + '" data-task="' + display_txt + display_url + '" data-listid="' + resp[2] + '" data-toggle="tooltip" data-placement="top" title="' + title_text + '">';
//                                if(display_val == 'checked'){
//                                    display_val = '';
//                                }
//                                td_val += '<span id="span_task_' + task_ids[json_cnt] + '" class="task_name_span">' + display_val + '</span>';
//
//                                td_val += '<div class="opertaions pull-right">';
//
//                                td_val += '</div>';
//                                td_val += '</div>';
//                                td_val += '</td>';
//                                json_cnt++;
//                            });
                            td_val += '<td class="list-table-view-attend' + hidden_comment_class + '">';
                            td_val += '<div class="add-comment-div edit_comment" data-id="' + resp['extra_id'] + '" data-listid="' + resp[2] + '" data-toggle="tooltip" data-placement="top" title="">';
                            td_val += '<span id="span_comment_' + resp['extra_id'] + '" class="comment_name_span">&nbsp;</span>';
                            td_val += '</div>';
                            td_val += '</td>';

                            td_val += '<td class="list-table-view-attend' + hidden_time_class + '">';
                            td_val += '<div class="add-date-div check_date" data-id="' + resp['extra_id'] + '" data-listid="' + resp[3] + '" data-toggle="tooltip" data-placement="top" title="">';
                            td_val += '<span id="span_time_' + resp['extra_id'] + '" class="time_name_span">&nbsp;</span>';
                            td_val += '</div>';
                            td_val += '</td>';

                            td_val += '</tr>';
                            if ($('#tabs-' + list_id).find('.icon-settings-sub').attr('data-typeid') == 5) {
                                if ($('#test_table_' + list_id).find('.complete_task:checked').length > 0) {
                                    $('#test_table_' + list_id).find('.complete_task:checked:first').parent().parent().before(td_val);
                                } else {
                                    $('#test_table_' + list_id).find('tbody').append(td_val);
                                }
                            } else {
                                $('#test_table_' + list_id).find('tbody').append(td_val);
                            }

                            if ($('#test_table_' + list_id).find('tbody tr').length == 1) {
                                $('#tabs-' + list_id).find('.whoisnext-div .nexup-group .nexup-group-two .button-outer').removeClass('whosnext_img_bg');
                                var next_elem_find = $('#test_table_' + list_id).find('tbody tr:eq(0) td.list-table-view:eq(0) .add-data-div .task_name_span').text();

                                var first_elem = '';
                                if ($('#tabs-' + list_id).find('#config_lnk_sub').attr('data-typeid') == 2) {
                                    first_elem += '<div class="nexup-sub-group nexup-sub-group-one" data-toggle="tooltip" title="' + $.trim(next_elem_find) + '">';
                                    first_elem += '<span id="next_task_name">' + $.trim(next_elem_find) + '</span>';
                                    first_elem += '</div>';
                                } else if ($('#tabs-' + list_id).find('#config_lnk_sub').attr('data-typeid') == 8) {
                                    first_elem += '<div class="nexup-sub-group nexup-sub-group-single" data-toggle="tooltip" title="' + $.trim(next_elem_find) + '">';
                                    first_elem += '<span id="next_task_name">' + $.trim(next_elem_find) + '</span>';
                                    first_elem += '</div>';
                                }
                                

                                var total_cols = $('#test_table_' + list_id).find('tbody tr:first-child td .add-data-div').length;
                                
                                if (total_cols >= 1) {
                                    var index_row = $('#test_table_' + list_id).find('tbody tr:eq(0) td.list-table-view:eq(0) .add-data-div .task_name_span').parent('td').parent().index();
                                    var loop_max = (total_cols - 1);
                                    if ($('#tabs-' + list_id).find('#config_lnk_sub').attr('data-typeid') == 3) {
                                        var loop_max = total_cols;
                                    }
                                    if (total_cols > 3) {
                                        var loop_max = 3;
                                    }
                                    if ($('#test_table_' + list_id).find('tbody tr:first-child td .edit_task').length > 1) {

                                        first_elem += '<div class="nexup-sub-group nexup-sub-group-two">';
                                        for (j = 0; j < loop_max; j++) {
                                            if (index_row < 0) {
                                                index_row = 0;
                                            }
                                            first_elem += '<span data-toggle="tooltip" title="' + $.trim($('#test_table_' + list_id).find('tbody tr:nth-child(' + (index_row + 1) + ') td:eq(' + (j + 2) + ') .add-data-div .task_name_span').text()) + '">';
                                            first_elem += $.trim($('#test_table_' + list_id).find('tbody tr:nth-child(' + (index_row + 1) + ') td:eq(' + (j + 2) + ') .add-data-div .task_name_span').text());
                                            first_elem += '</span>';
                                        }
                                        first_elem += '<p data-toggle="modal" data-target="#items-list" id="items_model_p" data-rowid="' + (index_row + 1) + '"><img src="<?php echo base_url() . 'assets/img/information-button-icon-23.png'; ?>"></p>';
                                        first_elem += '</div>';
                                    }
                                }
                                
                                


                                $('#tabs-' + list_id).find('.whoisnext-div .nexup-group .nexup-group-two .button-outer').html(first_elem);
                                var next_task = $('#tabs-' + list_id).find(".whoisnext-div .nexup-group .nexup-group-two .button-outer .nexup-sub-group-one #next_task_name");
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
                            }

                            
                            if ($('#tabs-' + list_id).find('.add-data-head-r').hasClass('hidden_add_column_btn')) {
                                $('#tabs-' + list_id).find('.add-data-head-r').removeClass('hidden_add_column_btn')
                            }
                            if ($('#tabs-' + list_id).find('#add_bulk_sub_data').hasClass('hdn_bulk')) {
                                $('#tabs-' + list_id).find('#add_bulk_sub_data').removeClass('hdn_bulk');
                            }
                            if ($('#tabs-' + list_id).find('#add_sub_data_desc').hasClass('hdn_desc')) {
                                $('#tabs-' + list_id).find('#add_sub_data_desc').removeClass('hdn_desc');
                            }
                            if ($('#tabs-' + list_id).find('.icon-settings-sub').attr('data-typeid') == 11) {
                                $('#tabs-' + list_id).find('#yes_cnt').text(resp['total_yes']);
                                $('#tabs-' + list_id).find('#maybe_cnt').text(resp['total_maybe']);
                                $('#tabs-' + list_id).find('#no_cnt').text(resp['total_no']);
                                $('#tabs-' + list_id).find('#blank_cnt').text(resp['total_blank']);
                            }
                            if (list_id == 0) {
                                $('#test_table_' + list_id).find('.heading_items_col_add:eq(0)').attr('data-listid', resp[2]);
                                $('#test_table_' + list_id).find('.heading_items_col_add:eq(0)').attr('data-colid', resp['col_id']);
                                //$('#save_col_sub').attr('data-listid', resp[2]);
                            }
                            if ($('#test_table_' + list_id).find('.edit_task').length > 0) {
                                $('#test_table_' + list_id).find('.add-data-head-r').show();
                            }
                            var type_id = $('#tabs-' + list_id).find('#config_lnk_sub').attr('data-typeid');
                            
                            $.ajax({
                                url: '<?php echo base_url() . 'listing/get_list_body'; ?>',
                                type: 'POST',
                                data: {
                                    'Listid': list_id,
                                },
                                success: function (res) {
                                    var resp = JSON.parse(res);
                                    $('#test_table_' + list_id).find('tbody').html(resp['body']);
                                    if ($('#test_table_' + list_id).find("tbody").hasClass('ui-sortable')){
                                        if (type_id == 11) {
                                            $('#test_table_' + list_id).find("tbody").sortable("disable");
                                        } else {
                                            $('#test_table_' + list_id).find("tbody").sortable("enable");
                                        }
                                        if($('#listConfig_lnk_' + list_id).attr('data-moveallow') == 0){
                                            $('#test_table_' + list_id).find("tbody").sortable("disable");
                                        }
                                    }else{
                                        $('#test_table_' + list_id).find("tbody").sortable({
                                            handle: '.icon-more',
                                            connectWith: ".add-data-div.edit_task",
                                            axis: "y",
                                            tolerance: 'pointer',
                                            scroll: true,
                                            animation: 100,
                                            revert: 100,
                                            stop:function( event, ui ) {
                                                $('#tabs-' + list_id).find('.my_table').removeClass('my_scroll_table_no_overflow');
                                                $('#tabs-' + list_id).find('.my_table').addClass('my_scroll_table');
                                            },
                                            update: function (event, ui) {
                                                if ($('#test_table_' + list_id).find('.rank_th').length > 0) {
                                                    var rnk_val = 1;
                                                    $('#test_table_' + list_id).find('.rank_th').each(function () {
                                                        $(this).text(rnk_val);
                                                        rnk_val++;
                                                    });
                                                }
                                                var tasks_orders = [];
                                                $('#test_table_' + list_id).find('tbody tr td.icon-more-holder').each(function (e) {
                                                    var orders = $(this).attr('data-order');
                                                    tasks_orders.push(orders);
                                                });

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
                                                            if ($('#test_table_' + list_id).find('.icon-more-holder').length > 0) {
                                                                var ord_val = 1;
                                                                $('#test_table_' + list_id).find('.icon-more-holder').each(function () {
                                                                    $(this).attr('data-order', ord_val);
                                                                    ord_val++;
                                                                });
                                                            }

                                                            var total_rows = $('#test_table_' + list_id).find('tbody tr').length;
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
                                                            $('#test_table_' + list_id).find("tbody").sortable('cancel');
                                                        }
                                                    }
                                                });
                                            }
                                        });
                                    }
                                        
                                }
                            });
                            $('#test_table_' + list_id).find('.task_sub_name').val('');
                            $('#test_table_' + list_id + ' thead tr:eq(0) th.heading_items_col_add:eq(0) .task_sub_name:eq(0)').focus();
                        }

                        $('#test_table_' + list_id).find('.task_name:eq(0)').focus();
                        var total_nexup_span = $('#content_' + list_id).find('.whoisnext-div .nexup-group .nexup-group-two .nexup-sub-group-two span').length;
                        $('#test_table_' + list_id).find('.task_name').val('');
                        $('.check_all_check_box').prop('checked', false);
                        if (resp['col_id'] != 'undefined') {
                            $('#test_table_' + list_id).find('#task_name:eq(0)').attr('data-colid', resp['col_id']);
                        }
                        if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                            $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                event.preventDefault()
                            }).tooltip({delay: { "hide": 100 }});
                        }
                        $(document).on('click', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                            $('.tooltip').remove();
                        })
                    },
                    error: function (textStatus, errorThrown) {
                        alert('something went wrong. Items were not added. Plese try again!');
                        $('#test_table_' + list_id).find('.task_name').prop('disabled', false);
                        $('#test_table_' + list_id).find('.delete_task').prop('disabled', false);
                    },
                    complete: function (data) {
                        $('#test_table_' + list_id).find('.task_name').prop('disabled', false);
                        $('#test_table_' + list_id).find('.delete_task').prop('disabled', false);
                    }
                });
                setTimeout(function () {
                    $('#test_table_' + list_id).find('.delete_task').prop('disabled', false);
                }, 5000);
            }

            //Add task for a list
            $(document).on('keydown', '.task_name', function (evt) {
                var key_code = evt.keyCode;
                if (key_code == 13) {
                        $('.task_name').prop('disabled', true);
                        var list_id = $(this).attr('data-listid');
                        var col_id = $(this).attr('data-colid');
                        var list_name = '';
                        if (list_id == 0) {
                            if ($('#edit_list_name').length > 0) {
                                list_name = $('#edit_list_name').val();
                            } else {
                                list_name = $('.add-data-head h2').val();
                            }
                        }
                        $('#add_task_li').removeClass('list-error');
                        $('.add_task_cls').text('');
                        $('.add_task_cls').hide();
                        var task_data = [];
                        var column = '';
                        var list = '';
                        var val_cnt = 0;
                        $('.task_name').each(function () {
                            column = $(this).attr('data-colid');
                            list = $(this).attr('data-listid');
                            var task_name = $(this).val();
                            var value_save = ' ';
                            if ($(this).val().trim() != '') {
                                value_save = $(this).val().replace(/</g, "&lt;").replace(/>/g, "&gt;");
                            }
                            task_data.push({column: column, val: value_save});
                            if ($(this).val() != '') {
                                val_cnt++;
                            }
                        });
                        if (val_cnt <= 0) {
                            alert('Please enter atleast 1 item.');
                            $('.task_name').prop('disabled', false);
                            $('.task_name:eq(0)').focus();
                            return false;
                        }
                        add_item_new(list_id, list_name, task_data, col_id);
//                    } else {
//                        var parent_index = $(this).parent().parent().index();
//                        $('#test_table thead tr th:nth-child(' + (parent_index + 2) + ') .task_name').focus();
//                    }
                }
            });
            
            $(document).on('click', '.mail_url', function(){
                var my_email = $(this).attr('href');
                window.open(my_email);
                return false;
            });
            
            $(document).on('keydown', '.task_sub_name', function (evt) {
                var key_code = evt.keyCode;
                
                if($(this).attr('data-type') == 'currency' || $(this).attr('data-type') == 'number'){
                    $(this).val($(this).val().replace(/[^0-9\.]/g,''));
                    if ((evt.keyCode >= 48 && evt.keyCode <= 57) || 
                        (evt.keyCode >= 96 && evt.keyCode <= 105) || 
                        evt.keyCode == 8 || evt.keyCode == 9 || evt.keyCode == 37 ||
                        evt.keyCode == 39 || evt.keyCode == 46 || evt.keyCode == 110 || evt.keyCode == 190) {
                        if((evt.keyCode == 110 || evt.keyCode == 190) && $(this).val().indexOf('.') != -1){
                            evt.preventDefault();
                        }
                    } else {
                        evt.preventDefault();
                    }
                }
                
                var did_edit = $(this).attr('data-id');
                if(key_code == 27){
                    $(this).trigger('blur');
                }
                    
                if (key_code == 13) {
                    if (evt.ctrlKey) {
                        var start = window.getSelection().anchorOffset;
                        if (start != $(this).html().length) {
                            document.execCommand('insertText', false, '\n');
                            if (start == ($(this).val().length - 2)) {
                                $(this).scrollTop($(this)[0].scrollHeight);
                            }
                        } else {
                            document.execCommand('insertText', false, '\n ');
                            $(this).scrollTop($(this)[0].scrollHeight);
                        }
                        return false;
                    }
                    
                    if($(this).attr('id') == 'edit_task_name'){
                        $(this).trigger('blur');
                    }
                    
                    if($(this).val() != ''){
                        if($(this).attr('data-type') == 'number'){
                            if(!$.isNumeric($(this).val())){
                                    $(this).val('1');
                            }
                        } else if($(this).attr('data-type') == 'date'){
                            if(!isDate($(this).val())){
                                alert('Please enter a valid date');
                                $(this).val('');
                                return false;
                            }
                        } else if($(this).attr('data-type') == 'time'){
                            var time_regexp = /([01][0-9]|[02][0-3]):[0-5][0-9]/;
                            if(time_regexp.test($(this).val()) == false){
                                $(this).val('');
                            }
                        } else if($(this).attr('data-type') == 'datetime'){
                            var date_time_regexp = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4}) ([01][0-9]|[02][0-3]):[0-5][0-9]$/;
                            if(date_time_regexp.test($(this).val()) == false){
                                var time_regexp = /([01][0-9]|[02][0-3]):[0-5][0-9]/;
                                if(isDate($(this).val())){
                                    $(this).val($(this).val() + ' 00:00');
                                }else if(time_regexp.test($(this).val()) != false){
                                    var d = new Date();
                                    var month = d.getMonth()+1;
                                    var day = d.getDate();

                                    var c_date = ((''+day).length<2 ? '0' : '') + day + '/' + ((''+month).length<2 ? '0' : '') + month + '/' + d.getFullYear();

                                    $(this).val(c_date + ' ' + $(this).val());
                                }else{
                                    alert('Please enter a valid date and time');
                                    $(this).val('');
                                    return false;
                                }
                            }
                        } else if($(this).attr('data-type') == 'email'){
                            var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,}$/i;
                            if (!testEmail.test($(this).val())){
                                alert('Please enter a valid email');
                                $(this).val('');
                                return false;
                            }
                        } else if($(this).attr('data-type') == 'link'){
                            var testLink = /(((ftp|https?):\/\/)[\-\w@:%_\+.~#?,&\/\/=]+)|((mailto:)?[_.\w-]+@([\w][\w\-]+\.)+[a-zA-Z]{2,3})/g;
                            var url = '';
                            var is_url = 0;
                            if(!testLink.test($(this).val())){
                                var link_tst = /([a-zA-Z0-9]+(\.[a-zA-Z0-9]{2,3})+.*)$/g;
                                if(link_tst.test($(this).val())){
                                    url = 'http://' + $(this).val();
                                    is_url = 1;
                                }else{
                                    is_url = 0;
                                }
                            }else{
                                url = $(this).val();
                                is_url = 1;
                            }
                            if (is_url == 0){
                                alert('Please enter a valid link');
                                return false;
                            }
                        }
                    }
                    
                        
                        $('#tabs-' + list_id).find('.task_name').prop('disabled', true);
                        var list_id = $(this).attr('data-listid');
                        var col_id = $(this).attr('data-colid');
                        var list_name = '';
                        if (list_id == 0) {
                            if ($('#test_table_' + list_id).find('#edit_list_name').length > 0) {
                                list_name = $('#test_table_' + list_id).find('#edit_list_name').val();
                            } else {
                                list_name = $('#test_table_' + list_id).find('.add-data-head h2').val();
                            }
                        }
                        $('#test_table_' + list_id).find('#add_task_li').removeClass('list-error');
                        $('#test_table_' + list_id).find('.add_task_cls').text('');
                        $('#test_table_' + list_id).find('.add_task_cls').hide();
                        var task_data = [];
                        var column = '';
                        var list = '';
                        var val_cnt = 0;
                        $('#test_table_' + list_id).find('.task_sub_name').each(function () {
                            column = $(this).attr('data-colid');
                            list = $(this).attr('data-listid');
                            var task_name = $(this).val();
                            var value_save = ' ';
                            if ($(this).val().trim() != '') {
                                value_save = $(this).val().replace(/</g, "&lt;").replace(/>/g, "&gt;");
                            }
                            task_data.push({column: column, val: value_save});
                            if ($(this).val() != '') {
                                val_cnt++;
                            }
                        });
                        if($(this).attr('id') != 'edit_task_name'){
                            if (val_cnt <= 0) {
                                alert('Please enter atleast 1 item.');
                                $('#test_table_' + list_id).find('.task_name').prop('disabled', false);
                                $('.task_name:eq(0)').focus();
                                return false;
                            }
                        }
                        if($(this).attr('id') == 'task_name'){
                            add_item_new_sub(list_id, list_name, task_data, col_id);
                        }
                }
            });

            //Display edit/delete/complete task menu on mouse over of task
            $(document).on('mouseover', '.task_li', function () {
                $(this).find("div.opertaions").css('display', 'block');
            });

            //Hide edit/delete/complete task menu on mouse out of task
            $(document).on('mouseout', '.task_li', function () {
                $(this).find("div.opertaions").css('display', 'none');
            });

            $(document).on('click', '.list-body-dropdown>.icon-more:first-child', function (e) {
                if($('.ul_list_option_submenu.show_drop_down').hasClass('show_drop_down')){
                    $('.ul_list_option_submenu.show_drop_down').removeClass('show_drop_down');
                }
            });

            //Hide options from list/tasks when mouse clicked anywhere on page

            $(document).on('click', 'body', function (e) {
                if ($(e.target).attr('id') == 'edit_task_name' || $(e.target).attr('id') == 'edit_list_name' || $(e.target).attr('class') == 'icon-more' || $(e.target).attr('class') == 'icon-cross' || $(e.target).attr('id') == 'menu_directory' || $(e.target).attr('class') == 'dropdown-action') {
                    e.preventDefault();
                } else {
//                    $('.edit_list_cls').remove();
                        $('.list-body-box').removeClass('action-show');
//                    $('#edit_task_name').remove();
                    $('.edit_task_cls').remove();
//                    if ($('#edit_list_name').attr('data-id') > 0) {
//                        $('#edit_list_name').remove();
//                        $('h2.edit_list_task').show();
//                    }
                    $('.list-body-box a big').show();
                    $('.dropdown-action').show();
                    $('.add-data-div').removeClass('list-error');
                    $('.opertaions').removeClass('hide_operations');
//                    $('.task_name_span').show();
                }

                if ($(e.target).attr('class') == 'column_name_class' || $(e.target).attr('class') == 'add-data-title' || $(e.target).attr('class') == 'add-data-title' || $(e.target).attr('class') == 'edit_column_box' || $(e.target).attr('class') == 'remove_col' || $(e.target).attr('class') == 'move_col' || $(e.target).attr('class') == 'icon-cross') {
                    e.preventDefault();

                } else {
                    $('#edit_column_box').remove();
                    $('.column_name_class').show();
                }

                if ($(e.target).attr('id') == 'edit_comment' || $(e.target).attr('class') == 'list-table-view-attend' || $(e.target).attr('class') == 'add-comment-div' || $(e.target).attr('id') == 'edit_commetn_box' || $(e.target).attr('id') == 'edit_comment_text') {
                    e.preventDefault();
                } else {
                    $('.edit_commetn_box').remove();
//                    $('.comment_name_span').show();
                }
                
                if($(e.target).attr('class') != 'div_option_wrap' && $(e.target).attr('class') != 'ul_table_option' && $(e.target).attr('class') != 'custom_radio_class' && $(e.target).attr('class') != 'col_type_lbl' && $(e.target).attr('class') != 'radio-col-type' && $(e.target).attr('class') != 'plus_a' && $(e.target).attr('class') != 'minus_a' && $(e.target).attr('id') != 'number_rows'){
                    $('.icon_listing_table').css({transform: 'rotate(90deg)'});
                    $(document).find('.div_option_wrap[style="display: block;"]').toggle( "slow", function() {});
                }else{
                }

            });

            //Open modal of edit task and fill value in text box
            $(document).on('click', '.edit_task', function (e) {
                if($(this).attr('data-type') != 'checkbox' && $(this).attr('data-type') != 'timestamp'){
                    e.preventDefault();
                    var span_width = $(this).children('.task_name_span').width();
                    if (span_width < '230') {
                        span_width = '230';
                    }
                    if ($('.plus-category').attr('data-access') == 0) {
                        alert('You are not allowed to edit this item!');
                        return false;
                    }
                    if ($(this).has('#edit_task_name').length > 0) {
                        return false;
                    }
                    $(this).css('pointer-events', 'none');
                    $('#edit_task_name').remove();
                    $('#TaskList .task_li .add-data-div span').show();
                    $('.opertaions').removeClass('hide_operations');
                    var task_nm = $(this).attr('data-task');
                    var task_id = $(this).attr('data-id');
                    var list_id = $(this).attr('data-listid');

                    $.ajax({
                        url: "<?php echo base_url() . 'item/get_task_data'; ?>",
                        type: 'POST',
                        context: this,
                        data: {
                            'task_id': task_id
                        },
                        success: function (res) {
                            $(this).css('pointer-events', 'auto');
                            if (res == null || res == 'empty') {
                                alert('Please login to perform action!');
                                $(this).css('pointer-events', 'auto');
                                return false;
                            } else if (res == 'unauthorised') {
                                alert('You are not authorised to edit this list!');
                                $(this).css('pointer-events', 'auto');
                                return false;
                            } else if (res == 'not found') {
                                alert('Task you are looking for does not exist!');
                            } else if (res == 'not allowed') {
                                alert('You are not allowed to edit this task!');
                            } else {
                                var txt_edit_bx = '<input id="edit_task_name" name="edit_task_name" class="task_sub_name" value="" data-id="' + task_id + '" data-listid="' + list_id + '" placeholder="item" type="text" autocomplete="off"><span class="span_enter" id="edit_item_span_enter"><img src="/assets/img/enter.png" class="enter_img"></span>';
                                if($(this).attr('data-type') == 'memo'){
                                        txt_edit_bx = '<textarea name="edit_task_name" id="edit_task_name" class="task_sub_name" data-id="' + task_id + '" data-listid="' + list_id + '" placeholder="item" data-type="memo"></textarea><span class="span_enter" id="edit_item_span_enter"><img src="/assets/img/enter.png" class="enter_img"></span>';
                                    }
                                    if($(this).attr('data-type') == 'currency'){
                                        txt_edit_bx = '<input id="edit_task_name" name="edit_task_name" class="task_sub_name" value="" data-id="' + task_id + '" data-listid="' + list_id + '" placeholder="$" type="number" min="1" data-type="currency" autocomplete="off"><span class="span_enter" id="edit_item_span_enter"><img src="/assets/img/enter.png" class="enter_img"></span>';
                                    }
                                    if($(this).attr('data-type') == 'number'){
                                        txt_edit_bx = '<input id="edit_task_name" name="edit_task_name" class="task_sub_name" value="" data-id="' + task_id + '" data-listid="' + list_id + '" placeholder="123456" type="number" min="1" data-type="number" autocomplete="off"><span class="span_enter" id="edit_item_span_enter"><img src="/assets/img/enter.png" class="enter_img"></span>';
                                    }
                                    if($(this).attr('data-type') == 'date'){
                                        var placeholder = 'MM/DD/YYYY';
                                        txt_edit_bx = '<input id="edit_task_name" name="edit_task_name" class="task_sub_name" value="" data-id="' + task_id + '" data-listid="' + list_id + '" placeholder="' + placeholder + '" type="text" min="1" data-type="date" autocomplete="off"><span class="span_enter" id="edit_item_span_enter"><img src="/assets/img/enter.png" class="enter_img"></span>';
                                    }
                                    if($(this).attr('data-type') == 'time'){
                                        var placeholder = 'HH:MM';
                                        txt_edit_bx = '<input id="edit_task_name" name="edit_task_name" class="task_sub_name" value="" data-id="' + task_id + '" data-listid="' + list_id + '" placeholder="' + placeholder + '" type="text" min="1" data-type="time" autocomplete="off"><span class="span_enter" id="edit_item_span_enter"><img src="/assets/img/enter.png" class="enter_img"></span>';
                                    }
                                    if($(this).attr('data-type') == 'datetime'){
                                        var placeholder = 'MM/DD/YYYY HH:MM';
                                        txt_edit_bx = '<input id="edit_task_name" name="edit_task_name" class="task_sub_name" value="" data-id="' + task_id + '" data-listid="' + list_id + '" placeholder="' + placeholder + '" type="text" min="1" data-type="datetime" autocomplete="off"><span class="span_enter" id="edit_item_span_enter"><img src="/assets/img/enter.png" class="enter_img"></span>';
                                    }
                                    if($(this).attr('data-type') == 'email'){
                                        txt_edit_bx = '<input id="edit_task_name" name="edit_task_name" class="task_sub_name" value="" data-id="' + task_id + '" data-listid="' + list_id + '" placeholder="username@domain.com" type="text" data-type="email" autocomplete="off"><span class="span_enter" id="edit_item_span_enter"><img src="/assets/img/enter.png" class="enter_img"></span>';
                                    }
                                    if($(this).attr('data-type') == 'link'){
                                        txt_edit_bx = '<input id="edit_task_name" name="edit_task_name" class="task_sub_name" value="" data-id="' + task_id + '" data-listid="' + list_id + '" placeholder="http://example.com" type="text"data-type="link" autocomplete="off"><span class="span_enter" id="edit_item_span_enter"><img src="/assets/img/enter.png" class="enter_img"></span>';
                                    }
                                var err_msg = '<div class="edit_task_cls" style=""></div>';
                                $('#span_task_' + task_id).css('visibility', 'hidden');
                                $('#span_task_' + task_id).before(txt_edit_bx);
                                
                                if($('#edit_task_name[data-id="' + task_id + '"]').attr('data-type') == 'datetime'){
                                    $('#edit_task_name[data-id="' + task_id + '"]').datetimepicker({
                                        format: 'MM/DD/YYYY HH:mm',
                                        widgetParent: $('#edit_task_name[data-id="' + task_id + '"]').parent().parent(),
                                        widgetPositioning: {
                                            horizontal: 'auto',
                                            vertical: 'bottom'
                                        }
                                    });
                                } else if($('#edit_task_name[data-id="' + task_id + '"]').attr('data-type') == 'date'){
//                                    $('.my_table').height($('.my_table').height() + 300);
                                    $('#edit_task_name[data-id="' + task_id + '"]').datetimepicker({
                                        format: 'MM/DD/YYYY',
                                        widgetParent: $('#edit_task_name[data-id="' + task_id + '"]').parent().parent(),
                                        widgetPositioning: {
                                            horizontal: 'auto',
                                            vertical: 'bottom'
                                        }
                                    });
                                } else if($('#edit_task_name[data-id="' + task_id + '"]').attr('data-type') == 'time'){
                                    $('#edit_task_name[data-id="' + task_id + '"]').datetimepicker({
                                        format: 'HH:mm',
                                        widgetParent: $('#edit_task_name[data-id="' + task_id + '"]').parent().parent(),
                                        widgetPositioning: {
                                            horizontal: 'auto',
                                            vertical: 'bottom'
                                        }
                                    });
                                }
                                
                                
                                $('#edit_task_name').val(res.replace("&lt;", '<').replace("&gt;", '>'));
                                $('#task_' + task_id + ' .add-data-div').after(err_msg);
                                var el = $("#edit_task_name").get(0);
                                var elemLen = el.value.length;
                                if($(el).attr('data-type') != 'number'){
                                    el.selectionStart = elemLen;
                                    el.selectionEnd = elemLen;
                                }
                                $('#edit_task_name').focus();
                            }
                            $(this).css('pointer-events', 'auto');
                        }
                    });
                    return false;
                }
            });

            function isAnchor(str) {
                return /^\<a.*\>.*\<\/a\>/i.test(str);
            }

            function edit_tasks(list_id, did_edit, edit_task_name, row_indx, cell_indx) {
                var c_width = $('#test_table').width();
                $('.edit_task_cls').text('');
                $('.edit_task_cls').hide();
                $('#task_' + did_edit + ' .add-data-div').removeClass('list-error');
                var display_task_name = edit_task_name;
                if($('#test_table_' + list_id).find('#span_task_' + did_edit).closest('div.edit_task').attr('data-type') == 'text' || $('#test_table_' + list_id).find('#span_task_' + did_edit).closest('div.edit_task').attr('data-type') == 'memo'){
                    display_task_name = display_task_name.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                    var eml = display_task_name.match(/(\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*)/);
                    if (eml != 'null' && eml != null && eml != 'undefined') {
                        display_task_name = display_task_name.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
                        display_task_name = display_task_name.replace(/(\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*)/, "<a href=\"mailto:$1\">$1</a>")
                        
                    }else{
                        var url = display_task_name.match(/(((ftp|https?):\/\/)[\-\w@:%_\+.~#?,&\/\/=]+)|((mailto:)?[_.\w-]+@([\w][\w\-]+\.)+[a-zA-Z]{2,3})/g);
                        if (url == 'null' || url == null || url == 'undefined') {
                            url = display_task_name.match(/([a-zA-Z0-9]+(\.[a-zA-Z0-9]{2,3})+.*)$/g);
                        }

                        var display_url = '';
                        var display_txt = '';
                        var title_text = '';

                        if (isAnchor(display_task_name.replace(/&lt;/g, "<").replace(/&gt;/g, ">"))) {
                            display_task_name = display_task_name.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
                            display_url = '';
                            display_txt = display_task_name.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
                        } else {
                            if (url != null) {
                                $.each(url, function (i, v) {
                                    display_url = v.split(' ')[0];
                                    title_text = display_task_name.replace(v.split(' ')[0], '|url|');
                                    display_txt = display_task_name.replace(v.split(' ')[0], '');
                                    display_task_name = display_task_name.replace(v.split(' ')[0], '<a class="link_clickable" href="' + v.split(' ')[0] + '">' + v.split(' ')[0] + '</a>');
                                    title_text = title_text.replace('|url|', v.split(' ')[0]);
                                });
                            }
                        }
                    }
                    
                    
                }
                    
                $.ajax({
                    url: "<?php echo base_url() . 'item/update'; ?>",
                    type: 'POST',
                    data: {
                        'ListId': list_id,
                        'TaskId': did_edit,
                        'Taskname': edit_task_name.replace(/</g, "&lt;").replace(/>/g, "&gt;")
                    },
                    success: function (res) {
                        if (res == 'fail') {
                            $('.edit_task_cls').text('Something went wrong. Please try again!');
                            $('.edit_task_cls').show();
                        } else {
                            if (res != 'success') {
                            } else if (!$.trim(res)) {
                                $('#span_task_' + did_edit).parent().parent().removeAttr('style');
                                $('#span_task_' + did_edit).parent().parent().find('.preview_image').remove();
                            }
                            
                            if($('#tabs-' + list_id).find('.icon-settings-sub').attr('data-typeid') == 2 || $('#tabs-' + list_id).find('.icon-settings-sub').attr('data-typeid') == 8)
                            {
                             $.ajax({
                                url: '<?php echo base_url(); ?>get_nexup_box',
                                type: 'POST',
                                data: {
                                    'list_id': list_id,
                                },
                                success: function (res) {
                                    if(res != ''){
                                        var row_indx = $('#tabs-' + list_id).find('#span_task_' + res).parent().parent().parent().index();
                                        if($('#tabs-' + list_id).find('.icon-settings-sub').attr('data-typeid') == 2){
                                            $('#tabs-' + list_id).find('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                            $('#tabs-' + list_id).find('.nexup-sub-group-one').attr('data-original-title', $.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                                
                                            var item_list_nexup_data =  '';
                                            var grp_two = '';
                                            var loop_through = $('#test_table_' + list_id).find('.heading_items_col').length;
                                            for(s = 1; s < 4; s++){
                                                if($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').length == 1){
                                                    grp_two += '<span data-toggle="tooltip" title="" data-original-title="' + $.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) +'">' + $.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</span>';
                                                }
                                            }
                                            for(g = 0; g < loop_through; g++){
                                                item_list_nexup_data += '<tr><td>' + $.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + g + ')').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                            }
                                            
                                            grp_two += '<p data-toggle="modal" data-target="#items-list" id="items_model_p" data-rowid="' + (row_indx + 1) +'" data-placement="top" title="Show all items"><img src="<?php echo base_url(); ?>assets/img/information-button-icon-23.png"></p>';
                                            if(grp_two != ''){
                                                $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').html(grp_two);
                                            }
                                            
                                        }else if($('#tabs-' + list_id).find('.icon-settings-sub').attr('data-typeid') == 8){
                                             $('#tabs-' + list_id).find('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                        }
                                        if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                                            $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                                event.preventDefault()
                                            }).tooltip({delay: { "hide": 100 }});
                                        }
                                        $(document).on('click', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                                            $('.tooltip').remove()
                                        });
                                    }
                                    var nexup_sun_grp_2_len = $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').length;
                                    if(nexup_sun_grp_2_len == 1){
                                        $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '18px');
                                    }else if(nexup_sun_grp_2_len == 2){
                                        $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '16px');
                                    }else{
                                        $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '13px');
                                    }
                                    set_nexup_box_data();
                                }
                            });
                            }
                            if($('#test_table_' + list_id).find('#span_task_' + did_edit).closest('div.edit_task').attr('data-type') == 'currency'){
                                if(display_task_name != ''){
                                    if(Number.isInteger(display_task_name)){
                                        title_text = title_text;
                                        display_task_name = '$ ' + display_task_name;
                                    }else{
                                        title_text = parseFloat(display_task_name).toFixed(2);
                                        display_task_name = '$ ' + parseFloat(display_task_name).toFixed(2);
                                    }
                                }
                            }
                            
                            if($('#test_table_' + list_id).find('#span_task_' + did_edit).closest('div.edit_task').attr('data-type') == 'email'){
                                display_task_name = '<a class="mail_url" href="mailto:' + display_task_name + '">' + display_task_name + '</a>'
                            }
                            
                            if($('#test_table_' + list_id).find('#span_task_' + did_edit).closest('div.edit_task').attr('data-type') == 'link'){
                                var testLink = /(((ftp|https?):\/\/)[\-\w@:%_\+.~#?,&\/\/=]+)|((mailto:)?[_.\w-]+@([\w][\w\-]+\.)+[a-zA-Z]{2,3})/g;
                                var url = '';
                                var is_url = 0;
                                if(!testLink.test(display_task_name)){
                                    var link_tst = /([a-zA-Z0-9]+(\.[a-zA-Z0-9]{2,3})+.*)$/g;
                                    if(link_tst.test(display_task_name)){
                                        url = 'http://' + display_task_name;
                                        is_url = 1;
                                    }else{
                                        is_url = 0;
                                    }
                                }else{
                                    url = display_task_name;
                                    is_url = 1;
                                }
                                display_task_name = '<a class="link_clickable" href="' + url + '">' + display_task_name + '</a>';
//                                display_task_name = display_task_name;
                            }
                            if($('#test_table_' + list_id).find('#span_task_' + did_edit).closest('div.edit_task').attr('data-type') == 'memo' || $('#test_table_' + list_id).find('#span_task_' + did_edit).closest('div.edit_task').attr('data-type') == 'text'){
                                var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,}$/i;
                                if (testEmail.test(edit_task_name)){
                                    display_task_name = '<a class="mail_url" href="mailto:' + edit_task_name + '">' + edit_task_name + '</a>'
                                }else{
                                    var testLink = /(((ftp|https?):\/\/)[\-\w@:%_\+.~#?,&\/\/=]+)|((mailto:)?[_.\w-]+@([\w][\w\-]+\.)+[a-zA-Z]{2,3})/g;
                                    var url = '';
                                    var is_url = 0;
                                    if(!testLink.test(display_task_name)){
                                        var link_tst = /([a-zA-Z0-9]+(\.[a-zA-Z0-9]{2,3})+.*)$/g;
                                        if(link_tst.test(display_task_name)){
                                            url = 'http://' + display_task_name;
                                            is_url = 1;
                                        }else{
                                            is_url = 0;
                                        }
                                    }else{
                                        url = display_task_name;
                                        is_url = 1;
                                    }
                                }
                            }
                            
                            $('#test_table_' + list_id).find('.edit_task[data-id="' + did_edit + '"]').attr('data-original-title', edit_task_name.replace(/</g, "&lt;").replace(/>/g, "&gt;"));
                            $('#test_table_' + list_id).find('.edit_task[data-id="' + did_edit + '"]').attr('data-task', edit_task_name.replace(/</g, "&lt;").replace(/>/g, "&gt;"));
                            
                            $('#span_task_' + did_edit).html(display_task_name.replace(/\n/g, "<br />"));
                            $('#span_task_' + did_edit).css('visibility', 'visible');
                            $('#edit_task_name').remove();
                            $('.edit_task_cls').remove();
                            $('#edit_item_span_enter').remove();
                            $('#task_' + did_edit + ' .add-data-div').removeClass('list-error');
                            $('#task_' + did_edit + ' .add-data-div .opertaions').removeClass('hide_operations');
                            $.ajax({
                                url: "<?php echo base_url() . 'item/push'; ?>",
                                type: 'POST',
                                data: {
                                    'ListId': list_id,
                                    'TaskId': did_edit,
                                    'Taskname': edit_task_name
                                },
                                success: function (response) {
                                }
                            });
                        }
                    }
                });
            }

            $(document).on('blur', '#edit_task_name', function (evt) {
                if($(this).attr('data-type') == 'number'){
                    if($(this).val() != ''){
                        if($(this).val() < 1){
                            $(this).val('1');
                            $(this).focus();
                            return false;
                        }
                    }
                }
                if($(this).attr('data-type') == 'date'){
                    if($(this).val() != ''){
                        if(!isDate($(this).val())){
                            alert('Please enter a valid date');
                            $(this).val('');
//                            $(this).focus();
                            return false;
                        }
                    }
                }
                if($(this).attr('data-type') == 'time'){
                    
                    if($(this).val() != ''){
                        var time_regexp = /([01][0-9]|[02][0-3]):[0-5][0-9]/;
                        if(time_regexp.test($(this).val()) == false){
                            alert('Please enter a valid time');
                            $(this).val('');
//                            $(this).focus();
                            return false;
                        }
                    }
                }
                if($(this).attr('data-type') == 'datetime'){
                    
                    if($(this).val() != ''){
                        var date_time_regexp = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4}) ([01][0-9]|[02][0-3]):[0-5][0-9]$/;
                        if(date_time_regexp.test($(this).val()) == false){
                            var time_regexp = /([01][0-9]|[02][0-3]):[0-5][0-9]/;
                            if(isDate($(this).val())){
                                $(this).val($(this).val() + ' 00:00');
                            }else if(time_regexp.test($(this).val()) != false){
                                var d = new Date();
                                var month = d.getMonth()+1;
                                var day = d.getDate();

                                var c_date = ((''+day).length<2 ? '0' : '') + day + '/' + ((''+month).length<2 ? '0' : '') + month + '/' + d.getFullYear();

                                $(this).val(c_date + ' ' + $(this).val());
                            }else{
                                alert('Please enter a valid date and time');
                                $(this).val('');
                                return false;
                            }
                        }
                    }
                }
                
                if($(this).attr('data-type') == 'email'){
                    if($(this).val() != ''){
                        var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,}$/i;
                        if (!testEmail.test($(this).val())){
                            alert('Please enter a valid email');
                            $(this).val('');
                            return false;
                        }
                    }
                }
                
                if($(this).attr('data-type') == 'link'){
                    var testLink = /(((ftp|https?):\/\/)[\-\w@:%_\+.~#?,&\/\/=]+)|((mailto:)?[_.\w-]+@([\w][\w\-]+\.)+[a-zA-Z]{2,3})/g;
                    var url = '';
                    var is_url = 0;
                    if(!testLink.test($(this).val())){
                        var link_tst = /([a-zA-Z0-9]+(\.[a-zA-Z0-9]{2,3})+.*)$/g;
                        if(link_tst.test($(this).val())){
                            url = 'http://' + $(this).val();
                            is_url = 1;
                        }else{
                            is_url = 0;
                        }
                    }else{
                        url = $(this).val();
                        is_url = 1;
                    }
                    
                    if (is_url == 0){
                        alert('Please enter a valid link');
                        $(this).val('');
                        return false;
                    }
                }
                
                var cell_indx = $(this).parent().parent().index();
                var row_indx = $(this).parent().parent().parent().index();
                var did_edit = $(this).attr('data-id');
                var edit_task_name = $('#edit_task_name').val();
                var list_id = $(this).attr('data-listid');
                edit_tasks(list_id, did_edit, edit_task_name, row_indx, cell_indx);
            });

            //Update task name
//            $(document).on('keydown', '#edit_task_name', function (evt) {
//                
//                if(key_code == 9){
//                    if($(this).val() != ''){
//                        if($(this).attr('data-type') == 'number'){
//                            if(!$.isNumeric($(this).val())){
//                                    $(this).val('1');
//                            }
//                        } else if($(this).attr('data-type') == 'date'){
//                            if(!isDate($(this).val())){
//                                alert('Please enter a valid date');
//                                $(this).val('');
//                                return false;
//                            }
//                        } else if($(this).attr('data-type') == 'time'){
//                            var time_regexp = /([01][0-9]|[02][0-3]):[0-5][0-9]/;
//                            if(time_regexp.test($(this).val()) == false){
//                                $(this).val('');
//                            }
//                        } else if($(this).attr('data-type') == 'datetime'){
//                            var time_regexp = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4}) ([01][0-9]|[02][0-3]):[0-5][0-9]$/;
//                            if(time_regexp.test($(this).val()) == false){
//                                alert('Please enter a valid date and time');
//                                $(this).val('');
//                                return false;
//                            }
//                        }
//                    }
//                    $(this).parent().find('.remove_text').remove();
//                }
//                    
//                    var cell_indx = $(this).parent().parent().index();
//                    var row_indx = $(this).parent().parent().parent().index();
//                    var key_code = evt.keyCode;
//                    var did_edit = $(this).attr('data-id');
//                    var edit_task_name = $('#edit_task_name').val();
//                    var list_id = $(this).attr('data-listid');
//                    if (key_code == 13) {
//                        if (evt.ctrlKey) {
//                            var start = window.getSelection().anchorOffset;
//                            if (start != $(this).html().length) {
//                                document.execCommand('insertText', false, '\n');
//                                if (start == ($(this).val().length - 2)) {
//                                    $(this).scrollTop($(this)[0].scrollHeight);
//                                }
//                            } else {
//                                document.execCommand('insertText', false, '\n ');
//                                $(this).scrollTop($(this)[0].scrollHeight);
//                            }
//                            return false;
//                        }
//                        $('#edit_task_name').trigger('blur');
//                    } else if (key_code == 27) {
//                        $('#edit_task_name').trigger('blur');
//                    }
//
//            });

            //Hide edit list text boxes when user takes control to add task text box
            $(document).on('focus', '#task_name', function () {
                $('#edit_task_name').trigger('blur');
            });

            //Edit task ajax call
            $(document).on('click', '#edit_task', function () {
                $('#edit_task').hide();
                $('#edit_task_loader').show();
                var did_edit = $(this).attr('data-id');
                var edit_task_name = $('#edit_task_name').val();
                var list_id = $(this).attr('data-listid');
                $('.edit_task_cls').text('');
                $('.edit_task_cls').hide();
                $('#plus-edit-task').removeClass('list-error');
                $.ajax({
                    url: "<?php echo base_url() . 'item/push'; ?>",
                    type: 'POST',
                    data: {
                        'ListId': list_id,
                        'TaskId': did_edit,
                        'Taskname': edit_task_name
                    },
                    success: function (res) {
                        if (res == 'success') {
                            $('#span_task_' + did_edit).text(edit_task_name.replace("&lt;", '<').replace("&gt;", '>'));
                        } else {
                            alert('Something went wrong. Please try again!');
                        }
                        $('#edit_task').show();
                        $('#edit_task_loader').hide();
                        $('#edit_task_name').val('');
                        $('#plus-edit-task').modal('hide');
                    }
                });
            });

            function sortNumber(a, b) {
                return a - b;
            }
            //Delete task ajax call
            $(document).on('click', '.delete_task', function () {
                var del_ord = $(this).parent().attr('data-order');
                var indx = $(this).parent().parent().index();
                var ul_id = $(this).parent().parent().parent().attr('id');
                var cnfrm = confirm('Are you sure want to delete this item?');
                var task_id = $(this).attr('data-id');
                var ListId = $(this).attr('data-listid');
                if (cnfrm) {
                    var orders = [];
                    var del_order = $(this).closest('.icon-more-holder').attr('data-order');
                    $('#test_table_' + ListId).find('.icon-more-holder').each(function () {
                        if ($(this).attr('data-order') != del_order) {
                            orders.push(parseInt($(this).attr('data-order')));
                        }
                    });
                    orders.sort(sortNumber);
                    $('#test_table_' + ListId).find('.icon-more-holder').css('pointer-events', 'none');
                    $.ajax({
                        url: "<?php echo base_url() . 'item/delete'; ?>",
                        type: 'POST',
                        data: {
                            'TaskId': task_id,
                            'ListId': ListId
                        },
                        success: function (res) {
                            if (res == 'fail') {
                                alert('Something went wrong. Please try again!');
                                return false;
                            }
                                $.ajax({
                                    url: "<?php echo base_url() . 'item/remove'; ?>",
                                    type: 'POST',
                                    data: {
                                        'TaskId': task_id,
                                        'ListId': ListId,
                                        'NewOrder': orders
                                    },
                                    success: function (res) {
                                        if (res == 'not allowed') {
                                            alert('You are not allowed to delete this item');
                                            return false;
                                        } else if (res != 'fail') {
                                            var ord_change = 1;
                                            var resp = JSON.parse(res);
                                            var del_ids = resp['remove'];
                                            $('#test_table_' + ListId).find('tbody tr:nth-child(' + (indx + 1) + ')').remove();
                                            setTimeout(function () {
                                                $('#test_table_' + ListId).find('.delete_task').prop('disabled', false);
                                            }, 2000);
                                            if ($('#test_table_' + ListId).find('.rank_th').length > 0) {
                                                var rnk_val = 1;
                                                $('#test_table_' + ListId).find('.rank_th').each(function () {
                                                    $(this).text(rnk_val);
                                                    rnk_val++;
                                                });
                                            }
                                            if ($('#tabs-' + ListId).find('#config_lnk_sub').attr('data-typeid') == 11) {
                                                
                                                $('#tabs-' + ListId).find('#yes_cnt').text(resp['total_yes']);
                                                $('#tabs-' + ListId).find('#maybe_cnt').text(resp['total_maybe']);
                                                $('#tabs-' + ListId).find('#no_cnt').text(resp['total_no']);
                                                $('#tabs-' + ListId).find('#blank_cnt').text(resp['total_blank']);
                                            }
                                            $('#test_table_' + ListId).find('.icon-more-holder').each(function () {
                                                var row_order = $(this).attr('data-order');
                                                if (row_order > del_ord) {
                                                    $(this).attr('data-order', (row_order - 1));
                                                }
                                            });
                                            if ($('#test_table_' + ListId).find('.edit_task').length == 0 && $('.heading_items_col_add').length == 1) {
                                                $('#tabs-' + ListId).find('.add-data-head-r').hide();
                                            }
                                            if($('#test_table_' + ListId).find('.icon-settings').attr('data-typeid') == 2 || $('.icon-settings').attr('data-typeid') == 8){
                                                $.ajax({
                                                   url: '<?php echo base_url(); ?>get_nexup_box',
                                                   type: 'POST',
                                                   data: {
                                                       'list_id': ListId,
                                                   },
                                                   success: function (res) {
                                                       if(res != ''){
                                                           var row_indx = $('#span_task_' + res).parent().parent().parent().index();
                                                           if($('#test_table_' + ListId).find('.icon-settings').attr('data-typeid') == 2){
                                                               if(row_indx >= 0){
                                                                $('#tabs-' + ListId).find('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table_' + ListId).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                                                $('#tabs-' + ListId).find('.nexup-sub-group-one').attr('data-original-title', $.trim($('#test_table_' + ListId).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                                                var item_list_nexup_data = '<tr><td>' + $.trim($('#test_table_' + ListId).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                                                var grp_two = '';
                                                                    
                                                                for(s = 1; s < 4; s++){
                                                                    if($('#test_table_' + ListId).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').length == 1){
                                                                        grp_two += '<span data-toggle="tooltip" title="" data-original-title="' + $.trim($('#test_table_' + ListId).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) +'">' + $.trim($('#test_table_' + ListId).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</span>';
                                                                        item_list_nexup_data += '<tr><td>' + $.trim($('#test_table_' + ListId).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                                                    }
                                                                }
                                                                grp_two += '<p data-toggle="modal" data-target="#items-list" id="items_model_p" data-rowid="' + (row_indx + 1) +'" data-placement="top" title="Show all items"><img src="<?php echo base_url(); ?>assets/img/information-button-icon-23.png"></p>';
                                                                if(grp_two != ''){
                                                                    var sub_grp_two = '<div class="nexup-sub-group nexup-sub-group-two">';
                                                                    sub_grp_two += grp_two;
                                                                    sub_grp_two += '</div>';
                                                                    if($('#tabs-' + ListId).find('.nexup-sub-group-two').length == 0){
                                                                        $('#tabs-' + ListId).find('.button-outer').find('.nexup-sub-group-one').after(sub_grp_two);
                                                                    }else{
                                                                        $('#tabs-' + ListId).find('.button-outer').find('.nexup-sub-group-two').html(grp_two);
                                                                    }
                                                                }else{
                                                                    $('#tabs' + ListId).find('.button-outer').find('.nexup-sub-group-two').remove();
                                                                }
                                                            }

                                                           }else if($('#tabs-' + ListId).find('.icon-settings').attr('data-typeid') == 8){
                                                                $('#tabs-' + ListId).find('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                                           }
                                                            if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                                                                $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                                                    event.preventDefault()
                                                                }).tooltip({delay: { "hide": 100 }});
                                                            }
                                                           $(document).on('click', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                                                               $('.tooltip').remove()
                                                           });
                                                       }else{
                                                            if($('#tabs-' + ListId).find('.icon-settings').attr('data-typeid') == 2){
                                                                 $('#tabs-' + ListId).find('.nexup-sub-group-one').find('#next_task_name').text('');
                                                                 $('#tabs-' + ListId).find('.nexup-sub-group-one').attr('data-original-title', '');
                                                                 if($('#tabs-' + ListId).find('.button-outer').find('.nexup-sub-group-two').length > 0){
                                                                    $('#tabs-' + ListId).find('.button-outer').find('.nexup-sub-group-two').remove();
                                                                }
                                                            }else if($('#tabs-' + ListId).find('.icon-settings').attr('data-typeid') == 8){
                                                                $('#tabs-' + ListId).find('.nexup-sub-group-single').find('#next_task_name').text('');
                                                                $('#tabs-' + ListId).find('.nexup-sub-group-single').attr('data-original-title', '');
                                                            }
                                                       }
                                                       var nexup_sun_grp_2_len = $('#tabs-' + ListId).find('.button-outer').find('.nexup-sub-group-two').find('span').length;
                                                        if(nexup_sun_grp_2_len == 1){
                                                            $('#tabs-' + ListId).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '18px');
                                                        }else if(nexup_sun_grp_2_len == 2){
                                                            $('#tabs-' + ListId).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '16px');
                                                        }else{
                                                            $('#tabs-' + ListId).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '13px');
                                                        }
                                                        set_nexup_box_data();
                                                   }
                                                    
                                               });
                                            }
                                        } else {
                                            alert('Something went wrong. Please try again!');
                                        }
                                        var total_rows = $('#test_table_' + ListId).find('tbody tr').length;
                                        if (total_rows < 1) {
                                            $('#tabs-' + ListId).find('.whoisnext-div .whoisnext-btn').addClass('whosnext_img_bg');
                                            $('#tabs-' + ListId).find('span#next_task_name').text('');
                                            $('#tabs-' + ListId).find('span#next_task_name').attr('title', '');
                                        } else {

                                            if (total_rows > 0) {
                                                var ind = 0;
                                                if (resp['last_log'] != 'no log') {
                                                    $('#test_table_' + ListId).find('.edit_task').each(function () {
                                                        if ($(this).attr('data-id') == resp['last_log']) {
                                                            ind = $(this).parent().parent().index();
                                                        }
                                                    });
                                                }

                                                $('#tabs-' + ListId).find('#next_task_name').text($('#test_table_' + ListId).find('tbody tr:nth-child(' + (ind + 1) + ') td.list-table-view:first .task_name_span').text());


                                                var task_nm_span = $('#tabs-' + ListId).find('.whoisnext-div .nexup-group .nexup-group-two .custom_cursor .nexup-sub-group-two span').length;
                                                var k = 1;
                                                for (k = 1; k <= task_nm_span; k++) {

                                                    if ($('#test_table_' + ListId).find('tbody tr:nth-child(' + (ind + 1) + ') td:eq(' + (k + 1) + ')').length) {
                                                        $('#tabs-' + ListId).find('.whoisnext-div .nexup-group .nexup-group-two .custom_cursor .nexup-sub-group-two span:nth-child(' + k + ')').text($('#test_table_' + ListId).find('tbody tr:nth-child(' + (ind + 1) + ') td:eq(' + (k + 1) + ') .task_name_span').text());
                                                    }
                                                }
                                            }


                                        }
                                        $('#test_table_' + ListId).find('.icon-more-holder').css('pointer-events', 'all');
                                    },
                                    complete: function (data) {
                                        $('#test_table_' + ListId).find('.icon-more-holder').css('pointer-events', 'all');
                                    }
                                });
                                
//                            } else if (res == 'fail') {
//                                alert('Something went wrong. Please try again!');
//                            } else if (res == 'not allowed') {
//                                alert('You are not allowed to delete this item');
//                                return false;
//                            }
                            $('#test_table_' + ListId).find('.icon-more-holder').css('pointer-events', 'all');
                        },
                        error: function (textStatus, errorThrown) {
                            alert('something went wrong. Plese try again!');
                            $('#test_table_' + ListId).find('.icon-more-holder').css('pointer-events', 'all');
//                            $('.delete_task').prop('disabled', false);
                        },
                        complete: function (data) {
                            $('#test_table_' + ListId).find('.icon-more-holder').css('pointer-events', 'all');
                            console.log($('#test_table_' + ListId).find('.icon-more-holder').attr('style'));
//                            $('.delete_task').prop('disabled', false);
                        }
                    });
                }
                setTimeout(function () {
                    $('#test_table_' + ListId).find('.delete_task').prop('disabled', false);
                }, 5000);
            });

            //Complete Task
            $(document).on('change', '.complete_task', function () {
            var list_id = $(this).attr('data-listid');
//            $('.delete_task').prop('disabled', true);
                $('#test_table_' + list_id).find('.icon-more-holder').prop('disabled', true);
                var task_status = 0;

                var tasks_orders = [];
                if ($(this).is(':checked')) {
                    task_status = 1;
                }


                var this_order = $(this).closest('td.icon-more-holder').attr('data-order');
                if (task_status == 1) {
                    $('#test_table_' + list_id).find('tbody tr').each(function () {
                        var rnk = $(this).children('td.icon-more-holder').attr('data-order');
                        if (rnk != this_order) {
                            tasks_orders.push(rnk);
                        }
                    });
                    tasks_orders.push(this_order);
                } else {
                    $('#test_table_' + list_id).find('tbody tr').each(function () {
                        var rnk = $(this).children('td.icon-more-holder').attr('data-order');
                        if (!$(this).hasClass('strikeout') && rnk != this_order) {
                            tasks_orders.push(rnk);
                        }
                    });
                    tasks_orders.push(this_order);
                    $('#test_table_' + list_id).find('.strikeout').each(function () {
                        var rnks = $(this).children('td.icon-more-holder').attr('data-order');
                        if ($(this).hasClass('strikeout') && rnks != this_order) {
                            tasks_orders.push(rnks);
                        }
                    });
                }

                $('#test_table_' + list_id).find('.complete_task').prop('disabled', true);
                $('#test_table_' + list_id).find('.icon-more-holder').css('pointer-events', 'none');

                var c_ids = [];

                $(this).parent().parent().find('.list-table-view').each(function () {
                    var child_id = $(this).find('.edit_task').attr('data-id');
                    c_ids.push(child_id);
                });

                var did = c_ids.join(',');

//                    var did = $(this).attr('data-id');
                $.ajax({
                    url: "<?php echo base_url() . 'item/complete'; ?>",
                    type: 'POST',
                    context: this,
                    data: {
                        'TaskId': did,
                        'task_status': task_status,
                        'tasks_orders': JSON.stringify(tasks_orders),
                        'ListId': list_id
                    },
                    success: function (res) {
                        if (res == 'success') {
                            if (task_status == 0) {
                                $(this).closest('tr').removeClass('strikeout');
                                $(this).closest('tr').removeClass('completed');
                                var loc = $(this).parent().attr('data-order');
                                var move_tr = $(this).closest('tr');
                                var move_row = move_tr.clone();
                                if($('#test_table_' + list_id).find('.completed:first').length > 0){
                                    var first_completed = $('.completed:first').index();
                                    loc = first_completed + 1
                                }
                                $('#test_table_' + list_id).find('tbody tr:eq(' + (loc - 1) + ')').before(move_row);
                                move_tr.remove();

                            } else {
                                $(this).closest('tr').addClass('strikeout');
                                $(this).closest('tr').addClass('completed');
                                if ($('#tabs-' + list_id).find('#listConfig_lnk_' + list_id).attr('data-showcompleted') == 0) {
                                    $(this).closest('tr').addClass('hidden_tbl_row');
                                } else {
                                    $(this).closest('tr').removeClass('hidden_tbl_row');
                                }
                                $(this).closest('tr').appendTo($('#test_table_' + list_id).find('tbody'));
                            }
                        } else if (res == 'fail') {
                            if (task_status == 0) {
                                $(this).prop('checked', true);
                            } else {
                                $(this).prop('checked', false);
                            }
                            alert('Task you are looking for dis not found!');
                        } else {
                            if (task_status == 0) {
                                $(this).prop('checked', true);
                            } else {
                                $(this).prop('checked', false);
                            }
                            alert('You are not allowed to complete/incomplete this task!');
                        }
                        $('#test_table_' + list_id).find('.complete_task').prop('disabled', false);
                        $('#test_table_' + list_id).find('.icon-more-holder').css('pointer-events', 'auto');
                    },
                    error: function (textStatus, errorThrown) {
                        alert('something went wrong. Plese try again!');
                        $('#test_table_' + list_id).find('.icon-more-holder').css('pointer-events', 'auto');
//                            $('.delete_task').prop('disabled', false);
                        setTimeout(function () {
                            $('#test_table_' + list_id).find('.icon-more-holder').prop('disabled', false);
                        }, 2000);
                    },
                    complete: function (data) {
                        $('#test_table_' + list_id).find('.icon-more-holder').css('pointer-events', 'auto');
//                            $('.delete_task').prop('disabled', false);
                        setTimeout(function () {
                            $('#test_table_' + list_id).find('.icon-more-holder').prop('disabled', false);
                        }, 2000);
                    }
                });
                setTimeout(function () {
//                        $('.delete_task').prop('disabled', false);
                    $('#test_table_' + list_id).find('.icon-more-holder').prop('disabled', false);
                }, 5000);
            });


            $(document).on('change', '.present_task', function () {
            var list_id = $(this).attr('data-listid');
            var parent_class_is_sub = $(this).parent().parent().parent().parent().parent().hasClass('my_sub_table');
            var config_link = $('#listConfig_lnk');
            if(parent_class_is_sub){
                config_link = $('#tabs-' + list_id).find('#listConfig_lnk_' + list_id);
            }
            
                $(this).prop('checked', false);
                var task_status = 1;

                if ($(this).closest('td.icon-more-holder').find('.present_lbl').hasClass('green_label')) {
                    if (config_link.attr('data-allowmaybe') == 1) {
                        task_status = 2;
                    } else {
                        task_status = 3;
                    }
                } else if ($(this).closest('td.icon-more-holder').find('.present_lbl').hasClass('yellow_label')) {
                    task_status = 3;
                } else if ($(this).closest('td.icon-more-holder').find('.present_lbl').hasClass('red_label')) {
                    task_status = 0;
                }
                var time_id = 0;
                if ($('#tabs-' + list_id).find('.icon-settings-sub').attr('data-typeid') == 11) {
                    time_id = $(this).closest('tr').find('div.check_date').attr('data-id');
                }

                var did = [];

                $(this).parent().parent().find('.list-table-view').each(function () {
                    did.push($(this).find('.edit_task').attr('data-id'));
                });
                did = did.join(',');

                $('test_table_' + list_id).find('.icon-more-holder').css('pointer-events', 'none');
                
                if (task_status == 1) {
                    $(this).closest('td.icon-more-holder').find('.present_lbl').addClass('green_label');
                    $(this).closest('td.icon-more-holder').find('.present_lbl').removeClass('yellow_label');
                    $(this).closest('td.icon-more-holder').find('.present_lbl').removeClass('red_label');
                } else if (task_status == 3) {
                    $(this).closest('td.icon-more-holder').find('.present_lbl').addClass('red_label');
                    $(this).closest('td.icon-more-holder').find('.present_lbl').removeClass('green_label');
                    $(this).closest('td.icon-more-holder').find('.present_lbl').removeClass('yellow_label');
                } else if (task_status == 2) {
                    $(this).closest('td.icon-more-holder').find('.present_lbl').addClass('yellow_label');
                    $(this).closest('td.icon-more-holder').find('.present_lbl').removeClass('green_label');
                    $(this).closest('td.icon-more-holder').find('.present_lbl').removeClass('red_label');
                } else if (task_status == 0) {
                    $(this).closest('td.icon-more-holder').find('.present_lbl').removeClass('green_label');
                    $(this).closest('td.icon-more-holder').find('.present_lbl').removeClass('yellow_label');
                    $(this).closest('td.icon-more-holder').find('.present_lbl').removeClass('red_label');
                }
                
                var fail_cnt = 0;
                
                call_present(this, did, task_status, list_id, time_id, fail_cnt);
                
                
                    
            });
            
            function call_present(self, did, task_status, list_id, time_id, fail_cnt){
                $.ajax({
                    url: "<?php echo base_url() . 'item/present'; ?>",
                    type: 'POST',
                    context: this,
                    data: {
                        'TaskId': did,
                        'task_status': task_status,
                        'ListId': list_id,
                        'time_id': time_id,
                    },
                    success: function (res) {
                        if (res == 'fail') {
//                            alert('Task you are looking for dis not found!');
                            if(fail_cnt < 5){
                                fail_cnt++;
                                call_present(self, did, task_status, list_id, time_id, fail_cnt);
                            }else{
//                                alert('Something went wrong. Item you tried was not successfully updated. The reason behind this failure might be Bad internet connection');
                                if (task_status == 1) {
                                    $(self).closest('td.icon-more-holder').find('.present_lbl').removeClass('green_label');
                                    $(self).closest('td.icon-more-holder').find('.present_lbl').removeClass('yellow_label');
                                    $(self).closest('td.icon-more-holder').find('.present_lbl').removeClass('red_label');

                                } else if (task_status == 3) {
                                    $(self).closest('td.icon-more-holder').find('.present_lbl').addClass('green_label');
                                    $(self).closest('td.icon-more-holder').find('.present_lbl').removeClass('red_label');
                                    $(self).closest('td.icon-more-holder').find('.present_lbl').removeClass('yellow_label');

                                } else if (task_status == 2) {
                                    $(self).closest('td.icon-more-holder').find('.present_lbl').addClass('red_label');
                                    $(self).closest('td.icon-more-holder').find('.present_lbl').removeClass('yellow_label');
                                    $(self).closest('td.icon-more-holder').find('.present_lbl').removeClass('green_label');
                                    //Move tr to the top of yellow (maybe) checkboxes
                                } else if (task_status == 0) {
                                    if ($('#listConfig_lnk').attr('data-allowmaybe') == 1) {
                                        $(self).closest('td.icon-more-holder').find('.present_lbl').addClass('yellow_label');
                                        $(self).closest('td.icon-more-holder').find('.present_lbl').removeClass('green_label');
                                        $(self).closest('td.icon-more-holder').find('.present_lbl').removeClass('red_label');
                                    }else{
                                        $(self).closest('td.icon-more-holder').find('.present_lbl').removeClass('yellow_label');
                                        $(self).closest('td.icon-more-holder').find('.present_lbl').removeClass('green_label');
                                        $(self).closest('td.icon-more-holder').find('.present_lbl').addClass('red_label');
                                    }

                                }
                                alert('Bad internet connection. Item you marked was not updated!');
                            }
                        } else if (res == 'empty') {
                            alert('You are not allowed to perform this action!');
                        } else if (res == 'unauthorized') {
                            alert('You are not allowed to perform this action!');
                        } else {
                            var move_row = '';
                            if (task_status == 1) {
                                $(self).closest('td.icon-more-holder').find('.present_lbl').addClass('green_label');
                                $(self).closest('td.icon-more-holder').find('.present_lbl').removeClass('yellow_label');
                                $(self).closest('td.icon-more-holder').find('.present_lbl').removeClass('red_label');

                            } else if (task_status == 3) {
                                $(self).closest('td.icon-more-holder').find('.present_lbl').addClass('red_label');
                                $(self).closest('td.icon-more-holder').find('.present_lbl').removeClass('green_label');
                                $(self).closest('td.icon-more-holder').find('.present_lbl').removeClass('yellow_label');

                                //Move tr to the top of red (false) checkboxes




                            } else if (task_status == 2) {
                                $(self).closest('td.icon-more-holder').find('.present_lbl').addClass('yellow_label');
                                $(self).closest('td.icon-more-holder').find('.present_lbl').removeClass('green_label');
                                $(self).closest('td.icon-more-holder').find('.present_lbl').removeClass('red_label');


                                //Move tr to the top of yellow (maybe) checkboxes



                            } else if (task_status == 0) {

                                $(self).closest('td.icon-more-holder').find('.present_lbl').removeClass('green_label');
                                $(self).closest('td.icon-more-holder').find('.present_lbl').removeClass('yellow_label');
                                $(self).closest('td.icon-more-holder').find('.present_lbl').removeClass('red_label');

                            }
                            var resp = JSON.parse(res);
                            var yes_cn = '0';
                            if (resp['yes'] != null) {
                                yes_cn = resp['yes'];
                            }
                            $('#tabs-' + list_id).find('#yes_cnt').text(yes_cn);

                            var no_cn = '0';
                            if (resp['no'] != null) {
                                no_cn = resp['no'];
                            }
                            $('#tabs-' + list_id).find('#no_cnt').text(no_cn);

                            var mb_cn = '0';
                            if (resp['maybe'] != null) {
                                mb_cn = resp['maybe'];
                            }
                            $('#tabs-' + list_id).find('#maybe_cnt').text(mb_cn);

                            var blank_cn = '0';
                            if (resp['blank'] != null) {
                                blank_cn = resp['blank'];
                            }
                            if (time_id > 0) {
                                if ($('#test_table_' + list_id).find('check_date .time_name_span').length > 0) {
                                    $(self).closest('tr').find('div.check_date .time_name_span').text('Just Now');
                                } else {
                                    var time_span = '<span id="span_time_' + time_id + '" class="time_name_span">Just Now</span>';
                                    $(self).closest('tr').find('div.check_date').html(time_span);
                                }
                            }

                            $('#tabs-' + list_id).find('#blank_cnt').text(blank_cn);
                            var new_ord = $('#test_table_' + list_id).find('present_' + did).closest('td.icon-more-holder').index();

                            var tasks_orders = [];
                            $('#test_table_' + list_id).find('tbody tr td.icon-more-holder').each(function (e) {
                                var orders = $(self).attr('data-order');
                                tasks_orders.push(orders);
                            });

                        }
                        $('#test_table_' + list_id).find('.icon-more-holder').css('pointer-events', 'auto');

                    },
                    error: function (textStatus, errorThrown) {
                        alert('something went wrong. Plese try again!');
                        $('#test_table_' + list_id).find('.icon-more-holder').css('pointer-events', 'auto');
                    },
                    complete: function (data) {
                        $('#test_table_' + list_id).find('.icon-more-holder').css('pointer-events', 'auto');
                    }
                });
            }



            //Visit list from history drop down
            $(document).on('change', '#history_dd', function () {
                var url_val = this.value;
                if (url_val != 0) {
                    location = this.value;
                }
            });
            
            //Update list type ajax
            $(document).on('click', '.list_sub_type_cls', function (e) {
                var list_id = $(this).attr('data-listid');
                if ($('#content_' + list_id).find('.plus-category').attr('data-access') == 0) {
                    if($('#edit_list_name').length == 1){
                        alert('You can not change this list type!');
                    }
//                    alert('You are not allowed to change list type!');
                    e.preventDefault();
                    return false;
                }
                
                var type_id = $(this).attr('data-typeid');
                
                if (($('#content_' + list_id).find('.nexup-sub-group-single').length > 0) && (type_id == 8)) {
                } else if (($('#content_' + list_id).find('.nexup-sub-group-one').length > 0) && (type_id == 2)) {
                } else {
                    $.ajax({
                        url: '<?php echo base_url() . 'change_listType'; ?>',
                        type: 'POST',
                        context: this,
                        data: {
                            'list_id': list_id,
                            'type_id': type_id
                        },
                        success: function (res) {
                            if (res != 'fail' && res != 'not allowed') {
                                $('#tabs-' + list_id).find('.icon-settings-sub').attr('data-typeid', type_id);
                                if(type_id == 2){
                                    $('#listConfig_lnk_' + list_id).attr('data-collapsed', 1);
                                }
                                if (type_id == 5) {
                                    $('#config_sub_list').find('#show_sub_completed_item').parent().parent().removeClass('hidden_checkbox');
                                    if ($('#tabs-' + list_id).find('.complete_task').length > 0) {
                                        $('#tabs-' + list_id).find('.complete_task').remove();
                                        $('#tabs-' + list_id).find('.complete_lbl').remove();
                                    }
                                    if ($('#tabs-' + list_id).find('.present_task').length > 0) {
                                        $('#tabs-' + list_id).find('.present_task').remove();
                                        $('#tabs-' + list_id).find('.present_lbl').remove();
                                    }
                                    if ($('#tabs-' + list_id).find('.count_box').length > 0) {
                                        $('#tabs-' + list_id).find('.count_box').remove();
                                    }
                                    if ($('#tabs-' + list_id).find('.reset_list').length > 0) {
                                        $('#tabs-' + list_id).find('.reset_list').remove();
                                    }
                                    
                                    if(!$('#test_table_' + list_id).hasClass('check_box_table')){
                                        $('#test_table_' + list_id).addClass('check_box_table')
                                    }


                                } else if (type_id == 11) {
                                    $('#config_sub_list').find('#show_sub_completed_item').closest('checkbox-outer').addClass('hidden_checkbox');
                                    $('#config_sub_list').find('#maybe_allowed_sub').closest('checkbox-outer').addClass('hidden_checkbox');
                                    var res_data = []
                                    if (res != '') {
                                        res_data = JSON.parse(res);
                                    }

                                    if ($('#tabs-' + list_id).find('.count_box').length == 0) {
                                        $('#tabs-' + list_id).find('.config_icons').after(res_data['box']);
                                    }
                                    if ($('#tabs-' + list_id).find('#reset_list').length == 0) {
                                        $('#tabs-' + list_id).find('#delete_sub_list_builder').before(res_data['reset_btn'])
//                                        $(res_data['reset_btn']).insertBefore('#delete_list_builder');
                                    }
                                    if(!$('#test_table_' + list_id).hasClass('check_box_table')){
                                        $('#test_table_' + list_id).addClass('check_box_table')
                                    }
                                } else {
                                    $('#config_sub_list').find('#show_sub_completed_item').closest('checkbox-outer').addClass('hidden_checkbox');
                                    if ($('#tabs-' + list_id).find('.reset_list').length > 0) {
                                        $('#tabs-' + list_id).find('.reset_list').remove();
                                    }
                                    if ($('#tabs-' + list_id).find('.count_box').length > 0) {
                                        $('#tabs-' + list_id).find('.count_box').remove();
                                    }
                                    if($('#test_table_' + list_id).hasClass('check_box_table')){
                                        $('#test_table_' + list_id).removeClass('check_box_table')
                                    }
                                }


                                if (type_id != 3) {
                                    $('#test_table_' + list_id).find('.rank_th_head').remove();
                                    $('#test_table_' + list_id).find('.rank_th').remove();
                                }
                                if (type_id != 2 && type_id != 8) {
                                    $('#tabs-' + list_id).find('.whoisnext-div').remove();
                                    if (type_id == 3) {
                                        $('#tabs-' + list_id).find('.rank_th_head').remove();
                                        $('#tabs-' + list_id).find('.rank_th').remove();
                                        var rank_item = 1;
                                        $('#tabs-' + list_id).find('#test_table tbody tr').each(function () {
                                            var first_th = $(this).find('.icon-more-holder');
                                            if (first_th != '') {
                                                var list_rank = '<td class="rank_th">' + rank_item + '</td>'
                                                rank_item++;
                                                $(first_th).after(list_rank);
                                            }
                                        });
                                        $('#test_table_' + list_id).find('thead tr').each(function () {
                                            var first_th_head = $(this).children('th:eq(0)');
                                            if (first_th_head != '') {
                                                var list_rank_head = '<th class="noDrag rank_th_head"></th>'
                                                $(first_th_head).after(list_rank_head);
                                            }
                                        });
                                    }
                                } else {
                                var resp = JSON.parse(res);
                                    var task_name = '';
                                    if ($('#test_table_' + list_id).length > 0) {
                                        var next_elem_found = $('#test_table_' + list_id).find('tbody').children('tr:first').children('td:first').children('div:first');
                                        if (res != '') {
                                            next_elem_found = $('#test_table_' + list_id).find("[data-id='" + resp['last_log'] + "']");
                                        }
                                        task_name = $(next_elem_found).children().text();
//                                        task_name = $('#test_table tbody').children('tr:first').children('td:first').children('div:first').children('span:first').text();

                                    }
                                    var total_cols = $('#test_table_' + list_id).find('tbody tr:first-child td .add-data-div').length;
                                    var nexup_div = '<div class="whoisnext-div">';
                                    nexup_div += '<div class="nexup-group">';
                                    nexup_div += '<div class="nexup-group-two">';
                                    nexup_div += '<div class="button-outer custom_cursor">';
                                    if (type_id == 2) {
                                        nexup_div += '<div class="nexup-sub-group nexup-sub-group-one" data-toggle="tooltip" title="' + $.trim(task_name) + '">';
                                        nexup_div += '<span id="next_task_name" style="font-size: 50px;">' + task_name + '</span>';
                                        nexup_div += '</div>';
                                    } else if (type_id == 8) {
                                        nexup_div += '<div class="nexup-sub-group nexup-sub-group-single" data-toggle="tooltip" title="' + $.trim(task_name) + '">';
                                        nexup_div += '<span id="next_task_name" style="font-size: 50px;">' + task_name + '</span>';
                                        nexup_div += '</div>';
                                    }
                                    if (type_id == 2) {
                                        if (total_cols > 1) {
                                            nexup_div += '<div class="nexup-sub-group nexup-sub-group-two">';
                                            var index_row = $(next_elem_found).parent('td').parent().index();
                                            var loop_max = (total_cols - 1);
                                            if (total_cols > 4) {
                                                var loop_max = 3;
                                            }
                                            for (j = 0; j < loop_max; j++) {
                                                var show_indx = j + 2;
                                                nexup_div += '<span data-toggle="tooltip" title="' + $.trim($('#test_table_' + list_id).find('tbody tr:nth-child(' + (index_row + 1) + ') td:eq(' + show_indx + ') .add-data-div .task_name_span').text()) + '">';
                                                nexup_div += $('#test_table_' + list_id).find('tbody tr:nth-child(' + (index_row + 1) + ') td:eq(' + show_indx + ') .add-data-div .task_name_span').text();
                                                nexup_div += '</span>';
                                            }
                                            nexup_div += '<p data-toggle="modal" data-target="#items-list" id="items_model_p" data-rowid="' + (index_row + 1) + '"><img src="<?php echo base_url() . 'assets/img/information-button-icon-23.png'; ?>" data-toggle="tooltip" data-placement="top" title="Show all items"></p>';
                                            nexup_div += '</div>';
                                        }
                                    }


                                    nexup_div += '</div>';
                                    nexup_div += '<div id="nexup_btns">';

                                    nexup_div += '<div class="cmnt-btn-div cmnt-btn-div_change">';
                                    nexup_div += '<div class="next_btn_div">';
                                    nexup_div += '<a class="whoisnext-btn-cmnt custom_cursor" data-toggle="tooltip" data-placement="top" title="Nexup"><span id="nexup_icon_cmnt" class=""><img class="defult_arrow" src="/assets/img/next-arrow.png"><img class="hover_arrow" src="/assets/img/next-arrow-white.png"></span></span></a>';
                                    var cmnt_disable_cls = '';
                                    if ($('#tabs-' + list_id).find('.sub_listConfig_lnk').attr('data-allowcmnt') == 0) {
                                        cmnt_disable_cls = ' hide_box';
                                    }
                                    nexup_div += '<span class="add-data-div add_comment_box' + cmnt_disable_cls + '" id="nexup_cmnt_span">';
                                    nexup_div += '<input type="text" id="nexup_comment" class="nexup_comment" placeholder="Comment...">';
                                    nexup_div += '</span>';
                                    nexup_div += '</div>';
                                    
                                    var allow_undo_cls = '';
                                    if($('#listConfig_lnk_' + list_id).attr('data-allowundo') == 0){
                                        allow_undo_cls = ' disabled_undo';
                                    }
                                    nexup_div += '<a class="undo-btn custom_cursor' + allow_undo_cls + '" data-listid="' + list_id + '"  data-toggle="tooltip" data-placement="top" title="Backup"><span id="undo_icon" class=""><img class="defult_arrow" src="/assets/img/prev-arrow.png"><img class="hover_arrow" src="/assets/img/prev-arrow-white.png"><img class="grey_arrow" src="/assets/img/prev-arrow-grey.png"></span></a>';
                                    nexup_div += '<div class="h-nav dropdown log_icon_btn">';
                                    nexup_div += '<a title="Log" class="custom_cursor" id="dropdownMenuLog_sub" data-toggle="dropdown" aria-haspopup="true" data-listid="' + list_id + '" aria-expanded="false"><img class="defult_arrow" src="/assets/img/log_icon.png"><img class="hover_arrow" src="/assets/img/log_icon-white.png"></i></a>';
                                    if(resp['log_list'] != ''){
                                        nexup_div += resp['log_list'];
                                    }
                                    nexup_div += '</div>';

                                    nexup_div += '</div>';
                                    nexup_div += '</div>';
                                    nexup_div += '</div>';
                                    nexup_div += '</div>';
                                    nexup_div += '</div>';
                                    if ($('#tabs-' + list_id).find('.whoisnext-div').length == 0) {
                                        $('#tabs-' + list_id).find('.config_icons_sub').after(nexup_div);
                                        var next_task = $(".whoisnext-div #next_task_name");
                                        var numWords = next_task.text().trim().length;
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
                                    } else if (type_id == 2 || type_id == 8) {
                                        $('#tabs-' + list_id).find('.whoisnext-div').remove();
                                        $('#tabs-' + list_id).find('.config_icons_sub').after(nexup_div);
                                        var next_task = $('#content_' + list_id).find(".whoisnext-div #next_task_name");
                                        var numWords = next_task.text().trim().length;
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
                                    }
                                }
                                $('#tabs-' + list_id).find('#ListType_msg').html('You have successfully changed list type.');
                                $('#tabs-' + list_id).find('#ListType_msg').removeClass('alert-danger');
                                $('#tabs-' + list_id).find('#ListType_msg').addClass('alert-success');
                                $('#tabs-' + list_id).find('#ListType_msg').show();
                                $.ajax({
                                    url: '<?php echo base_url() . 'update_listType'; ?>',
                                    type: 'POST',
                                    data: {
                                        'list_id': list_id,
                                        'type_id': type_id
                                    },
                                    success: function (res) {
                                    }
                                });



                                if (type_id == 11) {
                                    var first_col_title = '<div class="add-data-title-nodrag status-column" data-toggle="tooltip" data-placement="bottom">';
                                    first_col_title += '</div>';
                                    $('#tabs-' + list_id).find('#test_table thead tr.td_arrange_tr').find('th.nodrag_actions').html(first_col_title);
                                    $('#tabs-' + list_id).find('.nodrag_time').removeClass('hidden_nodrag');
                                    $('#tabs-' + list_id).find('.nodrag_comment').removeClass('hidden_nodrag');
                                    $('#tabs-' + list_id).find('.list-table-view-attend').removeClass('hidden_nodrag');
                                } else {
                                    $('#tabs-' + list_id).find('#test_table thead tr.td_arrange_tr').find('th.nodrag_actions').children('.add-data-title-nodrag').remove();
                                    $('#tabs-' + list_id).find('.nodrag_time').removeClass('hidden_nodrag');
                                    $('#tabs-' + list_id).find('.nodrag_comment').removeClass('hidden_nodrag');
                                    $('#tabs-' + list_id).find('.list-table-view-attend').removeClass('hidden_nodrag');
                                    $('#tabs-' + list_id).find('.nodrag_time').addClass('hidden_nodrag');
                                    $('#tabs-' + list_id).find('.nodrag_comment').addClass('hidden_nodrag');
                                    $('#tabs-' + list_id).find('.list-table-view-attend').addClass('hidden_nodrag');
                                    $('#tabs-' + list_id).find('list-table-view-attend').hide();

                                }


                                if (type_id == 11) {
                                    if ($('#tabs-' + list_id).find('.sub_listConfig_lnk').attr('data-showtime') == 1) {
                                        $('#tabs-' + list_id).find('.nodrag_time').removeClass('hidden_nodrag');

                                        $('#tabs-' + list_id).find('.check_date').each(function () {
                                            $(this).parent().removeClass('hidden_nodrag');
                                        });
                                    } else {
                                        $('#tabs-' + list_id).find('.my_table').find('.nodrag_time').addClass('hidden_nodrag');

                                        $('#tabs-' + list_id).find('.check_date').each(function () {
                                            $(this).parent().addClass('hidden_nodrag');
                                        });
                                    }

                                } else {
                                    if (!$('#tabs-' + list_id).find('.nodrag_time').hasClass('hidden_nodrag')) {
                                        $('#tabs-' + list_id).find('.nodrag_time').addClass('hidden_nodrag');
                                    }
                                    $('#tabs-' + list_id).find('.check_date').each(function () {
                                        if (!$(this).parent().hasClass('hidden_nodrag')) {
                                            $(this).parent().addClass('hidden_nodrag');
                                        }
                                    });

                                }
                            } else if (res == 'not allowed') {
                                if($('#tabs-' + list_id).find('#edit_list_name').length == 1){
                                    alert('Please name the list first.');
                                }else{
                                    alert('You can not change this list type!');
                                }
                                $('#tabs-' + list_id).find('#ListType_msg').html('You can not change this list type!');
                                $('#tabs-' + list_id).find('#ListType_msg').removeClass('alert-success');
                                $('#tabs-' + list_id).find('#ListType_msg').addClass('alert-danger');
                                $('#tabs-' + list_id).find('#ListType_msg').show();
                                return false;
                            } else {
                                $('#tabs-' + list_id).find('#ListType_msg').html('Something went wrong. List type was not changed. Please try again!');
                                $('#tabs-' + list_id).find('#ListType_msg').removeClass('alert-success');
                                $('#tabs-' + list_id).find('#ListType_msg').addClass('alert-danger');
                                $('#tabs-' + list_id).find('#ListType_msg').show();
                                return false;
                            }
                            $('.alert-danger').delay(5000).fadeOut('fast');
                            $('.alert-success').delay(5000).fadeOut('fast');
                            if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                                $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                    event.preventDefault()
                                }).tooltip({delay: { "hide": 100 }});
                            }
                            $(document).on('click', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                                $('.tooltip').remove();
                            });

                            if (type_id == 5) {
                                if (!$('#config_sub_list').find('#visible_comment_sub').parent().parent().hasClass('hidden_checkbox')) {
                                    $('#config_sub_list').find('#visible_comment_sub').parent().parent().addClass('hidden_checkbox');
                                }

                                if (!$('#config_sub_list').find('#maybe_allowed_sub').parent().parent().hasClass('hidden_checkbox')) {
                                    $('#config_sub_list').find('#maybe_allowed_sub').parent().parent().addClass('hidden_checkbox');
                                }

                                if (!$('#config_sub_list').find('#show_time_sub').parent().parent().hasClass('hidden_checkbox')) {
                                    $('#config_sub_list').find('#show_time_sub').parent().parent().addClass('hidden_checkbox');
                                }

                                if (!$('#config_sub_list').find('#undo_item_sub').parent().parent().hasClass('hidden_checkbox')) {
                                    $('#config_sub_list').find('#undo_item_sub').parent().parent().addClass('hidden_checkbox');
                                }

                                $('#config_sub_list').find('#move_item_sub').parent().parent().removeClass('hidden_checkbox');
                                $('#config_sub_list').find('#show_sub_completed_item').parent().parent().removeClass('hidden_checkbox');

                                if ($('#test_table_' + list_id).find('.heading_items_col').length == 1) {
                                    $('#test_table_' + list_id).find('.heading_items_col').addClass('hidden_heading');
                                }

                            } else if (type_id == 11) {
                                if (!$('#config_sub_list').find('#visible_comment_sub').parent().parent().hasClass('hidden_checkbox')) {
                                    $('#config_sub_list').find('#visible_comment_sub').parent().parent().addClass('hidden_checkbox');
                                }
                                if (!$('#config_sub_list').find('#undo_item_sub').parent().parent().hasClass('hidden_checkbox')) {
                                    $('#config_sub_list').find('#undo_item_sub').parent().parent().addClass('hidden_checkbox');
                                }

                                if (!$('#config_sub_list').find('#show_sub_completed_item').parent().parent().hasClass('hidden_checkbox')) {
                                    $('#config_sub_list').find('#show_sub_completed_item').parent().parent().addClass('hidden_checkbox');
                                }

                                $('#config_sub_list').find('#move_item_sub').parent().parent().removeClass('hidden_checkbox');
                                $('#config_sub_list').find('#show_time_sub').parent().parent().removeClass('hidden_checkbox');
                                $('#config_sub_list').find('#maybe_allowed_sub').parent().parent().removeClass('hidden_checkbox');
                                $('#test_table_' + list_id).find('.heading_items_col').removeClass('hidden_heading');
                            } else if (type_id == 2 || type_id == 8) {
                                if (!$('#config_sub_list').find('#maybe_allowed_sub').parent().parent().hasClass('hidden_checkbox')) {
                                    $('#config_sub_list').find('#maybe_allowed_sub').parent().parent().addClass('hidden_checkbox');
                                }

                                if (!$('#config_sub_list').find('#show_time_sub').parent().parent().hasClass('hidden_checkbox')) {
                                    $('#config_sub_list').find('#show_time_sub').parent().parent().addClass('hidden_checkbox');
                                }

                                if (!$('#config_sub_list').find('#show_sub_completed_item').parent().parent().hasClass('hidden_checkbox')) {
                                    $('#config_sub_list').find('#show_sub_completed_item').parent().parent().addClass('hidden_checkbox');
                                }


                                $('#config_sub_list').find('#visible_comment_sub').parent().parent().removeClass('hidden_checkbox');
                                $('#config_sub_list').find('#move_item_sub').parent().parent().removeClass('hidden_checkbox');
                                $('#config_sub_list').find('#undo_item_sub').parent().parent().removeClass('hidden_checkbox');
                                if ($('#test_table_' + list_id).find('.heading_items_col').length == 1) {
                                    $('#test_table_' + list_id).find('.heading_items_col').addClass('hidden_heading');
                                }
                            } else {
                                if (!$('#config_sub_list').find('#visible_comment_sub').parent().parent().hasClass('hidden_checkbox')) {
                                    $('#config_sub_list').find('#visible_comment_sub').parent().parent().addClass('hidden_checkbox');
                                }
                                if (!$('#config_sub_list').find('#maybe_allowed_sub').parent().parent().hasClass('hidden_checkbox')) {
                                    $('#config_sub_list').find('#maybe_allowed_sub').parent().parent().addClass('hidden_checkbox');
                                }

                                if (!$('#config_sub_list').find('#show_time_sub').parent().parent().hasClass('hidden_checkbox')) {
                                    $('#config_sub_list').find('#show_time_sub').parent().parent().addClass('hidden_checkbox');
                                }

                                if (!$('#config_sub_list').find('#show_sub_completed_item').parent().parent().hasClass('hidden_checkbox')) {
                                    $('#config_sub_list').find('#show_sub_completed_item').parent().parent().addClass('hidden_checkbox');
                                }

                                if (!$('#config_sub_list').find('#undo_item_sub').parent().parent().hasClass('hidden_checkbox')) {
                                    $('#config_sub_list').find('#undo_item_sub').parent().parent().addClass('hidden_checkbox');
                                }
                                $('#config_sub_list').find('#move_item_sub').parent().parent().removeClass('hidden_checkbox');

                                if ($('#test_table_' + list_id).find('.heading_items_col').length == 1) {
                                    $('#test_table_' + list_id).find('.heading_items_col').addClass('hidden_heading');
                                }

                            }
                            if (type_id != 11) {
                                if ($(window).width() >= 1350) {
                                    if ($('#content_' + list_id).find('.task_name').length <= 3) {
                                        if (!$('#tabs-' + list_id).find('#added_div').hasClass('add-data-left')) {
                                            $('#tabs-' + list_id).find('#added_div').addClass('add-data-left');
                                            $('#tabs-' + list_id).find('.my_table').addClass('table_one_page');
                                        }
                                    } else {
                                        if ($('#tabs-' + list_id).find('#added_div').hasClass('add-data-left')) {
                                            $('#tabs-' + list_id).find('#added_div').removeClass('add-data-left');
                                            $('#tabs-' + list_id).find('.my_table').removeClass('table_one_page');
                                        }
                                    }
                                } else if ($(window).width() >= 1200 && $(window).width() < 1350) {
                                    if ($('#content_' + list_id).find('.task_name').length <= 2) {
                                        if (!$('#tabs-' + list_id).find('#added_div').hasClass('add-data-left')) {
                                            $('#tabs-' + list_id).find('#added_div').addClass('add-data-left');
                                            $('#tabs-' + list_id).find('.my_table').addClass('table_one_page');
                                        }
                                    } else {
                                        if ($('#tabs-' + list_id).find('#added_div').hasClass('add-data-left')) {
                                            $('#tabs-' + list_id).find('#added_div').removeClass('add-data-left');
                                            $('#tabs-' + list_id).find('.my_table').removeClass('table_one_page');
                                        }
                                    }
                                }
                            } else {
                                if ($('#tabs-' + list_id).find('#added_div').hasClass('add-data-left')) {
                                    $('#tabs-' + list_id).find('#added_div').removeClass('add-data-left');
                                    $('#tabs-' + list_id).find('.my_table').removeClass('table_one_page');
                                }
                            }
                            $.ajax({
                                url: '<?php echo base_url() . 'listing/get_list_body'; ?>',
                                type: 'POST',
                                data: {
                                    'Listid': list_id,
                                },
                                success: function (res) {
                                    var resp = JSON.parse(res);
                                    $('#test_table_' + list_id).find('tbody').html(resp['body']);

                                    if (type_id == 11) {
                                        if($('#test_table_' + list_id).find("tbody").find('tr').length > 0){
                                            $('#test_table_' + list_id).find("tbody").sortable("disable");
                                        }
                                    } else {
                                        if($('#test_table_' + list_id).find("tbody").find('tr').length > 0){
                                            $('#test_table_' + list_id).find("tbody").sortable("enable");
                                        }
                                    }
                                    if($('#tabs-' + list_id).find('#listConfig_lnk').attr('data-moveallow') == 0){
                                        if($('#test_table_' + list_id).find("tbody").find('tr').length > 0){
                                            $('#test_table_' + list_id).find("tbody").sortable("disable");
                                        }
                                    }
                                }
                            });
                            var nexup_sun_grp_2_len = $('#content_' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').length;
                            if(nexup_sun_grp_2_len == 1){
                                $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '18px');
                            }else if(nexup_sun_grp_2_len == 2){
                                $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '16px');
                            }else{
                                $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '13px');
                            }
                        }
                    });
                }
            });

            $(document).on('click', '.whoisnext-btn-cmnt, .whoisnext-div .button-outer', function () {
            var list_id = 0;
            if($(this).hasClass('button-outer')){
                list_id = $(this).parent().parent().parent().parent().parent().find('.edit_list_task_sub').attr('data-id');
            }else{
                list_id = $(this).parent().parent().parent().parent().parent().parent().parent().find('.edit_list_task_sub').attr('data-id');
            }
                if ($('.plus-category').attr('data-access') == 0) {
                    alert('You don\'t have permission to perform this action!')
                    return false;
                }
                var url_post = '<?php echo base_url() . 'next_item'; ?>';
                if ($('#tabs-' + list_id).find('#config_lnk_sub').attr('data-typeid') == 8) {
                    url_post = '<?php echo base_url() . 'next_item_random'; ?>';
                }
                
                if ($('#test_table_' + list_id).find('tbody tr').length < 2) {
                    return false;
                }
                var last_task_id = $('#tabs-' + list_id).find('#TaskList li:nth-child(2)').attr('data-id');
                var list_id = $('#tabs-' + list_id).find('.icon-more-holder:eq(0)').attr('data-listid');
                var comment = $('#tabs-' + list_id).find('#nexup_comment').val();
                var user_ip = "<?php echo $_SERVER['REMOTE_ADDR']; ?>";

                $.ajax({
                    url: url_post,
                    type: 'POST',
                    data: {
                        'Listid': list_id,
                        'Taskid': last_task_id,
                        'comment': comment,
                        'user_ip': user_ip,
                    },
                    success: function (res) {
                        if (res != 'fail') {
                            $('#tabs-' + list_id).find('#nexup_comment').val('');
                            if (res != '') {
                                var next_elem_find = $("#test_table_" + list_id).find("[data-id='" + res + "']");
                                $('#tabs-' + list_id).find('#next_task_name').html($.trim($(next_elem_find).children().text()));
                                $('#tabs-' + list_id).find('.whoisnext-div .nexup-group .nexup-sub-group-one').attr('title', '');

                                var next_task = '';
                                if ($('#tabs-' + list_id).find('.whoisnext-div .nexup-group .nexup-group-two .button-outer .nexup-sub-group-one').length == 1) {
                                    next_task = $('#tabs-' + list_id).find(".whoisnext-div .nexup-group .nexup-group-two .button-outer .nexup-sub-group-one #next_task_name");
                                } else if ($('#tabs-' + list_id).find('.whoisnext-div .nexup-group .nexup-group-two .button-outer .nexup-sub-group-single').length == 1) {
                                    next_task = $('#tabs-' + list_id).find(".whoisnext-div .nexup-group .nexup-group-two .button-outer .nexup-sub-group-single #next_task_name");
                                }
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

//                                    $('.whoisnext-div .button-outer').attr('title', $.trim($(next_elem_find).children().text()));
                                $('#tabs-' + list_id).find('.whoisnext-div .nexup-group .nexup-sub-group-one').attr('data-original-title', $.trim($(next_elem_find).children().text()));
                                $('#tabs-' + list_id).find('.whoisnext-div .nexup-group .nexup-sub-group-single').attr('data-original-title', $.trim($(next_elem_find).children().text()));
                                var total_cols = $('#test_table_' + list_id).find('tbody tr:first-child td .add-data-div').length;
                                if (total_cols >= 1) {
                                    var index_row = $(next_elem_find).parent('td').parent().index();
                                    var loop_max = (total_cols - 1);
                                    if ($('#tabs-' + list_id).find('#config_lnk_sub').attr('data-typeid') == 3) {
                                        var loop_max = total_cols;
                                    }
                                    if (total_cols > 3) {
                                        var loop_max = 3;
                                    }

                                    if ($('#test_table_' + list_id).find('tbody tr:first-child td .edit_task').length > 1) {

                                        var sub_grp_two = '';
                                        for (j = 0; j < loop_max; j++) {
                                            sub_grp_two += '<span data-toggle="tooltip" title="' + $.trim($('#test_table tbody tr:nth-child(' + (index_row + 1) + ') td:eq(' + (j + 2) + ') .add-data-div .task_name_span').text()) + '">';
                                            sub_grp_two += $.trim($('#test_table_' + list_id).find('tbody tr:nth-child(' + (index_row + 1) + ') td:eq(' + (j + 2) + ') .add-data-div .task_name_span').text());
                                            sub_grp_two += '</span>';
                                        }
                                        sub_grp_two += '<p data-toggle="modal" data-target="#items-list" id="items_model_p" data-rowid="' + (index_row + 1) + '"><img src="<?php echo base_url() . 'assets/img/information-button-icon-23.png'; ?>"></p>';
                                        $('#tabs-' + list_id).find('.nexup-group .nexup-sub-group.nexup-sub-group-two').html(sub_grp_two);
                                    }
                                }
                            } else {
                                $('#tabs-' + list_id).find('#next_task_name').html($('#test_table tbody tr:first-child td:eq(0) .add-data-div .task_name_span').text());
                                $('#tabs-' + list_id).find('.whoisnext-div .button-outer').attr('title', $('#test_table tbody tr:first-child td:eq(0) .add-data-div .task_name_span').text());
                            }
                            var cm_val = 'no comment';
                            var cm_class = ' no-comt-class';
                            if (comment != '') {
                                cm_val = comment;
                                cm_class = '';
                            }
                            if($('#tabs-' + list_id).find('#log_dd').length > 0){
                                if($('#tabs-' + list_id).find('#log_dd').find('li').length == 6){
                                    $('#tabs-' + list_id).find('#log_dd').find('li:last').remove();
                                }
                                $('#tabs-' + list_id).find('#log_dd').prepend('<li class="log_options' + cm_class + '">' + cm_val + '<span class="comment-span">(Just Now)</span></li>');
                            }else{
                                var dd_log = '<ul class="dropdown-menu" id="log_dd" aria-labelledby="dropdownMenuLog">';
                                dd_log += '<li class="log_options"><span class="cmt_val' + cm_class + '">' + cm_val + '</span><span class="comment-span">(Just Now)</span></li>';
                                dd_log += '</ul>';
                                $('#tabs-' + list_id).find('#dropdownMenuLog_sub').after(dd_log);
                            }

                            var log_popup_apnd = '<tr>';
                            log_popup_apnd += '<td>';
                            log_popup_apnd += $(next_elem_find).children().text();
                            log_popup_apnd += '</td>';
                            log_popup_apnd += '<td>';
                            if (comment != '') {
                                log_popup_apnd += comment;
                            }
                            log_popup_apnd += '</td>';
                            log_popup_apnd += '<td>';
                            log_popup_apnd += 'Just Now';
                            log_popup_apnd += '</td>';
                            log_popup_apnd += '</tr>';

                            $('#tabs-' + list_id).find('#log_div table.log_table_popup tbody').prepend(log_popup_apnd);
                            set_nexup_box_data();

                        } else if (res == 'not allowed') {
                            alert('You are not allowed to perform this action. Please login to proceed with it!');
                        } else if (res == 'fail') {
                            alert('Something went wrong. Please try again!');
                        }
                        if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                            $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                event.preventDefault()
                            }).tooltip({delay: { "hide": 100 }});
                        }
                        $(document).on('click', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                            $('.tooltip').remove();
                        })
                    }
                });
//                    $('#nexup_cmnt_span').addClass('hide_box');
//                }
                $('#nexup_comment').focus();
                return false;
            });

//            $(document).on('keydown', '#nexup_comment', function (evt) {
//                var key_code = evt.keyCode;
//                if (key_code == 27) {
//                    $('#nexup_comment').val('');
//                    $('#nexup_cmnt_span').addClass('hide_box');
//                }
//            });

            //Who's next call
            $(document).on('click', '.whoisnext-btn', function () {
//                if ($('.icon-settings').attr('data-locked') == 1) {
//                    alert('This list is locked. Please unlock it to perform any operation!');
//                    return false;
//                }
                if ($('#TaskList li').length < 2) {
                    return false;
                }
                var last_task_id = $('#TaskList li:first-child').attr('data-id');
                var list_id = $('.edit_list_task').attr('data-id');
                $.ajax({
                    url: '<?php echo base_url() . 'next_item'; ?>',
                    type: 'POST',
                    data: {
                        'Listid': list_id,
                        'Taskid': last_task_id
                    },
                    success: function (res) {
                        if (res == 'success') {
                            $('#TaskList li:first-child').appendTo('#TaskList');
                            $('#next_task_name').html($('#TaskList li:first-child').text());
                            $('.whoisnext-div .button-outer').attr('title', $('#TaskList li:first-child').text());
                            $.ajax({
                                url: '<?php echo base_url() . 'next_task'; ?>',
                                type: 'POST',
                                data: {
                                    'Listid': list_id,
                                    'Taskid': last_task_id
                                },
                                success: function (resp) {
                                }
                            });
                        } else if (res == 'not allowed') {
                            alert('You are not allowed to perform this action. Please login to proceed with it!');
                        } else if (res == 'fail') {
                            alert('Something went wrong. Please try again!');
                        }
                    }
                });
            });

            //Display next item on who's next button mouse hover
//            $(document).on('mouseover', '.whoisnext-div .button-outer .whoisnext-btn', function () {
//                $('#next_task_name').html($('#TaskList li:first-child').text());
////                $('.whoisnext-div .button-outer').addClass('whoisnext-whole-section');
//            });
//            $(document).on('mouseout', '.whoisnext-div .button-outer', function () {
////                $('.whoisnext-div .button-outer').removeClass('whoisnext-whole-section');
//                $('#next_task_name').html('');
//            });

            //Lock the list
            $(document).on('click', '#listLock_lnk', function () {
                var list_id = $(this).attr('data-id');
                var list_slug = $(this).attr('data-slug');
                $.ajax({
                    url: '<?php echo base_url() . 'lock_nexup_list'; ?>',
                    type: 'POST',
                    data: {
                        'Listid': list_id,
                        'Lock': 1,
                    },
                    success: function (resp) {
                        var res = JSON.parse(resp);
                        if (res['success'] == 'fail') {
                            alert('Something went wroong. Your list was not locked. Please try again!')
                        } else if (res['success'] == 'not allowed') {
                            alert('You are not allowed to lock this list.');
                        } else if (res['success'] == 'success') {
                            $("#TaskList").sortable("disable");
                            $('.icon-settings').attr('data-locked', '1');
                            if ($('.hide_add_item').length == 0) {
                                $('#add_task_li .add-data-div').addClass('hide_add_item');
                            }
//                            $('.delete_task').hide();
                            $('#listLock_lnk').remove();
                            $('#listUnlock_lnk').remove();
                            $('.config_icons #add_data_desc').before('<a class="icon-lock2 custom_cursor" id="listUnlock_lnk" data-id="' + list_id + '" data-slug="' + list_slug + '" data-toggle="tooltip" title="Unlock List" data-placement="bottom"></a>');
                            if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                                $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                    event.preventDefault()
                                }).tooltip({delay: { "hide": 100 }});
                            }
//                            $('#addTaskDiv').mCustomScrollbar("destroy");
                            $('#addTaskDiv').accordion({
                                collapsible: true,
                                heightStyle: "content",
                                active: false
                            });
                            $('.added_div .add-data-head-r span.ui-accordion-header-icon.ui-icon.ui-icon-triangle-1-e').css('display', 'none');
                            $('.add-data-head-r').addClass('hide_add');
                            if(res['owner'] == 0){
                                $('#config_lnk').hide();
                                var lock_icn = '<a class="icon-lock2 custom_cursor" id="config_lcoked" data-locked="1"></a>';
                                $('#config_lnk').before(lock_icn);
                                $('.add-data-head-r').addClass('hidden_add_col');
                                $('#config_icons').addClass('hide_data');
                                $('.heading_items_col_add').addClass('hidden_add_row');
                                
                                    if(!$('.head_custom').find('.edit_list_task').hasClass('no_hover_table')){
                                        $('.head_custom').find('.edit_list_task').addClass('no_hover_table');
                                    }
                                if($('.h-nav_with_login').length == 0){
                                    $('#config_icons').find('a').each(function(){
                                        if(!$(this).hasClass('no-pointer')){
                                            $(this).addClass('no-pointer');
                                        }
                                    });
                                    if(!$('#test_table').hasClass('no_hover_table')){
                                        $('#test_table').addClass('no_hover_table')
                                    }
                                }
                                
                            }
                            $.ajax({
                                url: '<?php echo base_url() . 'lock_list'; ?>',
                                type: 'POST',
                                data: {
                                    'Listid': list_id,
                                    'Lock': 1,
                                },
                                success: function (res) {
                                }
                            });
                        }
                    }
                });
            });
            
            
            $(document).on('click', '#listsub_Lock_lnk', function () {
                var list_id = $(this).attr('data-id');
                var list_slug = $(this).attr('data-slug');
                $(this).css('pointer-events', 'none');
                $.ajax({
                    url: '<?php echo base_url() . 'lock_nexup_list'; ?>',
                    type: 'POST',
                    context: this,
                    data: {
                        'Listid': list_id,
                        'Lock': 1,
                    },
                    success: function (resp) {
                        var res = JSON.parse(resp);
                        if (res['success'] == 'fail') {
                            alert('Something went wroong. Your list was not locked. Please try again!');
                            $(this).css('pointer-events', 'all');
                        } else if (res['success'] == 'not allowed') {
                            alert('You are not allowed to lock this list.');
                            $(this).css('pointer-events', 'all');
                        } else if (res['success'] == 'success') {
                            $("#TaskList").sortable("disable");
                            $(this).parent().parent().find('.icon-settings-sub').attr('data-locked', '1');
                            $(this).remove();
                            $('#tabs-' + list_id).find('#add_sub_data_desc').before('<a class="icon-lock2 custom_cursor" id="listSub_Unlock_lnk" data-id="' + list_id + '" data-slug="' + list_slug + '" data-toggle="tooltip" title="Unlock List" data-placement="bottom"></a>');
                            if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                                $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                    event.preventDefault()
                                }).tooltip({delay: { "hide": 100 }});
                            }
//                            $('#addTaskDiv').mCustomScrollbar("destroy");
                            if($('#listConfig_lnk_' + list_id).attr('data-collapsed') == 1){
                                $('#tabs-' + list_id).find('#addSubTaskDiv').accordion({
                                    collapsible: true,
                                    heightStyle: "content",
                                });
                            }else{
                                $('#tabs-' + list_id).find('#addSubTaskDiv').accordion({
                                    collapsible: true,
                                    heightStyle: "content",
                                    active: false
                                });
                            }
                            $('#tabs-' + list_id).find('.added_div .add-data-head-r span.ui-accordion-header-icon.ui-icon.ui-icon-triangle-1-e').css('display', 'none');
                            $('#tabs-' + list_id).find('.add-data-head-r').addClass('hide_add');
                            if(res['owner'] == 0){
                                $('#tabs-' + list_id).find('#config_lnk_sub').hide();
                                var lock_icn = '<a class="icon-lock2 custom_cursor" id="config_sub_lcoked" data-locked="1"></a>';
                                $('#tabs-' + list_id).find('#config_lnk_sub').before(lock_icn);
                                
                                $('#tabs-' + list_id).find('.add-data-head-r').addClass('hidden_add_col');
                                $('#tabs-' + list_id).find('.config_icons_sub').addClass('hide_data');
                                $('#tabs-' + list_id).find('.heading_items_col_add').addClass('hidden_add_row');
                                
                                    if(!$('#tabs-' + list_id).find('.head_custom').find('.edit_list_task').hasClass('no_hover_table')){
                                        $('#tabs-' + list_id).find('.head_custom').find('.edit_list_task').addClass('no_hover_table');
                                    }
                                if($('.h-nav_with_login').length == 0){
                                    $('#tabs-' + list_id).find('#config_icons').find('a').each(function(){
                                        if(!$(this).hasClass('no-pointer')){
                                            $(this).addClass('no-pointer');
                                        }
                                    });
                                    if(!$('#tabs-' + list_id).find('#test_table').hasClass('no_hover_table')){
                                        $('#tabs-' + list_id).find('#test_table').addClass('no_hover_table')
                                    }
                                }
                                
                            }
                            $.ajax({
                                url: '<?php echo base_url() . 'lock_list'; ?>',
                                type: 'POST',
                                data: {
                                    'Listid': list_id,
                                    'Lock': 1,
                                },
                                success: function (res) {
                                }
                            });
                        }
                    }
                });
            });

            //Unlock the list
            $(document).on('click', '#listUnlock_lnk', function () {
                $(this).css('pointer-events', 'none');
                var list_id = $(this).attr('data-id');
                var list_slug = $(this).attr('data-slug');
                $.ajax({
                    url: '<?php echo base_url() . 'lock_nexup_list'; ?>',
                    type: 'POST',
                    data: {
                        'Listid': list_id,
                        'Lock': 0,
                    },
                    success: function (resp) {
                        var res = JSON.parse(resp);
                        if (res['success'] == 'fail') {
                            alert('Something went wroong. Your list was not unlocked. Please try again!')
                        } else if (res['success'] == 'not allowed') {
                            alert('You are not allowed to unlock this list.');
                        } else if (res['success'] == 'success') {
                            $("#TaskList").sortable("enable");
                            $('.icon-settings').attr('data-locked', '0');
                            $('#add_task_li .add-data-div').removeClass('hide_add_item');
                            $('.delete_task').show();
                            $('#listLock_lnk').remove();
                            $('#listUnlock_lnk').remove();
                            $('.config_icons #add_data_desc').before('<a class="icon-lock-open2 custom_cursor" id="listLock_lnk" data-id="' + list_id + '" data-slug="' + list_slug + '" data-toggle="tooltip" title="Lock List" data-placement="bottom"></a>');
                            if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                                $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                    event.preventDefault()
                                }).tooltip({delay: { "hide": 100 }});
                            }
                            $('#addTaskDiv').accordion("destroy");
                            var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.pushState({path:newurl},'',newurl);
                            $('div#TaskListDiv').css('width', $('#test_table').width());
                            $('.add-data-head-r').removeClass('hide_add');
                            $.ajax({
                                url: '<?php echo base_url() . 'lock_list'; ?>',
                                type: 'POST',
                                data: {
                                    'Listid': list_id,
                                    'Lock': 0,
                                },
                                success: function (res) {
                                }
                            });
                        }
                    }
                });
            });
            
            $(document).on('click', '#listSub_Unlock_lnk', function () {
                var list_id = $(this).attr('data-id');
                var list_slug = $(this).attr('data-slug');
                $(this).css('pointer-events', 'none');
                $.ajax({
                    url: '<?php echo base_url() . 'lock_nexup_list'; ?>',
                    type: 'POST',
                    context: this,
                    data: {
                        'Listid': list_id,
                        'Lock': 0,
                    },
                    success: function (resp) {
                        var res = JSON.parse(resp);
                        if (res['success'] == 'fail') {
                            alert('Something went wroong. Your list was not unlocked. Please try again!')
                        } else if (res['success'] == 'not allowed') {
                            alert('You are not allowed to unlock this list.');
                        } else if (res['success'] == 'success') {
                            $("#TaskList").sortable("enable");
                            $(this).parent().parent().find('.icon-settings-sub').attr('data-locked', '1');
                            $('#add_task_li .add-data-div').removeClass('hide_add_item');
                            $('#test_table_' + list_id).find('.delete_task').show();
                            $(this).remove();
                            $('#tabs-' + list_id).find('#add_sub_data_desc').before('<a class="icon-lock-open2 custom_cursor" id="listsub_Lock_lnk" data-id="' + list_id + '" data-slug="' + list_slug + '" data-toggle="tooltip" title="Lock List" data-placement="bottom"></a>');
                            if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                                $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                    event.preventDefault()
                                }).tooltip({delay: { "hide": 100 }});
                            }
                            $('#tabs-' + list_id).find('#addSubTaskDiv').accordion("destroy");
                            var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.pushState({path:newurl},'',newurl);
                            $('#tabs-' + list_id).find('div#TaskListDiv').css('width', $('#test_table').width());
                            $('#tabs-' + list_id).find('.add-data-head-r').removeClass('hide_add');
                            $.ajax({
                                url: '<?php echo base_url() . 'lock_list'; ?>',
                                type: 'POST',
                                data: {
                                    'Listid': list_id,
                                    'Lock': 0,
                                },
                                success: function (res) {
                                }
                            });
                        }
                    }
                });
            });
            $(window).on("orientationchange", function () {
                var screen_size = window.screen.width;
                var isAccordion = $("#header-r-accordian").hasClass("ui-accordion");
                if (screen_size < 521) {
                    if (isAccordion == false) {
                        $("#header-r-accordian").accordion({
                            collapsible: true
                        });
                    }
                } else {
                    if (isAccordion == true) {
                        $("#header-r-accordian").accordion("destroy");
                    }
                }
                
                if($('#config_lnk').attr('data-typeid') == 2){
                    var list_id = $('#list_desc_text').attr('data-listid');
                    $.ajax({
                        url: '<?php echo base_url(); ?>get_nexup_box',
                        type: 'POST',
                        data: {
                            'list_id': list_id,
                        },
                        success: function (res) {
                            if(res != ''){
                                var row_indx = $('#span_task_' + res).parent().parent().parent().index();
                                if($('.icon-settings').attr('data-typeid') == 2){
                                    $('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                    $('.nexup-sub-group-one').attr('data-original-title', $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                    var item_list_nexup_data = '<tr><td>' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                    var grp_two = '';
                                    for(s = 1; s < 4; s++){
                                        if($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').length == 1){
                                            grp_two += '<span data-toggle="tooltip" title="" data-original-title="' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) +'">' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</span>';
                                            item_list_nexup_data += '<tr><td>' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                        }
                                    }
                                    grp_two += '<p data-toggle="modal" data-target="#items-list" id="items_model_p" data-rowid="' + (row_indx + 1) +'" data-placement="top" title="Show all items"><img src="<?php echo base_url(); ?>assets/img/information-button-icon-23.png"></p>';
                                    if(grp_two != ''){
                                        $('.button-outer').find('.nexup-sub-group-two').html(grp_two);
                                    }

                                }else if($('.icon-settings').attr('data-typeid') == 8){
                                     $('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                }
                                if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                                    $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                        event.preventDefault()
                                    }).tooltip({delay: { "hide": 100 }});
                                }
                                $(document).on('click', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                                    $('.tooltip').remove()
                                });
                            }
                             var nexup_sun_grp_2_len = $('.button-outer').find('.nexup-sub-group-two').find('span').length;
                             if(nexup_sun_grp_2_len == 1){
                                 $('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '18px');
                             }else if(nexup_sun_grp_2_len == 2){
                                 $('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '16px');
                             }else{
                                 $('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '13px');
                             }
                             set_nexup_box_data();
                        }
                    });
                }
            });

            $(document).on('click', '.enable-move', function () {
                var list_id = $(this).parent().find('.sub_listConfig_lnk').attr('data-listid');
                if ($('#test_table_' + list_id).find('.icon-more').css('opacity') == 0) {
                    $('#test_table_' + list_id).find('.icon-more').css('opacity', 1);
                } else {
                    $('#test_table_' + list_id).find('.icon-more').css('opacity', 0);
                }
                if ($('#test_table_' + list_id).find('.delete_task').css('opacity') == 0) {
                    $('#test_table_' + list_id).find('.delete_task').css('opacity', 1);
                } else {
                    $('#test_table_' + list_id).find('.delete_task').css('opacity', 0);
                }
            });

            if ($('.collapse_div').length > 0) {
                $('#addTaskDiv').accordion({
                    collapsible: true,
                    heightStyle: "content",
                    active: false
                });
            }

            $(function () {

                var next_task = $(".whoisnext-div #next_task_name");

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

            });


            $(document).on('click', '.undo-btn', function () {
                if ($('.plus-category').attr('data-access') == 0) {
                    alert('You don\'t have permission to perform this action!')
                    return false;
                }
                var list_id = $(this).attr('data-listid');
                $.ajax({
                    url: '<?php echo base_url(); ?>undo_nexup',
                    type: 'POST',
                    data: {'list_id': list_id},
                    success: function (resp) {
                        if (resp != 'fail') {

                            if($('#tabs-' + list_id).find('.icon-settings').attr('data-typeid') == 2){
                                $.ajax({
                                   url: '<?php echo base_url(); ?>get_nexup_box',
                                   type: 'POST',
                                   data: {
                                       'list_id': list_id,
                                   },
                                   success: function (res) {
                                       if(res != ''){
                                           var row_indx = $('#tabs-' + list_id).find('#span_task_' + res).parent().parent().parent().index();
                                           if($('#tabs-' + list_id).find('.icon-settings').attr('data-typeid') == 2){
                                               $('#tabs-' + list_id).find('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                               $('#tabs-' + list_id).find('.nexup-sub-group-one').attr('data-original-title', $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                               var item_list_nexup_data = '<tr><td>' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                               var grp_two = '';
                                               for(s = 1; s < 4; s++){
                                                   if($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').length == 1){
                                                       grp_two += '<span data-toggle="tooltip" title="" data-original-title="' + $.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) +'">' + $.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</span>';
                                                       item_list_nexup_data += '<tr><td>' + $.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                                   }
                                               }
                                               grp_two += '<p data-toggle="modal" data-target="#items-list" id="items_model_p" data-rowid="' + (row_indx + 1) +'" data-placement="top" title="Show all items"><img src="<?php echo base_url(); ?>assets/img/information-button-icon-23.png"></p>';
                                               if(grp_two != ''){
//                                                   $('.nexup-sub-group-two').html(grp_two);
                                                   $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').html(grp_two);
                                               }

                                           }else if($('#tabs-' + list_id).find('.icon-settings').attr('data-typeid') == 8){
                                                $('#tabs-' + list_id).find('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                           }
                                           if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                                                $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                                    event.preventDefault()
                                                }).tooltip({delay: { "hide": 100 }});
                                            }
                                           $(document).on('click', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                                               $('.tooltip').remove()
                                           });
                                           set_nexup_box_data();
                                       }
                                       var nexup_sun_grp_2_len = $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').length;
                                       if(nexup_sun_grp_2_len == 1){
                                           $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '18px');
                                       }else if(nexup_sun_grp_2_len == 2){
                                           $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '16px');
                                       }else{
                                           $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '13px');
                                       }

                                   }
                               });
                            }else{
                                var next_elem_find = $("#test_table_" + list_id).find("[data-id='" + resp + "']");
                                $('#tabs-' + list_id).find('#next_task_name').html($(next_elem_find).children().text());
                                $('#tabs-' + list_id).find('.whoisnext-div .nexup-group .nexup-sub-group-one').attr('data-original-title', $(next_elem_find).children().text());

                                var next_task = $('#tabs-' + list_id).find(".whoisnext-div #next_task_name");
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

                                $('#tabs-' + list_id).find('.whoisnext-div .button-outer').attr('data-original-title', $(next_elem_find).children().text());


                                var total_cols = $('#test_table_' + list_id).find('tbody tr:first-child td .add-data-div').length;
                                if (total_cols > 1) {
                                    var index_row = $(next_elem_find).parent('td').parent().index();
                                    var loop_max = (total_cols - 1);
                                    if (total_cols > 3) {
                                        var loop_max = 3;
                                    }

                                    if ($('#test_table_' + list_id).find('tbody tr:first-child td .edit_task').length > 1) {
                                        var sub_grp_two = '';
                                        for (var k = 0; k < loop_max; k++) {
                                            sub_grp_two += '<span data-toggle="tooltip" title="' + $('#test_table_' + list_id).find('tbody tr:nth-child(' + (index_row + 1) + ') td:eq(' + (k + 2) + ') .add-data-div .task_name_span').text().trim() + '">';
                                            sub_grp_two += $('#test_table_' + list_id).find('tbody tr:nth-child(' + (index_row + 1) + ') td:eq(' + (k + 2) + ') .add-data-div .task_name_span').text();
                                            sub_grp_two += '</span>';
                                        }
                                        sub_grp_two += '<p data-toggle="modal" data-target="#items-list" id="items_model_p" data-rowid="' + (index_row + 1) + '"><img src="<?php echo base_url() . 'assets/img/information-button-icon-23.png'; ?>"></p>';
                                        $('#tabs-' + list_id).find('.nexup-group .nexup-sub-group.nexup-sub-group-two').html(sub_grp_two);
                                        if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                                            $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                                event.preventDefault()
                                            }).tooltip({delay: { "hide": 100 }});
                                        }
                                        $(document).on('click', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                                            $('.tooltip').remove();
                                        })
                                    }
                                }
                            }
                        }
                    }
                });
            });

            $(document).on('click', '#dropdownMenuLog', function () {
                $('#log-list').modal('show');
            });
            $(document).on('click', '#dropdownMenuLog_sub', function () {
                var list_id = $(this).attr('data-listid');
                $.ajax({
                    url: '/get_list_log',
                    type: 'POST',
                    context: this,
                    data: {list_id: list_id},
                    success: function (res) {
                        if(res == 'empty'){
                            alert('Ooops, Log was not found');
                        }else if(res != '' && res != 'empty'){
                            $('#log-list-sub').find('#log_sub_table_popup').find('tbody').html(res);
                        }
                    }
                });
                $(document).find('#log-list-sub').find('.logmodal-body-sub').find('.export_to_csv_btn').attr('href', "<?php echo base_url(); ?>export_log/" + list_id);
                $('#log-list-sub').modal('show');
            });

            $(document).on('mouseover', '#TaskAdd', function () {
                $('#add_column_li').show();
            });
            $(document).on('mouseout', '#TaskAdd', function () {
                $('#add_column_li').hide();
            });


            function add_cols(cells_cnt, col_name, list_id) {
                $('#nexup_column').prop('disabled', true);
                $('#save_col').prop('disabled', true);
                var completed_items = [];
                var yes_items = [];
                var no_items = [];
                var maybe_items = [];
                $('#test_table tbody tr').each(function () {
                    if ($(this).hasClass('completed')) {
                        var order_item = $(this).children('td.icon-more-holder').attr('data-order');
                        completed_items.push(order_item);
                    }
                });
                $('.present_lbl').each(function () {
                    if ($(this).hasClass('green_label')) {
                        yes_items.push($(this).closest('td.icon-more-holder').attr('data-order'));
                    }
                    if ($(this).hasClass('red_label')) {
                        no_items.push($(this).closest('td.icon-more-holder').attr('data-order'));
                    }
                    if ($(this).hasClass('yellow_label')) {
                        maybe_items.push($(this).closest('td.icon-more-holder').attr('data-order'));
                    }
                });
                completed_items = completed_items.join(',');
                yes_items = yes_items.join(',');
                no_items = no_items.join(',');
                maybe_items = maybe_items.join(',');
                $.ajax({
                    url: '<?php echo base_url(); ?>task/add_column',
                    type: 'POST',
                    data: {
                        'col_name': col_name,
                        'list_id': list_id,
                        'cells_cnt': cells_cnt,
                        'completed_items': completed_items,
                        'yes_items': yes_items,
                        'no_items': no_items,
                        'maybe_items': maybe_items,
                    },
                    success: function (res) {
                        if (res == 'not_allowed') {
                            alert('Nope...\nThis operation is not allowed.');
                            return false;
                        } else if (res != 'fail' && res != 'empty') {

                            $('#nexup_column').val('');

                            var resp = JSON.parse(res);

                            if ($('.heading_items_col').length == 0) {
                                if ($('.icon-settings').attr('data-typeid') == 3) {
                                    $('.td_arrange_tr').append('<th class="noDrag rank_th_head"></th>');
                                }
//                                $('.td_arrange_tr').append('<th class="noDrag"></th>');
                                $('.td_arrange_tr').append(resp.first_col);
                            } else {
                                if ($('.heading_items_col').hasClass('hidden_heading')) {
                                    $('.heading_items_col').removeClass('hidden_heading')
                                }
                            }
                            if ($('.td_arrange_tr').length != 0) {
                                $('.td_arrange_tr .heading_items_col:last').after(resp.new_col);
                            } else {
                                var tarrange_tr = '<tr class="td_arrange_tr ui-sortable">';
                                if ($('.icon-settings').attr('data-typeid') == 3) {
                                    tarrange_tr += '<th class="noDrag rank_th_head"></th>';
                                }
                                tarrange_tr += '<th class="noDrag"></th>';
                                if (resp.first_col != '' || resp.first_col != 'undefined') {
                                    tarrange_tr += resp.first_col;
                                }
                                tarrange_tr += resp.new_col;
                                tarrange_tr += '</tr>';
                                $('#test_table thead').append(tarrange_tr);
                            }

//                            $('.td_add_tr').append(resp.new_col_input);
                            $('.td_add_tr .heading_items_col_add:last').after(resp.new_col_input);

//                            if(resp.last_log > 0){
                            if ($('#config_lnk').attr('data-typeid') == 2) {
                                if ($('.heading_items_col').length > 0) {


                                    var log_index = $('div[data-id="' + resp.last_log + '"]').parents().parents().index();
                                    var first_log_item = $('#test_table tbody tr:eq(' + log_index + ') td.list-table-view:eq(0) .edit_task .task_name_span').text();
                                    $('.whoisnext-div .nexup-group .nexup-group-two .nexup-sub-group-one .next_task_name').html(first_log_item);
                                    if ($('.whoisnext-div .nexup-group .nexup-group-two .nexup-sub-group-two').length == 0) {
                                        var second_subgrp = '<div class="nexup-sub-group nexup-sub-group-two">';
                                        for (var m = 1; m < 4; m++) {
                                            if ($('#test_table tbody tr:eq(' + log_index + ') td.list-table-view:eq(' + (m - 1) + ')').length > 0) {
                                                var appnd_data = $('#test_table tbody tr:eq(' + log_index + ') td.list-table-view:eq(' + m + ') .edit_task .task_name_span').text();
                                                second_subgrp += '<span data-toggle="tooltip" title="' + $.trim(appnd_data) + '">' + appnd_data + '</span>';

                                            }
                                        }

                                        second_subgrp += '<p data-toggle="modal" data-target="#items-list" id="items_model_p" data-rowid="' + (log_index + 1) + '" data-placement="top" title="Show all items">';
                                        second_subgrp += '<img src="<?php echo base_url(); ?>assets/img/information-button-icon-23.png">';
                                        second_subgrp += '</p>';
                                        second_subgrp += '</div>';

                                        $('.whoisnext-div .nexup-group .nexup-group-two .button-outer').append(second_subgrp);
                                    }

                                }
                            }
                            
                            var j = 0;
                            var max_col = resp.new_col_data.length;
                            for (j = 0; j <= max_col; j++) {
                                var k = j + 1;
                                $('#test_table tbody tr').each(function () {
                                    var ord = $(this).find('.icon-more-holder').attr('data-order');
                                    if (ord == k) {
                                        $(this).find('.list-table-view:last').after(resp.new_col_data[j]);
                                    }
                                });
                            }
                            if ($('#task_name:eq(0)').attr('data-colid') == 0) {
                                var add_data_col_id_first = $('.add-data-title').attr('data-colid');
                                $('#task_name:eq(0)').attr('data-colid', add_data_col_id_first);
                            }

                            $('.col-modal').modal('hide');
                            if ($('.heading_items_col').hasClass('hidden_heading')) {
                                $('.heading_items_col').removeClass('hidden_heading');
                            }
                            if($('.icon-settings').attr('data-typeid') == 2 || $('.icon-settings').attr('data-typeid') == 8){
                             $.ajax({
                                url: '<?php echo base_url(); ?>get_nexup_box',
                                type: 'POST',
                                data: {
                                    'list_id': list_id,
                                },
                                success: function (res) {
                                    if(res != ''){
                                        var row_indx = $('#span_task_' + res).parent().parent().parent().index();
                                        if($('.icon-settings').attr('data-typeid') == 2){
                                            $('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                            $('.nexup-sub-group-one').attr('data-original-title', $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                            var item_list_nexup_data = '<tr><td>' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                            var grp_two = '';
                                            for(s = 1; s < 4; s++){
                                                if($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').length == 1){
                                                    grp_two += '<span data-toggle="tooltip" title="" data-original-title="' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) +'">' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</span>';
                                                    item_list_nexup_data += '<tr><td>' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                                }
                                            }
                                            grp_two += '<p data-toggle="modal" data-target="#items-list" id="items_model_p" data-rowid="' + (row_indx + 1) +'" data-placement="top" title="Show all items"><img src="<?php echo base_url(); ?>assets/img/information-button-icon-23.png"></p>';
                                            if(grp_two != ''){
                                                $('.button-outer').find('.nexup-sub-group-two').html(grp_two);
                                            }
                                            
                                        }else if($('.icon-settings').attr('data-typeid') == 8){
                                             $('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                        }
                                        if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                                            $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                                event.preventDefault()
                                            }).tooltip({delay: { "hide": 100 }});
                                        }
                                        $(document).on('click', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                                            $('.tooltip').remove()
                                        });
                                        set_nexup_box_data();
                                    }
                                    var nexup_sun_grp_2_len = $('.button-outer').find('.nexup-sub-group-two').find('span').length;
                                    if(nexup_sun_grp_2_len == 1){
                                        $('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '18px');
                                    }else if(nexup_sun_grp_2_len == 2){
                                        $('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '16px');
                                    }else{
                                        $('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '13px');
                                    }
                                    
                                }
                            });
                            }
                        } else {
                            $('.col_msg').text('Please enter column name!');
                            $('.col_msg').addClass('alert-danger');
                            $('.col_msg').show();
                        }
                        if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                            $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                event.preventDefault()
                            }).tooltip({delay: { "hide": 100 }});
                        }
                        $(document).on('click', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                            $('.tooltip').remove();
                        })

                        $("#TaskListDiv ul.tasks_lists_display:last-child #task_name").focus();

                        if ($('#config_lnk').attr('data-typeid') != 11) {
                            if ($(window).width() >= 1350) {
                                if ($('.task_name').length <= 3) {
                                    if (!$('#added_div').hasClass('add-data-left')) {
                                        $('#added_div').addClass('add-data-left');
                                        $('.my_table').addClass('table_one_page');
                                    }
                                } else {
                                    if ($('#added_div').hasClass('add-data-left')) {
                                        $('#added_div').removeClass('add-data-left');
                                        $('.my_table').removeClass('table_one_page');
                                    }
                                }
                            } else if ($(window).width() >= 1200 && $(window).width() < 1350) {
                                if ($('.task_name').length <= 2) {
                                    if (!$('#added_div').hasClass('add-data-left')) {
                                        $('#added_div').addClass('add-data-left');
                                        $('.my_table').addClass('table_one_page');
                                    }
                                } else {
                                    if ($('#added_div').hasClass('add-data-left')) {
                                        $('#added_div').removeClass('add-data-left');
                                        $('.my_table').removeClass('table_one_page');
                                    }
                                }
                            }
                        } else {
                            if ($('#added_div').hasClass('add-data-left')) {
                                $('#added_div').removeClass('add-data-left');
                                $('.my_table').removeClass('table_one_page');
                            }
                        }
                        $('#nexup_column').prop('disabled', false);
                        $('#save_col').prop('disabled', false);
                    },
                    error: function (textStatus, errorThrown) {
                        $('#nexup_column').prop('disabled', false);
                        $('#save_col').prop('disabled', false);
                    },
                    complete: function (data) {
                        $('#nexup_column').prop('disabled', false);
                        $('#save_col').prop('disabled', false);
                    }
                });
            }
            
            
            function add_sub_cols(cells_cnt, col_name, list_id) {
                $('#nexup_sub_column').prop('disabled', true);
                $('#save_sub_col').prop('disabled', true);
                var completed_items = [];
                var yes_items = [];
                var no_items = [];
                var maybe_items = [];
                $('#test_table_' + list_id + ' tbody tr').each(function () {
                    if ($(this).hasClass('completed')) {
                        var order_item = $(this).children('td.icon-more-holder').attr('data-order');
                        completed_items.push(order_item);
                    }
                });
                $('#test_table_' + list_id).find('.present_lbl').each(function () {
                    if ($(this).hasClass('green_label')) {
                        yes_items.push($(this).closest('td.icon-more-holder').attr('data-order'));
                    }
                    if ($(this).hasClass('red_label')) {
                        no_items.push($(this).closest('td.icon-more-holder').attr('data-order'));
                    }
                    if ($(this).hasClass('yellow_label')) {
                        maybe_items.push($(this).closest('td.icon-more-holder').attr('data-order'));
                    }
                });
                completed_items = completed_items.join(',');
                yes_items = yes_items.join(',');
                no_items = no_items.join(',');
                maybe_items = maybe_items.join(',');
                $.ajax({
                    url: '<?php echo base_url(); ?>task/add_column',
                    type: 'POST',
                    data: {
                        'col_name': col_name,
                        'list_id': list_id,
                        'cells_cnt': cells_cnt,
                        'completed_items': completed_items,
                        'yes_items': yes_items,
                        'no_items': no_items,
                        'maybe_items': maybe_items,
                        'type_column_list': 'child',
                    },
                    success: function (res) {
                        if (res == 'not_allowed') {
                            alert('Nope...\nThis operation is not allowed.');
                            return false;
                        } else if (res != 'fail' && res != 'empty') {

                            $('#nexup_sub_column').val('');

                            var resp = JSON.parse(res);

                            if ($('#test_table_' + list_id).find('.heading_items_col').length == 0) {
                                if ($('#tabs-' + list_id).find('.icon-settings-sub').attr('data-typeid') == 3) {
                                    $('#test_table_' + list_id).find('.td_arrange_tr').append('<th class="noDrag rank_th_head"></th>');
                                }
//                                $('.td_arrange_tr').append('<th class="noDrag"></th>');
                                $('#test_table_' + list_id).find('.td_arrange_tr').append(resp.first_col);
                            } else {
                                if ($('#test_table_' + list_id).find('.heading_items_col').hasClass('hidden_heading')) {
                                    $('#test_table_' + list_id).find('.heading_items_col').removeClass('hidden_heading')
                                }
                            }
                            if ($('.td_arrange_tr').length != 0) {
                                $('#test_table_' + list_id).find('.td_arrange_tr .heading_items_col:last').after(resp.new_col);
                            } else {
                                var tarrange_tr = '<tr class="td_arrange_tr ui-sortable">';
                                if ($('#tabs-' + list_id).find('.icon-settings').attr('data-typeid') == 3) {
                                    tarrange_tr += '<th class="noDrag rank_th_head"></th>';
                                }
                                tarrange_tr += '<th class="noDrag"></th>';
                                if (resp.first_col != '' || resp.first_col != 'undefined') {
                                    tarrange_tr += resp.first_col;
                                }
                                tarrange_tr += resp.new_col;
                                tarrange_tr += '</tr>';
                                $('#test_table_' + list_id).find('thead').append(tarrange_tr);
                            }

                            $('#test_table_' + list_id).find('.td_add_tr .heading_items_col_add:last').after(resp.new_col_input);

                            if ($('#tabs-' + list_id).find('#config_lnk_sub').attr('data-typeid') == 2) {
                                if ($('#test_table_' + list_id).find('.heading_items_col').length > 0) {


                                    var log_index = $('div[data-id="' + resp.last_log + '"]').parents().parents().index();
                                    var first_log_item = $('#test_table_' + list_id).find('tbody tr:eq(' + log_index + ') td.list-table-view:eq(0) .edit_task .task_name_span').text();
                                    $('#tabs-' + list_id).find('.whoisnext-div .nexup-group .nexup-group-two .nexup-sub-group-one .next_task_name').html(first_log_item);
                                    if ($('#tabs-' + list_id).find('.whoisnext-div .nexup-group .nexup-group-two .nexup-sub-group-two').length == 0) {
                                        var second_subgrp = '<div class="nexup-sub-group nexup-sub-group-two">';
                                        for (var m = 1; m < 4; m++) {
                                            if ($('#tabs-' + list_id).find('tbody tr:eq(' + log_index + ') td.list-table-view:eq(' + (m - 1) + ')').length > 0) {
                                                var appnd_data = $('#tabs-' + list_id).find('tbody tr:eq(' + log_index + ') td.list-table-view:eq(' + m + ') .edit_task .task_name_span').text();
                                                second_subgrp += '<span data-toggle="tooltip" title="' + $.trim(appnd_data) + '">' + appnd_data + '</span>';

                                            }
                                        }

                                        second_subgrp += '<p data-toggle="modal" data-target="#items-list" id="items_model_p" data-rowid="' + (log_index + 1) + '" data-placement="top" title="Show all items">';
                                        second_subgrp += '<img src="<?php echo base_url(); ?>assets/img/information-button-icon-23.png">';
                                        second_subgrp += '</p>';
                                        second_subgrp += '</div>';

                                        $('.whoisnext-div .nexup-group .nexup-group-two .button-outer').append(second_subgrp);
                                    }

                                }
                            }
                            var j = 0;
                            var max_col = resp.new_col_data.length;
                            for (j = 0; j <= max_col; j++) {
                                var k = j + 1;
                                $('#test_table_' + list_id).find('tbody tr').each(function () {
                                    var ord = $(this).find('.icon-more-holder').attr('data-order');
                                    if (ord == k) {
                                        $(this).find('.list-table-view:last').after(resp.new_col_data[j]);
                                    }
                                });
                            }
                            if ($('#test_table_' + list_id).find('#task_name:eq(0)').attr('data-colid') == 0) {
                                var add_data_col_id_first = $('#test_table_' + list_id).find('.add-data-title').attr('data-colid');
                                $('#test_table_' + list_id).find('#task_name:eq(0)').attr('data-colid', add_data_col_id_first);
                            }

                            $('.col-modal').modal('hide');
                            if ($('#test_table_' + list_id).find('.heading_items_col').hasClass('hidden_heading')) {
                                $('#test_table_' + list_id).find('.heading_items_col').removeClass('hidden_heading');
                            }
                            if($('#tabs-' + list_id).find('.icon-settings').attr('data-typeid') == 2 || $('#tabs-' + list_id).find('.icon-settings').attr('data-typeid') == 8){
                             $.ajax({
                                url: '<?php echo base_url(); ?>get_nexup_box',
                                type: 'POST',
                                data: {
                                    'list_id': list_id,
                                },
                                success: function (res) {
                                    if(res != ''){
                                        var row_indx = $('#span_task_' + res).parent().parent().parent().index();
                                        if($('#tabs-' + list_id).find('.icon-settings').attr('data-typeid') == 2){
                                            $('#tabs-' + list_id).find('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
											
                                            $('#tabs-' + list_id).find('.nexup-sub-group-one').attr('data-original-title', $.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                            var item_list_nexup_data = '<tr><td>' + $.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                            var grp_two = '';
                                            for(s = 1; s < 4; s++){
                                                if($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').length == 1){
                                                    grp_two += '<span data-toggle="tooltip" title="" data-original-title="' + $.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) +'">' + $.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</span>';
                                                    item_list_nexup_data += '<tr><td>' + $.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                                }
                                            }
                                            grp_two += '<p data-toggle="modal" data-target="#items-list" id="items_model_p" data-rowid="' + (row_indx + 1) +'" data-placement="top" title="Show all items"><img src="<?php echo base_url(); ?>assets/img/information-button-icon-23.png"></p>';
                                            if(grp_two != ''){
                                                $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').html(grp_two);
                                            }
                                            
                                        }else if($('#tabs-' + list_id).find('.icon-settings').attr('data-typeid') == 8){
                                             $('#tabs-' + list_id).find('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                        }
                                        if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                                            $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                                event.preventDefault()
                                            }).tooltip({delay: { "hide": 100 }});
                                        }
                                        $(document).on('click', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                                            $('.tooltip').remove()
                                        });
                                        set_nexup_box_data();
                                    }
                                    var nexup_sun_grp_2_len = $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').length;
                                    if(nexup_sun_grp_2_len == 1){
                                        $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '18px');
                                    }else if(nexup_sun_grp_2_len == 2){
                                        $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '16px');
                                    }else{
                                        $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '13px');
                                    }
                                    
                                }
                            });
                            }
//                            $('#test_table_' + list_id).find('thead tr').sortable('refresh');
                        } else {
                            $('.col_sub_msg').text('Please enter column name!');
                            $('.col_sub_msg').addClass('alert-danger');
                            $('.col_sub_msg').show();
                        }
                        if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                            $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                event.preventDefault()
                            }).tooltip({delay: { "hide": 100 }});
                        }
                        $(document).on('click', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                            $('.tooltip').remove();
                        })

                        $('#tabs-' + list_id).find("#TaskListDiv ul.tasks_lists_display:last-child #task_name").focus();

                        if ($('#tabs-' + list_id).find('#config_lnk').attr('data-typeid') != 11) {
                            if ($(window).width() >= 1350) {
                                if ($('#tabs-' + list_id).find('.task_name').length <= 3) {
                                    if (!$('#tabs-' + list_id).find('#added_div').hasClass('add-data-left')) {
                                        $('#tabs-' + list_id).find('#added_div').addClass('add-data-left');
                                        $('#tabs-' + list_id).find('.my_table').addClass('table_one_page');
                                    }
                                } else {
                                    if ($('#tabs-' + list_id).find('#added_div').hasClass('add-data-left')) {
                                        $('#tabs-' + list_id).find('#added_div').removeClass('add-data-left');
                                        $('#tabs-' + list_id).find('.my_table').removeClass('table_one_page');
                                    }
                                }
                            } else if ($(window).width() >= 1200 && $(window).width() < 1350) {
                                if ($('#tabs-' + list_id).find('.task_name').length <= 2) {
                                    if (!$('#tabs-' + list_id).find('#added_div').hasClass('add-data-left')) {
                                        $('#tabs-' + list_id).find('#added_div').addClass('add-data-left');
                                        $('#tabs-' + list_id).find('.my_table').addClass('table_one_page');
                                    }
                                } else {
                                    if ($('#tabs-' + list_id).find('#added_div').hasClass('add-data-left')) {
                                        $('#tabs-' + list_id).find('#added_div').removeClass('add-data-left');
                                        $('#tabs-' + list_id).find('.my_table').removeClass('table_one_page');
                                    }
                                }
                            }
                        } else {
                            if ($('#tabs-' + list_id).find('#added_div').hasClass('add-data-left')) {
                                $('#tabs-' + list_id).find('#added_div').removeClass('add-data-left');
                                $('#tabs-' + list_id).find('.my_table').removeClass('table_one_page');
                            }
                        }
                        $('#nexup_sub_column').prop('disabled', false);
                        $('#save_sub_col').prop('disabled', false);
                        if (res == 'empty') {
                            $('#nexup_sub_column').focus();
                        }
                    },
                    error: function (textStatus, errorThrown) {
                        $('#nexup_sub_column').prop('disabled', false);
                        $('#save_sub_col').prop('disabled', false);
                    },
                    complete: function (data) {
                        $('#nexup_sub_column').prop('disabled', false);
                        $('#save_sub_col').prop('disabled', false);
                    }
                });
            }
            
            
            $(document).on('mouseover', '.edit_list_task, #share_list, .delete_list_tabbed', function(){
                $('.delete_list_tabbed').css('opacity', '1');
            });
            $(document).on('mouseout', '.edit_list_task, .icon-share, .delete_list_tabbed', function(){
                $('.delete_list_tabbed').css('opacity', '0');
            });

            $(document).on('click', '#save_col', function (e) {
                $('.col_msg').text('');
                $('.col_msg').removeClass('alert-danger');
                $('.col_msg').hide();
                var cells_cnt = $('#test_table tbody tr td.icon-more-holder').length;
                var col_name = $('#nexup_column').val();
                var list_id = $('#save_col').attr('data-listid');
                add_cols(cells_cnt, col_name, list_id);

            });
            
            $(document).on('click', '#save_sub_col', function (e) {
                $('.col_sub_msg').text('');
                $('.col_sub_msg').removeClass('alert-danger');
                $('.col_sub_msg').hide();
                var list_id = $('#save_sub_col').attr('data-listid');
                
                var cells_cnt = $('#test_table_' + list_id).find('tbody tr td.icon-more-holder').length;
                var col_name = $('#nexup_sub_column').val();
                add_sub_cols(cells_cnt, col_name, list_id);

            });

            $(document).on('click', '.add-data-head-r a.add_column_url.icon-add', function () {
                if ($('.plus-category').attr('data-access') == 0) {
                    alert('You are not allowed to add new column!');
                    return false;
                }
                $('.col_msg').text('');
                $('.col_msg').removeClass('alert-danger');
                $('.col_msg').hide();
                $('#col_list').modal('toggle');
            });
            $(document).on('click', 'a.add_sub_column_url.icon-add', function () {
                $('#nexup_sub_column').val('');
                var list_id = $(this).attr('data-listid');
                if ($('#tabs-' + list_id).find('.plus-category').attr('data-access') == 0) {
                    alert('You are not allowed to add new column!');
                    return false;
                }
                $('#save_sub_col').attr('data-listid', list_id);
                $('.col_sub_msg').text('');
                $('.col_sub_msg').removeClass('alert-danger');
                $('.col_sub_msg').hide();
                $('#col_sub_list').modal('toggle');
            });

            $(document).on('shown.bs.modal', '#col_sub_list', function () {
                $('#nexup_sub_column').focus();
            });


            $(document).on('keydown', '#nexup_sub_column', function (evt) {
                var key_code = evt.keyCode;
                if (key_code == 13) {
                    $('#save_sub_col').trigger('click');
                }
            });


            $(document).on('click', '.heading_items_col .add-data-title', function (e) {
                if ($('.plus-category').attr('data-access') == 0) {
                    alert('You are not allowed to edit this column!');
                    return false;
                }

                if ($(this).children('#edit_column_box').length > 0) {
                    return false;
                }

                var col_id = $(this).attr('data-colid');
                var list_id = $(this).attr('data-listid');
                $.ajax({
                    url: '<?php echo base_url(); ?>task/get_column_name',
                    type: 'POST',
                    context: this,
                    data: {
                        'column_id': col_id,
                        'list_id': list_id
                    },
                    success: function (res) {
                        if (res == null || res == 'empty') {
                            alert('Please login to perform action!');
                            $(this).css('pointer-events', 'auto');
                            return false;
                        } else if (res == 'unauthorised') {
                            alert('You are not authorised to edit this!');
                            $(this).css('pointer-events', 'auto');
                            return false;
                        }
                        $('#edit_column_box').remove();
                        $('.column_name_class').show();
                        $(this).children('.column_name_class').hide();
                        var text_box = '<input type="text" id="edit_column_box" class="edit_column_box" value="' + res + '" data-listid="' + list_id + '" data-colid="' + col_id + '">';
                        $(this).children('.column_name_class').after(text_box);
                        $('#edit_column_box').select();
                    }
                });
            });

            $(document).on('keydown', '#edit_column_box', function (evt) {
                var key_code = evt.keyCode;
                if (key_code == 27) {
                    $(this).remove();
                    $('.column_name_class').show();
                } else if (key_code == 13) {
                    var column_id = $(this).attr('data-colid');
                    var list_id = $(this).attr('data-listid');
                    var column_name = $(this).val();
                    $.ajax({
                        url: '<?php echo base_url(); ?>item/update_column_name',
                        type: 'POST',
                        context: this,
                        data: {
                            'column_name': column_name,
                            'column_id': column_id,
                            'list_id': list_id
                        },
                        success: function (res) {
                            if (res == 'success') {
                                var indx = $(this).parent().parent().index();
                                $(this).remove();
                                $('#col_name_' + column_id).html(column_name);
                                $('#col_name_' + column_id).closest('th').attr('title', column_name);
//                                $('.column_name_class').html(column_name);
                                $('.column_name_class').show();
//                                if ($('#test_table').width() > 1200) {
//                                    $('#TaskListDiv .my_table').removeClass('my_scroll_table');
//                                    $('#addTaskDiv').mCustomScrollbar("destroy");
//                                    $("#addTaskDiv").mCustomScrollbar({
//                                        axis: "x",
//                                        scrollButtons: {enable: true},
//                                        theme: "3d",
//                                        scrollbarPosition: "outside",
//                                        advanced: {
//                                            updateOnContentResize: true,
//                                            updateOnBrowserResize: true,
//                                            autoExpandHorizontalScroll: true,
//                                        },
//                                        mouseWheel:{enable: false},
//                                    });
//                                } else {
//                                    $('#addTaskDiv').mCustomScrollbar("destroy");
//                                    $('#TaskListDiv .my_table').addClass('my_scroll_table');
//                                }
                                $('.heading_items_col_add:eq(' + (indx - 1) + ') .add-data-input .task_name').attr('placeholder', 'Add ' + column_name);
                            } else if(res == 'unauthorised'){
                                alert('You are not authorised to edit this!');
                                $(this).css('pointer-events', 'auto');
                                return false;
                            } else {
                                alert('Something went wrong. Column name was not updated. Please try again!');
                            }
                        }
                    });
                }
            });


            $(document).on('click', '.icon-more.move_col', function () {
                return false;
            });


            $(document).on('click', '.remove_col', function () {
                if ($('.plus-category').attr('data-access') == 0) {
                    alert('You are not allowed to remove this column!');
                    return false;
                }
                var col_index = $(this).closest('.heading_items_col').index();
                if (confirm('Are you sure want to delete this column?')) {
                    var list_id = $(this).attr('data-listid');
                    var col_id = $(this).attr('data-colid');
                    $.ajax({
                        url: '<?php echo base_url(); ?>delete_column',
                        type: 'POST',
                        context: this,
                        data: {
                            'list_id': list_id,
                            'column_id': col_id
                        },
                        success: function (res) {
                            if (res == 'success') {
                                if ($('#test_table thead tr.td_arrange_tr th.heading_items_col').length <= 2) {
                                    $('#test_table thead tr.td_arrange_tr th:eq(' + col_index + ')').remove();
                                    if ($('.icon-settings').attr('data-typeid') != 11) {
                                        $('.heading_items_col').addClass('hidden_heading');
                                    }
                                } else if ($('#test_table thead tr.td_arrange_tr th.heading_items_col').length > 2) {
                                    $('#test_table thead tr.td_arrange_tr th:eq(' + col_index + ')').remove();
                                }

                                if ($('#test_table thead tr.td_add_tr th.heading_items_col_add').length > 1) {
                                    $('#test_table thead tr.td_add_tr th:eq(' + col_index + ')').remove();
                                } else {
                                    $('#task_name').attr('data-colid', 0);
                                }
                                $('#test_table tbody tr').each(function () {
                                    $(this).children('td:eq(' + col_index + ')').remove();
                                });

                                $(this).parents('.tasks_lists_display').remove();

                                var current_id = $('.tasks_lists_display.ui-sortable').attr('id');
                                $('#' + current_id).sortable('destroy');
                                var first_id = $('#TaskListDiv ul:nth-child(2)').attr('id');
                                $("#" + first_id).sortable({
                                    handle: '.icon-more',
                                    cancel: '.heading_col',
                                    update: function (event, ui) {
                                        if ($('.rank_th').length > 0) {
                                            var rnk_val = 1;
                                            $('.rank_th').each(function () {
                                                $(this).text(rnk_val);
                                                rnk_val++;
                                            });
                                        }
                                        var tasks_ids = [];
                                        $('.tasks_lists_display li').each(function (e) {
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
                                                    if ($('.icon-more-holder').length > 0) {
                                                        var ord_val = 1;
                                                        $('.icon-more-holder').each(function () {
                                                            $(this).attr('data-order', ord_val);
                                                            ord_val++;
                                                        });
                                                    }
                                                    var total_rows = $('#test_table tbody tr').length;
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

                                $('#test_table tbody tr').each(function () {
                                    var item_id = $(this).children('td.list-table-view:eq(0)').find('.edit_task').attr('data-id');
                                    var item_name = $(this).children('td.list-table-view:eq(0)').find('.edit_task').attr('data-task');
                                    $(this).children('td.icon-more-holder:eq(0)').attr('data-taskname', item_name);

                                    $(this).children('td.icon-more-holder:eq(0)').find('.delete_task').attr('data-id', item_id);
                                    $(this).children('td.icon-more-holder:eq(0)').find('.complete_task').attr('data-id', item_id);
                                    $(this).children('td.icon-more-holder:eq(0)').find('.present_task').attr('data-id', item_id);

                                    $(this).children('td.icon-more-holder:eq(0)').find('.delete_task').attr('data-task', item_name);

                                    $(this).children('td.icon-more-holder:eq(0)').find('.complete_task').attr('id', 'complete_' + item_id);
                                    $(this).children('td.icon-more-holder:eq(0)').find('.present_task').attr('id', 'present_' + item_id);

                                    $(this).children('td.icon-more-holder:eq(0)').find('.complete_lbl').attr('for', 'complete_' + item_id);
                                    $(this).children('td.icon-more-holder:eq(0)').find('.present_lbl').attr('for', 'present_' + item_id);

                                });
                                if ($('.edit_task').length == 0 && $('.heading_items_col_add').length == 1) {
                                    $('.add-data-head-r').hide();
                                }
                                
                                if($('.icon-settings').attr('data-typeid') == 2 || $('.icon-settings').attr('data-typeid') == 8){
                             $.ajax({
                                url: '<?php echo base_url(); ?>get_nexup_box',
                                type: 'POST',
                                data: {
                                    'list_id': list_id,
                                },
                                success: function (res) {
                                    if(res != ''){
                                        var row_indx = $('#span_task_' + res).parent().parent().parent().index();
                                        if($('.icon-settings').attr('data-typeid') == 2){
                                            $('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                            $('.nexup-sub-group-one').attr('data-original-title', $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                            var item_list_nexup_data = '<tr><td>' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                            var grp_two = '';
                                            for(s = 1; s < 4; s++){
                                                if($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').length == 1){
                                                    grp_two += '<span data-toggle="tooltip" title="" data-original-title="' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) +'">' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</span>';
                                                    item_list_nexup_data += '<tr><td>' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                                }
                                            }
                                            grp_two += '<p data-toggle="modal" data-target="#items-list" id="items_model_p" data-rowid="' + (row_indx + 1) +'" data-placement="top" title="Show all items"><img src="<?php echo base_url(); ?>assets/img/information-button-icon-23.png"></p>';
                                            if(grp_two != ''){
                                                $('.button-outer').find('.nexup-sub-group-two').html(grp_two);
                                            }
                                            
                                        }else if($('.icon-settings').attr('data-typeid') == 8){
                                             $('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                        }
                                        if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                                            $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                                event.preventDefault()
                                            }).tooltip({delay: { "hide": 100 }});
                                        }
                                        $(document).on('click', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                                            $('.tooltip').remove()
                                        });
                                        set_nexup_box_data();
                                    }
                                    var nexup_sun_grp_2_len = $('.button-outer').find('.nexup-sub-group-two').find('span').length;
                                    if(nexup_sun_grp_2_len == 1){
                                        $('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '18px');
                                    }else if(nexup_sun_grp_2_len == 2){
                                        $('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '16px');
                                    }else{
                                        $('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '13px');
                                    }
                                    
                                }
                            });
                            }
                            } else if (res == 'not allowed') {
                                alert('Nope...\nThis operation is not allowed.');
                                return false;
                            }

                            if ($('#config_lnk').attr('data-typeid') != 11) {
                                if ($(window).width() >= 1350) {
                                    if ($('.task_name').length <= 3) {
                                        if (!$('#added_div').hasClass('add-data-left')) {
                                            $('#added_div').addClass('add-data-left');
                                            $('.my_table').addClass('table_one_page');
                                        }
                                    } else {
                                        if ($('#added_div').hasClass('add-data-left')) {
                                            $('#added_div').removeClass('add-data-left');
                                            $('.my_table').removeClass('table_one_page');
                                        }
                                    }
                                } else if ($(window).width() >= 1200 && $(window).width() < 1350) {
                                    if ($('.task_name').length <= 2) {
                                        if (!$('#added_div').hasClass('add-data-left')) {
                                            $('#added_div').addClass('add-data-left');
                                            $('.my_table').addClass('table_one_page');
                                        }
                                    } else {
                                        if ($('#added_div').hasClass('add-data-left')) {
                                            $('#added_div').removeClass('add-data-left');
                                            $('.my_table').removeClass('table_one_page');
                                        }
                                    }
                                }
                            } else {
                                if ($('#added_div').hasClass('add-data-left')) {
                                    $('#added_div').removeClass('add-data-left');
                                    $('.my_table').addClass('table_one_page');
                                }
                            }
                        }
                    });
                }
                return false;
            });

            $(document).on('change', '#custom_url', function (e) {
                var alphanumers = /^\s*[a-zA-Z0-9,\s]+\s*$/;
                if (!alphanumers.test($("#custom_url").val())) {
                    var new_val = $('#custom_url').val();
                    new_val = new_val.slice(0, -1)
                    $('#custom_url').val(new_val);
                }
            });
            
            $(document).on('keyup', '#custom_url', function (e) {
                var new_val = $('#custom_url').val();
                var alphanumers = /^\s*[a-zA-Z0-9,\s]+\s*$/;
                if (!alphanumers.test($("#custom_url").val())) {
                    new_val = new_val.slice(0, -1);
                    new_val = new_val.replace(/[^a-z0-9\s]/gi, '');
                }
                $("#custom_url").val(new_val);
            });
            
            $(document).on('keyup', '#custom_sub_url', function (e) {
                var new_val = $('#custom_sub_url').val();
                var alphanumers = /^\s*[a-zA-Z0-9,\s]+\s*$/;
                if (!alphanumers.test($("#custom_sub_url").val())) {
                    new_val = new_val.slice(0, -1);
                    new_val = new_val.replace(/[^a-z0-9\s]/gi, '');
                }
                $("#custom_sub_url").val(new_val);
            });
            
            function update_url_slug(list_id, updated_slug, sublist = 0) {
                $.ajax({
                    url: "<?php echo base_url() . 'item/update_url'; ?>",
                    type: 'POST',
                    context: this,
                    data: {
                        list_id: list_id,
                        new_slug: updated_slug
                    },
                    success: function (resp) {
                        if (resp == 'empty') {
                            $('#share_msg').text('Please enter a valid text');
                            $('#share_msg').show();
                        } else if (resp == 'existing') {
                            $('.custom_slug_err').text('URL in use, please choose another..');
                            $('.custom_slug_err').show();
                        } else if (resp == 'bad string') {
                            $('.custom_slug_err').text('You have entered an invalid string. URL must contain alpha numeric string only!');
                            $('.custom_slug_err').show();
                        } else {
                            $('.custom_slug_err').text('');
                            $('.custom_slug_err').hide();
                            if(sublist == 0){
                                window.location.href = '<?php echo base_url() . 'list/'; ?>' + $.trim(updated_slug);
                            }else{
                                $("#import_contacts_sub_copy, #custom_sub_url").val("");
                                $('#import_contacts_sub_copy, #custom_sub_url').remove();
                                var new_url = '<?php echo base_url(); ?>list/' + updated_slug;
                                $('#import_contacts_sub').attr('href', new_url);
                                $('#import_contacts_sub').html(new_url);
                                $('#import_contacts_sub').show();
//                                $('#customize_sub_btn').show();
                                $('#copy_sub_btn').removeClass('block-copy-btn');
                            }
                        }
                    }
                });
            }

            $(document).on('keydown', '#custom_url', function (e) {
                $('#share_msg').hide();
                var key_code = e.keyCode;
                if (key_code == 27) {
                    e.preventDefault();
                    $("#import_contacts_copy, #custom_url").val("");
                    $('#import_contacts_copy, #custom_url').remove();
                    $('#import_contacts').show();
//                    $('#customize_btn').show();
                    $('#copy_btn').removeClass('block-copy-btn');
                    return false;
                } else if (key_code == 13) {
                    var list_id = $('.edit_list_task').attr('data-id');
                    var updated_slug = $(this).val();
                    if (updated_slug == '') {
                        $('#share_msg').text('Please enter proper URL!');
                        $('#share_msg').show();
                        setTimeout(function () {
                            $('#share_msg').hide();
                        }, 5000);
                        return false;
                    }
                    update_url_slug(list_id, updated_slug, 0);
                }
            });
            $(document).on('keydown', '#custom_sub_url', function (e) {
                $('#share_sublist_msg').hide();
                var key_code = e.keyCode;
                if (key_code == 27) {
                    e.preventDefault();
                    $("#import_contacts_sub_copy, #custom_sub_url").val("");
                    $('#import_contacts_sub_copy, #custom_sub_url').remove();
                    $('#import_contacts_sub').show();
                    $('#customize_sub_btn').show();
                    $('#copy_sub_btn').removeClass('block-copy-btn');
                    return false;
                } else if (key_code == 13) {
                    var list_id = $('#share-contact-sublist').find('#share_btn').attr('data-id');
                    var updated_slug = $(this).val();
                    if (updated_slug == '') {
                        $('#share_sublist_msg').text('Please enter proper URL!');
                        $('#share_sublist_msg').show();
                        setTimeout(function () {
                            $('#share_msg').hide();
                        }, 5000);
                        return false;
                    }
                    update_url_slug(list_id, updated_slug, 1);
                }
            });

            

            $(document).on('blur', '#custom_url', function (e) {
                e.preventDefault();
                var list_id = $('.edit_list_task').attr('data-id');
                var updated_slug = $(this).val();
                if (updated_slug == '') {
                    return false;
                }
                update_url_slug(list_id, updated_slug);
            });
            
//            $('#items-list').on('toggle', function () {
//                $(document).find('#item_list_table_nexup').parent().scrollTop(0);
//            });
            
             $('#items-list').on('hide.bs.modal', function (e) {
                $(document).find('#item_list_table_nexup').parent().scrollTop(0);
              })

            $(document).on('click', '#items_model_p', function () {
                var list_id = $(this).parents('div.ui-tabs-panel').find('.edit_list_task_sub').attr('data-id');
                var row_id = $(this).attr('data-rowid');
                $('.items-modal-body .nexup-group .nexup-group-two .nexup-sub-group-two').html('');
                var item_show = ' <table class="table table-striped table-responsive" id="item_list_table_nexup">';
                $('#test_table_' + list_id).find('tbody tr:nth-child(' + row_id + ') td .add-data-div').each(function () {
                    var val_print = $(this).children('.task_name_span').text();
                    if ($(this).children('.task_name_span').text() == '') {
                        val_print = '';
                    }
                    item_show += '<tr><td>' + val_print + '</td></tr>';

                });
                item_show += '</table>';
                $('.items-modal-body .nexup-group .nexup-group-two .nexup-sub-group-two').append(item_show);
                return false;
            });

            $(document).on('click', '#TaskListHead', function () {

                $('div#TaskListDiv').css('width', $('#test_table').width());

//                if ($(this).attr('aria-expanded') == 'true') {
//                    $('#my_table').css('width', $('#test_table').width());
//                    if ($('#test_table').width() > 1200) {
//                        $('#TaskListDiv .my_table').removeClass('my_scroll_table');
//                        $('#addTaskDiv').mCustomScrollbar("destroy");
//                        $("#addTaskDiv").mCustomScrollbar({
//                            axis: "x",
//                            scrollButtons: {enable: true},
//                            theme: "3d",
//                            scrollbarPosition: "outside",
//                            advanced: {
//                                updateOnContentResize: true,
//                                updateOnBrowserResize: true,
//                                autoExpandHorizontalScroll: true,
//                            },
//                            mouseWheel:{enable: false},
//                        });
//                    } else {
//                        $('#my_table').mCustomScrollbar("destroy");
//                        $('#TaskListDiv .my_table').addClass('my_scroll_table');
//                    }
//                } else {
//                    $('#addTaskDiv').mCustomScrollbar("destroy");
//                    $('#my_table').css('width', '100%');
//                }
            });

            $(document).on('keydown', '#values_items', function (e) {
                if (e.keyCode === 9) {
                    var start = this.selectionStart;
                    var end = this.selectionEnd;
                    var $this = $(this);
                    var value = $this.val();
                    $this.val(value.substring(0, start)
                            + "\t"
                            + value.substring(end));
                    this.selectionStart = this.selectionEnd = start + 1;
                    e.preventDefault();
                }
            });
            $(document).on('keydown', '#values_sub_items', function (e) {
                if (e.keyCode === 9) {
                    
                    e.preventDefault();
                    var range = document.createRange();
                    var start = this.selectionStart;
                    var end = this.selectionEnd;
                    
                    var text = $(this).val();
                    var selText = text.substring(start, end);
                    
                    $(this).val(
                        text.substring(0, start) +
                        "\t" + selText.replace(/\n/g, "\n\t") +
                        text.substring(end)
                    );
                    this.selectionStart = this.selectionEnd = start + 1;
                    $('#values_sub_items').focus();
                    
                }
                if (e.keyCode === 13) {
                    var start = window.getSelection().anchorOffset;
                    if(start != $(this).html().length){
                        document.execCommand('insertHTML', false, '\n');
                        if(start == ($(this).html().length - 2)){
                            $('#values_sub_items').scrollTop($('#values_sub_items')[0].scrollHeight);
                        }
                    }else{
                        document.execCommand('insertHTML', false, '\n ');
                        placeCaretAtEnd($(this));
                        e.preventDefault();
                        $('#values_sub_items').scrollTop($('#values_sub_items')[0].scrollHeight);
                    }
                    return false;
                }
            });
            
            function placeCaretAtEnd(el) {
                $('#values_sub_items')[0].focus();
                if (typeof window.getSelection != "undefined"
                        && typeof document.createRange != "undefined") {
                    var range = document.createRange();
                    range.selectNodeContents($('#values_sub_items')[0]);
                    range.collapse(false);
                    var sel = window.getSelection();
                    sel.removeAllRanges();
                    sel.addRange(range);
                } else if (typeof document.body.createTextRange != "undefined") {
                    var textRange = document.body.createTextRange();
                    textRange.moveToElementText($('#values_sub_items')[0]);
                    textRange.collapse(false);
                    textRange.select();
                }
            }

            $(document).on('click', '#add_bulk_sub_data', function () {
                $('#type_option_cb_email').prop('checked', false);
                $('#type_option_normal').prop('checked', true);
                $('#spearator_sub_options').show();
                $('#include_sub_header').prop('checked', true);
                var loading_img = '<img id="bulk_preloader" src="/assets/img/loader.gif" style="height:50px;width:50px;position: absolute;z-index:  99999;top: 50%;left: 45%;">';
                $('#values_sub_items').before(loading_img);
                $('#values_sub_items').prop('disabled', true);
            $('#save_sub_bulk').removeAttr('disabled','disabled');
                $('#include_header').prop( "checked", true );
                $('#bulk_data_sub_modal #data_msg').text('');
                var list_id = $(this).attr('data-id');
                $('#save_sub_bulk').attr('data-listid', list_id);
                $.ajax({
                    url: "<?php echo base_url() . 'item/get_bulk_data'; ?>",
                    type: 'POST',
                    context: this,
                    data: {
                        list_id: list_id,
                    },
                    success: function (resp) {
                        $('#values_sub_items').prop('disabled', false);
//                        $('#values_sub_items').text(resp);
                        $('#values_sub_items').text(resp);
                        $('#bulk_preloader').remove();
                        if (resp != '') {
                            $('#spearator_sub_options').val('tab');
                        }
                        $("#values_items").animate({
                            scrollTop: $("#values_sub_items").prop("scrollHeight")
                        }, 2000);
                        var el = document.getElementById("values_sub_items");
                        var endrange = resp.length;
                        var range = document.createRange();
                        var sel = window.getSelection();
                        range.setStart(el.childNodes[0], endrange);
                        sel.removeAllRanges();
                        sel.addRange(range);
                    }
                });
                $('#bulk_data_sub_modal #data_msg').val('');
                $('#bulk_data_sub_modal').modal('show');
                $('#values_sub_items').focus();
            });
            

            $(document).on('shown.bs.modal', '#bulk_data_sub_modal', function () {
                $('#values_sub_items').focus();
            });
            
            function save_bulk_data(list_id, list_data,separator_opt, include_header, btn_click = 'click_save'){
                $.ajax({
                    url: "<?php echo base_url() . 'item/save_bulk_data'; ?>",
                    type: 'POST',
                    context: this,
                    async: false,
                    data: {
                        list_id: list_id,
                        list_data: JSON.stringify(list_data),
                        separator: separator_opt,
                        include_header: include_header
                    },
                    success: function (resp) {
                        if (resp == 'mismatch') {
                            $('#bulk_data_sub_modal #data_msg').text('Please check values you have entered!');
                            $('.bulk_loader').addClass('hiden_img');
                            $('.bulk_loader').css('display', 'none');
                        } else if (resp == 'error') {
                            $('#bulk_data_sub_modal #data_msg').text('Something went wrong. Data was not added. Please try again!');
                            $('.bulk_loader').css('display', 'none');
                            $('.bulk_loader').hide();
                        } else {
                            var total_rows = $('#test_table_' + list_id).find('tbody').find('tr').length;
                            $('.bulk_loader').css('display', 'none');
                            $('.bulk_loader').hide();
                            if(btn_click == 'click_save'){
                                $('#bulk_data_sub_modal').modal('toggle');
                            }
                            $('#log-list-sub').find('.logmodal-body-sub').find('.log_div').find('.log_sub_table_popup').find('tbody').html('');
                            $.ajax({
                            url:'/return_list_tab',
                            type: 'POST',
                            context: this,
                            data:{list_id: list_id},
                            success: function (res) {
                                if(res != 'fail' && res != '' && res != 'unauthorised'){
                                    $(document).find('#tabs-' + list_id).html(res);
                                    if($('#tabs-' + list_id).find('.edit_list_task_sub').attr('data-id') > 0){
                                        if($('#tabs-' + list_id).find('#listsub_Lock_lnk').length == 1){
                                            if($('#tabs-' + list_id).find('#listsub_Lock_lnk').hasClass('lock_hide')){
                                                $('#tabs-' + list_id).find('#config_icons').append($('#tabs-' + list_id).find('#listsub_Lock_lnk.lock_hide'));
                                            }
                                        }
                                        if($('#tabs-' + list_id).find('#listSub_Unlock_lnk').length == 1){
                                            if($('#tabs-' + list_id).find('#listSub_Unlock_lnk').hasClass('lock_hide')){
                                                $('#tabs-' + list_id).find('#config_icons').append($('#tabs-' + list_id).find('#listSub_Unlock_lnk.lock_hide'));
                                            }
                                        }
                                        if($('#tabs-' + list_id).find('.reset_sub_list').length == 1){
                                            if($('#tabs-' + list_id).find('.reset_sub_list').hasClass('login_prompt')){
                                                $('#tabs-' + list_id).find('#config_icons').append($('#tabs-' + list_id).find('.reset_sub_list.login_prompt'));
                                            }
                                        }
                                        if($('#tabs-' + list_id).find('#delete_sub_list_builder').length == 1){
                                            if($('#tabs-' + list_id).find('#delete_sub_list_builder').hasClass('disabled_btn')){
                                                $('#tabs-' + list_id).find('#config_icons').append($('#tabs-' + list_id).find('#delete_sub_list_builder.disabled_btn'));
                                            }
                                        }
                                        
                                        if($('#tabs-' + list_id).find('.sub_listConfig_lnk').attr('data-allowappendlocked') == 1){
                                            if($('#tabs-' + list_id).find('.ui-icon-triangle-1-e').length == 1){
                                                $('.ui-icon-triangle-1-e').trigger('click');
                                            }
                                        }
                                    }
                                }
                            }
                        });
                            
                            if($('#tabs-' + list_id).find('.count_box').length == 1){
                                $('#tabs-' + list_id).find('#yes_cnt').text('0');
                                $('#tabs-' + list_id).find('#maybe_cnt').text('0');
                                $('#tabs-' + list_id).find('#no_cnt').text('0');
                                
                                $('#tabs-' + list_id).find('#blank_cnt').text(total_rows);
                            }
                            
//                            $(this).removeAttr('disabled','disabled');
                        }
                    },
                    error: function (textStatus, errorThrown) {
                    },
                    complete: function (data) {
                    }
                });
            }
            
            $(document).on('click', '#export_sub_bulk_btn', function(){
                var list_id = $('#save_sub_bulk').attr('data-listid');
                var change = 0;
                var loading_img = '<img id="bulk_preloader" src="/assets/img/loader.gif" style="height:50px;width:50px;position: absolute;z-index:  99999;top: 50%;left: 45%;">'
                $('#values_sub_items').before(loading_img);
                $('#values_sub_items').prop('disabled', true);
                $.ajax({
                    url: "<?php echo base_url() . 'item/get_bulk_data'; ?>",
                    type: 'POST',
                    context: this,
                    data: {
                        list_id: list_id,
                    },
                    success: function (resp) {
                        if($('#values_sub_items').html() != resp){
                            change = 1;
                            var cnf = confirm('Do you want to save data before export?');
                            if(cnf){
                                $(this).parent().parent().find('.separator_div').find('.bulk_loader').removeClass('hiden_img');
                                $(this).parent().parent().find('.separator_div').find('.bulk_loader').removeAttr('style');
                                if ($('#tabs-' + list_id).find('.plus-category').attr('data-access') == 0) {
                                    alert('You are not allowed to edit this list!');
                                    return false;
                                }
                                var list_data = $('#bulk_data_sub_modal').find('#values_sub_items').html();
                                var separator_opt = $('#bulk_data_sub_modal').find('#spearator_sub_options').html();
                                var include_header = 0;
                                if ($('#bulk_data_sub_modal').find('#include_sub_header').is(':checked')) {
                                    include_header = 1;
                                }
                                save_bulk_data(list_id, list_data,separator_opt, include_header, '');
                            }
                        }
                        var export_data = btoa($('#values_sub_items').html());
                        var sep = $('#spearator_sub_options').val();
                        window.open('<?php echo base_url() . 'task/export_bulk?export_data=' ?>' + export_data + '&sep=' + sep);
                        $('#bulk_preloader').remove();
                        $('#values_sub_items').prop('disabled', false);
                    }
                });
            
                
            });

            
            $(document).on('click', '#save_sub_bulk', function (e) {
                $(this).parent().parent().find('.separator_div').find('.bulk_loader').removeClass('hiden_img');
                $(this).parent().parent().find('.separator_div').find('.bulk_loader').removeAttr('style');
                $(this).attr('disabled','disabled');
                var list_id = $('#save_sub_bulk').attr('data-listid');
                if ($('#tabs-' + list_id).find('.plus-category').attr('data-access') == 0) {
                    alert('You are not allowed to edit this list!');
                    return false;
                }
                
//                var list_data = $('#bulk_data_sub_modal').find('#values_sub_items').text();
                var list_data = $('#bulk_data_sub_modal').find('#values_sub_items').val();
                var separator_opt = $('#bulk_data_sub_modal').find('#spearator_sub_options').val();
                var list_type_opt = $('input[name=type_option]:checked').val();
                if($('#type_option_cb_email').is(':checked')){
                    list_data = parseEmailRecipients(list_data, separator_opt);
                }else{
                    list_data = list_data.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                }
                
                var include_header = 0;
                if ($('#bulk_data_sub_modal').find('#include_sub_header').is(':checked')) {
                    include_header = 1;
                }
                
                save_bulk_data(list_id, list_data,separator_opt, include_header, 'click_save');
                
//                $.ajax({
//                    url: "<?php echo base_url() . 'item/save_bulk_data'; ?>",
//                    type: 'POST',
//                    context: this,
//                    async: false,
//                    data: {
//                        list_id: list_id,
//                        list_data: JSON.stringify(list_data),
//                        separator: separator_opt,
//                        include_header: include_header
//                    },
//                    success: function (resp) {
//                        if (resp == 'mismatch') {
//                            $('#bulk_data_modal #data_msg').text('Please check values you have entered!');
//                            $('.bulk_loader').addClass('hiden_img');
//                            $('.bulk_loader').css('display', 'none');
//                        } else if (resp == 'error') {
//                            $('#bulk_data_modal #data_msg').text('Something went wrong. Data was not added. Please try again!');
//                            $('.bulk_loader').css('display', 'none');
//                            $('.bulk_loader').hide();
//                        } else {
//                            var total_rows = $('#test_table_' + list_id).find('tbody').find('tr').length;
//                            $('.bulk_loader').css('display', 'none');
//                            $('.bulk_loader').hide();
//                            $('#bulk_data_sub_modal').modal('toggle');
//                            $('#log-list-sub').find('.logmodal-body-sub').find('.log_div').find('.log_sub_table_popup').find('tbody').html('');
//                            $.ajax({
//                            url:'/return_list_tab',
//                            type: 'POST',
//                            context: this,
//                            data:{list_id: list_id},
//                            success: function (res) {
//                                if(res != 'fail' && res != '' && res != 'unauthorised'){
//                                    $(document).find('#tabs-' + list_id).html(res);
//                                    if($('#tabs-' + list_id).find('.edit_list_task_sub').attr('data-id') > 0){
//                                        if($('#tabs-' + list_id).find('#listsub_Lock_lnk').length == 1){
//                                            if($('#tabs-' + list_id).find('#listsub_Lock_lnk').hasClass('lock_hide')){
//                                                $('#tabs-' + list_id).find('#config_icons').append($('#tabs-' + list_id).find('#listsub_Lock_lnk.lock_hide'));
//                                            }
//                                        }
//                                        if($('#tabs-' + list_id).find('#listSub_Unlock_lnk').length == 1){
//                                            if($('#tabs-' + list_id).find('#listSub_Unlock_lnk').hasClass('lock_hide')){
//                                                $('#tabs-' + list_id).find('#config_icons').append($('#tabs-' + list_id).find('#listSub_Unlock_lnk.lock_hide'));
//                                            }
//                                        }
//                                        if($('#tabs-' + list_id).find('.reset_sub_list').length == 1){
//                                            if($('#tabs-' + list_id).find('.reset_sub_list').hasClass('login_prompt')){
//                                                $('#tabs-' + list_id).find('#config_icons').append($('#tabs-' + list_id).find('.reset_sub_list.login_prompt'));
//                                            }
//                                        }
//                                        if($('#tabs-' + list_id).find('#delete_sub_list_builder').length == 1){
//                                            if($('#tabs-' + list_id).find('#delete_sub_list_builder').hasClass('disabled_btn')){
//                                                $('#tabs-' + list_id).find('#config_icons').append($('#tabs-' + list_id).find('#delete_sub_list_builder.disabled_btn'));
//                                            }
//                                        }
//                                        
//                                        if($('#tabs-' + list_id).find('.sub_listConfig_lnk').attr('data-allowappendlocked') == 1){
//                                            if($('#tabs-' + list_id).find('.ui-icon-triangle-1-e').length == 1){
//                                                $('.ui-icon-triangle-1-e').trigger('click');
//                                            }
//                                        }
//                                    }
//                                }
//                            }
//                        });
//                            
//                            if($('#tabs-' + list_id).find('.count_box').length == 1){
//                                $('#tabs-' + list_id).find('#yes_cnt').text('0');
//                                $('#tabs-' + list_id).find('#maybe_cnt').text('0');
//                                $('#tabs-' + list_id).find('#no_cnt').text('0');
//                                
//                                $('#tabs-' + list_id).find('#blank_cnt').text(total_rows);
//                            }
//                            
////                            $(this).removeAttr('disabled','disabled');
//                        }
//                    },
//                    error: function (textStatus, errorThrown) {
//                    },
//                    complete: function (data) {
//                    }
//                });
            });
            
            $(window).on('focus', function (e) {
                var list_id = $('.edit_list_task').attr('data-id');
                if ($('#hidden_share_click').val() == 1) {
                    $.ajax({
                        url: '<?php echo base_url() . 'getShared' ?>',
                        type: 'POST',
                        data: {list_id: list_id},
                        success: function (res) {
                            $('#share-contact .input-outer.object_outer.input-sub-outer-cls').html(res);
                        }
                    });
                }
                e.preventDefault();
            });

            $(document).on('click', '.link_clickable', function (e) {
                var open_url = $(this).attr('href');
                window.open(open_url, '_blank');
                e.preventDefault();
                return false;
            });

            $(document).on('click', '#update_info', function () {
                var cnf = confirm('You will be forwarded to Inflo to edit your profile.  Although your changes will be immediate, but not reflected in Nexup until next login.');
                if (cnf) {
                    var open_link = $('#update_info').attr('href');
                    window.open(open_link, '_blank');
                    return false;
                } else {
                    return false;
                }
            });


            $(document).on('click', '.edit_comment', function () {
                if ($(this).closest('td.list-table-view-attend').find('.edit_comment_text').length == 0) {
                    var span_width = $(this).closest('div.edit_comment').width();
                    if (span_width < '73') {
                        span_width = '73';
                    }
                    $('#edit_comment_text').remove();
                    $(this).find('.comment_name_span').show();

                    var comment_id = $(this).attr('data-id');
                    var list_id = $(this).attr('data-listid');
                    $(this).css('pointer-events', 'none');
                    $.ajax({
                        url: "<?php echo base_url() . 'task/get_comment_data'; ?>",
                        type: 'POST',
                        context: this,
                        data: {
                            'comment_id': comment_id,
                            'list_id': list_id
                        },
                        success: function (res) {
                            if (res == 'empty') {
                                alert('Please select a valid comment!');
                            } else if (res == 'unauthorized') {
                                alert('You are not allowed to perform this operation!');
                            } else if (res == 'not found') {
                                alert('Please select a valid comment!');
                            } else {
                                var resp = JSON.parse(res);
                                var edit_comment_box = '<input type="text" class="edit_comment_text" id="edit_comment_text" value="' + resp['comment'] + '"  data-listid="' + list_id + '" data-commentid="' + comment_id + '" style="width:' + span_width + 'px;">';
                                $('#span_comment_' + comment_id).before(edit_comment_box);
                                $('#edit_comment_text').focus();
                                $('#span_comment_' + comment_id).hide();
                            }
                            $(this).css('pointer-events', 'auto');
                        }
                    });
                }


            });


            $(document).on('focus', '#edit_comment_text', function (e) {
                $(this).select();
                e.preventDefault();
                return false;
            });

            $(document).on('keypress', '.edit_comment_text', function (evt) {
                var key_code = evt.keyCode;
                if (key_code == 13) {
                    $(this).trigger('blur');
                }
                if (key_code == 27) {
                    $(this).trigger('blur');
                }
            });

            $(document).on('blur', '.edit_comment_text', function () {
                var c_width = $('#test_table').width();
                var comment = $(this).val();
                var list_id = $(this).attr('data-listid');
                var cmnt_id = $(this).attr('data-commentid');
                $.ajax({
                    url: "<?php echo base_url() . 'task/update_comment_data'; ?>",
                    type: 'POST',
                    context: this,
                    data: {
                        'comment_id': cmnt_id,
                        'list_id': list_id,
                        'comment': comment.replace(/</g, "&lt;").replace(/>/g, "&gt;")
                    },
                    success: function (res) {
                        if (res == 'empty') {
                            alert('Please enter comment!');
                        } else if (res == 'not found') {
                            alert('Please select a valid comment!');
                        } else {
                            if (res == 'fail') {
                                alert('Something went wrong. Please try again!')
                            } else if (res == 'not allowed') {
                                alert('You are not allowed to place comment on this list!')
                            } else {
                                $('.edit_comment_text').remove();
                                $('#span_comment_' + cmnt_id).text(res);
                                $('.comment_name_span').show();
                            }
                        }
                        $(this).css('pointer-events', 'auto');
//                        if ($('#test_table').width() > c_width || ($('#test_table').width() < c_width && $('#test_table').width() > 1303)) {
//                            $('#TaskListDiv .my_table').removeClass('my_scroll_table');
//                            $('#addTaskDiv').mCustomScrollbar("destroy");
//                            $("#addTaskDiv").mCustomScrollbar({
//                                axis: "x",
//                                scrollButtons: {enable: true},
//                                theme: "3d",
//                                scrollbarPosition: "outside",
//                                advanced: {
//                                    updateOnContentResize: true,
//                                    updateOnBrowserResize: true,
//                                    autoExpandHorizontalScroll: true,
//                                },
//                                mouseWheel:{enable: false},
//                            });
//                        }
//                        if ($('#test_table').width() <= 1303) {
//                            $('#my_table').mCustomScrollbar("destroy");
//                            $('#TaskListDiv .my_table').addClass('my_scroll_table');
//                        }
                    }
                });
            });

            $(document).on('click', '#added_div h3#TaskSubListHead span.ui-icon-triangle-1-e', function () {
                $(this).parent().parent().parent().find('.add-data-head-r').addClass('hide_add');
            });
            $(document).on('click', '#added_div h3#TaskSubListHead span.ui-icon-triangle-1-s', function () {
                $(this).parent().parent().parent().find('.add-data-head-r').removeClass('hide_add');
                $(this).parent().parent().css('display', 'inline-block');
                
            });

            $('.added_div').find('.add-data-head-r').click(function (e) {
                e.preventDefault();
            });

            $(document).on('click', '.help-btn-div img', function (e) {
                var open_url = $(this).attr('data-href');
                window.open(open_url, '_blank');
            });

            $(document).on('change', '#spearator_sub_options', function () {
                if ($(this).val() == 'comma') {
                    var data = $('#values_sub_items').html();
                    var data_arr = data.split("\n");
                    var data_split = '';
                    for (var i = 0; i < data_arr.length; i++) {
                        data_arr[i] = '"' + data_arr[i].replace(/\t/g, '|,|').split('|,|').join('","') + '"';
                    }
                    var new_str = data_arr.join('\n');
                    var data = $('#values_sub_items').html(new_str);
                } else if ($(this).val() == 'tab') {
                    var data = $('#values_sub_items').html();
                    var data_arr = data.split("\n");
                    var data_split = '';
                    for (var i = 0; i < data_arr.length; i++) {
//                        data_arr[i] = data_arr[i].replace(',', '\t').split(',').join(',');
//                        data_arr[i] = data_arr[i].replace('"', '').replace(',', '\t');
                        data_arr[i] = data_arr[i].replace(/\"/g, '').replace(/\,/g, '\t');
                    }
                    var new_str = data_arr.join('\n');
                    var data = $('#values_sub_items').html(new_str);
                }
            });

            $(document).on('click', '.add-data-input .span_enter', function () {
                if ($(this).is($('.heading_items_col_add:last .span_enter'))) {
                    $('.task_name').prop('disabled', true);
                    var list_id = $(this).parent().find('.task_name').attr('data-listid');
                    var col_id = $(this).parent().find('.task_name').attr('data-colid');
                    var list_name = '';
                    if (list_id == 0) {
                        if ($('#edit_list_name').length > 0) {
                            list_name = $('#edit_list_name').val();
                        } else {
                            list_name = $('.add-data-head h2').val();
                        }
                    }
                    $('#add_task_li').removeClass('list-error');
                    $('.add_task_cls').text('');
                    $('.add_task_cls').hide();

                    var task_data = [];
                    var column = '';
                    var list = '';
                    var val_cnt = 0;
                    $('.task_name').each(function () {
                        column = $(this).attr('data-colid');
                        list = $(this).attr('data-listid');
                        var task_name = $(this).val();
                        var value_save = ' ';
                        if ($(this).val().trim() != '') {
                            value_save = $(this).val().replace(/</g, "&lt;").replace(/>/g, "&gt;");
                        }
                        task_data.push({column: column, val: value_save});
                        if ($(this).val() != '') {
                            val_cnt++;
                        }
                    });
                    if (val_cnt <= 0) {
                        alert('Please enter atleast 1 item.');
                        $('.task_name').prop('disabled', false);
                        $('.task_name:eq(0)').focus();
                        return false;
                    }

                    add_item_new(list_id, list_name, task_data, col_id);
                } else {
                    var parent_index = $(this).parent().parent().index();
                    $('#test_table thead tr th:nth-child(' + (parent_index + 2) + ') .task_name').focus();
                }
            });

            $(document).on('focus', '.task_sub_name', function () {
                $(this).parent().find('.span_enter').css('opacity', '1');
            });
            
            $(document).on('input', '.task_sub_name', function () {
                if($(this).parent().find('.remove_text').length == 0){
                    var remove_btn = '<div class="remove_text">&times;</div>';
                    $(this).after(remove_btn);
                }
            });
            
            $(document).on('click', '.remove_text', function(){
                $(this).parent().find('.task_sub_name').val('');
                $(this).parent().find('.task_sub_name').focus();
                $(this).remove();
            });

            $(document).on('blur', '.task_sub_name', function () {
                $(this).parent().find('.span_enter').css('opacity', '0');
                $(this).parent().find('.remove_text').remove();
            });
            


            /*
             * Copy list
             * @author SG
             */
            $(document).on('click', '#copy_list_btn', function (e) {
                if($(this).hasClass('copy-list-btn-items-page')){
                    $(this).css('pointer-events', 'none');
                    var cnf = confirm('Are you sure want to copy this list?');
                    if(cnf != 1){
                        $(this).css('pointer-events', 'all');
                        return false;
                    }
                    else{
                        $(this).css('pointer-events', 'none');
                    }
                }
                var list_id = $(this).attr('data-id');
                if (list_id > 0) {
                    $.ajax({
                        url: '<?php echo base_url() . 'copy_list' ?>',
                        type: 'POST',
                        context: this,
                        data: {list_id: list_id},
                        success: function (res) {
                            if(res == 'unauthorised'){
                                alert('You are not allowed to perform this action!');
                                return false;
                            }
                            if (res != 'fail' && res != 'unauthorised') {
                                var resp = JSON.parse(res);
                                if ($(this).hasClass('copy-list-btn-items-page')) {
                                    window.location.href = resp['url'];
                                } else {
                                    if ($('li.own-li-list').length > 0) {
                                        $('li.own-li-list:last').after(resp['list']);
                                    } else if ($('li.shared-list-li').length > 0) {
                                        $('li.shared-list-li:first').before(resp['list']);
                                    } else if ($('li.visited-list-li').length > 0) {
                                        $('li.visited-list-li:first').before(resp['list']);
                                    }
                                }
                                if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                                    $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                        event.preventDefault();
                                    }).tooltip({delay: { "hide": 100 }});
                                }
                                $(document).on('click', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                                    $('.tooltip').remove();
                                });
                                alert('List copied successfully.');
                            }
                        }
                    });
                }
            });
            $(document).on('click', '#copy_sub_list_btn', function (e) {
                    var cnf = confirm('Are you sure want to copy this list?');
                    if(cnf != 1){
                        $(this).css('pointer-events', 'all');
                        return false;
                    }
                    else{
                        $(this).css('pointer-events', 'none');
                    }
                var list_id = $(this).attr('data-id');
                if (list_id > 0) {
                    $.ajax({
                        url: '<?php echo base_url() . 'copy_list' ?>',
                        type: 'POST',
                        context: this,
                        data: {list_id: list_id},
                        success: function (res) {
                            if(res == 'unauthorised'){
                                alert('You are not allowed to perform this action!');
                                return false;
                            }
                            if (res != 'fail' && res != 'unauthorised') {
                                var resp = JSON.parse(res);
                                if ($(this).hasClass('copy-list-btn-items-page')) {
                                    window.location.href = resp['url'];
                                } else {
                                    if ($('li.own-li-list').length > 0) {
                                        $('li.own-li-list:last').after(resp['list']);
                                    } else if ($('li.shared-list-li').length > 0) {
                                        $('li.shared-list-li:first').before(resp['list']);
                                    } else if ($('li.visited-list-li').length > 0) {
                                        $('li.visited-list-li:first').before(resp['list']);
                                    }
                                }
                                if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                                    $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                        event.preventDefault();
                                    }).tooltip({delay: { "hide": 100 }});
                                }
                                $(document).on('click', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                                    $('.tooltip').remove();
                                });
                                alert('List copied successfully.');
                            }
                        }
                    });
                }
            });


            $(document).on('click', '#menu_directory', function (e) {
                if ($(this).parent().find('#menu_dd').hasClass('show_drop_down')) {
                    $(this).parent().find('#menu_dd').removeClass('show_drop_down');
                } else {
                    $(this).parent().find('#menu_dd').addClass('show_drop_down');
                }
            });
            

            $(document).on('click', '#menu_dd li', function () {
                $(this).parent().removeClass('show_drop_down')
            });

            $(document).on('mouseleave', 'ul.list-body-ul li.list-body-li .list-body-box', function () {
                $('.ul_list_option_submenu').removeClass('show_drop_down');
            });
            
            function copySummaryToClipboard(elem) {
                var list_id = $('.edit_list_task').attr('data-id');
                if($(elem).parent().parent().parent().parent().parent().attr('class').indexOf("content_sublist") >= 0){
                    list_id = $(elem).parent().parent().parent().parent().parent().find('.edit_list_task_sub').attr('data-id');
                }
                $.ajax({
                    url: '<?php echo base_url() . 'copy_list_summary' ?>',
                    async : false,
                    type: 'POST',
                    data: {list_id: list_id},
                    success: function (res) {
                        if(res != ''){
                            if(res != ''){
                                if($(elem).parent().parent().parent().parent().parent().attr('class').indexOf("content_sublist") > 0){
                                    $(elem).parent().find('#hdn_summary_sub').html(res);
                                }else{
                                    $('#hdn_summary').html(res);
                                }
                            }
                        }
                    }
                });
                if($(elem).parent().parent().parent().parent().parent().attr('class').indexOf("content_sublist") > 0){
                    $(elem).parent().find('#hdn_summary_sub').focus();
                    var res_len = ($(elem).parent().find('#hdn_summary_sub').val().length + 10);
                    $(elem).parent().find('#hdn_summary_sub').get(0).setSelectionRange(0,res_len);
                }else{
                    $('#hdn_summary').focus();
                    $('#hdn_summary').get(0).setSelectionRange(0,9999);
                }
                if(document.execCommand("copy")){
                    $('#copy_msg_summary').text('Summary copied to clipboard');
                }else{
                    $('#copy_msg_summary').text('Summary not copied to clipboard');
                }
                $('#hdn_summary').trigger('blur');
//                $('#hdn_summary_containable').trigger('blur');
                    $('#copy_msg_summary').fadeIn('slow');

                window.setTimeout(
                        function () {
                            $('#copy_msg_summary').fadeOut('slow');
                        }
                , 1000);
                
            }
            
            
            function copySummaryDetailsToClipboard(elem) {
                if(!elem){
                    elem='null';
                }
                var list_id = $('.edit_list_task').attr('data-id');
                if($(elem).parent().parent().parent().parent().parent().attr('class').indexOf("content_sublist") >= 0){
                    list_id = $(elem).parent().parent().parent().parent().parent().find('.edit_list_task_sub').attr('data-id');
                }
                var cmnt_add = ''
                if($(elem).attr('class') == 'copy_summary_details_comments_li'){
                    cmnt_add = 'comments';
                }
                
                $.ajax({
                    url: '<?php echo base_url() . 'copy_list_summary_details' ?>',
                    async : false,
                    type: 'POST',
                    data: {list_id: list_id, cmnt_add: cmnt_add},
                    success: function (res) {
                        if(res != ''){
                            if($(elem).parent().parent().parent().parent().parent().attr('class').indexOf("content_sublist") > 0){
                                $(elem).parent().find('#hdn_summary_sub').html(res);
                            }else{
                                $('#hdn_summary').html(res);
                            }
                        }
                    }
                });
                
                if($(elem).parent().parent().parent().parent().parent().attr('class').indexOf("content_sublist") > 0){
                    $(elem).parent().find('#hdn_summary_sub').focus();
                    var res_len = ($(elem).parent().find('#hdn_summary_sub').val().length + 10);
                    $(elem).parent().find('#hdn_summary_sub').get(0).setSelectionRange(0,res_len);
                }else{
                    $('#hdn_summary').focus();
                    var res_len = ($('#hdn_summary').val().length + 10);
                    $('#hdn_summary').get(0).setSelectionRange(0,res_len);
                }
//                $('#hdn_summary').select();
                if(document.execCommand("copy")){
                    $('#hdn_summary').trigger('blur');
                    $('#copy_msg_summary').text('Summary copied to clipboard');
                }else{
                    $('#copy_msg_summary').text('Summary not copied to clipboard');
                }
                    $('#copy_msg_summary').fadeIn('slow');

                window.setTimeout(
                        function () {
                            $('#copy_msg_summary').fadeOut('slow');
                        }
                , 1000);
                
            }
            
//            $(document).on('click', '.copy_summary_li', function (){
////                $('#summary_dd').css('display', 'block');
////                $('#summary_dd').show();
//                var list_id = $('.edit_list_task').attr('data-id');
//                $.ajax({
//                    url: '<?php echo base_url() . 'copy_list_summary' ?>',
//                    type: 'POST',
//                    data: {list_id: list_id},
//                    success: function (res) {
//                        if(res != ''){
//                            var resp = JSON.parse(res);
//                            var data_res = '';
//                            if($('#config_lnk').attr('data-typeid') == 11){
//                                data_res += 'yes: ' + resp['yes_total'] + '\n';
//                                data_res += 'maybe: ' + resp['maybe_total'] + '\n';
//                                data_res += 'no: ' + resp['no_total'] + '\n';
//                                data_res += 'unresponded: ' + resp['unresponded_total'];
//                            }
//                            copySummaryToClipboard(data_res);
//                        }
//                    }
//                });
//            });

//            $(document).on('mouseover', '.task_name_span', function (){
//                if($('#config_lnk').attr('data-showprev') == 1){
//                    $(this).parent().parent().find('.preview_image').slideDown( "slow", function() {});
//               }
//             });
//            $(document).on('mouseout', '.task_name_span', function (){
//                $(this).parent().parent().find('.preview_image').slideUp( "slow", function() {
//                });
//             });


        </script>

        <script>
            function copyToClipboard(elementId, textarea_id) {
                $('#' + textarea_id).val(document.getElementById(elementId).innerHTML);
//                $('#hdn_copy_url').select();
                var text_len = ($('#' + textarea_id).val().length + 10);
                $('#' + textarea_id).focus();
                $('#' + textarea_id).get(0).setSelectionRange(0,text_len);
                document.execCommand("copy");
                $('#' + textarea_id).trigger('blur');
                $('#copy_msg_share').text('URL copied to clipboard');
                $('#copy_msg_share').fadeIn('slow');
                window.setTimeout(
                        function () {
                            $('#copy_msg_share').fadeOut('slow');
                        }
                , 1000);
            }
            function copyDataToClipboard(elementId, text_area_items = 'hdn_values_items') {
                $('#' + text_area_items).val(document.getElementById(elementId).innerHTML);
                var text_len = ($('#' + text_area_items).val().length + 10);
                $('#' + text_area_items).focus();
                $('#' + text_area_items).get(0).setSelectionRange(0,text_len);
                document.execCommand("copy");
//                $('#values_items').trigger('blur');
                $('#' + text_area_items).trigger('blur');
                $('#copy_msg').text('Data copied to clipboard');
                $('#copy_msg').fadeIn('slow');
                window.setTimeout(
                        function () {
                            $('#copy_msg').fadeOut('slow');
                        }
                , 1000);
            }

        </script>
        <?php
        $controller = $this->router->fetch_class();
        $method = $this->router->fetch_method();
//        if($controller == 'task' && ($method == 'index2')){
        ?>
        <script>
            
            var tabs = $( "#content" ).tabs({
                activate: function( event, ui ) {
                    var list_id = $(ui.newTab[0]).find('a').attr('data-listid');
//                    var list_id = $(this).attr('data-listid');
                    if($('#content').find('#tabs-' + list_id).find('.loader_img').length > 0){
                        $.ajax({
                            url:'/return_list_tab',
                            type: 'POST',
                            context: this,
                            data:{list_id: list_id},
                            success: function (res) {
                                if(res != 'fail' && res != '' && res != 'unauthorised'){
                                    $(document).find('#tabs-' + list_id).html(res);
                                    if($('#tabs-' + list_id).find('.edit_list_task_sub').attr('data-id') > 0){
                                        if($('#tabs-' + list_id).find('#listsub_Lock_lnk').length == 1){
                                            if($('#tabs-' + list_id).find('#listsub_Lock_lnk').hasClass('lock_hide')){
                                                $('#tabs-' + list_id).find('#config_icons').append($('#tabs-' + list_id).find('#listsub_Lock_lnk.lock_hide'));
                                            }
                                        }
                                        if($('#tabs-' + list_id).find('#listSub_Unlock_lnk').length == 1){
                                            if($('#tabs-' + list_id).find('#listSub_Unlock_lnk').hasClass('lock_hide')){
                                                $('#tabs-' + list_id).find('#config_icons').append($('#tabs-' + list_id).find('#listSub_Unlock_lnk.lock_hide'));
                                            }
                                        }
                                        if($('#tabs-' + list_id).find('.reset_sub_list').length == 1){
                                            if($('#tabs-' + list_id).find('.reset_sub_list').hasClass('login_prompt')){
                                                $('#tabs-' + list_id).find('#config_icons').append($('#tabs-' + list_id).find('.reset_sub_list.login_prompt'));
                                            }
                                        }
                                        if($('#tabs-' + list_id).find('#delete_sub_list_builder').length == 1){
                                            if($('#tabs-' + list_id).find('#delete_sub_list_builder').hasClass('disabled_btn')){
                                                $('#tabs-' + list_id).find('#config_icons').append($('#tabs-' + list_id).find('#delete_sub_list_builder.disabled_btn'));
                                            }
                                        }
                                        if($('#tabs-' + list_id).find('.sub_listConfig_lnk').attr('data-allowappendlocked') == 1){
                                            if($('#tabs-' + list_id).find('.ui-icon-triangle-1-e').length == 1){
                                                $('.ui-icon-triangle-1-e').trigger('click');
                                            }
                                        }
                                    }
                                    $('#tabs-' + list_id).find('.task_sub_name[data-type="datetime"]').datetimepicker({
                                        format: 'MM/DD/YYYY HH:mm',
                                        widgetPositioning: {
                                            horizontal: 'auto',
                                            vertical: 'bottom'
                                        }
                                    });
                                    $('#tabs-' + list_id).find('.task_sub_name[data-type="date"]').datetimepicker({
                                        format: 'MM/DD/YYYY',
                                        widgetPositioning: {
                                            horizontal: 'auto',
                                            vertical: 'bottom'
                                        }
                                    });
                                    $('#tabs-' + list_id).find('.task_sub_name[data-type="time"]').datetimepicker({
                                        format: 'HH:mm',
                                        widgetPositioning: {
                                            horizontal: 'auto',
                                            vertical: 'bottom'
                                        }
                                    });
                                }
                            }
                        });
                    }
                    
                }
                
            });
            
//                tabs.find( ".ui-tabs-nav" ).sortable({
//                    axis: "x",
//                    items: '> li:not(.add_tab_li)',
//                    stop: function() {
//                      tabs.tabs( "refresh" );
//                    },
//                });
            
            var tabTitle = $( "#tab_title" ),
            tabContent = $( "#tab_content" ),
            tabTemplate = "<li class='custom_tab'><a href='#{href}' class='custom_tab_anchor'>#{label}</a></li>",
            tabCounter = 2;
            
            function addTab(tab_data, list_id, column_id) {
                var label = 'List Name',
                id = "tabs-" + list_id,
                li = $( tabTemplate.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, label ) ),
                tabContentHtml = tab_data;
                
                  

                  $('#add_tab_li').before(li);
                tabs.append( "<div id='" + id + "'>" + tabContentHtml + "</div>" );
                tabs.tabs( "refresh" );
                
                $('.custom_tab:last').find('.custom_tab_anchor').trigger('click');
                
                $('#test_table_' + list_id).find('thead tr').sortable({
                    handle: '.move_sub_col',
                    cancel: '.noDrag',
                    connectWith: 'tbody thead tr.td_arrange_tr',
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
                        var next_class = $('#test_table_' + list_id).find('thead .td_arrange_tr th:nth-child(2)').attr('class');
                        if ($('#tabs-' + list_id).find('.icon-settings').attr('data-typeid') == 3) {
                            next_class = $('#test_table_' + list_id).find('thead .td_arrange_tr th:nth-child(3)').attr('class');
                        }
                        if (next_class == 'noDrag') {
                            event.preventDefault();
                        }

                        var total_rows = $('#test_table_' + list_id).find('tbody tr').length;

                        new_index = ui.item.index();
                        var col_ids = [];
                        var list_id = 0;
                        $('#test_table_' + list_id).find('.heading_items_col .add-data-title').each(function () {
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



                                    if ($('#tabs-' + list_id).find('.icon-settings').attr('data-typeid') == 3) {
                                        if (new_pos < 2) {
                                            new_pos = 2;
                                            $('#test_table_' + list_id).find('thead tr.td_arrange_tr').each(function () {
                                                var cols = $(this).children('th');
                                                cols.eq(old_pos).detach().insertBefore(cols.eq(new_pos));
                                            });
                                        }

                                    } else {
                                        if (new_pos < 1) {
                                            new_pos = 1;
                                            $('#test_table_' + list_id).find('thead tr.td_arrange_tr').each(function () {
                                                var cols = $(this).children('th');
                                                cols.eq(old_pos).detach().insertBefore(cols.eq(new_pos));
                                            });
                                        }

                                    }

                                    $('#test_table_' + list_id).find('thead tr.td_add_tr').each(function () {
                                        var cols = $(this).children('th');

                                        if (new_pos > old_pos) {
                                            cols.eq(old_pos).detach().insertAfter(cols.eq(new_pos));
                                        } else {
                                            cols.eq(old_pos).detach().insertBefore(cols.eq(new_pos));
                                        }
                                    });

                                    $('#test_table_' + list_id).find('tbody tr').each(function () {
                                        var cols = $(this).children('td');

                                        if (new_pos > old_pos) {
                                            cols.eq(old_pos).detach().insertAfter(cols.eq(new_pos));
                                        } else {
                                            cols.eq(old_pos).detach().insertBefore(cols.eq(new_pos));
                                        }
                                    });

                                    if (total_rows > 0) {

                                        if (resp.length > 0) {

                                            if($('#tabs-' + list_id).find('.icon-settings').attr('data-typeid') == 2 || $('#tabs-' + list_id).find('.icon-settings').attr('data-typeid') == 8){
                                                $.ajax({
                                                   url: '<?php echo base_url(); ?>get_nexup_box',
                                                   type: 'POST',
                                                   data: {
                                                       'list_id': list_id,
                                                   },
                                                   success: function (res) {
                                                       if(res != ''){
                                                           var row_indx = $('#span_task_' + res).parent().parent().parent().index();
                                                           if($('#tabs-' + list_id).find('.icon-settings').attr('data-typeid') == 2){
                                                               $('#tabs-' + list_id).find('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table_' + list_id).find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                                                                                           $('#tabs-' + list_id).find('.nexup-sub-group-one').attr('data-original-title', $.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                                               var item_list_nexup_data = '<tr><td>' + $.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                                               var grp_two = '';
                                                               for(s = 1; s < 4; s++){
                                                                   if($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').length == 1){
                                                                       grp_two += '<span data-toggle="tooltip" title="" data-original-title="' + $.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) +'">' + $.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</span>';
                                                                       item_list_nexup_data += '<tr><td>' + $.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                                                   }
                                                               }
                                                               grp_two += '<p data-toggle="modal" data-target="#items-list" id="items_model_p" data-rowid="' + (row_indx + 1) +'" data-placement="top" title="Show all items"><img src="<?php echo base_url(); ?>assets/img/information-button-icon-23.png"></p>';
                                                               if(grp_two != ''){
    //                                                               $('.nexup-sub-group-two').html(grp_two);
                                                                   $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').html(grp_two);
                                                               }

                                                           }else if($('#tabs-' + list_id).find('.icon-settings').attr('data-typeid') == 8){
                                                                $('#tabs-' + list_id).find('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                                           }
                                                           if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                                                                $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                                                   event.preventDefault()
                                                               }).tooltip({delay: { "hide": 100 }});
                                                           }
                                                           $(document).on('click', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                                                               $('.tooltip').remove()
                                                           });
                                                       }
                                                        var nexup_sun_grp_2_len = $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').length;
                                                        if(nexup_sun_grp_2_len == 1){
                                                            $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '18px');
                                                        }else if(nexup_sun_grp_2_len == 2){
                                                            $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '16px');
                                                        }else{
                                                            $('#tabs-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '13px');
                                                        }
                                                        set_nexup_box_data();
                                                   }
                                               });
                                               }
                                        }
                                    }

                                    var current_id = $('#tabs-' + list_id).find('.tasks_lists_display.ui-sortable').attr('id');
                                    $('#' + current_id).sortable('destroy');
                                    var first_id = $('#test_table_' + list_id).find('tbody tr:nth-child(2)').attr('id');
                                    $("#" + first_id).sortable({
                                        handle: '.icon-more',
                                        cancel: '.heading_col',
                                        update: function (event, ui) {
                                            if ($('#test_table_' + list_id).find('.rank_th').length > 0) {
                                                var rnk_val = 1;
                                                $('#test_table_' + list_id).find('.rank_th').each(function () {
                                                    $(this).text(rnk_val);
                                                    rnk_val++;
                                                });
                                            }
                                            var tasks_ids = [];
                                            $('#test_table_' + list_id).find('.tasks_lists_display li').each(function (e) {
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
                                                        if ($('#test_table_' + list_id).find('.icon-more-holder').length > 0) {
                                                            var ord_val = 1;
                                                            $('#test_table_' + list_id).find('.icon-more-holder').each(function () {
                                                                $(this).attr('data-order', ord_val);
                                                                ord_val++;
                                                            });
                                                        }
                                                        var total_rows = $('#test_table tbody tr').length;
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
                                    $('#test_table_' + list_id).find('thead tr').sortable('refresh');
                                }
                            }
                        });

                    }
                });
                tabCounter++;
            }
            
            $(document).on( "click", "#add_tab", function() {
                var parent_list_id = $('.edit_list_task').attr('data-id');
                $.ajax({
                    url: '<?php echo base_url() . 'listing/create_list_tab'; ?>',
                    type: 'POST',
                    data: {'parent_list_id' : parent_list_id},
                    success: function (res) {
                        if(res != '' || res != 'undefined'){
                            var resp = JSON.parse(res);
                            addTab(resp['list_body'], resp['new_list_id'], resp['new_col_id']);
                            if($('#tabs-' + resp['new_list_id']).find('#listsub_Lock_lnk').length == 1){
                                if($('#tabs-' + resp['new_list_id']).find('#listsub_Lock_lnk').hasClass('lock_hide')){
                                    $('#tabs-' + resp['new_list_id']).find('#config_icons').append($('#tabs-' + resp['new_list_id']).find('#listsub_Lock_lnk.lock_hide'));
                                }
                            }
                            $('.config-submodal-body').html(resp['list_config']);
                            $('#edit_list_name_sub_' + resp['new_list_id']).focus();
                            //Update configurations of sub-list (like allow re-ordering of list or not, Display completed tasks or not
                            var isTouchDevice = 'ontouchstart' in document.documentElement;
                            if (isTouchDevice) {
                                if ($(window).width() < 1367) {
                                    $('.enable-move').removeClass('hide_move_btn');
                                }
                            }
                        }
                    }
                });
            });
            
            $(document).on('click', '.icon-settings-sub', function () {
                if ($(this).hasClass('active-setting')) {
                    $(this).removeClass('active-setting');
                } else {
                    $(this).addClass('active-setting');
                }

                if ($(this).parent().parent().find('#config_icons').hasClass('hide_data'))
                {
                    $(this).parent().parent().find('#config_icons').fadeIn(1000);
                    $(this).parent().parent().find('#config_icons').removeClass('hide_data');
                }
                else
                {
                    $(this).parent().parent().find('#config_icons').fadeOut(1000);
                    $(this).parent().parent().find('#config_icons').addClass('hide_data');
                }
            });

            tabs.on( "click", "span.ui-icon-close", function() {
                var panelId = $( this ).closest( "li" ).remove().attr( "aria-controls" );
                $( "#" + panelId ).remove();
                tabs.tabs( "refresh" );
            });
            
            $(document).on('click', '#share_sub_list', function(){
                var list_id = $(this).attr('data-listid');
                
                $.ajax({
                    url: '<?php echo base_url() . 'get_list_details' ?>',
                    type: 'POST',
                    async: false,
                    data: {list_id: list_id},
                    success: function (res) {
                        if(res == '' || res == 'null' || res == 'empty'){
                            alert('Oops.. This list does not seems to be correct.');
                        }else{
                            var resp = JSON.parse(res);
//                            $('#share-contact-sublist').find('.sharemodal-sublist-head').find('h2').find('.share_heading_span_sublist').html('');
                            $('.share_heading_span_sublist').html(resp['name']);
                            var url_share = '<?php echo base_url(); ?>list/' + resp['slug'];
                            var url_sharing_html = '<div id="share_sublist_msg" class="alert no-border" style="display: none;"></div>';
                            url_sharing_html += '<div class="input-outer inner-outer-first-child">';
                            url_sharing_html += '<label>Share url</label>';
                            url_sharing_html += '<a id="import_contacts_sub" href="' + url_share + '" target="_blank">' + url_share + '</a>';
                            url_sharing_html += '<a class="btn btn-primary btn-sm customize-btn" id="customize_sub_btn">Customize</a>';
                            url_sharing_html += '<a class="btn btn-success btn-sm copy-btn" id="copy_sub_btn" onclick="copyToClipboard(\'import_contacts_sub\', \'hdn_copy_sub_url\');">Copy</a>';
                            url_sharing_html += '<textarea id="hdn_copy_sub_url" name="hdn_copy_sub_url" style="height:1px; width:1px;position: absolute;left: -1000px;"></textarea>';
                            url_sharing_html += '</div>';
                            url_sharing_html += '<div class="input-outer object_outer">';
                            url_sharing_html += '<span class="inflo_share_group_head">Share with others:</span>';
                            url_sharing_html += '<a class="btn btn-sm share-btn" id="share_btn" data-id="' + list_id + '">';
                            url_sharing_html += '<img class="inflo-icon" src="http://test.nexup.io//assets/img/inflo-alpha.png" alt=""> Share';
                            url_sharing_html += '</a>';
                            url_sharing_html += '<div class="input-outer object_outer input-sub-outer-cls">';
                            url_sharing_html += '</div>';
                            url_sharing_html += '</div>';
                            $('.sharemodal-sublist-body').html(url_sharing_html);
                        }
                    }
                });
                
                $.ajax({
                    url: '<?php echo base_url() . 'getShared' ?>',
                    type: 'POST',
                    data: {list_id: list_id},
                    success: function (res) {
                        $('#share-contact-sublist .input-outer.object_outer.input-sub-outer-cls').html(res);
                    }
                });
                
                
            });
            
            $(document).on('dblclick', '.custom_tab', function(){
                var list_id = $(this).find('a.custom_tab_anchor').attr('href');
                $(list_id).find('.edit_list_task_sub').trigger('click');
            });
            
            $(document).on('click', '.sub_listConfig_lnk', function(){
                $('.config-submodal-body').html('');
                var list_id = $(this).attr('data-listid');
                $.ajax({
                    url: '/get_list_config',
                    type: 'POST',
                    context: this,
                    data: {list_id: list_id},
                    success: function(res){
                        $('.config-submodal-body').html(res);
                    }
                });
            });
            
            $(document).on('click', '#save_sub_config', function () {
                $(this).css('pointer-events', 'none');
                var list_id = $(this).attr('data-listid');
                if ($('#tabs-' + list_id).find('.plus-category').attr('data-access') == 0) {
                    alert('You are not allowed to change configuration!');
                    return false;
                }
                
                var allow_move = 'False';
                if ($('.config-submodal-body').find('#move_sub_item').is(':checked')) {
                    allow_move = 'True';
                }
                
                var show_completed = 'False';
                if ($('.config-submodal-body').find('#show_sub_completed_item').is(':checked')) {
                    show_completed = 'True';
                }
                
                var allow_undo = 'False';
                if ($('.config-submodal-body').find('#undo_sub_item').is(':checked')) {
                    allow_undo = 'True';
                }
                
                var allow_maybe = '0';
                if ($('.config-submodal-body').find('#maybe_sub_allowed').is(':checked')) {
                    allow_maybe = '1';
                }
                
                var show_time = '0';
                if ($('.config-submodal-body').find('#show_sub_time').is(':checked')) {
                    show_time = '1';
                }
                
                var enable_comment = '0';
                if ($('.config-submodal-body').find('#visible_sub_comment').is(':checked')) {
                    enable_comment = '1';
                }
                
                var show_comment_attendance = '0';
                if ($('#enable_attendance_sub_comment').is(':checked')) {
                    show_comment_attendance = '1';
                }
                
                var show_preview = '0';
                if ($('.config-submodal-body').find('#show_sub_preview').is(':checked')) {
                    show_preview = '1';
                }
                
                var show_author = '0';
                if ($('.config-submodal-body').find('#show_sub_author').is(':checked')) {
                    show_author = '1';
                }
                
                var allow_append_locked = '0';
                if($('.config-submodal-body').find('#allow_append_sub_locked').is(':checked')){
                    allow_append_locked = '1';
                }
                
                var start_collapsed = '0';
                if ($('#start_collapsed').is(':checked')) {
                    start_collapsed = '1';
                }
                
                var visible_in_search = '0';
                if ($('#visible_in_search').is(':checked')) {
                    visible_in_search = '1';
                }
                
                $.ajax({
                    url: '<?php echo base_url() . 'update_config' ?>',
                    type: 'POST',
                    data: {
                        'list_id': list_id,
                        'allow_move': allow_move,
                        'show_completed': show_completed,
                        'allow_undo': allow_undo,
                        'allow_maybe': allow_maybe,
                        'show_time': show_time,
                        'enable_comment': enable_comment,
                        'show_preview': show_preview,
                        'show_author': show_author,
                        'allow_append_locked': allow_append_locked,
                        'show_comment_attendance': show_comment_attendance,
                        'start_collapsed': start_collapsed,
                        'visible_in_search': visible_in_search
                    },
                    success: function (res) {
                        if (res == 'success') {
                            if(allow_move == 'False'){
                                $('#tabs-' + list_id).find('.test_table').find('tbody').find('.icon-more').addClass('hidden_rearrange');
                                $('#tabs-' + list_id).find('.test_table').find('thead').find('.move_sub_col').addClass('hidden_rearrange');
                            }else{
                                $('#tabs-' + list_id).find('.test_table').find('tbody').find('.icon-more').removeClass('hidden_rearrange');
                                $('#tabs-' + list_id).find('.test_table').find('thead').find('.move_sub_col').removeClass('hidden_rearrange');
                            }
                            $('#config_sub_msg').html('Configurations updated successfully.');
                            $('#config_sub_msg').removeClass('alert-danger');
                            $('#config_sub_msg').addClass('alert-success');
                            $('#config_sub_msg').show();
                            $('#listConfig_lnk_' + list_id).attr('data-collapsed', start_collapsed);
                            if (allow_undo == 'False') {
                                $('#tabs-' + list_id).find('#nexup_btns .undo-btn').removeClass('disabled_undo');
                                $('#tabs-' + list_id).find('#nexup_btns .undo-btn').addClass('disabled_undo');
                                $('#tabs-' + list_id).find('#listConfig_lnk_' + list_id).attr('data-allowundo', '0');
                            } else {
                                $('#tabs-' + list_id).find('#nexup_btns .undo-btn').removeClass('disabled_undo');
                                $('#tabs-' + list_id).find('#listConfig_lnk_' + list_id).attr('data-allowundo', '1');
                            }
                            
                            
                            if (allow_move == 'False') {
                                $('#test_table_' + list_id).find("tbody").sortable("disable");
                                $('#tabs-' + list_id).find('#listConfig_lnk_' + list_id).attr('data-moveallow', '0');
                            } else {
                                $('#tabs-' + list_id).find('#listConfig_lnk_' + list_id).attr('data-moveallow', '1');
                                if ($('#tabs-' + list_id).find('#config_lnk_sub').attr('data-typeid') != 11) {
                                    if($('#test_table_' + list_id).find('tbody').hasClass('ui-sortable')) {
                                        $('#test_table_' + list_id).find("tbody").sortable("enable");
                                    } else {
                                        //                                    $("#TaskList").addClass('tasks_lists_display');
                                        $('#test_table_' + list_id).find("tbody").sortable({
                                            handle: '.icon-more',
                                            connectWith: ".tasks_lists_display",
                                            animation: 100,
                                            revert: 100,
                                            stop:function( event, ui ) {
                                                $('#tabs-' + list_id).find('.my_table').removeClass('my_scroll_table_no_overflow');
                                                $('#tabs-' + list_id).find('.my_table').addClass('my_scroll_table');
                                            },
                                            update: function (event, ui) {
                                                if ($('#test_table_' + list_id).find('.rank_th').length > 0) {
                                                    var rnk_val = 1;
                                                    $('#test_table_' + list_id).find('.rank_th').each(function () {
                                                        $(this).text(rnk_val);
                                                        rnk_val++;
                                                    });
                                                }
                                                var tasks_ids = [];
                                                $('#test_table_' + list_id).find('tbody tr td.icon-more-holder').each(function (e) {
                                                    var ids = $(this).attr('dta-taskid');
                                                    tasks_ids.push(ids);
                                                });

                                                var task_id = $(ui.item).children('th').attr('dta-taskid');
                                                var list_id = $(ui.item).children('th').attr('data-listid');
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
                                                            if ($('#test_table_' + list_id).find('.icon-more-holder').length > 0) {
                                                                var ord_val = 1;
                                                                $('#test_table_' + list_id).find('.icon-more-holder').each(function () {
                                                                    $(this).attr('data-order', ord_val);
                                                                    ord_val++;
                                                                });
                                                            }

                                                            var total_rows = $('#test_table_' + list_id).find('tbody tr').length;
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
                                    }
                                }

                            }
                            if (show_completed == 'False') {
                                $('#listConfig_lnk_' + list_id).attr('data-showcompleted', '0');
                                $('#test_table_' + list_id).find('.completed').addClass('hidden_tbl_row');
                            } else {
                                $('#listConfig_lnk_' + list_id).attr('data-showcompleted', '1');
                                $('#test_table_' + list_id).find('.completed').removeClass('hidden_tbl_row');
                            }
                            
                            if (allow_maybe == 0) {
                                $('#listConfig_lnk_' + list_id).attr('data-allowmaybe', '0');
                                $('#test_table_' + list_id).find('.present_lbl').each(function () {
                                    if ($(this).hasClass('yellow_label')) {
                                        $(this).removeClass('yellow_label');
                                    }
                                });
                                if($('#tabs-' + list_id).find('.yellow_box').length == 1){
                                    $('#tabs-' + list_id).find('.yellow_box').hide();
                                }
                            } else {
                                $('#listConfig_lnk_' + list_id).attr('data-allowmaybe', '1');
                                if($('#tabs-' + list_id).find('.yellow_box').length == 1){
                                    $('#tabs-' + list_id).find('.yellow_box').show();
                                }
                            }
                            
                            if (show_time == 0) {
                                $('#listConfig_lnk_' + list_id).attr('data-showtime', '0');

                                if (!$('#test_table_' + list_id).find('.nodrag_time').hasClass('hidden_nodrag')) {
                                    $('#test_table_' + list_id).find('.nodrag_time').addClass('hidden_nodrag');
                                }
                                $('#test_table_' + list_id).find('.check_date').each(function () {
                                    if (!$(this).parent().hasClass('hidden_nodrag')) {
                                        $(this).parent().addClass('hidden_nodrag');
                                    }
                                });


                                $('#test_table_' + list_id).find('.check_date').closest('td.list-table-view-attend').addClass('hidden_tbl_row');
                                if (!$('#test_table_' + list_id).find('.check_date').closest('td.list-table-view-attend').hasClass('hidden_nodrag')) {
                                    $('#test_table_' + list_id).find('.check_date').closest('td.list-table-view-attend').addClass('hidden_nodrag');
                                }
                            } else {
                                $('#listConfig_lnk_' + list_id).attr('data-showtime', '1');
                                $('#test_table_' + list_id).find('.check_date').closest('td.list-table-view-attend').removeClass('hidden_tbl_row');
                                if ($('#tabs-' + list_id).find('.icon-settings-sub').attr('data-typeid') == 11) {
                                    if ($('#test_table_' + list_id).find('.nodrag_time').hasClass('hidden_nodrag')) {
                                        $('#test_table_' + list_id).find('.nodrag_time').removeClass('hidden_nodrag');
                                    }
                                    $('#test_table_' + list_id).find('.check_date').each(function () {
                                        if ($(this).parent().hasClass('hidden_nodrag')) {
                                            $(this).parent().removeClass('hidden_nodrag');
                                        }
                                    });
                                }
                            }
                            
                            if (show_preview == 1) {
                                $('#tabs-' + list_id).find('#config_lnk_sub').attr('data-showprev', '1');
                            } else {
                                $('#tabs-' + list_id).find('#config_lnk_sub').attr('data-showprev', '0');
                            }
                            
                            if(show_author == 1){
                                $('#tabs-' + list_id).find('#config_lnk_sub').attr('data-showowner', '1');
                            }else{
                                $('#tabs-' + list_id).find('#config_lnk_sub').attr('data-showowner', '0');
                            }
                            
                            if(allow_append_locked == 1){
                                $('#tabs-' + list_id).find('.sub_listConfig_lnk').attr('data-allowappendLocked', '1');
                            }else{
                                $('#tabs-' + list_id).find('.sub_listConfig_lnk').attr('data-allowappendLocked', '0');
                            }
                            
                            if ($('#tabs-' + list_id).find('.count_box').length == 1) {
                                var total_lbl = $('#tabs-' + list_id).find('.present_lbl').length;
                                var yes_cnt = $('#tabs-' + list_id).find('.green_label').length;
                                var maybe_cnt = $('#tabs-' + list_id).find('.yellow_label').length;
                                var no_cnt = $('#tabs-' + list_id).find('.red_label').length;
                                var blank_cnt = total_lbl - (yes_cnt + maybe_cnt + no_cnt);

                                $('#tabs-' + list_id).find('#yes_cnt').text(yes_cnt);
                                $('#tabs-' + list_id).find('#maybe_cnt').text(maybe_cnt);
                                $('#tabs-' + list_id).find('#no_cnt').text(no_cnt);
                                $('#tabs-' + list_id).find('#blank_cnt').text(blank_cnt);
                            }
                            
                            $.ajax({
                                url: '<?php echo base_url() . 'save_config' ?>',
                                type: 'POST',
                                data: {
                                    'list_id': list_id,
                                    'allow_move': allow_move,
                                    'show_completed': show_completed
                                },
                                success: function (res) {
                                }
                            });
                            
                            $.ajax({
                                url: '<?php echo base_url() . 'listing/get_list_body'; ?>',
                                type: 'POST',
                                data: {
                                    'Listid': list_id,
                                },
                                success: function (res) {
                                    var resp = JSON.parse(res);
                                    $('#test_table_' + list_id).find('tbody').html(resp['body']);
                                    if ($('#tabs-' + list_id).find('#config_lnk_sub').attr('data-typeid') == 11) {
                                        $('#test_table_' + list_id).find("tbody").sortable("disable");
                                    } else {
                                        $('#test_table_' + list_id).find("tbody").sortable("enable");
                                    }
                                    if(show_author == 1){
                                        if($('#tabs-' + list_id).find('.sub_list_title_head').find('.list_author_cls').length == 1){
                                            $('#tabs-' + list_id).find('.sub_list_title_head').find('.list_author_cls').html(resp['owner']);
                                        }else{
                                            $('#tabs-' + list_id).find('.sub_list_title_head').append('<div class="list_author_cls">' + resp['owner'] + '</div>');
                                        }
                                    }else{
                                        $('#tabs-' + list_id).find('.list_author_cls').remove();
                                    }
                                    
                                    if($('#listConfig_lnk_' + list_id).attr('data-moveallow') == 0){
                                        $('#test_table_' + list_id).find("tbody").sortable("disable");
                                    }

                                }
                            });
                            
                            if (enable_comment == 0) {
                                if (!$('#tabs-' + list_id).find('#nexup_cmnt_span').hasClass('hide_box')) {
                                    $('#tabs-' + list_id).find('#nexup_cmnt_span').addClass('hide_box');
                                }
                            } else {
                                if ($('#tabs-' + list_id).find('#nexup_cmnt_span').hasClass('hide_box')) {
                                    $('#tabs-' + list_id).find('#nexup_cmnt_span').removeClass('hide_box');
                                }
                            }
                            if($('#config_lnk_sub').attr('data-typeid') == 11){
                                if (show_comment_attendance == 0) {
                                    if(!$('#test_table_' + list_id).find('thead').find('.td_arrange_tr').find('.nodrag_comment').hasClass('hidden_nodrag')){
                                        $('#test_table_' + list_id).find('thead').find('.td_arrange_tr').find('.nodrag_comment').addClass('hidden_nodrag');
                                    }
                                } else {
                                    if($('#test_table_' + list_id).find('thead').find('.td_arrange_tr').find('.nodrag_comment').hasClass('hidden_nodrag')){
                                        $('#test_table_' + list_id).find('thead').find('.td_arrange_tr').find('.nodrag_comment').removeClass('hidden_nodrag');
                                    }
                                }
                            }
                            
                            $('#listConfig_lnk_' + list_id).attr('data-allowcmnt', enable_comment);
                            $('.config-modal-sub-list').modal('toggle');
                            $('#save_sub_config').css('pointer-events', 'all');
                            if(show_author == 0){
                                if(!$('#tabs-' + list_id).find('.list_author_cls').hasClass('hide_author')){
                                    $('#tabs-' + list_id).find('.list_author_cls').addClass('hide_author');
                                }
                            }else{
                                if($('#tabs-' + list_id).find('.list_author_cls').hasClass('hide_author')){
                                    $('#tabs-' + list_id).find('.list_author_cls').removeClass('hide_author');
                                }
                            }
                        } else if (res == 'not allowed') {
                            $('#config_sub_msg').html('You have not created list. Please create list to proceed!');
                            $('#config_sub_msg').addClass('alert-danger');
                            $('#config_sub_msg').removeClass('alert-success');
                            $('#config_sub_msg').show();
                        } else {
                            $('#config_sub_msg').html('Something went wrong. Configuration was not updated. Please try again!');
                            $('#config_sub_msg').addClass('alert-danger');
                            $('#config_sub_msg').removeClass('alert-success');
                            $('#config_sub_msg').show();
                        }
                        
                        $('#config_sub_msg').delay(5000).fadeOut('fast');
                    }
                });
            });
            
            
            $(document).on('click', '.remove_sub_col', function () {
                var list_id = $(this).attr('data-listid');
                if ($('#tabs-' + list_id).find('.plus-category').attr('data-access') == 0) {
                    alert('You are not allowed to remove this column!');
                    return false;
                }
                var col_index = $(this).closest('.heading_items_col').index();
                if (confirm('Are you sure want to delete this column?')) {
                    var col_id = $(this).attr('data-colid');
                    $.ajax({
                        url: '<?php echo base_url(); ?>delete_column',
                        type: 'POST',
                        context: this,
                        data: {
                            'list_id': list_id,
                            'column_id': col_id
                        },
                        success: function (res) {
                            if (res == 'success') {
                                if ($('#test_table_' + list_id).find('thead tr.td_arrange_tr th.heading_items_col').length <= 2) {
                                    $('#test_table_' + list_id).find('thead tr.td_arrange_tr th:eq(' + col_index + ')').remove();
                                    if ($('#tabs-' + list_id).find('.icon-settings-sub').attr('data-typeid') != 11) {
                                        $('#test_table_' + list_id).find('.heading_items_col').addClass('hidden_heading');
                                    }
                                } else if ($('#test_table_' + list_id).find('thead tr.td_arrange_tr th.heading_items_col').length > 2) {
                                    $('#test_table_' + list_id).find('thead tr.td_arrange_tr th:eq(' + col_index + ')').remove();
                                }

                                if ($('#test_table_' + list_id).find('thead tr.td_add_tr th.heading_items_col_add').length > 1) {
                                    $('#test_table_' + list_id).find('thead tr.td_add_tr th:eq(' + col_index + ')').remove();
                                } else {
                                    $('#test_table_' + list_id).find('#task_name').attr('data-colid', 0);
                                }
                                $('#test_table_' + list_id).find('tbody tr').each(function () {
                                    $(this).children('td:eq(' + col_index + ')').remove();
                                });

                                $(this).parents('.tasks_lists_display').remove();

                                var current_id = $('#tabs-' + list_id).find('.tasks_lists_display.ui-sortable').attr('id');
                                $('#' + current_id).sortable('destroy');
                                var first_id = $('#tabs-' + list_id).find('#TaskListDiv ul:nth-child(2)').attr('id');
                                $("#" + first_id).sortable({
                                    handle: '.icon-more',
                                    cancel: '.heading_col',
                                    update: function (event, ui) {
                                        if ($('#test_table_' + list_id).find('.rank_th').length > 0) {
                                            var rnk_val = 1;
                                            $('#test_table_' + list_id).find('.rank_th').each(function () {
                                                $(this).text(rnk_val);
                                                rnk_val++;
                                            });
                                        }
                                        var tasks_ids = [];
                                        $('#tabs-' + list_id).find('.tasks_lists_display li').each(function (e) {
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
                                                    if ($('#test_table_' + list_id).find('.icon-more-holder').length > 0) {
                                                        var ord_val = 1;
                                                        $('#test_table_' + list_id).find('.icon-more-holder').each(function () {
                                                            $(this).attr('data-order', ord_val);
                                                            ord_val++;
                                                        });
                                                    }
                                                    var total_rows = $('#test_table_' + list_id).find('tbody tr').length;
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

                                $('#test_table_' + list_id).find('tbody tr').each(function () {
                                    var item_id = $(this).children('td.list-table-view:eq(0)').find('.edit_task').attr('data-id');
                                    var item_name = $(this).children('td.list-table-view:eq(0)').find('.edit_task').attr('data-task');

                                    $(this).children('td.icon-more-holder:eq(0)').attr('data-taskname', item_name);



                                    $(this).children('td.icon-more-holder:eq(0)').find('.delete_task').attr('data-id', item_id);
                                    $(this).children('td.icon-more-holder:eq(0)').find('.complete_task').attr('data-id', item_id);
                                    $(this).children('td.icon-more-holder:eq(0)').find('.present_task').attr('data-id', item_id);

                                    $(this).children('td.icon-more-holder:eq(0)').find('.delete_task').attr('data-task', item_name);

                                    $(this).children('td.icon-more-holder:eq(0)').find('.complete_task').attr('id', 'complete_' + item_id);
                                    $(this).children('td.icon-more-holder:eq(0)').find('.present_task').attr('id', 'present_' + item_id);

                                    $(this).children('td.icon-more-holder:eq(0)').find('.complete_lbl').attr('for', 'complete_' + item_id);
                                    $(this).children('td.icon-more-holder:eq(0)').find('.present_lbl').attr('for', 'present_' + item_id);

                                });
                                if ($('#test_table_' + list_id).find('.edit_task').length == 0 && $('#test_table_' + list_id).find('.heading_items_col_add').length == 1) {
                                    $('#tabs-' + list_id).find('.add-data-head-r').hide();
                                }
                                
                                if($('.icon-settings').attr('data-typeid') == 2 || $('.icon-settings').attr('data-typeid') == 8){
                             $.ajax({
                                url: '<?php echo base_url(); ?>get_nexup_box',
                                type: 'POST',
                                data: {
                                    'list_id': list_id,
                                },
                                success: function (res) {
                                    if(res != ''){
                                        var row_indx = $('#span_task_' + res).parent().parent().parent().index();
                                        if($('.icon-settings').attr('data-typeid') == 2){
                                            $('#tab-' + list_id).find('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                            $('#tab-' + list_id).find('.nexup-sub-group-one').attr('data-original-title', $.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                            var item_list_nexup_data = '<tr><td>' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                            var grp_two = '';
                                            for(s = 1; s < 4; s++){
                                                if($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').length == 1){
                                                    grp_two += '<span data-toggle="tooltip" title="" data-original-title="' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) +'">' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</span>';
                                                    item_list_nexup_data += '<tr><td>' + $.trim($('#test_table').find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(' + s + ')').find('.add-data-div').find('.task_name_span').text()) + '</td></tr>';
                                                }
                                            }
                                            grp_two += '<p data-toggle="modal" data-target="#items-list" id="items_model_p" data-rowid="' + (row_indx + 1) +'" data-placement="top" title="Show all items"><img src="<?php echo base_url(); ?>assets/img/information-button-icon-23.png"></p>';
                                            if(grp_two != ''){
                                                $('#tab-' + list_id).find('.button-outer').find('.nexup-sub-group-two').html(grp_two);
                                            }
                                            
                                        }else if($('#tab-' + list_id).find('.icon-settings-sub').attr('data-typeid') == 8){
                                             $('#tab-' + list_id).find('.nexup-sub-group-one').find('#next_task_name').text($.trim($('#test_table_' + list_id).find('tbody').find('tr:eq(' + row_indx + ')').find('td.list-table-view:eq(0)').find('.add-data-div').find('.task_name_span').text()));
                                        }
                                        if (!navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                                            $('[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list').on('mouseout focusout', function (event) {
                                                event.preventDefault()
                                            }).tooltip({delay: { "hide": 100 }});
                                        }
                                        $(document).on('click', '[data-toggle="tooltip"], #listTypes_lnk, #listConfig_lnk, #share_list', function (event) {
                                            $('.tooltip').remove()
                                        });
                                        set_nexup_box_data();
                                    }
                                    var nexup_sun_grp_2_len = $('#tab-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').length;
                                    if(nexup_sun_grp_2_len == 1){
                                        $('#tab-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '18px');
                                    }else if(nexup_sun_grp_2_len == 2){
                                        $('#tab-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '16px');
                                    }else{
                                        $('#tab-' + list_id).find('.button-outer').find('.nexup-sub-group-two').find('span').css('font-size', '13px');
                                    }
                                    
                                }
                            });
                            }
                            } else if (res == 'not allowed') {
                                alert('Nope...\nThis operation is not allowed.');
                                return false;
                            }

                            if ($('#tab-' + list_id).find('#config_lnk_sub').attr('data-typeid') != 11) {
                                if ($(window).width() >= 1350) {
                                    if ($('#test_table_' + list_id).find('.task_name').length <= 3) {
                                        if (!$('#tabs-' + list_id).find('#added_div').hasClass('add-data-left')) {
                                            $('#tabs-' + list_id).find('#added_div').addClass('add-data-left');
                                            $('#tabs-' + list_id).find('.my_table').addClass('table_one_page');
                                        }
                                    } else {
                                        if ($('#tabs-' + list_id).find('#added_div').hasClass('add-data-left')) {
                                            $('#tabs-' + list_id).find('#added_div').removeClass('add-data-left');
                                            $('#tabs-' + list_id).find('.my_table').removeClass('table_one_page');
                                        }
                                    }
                                } else if ($(window).width() >= 1200 && $(window).width() < 1350) {
                                    if ($('#test_table_' + list_id).find('.task_name').length <= 2) {
                                        if (!$('#tabs-' + list_id).find('#added_div').hasClass('add-data-left')) {
                                            $('#tabs-' + list_id).find('#added_div').addClass('add-data-left');
                                            $('#tabs-' + list_id).find('.my_table').addClass('table_one_page');
                                        }
                                    } else {
                                        if ($('#tabs-' + list_id).find('#added_div').hasClass('add-data-left')) {
                                            $('#tabs-' + list_id).find('#added_div').removeClass('add-data-left');
                                            $('#tabs-' + list_id).find('.my_table').removeClass('table_one_page');
                                        }
                                    }
                                }
                            } else {
                                if ($('#tabs-' + list_id).find('#added_div').hasClass('add-data-left')) {
                                    $('#tabs-' + list_id).find('#added_div').removeClass('add-data-left');
                                    $('#tabs-' + list_id).find('.my_table').addClass('table_one_page');
                                }
                            }
                        }
                    });
                }
                return false;
            });
            
            $(document).on('change', 'input[type=radio][name=type_option]', function(){
                var elem_id = $(this).attr('id');
                if(elem_id == 'type_option_normal'){
                    $('#spearator_sub_options').show();
                }else{
                    $('#spearator_sub_options').hide();
                }
            });
            
            $(document).on('paste', '#values_sub_items', function(event){
                event.preventDefault();
                var clipboardData = event.originalEvent.clipboardData.getData('text/plain');
                var sep = $('#spearator_sub_options').val();
                if (!$('#type_option_cb_email').is(':checked')) {
                    var start = window.getSelection().anchorOffset;
                    var paste_data = clipboardData.split("\n");
                    var parsed_data = '';
                    for(x = 0; x < paste_data.length; x++){
                        var datavalue = paste_data[x].split(",");
                        if($('#spearator_sub_options').val() == 'tab'){
                            datavalue = paste_data[x].split("\t");
                        }
                        
                        for (var y = 0; y < datavalue.length; y++){
                            var temp = datavalue[y];
                            if (sep == 'comma') {
                                temp = '"' + datavalue[y] + '"';
                            }
                            
                            if (y == 0) {
                                parsed_data = parsed_data + temp.trim();
                            } else {
                                if (sep == 'comma') {
                                    parsed_data += ',' + temp.trim();
                                } else {
                                    parsed_data += "\t" + temp.trim();
                                }
                            }
                        }
                        if(x < paste_data.length - 1){
                            parsed_data += '\n';
                        }
                    }
                    
                    document.execCommand('insertText', false, parsed_data.trim());
                }else{
                    document.execCommand('insertText', true, clipboardData.trim());
                }
                    
                $("#values_sub_items").animate({
                    scrollTop: $("#values_sub_items").prop("scrollHeight")
                }, 2000);
            });
            
            $(document).on('change', '#type_option_cb_email', function(){
                if($(this).is(':checked')){
                    var str_add = '';
                    if ($("#values_sub_items").text().indexOf("Name<email>") !== 0 || $("#values_items").text().indexOf("name<email>") !== 0){
                        str_add += 'Name<email>' + '\n';
                    }
                    str_add += $("#values_sub_items").text();
//                    console.log(str_add);
                    $("#values_sub_items").text(str_add);
                    $("#values_sub_items").animate({
                        scrollTop: $("#values_sub_items").prop("scrollHeight")
                    }, 2000);
                }else{
                     var str_add = '';
                    if ($("#values_sub_items").text().indexOf("Name<email>") === 0){
                        str_add += $("#values_sub_items").text().replace("Name<email>\n", "");
                    }
                    if ($("#values_sub_items").text().indexOf("name<email>") === 0){
                        str_add += $("#values_sub_items").text().replace("name<email>\n", "");
                    }
                    $("#values_sub_items").text(str_add);
                    $("#values_sub_items").animate({
                        scrollTop: $("#values_sub_items").prop("scrollHeight")
                    }, 2000);
                }
            });
            
            $(document).on('click', '#import_sub_bulk_btn', function(){
                $('#bulk_sub_import').trigger('click');
            });
            
            $(document).on('change', '#bulk_sub_import', function (e) {
                var sep = $('#spearator_sub_options').val();
                var ext = $("input#bulk_sub_import").val().split(".").pop().toLowerCase();
                if ($.inArray(ext, ["csv"]) == -1) {
                    alert('File you uploaded is not csv. Please upload a csv file!');
                    return false;
                }
                if (e.target.files != undefined) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        var inputrad = "";
                        if ($('#type_options').val() == 'normal') {
                            var csvval = e.target.result.split("\n");
                            for (j = 0; j < csvval.length; j++) {
                                var csvvalue = csvval[j].split(",");

                                for (var i = 0; i < csvvalue.length; i++)
                                {
                                    var temp = csvvalue[i];
                                    if (sep == 'comma') {
                                        temp = '"' + csvvalue[i] + '"';
                                    }


                                    if (i == 0) {
                                        inputrad += inputrad + temp
                                    } else {
                                        if (sep == 'comma') {
                                            inputrad += ',' + inputrad + ',' + temp
                                        } else {
                                            inputrad += inputrad + "\t" + temp
                                        }
                                    }
                                }
                            }
                        } else {
                            inputrad = parseEmailRecipients(e.target.result, sep);
                        }
                        var input_vals = $("#values_sub_items").html();
                        input_vals += inputrad;
                        $("#values_sub_items").html(input_vals);
                        $("#init_value_sub_items").html(input_vals);
                        $("#values_sub_items").animate({
                            scrollTop: $("#values_sub_items").prop("scrollHeight")
                        }, 2000);
                        var el = document.getElementById("values_sub_items");
                        var endrange = $("#values_sub_items").html().length;
                        var range = document.createRange();
                        var sel = window.getSelection();
                        range.setStart(el.childNodes[0], endrange);
                        range.collapse(true);
                        sel.removeAllRanges();
                        sel.addRange(range);
                    };
                    reader.readAsText(e.target.files.item(0));
                }
                $('#bulk_sub_import').val('');
            });
            
            $(document).on('click', '.icon_listing_table', function (){
                $('.icon_listing_table').css({transform: 'rotate(90deg)'});
                $('.tooltip').remove();
                if(!$(this).parent().find('.div_option_wrap').is(':visible')){
                    $('.div_option_wrap').hide();
                    $(this).css({transform: 'rotate(0deg)', right: '20px'});
                }else{
                    $(this).css({transform: 'rotate(90deg)', right: '10px'});
                }
                $(this).parent().find('.div_option_wrap').toggle( "slow", function() {});
                if($(this).parent().parent().parent().parent().parent().hasClass('my_table_drop')){
                    setTimeout( function(){ 
                        if($(".div_option_wrap:visible").length == 0){
                            $('.my_table').removeClass('my_table_drop');
                        }
                    }  , 1000 );
                }else{
                    $('.my_table').addClass('my_table_drop');
                }
                return false;
            });
            
            $(document).on('change', '.custom_radio_class input[type="radio"]', function(){
                var current_type = $(this).parent().parent().parent().parent().parent().find('.add-data-title').attr('data-type');
                var new_type = $(this).val();
                var cnf = 0;
                var clear_data = 1;
                
                if((current_type == 'text' && new_type == 'memo') || (current_type == 'memo' && new_type == 'text') || (current_type == 'number' && (new_type == 'memo'|| new_type == 'text'))){
                        cnf = 1;
                        clear_data = 0;
                }else{
                    cnf = confirm('All your data will be loss if you change column type. Are you sure want to proceed?');
                    clear_data = 1;
                }
                
//                var cnf = confirm('All your data will be loss if you change column type. Are you sure want to proceed?');
                if (cnf) {
                    var col_id = ($(this).attr('data-col_id'));
                    var col_name = $('#col_name_' + col_id).text();
                    var list_id = $(this).parent().parent().parent().attr('data-listid');
                    var height_col = 0;
                    if(new_type == 'memo'){
                        height_col = $(this).parent().parent().find('.plus_minus_wrap').find('#number_rows').val();
                    }
                    $.ajax({
                        url: '<?php echo base_url() . 'listing/update_col_type'; ?>',
                        type: 'POST',
                        data: {
                            list_id: list_id,
                            col_id: col_id,
                            type: new_type,
                            height_col: height_col,
                            clear_data: clear_data
                        },
                        success: function (res) {
                            if(res == 'success'){
                                var input_var = '';
                                if(new_type == 'memo'){
                                    input_var = '<textarea name="task_name" id="task_name" class="task_sub_name" data-listid="' + list_id + '" data-colid="' + col_id + '" placeholder="Add ' + col_name + '" data-type="memo" data-gramm_editor="false" rows="' + height_col + '"></textarea>';
                                } else if(new_type == 'number'){
                                    input_var = '<input type="number" name="task_name" id="task_name" class="task_sub_name" data-listid="' + list_id + '" data-colid="' + col_id + '" placeholder="Add ' + col_name + '" data-type="number">';
                                } else if(new_type == 'email'){
                                    input_var = '<input type="text" name="task_name" id="task_name" class="task_sub_name" data-listid="' + list_id + '" data-colid="' + col_id + '" placeholder="username@domain.com" data-type="email">';
                                } else if(new_type == 'email'){
                                    input_var = '<input type="text" name="task_name" id="task_name" class="task_sub_name" data-listid="' + list_id + '" data-colid="' + col_id + '" placeholder="username@domain.com" data-type="email">';
                                } else {
                                    input_var = '<input type="text" name="task_name" id="task_name" class="task_sub_name" data-listid="' + list_id + '" data-colid="' + col_id + '" placeholder="Add ' + col_name + '" data-type="text">';
                                }
                                if(input_var != ''){
                                    $('.heading_items_col_add[data-colid=' + col_id + ']').find('.add-data-input').find('#task_name').remove();
                                    $('.heading_items_col_add[data-colid=' + col_id + ']').find('.add-data-input').prepend(input_var);
                                }
                                $('#task_name[data-colid="' + col_id + '"]').attr('data-type', new_type);
                                $('#col_name_' + col_id).parent().attr('data-type', new_type);
                                
                                
                                
                                var place_holder = 'Add ' + $('#col_name_' + col_id).text();
                                if($('#col_name_' + col_id).parent().attr('data-type') == 'datetime'){
                                    place_holder = 'MM/DD/YYYY HH:MM';
                                } else if($('#col_name_' + col_id).parent().attr('data-type') == 'date'){
                                    place_holder = 'MM/DD/YYYY';
                                } else if($('#col_name_' + col_id).parent().attr('data-type') == 'time'){
                                    place_holder = 'HH:MM';
                                } else if($('#col_name_' + col_id).parent().attr('data-type') == 'currency'){
                                    place_holder = '$';
                                } else if($('#col_name_' + col_id).parent().attr('data-type') == 'number'){
                                    place_holder = col_name;
                                } else if($('#col_name_' + col_id).parent().attr('data-type') == 'email'){
                                    place_holder = 'username@domain.com';
                                }
                                $('.task_sub_name[data-colid="' + col_id + '"]').attr('placeholder', place_holder);
                                $.ajax({
                                    url: '<?php echo base_url() . 'listing/get_list_body'; ?>',
                                    type: 'POST',
                                    data: {
                                        'Listid': list_id,
                                    },
                                    success: function (res) {
                                        var resp = JSON.parse(res);
                                        $('#tabs-' + list_id).find('.test_table tbody').html(resp['body']);
                                        if ($('#config_lnk').attr('data-typeid') == 11) {
                                            $("#test_table tbody").sortable("disable");
                                        } else {
                                            $("#test_table tbody").sortable("enable");
                                        }
                                        if ($('#config_lnk').attr('data-showowner') == 1) {
                                            if ($('.list_title_head').find('.list_author_cls').length == 1) {
                                                $('.list_author_cls').html(resp['owner']);
                                            } else {
                                                $('.list_title_head').append('<div class="list_author_cls">' + resp['owner'] + '</div>');
                                            }
                                        } else {
                                            $('.list_author_cls').remove();
                                        }

                                        if ($('#listConfig_lnk').attr('data-moveallow') == 0) {
                                            $("#test_table tbody").sortable("disable");
                                        }
                                        if(new_type != 'checkbox' && new_type != 'timestamp'){
                                            if(new_type != 'checkbox'){
                                                if($('.heading_items_col_add[data-colid=' + col_id + ']').find('.add-data-input').find('.check_all_check_box').length > 0){
                                                    $('.heading_items_col_add[data-colid=' + col_id + ']').find('.add-data-input').find('.check_all_check_box').remove();
                                                }
                                                if($('.heading_items_col_add[data-colid=' + col_id + ']').find('.add-data-input').find('.timestamp-all-btn').length > 0){
                                                    $('.heading_items_col_add[data-colid=' + col_id + ']').find('.add-data-input').find('.timestamp-all-btn').remove();
                                                }
                                                $('.heading_items_col_add[data-colid=' + col_id + ']').find('div.add-data-input').find('.task_sub_name').show();
                                            }
                                        }else{
                                            $('.heading_items_col_add[data-colid=' + col_id + ']').find('div.add-data-input').find('.task_sub_name').hide();
                                            
                                            if(new_type == 'checkbox'){
                                                var check_all_box = '<input type="checkbox" id="check_all_' + col_id + '" class="check_all_check_box" data-colid="' + col_id + '">';
                                                if($('.heading_items_col_add[data-colid=' + col_id + ']').find('.add-data-input').find('.check_all_check_box').length == 0){
                                                    $('.heading_items_col_add[data-colid=' + col_id + ']').find('.add-data-input').append(check_all_box);
                                                }
                                            } else if(new_type == 'timestamp'){
                                                $('.heading_items_col_add[data-colid=' + col_id + ']').find('.add-data-input').find('.check_all_check_box').remove();
                                                var timestamp_all_btn = '<a class="btn btn-default timestamp-all-btn" data-colid="' + col_id + '">Timestamp</a>';
                                                if($('.heading_items_col_add[data-colid=' + col_id + ']').find('.add-data-input').find('.timestamp-all-btn').length == 0){
                                                    $('.heading_items_col_add[data-colid=' + col_id + ']').find('.add-data-input').append(timestamp_all_btn);
                                                }
                                            }
                                        }
                                        if(new_type == 'memo'){
                                            var col_indx = $('#test_table_' + list_id).find('.td_arrange_tr').find('th.heading_items_col').find('.add-data-title[data-colid="' + col_id + '"]').index();
                                            var div_css = '';
                                            if(height_col == 1){
                                                div_css = '50px';
                                            } else if(height_col == 2){
                                                div_css = '80px';
                                            } else if(height_col == 1){
                                                div_css = '100px';
                                            }else{
                                                var height = 110 + (30 * (height_col - 3)) + 'px';
                                                div_css = height;
                                            }
                                            $('#test_table_' + list_id).find('tbody').find('tr').each(function(){
                                                $(this).find('.list-table-view:eq(' + (col_indx - 1) + ')').find('.add-data-div').css('padding', '11px 20px;');
                                            });
                                            $('#test_table_' + list_id).find('tbody').find('tr').each(function(){
                                                $(this).find('.list-table-view:eq(' + (col_indx - 1) + ')').find('.add-data-div').css('height', div_css);
                                            });
                                        }
                                    }
                                });
                                if(new_type == 'datetime'){
                                    $('.task_sub_name[data-colid="' + col_id + '"]').datetimepicker({
                                        format: 'MM/DD/YYYY HH:mm',
                                        widgetPositioning: {
                                            horizontal: 'auto',
                                            vertical: 'bottom'
                                        }
                                    });
                                }
                                if(new_type == 'date'){
                                    $('.task_sub_name[data-colid="' + col_id + '"]').datetimepicker({
                                        format: 'MM/DD/YYYY',
                                        widgetPositioning: {
                                            horizontal: 'auto',
                                            vertical: 'bottom'
                                        }
                                    });
                                }
                                if(new_type == 'time'){
                                    $('.task_sub_name[data-colid="' + col_id + '"]').datetimepicker({
                                        format: 'HH:mm',
                                        widgetPositioning: {
                                            horizontal: 'auto',
                                            vertical: 'bottom'
                                        }
                                    });
                                }
                            }
                        }
                    });
                }else{
                $(this).removeAttr('checked');
                $(this).parent().parent().parent().find('.custom_radio_class').find('input[value="' + current_type + '"]').prop('checked', true);
                }
            });
            
            $(document).on('change', '.my_data_checkbox', function(){
//                var list_id = $('.edit_list_task').attr('data-id');
                var list_id = $(this).parent().parent().attr('data-listid');
                var item_id = $(this).attr('data-id');
                var chkbx = '<input type="checkbox" name="value_cb_' + item_id + '" data-id="' + item_id + '" class="my_data_checkbox">';
                if($(this).is(':checked')){
                    chkbx = '<input type="checkbox" name="value_cb_' + item_id + '" data-id="' + item_id + '" class="my_data_checkbox" checked="checked">';
                }
                
                $.ajax({
                    url: "<?php echo base_url() . 'item/update'; ?>",
                    type: 'POST',
                    data: {
                        'ListId': list_id,
                        'TaskId': item_id,
                        'Taskname': chkbx
                    },
                    success: function (res) {
                        if (res == 'fail') {
                            $('.edit_task_cls').text('Something went wrong. Please try again!');
                            $('.edit_task_cls').show();
                        } else {
                            $.ajax({
                                url: "<?php echo base_url() . 'item/push'; ?>",
                                type: 'POST',
                                data: {
                                    'ListId': list_id,
                                    'TaskId': item_id,
                                    'Taskname': chkbx
                                },
                                success: function (response) {
                                }
                            });
                        }
                    }
                });
            });
            
            $(document).on('click touchstart', '.plus_a', function(){
                var num_exist = $(this).parent().find('#number_rows').val();
                if(num_exist != ''){
                    $(this).parent().find('#number_rows').val(parseInt(num_exist) + 1);
                }else{
                    $(this).parent().find('#number_rows').val(1);
                }
                if($(this).parent().parent().find('.custom_radio_class').find('input[type="radio"]').is(':checked')){
                    var col_id = ($(this).parent().parent().find('.custom_radio_class').find('input[type="radio"]').attr('data-col_id'));
                    var list_id = $(this).parent().parent().parent().attr('data-listid');
                    var height_col = $(this).parent().find('#number_rows').val();

                    $.ajax({
                        url: '<?php echo base_url() . 'listing/update_col_height'; ?>',
                        type: 'POST',
                        data: {
                            list_id: list_id,
                            col_id: col_id,
                            height_col: height_col
                        },
                        success: function (res) {
                            if(res == 'success'){
                                var col_indx = $('#test_table_' + list_id).find('.td_arrange_tr').find('th.heading_items_col').find('.add-data-title[data-colid="' + col_id + '"]').index();
                                var div_css = '';
                                if(height_col == 1){
                                    div_css = '50px';
                                } else if(height_col == 2){
                                    div_css = '80px';
                                } else if(height_col == 1){
                                    div_css = '100px';
                                }else{
                                    var height = 110 + (30 * (height_col - 3)) + 'px';
                                    div_css = height;
                                }
                                $('#test_table_' + list_id).find('tbody').find('tr').each(function(){
                                    $(this).find('.list-table-view:eq(' + (col_indx - 1) + ')').find('.add-data-div').css('padding', '11px 20px;');
                                });
                                $('#test_table_' + list_id).find('tbody').find('tr').each(function(){
                                    $(this).find('.list-table-view:eq(' + (col_indx - 1) + ')').find('.add-data-div').css('height', div_css);
                                });
                            }
                        }
                    });
                }
            });
            
            $(document).on('click touchstart', '.minus_a', function(){
                var num_exist = $(this).parent().find('#number_rows').val();
                if(num_exist != ''){
                    if(parseInt(num_exist) > 1){
                        $(this).parent().find('#number_rows').val(parseInt(num_exist) - 1);
                    }else{
                        $(this).parent().find('#number_rows').val(1);
                    }
                }else{
                    $(this).parent().find('#number_rows').val(1);
                }
                
                if($(this).parent().parent().find('.custom_radio_class').find('input[type="radio"]').is(':checked')){
                    var col_id = ($(this).parent().parent().find('.custom_radio_class').find('input[type="radio"]').attr('data-col_id'));
                    var list_id = $(this).parent().parent().parent().attr('data-listid');
                    var height_col = $(this).parent().find('#number_rows').val();

                    $.ajax({
                        url: '<?php echo base_url() . 'listing/update_col_height'; ?>',
                        type: 'POST',
                        data: {
                            list_id: list_id,
                            col_id: col_id,
                            height_col: height_col
                        },
                        success: function (res) {
                            if(res == 'success'){
                                var col_indx = $('#test_table_' + list_id).find('.td_arrange_tr').find('th.heading_items_col').find('.add-data-title[data-colid="' + col_id + '"]').index();
                                var div_css = '';
                                if(height_col == 1){
                                    div_css = '50px';
                                } else if(height_col == 2){
                                    div_css = '80px';
                                } else if(height_col == 1){
                                    div_css = '100px';
                                }else{
                                    var height = 110 + (30 * (height_col - 3)) + 'px';
                                    div_css = height;
                                }
                                $('#test_table_' + list_id).find('tbody').find('tr').each(function(){
                                    $(this).find('.list-table-view:eq(' + (col_indx - 1) + ')').find('.add-data-div').css('padding', '11px 20px;');
                                });
                                $('#test_table_' + list_id).find('tbody').find('tr').each(function(){
                                    $(this).find('.list-table-view:eq(' + (col_indx - 1) + ')').find('.add-data-div').css('height', div_css);
                                });
                            }
                        }
                    });
                }
                
            });
            
            function isDate(txtDate){
                var currVal = txtDate;
                if(currVal == '')
                    return false;

                var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/; //Declare Regex
                var dtArray = currVal.match(rxDatePattern); // is format OK?
                if (dtArray == null) 
                    return false;

                //Checks for dd/mm/yyyy format.
                dtDay= dtArray[1];
                dtMonth = dtArray[3];
                dtYear = dtArray[5];        

                if (dtMonth < 1 || dtMonth > 12) 
                    return false;
                else if (dtDay < 1 || dtDay> 31) 
                    return false;
                else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31) 
                    return false;
                else if (dtMonth == 2) 
                {
                    var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
                    if (dtDay> 29 || (dtDay ==29 && !isleap)) 
                            return false;
                }
                return true;
            }
            
            
            $(document).on('change', '.check_all_check_box', function(){
                if($(this).is(':checked')){
                    $(this).parent().find('.task_sub_name').val('checked');
                }else{
                    $(this).parent().find('.task_sub_name').val('');
                }
            });
            
        </script>
        <?php
//        }
        ?>

    </body>
</html>
