<?php

namespace UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface;
use UserBundle\Entity\User;
use UserBundle\Entity\Usergroup;

class UserController extends Controller {

    /**
     * @Rest\View(statusCode=200)
     */
    public function getAction($username) {
        return $this->responseMessageCreator(200, 'OK');
    }

    /**
     * @Rest\View(statusCode=201)
     */
    public function addAction(Request $request) {
        $userName = null;
        //add validation here
        if (null !== $request->request->get('username')) {
            $userName = $request->request->get('username');
        } else {
            return $this->responseMessageCreator(400, json_encode('missing paramters in request', 1));
        }

        if ($this->checkUserByUsernameExists($userName)) {
            return $this->responseMessageCreator(200, json_encode('user already existed', 1));
        }
        try {
            $user = new User();
            $user->setName($userName);

            $this->manager()->persist($user);
            $this->manager()->flush();
            return $this->responseMessageCreator(201, json_encode('successfully added', 1));
        } catch (\Doctrine\ORM\ORMException $e) {
            return $this->responseMessageCreator(409, 'CONFLICT');
        } catch (\Exception $e) {
//            $logger->error('An error occurred' .$e);
            return $this->responseMessageCreator(500, 'server internal error');
        }
    }

    /**
     * @Rest\View(statusCode=204)
     */
    public function deleteAction($username) {

        if (!$this->checkUserByUsernameExists($username)) {
            return $this->responseMessageCreator(404, json_encode('user is not found', 1));
        }
        $user = $this->getUserByUsername($username);
        try {

            foreach ($this->manager()->getRepository('UserBundle:Usergroup')->findByUserId(
                    ['userId' => $user->getId()]
            ) as $obj) {
                $this->manager()->remove($obj);
            }
            $this->manager()->remove($user);
            $this->manager()->flush();
            return $this->responseMessageCreator(204, json_encode('successfully deleted', 1));
        } catch (\Doctrine\ORM\ORMException $e) {
            return $this->responseMessageCreator(409, 'CONFLICT');
        } catch (\Exception $e) {
//            $logger->error('An error occurred' .$e);
            return $this->responseMessageCreator(500, 'server internal error');
        }
    }

    /**
     * PUT request
     * @param Request $request
     * @param type $id
     * @return type
     */
    public function assignAction(Request $request) {
        if (null !== $request->request->get('groupname')) {
            $groupName = $request->request->get('groupname');
        } else {
            return $this->responseMessageCreator(400, json_encode('missing paramters in request', 1));
        }
        if (null !== $request->request->get('username')) {
            $userName = $request->request->get('username');
        } else {
            return $this->responseMessageCreator(400, json_encode('missing paramters in request', 1));
        }

        if (!$this->groupIsExited($groupName)) {
            return $this->responseMessageCreator(200, json_encode('group doesn\'t existed', 1));
        }
        if ($this->checkUserByUsername($userName, $groupName)) {
            return $this->responseMessageCreator(200, json_encode('user is already in the group', 1));
        }
        $user = $this->manager()->getRepository('UserBundle:User')->findOneBy(
                array('name' => $userName)
        );

        $groups = $this->manager()->getRepository('UserBundle:Groups')->findOneBy(
                array('name' => $groupName)
        );
        $userGroup = new Usergroup();

        try {
            $userGroup->setGroupId($groups->getId());
            $userGroup->setUserId($user->getId());
            $this->manager()->persist($userGroup);
            $this->manager()->flush();
            return $this->responseMessageCreator(201, json_encode('user is apart of the group successfully', 1));
        } catch (\Doctrine\ORM\ORMException $e) {
            return $this->responseMessageCreator(409, 'CONFLICT');
        } catch (\Exception $e) {
//            $logger->error('An error occurred' .$e);
            return $this->responseMessageCreator(500, 'server internal error');
        }
    }

    public function removeUserFromGroupAction(Request $request) {

        if (null !== $request->request->get('username')) {
            $userName = $request->request->get('username');
        } else {
            return $this->responseMessageCreator(400, json_encode('missing paramters in request', 1));
        }
       
        if (null !== $request->request->get('groupname')) {
            $groupName = $request->request->get('groupname');
        } else {
            return $this->responseMessageCreator(400, json_encode('missing paramters in request', 1));
        }
        
        if ($this->checkUserByUsername($userName, $groupName)) {
            try {
                $userGroupMergedResults = $this->userGroupFinder($userName, $groupName);
                $userGroup = $this->manager()->getRepository('UserBundle:Usergroup')->findOneBy(
                        ['userId' => $userGroupMergedResults['user']->getId(),
                            'groupId' => $userGroupMergedResults['groups']->getId()
                        ]
                );
                $this->manager()->remove($userGroup);
                $this->manager()->flush();
                return $this->responseMessageCreator(204, json_encode('user is deleted from the group successfully', 1));
            } catch (\Doctrine\ORM\ORMException $e) {
                return $this->responseMessageCreator(409, 'CONFLICT');
            } catch (\Exception $e) {
//            $logger->error('An error occurred' .$e);
                return $this->responseMessageCreator(500, 'server internal error');
            }
        } else {
            return $this->responseMessageCreator(200, json_encode('no user found to delete from the group', 1));
        }
    }

    public function removeAllUsersFromGroupAction($groupname) {
        if (null !== $groupname) {
            $groupName = $groupname;
        } else {
            return $this->responseMessageCreator(400, json_encode('missing paramters in request', 1));
        }
        $group = $this->manager()
                        ->getRepository('UserBundle:Groups')->findOneBy(
                array('name' => $groupName)
        );
        try {
            foreach ($this->manager()->getRepository('UserBundle:Usergroup')->findBy(
                    ['groupId' => $group->getId()]
            ) as $obj) {
                $this->manager()->remove($obj);
            }
            $this->manager()->flush();
            return $this->responseMessageCreator(204, json_encode('All users are deleted from the group successfully', 1));
        } catch (\Doctrine\ORM\ORMException $e) {
            return $this->responseMessageCreator(409, 'CONFLICT');
        } catch (\Exception $e) {
            return $this->responseMessageCreator(500, 'server internal error');
        }
    }

    private function userGroupFinder($userName, $groupName) {

        $user = $this->manager()->getRepository('UserBundle:User')->findOneBy(
                ['name' => $userName]
        );

        $groups = $this->manager()->getRepository('UserBundle:Groups')->findOneBy(
                ['name' => $groupName]
        );
        return ['user' => $user, 'groups' => $groups];
    }

    private function checkUserByUsername($userName, $groupName) {

        $user = $this->manager()
                        ->getRepository('UserBundle:User')->findOneBy(
                array('name' => $userName)
        );
        $group = $this->manager()
                        ->getRepository('UserBundle:Groups')->findOneBy(
                array('name' => $groupName)
        );

        $userGroup = $this->manager()
                        ->getRepository('UserBundle:Usergroup')->findOneBy(
                array('userId' => $user->getId(), 'groupId' => $group->getId())
        );

        return ($userGroup === null) ? false : true;
    }

    private function checkUserByUsernameExists($name) {

        (array) $existed = $this->manager()
                        ->getRepository('UserBundle:User')->findOneBy(
                array('name' => $name)
        );
        return ($existed === null) ? false : true;
    }

    private function getUserByUsername($name) {

        return $this->manager()
                        ->getRepository('UserBundle:User')->findOneBy(
                        array('name' => $name)
        );
    }

    private function responseMessageCreator($statusCode, $message) {
        $response = new Response($message);
        $response->setStatusCode($statusCode);
        return $response;
    }

    private function groupIsExited($userGroupName) {
        (array) $existed = $this->manager()
                        ->getRepository('UserBundle:Groups')->findOneByName($userGroupName);
        return ($existed === null) ? false : true;
    }

    private function manager() {
        return $this->getDoctrine()->getManager();
    }

}
