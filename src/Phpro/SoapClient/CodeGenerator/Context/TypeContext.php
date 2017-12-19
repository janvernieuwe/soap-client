<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Model\Type;
use Zend\Code\Generator\AbstractGenerator;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;

/**
 * Class TypeContext
 *
 * @package Phpro\SoapClient\CodeGenerator\Context
 */
class TypeContext extends AbstractGeneratorInterface implements ContextInterface, GeneratorContextInterface
{
    /**
     * @var ClassGenerator
     */
    private $class;

    /**
     * @var Type
     */
    private $type;

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
     * @param Type           $type
     * @param string         $destination
     * @param string         $name
     */
    public function __construct(ClassGenerator $class, Type $type, string $destination, string $name)
    {
        $this->class = $class;
        $this->type = $type;
        $this->destination = $destination;
        $this->name = $name;
    }

    /**
     * @return Type
     */
    public function getType(): Type
    {
        return $this->type;
    }

    public function getFileInfo(): \SplFileInfo
    {
        return new \SplFileInfo($this->destination.DIRECTORY_SEPARATOR.$this->name.'.php');
    }

    public function getFileGenerator(): FileGenerator
    {
        $file = new FileGenerator();
        $file->setClass($this->class);

        return $file;
    }

    /**
     * @return ClassGenerator
     */
    public function getClass(): ClassGenerator
    {
        return $this->class;
    }
}
