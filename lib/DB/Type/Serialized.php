<?php
/**
 * Class DB_Type_Json
 */
class DB_Type_Serialized extends DB_Type_Abstract_Primitive
{

    /**
     * @param mixed $value
     *
     * @return int|null|string
     * @throws DB_Type_Exception_Int
     */
    public function output($value)
    {
        if($value === null) {
            return null;
        }

        return serialize($value);
    }

    /**
     * @param string $value
     * @param string $for
     *
     * @return string
     */
    public function input($value, $for = '')
    {
        return unserialize($value);
    }

    /**
     * @return string
     */
    public function getNativeType()
    {
        return 'TEXT';
    }
}
