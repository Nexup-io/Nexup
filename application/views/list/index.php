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
//                if ($this->session->userdata('logged_in') && isset($totalTaskCount) && $find_param == '') {
                ?>
<!--                <li id="all_items_li">
                    <div class="list-body-box custom_cursor">
                        <a data-id="0" data-slug="all_tasks">
                            <big>All Items</big>
                            <small><?php
                                echo '<span class="all_items_count">' . $totalTaskCount . '</span>';
                                if ($totalTaskCount > 1) {
                                    echo ' Items';
                                } else {
                                    echo ' Item';
                                }
                                ?></small>
                        </a>
                    </div>
                </li>-->
                <?php
//                }
                ?>
                <?php
                if(!empty($lists)){
                foreach ($lists as $list):
                    if (!$this->session->userdata('logged_in')) {
                        if($list['user_id'] == 0){
                        if ($this->session->userdata('list_id') != null) {
                            if (in_array($list['ListId'], $this->session->userdata('list_id'))) {
                                ?>
                                <li id="list_<?php echo $list['ListId']; ?>"class="list-body-li own-li-list">
                                    <div class="list-body-box custom_cursor">
                                        <a href="<?php echo base_url() . 'list/' . $list['ListSlug']; ?>" data-id="<?php echo $list['ListId']; ?>" data-slug="<?php echo $list['ListSlug']; ?>">
                                            <big id="listname_<?php echo $list['ListId']; ?>" class="listname_<?php echo $list['ListId']; ?>"><?php echo htmlentities($list['ListName']); ?></big>
                                            <small>
                                                <?php
                                                if($list['list_type_id'] == 12){
                                                    $total_child = 0;
                                                    if($list['total_child'] != NULL){
                                                        $total_child = $list['total_child'];
                                                    }
                                                    echo '<span class="list_item_count">' . $total_child . '</span>';
                                                    if ($total_child > 1 || $total_child == 0) {
                                                        echo ' tabs';
                                                    } else {
                                                        echo ' tab';
                                                    }
                                                }else{
                                                    $total_cols = $this->TasksModel->count_col($list['ListId']);
                                                    if($total_cols == 0){
                                                        $total_cols = 1;
                                                    }
                                                    $total_rows = ($list['total_items'] / $total_cols);
                                                    echo '<span class="list_item_count">' . $total_rows . '</span>';
                                                    if ($total_rows > 1 || $total_rows == 0) {
                                                        echo ' rows';
                                                    } else {
                                                        echo ' row';
                                                    }
                                                }
                                                ?>
                                            </small>
                                            <div class="clearfix"></div>
                                            <small><?php echo $list['created_user_name']; ?></small>
                                            <span class="icon-tabbed-list"><img src="<?php echo base_url(); ?>assets/img/<?php echo $list['icon']; ?>"></span>
                                        </a>
                                        <div class="list-body-dropdown first-drop-down">
                                            <a href="javascript:void(0)" class="icon-more" data-toggle="tooltip" data-placement="top" title="Options" style="display: none;"></a>
                                        </div>
                                        <div class="dropdown-action">
                                            <a class="icon-edit edit_list custom_cursor" data-toggle="tooltip" data-placement="top" title="Edit" data-id="<?php echo $list['ListId']; ?>" data-slug="<?php echo $list['ListSlug']; ?>"> </a>
                                            <a class="icon-cross-out delete_list custom_cursor" data-id="<?php echo $list['ListId']; ?>" data-toggle="tooltip" data-placement="top" title="Delete" data-slug="<?php echo $list['ListSlug']; ?>"> </a>
                                            <?php if ($this->session->userdata('logged_in')) { ?>
                                            <div class="list-body-dropdown">
                                                <a href="javascript:void(0)" class="icon-more" data-toggle="tooltip" data-placement="top" title="Options" style="display: none;"></a>
                                            </div>
                                        <?php } ?>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            }
                        }
                        }
                    } else {
                        ?>
                        <li id="list_<?php echo $list['ListId']; ?>"class="list-body-li own-li-list">
                            <div class="list-body-box custom_cursor">
                                <a href="<?php echo base_url() . 'list/' . $list['ListSlug']; ?>" data-id="<?php echo $list['ListId']; ?>" data-slug="<?php echo $list['ListSlug']; ?>">
                                    <big id="listname_<?php echo $list['ListId']; ?>" class="listname_<?php echo $list['ListId']; ?>"><?php echo html_entity_decode($list['ListName']); ?></big>
                                    <small>
                                        <?php
                                        if($list['list_type_id'] == 12){
                                            $total_child = 0;
                                            if($list['total_child'] != NULL){
                                                $total_child = $list['total_child'];
                                            }
                                            echo '<span class="list_item_count">' . $total_child . '</span>';
                                            if ($total_child > 1 || $total_child == 0) {
                                                echo ' tabs';
                                            } else {
                                                echo ' tab';
                                            }
                                        }else{
                                            $total_cols = $this->TasksModel->count_col($list['ListId']);
                                            if($total_cols == 0){
                                                $total_cols = 1;
                                            }
                                            $total_rows = ($list['total_items']/$total_cols);
                                            echo '<span class="list_item_count">' . $total_rows . '</span>';
                                            if ($total_rows > 1 || $total_rows == 0) {
                                                echo ' rows';
                                            } else {
                                                echo ' row';
                                            }
                                        }
                                        ?>
                                    </small>
                                    <div class="clearfix"></div>
                                    <small><?php echo $list['created_user_name']; ?></small>
                                    <span class="icon-tabbed-list"><img src="<?php echo base_url(); ?>assets/img/<?php echo $list['icon']; ?>"></span>
                                </a>
                                <div class="list-body-dropdown first-drop-down">
                                    <a href="javascript:void(0)" class="icon-more" data-toggle="tooltip" data-placement="top" title="Options" style="display: none;"></a>
                                </div>
                                <div class="dropdown-action">
<!--                                    <a class="icon-edit edit_list custom_cursor" data-toggle="tooltip" data-placement="top" title="Edit" data-id="<?php echo $list['ListId']; ?>" data-slug="<?php echo $list['ListSlug']; ?>"> </a>-->
                                    <a class="icon-cross-out delete_list custom_cursor" data-toggle="tooltip" data-placement="top" title="Delete" data-id="<?php echo $list['ListId']; ?>" data-slug="<?php echo $list['ListSlug']; ?>"> </a>
                                    <?php if ($this->session->userdata('logged_in')) { ?>
                                    <div class="list-body-dropdown">
                                        <a title="Options" data-toggle="tooltip" data-placement="top" class="icon-more" id="menu_directory" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></a>
                                        <ul class="dropdown-menu ul_list_option_submenu" id="menu_dd" aria-labelledby="menu_directory">
                                            <li><a class="edit_list custom_cursor" data-toggle="tooltip" data-placement="top" title="Rename" data-id="<?php echo $list['ListId']; ?>" data-slug="<?php echo $list['ListSlug']; ?>">Rename</a></li>
                                            <li><a href="<?php echo base_url() . 'list/' . $list['ListSlug']; ?>" class="custom_cursor" data-toggle="tooltip" data-placement="top" title="Open" data-id="<?php echo $list['ListId']; ?>" data-slug="<?php echo $list['ListSlug']; ?>">Open</a></li>
                                            <li><a class="delete_list custom_cursor" data-toggle="tooltip" data-placement="top" title="Delete" data-id="<?php echo $list['ListId']; ?>" data-slug="<?php echo $list['ListSlug']; ?>">delete</a></li>
                                            <li id="copy_list_btn" class="copy-list-btn custom_cursor" data-id="<?php echo $list['ListId']; ?>" data-slug="<?php echo $list['ListSlug']; ?>" data-toggle="tooltip" data-placement="top" title="Copy List" style=""><a>copy</a></li>
                                            <?php
                                            if(isset($_SESSION['logged_in']) && $list['list_type_id'] != 12){
                                            ?>
                                            <li id="move_list_btn" class="move-list-btn custom_cursor" data-id="<?php echo $list['ListId']; ?>" data-slug="<?php echo $list['ListSlug']; ?>" data-toggle="tooltip" data-placement="top" title="Move List"><a>move</a></li>
                                            <?php
                                            }
                                            ?>
                                            <li><a class="share-btn share-deirectory-btn" id="share_btn" data-id="<?php echo $list['ListInfloId']; ?>" data-toggle="tooltip" data-placement="top" title="Share">share</a></li>
                                        </ul>
                                    </div>
                                    
                                <?php } ?>
                                </div>
                            </div>
                        </li>
                        <?php
                    }
                endforeach;
                }
                ?>
                <?php
                if (isset($_SESSION['logged_in'])) {
                    if(!empty($shared_lists)){
                        foreach ($shared_lists as $sid=>$slist):
                ?>
                    <li id="list_<?php echo $slist['ListId']; ?>"class="list-body-li shared-list-li">
                        <div class="list-body-box custom_cursor">
                            <a href="<?php echo base_url() . 'list/' . $slist['ListSlug']; ?>" data-id="<?php echo $slist['ListId']; ?>" data-slug="<?php echo $slist['ListSlug']; ?>">
                                <big id="listname_<?php echo $slist['ListId']; ?>" class="listname_<?php echo $slist['ListId']; ?>"><?php echo html_entity_decode($slist['ListName']); ?></big>
                                <small>
                                    <?php
                                    if($slist['list_type_id'] == 12){
                                        $total_child = 0;
                                        if($slist['total_child'] != NULL){
                                            $total_child = $slist['total_child'];
                                        }
                                        echo '<span class="list_item_count">' . $total_child . '</span>';
                                        if ($total_child > 1 || $total_child == 0) {
                                            echo ' tabs';
                                        } else {
                                            echo ' tab';
                                        }
                                    }else{
                                        $total_cols = $this->TasksModel->count_col($slist['ListId']);
                                        if($total_cols == 0){
                                            $total_cols = 1;
                                        }
                                        $total_shared_rows = ($slist['total_items']/$total_cols);
                                        echo $total_shared_rows;
                                        if ($total_shared_rows > 1 || $total_shared_rows == 0) {
                                            echo ' rows';
                                        } else {
                                            echo ' row';
                                        }
                                    }
                                    ?>
                                </small>
                                <div class="clearfix"></div>
                                <small><?php echo $slist['created_user_name']; ?></small>
                                <span class="icon-tabbed-list"><img src="<?php echo base_url(); ?>assets/img/<?php echo $slist['icon']; ?>"></span>
                            </a>
                            <img src="<?php echo base_url(); ?>/assets/img/file.png">
                            <?php if ($this->session->userdata('logged_in')) { ?>
                                <div class="list-body-dropdown first-drop-down">
                                    <a href="javascript:void(0)" class="icon-more" data-toggle="tooltip" data-placement="top" title="Options" style="display: none;"></a>
                                </div>
                            <?php } ?>
                            <div class="dropdown-action">
<!--                                <a class="icon-edit edit_list custom_cursor" data-toggle="tooltip" data-placement="top" title="Edit" data-id="<?php echo $slist['ListId']; ?>" data-slug="<?php echo $slist['ListSlug']; ?>"> </a>-->
                                <a class="remove_list_local_share custom_cursor" data-toggle="tooltip" data-placement="top" title="remove" data-id="<?php echo $slist['ListId']; ?>" data-slug="<?php echo $slist['ListSlug']; ?>">
                                </a>
                                
                                <?php if ($this->session->userdata('logged_in')) { ?>
                                    <div class="list-body-dropdown">
                                        <a title="Options" data-toggle="tooltip" data-placement="top" class="icon-more" id="menu_directory" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></a>
                                        <ul class="dropdown-menu ul_list_option_submenu" id="menu_dd" aria-labelledby="menu_directory">
                                            <li><a class="edit_list custom_cursor" data-toggle="tooltip" data-placement="top" title="Rename" data-id="<?php echo $slist['ListId']; ?>" data-slug="<?php echo $slist['ListSlug']; ?>">Rename</a></li>
                                            <li><a href="<?php echo base_url() . 'list/' . $slist['ListSlug']; ?>" class="custom_cursor" data-toggle="tooltip" data-placement="top" title="Open" data-id="<?php echo $slist['ListId']; ?>" data-slug="<?php echo $slist['ListSlug']; ?>">Open</a></li>
<!--                                            <li><a class="delete_list custom_cursor" data-toggle="tooltip" data-placement="top" title="Delete" data-id="<?php echo $slist['ListId']; ?>" data-slug="<?php echo $slist['ListSlug']; ?>">delete</a></li>-->
                                            <li id="copy_list_btn" class="copy-list-btn custom_cursor" data-id="<?php echo $slist['ListId']; ?>" data-slug="<?php echo $slist['ListSlug']; ?>" data-toggle="tooltip" data-placement="top" title="Copy List" style=""><a>copy</a></li>
                                            <li><a class="share-btn share-deirectory-btn" id="share_btn" data-id="<?php echo $slist['ListInfloId']; ?>" data-toggle="tooltip" data-placement="top" title="Share">share</a></li>
                                        </ul>
                                    </div>
                                    
                                <?php } ?>
                                
                            </div>
                        </div>
                    </li>
                <?php
                        endforeach;
                    }
                    if(!empty($visited_lists)){
                        foreach ($visited_lists as $vid=>$vlist):
                ?>
                    <li id="list_<?php echo $vlist['ListId']; ?>"class="list-body-li visited-list-li">
                        <div class="list-body-box custom_cursor">
                            <a href="<?php echo base_url() . 'list/' . $vlist['ListSlug']; ?>" data-id="<?php echo $vlist['ListId']; ?>" data-slug="<?php echo $vlist['ListSlug']; ?>">
                                <big id="listname_<?php echo $vlist['ListId']; ?>" class="listname_<?php echo $vlist['ListId']; ?>"><?php echo html_entity_decode($vlist['ListName']); ?></big>
                                <small>
                                    <?php
                                    if($vlist['list_type_id'] == 12){
                                        $total_child = 0;
                                        if($vlist['total_child'] != NULL){
                                            $total_child = $vlist['total_child'];
                                        }
                                        echo '<span class="list_item_count">' . $total_child . '</span>';
                                        if ($total_child > 1 || $total_child == 0) {
                                            echo ' tabs';
                                        } else {
                                            echo ' tab';
                                        }
                                    }else{
                                        $total_cols = $this->TasksModel->count_col($vlist['ListId']);
                                        if($total_cols == 0){
                                            $total_cols = 1;
                                        }
                                        $total_visited_rows = ($vlist['total_items']/$total_cols);
                                        echo $total_visited_rows;
                                        if ($total_visited_rows || $total_visited_rows == 0) {
                                            echo ' rows';
                                        } else {
                                            echo ' row';
                                        }
                                    }
                                    ?>
                                </small>
                                <div class="clearfix"></div>
                                <small><?php echo $vlist['created_user_name']; ?></small>
                                <span class="icon-tabbed-list"><img src="<?php echo base_url(); ?>assets/img/<?php echo $vlist['icon']; ?>"></span>
                            </a>
                            <img src="<?php echo base_url(); ?>/assets/img/browsing_b&w.png">
                            <?php if ($this->session->userdata('logged_in')) { ?>
                                <div class="list-body-dropdown first-drop-down">
                                    <a href="javascript:void(0)" class="icon-more" data-toggle="tooltip" data-placement="top" title="Options" style="display: none;"></a>
                                </div>
                            <?php } ?>
                            <div class="dropdown-action">
                                <a class="remove_list custom_cursor" data-toggle="tooltip" data-placement="top" title="Remove" data-id="<?php echo $vlist['ListId']; ?>" data-slug="<?php echo $vlist['ListSlug']; ?>"> </a>
                                <?php if ($this->session->userdata('logged_in')) { ?>
                                    <div class="list-body-dropdown">
                                        <a title="Options" data-toggle="tooltip" data-placement="top" class="icon-more" id="menu_directory" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></a>
                                        <ul class="dropdown-menu ul_list_option_submenu" id="menu_dd" aria-labelledby="menu_directory">
                                            <li><a class="edit_list custom_cursor" data-toggle="tooltip" data-placement="top" title="Rename" data-id="<?php echo $vlist['ListId']; ?>" data-slug="<?php echo $vlist['ListSlug']; ?>">Rename</a></li>
                                            <li><a href="<?php echo base_url() . 'list/' . $vlist['ListSlug']; ?>" class="custom_cursor" data-toggle="tooltip" data-placement="top" title="Open" data-id="<?php echo $vlist['ListId']; ?>" data-slug="<?php echo $vlist['ListSlug']; ?>">Open</a></li>
                                            <li id="copy_list_btn" class="copy-list-btn custom_cursor" data-id="<?php echo $vlist['ListId']; ?>" data-slug="<?php echo $vlist['ListSlug']; ?>" data-toggle="tooltip" data-placement="top" title="Copy List" style=""><a>Copy</a></li>
                                            <li><a class="share-btn share-deirectory-btn" id="share_btn" data-id="<?php echo $vlist['ListInfloId']; ?>" data-toggle="tooltip" data-placement="top" title="Share">share</a></li>
                                        </ul>
                                    </div>
                                    
                                <?php } ?>
                                
                            </div>
                        </div>
                    </li>
                <?php
                        endforeach;
                    }
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


<div class="modal fade move-modal" id="move_modal" tabindex="-2" role="dialog" aria-labelledby="LogModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="movemodal-head">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true" class="icon-cross-out"/>
				</button>
				<h2>Move list</h2>
			</div>
			<div class="movemodal-body">
				
			</div>
			<div class="button-outer" id="config_btn_div">
                            <button type="submit" name="save_move" id="save_move">Ok</button>
                            <button type="submit" name="close_move" id="close_move" class="close_move" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>