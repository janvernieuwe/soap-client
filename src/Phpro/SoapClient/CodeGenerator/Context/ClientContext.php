<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Model\Client;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;

/**
 * Class ClientContext
 *
 * @package Phpro\SoapClient\CodeGenerator\Context
 */
class ClientContext extends AbstractGeneratorInterface implements ContextInterface, GeneratorContextInterface
{
    /**
     * @var ClassGenerator
     */
    private $class;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $destination;

    /**
     * @var string
     */
    private $name;

    /**
     * PropertyContext constructor.
     *
     * @param ClassGenerator $class
     * @param Client         $client
     * @param string         $destination
     * @param string         $name
     */
    public function __construct(ClassGenerator $class, Client $client, string $destination, string $name)
    {
        $this->class = $class;
        $this->client = $client;
        $this->destination = $destination;
        $this->name = $name;
    }

    /**
     * @return FileGenerator
     */
    public function getFileGenerator(): FileGenerator
    {
        $file = new FileGenerator();
        $file->setClass($this->class);

        return $file;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return ClassGenerator
     */
    public function getClass(): ClassGenerator
    {
        return $this->class;
    }

    public function getObject()
    {
        return $this->client;
    }

    public function getFileInfo(): \SplFileInfo
    {
        return new \SplFileInfo($this->destination.DIRECTORY_SEPARATOR.$this->name.'.php');
    }
}
