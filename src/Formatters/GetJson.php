<?php

namespace Differ\GenDiff\Formatters\GetJson;

function getJson()
{
    return function ($tree) {
        $result = json_encode($tree);
        return $result;
    };
}
