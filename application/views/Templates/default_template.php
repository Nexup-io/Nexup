<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $title; ?></title>

        <link href="https://fonts.googleapis.com/css?family=Poppins:300,700|Roboto:300,400" rel="stylesheet">
        <link href="<?php echo base_url() . 'assets/css/bootstrap.min.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/bootstrap-datetimepicker.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/icomoon.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/bootstrap-select.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/font-awesome.min.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/jquery-ui.min.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/token-input.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/token-input-facebook.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/style.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/responsive.css'; ?>" rel="stylesheet" />
    </head>
    <body>
        <div class="wrapper">
            <header id="header" class="header">
                <div class="header-l">
                    <div class="logo"><a href="<?php echo base_url() . 'lists'; ?>"><img src="<?php echo base_url() . 'assets/img/logo-02.png'; ?>" alt="" /></a></div>

                </div>
                <div id="header-r-accordian">
                    <div></div>
                    <div class="header-r">
                        <div class="search">
                            <input type="text" name="search_list" id="search_list" placeholder="Search here" />
                            <button class="icon-search" type="submit"> </button>
                            <div class="loader_div">
                                <a class="loader_inline" id="loader_search" style="display:none;"><img src="<?php echo base_url() . 'assets/img/loader.gif' ?>"></a>
                            </div>
                        </div>
                        <div class="nav-view">
                            <a class="icon-nine-circles-button custom_cursor" href="<?php echo base_url() . 'lists'; ?>"> </a>
                        </div>
                        <?php if (isset($_SESSION['unauth_visit']) && !empty($_SESSION['unauth_visit']) || isset($_SESSION['auth_visit']) && !empty($_SESSION['auth_visit'])) { ?>
                            <div class="h-nav dropdown">
                                <a title="History" class="icon-history custom_cursor" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> </a>
                                <ul class="dropdown-menu" id="history_dd" aria-labelledby="dropdownMenu2">

                                    <?php
                                    $visited_lists_show = array();
                                    if (isset($_SESSION['auth_visit']) && $_SESSION['auth_visit'] != null) {
                                        $auth_ssn_ids = array_reverse($_SESSION['auth_visit']['list_id']);
                                        $auth_ssn_slugs = array_reverse($_SESSION['auth_visit']['list_slug']);
                                        $auth_ssn_names = array_reverse($_SESSION['auth_visit']['list_name']);
                                        foreach ($auth_ssn_ids as $key => $val):
                                            echo "<li class='history_options' value='" . base_url() . "item/" . $auth_ssn_slugs[$key] . "' data-slug='" . $auth_ssn_slugs[$key] . "'><a href='" . base_url() . "item/" . $auth_ssn_slugs[$key] . "'>" . $auth_ssn_names[$key] . "</a></li>";
                                        endforeach;
                                    }else {
                                        $unauth_ssn_ids = array_reverse($_SESSION['unauth_visit']['list_id']);
                                        $unauth_ssn_slugs = array_reverse($_SESSION['unauth_visit']['list_slug']);
                                        $unauth_ssn_names = array_reverse($_SESSION['unauth_visit']['list_name']);
                                        foreach ($unauth_ssn_ids as $key => $val):
                                            echo "<li class='history_options' value='" . base_url() . "item/" . $unauth_ssn_slugs[$key] . "'><a href='" . base_url() . "item/" . $unauth_ssn_slugs[$key] . "'>" . $unauth_ssn_names[$key] . "</a></li>";
                                        endforeach;
                                    }
                                    ?>
                                </ul>
                            </div>
                            <?php
                        }
                        ?>
                        <?php if ($this->session->userdata('logged_in')) { ?>
                            <div class="h-nav dropdown">
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
                                    <li><a href="<?php echo base_url() . 'profile'; ?>">Profile</a></li>
                                    <li><a href="<?php echo base_url() . 'logout'; ?>">Logout</a></li>
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
        <input type="hidden" id="hndautoid" />
        <input type="hidden" id="hdnuserid"/>
        <input type="hidden" id="hdnaccesstoken"/>
        <footer id="footer">
            <p>Â© <?php echo date('Y'); ?> Copyright - All Right Reserved.</p>
        </footer>


        <!--        <div class="modal fade login-modal" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="sharemodal-head">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon-cancel"></span></button>
                                <h2>Nexup <span>Login:</span></h2>
                            </div>
                            <div class="sharemodal-body">
                                <form id="LoginForm" method="POST" action="<?php echo base_url() . 'login'; ?>">
                                    <div class="input-outer">
                                        <label>Email</label>
                                        <input type="text" name="user_name" id="user_name" placeholder="johndoe@yourmail.com" />
                                    </div>
                                    <div class="input-outer">
                                        <label>Password</label>
                                        <input type="password" name="password" id="password" placeholder="Password" />
                                    </div>
        
                                    <div class="button-outer">
                                        <button type="submit">Login</button>
                                    </div>
                                    <div class="social-login">
                                        <ul>
                                            <li><a class="inflologinlink btn btn-inflo"><img class="inflo-icon" src="<?php echo base_url() . 'assets/img/inflo-alpha.png'; ?>" alt=""> Login with inflo</a></li>
                                            <li><a class="btn btn-primary btn-fb" href="<?php echo base_url() . 'login/facebook'; ?>"><i class="fa fa-facebook-square" aria-hidden="true"></i> Login with Facebook</a></li>
                                            <li><a class="btn btn-info btn-tw" href=""><i class="fa fa-twitter-square" aria-hidden="true"></i> Login with Twitter</a></li>
                                            <li><a class="btn btn-danger btn-gplus" href=""><i class="fa fa-google-plus-square" aria-hidden="true"></i> Login with Google Plus</a></li>
                                            <input type="hidden" id="hndautoid" />
                                            <input type="hidden" id="hdnuserid"/>
                                            <input type="hidden" id="hdnaccesstoken"/>
                                        </ul>
                                    </div>
                                </form>
                            </div>	
                        </div>
                    </div>
                </div>-->


        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery.min.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/bootstrap.min.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/moment-with-locales.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/responsive-tabs.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/bootstrap-datetimepicker.min.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/bootstrap-select.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/validate.min.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-ui.min.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-ui-touch-punch.min.js'; ?>"></script>
        <!--<script type="text/javascript" src="https://developer.inflo.io/Scripts/InfloAPIScript.js"></script>-->
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/InfloAPIScript.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery.tokeninput.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/clipboard.min.js'; ?>"></script>


        <script>
            $(document).ready(function () {
                $('[data-toggle="tooltip"]').tooltip();
                if ($('.icon-settings').attr('data-locked') == 1) {
                    $('.delete_task').hide();
                    $("#TaskList").sortable("disable");
                }
                var screen_size = $(window).width();
                if (screen_size < 521) {
                    $("#header-r-accordian").accordion({
                        collapsible: true
                    });
                }
            });
            $(document).on('click', 'ul.nav.nav-tabs  a', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });
            (function ($) {
                fakewaffle.responsiveTabs(['xs', 'sm']);
            })(jQuery);

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
            $(document).on('click', '.list-body-dropdown > a', function () {
                if ($(this).closest('.list-body-box').hasClass('action-show')) {
                    $(this).closest('.list-body-box').removeClass('action-show');
                } else {
                    $('.list-body-box').removeClass('action-show');
                    $(this).closest('.list-body-box').addClass('action-show');
                }
            });

            //Display icon for options when mouse is over list
            $(document).on('mouseover', '.list-body-box', function () {
                $(this).find("a.icon-more").css('display', 'block');
            });

            //Hide icon for options when mouse out of list
            $(document).on('mouseout', '.list-body-box', function () {
                $(this).find("a.icon-more").css('display', 'none');
            });
        </script>

        <!-- Autocomplete javascript for search box -->
        <script>

            //Search list auto complere
            jQuery("#search_list").autocomplete({
                source: function (request, response) {
                    var list_name = $("#search_list").val();
                    $('#loader_search').show();
                    $.ajax({
                        url: '<?php echo base_url() . 'searchlist' ?>',
                        type: 'POST',
                        data: {list_name: list_name},
                        success: function (res) {
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
                        window.location.href = '<?php echo base_url() . 'lists/'; ?>' + ui.item.value;
                    }
                }
            });

            //Re-order tasks
            $(".tasks_lists_display").sortable({
                handle: '.icon-more',
                update: function (event, ui) {
                    var task_id = $(ui.item).attr('data-id');
                    $.ajax({
                        url: '<?php echo base_url() . 'item_order' ?>',
                        type: 'POST',
                        data: {
                            OrderId: ui.item.index() + 1,
                            Taskid: task_id
                        },
                        success: function (res) {
                            if (res == 'success') {
                                $('span#next_task_name').text($('#TaskList li:first-child').text());
                                $('.whoisnext-div .button-outer').attr('title', $('#TaskList li:first-child').text());
                            }
                        }
                    });
                }
            });
            $("#TaskList").disableSelection();


            //Autocomplete for share list
            $("#share_email").tokenInput('<?php echo base_url() . 'sharelist' ?>', {theme: "facebook", preventDuplicates: true});


            //Save configuration for list
            $(document).on('click', '#save_config', function () {
                if ($('#config_lnk').attr('data-locked') == 1) {
                    alert('This list is locked. You can not change configuration!');
                    $('#config-list').modal('hide');
                    return false;
                }
                var list_id = $(this).attr('data-listid');
                var allow_move = 'False';
                if ($('#move_item').is(':checked')) {
                    allow_move = 'True';
                }
                var show_completed = 'False';
                if ($('#show_completed_item').is(':checked')) {
                    show_completed = 'True';
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
                        if (res == 'success') {
                            $('#config_msg').html('Configurations updated successfully.');
                            $('#config_msg').removeClass('alert-danger');
                            $('#config_msg').addClass('alert-success');
                            $('#config_msg').show();
                            if (allow_move == 'False') {
//                                $('#TaskList').removeClass('tasks_lists_display');
                                $("#TaskList").sortable("disable");
                            } else {
                                if ($('#TaskList').hasClass('tasks_lists_display')) {
                                    $("#TaskList").sortable("enable");
                                } else {
                                    $("#TaskList").addClass('tasks_lists_display');
                                    $(".tasks_lists_display").sortable({
                                        handle: '.icon-more',
                                        update: function (event, ui) {
                                            var task_id = $(ui.item).attr('data-id');
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
                                    });
                                }
                            }
                        } else if (res == 'not allowed') {
                            $('#config_msg').html('You have not created list. Please create list to proceed!');
                            $('#config_msg').addClass('alert-danger');
                            $('#config_msg').removeClass('alert-success');
                            $('#config_msg').show();
                        } else {
                            $('#config_msg').html('Something went wrong. Configuration was not updated. Please try again!');
                            $('#config_msg').addClass('alert-danger');
                            $('#config_msg').removeClass('alert-success');
                            $('#config_msg').show();
                        }
                        $('#config_msg').delay(5000).fadeOut('fast');
                    }
                });

            });

            var isTouchDevice = 'ontouchstart' in document.documentElement;
            if (isTouchDevice) {
//                if ($('#TaskList li.task_li').length > 0) {
//                    $('.enable-move').removeClass('hide_move_btn');
//                } else {
//                    $('.enable-move').removeClass('hide_move_btn');
//                    $('.enable-move').addClass('hide_move_btn');
//                }
            } else {
//                $('.enable-move').hide();
                $(document).on('mouseover', '.task_li', function () {
                    var did = $(this).attr('data-id');
                    $('#task_' + did + ' .icon-more').css({'visibility': 'visible'});
                });

                //Hide re-order handle when mouse out of task
                $(document).on('mouseout', '.task_li', function () {
                    var did = $(this).attr('data-id');
                    $('#task_' + did + ' .icon-more').css({'visibility': 'hidden'});
                });
            }


//            if (window.screen.width > 767) {
//                //Show re-order handle when mouse is over task
//                $(document).on('mouseover', '.task_li', function () {
//                    var did = $(this).attr('data-id');
//                    $('#task_' + did + ' .icon-more').css({'visibility': 'visible'});
//                });
//
//                //Hide re-order handle when mouse out of task
//                $(document).on('mouseout', '.task_li', function () {
//                    var did = $(this).attr('data-id');
//                    $('#task_' + did + ' .icon-more').css({'visibility': 'hidden'});
//                });
//            }


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
            });</script>

        <script>

            //Hide success/error message after 5 seconds of display
            setTimeout(function () {
                $('.alert-danger').fadeOut('fast');
                $('.alert-success').fadeOut('fast');
            }, 5000);
            $(document).on('click', '#add_list_top_btn', function () {
                $('.list-body-plus a.icon-add').trigger('click');
            });
//            $(document).on('click', '.list-body-plus a.icon-add', function () {
//                $('.add-list-class').show();
//                $('.add-list-class').focus();
//                $('#edit_list_name').remove();
//                $('.edit_list_cls').remove();
//                $('.dropdown-action').show();
//                $('.list-body-box .list-body-box-link big').show();
//                $('.list-body-box .list-body-box-link big').show();
//                $('.list-body-li div').removeClass('action-show');
//            });

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
                                $("#add_item_li").before(res);
                                $('#list_name').val('');
                                $('.add_list_cls').text('');
                                $('.add_list_cls').hide();
                                $('#list_name').removeClass('list-error');
                            }
                        }
                    });
                }
            });

            //Display Edit list text box on list page
            $(document).on('click', '.edit_list', function () {

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
                        } else if (res == 'not allowed') {
                            alert('You are not allowed to edit this list!');
                        } else {
                            $('#list_name').val('');
                            $('#list_name').hide();
                            var txtbx = '<input type="text" name="edit_list_name" id="edit_list_name" class="edit-list-class" data-id="' + did + '" value="' + res + '" placeholder="I want to..." /><div class="edit_list_cls" style="display: none;"></div>';
                            $('#list_' + did + ' .list-body-box .list-body-dropdown').before(txtbx);
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
            $(document).on('click', '.edit_list_task', function () {
                if ($('#config_lnk').attr('data-locked') == 1) {
                    alert('This list is locked. You can not edit it!');
                    return false;
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
                        } else if (res == 'not allowed') {
                            alert('You are not allowed to edit this list!');
                        } else {
                            var txtbx = '<input type="text" name="edit_list_name" id="edit_list_name" class="edit-list-class" data-id="' + did + '" value="' + res + '" placeholder="I want to..." /><div class="edit_list_cls" style="display: none;"></div>';
                            $('.add-data-head #listname_' + did).hide();
                            $('.add-data-head #listname_' + did).before(txtbx);
                            var el = $("#edit_list_name").get(0);
                            var elemLen = el.value.length;
                            el.selectionStart = elemLen;
                            el.selectionEnd = elemLen;
                            $('#edit_list_name').focus();
                            $('#listname_' + did).hide();
                            $('#edit_list_task_page').hide();
                        }
                    }
                });
            });

            $(document).on('focus', '#edit_list_name', function () {
                $('#edit_list_name').select();
            });
            $(document).on('focus', '#edit_task_name', function () {
                $('#edit_task_name').select();
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
                    var operation = '';
                    var data_send = {};
                    if (did_edit > 0) {
                        operation = 'edit';
                        data_send.list_id = did_edit;
                        data_send.edit_list_name = list_txt;
                        call_url = "<?php echo base_url() . 'listing/update'; ?>"
                    } else {
                        operation = 'add';
                        data_send.list_name = list_txt;
                        var call_url = "<?php echo base_url() . 'listing/add'; ?>";
                    }
                    $.ajax({
                        url: call_url,
                        type: 'POST',
                        data: data_send,
                        success: function (res) {
                            if (res == 'existing') {
                                $('.edit_list_cls').text('This list already exist. Please try different name!');
                                $('.edit_list_cls').show();
                                $('#edit_list_name').removeClass('list-error');
                                $('#edit_list_name').addClass('list-error');
                            } else if (res == 'fail') {
                                if (operation == 'add') {
                                    $('#edit_list_name').val('');
                                }
                                $('.edit_list_cls').text('Something went wrong. Your list was not updated. Please try again later!');
                                $('.edit_list_cls').show();
                                $('#edit_list_name').removeClass('list-error');
                                $('#edit_list_name').addClass('list-error');
                            } else if (res == 'empty') {
                                $('.edit_list_cls').text('Please enter list name!');
                                $('.edit_list_cls').show();
                                $('#edit_list_name').removeClass('list-error');
                                $('#edit_list_name').addClass('list-error');
                            } else {
                                var resp = JSON.parse(res);
                                if (operation == 'add') {
                                    if ($("#history_dd").length == 0) {
                                        var ddl_history = '<div class="h-nav dropdown">';
                                        ddl_history += '<a title="History" class="icon-history custom_cursor" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> </a>';
                                        ddl_history += '<ul class="dropdown-menu" id="history_dd" aria-labelledby="dropdownMenu2">';
                                        ddl_history += '<li class="history_options" value="<?php echo base_url() . 'item/' ?>' + resp[1] + '" data-slug="' + resp[1] + '"><a href="<?php echo base_url() . 'item/' ?>' + resp[1] + '">' + list_txt + '</a></li>';
                                        ddl_history += '</ul>';
                                        ddl_history += '</div>';
//                                        alert(ddl_history);
//                                        $('.history_div').html(ddl_history);
                                        $('.nav-view').after(ddl_history);
                                    } else {
                                        var ddl_history_option = '<li class="history_options" value="<?php echo base_url() . 'item/' ?>' + resp[1] + '" data-slug="' + resp[1] + '"><a href="<?php echo base_url() . 'item/' ?>' + resp[1] + '">' + list_txt + '</a></li>';
                                        $('#history_dd').prepend(ddl_history_option);
                                        $('#history_dd').val('<?php echo base_url() . 'item/' ?>' + resp[1]);
                                    }
                                    $('#save_config').attr('ddata-listid', resp[0])
                                    $('.edit_list_task').attr('data-id', resp[0])
                                    $('.edit_list_task').attr('data-slug', resp[1]);
                                } else if (operation == 'edit') {
                                    $('#history_dd li[value="<?php echo base_url() . 'item/' ?>' + resp[1] + '"] a').text(list_txt);
                                    $.ajax({
                                        url: "<?php echo base_url() . 'listing/edit'; ?>",
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
                                $('.edit_list_cls').remove();
                                $('#edit_list_name').removeClass('list-error');
                                $('.listname_' + did_edit).text(list_txt);
                                $('.listname_' + did_edit).show();
                                $('#list_' + did_edit + ' .list-body-box .dropdown-action').show();
                                $('#list_' + did_edit + ' .list-body-box .list-body-box-link').removeAttr('style');
                                $('#edit_list_task_page').show();

                                $('.sharemodal-head h2 span').html(list_txt);
                                $('#edit_list_task_page').attr('data-id', resp[0]);
                                $('.list_type_cls').attr('data-listid', resp[0]);
                                if (resp[1] != '') {
                                    $('#edit_list_task_page').attr('data-slug', resp[1]);
                                    $('#share_list').show();
                                    $('#import_contacts').attr('href', '<?php echo base_url() . 'item/' ?>' + resp[1]);
                                    $('#import_contacts').text('<?php echo base_url() . 'item/' ?>' + resp[1]);
                                }
                                $('#task_name').attr('data-listid', resp[0]);
                                $('#add_task_li #name').attr('data-listid', resp[0]);
                                $('.add-data-head h2').attr('id', 'listname_' + resp[0]);
                                $('.add-data-head h2').removeClass('listname_0');
                                $('.add-data-head h2').addClass('listname_' + resp[0]);
                            }
                        }
                    });
                }
            });

            //Delete list
            $(document).on('click', '.delete_list', function () {
                var cnf = confirm('Are you sure want to delete this list?');
                if (cnf) {
                    var did_del = $(this).attr('data-id');
                    $.ajax({
                        url: "listing/remove",
                        type: 'POST',
                        data: {
                            'list_id': did_del,
                        },
                        success: function (res) {
                            if (res == 'success') {
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

                            } else if (res == 'fail') {
                                alert('Something went wrong. List was not deleted. Please try again!')
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
                if ($('#config_icons').hasClass('hide_data'))
                {
                    $('#config_icons').fadeIn(1000);
                    $('#config_icons').removeClass('hide_data');
                }
                else
                {
                    $('#config_icons').fadeOut(1000);
                    $('#config_icons').addClass('hide_data');
                }
            });

            //Display configuration modal
            $(document).on('click', '#listConfig_lnk', function () {
                $('#config-list').modal('show');
            });

            //Display list types modal
//            $(document).on('click', '#listTypes_lnk', function () {
//                $('#listTypesModal').modal('show');
//            });

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



//            $(document).on('click', '#add_task_lnk', function () {
//                $('#task_name').focus();
//            });
            //Add task for a list
            $(document).on('keydown', '#task_name', function (evt) {
                var key_code = evt.keyCode;
                if (key_code == 13) {
                    if ($('.icon-settings').attr('data-locked') == 1) {
                        alert('This list is locked. You can not add any item in it!');
                        return false;
                    }
                    var list_id = $('#task_name').attr('data-listid');
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
                    $.ajax({
                        url: "<?php echo base_url() . 'item/add'; ?>",
                        type: 'POST',
                        data: {
                            'list_id': list_id,
                            'task_name': $(this).val(),
                            'list_name': list_name
                        },
                        success: function (res) {
                            if (res == 'existing') {
                                $('.add_task_cls').text('This task already exist. Please try different name!');
                                $('.add_task_cls').show();
                                $('#add_task_li').removeClass('list-error');
                                $('#add_task_li').addClass('list-error');
                            } else if (res == 'fail') {
                                $('#task_name').val('');
                                $('.add_task_cls').text('Something went wrong. Your task was not added. Please try again later!');
                                $('.add_task_cls').show();
                                $('#add_task_li').removeClass('list-error');
                                $('#add_task_li').addClass('list-error');
                            } else if (res == 'empty') {
                                $('.add_task_cls').text('Please enter task name!');
                                $('.add_task_cls').show();
                                $('#add_task_li').removeClass('list-error');
                                $('#add_task_li').addClass('list-error');
                            } else {
                                var resp = JSON.parse(res);
                                $('#add_task_li #task_name').attr('data-listid', resp[0]);
                                $('.add-data-head #edit_list_name').attr('data-id', resp[0]);
                                $('.add-data-head .edit_list_task').attr('data-id', resp[0]);
                                $('#update_listType').attr('data-id', resp[0]);
                                $('#edit_list_task_page').attr('data-id', resp[0]);
                                $('.list_type_cls').attr('data-listid', resp[0]);
                                if (resp[2] != '') {
                                    $('#edit_list_task_page').attr('data-slug', resp[2]);
                                    $('.add-data-head .edit_list_task').attr('data-slug', resp[2]);
                                    $('.add-data-head h2').attr('id', 'listname_' + resp[0]);
                                    $('.add-data-head h2').removeClass('listname_0');
                                    $('.add-data-head h2').addClass('listname_' + resp[0]);
                                    $('#import_contacts').attr('href', '<?php echo base_url() . 'item/' ?>' + resp[2]);
                                    $('#import_contacts').text('<?php echo base_url() . 'item/' ?>' + resp[2]);
                                }
                                if (resp[3] == 0) {
                                    $('#listname_0').removeClass('listname_0');
                                    $('#listname_0').addClass('listname_' + resp[0]);
                                    $('#listname_0').attr('id', 'listname_' + resp[0]);
                                    $('#listname_' + resp[0]).html(list_name);
                                    $('#edit_list_name').remove();
                                    $('#listname_' + resp[0]).show();
                                    $('#edit_list_task_page').show();
                                    $('#share_list').show();
                                    $('#save_config').attr('data-listid', resp[0]);

                                    if ($("#history_dd").length == 0) {
                                        var ddl_history = '<div class="h-nav dropdown">';
                                        ddl_history += '<a title="History" class="icon-history custom_cursor" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> </a>';
                                        ddl_history += '<ul class="dropdown-menu" id="history_dd" aria-labelledby="dropdownMenu2">';
                                        ddl_history += '<li class="history_options" value="<?php echo base_url() . 'item/' ?>' + resp[2] + '" data-slug="' + resp[2] + '"><a href="<?php echo base_url() . 'item/' ?>' + resp[2] + '">' + list_name + '</a></li>';
                                        ddl_history += '</ul>';
                                        ddl_history += '</div>';
                                        $('#header .header-r').append(ddl_history);
                                    } else {
                                        var ddl_history_option = '<li class="history_options" value="<?php echo base_url() . 'item/' ?>' + resp[2] + '" data-slug="' + resp[2] + '"><a href="<?php echo base_url() . 'item/' ?>' + resp[2] + '">' + list_name + '</a></li>';
                                        $('#history_dd').prepend(ddl_history_option);
//                                        $('#history_dd').val('<?php echo base_url() . 'item/' ?>' + resp[1]);
                                    }
                                    $('#save_config').attr('ddata-listid', resp[0])


                                    $("#TaskList").addClass('tasks_lists_display');
                                    $(".tasks_lists_display").sortable({
                                        handle: '.icon-more',
                                        update: function (event, ui) {
                                            var task_id = $(ui.item).attr('data-id');
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
                                    });
                                    $("#TaskList").disableSelection();

                                }
                                $('#task_name').val('');
                                $('.add_task_cls').text('');
                                $('.add_task_cls').hide();
                                $('#add_task_li').removeClass('list-error');
                                $('#TaskList').append(resp[1]);
                                if ($('#TaskList li').length <= 1) {
                                    $('.whoisnext-div .whoisnext-btn').removeClass('whosnext_img_bg');
//                                    $('.whoisnext-div .whoisnext-btn').removeClass('whoisnext-btn_light_bg');
//                                    $('.whoisnext-div .whoisnext-btn').addClass('whoisnext-btn_light_bg');
                                    $('span#next_task_name').text($('#TaskList li:first-child').text());
                                    $('.whoisnext-div .button-outer').attr('title', $('#TaskList li:first-child').text());
                                }
                                $('.whoisnext-div .button-outer').removeClass('whosnext_img_bg');
                            }
                            $('.icon-lock2').removeClass('lock_hide');
                            $('.icon-lock-open2').removeClass('lock_hide');
                        }
                    });
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


            //Hide options from list/tasks when mouse clicked anywhere on page

            $('body').on('click', function (e) {
                if ($(e.target).attr('id') == 'edit_task_name' || $(e.target).attr('id') == 'edit_list_name') {
                    e.preventDefault();
                } else {
                    $('.edit_list_cls').remove();
                    $('.list-body-box').removeClass('action-show');
                    $('#edit_task_name').remove();
                    $('.edit_task_cls').remove();
                    if ($('#edit_list_name').attr('data-id') > 0) {
                        $('#edit_list_name').remove();
                        $('h2.edit_list_task').show();
                    }
                    $('.list-body-box a big').show();
                    $('.dropdown-action').show();
                    $('.add-data-div').removeClass('list-error');
                    $('.opertaions').removeClass('hide_operations');
                    $('.task_name_span').show();
                }

            });

            //Open modal of edit task and fill value in text box
            $(document).on('click', '.task_name_span', function () {
                if ($('.icon-settings').attr('data-locked') == 1) {
                    alert('This list is locked. Please unlock it to perform any operation!');
                    return false;
                }
                $('#edit_task_name').remove();
                $('#TaskList .task_li .add-data-div span').show();
                $('.opertaions').removeClass('hide_operations');
                var task_nm = $(this).parent().closest('div').attr('data-task');
                var task_id = $(this).parent().closest('div').attr('data-id');
                var list_id = $(this).parent().closest('div').attr('data-listid');
                var did = $(this).attr('id');
                $.ajax({
                    url: "<?php echo base_url() . 'item/get_task_data'; ?>",
                    type: 'POST',
                    data: {
                        'task_id': task_id
                    },
                    success: function (res) {
                        if (res == 'not found') {
                            alert('Task you are looking for does not exist!');
                        } else if (res == 'not allowed') {
                            alert('You are not allowed to edit this task!');
                        } else {
                            var txt_edit_bx = '<input id="edit_task_name" name="edit_task_name" value="' + res + '" data-id="' + task_id + '" data-listid="' + list_id + '" placeholder="I want to..." type="text">';
                            var err_msg = '<div class="edit_task_cls" style=""></div>';
                            $('#span_task_' + task_id).before(txt_edit_bx);
                            $('#task_' + task_id + ' .add-data-div').after(err_msg);
                            $('#span_task_' + task_id).hide();
                            var el = $("#edit_task_name").get(0);
                            var elemLen = el.value.length;
                            el.selectionStart = elemLen;
                            el.selectionEnd = elemLen;
                            $('#edit_task_name').focus();
                            $('#task_' + task_id + ' .opertaions').addClass('hide_operations');
                        }
                    }
                });
                return false;
            });

            //Update task name
            $(document).on('keydown', '#edit_task_name', function (evt) {
                var key_code = evt.keyCode;
                var did_edit = $(this).attr('data-id');
                var edit_task_name = $('#edit_task_name').val();
                var list_id = $(this).attr('data-listid');
                if (key_code == 13) {
                    $('.edit_task_cls').text('');
                    $('.edit_task_cls').hide();
                    $('#task_' + did_edit + ' .add-data-div').removeClass('list-error');
                    $.ajax({
                        url: "<?php echo base_url() . 'item/update'; ?>",
                        type: 'POST',
                        data: {
                            'ListId': list_id,
                            'TaskId': did_edit,
                            'Taskname': edit_task_name
                        },
                        success: function (res) {
                            if (res != 'success') {
                                $('.edit_task_cls').text('Something went wrong. Please try again!');
                                $('.edit_task_cls').show();
                            } else {
                                $('#span_task_' + did_edit).text(edit_task_name);
                                $('#span_task_' + did_edit).show();
                                $('#edit_task_name').remove();
                                $('.edit_task_cls').remove();
                                $('#task_' + did_edit + ' .add-data-div').removeClass('list-error');
                                $('#task_' + did_edit + ' .add-data-div .opertaions').removeClass('hide_operations');
                                $.ajax({
                                    url: "<?php echo base_url() . 'item/edit'; ?>",
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
                } else if (key_code == 27) {
                    $('#edit_task_name').remove();
                    $('.edit_task_cls').remove();
                    $('#task_' + did_edit + ' .add-data-div').removeClass('list-error');
                    $('#task_' + did_edit + ' .add-data-div .opertaions').removeClass('hide_operations');
                    $('#span_task_' + did_edit).show();
                }

            });

//            $(document).on('click', '#edit_task_name', function (e) {
//                console.log(e);
//                if (e.target.name === $(this).attr('name')){
//                    return false;
//                }
//                $('.list-body-box').removeClass('action-show');
//                $('#edit_task_name').remove();
//                $('.edit_task_cls').remove();
//                $('.add-data-div').removeClass('list-error');
//                $('.opertaions').removeClass('hide_operations');
//                $('.task_name_span').show();
//            });

            //Hide edit list text boxes when user takes control to add task text box
            $(document).on('focus', '#task_name', function () {
                $('#edit_task_name').remove();
                $('.edit_task_cls').remove();
                $('.add-data-div span').show();
                $('.task_li .add-data-div div').removeClass('hide_operations');
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
                    url: "<?php echo base_url() . 'item/edit'; ?>",
                    type: 'POST',
                    data: {
                        'ListId': list_id,
                        'TaskId': did_edit,
                        'Taskname': edit_task_name
                    },
                    success: function (res) {
                        if (res == 'success') {
                            $('#span_task_' + did_edit).text(edit_task_name);
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

            //Delete task ajax call
            $(document).on('click', '.delete_task', function () {
                var cnfrm = confirm('Are you sure want to delete this item?');
                if (cnfrm) {
                    var task_id = $(this).attr('data-id');
                    var ListId = $(this).attr('data-listid');
                    $.ajax({
                        url: "<?php echo base_url() . 'item/remove'; ?>",
                        type: 'POST',
                        data: {
                            'TaskId': task_id,
                            'ListId': ListId
                        },
                        success: function (res) {

                            if (res == 'success') {
                                $('#task_' + task_id).remove();
                                if ($('li.task_li').length == 0) {
                                    $('.whoisnext-div .button-outer').addClass('whosnext_img_bg');
                                }
                                $.ajax({
                                    url: "<?php echo base_url() . 'item/delete'; ?>",
                                    type: 'POST',
                                    data: {
                                        'TaskId': task_id,
                                        'ListId': ListId
                                    },
                                    success: function (res) {
                                    }
                                });
                            } else {
                                alert('Something went wrong. Please try again!');
                            }
                            if ($('#TaskList li').length <= 0) {
                                $('.whoisnext-div .whoisnext-btn').addClass('whosnext_img_bg');
//                                $('.whoisnext-div .whoisnext-btn').removeClass('whoisnext-btn_light_bg');
//                                $('.whoisnext-div .whoisnext-btn').removeClass('whoisnext-btn_light_bg');
                                $('span#next_task_name').text('');
                                $('span#next_task_name').attr('title', '');
                            } else {
                                $('span#next_task_name').text($('#TaskList li:first-child').text());
                                $('.whoisnext-div .button-outer').attr('title', $('#TaskList li:first-child').text());
                            }

                        }
                    });
                }
            });

            //Complete Task
            $(document).on('click', '.complete_task', function () {
                var cnfrm = confirm('Are you sure want to complete this task?');
                if (cnfrm) {
                    var did = $(this).attr('data-id');
                    $.ajax({
                        url: "<?php echo base_url() . 'item/complete'; ?>",
                        type: 'POST',
                        data: {
                            'TaskId': did
                        },
                        success: function (res) {
                            if (res == 'success') {
                                $('#task_' + did + ' .add-data-div').addClass('completed_task');
                                $('#task_' + did + ' .add-data-div .opertaions').remove();
                            } else if (res == 'fail') {
                                alert('Task you are looking for dis not found!');
                            } else {
                                alert('You are not allowed to delete this task!');
                            }
                        }
                    });
                }
            });

            //Visit list from history drop down
            $(document).on('change', '#history_dd', function () {
                var url_val = this.value;
                if (url_val != 0) {
                    location = this.value;
                }
            });


            //Update list type ajax
            $(document).on('click', '.list_type_cls', function (e) {
                if ($('#config_lnk').attr('data-locked') == 1) {
                    alert('This list is locked. You can not change list type!');
                    return false;
                }
                var list_id = $(this).attr('data-listid');
                var type_id = $(this).attr('data-typeid');

                if ($('.whoisnext-btn').length > 0 && type_id == 2) {

                } else {
                    $.ajax({
                        url: '<?php echo base_url() . 'change_listType'; ?>',
                        type: 'POST',
                        data: {
                            'list_id': list_id,
                            'type_id': type_id
                        },
                        success: function (res) {
                            if (res == 'success') {
                                if (type_id != 2) {
                                    $('.whoisnext-div').remove();
                                } else {
                                    var task_name = '';
                                    if ($('#TaskList .task_li').length > 0) {
                                        task_name = $('#TaskList li:first-child').text();
                                    }
                                    if ($('.whoisnext-div').length == 0) {
                                        if (task_name == '') {
                                            $('#content').prepend('<div class="whoisnext-div"><div class="button-outer whosnext_img_bg"><span id="next_task_name">' + task_name + '</span></div><a class="whoisnext-btn custom_cursor"><span id="nexup_icon" class="icon-redo2"> </span> Nexup</a></div>');
                                        } else {
                                            $('#content').prepend('<div class="whoisnext-div"><div class="button-outer"><span id="next_task_name">' + task_name + '</span></div><a class="whoisnext-btn custom_cursor"><span id="nexup_icon" class="icon-redo2"> </span> Nexup</a></div>');
                                        }
                                    }
                                }
                                $('#ListType_msg').html('You have successfully changed list type.');
                                $('#ListType_msg').removeClass('alert-danger');
                                $('#ListType_msg').addClass('alert-success');
                                $('#ListType_msg').show();
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
                            } else if (res == 'not allowed') {
                                $('#ListType_msg').html('Please create/select a list to proceed!');
                                $('#ListType_msg').removeClass('alert-success');
                                $('#ListType_msg').addClass('alert-danger');
                                $('#ListType_msg').show();
                            } else {
                                $('#ListType_msg').html('Something went wrong. List type was not changed. Please try again!');
                                $('#ListType_msg').removeClass('alert-success');
                                $('#ListType_msg').addClass('alert-danger');
                                $('#ListType_msg').show();
                            }
                            $('.alert-danger').delay(5000).fadeOut('fast');
                            $('.alert-success').delay(5000).fadeOut('fast');
                        }
                    });
                }
            });

            $(document).on('click', '.whoisnext-btn-cmnt', function () {
            if($('#nexup_cmnt_span').hasClass('hide_box')){
                    $('#nexup_cmnt_span').removeClass('hide_box');
                }else{
                    $('#nexup_cmnt_span').addClass('hide_box');
                }
                $('#nexup_comment').focus();
                return false;
            });

            $(document).on('keydown', '#nexup_comment', function (evt) {
                var key_code = evt.keyCode;
                if (key_code == 27) {
                    $('#nexup_comment').val('');
                    $('#nexup_cmnt_span').addClass('hide_box');
                } else if (key_code == 13) {
                    if ($('.icon-settings').attr('data-locked') == 1) {
                        alert('This list is locked. Please unlock it to perform any operation!');
                        return false;
                    }
                    if ($('#TaskList li').length < 2) {
                        return false;
                    }
                    var last_task_id = $('#TaskList li:first-child').attr('data-id');
                    var list_id = <?php if (isset($list_id)) {echo $list_id;} else {echo 0;} ?>;
                    var comment = $('#nexup_comment').val();
                    $.ajax({
                        url: '<?php echo base_url() . 'next_item'; ?>',
                        type: 'POST',
                        data: {
                            'Listid': list_id,
                            'Taskid': last_task_id,
                            'comment': comment
                        },
                        success: function (res) {
                            if (res == 'success') {
                                $('#nexup_comment').val('');
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
                }
            });

            //Who's next call
            $(document).on('click', '.whoisnext-btn, .whoisnext-div .button-outer', function () {
                if ($('.icon-settings').attr('data-locked') == 1) {
                    alert('This list is locked. Please unlock it to perform any operation!');
                    return false;
                }
                if ($('#TaskList li').length < 2) {
                    return false;
                }
                var last_task_id = $('#TaskList li:first-child').attr('data-id');
                var list_id = <?php
            if (isset($list_id)) {
                echo $list_id;
            } else {
                echo 0;
            }
            ?>;
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
                    success: function (res) {
                        if (res == 'fail') {
                            alert('Something went wroong. Your list was not locked. Please try again!')
                        } else if (res == 'not allowed') {
                            alert('You are not allowed to lock this list.');
                        } else if (res == 'success') {
                            $("#TaskList").sortable("disable");
                            $('.icon-settings').attr('data-locked', '1');
                            if ($('.hide_add_item').length == 0) {
                                $('#add_task_li .add-data-div').addClass('hide_add_item');
                            }
                            $('.delete_task').hide();
                            $('#listLock_lnk').remove();
                            $('#listUnlock_lnk').remove();
                            $('.config_icons').append('<a class="icon-lock-open2 custom_cursor" id="listUnlock_lnk" data-id="' + list_id + '" data-slug="' + list_slug + '"></a>');
                            $('#TaskListDiv').accordion({
                                collapsible: true,
                                active: false
                            });
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
                var list_id = $(this).attr('data-id');
                var list_slug = $(this).attr('data-slug');
                $.ajax({
                    url: '<?php echo base_url() . 'lock_nexup_list'; ?>',
                    type: 'POST',
                    data: {
                        'Listid': list_id,
                        'Lock': 0,
                    },
                    success: function (res) {
                        if (res == 'fail') {
                            alert('Something went wroong. Your list was not unlocked. Please try again!')
                        } else if (res == 'not allowed') {
                            alert('You are not allowed to unlock this list.');
                        } else if (res == 'success') {
                            $("#TaskList").sortable("enable");
                            $('.icon-settings').attr('data-locked', '0');
                            $('#add_task_li .add-data-div').removeClass('hide_add_item');
                            $('.delete_task').show();
                            $('#listLock_lnk').remove();
                            $('#listUnlock_lnk').remove();
                            $('.config_icons').append('<a class="icon-lock2 custom_cursor" id="listLock_lnk" data-id="' + list_id + '" data-slug="' + list_slug + '"></a>');
                            $('#TaskListDiv').accordion("destroy");
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
            });

            $(document).on('click', '.enable-move', function () {
                if ($('#TaskList').hasClass('display_sort_handle')) {
                    $('#TaskList').removeClass('display_sort_handle');
                    $('.ui-sortable-handle').css('visibility', 'hidden');
                } else {
                    $('#TaskList').addClass('display_sort_handle');
                    $('.ui-sortable-handle').css('visibility', 'visible');
                }
            });

            if ($('.collapse_div').length > 0) {
                $('#TaskListDiv').accordion({
                    collapsible: true,
                    active: false
                });
            }

            $(function () {

                var next_task = $(".whoisnext-div #next_task_name");

                var numWords = next_task.text().length;

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

        </script>

        <script>
            function copyToClipboard(elementId) {
                $('#hdn_copy_url').val(document.getElementById(elementId).innerHTML);
                $('#hdn_copy_url').select();
                document.execCommand("copy");
                $('#hdn_copy_url').trigger('blur');
            }
        </script>

    </body>
</html>