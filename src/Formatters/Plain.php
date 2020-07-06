<?php

namespace Differ\GenDiff\Formatters\Plain;

function plain()
{
    return function ($diff) {
        $result = parse($diff);
        return $result;
    };
}

function parse($diff, $path = '')
{
    $result = array_map(function ($v) use ($path) {
        $path = ($path == '') ? $v['key'] : $path . '.' . $v['key'];
        $iter = '';
        if ($v['type'] === 'nested') {
            $iter = parse($v['children'], $path);
        } elseif ($v['type'] === 'deleted') {
            $iter = sprintf("Property '%s' was removed\n", $path);
        } elseif ($v['type'] === 'added') {
            $iter = sprintf("Property '%s' was added with value: '%s'\n", $path, stringify($v['newValue']));
        } elseif ($v['type'] === 'changed') {
            $valueOld = stringify($v['oldValue']);
            $valueNew = stringify($v['newValue']);
            $iter = sprintf("Property '%s' was changed. From '%s' to '%s'\n", $path, $valueOld, $valueNew);
        }
        return $iter;
    }, $diff);
    return implode($result);
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
