<?php

require_once('AbstractResult.php');
require_once('Error.php');
require_once('Method.php');

/**
 * Class TrakingResult
 * Container for Rates
 *
 */
class TrakingResult
{
    /**
     * @var array
     */
    protected $_trackings = [];
    
    /**
     * Shipping errors
     *
     * @var null|bool
     */
    protected $_error = null;
 
    /**
     * 
     */
    public function __construct()
    {
    }
    /**
     * Reset result
     *
     * @return $this
     */
    public function reset()
    {
        $this->_rates = [];
        return $this;
    }
    /**
     * Set Error
     *
     * @param bool $error
     * @return void
     */
    public function setError($error)
    {
        $this->_error = $error;
    }
    /**
     * Get Error
     *
     * @return null|bool
     */
    public function getError()
    {
        return $this->_error;
    }
    
    /**
     * Add a tracking to the result
     *
     * @param AbstractResult|RateResult $result
     * @return $this
     */
    public function append($result)
    {
        if ($result instanceof AbstractResult) {
            $this->_trackings[] = $result;
        } elseif ($result instanceof RateResult) {
            $trackings = $result->getAllTrackings();
            foreach ($trackings as $track) {
                $this->append($track);
            }
        }
        return $this;
    }
    
    /**
     * Return all trackings in the result
     *
     * @return array
     */
    public function getAllTrackings()
    {
        return $this->_trackings;
    }
}
