<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Purchase;
use AppBundle\Form\PurchaseType;

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
}
