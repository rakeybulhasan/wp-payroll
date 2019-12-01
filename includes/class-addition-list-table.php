<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
/**
 * List table class
 */
class Addition_List_Table extends WP_List_Table {

    protected $per_page;
    protected $slug = null;
    function __construct() {
        global $status, $page;
        $this->slug = 'payroll-addition';
        parent::__construct( array(
            'singular' => 'Addition',
            'plural'   => 'Additions',
            'ajax'     => false
        ) );
    }

    /**
     * Retrieve addition’s data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_companies( $per_page = 25, $page_number = 1 ) {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}rbs_erp_payroll_additions";

        if(!empty($_REQUEST['status']) && $_REQUEST['status']!='all' ){
            $status ='';
            if($_REQUEST['status']=='active'){
                $status = 1;
            }elseif ($_REQUEST['status']=='trash'){
                $status = 0;
            }

            $sql .=' WHERE status='.$status;
        }
        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }

        $sql .= " LIMIT $per_page";

        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}rbs_erp_payroll_additions";

        return $wpdb->get_var( $sql );
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function active_record_count($satus) {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}rbs_erp_payroll_additions WHERE status={$satus}";

        return $wpdb->get_var( $sql );
    }

    /**
     * Message to show if no addition found
     *
     * @return void
     */
    function no_items() {
        _e( 'No addition found.', 'rbs-erp' );
    }

    /**
     * Render a column when no column specific method exists.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'addition_name':
            case 'description':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the designation name column
     *
     * @param  object  $item
     *
     * @return string
     */
    function column_addition_name( $item ) {

        $data_hard         = ( isset( $_REQUEST['status'] ) && $_REQUEST['status'] == 'trash' ) ? 1 : 0;
        $delete_text       = ( isset( $_REQUEST['status'] ) && $_REQUEST['status'] == 'trash' ) ? __( 'Permanent Delete', 'rbs-erp' ) : __( 'Delete', 'rbs-erp' );

        $actions            = array();
        if( current_user_can( 'manage_options' ) ) {
            $actions['edit']    = sprintf( '<a href="%s" data-id="%d" title="%s">%s</a>', admin_url( 'admin.php?page=' . $this->slug . '&action=edit&id=' . $item["id"] ), $item["id"], __( 'Edit this item', 'rbs-erp' ), __( 'Edit', 'rbs-erp' ) );
        }

        if( current_user_can( 'manage_options' ) ) {
            $actions['delete'] = sprintf( '<a href="%s" class="erp-ac-submitdelete" data-id="%d" data-hard=%d title="%s" data-type="%s">%s</a>', admin_url( 'admin.php?page=' . $this->slug . '&action=move_to_trash&id[]=' . $item["id"] ), $item["id"], $data_hard, __( 'Delete this item', 'rbs-erp' ), $this->type, $delete_text );
        }

        return sprintf( '<a href="%1$s"><strong>%2$s</strong></a> %3$s', admin_url( 'admin.php?page=' . $this->slug . '&action=view&id=' . $item["id"] ), $item["addition_name"], $this->row_actions( $actions ) );
    }
    /**
     * Get the column names
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'              => '<input type="checkbox" />',
            'addition_name'            => __( 'Name', 'rbs-erp' ),
            'description'            => __( 'Description', 'rbs-erp' ),
        );

        return apply_filters( 'rbs_erp_addition_table_cols', $columns );
    }
    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />', $item['id']
        );
    }
    /**
     * Set the views
     *
     * @return array
     */
    public function get_views() {

        $status_links   = array();
        $base_link      = admin_url( 'admin.php?page=payroll-addition' );

        $status_links['all']   = sprintf( '<a href="%s">%s <span class="count">(%s)</span></a>', add_query_arg( array( 'status' => 'all' ), $base_link ), __( 'All', 'rbs-erp' ),  self::record_count() );
        $status_links['active'] = sprintf( '<a href="%s" >%s <span class="count">(%s)</span></a>', add_query_arg( array( 'status' => 'active' ), $base_link ), __( 'Active', 'rbs-erp' ),  self::active_record_count(1) );
        $status_links['trash'] = sprintf( '<a href="%s" >%s <span class="count">(%s)</span></a>', add_query_arg( array( 'status' => 'trash' ), $base_link ), __( 'Trash', 'rbs-erp' ),  self::active_record_count(0) );

        return $status_links;
    }
    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'addition_name' => array( 'addition_name', true ),
        );

        return $sortable_columns;
    }


    /**
     * Set the bulk actions
     *
     * @return array
     */
    function get_bulk_actions() {
        $actions = array(
            'move_to_trash'  => __( 'Move to Trash', 'rbs-erp' ),
        );

        if ( isset( $_REQUEST['status'] ) && $_REQUEST['status'] == 'trash' ) {
            unset( $actions['move_to_trash'] );

            $actions['delete'] = __( 'Permanent Delete', 'erp' );
            $actions['restore'] = __( 'Restore', 'erp' );
        }
        return $actions;
    }

    public function prepare_items() {

//        $this->_column_headers = $this->get_column_info();
        $columns               = $this->get_columns();
        $hidden                = array( );
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );

        $per_page     = $this->get_items_per_page( 'companies_per_page', 20 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ] );


        $this->items = self::get_companies( $per_page, $current_page );
    }



}
