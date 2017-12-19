<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\CodeGenerator\ClassMapGenerator;
use Phpro\SoapClient\CodeGenerator\Config\ConfigInterface;
use Phpro\SoapClient\CodeGenerator\Context\ClassMapContext;
use Phpro\SoapClient\CodeGenerator\Model\TypeMap;
use Phpro\SoapClient\Console\Filesystem\FileHandler;
use Phpro\SoapClient\Console\Helper\FileOverwriteHelper;
use Phpro\SoapClient\Exception\InvalidArgumentException;
use Phpro\SoapClient\Soap\ClassMap\ClassMap;
use Phpro\SoapClient\Soap\SoapClient;
use Phpro\SoapClient\Util\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;

/**
 * Class GenerateTypesCommand
 *
 * @package Phpro\SoapClient\Console\Command
 */
class GenerateClassmapCommand extends Command
{

    public const COMMAND_NAME = 'generate:classmap';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * GenerateClassmapCommand constructor.
     *
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
            ->setDescription('Generates a classmap based on WSDL.')
            ->addOption(
                'config',
                null,
                InputOption::VALUE_REQUIRED,
                'The location of the soap code-generator config file'
            )
            ->addOption(
                'overwrite',
                'o',
                InputOption::VALUE_NONE,
                'Makes it possible to overwrite by default'
            );
    }

    /**
     * {@inheritdoc}
     * @throws \Phpro\SoapClient\Exception\InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configFile = $input->getOption('config');
        if (!$configFile || !$this->filesystem->fileExists($configFile)) {
            throw InvalidArgumentException::invalidConfigFile();
        }

        /** @noinspection PhpIncludeInspection */
        $config = include $configFile;
        if (!$config instanceof ConfigInterface) {
            throw InvalidArgumentException::invalidConfigFile();
        }

        $soapClient = new SoapClient($config->getWsdl(), $config->getSoapOptions());
        $typeMap = TypeMap::fromSoapClient($config->getTypeNamespace(), $soapClient);
        $overwite = new FileOverwriteHelper($this->filesystem, $input, $output);
        $generator = new ClassMapGenerator($config->getRuleSet());
        $context = new ClassMapContext(
            new ClassGenerator(),
            $typeMap,
            $config->getClassMapName(),
            $config->getClassMapNamespace(),
            $config->getClassMapDestination()
        );
        $overwite->handle($generator, $context);
        $output->write('DONE');
    }
}
