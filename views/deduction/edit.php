<div class="wrap rbs rbs-company-single">
    <h2>
        <?php _e( 'Edit Deduction', 'rbs-erp' ); ?>
        <a href="<?php echo admin_url( 'admin.php?page=payroll-deduction&action=new' ); ?>" class="add-new-h2"><?php _e( 'Add New', 'erp' ); ?></a>
    </h2>

    <?php

    if (isset($_GET['status'])&& $_GET['status'] == 'submitted' ) {
        erp_html_show_notice( __( 'Deduction has been updated successfully.', 'rbs-erp' ) );
    } elseif(isset($_GET['status'])&& $_GET['status']=='error' && isset($_GET['error'])) {

        erp_html_show_notice(isset($_GET['error'])?$_GET['error']:'', 'error' );
    }
    $deductionObj = new Deduction();
    $deduction = $deductionObj->getDeductionById($id);

    ?>


    <form class="form-horizontal" action="" method="post" id="erp-new-deduction">
        <div class="row rbs-single-container">
            <div class="col-md-9">

                <div class="postbox company-postbox">
                    <h3 class="hndle"><span><?php _e( 'Deduction Information', 'rbs-erp' ); ?></span></h3>
                    <div class="inside">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group required">
                                    <label class="control-label col-sm-2" for="deduction_name"><?php _e('Name','rbs-erp');?></label>
                                    <div class="col-sm-10">
                                        <?php erp_html_form_input(array(
                                            'name'  => 'deduction_name',
                                            'type'  => 'text',
                                            'value' => $deduction->deduction_name?$deduction->deduction_name:'',
                                            'required' => true,
                                        )); ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group required">
                                            <label class="control-label col-sm-4" for="deduction_amount"><?php _e('Deduction Amount','rbs-erp');?></label>
                                            <div class="col-sm-8">
                                                <?php erp_html_form_input(array(
                                                    'name'  => 'deduction_amount',
                                                    'type'  => 'text',
                                                    'value' => $deduction->deduction_amount?$deduction->deduction_amount:'',
                                                    'required' => true,
                                                )); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="amount_type"><?php _e('Amount Type','rbs-erp');?></label>
                                            <div class="col-sm-8">
                                                <?php erp_html_form_input(array(
                                                    'name'  => 'amount_type',
                                                    'type'  => 'select',
                                                    'value' => $deduction->amount_type?$deduction->amount_type:'percentage',
                                                    'options'=> array('percentage'=>'%','currency'=>'TK.','hour'=>'Hour')
                                                )); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="description"><?php _e('Description','rbs-erp');?></label>
                                    <div class="col-sm-10">
                                        <?php erp_html_form_input(array(
                                            'name'  => 'description',
                                            'type'  => 'textarea',
                                            'value' => $deduction->description?$deduction->description:'',
                                        )); ?>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div><!-- .inside -->
                </div><!-- .postbox -->

            </div><!-- .erp-area-left -->
            <div class="col-md-3">
                <div class="postbox company-postbox">
                    <h3 class="hndle"><span><?php _e( 'Actions', 'rbs-erp' ); ?></span></h3>
                    <div class="inside">
                        <div class="submitbox" id="submitbox">
                            <div id="major-publishing-actions">

                                <div id="publishing-action">

                                    <?php wp_nonce_field( 'new-deduction-add' ); ?>
                                    <input type="hidden" name="rbs-erp-action" value="deduction_edit">
                                    <input type="hidden" name="id" value="<?php echo $deduction->id?$deduction->id:''?>">
                                    <input type="hidden" name="exists_deduction_name" value="<?php echo $deduction->deduction_name?$deduction->deduction_name:''?>">
                                    <input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php echo __( 'Update', 'rbs-erp' ); ?>">
                                </div>

                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- .erp-single-container -->
    </form>
</div>
