<?php

namespace Differ\GenDiff\Formatters\Pretty;

const MARK = [
    'deleted' => '-',
    'added' => '+',
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
        if ($v['diff'] == 'nested') {
            $acc .= buildNode($countTab, MARK[$v['diff']], $v['key'], getValue($v['children'], $countTab));
        } elseif ($v['diff'] === 'changed') {
            $acc .= buildNode($countTab, '+', $v['key'], getValue($v['newValue'], $countTab));
            $acc .= buildNode($countTab, '-', $v['key'], getValue($v['oldValue'], $countTab));
        } elseif ($v['diff'] === 'added') {
            $acc .= buildNode($countTab, MARK[$v['diff']], $v['key'], getValue($v['newValue'], $countTab));
        } else {
            $acc .= buildNode($countTab, MARK[$v['diff']], $v['key'], getValue($v['oldValue'], $countTab));
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


function buildNode($tab, $type, $key, $value)
{
    return "\n" . $tab . $type . ' ' . $key . ': ' . $value;
}


function getValue($tree, $tab = '')
{
    if (is_array($tree)) {
        $s =  array_map(function ($val) use ($tab) {
            return  parse([$val], $tab . '    ');
        }, $tree);
        return '{' . implode($s) . "\n" . $tab . "  }";
    }
    if (!is_object($tree)) {
        return stringify($tree);
    }
    $keys  = array_keys(get_object_vars($tree));
    return array_reduce($keys, function ($acc, $key) use ($tree, $tab) {
        if (!is_object($tree->$key)) {
            return $acc . stringify("{\n " . $tab . '     ' . $key . ': ' . $tree->$key . "\n" . $tab . "  }");
        } else {
            return $acc . "{\n" . parse($tree->$key, $tab . '    ');
        }
    }, '');
}
