<?php

namespace Phpro\SoapClient\CodeGenerator\Context;

use Zend\Code\Generator\FileGenerator;

class AbstractGeneratorInterface
{
    protected $file;

    /**
     * @param string $path
     * @return FileGenerator
     */
    public function fileFromReflection(string $path): FileGenerator
    {
        return $this->file = FileGenerator::fromReflectedFileName($path);
    }
}
