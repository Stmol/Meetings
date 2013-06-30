<?php

namespace Stmol\HuddleBundle\Controller;

use Stmol\HuddleBundle\Form\NewMeetingType;
use Stmol\HuddleBundle\Services\MeetingManager;
use Stmol\HuddleBundle\Services\MemberManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

            $member = $memberManager->createMember($form->get('author')->getData());
            $meeting = $meetingManager->createMeeting($form->get('meeting')->getData(), $member);

            $message = \Swift_Message::newInstance()
                ->setSubject($meeting->getTitle())
                // TODO (Stmol) Define global param with email address
                ->setFrom('meetings@stmol.me')
                ->setTo($member->getEmail())
                ->setBody(
                    $this->renderView(
                        'StmolHuddleBundle:Meeting:email.txt.twig',
                        array(
                            'author'  => $member,
                            'meeting' => $meeting,
                        )
                    )
                );

            $this->get('mailer')->send($message);

            $cookie = array(
                'name'  => 'meeting_' . $meeting->getId(),
                'value' => $meeting->getSecret(),
            );

            $response = new Response();
            // TODO (Stmol) Other arguments for Cookie!
            $response->headers->setCookie(new Cookie($cookie['name'], $cookie['value']));
            $response->sendHeaders();

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