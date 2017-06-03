<?php

namespace Gestime\EventBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Goutte\Client;

/**
 * Classe de tests des IHMs absence
 *
 */
class AbsenceControllerTest extends WebTestCase
{
    /**
     * [testIndex description]
     * @return void
     */
    public function testIndex()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/admin/absences/');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Affichage de l")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Dernier")')->count());

        //On teste l'ajout (Acces à la page)
        $form = $crawler->selectButton('Ajouter une absence')->form();
        $client->submit($form);
    }

    /**
     * [testAjouter description]
     * @return void
     */
    public function testAjouter()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/admin/absences/ajouter');

        //La liste s'affiche
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Ajouter une absence")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Enregistrer")')->count());

        $form = $crawler->selectButton('Enregistrer')->form();
        //Recupératon de l'iD du medecin
        $medecinId = $crawler->filter('#absence_medecin option:contains("Dr Bouquillon")')->attr('value');
        $crawler = $client->submit($form, array('absence[medecin]'          => $medecinId ,
                                                'absence[debut]'            => '2015-12-30',
                                                'absence[fin]'              => '2015-12-31',
                                                'datepicker_absence[debut]' => '2015-12-30',
                                                'datepicker_absence[fin]'   => '2015-12-31',
                                                'absence[commentaire]'      => 'test'
                                                ));

    }
    /**
     * [testEdit description]
     * @return void
     */
    public function testEdit()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/admin/absences/edit/1');

        //La liste s'affiche
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Modifier une absence")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Enregistrer")')->count());

        //On teste la mise à jour (Acces à la page)
        $form = $crawler->selectButton('Enregistrer')->form();
        $client->submit($form);
    }

    /**
     * [testDelete description]
     * @return void
     */
    public function testDelete()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/admin/absences/suppr/1');

        //La liste s'affiche
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Supprimer une absence")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Enregistrer")')->count());

        //On teste la suppression (Acces à la page)
        $form = $crawler->selectButton('Enregistrer')->form();
        $client->submit($form);
    }


    /**
     * Test recupération infos
     * @return void
     */
    public function testJsonIndex()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/admin/absences/grille');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("iTotalRecords")')->count());
    }

    /**
     * [createAuthorizedClient description]
     * @return Client
     */
    protected function createAuthorizedClient()
    {
        $client = static::createClient();
        $cnxMgr = $client->getContainer()->get('gestime.test.manager');

        return $cnxMgr->getAuthenticatedSession($client);
    }
}
