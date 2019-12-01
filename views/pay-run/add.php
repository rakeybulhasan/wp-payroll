<div class="wrap rbs rbs-company-single">
    <h2><?php _e( 'Pay Run', 'rbs-erp' ); ?></h2>
    <?php

    if (isset($_GET['status'])&& $_GET['status'] == 'submitted' ) {
        erp_html_show_notice( __( 'Pay Calendar has been created successfully.', 'rbs-erp' ) );
    } elseif(isset($_GET['status'])&& $_GET['status']=='error' && isset($_GET['error'])) {

        erp_html_show_notice(isset($_GET['error'])?$_GET['error']:'', 'error' );
    }

    ?>
    <form class="form-horizontal" action="" method="post" id="erp-new-pay-calendar-run">
        <div class="row rbs-single-container">

            <div class="col-md-9 print_hidden">

                <div class="postbox company-postbox">
                    <h3 class="hndle"><span><?php _e( 'Pay Run Information', 'rbs-erp' ); ?></span></h3>
                    <div class="inside">

                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group required">
                                    <label class="control-label col-sm-5" for="from_date"><?php _e('From Date','rbs-erp');?></label>
                                    <div class="col-sm-7">
                                        <?php erp_html_form_input(array(
                                            'name'  => 'from_date',
                                            'type'  => 'text',
                                            'value' => '',
                                            'class' => 'form-control date-field',
                                            'required' => true,
                                            'custom_attr'=> array('autocomplete' => 'off'),
                                        )); ?>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    <label class="control-label col-sm-5" for="payment_date"><?php _e('Payment Date','rbs-erp');?></label>
                                    <div class="col-sm-7">
                                        <?php erp_html_form_input(array(
                                            'name'  => 'payment_date',
                                            'type'  => 'text',
                                            'value' => '',
                                            'class' => 'form-control date-field',
                                            'required' => true,
                                            'custom_attr'=> array('autocomplete' => 'off'),
                                        )); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">

                                <div class="form-group required">
                                    <label class="control-label col-sm-4" for="to_date"><?php _e('To Date','rbs-erp');?></label>
                                    <div class="col-sm-8">
                                        <?php erp_html_form_input(array(
                                            'name'  => 'to_date',
                                            'type'  => 'text',
                                            'value' => '',
                                            'class' => 'form-control date-field',
                                            'required' => true,
                                            'custom_attr'=> array('autocomplete' => 'off'),
                                        )); ?>
                                    </div>
                                </div>

                                <div class="form-group required">
                                    <label class="control-label col-sm-4" for="payment_type"><?php _e('Payment Type','rbs-erp');?></label>
                                    <div class="col-sm-8">
                                        <?php erp_html_form_input(array(
                                            'name'  => 'payment_type',
                                            'type'  => 'select',
                                            'value' => 'Full_PAYMENT',
                                            'required' => true,
                                            'options'   => array('FULL_PAYMENT'=>'Full Payment', 'PARTIAL_PAYMENT'=>'Partial Payment', 'BONUS_PAYMENT'=>'Bonus', 'SALARY_ARREAR'=>'Salary Arrear', 'BONUS_ARREAR'=>'Bonus Arrear')
                                        )); ?>
                                    </div>
                                </div>
                                <div class="form-group bonus_name_section" style="display: none">
                                    <label class="control-label col-sm-4" for="bonus_name"><?php _e('Payment Type','rbs-erp');?></label>
                                    <div class="col-sm-8">
                                        <?php erp_html_form_input(array(
                                            'name'  => 'bonus_name',
                                            'type'  => 'radio',
                                            'value' => '',
                                            'class' => 'form-control bonus_name',
                                            'options'   => array('EID_UL_FITR'=>'Eid ul Fitr', 'EID_UL_ADHA'=>'Eid ul Adha')
                                        )); ?>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div><!-- .inside -->

                </div><!-- .postbox -->


            </div><!-- .erp-area-left -->
            <div class="col-md-3 print_hidden">
                <div class="postbox company-postbox">
                    <h3 class="hndle"><span><?php _e( 'Actions', 'rbs-erp' ); ?></span></h3>
                    <div class="inside">
                        <div class="submitbox" id="submitbox">
                            <div id="major-publishing-actions">

                                <div id="publishing-action">

                                    <?php wp_nonce_field( 'new-pay-calendar-run-add' ); ?>
                                    <input type="hidden" name="rbs-erp-action" value="pay_calendar_run_new">
                                    <input type="hidden" name="pay_calendar_id" value="<?php echo $id;?>">
                                    <input type="hidden" name="exists_pay_calendar_run_name" value="">
                                    <input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php echo __( 'Create', 'rbs-erp' ); ?>">
                                </div>

                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="postbox company-postbox" style="position: relative">
                    <h3 class="hndle"><span><?php _e( 'Active Employees', 'rbs-erp' ); ?></span></h3>
                    <div class="inside">
                        <div class="row">
                            <div class="col-md-12">
                                <?php
                                $payColObj= new Pay_Calendar();
                                $payCol = $payColObj->getPayCalendarById($id);

                                ?>
<!--                                <h3 style="text-align: center;margin-top: 0">--><?php //echo $payCol->pay_calendar_name;?><!--</h3>-->
                                <h4 style="text-align: center;margin-top: 0"><?php echo $payCol->company_name;?></h4>
                                <h4 style="text-align: center;margin-top: 0"><?php echo $payCol->branch_name;?></h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div id="company-locations-inside" class="print_visible">
                                <table class="table table-striped pay_calendar_setup">
                                    <thead>
                                    <tr>
                                        <td style="width: 10%">Employee </td>
                                        <td width="12%">Code </td>
<!--                                        <td>Department</td>-->
                                        <td>Designation</td>
                                        <td>Pay Basic</td>
                                        <td>Allowance</td>
                                        <td>Total Allowance</td>
                                        <td>Deduction</td>
                                        <td>Total Deduction</td>
                                        <td>Net Pay</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $employeeObj = new Employee();
                                    $payColRunObj= new Pay_Calendar_Run();
                                    $employees = $payColRunObj->getEmployeeByPayCalender($id);
                                    ?>
                                    <?php
                                    $all_total = 0;
                                        foreach ($employees as $employee){
                                            $adjustedAmountPerMonth = 0;
                                            if($employee->advanceYesNo==1 && $employee->total_advance_amount > $employee->total_adjusted_amount){
                                                $advance_due_balance = ($employee->total_advance_amount)-($employee->total_adjusted_amount);
                                                if($employee->adjusted_amount_per_month < $advance_due_balance){
                                                    $adjustedAmountPerMonth= isset($employee->adjusted_amount_per_month)?$employee->adjusted_amount_per_month:0;
                                                }else{
                                                    $adjustedAmountPerMonth= isset($advance_due_balance)?$advance_due_balance:0;
                                                }

                                            }
                                            $basicSalary = isset($employee->basic_salary)?(float)$employee->basic_salary:0;
                                            $addition_items = $employeeObj->getAllowanceByEmployee($employee->emId);
                                            $deduction_items = $employeeObj->getDeductionByEmployee($employee->emId);
                                            $addition= $payColRunObj->getAllowanceTotalByEmployee($employee->emId);
                                            $deduction= $payColRunObj->getDeductionTotalByEmployee($employee->emId);
                                            $addAmount = $addition?$addition[0]->addTotalAmount:0;
                                            $lessAmount = $deduction?$deduction[0]->totalAmount:0;
                                            $lessAmount = $lessAmount+$adjustedAmountPerMonth;
                                            $total =($employee->basic_salary + $addAmount) - $lessAmount;
                                            $all_total = $all_total+$total;
                                            ?>
                                            <tr>
                                                <td>
                                                    <input class="print_hidden" type="hidden" name="added_employee_id[]" value="<?php echo $employee->emId;?>">
                                                    <input class="print_hidden" type="hidden" name="employee_basic_salary[]" value="<?php echo ($employee->basic_salary)?$employee->basic_salary:0;?>">
                                                    <input class="print_hidden" type="hidden" name="bonus_ratio[]" value="<?php echo ($employee->bonus_ratio)?$employee->bonus_ratio:100;?>">
                                                    <input class="print_hidden" type="hidden" name="adjusted_amount_per_transaction[]" value="<?php echo $adjustedAmountPerMonth;?>">
                                                    <input class="print_hidden" type="hidden" name="advancePayID[]" value="<?php echo isset($employee->advancePayID)?$employee->advancePayID:'';?>">
                                                    <a class="print_hidden" href="<?php echo admin_url( 'admin.php?page=hr-employee&action=edit&id=' . $employee->emId );?>">
                                                        <?php echo $employee->employee_name;?>
                                                    </a>
                                                    <span class="test"><?php echo $employee->employee_name;?></span>
                                                </td>
                                                <td><?php echo $employee->code;?></td>
<!--                                                <td>--><?php //echo $employee->department_name;?><!--</td>-->
                                                <td><?php echo $employee->designation_name;?></td>
                                                <td><?php echo number_format($basicSalary,2,'.',',')?></td>
                                                <td><?php echo $addition_items; ?></td>
                                                <td><?php echo number_format($addAmount,2,'.',','); ?></td>
                                                <td><?php echo $deduction_items; ?></td>
                                                <td><?php echo number_format($lessAmount,2,'.',','); ?></td>
                                                <td><?php echo number_format($total,2,'.',',');  ?></td>
                                            </tr>
                                    <?php

                                        }
                                    ?>

                                    <tr>
                                        <td colspan="8" style="text-align: right">Total Amount</td>
                                        <td><?php echo number_format($all_total,2,'.',',');?></td>
                                    </tr>

                                    </tbody>
                                </table>
                                </div>
                            </div>

                        </div>

                    </div><!-- .inside -->

                </div><!-- .postbox -->
            </div>

        </div><!-- .erp-single-container -->
    </form>
</div>