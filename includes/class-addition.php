<?php

/**
 * Addition class
 */
class Addition {

    public function get_addition_name($id ) {

        $addition = $this-> getAdditionById($id);
        if ( $addition ) {
            return stripslashes( $addition->addition_name );
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
            admin_url( 'admin.php?page=rbs-erp-hr' )
        );

        return $url;
    }
    public static function active_record_count($satus) {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}rbs_erp_payroll_additions WHERE status={$satus}";

        return $wpdb->get_var( $sql );
    }
    public function getAllAddition(){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_additions";

        $results = $wpdb->get_results('select * from '.$table_name );
        return $results;
    }

    public function getActiveAddition(){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_additions";

        $results = $wpdb->get_results('select * from '.$table_name.' where status=1' );
        return $results;
    }

    public function getAdditionById($id){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_additions";

        $result = $wpdb->get_row('select * from '.$table_name.' where id='.$id );
        return $result;
    }

    public function getExistsAddition($name){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_additions";

        $result = $wpdb->get_row("select * from ".$table_name." where addition_name='".$name."'");
        return $result;
    }

    public function create_addition($data){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_additions";

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

    public function addition_edit($data){
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_additions";
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
     * Delete a addition record.
     *
     * @param int $id addition ID
     */
    public static function delete_addition( $id ) {
        global $wpdb;

        $wpdb->delete(
            "{$wpdb->prefix}rbs_erp_payroll_additions",
            [ 'ID' => $id ],
            [ '%d' ]
        );
    }
    /**
     * Delete a addition record.
     *
     * @param int $id addition ID
     */
    public static function soft_delete_addition( $id ) {
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_additions";
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
    /**
     * Delete a addition record.
     *
     * @param int $id addition ID
     */
    public static function restore_addition( $id ) {
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $table_name = $table_prefix . "rbs_erp_payroll_additions";
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

new Addition();
