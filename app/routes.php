<?php declare(strict_types = 1);
return [
    ['GET', '/', ['Controllers\Home', 'list']],
    ['GET', '/another-route', function () {echo 'This works too';}],
];