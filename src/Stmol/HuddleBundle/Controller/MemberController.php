<?php

namespace Stmol\HuddleBundle\Controller;

use Stmol\HuddleBundle\Entity\Meeting;
use Stmol\HuddleBundle\Entity\Member;
use Stmol\HuddleBundle\Services\MemberManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MemberController extends Controller
{
    /**
     * @param $url
     * @param $action
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changeStateAction($url, $action)
    {
        if (!$this->getUser()) {
            return $this->redirect(
                $this->generateUrl('show_meeting', array('url' => $url))
            );
        }

        /** @var Meeting $meeting */
        $meeting = $this->getDoctrine()
            ->getRepository('StmolHuddleBundle:Meeting')
            ->findOneBy(array('url' => $url));

        if (!$meeting) {
            // TODO (Stmol) Translate
            throw new NotFoundHttpException('Meeting not found');
        }

        /** @var MemberManager $memberManager */
        $memberManager = $this->get('stmol_huddle.member_manager');
        $memberManager->changeState($this->getUser(), $meeting, $action);

        return $this->redirect(
            $this->generateUrl('show_meeting', array('url' => $url))
        );
    }

    /**
     * Manage member from edit page.
     *
     * @param $url
     * @param $id
     * @param $action
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function manageAction($url, $id, $action)
    {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('default'));
        }

        /** @var Meeting $meeting */
        $meeting = $this->getDoctrine()
            ->getRepository('StmolHuddleBundle:Meeting')
            ->findOneBy(array('url' => $url));

        if (!$meeting) {
            // TODO (Stmol) Translate
            throw new NotFoundHttpException('Meeting not found');
        }

        /** @var MemberMeetingRole $relation */
        $relation = $this->getDoctrine()
            ->getRepository('StmolHuddleBundle:MemberMeetingRole')
            ->findOneBy(
                array(
                    'member'  => $this->getUser(),
                    'meeting' => $meeting,
                    'role'    => 1,
                )
            );

        if (!$relation) {
            // TODO (Stmol) Translate
            throw new AccessDeniedException('You havent privileges to do it');
        }

        /** @var Member $member */
        $member = $this->getDoctrine()
            ->getRepository('StmolHuddleBundle:Member')
            ->find($id);

        if (!$member) {
            // TODO (Stmol) Translate
            throw new NotFoundHttpException('Member not found');
        }

        // TODO (Stmol) Refactor
        if ($member == $this->getUser()) {
            return $this->redirect($this->generateUrl('edit_meeting', array('url' => $url)));
        }

        /** @var MemberManager $memberManager */
        $memberManager = $this->get('stmol_huddle.member_manager');
        $memberManager->changeState($member, $meeting, $action);

        return $this->redirect($this->generateUrl('edit_meeting', array('url' => $url)));
    }
}