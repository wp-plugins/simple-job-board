<?php

// Assign Default Radio button Check
function job_board_is_checked ( $i )
{
    $checked = ( $i == 0 ) ? "checked" : NULL;
    return $checked;
}

// Merging Arrays
function mergeArrays ( $job_application_field_name, $job_application_field_type, $job_application_field_option )
{
    $result = array ();
    foreach ( $job_application_field_name as $key => $name ) {
        $result = array_merge ( $result, array (
            $name => array (
                'type' => $job_application_field_type[ $key ],
                'option' => $job_application_field_option[ $key ]
            )
                ) );
    }

    return $result;
}
