<?php

namespace Gestime\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Reception des SMS
 *
 */
class SMSReceiveCommand extends ContainerAwareCommand
{
    /**
     * [configure description]
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('sms:receive')
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
                       ->getListReponseAChercher();

        foreach ($messages as $message) {
            $SMSManager->ReceptionReponse($message);
        }
        $output->writeln($SMSManager->getStatus());
    }
}
