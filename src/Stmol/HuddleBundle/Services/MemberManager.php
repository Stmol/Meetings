<?php

namespace Stmol\HuddleBundle\Services;

use Stmol\HuddleBundle\Entity\Member;
use Doctrine\Common\Persistence\ObjectManager;

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
     * @param bool $flush
     */
    public function createMember(Member $member, $flush = true)
    {
        $this->_entityManager->persist($member);

        if ($flush) {
            $this->_entityManager->flush();
        }
    }
}