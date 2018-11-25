<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PurchaseControllerTest extends WebTestCase
{
    /**
     * Testing index page
     */
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/fr/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Acheter des billets', $crawler->text());
    }

    /**
     * Testing if order-step-2 page is loaded straight
     * If so, there's no Purchase stored in session yet and
     * a NoCurrentPurchaseException is thrown
     */
    public function testOrderStepTwo()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/fr/order-step-2');

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $this->assertContains('There\'s no Purchase in session', $crawler->text());
    }
    /**
     * Testing if order-step-3 page is loaded straight
     * If so, there's no Purchase stored in session yet and
     * a NoCurrentPurchaseException is thrown
     */
    public function testOrderStepThree()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/fr/order-step-3');

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $this->assertContains('There\'s no Purchase in session', $crawler->text());
    }
    /**
     * Testing if checkout page is loaded straight
     * If so, there's no Purchase stored in session yet and
     * a NoCurrentPurchaseException is thrown
     */
    public function testCheckout()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/fr/checkout');

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $this->assertContains('There\'s no Purchase in session', $crawler->text());
    }

    // Testing Purchase workflow through several forms and steps
    public function testPurchaseWorkFlow()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/fr/');
        $purchaseForm = $crawler->selectButton('étape suivante')->form();
        // Use without js-datepicker
        //$purchaseForm['appbundle_purchase[dateOfVisit]'] = ['day' => '4','month' => '9','year' => '2019'];
        // Use with js-datepicker
        $purchaseForm['appbundle_purchase[dateOfVisit]'] = '2019-09-04';
        $purchaseForm['appbundle_purchase[ticketType]'] = 1;
        $purchaseForm['appbundle_purchase[numberOfTickets]'] = 2;
        $client->submit($purchaseForm);
        $this->assertTrue($client->getResponse()->isRedirect('/fr/order-step-2'));
        $crawler = $client->followRedirect();
        $this->assertSame(1, $crawler->filter('html:contains("enregistrement des visiteurs")')->count());
        $formTickets = $crawler->selectButton('étape suivante')->form();
        $formTickets['multi_ticket[tickets][0][firstname]'] = 'John';
        $formTickets['multi_ticket[tickets][0][lastname]'] = 'Doe';
        $formTickets['multi_ticket[tickets][0][birthdate]'] = ['day' => '2','month' => '10','year' => '1977'];
        $formTickets['multi_ticket[tickets][0][country]'] = 'FR';
        $formTickets['multi_ticket[tickets][1][firstname]'] = 'Jane';
        $formTickets['multi_ticket[tickets][1][lastname]'] = 'Doe';
        $formTickets['multi_ticket[tickets][1][birthdate]'] = ['day' => '21','month' => '9','year' => '1987'];
        $formTickets['multi_ticket[tickets][1][country]'] = 'FR';
        $formTickets['multi_ticket[tickets][1][discounted]'] = 1;
        $client->submit($formTickets);
        $this->assertTrue($client->getResponse()->isRedirect('/fr/order-step-3'));
        $crawler = $client->followRedirect();
        $this->assertSame(1, $crawler->filter('html:contains("récapitulatif de commande")')->count());
        $formConfirm = $crawler->selectButton('confirmer la commande')->form();
        $formConfirm['appbundle_purchase[email]'] = ['first' => 'john.doe@doe.com','second' => 'john.doe@doe.com'];
		$formConfirm['appbundle_purchase[agree]'] = 1;
        $client->submit($formConfirm);
        $this->assertTrue($client->getResponse()->isRedirect('/fr/checkout'));
        $crawler = $client->followRedirect();
        $this->assertSame(1, $crawler->filter('html:contains("paiement")')->count());
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
