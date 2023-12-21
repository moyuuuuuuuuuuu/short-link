<?php

namespace app\service\enums;

class BaseEnum
{
    public function __construct(array $enums = [])
    {
        if (!empty($enums)) {
            foreach ($enums as $key => $value) {
                $key        = convert($key, false);
                $this->$key = $value;
            }
        }
    }

    public function toArray()
    {
        $newData = [];
        foreach ((array)$this as $key => $value) {
            $newData[reconvert($key)] = $value;
        }
        return $newData;
    }
}
