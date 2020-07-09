<?php

namespace Differ\GenDiff\Formatters\Plain;

function render($diff)
{
    $result = iter($diff);
    return $result . "\n";
}

function iter($diff, $path = '')
{
    $filterDiff = array_filter($diff, fn($v) => $v['type'] !== 'unchanged');
    $result = array_map(function ($v) use ($path) {
        $newPath = ($path == '') ? $v['key'] : $path . '.' . $v['key'];
        if ($v['type'] === 'nested') {
            $iter = iter($v['children'], $newPath);
        } elseif ($v['type'] === 'deleted') {
            $iter = sprintf("Property '%s' was removed", $newPath);
        } elseif ($v['type'] === 'added') {
            $iter = sprintf("Property '%s' was added with value: '%s'", $newPath, stringify($v['newValue']));
        } else {
            $valueOld = stringify($v['oldValue']);
            $valueNew = stringify($v['newValue']);
            $iter = sprintf("Property '%s' was changed. From '%s' to '%s'", $newPath, $valueOld, $valueNew);
        }
        return $iter;
    }, $filterDiff);
    return implode("\n", $result);
}


function stringify($value)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    } elseif (is_object($value)) {
        return 'complex value';
    } else {
        return $value;
    }
}
