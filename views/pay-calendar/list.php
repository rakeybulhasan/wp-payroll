<div class="wrap erp erp-company-single">
    <h2>
        <?php _e( 'Pay Calendar', 'rbs-erp' ); ?>
        <a href="<?php echo admin_url( 'admin.php?page=pay-calendar&action=new' ); ?>" class="add-new-h2"><?php _e( 'Add New Pay Calendar', 'erp' ); ?></a>
    </h2>

    <?php
    if (isset($_GET['status'])&& $_GET['status'] == 'success' ) {
        erp_html_show_notice( __( 'Pay Calendar has been deleted successfully.', 'rbs-erp' ) );
    } elseif(isset($_GET['status'])&& $_GET['status']=='trash' && isset($_GET['error'])) {

        erp_html_show_notice(isset($_GET['error'])?$_GET['error']:'', 'error' );
    }

    ?>

    <div class="metabox-holder company-accounts">


        <div class="company-location-wrap">

            <div id="company-locations">
                <div id="company-locations-inside">
                    <form>
                    <input type="hidden" name="page" value="pay-calendar">
                        <div id="exTab2" class="">
                            <ul class="nav nav-tabs">
                                <?php $i=1; foreach (payCalenderType() as $key=>$calenderType) {?>
                                    <li class="<?php echo $i==1?'active':''?>">
                                        <a  href="#<?php echo $key;?>" data-toggle="tab"><?php echo $calenderType;?></a>
                                    </li>
                                <?php $i++; }?>
                            </ul>

                            <div class="tab-content row">
                                <?php
                                $payCalendarObj = new Pay_Calendar();
                                $j=1;
                                foreach (payCalenderType() as $key=>$calenderType) {?>
                                    <div class="tab-pane <?php echo $j==1?'active':''?>" id="<?php echo $key;?>">
                                        <?php

                                        $payCalendars = $payCalendarObj->getPayCalendar($calenderType);

                                        $branchObj = new Branch();

                                        if($payCalendars){
                                            $count=1;
                                            foreach ($payCalendars as $payCalendar) {?>
                                                <div class="col-md-3">
                                                    <div class="postbox company-postbox">
                                                        <h3 class="hndle"><span><?php echo ucfirst($payCalendar->pay_calendar_name); ?></span></h3>
                                                        <div class="inside">
                                                            <div class="row">
                                                                <div class="col-md-12">

                                                                    <div class="form-group row margin_bottom_10">
                                                                        <label for="staticEmail" class="col-sm-5 col-form-label no_padding_right font_size_12">Company Name:</label>
                                                                        <div class="col-sm-7 no_padding_left font_size_12">
                                                                            <?php echo ucfirst($payCalendar->company_name);?>
                                                                        </div>
                                                                    </div>
                                                                    <?php if ($payCalendar->branch_id || $payCalendar->branch_id!='0'){?>

                                                                        <div class="form-group row margin_bottom_10">
                                                                            <label for="staticEmail" class="col-sm-5 col-form-label no_padding_right font_size_12">Branch Name:</label>
                                                                            <div class="col-sm-7 no_padding_left font_size_12">
                                                                                <?php echo $branchObj->get_branch_name($payCalendar->branch_id);?>
                                                                            </div>
                                                                        </div>
                                                                    <?php }?>

                                                                    <div class="form-group row margin_bottom_10">
                                                                        <label for="inputPassword" class="col-sm-5 col-form-label no_padding_right font_size_12">Calendar Type:</label>
                                                                        <div class="col-sm-7 no_padding_left font_size_12">
                                                                            <?php echo ucfirst($payCalendar->pay_calendar_type);?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row margin_bottom_10">
                                                                        <label for="inputPassword" class="col-sm-5 col-form-label no_padding_right font_size_12">No of Employee:</label>
                                                                        <div class="col-sm-7 no_padding_left font_size_12">
                                                                            <?php echo ucfirst($payCalendar->employ_count);?>
                                                                        </div>
                                                                    </div>


                                                                </div>

                                                            </div>

                                                        </div><!-- .inside -->

                                                        <div class="action-col">
                                                            <a onclick="javascript:return confirm('Are you Sure deleted?');" class="btn btn-xs btn-danger" href="<?php echo admin_url( 'admin.php?page=pay-calendar&action=delete&id='. $payCalendar->pId )?>"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                                                            <a class="btn btn-xs btn-primary" href="<?php echo admin_url( 'admin.php?page=pay-calendar&action=edit&id='. $payCalendar->pId )?>"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>
                                                            <a href="<?php echo admin_url( 'admin.php?page=pay-run&tab=employees&id='. $payCalendar->pId )?>" style="float: right" class="btn btn-primary btn-xs start_pay_run">Start Payrun</a>
                                                        </div>

                                                    </div>
                                                </div>
                                                <?php if($count%4==0){?>
                                                    <div class="clearfix"></div>
                                                <?php }?>

                                                <?php

                                                $count++;
                                            }
                                        }else{
                                            echo '<p>No record found.</p>';
                                        }
                                        ?>
                                    </div>
                                    <?php $j++;
                                }?>

                            </div>
                        </div>

                    </form>
                </div><!-- #company-locations-inside -->
            </div><!-- #company-locations -->
        </div><!-- .company-location-wrap -->
    </div><!-- .metabox-holder -->

</div>
