<?php

namespace Gestime\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * MedecinControllerTest
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class MedecinControllerTest extends WebTestCase
{
    /**
     * [testIndex description]
     * @return void
     */
    public function testIndex()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/admin/medecins/');

        //La liste s'affiche
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Affichage de l")')->count());

        //La pagination fonctionne
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Dernier")')->count());

        //On teste l'ajout (Acces à la page)
        $form = $crawler->selectButton('Ajouter un médecin')->form();
        $client->submit($form);
    }

    /**
     * [testAjouter description]
     * @return void
     */
    public function testAjouter()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/admin/medecins/ajouter');

        //La liste s'affiche
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Ajouter un médecin")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Ajouter un téléphone")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Ajouter un horaire")')->count());
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
        $crawler = $client->request('GET', '/admin/medecins/edit/1');

        //La liste s'affiche
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Mettre à jour un médecin")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Ajouter un téléphone")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Ajouter un horaire")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Enregistrer")')->count());

        //On teste l'ajout (Acces à la page)
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
        $crawler = $client->request('GET', '/admin/medecins/suppr/1');

        //La liste s'affiche
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Supprimer un médecin")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Ajouter un téléphone")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Ajouter un horaire")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Enregistrer")')->count());

        //On teste l'ajout (Acces à la page)
        $form = $crawler->selectButton('Enregistrer')->form();
        $client->submit($form);
    }

    protected function createAuthorizedClient()
    {
        $client = static::createClient();
        $cnxMgr = $client->getContainer()->get('gestime.test.manager');

        return $cnxMgr->getAuthenticatedSession($client);
    }
}
