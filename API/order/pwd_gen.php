<?php
$digit = 0;
$pwd = "";
while ($digit < 10) {
    $type_c = rand(1, 2);
    switch ($type_c) {
        case 1:
            $pwd .= chr(random_int(48, 57));
            break;
        case 2:
            $pwd .= chr(random_int(65, 90));
            break;
    }
    $digit++;
}
