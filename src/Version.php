<?php

namespace One23\Helpers;

class Version
{
    public static function object(
        ObjectVersion|string $val
    ): ObjectVersion {
        return new ObjectVersion($val);
    }

    public static function compare(
        ObjectVersion|string $ver1,
        ObjectVersion|string $ver2
    ): int {
        if (! $ver1 instanceof ObjectVersion) {
            $ver1 = static::object($ver1);
        }

        if (! $ver2 instanceof ObjectVersion) {
            $ver2 = static::object($ver2);
        }

        $parts1 = $ver1->toArray();
        $parts2 = $ver2->toArray();

        $invert = (bool)(count($parts2) > count($parts1));
        if ($invert) {
            $arr1 = $parts2;
            $arr2 = $parts1;
        } else {
            $arr1 = $parts1;
            $arr2 = $parts2;
        }

        foreach ($arr1 as $i => $part) {
            $cmp = $part->compare($arr2[$i] ?? '0');

            if ($cmp) {
                return $invert ? -$cmp : $cmp;
            }
        }

        return 0;
    }
}
