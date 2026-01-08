<?php

if (!function_exists('getKerusakanColor')) {
    function getKerusakanColor($value) {
        $r = intval(0 + ($value / 100) * 255);
        $g = intval(200 - ($value / 100) * 200);
        return "rgb($r, $g, 0)";
    }
}

if (!function_exists('getProgressColor')) {
    function getProgressColor($value) {
        $r = intval(255 - ($value / 100) * 255);
        $g = intval(0 + ($value / 100) * 200);
        return "rgb($r, $g, 0)";
    }
}
