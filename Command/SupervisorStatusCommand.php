<?php

namespace DavidKmenta\CommandSupervisorBundle\Command;

use DavidKmenta\CommandSupervisorBundle\Service\CommandSupervisor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

class SupervisorStatusCommand extends Command
{
    /**
     * @var CommandSupervisor
     */
    private $commandSupervisor;

    /**
     * @param array $commands
     * @param string $path
     * @param Finder $finder
     */
    public function __construct(CommandSupervisor $commandSupervisor)
    {
        parent::__construct();

        $this->commandSupervisor = $commandSupervisor;
    }

    protected function configure()
    {
        $this
            ->setName('command-supervisor:status')
            ->setDescription('Shows the current status of the supervised commands.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);

        $style->title('Status of the supervised commands');

        $style->table(
            ['Command name', 'Threshold', 'Last successful run', 'Current status'],
            $this->resolveCommandsStatuses()
        );
    }

    /**
     * @return array[]
     */
    private function resolveCommandsStatuses()
    {
        $statuses = [];

        foreach ($this->commandSupervisor->getCommandsStatuses() as $commandStatus) {
            $statuses[] = [
                $commandStatus->getName(),
                $this->resolveThershold($commandStatus->getThreshold()),
                $this->resolveLastRun($commandStatus->getLastRun()),
                $this->resolveStatus($commandStatus->getStatus()),
            ];
        }

        return $statuses;
    }

    /**
     * @param int|null $threshold
     * @return string
     */
    private function resolveThershold($threshold)
    {
        return $threshold > 0 ? sprintf('%d sec.', $threshold) : 'N/A';
    }

    /**
     * @param \DateTime|null $lastRun
     * @return string
     */
    private function resolveLastRun($lastRun)
    {
        return $lastRun ? $lastRun->format('Y-m-d H:i:s') : 'N/A';
    }

    /**
     * @param bool|null $status
     * @return string
     */
    private function resolveStatus($status)
    {
        if ($status === null) {
            return 'Unknown';
        }

        return $status ? 'OK' : 'ERR';
    }
}
