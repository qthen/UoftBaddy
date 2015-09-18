<?php
$array1 = array('domain.com','domain1.com','domain2.com','domain3.com','domain5.com','domaindd5.com');
$array2 = array('domain.com','domain12.com','domain22.com','domain32.com','domain42.com','domain5.com');
$array3 = array('domain.com','domain31.com','domain332.com','domain33.com','domain5.com','domaindd5.com');
sort($array1);
sort($array2);
sort($array3);
$end_of_array1 = end($array1);
reset($array1);
while (current($array1) != $end_of_array1) {
    if (current($array1) > current($array2)) {
        next($array2) || end($array2);
    }
    elseif (current($array1) < current($array2)) {
        next($array1) || end($array1);
    }
    else {
        //Array intersection, values are matching
        if (isset($duplicates[current($array1)])) {
            array_push($duplicates[current($array1)], 'array1', 'array2');
        }
        else {
            $duplicates[current($array1)] =  array('array1', 'array2');
        }
        next($array1);
        next($array2);
    }
}
reset($array1);
$end_of_array3 = end($array3);
reset($array1);
reset($array2);
reset($array3);
while (current($array3) != $end_of_array3){
    if (current($array1) > current($array3)) {
        next($array3) || end($array3);
    }
    elseif (current($array1) < current($array3)) {
        next($array1) || end($array1);
    }
    else {
        //Array intersection, values are matching
        if (isset($duplicates[current($array1)])) {
            array_push($duplicates[current($array1)], 'array1', 'array3');
        }
        else {
            $duplicates[current($array1)] = array('array1', 'array3');
        }
        next($array1);
        next($array3);
    }
}
reset($array2);
reset($array3);
while (current($array3) != $end_of_array3) {
    if (current($array2) > current($array3)) {
        next($array3) || end($array3);
    }
    elseif (current($array2) < current($array3)) {
        next($array2) || end($array2);
    }
    else {
        //Array intersection, values are matching
        if (isset($duplicates[current($array2)])) {
            array_push($duplicates[current($array2)], 'array2', 'array3');
        }
        else {
            $duplicates[current($array2)] =  array('array2', 'array3');
        }
        next($array2);
        next($array3);
    }
}
foreach ($duplicates as $key=>$array) {
    $duplicates[$key] = array_unique($array);
}
print_r($duplicates);
?>