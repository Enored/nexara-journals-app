<?php

$lines = file(__DIR__.'/../use_this_UI_for_dashboards/dist/index.html');
$chunk = implode('', array_slice($lines, 3224, 3637 - 3224));

$chunk = preg_replace('#assets/images/([^"\')]+)#', "{{ ubold_asset('images/$1') }}", $chunk);

file_put_contents(
    __DIR__.'/../resources/views/partials/dashboard/ubold/theme-offcanvas.blade.php',
    $chunk
);

echo "OK\n";
