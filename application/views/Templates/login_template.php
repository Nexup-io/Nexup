<html>

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $title; ?></title>
        <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Roboto" rel="stylesheet">
        <link href="<?php echo base_url() . 'assets/css/bootstrap.min.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/bootstrap-datetimepicker.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/icomoon.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/bootstrap-select.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/style.css'; ?>" rel="stylesheet" />
        <link href="<?php echo base_url() . 'assets/css/responsive.css'; ?>" rel="stylesheet" />

<!--        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery.min.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/bootstrap.min.js'; ?>"></script>-->
    </head>
    <body class="login-register">
        <div class="login-register-content">
            <div class="login-register-head">
                <a href=""><img src="<?php echo base_url() . 'assets/img/nexup-logo.jpg'; ?>" alt=""/></a>
            </div>
            <?php
            echo $body;
            ?>
        </div>

        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery.min.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/bootstrap.min.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/moment-with-locales.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/responsive-tabs.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/bootstrap-datetimepicker.min.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/bootstrap-select.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/validate.min.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo base_url() . 'assets/js/InfloAPIScript.js'; ?>"></script>

        <script>
            $('ul.nav.nav-tabs  a').click(function (e) {
                e.preventDefault();
                $(this).tab('show');
            });
            (function ($) {
                fakewaffle.responsiveTabs(['xs', 'sm']);
            })(jQuery);
            $('#datetimepicker1').datetimepicker({
                sideBySide: false
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
        
        
            $('#RegisterForm').validate({
                ignore: [],
                errorClass: 'custom_form_error',
                rules: {
                    first_name: {
                        required: true,
                    },
                    last_name: {
                        required: true,
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true,
                        minlength: 8
                    },
                    terms:{
                        required: true
                    }
                },
                messages: {
                    first_name: {
                        required: 'Please enter first name!',
                    },
                    last_name: {
                        required: 'Please enter last name!',
                    },
                    email: {
                        required: 'Please enter email!',
                        email: 'Please enter a valid email!'
                    },
                    password: {
                        required: 'Please enter password!',
                        minlength: 'Minimum 8 characters are required!'
                    },
                    terms:{
                        required: 'Please accept terms of use!'
                    }
                }
            });
        </script>

    </body>
</html>