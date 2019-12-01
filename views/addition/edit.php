<div class="wrap rbs rbs-company-single">
    <h2>
        <?php _e( 'Edit Addition', 'rbs-erp' ); ?>
        <a href="<?php echo admin_url( 'admin.php?page=payroll-addition&action=new' ); ?>" class="add-new-h2"><?php _e( 'Add New', 'erp' ); ?></a>
    </h2>

    <?php

    if (isset($_GET['status'])&& $_GET['status'] == 'submitted' ) {
        erp_html_show_notice( __( 'Addition has been updated successfully.', 'rbs-erp' ) );
    } elseif(isset($_GET['status'])&& $_GET['status']=='error' && isset($_GET['error'])) {

        erp_html_show_notice(isset($_GET['error'])?$_GET['error']:'', 'error' );
    }
    $additionObj = new Addition;
    $addition = $additionObj->getAdditionById($id);

    ?>


    <form class="form-horizontal" action="" method="post" id="erp-new-addition">
        <div class="row rbs-single-container">
            <div class="col-md-9">

                <div class="postbox company-postbox">
                    <h3 class="hndle"><span><?php _e( 'Addition Information', 'rbs-erp' ); ?></span></h3>
                    <div class="inside">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group required">
                                    <label class="control-label col-sm-2" for="addition_name"><?php _e('Name','rbs-erp');?></label>
                                    <div class="col-sm-10">
                                        <?php erp_html_form_input(array(
                                            'name'  => 'addition_name',
                                            'type'  => 'text',
                                            'value' => $addition->addition_name?$addition->addition_name:'',
                                            'required' => true,
                                        )); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group required">
                                            <label class="control-label col-sm-4" for="addition_amount"><?php _e('Addition Amount','rbs-erp');?></label>
                                            <div class="col-sm-8">
                                                <?php erp_html_form_input(array(
                                                    'name'  => 'addition_amount',
                                                    'type'  => 'text',
                                                    'value' => $addition->addition_amount?$addition->addition_amount:'',
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
                                                    'value' => $addition->amount_type?$addition->amount_type:'percentage',
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
                                            'value' => $addition->description?$addition->description:'',
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

                                    <?php wp_nonce_field( 'new-addition-add' ); ?>
                                    <input type="hidden" name="rbs-erp-action" value="addition_edit">
                                    <input type="hidden" name="id" value="<?php echo $addition->id?$addition->id:''?>">
                                    <input type="hidden" name="exists_addition_name" value="<?php echo $addition->addition_name?$addition->addition_name:''?>">
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
