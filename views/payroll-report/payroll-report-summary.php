<div class="wrap erp erp-company-single summary_sheet">


    <div class="metabox-holder company-accounts">


        <div class="company-location-wrap ">

            <div id="company-locations" class="">
                <div class="col-md-12">
                    <a style="float: right" class="btn btn-primary" href="javascript:void (0)" onclick="window.print()"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Print</a>
                </div>
                <form class="form-horizontal" action="" method="get">
                    <?php
                    $employee_id = isset($_GET['employee_id'])?$_GET['employee_id']:0;
                    $from_date = isset($_GET['from_date'])?$_GET['from_date']:'';
                    $to_date = isset($_GET['to_date'])?$_GET['to_date']:'';
                    ?>
                    <div class="row print_hidden">

                        <input type="hidden" name="page" value="payroll-report">
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label class="control-label col-sm-4" for="employee_id"><?php _e('Employee ID','rbs-erp');?></label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="employee_id" value="<?php echo $employee_id?>" placeholder="Enter Employee ID">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="from_date"><?php _e('From Date','rbs-erp');?></label>
                                <div class="col-sm-8">
                                    <?php erp_html_form_input(array(
                                        'name'  => 'from_date',
                                        'type'  => 'text',
                                        'value' => $from_date,
                                        'class' => 'form-control date-field',
                                        'custom_attr'=> array('autocomplete' => 'off'),
                                    )); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="to_date"><?php _e('To Date','rbs-erp');?></label>
                                <div class="col-sm-8">
                                    <?php erp_html_form_input(array(
                                        'name'  => 'to_date',
                                        'type'  => 'text',
                                        'value' => $to_date,
                                        'class' => 'form-control date-field',
                                        'custom_attr'=> array('autocomplete' => 'off'),
                                    )); ?>
                                </div>
                            </div>
                            <button type="submit" class="col-md-offset-4 btn btn-success btn-sm">Filter</button>
                        </div>

                    </div>
                <div class=" table-responsive">
                    <?php

                    $payCalendarRunObj = new Payroll_Report();
                    $payCalendarRuns = $payCalendarRunObj->generatePaySlipForReport($employee_id, $from_date, $to_date);
                    $allowanceObj = new Addition();
                    $allowances = $allowanceObj->getActiveAddition();
                    $allowance_count = count($allowances);
                    $deductionObj = new Deduction();
                    $deductions = $deductionObj->getActiveDeduction();
                    $deductions_count = count($deductions);
                    $total_item_count = 3 + $allowance_count + $deductions_count;
                    ?>
                    <div id="company-locations-inside" class="printableArea">
                       <?php if ($payCalendarRuns){?>
                        <table class="table">
                            <tr>
                                <td style="width: 50%">
                                    <p><?php echo $payCalendarRuns[0]->company_name;?></p>
                                    <p><?php echo $payCalendarRuns[0]->branch_name;?></p>
                                    <p><?php echo $payCalendarRuns[0]->address_1;?></p>
                                </td>
                                <td style="width: 50%">
                                    <p><?php echo $payCalendarRuns[0]->pcName;?></p>
                                    <p>Month : <?php echo getMonthStringToDate($payCalendarRuns[0]->from_date,'F') ;?> &ensp;&ensp;&ensp; Year : <?php echo getMonthStringToDate($payCalendarRuns[0]->from_date,'Y') ;?></p>
                                    <p>From  : <?php echo getMonthStringToDate($payCalendarRuns[0]->from_date,'d-m-Y') ;?>&ensp;&ensp;&ensp;  To : <?php echo getMonthStringToDate($payCalendarRuns[0]->to_date,'d-m-Y') ;?></p>
                                </td>
                            </tr>
                        </table>

                        <table border="1" class="table table-sm pay_summery">
                            <thead>
                            <tr>
                                <th style="vertical-align: middle;border-bottom: 1px solid #000">Employee</th>
                                <th style="vertical-align: middle">Code</th>
                                <th style="vertical-align: middle;border-bottom: 1px solid #000">Basic</th>
                                <?php
                                $allowance_id= array();
                                foreach ($allowances as $allowance){
                                    $allowance_id[]=$allowance->id;
                                    ?>
                                    <th style="vertical-align: middle;border-bottom: 1px solid #000"><?php echo $allowance->addition_name;?></th>
                                <?php }?>
                                <!--                            <th>Total Allowance</th>-->
                                <th style="vertical-align: middle;border-bottom: 1px solid #000">Adjusted Amount</th>
                                <?php
                                $deduction_id=array();
                                foreach ($deductions as $deduction){
                                    $deduction_id[]=$deduction->id;
                                    ?>
                                    <th style="vertical-align: middle;border-bottom: 1px solid #000"><?php echo $deduction->deduction_name;?></th>
                                <?php }?>
                                <!--                            <th>Total Deduction</th>-->
                                <th style="vertical-align: middle;border-bottom: 1px solid #000">Net Pay</th>
                                <th style="vertical-align: middle" class="print_hidden">Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php
                            if($payCalendarRuns){
                                $i=1;
                                $all_total = 0;
                                $basic_all_total = 0;
                                $adjusted_all_total = 0;
                                foreach ($payCalendarRuns as $payCalendarRun){


                                    $absentDay= isset($payCalendarRun->number_of_absence_day)?$payCalendarRun->number_of_absence_day:0;
                                    ?>


                                    <?php
                                    $totalDay = cal_days_in_month(CAL_GREGORIAN, getMonthStringToDate($payCalendarRun->from_date,'m'), getMonthStringToDate($payCalendarRun->from_date,'Y'));
                                    ?>
                                    <tr>
                                        <?php
                                        $allowance_items_json = json_decode($payCalendarRun->allowance,true);
                                        $deduction_items_json = json_decode($payCalendarRun->deduction,true);
                                        $rateBasicSalary = $payCalendarRun->payroll_info_basic_salary?$payCalendarRun->payroll_info_basic_salary:0;
                                        $basic_pay = $payCalendarRun->basicSalary?$payCalendarRun->basicSalary:0;
                                        $adjustedAmount = $payCalendarRun->adjustedAmount?$payCalendarRun->adjustedAmount:0;
                                        $total_allowance = array_sum($allowance_items_json);
                                        $total_deduction = array_sum($deduction_items_json);
                                        $total_deduction = $total_deduction + $adjustedAmount;
                                        $total = $basic_pay + $total_allowance - $total_deduction;
                                        $all_total = $all_total+$total;
                                        $basic_all_total = $basic_all_total+$basic_pay;
                                        $adjusted_all_total = $adjusted_all_total+$adjustedAmount;
                                        ?>
                                        <td><pre><?php echo $payCalendarRun->employee_name;?></pre></td>
                                        <td><pre><?php echo $payCalendarRun->employee_code;?></pre></td>
                                        <td style="font-weight: normal!important;"><?php echo number_format($basic_pay,2,'.',',');?></td>
                                        <?php
                                        $allowance_all_total[] =0;
                                        $allowance_all=0;
                                        foreach ($allowance_id as $add_id){
                                            ?>
                                            <td style="font-weight: normal!important;">
                                                <?php
                                                if($allowance_items_json && array_key_exists($add_id, $allowance_items_json)){
                                                    $allowance_all_total[$add_id] += $allowance_items_json[$add_id];
                                                    echo number_format($allowance_items_json[$add_id],2,'.',',');
                                                }else{
                                                    echo number_format(0,2) ;
                                                }
                                                ?>
                                            </td>
                                        <?php }?>
                                        <!--<td>
                                    <?php
                                        /*                                    echo number_format($total_allowance,2,'.',',');
                                                                            */?>
                                </td>-->
                                        <td style="font-weight: normal!important;"><?php echo number_format($adjustedAmount,2,'.',',');?></td>

                                        <?php
                                        $deduction_all_total[] =0;
                                        $deduction_all=0;
                                        foreach ($deduction_id as $dec_id){
                                            ?>
                                            <td style="font-weight: normal!important;">
                                                <?php
                                                if($deduction_items_json && array_key_exists($dec_id, $deduction_items_json)){
                                                    $deduction_all_total[$dec_id] += $deduction_items_json[$dec_id];
                                                    echo number_format($deduction_items_json[$dec_id],2,'.','');
                                                }else{
                                                    echo number_format(0,2) ;
                                                }
                                                ?>
                                            </td>
                                        <?php }?>
                                        <!--<td>
                                    <?php
                                        /*                                        echo number_format($total_deduction,2,'.',',');
                                                                            */?>
                                </td>-->
                                        <td style="font-weight: normal!important;"><?php echo number_format($total,2,'.',',');?></td>
                                        <td class="print_hidden">
                                            <button type="button" data-amount="<?php echo $total;?>" class="btn btn-danger btn-xs row_hide"><span class="glyphicon glyphicon-remove"></span></button>
                                        </td>

                                    </tr>

                                    <?php
                                    $i++;

                                }?>
                                <tr>
                                    <td colspan="2" style="text-align: center;"><strong>Total Amount</strong></td>
                                    <td style="text-align: center"><span class="grand_total_amount"><?php echo number_format($basic_all_total,2,'.',',');?></span></td>
                                    <?php

                                    foreach ($allowance_id as $add_id){
                                        ?>
                                        <td style="font-weight: normal!important;">
                                            <?php
                                            echo $allowance_all_total[$add_id]? number_format($allowance_all_total[$add_id],2,'.',','):number_format(0,2);
                                            //                                            }
                                            ?>
                                        </td>
                                    <?php }?>
                                    <td><?php echo number_format($adjusted_all_total,2,'.',',');?></td>
                                    <?php

                                    foreach ($deduction_id as $dec_id){
                                        ?>
                                        <td style="font-weight: normal!important;">
                                            <?php
                                            echo $deduction_all_total[$dec_id]? number_format($deduction_all_total[$dec_id],2,'.',','):number_format(0,2);
                                            //                                            }
                                            ?>
                                        </td>
                                    <?php }?>
                                    <td><input type="hidden" class="grand_total print_hidden" value="<?php echo $all_total;?>"><span class="grand_total_amount"><?php echo number_format($all_total,2,'.',',');?></span></td>
                                </tr>

                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                        <div class="col-md-12">
                            <a style="float: right" href="<?php echo admin_url( 'admin.php?page=payroll-report&tab=payroll-report-payslips&employee_id='.$employee_id.'&from_date='.$from_date.'&to_date='.$to_date)?>" class="btn btn-success">Generate Pay Slip</a>
                        </div>
                        <?php }?>

                    </div>
                </div>
                </form>
                <!-- #company-locations-inside -->
            </div><!-- #company-locations -->
        </div><!-- .company-location-wrap -->
    </div><!-- .metabox-holder -->

</div>