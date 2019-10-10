<?php
namespace Customize\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class OsTemplateInstall extends \Symfony\Component\Console\Command\Command
{
    protected static $defaultName = 'onshop:template:install';

    /**
     * @var \Customize\Service\TemplateService
     */
    protected $templateService;

    /**
     * OsTemplateInstall constructor.
     * @param \Customize\Service\TemplateService $templateService
     * @param null $name
     */
    public function __construct(
        \Customize\Service\TemplateService $templateService,
        $name = null
    ) {
        $this->templateService = $templateService;
        parent::__construct($name);
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('Install a template from package')
            ->setHelp('Install a template from --path, --name, --code options');
        $this->addOption('path', null, InputOption::VALUE_REQUIRED, 'Path of .zip file');
        $this->addOption('name', null, InputOption::VALUE_REQUIRED, 'Name of template');
        $this->addOption('code', null, InputOption::VALUE_REQUIRED, 'Code of template');
    }

    /**
     * {@inheritDoc}
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \ErrorException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getOption('path');
        $name = $input->getOption('name');
        $code = $input->getOption('code');
        $result = $this->templateService->install($path, $name, $code);
        $output->writeln($result);
    }
}
