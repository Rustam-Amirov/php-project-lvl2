<?php

namespace Differ\GenDiff\Formatters\Json;

function render($tree)
{
    $result = json_encode($tree);
    return $result;
}
