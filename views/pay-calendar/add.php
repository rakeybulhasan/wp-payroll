<div class="wrap rbs rbs-company-single">
    <h2><?php _e( 'Pay Calendar Add', 'rbs-erp' ); ?></h2>

    <?php

    if (isset($_GET['status'])&& $_GET['status'] == 'submitted' ) {
        erp_html_show_notice( __( 'Pay Calendar has been created successfully.', 'rbs-erp' ) );
    } elseif(isset($_GET['status'])&& $_GET['status']=='error' && isset($_GET['error'])) {

        erp_html_show_notice(isset($_GET['error'])?$_GET['error']:'', 'error' );
    }

    ?>


    <form class="form-horizontal" action="" method="post" id="erp-new-pay-calendar">
        <div class="row rbs-single-container">
            <div class="col-md-9">

                <div class="postbox company-postbox">
                    <h3 class="hndle"><span><?php _e( 'Pay Calendar Information', 'rbs-erp' ); ?></span></h3>
                    <div class="inside">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group required">
                                    <label class="control-label col-sm-2" for="pay_calendar_name"><?php _e('Calendar Name','rbs-erp');?></label>
                                    <div class="col-sm-6">
                                        <?php erp_html_form_input(array(
                                            'name'  => 'pay_calendar_name',
                                            'type'  => 'text',
                                            'value' => '',
                                            'required' => true,
                                        )); ?>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    <label class="control-label col-sm-2" for="pay_calendar_type"><?php _e('Calendar Type','rbs-erp');?></label>
                                    <div class="col-sm-6">
                                        <?php erp_html_form_input(array(
                                            'name'  => 'pay_calendar_type',
                                            'type'  => 'select',
                                            'value' => 'monthly',
                                            'required' => true,
                                            'options'   => payCalenderType()
                                        )); ?>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div><!-- .inside -->
                    <h3 class="hndle"><span><?php _e( 'Add Employee', 'rbs-erp' ); ?></span></h3>
                    <div class="inside">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group required">
                                    <label class="control-label col-sm-4" for="company_id"><?php _e('Company','rbs-erp');?></label>
                                    <div class="col-sm-8">
                                        <?php erp_html_form_input( array(
                                            'name'        => 'company_id',
                                            'type'        => 'select',
                                            'required' => true,
                                            'options'     => companyDropdownOptions()
                                        ) ); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">

                                <div class="form-group branch_group required">
                                    <label class="control-label col-sm-4" for="branch_id"><?php _e('Branch','rbs-erp');?></label>
                                    <div class="col-sm-8">
                                        <?php erp_html_form_input( array(
                                            'name'        => 'branch_id',
                                            'type'        => 'select',
                                            'required' => true,
                                            'options'     => array( '' => __( ' Select Branch', 'rbs-erp' ) )
                                        ) ); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-success btn-sm add_employee_pay_calendar">Add Employee</button>
                            </div>

                        </div>

                    </div>
                </div><!-- .postbox -->

                <div class="postbox company-postbox">
                    <h3 class="hndle"><span><?php _e( 'Pay Calendar Setup', 'rbs-erp' ); ?></span></h3>
                    <div class="inside">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped pay_calendar_setup">
                                    <thead>
                                    <tr>
                                        <td><input value="1" type="checkbox" class="all_employee_check_uncheck"></td>
                                        <td>Employee <span class="employee_count">(0)</span></td>
                                        <td>Email</td>
                                        <td>Department</td>
                                        <td>Designation</td>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>

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

                                    <?php wp_nonce_field( 'new-pay-calendar-add' ); ?>
                                    <input type="hidden" name="rbs-erp-action" value="pay_calendar_new">
                                    <input type="hidden" name="id" value="">
                                    <input type="hidden" name="exists_pay_calendar_name" value="">
                                    <input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php echo __( 'Create', 'rbs-erp' ); ?>">
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
