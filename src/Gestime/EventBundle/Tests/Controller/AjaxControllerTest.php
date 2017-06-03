<?php

namespace Gestime\EventBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests des fonctions Ajax associées à l'IHM Agenda
 */
class AjaxControllerTest extends WebTestCase
{
    /**
     * @return void
     */
    public function testAjaxNomEntreprise()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/personnes/entreprise');
        $response = $client->getResponse();
        $position = strpos($response, 'ItemType');
        $this->assertGreaterThan(0, $position);

    }

    /**
     * @return void
     */
    public function testAjaxNomPatient()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/personnes/patients');
        $response = $client->getResponse();
        $position = strpos($response, 'Telephone');
        $this->assertGreaterThan(0, $position);
    }

    /**
     * @return void
     */
    public function testAjaxGetVillesByCpostal()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/villes/62840');
        $response = $client->getResponse();
        $position = strpos($response, 'Laventie');
        $this->assertGreaterThan(0, $position);
    }

    /**
     * @return void
     */
    public function testAjaxGetAnnotations()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/admin/horaires/1');
        $response = $client->getResponse();
        $position = strpos($response, 'activite');
        $this->assertGreaterThan(0, $position);
    }

    /**
     * @return void
     */
    public function testAjaxGetAbsencesMedecin()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/admin/absence/medecin/1');
        $position=1;
        $this->assertGreaterThan(0, $position);
    }

    protected function createAuthorizedClient()
    {
        $client = static::createClient();
        $cnxMgr = $client->getContainer()->get('gestime.test.manager');

        return $cnxMgr->getAuthenticatedSession($client);
    }
}
