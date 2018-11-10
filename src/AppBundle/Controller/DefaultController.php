<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Purchase;
use AppBundle\Form\ContactEmailType;
use AppBundle\Form\PurchaseType;
use AppBundle\Form\MultiTicketType;
use AppBundle\Form\PurchaseConfirmType;
use AppBundle\Manager\ContactEmailManager;
use AppBundle\Service\ReCaptchaVerify;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Manager\PurchaseManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @param PurchaseManager $purchaseManager
     * @return RedirectResponse|Response
     */
    public function indexAction(Request $request, PurchaseManager $purchaseManager)
    {
        $purchase = $purchaseManager->initPurchase();
        // Building form based on Purchase entity
        $form = $this->createform(PurchaseType::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchaseManager->generateTickets($purchase);
            return $this->redirectToRoute('order_step_2');
        }
        return $this->render('default/index.html.twig', [
            'form' => $form->createView(),
            'purchase' => $purchase,
        ]);
    }

    /**
     * @Route("/order-step-2", name="order_step_2")
     * @param Request $request
     * @param PurchaseManager $purchaseManager
     * @return RedirectResponse|Response
     * @throws \AppBundle\Exceptions\NoCurrentPurchaseException
     * @throws \AppBundle\Exceptions\NoMatchingPurchaseFoundException
     * @throws \AppBundle\Exceptions\NotAPurchaseException
     */
    public function fillTicketsAction(Request $request, PurchaseManager $purchaseManager)
    {
        $purchase = $purchaseManager->getCurrentPurchase(Purchase::STATUS_STEP_2);

        // Building form based on Purchase entity with multi ticket entries
        $form = $this->createform(MultiTicketType::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchaseManager->generatePrices($purchase);
            return $this->redirectToRoute('order_step_3');
        }
        return $this->render('default/order-step-2.html.twig', [
            'form' => $form->createView(),
            'purchase' => $purchase,
        ]);
    }

    /**
     * @Route("/order-step-3", name="order_step_3")
     * @param Request $request
     * @param PurchaseManager $purchaseManager
     * @return RedirectResponse|Response
     * @throws \AppBundle\Exceptions\NoCurrentPurchaseException
     * @throws \AppBundle\Exceptions\NoMatchingPurchaseFoundException
     * @throws \AppBundle\Exceptions\NotAPurchaseException
     */
    public function confirmAction(Request $request, PurchaseManager $purchaseManager)
    {
        $purchase = $purchaseManager->getCurrentPurchase(Purchase::STATUS_STEP_3);

        // Building form based on Purchase entity with multi ticket entries
        $form = $this->createform(PurchaseConfirmType::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchaseManager->confirmPurchase($purchase);
            return $this->redirectToRoute('order_step_4');
        }
        return $this->render('default/order-step-3.html.twig', [
            'form' => $form->createView(),
            'purchase' => $purchase,
        ]);
    }

    /**
     * @Route("/checkout", name="order_step_4")
     * @param Request $request
     * @param PurchaseManager $purchaseManager
     * @return Response
     * @throws \AppBundle\Exceptions\NoCurrentPurchaseException
     * @throws \AppBundle\Exceptions\NoMatchingPurchaseFoundException
     * @throws \AppBundle\Exceptions\NotAPurchaseException
     */
    public function checkoutAction(Request $request, PurchaseManager $purchaseManager)
    {
        $purchase = $purchaseManager->getCurrentPurchase(Purchase::STATUS_STEP_4);

        if ($request->isMethod('POST')) {
            try {
                $purchaseManager->doPayment($purchase);
                $this->addFlash('success', 'Order Complete !');
                return $this->render('default/confirmation.html.twig', [
                    'purchase' => $purchase,
                ]);
            } catch (\Exception $e) {
                $this->addFlash('warning', 'Payment failed !');
            }
        }
        return $this->render('default/order-step-4.html.twig', [
            'purchase' => $purchase,
        ]);
    }

    /**
     * @Route("/cgv", name="cgv")
     */
    public function cgvAction()
    {
        return $this->render('default/cgv.html.twig');
    }

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

    /**
     * @Route("/legal-notice", name="legal_notice")
     */
    public function legalNoticeAction()
    {
        return $this->render('default/legal-notice.html.twig');
    }

}
