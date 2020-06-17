<?php

namespace Differ\GenDiff\Formatters\GetJson;

use function Differ\GenDiff\Formatters\Pretty\stringify;

function getJson()
{
    return function ($tree) {
        $result = json_encode(parser($tree));
        return $result;
    };
}

function parser($tree)
{
    return array_reduce($tree, function ($acc, $v) {
        if (isset($v['children'])) {
            $acc[] = [$v['diff'] . ' ' . $v['key'] => parser($v['children'])];
        } else {
            if ($v['diff'] == '+' || $v['diff'] == '-' || $v['diff'] == ' ') {
                $acc[] = [$v['diff'] . ' ' . $v['key'] => stringify($v['value'])];
            } elseif ($v['diff'] == '=') {
                $acc[] = [' ' . $v['key'] => stringify($v['value'])];
            } else {
                $acc[] = ['+ ' . $v['key'] => stringify($v['value2'])];
                $acc[] = ['- ' . $v['key'] => stringify($v['value1'])];
            }
        }
        return $acc;
    }, []);
}
