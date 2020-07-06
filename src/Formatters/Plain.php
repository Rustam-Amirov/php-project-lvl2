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
        if ($v['type'] === 'nested') {
            return parse($v['children'], $path);
        } elseif ($v['type'] === 'deleted') {
            return sprintf("Property '%s' was removed\n", $path);
        } elseif ($v['type'] === 'added') {
            $value = is_object($v['newValue']) ? 'complex value' : stringify($v['newValue']);
            return sprintf("Property '%s' was added with value: '%s'\n", $path, $value);
        } elseif ($v['type'] === 'changed') {
            $valueOld = stringify($v['oldValue']);
            $valueNew = stringify($v['newValue']);
            return sprintf("Property '%s' was changed. From '%s' to '%s'\n", $path, $valueOld, $valueNew);
        }
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
