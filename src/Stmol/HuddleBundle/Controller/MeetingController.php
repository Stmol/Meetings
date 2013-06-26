<?php

namespace Stmol\HuddleBundle\Controller;

use Stmol\HuddleBundle\Services\MeetingManager;
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
        $form = $this->createForm('meeting');

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var MeetingManager $meetingManager */
            $meetingManager = $this->get('stmol_huddle.meetings_manager');
            $meetingManager->createMeeting($form->getData());

            return $this->redirect('/');
        }

        return $this->render('StmolHuddleBundle:Meeting:index.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}