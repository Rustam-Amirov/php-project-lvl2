<?php

namespace Differ\GenDiff\Formatters\Pretty;

use function Funct\Strings\countOccurrences;
use function Funct\Strings\padLeft;

function pretty()
{
    return function ($diff) {
        $res =  parser($diff);
        return "{" . $res . "\n}\n";
    };
}
function parser($diff)
{
    return array_reduce($diff, function ($acc, $v) {
        $countTab = padLeft('', (countOccurrences($v['path'], '/') * 4 - 2));
        if (isset($v['children'])) {
            $children = parser($v['children']);
            $acc .= "\n" . $countTab . $v['diff'] . ' ' . $v['key'] . ': {' . $children . "\n" . $countTab . '  }';
        } else {
            if ($v['diff'] == '+' || $v['diff'] == '-' || $v['diff'] == ' ') {
                $acc .= "\n" . $countTab . $v['diff'] . ' ' . $v['key'] . ': ' . stringify($v['value']);
            } elseif ($v['diff'] == '=') {
                $acc .= "\n" . $countTab . '  ' . $v['key'] . ': ' . stringify($v['value']);
            } else {
                $acc .= "\n" . $countTab . '+ ' . $v['key'] . ': ' . stringify($v['value2']);
                $acc .= "\n" . $countTab . '- ' . $v['key'] . ': ' . stringify($v['value1']);
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
