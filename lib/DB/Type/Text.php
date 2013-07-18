<?php
/**
 * Class DB_Type_Text
 */
class DB_Type_Text extends DB_Type_String
{

    /**
     * @var null
     */
    private $_min;
    /**
     * @var null
     */
    private $_max;

    /**
     * @param null $max
     * @param null $min
     */
    public function __construct($max = null, $min = null)
    {
        $this->_max = $max;
        $this->_min = $min;
    }

    /**
     * @param mixed $value
     *
     * @return null|string
     * @throws DB_Type_Exception_Common
     */
    public function output($value)
    {
        if($value === null) {
            return null;
        }

        $value = strval($value);

        if($this->_max !== null && strlen($value) > $this->_max) {
            throw new DB_Type_Exception_Common($this, __FUNCTION__, 'String less than: ' . $this->_max, $value);
        }

        if($this->_min !== null && strlen($value) < $this->_min) {
            throw new DB_Type_Exception_Common($this, __FUNCTION__, 'String more than: ' . $this->_max, $value);
        }

        return $value;
    }

    /**
     * @param string $native
     * @param string $for
     *
     * @return null|string
     */
    public function input($native, $for = '')
    {
        if($native === null) {
            return null;
        }

        return strval($native);
    }

    /**
     * @return string
     */
    public function getNativeType()
    {
        return 'TEXT';
    }
}
