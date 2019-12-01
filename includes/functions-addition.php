<?php

function additionDropdown($defaultAddition = "", $id = "", $name = "", $classes = ""){

    $additionObject = new Addition();
    $additions = $additionObject -> getActiveAddition();
    $output = "<select id='".$id."' name='".$name."' class='".$classes."'>";
    $output .= sprintf( '<option value="-1">%s</option>', __( 'Select', 'rbs-erp' ) );
    foreach($additions as $addition){

        $output .= "<option value='".$addition->id."' ".(($addition->id==strtoupper($defaultAddition))?"selected":"").">".$addition->addition_name." </option>";
    }

    $output .= "</select>";

    return $output;
}

function additionDropdownOptions(){

    $additionObject = new Addition();
    $additions = $additionObject-> getActiveAddition();
    $dropdown    = array( '-1' => __( 'Select Addition', 'rbs-erp' ) );
    foreach($additions as $addition){
        $dropdown[$addition->id]= stripslashes($addition->addition_name);

    }

    return $dropdown;
}
