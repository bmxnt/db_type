<?php
/**
 * Class DB_Type_Json
 */
class DB_Type_Bit extends DB_Type_Abstract_Primitive
{

    /**
     * @param mixed $value
     *
     * @return int|null|string
     */
    public function output($value)
    {
        if($value === null) {
            return null;
        }

        return decbin($value);
    }

    /**
     * @param string $value
     * @param string $for
     *
     * @return string
     */
    public function input($value, $for = '')
    {
        return bindec($value);
    }

    /**
     * @return string
     */
    public function getNativeType()
    {
        return 'BIT';
    }
}
