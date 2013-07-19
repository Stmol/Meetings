<?php

namespace Stmol\HuddleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Stmol\HuddleBundle\Entity\Member;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * MemberMeetingRole
 *
 * @ORM\Table(name="member_meeting_relations", uniqueConstraints={@UniqueConstraint(name="member_meeting_idx", columns={"member_id", "meeting_id"})})
 * @ORM\Entity
 */
class MemberMeetingRole
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="smallint", options={"default":1})
     */
    private $state;

    /**
     * @var integer
     *
     * @ORM\Column(name="role", type="smallint", options={"default":0})
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity="Member", inversedBy="meetingRelations")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id", nullable=false)
     */
    private $member;

    /**
     * @ORM\ManyToOne(targetEntity="Meeting", inversedBy="memberRelations")
     * @ORM\JoinColumn(name="meeting_id", referencedColumnName="id", nullable=false)
     */
    private $meeting;

    public function __construct()
    {
        // Default member takes part in a meeting
        $this->state = 1;
        // Default member not the organizer
        $this->role = 0;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return MemberMeetingRole
     */
    public function setState($state)
    {
        $this->state = $state;
    
        return $this;
    }

    /**
     * Get state
     *
     * @return integer 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set role
     *
     * @param integer $role
     * @return MemberMeetingRole
     */
    public function setRole($role)
    {
        $this->role = $role;
    
        return $this;
    }

    /**
     * Get role
     *
     * @return integer 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set member
     *
     * @param \Stmol\HuddleBundle\Entity\Member $member
     * @return MemberMeetingRole
     */
    public function setMember(Member $member = null)
    {
        $this->member = $member;
    
        return $this;
    }

    /**
     * Get member
     *
     * @return \Stmol\HuddleBundle\Entity\Member 
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * Set meeting
     *
     * @param Meeting $meeting
     * @return MemberMeetingRole
     */
    public function setMeeting(Meeting $meeting = null)
    {
        $this->meeting = $meeting;
    
        return $this;
    }

    /**
     * Get meeting
     *
     * @return Meeting
     */
    public function getMeeting()
    {
        return $this->meeting;
    }
}