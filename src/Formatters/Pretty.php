<?php

namespace Differ\GenDiff\Formatters\Pretty;

function render($diff)
{
    $result = iter($diff);
    return sprintf("{\n%s\n}", $result);
}


function iter($diff, $level = 1)
{
    $newTree = array_map(function ($v) use ($level) {
        $tab = str_repeat(' ', $level * 4 - 2);
        if ($v['type'] === 'nested') {
            $children = sprintf("{\n%s\n  %s}", iter($v['children'], $level + 1), $tab);
            $iter = bringNodeToString($tab, ' ', $v['key'], stringify($children, $tab));
        } elseif ($v['type'] === 'changed') {
            $newNode = bringNodeToString($tab, '+', $v['key'], stringify($v['newValue'], $tab));
            $oldNode = bringNodeToString($tab, '-', $v['key'], stringify($v['oldValue'], $tab));
            $iter = $newNode . "\n" . $oldNode;
        } elseif ($v['type'] === 'added') {
            $iter = bringNodeToString($tab, '+', $v['key'], stringify($v['newValue'], $tab));
        } elseif ($v['type'] === 'deleted') {
            $iter = bringNodeToString($tab, '-', $v['key'], stringify($v['oldValue'], $tab));
        } else {
            $iter = bringNodeToString($tab, ' ', $v['key'], stringify($v['oldValue'], $tab));
        }
        return $iter;
    }, $diff);
    return implode("\n", $newTree);
}
        

function bringNodeToString($tab, $type, $key, $value)
{
    return sprintf("%s%s %s: %s", $tab, $type, $key, $value);
}


function stringify($tree, $tab = '')
{
    if (is_bool($tree)) {
        return $tree ? 'true' : 'false';
    }
    if (!is_array($tree) && !is_object($tree)) {
        return (string)$tree;
    }
    if (is_array($tree)) {
        $values = array_map(function ($val) use ($tab) {
            return implode($val);
        }, $tree);
        return  "{{implode($values)},\n $tab }";
    }
    if (is_object($tree)) {
        $keys = array_keys(get_object_vars($tree));
        $values = array_map(function ($key) use ($tree, $tab) {
            return sprintf("{\n %s     %s: %s\n%s  }", $tab, $key, $tree->$key, $tab);
        }, $keys);
        return implode($values);
    }
}
