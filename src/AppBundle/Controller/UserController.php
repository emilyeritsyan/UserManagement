<?php

namespace AppBundle\Controller;



use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController
{
    /**
     * @Rest\View
     */
    public function allAction()
    {
        echo "hello";
//        $users = UserQuery::create()->find();
//
//        return array('users' => $users);
    }

    /**
     * @Rest\View
     */
    public function getAction($id)
    {
//        $user = UserQuery::create()->findPk($id);
//        $user = "test";
        echo "hello2";
//        if (!$user instanceof User) {
//            throw new NotFoundHttpException('User not found');
//        }
//
//        return array('user' => $user);
    }
}