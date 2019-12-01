<?php

/**
 * Payroll_Basic_Info class
 */
class Payroll_Basic_Info {

    public function __construct()
    {
        add_action( 'hr_employee_create', array($this, 'create_payroll') );
        add_action( 'hr_employee_update', array($this, 'create_payroll') );

        add_action( 'hr_payroll_info_create', array($this, 'create_payroll_info_history') );
        add_action( 'hr_payroll_info_update', array($this, 'create_payroll_info_history') );

        add_action( 'hr_payroll_info_create', array($this, 'joinAdditionWithEmployee') );
        add_action( 'hr_payroll_info_update', array($this, 'joinAdditionWithEmployee') );

        add_action( 'hr_payroll_info_create', array($this, 'joinDeductionWithEmployee') );
        add_action( 'hr_payroll_info_update', array($this, 'joinDeductionWithEmployee') );
    }

    public function create_payroll($employeeid){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_info";
        $post = $_POST;
        $data = array();
        $data['employee_id'] = $employeeid;
//        $data['tax_number'] = $post['tax_number'];
        $data['total_salary'] = $post['total_salary'];
        $data['basic_salary'] = $post['basic_salary'];
        $data['bonus_ratio'] = ($post['bonus_ratio']!='')?$post['bonus_ratio']:null;
        $data['payment_method'] = $post['payment_method'];
        $data['bank_account_number'] = $post['bank_account_number'];
        $data['bank_account_name'] = $post['bank_account_name'];
        $data['bank_name'] = $post['bank_name'];
        $data['bank_branch_name'] = $post['bank_branch_name'];
        $data['status'] = 1;
        if($this->getActivePayrollBasicInfoByEmployeeId($employeeid)){
            $data['updated_at'] = date("Y-m-d H:i:s");
            if($wpdb->update(
                $table_name, $data,
                array('id'=>$post['payroll_basic_info_id'])
            )){
                do_action('hr_payroll_info_update', $data['employee_id'] );
                return true;
            }else{
                return false;
            }
        }else{
            $data['created_at'] = date("Y-m-d H:i:s");
            if($wpdb->insert(
                $table_name,$data
            )){
                $inserted_payroll_info_id = $wpdb->insert_id;
                do_action('hr_payroll_info_create', $data['employee_id'] );
                return $inserted_payroll_info_id;
            }
        }

        return false;
    }

    public function create_payroll_info_history($employeeid){
        $current_user = wp_get_current_user();
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_info_history";
        $table_name_allowance_history = $table_prefix . "rbs_erp_payroll_join_addition_employee_history";
        $table_name_deduction_history = $table_prefix . "rbs_erp_payroll_join_deduction_employee_history";
        $post = $_POST;

        $previousAllowanceHistory = $this->getActivePreviousAllowanceHistoryByEmployeeId($employeeid);
        $previousDeductionHistory = $this->getActivePreviousDeductionHistoryByEmployeeId($employeeid);

        $previousAllowance = count(json_decode($previousAllowanceHistory[0]->previous_allowance));
        $previousDeduction = count(json_decode($previousDeductionHistory[0]->previous_deduction));

        $selected_addition_id = isset($_POST['selected_addition_id'])?$_POST['selected_addition_id']:array();
        $selected_addition_amount = isset($_POST['selected_addition_amount'])?$_POST['selected_addition_amount']:array();
        $selected_amount_type = isset($_POST['selected_amount_type'])?$_POST['selected_amount_type']:array();

        $selectedAllowance = array();
        foreach ($selected_addition_id as $selectKeyAllow=>$selectValueAllow){
            $selectedAllowance[]=array(
                'id'=>$selectValueAllow,
                'amount'=>$selected_addition_amount[$selectKeyAllow],
                'type'=>$selected_amount_type[$selectKeyAllow]
            );
        }

        $added_allowance_item = isset($_POST['added_allowance_item_id'])?$_POST['added_allowance_item_id']:array();
        $added_allowance_amount = isset($_POST['added_allowance_amount'])?$_POST['added_allowance_amount']:array();
        $added_allowance_amount_type = isset($_POST['added_allowance_amount_type'])?$_POST['added_allowance_amount_type']:array();

        $addedAllowance = array();
        foreach ($added_allowance_item as $addKeyAllow=>$addValueAllow){
            $addedAllowance[]=array(
                'id'=>$addValueAllow,
                'amount'=>$added_allowance_amount[$addKeyAllow],
                'type'=>$added_allowance_amount_type[$addKeyAllow]
            );
        }

        $selected_deduction_id = isset($_POST['selected_deduction_id'])?$_POST['selected_deduction_id']:array();
        $selected_deduction_amount = isset($_POST['selected_deduction_amount'])?$_POST['selected_deduction_amount']:array();
        $selected_deduction_amount_type = isset($_POST['selected_deduction_amount_type'])?$_POST['selected_deduction_amount_type']:array();

        $selectedDeduction = array();
        foreach ($selected_deduction_id as $selectKeyDeduction=>$selectValueDeduction){
            $selectedDeduction[]=array(
                'id'=>$selectValueDeduction,
                'amount'=>$selected_deduction_amount[$selectKeyDeduction],
                'type'=>$selected_deduction_amount_type[$selectKeyDeduction]
            );
        }

        $added_deduction_item = isset($_POST['added_deduction_item_id'])?$_POST['added_deduction_item_id']:array();
        $added_deduction_amount = isset($_POST['added_deduction_amount'])?$_POST['added_deduction_amount']:array();
        $added_deduction_amount_type = isset($_POST['added_deduction_amount_type'])?$_POST['added_deduction_amount_type']:array();

        $addedDeduction = array();
        foreach ($added_deduction_item as $addKeyDeduction=>$addValueDeduction){
            $addedDeduction[]=array(
                'id'=>$addValueDeduction,
                'amount'=>$added_deduction_amount[$addKeyDeduction],
                'type'=>$added_deduction_amount_type[$addKeyDeduction]
            );
        }

        $allAllowance = array_merge($selectedAllowance, $addedAllowance);
        $allDeduction = array_merge($selectedDeduction, $addedDeduction);
        $data = array();
        $update_data=array();
        $data['employee_id'] = $employeeid;
        $data['previous_basic_salary'] = $post['previous_basic_salary'];
        $data['current_basic_salary'] = $post['basic_salary'];

//        $data['previous_allowance'] = json_encode($allAllowance);
//        $data['previous_deduction'] = json_encode($allDeduction);

        $data['created_by'] = $current_user->ID;
        $data['created_at'] = date("Y-m-d H:i:s");

        if($post['previous_basic_salary']!=$post['basic_salary']){

            if($this->getActivePayrollBasicInfoHistoryByEmployeeId($employeeid)){
                $update_data['status'] = 0;
                $wpdb->update(
                    $table_name, $update_data,
                    array('employee_id'=>$employeeid)
                );
            }

            if($wpdb->insert(
                $table_name,$data
            )){
                $inserted_payroll_info_history_id = $wpdb->insert_id;
//                return $inserted_payroll_info_history_id;
            }else{
                return false;
            }
        }

        if(count($allAllowance)!=$previousAllowance){

            $arrayAllowance = array();
            $arrayUpdateAllowance = array();
            $arrayAllowance['employee_id'] = $employeeid;
            $arrayAllowance['previous_allowance'] = json_encode($allAllowance);

            $arrayAllowance['created_by'] = $current_user->ID;
            $arrayAllowance['created_at'] = date("Y-m-d H:i:s");


            if($this->getActivePreviousAllowanceHistoryByEmployeeId($employeeid)){
                $arrayUpdateAllowance['status'] = 0;
                $wpdb->update(
                    $table_name_allowance_history, $arrayUpdateAllowance,
                    array('employee_id'=>$employeeid)
                );
            }

            if($wpdb->insert(
                $table_name_allowance_history,$arrayAllowance
            )){
                $inserted_allowance_history_id = $wpdb->insert_id;
//                return $inserted_allowance_history_id;
            }else{
                return false;
            }
        }

        if(count($allDeduction)!=$previousDeduction){
            $arrayDeduction = array();
            $arrayUpdateDeduction = array();

            $arrayDeduction['employee_id'] = $employeeid;
            $arrayDeduction['previous_deduction'] = json_encode($allDeduction);

            $arrayDeduction['created_by'] = $current_user->ID;
            $arrayDeduction['created_at'] = date("Y-m-d H:i:s");
//        var_dump($arrayDeduction);die;
            if($this->getActivePreviousDeductionHistoryByEmployeeId($employeeid)){
                $arrayUpdateDeduction['status'] = 0;
                $wpdb->update(
                    $table_name_deduction_history, $arrayUpdateDeduction,
                    array('employee_id'=>$employeeid)
                );
            }

            if($wpdb->insert(
                $table_name_deduction_history,$arrayDeduction
            )){
                $inserted_deduction_history_id = $wpdb->insert_id;
                return $inserted_deduction_history_id;
            }else{
                return false;
            }
        }
        return false;
    }

    public function getActivePayrollBasicInfoByEmployeeId($id){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_info";

        $result = $wpdb->get_row('select * from '.$table_name.' where status=1 AND employee_id='.$id );
        return $result;
    }

    public function getActivePayrollBasicInfoHistoryByEmployeeId($id){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_info_history";

        $result = $wpdb->get_results('select * from '.$table_name.' where status=1 AND employee_id='.$id );
        return $result;
    }

    public function joinAdditionWithEmployee($employeeid){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_join_addition_employee";

        $selected_addition_id = $_POST['selected_addition_id'];
        $added_allowance_item = $_POST['added_allowance_item'];
        $basic_salary = $_POST['basic_salary'];
        if ($selected_addition_id){
            foreach ($selected_addition_id as $key=>$value){
                $data = array();
                $data['employee_id'] = $employeeid;
                $data['addition_id'] = $value;
                $data['addition_name'] = $_POST['selected_addition_name'][$key];
                $data['addition_amount'] = $_POST['selected_addition_amount'][$key];
                $data['amount_type'] = $_POST['selected_amount_type'][$key];
                $data['calculated_amount'] = ($_POST['selected_amount_type'][$key]=='percentage')? $this->convertPercentageToAmount($basic_salary,$_POST['selected_addition_amount'][$key]): $_POST['selected_addition_amount'][$key];
                $data['created_at'] = date("Y-m-d H:i:s");
                $wpdb->insert(
                    $table_name,$data
                );
            }
        }
        if ($added_allowance_item){
            foreach ($added_allowance_item as $key1=>$value){
                $data = array();
                $data['calculated_amount'] = ($_POST['added_allowance_amount_type'][$key1]=='percentage')? $this->convertPercentageToAmount($basic_salary,$_POST['added_allowance_amount'][$key1]): $_POST['added_allowance_amount'][$key1];
                $wpdb->update(
                    $table_name,$data,array('id'=> intval($value) )
                );
            }
        }

    }


    public function getActivePreviousAllowanceHistoryByEmployeeId($id){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_join_addition_employee_history";

        $result = $wpdb->get_results('select * from '.$table_name.' where status=1 AND employee_id='.$id .' ORDER BY id DESC LIMIT 1' );
        return $result;
    }

    public function getAllowanceItemByEmpolyee($employee_id){

        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_join_addition_employee";
        $result = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' .$table_name .' WHERE employee_id=%d', $employee_id));
        return $result;
    }

    public function joinDeductionWithEmployee($employeeid){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_join_deduction_employee";

        $selected_deduction_id = $_POST['selected_deduction_id'];
        $added_deduction_item = $_POST['added_deduction_item'];
        $basic_salary = $_POST['basic_salary'];
        if ($selected_deduction_id){
            foreach ($selected_deduction_id as $key=>$value){
                $data = array();
                $data['employee_id'] = $employeeid;
                $data['deduction_id'] = $value;
                $data['deduction_name'] = $_POST['selected_deduction_name'][$key];
                $data['deduction_amount'] = $_POST['selected_deduction_amount'][$key];
                $data['amount_type'] = $_POST['selected_deduction_amount_type'][$key];
                $data['calculated_amount'] = ($_POST['selected_deduction_amount_type'][$key]=='percentage')? $this->convertPercentageToAmount($basic_salary,$_POST['selected_deduction_amount'][$key]): $_POST['selected_deduction_amount'][$key];
                $data['created_at'] = date("Y-m-d H:i:s");
                $wpdb->insert(
                    $table_name,$data
                );
            }
        }
        if ($added_deduction_item){
            foreach ($added_deduction_item as $key1=>$value){
                $data = array();
                $data['calculated_amount'] = ($_POST['added_deduction_amount_type'][$key1]=='percentage')? $this->convertPercentageToAmount($basic_salary,$_POST['added_deduction_amount'][$key1]): $_POST['added_deduction_amount'][$key1];
                $wpdb->update(
                    $table_name,$data,array('id'=> intval($value) )
                );
            }
        }

    }

    public function getDeductionItemByEmpolyee($employee_id){

        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_join_deduction_employee";
        $result = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' .$table_name .' WHERE employee_id=%d', $employee_id));
        return $result;
    }

    public function getActivePreviousDeductionHistoryByEmployeeId($id){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_join_deduction_employee_history";

        $result = $wpdb->get_results('select * from '.$table_name.' where status=1 AND employee_id='.$id .' ORDER BY id DESC LIMIT 1' );
        return $result;
    }

    private function convertPercentageToAmount($basicAmount, $percentage){
        return ($basicAmount * $percentage)/100;
    }

    private function getOvertimeCalculateAmount($basicAmount, $percentage){
        return ($basicAmount * $percentage)/100;
    }


}

new Payroll_Basic_Info();
