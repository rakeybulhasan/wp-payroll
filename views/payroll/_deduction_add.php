<div class="postbox company-deduction">
    <h3 class="hndle"><span><?php _e( 'Deduction Items', 'rbs-erp' ); ?></span></h3>
    <div class="inside">

        <div class="row">

            <div class="col-md-5">

                <table class="table selected_deduction_item">
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
                    $joinDeductionItems = $payrollBasicInfo->getDeductionItemByEmpolyee($id);
                    if($joinDeductionItems){
                        foreach ($joinDeductionItems as $deductionItem){?>
                            <tr>
                                <td>
                                    <input type="hidden" name="added_deduction_item[]" value="<?php echo $deductionItem->id;?>">
                                    <input type="hidden" name="added_deduction_item_id[]" value="<?php echo $deductionItem->deduction_id;?>">
                                    <input type="hidden" name="added_deduction_amount_type[]" value="<?php echo $deductionItem->amount_type;?>">
                                    <input type="hidden" name="added_deduction_amount[]" value="<?php echo $deductionItem->deduction_amount;?>">
                                    <?php echo $deductionItem->deduction_name;?>
                                </td>
                                <td><?php echo $deductionItem->deduction_amount;?><?php if($deductionItem->amount_type=='percentage'){ echo '%';}elseif ($deductionItem->amount_type=='hour'){echo ' Hour'; }else{ echo ' TK.';} ?></td>
                                <td><button type="button" data-id="<?php echo $deductionItem->id;?>" title="Remove" value="<?php echo $deductionItem->deduction_id;?>" class="btn btn-danger btn-xs deduction-delete-row">X</button></td>
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
                                'name'  => 'deduction_name',
                                'type'  => 'select',
                                'value' => '',
                                'class' => 'form-control deduction_name',
                                'options' => deductionDropdownOptions(),
                            )); ?>
                        </td>

                        <td>
                            <?php erp_html_form_input(array(
                                'name'  => 'deduction_amount',
                                'type'  => 'text',
                                'class' => 'form-control deduction_amount',
                                'value' => ''
                            )); ?>
                        </td>
                        <td>
                            <?php erp_html_form_input(array(
                                'name'  => 'amount_type',
                                'type'  => 'select',
                                'value' => 'percentage',
                                'class' => 'form-control deduction_amount_type',
                                'options'=> array('percentage'=>'%','currency'=>'TK.')
                            )); ?>
                        </td>
                        <td><input type="button" class="btn btn-primary add-row-deduction" value="Add"></td>
                    </tr>
                    </tbody>

                </table>

            </div>


        </div>

    </div><!-- .inside -->
</div><!-- .postbox -->
