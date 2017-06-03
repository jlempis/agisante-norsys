<?php

namespace Gestime\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\Collections;

class ImportProspectsCommand  extends ContainerAwareCommand {
  /**
   * [configure description]
   * @return void
   */
  protected function configure()
  {
    $this
      ->setName('prospects:geoloc')
      ->setDescription('Agisante : Geolocalisation des prospects');
  }

  /**
   * [execute description]
   * @param  InputInterface  $input
   * @param  OutputInterface $output
   * @return boolean
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {

    $em = $this->getContainer()->get('doctrine')->getManager();
    $utils = $this->getContainer()->get('gestime_core.utilities');

    //Liste des abonnes du site
    $prospects = $em->getRepository('GestimeCoreBundle:Prospect')->getNonGeocodedAdresses();


    //Mise à jour des coordonnées
    $nbProspectsMaj = 0;
    $nbProspects = 0;
    foreach ($prospects as $prospect) {
      $nbProspects ++;
      $adresse =$prospect->getAdresse3().' '.$prospect->getCodePostal().' '. $prospect->getVille();

      $coordonnees = $utils->getGeoLoc($adresse);

      if (count($coordonnees) > 0) {
        if ($coordonnees['lng'] != 0 && $coordonnees['lat'] != 0)
        {
          $prospect->setLongitude($coordonnees['lng']);
          $prospect->setLatitude($coordonnees['lat']);
          $em->persist($prospect);
          $nbProspectsMaj ++;
          if ($nbProspectsMaj >= 2000) {
            $em->flush();
            break;
          }
          sleep(1);
          $output->writeln($nbProspectsMaj);
          if ($nbProspectsMaj % 100 == 0) {
            $em->flush();
          }
        }
        else
        {

          $output->writeln('---------------------');
          $output->writeln($adresse);
        }
      }
    }

    $em->flush();
    $texte = '%d adresses mise à jour sur %d';
    $output->writeln(sprintf($texte, $nbProspectsMaj, $nbProspects));
  }

}
