<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/yoloo")
     */
    public function indexAction()
    {
        $companies = $this->get('app.rest-client')
            ->generateFromRoute('GET', 'get_companies');

        return $this->render('AppBundle:Default:index.html.twig', [
            'companies' => $companies
        ]);
    }
}
