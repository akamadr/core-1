<?php
namespace TypeRocket\Utility;

class Data
{
    /**
     * Dots Walk
     *
     * Traverse array with dot notation.
     *
     * @param string|array $dots dot notation key.next.final
     * @param array|object $array an array to traverse
     * @param null|mixed $default
     *
     * @return array|mixed|null
     */
    public static function walk($dots, $array, $default = null)
    {
        $traverse = is_array($dots) ? $dots : explode('.', $dots);
        foreach ($traverse as $step) {
            $v = is_object($array) ? ($array->$step ?? null) : ($array[$step] ?? null);

            if ( !isset($v) && ! is_string($array) ) {
                return $default;
            }
            $array = $v ?? $default;
        }

        return $array;
    }

    /**
     * @param mixed $value
     * @param string|callable $type
     *
     * @return bool|float|int|mixed|string
     */
    public static function cast($value, $type)
    {
        // Integer
        if ($type == 'int' || $type == 'integer') {
            return is_object($value) || is_array($value) ? null : (int) $value;
        }

        // Float
        if ($type == 'float' || $type == 'double' || $type == 'real') {
            return is_object($value) || is_array($value) ? null : (float) $value;
        }

        // JSON
        if ($type == 'json') {

            if(is_serialized($value)) {
                $value = unserialize($value);
            } if(static::isJson($value)) {
                return $value;
            }

            return json_encode($value);
        }

        // Serialize
        if ($type == 'serialize' || $type == 'serial') {

            if(static::isJson($value)) {
                $value = json_decode((string) $value, true);
            } if(is_serialized($value)) {
                return $value;
            }

            return serialize($value);
        }

        // String
        if ($type == 'str' || $type == 'string') {
            if(is_object($value) || is_array($value)) {
                $value = json_encode($value);
            } else {
                $value = (string) $value;
            }

            return $value;
        }

        // Bool
        if ($type == 'bool' || $type == 'boolean') {
            return (bool) $value;
        }

        // Array
        if ($type == 'array') {
            if(is_numeric($value)) {
                return $value;
            } elseif (is_string($value) && static::isJson($value)) {
                $value = json_decode($value, true);
            } elseif (is_string($value) && is_serialized($value)) {
                $value = unserialize($value);
            } elseif(!is_string($value)) {
                $value = (array) $value;
            }

            return $value;
        }

        // Object
        if ($type == 'object' || $type == 'obj') {
            if(is_numeric($value)) {
                return $value;
            } elseif (is_string($value) && static::isJson($value)) {
                $value = (object) json_decode($value);
            } elseif (is_string($value) && is_serialized($value)) {
                $value = (object) unserialize($value);
            } elseif(!is_string($value)) {
                $value = (object) $value;
            } elseif (is_array($value)) {
                $value = (object) $value;
            }

            return $value;
        }

        // Callback
        if (is_callable($type)) {
            return call_user_func($type, $value);
        }

        return $value;
    }

    /**
     * Detect is JSON
     *
     * @param $args
     *
     * @return bool
     */
    public static function isJson(...$args)
    {
        if(is_array($args[0]) || is_object($args[0])) {
            return false;
        }

        if (trim($args[0]) === '') {
            return false;
        }

        json_decode(...$args);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}