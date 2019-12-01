<?php

class Form_Handler_Payroll {

    /**
     * Hook 'em all
     */
    public function __construct() {

//        add_action( 'admin_init', array( $this, 'handle_customer_form' ) );
        $payroll_management = sanitize_title( __( 'Payroll Management', 'rbs-erp' ) );

        add_action( 'rbs_erp_action_addition_new', array( $this, 'addition_add' ) );
        add_action( 'rbs_erp_action_addition_edit', array( $this, 'addition_edit' ) );
        add_action( "load-{$payroll_management}_page_payroll-addition", array( $this, 'addition_bulk_action' ) );

        add_action( 'rbs_erp_action_deduction_new', array( $this, 'deduction_add' ) );
        add_action( 'rbs_erp_action_deduction_edit', array( $this, 'deduction_edit' ) );
        add_action( "load-{$payroll_management}_page_payroll-deduction", array( $this, 'deduction_bulk_action' ) );

        add_action( 'rbs_erp_action_pay_calendar_new', array( $this, 'pay_calendar_add' ) );
        add_action( 'rbs_erp_action_pay_calendar_edit', array( $this, 'pay_calendar_edit' ) );
        add_action( "load-{$payroll_management}_page_pay-calendar", array( $this, 'pay_calendar_bulk_action' ) );

        add_action( 'rbs_erp_action_pay_calendar_run_new', array( $this, 'pay_calendar_run_add' ) );

        add_action( 'rbs_erp_action_find_employee_into_pay_run', array( $this, 'get_employee_into_pay_calendar_run' ) );
    }

    /**
     * Addition handle bulk action
     *
     * @since 0.1
     *
     * @return void [redirection]
     */
    public function employee_bulk_action() {
        //var_dump('ok');die;
        // Check nonce validation
        /* if ( ! $this->verify_current_page_screen( 'erp-payroll-depts', 'bulk-employees' ) ) {
             return;
         }*/

        // Check permission if not payroll manager then go out from here
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }


        $employee_table = new Employee_List_Table();
        $action         = $employee_table->current_action();

        if ( $action ) {

            $redirect = remove_query_arg( array(
                '_wp_http_referer',
                '_wpnonce',
                'action',
                'action2',
                'paged',
            ), wp_unslash( $_SERVER['REQUEST_URI'] ) );
            $resp     = [];
            $employee= new Employee();
            switch ( $action ) {
                case 'delete' :
                    if ( isset( $_GET['id'] ) && $_GET['id'] ) {
                        foreach ( $_GET['id'] as $key => $dept_id ) {
                            $resp[] = $employee->delete_employee( $dept_id );
                        }
                    }

                    $redirect = add_query_arg( array( 'status' => 'trash' ), $redirect );
                    wp_redirect( $redirect );
                    exit();
                case 'move_to_trash':
                    if ( isset( $_GET['id'] ) && $_GET['id'] ) {
                        foreach ( $_GET['id'] as $key => $dept_id ) {
                            $resp[] = $employee->soft_delete_employee( $dept_id );
                        }
                    }

                    $redirect = add_query_arg( array( 'status' => 'all' ), $redirect );
                    wp_redirect( $redirect );
                    exit();
                case 'restore':
                    if ( isset( $_GET['id'] ) && $_GET['id'] ) {
                        foreach ( $_GET['id'] as $key => $dept_id ) {
                            $resp[] = $employee->restore_employee( $dept_id );
                        }
                    }
                    $redirect = add_query_arg( array( 'status' => 'trash' ), $redirect );
                    wp_redirect( $redirect );
                    exit();

            }
        }
    }



    function handle_form_submission() {
        // do your validation, nonce and stuffs here
        global $error;
        $error = new WP_Error();

        // Make sure user supplies the first name
        if ( empty( $_POST['addition_name'] ) ) {
            $error->add( 'empty', 'Addition_name-is_required.' );
            //return false;
        }
        $addition = new Addition();
        if($_POST['exists_addition_name'] != $_POST['addition_name']){

            $existsAddition = $addition->getExistsAddition($_POST['addition_name']);
            if($existsAddition){
                $error->add( 'empty', 'This_addition_name_already_exists.' );

            }
        }
        // Send the result
        if ( !empty( $error->get_error_codes() ) ) {
            return $error;
//            print_r($error);die;
        }

        // Everything is fine
        return true;
    }

    /**
     * Submit a new leave request
     *
     * @since 0.1
     *
     * @return void
     */
    public function addition_add() {

        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'new-addition-add' ) ) {
            die( __( 'Something went wrong!', 'rbs-erp' ) );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to do this action', 'rbs-erp' ) );
        }
        $data= array();
        $data['addition_name']= $_POST['addition_name'];
        $data['addition_amount']= $_POST['addition_amount'];
        $data['amount_type']= $_POST['amount_type'];
        $data['description']= $_POST['description'];
        $data['status']= 1;
        $data['created_at']= date("Y-m-d H:i:s");

        $page_url    = admin_url( 'admin.php?page=payroll-addition' );

        $errorObj = $this->handle_form_submission();

        if ( is_wp_error($errorObj) && count($errorObj->get_error_messages())> 0 ) {
            $errors = $errorObj->get_error_messages();
            $first_error = reset( $errors );
            $redirect_to = add_query_arg( array( 'error'=> $first_error ), $page_url.'&action=new&status=error' );
            wp_redirect( $redirect_to );
            exit;

        } else {
            $addition = new Addition();
            $insert = $addition->create_addition($data);

            if ( ! is_wp_error( $insert ) ) {
                $redirect_to = admin_url( 'admin.php?page=payroll-addition&action=new&status=submitted' );
                wp_redirect( $redirect_to );
            }
        }

    }

    /**
     * Submit a new leave request
     *
     * @since 0.1
     *
     * @return void
     */
    public function addition_edit() {

        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'new-addition-add' ) ) {
            die( __( 'Something went wrong!', 'rbs-erp' ) );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to do this action', 'rbs-erp' ) );
        }
        $data= array();
        $data['id']= $_POST['id'];
        $data['addition_name']= $_POST['addition_name'];
        $data['addition_amount']= $_POST['addition_amount'];
        $data['amount_type']= $_POST['amount_type'];
        $data['description']= $_POST['description'];
        $data['status']= 1;
        $data['updated_at']= date("Y-m-d H:i:s");

        $page_url    = admin_url( 'admin.php?page=payroll-addition' );

        $errorObj = $this->handle_form_submission();

        if ( is_wp_error($errorObj) && count($errorObj->get_error_messages())> 0 ) {
            $errors = $errorObj->get_error_messages();
            $first_error = reset( $errors );
            $redirect_to = add_query_arg( array( 'error'=> $first_error ), $page_url.'&action=edit&id='.$data["id"].'&status=error' );
            wp_redirect( $redirect_to );
            exit;

        } else {

            $addition = new Addition();
            $insert = $addition->addition_edit($data);

            if ( ! is_wp_error( $insert ) ) {
                $redirect_to = $page_url;
                wp_redirect( $redirect_to );
            }
            exit;
        }


    }

    /**
     * Addition handle bulk action
     *
     * @since 0.1
     *
     * @return void [redirection]
     */
    public function addition_bulk_action() {

//        var_dump('ok');die;
        // Check nonce validation
       /* if ( ! $this->verify_current_page_screen( 'erp-payroll-depts', 'bulk-additions' ) ) {
            return;
        }*/

        // Check permission if not payroll manager then go out from here
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }


        $addition_table = new Addition_List_Table();
        $action         = $addition_table->current_action();

        if ( $action ) {

            $redirect = remove_query_arg( array(
                '_wp_http_referer',
                '_wpnonce',
                'action',
                'action2',
                'paged',
            ), wp_unslash( $_SERVER['REQUEST_URI'] ) );
            $resp     = [];
            $addition= new Addition();
            switch ( $action ) {
                case 'delete' :
                    if ( isset( $_GET['id'] ) && $_GET['id'] ) {
                        foreach ( $_GET['id'] as $key => $dept_id ) {
                            $resp[] = $addition->delete_addition( $dept_id );
                        }
                    }

                    $redirect = add_query_arg( array( 'status' => 'trash' ), $redirect );
                    wp_redirect( $redirect );
                    exit();
                case 'move_to_trash':
                    if ( isset( $_GET['id'] ) && $_GET['id'] ) {
                        foreach ( $_GET['id'] as $key => $dept_id ) {
                            $resp[] = $addition->soft_delete_addition( $dept_id );
                        }
                    }

                    $redirect = add_query_arg( array( 'status' => 'all' ), $redirect );
                    wp_redirect( $redirect );
                    exit();
                case 'restore':
                    if ( isset( $_GET['id'] ) && $_GET['id'] ) {
                        foreach ( $_GET['id'] as $key => $dept_id ) {
                            $resp[] = $addition->restore_addition( $dept_id );
                        }
                    }
                    $redirect = add_query_arg( array( 'status' => 'trash' ), $redirect );
                    wp_redirect( $redirect );
                    exit();

            }
        }
    }

    function handle_form_submission_deduction() {
        // do your validation, nonce and stuffs here
        global $error;
        // Instantiate the WP_Error object
        $error = new WP_Error();

        // Make sure user supplies the first name
        if ( empty( $_POST['deduction_name'] ) ) {
            $error->add( 'empty', 'Deduction_name-is_required.' );
            //return false;
        }
        $deduction = new Deduction();
        if($_POST['exists_deduction_name'] != $_POST['deduction_name']){

            $existsDeduction = $deduction->getExistsDeduction($_POST['deduction_name']);
            if($existsDeduction){
                $error->add( 'empty', 'This_deduction_name_already_exists.' );

            }
        }
        // Send the result
        if ( !empty( $error->get_error_codes() ) ) {
            return $error;
//            print_r($error);die;
        }

        // Everything is fine
        return true;
    }

    /**
     * Submit a new leave request
     *
     * @since 0.1
     *
     * @return void
     */
    public function deduction_add() {

        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'new-deduction-add' ) ) {
            die( __( 'Something went wrong!', 'rbs-erp' ) );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to do this action', 'rbs-erp' ) );
        }
        $data= array();
        $data['deduction_name']= $_POST['deduction_name'];
        $data['deduction_amount']= $_POST['deduction_amount'];
        $data['amount_type']= $_POST['amount_type'];
        $data['description']= $_POST['description'];
        $data['status']= 1;
        $data['created_at']= date("Y-m-d H:i:s");

        $page_url    = admin_url( 'admin.php?page=payroll-deduction' );

        $errorObj = $this->handle_form_submission_deduction();

        if ( is_wp_error($errorObj) && count($errorObj->get_error_messages())> 0 ) {
            $errors = $errorObj->get_error_messages();
            $first_error = reset( $errors );
            $redirect_to = add_query_arg( array( 'error'=> $first_error ), $page_url.'&action=new&status=error' );
            wp_redirect( $redirect_to );
            exit;

        } else {
            $deduction = new Deduction();
            $insert = $deduction->create_deduction($data);

            if ( ! is_wp_error( $insert ) ) {
                $redirect_to = admin_url( 'admin.php?page=payroll-deduction&action=new&status=submitted' );
                wp_redirect( $redirect_to );
            }
        }

    }

    /**
     * Submit a new leave request
     *
     * @since 0.1
     *
     * @return void
     */
    public function deduction_edit() {

        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'new-deduction-add' ) ) {
            die( __( 'Something went wrong!', 'rbs-erp' ) );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to do this action', 'rbs-erp' ) );
        }
        $data= array();
        $data['id']= $_POST['id'];
        $data['deduction_name']= $_POST['deduction_name'];
        $data['deduction_amount']= $_POST['deduction_amount'];
        $data['amount_type']= $_POST['amount_type'];
        $data['description']= $_POST['description'];
        $data['status']= 1;
        $data['updated_at']= date("Y-m-d H:i:s");

        $page_url    = admin_url( 'admin.php?page=payroll-deduction' );

        $errorObj = $this->handle_form_submission_deduction();

        if ( is_wp_error($errorObj) && count($errorObj->get_error_messages())> 0 ) {
            $errors = $errorObj->get_error_messages();
            $first_error = reset( $errors );
            $redirect_to = add_query_arg( array( 'error'=> $first_error ), $page_url.'&action=edit&id='.$data["id"].'&status=error' );
            wp_redirect( $redirect_to );
            exit;

        } else {

            $deduction = new Deduction();
            $insert = $deduction->deduction_edit($data);

            if ( ! is_wp_error( $insert ) ) {
                $redirect_to = $page_url;
                wp_redirect( $redirect_to );
            }
            exit;
        }


    }

    /**
     * Deduction handle bulk action
     *
     * @since 0.1
     *
     * @return void [redirection]
     */
    public function deduction_bulk_action() {

        if ( isset($_POST['_wpnonce']) && ! wp_verify_nonce( $_POST['_wpnonce'] ) ) {
            die( __( 'Something went wrong!', 'rbs-erp' ) );
        }

        // Check permission if not payroll manager then go out from here
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }


        $deduction_table = new Deduction_List_Table();
        $action         = $deduction_table->current_action();

        if ( $action ) {

            $redirect = remove_query_arg( array(
                '_wp_http_referer',
                '_wpnonce',
                'action',
                'action2',
                'paged',
            ), wp_unslash( $_SERVER['REQUEST_URI'] ) );
            $resp     = [];
            $deduction= new Deduction();
            switch ( $action ) {
                case 'delete' :
                    if ( isset( $_GET['id'] ) && $_GET['id'] ) {
                        foreach ( $_GET['id'] as $key => $dept_id ) {
                            $resp[] = $deduction->delete_deduction( $dept_id );
                        }
                    }

                    $redirect = add_query_arg( array( 'status' => 'trash' ), $redirect );
                    wp_redirect( $redirect );
                    exit();
                case 'move_to_trash':
                    if ( isset( $_GET['id'] ) && $_GET['id'] ) {
                        foreach ( $_GET['id'] as $key => $dept_id ) {
                            $resp[] = $deduction->soft_delete_deduction( $dept_id );
                        }
                    }

                    $redirect = add_query_arg( array( 'status' => 'all' ), $redirect );
                    wp_redirect( $redirect );
                    exit();
                case 'restore':
                    if ( isset( $_GET['id'] ) && $_GET['id'] ) {
                        foreach ( $_GET['id'] as $key => $dept_id ) {
                            $resp[] = $deduction->restore_deduction( $dept_id );
                        }
                    }
                    $redirect = add_query_arg( array( 'status' => 'trash' ), $redirect );
                    wp_redirect( $redirect );
                    exit();

            }
        }
    }


    function handle_form_submission_pay_calendar() {
        // do your validation, nonce and stuffs here
        global $error;
        // Instantiate the WP_Error object
        $error = new WP_Error();

        // Make sure user supplies the first name
        if ( empty( $_POST['pay_calendar_name'] ) ) {
            $error->add( 'empty', 'Pay_Calendar_name-is_required.' );
            //return false;
        }
        if ( empty( $_POST['company_id'] ) || $_POST['company_id']=='-1'  ) {
            $error->add( 'empty', 'Company_is_required.' );
            //return false;
        }

        // Send the result
        if ( !empty( $error->get_error_codes() ) ) {
            return $error;
//            print_r($error);die;
        }

        // Everything is fine
        return true;
    }

    public function pay_calendar_add() {

        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'new-pay-calendar-add' ) ) {
            die( __( 'Something went wrong!', 'rbs-erp' ) );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to do this action', 'rbs-erp' ) );
        }
        $data= array();
        $data['pay_calendar_name']= $_POST['pay_calendar_name'];
        $data['pay_calendar_type']= $_POST['pay_calendar_type'];
        $data['company_id']= isset($_POST['company_id'])?$_POST['company_id']:'';
        $data['branch_id']= isset($_POST['branch_id'])?$_POST['branch_id']:'';
        $data['status']= 1;
        $data['created_at']= date("Y-m-d H:i:s");

        $page_url    = admin_url( 'admin.php?page=pay-calendar' );

        $errorObj = $this->handle_form_submission_pay_calendar();

        if ( is_wp_error($errorObj) && count($errorObj->get_error_messages())> 0 ) {
            $errors = $errorObj->get_error_messages();
            $first_error = reset( $errors );
            $redirect_to = add_query_arg( array( 'error'=> $first_error ), $page_url.'&action=new&status=error' );
            wp_redirect( $redirect_to );
            exit;

        } else {
            $deduction = new Pay_Calendar();
            $insert = $deduction->create_pay_calendar($data);

            if ( ! is_wp_error( $insert ) ) {
                $redirect_to = admin_url( 'admin.php?page=pay-calendar&action=new&status=submitted' );
                wp_redirect( $redirect_to );
            }
        }

    }

    public function pay_calendar_edit() {

        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'new-pay-calendar-edit' ) ) {
            die( __( 'Something went wrong!', 'rbs-erp' ) );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to do this action', 'rbs-erp' ) );
        }
        $pay_calender_id = $_GET['id'];
        $data= array();
        $data['pay_calendar_name']= $_POST['pay_calendar_name'];
        $data['pay_calendar_type']= $_POST['pay_calendar_type'];
        $data['company_id']= isset($_POST['company_id'])?$_POST['company_id']:'';
        $data['branch_id']= isset($_POST['branch_id'])?$_POST['branch_id']:'';
        $data['status']= 1;
        $data['created_at']= date("Y-m-d H:i:s");

        $page_url    = admin_url( 'admin.php?page=pay-calendar' );

        $errorObj = $this->handle_form_submission_pay_calendar();

        if ( is_wp_error($errorObj) && count($errorObj->get_error_messages())> 0 ) {
            $errors = $errorObj->get_error_messages();
            $first_error = reset( $errors );
            $redirect_to = add_query_arg( array( 'error'=> $first_error ), $page_url.'&action=new&status=error' );
            wp_redirect( $redirect_to );
            exit;

        } else {
            $deduction = new Pay_Calendar();
            $insert = $deduction->update_pay_calendar($data, $pay_calender_id);

            if ( ! is_wp_error( $insert ) ) {
                $redirect_to = admin_url( 'admin.php?page=pay-calendar&action=new&status=submitted' );
                wp_redirect( $redirect_to );
            }
        }

    }

    /**
     * Deduction handle bulk action
     *
     * @since 0.1
     *
     * @return void [redirection]
     */
    public function pay_calendar_bulk_action() {
        global $error;
        // Instantiate the WP_Error object
        $error = new WP_Error();
        if ( isset($_POST['_wpnonce']) && ! wp_verify_nonce( $_POST['_wpnonce'] ) ) {
            die( __( 'Something went wrong!', 'rbs-erp' ) );
        }

        // Check permission if not payroll manager then go out from here
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        $action = isset($_GET['action'])?$_GET['action']:'';

        if ( $action ) {

            $redirect = remove_query_arg( array(
                '_wp_http_referer',
                '_wpnonce',
                'action',
                'action2',
                'paged',
            ), wp_unslash( $_SERVER['REQUEST_URI'] ) );
            $deduction= new Pay_Calendar();
            switch ( $action ) {
                case 'delete' :
                    if ( isset( $_GET['id'] ) && $_GET['id'] ) {
                       $return =  $deduction->delete_pay_calendar( $_GET['id'] );
                    }

                    $redirect_to = add_query_arg( array( 'status' => $return=='success'?'success':'trash', 'error' => $return ), $redirect );
                    wp_redirect( $redirect_to );
                    exit();

            }
        }
    }



    function handle_form_submission_pay_calendar_run() {
        // do your validation, nonce and stuffs here
        global $error;
        // Instantiate the WP_Error object
        $error = new WP_Error();

        // Make sure user supplies the first name
        if ( empty( $_POST['from_date'] ) ) {
            $error->add( 'empty', 'From_date_is_required.' );
            //return false;
        }
        if ( empty( $_POST['to_date'] )  ) {
            $error->add( 'empty', 'To_date_is_required.' );
            //return false;
        }
        if ( empty( $_POST['payment_date'] )  ) {
            $error->add( 'empty', 'Payment_date_is_required.' );
            //return false;
        }
        if ( empty( $_POST['payment_type'] )  ) {
            $error->add( 'empty', 'Payment_type_is_required.' );
            //return false;
        }
        if ($_POST['payment_type']=='BONUS_PAYMENT' && empty( $_POST['bonus_name'] )  ) {
            $error->add( 'empty', 'Bonus_name_is_required.' );
            //return false;
        }
        $pay_cal_run = new Pay_Calendar_Run();
        if($_POST['from_date']&& $_POST['to_date']){

            $existsPayCalRun = $pay_cal_run->exist_pay_calendar_run($_POST['pay_calendar_id'],$_POST['from_date'], $_POST['to_date'],  $_POST['payment_type'], $_POST['bonus_name']);
            if($existsPayCalRun){
                $error->add( 'empty', 'This_pay_calendar_already_run_in_this_month.' );

            }
        }

        // Send the result
        if ( !empty( $error->get_error_codes() ) ) {
            return $error;
//            print_r($error);die;
        }

        // Everything is fine
        return true;
    }

    public function pay_calendar_run_add() {

        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'new-pay-calendar-run-add' ) ) {
            die( __( 'Something went wrong!', 'rbs-erp' ) );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to do this action', 'rbs-erp' ) );
        }

        $data= array();
        $data['from_date']= getMonthStringToDate($_POST['from_date'],"Y-m-d");
        $data['to_date']= getMonthStringToDate($_POST['to_date'],"Y-m-d");
        $data['payment_date']= getMonthStringToDate($_POST['payment_date'],"Y-m-d");
        $data['payment_type']= $_POST['payment_type'];
        $data['bonus_name']= ($_POST['bonus_name']!='')?$_POST['bonus_name']:null;
        $data['pay_calendar_id']= isset($_POST['pay_calendar_id'])?$_POST['pay_calendar_id']:'';
        $data['status']= 0;
        $data['created_at']= date("Y-m-d H:i:s");

        $page_url    = admin_url( 'admin.php?page=pay-run' );

        $errorObj = $this->handle_form_submission_pay_calendar_run();

        if ( is_wp_error($errorObj) && count($errorObj->get_error_messages())> 0 ) {
            $errors = $errorObj->get_error_messages();
            $first_error = reset( $errors );
            $redirect_to = add_query_arg( array( 'error'=> $first_error ), $page_url.'&tab=employees&id='.$_POST['pay_calendar_id'].'&status=error' );
            wp_redirect( $redirect_to );
            exit;

        } else {
            $payCalRunObj = new Pay_Calendar_Run();
            $insert = $payCalRunObj->create_pay_calendar_run($data);

            if ( ! is_wp_error( $insert ) ) {
                $redirect_to = admin_url( 'admin.php?page=pay-run&tab=allowance-deduction-input&id='.$insert.'&status=submitted' );
                wp_redirect( $redirect_to );
            }
        }

    }

    public function get_employee_into_pay_calendar_run() {
//var_dump('ok');die;
        if ( isset($_POST['_wpnonce']) && ! wp_verify_nonce( $_POST['_wpnonce'] ) ) {
            die( __( 'Something went wrong!', 'rbs-erp' ) );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to do this action', 'rbs-erp' ) );
        }
//        var_dump($_GET['id']);die;
        $redirect = remove_query_arg( array(
            '_wp_http_referer',
            '_wpnonce',
            'action',
            'action2',
            'rbs-erp-action',
            'paged',
        ), wp_unslash( $_SERVER['REQUEST_URI'] ) );

//        $redirect_to = add_query_arg( array( 'page' => 'pay-run',  'tab' => 'payslips' ), $redirect );
//        $redirect_to = admin_url( 'admin.php?page=pay-run&tab=payslips&id=24&status=submitted' );
//        wp_redirect( $redirect_to );
        wp_redirect( $redirect );
    }



}

new Form_Handler_Payroll();
