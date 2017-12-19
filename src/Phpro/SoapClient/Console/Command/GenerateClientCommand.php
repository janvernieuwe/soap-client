<?php

namespace Phpro\SoapClient\Console\Command;

use Phpro\SoapClient\CodeGenerator\ClientGenerator;
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Config\ConfigInterface;
use Phpro\SoapClient\CodeGenerator\Context\ClientContext;
use Phpro\SoapClient\CodeGenerator\Model\Client;
use Phpro\SoapClient\CodeGenerator\Model\ClientMethodMap;
use Phpro\SoapClient\Console\Filesystem\FileHandler;
use Phpro\SoapClient\Console\Helper\FileOverwriteHelper;
use Phpro\SoapClient\Exception\InvalidArgumentException;
use Phpro\SoapClient\Soap\SoapClient;
use Phpro\SoapClient\Util\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;

/**
 * Class GenerateClientCommand
 *
 * @package Phpro\SoapClient\Console\Command
 */
class GenerateClientCommand extends Command
{

    public const COMMAND_NAME = 'generate:client';

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
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Generates a client based on WSDL.')
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

        $configFile = $this->input->getOption('config');
        if (!$configFile || !$this->filesystem->fileExists($configFile)) {
            throw InvalidArgumentException::invalidConfigFile();
        }

        $config = include $configFile;
        if (!$config instanceof ConfigInterface) {
            throw InvalidArgumentException::invalidConfigFile();
        }
        if (!$config instanceof Config) {
            throw InvalidArgumentException::invalidConfigFile();
        }

        $soapClient = new SoapClient($config->getWsdl(), $config->getSoapOptions());
        $methodMap = ClientMethodMap::fromSoapClient($soapClient, $config->getTypesNamespace());
        $client = new Client($config->getClientName(), $config->getClientNamespace(), $methodMap);
        $generator = new ClientGenerator($config->getRuleSet());
        $filehandler = new FileOverwriteHelper($this->filesystem, $input, $output);
        $context = new ClientContext(
            new ClassGenerator(),
            $client,
            $config->getClientDestination(),
            $config->getClientName()
        );

        $filehandler->handle($generator, $context);
        $this->output->writeln('Done');
    }
}
