<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\User;
use ApiBundle\Form\Type\UserType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends FOSRestController
{
    /**
     * @Rest\View(serializerGroups={"user"})
     */
    public function getUsersAction()
    {
        $users = $this->get('doctrine.orm.entity_manager')
            ->getRepository('ApiBundle:User')
            ->findAll();

        return $users;
    }

    /**
     * @Rest\View(serializerGroups={"user"}, statusCode=Response::HTTP_CREATED)
     */
    public function postUserAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $user->setSalt(md5(random_bytes(15)));
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password)
                ->eraseCredentials();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            return $user;
        } else {
            return $form;
        }
    }
}
