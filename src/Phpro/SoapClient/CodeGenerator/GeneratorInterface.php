<?php

namespace Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\Context\GeneratorContextInterface;

/**
 * Interface GeneratorInterface
 *
 * @package Phpro\SoapClient\CodeGenerator
 */
interface GeneratorInterface
{
    /**
     * @param GeneratorContextInterface $context
     * @return string
     */
    public function generate(GeneratorContextInterface $context): string;
}
