<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\CodeGenerator\Config\ConfigInterface;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Phpro\SoapClient\CodeGenerator\TypeGenerator;
use Phpro\SoapClient\Console\Helper\FileOverwriteHelper;
use Phpro\SoapClient\Exception\InvalidArgumentException;
use Phpro\SoapClient\Soap\SoapClient;
use Phpro\SoapClient\Util\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Code\Generator\ClassGenerator;

/**
 * Class GenerateTypesCommand
 *
 * @package Phpro\SoapClient\Console\Command
 */
class GenerateTypesCommand extends Command
{

    public const COMMAND_NAME = 'generate:types';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Generates types based on WSDL.')
            ->addOption(
                'config',
                null,
                InputOption::VALUE_REQUIRED,
                'The location of the soap code-generator config file'
            )
            ->addOption('overwrite', 'o', InputOption::VALUE_NONE, 'Makes it possible to overwrite by default');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $filehandler = new FileOverwriteHelper($this->filesystem, $input, $output);

        $configFile = $this->input->getOption('config');
        if (!$configFile || !$this->filesystem->fileExists($configFile)) {
            throw InvalidArgumentException::invalidConfigFile();
        }

        $config = include $configFile;
        if (!$config instanceof ConfigInterface) {
            throw InvalidArgumentException::invalidConfigFile();
        }

        $soapClient = new SoapClient($config->getWsdl(), $config->getSoapOptions());
        $typeMap = TypeMap::fromSoapClient($config->getTypeNamespace(), $soapClient);
        $generator = new TypeGenerator($config->getRuleSet());

        foreach ($typeMap->getTypes() as $type) {
            $fileInfo = $type->getFileInfo($config->getTypeDestination());
            $context = new TypeContext(new ClassGenerator(), $type, $config->getTypeDestination(), $type->getName());
            if ($filehandler->handle($generator, $context)) {
                $this->output->writeln(
                    sprintf('Generated class %s to %s', $type->getFullName(), $fileInfo->getPathname())
                );
            }
        }

        $this->output->writeln('Done');
    }
}
