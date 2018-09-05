<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Expires" content="0" />
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
        <link href="<?php echo base_url() . 'assets/css/jquery.mCustomScrollbar.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/style.css?v=' . time(); ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/responsive.css?v=' . time(); ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/tabledragdrop.css'; ?>" rel="stylesheet" />
        <link rel="icon" href="<?php echo base_url(); ?>assets/img/favicon.ico" type="image/ico">
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
<!--                            <input type="text" name="search_list" id="search_list" placeholder="Search here"  <?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != 1) {
    echo 'disabled';
} ?> value="<?php if (isset($find_param)) {
                                echo $find_param;
                            } ?>"/>-->
                            <input type="text" name="search_list" id="search_list" placeholder="Search here" value="<?php if (isset($find_param)) {
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
        <input type="hidden" id="hndautoid" />
        <input type="hidden" id="hdnuserid"/>
        <input type="hidden" id="hdnaccesstoken"/>
        <div id="copy_msg"></div>
        <input type="hidden" id="hidden_share_click" value="0">
        <footer id="footer">
            <p>© <?php echo date('Y'); ?> Copyright - All Right Reserved.</p>
        </footer>


        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery.min.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-ui.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/bootstrap.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/moment-with-locales.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/responsive-tabs.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/bootstrap-datetimepicker.min.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/bootstrap-select.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/validate.min.js'; ?>"></script>
        <!--<script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-ui.min.js'; ?>"></script>-->
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-ui-touch-punch.min.js'; ?>"></script>
        <!--<script type="text/javascript" src="https://developer.inflo.io/Scripts/InfloAPIScript.js"></script>-->
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/InfloAPIScript.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery.tokeninput.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/clipboard.min.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery.mCustomScrollbar.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/tabledragdrop.js'; ?>"></script>


        <noscript><div class="no-script-enabled">It seems you have not enabled Java script! Please enable java script to perform any operation on NEXUP.</div></noscript>
        <script>

//            $(document).ready(function () {
//                setTimeout(function () {
//                    window.location = "<?php echo base_url(); ?>lists";
//            }, 100);
//            });


        </script>
    </body>
</html>
