<?php

namespace Differ\GenDiff\Formatters\Pretty;

function render($diff)
{
    $result = iter($diff);
    return sprintf("{%s\n}", $result);
}


function iter($diff, $countTab = '  ')
{
    $newTree = array_map(function ($v) use ($countTab) {
        if ($v['type'] === 'nested') {
            $children = sprintf("{%s\n  %s}", iter($v['children'], $countTab . '    '), $countTab);
            $iter = buildNode($countTab, ' ', $v['key'], stringify($children, $countTab));
        } elseif ($v['type'] === 'changed') {
            $iter = buildChangedNode($countTab, $v['key'], $v['newValue'], $v['oldValue']);
        } elseif ($v['type'] === 'added') {
            $iter = buildNode($countTab, '+', $v['key'], stringify($v['newValue'], $countTab));
        } elseif ($v['type'] === 'deleted') {
            $iter = buildNode($countTab, '-', $v['key'], stringify($v['oldValue'], $countTab));
        } else {
            $iter = buildNode($countTab, ' ', $v['key'], stringify($v['oldValue'], $countTab));
        }
        return $iter;
    }, $diff);
    return implode($newTree);
}
        

function buildChangedNode($tab, $key, $newValue, $oldValue)
{
    $newNode = buildNode($tab, '+', $key, stringify($newValue, $tab));
    $oldNode = buildNode($tab, '-', $key, stringify($oldValue, $tab));
    return $newNode . $oldNode;
}


function buildNode($tab, $type, $key, $value)
{
    return "\n" . $tab . $type . ' ' . $key . ': ' . $value;
}


function stringify($tree, $tab = '')
{
    if (is_array($tree)) {
        $s = array_map(function ($val) use ($tab) {
            return implode($val);
        }, $tree);
        return  sprintf("{%s \n %s }", implode($s), $tab);
    }
    if (is_object($tree)) {
        $keys = array_keys(get_object_vars($tree));
        $s = array_map(function ($key) use ($tree, $tab) {
            return sprintf("{\n %s     %s: %s\n%s  }", $tab, $key, $tree->$key, $tab);
        }, $keys);
        return implode($s);
    }
    if (is_bool($tree)) {
        return $tree ? 'true' : 'false';
    }
    return (string)$tree;
}
