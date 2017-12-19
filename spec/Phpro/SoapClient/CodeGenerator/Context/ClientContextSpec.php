<?php

namespace spec\Phpro\SoapClient\CodeGenerator\Context;

use Phpro\SoapClient\CodeGenerator\Context\ClientContext;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Model\Client;
use PhpSpec\ObjectBehavior;
use Zend\Code\Generator\ClassGenerator;

/**
 * Class TypeContextSpec
 *
 * @package spec\Phpro\SoapClient\CodeGenerator\Context
 * @mixin ClientContext
 */
class ClientContextSpec extends ObjectBehavior
{
    function let(ClassGenerator $class, Client $client)
    {
        $this->beConstructedWith($class, $client, 'src/test', 'Client');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClientContext::class);
    }

    function it_is_a_context()
    {
        $this->shouldImplement(ContextInterface::class);
    }

    function it_has_a_class_generator(ClassGenerator $class)
    {
        $this->getClass()->shouldReturn($class);
    }

    function it_has_a_client(Client $client)
    {
        $this->getClient()->shouldReturn($client);
    }
}
