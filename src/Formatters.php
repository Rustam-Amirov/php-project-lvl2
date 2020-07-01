<?php

namespace Differ\Formatters;

use function Differ\GenDiff\Formatters\Pretty\pretty;
use function Differ\GenDiff\Formatters\Plain\plain;
use function Differ\GenDiff\Formatters\GetJson\getJson;

function getFormatter($format)
{
    switch ($format) {
        case 'pretty':
            $fn = pretty();
            break;
        case 'plain':
            $fn = plain();
            break;
        case 'json':
            $fn = getJson();
            break;
    }
    return $fn;
}
