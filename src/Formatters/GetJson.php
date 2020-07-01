<?php

namespace Differ\GenDiff\Formatters\GetJson;

function getJson()
{
    return function ($tree) {
        $result = json_encode(parser($tree));
        return $result;
    };
}

const MARK = [
    'deleted' => "-",
    'added' => "+",
    'unchanged' => "",
    'nested' => " "
];


function parser($tree)
{
    return array_reduce($tree, function ($acc, $v) {
        if ($v['diff'] === 'nested') {
            $acc[] = [MARK[$v['diff']] . " " . $v['key'] => parser($v['children'])];
        } elseif ($v['diff'] === 'changed') {
            $acc[] = ["+ " . $v['key'] => $v['newValue']];
            $acc[] = ["- " . $v['key'] => $v['oldValue']];
        } elseif ($v['diff'] === 'added') {
            $acc[] = [MARK[$v['diff']] . " " . $v['key'] => $v['newValue']];
        } else {
            $acc[] = [MARK[$v['diff']] . " " . $v['key'] => $v['oldValue']];
        }
        return $acc;
    }, []);
}
