<?php

namespace Gestime\MessageBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
/**
 * Tests des fonctions Ajax associées à l'IHM
 */
class MessageControllerTest extends WebTestCase
{
    /**
     * Test affichage de la liste des messages
     * @return void
     */
    public function testListe()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/messages/liste');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Boite de réception")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Catégories")')->count());

        //On teste l'ajout (Acces à la page)
        $form = $crawler->selectButton('Rechercher')->form();
        $client->submit($form);
    }

    /**
     * Test affichage d'un message
     * @return void
     */
    public function testView()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/admin/telephonie/fermetures/ajouter');

        //La liste s'affiche
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Ajouter une période de fermeture")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Enregistrer")')->count());

        //On teste l'ajout (Acces à la page)
        $form = $crawler->selectButton('Enregistrer')->form();
        $client->submit($form);
    }

    /**
     * Test de réponse à un message
     * @return void
     */
    public function testResponse()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/admin/telephonie/fermetures/edit/1');

        //La liste s'affiche
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Modifier une période de fermeture")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Enregistrer")')->count());

        //On teste l'ajout (Acces à la page)
        $form = $crawler->selectButton('Enregistrer')->form();
        $client->submit($form);
    }

    /**
     * Test d'écriture d'un nouveau message
     * @return void
     */
    public function testNew()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/messages/compose');

        //La liste s'affiche
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Nouveau message")')->count());

        //On teste l'ajout (Acces à la page)
        //$form = $crawler->selectButton('Enregistrer')->form();
        //$client->submit($form);
    }

    protected function createAuthorizedClient()
    {
        $client = static::createClient();
        $cnxMgr = $client->getContainer()->get('gestime.test.manager');

        return $cnxMgr->getAuthenticatedSession($client);
    }
}
