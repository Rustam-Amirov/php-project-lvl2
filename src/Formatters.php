<?php

namespace Differ\Formatters;

use function Differ\GenDiff\Formatters\Pretty\render as pretty;
use function Differ\GenDiff\Formatters\Plain\render as plain;
use function Differ\GenDiff\Formatters\Json\render as json;

function getFormatter($format)
{
    return function ($diff) use ($format) {
        switch ($format) {
            case 'pretty':
                return pretty($diff);
            break;
            case 'plain':
                return plain($diff);
            break;
            case 'json':
                return json($diff);
            break;
            default:
                throw new \Exception("Неподдерживаемый формат вывода: {$format}", 1);
        }
    };
}
