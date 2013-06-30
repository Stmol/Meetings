<?php

namespace Stmol\HuddleBundle\Tests\Services;

use Stmol\HuddleBundle\Entity\Meeting;
use Stmol\HuddleBundle\Entity\Member;
use Stmol\HuddleBundle\Services\MeetingManager;
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
     * @dataProvider providerMeetings
     * @param $title
     * @param $description
     * @param $startDate
     */
    public function testCreateMeeting($title, $description, $startDate)
    {
        $meeting = new Meeting();
        $meeting->setTitle($title);
        $meeting->setDescription($description);
        $meeting->setStartDate($startDate);

        $author = new Member();
        $author->setEmail('test@test.com');
        $author->setName('Name');
        $author->setSurname('Surname');

        $meetingManager = new MeetingManager($this->_doctrine->getManager());
        $meetingManager->createMeeting($meeting, $author);

        // Test new record
        $testMeeting = $this->_doctrine
            ->getRepository('StmolHuddleBundle:Meeting')
            ->findOneBy(
                array(
                    'title'       => $title,
                    'description' => $description,
                    'startDate'   => $startDate,
                )
            );

        $this->assertInstanceOf('Stmol\HuddleBundle\Entity\Meeting', $testMeeting);
    }

    public function providerMeetings()
    {
        return array(
            array('Test title', 'Test description', new \DateTime()),
            array('Test title2', 1234567890, new \DateTime()),
            array(123, 'TEST DESCRIPTION', new \DateTime()),
        );
    }
}