<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 03/11/2018
 * Time: 12:07
 */

namespace AppBundle\Manager;

use AppBundle\Entity\ContactEmail;
use AppBundle\Service\MailSender;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManager;

class ContactEmailManager
{
    const SESSION_MAIL_KEY = 'contactEmail';

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
     * @var RequestStack
     */
    private $requestStack;

    /**
     * ContactMailManager constructor.
     * @param MailSender $mailSender
     * @param SessionInterface $session
     * @param EntityManager $em
     * @param RequestStack $requestStack
     */
    public function __construct(MailSender $mailSender,
                                SessionInterface $session,
                                EntityManager $em,
                                RequestStack $requestStack)
    {
        $this->mailSender = $mailSender;
        $this->session = $session;
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    public function initContactEmail()
    {
        // Check if object already in session
        if ($this->session->has(self::SESSION_MAIL_KEY) && $this->session->get(self::SESSION_MAIL_KEY) instanceof ContactEmail) {
            $email =  $this->session->get(self::SESSION_MAIL_KEY);
        } else {
            $email = new ContactEmail();
            $this->session->set(self::SESSION_MAIL_KEY, $email);
        }
        return $email;
    }

    /**
     * @param ContactEmail $contactEmail
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function send(ContactEmail $contactEmail)
    {
        try {
            $request = $this->requestStack->getCurrentRequest();
            $ip = $request->getClientIp();
            $contactEmail->setIp($ip);
            $this->storeContactEmail($contactEmail);
        } catch (\Exception $e) {

            // Notify webmaster that storage failed
            $this->mailSender->setMailBody(['contactEmail' => $contactEmail, 'error' => $e->getMessage()],
                'emails/contactEmailFailureNotification.html.twig');
            $this->mailSender->sendMail('webmaster@museedulouvre.fr',
                'billetterie@museedulouvre.fr',
                'Online ticketing - Contact Email problem');
        }
        $this->mailSender->setMailBody(['contactEmail' => $contactEmail],
            'emails/contactEmail.html.twig');

        $this->mailSender->sendMail('infos@museedulouvre.fr',
            $contactEmail->getEmail(),
            $contactEmail->getSubject());
        $this->clearCurrentContactEmail();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getCurrentContactEmail()
    {
        if(!$this->session->has(self::SESSION_MAIL_KEY) ){
            throw new Exception('There\'s no Contact Email in session !');
        }
        $currentContactEmail = $this->session->get(self::SESSION_MAIL_KEY);
        if (!$currentContactEmail  instanceof ContactEmail) {
            throw new Exception('Mail found is not a Contact Email !');
        }
        return $currentContactEmail;
    }

    /**
     * @param ContactEmail $contactEmail
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function storeContactEmail(ContactEmail $contactEmail)
    {
        $this->em->persist($contactEmail);
        $this->em->flush();
    }

    /**
     *
     */
    private function clearCurrentContactEmail()
    {
        $this->session->clear();
    }
}