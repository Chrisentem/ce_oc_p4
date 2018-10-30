<?php

namespace AppBundle\Manager;

use AppBundle\Entity\EntryTicket;
use AppBundle\Entity\Purchase;
use AppBundle\Exceptions\NoCurrentPurchaseException;
use AppBundle\Exceptions\NoMatchingPurchaseFoundException;
use AppBundle\Exceptions\NotAPurchaseException;
use AppBundle\Service\MailSender;
use AppBundle\Service\Payment;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig_Environment;

class PurchaseManager
{
    const SESSION_PURCHASE_KEY = 'purchase';
    const BOOKING_CODE_LENGTH = 8;

    /**
     * @var MailSender
     */
    private $mailSender;
    /**
     * @var Twig_Environment
     */
    private $twig;
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
     * @param MailSender $mailSender
     * @param Twig_Environment $twig
     * @param SessionInterface $session
     * @param EntityManager $em
     * @param Payment $payment
     * @param PriceManager $priceManager
     */
    public function __construct(MailSender $mailSender,
                                Twig_Environment $twig,
                                SessionInterface $session,
                                EntityManager $em,
                                Payment $payment,
                                PriceManager $priceManager)
    {
        $this->mailSender = $mailSender;
        $this->twig = $twig;
        $this->session = $session;
        $this->em = $em;
        $this->payment = $payment;
        $this->priceManager = $priceManager;
    }

    /**
     *
     */
    public function initPurchase()
    {
        // Check if object already in session
        if ($this->session->has(self::SESSION_PURCHASE_KEY) && $this->session->get(self::SESSION_PURCHASE_KEY) instanceof Purchase) {
            $purchase =  $this->session->get(self::SESSION_PURCHASE_KEY);
        } else {
            $purchase = new Purchase();
            $this->session->set(self::SESSION_PURCHASE_KEY, $purchase);
        }
        $this->currentPurchase = $purchase;
        return $purchase;
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
        if ($this->currentPurchase->getStatus() == Purchase::STATUS_STEP_1) {
            $this->currentPurchase->setStatus(Purchase::STATUS_STEP_2);
        }
        return $this->currentPurchase;
    }

    /**
     */
    public function generatePrices()
    {
        $this->priceManager->computePurchasePrice($this->currentPurchase);
        if ($this->currentPurchase->getStatus() == Purchase::STATUS_STEP_2) {
            $this->currentPurchase->setStatus(Purchase::STATUS_STEP_3);
        }
    }

    /**
     * @return Purchase|bool
     * @throws NoCurrentPurchaseException
     * @throws NoMatchingPurchaseFoundException
     * @throws NotAPurchaseException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function doPayment()
    {
        $purchase = $this->getCurrentPurchase(Purchase::STATUS_STEP_4);
        $amount = $purchase->getTotal();
        $email = $purchase->getEmail();
        $description = 'Purchase payment for '.$email;

        if ($amount == 0 || $this->payment->applyPayment($amount, $description, $email)) {
            $this->buildBookingCode(self::BOOKING_CODE_LENGTH);
            $purchase = $this->getCurrentPurchase(Purchase::STATUS_STEP_4);
            try{
                $this->storePurchase();
            }catch(\Exception $e){
                // Notify webmaster that storage failed
                $this->mailSender->setMailBody(['purchase' => $purchase, 'error' => $e->getMessage()],
                    'emails/purchaseFailureNotification.html.twig');
                $this->mailSender->sendMail('webmaster@museedulouvre.fr', 'billetterie@museedulouvre.fr',
                    'Online ticketing problem');
            }
            // If purchase payment is successful we send the tickets anyway even if server fails storage
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
        if ($this->currentPurchase->getStatus() == Purchase::STATUS_STEP_3) {
            $this->currentPurchase->setStatus(Purchase::STATUS_STEP_4);
        }
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
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
    private function sendDigitalTicket()
    {
        $cid = $this->mailSender->addEmbedImage('img/logo-louvre-2018.jpg');
        // Generating content with twig template and data
        $this->mailSender->setMailBody(['purchase' => $this->currentPurchase, 'logo' => $cid],
            'emails/digitalTicket.html.twig');

        $this->mailSender->sendMail($this->currentPurchase->getEmail(), 'billetterie@museedulouvre.fr',
            'Vos entrées pour le Musée du Louvre');
    }

    /**
     * @param bool $status
     * @return Purchase
     * @throws NoCurrentPurchaseException
     * @throws NoMatchingPurchaseFoundException
     * @throws NotAPurchaseException
     */
    public function getCurrentPurchase($status)
    {
        if(!$this->session->has(self::SESSION_PURCHASE_KEY) ){
            throw new NoCurrentPurchaseException('There\'s no Purchase in session !');
        }
        $currentPurchase = $this->session->get(self::SESSION_PURCHASE_KEY);
        if (!$currentPurchase  instanceof Purchase) {
            throw new NotAPurchaseException('Purchase found is not a Purchase !');
        }
        if ($currentPurchase->getStatus() < $status) {
            throw new NoMatchingPurchaseFoundException('Purchase found does not match requirement');
        }
        $this->currentPurchase = $currentPurchase;
        return $currentPurchase;
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