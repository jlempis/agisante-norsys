<?php

namespace Gestime\RapportsBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * SmsControllerTest
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class SmsControllerTest extends WebTestCase
{
    /**
     * Test d'affichage de la liste des non excuses
     * @return void
     */
    public function testIndex()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/rapports/sms');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Affichage de l")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Dernier")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Gestion des Sms")')->count());

        //On teste l'ajout (Acces à la page)
        $form = $crawler->selectButton('Filtrer et afficher')->form();
        $client->submit($form);
    }

    /**
     * Test recupération infos
     * @return void
     */
    public function testJsonIndex()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/rapports/sms/grille/0/2014-12-17/2014-12-17');

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
