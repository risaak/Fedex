<?php

require_once('AbstractResult.php');
require_once('Error.php');
require_once('Method.php');

/**
 * Class RateResult
 * Container for Rates
 *
 */
class RateResult
{
    /**
     * Shipping method rates
     *
     * @var array
     */
    protected $_rates = [];
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
     * Add a rate to the result
     *
     * @param AbstractResult|RateResult $result
     * @return $this
     */
    public function append($result)
    {
        if ($result instanceof Error) {
            $this->setError(true);
        }
        if ($result instanceof AbstractResult) {
            $this->_rates[] = $result;
        } elseif ($result instanceof RateResult) {
            $rates = $result->getAllRates();
            foreach ($rates as $rate) {
                $this->append($rate);
            }
        }
        return $this;
    }
    /**
     * Return all quotes in the result
     *
     * @return Method[]
     */
    public function getAllRates()
    {
        return $this->_rates;
    }
    /**
     * Return rate by id in array
     *
     * @param int $id
     * @return Method|null
     */
    public function getRateById($id)
    {
        return isset($this->_rates[$id]) ? $this->_rates[$id] : null;
    }
    /**
     * Return quotes for specified type
     *
     * @param string $carrier
     * @return array
     */
    public function getRatesByCarrier($carrier)
    {
        $result = [];
        foreach ($this->_rates as $rate) {
            if ($rate->getCarrier() === $carrier) {
                $result[] = $rate;
            }
        }
        return $result;
    }
    /**
     * Converts object to array
     *
     * @return array
     */
    public function asArray()
    {
        $rates = [];
        $allRates = $this->getAllRates();
        foreach ($allRates as $rate) {
            $rates[$rate->getCarrier()]['title'] = $rate->getCarrierTitle();
            $rates[$rate->getCarrier()]['methods'][$rate->getMethod()] = [
                'title' => $rate->getMethodTitle(),
                'price' => $rate->getPrice(),
                'price_formatted' => $rate->getPrice(),
            ];
        }
        return $rates;
    }
    /**
     * Get cheapest rate
     *
     * @return null|\Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    public function getCheapestRate()
    {
        $cheapest = null;
        $minPrice = 100000;
        foreach ($this->getAllRates() as $rate) {
            if (is_numeric($rate->getPrice()) && $rate->getPrice() < $minPrice) {
                $cheapest = $rate;
                $minPrice = $rate->getPrice();
            }
        }
        return $cheapest;
    }
    /**
     * Sort rates by price from min to max
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function sortRatesByPrice()
    {
        if (!is_array($this->_rates) || !count($this->_rates)) {
            return $this;
        }
        /* @var $rate \Magento\Quote\Model\Quote\Address\RateResult\Method */
        foreach ($this->_rates as $i => $rate) {
            $tmp[$i] = $rate->getPrice();
        }
        natsort($tmp);
        foreach ($tmp as $i => $price) {
            $result[] = $this->_rates[$i];
        }
        $this->reset();
        $this->_rates = $result;
        return $this;
    }
    /**
     * Set price for each rate according to count of packages
     *
     * @param int $packageCount
     * @return $this
     */
    public function updateRatePrice($packageCount)
    {
        if ($packageCount > 1) {
            foreach ($this->_rates as $rate) {
                $rate->setPrice($rate->getPrice() * $packageCount);
            }
        }
        return $this;
    }
}
