<?php

namespace Stmol\HuddleBundle\Tests\Services;

use Stmol\HuddleBundle\Entity\Meeting;
use Stmol\HuddleBundle\Entity\Member;
use Stmol\HuddleBundle\Services\MeetingManager;
use Stmol\HuddleBundle\Services\MemberManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MeetingManagerTest extends WebTestCase
{
    protected $_doctrine;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->_doctrine = static::$kernel->getContainer()
            ->get('doctrine');
    }

    protected function tearDown()
    {
        $this->_doctrine->getManager()->close();
    }

    /**
     * @dataProvider providerCreateMeeting
     * @param \Stmol\HuddleBundle\Entity\Meeting $meeting
     * @param \Stmol\HuddleBundle\Entity\Member $author
     */
    public function testCreateMeeting(Meeting $meeting, Member $author)
    {
        $meetingManager = new MeetingManager($this->_doctrine->getManager());
        $meetingManager->createMeeting($meeting);

        // Test new record
        $testMeeting = $this->_doctrine
            ->getRepository('StmolHuddleBundle:Meeting')
            ->findOneBy(
                array(
                    'title'       => $meeting->getTitle(),
                    'description' => $meeting->getDescription(),
                    'startDate'   => $meeting->getStartDate(),
                )
            );

        $this->assertInstanceOf('Stmol\HuddleBundle\Entity\Meeting', $testMeeting);
    }

    /**
     * Test addAuthor method.
     *
     * @dataProvider providerAddAuthor
     * @param Meeting $meeting
     * @param Member $member
     */
    public function testAddAuthor(Meeting $meeting, Member $member)
    {
        $meetingManager = new MeetingManager($this->_doctrine->getManager());
        $memberManager = new MemberManager($this->_doctrine->getManager());

        $meetingManager->createMeeting($meeting);
        $memberManager->createMember($member);

        $meetingManager->addAuthor($meeting, $member);

        $relation = $this->_doctrine->getManager()
            ->getRepository('StmolHuddleBundle:MemberMeetingRole')
            ->findOneBy(
                array(
                    'member'  => $member,
                    'meeting' => $meeting,
                    'role'    => 1,
                )
            );

        $this->assertInstanceOf('Stmol\HuddleBundle\Entity\MemberMeetingRole', $relation);
    }

    /**
     * Test addMember method.
     *
     * TODO (Stmol) make separate data provider
     * @dataProvider providerAddAuthor
     * @param Meeting $meeting
     * @param Member $member
     */
    public function testAddMember(Meeting $meeting, Member $member)
    {
        $meetingManager = new MeetingManager($this->_doctrine->getManager());
        $memberManager = new MemberManager($this->_doctrine->getManager());

        $meetingManager->createMeeting($meeting);
        $memberManager->createMember($member);

        $meetingManager->addMember($meeting, $member);

        $relation = $this->_doctrine->getManager()
            ->getRepository('StmolHuddleBundle:MemberMeetingRole')
            ->findOneBy(
                array(
                    'meeting' => $meeting,
                    'member'  => $member,
                    'state'   => 1,
                    'role'    => 0
                )
            );

        $this->assertInstanceOf('Stmol\HuddleBundle\Entity\MemberMeetingRole', $relation);
    }

    /**
     * @return array
     */
    public function providerAddAuthor()
    {
        $firstMeeting = new Meeting();
        $firstMeeting
            ->setTitle('Test title')
            ->setDescription('Test description')
            ->setStartDate(new \DateTime());

        $firstMember = new Member();
        $firstMember
            ->setEmail('test@test.com')
            ->setName('Test name')
            ->setSurname('Test surname');

        return array(
            # 1
            array($firstMeeting, $firstMember),
        );
    }

    /**
     * @return array
     */
    public function providerCreateMeeting()
    {
        $firstMeeting = new Meeting();
        $firstMeeting->setTitle('TestMeeting');
        $firstMeeting->setDescription('TestDescription');
        $firstMeeting->setStartDate(new \DateTime());

        $firstMember = new Member();
        $firstMember->setEmail('test123@test.com');
        $firstMember->setName('TestNameUser');
        $firstMember->setSurname('Noname');

        return array(
            # 1
            array($firstMeeting, $firstMember),
        );
    }
}