<?php
/**
 * Created by PhpStorm.
 * User: Chris
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Purchase;
use AppBundle\Form\PurchaseType;
use AppBundle\Form\MultiTicketType;
use AppBundle\Form\PurchaseConfirmType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Manager\PurchaseManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PurchaseController
 * @package AppBundle\Controller
 * @Route("/{_locale}")
 */
class PurchaseController extends Controller
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
                $this->addFlash('success',
                    $this->get('translator')->trans('flash.message.order_success'));
                return $this->render('default/confirmation.html.twig', [
                    'purchase' => $purchase,
                ]);
            } catch (\Exception $e) {
                $this->addFlash('warning',
                    $this->get('translator')->trans('flash.message.payment_failed'));
            }
        }
        return $this->render('default/order-step-4.html.twig', [
            'purchase' => $purchase,
        ]);
    }
}
