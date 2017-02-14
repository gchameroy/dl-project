<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\Company;
use ApiBundle\Form\Type\CompanyType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends FOSRestController
{
    /**
     * @Rest\View(serializerGroups={"company2"})
     */
    public function getCompaniesAction()
    {
        $companies = $this->getDoctrine()->getManager()
            ->getRepository('ApiBundle:Company')
            ->findAll();

        return $companies;
    }

    /**
     * @Rest\View(serializerGroups={"company"}, statusCode=Response::HTTP_CREATED)
     */
    public function postCompaniesAction(Request $request)
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
            $em->flush();
            
            return $company;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerGroups={"company"})
     */
    public function getCompanyAction($id)
    {
        $company = $this->get('doctrine.orm.entity_manager')
            ->getRepository('ApiBundle:Company')
            ->find($id);

        if (!$company) {
            return $this->companyNotFound();
        }

        return $company;
    }
    
    /**
     * @Rest\View(serializerGroups={"company"})
     */
    public function putCompanyAction($id, Request $request)
    {
        return $this->updateCompany($id, $request, true);
    }

    /**
     * @Rest\View(serializerGroups={"company"})
     */
    public function patchCompanyAction($id, Request $request)
    {
        return $this->updateCompany($id, $request, false);
    }
    
    private function updateCompany($id, Request $request, $clearMissing)
    {
        $company = $this->getDoctrine()->getManager()
                ->getRepository('ApiBundle:Company')
                ->find($id);

        if (!$company) {
            return $this->companyNotFound();
        }
        
        $form = $this->createForm(CompanyType::class, $company);
        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->merge($company);
            $em->flush();
            
            return $company;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerGroups={"company"}, statusCode=Response::HTTP_NO_CONTENT)
     */
    public function deleteCompanyAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $company = $em
            ->getRepository('ApiBundle:Company')
            ->find($id);

        if (!$company) {
            return;
        }

        $em->remove($company);
        $em->flush();
    }

    private function companyNotFound()
    {
        return View::create(['message' => 'Compny not found'], Response::HTTP_NOT_FOUND);
    }
}
