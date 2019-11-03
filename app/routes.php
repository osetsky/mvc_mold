<?php declare(strict_types = 1);
return [
    ['GET', '/hello-world', ['Controllers\Home', 'show']],
    ['GET', '/another-route', function () {echo 'This works too';}],
];