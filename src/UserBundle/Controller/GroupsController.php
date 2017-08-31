<?php

namespace UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface;
use UserBundle\Entity\Groups;
use UserBundle\Entity\User;

//use Symfony\Component\Validator\Constraints\Collection;
//use Symfony\Component\Validator\Constraints\Email;
//use Symfony\Component\Validator\Constraints\Date;
//use Symfony\Component\Validator\Constraints\NotBlank;

class GroupsController extends Controller {

    public function addAction(Request $request) {

        //add validation here
        $groupName = null;
        $permissionId = null;
        if (null !== $request->request->get('groupname')) {
            $groupName = $request->request->get('groupname');
        } else {
            return $this->responseMessageCreator(400, json_encode('missing paramters in request', 1));
        }
        if (null !== $request->request->get('permissionid')) {
            $permissionId = $request->request->get('permissionid');
        } else {
            return $this->responseMessageCreator(400, json_encode('missing paramters in request', 1));
        }

        if ($this->checkGroupnameExits($groupName)) {
            return $this->responseMessageCreator(200, json_encode('group already existed', 1));
        }

        try {
            $groups = new Groups;
            $groups->setName($groupName);
            $groups->setPermissionId($permissionId);
            $this->manager()->persist($groups);
            $this->manager()->flush();
//            $logger->info('successfully added' );
            return $this->responseMessageCreator(201, 'successfully added');
        } catch (\Doctrine\ORM\ORMException $e) {
//            $logger->error('An error occurred' .$e);
            return $this->responseMessageCreator(409, 'CONFLICT');
        } catch (\Exception $e) {
//            $logger->error('An error occurred' .$e);
            return $this->responseMessageCreator(500, 'server internal error');
        }
    }

    public function deleteAction($groupname) {
        if (null !== $groupname) {
            $groupName = $groupname;
        } else {
            return $this->responseMessageCreator(400, json_encode('missing paramters in request', 1));
        }
        if (!$this->checkGroupnameExits($groupname)) {
            return $this->responseMessageCreator(200, json_encode('group doesn\'t existed', 1));
        }
        if ($this->userFinderinGroup($groupname)) {
            return $this->responseMessageCreator(200, json_encode('Could not complete operation user(s) exits in this group', 1));
        }
        try {
            $group = $this->getGroupByName($groupname);
            $this->manager()->remove($group);
            $this->manager()->flush();
            return $this->responseMessageCreator(204, json_encode('successfully deleted', 1));
        } catch (\Doctrine\ORM\ORMException $e) {
            return $this->responseMessageCreator(409, 'CONFLICT');
        } catch (\Exception $e) {
//            $logger->error('An error occurred' .$e);
            return $this->responseMessageCreator(500, 'server internal error');
        }
    }

    private function userFinderinGroup($groupName) {
        $groups = $this->manager()->getRepository('UserBundle:Groups')->findOneBy(
                ['name' => $groupName]
        );
        (array) $userGroup = $this->manager()->getRepository('UserBundle:Usergroup')->findOneBy(
                ['groupId' => $groups->getId()]
        );
        return ($userGroup === null) ? false : true;
    }

    private function responseMessageCreator($statusCode, $message) {
        $response = new Response($message);
        $response->setStatusCode($statusCode);

        return $response;
    }

    private function manager() {
        return $this->getDoctrine()->getManager();
    }

    private function checkGroupnameExits($name) {
        (array) $name = $this->manager()
                        ->getRepository('UserBundle:Groups')->findOneByName($name);
        return ($name === null) ? false : true;
    }

    private function getGroupByName($name) {
        return $this->manager()
                        ->getRepository('UserBundle:Groups')->findOneBy(array('name' => $name));
    }


}
