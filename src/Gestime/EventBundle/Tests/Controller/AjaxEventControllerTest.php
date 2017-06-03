<?php

namespace Gestime\MessageBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
/**
 * Tests des fonctions Ajax associées à l'IHM Agenda
 */
class AjaxEventControllerTest extends WebTestCase
{
    /**
     * @return void
     */
    public function testAjaxEvenement()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/event/1');
        $response = $client->getResponse();
        $position = strpos($response, '1');
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
    public function testAjaxDureeConsultation()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/medecin/duree_rdv/1');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("dureeRdv")')->count());
    }

    protected function createAuthorizedClient()
    {
        $client = static::createClient();
        $cnxMgr = $client->getContainer()->get('gestime.test.manager');

        return $cnxMgr->getAuthenticatedSession($client);
    }
}
