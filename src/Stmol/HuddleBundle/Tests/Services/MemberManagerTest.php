<?php

namespace Stmol\HuddleBundle\Tests\Services;

use Stmol\HuddleBundle\Entity\Member;
use Stmol\HuddleBundle\Services\MemberManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MemberManagerTest extends WebTestCase
{
    protected $_doctrine;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->_doctrine = static::$kernel->getContainer()
            ->get('doctrine');
    }

    /**
     * @dataProvider providerCreateMember
     * @param \Stmol\HuddleBundle\Entity\Member $member
     */
    public function testCreateMember(Member $member)
    {
        $memberManager = new MemberManager($this->_doctrine->getManager());
        $memberManager->createMember($member);

        // Test new member
        $testMember = $this->_doctrine
            ->getRepository('StmolHuddleBundle:Member')
            ->findOneBy(
                array(
                    'email'   => $member->getEmail(),
                    'name'    => $member->getName(),
                    'surname' => $member->getSurname(),
                )
            );

        $this->assertInstanceOf('Stmol\HuddleBundle\Entity\Member', $testMember);
    }

    public function providerCreateMember()
    {
        $m1 = new Member();

        $m1
            ->setEmail('fake@test.com')
            ->setName('FakeName')
            ->setSurname('FakeSurName')
        ;

        return array(
            # 1
            array($m1),
        );
    }
}
