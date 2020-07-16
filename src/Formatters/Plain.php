<?php

namespace Differ\GenDiff\Formatters\Plain;

function render($diff)
{
    $result = iter($diff);
    return $result;
}

function iter($diff, $path = '')
{
    $filterDiff = array_filter($diff, fn($v) => $v['type'] !== 'unchanged');
    $result = array_map(function ($v) use ($path) {
        $newPath = ($path == '') ? $v['key'] : sprintf("%s.%s", $path, $v['key']);
        switch ($v['type']) {
            case 'nested':
                return iter($v['children'], $newPath);
            case 'deleted':
                return sprintf("Property '%s' was removed", $newPath);
            case 'added': 
                return  sprintf("Property '%s' was added with value: '%s'", $newPath, stringify($v['newValue']));
            default:
                $value = ['old' => stringify($v['oldValue']), 'new' => stringify($v['newValue'])];
                return  sprintf("Property '%s' was changed. From '%s' to '%s'", $newPath, $value['old'], $value['new']);
            }
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
