<?php

namespace Gestime\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Recupération du nombre de SMS Restants
 *
 */
class SMSStatusCommand extends ContainerAwareCommand
{
    /**
     * [configure description]
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('sms:nbrestants')
            ->setDescription('Gestime : Récupération du nb de SMS restants');
    }

    /**
     * [execute description]
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return boolean
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $SMSManager = $this->getContainer()->get('gestime.sms');
        $output->writeln($SMSManager->getStatus());
    }
}
