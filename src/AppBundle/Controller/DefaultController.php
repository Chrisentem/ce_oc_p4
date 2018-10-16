<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Purchase;
use AppBundle\Form\PurchaseType;

use AppBundle\Entity\EntryTicket;
use AppBundle\Form\MultiTicketType;
use AppBundle\Form\PurchaseConfirmType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Manager\PriceManager;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request, session $session)
    {
        // Check if object already in session
        if ($session->has('purchase') && $session->get('purchase') instanceof Purchase) {
            $purchase = $session->get('purchase');
        } else {
            // instanciate new Purchase object to fill with form
            $purchase = new Purchase;
        }

        // Building form based on Purchase entity
        $form = $this->createform(PurchaseType::class, $purchase);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            while (count($purchase->getTickets()) !== $purchase->getNumberOfTickets()) {
                    if (count($purchase->getTickets()) < $purchase->getNumberOfTickets()) {
                            $ticket = new EntryTicket;
                            $purchase->addTicket($ticket);
                    } else {
                        $purchase->removeTicket($purchase->getTickets()->last());
                    }
            }

            $session->set('purchase', $purchase);

            // Redirect to step 2
            return $this->redirectToRoute('order_step_2');
        }
        return $this->render('default/index.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/order-step-2", name="order_step_2")
     */
    public function fillTicketsAction(Request $request, Session $session, PriceManager $priceManager)
    {
        if ($session->has('purchase') && $session->get('purchase') instanceof Purchase) {
            $purchase = $session->get('purchase');

            // Building form based on Purchase entity with multi ticket entries
            $form = $this->createform(MultiTicketType::class, $purchase);
            
            $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) {
                $priceManager->computePurchasePrice($purchase);        
                // Redirect to step 3
                return $this->redirectToRoute('order_step_3');
            }
            return $this->render('default/order-step-2.html.twig', [
                'form' => $form->createView(),
                ]);
        }
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/order-step-3", name="order_step_3")
     */
    public function confirmAction(Request $request, Session $session)
    {
        if ($session->has('purchase') && $session->get('purchase') instanceof Purchase) {
            $purchase = $session->get('purchase');
            $visitors = $purchase->getTickets();
            $total = $purchase->getTotal();

            // Building form based on Purchase entity with multi ticket entries
            $form = $this->createform(PurchaseConfirmType::class, $purchase);
            
            $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) {
                        
                // Redirect to step 4
                return $this->redirectToRoute('order_step_4');
            }
            return $this->render('default/order-step-3.html.twig', [
                'form' => $form->createView(),
                'visitors' => $visitors,
                'totalPrice' => $total,
                ]);    
        }
        return $this->redirectToRoute('homepage');
    /**
     * @Route("/cgv", name="cgv")
     */
    public function cgvAction() {

        return $this->render('default/cgv.html.twig');
    }

    /**
    }
}
