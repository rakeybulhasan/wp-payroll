
            <div class="postbox company-postbox">
                <h3 class="hndle"><span><?php _e( 'Advance Payments info', 'rbs-erp' ); ?></span></h3>
                <div class="inside">
                    <?php
                        $advancePaymentInfoObj = new Advance_Payment();
                        $advancePaymentInfo= $advancePaymentInfoObj->getActiveAdvancePaymentInfoByEmployeeId($id);
                    ?>
                    <div class="row">

                        <div class="col-md-6">
                            <input type="hidden" name="advance_payment_info_id" value="<?php echo isset($advancePaymentInfo)&&$advancePaymentInfo->id?$advancePaymentInfo->id:''?>">
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="total_advance_amount"><?php _e('Total Amount','rbs-erp');?></label>
                                <div class="col-sm-8">
                                    <?php erp_html_form_input(array(
                                        'name'  => 'total_advance_amount',
                                        'type'  => 'text',
                                        'value' => isset($advancePaymentInfo)&&$advancePaymentInfo->total_advance_amount?$advancePaymentInfo->total_advance_amount:'',
                                    )); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4" for="balance"><?php _e('Total Adjusted Amount','rbs-erp');?></label>
                                <div class="col-sm-8">
                                    <label class="control-label">
                                        <?php echo isset($advancePaymentInfo) && $advancePaymentInfo->total_adjusted_amount ?'Tk. '.$advancePaymentInfo->total_adjusted_amount:'' ?>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4" for="balance"><?php _e('Due Amount','rbs-erp');?></label>
                                <div class="col-sm-8">
                                    <label class="control-label">
                                        <?php echo isset($advancePaymentInfo) && $advancePaymentInfo->total_advance_amount ?'Tk. '.($advancePaymentInfo->total_advance_amount - $advancePaymentInfo->total_adjusted_amount):'' ?>
                                    </label>
                                </div>
                            </div>


                        </div>

                        <div class="col-md-6">


                            <div class="form-group">
                                <label class="control-label col-sm-5" for="adjusted_amount_per_month"><?php _e('Adjusted Amount (per month)','rbs-erp');?></label>
                                <div class="col-sm-7">
                                    <?php erp_html_form_input(array(
                                        'name'  => 'adjusted_amount_per_month',
                                        'type'  => 'text',
                                        'value' => isset($advancePaymentInfo) && $advancePaymentInfo->adjusted_amount_per_month ?$advancePaymentInfo->adjusted_amount_per_month:'',
                                    )); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-5" for="adjusted_starting_date"><?php _e('Adjusted Starting Date','rbs-erp');?></label>
                                <div class="col-sm-7">
                                    <?php erp_html_form_input(array(
                                        'name'  => 'adjusted_starting_date',
                                        'type'  => 'text',
                                        'class' => 'form-control date-field',
                                        'custom_attr'=> array('autocomplete' => 'off'),
                                        'value' => isset($advancePaymentInfo) && $advancePaymentInfo->adjusted_starting_date?date('d-m-Y', strtotime($advancePaymentInfo->adjusted_starting_date)):'',
                                    ));
                                    ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-5" for="status"><?php _e('Are you adjusted starting?','rbs-erp');?></label>
                                <div class="col-sm-7">
                                    <input id="status" name="status" type="checkbox" value="1" <?php echo isset($advancePaymentInfo) && $advancePaymentInfo->status==1?'checked="checked"':''?>>
                                </div>
                            </div>

                        </div>

                    </div>

                </div><!-- .inside -->
            </div><!-- .postbox -->

<!--            --><?php //do_action('hr-employee-after-payroll-info');?>