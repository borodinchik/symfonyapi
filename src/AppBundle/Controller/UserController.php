<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\User;

class UserController extends FOSRestController
{
    /**
     * @Rest\Get("/user")
     */
    public function getAllUserAction()
    {
        $response = $this->getDoctrine()->getRepository(User::class)->findAll();
        if ($response === null)
        {
            return new View('There are user no exist!', Response::HTTP_NOT_FOUND);
        }
        return $response;
    }
    /**
     * @Rest\Get("/user/{id}")
     */
    public function showIdAction($id)
    {
        $response = $this->getDoctrine()->getRepository(User::class)->find($id);
        if ($response === null)
        {
            return new View('User not found', Response::HTTP_NOT_FOUND);
        }
        return $response;
    }

    /**
     * @Rest\Post("/user/")
     * @param Request $request
     */
    public function createNewUserAction(Request $request)
    {
        $user = new User();
        $name = $request->get('name');
        $role = $request->get('role');

        if (empty($name) || empty($role))
        {
            return new View('NULL VALUES ARE NOT ALLOWED1', Response::HTTP_NOT_ACCEPTABLE);
        }
        $user->setName($name);
        $user->setRole($role);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return new View('User Added Successfully', Response::HTTP_OK);
    }

    /**
     * @Rest\Put("/user/{id}")
     * @param $id
     * @param Request $request
     */
    public function updateUserAction($id, Request $request)
    {
        $data = new User();
        $name = $request->get('name');
        $role = $request->get('role');

        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        switch ($user)
        {
            case empty($user):
                return new View('User not found', Response::HTTP_NOT_FOUND);

            case !empty($name) && !empty($role):
                $user->setName($name);
                $user->setRole($role);
                $entityManager->flush();

                return new View('User Updated Successfully', Response::HTTP_OK);

            case empty($name) && !empty($role):
                $user->setRole($role);
                $entityManager->flush();

                return new View('Role Updated Successfully');

            case !empty($name) && empty($role):
                $user->setName($name);
                $entityManager->flush();

                return new View('Name Updated Successfully');
        }
        return new View('User name or role cannot be empty', Response::HTTP_NOT_ACCEPTABLE);
    }

}
