<?php

namespace spec\Phpro\SoapClient\Console\Helper;

use Phpro\SoapClient\CodeGenerator\Context\GeneratorContextInterface;
use Phpro\SoapClient\CodeGenerator\GeneratorInterface;
use Phpro\SoapClient\Util\Filesystem;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Phpro\SoapClient\Console\Helper\FileOverwriteHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Code\Generator\FileGenerator;

/**
 * Class FileOverwriteHelperSpec
 */
class FileOverwriteHelperSpec extends ObjectBehavior
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function let(Filesystem $filesystem, InputInterface $input, OutputInterface $output)
    {
        $this->filesystem = $filesystem;
        $this->beConstructedWith($filesystem, $input, $output);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FileOverwriteHelper::class);
    }

    function it_should_handle_new_files(GeneratorInterface $generator, FileGenerator $fileGenerator, GeneratorContextInterface $context)
    {
        $this->filesystem->fileExists('/tmp/test.php')->willReturn(false);
        $this->filesystem->ensureDirectoryExists('/tmp')->shouldBeCalled();
        $generator->generate($fileGenerator, null)->willReturn('');
        $object = null;
        $path = '/tmp/test.php';

        $this->handle($generator, $context)->shouldReturn(false);
    }
}
