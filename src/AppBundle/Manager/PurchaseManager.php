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

class PurchaseManager
{
    const SESSION_PURCHASE_KEY = 'purchase';
    const BOOKING_CODE_LENGTH = 8;

    /**
     * @var MailSender
     */
    private $mailSender;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var EntityManager
     */
    private $em;
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
     * @param SessionInterface $session
     * @param EntityManager $em
     * @param Payment $payment
     * @param PriceManager $priceManager
     */
    public function __construct(MailSender $mailSender,
                                SessionInterface $session,
                                EntityManager $em,
                                Payment $payment,
                                PriceManager $priceManager)
    {
        $this->mailSender = $mailSender;
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
        return $purchase;
    }

    /**
     * Generate tickets from attribute's value numberOfTickets of $purchase
     *
     * @param Purchase $purchase
     * @return Purchase
     */
    public function generateTickets(Purchase $purchase)
    {
        while (count($purchase->getTickets()) !== $purchase->getNumberOfTickets()) {
            if (count($purchase->getTickets()) < $purchase->getNumberOfTickets()) {
                $ticket = new EntryTicket;
                $purchase->addTicket($ticket);
            } else {
                $purchase->removeTicket($purchase->getTickets()->last());
            }
        }
        if ($purchase->getStatus() == Purchase::STATUS_STEP_1) {
            $purchase->setStatus(Purchase::STATUS_STEP_2);
        }
        return $purchase;
    }

    /**
     * @param Purchase $purchase
     */
    public function generatePrices(Purchase $purchase)
    {
        $this->priceManager->computePurchasePrice($purchase);
        if ($purchase->getStatus() == Purchase::STATUS_STEP_2) {
            $purchase->setStatus(Purchase::STATUS_STEP_3);
        }
    }

    /**
     * @param Purchase $purchase
     * @return Purchase|bool
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function doPayment(Purchase $purchase)
    {
        $amount = $purchase->getTotal();
        $email = $purchase->getEmail();
        $description = 'Purchase payment for ' . $email;

        if ($amount == 0 || $this->payment->applyPayment($amount, $description, $email)) {
            $purchase->setBookingCode($this->buildBookingCode(self::BOOKING_CODE_LENGTH));
            try {
                $this->storePurchase($purchase);
            } catch (\Exception $e) {
                // Notify webmaster that storage failed
                $this->mailSender->setMailBody(['purchase' => $purchase, 'error' => $e->getMessage()],
                    'emails/purchaseFailureNotification.html.twig');
                $this->mailSender->sendMail('webmaster@museedulouvre.fr',
                    'billetterie@museedulouvre.fr',
                    'Online ticketing problem');
            }
            // If purchase payment is successful we send the tickets anyway even if server fails storage
            $this->sendDigitalTicket($purchase);
            $this->clearCurrentPurchase();
            return $purchase;
        }
        return false;
    }

    /**
     * @param Purchase $purchase
     */
    public function confirmPurchase(Purchase $purchase)
    {
        if ($purchase->getStatus() == Purchase::STATUS_STEP_3) {
            $purchase->setStatus(Purchase::STATUS_STEP_4);
        }
    }

    /**
     * @param Purchase $purchase
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function storePurchase(Purchase $purchase)
    {
        $this->em->persist($purchase);
        $this->em->flush();
    }

    /**
     * @param Purchase $purchase
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function sendDigitalTicket(Purchase $purchase)
    {
        $cid = $this->mailSender->addEmbedImage('img/logo-louvre-2018.jpg');
        // Generating content with twig template and data
        $this->mailSender->setMailBody(['purchase' => $purchase, 'logo' => $cid],
            'emails/digitalTicket.html.twig');

        $this->mailSender->sendMail($purchase->getEmail(), 'billetterie@museedulouvre.fr',
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
        return $currentPurchase;
    }

    /**
     *
     */
    private function clearCurrentPurchase()
    {
        $this->session->remove(self::SESSION_PURCHASE_KEY);
    }

    /**
     * @param int $length
     * @return bool|string
     */
    private function buildBookingCode($length)
    {
        $code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
        return $code;
    }

}