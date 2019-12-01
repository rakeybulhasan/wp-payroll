<?php

/**
 * Advance_Payment class
 */
class Advance_Payment {

    public function __construct()
    {
        add_action( 'hr_employee_create', array($this, 'create_advance_payments') );
        add_action( 'hr_employee_update', array($this, 'create_advance_payments') );
    }

    public function create_advance_payments($employeeid){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_advance_payments_info";
        $post = $_POST;
        $data = array();
        $data['employee_id'] = $employeeid;
        $data['total_advance_amount'] = $post['total_advance_amount'];
        $data['adjusted_amount_per_month'] = $post['adjusted_amount_per_month'];
        $data['adjusted_starting_date'] = getMonthStringToDate($post['adjusted_starting_date'],"Y-m-d");
        $data['status'] = isset($post['status'])?$post['status']:0;
        if($this->getActiveAdvancePaymentInfoByEmployeeId($employeeid)){
            //var_dump($post['advance_payment_info_id']);die;
            if($wpdb->update(
                $table_name, $data,
                array('id'=>$post['advance_payment_info_id'])
            )){
                do_action('advance_payment_update', $data['employee_id'] );
                return true;
            }else{
                return false;
            }
        }else{
            $data['created_at'] = date("Y-m-d H:i:s");
            if($data['status']!=0){
                if($wpdb->insert(
                    $table_name,$data
                )){
                    $inserted_payroll_info_id = $wpdb->insert_id;
                    do_action('advance_payment_create', $data['employee_id'] );
                    return $inserted_payroll_info_id;
                }
            }

        }

        return false;
    }

    public function getActiveAdvancePaymentInfoByEmployeeId($id){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_advance_payments_info";

        $result = $wpdb->get_row('select * from '.$table_name.' where employee_id='.$id );
        return $result;
    }

    public function getActiveAdvancePaymentInfoById($id){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_advance_payments_info";

        $result = $wpdb->get_row('select * from '.$table_name.' where id='.$id );
        return $result;
    }



}

new Advance_Payment();
