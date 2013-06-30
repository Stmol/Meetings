<?php

namespace Stmol\HuddleBundle\Controller;

use Stmol\HuddleBundle\Form\NewMeetingType;
use Stmol\HuddleBundle\Services\MeetingManager;
use Stmol\HuddleBundle\Services\MemberManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MeetingController extends Controller
{
    /**
     * Page with form for new meeting.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(new NewMeetingType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var MemberManager $memberManager */
            $memberManager = $this->get('stmol_huddle.member_manager');

            /** @var MeetingManager $meetingManager */
            $meetingManager = $this->get('stmol_huddle.meeting_manager');

            $memberManager->createMember($form->get('author')->getData());
            $meetingManager->createMeeting($form->get('meeting')->getData(), $form->get('author')->getData());

            return $this->redirect('/');
        }

        return $this->render(
            'StmolHuddleBundle:Meeting:index.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }
}