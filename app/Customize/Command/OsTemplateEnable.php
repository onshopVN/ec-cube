<?php
namespace Customize\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class OsTemplateEnable extends \Symfony\Component\Console\Command\Command
{
    protected static $defaultName = 'onshop:template:enable';

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
        $this->setDescription('Enable a template by code')
            ->setHelp('Install a template by --code option');
        $this->addOption('code', null, InputOption::VALUE_REQUIRED, 'Code of template');
    }

    /**
     * {@inheritDoc}
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $code = $input->getOption('code');
        $result = $this->templateService->enable( $code);
        $output->writeln($result);
    }
}
