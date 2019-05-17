<?php

require_once('AbstractResult.php');

/**
 * Fields:
 * - carrier: carrier code
 * - carrierTitle: carrier title
 * - method: carrier method
 * - methodTitle: method title
 * - price: cost+handling
 * - cost: cost
 *
 */
class Method extends AbstractResult
{
    /**
     * @param array $data
     */
    public function __construct(
        array $data = []
    ) {
        parent::__construct($data);
    }
    /**
     * Round shipping carrier's method price
     *
     * @param string|float|int $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->setData('price', $price);
        return $this;
    }
}