<div class="wrap rbs rbs-company-single">
    <h2><?php _e( 'Pay Calendar Setting', 'rbs-erp' ); ?></h2>

    <?php

    if (isset($_GET['status'])&& $_GET['status'] == 'submitted' ) {
        erp_html_show_notice( __( 'Pay Calendar has been created successfully.', 'rbs-erp' ) );
    } elseif(isset($_GET['status'])&& $_GET['status']=='error' && isset($_GET['error'])) {

        erp_html_show_notice(isset($_GET['error'])?$_GET['error']:'', 'error' );
    }
    $payColObj= new Pay_Calendar();
    $payCal = $payColObj->getAddedEmployeeByPayCalender($id);
//    var_dump($payCal);die;

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
                                            'value' => $payCal['pay_calendar_name'],
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
                                            'value' => $payCal['pay_calendar_type'],
                                            'required' => true,
                                            'options'   => array('monthly'=>'Monthly', 'daily'=>'Daily', 'weekly'=>'Weekly', 'individual'=>'Individual')
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
                                    <div class="col-sm-8"><p class="form-control">
                                        <?php $companeyObj = new Company();
                                            echo $companeyObj->get_company_name($payCal['company_id'])
                                        ?>
                                        </p>
                                        <input type="hidden" id="company_id" name="company_id" value="<?php echo $payCal['company_id']?>">

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">

                                <div class="form-group branch_group required">
                                    <label class="control-label col-sm-4" for="branch_id"><?php _e('Branch','rbs-erp');?></label>
                                    <div class="col-sm-8">
                                        <?php if (!empty($payCal['branch_id']) && $payCal['branch_id']!=0){?>
                                            <p class="form-control">
                                                <?php $branchObj = new Branch();
                                                echo $branchObj->get_branch_name($payCal['branch_id'])
                                                ?>
                                            </p>
                                            <input type="hidden" id="branch_id" name="branch_id" value="<?php echo $payCal['branch_id']?>">

                                        <?php }else{
                                            echo '<p class="form-control">All Branches</p>';
                                        }?>

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
                                        <td>#</td>
                                        <td>Employee <span class="employee_count">(0)</span></td>
                                        <td>Email</td>
                                        <td>Department</td>
                                        <td>Designation</td>
                                        <td>Action</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($payCal['employees'] as $employee){?>
                                        <tr>
                                            <td><input disabled checked name="selected_employee_id[]" type="checkbox" value="<?php echo $employee['emId'];?>"></td>
                                            <td><?php echo $employee['name'];?></td>
                                            <td><?php echo $employee['email'];?></td>
                                            <td><?php echo $employee['department_name'];?></td>
                                            <td><?php echo $employee['designation_name'];?></td>
                                            <td><button class="btn btn-danger btn-xs remove_employee_from_pay_calendar" type="button" value="<?php echo $employee['pceId'];?>"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button></td>
                                        </tr>
                                    <?php }?>

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

                                    <?php wp_nonce_field( 'new-pay-calendar-edit' ); ?>
                                    <input type="hidden" name="rbs-erp-action" value="pay_calendar_edit">
                                    <input type="hidden" id="pay_calender_id" name="id" value="<?php echo $payCal['pcId'];?>">
                                    <input type="hidden" name="exists_pay_calendar_name" value="<?php echo $payCal['pay_calendar_name'];?>">
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
