<div class="wrap erp erp-company-single salary_slip_sheet">


    <div class="metabox-holder company-accounts">

        <div class="company-location-wrap">

            <div id="company-locations">
                <div class="col-md-12 print_hidden" style="margin-bottom: 15px">
                    <a style="float: right" class="btn btn-primary print_hidden" href="javascript:void (0)" onclick="window.print()"><span class="glyphicon glyphicon-print" aria-hidden="true"></span>  Print Payslip</a>

                    <form class="form-inline print_hidden" action="" method="get">
                        <?php wp_nonce_field( 'find-employee-into-pay-run' ); ?>
                        <input type="hidden" name="rbs-erp-action" value="find_employee_into_pay_run">
                        <input type="hidden" name="page" value="pay-run">
                        <input type="hidden" name="tab" value="payslips">
                        <input type="hidden" name="id" value="<?php echo $_GET['id']?>">
                        <input class="form-control" type="text" name="employee_id" value="" placeholder="Enter Employee ID">
                        <input class="btn btn-primary" type="submit" value="Filter">
                    </form>
                </div>

                <div id="company-locations-inside" class="printableArea" style="clear: both;">

                        <?php
                        $employee_id = isset($_GET['employee_id'])?$_GET['employee_id']:null;
                        $payCalendarRunObj = new Pay_Calendar_Run();
                        $payCalendarRunsMethod = $payCalendarRunObj->generatePaySlipByPayCalenderRunId($id, $employee_id);
                        ?>

                    <?php
                    if($payCalendarRunsMethod){
                        $paycalenderChunk = array_chunk($payCalendarRunsMethod,3);
                        foreach ($paycalenderChunk as $payCalendarRuns){

                            $i=1;
                            foreach ($payCalendarRuns as $payCalendarRun){
                                $allowanceObj = new Addition();
                                $allowances = $allowanceObj->getActiveAddition();
                                $deductionObj = new Deduction();
                                $deductions = $deductionObj->getActiveDeduction();

                                $absentDay= isset($payCalendarRun->number_of_absence_day)?$payCalendarRun->number_of_absence_day:0;
                                ?>

                                <table class="table pay_slip_outside <?php echo $i%3==0?'even':'odd';?> <?php echo $i==1?'first_table':'';?>" style="border-bottom: 2px dotted black">

                                    <tr class="print_hidden">
                                        <td style="text-align: right" colspan="4">
                                            <button type="button" data-amount="" class="btn btn-danger btn-xs table_hide"><span class="glyphicon glyphicon-remove"></span></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 28%;padding: 0">
                                            <p><?php echo $payCalendarRun->company_name;?></p>
                                            <p><?php echo $payCalendarRun->branch_name;?></p>
                                            <p><?php echo $payCalendarRun->address_1;?></p>
                                        </td>
                                        <td style="width: 33%;padding: 0">
                                            <p><?php echo $payCalendarRun->pcName;?></p>
                                            <p>Month : <?php echo getMonthStringToDate($payCalendarRun->from_date,'F') ;?> &ensp;&ensp; Year : <?php echo getMonthStringToDate($payCalendarRun->from_date,'Y') ;?></p>
                                            <?php if ($payCalendarRun->payment_type=='BONUS_PAYMENT'){?>
                                                <p>Bonus : <?php echo str_replace(array('_','-'),' ', $payCalendarRun->bonus_name); ?>, <?php echo getMonthStringToDate($payCalendarRun->from_date,'Y') ;?></p>
                                            <?php }else{?>
                                                <p>From  : <?php echo getMonthStringToDate($payCalendarRun->from_date,'d-m-Y') ;?>&ensp; To : <?php echo getMonthStringToDate($payCalendarRun->to_date,'d-m-Y') ;?></p>
                                            <?php }?>

                                        </td>
                                        <td style="width: 39%;padding: 0">
                                            <p>Name : <?php echo $payCalendarRun->employee_name;?></p>
                                            <p>Position : <?php echo $payCalendarRun->designation_name;?>&ensp; Joining : <?php echo getMonthStringToDate($payCalendarRun->joining_date,'d-m-Y');?></p>
                                            <p>Code : <?php echo $payCalendarRun->employee_code;?>&ensp;&ensp;Days Absent: <?php echo $absentDay;?></p>
                                        </td>
                                    </tr>
                                    <?php
                                    $totalDay = cal_days_in_month(CAL_GREGORIAN, getMonthStringToDate($payCalendarRun->from_date,'m'), getMonthStringToDate($payCalendarRun->from_date,'Y'));
                                    ?>
                                    <tr>
                                        <td style="padding: inherit" colspan="3">
                                            <?php
                                            $rate_allowance_items_json = json_decode($payCalendarRun->rate_allowance,true);
                                            $allowance_items_json = json_decode($payCalendarRun->allowance,true);
                                            $deduction_items_json = json_decode($payCalendarRun->deduction,true);
                                            $rateBasicSalary = $payCalendarRun->payroll_info_basic_salary?$payCalendarRun->payroll_info_basic_salary:0;
                                            $basic_pay = $payCalendarRun->basicSalary?$payCalendarRun->basicSalary:0;
                                            $adjustedAmount = $payCalendarRun->adjustedAmount?$payCalendarRun->adjustedAmount:0;
                                            ?>

                                            <?php $total = $basic_pay + array_sum($allowance_items_json) - array_sum($deduction_items_json) - $adjustedAmount;?>
                                            <table border="1" style="margin-bottom: <?php echo $payCalendarRun->payment_type=='BONUS_PAYMENT'?'40px':0?>;background-color: inherit" class="table pay_info" width="100%">
                                                <thead>
                                                <tr>
                                                    <td rowspan="2" style="vertical-align: middle;"></td>
                                                    <td rowspan="2" style="vertical-align: middle;">Basic Pay</td>

                                                    <td style="text-align: center" colspan="<?php echo count($allowances)+1;?>">Allowance</td>
                                                    <td style="text-align: center" colspan="<?php echo count($deductions)+2;?>">Deduction</td>
                                                    <?php if($payCalendarRun->payment_type=='BONUS_PAYMENT'){?>
                                                        <td rowspan="2" style="vertical-align: middle;">Bonus</td>
                                                    <?php }?>
                                                    <td rowspan="2" style="vertical-align: middle;">Net Total</td>
                                                </tr>
                                                <tr>
                                                    <?php
                                                    $allowance_id= array();
                                                    foreach ($allowances as $allowance){
                                                        $allowance_id[]=$allowance->id;
                                                        ?>
                                                        <td><?php echo $allowance->addition_name;?></td>
                                                    <?php }?>
                                                    <td>Total Allowance</td>
                                                    <td>Adjusted Amount</td>
                                                    <?php
                                                    $deduction_id=array();
                                                    foreach ($deductions as $deduction){
                                                        $deduction_id[]=$deduction->id;
                                                        ?>
                                                        <td><?php echo $deduction->deduction_name;?></td>
                                                    <?php }?>
                                                    <td>Total Deduction</td>
                                                </tr>

                                                </thead>

                                                <tr>
                                                    <td>Rates</td>
                                                    <td><?php echo number_format($rateBasicSalary,2,'.',',');?></td>

                                                    <?php
                                                    $total_allowance=0;
                                                    foreach ($allowance_id as $add_id){
                                                        ?>
                                                        <td>
                                                            <?php

                                                            if( $rate_allowance_items_json && array_key_exists($add_id, $rate_allowance_items_json)){
                                                                $total_allowance = $total_allowance + $rate_allowance_items_json[$add_id];
                                                                echo number_format($rate_allowance_items_json[$add_id],2,'.',',');
                                                            }else{
                                                                $total_allowance = $total_allowance+0;
                                                                echo number_format(0,2) ;
                                                            }
                                                            ?>
                                                        </td>
                                                    <?php }?>

                                                    <td>
                                                        <?php
                                                        echo number_format($total_allowance,2,'.',',');
                                                        ?>
                                                    </td>

                                                    <td><?php echo number_format($adjustedAmount,2,'.',',');?></td>

                                                    <?php
                                                    $total_deduction=0;
                                                    foreach ($deduction_id as $dec_id){
                                                        ?>
                                                        <td>
                                                            <?php
                                                            if( $deduction_items_json && array_key_exists($dec_id, $deduction_items_json)){
                                                                $total_deduction = $total_deduction + $deduction_items_json[$dec_id];
                                                                echo number_format($deduction_items_json[$dec_id],2,'.',',');
                                                            }else{
                                                                $total_deduction = $total_deduction+0;
                                                                echo number_format(0,2) ;
                                                            }
                                                            //                                                    $total_deduction = $total_deduction+ round($deduction_items_json[$dec_id]);
                                                            //                                                    echo $deduction_items_json && array_key_exists($dec_id, $deduction_items_json)? number_format(getCalculateAmountByAbsenceDay($deduction_items_json[$dec_id],$totalDay,$absentDay),2,'.',''):number_format(0,2) ;
                                                            ?>
                                                        </td>
                                                    <?php }?>

                                                    <td>
                                                        <?php
                                                        //                                                    $total_deduction = array_sum($deduction_items_json);
                                                        //                                                    $total_deduction = getCalculateAmountByAbsenceDay($total_deduction,$totalDay,$absentDay) + $adjustedAmount;
                                                        echo number_format($total_deduction,2,'.','');
                                                        ?>
                                                    </td>
                                                    <?php $rateTotal = $rateBasicSalary + $total_allowance - $total_deduction - $adjustedAmount;?>
                                                    <?php if($payCalendarRun->payment_type=='BONUS_PAYMENT'){?>
                                                        <td><?php echo number_format($rateBasicSalary,2,'.',',');?></td>
                                                    <?php }?>
                                                    <td><?php echo number_format($rateTotal,2,'.',',');?></td>

                                                </tr>

                                                <tr>
                                                    <td>Applicable</td>
                                                    <td><?php echo number_format($basic_pay,2,'.',',');?></td>

                                                    <?php foreach ($allowance_id as $add_id){
                                                        ?>
                                                        <td>
                                                            <?php
                                                            echo $allowance_items_json && array_key_exists($add_id, $allowance_items_json)? number_format($allowance_items_json[$add_id],2,'.',','):number_format(0,2) ;

                                                            ?>
                                                        </td>
                                                    <?php }?>

                                                    <td>
                                                        <?php
                                                        $total_allowance = array_sum($allowance_items_json);
                                                        echo number_format($total_allowance,2,'.',',');
                                                        ?>
                                                    </td>

                                                    <td><?php echo number_format($adjustedAmount,2,'.',',');?></td>

                                                    <?php foreach ($deduction_id as $dec_id){
                                                        ?>
                                                        <td>
                                                            <?php
                                                            echo $deduction_items_json && array_key_exists($dec_id, $deduction_items_json)? number_format($deduction_items_json[$dec_id],2,'.',''):number_format(0,2) ;
                                                            ?>
                                                        </td>
                                                    <?php }?>

                                                    <td>
                                                        <?php
                                                        $total_deduction = array_sum($deduction_items_json);
                                                        $total_deduction = $total_deduction + $adjustedAmount;
                                                        echo number_format($total_deduction,2,'.',',');
                                                        ?>
                                                    </td>
                                                    <?php if($payCalendarRun->payment_type=='BONUS_PAYMENT'){?>
                                                        <td><?php echo number_format($basic_pay,2,'.',',');?></td>
                                                    <?php }?>
                                                    <td><?php echo number_format($total,2,'.',',');?></td>

                                                </tr>
                                                <?php if($payCalendarRun->payment_type!='BONUS_PAYMENT'){?>
                                                    <tr>
                                                        <td class="line_hight_1" style="text-align: right; padding-right: 10px" colspan="<?php echo count($allowances)+3;?>">Balance Advance Payment</td>
                                                        <td class="line_hight_1" colspan="<?php echo count($deductions)+4;?>">
                                                            <?php
                                                            $totalAdvancePayment = isset($payCalendarRun->totalAdvPayment)?$payCalendarRun->totalAdvPayment:0;
                                                            $totalAdjAmount = isset($payCalendarRun->totalAdjAmount)?$payCalendarRun->totalAdjAmount:0;
                                                            $balance =  ($totalAdvancePayment>$totalAdjAmount)?$totalAdvancePayment - $totalAdjAmount:0;
                                                            echo number_format($balance,2,'.',',');
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php }?>
                                                <?php if (!empty($payCalendarRun->allowance_description) || !empty($payCalendarRun->deduction_description)){?>
                                                    <tr>
                                                        <td class="line_hight_1" style="text-align: center; padding-right: 10px" colspan="<?php echo count($allowances)+3;?>"><?php echo $payCalendarRun->allowance_description;?></td>
                                                        <td class="line_hight_1" colspan="<?php echo count($deductions)+4;?>"><?php echo $payCalendarRun->deduction_description;?>
                                                        </td>
                                                    </tr>
                                                <?php }?>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="border_top">
                                            <table width="100%">
                                                <tr>
                                                    <td width="10%">Net Taka : </td>
                                                    <td width="20%"><?php echo number_format($total,2,'.',',');?></td>
                                                    <td width="5%"> Taka : </td>
                                                    <td><?php echo NumbersToWords::convert($total);?></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tfoot>
                                    <tr>
                                        <td style="padding-top: 53px; vertical-align: bottom">
                                            <div class="stamp">Stamp</div>
                                            Employees Signature</td>
                                        <td style="vertical-align: bottom;text-align: center">Countersigned By</td>
                                        <td style="vertical-align: bottom;text-align: center">Authorised Signature</td>
                                    </tr>
                                    </tfoot>


                                </table>

                                <?php
                                $i++;

                            }
                        }
                    }
                    ?>

                </div><!-- #company-locations-inside -->
            </div><!-- #company-locations -->
        </div><!-- .company-location-wrap -->
    </div><!-- .metabox-holder -->

</div>