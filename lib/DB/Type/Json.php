<?php
/**
 * Class DB_Type_Json
 */
class DB_Type_Json extends DB_Type_Abstract_Primitive
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

        return json_encode($value);
    }

    /**
     * @param string $value
     * @param string $for
     *
     * @return string
     */
    public function input($value, $for = '')
    {
        return json_decode($value, true);
    }

    /**
     * @return string
     */
    public function getNativeType()
    {
        return 'JSON';
    }
}
