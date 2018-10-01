<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Purchase;
use AppBundle\Form\PurchaseType;

use AppBundle\Entity\EntryTicket;
use AppBundle\Form\MultiTicketType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // instanciate new Purchase object to fill with form
        $purchase = new Purchase;
        // Building form based on Purchase entity
        $form = $this->createform(PurchaseType::class, $purchase);
        
        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isValid() && $form->isSubmitted()) {

                $session = new Session();
                $session->set('purchase', $purchase);
      
                // Redirect to step 2
                return $this->redirectToRoute('order_step_2');
            }
            return $this->render('default/index.html.twig', [
                'form' => $form->createView(),
                ]);
        }        
        
        return $this->render('default/index.html.twig', [
            'form' => $form->createView(),
            ]);
    }

    /**
     * @Route("/order-step-2", name="order_step_2")
     */
    public function fillTicketsAction(Request $request, Session $session)
    {
        $purchase = $session->get('purchase');
        // $chosenDate = $purchase->getDateOfVisit();
        $numVisitors = $purchase->getNumberOfTickets();

        // Check if there's allready ticket instances in session
        $ticketsCount = count($purchase->getTickets());
        if($ticketsCount == 0) {
            for($i = 1; $i <= $numVisitors; $i++) {
                $ticket = new EntryTicket;
                $purchase->addTicket($ticket);
            }
        }

        // Building form based on Purchase entity with multi ticket entries
        $form = $this->createform(MultiTicketType::class, $purchase);
        
        if ($request->isMethod('POST')) {

            $form->handleRequest($request);
    
            if ($form->isValid() && $form->isSubmitted()) {
                     
                $_SESSION['purchase'] = $purchase;

                // Redirect to step 3
                return $this->redirectToRoute('order_step_3');
            }
            return $this->render('default/order-step-2.html.twig', [
                'form' => $form->createView(),
                ]);    
        }        
        return $this->render('default/order-step-2.html.twig', [
            'form' => $form->createView(),
            ]);
    }
}
