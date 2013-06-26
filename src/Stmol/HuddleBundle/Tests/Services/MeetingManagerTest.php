<?php

namespace Stmol\HuddleBundle\Tests\Services;

use Stmol\HuddleBundle\Entity\Meeting;
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
     * Test method createMeeting.
     */
    public function testCreate()
    {
        $meetingManager = new MeetingManager($this->_doctrine->getManager());

        $fixtures = array(
            'title'       => 'testTitle',
            'description' => 'testDescription',
            'startDate'   => new \DateTime()
        );

        // Create new meeting
        $testMeeting = new Meeting();
        $testMeeting->setTitle($fixtures['title']);
        $testMeeting->setDescription($fixtures['description']);
        $testMeeting->setStartDate($fixtures['startDate']);

        $meetingManager->createMeeting($testMeeting);

        // Test new record
        $meeting = $this->_doctrine
            ->getRepository('StmolHuddleBundle:Meeting')
            ->findOneBy($fixtures);

        $this->assertInstanceOf('Stmol\HuddleBundle\Entity\Meeting', $meeting);
    }
}