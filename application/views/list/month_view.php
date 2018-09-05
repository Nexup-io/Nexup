<div class="caleneder_div_wrapper">
    <div class="container month_view_container">
        <div class="div_btn_name">
            <div class="btn_box_add">
                <a>Day</a>
                <a>Week</a>
                <a class="active">Month</a>
                <span class="change_month_resp">
                    <a class="prev_month"><i class="fa fa-angle-left"></i></a>
                    <a class="month_calendar_show"><i class="fa fa-calendar"></i></a>
                    <a class="next_month"><i class="fa fa-angle-right"></i></a>
                </span>
            </div>
        </div>
        <div class="calender_div_inner_box">
            <div class="div_left_one">
                <div class="month_calendar"></div>
            </div>
            <div class="div_right_one month_view_class">
                <?php
                $running_day = date('w',mktime(0,0,0,date('m'),1,date('Y')));
                //Get date before 3 days on first sunday of first week
                $days_in_month = date('t',mktime(0,0,0,date('m'),1,date('Y')));
                
                $month_end_day = date('w',mktime(0,0,0,date('m'),$days_in_month,date('Y')));
                
                
                $days_in_this_week = 1;
                $day_counter = 0;
                $dates_array = array();
                ?>
                <div class="div_celender_detail">
                    <div class="row_date">
                        <div class="day_name"><span>Sun</span></div>
                        <div class="day_name"><span>Mon</span></div>
                        <div class="day_name"><span>Tue</span></div>
                        <div class="day_name"><span>Wed</span></div>
                        <div class="day_name"><span>Thu</span></div>
                        <div class="day_name"><span>Fri</span></div>
                        <div class="day_name"><span>Sat</span></div>
                    </div>
                    <div class="presentation">
                        <div class="row_detail">
                            <div class="date_wrap">
                                <div class="day_number"><h2 class="date_h2">Apr 1</h2></div>
                                <div class="day_number"><h2 class="date_h2">2</h2></div>
                                <div class="day_number"><h2 class="date_h2">3</h2></div>
                                <div class="day_number"><h2 class="date_h2">4</h2></div>
                                <div class="day_number"><h2 class="date_h2">5</h2></div>
                                <div class="day_number"><h2 class="date_h2">6</h2></div>
                                <div class="day_number"><h2 class="date_h2">7</h2></div>
                            </div>
                            <div class="content_wrap">
                                <div class=""><h2 class="h2_content">BaberBaberBaberBaberBaberBaberBaberBaber</h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                            </div>
                        </div>
                        <div class="row_detail">
                            <div class="date_wrap">
                                <div class="day_number"><h2 class="date_h2">8</h2></div>
                                <div class="day_number"><h2 class="date_h2">9</h2></div>
                                <div class="day_number"><h2 class="date_h2">10</h2></div>
                                <div class="day_number"><h2 class="date_h2">11</h2></div>
                                <div class="day_number"><h2 class="date_h2">12</h2></div>
                                <div class="day_number"><h2 class="date_h2">13</h2></div>
                                <div class="day_number"><h2 class="date_h2">14</h2></div>
                            </div>
                            <div class="content_wrap">
                                <div class=""><h2 class="h2_content">Hiren</h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                            </div>
                        </div>
                        <div class="row_detail">
                            <div class="date_wrap">
                                <div class="day_number"><h2 class="date_h2">15</h2></div>
                                <div class="day_number"><h2 class="date_h2">16</h2></div>
                                <div class="day_number"><h2 class="date_h2">17</h2></div>
                                <div class="day_number"><h2 class="date_h2">18</h2></div>
                                <div class="day_number"><h2 class="date_h2">19</h2></div>
                                <div class="day_number"><h2 class="date_h2">20</h2></div>
                                <div class="day_number"><h2 class="date_h2">21</h2></div>
                            </div>
                            <div class="content_wrap">
                                <div class=""><h2 class="h2_content">Suresh</h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                            </div>
                        </div>
                        <div class="row_detail">
                            <div class="date_wrap">
                                <div class="day_number"><h2 class="date_h2">22</h2></div>
                                <div class="day_number"><h2 class="date_h2">23</h2></div>
                                <div class="day_number"><h2 class="date_h2">24</h2></div>
                                <div class="day_number"><h2 class="date_h2">25</h2></div>
                                <div class="day_number"><h2 class="date_h2">26</h2></div>
                                <div class="day_number"><h2 class="date_h2">27</h2></div>
                                <div class="day_number"><h2 class="date_h2">28</h2></div>
                            </div>
                            <div class="content_wrap">
                                <div class=""><h2 class="h2_content">Prerna</h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                            </div>
                        </div>
                        <div class="row_detail">
                            <div class="date_wrap">
                                <div class="day_number"><h2 class="date_h2">29</h2></div>
                                <div class="day_number"><h2 class="date_h2">30</h2></div>
                                <div class="day_number"><h2 class="date_h2">1</h2></div>
                                <div class="day_number"><h2 class="date_h2">2</h2></div>
                                <div class="day_number"><h2 class="date_h2">3</h2></div>
                                <div class="day_number"><h2 class="date_h2">4</h2></div>
                                <div class="day_number"><h2 class="date_h2">5</h2></div>
                            </div>
                            <div class="content_wrap">
                                <div class=""><h2 class="h2_content">Test</h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                                <div class=""><h2 class="h2_content"></h2></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    @media(max-width:767px){
        .calender_div_inner_box .div_left_one{display: none !important;}
    }
</style>