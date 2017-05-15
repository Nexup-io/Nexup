<section id="content" class="content">
    <div class="history-wrraper">
        <div class="history-holder">
            <ul style="text-align:left;">
                <?php
                if (!empty($operations)) {
                    foreach ($operations as $operation):
                        $operation_type = $operation->Operation;
                        if (isset($operation->ListId)) {
                            if (strtolower($operation->Operation) == strtolower('Create List')) {
                                $det = 'Created List "' . $operation->NewListName . '"';
                            } elseif (strtolower($operation->Operation) == strtolower('Delete List')) {
                                $det = 'Deleted List "' . $operation->NewListName . '"';
                            } elseif (strtolower($operation->Operation) == strtolower('Update List')) {
                                $det = 'Updated List "' . $operation->NewListName . '"';
                            } else {
                                $det = '';
                            }
                            if ($det != '') {
                                ?>
                                <li><i class="fa fa-check" aria-hidden="true"></i> <?php echo $det; ?></li>
                                <?php
                            }
                        } elseif (isset($operation->TaskId)) {
                            if (strtolower($operation->Operation) == strtolower('Create Task')) {
                                $dets = 'Created Task "' . $operation->NewTaskName . '"';
                            } elseif (strtolower($operation->Operation) == strtolower('Delete Task')) {
                                $dets = 'Deleted Task "' . $operation->NewTaskName . '"';
                            } elseif (strtolower($operation->Operation) == strtolower('Update Task')) {
                                $dets = 'Updated Task "' . $operation->NewTaskName . '"';
                            } else {
                                $dets = '';
                            }
                            if ($dets != '') {
                            ?>
                            <li><i class="fa fa-check" aria-hidden="true"></i> <?php echo $dets; ?></li>
                            <?php
                            }
                        }
                    endforeach;
                }
                ?>
            </ul>
        </div>
    </div>

<!--    <div class="history-wrraper">   
        <div class="history-holder">
            <ul>
                <li><i class="fa fa-check" aria-hidden="true"></i> Lorem ipsum dolor sit amet, consectetur adipiscing elit</li>
                <li><i class="fa fa-check" aria-hidden="true"></i> Lorem ipsum dolor sit amet, consectetur adipiscing elit</li>
                <li><i class="fa fa-check" aria-hidden="true"></i> Lorem ipsum dolor sit amet, consectetur adipiscing elit</li>
                <li><i class="fa fa-check" aria-hidden="true"></i> Lorem ipsum dolor sit amet, consectetur adipiscing elit</li>
                <li><i class="fa fa-check" aria-hidden="true"></i> Lorem ipsum dolor sit amet, consectetur adipiscing elit</li>
                <li><i class="fa fa-check" aria-hidden="true"></i> Lorem ipsum dolor sit amet, consectetur adipiscing elit</li>
                <li><i class="fa fa-check" aria-hidden="true"></i> Lorem ipsum dolor sit amet, consectetur adipiscing elit</li>
                <li><i class="fa fa-check" aria-hidden="true"></i> Lorem ipsum dolor sit amet, consectetur adipiscing elit</li>
                <li><i class="fa fa-check" aria-hidden="true"></i> Lorem ipsum dolor sit amet, consectetur adipiscing elit</li>
            </ul>
        </div>
    </div>-->

</section>