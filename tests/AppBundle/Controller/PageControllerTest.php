<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllerTest extends WebTestCase
{
    /**
     * Testing Sales Terms page
     */
    public function testSalesTerms()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/fr/general-sales-terms-and-conditions');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Conditions Générales de Vente', $crawler->text());
    }

    /**
     * Testing Legal Notice page
     */
    public function testLegalNotice()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/fr/legal-notice');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('mentions légales', $crawler->text());
    }

    /**
     * Test pages reachable
     * @dataProvider urlProviderSuccessful
     * @param $url
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * Provides list of successful pages
     * @return \Generator
     */
    public function urlProviderSuccessful()
    {
        yield ['/fr/legal-notice'];
        yield ['/fr/general-sales-terms-and-conditions'];
        yield ['/fr/contact'];
    }

    /**
     * Test pages Not Found
     * @dataProvider urlProviderNotFound
     * @param $url
     */
    public function testNotFoundPages($url)
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isNotFound());
    }

    /**
     * Provides list of unreachable pages
     * @return array
     */
    public function urlProviderNotFound()
    {
        return [
            ['/fr/order-step-one'],
            ['/fr/order-step-two'],
            ['/fr/homepage'],
            ['/fr/faq'],
            ];
    }

}
