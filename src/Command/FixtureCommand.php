<?php

namespace WebEtDesign\ParameterBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebEtDesign\ParameterBundle\Entity\Parameter;
use WebEtDesign\ParameterBundle\Model\ParameterManagerInterface;

class FixtureCommand extends Command
{

    private array $parameterFixtures;

    private ParameterManagerInterface $manager;

    public function __construct(ParameterManagerInterface $manager, string $name = null, $parameterFixtures = [])
    {
        parent::__construct($name);
        $this->manager           = $manager;
        $this->parameterFixtures = $parameterFixtures;
    }

    protected function configure(): void
    {
        $this
            ->setName('parameter:fixture')
            ->setDescription('Create functional parameter in database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $parameters = $this->manager->findIndexByCode();
        foreach ($this->parameterFixtures as $code => $fixture) {
            if (isset($parameters[$code])) {
                $output->writeln('<info>Parameter ' . $code . ' already exist</info>');
                continue;
            }

            $parameter = new Parameter($code);
            $parameter->setType($fixture['type']);
            $parameter->setLabel($fixture['label'] ?? '');
            $parameter->setValue($fixture['default_value'] ?? null);
            $parameter->setDeletable(false);
            $parameter->setConfig($fixture['config'] ?? null);

            $this->manager->add($parameter);

            $output->writeln('<info>Parameter ' . $code . ' as added</info>');
        }

        $this->manager->save();

        $output->writeln('<info>Parameters saved</info>');

        return Command::SUCCESS;
    }
}
