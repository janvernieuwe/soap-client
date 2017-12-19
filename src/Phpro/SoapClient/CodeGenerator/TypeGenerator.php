<?php

namespace Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\Context\GeneratorContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSetInterface;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;

/**
 * Class TypeGenerator
 *
 * @package Phpro\SoapClient\CodeGenerator
 */
class TypeGenerator implements GeneratorInterface
{
    /**
     * @var RuleSetInterface
     */
    private $ruleSet;

    /**
     * TypeGenerator constructor.
     *
     * @param RuleSetInterface $ruleSet
     */
    public function __construct(RuleSetInterface $ruleSet)
    {
        $this->ruleSet = $ruleSet;
    }

    /**
     * @param GeneratorContextInterface|TypeContext $context
     * @return string
     */
    public function generate(GeneratorContextInterface $context): string
    {
        $type = $context->getType();
        $class = $context->getClass() ?: new ClassGenerator();
        $class->setNamespaceName($type->getNamespace());
        $class->setName($type->getName());
        $this->ruleSet->applyRules($context);

        foreach ($type->getProperties() as $property) {
            $this->ruleSet->applyRules(new PropertyContext($class, $type, $property));
        }
        $file = new FileGenerator();
        $file->setClass($class);

        return $file->generate();
    }
}
