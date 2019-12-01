<?php

/** Actions *******************************************************************/

// process erp actions on admin_init
add_action( 'hr-employee-after-personal-information', 'advance_payment_form' );
add_action( 'hr-employee-after-personal-information', 'payroll_basic_form' );
add_action( 'hr-employee-after-payroll-info', 'payroll_allowance_join_form' );
add_action( 'hr-employee-after-payroll-info', 'payroll_deduction_join_form' );

function payroll_basic_form(){
    $id = isset($_REQUEST['id'])?$_REQUEST['id']:0;
    include_once RBS_ERP_PAYROLL_VIEWS . '/payroll/add.php';
}

function advance_payment_form(){
    $id = isset($_REQUEST['id'])?$_REQUEST['id']:0;
    include_once RBS_ERP_PAYROLL_VIEWS . '/advance-payments/add.php';
}

function payroll_allowance_join_form(){
    $id = isset($_REQUEST['id'])?$_REQUEST['id']:0;
    include_once RBS_ERP_PAYROLL_VIEWS . '/payroll/_allowance_add.php';
}

function payroll_deduction_join_form(){
    $id = isset($_REQUEST['id'])?$_REQUEST['id']:0;
    include_once RBS_ERP_PAYROLL_VIEWS . '/payroll/_deduction_add.php';
}