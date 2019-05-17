
<?php

require_once('AbstractResult.php');

class Status extends AbstractResult
{
    /**
     * @return array
     */
    public function getAllData()
    {
        return $this->_data;
    }
}