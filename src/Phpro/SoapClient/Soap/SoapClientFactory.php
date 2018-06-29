<?php

namespace Phpro\SoapClient\Soap;

use Phpro\SoapClient\Soap\ClassMap\ClassMapCollection;
use Phpro\SoapClient\Soap\TypeConverter\TypeConverterCollection;

/**
 * Class SoapClientFactory
 *
 * @package Phpro\SoapClient\Soap
 */
class SoapClientFactory
{

    /**
     * @var ClassMapCollection
     */
    private $classMap;

    /**
     * @param ClassMapCollection $classMap
     */
    public function __construct(ClassMapCollection $classMap)
    {
        $this->classMap = $classMap;
    }

    /**
     * @param string $wsdl
     * @param array $soapOptions
     *
     * @return SoapClient
     */
    public function factory(string $wsdl, array $soapOptions = []): SoapClient
    {
        $defaults = [
            'trace' => true,
            'exceptions' => true,
            'keep_alive' => true,
            'cache_wsdl' => WSDL_CACHE_BOTH,
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            'classmap' => $this->classMap->toSoapClassMap(),
            'typemap' => TypeConverterCollection::createDefaultCollection(),
        ];

        $options = array_merge($defaults, $soapOptions);
        $options['typemap'] = $this->convertTypeMapToSoap($options['typemap']);

        /** @var TypeConverterCollection $typemap */
        $typemap = $options['typemap'];
        $options['typemap'] = $typemap->toSoapTypeMap();

        /** @var TypeConverterCollection $typemap */
        $typemap = $options['typemap'];
        $options['typemap'] = $typemap->toSoapTypeMap();

        return new SoapClient($wsdl, $options);
    }

    /**
     * @param TypeConverterCollection $collection
     * @return array
     */
    private function convertTypeMapToSoap(TypeConverterCollection $collection): array
    {
        return $collection->toSoapTypeMap();
    }
}
