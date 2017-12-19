<?php

namespace spec\Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\ClassMapGenerator;
use Phpro\SoapClient\CodeGenerator\Context\ClassMapContext;
use Phpro\SoapClient\CodeGenerator\GeneratorInterface;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSetInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;

/**
 * Class ClassMapGeneratorSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator
 * @mixin ClassMapGenerator
 */
class ClassMapGeneratorSpec extends ObjectBehavior
{
    function let(RuleSetInterface $ruleSet, TypeMap $typeMap)
    {
        $this->beConstructedWith($ruleSet);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClassMapGenerator::class);
    }

    function it_is_a_generator()
    {
        $this->shouldImplement(GeneratorInterface::class);
    }

    function it_generates_classmaps(RuleSetInterface $ruleSet, FileGenerator $file, TypeMap $typeMap, ClassMapContext $context)
    {
        $ruleSet->applyRules(Argument::type(ClassMapContext::class))->shouldBeCalled();
        $file->generate()->willReturn('code');
        $context->getFileGenerator()->willReturn($file);
        $this->generate($context)->shouldReturn('code');
    }
}
