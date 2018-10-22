<?php

namespace AppBundle\Manager;

use AppBundle\Entity\EntryTicket;
use AppBundle\Entity\Purchase;
use AppBundle\Service\dataConverter;
use AppBundle\Service\Payment;
use Doctrine\ORM\EntityManager;
use Swift_Image;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class PurchaseManager
{

    const SESSION_PURCHASE_KEY = 'purchase';
    const BOOKING_CODE_LENGTH = 8;
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var \Twig_Environment
     */
    private $environment;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var Purchase
     */
    private $currentPurchase;
    /**
     * @var Payment
     */
    private $payment;
    /**
     * @var PriceManager
     */
    private $priceManager;


    /**
     * PurchaseManager constructor.
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $environment
     * @param SessionInterface $session
     * @param EntityManager $em
     * @param Payment $payment
     * @param PriceManager $priceManager
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $environment, SessionInterface $session, EntityManager $em, Payment $payment, PriceManager $priceManager)
    {
        $this->mailer = $mailer;
        $this->environment = $environment;
        $this->session = $session;
        $this->em = $em;
        $this->currentPurchase = $this->initPurchase();
        $this->payment = $payment;
        $this->priceManager = $priceManager;
    }

    /**
     *
     */
    private function initPurchase()
    {
        // Check if object already in session
        if ($this->session->has(self::SESSION_PURCHASE_KEY) && $this->session->get(self::SESSION_PURCHASE_KEY) instanceof Purchase) {
            return $this->session->get(self::SESSION_PURCHASE_KEY);
        } else {
            $purchase = new Purchase();
            $this->session->set(self::SESSION_PURCHASE_KEY, $purchase);
            return $purchase;
        }
    }

    /**
     * Generate tickets from attribute's value numberOfTickets of $purchase
     *
     * @return Purchase
     */
    public function generateTickets()
    {
        while (count($this->currentPurchase->getTickets()) !== $this->currentPurchase->getNumberOfTickets()) {
            if (count($this->currentPurchase->getTickets()) < $this->currentPurchase->getNumberOfTickets()) {
                $ticket = new EntryTicket;
                $this->currentPurchase->addTicket($ticket);
            } else {
                $this->currentPurchase->removeTicket($this->currentPurchase->getTickets()->last());
            }
        }
        if ($this->currentPurchase->getStatus() < Purchase::STATUS_STEP_2) {
            $this->currentPurchase->setStatus(Purchase::STATUS_STEP_2);
        }
        return $this->currentPurchase;
    }

    /**
     */
    public function generatePrices()
    {
        $this->priceManager->computePurchasePrice($this->currentPurchase);
        if ($this->currentPurchase->getStatus() < Purchase::STATUS_STEP_3) {
            $this->currentPurchase->setStatus(Purchase::STATUS_STEP_3);
        }
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Exception
     */
    public function doPayment()
    {
        $amount = $this->currentPurchase->getTotal();
        $email = $this->currentPurchase->getEmail();
        $description = 'Purchase payment for '.$email;

        if ($amount == 0 || $this->payment->applyPayment($amount, $description, $email)) {
            $this->buildBookingCode(self::BOOKING_CODE_LENGTH);
            $purchase = $this->getCurrentPurchase();
            $this->storePurchase();
            $this->sendDigitalTicket();
            $this->clearCurrentPurchase();
            return $purchase;
        }
        return false;
    }

    /**
     *
     */
    public function confirmPurchase()
    {
        if ($this->currentPurchase->getStatus() < Purchase::STATUS_STEP_4) {
            $this->currentPurchase->setStatus(Purchase::STATUS_STEP_4);
        }
    }

    /**
     * @throws \Exception
     */
    private function storePurchase()
    {
        $this->em->persist($this->currentPurchase);
        $this->em->flush();
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendDigitalTicket()
    {
        $message = \Swift_Message::newInstance();
        // Embed logo
        $cid = $message->embed(Swift_Image::fromPath('img/logo-louvre-2018.jpg'));

        $htmlContent = $this->environment->render('default/digitalTicket.html.twig', [
            'purchase' => $this->currentPurchase,
            'totalPrice' => $this->currentPurchase->getTotal(),
            'logo' => $cid,
        ]);
        $textContent = DataConverter::stripHTML($htmlContent);

        $message = $message->setContentType('text/html')
            ->setSubject("Vos entrées pour le Musée du Louvre")
            ->setFrom('chrisentemdev-484d0d@inbox.mailtrap.io')
            ->setTo($this->currentPurchase->getEmail())
            ->setBody($htmlContent)
            ->addPart($textContent, 'text/plain');

        $this->mailer->send($message);

    }

    /**
     * @return Purchase
     */
    public function getCurrentPurchase()
    {
        return $this->currentPurchase;
    }

    /**
     *
     */
    private function clearCurrentPurchase()
    {
        $this->session->clear();
    }

    /**
     * @param int $length
     * @return bool|string
     */
    private function buildBookingCode($length)
    {
        $code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
        $this->currentPurchase->setBookingCode($code);
        return $code;
    }

}