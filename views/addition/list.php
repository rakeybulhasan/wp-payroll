<div class="wrap erp erp-company-single">
    <h2>
        <?php _e( 'Addition List', 'rbs-erp' ); ?>
        <a href="<?php echo admin_url( 'admin.php?page=payroll-addition&action=new' ); ?>" class="add-new-h2"><?php _e( 'Add New', 'erp' ); ?></a>
    </h2>

    <div class="metabox-holder company-accounts">


        <div class="company-location-wrap">

            <div id="company-locations">
                <div id="company-locations-inside">
                    <form method="get">
                        <input type="hidden" name="page" value="payroll-addition">
                        <?php
                        $addition_table = new Addition_List_Table();
                        $addition_table->prepare_items();
                        $addition_table->views();

                        $addition_table->display();
                        ?>
                    </form>
                </div><!-- #company-locations-inside -->
            </div><!-- #company-locations -->
        </div><!-- .company-location-wrap -->
    </div><!-- .metabox-holder -->

</div>
