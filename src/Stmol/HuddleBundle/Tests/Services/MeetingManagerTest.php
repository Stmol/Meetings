<?php

namespace Stmol\HuddleBundle\Tests\Services;

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
     * Test method create.
     */
    public function testCreate()
    {
        $meetingManager = new MeetingManager($this->_doctrine->getManager());

        $testData = array(
            'title'       => 'testTitle',
            'description' => 'testDescription',
            'startDate'   => new \DateTime()
        );

        // Create new meeting
        $meetingManager->create($testData);

        // Test new record
        $meeting = $this->_doctrine
            ->getRepository('StmolHuddleBundle:Meeting')
            ->findOneBy($testData);

        $this->assertEquals(1, count($meeting));
        $this->assertInstanceOf('Stmol\HuddleBundle\Entity\Meeting', $meeting);
    }
}