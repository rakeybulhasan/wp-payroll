<div class="wrap erp erp-company-single summary_sheet">


    <div class="metabox-holder company-accounts">


        <div class="company-location-wrap ">

            <div id="company-locations" class="">
                <form class="form-horizontal" action="" method="get">
                    <?php
                        $company_id = isset($_GET['company_id'])?$_GET['company_id']:0;
                        $from_date = isset($_GET['from_date'])?$_GET['from_date']:'';
                        $branch_id = isset($_GET['branch_id'])?implode(', ', $_GET['branch_id']):'';

                        $branch = new Branch();
                        $branchObjs = $branch->getActiveCompanyBranchByCompanyId($company_id);

                    ?>
                    <div class="row print_hidden">

                        <input type="hidden" name="page" value="top-sheet-summery">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group required">
                                    <label class="control-label col-sm-4" for="company_id"><?php _e('Company','rbs-erp');?></label>
                                    <div class="col-sm-8">
                                        <?php erp_html_form_input( array(
                                            'name'        => 'company_id',
                                            'type'        => 'select',
                                            'value'=> $company_id,
                                            'options'     => companyDropdownOptions()
                                        ) ); ?>

                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-4" for="from_date"><?php _e('Date','rbs-erp');?></label>
                                    <div class="col-sm-8">
                                        <?php erp_html_form_input(array(
                                            'name'  => 'from_date',
                                            'type'  => 'text',
                                            'value' => $from_date,
                                            'class' => 'form-control date-field',
                                            'custom_attr'=> array('autocomplete' => 'off'),
                                        )); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">

                                <div class="form-group branch_group required">
                                    <label class="control-label col-sm-2" for="branch_id"><?php _e('Branch','rbs-erp');?></label>
                                    <div class="col-sm-8">
                                        <select name="branch_id[]" id="branch_id" class="multi_select" multiple>
                                            <option>Select Branch</option>
                                            <?php if($branchObjs){
                                                foreach ($branchObjs as $value){
                                                ?>
                                                <option value="<?php echo $value->id?>" <?php echo in_array($value->id, $_GET['branch_id'])? 'selected="selected"':''?>><?php echo $value->branch_name?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-10" style="text-align: center">
                                <button type="submit" name="top_sheet_form" class="btn btn-success btn-sm">Filter</button>
                            </div>

                            <div class="col-md-12">
                                <a style="float: right" class="btn btn-primary" href="javascript:void (0)" onclick="window.print()"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Print</a>
                            </div>

                        </div>

                    </div>
                <div class=" table-responsive">
                    <?php

                    $payrollReport = new Payroll_Report();
                    $topSheetSummeries = $payrollReport->generateTopSheetSummery($company_id, $from_date, $branch_id);

                    $company = new Company();
                     $companyObj = $company->getCompanyById($company_id);
                    ?>
                    <div id="company-locations-inside" class="printableArea">
                       <?php if ($topSheetSummeries){?>
                           <h3 style="text-align: center"><?php echo isset($companyObj)?$companyObj->company_name:''?></h3>
                           <h4 style="text-align: center">Date : <?php echo isset($from_date)?getMonthStringToDate($from_date,"F, Y"):''?></h4>


                           <table border="1" class="table topsheet_summery">
                               <thead>
                               <tr>
                                   <th>Branch Name</th>
                                   <th>Basic</th>
                                   <th>Allowance</th>
                                   <th>Deduction</th>
                                   <th>Net Total</th>
                               </tr>
                               </thead>
                               <tbody>
                               <?php
                               $totalBasic = 0;
                               $totalAllowance = 0;
                               $totalDeduction = 0;
                               $grandTotal = 0;
                               foreach ($topSheetSummeries as $key=>$value){
                                   $totalBasic += $value['basicTotal'];
                                   $totalAllowance += $value['allowance'];
                                   $totalDeduction += $value['deduction'];
                                   $grandTotal += $value['netTotal'];

                                   ?>

                                   <tr>
                                       <td><?php echo $value['branchName'];?></td>
                                       <td><?php echo number_format($value['basicTotal'], 2, '.', ',' )?></td>
                                       <td><?php echo number_format($value['allowance'], 2, '.', ',' )?></td>
                                       <td><?php echo number_format($value['deduction'], 2, '.', ',' )?></td>
                                       <td><?php echo number_format($value['netTotal'], 2, '.', ',' )?></td>
                                   </tr>
                               <?php

                               }

                               ?>
                               </tbody>
                               <tfoot>
                               <tr>
                                   <td style="text-align: right;font-weight: bold"">Total</td>
                                   <td style="font-weight: bold"><?php echo number_format($totalBasic, 2, '.', ',' );?></td>
                                   <td style="font-weight: bold"><?php echo number_format($totalAllowance, 2, '.', ',' );?></td>
                                   <td style="font-weight: bold"><?php echo number_format($totalDeduction, 2, '.', ',' );?></td>
                                   <td style="font-weight: bold"><?php echo number_format($grandTotal, 2, '.', ',' );?></td>
                               </tr>
                               </tfoot>
                           </table>

                        <?php }?>

                    </div>
                </div>
                </form>
                <!-- #company-locations-inside -->
            </div><!-- #company-locations -->
        </div><!-- .company-location-wrap -->
    </div><!-- .metabox-holder -->

</div>