<?php

namespace Differ\GenDiff;

use function Funct\Collection\flattenAll;

function genDiff($request, $secondPathToFile = false)
{
    if (isset($request['<firstFile>']) && file_exists(__DIR__ . '/' . $request['<firstFile>'])) {
        $firstFile = json_decode(file_get_contents(__DIR__ . '/' . $request['<firstFile>']), true);
    } elseif ($secondPathToFile && !is_array($request) && file_exists(__DIR__ . '/' . $request)) {
        $firstFile = json_decode(file_get_contents(__DIR__ . '/' . $request, true));
    }
    if (isset($request["<secondFile>"]) && file_exists(__DIR__ . '/' . $request['<secondFile>'])) {
        $secondFile = json_decode(file_get_contents(__DIR__ . '/' . $request['<secondFile>']), true);
    } elseif ($secondPathToFile && file_exists(__DIR__ . '/' . $secondPathToFile)) {
        $secondFile = json_decode(file_get_contents(__DIR__ . '/' . $secondPathToFile), true);
    }
    $keys = [array_keys($firstFile), array_keys($secondFile)];

    $string = array_reduce(array_unique(flattenAll($keys)), function ($acc, $key) use ($firstFile, $secondFile) {
        if (isset($firstFile[$key]) && isset($secondFile[$key])) {
            if ($secondFile[$key] === $firstFile[$key]) {
                $acc = $acc . "  $key: $secondFile[$key]\n";
            } else {
                $acc = $acc . "+ $key: $secondFile[$key]\n";
                $acc = $acc . "- $key: $firstFile[$key]\n";
            }
        } elseif (isset($secondFile[$key])) {
            $acc = $acc . "+ $key: $secondFile[$key]\n";
        } else {
            $acc = $acc . "- $key: $firstFile[$key]\n";
        }
        return $acc;
    }, "");
        echo("{\n$string}\n");
}
