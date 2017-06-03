<?php

namespace Gestime\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * AbonneControllerTest
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class AbonneControllerTest extends WebTestCase
{
    /**
     * [testIndex description]
     * @return void
     */
    public function testIndex()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/admin/abonnes/');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Affichage de l")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Dernier")')->count());

        //On teste l'ajout (Acces à la page)
        $form = $crawler->selectButton('Ajouter un abonné')->form();
        $client->submit($form);
    }

    /**
     * [testAjouter description]
     * @return void
     */
    public function testAjouter()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/admin/abonnes/ajouter');

        //La liste s'affiche
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Ajouter un abonné")')->count());
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
        $crawler = $client->request('GET', '/admin/abonnes/edit/1');

        //La liste s'affiche
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Modifier un abonné")')->count());
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
        $crawler = $client->request('GET', '/admin/abonnes/suppr/1');

        //La liste s'affiche
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Supprimer un abonné")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Enregistrer")')->count());

        //L'abonné existe, un message s'affiche
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Cet abonné est encore relié à des médecins")')->count());

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
