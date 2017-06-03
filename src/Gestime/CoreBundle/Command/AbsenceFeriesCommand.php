<?php

namespace Gestime\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\Collections;

/**
 * Renseignement des jours feriés dans les absences
 *
 */
class AbsenceFeriesCommand extends ContainerAwareCommand
{
    /**
     * [configure description]
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('absences:feries')
            ->setDescription('Gestime : Renseigne les  jours feries dechaque année pour tous les medecins')
            ->setDefinition(array(
                new InputArgument('site', InputArgument::REQUIRED, 'Le nom du site'),
                new InputArgument('annee', InputArgument::REQUIRED, 'Année à intégrer'),
            ));
    }

    /**
     * [execute description]
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return boolean
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $site = $input->getArgument('site');
        $annee = $input->getArgument('annee');

        $em = $this->getContainer()->get('doctrine')->getManager();
        $absenceMgr = $this->getContainer()->get('gestime.absence.manager');

        //Liste des médecins du site
        $site = $em->getRepository('GestimeCoreBundle:Site')
                       ->findByNom($site);

        $medecins = $em->getRepository('GestimeCoreBundle:Medecin')
                       ->getObjectListMedecins($site)->getQuery()->getResult();

        //Ajout des jours chomés
        foreach ($medecins as $medecin) {
            $absenceMgr->majFeries($medecin, $annee);
        }
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

        if (!$input->getArgument('annee')) {
            $annee = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Choisissez l\'année à integrer ('.date('Y').') :',
                function($annee) {
                    $annee = date('Y');

                    return $annee;
                }
            );
            $input->setArgument('annee', $annee);
        }
    }
}
