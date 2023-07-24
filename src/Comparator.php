<?php

namespace Meisterwerk\Core;

class Comparator
{
    /**
     * returns changes likes this:
     * [
     *      "name" => ["sara", "anna"]
     * ].
     */
    public static function getPropertyDifferences($properties1, $properties2): array
    {
        $changes = [];
        foreach ($properties1 as $key => $value) {
            if (array_key_exists($key, $properties2)) {
                if ($value != $properties2[$key]) {
                    $changes[$key] = [$value, $properties2[$key]];
                }
            } else {
                $changes[$key] = [$value, null];
            }
        }
        // additional array_diff to get the keys of $properties2 that are not in $properties1
        $diffArray = array_diff(array_keys($properties2), array_keys($properties1));
        foreach ($diffArray as $key) {
            $changes[$key] = [null, $properties2[$key]];
        }

        return $changes;
    }
}