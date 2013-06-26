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

    protected function tearDown() {}

    public function testNew()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/new');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertTrue($crawler->filter('form')->count() == 1);

        $fixtures = array(
            'title'       => 'Test meeting caption',
            'description' => 'Test meeting long text',
        );

        // Send form
        $buttonCrawlerNode = $crawler->selectButton('Save');

        $form = $buttonCrawlerNode->form();

        $form['meeting[title]'] = $fixtures['title'];
        $form['meeting[description]'] = $fixtures['description'];

        $client->submit($form);

        $meeting = $this->_doctrine
            ->getRepository('StmolHuddleBundle:Meeting')
            ->findOneBy($fixtures);

        $this->assertInstanceOf('Stmol\HuddleBundle\Entity\Meeting', $meeting);
        $this->assertTrue($client->getResponse()->isRedirect());
    }
}