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
     * @dataProvider providerMembers
     * @param $email
     * @param $name
     * @param $surname
     */
    public function testCreateMember($email, $name, $surname)
    {
        $member = new Member();
        $member->setEmail($email);
        $member->setName($name);
        $member->setSurname($surname);

        $memberManager = new MemberManager($this->_doctrine->getManager());
        $memberManager->createMember($member);

        // Test new member
        $testMember = $this->_doctrine
            ->getRepository('StmolHuddleBundle:Member')
            ->findOneBy(
                array(
                    'email'   => $email,
                    'name'    => $name,
                    'surname' => $surname,
                )
            );

        $this->assertInstanceOf('Stmol\HuddleBundle\Entity\Member', $testMember);
    }

    public function providerMembers()
    {
        return array(
            array('test@test.com', 'Name1', 'Surname'),
            array('test2@test.com', 'Name 2', 12345),
            array('some@some.is', 3, 'My long surname'),
        );
    }
}
