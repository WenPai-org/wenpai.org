<?php

namespace Branda_Vendor;

// Don't redefine the functions if included multiple times.
if (!\function_exists('Branda_Vendor\\GuzzleHttp\\Promise\\promise_for')) {
    require __DIR__ . '/functions.php';
}
