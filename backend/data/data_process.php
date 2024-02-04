<?php

function setDataAsArrays($dataSet)
{
    $values = [];
    $dates = [];

    foreach ($dataSet as $data) {
        $attribute = ($data->{'@attributes'});

        array_push($values, $attribute->{'OBS_VALUE'});
        array_push($dates, $attribute->{'TIME_PERIOD'});
    }

    return ['values' => $values, 'dates' => $dates];
}
