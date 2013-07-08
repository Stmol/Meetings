<?php

namespace Stmol\HuddleBundle\Services;

use Stmol\HuddleBundle\Entity\Meeting;
use Stmol\HuddleBundle\Entity\Member;
use Doctrine\Common\Persistence\ObjectManager;
use Stmol\HuddleBundle\Entity\MemberMeetingRole;

/**
 * Class MemberManager
 *
 * @package Stmol\HuddleBundle\Services
 * @author Yury [Stmol] Smidovich
 */
class MemberManager
{
    private $_entityManager;

    /**
     * @param ObjectManager $entityManager
     */
    public function __construct($entityManager)
    {
        $this->_entityManager = $entityManager;
    }

    /**
     * Create new member.
     *
     * @param Member $member
     * @param \Stmol\HuddleBundle\Entity\Meeting $meeting
     * @param bool $flush
     * @return \Stmol\HuddleBundle\Entity\Member
     */
    public function createMember(Member $member, Meeting $meeting = null, $flush = true)
    {
        $this->_entityManager->persist($member);

        if ($flush) {
            $this->_entityManager->flush();
        }

        if ($meeting) {
            $this->addToMeeting($member, $meeting);
        }

        return $member;
    }

    /**
     * Add member to meeting.
     *
     * @param Member $member
     * @param Meeting $meeting
     * @param bool $flush
     * @return MemberMeetingRole
     */
    public function addToMeeting(Member $member, Meeting $meeting, $flush = true)
    {
        $relation = new MemberMeetingRole();
        $relation->setMeeting($meeting);
        $relation->setMember($member);

        $this->_entityManager->persist($relation);

        if ($flush) {
            $this->_entityManager->flush();
        }

        return $relation;
    }
}