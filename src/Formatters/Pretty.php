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
        $indent = str_repeat(' ', $level * 4 - 2);
        if ($v['type'] === 'nested') {
            $children = sprintf("{\n%s\n  %s}", iter($v['children'], $level + 1), $indent);
            $iter = bringNodeToString($indent, ' ', $v['key'], stringify($children, $indent));
        } elseif ($v['type'] === 'changed') {
            $newNode = bringNodeToString($indent, '+', $v['key'], stringify($v['newValue'], $indent));
            $oldNode = bringNodeToString($indent, '-', $v['key'], stringify($v['oldValue'], $indent));
            $iter = $newNode . "\n" . $oldNode;
        } elseif ($v['type'] === 'added') {
            $iter = bringNodeToString($indent, '+', $v['key'], stringify($v['newValue'], $indent));
        } elseif ($v['type'] === 'deleted') {
            $iter = bringNodeToString($indent, '-', $v['key'], stringify($v['oldValue'], $indent));
        } else {
            $iter = bringNodeToString($indent, ' ', $v['key'], stringify($v['oldValue'], $indent));
        }
        return $iter;
    }, $diff);
    return implode("\n", $newTree);
}
        

function bringNodeToString($indent, $type, $key, $value)
{
    return sprintf("%s%s %s: %s", $indent, $type, $key, $value);
}


function stringify($tree, $indent = '')
{
    if (is_bool($tree)) {
        return $tree ? 'true' : 'false';
    }
    if (!is_array($tree) && !is_object($tree)) {
        return (string)$tree;
    }
    if (is_array($tree)) {
        $values = array_map(function ($val) use ($indent) {
            return implode($val);
        }, $tree);
        return  "{{implode($values)},\n $indent }";
    }
    if (is_object($tree)) {
        $keys = array_keys(get_object_vars($tree));
        $values = array_map(function ($key) use ($tree, $indent) {
            return sprintf("{\n %s     %s: %s\n%s  }", $indent, $key, $tree->$key, $indent);
        }, $keys);
        return implode($values);
    }
}
