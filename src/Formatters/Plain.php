<?php

namespace Differ\GenDiff\Formatters\Plain;

use function Differ\GenDiff\Formatters\Pretty\stringify;

const SUFFIXOUTPUT = [
        '+' => ' was added with value:',
        '-' => ' was removed',
        '' => '',
        ' ' => ' was changed. From'
];

function plain()
{
    return function ($diff) {
        //print_r($diff);
        $result = parser($diff);
        return $result;
    };
}

function parser($diff)
{
    return array_reduce($diff, function ($acc, $v) {
        if (isset($v['children']) && $v['diff'] == ' ') {
            $acc .= parser($v['children']);
        } else {
            $path = substr(str_replace('/', '.', $v['path']), 1);
            if ($v['diff'] == '-') {
                $acc .= "Property '" . $path . "' was removed\n";
            } elseif ($v['diff'] == '+') {
                $value = isset($v['children']) ? 'complex value' : stringify($v['value']);
                $acc .= "Property '" . $path . "' was added with value: '" . stringify($value) . "'\n";
            } elseif ($v['diff'] == '!') {
                $value1 = stringify($v['value1']);
                $value2 = stringify($v['value2']);
                $acc .= "Property '" . $path . "' was changed. From '" . $value1 . "' to '" . $value2 . "'\n";
            }
        }
        return $acc;
    }, '');
}
