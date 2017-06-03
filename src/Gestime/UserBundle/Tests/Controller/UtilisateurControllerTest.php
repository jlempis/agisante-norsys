<?php

namespace Gestime\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * UtilisateurControllerTest
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class UtilisateurControllerTest extends WebTestCase
{
    /**
     * [testIndex description]
     * @return void
     */
    public function testIndex()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/admin/utilisateurs/');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Affichage de l")')->count());

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Dernier")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Ajouter un utilisateur")')->count());

        //On teste l'ajout (Acces à la page)
        $form = $crawler->selectButton('Ajouter un utilisateur')->form();
        $client->submit($form);
    }

    /**
     * [testAjouter description]
     * @return void
     */
    public function testAjouter()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/admin/utilisateurs/ajouter');

        //La liste s'affiche
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Nouvel utilisateur")')->count());
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
        $crawler = $client->request('GET', '/admin/utilisateurs/edit/1');

        //La liste s'affiche
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Modifier un utilisateur")')->count());
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
        $crawler = $client->request('GET', '/admin/utilisateurs/suppr/1');

        //La liste s'affiche
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Supprimer un utilisateur")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Enregistrer")')->count());

        //On teste la suppression (Acces à la page)
        //$form = $crawler->selectButton('Enregistrer')->form();
        //$client->submit($form);
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
