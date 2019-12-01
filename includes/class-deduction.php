<?php

/**
 * Deduction class
 */
class Deduction {

    public function get_deduction_name($id ) {

        $deduction = $this-> getDeductionById($id);
        if ( $deduction ) {
            return stripslashes( $deduction->deduction_name );
        }
    }
    /**
     * [get_edit_url description]
     *
     * @return string the url
     */
    public function get_edit_url() {
        $url = add_query_arg(
            array( 'action' => 'edit' ),
            admin_url( 'admin.php?page=rbs-erp-payroll' )
        );

        return $url;
    }
    public static function active_record_count($satus) {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}rbs_erp_payroll_deductions WHERE status={$satus}";

        return $wpdb->get_var( $sql );
    }

    public function getAllDeduction(){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_deductions";

        $results = $wpdb->get_results('select * from '.$table_name );
        return $results;
    }

    public function getActiveDeduction(){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_deductions";

        $results = $wpdb->get_results('select * from '.$table_name.' where status=1' );
        return $results;
    }

    public function getDeductionById($id){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_deductions";

        $result = $wpdb->get_row('select * from '.$table_name.' where id='.$id );
        return $result;
    }

    public function getExistsDeduction($name){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_deductions";

        $result = $wpdb->get_row("select * from ".$table_name." where deduction_name='".$name."'");
        return $result;
    }

    public function create_deduction($data){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_deductions";

        if($wpdb->insert(
            $table_name,$data,
            array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s'
            )
        )){
            return $wpdb->insert_id;
        }

        return false;
    }

    public function deduction_edit($data){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_deductions";
      $id =  array_shift($data);
        if($wpdb->update(
            $table_name,$data,
            array( 'id' => $id ),
            array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s'
            ),
            array('%d')
        )){
            return true;
        }

        return false;
    }

    /**
     * Delete a deduction record.
     *
     * @param int $id deduction ID
     */
    public static function delete_deduction( $id ) {
        global $wpdb;

        $wpdb->delete(
            "{$wpdb->prefix}rbs_erp_payroll_deductions",
            [ 'ID' => $id ],
            [ '%d' ]
        );
    }

    public static function soft_delete_deduction( $id ) {
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_deductions";
        $data= array();
        $data['status']= 0;
        $data['updated_at']= date("Y-m-d H:i:s");
        if($wpdb->update(
            $table_name,$data,
            array( 'id' => $id ),
            array(
                '%d',
                '%s'
            ),
            array('%d')
        )){
            return true;
        }

        return false;
    }

    public static function restore_deduction( $id ) {
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_deductions";
        $data= array();
        $data['status']= 1;
        $data['updated_at']= date("Y-m-d H:i:s");
        if($wpdb->update(
            $table_name,$data,
            array( 'id' => $id ),
            array(
                '%d',
                '%s'
            ),
            array('%d')
        )){
            return true;
        }

        return false;
    }

}

new Deduction();
