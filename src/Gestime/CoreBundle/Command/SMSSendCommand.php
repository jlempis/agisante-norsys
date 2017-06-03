<?php

namespace Gestime\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Envoi des SMS
 *
 */
class SMSSendCommand extends ContainerAwareCommand
{
    /**
     * [configure description]
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('sms:send')
            ->setDescription('Gestime : Traite les messages en attente dans la log');
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
        $em = $this->getContainer()->get('doctrine')->getManager();

        $messages = $em->getRepository('GestimeCoreBundle:LogMessage')
                       ->getListSmsATraiter();

        foreach ($messages as $message) {
            $SMSManager->traiteQueue($message->getId(),
                $message->getTexte(),
                $message->getNumeroEnvoi()
            );
        }
        $output->writeln($SMSManager->getStatus());
    }
}
