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
        switch ($v['type']) {
            case 'nested':
                $children = sprintf("{\n%s\n  %s}", iter($v['children'], $level + 1), $indent);
                return bringNodeToString($indent, ' ', $v['key'], stringify($children, $indent));
            case 'changed':
                $newNode = bringNodeToString($indent, '+', $v['key'], stringify($v['newValue'], $indent));
                $oldNode = bringNodeToString($indent, '-', $v['key'], stringify($v['oldValue'], $indent));
                return $newNode . "\n" . $oldNode;
            case 'added':
                return bringNodeToString($indent, '+', $v['key'], stringify($v['newValue'], $indent));
            case 'deleted':
                return bringNodeToString($indent, '-', $v['key'], stringify($v['oldValue'], $indent));
            case 'unchanged':
                return bringNodeToString($indent, ' ', $v['key'], stringify($v['oldValue'], $indent));
            default:
                throw new \Exception("Unknown state in" . __DIR__ . "/Pretty/iter()", 1);
        }
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
