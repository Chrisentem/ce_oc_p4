<?php
/**
 * Created by PhpStorm.
 * User: Chris
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PageController
 * @package AppBundle\Controller
 * @Route("/{_locale}")
 */
class PageController extends Controller
{
    /**
     * @Route("/general-sales-terms-and-conditions", name="sales_terms")
     */
    public function gtcAction()
    {
        return $this->render('default/general-sales-terms.html.twig');
    }

    /**
     * @Route("/legal-notice", name="legal_notice")
     */
    public function legalNoticeAction()
    {
        return $this->render('default/legal-notice.html.twig');
    }
}
