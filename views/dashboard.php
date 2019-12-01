<div class="wrap erp erp-company-single">
    <h2>
        <?php _e( 'Payroll Management System', 'rbs-erp' ); ?>
    </h2>

    <div class="metabox-holder company-accounts">

        <div class="row">
            <div class="col-sm-4">
                <a href="<?php echo admin_url( 'admin.php?page=payroll-addition' )?>" class="tile purple">
                    <?php $additionObj= new Addition();?>
                    <h3 class="title"><?php echo $additionObj->active_record_count(1)?></h3>
                    <p>Number of Additions</p>
                </a>
            </div>
            <div class="col-sm-4">
                <a href="<?php echo admin_url( 'admin.php?page=payroll-deduction' )?>" class="tile orange">
                    <?php $deductionObj= new Deduction();?>
                    <h3 class="title"><?php echo $deductionObj->active_record_count(1)?></h3>
                    <p>Number of Deductions</p>
                </a>
            </div>
            <!--<div class="col-sm-4">
                <?php
/*                    if(isset($_POST['allowance_deduction_item_update_by_basic_salary'])){
                        global $wpdb;
                        $table_prefix = $wpdb->prefix;
                        $table_name = $table_prefix . "rbs_erp_payroll_join_addition_employee";
                        $table_name_deduction = $table_prefix . "rbs_erp_payroll_join_deduction_employee";
                        $employeeObj= new Employee();
                        $employees = $employeeObj->getActiveEmployee();
                        $payrollBasicInfoObj = new Payroll_Basic_Info();
                        foreach ($employees as $employee){

                            $payrollBasicInfo= $payrollBasicInfoObj->getActivePayrollBasicInfoByEmployeeId($employee->id);
                            if($payrollBasicInfo){
                                $basicSalary = $payrollBasicInfo->basic_salary;
                                $joinAllowanceItemsByEmployeeId = $payrollBasicInfoObj->getAllowanceItemByEmpolyee($employee->id);
                                $joinDeductionItemsByEmployeeId = $payrollBasicInfoObj->getDeductionItemByEmpolyee($employee->id);

                                if ($joinAllowanceItemsByEmployeeId){
                                    foreach ($joinAllowanceItemsByEmployeeId as $value){

                                        $data = array();
                                        $data['calculated_amount'] = ($value->amount_type=='percentage')? ($basicSalary * $value->addition_amount)/100: $value->addition_amount;
                                        $wpdb->update(
                                            $table_name,$data,array('id'=> $value->id )
                                        );
                                    }
                                }

                                if ($joinDeductionItemsByEmployeeId){
                                    foreach ($joinDeductionItemsByEmployeeId as $value){

                                        $data = array();
                                        $data['calculated_amount'] = ($value->amount_type=='percentage')? ($basicSalary * $value->deduction_amount)/100: $value->deduction_amount;
                                        $wpdb->update(
                                            $table_name_deduction,$data,array('id'=> $value->id )
                                        );
                                    }
                                }
                            }

                        }

                    }

                */?>
                <form action="" method="post">
                    <input type="submit" class="btn btn-danger" name="allowance_deduction_item_update_by_basic_salary" value="Sync Database Update">
                </form>
            </div>-->

        </div>

    </div><!-- .metabox-holder -->

</div>
