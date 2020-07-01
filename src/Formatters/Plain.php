<?php

namespace Differ\GenDiff\Formatters\Plain;

use function Differ\GenDiff\Formatters\Pretty\stringify;

function plain()
{
    return function ($diff) {
        $result = parse($diff);
        return $result;
    };
}

function parse($diff, $path = '')
{
    return array_reduce($diff, function ($acc, $v) use ($path) {
        $path = ($path == '') ? $v['key'] : $path . '.' . $v['key'];
        if ($v['diff'] === 'nested') {
            $acc .= parse($v['children'], $path);
        } elseif ($v['diff'] === 'deleted') {
            $acc .= "Property '" . $path . "' was removed\n";
        } elseif ($v['diff'] === 'added') {
            $value = is_object($v['newValue']) ? 'complex value' : stringify($v['newValue']);
            $acc .= "Property '" . $path . "' was added with value: '" . stringify($value) . "'\n";
        } elseif ($v['diff'] === 'changed') {
            $valueOld = stringify($v['oldValue']);
            $valueNew = stringify($v['newValue']);
            $acc .= "Property '" . $path . "' was changed. From '" . $valueOld . "' to '" . $valueNew . "'\n";
        }
        return $acc;
    }, '');
}
