<?php

class Admin_Menu_Payroll
{
    public function __construct() {
        add_action( 'admin_menu', array($this, 'admin_menu'),10);

        add_action( 'admin_init', array($this, 'theme_options_init') );
//        add_action( 'admin_menu', 'theme_options_add_page' );

    }

    public function admin_menu() {
        add_menu_page( __( 'Payroll Management', 'rbs-erp' ), __('Payroll Management','rbs-erp'), 'manage_options', 'rbs-erp-payroll', array( $this, 'dashboard_page' ), 'dashicons-money' );
        add_submenu_page( 'rbs-erp-payroll', __( 'Overview', 'rbs-erp' ), __( 'Overview', 'rbs-erp' ), 'manage_options', 'rbs-erp-payroll', array( $this, 'dashboard_page' ) );
        add_submenu_page( 'rbs-erp-payroll', __( 'Additions', 'rbs-erp' ), __( 'Additions', 'rbs-erp' ), 'manage_options', 'payroll-addition', array( $this, 'addition_page' ) );
        add_submenu_page( 'rbs-erp-payroll', __( 'Deductions', 'rbs-erp' ), __( 'Deductions', 'rbs-erp' ), 'manage_options', 'payroll-deduction', array( $this, 'deduction_page' ) );
        add_submenu_page( 'rbs-erp-payroll', __( 'Pay Calendars', 'rbs-erp' ), __( 'Pay Calendars', 'rbs-erp' ), 'manage_options', 'pay-calendar', array( $this, 'pay_calendar_page' ) );
        add_submenu_page( 'rbs-erp-payroll', __( 'Pay Run', 'rbs-erp' ), __( 'Pay Run', 'rbs-erp' ), 'manage_options', 'pay-run', array( $this, 'pay_run_page' ) );
        add_submenu_page( 'rbs-erp-payroll', __( 'Payroll Report', 'rbs-erp' ), __( 'Payroll Report', 'rbs-erp' ), 'manage_options', 'payroll-report', array( $this, 'payroll_report_page' ) );
        add_submenu_page( 'rbs-erp-payroll', __( 'Top Sheet Summery', 'rbs-erp' ), __( 'Top Sheet Summery', 'rbs-erp' ), 'manage_options', 'top-sheet-summery', array( $this, 'payroll_top_sheet_summery_page' ) );
        add_submenu_page( 'rbs-erp-payroll', __( 'Plugin Settings' ), __( 'Plugin Settings' ), 'edit_theme_options', 'site-settings', array($this,'theme_options_do_page') );

    }


    public function dashboard_page() {
        include_once RBS_ERP_PAYROLL_VIEWS . '/dashboard.php';
    }

    /**
     * Handles the addition page
     *
     * @return void
     */
    public function addition_page() {
        $action = isset( $_GET['action'] ) ? $_GET['action'] : 'list';
        $id     = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

        switch ($action) {
            case 'edit':
                $template = RBS_ERP_PAYROLL_VIEWS . '/addition/edit.php';
                break;

            case 'new':
                $template = RBS_ERP_PAYROLL_VIEWS . '/addition/add.php';
                break;

            default:
                $template = RBS_ERP_PAYROLL_VIEWS . '/addition/list.php';
                break;
        }

        if ( file_exists( $template ) ) {
            include $template;
        }
    }

    /**
     * Handles the deduction page
     *
     * @return void
     */
    public function deduction_page() {
        $action = isset( $_GET['action'] ) ? $_GET['action'] : 'list';
        $id     = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

        switch ($action) {
            case 'edit':
                $template = RBS_ERP_PAYROLL_VIEWS . '/deduction/edit.php';
                break;

            case 'new':
                $template = RBS_ERP_PAYROLL_VIEWS . '/deduction/add.php';
                break;

            default:
                $template = RBS_ERP_PAYROLL_VIEWS . '/deduction/list.php';
                break;
        }

        if ( file_exists( $template ) ) {
            include $template;
        }
    }

    /**
     * Handles the deduction page
     *
     * @return void
     */
    public function pay_calendar_page() {
        $action = isset( $_GET['action'] ) ? $_GET['action'] : 'list';
        $id     = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

        switch ($action) {
            case 'edit':
                $template = RBS_ERP_PAYROLL_VIEWS . '/pay-calendar/edit.php';
                break;

            case 'new':
                $template = RBS_ERP_PAYROLL_VIEWS . '/pay-calendar/add.php';
                break;

            default:
                $template = RBS_ERP_PAYROLL_VIEWS . '/pay-calendar/list.php';
                break;
        }

        if ( file_exists( $template ) ) {
            include $template;
        }
    }

    /**
     * Handles the deduction page
     *
     * @return void
     */
    public function pay_run_page() {

        $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'list';
        $id     = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

        switch ($tab) {
            case 'employees':
                $template = RBS_ERP_PAYROLL_VIEWS . '/pay-run/add.php';
                break;

            case 'allowance-deduction-input':
                $template = RBS_ERP_PAYROLL_VIEWS . '/pay-run/allowance-deduction-input.php';
                break;

            case 'summary':
                $template = RBS_ERP_PAYROLL_VIEWS . '/pay-run/pay-summary.php';
                break;

            case 'payslips':
                $template = RBS_ERP_PAYROLL_VIEWS . '/pay-run/pay-slips.php';
                break;

            default:
                $template = RBS_ERP_PAYROLL_VIEWS . '/pay-run/list.php';
                break;
        }

        if ( file_exists( $template ) ) {
            include $template;
        }
    }

    /**
     * Handles the deduction page
     *
     * @return void
     */
    public function payroll_report_page() {

        $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'list';
        $id     = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

        switch ($tab) {

            case 'summary':
                $template = RBS_ERP_PAYROLL_VIEWS . '/payroll-report/payroll-report-summary.php';
                break;

            case 'payroll-report-payslips':
                $template = RBS_ERP_PAYROLL_VIEWS . '/payroll-report/payroll-report-slips.php';
                break;

            default:
                $template = RBS_ERP_PAYROLL_VIEWS . '/payroll-report/payroll-report-summary.php';
                break;
        }

        if ( file_exists( $template ) ) {
            include $template;
        }
    }

    /**
     * Handles the deduction page
     *
     * @return void
     */
    public function payroll_top_sheet_summery_page() {

        $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'list';
        $id     = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

        switch ($tab) {

            case 'summary':
                $template = RBS_ERP_PAYROLL_VIEWS . '/pay-top-sheet-summery/pay-top-sheet-summary.php';
                break;

            default:
                $template = RBS_ERP_PAYROLL_VIEWS . '/pay-top-sheet-summery/pay-top-sheet-summary.php';
                break;
        }

        if ( file_exists( $template ) ) {
            include $template;
        }
    }


    /**
     * Init plugin options to white list our options
     */
    function theme_options_init(){
        register_setting( 'ecb_options', 'payroll_plugin_setting_options', 'theme_options_validate' );
    }

     /**
     * Create the options page
     */
    function theme_options_do_page() {
        global $select_options, $radio_options;

        if ( ! isset( $_REQUEST['updated'] ) )
            $_REQUEST['updated'] = false;

        ?>
        <div class="wrap theme_option">
            <?php echo "<h2>" . __( 'Payroll Plugin Settings' ) . "</h2>"; ?>

            <?php if ( false !== $_REQUEST['updated'] ) : ?>
                <div class="updated fade"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>
            <?php endif; ?>

            <form method="post" action="options.php">
                <?php settings_fields( 'ecb_options' ); ?>
                <?php $options = get_option( 'payroll_plugin_setting_options' ); ?>

                <table class="form-table">

                    <tr valign="top">
                        <th scope="row"><?php _e( 'Allowance for absence day applicable' ); ?></th>
                        <td>
                            <?php
                            $additionObj = new Addition();
                            $addtions = $additionObj->getActiveAddition();
                            $selectd_addtion = isset($options['selectd_addtion'])?$options['selectd_addtion']:array();
                            ?>
                            <select name="payroll_plugin_setting_options[selectd_addtion][]" class="multi_select" id="" multiple>
                                <?php foreach ($addtions as $addtion){?>
                                    <option value="<?php echo $addtion->id?>" <?php echo in_array($addtion->id, $selectd_addtion)?'selected="selected"':"";?>><?php echo $addtion->addition_name?></option>
                                    <?php }?>
                            </select>
                        </td>
                    </tr>

                </table>

                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e( 'Save Options' ); ?>" />
                </p>
            </form>
        </div>

        <?php
    }

    /**
     * Sanitize and validate input. Accepts an array, return a sanitized array.
     */
    function theme_options_validate( $input ) {
        global $select_options, $radio_options;

        $input['sometextarea'] = wp_filter_post_kses( $input['sometextarea'] );
        return $input;
    }

}