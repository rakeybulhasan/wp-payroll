<?php

class Payroll
{
    public function __construct() {
        // prevent duplicate loading
        if ( did_action( 'erp_payroll_loaded' ) ) {
            return;
        }

        // Define constants
        $this->define_constants();

        // Include required files
        $this->includes();

        // Initialize the classes
        $this->init_classes();
        $this->init_action();
        do_action( 'erp_payroll_loaded' );
    }

    private function init_action(){
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'pay_run_scripts'));
    }

    /**
     * Define the plugin constants
     *
     * @return void
     */
    private function define_constants() {
        define( 'RBS_ERP_PAYROLL_FILE', __FILE__ );
        define( 'RBS_ERP_PAYROLL_PATH', dirname( __FILE__ ) );
        define( 'RBS_ERP_PAYROLL_VIEWS', dirname( __FILE__ ) . '/views' );
        define( 'RBS_ERP_PAYROLL_ASSETS', plugins_url( '/assets', __FILE__ ) );
    }

    /**
     * @return void
     */
    private function includes() {
        require_once RBS_ERP_PAYROLL_PATH . '/includes/actions-filters.php';
        require_once RBS_ERP_PAYROLL_PATH . '/includes/class-payroll-ajax.php';
        require_once RBS_ERP_PAYROLL_PATH . '/includes/class-payroll-run-ajax.php';
        require_once RBS_ERP_PAYROLL_PATH . '/includes/class-payroll.php';

        require_once RBS_ERP_PAYROLL_PATH . '/includes/functions-addition.php';
        require_once RBS_ERP_PAYROLL_PATH . '/includes/class-addition.php';
        require_once RBS_ERP_PAYROLL_PATH . '/includes/class-addition-list-table.php';
        require_once RBS_ERP_PAYROLL_PATH . '/includes/functions-deduction.php';
        require_once RBS_ERP_PAYROLL_PATH . '/includes/class-deduction.php';
        require_once RBS_ERP_PAYROLL_PATH . '/includes/class-deduction-list-table.php';
        require_once RBS_ERP_PAYROLL_PATH . '/includes/class-pay-calendar.php';
        require_once RBS_ERP_PAYROLL_PATH . '/includes/class-pay-calendar-run.php';
        require_once RBS_ERP_PAYROLL_PATH . '/includes/functions-pay-run.php';

        require_once RBS_ERP_PAYROLL_PATH . '/includes/class-advance-payments.php';

        require_once RBS_ERP_PAYROLL_PATH . '/includes/class-form-handler.php';
//        require_once RBS_ERP_PAYROLL_PATH . '/includes/settings.php';

        require_once RBS_ERP_PAYROLL_PATH . '/includes/class-payroll-report.php';

        require_once RBS_ERP_PAYROLL_PATH . '/includes/admin/class-payroll-admin-menu.php';

    }

    private function init_classes()
    {
        new Admin_Menu_Payroll();
    }


    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function admin_scripts() {

        wp_enqueue_script( 'erp-payroll-script', plugin_dir_url( __FILE__ ) . 'assets/js/payroll.js' );
        wp_enqueue_script( 'erp-payroll-script' );
        wp_localize_script( 'erp-payroll-script', 'RbsErpPayroll', array(
            'ajaxurl'         => admin_url( 'admin-ajax.php' ),
        ) );

    }
    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function pay_run_scripts() {

        wp_enqueue_script( 'erp-pay-run-script', plugin_dir_url( __FILE__ ) . 'assets/js/payroll-run.js' );
        wp_enqueue_script( 'erp-pay-run-script' );
        wp_localize_script( 'erp-pay-run-script', 'RbsErpPayrollRun', array(
            'ajaxurlpayrun'         => admin_url( 'admin-ajax.php' ),
        ) );

    }


}

new Payroll();