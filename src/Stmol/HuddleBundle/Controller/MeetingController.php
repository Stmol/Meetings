<?php

namespace Stmol\HuddleBundle\Controller;

use Stmol\HuddleBundle\Entity\Meeting;
use Stmol\HuddleBundle\Services\MeetingManager;
use Stmol\HuddleBundle\Services\MemberManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class MeetingController
 * @package Stmol\HuddleBundle\Controller
 * @author Yury [Stmol] Smidovich
 */
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
        $form = $this->createForm('new_meeting', array('member' => $this->getUser()));
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var MemberManager $memberManager */
            $memberManager = $this->get('stmol_huddle.member_manager');

            /** @var MeetingManager $meetingManager */
            $meetingManager = $this->get('stmol_huddle.meeting_manager');

            $member = $memberManager->createMember($form->get('member')->getData());
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

            // Set the cookie with secret token
            // TODO (Stmol) Make a service for this action
            if (!$this->getRequest()->cookies->has($this->container->getParameter('stmol_huddle.cookie.user_secret'))
                OR !$this->getUser()
            ) {
                $response = new Response();
                // TODO (Stmol) Other arguments for Cookie!
                $response->headers->setCookie(
                    new Cookie(
                        $this->container->getParameter('stmol_huddle.cookie.user_secret'),
                        $member->getSecret()
                    )
                );
                $response->sendHeaders();
            }

            return $this->redirect($this->generateUrl('show_meeting', array('url' => $meeting->getUrl())));
        }

        return $this->render(
            'StmolHuddleBundle:Meeting:new.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @param Request $request
     * @param $url
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return Response
     */
    public function showAction(Request $request, $url)
    {
        /** @var Meeting $meeting */
        $meeting = $this->getDoctrine()
            ->getRepository('StmolHuddleBundle:Meeting')
            ->findOneBy(array('url' => $url));

        if (!$meeting) {
            // TODO (Stmol) translate
            throw new NotFoundHttpException('Meeting not found!');
        }

        $form = $this->createForm('member', $this->getUser());
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var Member $memberManager */
            $member = $this->get('stmol_huddle.member_manager')->createMember($form->getData(), $meeting);

            if (!$this->getRequest()->cookies->has($this->container->getParameter('stmol_huddle.cookie.user_secret'))
            OR !$this->getUser()) {
                $response = new Response();
                // TODO (Stmol) Other arguments for Cookie!
                $response->headers->setCookie(
                    new Cookie(
                        $this->container->getParameter('stmol_huddle.cookie.user_secret'),
                        $member->getSecret()
                    )
                );
                $response->sendHeaders();
            }

            return $this->redirect($this->generateUrl('show_meeting', array('url' => $meeting->getUrl())));
        }

        return $this->render(
            'StmolHuddleBundle:Meeting:show.html.twig', array(
                'form'    => $form->createView(),
                'meeting' => $meeting,
            )
        );
    }

    /**
     * @param Request $request
     * @param $url
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return Response
     */
    public function editAction(Request $request, $url)
    {
        /** @var Meeting $meeting */
        $meeting = $this->getDoctrine()
            ->getRepository('StmolHuddleBundle:Meeting')
            ->findOneBy(array('url' => $url));

        if (!$meeting) {
            throw new NotFoundHttpException('Meeting with url ' . $url . ' not found');
        }

        // Secure zone:
        // - meeting may be changed only one
        // - who has the cookies with a secret key
        $cookie = $this->getRequest()->cookies;

        if (!$cookie->has($this->container->getParameter('stmol_huddle.cookie.user_secret'))) {
            return $this->redirect($this->generateUrl('show_meeting', array('url' => $meeting->getUrl())));
        }

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
            return $this->redirect($this->generateUrl('show_meeting', array('url' => $meeting->getUrl())));
        }

        $form = $this->createForm('meeting', $meeting);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var MeetingManager $meetingManager */
            $meetingManager = $this->get('stmol_huddle.meeting_manager');
            $meetingManager->updateMeeting($meeting);

            return $this->redirect($this->generateUrl('show_meeting', array('url' => $meeting->getUrl())));
        }

        return $this->render(
            'StmolHuddleBundle:Meeting:edit.html.twig', array(
                'meeting' => $meeting,
                'form'    => $form->createView(),
            )
        );
    }
}