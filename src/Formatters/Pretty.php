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
    $newTree = array_map(function ($v) use ($countTab) {
        if ($v['type'] == 'nested') {
            return buildNode($countTab, MARK[$v['type']], $v['key'], getValue($v['children'], $countTab));
        } elseif ($v['type'] === 'changed') {
            $iter =  buildNode($countTab, '+', $v['key'], getValue($v['newValue'], $countTab));
            $iter .= buildNode($countTab, '-', $v['key'], getValue($v['oldValue'], $countTab));
            return $iter;
        } elseif ($v['type'] === 'added') {
            return buildNode($countTab, MARK[$v['type']], $v['key'], getValue($v['newValue'], $countTab));
        } else {
            return buildNode($countTab, MARK[$v['type']], $v['key'], getValue($v['oldValue'], $countTab));
        }
    }, $diff);
    return implode($newTree);
}
        

function stringify($value)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    } else {
        return $value;
    }
}


function buildNode($tab, $type, $key, $value)
{
    return "\n" . $tab . $type . ' ' . $key . ': ' . $value;
}


function getValue($tree, $tab = '')
{
    if (is_array($tree)) {
        $s = array_map(function ($val) use ($tab) {
            return  parse([$val], $tab . '    ');
        }, $tree);
        return '{' . implode($s) . "\n" . $tab . "  }";
    }
    if (!is_object($tree)) {
        return stringify($tree);
    }
    $keys = array_keys(get_object_vars($tree));
    $result = array_map(function ($key) use ($tree, $tab) {
        if (!is_object($tree->$key)) {
            return stringify("{\n " . $tab . '     ' . $key . ': ' . $tree->$key . "\n" . $tab . "  }");
        } else {
            return "{\n" . parse($tree->$key, $tab . '    ');
        }
    }, $keys);
    return implode($result);
}
