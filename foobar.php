<?php

$arr = [];
for ($i = 1; $i <= 100; $i++) {
    $mod3 = $i % 3;
    $mod5 = $i % 5;
    switch (true) {
        case ($mod3 == 0 && $mod5 == 0):
            echo "foobar\n";
            break;
        case ($mod3 == 0):
            echo "foo\n";
            break;
        case ($mod5 == 0):
            echo "bar\n";
            break;
        default:
            echo "$i\n";
    }
}