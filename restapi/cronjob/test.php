<?php

date_default_timezone_set('Asia/Jerusalem');

$var = date('H-i');
echo $var;

$targetTime = "19-06";

$b = $var == $targetTime;

if ( $var == $targetTime ) {
    echo 'strings are the same';
} else {
    echo 'strings different';
}
