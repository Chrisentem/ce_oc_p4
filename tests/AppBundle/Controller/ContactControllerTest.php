<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    /**
     * Testing Contact page
     */
    public function testContact()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/fr/contact');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Formulaire de contact', $crawler->text());
    }

}
