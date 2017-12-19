<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Zend\Code\Generator\FileGenerator;

/**
 * Interface ContextInterface
 *
 * @package Phpro\SoapClient\CodeGenerator\Context
 */
interface GeneratorContextInterface
{
    /**
     * @return FileGenerator
     */
    public function getFileGenerator(): FileGenerator;

    /**
     * Information about where the file should be generated
     * @return \SplFileInfo
     */
    public function getFileInfo(): \SplFileInfo;

    /**
     * @param string $path
     * @return FileGenerator
     */
    public function fileFromReflection(string $path): FileGenerator;
}
