<div class="list_div">
    <section id="content" class="content">
        <div class="list-head">
            <h2>Welcome  <small>to Nexup</small> </h2>
            <a class="icon-add custom_cursor" id="add_list_top_btn" href="<?php echo base_url(); ?>"></a>
        </div>
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
        <div class="list-body">
            <ul class="list-body-ul">
                <?php
                if ($this->session->userdata('logged_in') && isset($totalTaskCount)) {
                ?>
                <li id="all_items_li">
                    <div class="list-body-box custom_cursor">
                        <a data-id="0" data-slug="all_tasks">
                            <big>All Items</big>
                            <small><?php
                                echo $totalTaskCount;
                                if ($totalTaskCount > 1) {
                                    echo ' Items';
                                } else {
                                    echo ' Item';
                                }
                                ?></small>
                        </a>
                    </div>
                </li>
                <?php
                }
                ?>
                <?php
                if(!empty($lists)){
                foreach ($lists as $list):
                    if (!$this->session->userdata('logged_in')) {
                        if ($this->session->userdata('list_id') != null) {
                            if (in_array($list['ListId'], $this->session->userdata('list_id'))) {
                                ?>
                                <li id="list_<?php echo $list['ListId']; ?>"class="list-body-li">
                                    <div class="list-body-box custom_cursor">
                                        <a href="<?php echo base_url() . 'item/' . $list['ListSlug']; ?>" data-id="<?php echo $list['ListId']; ?>" data-slug="<?php echo $list['ListSlug']; ?>">
                                            <big id="listname_<?php echo $list['ListId']; ?>" class="listname_<?php echo $list['ListId']; ?>"><?php echo $list['ListName'] ?></big>
                                            <small><?php echo $list['total_items']; ?> <?php
                                                if ($list['total_items'] > 1) {
                                                    echo 'Items';
                                                } else {
                                                    echo 'Item';
                                                }
                                                ?></small>
                                        </a>
                                        <?php if ($this->session->userdata('logged_in')) { ?>
                                            <div class="list-body-dropdown">
                                                <a href="javascript:void(0)" class="icon-more" style="display: none;"></a>
                                            </div>
                                        <?php } ?>
                                        <div class="dropdown-action">
                                            <a class="icon-edit edit_list custom_cursor" data-id="<?php echo $list['ListId']; ?>" data-slug="<?php echo $list['ListSlug']; ?>"> </a>
                                            <a class="icon-cross-out delete_list custom_cursor" data-id="<?php echo $list['ListId']; ?>" data-slug="<?php echo $list['ListSlug']; ?>"> </a>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            }
                        }
                    } else {
                        ?>
                        <li id="list_<?php echo $list['ListId']; ?>"class="list-body-li">
                            <div class="list-body-box custom_cursor">
                                <a href="<?php echo base_url() . 'item/' . $list['ListSlug']; ?>" data-id="<?php echo $list['ListId']; ?>" data-slug="<?php echo $list['ListSlug']; ?>">
                                    <big id="listname_<?php echo $list['ListId']; ?>" class="listname_<?php echo $list['ListId']; ?>"><?php echo $list['ListName'] ?></big>
                                    <small><?php echo $list['total_items']; ?> <?php
                                        if ($list['total_items'] > 1) {
                                            echo 'Items';
                                        } else {
                                            echo 'Item';
                                        }
                                        ?></small>
                                </a>
                                <?php if ($this->session->userdata('logged_in')) { ?>
                                    <div class="list-body-dropdown">
                                        <a href="javascript:void(0)" class="icon-more" style="display: none;"></a>
                                    </div>
                                <?php } ?>
                                <div class="dropdown-action">
                                    <a class="icon-edit edit_list custom_cursor" data-id="<?php echo $list['ListId']; ?>" data-slug="<?php echo $list['ListSlug']; ?>"> </a>
                                    <a class="icon-cross-out delete_list custom_cursor" data-id="<?php echo $list['ListId']; ?>" data-slug="<?php echo $list['ListSlug']; ?>"> </a>
                                </div>
                            </div>
                        </li>
                        <?php
                    }
                endforeach;
                }
                ?>
                <li id="add_item_li">
                    <div class="list-body-box">
                        <input type="text" class="add-list-class" name="list_name" id="list_name" placeholder="I want to..." style="display:none;" />
                        <div class="add_list_cls" style="display: none;"></div>
                        <div class="list-body-plus"><a class="icon-add custom_cursor" href="<?php echo base_url(); ?>"></a></div>
                    </div>
                </li>
            </ul>
        </div>
    </section>
</div>


