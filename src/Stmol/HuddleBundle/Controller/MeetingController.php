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
        $form = $this->createForm('new_meeting');
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

        $form = $this->createForm('member');
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var MemberManager $memberManager */
            $memberManager = $this->get('stmol_huddle.member_manager');
            $memberManager->createMember($form->getData(), $meeting);
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

        if (!$cookie->has('meeting_' . $meeting->getId()) OR $cookie->get('meeting_' . $meeting->getId()) !== $meeting->getSecret()) {
            return $this->redirect($this->generateUrl('show_meeting', array('url' => $meeting->getUrl())));
        }

        $form = $this->createForm('meeting', $meeting);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var MeetingManager $meetingManager */
            $meetingManager = $this->get('stmol_huddle.meeting_manager');
            $meetingManager->updateMeeting($meeting);

            // TODO (Stmol) do it through MeetingManager or as a way!
//            $manager = $this->getDoctrine()->getManager();
//            $manager->persist($meeting);
//            $manager->flush();
        }

        return $this->render(
            'StmolHuddleBundle:Meeting:edit.html.twig', array(
                'meeting' => $meeting,
                'form'    => $form->createView(),
            )
        );
    }
}