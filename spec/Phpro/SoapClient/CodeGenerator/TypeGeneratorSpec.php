<?php

namespace spec\Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\GeneratorInterface;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Phpro\SoapClient\CodeGenerator\Rules\RuleSetInterface;
use Phpro\SoapClient\CodeGenerator\TypeGenerator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;

/**
 * Class TypeGeneratorSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator
 * @mixin TypeGenerator
 */
class TypeGeneratorSpec extends ObjectBehavior
{

    function let(RuleSetInterface $ruleSet)
    {
        $this->beConstructedWith($ruleSet);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TypeGenerator::class);
    }

    function it_is_a_generator()
    {
        $this->shouldImplement(GeneratorInterface::class);
    }

    function it_generates_types(
        RuleSetInterface $ruleSet,
        FileGenerator $file,
        ClassGenerator $class,
        TypeContext $context
    ) {


        $code = <<<CODE
<?php

code

CODE;
        $type = new Type('MyNamespace', 'MyType', ['prop1' => 'string']);
        $property = $type->getProperties()[0];

        $file->generate()->willReturn($code);
        $file->getClass()->willReturn($class);

        $class->setNamespaceName('MyNamespace')->shouldBeCalled();
        $class->setName('MyType')->shouldBeCalled();
        $class->getName()->shouldBeCalled();
        $class->isSourceDirty()->shouldBeCalled();
        $class->getUses()->shouldBeCalled();
        $class->generate()->willReturn('code');

        $ruleSet->applyRules(
            Argument::that(
                function (ContextInterface $context) use ($type) {
                    return $context instanceof TypeContext
                        && $context->getType() === $type;
                }
            )
        )->shouldBeCalled();


        $ruleSet->applyRules(
            Argument::that(
                function (ContextInterface $context) use ($type, $property) {
                    return $context instanceof PropertyContext
                        && $context->getType() === $type
                        && $context->getProperty() === $property;
                }
            )
        )->shouldBeCalled();

        $context->getFileGenerator()->willReturn($file);
        $context->getType()->willReturn($type);
        $context->getClass()->willReturn($class);
        $this->generate($context)->shouldReturn($code);
    }
}
