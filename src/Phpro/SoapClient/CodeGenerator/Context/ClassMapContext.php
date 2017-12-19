<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Zend\Code\Generator\AbstractGenerator;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;

/**
 * Class ClassMapContext
 *
 * @package Phpro\SoapClient\CodeGenerator\Context
 */
class ClassMapContext extends AbstractGeneratorInterface implements ContextInterface, GeneratorContextInterface
{

    /**
     * @var TypeMap
     */
    private $typeMap;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $destination;

    /**
     * @var ClassGenerator
     */
    private $class;

    /**
     * TypeContext constructor.
     *
     * @param ClassGenerator $class
     * @param TypeMap        $typeMap
     * @param string         $name
     * @param string         $namespace
     * @param string         $destination
     */
    public function __construct(
        ClassGenerator $class,
        TypeMap $typeMap,
        string $name,
        string $namespace,
        string $destination
    ) {
        $this->typeMap = $typeMap;
        $this->name = $name;
        $this->namespace = $namespace;
        $this->destination = $destination;
        $this->class = $class;
    }

    /**
     * @return TypeMap
     */
    public function getTypeMap(): TypeMap
    {
        return $this->typeMap;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * Where should the file be written to?
     * @return \SplFileInfo
     */
    public function getFileInfo(): \SplFileInfo
    {
        return new \SplFileInfo($this->destination.DIRECTORY_SEPARATOR.$this->name.'.php');
    }

    public function getFileGenerator(): FileGenerator
    {
        if ($this->file !== null) {
            return $this->file;
        }
        $file = new FileGenerator();
        $file->setClass($this->class);

        return $file;
    }
}
