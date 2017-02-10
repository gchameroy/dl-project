<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\Contact;
use ApiBundle\Form\Type\ContactType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends FOSRestController
{
    /**
     * @Rest\View(serializerGroups={"contact"})
     */
    public function getCompanyContactsAction($id)
    {
        $company = $this->get('doctrine.orm.entity_manager')
            ->getRepository('ApiBundle:Company')
            ->find($id);

        if (!$company) {
            return $this->companyNotFound();
        }

        $contacts = $this->get('doctrine.orm.entity_manager')
            ->getRepository('ApiBundle:Contact')
            ->findByCompany($company);

        return $contacts;
    }
    
    /**
     * @Rest\View(serializerGroups={"contact"}, statusCode=Response::HTTP_CREATED)
     */
    public function postCompanyContactsAction($id, Request $request)
    {
        $company = $this->get('doctrine.orm.entity_manager')
            ->getRepository('ApiBundle:Company')
            ->find($id);

        if (!$company) {
            return $this->companyNotFound();
        }

        $contact = new Contact();
        $contact->setCompany($company);
        $form = $this->createForm(ContactType::class, $contact);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($contact);
            $em->flush();
            
            return $contact;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerGroups={"contact"})
     */
    public function getContactAction($id)
    {
        $contact = $this->get('doctrine.orm.entity_manager')
            ->getRepository('ApiBundle:Contact')
            ->find($id);

        if (!$contact) {
            return $this->contactNotFound();
        }

        return $contact;
    }
    
    /**
     * @Rest\View(serializerGroups={"contact"})
     */
    public function putContactAction($id, Request $request)
    {
        return $this->updateContact($id, $request, true);
    }

    /**
     * @Rest\View(serializerGroups={"contact"})
     */
    public function patchContactAction($id, Request $request)
    {
        return $this->updateContact($id, $request, false);
    }
    
    private function updateContact($id, Request $request, $clearMissing)
    {
        $contact = $this->get('doctrine.orm.entity_manager')
                ->getRepository('ApiBundle:Contact')
                ->find($id);

        if (!$contact) {
            return $this->contactNotFound();
        }
        
        $form = $this->createForm(ContactType::class, $contact);
        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->merge($contact);
            $em->flush();
            
            return $contact;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerGroups={"contact"}, statusCode=Response::HTTP_NO_CONTENT)
     */
    public function deleteContactAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $contact = $em
            ->getRepository('ApiBundle:Contact')
            ->find($id);

        if (!$contact) {
            return;
        }

        $em->remove($contact);
        $em->flush();
    }

    private function contactNotFound()
    {
        return View::create(['message' => 'Contact not found'], Response::HTTP_NOT_FOUND);
    }
    private function companyNotFound()
    {
        return View::create(['message' => 'Company not found'], Response::HTTP_NOT_FOUND);
    }
}
