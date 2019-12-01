<?php

function getCalculateAmountByAbsenceDay($amount, $totalDay, $absenceDay){

    if(!empty($absenceDay)|| $absenceDay!=0){
        $amount = ($amount/($totalDay-$absenceDay))*$totalDay;
        return round($amount);
    }else{
        return $amount;
    }

}


function payCalenderType() {

        $pay_calender_type = array(
            'monthly'=>'Monthly',
            'daily'=>'Daily',
            'weekly'=>'Weekly',
            'individual'=>'Individual'
        );

    return apply_filters( 'rbs_pay_calender_type', $pay_calender_type );
}