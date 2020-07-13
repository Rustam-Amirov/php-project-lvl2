<?php

namespace Differ\Gendiff\Formatters;

function getFormatter($format)
{
    return function ($diff) use ($format) {
        switch ($format) {
            case 'pretty':
                return  Pretty\render($diff);
            case 'plain':
                return Plain\render($diff);
            case 'json':
                return Json\render($diff);
            default:
                throw new \Exception("Неподдерживаемый формат вывода: {$format}", 1);
        }
    };
}
