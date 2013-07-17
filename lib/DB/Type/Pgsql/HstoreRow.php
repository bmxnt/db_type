<?php

/**
 * Class DB_Type_Pgsql_HstoreRow
 */
class DB_Type_Pgsql_HstoreRow extends DB_Type_Pgsql_Hstore
{

    /**
     * @var array
     */
    private $_items;

    /**
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->_items = $items;
        parent::__construct(new DB_Type_String());
    }

    /**
     * @param mixed $value
     *
     * @return null|string
     * @throws DB_Type_Exception_Container
     */
    public function output($value)
    {
        if(is_array($value)) {
            $newValue = array();
            foreach($value as $key => $v) {
                if(isset($this->_items[$key])) {
                    try {
                        $newValue[$key] = $this->_items[$key]->output($v);
                    } catch ( Exception $e ) {
                        throw new DB_Type_Exception_Container($this, "output", $key, $e->getMessage());
                    }
                }
            }
            $value = $newValue;
        }

        return parent::output($value);
    }

    /**
     * @param string $str
     * @param int    $p
     * @param string $for
     *
     * @return array
     */
    protected function _parseInput($str, &$p, $for = '')
    {
        $result = parent::_parseInput($str, $p);
        foreach($result as $key => $v) {
            if(isset($this->_items[$key])) {
                $result[$key] = $this->_items[$key]->input($v, $for);
            } else {
                // Extra column in hstore must not break the program execution!
                $result[$key] = null;
                //throw new DB_Type_Exception_Common($this, "input", "unexpected key", $key);
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getNativeType()
    {
        return 'hstore';
    }

    /**
     * @param array  $native
     * @param string $for
     *
     * @return array
     */
    protected function _itemsInput(array $native, $for = '')
    {
        $result = array();

        foreach($native as $field => $value) {
            if(array_key_exists($field, $this->_items)) {
                $result[$field] = $this->_items[$field]->input($value, $for);
            }
            /*else
                $result[$field] = $value;*/
        }

        return $result;
    }

}
