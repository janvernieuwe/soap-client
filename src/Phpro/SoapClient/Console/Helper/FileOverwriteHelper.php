<?php

namespace Phpro\SoapClient\Console\Helper;

use Phpro\SoapClient\CodeGenerator\Context\GeneratorContextInterface;
use Phpro\SoapClient\CodeGenerator\GeneratorInterface;
use Phpro\SoapClient\Util\Filesystem;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Zend\Code\Generator\FileGenerator;

class FileOverwriteHelper
{
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
     * FileHandler constructor.
     * @param Filesystem      $filesystem
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function __construct(Filesystem $filesystem, InputInterface $input, OutputInterface $output)
    {
        $this->filesystem = $filesystem;
        $this->input = $input;
        $this->output = $output;
    }

    public function handle(GeneratorInterface $generator, GeneratorContextInterface $context): bool
    {
        $path = (string)$context->getFileInfo();
        $this->filesystem->ensureDirectoryExists($context->getFileInfo()->getPath());

        // Handle existing class:
        if ($this->filesystem->fileExists($path)) {
            if ($this->handleExistingFile($generator, $context)) {
                return true;
            }

            // Ask if a class can be overwritten if it contains errors
            if (!$this->askForOverwrite()) {
                $this->output->writeln(sprintf('Skipping %s', $path));

                return false;
            }
        }

        // Try to create a blanco class:
        try {
            $this->generate($generator, $context);
        } catch (\Exception $e) {
            $this->output->writeln('<fg=red>'.$e->getMessage().'</fg=red>');

            return false;
        }

        return true;
    }

    /**
     * An existing file was found. Try to patch or ask if it can be overwritten.
     *
     * @param GeneratorInterface $generator
     * @param GeneratorContextInterface   $context
     * @return bool
     */
    private function handleExistingFile(GeneratorInterface $generator, GeneratorContextInterface $context): bool
    {
        $this->output->write(sprintf('%s exists. Trying to patch ...', $context->getFileInfo()->getPath()));
        $patched = $this->patchExistingFile($generator, $context);

        if ($patched) {
            $this->output->writeln('Patched!');

            return true;
        }

        $this->output->writeln('Could not patch.');

        return false;
    }

    /**
     * This method tries to patch an existing type class.
     *
     * @param GeneratorInterface $generator
     * @param GeneratorContextInterface   $context
     * @return bool
     * @internal param Type $type
     */
    private function patchExistingFile(GeneratorInterface $generator, GeneratorContextInterface $context): bool
    {
        $path = $context->getFileInfo()->getRealPath();
        try {
            $this->filesystem->createBackup($path);
            $file = FileGenerator::fromReflectedFileName($path);
            $context->setFile($file);
            $this->generate($generator, $context);
        } catch (\Exception $e) {
            $this->output->writeln('<fg=red>'.$e->getMessage().'</fg=red>');
            $this->filesystem->removeBackup($path);

            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function askForOverwrite(): bool
    {
        $overwriteByDefault = $this->input->getOption('overwrite');
        $question = new ConfirmationQuestion('Do you want to overwrite it?', $overwriteByDefault);

        return (new QuestionHelper())->ask($this->input, $this->output, $question);
    }

    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * Generates one type class
     *
     * @param GeneratorInterface $generator
     * @param GeneratorContextInterface   $context
     */
    private function generate(GeneratorInterface $generator, GeneratorContextInterface $context): void
    {
        $code = $generator->generate($context);
        $this->filesystem->putFileContents((string)$context->getFileInfo(), $code);
    }
}
