<?php

namespace Gestime\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Classe de tests des IHMs repondeur
 *
 */
class RepondeurControllerTest extends WebTestCase
{
    /**
     * [testIndex description]
     * @return void
     */
    public function testIndex()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/admin/telephonie/repondeurs');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Affichage de l")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Dernier")')->count());

        //On teste l'ajout (Acces à la page)
        $form = $crawler->selectButton('Ajouter un répondeur')->form();
        $client->submit($form);
    }

    /**
     * [testAjouter description]
     * @return void
     */
    public function testAjouter()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/admin/telephonie/lignes/ajouter');

        //La liste s'affiche
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Nouvelle ligne")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Enregistrer")')->count());

        //On teste l'ajout (Acces à la page)
        $form = $crawler->selectButton('Enregistrer')->form();
        $client->submit($form);
    }

    /**
     * [testEdit description]
     * @return void
     */
    public function testEdit()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/admin/telephonie/repondeurs/edit/1');

        //La liste s'affiche
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Modifier un répondeur")')->count());
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
        $crawler = $client->request('GET', '/admin/telephonie/repondeurs/suppr/1');

        //La liste s'affiche
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Supprimer un répondeur")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Enregistrer")')->count());

        //La ligne existe, un message s'affiche
        //$this->assertGreaterThan(0, $crawler->filter('html:contains("Cette ligne est encore affectée")')->count());

        //On teste la suppression (Acces à la page)
        //$form = $crawler->selectButton('Enregistrer')->form();
        //$client->submit($form);
    }


    /**
     * Test recupération infos
     * @return void
     */
    public function testJsonIndex()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/admin/telephonie/repondeurs/grille');

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
