<?php

namespace Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\Context\ClassMapContext;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\GeneratorContextInterface;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSetInterface;
use Zend\Code\Generator\FileGenerator;

/**
 * Class ClassMapGenerator
 *
 * @package Phpro\SoapClient\CodeGenerator
 */
class ClassMapGenerator implements GeneratorInterface
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
     * @param GeneratorContextInterface $context
     * @return string
     */
    public function generate(GeneratorContextInterface $context): string
    {
        $this->ruleSet->applyRules($context);

        return $context->getFileGenerator()->generate();
    }
}
