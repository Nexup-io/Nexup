<section id="content" class="content">

    <div class="container">
        <div class="profile-wrraper">
            <div class="col-md-2 col-sm-4 col-xs-12">
                <form id="avatarForm" method="POST" enctype="multipart/form-data" action="<?php echo base_url() . 'change_avatar'; ?>">
                    <div class="images-pro">
                        <div class="go-top">
                            <input class="my-set_3" type="file" name="avatar" id="avatar">
                            <a href="#"><i class="fa fa-camera" aria-hidden="true"></i></a> 
                        </div>
                        <?php
                        $image = base_url() . 'assets/Uploads/avatar.png';
                        if ($_SESSION['image'] != '') {


//                            if (filter_var($_SESSION['image'], FILTER_VALIDATE_URL) === FALSE) {
//                                $image = base_url() . 'assets/Uploads/' . $_SESSION['image'];
//                            } else {
                                $image = $_SESSION['image'];
//                            }

                        }
                        ?>
                        <img src="<?php echo $image; ?>" alt="User profile picture" id="imgpreview" style="height: 128px; width: 128px"> 
                    </div>
                    <div id="upliad_btn_div">

                    </div>
                </form>
            </div>
            <div class="col-md-8 col-sm-8 col-xs-12">
                <div class="user-detail">
                    <p class="user-name"> <?php echo $user_details['first_name'] . ' ' . $user_details['last_name']; ?> </p>
                </div>
            </div>
        </div>

        <div class="profile-content">
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
            <div class="row">
                <div class="col-md-3 col-sm-3">

                    <ul id="nav-tabs-wrapper" class="nav nav-tabs nav-pills nav-stacked tab-holder">
                        <li class="active"><a href="#vtab1" data-toggle="tab">User Info</a></li>
                        <!--<li><a href="#vtab2" data-toggle="tab">Password</a></li>-->

                    </ul>
                </div>
                <div class="col-md-9 col-sm-9">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade in active" id="vtab1">
                            <div class="col-xs-12">
                                <h4>User Information</h4>
                            </div>

                            <form method="POST" action="<?php echo base_url() . 'profile'; ?>" id="profileForm">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="input-outer">
                                        <label>First Name</label>
                                        <input type="text" id="update_first_name" name="update_first_name" placeholder="First Name" value="<?php echo $user_details['first_name']; ?>" disabled="" style="background: #fff;border: none;">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="input-outer">
                                        <label>Last Name</label>
                                        <input type="text" id="update_last_name" name="update_last_name" placeholder="Last Name" value="<?php echo $user_details['last_name']; ?>" disabled="" style="background: #fff;border: none;">
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="input-outer">
                                        <label>Email</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo $user_details['email']; ?>" disabled="" style="background: #fff;border: none;">
                                    </div>  
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <a class="btn btn-info save-btn" id="update_info" href="https://inflo.io/User/MyProfile.aspx">Edit Profile</a>
                                </div>
                            </form>

                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="vtab2">
                            <div class="col-xs-12">
                                <h4>Password</h4>
                            </div>

                            <form method="POST" action="<?php echo base_url() . 'change_password'; ?>" id="passwordForm">

                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="input-outer">
                                        <label>Current Password</label>
                                        <input type="password" id="currentpassword" name="currentpassword" placeholder="current password">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="input-outer">
                                        <label>New Password</label>
                                        <input type="password" id="password" name="password" placeholder="new password">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="input-outer">
                                        <label>Confirm Password</label>
                                        <input type="password" id="confirmpassword" name="confirmpassword" placeholder="confirm password">
                                    </div>  
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <button type="submit" class="btn btn-info save-btn">Update</button>
                                    <input type="reset" class="btn btn-default" value="Clear">
                                </div>
                                
                                
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</section>