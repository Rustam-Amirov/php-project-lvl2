<?php

namespace Differ\GenDiff\Formatters\Pretty;

function render($diff)
{
    $result = iter($diff);
    return "{" . $result . "\n}\n";
}


function iter($diff, $countTab = '  ')
{
    $newTree = array_map(function ($v) use ($countTab) {
        if ($v['type'] == 'nested') {
            $iter = buildNode($countTab, ' ', $v['key'], getValue($v['children'], $countTab));
        } elseif ($v['type'] === 'changed') {
            $iter = buildChangedNode($countTab, $v['key'], $v['newValue'], $v['oldValue']);
        } elseif ($v['type'] === 'added') {
            $iter = buildNode($countTab, '+', $v['key'], getValue($v['newValue'], $countTab));
        } elseif ($v['type'] === 'deleted') {
            $iter = buildNode($countTab, '-', $v['key'], getValue($v['oldValue'], $countTab));
        } else {
            $iter = buildNode($countTab, ' ', $v['key'], getValue($v['oldValue'], $countTab));
        }
        return $iter;
    }, $diff);
    return implode($newTree);
}
        

function buildChangedNode($tab, $key, $newValue, $oldValue)
{
    $newNode = buildNode($tab, '+', $key, getValue($newValue, $tab));
    $oldNode = buildNode($tab, '-', $key, getValue($oldValue, $tab));
    return $newNode . $oldNode;
}


function buildNode($tab, $type, $key, $value)
{
    return "\n" . $tab . $type . ' ' . $key . ': ' . $value;
}


function getValue($tree, $tab = '')
{
    if (is_array($tree)) {
        $s = array_map(function ($val) use ($tab) {
            return iter([$val], $tab . '    ');
        }, $tree);
        $value = '{' . implode($s) . "\n" . $tab . "  }";
    } elseif (is_object($tree)) {
        $keys = array_keys(get_object_vars($tree));
        $s = array_map(function ($key) use ($tree, $tab) {
            return "{\n " . $tab . '     ' . $key . ': ' . $tree->$key . "\n" . $tab . "  }";
        }, $keys);
        $value = implode($s);
    } elseif (is_bool($tree)) {
        $value = $tree ? 'true' : 'false';
    } else {
        $value = (string)$tree;
    }
    return $value;
}
