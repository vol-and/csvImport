<?php


function detectCsvDelimiter($fh)
{
    $delimiters = ["\t", ";", "|", ","];
    $data_1 = [];
    $data_2 = [];
    $delimiter = $delimiters[0];
    foreach ($delimiters as $d) {
        $data_1 = fgetcsv($fh, 8192, $d);
        if (sizeof($data_1) > sizeof($data_2)) {
            $delimiter = $d;
            $data_2 = $data_1;
        }
        rewind($fh);
    }
    return $delimiter;
}

function createObjectFromCsv($file)
{
    $numcols = [];
    while (($row = fgetcsv($file, 8192, ',')) !== false) {
        $numcols = count($row);
        if (array(null) !== $row) {
            $csv[] = $row;
        }
    }
    $array = array_filter(
        $csv,
        fn($val) => 0 < array_reduce(
                $val,
                fn($carry, $item) => empty(trim($item)) ? $carry : $carry + 1,
                0
            )
    );

    return (object)array('numcols' => $numcols, 'array' => $array);
}

function checkColumnsQuantity($quantity, $check)
{
    $bool = false;
    if ($quantity == $check) $bool = true;

    return $bool;
}

function generateRandomString($length = 10)
{
    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}