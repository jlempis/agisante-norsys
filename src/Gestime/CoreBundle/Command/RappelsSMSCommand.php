<?php

namespace Gestime\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Génération des rappels des rendez-vous par SMS
 *
 */
class RappelsSMSCommand extends ContainerAwareCommand
{
    /**
     * configure
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('rappels:sms')
            ->setDescription('Gestime : Génération des rappels des rendez-vous par SMS')
            ->setDefinition(array(
                new InputArgument('site', InputArgument::REQUIRED, 'Le nom du site'),
            ));
    }

    /**
     * execute
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return boolean
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $site = $input->getArgument('site');

        $em = $this->getContainer()->get('doctrine')->getManager();
        $serviceSms = $this->getContainer()->get('gestime.sms');

        //Liste des médecins du site
        $site = $em->getRepository('GestimeCoreBundle:Site')
                       ->findByNom($site);

        //Liste des médecins qui ont souscrit au service
        $medecins = $em->getRepository('GestimeCoreBundle:Medecin')
                       ->getObjectListMedecins($site, 'O')->getQuery()->getResult();

        //Pour tous les médecins qui on souscrits au service de rappels
        $nbRappels = 0;
        $dateMini = new \DateTime('NOW');

        foreach ($medecins as $medecin) {
            $msg = $medecin->getMsgRappel();
            $dateMaxi = new \DateTime('NOW');
            $dateMaxi->add(new \DateInterval('P' . $medecin->getTempsAvantRappel() . 'D'));
            $dateMaxi->setTime(23, 59, 00);

            $output->writeln('Medecin : '.$medecin.'. Rdv du '.$dateMini->format('d/m/Y').' au '.$dateMaxi->format('d/m/Y'));

            $evenements = $em->getRepository('GestimeCoreBundle:Evenement')
                       ->getRappelsAFaire($medecin, $dateMini, $dateMaxi)->getQuery()->getResult();

            foreach ($evenements as $evenement) {
                $jour = $evenement->getDebutRdv()->format('d/m/Y');
                $heure = $evenement->getDebutRdv()->format('H:i');

                $txt1 = str_replace('%jour%', $jour, $msg);
                $messageAEnvoyer = str_replace('%heure%', $heure, $txt1);

                $serviceSms->addRappelToQueue($evenement, $messageAEnvoyer);
                $nbRappels += 1;

                //on tope le rendez-vous comme rappelé
                $evenement->setDateRappel($dateMini);
                $em->persist($evenement);
                $em->flush();
            }
        }
        $output->writeln($nbRappels.' rappels effectués.');
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('site')) {
            $site = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Le nom du site (SMF) :',
                function($site) {
                    $site = 'SMF';

                    return $site;
                }
            );
            $input->setArgument('site', $site);
        }
    }
}
