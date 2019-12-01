<?php

/**
 * The ajax handler class
 *
 * Handles the requests from core ERP, not from modules
 */
class Payroll_Run_Ajax {
    /**
     * Bind events
     */
    public function __construct() {

        add_action('wp_ajax_on_change_load_employee_payment_info', array($this, 'getOnChangePayRunAddedEmployeeInfo'));
        add_action('wp_ajax_add_addition_item_into_json', array($this, 'add_addition_item_into_json'));
        add_action('wp_ajax_add_addition_description', array($this, 'add_addition_description'));
        add_action('wp_ajax_update_addition_json_item', array($this, 'delete_addition_item_from_json'));
        add_action('wp_ajax_add_deduction_item_into_json', array($this, 'add_deduction_item_into_json'));
        add_action('wp_ajax_add_deduction_description', array($this, 'add_deduction_description'));
        add_action('wp_ajax_delete_deduction_json_item', array($this, 'delete_deduction_json_item'));

        add_action('wp_ajax_update_number_of_absence_day', array($this, 'update_absence_day'));

        add_action('wp_ajax_update_adjusted_amount_per_transaction', array($this, 'update_adjusted_amount_per_transaction'));
//        add_action('wp_ajax_remove_join_addition_item', array($this, 'removeJoinAdditionItem'));

//        add_action('wp_ajax_on_change_load_deduction_info', array($this, 'getOnChangeDeductionInfo'));
//        add_action('wp_ajax_remove_join_deduction_item', array($this, 'removeJoinDeductionItem'));

//        add_action('wp_ajax_on_click_load_employee_info', array($this, 'getEmployeeByCompanyAndBranch'));
//        add_action('wp_ajax_on_click_remove_employee_from_pay_calendar', array($this, 'removeEmployeeFromPayCalendar'));

    }

    function getOnChangePayRunAddedEmployeeInfo(){
        if(!empty($_POST["employee_id"])) {
            global $wpdb;
            $employee_id = $_POST["employee_id"];
            $pay_calendar_run_id = $_POST["pay_calendar_run_id"];
            $results = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix .'rbs_erp_payroll_pay_calendar_run_details WHERE employee_id=%d AND pay_calendar_run_id=%d', $employee_id, $pay_calendar_run_id));
            $allowance_items_json = json_decode($results->allowance,true);
            $total_allowance = array_sum($allowance_items_json);

            $deduction_items_json = json_decode($results->deduction,true);
            $total_deduction = array_sum($deduction_items_json);


            $addtitionObj = new Addition();
            $deductionObj = new Deduction();
            $allowanceItems='<tbody>';
            foreach ($allowance_items_json as $key=>$item){
                $allowanceItems.='<tr>';
                $allowanceItems .= '<td class="col-md-1"><button type="button" data-amount="'.$item.'" data-id="'.$results->id.'" value="'.$key.'" class="btn btn-danger btn-xs delete_allowance_item">x</button></td><td><input type="hidden" name="added_addtion_id[]" value="'.$key.'">'.$addtitionObj->get_addition_name($key).'</td>';
                $allowanceItems .= '<td><input type="hidden" name="added_addtion_amount" value="'.$item.'">'.$item.'</td>';
                $allowanceItems.='</tr>';
            }
            $allowanceItems.='</tbody>';
            $deductionItems='<tbody>';
            foreach ($deduction_items_json as $key_d=>$item_d){
                $deductionItems.='<tr>';
                $deductionItems .= '<td class="col-md-1"><button type="button" data-amount="'.$item_d.'" data-id="'.$results->id.'" value="'.$key_d.'" class="btn btn-danger btn-xs delete_deduction_item">x</button></td><td><input type="hidden" name="added_deduction_id[]" value="'.$key_d.'">'.$deductionObj->get_deduction_name($key_d).'</td>';
                $deductionItems .= '<td><input type="hidden" name="added_deduction_amount" value="'.$item_d.'">'.$item_d.'</td>';
                $deductionItems.='</tr>';
            }
            if ($results->adjusted_amount_per_transaction>0){
                $deductionItems.='<tr>';
                $deductionItems .= '<td class="col-md-1"></td><td>Adjusted Amount</td>';
                $deductionItems .= '<td>'.$results->adjusted_amount_per_transaction.'</td>';
                $deductionItems.='</tr>';

                $total_deduction = $total_deduction+$results->adjusted_amount_per_transaction;
            }
            $deductionItems.='</tbody>';
            $return = array(
                'prDetails_id'=>$results->id,
                'basic_salary'=>$results->basic_salary,
                'payroll_info_basic_salary'=>$results->payroll_info_basic_salary,
                'allowance'   => $allowanceItems,
                'total_payment'   => $results->basic_salary+$total_allowance,
                'deduction'   => $deductionItems,
                'total_deduction'   => $total_deduction,
                'net_total'   => ($results->basic_salary+$total_allowance)-$total_deduction,
                'absence_day'=>isset($results->number_of_absence_day)?$results->number_of_absence_day:0,
                'allowance_description'=>isset($results->allowance_description)?$results->allowance_description:'',
                'deduction_description'=>isset($results->deduction_description)?$results->deduction_description:'',
                );
            wp_send_json($return);
        }else{
            return false;
        }die();
    }

    function add_addition_item_into_json(){
        if(!empty($_POST["prDetailId"])){

            global $wpdb;
            $table_prefix = $wpdb->prefix;
            $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar_run_details";
            $prDetailId = $_POST["prDetailId"];
            $addItemId = $_POST["addItemId"];
            $addItemAmount = $_POST["addItemAmount"];

            $results = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix .'rbs_erp_payroll_pay_calendar_run_details WHERE id=%d', $prDetailId));
            $data= array();
            $data['allowance']= $this->addItemIntoJson($results->allowance, $addItemId, $addItemAmount );
            $data['rate_allowance']= $this->addItemIntoJson($results->allowance, $addItemId, $addItemAmount );
            if($wpdb->update(
                $table_name,$data,
                array( 'id' => $prDetailId )
            )){
                return true;
            }die;
        }
    }

    function add_addition_description(){
        if(!empty($_POST["prDetailId"])){

            global $wpdb;
            $table_prefix = $wpdb->prefix;
            $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar_run_details";
            $prDetailId = $_POST["prDetailId"];
            $allowance_description = $_POST["allowance_description"];
            $data= array();
            $data['allowance_description']= $allowance_description;
            if($wpdb->update(
                $table_name,$data,
                array( 'id' => $prDetailId )
            )){
                return true;
            }die;
        }
    }

    function delete_addition_item_from_json(){
        if(!empty($_POST["prDetailId"])){

            global $wpdb;
            $table_prefix = $wpdb->prefix;
            $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar_run_details";
            $prDetailId = $_POST["prDetailId"];
            $removeItem = $_POST["removeItem"];

            $results = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix .'rbs_erp_payroll_pay_calendar_run_details WHERE id=%d', $prDetailId));
            $data= array();
            //var_dump($results->allowance);die;
            $data['allowance']= $this->removeItemFromJson($results->allowance,$removeItem);
            $data['rate_allowance']= $this->removeItemFromJson($results->allowance,$removeItem);
//            var_dump($this->removeItemFromJson($results->allowance,$removeItem));die;
            if($wpdb->update(
                $table_name,$data,
                array( 'id' => $prDetailId )
            )){
                return true;
            }die;
        }
    }

    function add_deduction_item_into_json(){
        if(!empty($_POST["prDetailId"])){

            global $wpdb;
            $table_prefix = $wpdb->prefix;
            $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar_run_details";
            $prDetailId = $_POST["prDetailId"];
            $addItemId = $_POST["addItemId"];
            $addItemAmount = $_POST["addItemAmount"];

            $results = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix .'rbs_erp_payroll_pay_calendar_run_details WHERE id=%d', $prDetailId));
            $data= array();
            $data['deduction']= $this->addItemIntoJson($results->deduction, $addItemId, $addItemAmount );
            $data['rate_deduction']= $this->addItemIntoJson($results->deduction, $addItemId, $addItemAmount );
            if($wpdb->update(
                $table_name,$data,
                array( 'id' => $prDetailId )
            )){
                return true;
            }die;
        }
    }

    function add_deduction_description(){
        if(!empty($_POST["prDetailId"])){

            global $wpdb;
            $table_prefix = $wpdb->prefix;
            $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar_run_details";
            $prDetailId = $_POST["prDetailId"];
            $deduction_description = $_POST["deduction_description"];
            $data= array();
            $data['deduction_description']= $deduction_description;
            if($wpdb->update(
                $table_name,$data,
                array( 'id' => $prDetailId )
            )){
                return true;
            }die;
        }
    }

    function delete_deduction_json_item(){
        if(!empty($_POST["prDetailId"])){

            global $wpdb;
            $table_prefix = $wpdb->prefix;
            $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar_run_details";
            $prDetailId = $_POST["prDetailId"];
            $removeItem = $_POST["removeItem"];

            $results = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix .'rbs_erp_payroll_pay_calendar_run_details WHERE id=%d', $prDetailId));
            $data= array();
            $data['deduction']= $this->removeItemFromJson($results->deduction,$removeItem);
            $data['rate_deduction']= $this->removeItemFromJson($results->deduction,$removeItem);
            if($wpdb->update(
                $table_name,$data,
                array( 'id' => $prDetailId )
            )){
                return true;
            }die;
        }
    }

    function removeItemFromJson($items, $remove_id){
        $items = json_decode($items , true);
        if(array_key_exists($remove_id, $items)){
            unset($items[$remove_id]);
        }
        $items = json_encode($items );
        return $items;
    }

    function addItemIntoJson($items, $item_id, $item_amount){
        $items = json_decode($items , true);
        if(!array_key_exists($item_id, $items)){
            $items[$item_id]=$item_amount;
        }
        $items = json_encode($items );
        return $items;
    }




    function update_absence_day(){
        if(!empty($_POST["prDetailId"])){

            global $wpdb;
            $table_prefix = $wpdb->prefix;
            $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar_run_details";

            $prDetailId = $_POST["prDetailId"];
            $numberOfAbsence = $_POST["numberOfAbsence"];
            $total_number_of_day = $_POST["total_number_of_day"];
            $results = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix .'rbs_erp_payroll_pay_calendar_run_details WHERE id=%d', $prDetailId));

            $basic_salary =isset($results->payroll_info_basic_salary)? (($results->payroll_info_basic_salary/$total_number_of_day)*($total_number_of_day-$numberOfAbsence)):0;

            $data= array();
            $data['basic_salary']= $basic_salary;
            $data['number_of_absence_day']= $numberOfAbsence;
            $data['allowance']= $this->updateItemIntoJson($results->rate_allowance, $total_number_of_day, $numberOfAbsence );
//            $data['deduction']= $this->updateItemIntoJson($results->deduction, $total_number_of_day, $numberOfAbsence );
            if($wpdb->update(
                $table_name,$data,
                array( 'id' => $prDetailId )
            )){
                $return_results = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix .'rbs_erp_payroll_pay_calendar_run_details WHERE id=%d', $prDetailId));
                $return = array(
                    'absence_day'=>isset($return_results->number_of_absence_day)?$return_results->number_of_absence_day:0,
                );
                wp_send_json($return);
            }die;
        }
    }

    function update_adjusted_amount_per_transaction(){
        if(!empty($_POST["prDetailId"])){

            global $wpdb;
            $table_prefix = $wpdb->prefix;
            $table_name = $table_prefix . "rbs_erp_payroll_pay_calendar_run_details";
            $table_name_advance_payment_info = $table_prefix . "rbs_erp_advance_payments_info";

            $prDetailId = $_POST["prDetailId"];
            $adjusted_amount_per_transaction = $_POST["adjusted_amount_per_transaction"];
            $employee_id = $_POST["employee_id"];

            $results = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix .'rbs_erp_payroll_pay_calendar_run_details WHERE id=%d', $prDetailId));
            $advancPaymentObj = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $table_name_advance_payment_info .' WHERE employee_id=%d', $employee_id));
           if($advancPaymentObj){
               if($advancPaymentObj->total_advance_amount > $advancPaymentObj->total_adjusted_amount){
                   $remaining_adjusted_amount = $advancPaymentObj->total_advance_amount > $advancPaymentObj->total_adjusted_amount? $advancPaymentObj->total_advance_amount - $advancPaymentObj->total_adjusted_amount:0;
                   if($remaining_adjusted_amount<$adjusted_amount_per_transaction){
                       $adjusted_amount_per_transaction = $remaining_adjusted_amount;
                   }
                   $data= array();
                   $data['adjusted_amount_per_transaction']= $adjusted_amount_per_transaction;
                   if($wpdb->update(
                       $table_name,$data,
                       array( 'id' => $prDetailId )
                   )){
                       $transCount = $results->adjusted_amount_per_transaction==0?1:0;
                       $payment_array = array();
                       $payment_array['total_adjusted_amount']= ($advancPaymentObj->total_adjusted_amount+$results->adjusted_amount_per_transaction)-$adjusted_amount_per_transaction;
                       $payment_array['transaction_count']= $advancPaymentObj->transaction_count+$transCount;
                       $wpdb->update(
                           $table_name_advance_payment_info,$payment_array,
                           array( 'id' => $advancPaymentObj->id )
                       );
                   }die;
               }
           }

        }
    }

    function updateItemIntoJson($items, $totalNumberOfDays, $absenceDay){

        $items = json_decode($items , true);
        $options = get_option( 'payroll_plugin_setting_options' );
        $selectd_addtion = isset($options['selectd_addtion'])?$options['selectd_addtion']:'';
        if($items){
            foreach ($items as  $key=>$item) {

                if (in_array($key,$selectd_addtion)) {
                    $items[$key] = number_format((($item / $totalNumberOfDays) * ($totalNumberOfDays - $absenceDay)), '2', '.', '');

                }else{
                    $items[$key]= $item;
                }
            }

        }
        $items = json_encode($items );
        return $items;
    }



}

new Payroll_Run_Ajax();
