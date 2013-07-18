<?php
class DB_Type_BigInt extends DB_Type_Numeric
{
	public function output($value)
	{
		if ($value === null) {
			return null;
		}
		if (!is_numeric($value)) {
			throw new DB_Type_Exception_Int($this, $value);
		}
		return $value;
	}

	public function input($value, $for = '')
	{
		return $value;
	}

    public function getNativeType()
    {
    	return 'BIGINT';
    }
}
