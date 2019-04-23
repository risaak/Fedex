<?php

require_once('RateResult.php');
require_once('Error.php');
require_once('Method.php');
require_once('DataObject.php');

class FedexConection {

    /**
     * Code of the carrier
     *
     * @var string
     */
    protected $_code = 'fedex';

    /**
     * Rate request data
     *
     * @var Array|null
     */
    protected $_request = null;

    /**
     * Path to wsdl file of rate service
     *
     * @var string
     */
    protected $_rateServiceWsdl;

    /**
     * Path to wsdl file of ship service
     *
     * @var string
     */
    protected $_shipServiceWsdl = null;

    /**
     * Path to wsdl file of track service
     *
     * @var string
     */
    protected $_trackServiceWsdl;

    /**
     * Purpose of rate request
     *
     * @var string
     */
    const RATE_REQUEST_GENERAL = 'general';

    /**
     * Purpose of rate request
     *
     * @var string
     */
    const RATE_REQUEST_SMARTPOST = 'SMART_POST';

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var ClientFactory
     */
    private $soapClientFactory;

    /**
     * @var Array
     */
    private $allowedMethods;

    /**
     * Raw rate request data
     *
     * @var DataObject|null
     */
    protected $_rawRequest = null;

    /**
     * Types of rates, order is important
     *
     * @var array
     */
    protected $_ratesOrder = [
        'RATED_ACCOUNT_PACKAGE',
        'PAYOR_ACCOUNT_PACKAGE',
        'RATED_ACCOUNT_SHIPMENT',
        'PAYOR_ACCOUNT_SHIPMENT',
        'RATED_LIST_PACKAGE',
        'PAYOR_LIST_PACKAGE',
        'RATED_LIST_SHIPMENT',
        'PAYOR_LIST_SHIPMENT',
    ];

    /**
     * @var boolean
     */
    private $debug;

    public function __construct($debug = false) {
        $wsdlBasePath = __DIR__ . '/wsdl/';
        $this->_shipServiceWsdl = $wsdlBasePath . 'ShipService_v23.wsdl';
        $this->_rateServiceWsdl = $wsdlBasePath . 'RateService_v24.wsdl';
        $this->_trackServiceWsdl = $wsdlBasePath . 'TrackService_v16.wsdl';
        $this->serializer = $serializer = new Json();
        $this->soapClientFactory = new ClientFactory();
        $this->debug = $debug;
    }

    /**
     * Create soap client with selected wsdl
     *
     * @param string $wsdl
     * @param bool|int $trace
     * @return \SoapClient
     */
    protected function _createSoapClient($wsdl, $trace = false)
    {
        $client = $this->soapClientFactory->create($wsdl, ['trace' => $trace]);
        $client->__setLocation(
            $this->debug ? 'https://wsbeta.fedex.com:443/web-services': ''
        );
        return $client;
    }

    /**
     * Create rate soap client
     *
     * @return \SoapClient
     */
    protected function _createRateSoapClient()
    {
        return $this->_createSoapClient($this->_rateServiceWsdl);
    }
    /**
     * Create ship soap client
     *
     * @return \SoapClient
     */
    protected function _createShipSoapClient()
    {
        return $this->_createSoapClient($this->_shipServiceWsdl);
    }

    /**
     * Create track soap client
     *
     * @return \SoapClient
     */
    protected function _createTrackSoapClient()
    {
        return $this->_createSoapClient($this->_trackServiceWsdl);
    }

    /**
     * Get Fedex Account
     *
     * @return String
     */
    protected function getFedexAccount()
    {
        return $this->debug ? '510087860': '';
    }

    /**
     * Get Meter Number
     *
     * @return String
     */
    protected function getMeterNumber()
    {
        return $this->debug ? '119128331': '';
    }

    /**
     * Get Key
     *
     * @return String
     */
    protected function getKey()
    {
        return $this->debug ? 'tDWROedxHEzLvd4E': '';
    }

    /**
     * Get Password
     *
     * @return String
     */
    protected function getPassword()
    {
        return $this->debug ? 'O14MYlDz9bx4UlmKrHj85XlUx': '';
    }

    /**
     * Set Raw Request
     *
     * @param DataObject|null $request
     * @return $this
     * @api
     */
    public function setRawRequest($request)
    {
        $this->_rawRequest = $request;
        return $this;
    }

    /**
     * Set AllowedMethod
     *
     * @param Array $allowedMethods
     * @return Void
     */
    public function setAllowedMethods($allowedMethods)
    {
        $this->allowedMethods = $allowedMethods;
    }

    /**
     * Collect and get rates
     *
     * @param $request
     * @return Result|bool|null
     */
    public function collectRates($request)
    {
        $this->setRequestRate($request);
        $this->_getQuotes();
        return $this->getResult();
    }

    /**
     * Collect and get rates
     *
     * @param $request
     * @return Result|bool|null
     */
    public function collectTrack($request)
    {
        $this->setRequestRate($request);
        return $this->getResult();
    }

    /**
     * Collect and get rates
     *
     * @param $request
     * @return Result|bool|null
     */
    public function collectShip($request)
    {
        $req = new DataObject($request);
        return $this->_doShipmentRequest($req);
    }

    /**
     * Prepare and set request to this instance
     *
     * @param Array $request
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function setRequestRate($request)
    {
        $this->_request = $request;
        $r = new DataObject();

        $r->setAccount($this->getFedexAccount());
        if (array_key_exists('dropoff',$request)) {
            $dropoff = $request['dropoff'];
        } else {
            $dropoff = $request['dropoff']['REGULARPICKUP'];
        }
        $r->setDropoffType($dropoff);
        if (array_key_exists('packaging',$request)) {
            $packaging = $request['packaging'];
        } else {
            $packaging = $code['packaging']['YOURPACKAGING'];
        }
        $r->setPackaging($packaging);

        // iso2_code
        $r->setOrigName($request['origin_name']);
        $r->setOrigPhone($request['origin_phone']);
        $r->setOrigEmail($request['origin_email']);
        $r->setOrigStreet($request['origin_street']);
        $r->setOrigCity($request['origin_city']);
        $r->setOrigStatecode($request['origin_state_code']);
        $r->setOrigCountry($request['origin_country']);
        $r->setOrigPostal($request['origin_postcode']);

        // iso2_code
        $r->setDestName($request['dest_name']);
        $r->setDestPhone($request['dest_phone']);
        $r->setDestEmail($request['dest_email']);
        $r->setDestStreet($request['dest_street']);
        $r->setDestStatecode($request['dest_state_code']);
        $r->setDestCountry($request['dest_country']);
        $r->setDestPostal($request['dest_postcode']);
        $r->setDestCity($request['dest_city']);

        $r->setWeight($request['weight']);
        //$r->freeMethodWeight = $request['free_method_weight'];
        $r->setValue($request['value']);
        $r->setMeterNumber($this->getMeterNumber());
        $r->setKey($this->getKey());
        $r->setPassword($this->getPassword());
        if (array_key_exists('isReturn',$request)) {
            $r->setIsReturn($request['isReturn']);
        }
        if (array_key_exists('smartpost_hubid',$request)) {
            $r->setHubid($request['smartpost_hubid']);
        }
        //$r->setCurrency($request['currency']);

        $this->setRawRequest($r);

        return $this;
    }

    /**
     * Prepare and set request to this instance
     *
     * @param Array $request
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function setRequestTrack($request)
    {
        $this->_request = $request;
        $r = new DataObject();

        $r->setAccount($this->getFedexAccount());
        if (array_key_exists('dropoff',$request)) {
            $dropoff = $request['dropoff'];
        } else {
            $dropoff = $request['dropoff']['REGULARPICKUP'];
        }
        $r->setDropoffType($dropoff);
        if (array_key_exists('packaging',$request)) {
            $packaging = $request['packaging'];
        } else {
            $packaging = $code['packaging']['YOURPACKAGING'];
        }
        $r->setPackaging($packaging);

        // iso2_code
        $r->setOrigName($request['origin_name']);
        $r->setOrigPhone($request['origin_phone']);
        $r->setOrigEmail($request['origin_email']);
        $r->setOrigStreet($request['origin_street']);
        $r->setOrigCity($request['origin_city']);
        $r->setOrigStatecode($request['origin_state_code']);
        $r->setOrigCountry($request['origin_country']);
        $r->setOrigPostal($request['origin_postcode']);

        // iso2_code
        $r->setDestName($request['dest_name']);
        $r->setDestPhone($request['dest_phone']);
        $r->setDestEmail($request['dest_email']);
        $r->setDestStreet($request['dest_street']);
        $r->setDestStatecode($request['dest_state_code']);
        $r->setDestCountry($request['dest_country']);
        $r->setDestPostal($request['dest_postcode']);
        $r->setDestCity($request['dest_city']);

        $r->setWeight($request['weight']);
        //$r->freeMethodWeight = $request['free_method_weight'];
        $r->setValue($request['value']);
        $r->setMeterNumber($this->getMeterNumber());
        $r->setKey($this->getKey());
        $r->setPassword($this->getPassword());
        if (array_key_exists('isReturn',$request)) {
            $r->setIsReturn($request['isReturn']);
        }
        if (array_key_exists('smartpost_hubid',$request)) {
            $r->setHubid($request['smartpost_hubid']);
        }
        //$r->setCurrency($request['currency']);

        $this->setRawRequest($r);

        return $this;
    }

    /** 
      * Do remote request for and handle errors
      *
      * @return Result
    */
    protected function _getQuotes()
    {
        $this->_result = new RateResult();
        // make separate request for Smart Post method
        $allowedMethods = $this->allowedMethods;
        if (in_array(self::RATE_REQUEST_SMARTPOST, $allowedMethods)) {
            $response = $this->_doRatesRequestRate(self::RATE_REQUEST_SMARTPOST);
            $preparedSmartpost = $this->_prepareRateResponse($response);
            if (!$preparedSmartpost->getError()) {
                $this->_result->append($preparedSmartpost);
            }
        }
        // make general request for all methods
        $response = $this->_doRatesRequestRate(self::RATE_REQUEST_GENERAL);
        $preparedGeneral = $this->_prepareRateResponse($response);
        if (!$preparedGeneral->getError()
            || $this->_result->getError() && $preparedGeneral->getError()
            || empty($this->_result->getAllRates())
        ) {
            $this->_result->append($preparedGeneral);
        }
        return $this->_result;
    }

    /**
     * Makes remote request to the carrier and returns a response
     *
     * @param string $purpose
     * @return mixed
     */
    protected function _doRatesRequestRate($purpose)
    {
        $ratesRequest = $this->_formRateRequest($purpose);
        $ratesRequestNoShipTimestamp = $ratesRequest;
        unset($ratesRequestNoShipTimestamp['RequestedShipment']['ShipTimestamp']);
        $requestString = $this->serializer->serialize($ratesRequestNoShipTimestamp);
        //$response = $this->_getCachedQuotes($requestString);
        //if ($response === null) {
            try {
                $client = $this->_createRateSoapClient();
                $response = $client->getRates($ratesRequest);
                //$this->_setCachedQuotes($requestString, $response);
            } catch (\Exception $e) {
                echo print_r(['error' => $e->getMessage(), 'code' => $e->getCode()],true);
            }
        //}
        return $response;
    }

    /**
     * Forming request for rate estimation depending to the purpose
     *
     * @param string $purpose
     * @return array
     */
    protected function _formRateRequest($purpose)
    {
        $r = $this->_rawRequest;
        $ratesRequest = [
            'WebAuthenticationDetail' => [
                'UserCredential' => ['Key' => $r->getKey(), 'Password' => $r->getPassword()],
            ],
            'ClientDetail' => ['AccountNumber' => $r->getAccount(), 'MeterNumber' => $r->getMeterNumber()],
            'Version' => $this->getVersionInfoRate(), 
            'RequestedShipment' => [
                'DropoffType' => $r->getDropoffType(),
                'ShipTimestamp' => date('c'),
                'PackagingType' => $r->getPackaging(),
                'TotalInsuredValue' => ['Amount' => $r->getValue(), 'Currency' => 'NMP'],
                'Shipper' => [
                    'Contact' => [
                        'PersonName' => $r->getOrigName(), 
                        'PhoneNumber' => $r->getOrigPhone(),
                        'EmailAddress' => $r->getOrigEmail()
                    ],
                    'Address' => [
                        'StreetLines' => $r->getOrigStreet(),
                        'City' => $r->getOrigCity(), 
                        'StateOrProvinceCode' => $r->getOrigStatecode(), 
                        'PostalCode' => $r->getOrigPostal(), 
                        'CountryCode' => $r->getOrigCountry()
                    ],
                ],
                'Recipient' => [
                    'Contact' => [
                        'PersonName' => $r->getDestName(), 
                        'PhoneNumber' => $r->getDestPhone(),
                        'EmailAddress' => $r->getDestEmail()
                    ],
                    'Address' => [
                        'StreetLines' => $r->getDestStreet(),
                        'StateOrProvinceCode' => $r->getDestStatecode(),
                        'PostalCode' => $r->getDestPostal(),
                        'CountryCode' => $r->getDestCountry(),
                        'Residential' => false,
                    ],
                ],
                'ShippingChargesPayment' => [
                    'PaymentType' => 'SENDER',
                    'Payor' => ['AccountNumber' => $r->getAccount(), 'CountryCode' => $r->getOrigCountry()],
                ],
                'CustomsClearanceDetail' => [
                    'CustomsValue' => ['Amount' => $r->getValue(), 'Currency' => 'NMP'],
                ],
                'RateRequestTypes' => 'LIST',
                'PackageCount' => '1',
                'PackageDetail' => 'INDIVIDUAL_PACKAGES',
                'RequestedPackageLineItems' => [
                    '0' => [
                        'Weight' => [
                            'Value' => (double)$r->getWeight(),
                            'Units' => 'KG',
                        ],
                        'GroupPackageCount' => 1,
                    ],
                ],
            ],
        ];
        if ($r->getDestCity()) {
            $ratesRequest['RequestedShipment']['Recipient']['Address']['City'] = $r->getDestCity();
        }
        if ($purpose == self::RATE_REQUEST_GENERAL) {
            $ratesRequest['RequestedShipment']['RequestedPackageLineItems'][0]['InsuredValue'] = [
                'Amount' => $r->getValue(),
                'Currency' => 'NMP',
            ];
        } else {
            if ($purpose == self::RATE_REQUEST_SMARTPOST) {
                $ratesRequest['RequestedShipment']['ServiceType'] = self::RATE_REQUEST_SMARTPOST;
                $ratesRequest['RequestedShipment']['SmartPostDetail'] = [
                    'Indicia' => (double)$r->getWeight() >= 1 ? 'PARCEL_SELECT' : 'PRESORTED_STANDARD',
                    'HubId' => $r->getHubid(),
                ];
            }
        }
        //die(print_r($ratesRequest,1));
        return $ratesRequest;
    }

    /**
     * Get version of rates request
     *
     * @return array
     */
    public function getVersionInfoRate()
    {
        return ['ServiceId' => 'crs', 'Major' => '24', 'Intermediate' => '0', 'Minor' => '0'];
    }

    /**
     * Get version of rates request
     *
     * @return array
     */
    public function getVersionInfoShip()
    {
        return ['ServiceId' => 'ship', 'Major' => '23', 'Intermediate' => '0', 'Minor' => '0'];
    }

    /**
     * Get result of request
     *
     * @return Result|null
     */
    public function getResult()
    {
        if (!$this->_result) {
            //$this->_result = $this->_trackFactory->create();
        }
        return $this->_result;
    }

    /**
     * Prepare shipping rate result based on response
     *
     * @param mixed $response
     * @return Result
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _prepareRateResponse($response)
    {
        $costArr = [];
        $priceArr = [];
        $errorTitle = 'Por alguna razon no podemos entregar la información ene este momento';
        if (is_object($response)) {
            if ($response->HighestSeverity == 'FAILURE' || $response->HighestSeverity == 'ERROR') {
                if (is_array($response->Notifications)) {
                    $notification = array_pop($response->Notifications);
                    $errorTitle = (string)$notification->Message;
                } else {
                    $errorTitle = (string)$response->Notifications->Message;
                }
            } elseif (isset($response->RateReplyDetails)) {
                $allowedMethods = $this->allowedMethods;
                if (is_array($response->RateReplyDetails)) {
                    foreach ($response->RateReplyDetails as $rate) {
                        $serviceName = (string)$rate->ServiceType;
                        if (in_array($serviceName, $allowedMethods)) {
                            $amount = $this->_getRateAmountOriginBased($rate);
                            $costArr[$serviceName] = $amount;
                            $priceArr[$serviceName] = $amount;
                        }
                    }
                    asort($priceArr);
                } else {
                    $rate = $response->RateReplyDetails;
                    $serviceName = (string)$rate->ServiceType;
                    if (in_array($serviceName, $allowedMethods)) {
                        $amount = $this->_getRateAmountOriginBased($rate);
                        $costArr[$serviceName] = $amount;
                        $priceArr[$serviceName] = $amount;
                    }
                }
            }
        }
        $result = new RateResult();
        if (empty($priceArr)) {
            $error = new Errorr();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle('Fedex');
            $error->setErrorMessage($errorTitle);
            $error->setErrorMessage('Este método no esta habilitado. Para utilizar este método por favor ponte en contacto con nosotros.');
            $result->append($error);
        } else {
            foreach ($priceArr as $method => $price) {
                $rate = new Method();
                $rate->setCarrier($this->_code);
                $rate->setCarrierTitle('Fedex');
                $rate->setMethod($method);
                $rate->setMethodTitle($this->getCode('method', $method));
                $rate->setCost($costArr[$method]);
                $rate->setPrice($price);
                $result->append($rate);
            }
        }
        return $result;
    }

    /**
     * Get origin based amount form response of rate estimation
     *
     * @param \stdClass $rate
     * @return null|float
     */
    protected function _getRateAmountOriginBased($rate)
    {
        $amount = null;
        $rateTypeAmounts = [];
        if (is_object($rate)) {
            // The "RATED..." rates are expressed in the currency of the origin country
            foreach ($rate->RatedShipmentDetails as $ratedShipmentDetail) {
                $netAmount = (string)$ratedShipmentDetail->ShipmentRateDetail->TotalNetCharge->Amount;
                $rateType = (string)$ratedShipmentDetail->ShipmentRateDetail->RateType;
                $rateTypeAmounts[$rateType] = $netAmount;
            }
            foreach ($this->_ratesOrder as $rateType) {
                if (!empty($rateTypeAmounts[$rateType])) {
                    $amount = $rateTypeAmounts[$rateType];
                    break;
                }
            }
            if ($amount === null) {
                $amount = (string)$rate->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount;
            }
        }
        return $amount;
    }

    /**
     * Get configuration data of carrier
     *
     * @param string $type
     * @param string $code
     * @return array|false
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getCode($type, $code = '')
    {
        $codes = [
            'method' => [
                'EUROPE_FIRST_INTERNATIONAL_PRIORITY' => 'Europe First Priority',
                'FEDEX_1_DAY_FREIGHT' => '1 Day Freight',
                'FEDEX_2_DAY_FREIGHT' => '2 Day Freight',
                'FEDEX_2_DAY' => '2 Day',
                'FEDEX_2_DAY_AM' => '2 Day AM',
                'FEDEX_3_DAY_FREIGHT' => '3 Day Freight',
                'FEDEX_EXPRESS_SAVER' => 'Express Saver',
                'FEDEX_GROUND' => 'Ground',
                'FIRST_OVERNIGHT' => 'First Overnight',
                'GROUND_HOME_DELIVERY' => 'Home Delivery',
                'INTERNATIONAL_ECONOMY' => 'International Economy',
                'INTERNATIONAL_ECONOMY_FREIGHT' => 'Intl Economy Freight',
                'INTERNATIONAL_FIRST' => 'International First',
                'INTERNATIONAL_GROUND' => 'International Ground',
                'INTERNATIONAL_PRIORITY' => 'International Priority',
                'INTERNATIONAL_PRIORITY_FREIGHT' => 'Intl Priority Freight',
                'PRIORITY_OVERNIGHT' => 'Priority Overnight',
                'SMART_POST' => 'Smart Post',
                'STANDARD_OVERNIGHT' => 'Standard Overnight',
                'FEDEX_FREIGHT' => 'Freight',
                'FEDEX_NATIONAL_FREIGHT' => 'National Freight',
            ],
            'dropoff' => [
                'REGULAR_PICKUP' => 'Regular Pickup',
                'REQUEST_COURIER' => 'Request Courier',
                'DROP_BOX' => 'Drop Box',
                'BUSINESS_SERVICE_CENTER' => 'Business Service Center',
                'STATION' => 'Station',
            ],
            'packaging' => [
                'FEDEX_ENVELOPE' => 'FedEx Envelope',
                'FEDEX_PAK' => 'FedEx Pak',
                'FEDEX_BOX' => 'FedEx Box',
                'FEDEX_TUBE' => 'FedEx Tube',
                'FEDEX_10KG_BOX' => 'FedEx 10kg Box',
                'FEDEX_25KG_BOX' => 'FedEx 25kg Box',
                'YOUR_PACKAGING' => 'Your Packaging',
            ],
            'containers_filter' => [
                [
                    'containers' => ['FEDEX_ENVELOPE', 'FEDEX_PAK'],
                    'filters' => [
                        'within_us' => [
                            'method' => [
                                'FEDEX_EXPRESS_SAVER',
                                'FEDEX_2_DAY',
                                'FEDEX_2_DAY_AM',
                                'STANDARD_OVERNIGHT',
                                'PRIORITY_OVERNIGHT',
                                'FIRST_OVERNIGHT',
                            ],
                        ],
                        'from_us' => [
                            'method' => ['INTERNATIONAL_FIRST', 'INTERNATIONAL_ECONOMY', 'INTERNATIONAL_PRIORITY'],
                        ],
                    ],
                ],
                [
                    'containers' => ['FEDEX_BOX', 'FEDEX_TUBE'],
                    'filters' => [
                        'within_us' => [
                            'method' => [
                                'FEDEX_2_DAY',
                                'FEDEX_2_DAY_AM',
                                'STANDARD_OVERNIGHT',
                                'PRIORITY_OVERNIGHT',
                                'FIRST_OVERNIGHT',
                                'FEDEX_FREIGHT',
                                'FEDEX_1_DAY_FREIGHT',
                                'FEDEX_2_DAY_FREIGHT',
                                'FEDEX_3_DAY_FREIGHT',
                                'FEDEX_NATIONAL_FREIGHT',
                            ],
                        ],
                        'from_us' => [
                            'method' => ['INTERNATIONAL_FIRST', 'INTERNATIONAL_ECONOMY', 'INTERNATIONAL_PRIORITY'],
                        ],
                    ],
                ],
                [
                    'containers' => ['FEDEX_10KG_BOX', 'FEDEX_25KG_BOX'],
                    'filters' => [
                        'within_us' => [],
                        'from_us' => ['method' => ['INTERNATIONAL_PRIORITY']],
                    ],
                ],
                [
                    'containers' => ['YOUR_PACKAGING'],
                    'filters' => [
                        'within_us' => [
                            'method' => [
                                'FEDEX_GROUND',
                                'GROUND_HOME_DELIVERY',
                                'SMART_POST',
                                'FEDEX_EXPRESS_SAVER',
                                'FEDEX_2_DAY',
                                'FEDEX_2_DAY_AM',
                                'STANDARD_OVERNIGHT',
                                'PRIORITY_OVERNIGHT',
                                'FIRST_OVERNIGHT',
                                'FEDEX_FREIGHT',
                                'FEDEX_1_DAY_FREIGHT',
                                'FEDEX_2_DAY_FREIGHT',
                                'FEDEX_3_DAY_FREIGHT',
                                'FEDEX_NATIONAL_FREIGHT',
                            ],
                        ],
                        'from_us' => [
                            'method' => [
                                'INTERNATIONAL_FIRST',
                                'INTERNATIONAL_ECONOMY',
                                'INTERNATIONAL_PRIORITY',
                                'INTERNATIONAL_GROUND',
                                'FEDEX_FREIGHT',
                                'FEDEX_1_DAY_FREIGHT',
                                'FEDEX_2_DAY_FREIGHT',
                                'FEDEX_3_DAY_FREIGHT',
                                'FEDEX_NATIONAL_FREIGHT',
                                'INTERNATIONAL_ECONOMY_FREIGHT',
                                'INTERNATIONAL_PRIORITY_FREIGHT',
                            ],
                        ],
                    ],
                ],
            ],
            'delivery_confirmation_types' => [
                'NO_SIGNATURE_REQUIRED' => 'Not Required',
                'ADULT' => 'Adult',
                'DIRECT' => 'Direct',
                'INDIRECT' => 'Indirect',
            ],
            'unit_of_measure' => [
                'LB' => 'Pounds',
                'KG' => 'Kilograms',
            ],
        ];
        if (!isset($codes[$type])) {
            return false;
        } elseif ('' === $code) {
            return $codes[$type];
        }
        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }
    }

    /**
     * Do shipment request to carrier web service, obtain Print Shipping Labels and process errors in response
     *
     * @param DataObject $request
     * @return DataObject
     */
    protected function _doShipmentRequest(DataObject $request)
    {
        $this->_prepareShipmentRequest($request);
        $result = new DataObject();
        $client = $this->_createShipSoapClient();
        $requestClient = $this->_formShipmentRequest($request);
        
        $response = $client->processShipment($requestClient);
    

        if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {
            $shippingLabelContent = $response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image;
            $trackingNumber = $this->getTrackingNumber(
                $response->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds
            );
            $result->setShippingLabelContent($shippingLabelContent);
            $result->setTrackingNumber($trackingNumber);
        } else {
            $error = [];
            if (is_array($response->Notifications)) {
                foreach ($response->Notifications as $notification) {
                    $error['result']['code'] .= $notification->Code . '; ';
                    $error['result']['error'] .= $notification->Message . '; ';
                }
            } else {
                $error['result']['code'] = $response->Notifications->Code . ' ';
                $error['result']['error'] = $response->Notifications->Message . ' ';
            }
            $result->setErrors($error);
        }
        $result->setGatewayResponse($client->__getLastResponse());

        return $result;
    }

    /**
     * Prepare shipment request.
     * Validate and correct request information
     *
     * @param DataObject $request
     * @return void
     */
    protected function _prepareShipmentRequest(DataObject $request)
    {
        $phonePattern = '/[\s\_\-\(\)]+/';
        $phoneNumber = $request->getShipperContactPhoneNumber();
        $phoneNumber = preg_replace($phonePattern, '', $phoneNumber);
        $request->setShipperContactPhoneNumber($phoneNumber);
        $phoneNumber = $request->getRecipientContactPhoneNumber();
        $phoneNumber = preg_replace($phonePattern, '', $phoneNumber);
        $request->setRecipientContactPhoneNumber($phoneNumber);
    }

    /**
     * @param array|object $trackingIds
     * @return string
     */
    private function getTrackingNumber($trackingIds)
    {
        return is_array($trackingIds) ? array_map(
            function ($val) {
                return $val->TrackingNumber;
            },
            $trackingIds
        ) : $trackingIds->TrackingNumber;
    }

    /**
     * Form array with appropriate structure for shipment request
     *
     * @param DataObject $request
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _formShipmentRequest(DataObject $request)
    {
        if ($request->getReferenceData()) {
            $referenceData = $request->getReferenceData();
        } else {
            $referenceData = '';
        }
        $packageParams = new DataObject($request->getPackageParams());
        $customsValue = $packageParams->getCustomsValue();
        $height = $packageParams->getHeight();
        $width = $packageParams->getWidth();
        $length = $packageParams->getLength();
        $weightUnits = 'KG';
        $unitPrice = 0;
        $itemsQty = 0;
        $itemsDesc = [];
        $productIds = [];
        $packageItems = $request->getPackageItems();
        foreach ($packageItems as $itemShipment) {
            $item = new DataObject($itemShipment);

            $unitPrice += $item->getPrice();
            $itemsQty += $item->getQty();

            $itemsDesc[] = $item->getName();
            $productIds[] = $item->getId();
        }

        $optionType = $request->getShippingMethod() == self::RATE_REQUEST_SMARTPOST
            ? 'SERVICE_DEFAULT' : 'ADULT';
        $requestClient = [
            'RequestedShipment' => [
                'ShipTimestamp' => time(),
                'DropoffType' => $request->getDropoff(),
                'PackagingType' => $request->getPackagingType(),
                'ServiceType' => $request->getShippingMethod(),
                'Shipper' => [
                    'Contact' => [
                        'PersonName' => $request->getShipperContactPersonName(),
                        'CompanyName' => $request->getShipperContactCompanyName(),
                        'PhoneNumber' => $request->getShipperContactPhoneNumber(),
                    ],
                    'Address' => [
                        'StreetLines' => [$request->getShipperAddressStreet()],
                        'City' => $request->getShipperAddressCity(),
                        'StateOrProvinceCode' => $request->getShipperAddressStateOrProvinceCode(),
                        'PostalCode' => $request->getShipperAddressPostalCode(),
                        'CountryCode' => $request->getShipperAddressCountryCode(),
                    ],
                ],
                'Recipient' => [
                    'Contact' => [
                        'PersonName' => $request->getRecipientContactPersonName(),
                        'CompanyName' => $request->getRecipientContactCompanyName(),
                        'PhoneNumber' => $request->getRecipientContactPhoneNumber(),
                    ],
                    'Address' => [
                        'StreetLines' => [$request->getRecipientAddressStreet()],
                        'City' => $request->getRecipientAddressCity(),
                        'StateOrProvinceCode' => $request->getRecipientAddressStateOrProvinceCode(),
                        'PostalCode' => $request->getRecipientAddressPostalCode(),
                        'CountryCode' => $request->getRecipientAddressCountryCode(),
                        'Residential' => false,
                    ],
                ],
                'ShippingChargesPayment' => [
                    'PaymentType' => 'SENDER',
                    'Payor' => [
                        'ResponsibleParty' => [
                            'AccountNumber' => $this->getFedexAccount()
                        ]
                    ],
                ],
                'LabelSpecification' => [
                    'LabelFormatType' => 'COMMON2D',
                    'ImageType' => 'PDF',
                    'LabelStockType' => 'PAPER_8.5X11_TOP_HALF_LABEL',
                ],
                'RateRequestTypes' => 'LIST',
                'PackageCount' => 1,
                'RequestedPackageLineItems' => [
                    'SequenceNumber' => '1',
                    'Weight' => ['Units' => $weightUnits, 'Value' => $request->getPackageWeight()],
                    'CustomerReferences' => [
                        'CustomerReferenceType' => 'CUSTOMER_REFERENCE',
                        'Value' => $referenceData,
                    ],
                    'SpecialServicesRequested' => [
                        'SpecialServiceTypes' => 'SIGNATURE_OPTION',
                        'SignatureOptionDetail' => ['OptionType' => $optionType],
                    ],
                ],
            ],
        ];

        if ($request->getMasterTrackingId()) {
            $requestClient['RequestedShipment']['MasterTrackingId'] = $request->getMasterTrackingId();
        }

        if ($request->getShippingMethod() == self::RATE_REQUEST_SMARTPOST) {
            $requestClient['RequestedShipment']['SmartPostDetail'] = [
                'Indicia' => (double)$request->getPackageWeight() >= 1 ? 'PARCEL_SELECT' : 'PRESORTED_STANDARD',
                'HubId' => $request->getData('smartpost_hubid'),
            ];
        }

        // set dimensions
        if ($length || $width || $height) {
            $requestClient['RequestedShipment']['RequestedPackageLineItems']['Dimensions'] = [
                'Length' => $length,
                'Width' => $width,
                'Height' => $height,
                'Units' => $packageParams->getDimensionUnits(),
            ];
        }

        return $this->_getAuthDetails() + $requestClient;
    }

    /**
     * Return array of authenticated information
     *
     * @return array
     */
    protected function _getAuthDetails()
    {
        return [
            'WebAuthenticationDetail' => [
                'UserCredential' => [
                    'Key' => $this->getKey(),
                    'Password' => $this->getPassword()
                ]
            ],
            'ClientDetail' => [
                'AccountNumber' => $this->getFedexAccount(),
                'MeterNumber' => $this->getMeterNumber(),
                'Localization' => [
                    'LanguageCode' => 'ES',
                    'LocaleCode' => 'ES'
                ]
            ],
            'TransactionDetail' => [
                'CustomerTransactionId' => '*** Express Domestic Shipping Request v9 using PHP ***',
            ],
            'Version' => $this->getVersionInfoShip()
        ];
    }
}

/**
 * Serialize data to JSON, unserialize JSON encoded data
 *
 * @api
 * @since 100.2.0
*/
class Json
{
    /**
     * @inheritDoc
     * @since 100.2.0
     */
    public function serialize($data)
    {
        $result = json_encode($data);
        if (false === $result) {
            throw new \InvalidArgumentException("Unable to serialize value. Error: " . json_last_error_msg());
        }
        return $result;
    }
    /**
     * @inheritDoc
     * @since 100.2.0
     */
    public function unserialize($string)
    {
        $result = json_decode($string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException("Unable to unserialize value. Error: " . json_last_error_msg());
        }
        return $result;
    }
}

/**
* Class ClientFactory
*/
class ClientFactory
{
    /**
     * Factory method for \SoapClient
     *
     * @param string $wsdl
     * @param array $options
     * @return \SoapClient
     */
    public function create($wsdl, array $options = [])
    {
        return new \SoapClient($wsdl, $options);
    }
}

/**
* Class stdObject
*/
class stdObject {
    public function __construct(array $arguments = array()) {
        if (!empty($arguments)) {
            foreach ($arguments as $property => $argument) {
                $this->{$property} = $argument;
            }
        }
    }

    public function __call($method, $arguments) {
        $arguments = array_merge(array("stdObject" => $this), $arguments); // Note: method argument 0 will always referred to the main class ($this).
        if (isset($this->{$method}) && is_callable($this->{$method})) {
            return call_user_func_array($this->{$method}, $arguments);
        } else {
            throw new \Exception("Fatal error: Call to undefined method stdObject::{$method}()");
        }
    }
}