<?php

namespace Differ\GenDiff;

use function Funct\Collection\flattenAll;

function genDiff($request, $secondPathToFile = false)
{
    if (file_exists(__DIR__ . '/' . $request['<firstFile>']) && file_exists(__DIR__ . '/' . $request['<secondFile>'])) {
        $firstFile = json_decode(file_get_contents(__DIR__ . '/' . $request['<firstFile>']), true);
        $secondFile = json_decode(file_get_contents(__DIR__ . '/' . $request['<secondFile>']), true);
    } elseif (file_exists(__DIR__ . '/' . $request) && file_exists(__DIR__ . '/' . $secondPathToFile)) {
        $firstFile = json_decode(file_get_contents(__DIR__ . '/' . $request), true);
        $secondFile = json_decode(file_get_contents(__DIR__ . '/' . $secondPathToFile), true);
    } else {
        echo 'Error';
    }
    
    $keys = [array_keys($firstFile), array_keys($secondFile)];

    $string = array_reduce(array_unique(flattenAll($keys)), function ($acc, $key) use ($firstFile, $secondFile) {
        if (isset($firstFile[$key]) && isset($secondFile[$key])) {
            if ($secondFile[$key] === $firstFile[$key]) {
                $acc = $acc . stringify($key, $secondFile[$key]);
            } else {
                $acc = $acc . stringify($key, $secondFile[$key], '-');
                $acc = $acc . stringify($key, $firstFile[$key], '+');
            }
        } elseif (isset($secondFile[$key])) {
            $acc = $acc . stringify($key, $secondFile[$key], '-');
        } else {
            $acc = $acc . stringify($key, $firstFile[$key], '+');
        }
        return $acc;
    }, "");
        printResult($string);
}

function stringify($key, $value, $prefix = ' ')
{
    if (is_bool($key) && is_bool($value)) {
        return $prefix . " " . json_encode($key) . ": " . json_encode($value) . "\n";
    } elseif (is_bool($key)) {
        return $prefix . " " . json_encode($key) . ": " . $value . "\n";
    } elseif (is_bool($value)) {
        return $prefix . " " . $key . ": " . json_encode($value) . "\n";
    } else {
        return $prefix . " " . $key . ": " . $value . "\n";
    }
}

function printResult($result)
{
    echo("{\n$result}\n");
}
