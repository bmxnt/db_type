<?php

/**
 * Class DB_Type_Pgsql_Row
 */
class DB_Type_Pgsql_Row extends DB_Type_Abstract_Container
{

    /**
     * @var array
     */
    private $_items;
    /**
     * @var null
     */
    private $_nativeType;

    /**
     * @param array $items
     * @param null  $nativeType
     */
    public function __construct(array $items, $nativeType = null)
    {
        $this->_items = $items;
        $this->_nativeType = $nativeType;
        // ROW() has no base item type, so pass null to parent::__construct().
        parent::__construct(null);
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * @param mixed $value
     *
     * @return null|string
     * @throws DB_Type_Exception_Common
     * @throws DB_Type_Exception_Container
     */
    public function output($value)
    {
        if($value === null) {
            return null;
        }

        if(is_object($value)) {
            $value = (array)$value;
        }

        if(!is_array($value)) {
            throw new DB_Type_Exception_Common($this, "output", "row or null", $value);
        }

        $parts = array();
        foreach($this->_items as $field => $type) {
            try {
                $v = $type->output(isset($value[$field]) ? $value[$field] : null);
            } catch ( Exception $e ) {
                throw new DB_Type_Exception_Container($this, "output", $field, $e->getMessage());
            }
            if($v === null) {
                $parts[] = 'NULL';
            } else {
                // ROW() quote ['] and [\] characters: src\backend\adt\rowtypes.c
                // Single-quote string literals.
                $parts[] = '\'' . str_replace(array( '\'', '\\' ), array( '\'\'', '\\\\' ), $v) . '\'';
            }
        }

        return '(' . join(",", $parts) . ')';
    }

    /**
     * @param string $str
     * @param int    $p
     * @param string $for
     *
     * @return array
     * @throws DB_Type_Exception_Common
     */
    protected function _parseInput($str, &$p, $for = '')
    {
        reset($this->_items);
        $result = array();
        $m = null;

        // Leading "(".
        $c = $this->_charAfterSpaces($str, $p);
        if($c != '(') {
            throw new DB_Type_Exception_Common($this, "input", "start of a row '('", $str, $p);
        }
        $p++;

        // Check for immediate trailing ')'.
        $c = $this->_charAfterSpaces($str, $p);
        if($c == ')') {
            if(list ($field,) = each($this->_items)) {
                throw new DB_Type_Exception_Common($this, "input", "field '$field' value", $str, $p);
            }
            $p++;

            return $result;
        }

        // Row may contain:
        // - "-quoted strings (escaping: ["] is doubled)
        // - unquoted strings (before first "," or ")")
        // - empty string (it is treated as NULL)
        // Nested rows and all other things are represented as strings.
        while(1) {
            // We read a value in this iteration, then - delimiter.
            $c = $this->_charAfterSpaces($str, $p);

            // Check if we have more fields left.
            if(!(list ($field, $type) = each($this->_items))) {
                throw new DB_Type_Exception_Common($this, "input", "end of the row: no more fields left", $str, $p);
            }

            // Always read a next element value.
            if($c == ',' || $c == ')') {
                // Comma or end of row instead of value: treat as NULL.
                $result[$field] = null;
            } else if($c != '"') {
                // Unquoted string. NULL here is treated as "NULL" string, but NOT as a null value!
                $len = strcspn($str, ",)", $p);
                $v = call_user_func(self::$_substr, $str, $p, $len);
                $result[$field] = $type->input($v, $for);
                $p += $len;
            } else if(preg_match('/" ((?' . '>[^"]+|"")*) "/Asx', $str, $m, 0, $p)) {
                // Quoted string.
                $v = str_replace(array( '""', '\\\\' ), array( '"', '\\' ), $m[1]);
                $result[$field] = $type->input($v, $for);
                $p += call_user_func(self::$_strlen, $m[0]);
            } else {
                // Error.
                throw new DB_Type_Exception_Common($this, "input", "balanced quoted or unquoted string", $str, $p);
            }

            // Delimiter or the end of row.
            $c = $this->_charAfterSpaces($str, $p);
            if($c == ',') {
                $p++;
                continue;
            } else if($c == ')') {
                $p++;
                break;
            } else {
                throw new DB_Type_Exception_Common($this, "input", "delimiter ',' or ')'", $str, $p);
            }
        }

        return $result;
    }

    /**
     * @return null
     */
    public function getNativeType()
    {
        return $this->_nativeType;
    }

    /**
     * Parse each element of an array of native values into PHP array.
     * Method used for parsing SQL query result (as assoc array)
     * which contains complex data types.
     *
     * @param        $native
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

    /**
     * @return array
     */
    public function getEmpty()
    {
        $result = array();
        foreach($this->_items as $field => $type)
            $result[$field] = $type->getEmpty();

        return $result;
    }

}
