<?php
// src/Gestime/CoreBundle/Command/FirstInit.php
namespace Gestime\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Gestime\CoreBundle\Entity\Group;
use Gestime\CoreBundle\Entity\Site;

/**
 * Initialisation des utilisateurs
 *
 */
class FirstInitCommand extends ContainerAwareCommand
{
    /**
     * [configure description]
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('init:first')
            ->setDescription('Gestime : Création premier utilisateur et roles')
            ->addArgument('name', InputArgument::REQUIRED, 'Nom')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
            ->addOption('option', null, InputOption::VALUE_NONE, 'option')
        ;
    }

    /**
     * [execute description]
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return boolean
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Si  les groupes sont déjà crées, le script a déjà été exécuté. Arret
        $userManager = $this->getContainer()->get('fos_user.user_manager');
        $groupManager = $this->getContainer()->get('fos_user.group_manager');

        $groups = $groupManager->findGroups();

        if (count($groups) > 0) {
            $output->writeln('Le script a déjà été exécuté. Arrêt'."\n");

            return false;
        }

        $username = $input->getArgument('name');
        $password = $input->getArgument('password');

        $em = $this->getContainer()->get('doctrine')->getManager();

        //Création des roles élémentaires
        //Création des groupes
        $groupAdmin = new Group('SUPER_ADMIN_GESTIME');
        $groupAdmin->setRoles(array( 'ROLE_GESTION_SITES',
                                'ROLE_GESTION_UTILISATEURS',
                                'ROLE_GESTION_MEDECINS',
                                'ROLE_GESTION_ABONNES',
                                'ROLE_GESTION_LIGNES',
                                'ROLE_GESTION_REPONDEURS',
                                'ROLE_GESTION_FERMETURES',
                                'ROLE_GESTION_RDV',
                                'ROLE_GESTION_ABSENCES',
                                'ROLE_GESTION_MESSAGERIE',
                                'ROLE_GESTION_SMS',
                                'ROLE_GESTION_CONSIGNES',
                                'ROLE_VISU_AGENDA',
                                'ROLE_ACCES_DIRECT',
                                'ROLE_VISU_AGENDA_TOUS',
                                'ROLE_VISU_STATISTIQUES', ));
        $em->persist($groupAdmin);

        $groupAdminSite = new Group('ADMIN_SITE');
        $groupAdminSite->setRoles(array( 'ROLE_GESTION_UTILISATEURS',
                                'ROLE_GESTION_MEDECINS',
                                'ROLE_GESTION_ABONNES',
                                'ROLE_GESTION_FERMETURES',
                                'ROLE_VISU_AGENDA_TOUS', ));
        $em->persist($groupAdminSite);

        $group = new Group('ADMIN_BACK');
        $group->setRoles(array( 'ROLE_GESTION_UTILISATEURS',
                                'ROLE_GESTION_MEDECINS',
                                'ROLE_GESTION_ABONNES',
                                'ROLE_ACCES_DIRECT',
                                'ROLE_GESTION_REPONDEURS',
                                'ROLE_VISU_STATISTIQUES', ));
        $em->persist($group);

        $groupTousMedecin = new Group('TOUS_MEDECINS');
        $groupTousMedecin->setRoles(array('ROLE_VISU_AGENDA_TOUS',
                               'ROLE_GESTION_RDV', ));
        $em->persist($groupTousMedecin);

        $groupSecretaireSite = new Group('SECRETAIRE-SITE');
        $groupSecretaireSite->setRoles(array('ROLE_VISU_AGENDA_TOUS',
                               'ROLE_GESTION_RDV',
                               'ROLE_GESTION_CONSIGNES',
                               'ROLE_GESTION_MESSAGERIE',
                               'ROLE_MESSAGERIE_SITE',
                               'ROLE_GESTION_ABSENCES',
                               'ROLE_GESTION_SMS', ));
        $em->persist($groupSecretaireSite);

        $groupSecretaireCabinet = new Group('SECRETAIRE-CABINET');
        $groupSecretaireCabinet->setRoles(array('ROLE_VISU_AGENDA',
                               'ROLE_GESTION_ABSENCES',
                               'ROLE_GESTION_CONSIGNES',
                               'ROLE_GESTION_MESSAGERIE',
                               'ROLE_GESTION_RDV', ));
        $em->persist($groupSecretaireCabinet);

        $groupMedecin = new Group('MEDECIN');
        $groupMedecin->setRoles(array('ROLE_MEDECIN',
                               'ROLE_GESTION_ABSENCES',
                               'ROLE_VISU_AGENDA',
                               'ROLE_GESTION_RDV', ));
        $em->persist($groupMedecin);

        //Création du premier utilisateur

        $repository = $em->getRepository('GestimeCoreBundle:Site');
        $site = $repository->findByNom('SMF')[0];

        $repository = $em->getRepository('GestimeCoreBundle:Medecin');
        $medecin = $repository->findByNom('Dr Bouquillon')[0];

        $user = $userManager->createUser();
        $user->setSite($site);
        $user->setUsername($username);
        $user->setPlainPassword($password);
        $user->setEmail('test@example.com');
        $user->addGroup($groupAdmin);
        $user->addGroup($groupAdminSite);
        $user->addGroup($groupTousMedecin);
        $user->addGroup($groupSecretaireSite);
        $user->setSuperAdmin(true);
        $user->addMedecin($medecin);
        $user->setMedecindefault($medecin);
        $user->setEnabled(true);
        $userManager->updateUser($user);

        $em->flush();

        $text = 'Fin du script';
        $output->writeln($text);
    }
}
