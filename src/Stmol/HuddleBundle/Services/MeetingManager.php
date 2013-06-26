<?php

namespace Stmol\HuddleBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Stmol\HuddleBundle\Entity\Meeting;

class MeetingManager 
{
    private $_entityManager;

    public function __construct(ObjectManager $entityManager)
    {
        $this->_entityManager = $entityManager;
    }

    /**
     * Create new meeting.
     *
     * @param array $data
     * @param bool $flush
     */
    public function create(array $data, $flush = true)
    {
        $meeting = new Meeting();
        $meeting->setTitle($data['title']);
        $meeting->setDescription($data['description']);
        // TODO (Stmol) Check unique URL address before persist
        $meeting->setUrl($this->_generateRandString());
        $meeting->setStartDate($data['startDate']);

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
