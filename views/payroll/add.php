
            <div class="postbox company-postbox">
                <h3 class="hndle"><span><?php _e( 'Payroll Basic and Tax info', 'rbs-erp' ); ?></span></h3>
                <div class="inside">
                    <?php
                        $payrollBasicInfoObj = new Payroll_Basic_Info();
                        $payrollBasicInfo= $payrollBasicInfoObj->getActivePayrollBasicInfoByEmployeeId($id);
                    ?>
                    <div class="row">

                        <div class="col-md-6">
                            <input type="hidden" name="payroll_basic_info_id" value="<?php echo isset($payrollBasicInfo)&&$payrollBasicInfo->id?$payrollBasicInfo->id:''?>">
                            <!--<div class="form-group">
                                <label class="control-label col-sm-4" for="tax_number"><?php /*_e('Tax Number','rbs-erp');*/?></label>
                                <div class="col-sm-8">
                                    <?php /*erp_html_form_input(array(
                                        'name'  => 'tax_number',
                                        'type'  => 'text',
                                        'value' => isset($payrollBasicInfo)&&$payrollBasicInfo->tax_number?$payrollBasicInfo->tax_number:'',
                                    )); */?>
                                </div>
                            </div>-->
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="basic_salary"><?php _e('Basic Salary','rbs-erp');?></label>
                                <div class="col-sm-8">
                                    <?php erp_html_form_input(array(
                                        'name'  => 'basic_salary',
                                        'type'  => 'text',
                                        'value' => isset($payrollBasicInfo) && $payrollBasicInfo->basic_salary ?$payrollBasicInfo->basic_salary:'',
                                    )); ?>
                                    <input type="hidden" name="previous_basic_salary" value="<?php echo isset($payrollBasicInfo) && $payrollBasicInfo->basic_salary ?$payrollBasicInfo->basic_salary:0?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4" for="total_salary"><?php _e('Total Salary','rbs-erp');?></label>
                                <div class="col-sm-8">
                                    <?php erp_html_form_input(array(
                                        'name'  => 'total_salary',
                                        'type'  => 'text',
                                        'value' => isset($payrollBasicInfo) && $payrollBasicInfo->total_salary ?$payrollBasicInfo->total_salary:'',
                                    )); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4" for="bonus_ratio"><?php _e('Bonus Ratio','rbs-erp');?></label>
                                <div class="col-sm-8">
                                    <?php erp_html_form_input(array(
                                        'name'  => 'bonus_ratio',
                                        'type'  => 'text',
                                        'value' => isset($payrollBasicInfo) && $payrollBasicInfo->bonus_ratio ?$payrollBasicInfo->bonus_ratio:'',
                                    )); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4" for="payment_method"><?php _e('Payment Method','rbs-erp');?></label>
                                <div class="col-sm-8">
                                    <?php erp_html_form_input(array(
                                        'name'  => 'payment_method',
                                        'type'  => 'select',
                                        'value' => isset($payrollBasicInfo) && $payrollBasicInfo->payment_method?$payrollBasicInfo->payment_method:'bank',
                                        'options' => array('cash'=>'Cash','cheque'=>'Cheque','bank'=>'Bank')
                                    )); ?>
                                </div>
                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="control-label col-sm-4" for="bank_account_number"><?php _e('Bank A/C No','rbs-erp');?></label>
                                <div class="col-sm-8">
                                    <?php erp_html_form_input(array(
                                        'name'  => 'bank_account_number',
                                        'type'  => 'text',
                                        'value' => isset($payrollBasicInfo) && $payrollBasicInfo->bank_account_number?$payrollBasicInfo->bank_account_number:'',
                                    )); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4" for="bank_account_name"><?php _e('Bank A/C Name','rbs-erp');?></label>
                                <div class="col-sm-8">
                                    <?php erp_html_form_input(array(
                                        'name'  => 'bank_account_name',
                                        'type'  => 'text',
                                        'value' => isset($payrollBasicInfo) && $payrollBasicInfo->bank_account_name?$payrollBasicInfo->bank_account_name:'',
                                    )); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4" for="bank_name"><?php _e('Bank Name','rbs-erp');?></label>
                                <div class="col-sm-8">
                                    <?php erp_html_form_input(array(
                                        'name'  => 'bank_name',
                                        'type'  => 'text',
                                        'value' => isset($payrollBasicInfo)&&$payrollBasicInfo->bank_name?$payrollBasicInfo->bank_name:'',
                                    )); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4" for="bank_branch_name"><?php _e('Bank Branch Name','rbs-erp');?></label>
                                <div class="col-sm-8">
                                    <?php erp_html_form_input(array(
                                        'name'  => 'bank_branch_name',
                                        'type'  => 'text',
                                        'value' => isset($payrollBasicInfo)&&$payrollBasicInfo->bank_branch_name?$payrollBasicInfo->bank_branch_name:'',
                                    )); ?>
                                </div>
                            </div>
                        </div>

                    </div>

                </div><!-- .inside -->
            </div><!-- .postbox -->

            <?php do_action('hr-employee-after-payroll-info');?>