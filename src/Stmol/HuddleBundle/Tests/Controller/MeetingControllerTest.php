<?php

namespace Stmol\HuddleBundle\Tests\Controller;

use Stmol\HuddleBundle\Entity\Meeting;
use Stmol\HuddleBundle\Entity\Member;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MeetingControllerTest extends WebTestCase
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
    }

    /**
     * @dataProvider providerNewMeetings
     * @param array $meeting
     * @param array $author
     */
    public function testNew(array $meeting, array $author)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/new');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertTrue($crawler->filter('form')->count() == 1);

        // Send form
        $buttonCrawlerNode = $crawler->selectButton('Submit');

        $form = $buttonCrawlerNode->form();

        $client->submit(
            $form,
            array(
                'new_meeting[meeting][title]'       => $meeting['title'],
                'new_meeting[meeting][description]' => $meeting['description'],
                'new_meeting[author][email]'        => $author['email'],
                'new_meeting[author][name]'         => $author['name'],
                'new_meeting[author][surname]'      => $author['surname'],
            )
        );

        // Assert author
        /** @var Member $authorTest */
        $authorTest = $this->_doctrine
            ->getRepository('StmolHuddleBundle:Member')
            ->findOneBy(
                array(
                    'email'   => $author['email'],
                    'name'    => $author['name'],
                    'surname' => $author['surname'],
                )
            );

        /** @var Meeting $meetingTest */
        $meetingTest = $this->_doctrine
            ->getRepository('StmolHuddleBundle:Meeting')
            ->findOneBy(
                array(
                    'title'       => $meeting['title'],
                    'description' => $meeting['description'],
                    'author'      => $authorTest,
                )
            );

        $this->assertInstanceOf('Stmol\HuddleBundle\Entity\Member', $authorTest);
        $this->assertInstanceOf('Stmol\HuddleBundle\Entity\Meeting', $meetingTest);

        // TODO (Stmol) make test for cookies!

        $this->assertTrue($client->getResponse()->isRedirect());
    }

    /**
     * @dataProvider providerNewMembers
     * @param array $author
     */
    public function testShow(array $author)
    {
        /** @var Meeting $meeting */
        $meeting = $this->_doctrine
            ->getRepository('StmolHuddleBundle:Meeting')
            ->findOneById(1);

        $client = static::createClient();
        $crawler = $client->request('GET', '/m/' . $meeting->getUrl());

        // Assert code 200
        $this->assertTrue($client->getResponse()->isOk());

        // Assert data of meetings on page (title of meeting)
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $meeting->getTitle() . '")')->count());

        // Assert form
        $this->assertTrue($crawler->filter('form')->count() == 1);

        // Send form
        $buttonCrawlerNode = $crawler->selectButton('Submit');

        $form = $buttonCrawlerNode->form();

        $client->submit(
            $form,
            array(
                'member[email]'   => $author['email'],
                'member[name]'    => $author['name'],
                'member[surname]' => $author['surname'],
            )
        );

        /** @var Member $memberTest */
        $memberTest = $this->_doctrine
            ->getRepository('StmolHuddleBundle:Member')
            ->findOneBy(
                array(
                    'email'   => $author['email'],
                    'name'    => $author['name'],
                    'surname' => $author['surname'],
                )
            );

        // Assert new member
        $this->assertInstanceOf('Stmol\HuddleBundle\Entity\Member', $memberTest);
    }

    public function providerNewMeetings()
    {
        return array(
            # 1
            array(
                # Meeting
                array(
                    'title'       => 'First meeting (unit test)',
                    'description' => 'First meeting description (unit test)',
                ),
                # Author
                array(
                    'email'   => 'first@unittest.com',
                    'name'    => 'FirstAuthorName',
                    'surname' => 'FirstSurName'
                )
            )
        );
    }

    public function providerNewMembers()
    {
        return array(
            # 1
            array(
                array(
                    'email'   => 'test@test.com',
                    'name'    => 'Name',
                    'surname' => 'Surname'
                )
            )
        );
    }
}