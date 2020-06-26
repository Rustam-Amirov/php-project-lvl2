<?php

namespace Differ\GenDiff\Formatters\Pretty;

use function Funct\Strings\countOccurrences;
use function Funct\Strings\padLeft;

const MARK = [
    'deleted' => '-',
    'add' => '+',
    'unchanged' => ' ',
    'nested' => ' '
];


function pretty()
{
    return function ($diff) {
        $res = parse($diff);
        return "{" . $res . "\n}\n";
    };
}
function parse($diff, $countTab = '  ')
{
    return array_reduce($diff, function ($acc, $v) use ($countTab) {
        if (!empty($v['children'])) {
            $child = parse($v['children'], $countTab . '    ');
            $acc .= "\n" . $countTab . MARK[$v['diff']] . ' ' . $v['key'] . ': {' . $child . "\n" . $countTab . '  }';
        } else {
            if ($v['diff'] == 'changed') {
                $acc .= "\n" . $countTab . '+ ' . $v['key'] . ': ' . stringify($v['value']['new']);
                $acc .= "\n" . $countTab . '- ' . $v['key'] . ': ' . stringify($v['value']['old']);
            } else {
                $acc .= "\n" . $countTab . MARK[$v['diff']] . ' ' . $v['key'] . ': ' . stringify($v['value']);
            }
        }
        return $acc;
    }, '');
}
        
function stringify($str)
{
    if (is_bool($str)) {
        return json_encode($str);
    } else {
        return $str;
    }
}
