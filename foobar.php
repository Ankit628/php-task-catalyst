<?php

$arr = [];
for ($i = 1; $i <= 100; $i++) {
    $mod3 = $i % 3;
    $mod5 = $i % 5;
    switch (true) {
        case ($mod3 == 0 && $mod5 == 0):
            $arr[] = 'foobar';
            break;
        case ($mod3 == 0):
            $arr[] = 'foo';
            break;
        case ($mod5 == 0):
            $arr[] = 'bar';
            break;
        default:
            $arr[] = $i;
    }
}

print_r($arr);