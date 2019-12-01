<div class="wrap erp erp-company-single">
    <h2>
        <?php _e( 'Deduction List', 'rbs-erp' ); ?>
        <a href="<?php echo admin_url( 'admin.php?page=payroll-deduction&action=new' ); ?>" class="add-new-h2"><?php _e( 'Add New', 'erp' ); ?></a>
    </h2>

    <div class="metabox-holder company-accounts">


        <div class="company-location-wrap">

            <div id="company-locations">
                <div id="company-locations-inside">
                    <form method="get">
                        <input type="hidden" name="page" value="payroll-deduction">
                        <?php
                        $deduction_table = new Deduction_List_Table();
                        $deduction_table->prepare_items();
                        $deduction_table->views();

                        $deduction_table->display();
                        ?>
                    </form>
                </div><!-- #company-locations-inside -->
            </div><!-- #company-locations -->
        </div><!-- .company-location-wrap -->
    </div><!-- .metabox-holder -->

</div>
