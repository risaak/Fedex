<?php

require_once('DataObject.php');

class EstafetaConection {

    protected $_rateServiceWsdl;
    protected $_shipServiceWsdl = null;
    protected $_trackServiceWsdl;
    
    private $serializer;
    private $soapClientFactory;

    protected $_rawRequest = null;
    protected $_result = null;

    private $debug;

    public function __construct($debug = false) {
        $wsdlBasePath = __DIR__ . '/wsdl/';
        
        $this->_shipServiceWsdl = $wsdlBasePath . 'ShipService_v23.wsdl';
        
        if($debug){
            $this->_rateServiceWsdl = 'http://frecuenciacotizadorqa.estafeta.com/Service.asmx?wsdl';
            $this->_rateServiceWsdl = $wsdlBasePath . 'rate_estafeta_qa.wsdl';
            $this->_shipServiceWsdl = 'https://labelqa.estafeta.com/EstafetaLabel20/services/EstafetaLabelWS?wsdl';
        }else{
            $this->_rateServiceWsdl = $wsdlBasePath . 'rate_estafeta.wsdl';
            $this->_trackServiceWsdl = 'https://tracking.estafeta.com/Service.asmx?wsdl';
            $this->_shipServiceWsdl = 'https://label.estafeta.com/EstafetaLabel20/services/EstafetaLabelWS?wsdl';
        }
        
        $this->serializer = $serializer = new Json();
        $this->soapClientFactory = new ClientFactory();
        $this->debug = $debug;
    }

    protected function _createSoapClient($wsdl, $trace = false)
    {
        $client = $this->soapClientFactory->create($wsdl);
        return $client;
    }

    protected function _createRateSoapClient()
    {
        return $this->_createSoapClient($this->_rateServiceWsdl);
    }

    protected function _createShipSoapClient()
    {
        return $this->_createSoapClient($this->_shipServiceWsdl);
    }

    protected function _createTrackSoapClient()
    {
        return $this->_createSoapClient($this->_trackServiceWsdl);
    }

    protected function getEstafetaId()
    {
        //Agregar id de producción en el string vacio
        return $this->debug ? '1': '';
    }

    protected function getEstafetaIdTracking()
    {
        //Agregar id de producción en el string vacio
        return $this->debug ? '25': '';
    }

    protected function getSuscriberId()
    {
        //Agregar id de producción en el string vacio
        return $this->debug ? '28': '';
    }

    protected function getEstafetaUser()
    {
        //Agregar usuario de producción en el string vacio
        return $this->debug ? 'AdminUser': '';
    }

    protected function getEstafetaUserTracking()
    {
        //Agregar usuario de producción en el string vacio
        return $this->debug ? 'Usuario1': '';
    }

    protected function getUserLabel()
    {
        //Agregar usuario de producción en el string vacio
        return $this->debug ? 'prueba1': '';
    }

    protected function getPassword()
    {
        //Agregar password de producción en el string vacio
        return $this->debug ? ',1,B(vVi': '';
    }

    protected function getPasswordTracking()
    {
        //Agregar password de producción en el string vacio
        return $this->debug ? '1GCvGIu$': '';
    }

    protected function getPasswordLabel()
    {
        //Agregar password de producción en el string vacio
        return $this->debug ? 'lAbeL_K_11': '';
    }

    public function setRawRequest($request)
    {
        $this->_rawRequest = $request;
        return $this;
    }

    public function getTracking($request) {
        $this->setRequestTracking($request);
        $response = $this->doRequestTracking();
        return $this->convertObjectToArray($response);
    }

    protected function doRequestTracking() {
        $r = $this->_rawRequest;

        $waylbills = $r->getWayBills();
        
        $WaybillRange = new StdClass();
        $WaybillRange -> initialWaybill = '';
        $WaybillRange -> finalWaybill = '';
       
        $WaybillList = new StdClass();
        $WaybillList -> waybillType = 'G';
        $WaybillList -> waybills = $waylbills;
        
        $SearchType = new StdClass();
        $SearchType -> waybillList = $WaybillList;
        $SearchType -> type = 'L';
        
        $HistoryConfiguration = new StdClass;
        $HistoryConfiguration -> includeHistory = 1;
        $HistoryConfiguration -> historyType = $r->getHistoryType();
        
        $Filter = new StdClass;
        $Filter -> filterInformation = 0;
        $Filter -> filterType = $r->getFilterType();
        
        $SearchConfiguration = new StdClass();
        $SearchConfiguration -> includeDimensions = $r->getDimensions();
        $SearchConfiguration -> includeWaybillReplaceData =  $r->getWaybillReplaceData();
        $SearchConfiguration -> includeReturnDocumentData =  $r->getReturnDocumentData();
        $SearchConfiguration -> includeMultipleServiceData =  $r->getMultipleServiceData();
        $SearchConfiguration -> includeInternationalData =  $r->getInternationalData();
        $SearchConfiguration -> includeSignature =  $r->getSignature();
        $SearchConfiguration -> includeCustomerInfo =  $r->getCustomerInfo();
        $SearchConfiguration -> historyConfiguration = $HistoryConfiguration;
        $SearchConfiguration -> filterType= $Filter;
        
        $client = $this->_createTrackSoapClient();

        $result = $client->ExecuteQuery(array(
            'suscriberId'=>25,
            'login'=>'Usuario1',
            'password'=> '1GCvGIu$',
            'searchType' => $SearchType,
            'searchConfiguration' => $SearchConfiguration
            )
        );

        return $result;
    }

    public function setRequestTracking($request) {
        $r = new DataObject();

        $r->setWayBills($request['waybills']);
        $r->setFilterType($request['filterType']);
        $r->setHistoryType($request['historyType']);
        $r->setDimensions($request['dimensions']);
        $r->setWaybillReplaceData($request['waybillReplaceData']);
        $r->setReturnDocumentData($request['returnDocumentData']);
        $r->setMultipleServiceData($request['multipleServiceData']);
        $r->setInternationalData($request['internationalData']);
        $r->setSignature($request['signature']);
        $r->setCustomerInfo($request['customerInfo']);

        $this->setRawRequest($r);

        return $this;
    }

    public function getRates($request) {
        $this->setRequestRate($request);
        $response = $this->_doRatesRequestRate();
        return $this->convertObjectToArray($response);
    }

    public function setRequestRate($request) {
        $r = new DataObject();

        $r->setfrecuencia($request['frecuencia']);
        $r->setPaquete($request['paquete']);
        $r->setLargo($request['largo']);
        $r->setPeso($request['peso']);
        $r->setAlto($request['alto']);
        $r->setAncho($request['ancho']);
        $r->setCpOrigen($request['cpOrigen']);
        $r->setCpDestino($request['cpDestino']);

        $this->setRawRequest($r);

        return $this;
    }

    protected function _doRatesRequestRate()
    {
        $ratesRequest = $this->_formRateRequest();
        try {
            $client = $this->_createRateSoapClient();
            $response = $client->FrecuenciaCotizador($ratesRequest);
        } catch (\Exception $e) {
            echo print_r(['error' => $e->getMessage(), 'code' => $e->getCode()],true);
        }
        return $response;
    }

    protected function _formRateRequest()
    {
        $r = $this->_rawRequest;
        $ratesRequest = [
            'idusuario' => $this->getEstafetaId(),
            'usuario' => $this->getEstafetaUser(), 
            'contra' => $this->getPassword(),
            'esFrecuencia' => $r->getfrecuencia(),
            'esLista' => 'true',
            'tipoEnvio' => [
                'EsPaquete' =>  $r->getPaquete(),
                'Largo' => $r->getLargo(), 
                'Peso' =>  $r->getPeso(), 
                'Alto' =>  $r->getAlto(), 
                'Ancho' =>  $r->getAncho()
            ],
            'datosOrigen' =>  $r->getCpOrigen(),
            'datosDestino' =>  $r->getCpDestino(),
        ];
        return $ratesRequest;
    }

    public function getLabel($request) {
        $this->setRequestLabel($request);
        $response = $this->_doRatesRequestLabel();
        return $this->convertObjectToArray($response);
    }

    public function setRequestLabel($request) {
        $r = new DataObject();

        $r->setValid($request['valid']);
        $r->setCustomerNumber($request['customerNumber']);
        $r->setQuadrant($request['quadrant']);
        $r->setPaperType($request['paperType']);
        $r->setLabelDescriptionListCount($request['labelDescriptionListCount']);
        $r->setAditionalInfo($request['aditionalInfo']);
        $r->setContent($request['content']);
        $r->setContentDescription($request['contentDescription']);
        $r->setCostCenter($request['costCenter']);
        $r->setDeliveryToEstafetaOffice($request['deliveryToEstafetaOffice']);
        $r->setDestinationCountryId($request['destinationCountryId']);
        $r->setDestinationAddress1($request['destination_address1']);
        $r->setDestinationAddress2($request['destination_address2']);
        $r->setDestinationCellPhone($request['destination_cellPhone']);
        $r->setDestinationCity($request['destination_city']);
        $r->setDestinationContactName($request['destination_contactName']);
        $r->setDestinationCorporateName($request['destination_corporateName']);
        $r->setDestinationCustomerNumber($request['destination_customerNumber']);
        $r->setDestinationNeighborhood($request['destination_neighborhood']);
        $r->setDestinationPhoneNumber($request['destination_phoneNumber']);
        $r->setDestinationState($request['destination_state']);
        $r->setDestinationZipCode($request['destination_zipCode']);
        $r->setNumberOfLabels($request['numberOfLabels']);
        $r->setOfficeNum($request['officeNum']);
        $r->setOriginAddress1($request['origin_address1']);
        $r->setOriginAddress2($request['origin_address2']);
        $r->setOriginCellPhone($request['origin_cellPhone']);
        $r->setOriginCity($request['origin_city']);
        $r->setOriginContactName($request['origin_contactName']);
        $r->setOriginCorporateName($request['origin_corporateName']);
        $r->setOriginCustomerNumber($request['origin_customerNumber']);
        $r->setOriginNeighborhood($request['origin_neighborhood']);
        $r->setOriginPhoneNumber($request['origin_phoneNumber']);
        $r->setOriginState($request['origin_state']);
        $r->setOriginZipCode($request['origin_zipCode']);
        $r->setParcelTypeId($request['parcelTypeId']);
        $r->setReference($request['reference']);
        $r->setReturnDocument($request['returnDocument']);
        $r->setServiceTypeId($request['serviceTypeId']);
        $r->setWeight($request['weight']);

        $this->setRawRequest($r);

        return $this;
    }

    protected function _doRatesRequestLabel()
    {
        $ratesRequest = $this->_formLabelRequest();
        try {
            $client = $this->_createShipSoapClient();
            $response = $client->CreateLabel($ratesRequest);
        } catch (\Exception $e) {
            echo print_r(['error' => $e->getMessage(), 'code' => $e->getCode()],true);
        }
        return $response;
    }

    protected function _formLabelRequest()
    {
        $r = $this->_rawRequest;
        $ratesRequest = [
            'valid' => $r->getValid(),
            'suscriberId' => $this->getSuscriberId(),
            'login' => $this->getUserLabel(), 
            'password' => $this->getPasswordLabel(),
            'customerNumber' => $r->getCustomerNumber(),
            'quadrant' => $r->getQuadrant(),
            'paperType' => $r->getPaperType(),
            'labelDescriptionListCount' => $r->getLabelDescriptionListCount(),
            'labelDescriptionList' => [
                'aditionalInfo' => $r->getAditionalInfo(),
                'content' => $r->getContent(),
                'contentDescription' => $r->getContentDescription(),
                'costCenter' => $r->getCostCenter(),
                'deliveryToEstafetaOffice' => $r->getDeliveryToEstafetaOffice(),
                'destinationCountryId' => $r->getDestinationCountryId(),
                'destinationInfo' => [
                    'address1' => $r->getDestinationAddress1(),
                    'address2' => $r->getDestinationAddress2(),
                    'cellPhone' => $r->getDestinationCellPhone(),
                    'city' => $r->getDestinationCity(),
                    'contactName' => $r->getDestinationContactName(),
                    'corporateName' => $r->getDestinationCorporateName(),
                    'customerNumber' => $r->getCustomerNumber(),
                    'neighborhood' => $r->getDestinationNeighborhood(),
                    'phoneNumber' => $r->getDestinationPhoneNumber(),
                    'state' => $r->getDestinationState(),
                    'valid' => $r->getValid(),
                    'zipCode' => $r->getDestinationZipCode()
                ],
                'numberOfLabels' => $r->getNumberOfLabels(),
                'officeNum' => $r->getOfficeNum(),
                'originInfo' => [
                    'address1' => $r->getOriginAddress1(),
                    'address2' => $r->getOriginAddress2(),
                    'cellPhone' => $r->getOriginCellPhone(),
                    'city' => $r->getOriginCity(),
                    'contactName' => $r->getOriginContactName(),
                    'corporateName' => $r->getOriginCorporateName(),
                    'customerNumber' => $r->getCustomerNumber(),
                    'neighborhood' => $r->getOriginNeighborhood(),
                    'phoneNumber' => $r->getOriginPhoneNumber(),
                    'state' => $r->getOriginState(),
                    'valid' => $r->getValid(),
                    'zipCode' => $r->getOriginZipCode()
                ],
                'originZipCodeForRouting' => $r->getOriginZipCode(),
                'parcelTypeId' => $r->getParcelTypeId(),
                'reference' => $r->getReference(),
                'returnDocument' => $r->getReturnDocument(),
                //'serviceTypeIdDocRet' => '50',
                'serviceTypeId' => $r->getServiceTypeId(),
                'valid' => $r->getValid(),
                'weight' => $r->getWeight(),
            ],
        ];
        return $ratesRequest;
    }

    public function convertObjectToArray($data) {

        if (is_object($data)) {
            $data = get_object_vars($data);
        }
    
        if (is_array($data)) {
            return array_map(array($this,__METHOD__), $data);
        }
        else {
            return $data;
        }
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