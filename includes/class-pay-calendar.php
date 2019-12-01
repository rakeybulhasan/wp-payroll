<?php

/**
 * Pay_Calendar class
 */
class Pay_Calendar {

    public function __construct()
    {

        add_action( 'pay_calendar_create', array($this, 'joinPayCalendarWithEmployee') );
        add_action( 'pay_calendar_update', array($this, 'joinPayCalendarWithEmployee') );
//        add_action( 'hr_payroll_info_update', array($this, 'joinDeductionWithEmployee') );
    }

    public function getPayCalendar($calenderType='monthly'){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar";
        $join_table_name = $table_prefix . "rbs_erp_payroll_join_pay_calendar_employee";
        $join_table_employee = $table_prefix . "rbs_erp_employees";
        $join_table_company = $table_prefix . "rbs_erp_companies";
        $sql = 'select *, pay_cal.id AS pId, COUNT(pay_cal_employ.employee_id) AS employ_count from '.$table_name.' AS pay_cal';
        $sql .= ' LEFT JOIN '.$join_table_name.' pay_cal_employ ON pay_cal.id=pay_cal_employ.pay_calendar_id';
        $sql .= ' INNER JOIN '.$join_table_employee.' employee ON pay_cal_employ.employee_id=employee.id';
        $sql .= ' INNER JOIN '.$join_table_company.' company ON pay_cal.company_id=company.id';
        $sql .= ' where pay_cal.status=1 AND employee.status=1 AND pay_cal.pay_calendar_type='.'"'.$calenderType.'"';
        $sql .= ' GROUP BY pay_cal_employ.pay_calendar_id';
        $results = $wpdb->get_results( $sql);
        return $results;
    }

    public function getPayCalendarById($id){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar";
        $join_table_company = $table_prefix . "rbs_erp_companies";
        $join_table_branch = $table_prefix . "rbs_erp_branches";
        $sql = 'select *, pay_cal.id AS pId from '.$table_name.' AS pay_cal';
        $sql .= ' INNER JOIN '.$join_table_company.' company ON pay_cal.company_id=company.id';
        $sql .= ' INNER JOIN '.$join_table_branch.' branch ON company.id=branch.company_id';
        $sql .= ' where pay_cal.id='.$id;
        $results = $wpdb->get_row( $sql);
        return $results;
    }

    public function create_pay_calendar($data){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar";

        if($wpdb->insert(
            $table_name,$data
        )){
            $inserted_pay_cal_id = $wpdb->insert_id;
            do_action('pay_calendar_create', $wpdb->insert_id );
            return $inserted_pay_cal_id;
        }

        return false;
    }

    public function update_pay_calendar($data, $id){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar";

        if($wpdb->update(
            $table_name,
            $data,
            array(
                'id'=>$id
            )
        )){
            $updated_pay_cal_id = $id ;
            do_action('pay_calendar_update', $id );
            return $updated_pay_cal_id;
        }

        return false;
    }

    function getAddedEmployeeByPayCalender( $id) {

        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar";
        $join_table_pay_calendar_employee = $table_prefix . "rbs_erp_payroll_join_pay_calendar_employee";
        $join_table_employee = $table_prefix . "rbs_erp_employees";
        $join_table_department = $table_prefix . "rbs_erp_departments";
        $join_table_designation = $table_prefix . "rbs_erp_designations";


        $sql = "SELECT *, pay_cal.id AS pcId, pay_cal.company_id AS comId, pay_cal.branch_id AS brId, employee.id AS emId, pay_cal_emp.id AS pceId FROM {$table_name} AS pay_cal
                  LEFT JOIN {$join_table_pay_calendar_employee} pay_cal_emp ON pay_cal.id = pay_cal_emp.pay_calendar_id
                  INNER JOIN {$join_table_employee} employee ON pay_cal_emp.employee_id = employee.id
                  INNER JOIN {$join_table_department} department ON employee.department_id = department.id
                  INNER JOIN {$join_table_designation} designation ON employee.designation_id = designation.id";

        $sql .=' WHERE pay_cal.status=1 AND pay_cal.id='.$id;


        $result = $wpdb->get_results( $sql, ARRAY_A );
        $newBookInfo = [];
        $newBookKey = [];
        $newKey = 0;
        foreach($result as $bookKey => $bookValue){

            if(!in_array($bookValue["pcId"],$newBookKey)){
                ++$newKey;
                $newBookInfo["pcId"] = $bookValue["pcId"];
                $newBookInfo["pay_calendar_name"] = $bookValue["pay_calendar_name"];
                $newBookInfo["pay_calendar_type"] = $bookValue["pay_calendar_type"];
                $newBookInfo["company_id"] = $bookValue["comId"];
                $newBookInfo["branch_id"] = $bookValue["brId"];
            }
            $newBookInfo["employees"][$bookKey] = array('pceId'=>$bookValue["pceId"],'emId'=>$bookValue["emId"],'name'=>$bookValue["employee_name"],'email'=>$bookValue["email"],'department_name'=>$bookValue["department_name"],'designation_name'=>$bookValue["designation_name"]);
            $newBookKey[]  = $bookValue["pcId"];
        }
        return $newBookInfo;

    }

    public function joinPayCalendarWithEmployee($payCalendarId){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_join_pay_calendar_employee";

        $selected_employee = $_POST['selected_employee_id'];
        $calenderType = $_POST['pay_calendar_type'];

            $i=1;
            $data1 = array();
            foreach ($selected_employee as $key=>$value){

                $data1['employee_id'] = $value;
                $data1['pay_calendar_id'] = $payCalendarId;
                $wpdb->insert(
                    $table_name,$data1
                );

                $i++;
            }
            if ($calenderType=='individual'){
                wp_safe_redirect(admin_url( 'admin.php?page=pay-run&tab=employees&id='. $payCalendarId));
            }else{
                wp_safe_redirect(admin_url( 'admin.php?page=pay-calendar' ));
            }

        die;
    }

    public function delete_pay_calendar($id){
//        var_dump('ok');die;
        global $wpdb;
        global $error;
        // Instantiate the WP_Error object
        $error = new WP_Error();
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar";
        $join_table_name = $table_prefix . "rbs_erp_payroll_join_pay_calendar_employee";

        $table_name_pay_calender_run = $table_prefix . "rbs_erp_payroll_pay_calendar_run";

        $sql = "select * from ".$table_name_pay_calender_run." where pay_calendar_id=".$id;
        $result = $wpdb->get_row($sql);
        if($result){
           return 'This_pay_calendar_already_used_in_this_system.';
        }else{
            $wpdb->query($wpdb->prepare('DELETE '.$table_name.' FROM '.$table_name.'  WHERE   '.$table_name.'.id =%d',$id));
            $wpdb->query($wpdb->prepare('DELETE '.$join_table_name.' FROM '.$join_table_name.'  WHERE   '.$join_table_name.'.pay_calendar_id =%d',$id));
            return 'success';
        }

       }


}

new Pay_Calendar();
