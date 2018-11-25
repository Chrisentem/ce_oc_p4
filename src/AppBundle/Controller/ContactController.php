<?php
/**
 * Created by PhpStorm.
 * User: Chris
 */

namespace AppBundle\Controller;

use AppBundle\Form\ContactEmailType;
use AppBundle\Manager\ContactEmailManager;
use AppBundle\Service\ReCaptchaVerify;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ContactController
 * @package AppBundle\Controller
 * @Route("/{_locale}")
 */
class ContactController extends Controller
{
    /**
     * @Route("/contact", name="contact")
     * @param Request $request
     * @param ContactEmailManager $contactEmailManager
     * @param ReCaptchaVerify $reCaptchaVerify
     * @return Response
     */
    public function contactAction(Request $request, ContactEmailManager $contactEmailManager, ReCaptchaVerify $reCaptchaVerify)
    {
        $email = $contactEmailManager->initContactEmail();

        $form = $this->createform(ContactEmailType::class, $email);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $res = $reCaptchaVerify->verify($request->get('g-recaptcha-response'));
            if(!$res) {
                $this->addFlash(
                    'error',
                    'Captcha required'
                );
            }else {
                try {
                    $contactEmailManager->send($email);
                    $this->addFlash('success', 'sending successful !');
                    return $this->render('default/contact-confirmation.html.twig', [
                        'contactEmail' => $email,
                    ]);
                } catch (\Exception $e) {
                    $this->addFlash('warning', 'sending failed !');
                }
            }
        }
        return $this->render('default/contact.html.twig', ['contactForm' => $form->createView(),]);
    }
}
