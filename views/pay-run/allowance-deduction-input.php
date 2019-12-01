<div class="wrap erp erp-company-single ">

    <div class="metabox-holder company-accounts">

        <div class="company-location-wrap">

            <div id="company-locations">
                <div id="company-locations-inside" class="">

                    <div class="col-md-12">
                        <a style="float: right" href="<?php echo admin_url( 'admin.php?page=pay-run&tab=summary&id='.$id.'&status=submitted' )?>" class="btn btn-success">Generate Pay Summery</a>
                    </div>

                    <?php
                    $payCalendarRunObj = new Pay_Calendar_Run();
                    $payCalendarRuns = $payCalendarRunObj->generatePaySlipByPayCalenderRunId($id);
//                    var_dump($payCalendarRuns[0]->pcName);
                    ?>
                    <table class="table">
                        <tr>
                            <td>
                                <p><?php echo $payCalendarRuns[0]->company_name;?></p>
                                <p><?php echo $payCalendarRuns[0]->branch_name;?></p>
                                <p><?php echo $payCalendarRuns[0]->address_1;?></p>
                            </td>
                            <td>
                                <p><?php echo $payCalendarRuns[0]->pcName;?></p>
                                <p>Month : <?php echo getMonthStringToDate($payCalendarRuns[0]->from_date,'F') ;?> &ensp;&ensp;&ensp; Year : <?php echo getMonthStringToDate($payCalendarRuns[0]->from_date,'Y') ;?></p>
                                <p>From  : <?php echo getMonthStringToDate($payCalendarRuns[0]->from_date,'d-m-Y') ;?>&ensp;&ensp;&ensp;  To : <?php echo getMonthStringToDate($payCalendarRuns[0]->to_date,'d-m-Y') ;?>

                                </p>
                            </td>
                        </tr>
                    </table>

                    <div class="row">
                        <?php
                        $number_of_days = cal_days_in_month(CAL_GREGORIAN, getMonthStringToDate($payCalendarRuns[0]->from_date,'m'), getMonthStringToDate($payCalendarRuns[0]->from_date,'Y'));
                        ?>
                        <form class="form-horizontal" action="" method="post" id="erp-new-pay-calendar-run">
                            <input type="hidden" name="pay_calendar_run_id" id="current_pay_calendar_run_id" value="<?php echo $id;?>">
                            <input type="hidden" value="<?php echo $number_of_days?>" class="total_number_of_day_in_month">
                            <div class="col-md-5">
                                <div class="postbox company-postbox">
                                    <h3 class="hndle"><span><?php _e( 'Employee info', 'rbs-erp' ); ?></span></h3>
                                    <div class="inside">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group required">
                                                    <label class="control-label col-sm-4" for="from_date"><?php _e('Employee','rbs-erp');?></label>
                                                    <div class="col-sm-8">
                                                        <select class="form-control selected_employee_id" name="employee_id" id="selected_employee_id">
                                                            <option value="-1">Select Employee</option>
                                                            <?php if ($payCalendarRuns){
                                                                foreach ($payCalendarRuns as $calendarRun){
                                                                    ?>
                                                                    <option value="<?php echo $calendarRun->emId;?>"><?php echo $calendarRun->employee_name;?></option>
                                                                    <?php
                                                                }
                                                            }?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 variable_input">
                                                <table class="table">
                                                    <tr>
                                                        <td class="col-md-1"></td>
                                                        <td>Basic Pay</td>
                                                        <td class="basic_payment">0.00</td>
                                                        <input type="hidden" class="basic_payment_hidden" value="0.00">
                                                        <input type="hidden" class="prDetails_id" value="">
                                                    </tr>
                                                </table>
                                                <table border="0" class="table allowance_items">
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                                <table class="table">
                                                    <tr>
                                                        <td class="col-md-1"></td>
                                                        <td><strong>Total Payment Amount</strong></td>
                                                        <td class="total_payment">0.00</td>
                                                    </tr>
                                                </table>
                                                <table class="table deduction_items">
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                                <table class="table">
                                                    <tr>
                                                        <td class="col-md-1"></td>
                                                        <td><strong>Total Deduction Amount</strong></td>
                                                        <td class="total_deduction">0.00</td>
                                                    </tr>
                                                </table>
                                                <table class="table">
                                                    <tr>
                                                        <td class="col-md-1"></td>
                                                        <td><strong>Net Payment</strong></td>
                                                        <td class="net_total">0.00</td>
                                                    </tr>
                                                </table>

                                                <table class="table">
                                                    <tbody>
                                                    <tr>
                                                        <td>Allowance Description</td>
                                                        <td style="text-align: right" class="show_allowance_description"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Deduction Description</td>
                                                        <td style="text-align: right" class="show_deduction_description"></td>
                                                    </tr>

                                                    </tbody>
                                                </table>

                                                <table class="table">
                                                    <tbody>
                                                    <tr>
                                                        <td>
                                                           <strong> Number of Absence day </strong>
                                                            <input type="hidden" class="absence_day_hidden" value="">
                                                        </td>

                                                        <td style="text-align: right" class="absence_day">0
                                                        </td>

                                                    </tr>
                                                    </tbody>
                                                </table>

                                            </div>


                                        </div>

                                    </div><!-- .inside -->

                                </div><!-- .postbox -->
                                <div class="postbox company-postbox">
                                    <h3 class="hndle"><span><?php _e( 'Adjusted amount in this month', 'rbs-erp' ); ?></span></h3>
                                    <div class="inside">
                                        <table class="table">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    Adjusted Amount
                                                </td>

                                                <td>
                                                    <?php erp_html_form_input(array(
                                                        'name'  => 'adjusted_amount_per_transaction',
                                                        'type'  => 'text',
                                                        'class' => 'form-control adjusted_amount_per_transaction',
                                                        'value' => ''
                                                    )); ?>
                                                </td>

                                                <td>
                                                    <?php if($payCalendarRuns[0]->payment_type!='BONUS_PAYMENT'){?>
                                                        <input type="button" class="btn btn-primary add_adjusted_amount_per_transaction" value="Add">
                                                    <?php }?>
                                                </td>
                                            </tr>
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-7">
                                <div class="postbox company-postbox">
                                    <h3 class="hndle"><span><?php _e( 'Allowance and deduction for this pay run only', 'rbs-erp' ); ?></span></h3>
                                    <div class="inside allowance_deduction_panel">
                                        <h5>Allowance</h5>
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <td class="col-md-5">Pay Item</td>
                                                <td class="col-md-3">Pay Amount</td>
                                                <td class="col-md-2">Type</td>
                                                <td class="col-md-2">action</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <?php erp_html_form_input(array(
                                                        'name'  => 'addition_name',
                                                        'type'  => 'select',
                                                        'value' => '',
                                                        'class' => 'form-control addition_name',
                                                        'options' => additionDropdownOptions(),
                                                    )); ?>
                                                </td>

                                                <td>
                                                    <?php erp_html_form_input(array(
                                                        'name'  => 'addition_amount',
                                                        'type'  => 'text',
                                                        'class' => 'form-control addition_amount',
                                                        'value' => ''
                                                    )); ?>
                                                </td>
                                                <td>
                                                    <?php erp_html_form_input(array(
                                                        'name'  => 'amount_type',
                                                        'type'  => 'select',
                                                        'class' => 'form-control amount_type',
                                                        'options'=> array('percentage'=>'%','currency'=>'TK.','hour'=>'Hour','day'=>'Day')
                                                    )); ?>
                                                </td>
                                                <td>
                                                    <?php if($payCalendarRuns[0]->payment_type!='BONUS_PAYMENT'){?>
                                                    <input type="button" class="btn btn-primary add-allowance-item" value="Add">
                                                    <?php }?>
                                                </td>
                                            </tr>
                                            </tbody>

                                        </table>
                                        <table class="table">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    Allowance Description
                                                </td>

                                                <td>
                                                    <?php erp_html_form_input(array(
                                                        'name'  => 'allowance_description',
                                                        'type'  => 'textarea',
                                                        'class' => 'form-control allowance_description',
                                                        'value' => ''
                                                    )); ?>
                                                </td>

                                                <td>
                                                    <input type="button" class="btn btn-info add_allowance_description" value="Add">
                                                </td>
                                            </tr>
                                            </tbody>

                                        </table>
                                        <h5>Deduction</h5>
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <td class="col-md-5">Pay Item</td>
                                                <td class="col-md-3">Pay Amount</td>
                                                <td class="col-md-2">Type</td>
                                                <td class="col-md-2">action</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <?php erp_html_form_input(array(
                                                        'name'  => 'deduction_name',
                                                        'type'  => 'select',
                                                        'value' => '',
                                                        'class' => 'form-control deduction_name',
                                                        'options' => deductionDropdownOptions(),
                                                    )); ?>
                                                </td>

                                                <td>
                                                    <?php erp_html_form_input(array(
                                                        'name'  => 'deduction_amount',
                                                        'type'  => 'text',
                                                        'class' => 'form-control deduction_amount',
                                                        'value' => ''
                                                    )); ?>
                                                </td>
                                                <td>
                                                    <?php erp_html_form_input(array(
                                                        'name'  => 'amount_type',
                                                        'type'  => 'select',
                                                        'value' => 'percentage',
                                                        'class' => 'form-control deduction_amount_type',
                                                        'options'=> array('percentage'=>'%','currency'=>'TK.')
                                                    )); ?>
                                                </td>
                                                <td>
                                                    <?php if($payCalendarRuns[0]->payment_type!='BONUS_PAYMENT'){?>
                                                    <input type="button" class="btn btn-primary add-deduction-item" value="Add">
                                                    <?php }?>
                                                </td>
                                            </tr>
                                            </tbody>

                                        </table>
                                        <table class="table">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    Deduction Description
                                                </td>

                                                <td>
                                                    <?php erp_html_form_input(array(
                                                        'name'  => 'deduction_description',
                                                        'type'  => 'textarea',
                                                        'class' => 'form-control deduction_description',
                                                        'value' => ''
                                                    )); ?>
                                                </td>

                                                <td>
                                                    <input type="button" class="btn btn-info add_deduction_description" value="Add">
                                                </td>
                                            </tr>
                                            </tbody>

                                        </table>
                                    </div>
                                </div>

                                <div class="postbox company-postbox">
                                    <h3 class="hndle"><span><?php _e( 'Number of Absence in this month', 'rbs-erp' ); ?></span></h3>
                                    <div class="inside">
                                        <table class="table">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    Number of Absence day
                                                </td>

                                                <td>
                                                    <?php erp_html_form_input(array(
                                                        'name'  => 'number_of_absence_day',
                                                        'type'  => 'text',
                                                        'class' => 'form-control number_of_absence_day',
                                                        'value' => ''
                                                    )); ?>
                                                </td>

                                                <td>
                                                    <?php if($payCalendarRuns[0]->payment_type!='BONUS_PAYMENT'){?>
                                                    <input type="button" class="btn btn-primary add_number_of_absence_day" value="Add">
                                                    <?php }?>
                                                </td>
                                            </tr>
                                            </tbody>

                                        </table>
                                    </div>
                                </div>



                            </div>


                        </form>
                    </div>


                </div><!-- #company-locations-inside -->
            </div><!-- #company-locations -->
        </div><!-- .company-location-wrap -->
    </div><!-- .metabox-holder -->

</div>