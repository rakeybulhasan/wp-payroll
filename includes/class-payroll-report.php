<?php

/**
 * Payroll_Basic_Info class
 */
class Payroll_Report {



    public function generatePaySlipForReport($employee_id, $from_date, $to_date) {

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

            $sql.= ' WHERE employee.id='.$employee_id;

            if (!empty($from_date) && !empty($to_date)){
                $sql.= '  AND pay_cal_run.from_date >= "'.getMonthStringToDate($from_date,"Y-m-d").'" AND pay_cal_run.to_date <= "'.getMonthStringToDate($to_date,"Y-m-d").'"';
            }
//        $sql .=' GROUP BY pay_cal_run_details.employee_id';

        $results = $wpdb->get_results( $sql );

        return $results;

    }


    public function generateTopSheetSummery($company_id, $date, $branch_id) {

        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar_run";
        $join_table_pay_calendar_employee = $table_prefix . "rbs_erp_payroll_pay_calendar_run_details";
        $join_table_pay_calendar = $table_prefix . "rbs_erp_payroll_pay_calendar";
        $join_table_company = $table_prefix . "rbs_erp_companies";
        $join_table_branch = $table_prefix . "rbs_erp_branches";

        $sql = "SELECT pay_cal_run.id AS pcId,
                branch.id as bId,
                branch.branch_name as bName,
                pay_cal_run_details.allowance,
                pay_cal_run_details.deduction,
                pay_cal_run_details.adjusted_amount_per_transaction as adjustedAmount,
                pay_cal_run_details.basic_salary AS basicSalary
                FROM {$table_name} AS pay_cal_run
                INNER JOIN {$join_table_pay_calendar_employee} pay_cal_run_details ON pay_cal_run.id = pay_cal_run_details.pay_calendar_run_id
                INNER JOIN {$join_table_pay_calendar} pay_cal ON pay_cal.id = pay_cal_run.pay_calendar_id
                INNER JOIN {$join_table_company} company ON pay_cal.company_id = company.id
                INNER JOIN {$join_table_branch} branch ON pay_cal.branch_id = branch.id";

            $sql.= ' WHERE pay_cal.company_id='.$company_id;

            if (!empty($branch_id)){
                $sql.= '  AND pay_cal.branch_id IN ('.$branch_id.')';
            }
            if (!empty($date)){
                $sql.= '  AND pay_cal_run.from_date = "'.getMonthStringToDate($date,"Y-m-d").'"';
            }
//        $sql .=' GROUP BY pay_cal.branch_id';

        $results = $wpdb->get_results( $sql );

        $returnArray = array();
        foreach ($results as $topSheetSummery){

            $allowance_items_json = json_decode($topSheetSummery->allowance,true);
            $deduction_items_json = json_decode($topSheetSummery->deduction,true);
            $basic_pay = $topSheetSummery->basicSalary?$topSheetSummery->basicSalary:0;
            $adjustedAmount = $topSheetSummery->adjustedAmount?$topSheetSummery->adjustedAmount:0;
            $total_allowance = array_sum($allowance_items_json);
            $total_deduction = array_sum($deduction_items_json);
            $total_deduction = $total_deduction + $adjustedAmount;
            $total = $basic_pay + $total_allowance - $total_deduction;

            $returnArray[$topSheetSummery->bId]['branchName'] = $topSheetSummery->bName;
            $returnArray[$topSheetSummery->bId]['basicTotal'] += $basic_pay;
            $returnArray[$topSheetSummery->bId]['allowance'] += $total_allowance;
            $returnArray[$topSheetSummery->bId]['deduction'] += $total_deduction;
            $returnArray[$topSheetSummery->bId]['netTotal'] += $total;

        }
        return $returnArray;

    }

}

new Payroll_Report();
