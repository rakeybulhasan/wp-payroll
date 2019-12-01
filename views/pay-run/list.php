<div class="wrap erp erp-company-single">
    <h2>
        <?php _e( 'Pay Calendar Run List', 'rbs-erp' ); ?>
    </h2>

    <div class="metabox-holder company-accounts">


        <div class="company-location-wrap">

            <div id="company-locations">
                <div id="company-locations-inside">
                    <?php
                    $selected_company = ( isset( $_GET['company_id'] ) ) ? $_GET['company_id'] : 0;
                    $selected_branch_id = ( isset( $_GET['branch_id'] ) ) ? $_GET['branch_id'] : 0;

                    ?>
                    <form action="" method="get">
                        <input type="hidden" name="page" value="pay-run">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group required">
                                    <label class="control-label col-sm-4" for="company_id"><?php _e('Company','rbs-erp');?></label>
                                    <div class="col-sm-8">
                                        <?php erp_html_form_input( array(
                                            'name'        => 'company_id',
                                            'type'        => 'select',
                                            'value'=> $selected_company,
                                            'options'     => companyDropdownOptions()
                                        ) ); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">

                                <div class="form-group branch_group required">
                                    <label class="control-label col-sm-4" for="branch_id"><?php _e('Branch','rbs-erp');?></label>
                                    <div class="col-sm-8">
                                        <?php erp_html_form_input( array(
                                            'name'        => 'branch_id',
                                            'type'        => 'select',
                                            'options'     => array( '' => __( ' Select Branch', 'rbs-erp' ) )
                                        ) ); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success btn-sm">Filter</button>
                            </div>

                        </div>
                        <?php
                        $payCalendarRunObj = new Pay_Calendar_Run();
                        $payCalendarRuns = $payCalendarRunObj->getPayCalendarRunList($selected_company, $selected_branch_id);

//                        if($payCalendars){
//                            foreach ($payCalendars as $payCalendar) {?>
                                <table class="table table-striped pay_calendar_setup">
                                    <thead>
                                    <tr>
                                        <td>ID </td>
                                        <td>Calender Name </td>
                                        <td>Company Name </td>
                                        <td>Branch Name </td>
                                        <td>Month </td>
                                        <td>Payment Date</td>
                                        <td>No. of Employee</td>
                                        <td>Action</td>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    if($payCalendarRuns){
                                    foreach ($payCalendarRuns as $payCalendarRun){
                                        ?>
                                        <tr>
                                            <td><?php echo $payCalendarRun->pId;?></td>
                                            <td><?php echo $payCalendarRun->pay_calendar_name;?></td>
                                            <td><?php echo $payCalendarRun->company_name;?></td>
                                            <td><?php echo $payCalendarRun->branch_name;?></td>
                                            <td><?php echo getMonthStringToDate($payCalendarRun->from_date,'M-Y');?></td>
                                            <td><?php echo getMonthStringToDate($payCalendarRun->payment_date,'d-m-Y');?></td>
                                            <td><?php echo $payCalendarRun->employ_count;?></td>
                                            <td>
                                                <a href="<?php echo admin_url( 'admin.php?page=pay-run&tab=allowance-deduction-input&id='. $payCalendarRun->pId )?>" title="Pay Run Update" class="btn btn-primary"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                                                <a href="<?php echo admin_url( 'admin.php?page=pay-run&tab=summary&id='. $payCalendarRun->pId )?>" title="Top Sheet Generate" class="btn btn-success"><span class="glyphicon glyphicon glyphicon-th-list" aria-hidden="true"></span></a>
                                                <a href="<?php echo admin_url( 'admin.php?page=pay-run&tab=payslips&id='. $payCalendarRun->pId )?>" title="Pay Slip Generate" class="btn btn-primary"><span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span></a>
                                            </td>
                                        </tr>
                                        <?php

                                    }
                                    }else{?>

                                        <tr>
                                            <td colspan="10">No record found.</td>
                                        </tr>

                                    <?php

                                    }
                                    ?>

                                    </tbody>
                                </table>

                    </form>
                </div><!-- #company-locations-inside -->
            </div><!-- #company-locations -->
        </div><!-- .company-location-wrap -->
    </div><!-- .metabox-holder -->

</div>