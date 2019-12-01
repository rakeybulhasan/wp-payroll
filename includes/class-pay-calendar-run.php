<?php

/**
 * Pay_Calendar_Run class
 */
class Pay_Calendar_Run {

    public function __construct()
    {

    add_action('pay_calendar_run_create', array($this,'create_pay_calendar_run_details'));
    }

    public function exist_pay_calendar_run($pay_cal_id, $fromDate, $toDate, $paymentType, $bonus_name){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar_run";
        if($paymentType=='PARTIAL_PAYMENT'){
            $sql = "select * from ".$table_name." where pay_calendar_id=".$pay_cal_id." AND payment_type='".$paymentType."' AND (from_date <= '".getMonthStringToDate($fromDate,"Y-m-d")."' OR to_date <= '".getMonthStringToDate($toDate,"Y-m-d")."')";
        }elseif ($paymentType=='BONUS_PAYMENT'){
            $sql = "select * from ".$table_name." where pay_calendar_id=".$pay_cal_id." AND payment_type='".$paymentType."' AND bonus_name='".$bonus_name."' AND YEAR(from_date) = '".getMonthStringToDate($fromDate,"Y")."' AND YEAR(to_date) = '".getMonthStringToDate($fromDate,"Y")."'";
        }else{
            $sql = "select * from ".$table_name." where pay_calendar_id=".$pay_cal_id." AND payment_type='".$paymentType."' AND YEAR(from_date) = '".getMonthStringToDate($fromDate,"Y")."' AND MONTH(from_date) = '".getMonthStringToDate($fromDate)."' AND YEAR(to_date) = '".getMonthStringToDate($fromDate,"Y")."' AND MONTH(to_date) = '".getMonthStringToDate($toDate)."'";
        }

        $result = $wpdb->get_row($sql);
        return $result;

    }

    public function payCalRunDetailsByEmpIdLimitOne($empId, $calId){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar_run_details";
        $join_table_name = $table_prefix . "rbs_erp_payroll_pay_calendar_run";

        $sql = "SELECT * FROM ".$table_name." AS payDetails JOIN ".$join_table_name." payrun ON payDetails.pay_calendar_run_id= payrun.id WHERE employee_id=".$empId." AND payrun.pay_calendar_id=".$calId." AND payrun.payment_type='FULL_PAYMENT'  ORDER by payDetails.id DESC LIMIT 1";
        $result = $wpdb->get_row($sql);
        return $result;
    }



    public function create_pay_calendar_run($data){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar_run";

        if($wpdb->insert(
            $table_name,$data
        )){
            $inserted_pay_cal_run_id = $wpdb->insert_id;
            do_action('pay_calendar_run_create', $wpdb->insert_id );
            return $inserted_pay_cal_run_id;
        }

        return false;
    }

    public function create_pay_calendar_run_details($pay_cal_run_id){

        global $wpdb;
        $advancePaymentObj = new Advance_Payment();
        $employeeObj = new Employee();
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar_run_details";
        $advance_payment_table_name = $table_prefix . "rbs_erp_advance_payments_info";
        $employees= isset($_POST['added_employee_id'])?$_POST['added_employee_id']:null;
        $employee_basic_salary= isset($_POST['employee_basic_salary'])?$_POST['employee_basic_salary']:0;
        $bonus_ratio= isset($_POST['bonus_ratio'])?$_POST['bonus_ratio']:0;
        $adjusted_amount_per_transaction= isset($_POST['adjusted_amount_per_transaction'])?$_POST['adjusted_amount_per_transaction']:0;
        $advancePayID= isset($_POST['advancePayID'])?$_POST['advancePayID']:'';
        $paymentType = isset($_POST['payment_type'])?$_POST['payment_type']:'';
        $number_of_days = cal_days_in_month(CAL_GREGORIAN, getMonthStringToDate($_POST['from_date'],'m'), getMonthStringToDate($_POST['from_date'],'Y'));

        $payCalId = $_POST['pay_calendar_id'];

        $payrollBasicInfoObj = new Payroll_Basic_Info();

        foreach ($employees as $key=>$employee){
            $employeeById = $employeeObj->getEmployeeById($employee);



            $emp_basic_salary = $employee_basic_salary[$key];
            $bonus_ratio_amount = $bonus_ratio[$key];

            $adjustedAmount = 0;
            if($advancePayID[$key]){
                $advancePayment = $advancePaymentObj->getActiveAdvancePaymentInfoById($advancePayID[$key]);
                if($paymentType=='FULL_PAYMENT' && $advancePayment->status==1 && $advancePayment->total_advance_amount>$advancePayment->total_adjusted_amount && getMonthStringToDate($advancePayment->adjusted_starting_date,'Y-m') <= getMonthStringToDate($_POST['from_date'],'Y-m')){
                    $adjustedAmount = $adjusted_amount_per_transaction[$key];
                }
            }

            if($paymentType=='BONUS_PAYMENT'){
                $allowance = json_encode(array());
                $deduction = json_encode(array());
            }elseif ($paymentType=='SALARY_ARREAR'){
                $monthCount = getMonthCount($_POST['from_date'], $_POST['to_date']);
//                $prev_payrun_details = $this->payCalRunDetailsByEmpIdLimitOne($employee, $payCalId);
                $payrollInfoHistoryObj = $payrollBasicInfoObj->getActivePayrollBasicInfoHistoryByEmployeeId($employee);

                if($payrollInfoHistoryObj){
                    $prev_basic_salary = $payrollInfoHistoryObj[0]->previous_basic_salary;

                    $diff_basic_salary = ($employee_basic_salary[$key]-$prev_basic_salary);

                }else{
                    $diff_basic_salary = 0;
                }
                /*$prev_basic_salary = $payrollInfoHistoryObj[0]->previous_basic_salary;

                $diff_basic_salary = ($employee_basic_salary[$key]-$prev_basic_salary);*/

                $emp_basic_salary = $diff_basic_salary * $monthCount;

                $allowanceItems = $payrollBasicInfoObj->getAllowanceItemByEmpolyee($employee);

                $prev_allowance = $this->getPreviousAllowanceAmountById($employee, $diff_basic_salary);

                $arrayAllow = array();


                foreach ($allowanceItems as $allowanceItem){
                    if($allowanceItem->amount_type=='hour'&& strtoupper($allowanceItem->addition_name)=='OVERTIME'){
                        $arrayAllow[$allowanceItem->addition_id]= number_format(((($diff_basic_salary/$number_of_days)/8)*2)*$allowanceItem->addition_amount,2,'.','');
                    }elseif ($allowanceItem->amount_type=='percentage'){

                        $arrayAllow[$allowanceItem->addition_id]=(($diff_basic_salary*$allowanceItem->addition_amount)/100)*$monthCount;

                    }else{
                        $arrayAllow[$allowanceItem->addition_id]=($prev_allowance[$allowanceItem->addition_id])?(($allowanceItem->calculated_amount - $prev_allowance[$allowanceItem->addition_id])*$monthCount):0;
                    }

                }

                $allowance = json_encode($arrayAllow);
                $deduction = json_encode(array());
            }elseif ($paymentType=='BONUS_ARREAR'){
                $monthCount = getMonthCount($_POST['from_date'], $_POST['to_date']);

                $payrollInfoHistoryObj = $payrollBasicInfoObj->getActivePayrollBasicInfoHistoryByEmployeeId($employee);
                if($payrollInfoHistoryObj){
                    $prev_basic_salary = $payrollInfoHistoryObj[0]->previous_basic_salary;

                    $diff_basic_salary = ($employee_basic_salary[$key]-$prev_basic_salary);

                }else{
                    $diff_basic_salary = 0;
                }

                $emp_basic_salary = $diff_basic_salary * $monthCount;

                $allowance = json_encode(array());
                $deduction = json_encode(array());
            }else{
               $allowance = $this->getAllowanceByEmployeeForJson($employee, $number_of_days, $emp_basic_salary);
               $deduction = $this->getDeductionByEmployeeForJson($employee);
            }
            $data = array(
                'pay_calendar_run_id'=> $pay_cal_run_id,
                'employee_id'=> $employee,
                'employee_code'=> $employeeById->employee_id,
                'employee_company_id'=> $employeeById->company_id,
                'employee_branch_id'=> $employeeById->branch_id,
                'employee_department_id'=> $employeeById->department_id,
                'employee_designation_id'=> $employeeById->designation_id,
                'payroll_info_basic_salary'=> $emp_basic_salary,
                'basic_salary'=> ($paymentType=='BONUS_PAYMENT' || $paymentType=='BONUS_ARREAR')? ($emp_basic_salary*$bonus_ratio_amount)/100 : $emp_basic_salary,
                'adjusted_amount_per_transaction'=> $adjustedAmount,
                'rate_allowance'=> $allowance,
                'allowance'=> $allowance,
                'rate_deduction'=> $deduction,
                'deduction'=> $deduction,

            );
            $wpdb->insert(
                $table_name,$data
            );

            if($paymentType=='FULL_PAYMENT' && !empty($advancePayID[$key]) && $adjusted_amount_per_transaction[$key]>0){

                $advance_payment_data = array(
                   'total_adjusted_amount'=> $advancePayment->total_adjusted_amount + $adjusted_amount_per_transaction[$key],
                   'transaction_count'=> $advancePayment->transaction_count + 1
                );
                if($advancePayment->status==1 && $advancePayment->total_advance_amount>$advancePayment->total_adjusted_amount && getMonthStringToDate($advancePayment->adjusted_starting_date,'Y-m') <= getMonthStringToDate($_POST['from_date'],'Y-m')){
                    $wpdb->update(
                        $advance_payment_table_name,
                        $advance_payment_data,
                        array('id'=>$advancePayID[$key])
                    );
                }

            }
        }
        wp_safe_redirect(admin_url( 'admin.php?page=pay-run&tab=allowance-deduction-input&id='.$pay_cal_run_id.'&status=submitted' ));
        die;
    }

    private function getPreviousAllowanceAmountById($employeeId, $differentBasicSalary){
        $payrollBasicInfoObj = new Payroll_Basic_Info();
        $arrayData = array();
        $payrollAllowanceHistoryObj = $payrollBasicInfoObj->getActivePreviousAllowanceHistoryByEmployeeId($employeeId);
        if($payrollAllowanceHistoryObj){
            $prev_allow_jsonDecode =  json_decode($payrollAllowanceHistoryObj[0]->previous_allowance,true);
            foreach ($prev_allow_jsonDecode as $item){
                $arrayData[$item['id']]= $item['type']=='currency'? $item['amount']:($differentBasicSalary*$item['amount'])/100;
            }
        }

        return $arrayData;

    }

    private function getAllowanceByEmployeeForJson($employee_id, $noOfDay, $basicSalary){
        $payrollBasicInfoObj = new Payroll_Basic_Info();
        $allowanceItems = $payrollBasicInfoObj->getAllowanceItemByEmpolyee($employee_id);
        $array = array();
        foreach ($allowanceItems as $allowanceItem){
            if($allowanceItem->amount_type=='hour'&& strtoupper($allowanceItem->addition_name)=='OVERTIME'){
                $array[$allowanceItem->addition_id]=number_format(((($basicSalary/$noOfDay)/8)*2)*$allowanceItem->addition_amount,2,'.','');
            }else{
                $array[$allowanceItem->addition_id]=$allowanceItem->calculated_amount;
            }

        }
        return json_encode($array);
    }

    private function getDeductionByEmployeeForJson($employee_id){
        $payrollBasicInfoObj = new Payroll_Basic_Info();
        $deductionItems = $payrollBasicInfoObj->getDeductionItemByEmpolyee($employee_id);
        $array = array();
        foreach ($deductionItems as $deductionItem){
            $array[$deductionItem->deduction_id]=$deductionItem->calculated_amount;
        }
        return json_encode($array);
    }

    public function getPayCalendarRunList($company_id, $branch_id){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar_run";
        $join_table_name = $table_prefix . "rbs_erp_payroll_pay_calendar_run_details";
        $join_payroll_pay_calendar_table_name = $table_prefix . "rbs_erp_payroll_pay_calendar";
        $join_table_company = $table_prefix . "rbs_erp_companies";
        $join_table_branch = $table_prefix . "rbs_erp_branches";
        $sql = 'select *, pay_cal_run.id AS pId, COUNT(pay_cal_run_details.employee_id) AS employ_count from '.$table_name.' AS pay_cal_run';
        $sql .= ' JOIN '.$join_payroll_pay_calendar_table_name.' pay_cal ON pay_cal_run.pay_calendar_id=pay_cal.id';
        $sql .= ' JOIN '.$join_table_company.' company ON pay_cal.company_id = company.id';
        $sql .= ' LEFT JOIN '.$join_table_name.' pay_cal_run_details ON pay_cal_run.id=pay_cal_run_details.pay_calendar_run_id';
        $sql .= ' LEFT JOIN '.$join_table_branch.' branch ON pay_cal.branch_id = branch.id';
        if($company_id){
            $sql .= ' where pay_cal.company_id='.$company_id;
        }
        if($branch_id){
            $sql .= ' AND pay_cal.branch_id='.$branch_id;
        }
//        $sql .= ' where pay_cal_run.status=1';
        $sql .= ' GROUP BY pay_cal_run_details.pay_calendar_run_id';
        $sql .= ' ORDER BY pay_cal_run.id DESC';
        $results = $wpdb->get_results( $sql);
        return $results;
    }


  public function getEmployeeByPayCalender( $id) {

        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar";
        $join_table_pay_calendar_employee = $table_prefix . "rbs_erp_payroll_join_pay_calendar_employee";
        $join_table_employee = $table_prefix . "rbs_erp_employees";
        $join_table_department = $table_prefix . "rbs_erp_departments";
        $join_table_designation = $table_prefix . "rbs_erp_designations";
        $join_table_payroll_info = $table_prefix . "rbs_erp_payroll_info";
        $join_table_advance_payment = $table_prefix . "rbs_erp_advance_payments_info";

        $sql = "SELECT *, pay_cal.id AS pcId, pay_cal.company_id AS comId, pay_cal.branch_id AS brId, 
                employee.id AS emId, employee.employee_id AS code, pay_cal_emp.id AS pceId,
                 advance_payment.id AS advancePayID, advance_payment.status AS advanceYesNo
                FROM {$table_name} AS pay_cal
                INNER JOIN {$join_table_pay_calendar_employee} pay_cal_emp ON pay_cal.id = pay_cal_emp.pay_calendar_id
                INNER JOIN {$join_table_employee} employee ON pay_cal_emp.employee_id = employee.id
                INNER JOIN {$join_table_department} department ON employee.department_id = department.id
                INNER JOIN {$join_table_designation} designation ON employee.designation_id = designation.id
                LEFT JOIN {$join_table_payroll_info} payroll_info ON employee.id = payroll_info.employee_id
                LEFT JOIN {$join_table_advance_payment} advance_payment ON employee.id = advance_payment.employee_id";

        $sql .=' WHERE pay_cal.status=1 AND employee.status=1 AND pay_cal.id='.$id;
        $sql .=' GROUP BY pay_cal_emp.employee_id';
        $sql .=' ORDER BY employee.sorting_order_by ASC';

        $results = $wpdb->get_results( $sql );

       return $results;

    }


  public function generatePaySlipByPayCalenderRunId($id, $employee_id=null) {

        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar_run";
        $join_table_pay_calendar_employee = $table_prefix . "rbs_erp_payroll_pay_calendar_run_details";
        $join_table_pay_calendar = $table_prefix . "rbs_erp_payroll_pay_calendar";
        $join_table_employee = $table_prefix . "rbs_erp_employees";
        $join_table_department = $table_prefix . "rbs_erp_departments";
        $join_table_designation = $table_prefix . "rbs_erp_designations";
        $join_table_company = $table_prefix . "rbs_erp_companies";
        $join_table_branch = $table_prefix . "rbs_erp_branches";
        $join_table_payroll_info = $table_prefix . "rbs_erp_payroll_info";
        $join_table_advance_payment = $table_prefix . "rbs_erp_advance_payments_info";

        $sql = "SELECT *, pay_cal_run.id AS pcId,
                employee.employee_id AS code,
                pay_cal.pay_calendar_name AS pcName,
                pay_cal_run_details.basic_salary AS basicSalary,
                pay_cal_run_details.adjusted_amount_per_transaction AS adjustedAmount,
                advance_payment.total_advance_amount AS totalAdvPayment, advance_payment.total_adjusted_amount AS totalAdjAmount,
                employee.id AS emId
                FROM {$table_name} AS pay_cal_run
                INNER JOIN {$join_table_pay_calendar_employee} pay_cal_run_details ON pay_cal_run.id = pay_cal_run_details.pay_calendar_run_id
                INNER JOIN {$join_table_pay_calendar} pay_cal ON pay_cal.id = pay_cal_run.pay_calendar_id
                INNER JOIN {$join_table_employee} employee ON pay_cal_run_details.employee_id = employee.id
                INNER JOIN {$join_table_department} department ON pay_cal_run_details.employee_department_id = department.id
                INNER JOIN {$join_table_designation} designation ON pay_cal_run_details.employee_designation_id = designation.id
                INNER JOIN {$join_table_company} company ON pay_cal_run_details.employee_company_id = company.id
                INNER JOIN {$join_table_branch} branch ON pay_cal_run_details.employee_branch_id = branch.id
                LEFT JOIN {$join_table_payroll_info} payroll_info ON employee.id = payroll_info.employee_id
                LEFT JOIN {$join_table_advance_payment} advance_payment ON employee.id = advance_payment.employee_id";

        $sql .=' WHERE pay_cal_run.id='.$id;
        if ($employee_id){
            $sql.= ' AND employee.id='.$employee_id;
        }
      $sql .=' ORDER BY employee.sorting_order_by ASC';
//        $sql .=' GROUP BY pay_cal_run_details.employee_id';

        $results = $wpdb->get_results( $sql );

       return $results;

    }

    public function getAllowanceTotalByEmployee($employee_id){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_join_addition_employee";

        $sql = "SELECT employee_id, SUM(calculated_amount) AS addTotalAmount FROM {$table_name} WHERE employee_id={$employee_id} GROUP BY employee_id";
        $results = $wpdb->get_results( $sql );

        return $results;
    }
    public function getDeductionTotalByEmployee($employee_id){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_join_deduction_employee";

        $sql = "SELECT employee_id, SUM(calculated_amount) AS totalAmount FROM {$table_name} WHERE employee_id={$employee_id} GROUP BY employee_id";
        $results = $wpdb->get_results( $sql );

        return $results;
    }



}

new Pay_Calendar_Run();
