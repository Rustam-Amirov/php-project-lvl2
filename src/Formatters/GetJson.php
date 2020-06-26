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

const MARK = [
    'deleted' => '-',
    'add' => '+',
    'unchanged' => '',
    'nested' => ' '
];


function parser($tree)
{
    return array_reduce($tree, function ($acc, $v) {
        if (!empty($v['children'])) {
            $acc[] = [MARK[$v['diff']] . ' ' . $v['key'] => parser($v['children'])];
        } else {
            if ($v['diff'] == 'changed') {
                $acc[] = ['+ ' . $v['key'] => stringify($v['value']['new'])];
                $acc[] = ['- ' . $v['key'] => stringify($v['value']['old'])];
            } else {
                $acc[] = [MARK[$v['diff']] . ' ' . $v['key'] => stringify($v['value'])];
            }
        }
        return $acc;
    }, []);
}

function substituteDifference($diff)
{
}
