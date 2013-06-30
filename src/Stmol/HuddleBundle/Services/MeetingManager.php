<?php

namespace Stmol\HuddleBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Stmol\HuddleBundle\Entity\Meeting;
use Stmol\HuddleBundle\Entity\Member;

class MeetingManager
{
    private $_entityManager;

    /**
     * @param ObjectManager $entityManager
     */
    public function __construct(ObjectManager $entityManager)
    {
        $this->_entityManager = $entityManager;
    }

    /**
     * Create new meeting.
     *
     * @param \Stmol\HuddleBundle\Entity\Meeting $meeting
     * @param \Stmol\HuddleBundle\Entity\Member $author
     * @param bool $flush
     */
    public function createMeeting(Meeting $meeting, Member $author, $flush = true)
    {
        // TODO (Stmol) Check unique URL address before persist
        $meeting->setUrl($this->_generateRandString());
        $meeting->setAuthor($author);

        $this->_entityManager->persist($meeting);

        if ($flush) {
            $this->_entityManager->flush();
        }
    }

    /**
     * Generate random string for URL.
     *
     * @param int $length
     * @return string
     */
    protected function _generateRandString($length = 7)
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }
}
