<div class="postbox company-postbox">
    <h3 class="hndle"><span><?php _e( 'Allowance Items', 'rbs-erp' ); ?></span></h3>
    <div class="inside">

        <div class="row">

            <div class="col-md-5">

                <table class="table selected_item">
                    <thead>
                    <tr>
                        <td class="col-md-7">Pay Item</td>
                        <td class="col-md-4">Pay Amount</td>
                        <td class="col-md-1">Action</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        $payrollBasicInfo = new Payroll_Basic_Info();
                        $joinAllowanceItems = $payrollBasicInfo->getAllowanceItemByEmpolyee($id);
                    if($joinAllowanceItems){
                        foreach ($joinAllowanceItems as $allowanceItem){?>
                        <tr>
                            <td>
                                <input type="hidden" name="added_allowance_item[]" value="<?php echo $allowanceItem->id;?>">
                                <input type="hidden" name="added_allowance_item_id[]" value="<?php echo $allowanceItem->addition_id;?>">
                                <input type="hidden" name="added_allowance_amount_type[]" value="<?php echo $allowanceItem->amount_type;?>">
                                <input type="hidden" name="added_allowance_amount[]" value="<?php echo $allowanceItem->addition_amount;?>">
                                <?php echo $allowanceItem->addition_name;?>
                            </td>
                            <td><?php echo $allowanceItem->addition_amount;?><?php if($allowanceItem->amount_type=='percentage'){ echo '%';}elseif ($allowanceItem->amount_type=='hour'){echo ' Hour'; }else{ echo ' TK.';} ?></td>
                            <td><button type="button" data-id="<?php echo $allowanceItem->id;?>" title="Remove" value="<?php echo $allowanceItem->addition_id;?>" class="btn btn-danger btn-xs delete-row">X</button></td>
                        </tr>
                    <?php

                        }
                    }

                    ?>

                    </tbody>
                </table>
            </div>

            <div class="col-md-7">
                <table class="table">
                    <thead>
                        <tr>
                            <td class="col-md-5">Pay Item</td>
                            <td class="col-md-3">Pay Amount</td>
                            <td class="col-md-2">Type</td>
                            <td class="col-md-2">action</td>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <?php erp_html_form_input(array(
                                'name'  => 'addition_name',
                                'type'  => 'select',
                                'value' => '',
                                'class' => 'form-control addition_name',
                                'options' => additionDropdownOptions(),
                            )); ?>
                        </td>

                        <td>
                            <?php erp_html_form_input(array(
                                'name'  => 'addition_amount',
                                'type'  => 'text',
                                'class' => 'form-control addition_amount',
                                'value' => ''
                            )); ?>
                        </td>
                        <td>
                            <?php erp_html_form_input(array(
                                'name'  => 'amount_type',
                                'type'  => 'select',
                                'value' => 'percentage',
                                'class' => 'form-control amount_type',
                                'options'=> array('percentage'=>'%','currency'=>'TK.','hour'=>'Hour')
                            )); ?>
                        </td>
                        <td><input type="button" class="btn btn-primary add-row" value="Add"></td>
                    </tr>
                    </tbody>

                </table>

            </div>


        </div>

    </div><!-- .inside -->
</div><!-- .postbox -->
