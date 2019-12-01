<?php

function deductionDropdown($defaultCountry = "", $id = "", $name = "", $classes = ""){

    $deductionObject = new Deduction();
    $deductions = $deductionObject -> getActiveDeduction();
    $output = "<select id='".$id."' name='".$name."' class='".$classes."'>";
    $output .= sprintf( '<option value="-1">%s</option>', __( 'Select', 'rbs-erp' ) );
    foreach($deductions as $deduction){

        $output .= "<option value='".$deduction->id."' ".(($deduction->id==strtoupper($defaultCountry))?"selected":"").">".$deduction->deduction_name." </option>";
    }

    $output .= "</select>";

    return $output;
}

function deductionDropdownOptions(){

    $deductionObject = new Deduction();
    $deductions = $deductionObject-> getActiveDeduction();
    $dropdown    = array( '-1' => __( 'Select Deduction', 'rbs-erp' ) );
    foreach($deductions as $deduction){
        $dropdown[$deduction->id]= stripslashes($deduction->deduction_name);

    }

    return $dropdown;
}
