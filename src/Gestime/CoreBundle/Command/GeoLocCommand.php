<?php

namespace Gestime\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\Collections;

class GeoLocCommand  extends ContainerAwareCommand {
  /**
   * [configure description]
   * @return void
   */
  protected function configure()
  {
    $this
      ->setName('geoloc:abonne')
      ->setDescription('Agisante : Mise à jour des coordonnées adresses abonnés')
      ->setDefinition(array(
        new InputArgument('site', InputArgument::REQUIRED, 'Le nom du site'),
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

    $em = $this->getContainer()->get('doctrine')->getManager();
    $utils = $this->getContainer()->get('gestime_core.utilities');

    //Liste des abonnes du site
    $site = $em->getRepository('GestimeCoreBundle:Site')
      ->findByNom($site);

    $abonnes = $em->getRepository('GestimeCoreBundle:Abonne')
      ->getAbonnes($site)->getQuery()->getResult();

    //Mise à jour des coordonnées
    $abonnesMaj = 0;
    $nbabonnes = 0;
    foreach ($abonnes as $abonne) {
      $nbabonnes ++;
      $coordonnees = $utils->getGeoLoc($abonne->getAdresse());
      if (count($coordonnees) > 0) {
        if ($coordonnees['lng'] != 0 && $coordonnees['lat'] != 0)
        {
          $abonne->setLongitude($coordonnees['lng']);
          $abonne->setLatitude($coordonnees['lat']);
          $em->persist($abonne);
          $abonnesMaj ++;
        }
        else
        {
          $output->writeln('---------------------');
          $output->writeln($abonne->getAdresse());
        }
      }
    }

    $em->flush();
    $texte = '%d adresses mise à jour sur %d';
    $output->writeln(sprintf($texte, $abonnesMaj, $nbabonnes));
  }


  /**
   * @see Command
   */
  protected function interact(InputInterface $input, OutputInterface $output) {
    if (!$input->getArgument('site')) {
      $site = $this->getHelper('dialog')->askAndValidate(
        $output,
        'Le nom du site (SMF) :',
        function ($site) {
          $site = 'SMF';

          return $site;
        }
      );
      $input->setArgument('site', $site);
    }
  }
}
