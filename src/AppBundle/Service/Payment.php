<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 09/10/2018
 * Time: 11:03
 */

namespace AppBundle\Service;

use Stripe\Charge;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class Payment
{
    /**
     * @var string
     */
    private $stripePrivateKey;
    /**
     * @var Stripe
     */
    private $stripe;
    /**
     * @var Charge
     */
    private $charge;
    /**
     * @var null|Request
     */
    private $request;

    /**
     * Payment constructor.
     * @param $stripePrivateKey
     * @param RequestStack $requestStack
     * @param Stripe $stripe
     * @param Charge $charge
     */
    public function __construct($stripePrivateKey, RequestStack $requestStack, Stripe $stripe, Charge $charge)
    {
        $this->stripePrivateKey = $stripePrivateKey;
        $this->stripe = $stripe;
        $this->charge = $charge;
        $this->request = $requestStack->getCurrentRequest();
    }


    /**
     * @param $amount
     * @param $description
     * @param $email
     * @return bool
     */
    public function applyPayment($amount, $description, $email)
    {
        try {
            $this->stripe->setApiKey($this->stripePrivateKey);
            $this->charge->create(array(
                "amount" => $amount * 100,
                "currency" => "eur",
                "source" => $this->request->get('stripeToken'),
                "receipt_email" => $email,
                "description" => $description,
            ));
        } catch (\Exception $e) {
            return false;
        }
       return true;
    }
}