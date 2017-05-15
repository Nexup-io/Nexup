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

<div class="login-register-body">
    <ul class="nav nav-tabs responsive" id="myTab">
        <li class="active"><a href="#login-tab"> Login to your Account </a></li>
        <li><a href="#register-tab"> Creat New Account </a></li>
    </ul>

    <div class="tab-content responsive">
        <div class="tab-pane active" id="login-tab">
            <div class="landing-form-content">
                <form id="LoginForm" method="POST">
                    <div class="input-outer">
                        <label>Your Email</label>
                        <input type="text" name="user_name" id="user_name" placeholder="Email" placeholder="johndoe@yourmail.com" />
                    </div>
                    <div class="input-outer">
                        <label>Your Password</label>
                        <input type="password" name="password" id="password" placeholder="password" />
                    </div>
                    <div class="checkbox-outer">
                        <div class="checkbox">
                            <input id="check1" type="checkbox" name="check" value="check1">
                            <label for="check1">Remember Me</label>
                        </div>	
                        <a href="#" data-toggle="modal" data-target="#forgot-password">Forgot my Password</a>
                    </div>
                    <div class="button-outer">
                        <button type="submit" name="">Login</button>
                    </div>
                    <div class="redirect-register">
                        <p>Don’t you have an account? <span>Register Now!</span> it’s really simple and you can start enjoying all the benefits!</p>
                    </div>
                </form>
            </div>
        </div>

        <div class="tab-pane" id="register-tab">
            <div class="landing-form-content">
                <form id="RegisterForm" method="POST" action="<?php echo base_url() . 'register'; ?>">
                    <div class="two-field-input">
                        <div class="input-outer">
                            <label>First Name</label>
                            <input type="text" name="first_name" id="first_name" placeholder="First Name" />
                        </div>
                        <div class="input-outer">
                            <label>Last Name</label>
                            <input type="text" name="last_name" id="last_name" placeholder="Last Name" />
                        </div>
                    </div>

                    <div class="input-outer">
                        <label>Your Email</label>
                        <input type="text"  name="email" id="email" placeholder="johndoe@yourmail.com" />
                    </div>

                    <div class="input-outer">
                        <label>Password</label>
                        <input type="password" name="password" id="password" placeholder="Your Password" />
                    </div>

                    <div class="checkbox-outer i-accept">
                        <div class="checkbox">
                            <input id="terms_check" type="checkbox" name="terms" value="1">
                            <label for="terms_check">I accept the <a href="">Terms and Conditions</a> of the website</label>
                        </div>
                        <label id="terms-error" class="custom_form_error" for="terms" style="display: none;"></label>
                    </div>

                    <div class="button-outer register-btn">
                        <button id="register" type="submit" name="">Complete Registration!</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>