<?php

namespace Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\Context\ClientContext;
use Phpro\SoapClient\CodeGenerator\Context\ClientMethodContext;
use Phpro\SoapClient\CodeGenerator\Context\GeneratorContextInterface;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSetInterface;
use Zend\Code\Generator\ClassGenerator;

/**
 * Class ClientGenerator
 *
 * @package Phpro\SoapClient\CodeGenerator
 */
class ClientGenerator implements GeneratorInterface
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
     * @param GeneratorContextInterface|ClientContext $context
     * @return string
     */
    public function generate(GeneratorContextInterface $context): string
    {
        $file = $context->getFileGenerator();
        $client = $context->getObject();
        $class = $context->getClass() ?: new ClassGenerator();
        $class->setNamespaceName($client->getNamespace());
        $class->setName($client->getName());
        $methods = $client->getMethodMap();

        foreach ($methods->getMethods() as $method) {
            $this->ruleSet->applyRules(new ClientMethodContext($class, $method));
        }

        return $file->generate();
    }
}
