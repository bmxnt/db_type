<?php

/**
 * Class DB_Type_DateTime
 */
class DB_Type_DateTime extends DB_Type_Abstract_Primitive
{

    /**
     * @var
     */
    protected $_format;
    /**
     * @var
     */
    protected $_timezone;

    /**
     * @param        $format
     * @param string $timezone
     */
    public function __construct($format = 'Y-m-d H:i:s.u', $timezone = 'Europe/Moscow')
    {
        $this->setFormat($format)->setTimezone(new DateTimeZone($timezone));
    }

    /**
     * Timezone setter
     *
     * @param mixed $timezone
     *
     * @return DB_Type_DateTime
     */
    public function setTimezone($timezone)
    {
        $this->_timezone = $timezone;

        return $this;
    }

    /**
     * Format setter
     *
     * @param mixed $format
     *
     * @return DB_Type_DateTime
     */
    public function setFormat($format)
    {
        $this->_format = $format;

        return $this;
    }

    /**
     * @param string $native
     * @param string $for
     *
     * @return mixed|void
     */
    public function input($native, $for = '')
    {
        if(null === $native){
            return null;
        }

        $dateTime = $native instanceof \DateTime ? $native : new DateTime($native, $this->getTimezone());

        return $dateTime->format($this->getFormat());
    }

    /**
     * Timezone getter
     * @return mixed
     */
    public function getTimezone()
    {
        return $this->_timezone;
    }

    /**
     * Format getter
     * @return mixed
     */
    public function getFormat()
    {
        return $this->_format;
    }

    /**
     * @param mixed $value
     *
     * @return string|void
     */
    public function output($value)
    {
        if(null === $value){
            return null;
        }

        $dateTime = new DateTime($value, $this->getTimezone());

        return $dateTime->format($this->getFormat());
    }

    /**
     * Return native type name for this value.
     * @return string
     */
    public function getNativeType()
    {
        return 'TIMESTAMP';
    }
}
