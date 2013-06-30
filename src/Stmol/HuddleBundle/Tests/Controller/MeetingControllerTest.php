<?php

namespace Stmol\HuddleBundle\Tests\Controller;

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
        $authorTest = $this->_doctrine
            ->getRepository('StmolHuddleBundle:Member')
            ->findOneBy(
                array(
                    'email'   => $author['email'],
                    'name'    => $author['name'],
                    'surname' => $author['surname'],
                )
            );

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

        $this->assertTrue($client->getResponse()->isRedirect());
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
}