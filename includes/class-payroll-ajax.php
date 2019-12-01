<?php

/**
 * The ajax handler class
 *
 * Handles the requests from core ERP, not from modules
 */
class Payroll_Ajax {
    /**
     * Bind events
     */
    public function __construct() {


        add_action('wp_ajax_on_change_load_addition_info', array($this, 'getOnChangeAdditionInfo'));
        add_action('wp_ajax_remove_join_addition_item', array($this, 'removeJoinAdditionItem'));

        add_action('wp_ajax_on_change_load_deduction_info', array($this, 'getOnChangeDeductionInfo'));
        add_action('wp_ajax_remove_join_deduction_item', array($this, 'removeJoinDeductionItem'));

        add_action('wp_ajax_on_click_load_employee_info', array($this, 'getEmployeeByCompanyAndBranch'));
        add_action('wp_ajax_on_click_remove_employee_from_pay_calendar', array($this, 'removeEmployeeFromPayCalendar'));

    }

    function getOnChangeAdditionInfo(){
        if(!empty($_POST["addition_id"])) {
            global $wpdb;
            $addition_id = $_POST["addition_id"];

            $results = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix .'rbs_erp_payroll_additions WHERE status=%d AND id=%d',1, $addition_id));

            $return = array(
                'addition_amount'=>$results->addition_amount,
                'amount_type'   => $results->amount_type
                );
            wp_send_json($return);
        }else{
            return false;
        }die();
    }

    function getOnChangeDeductionInfo(){
        if(!empty($_POST["deduction_id"])) {
            global $wpdb;
            $deduction_id = $_POST["deduction_id"];

            $results = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix .'rbs_erp_payroll_deductions WHERE status=%d AND id=%d',1, $deduction_id));

            $return = array(
                'deduction_amount'=>$results->deduction_amount,
                'amount_type'   => $results->amount_type
                );
            wp_send_json($return);
        }else{
            return false;
        }die();
    }

    function removeJoinAdditionItem(){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_join_addition_employee";
        $id = $_POST['dataId'];
        $wpdb->delete(
            $table_name,
            [ 'id' => $id ],
            [ '%d' ]
        );
        die();
    }

    function removeJoinDeductionItem(){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_join_deduction_employee";
        $id = $_POST['dataId'];
        $wpdb->delete(
            $table_name,
            [ 'id' => $id ],
            [ '%d' ]
        );
        die();
    }

    function getEmployeeByCompanyAndBranch() {

        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_employees";
        $join_table_department = $table_prefix . "rbs_erp_departments";
        $join_table_designation = $table_prefix . "rbs_erp_designations";
        $company_id = $_POST["company_id"];
        $branch_id = $_POST["branch_id"];
        $calender_id = isset($_POST["pay_calender_id"])? $_POST["pay_calender_id"]:'';

        //var_dump($calender_id);

        $sql = "SELECT *, employee.id AS emId FROM {$table_name} AS employee
                  INNER JOIN {$join_table_department} department ON employee.department_id = department.id
                  INNER JOIN {$join_table_designation} designation ON employee.designation_id = designation.id";

        $sql .=' WHERE employee.status=1';

        if ( ! empty( $company_id && $company_id !='-1') ) {
            $sql .= ' AND employee.company_id='.$company_id;

        }
        if ( ! empty( $branch_id) && $branch_id !='-1' ) {
            $sql .= ' AND branch_id='.$branch_id;

        }
        $result = $wpdb->get_results( $sql );

        $exist_employee= $this->getAddedEmployeeWithPayCalendar($calender_id);
//        print_r($exist_employee);die;

        foreach ($result as $value){
            if (!in_array($value->emId, $exist_employee)){
            ?>
            <tr>
                <td><input checked name="selected_employee_id[]" type="checkbox" value="<?php echo $value->emId;?>"></td>
                <td><?php echo $value->employee_name;?></td>
                <td><?php echo $value->email;?></td>
                <td><?php echo $value->department_name;?></td>
                <td><?php echo $value->designation_name;?></td>
                <td></td>
            </tr>
    <?php }else{?>
                <tr>
                    <td></td>
                    <td><?php echo $value->employee_name;?></td>
                    <td><?php echo $value->email;?></td>
                    <td><?php echo $value->department_name;?></td>
                    <td><?php echo $value->designation_name;?></td>
                    <td></td>
                </tr>
                <?php
            }
        }
        die;
    }

    function removeEmployeeFromPayCalendar(){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_join_pay_calendar_employee";
        $id = $_POST['pceId'];
        $wpdb->delete(
            $table_name,
            [ 'id' => $id ],
            [ '%d' ]
        );
        die();
    }

    function getAddedEmployeeWithPayCalendar($id){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_join_pay_calendar_employee";
        $results = $wpdb->get_results($wpdb->prepare('SELECT employee_id FROM ' . $table_name .' WHERE pay_calendar_id=%d', $id));
//        return $results;
        $data = [];
        foreach ($results as $result){
            $data[]=$result->employee_id;
        }
        return $data;
    }



}

new Payroll_Ajax();
