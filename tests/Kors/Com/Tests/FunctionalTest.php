<?php

namespace Kors\Com\Tests;

use Symfony\Component\DomCrawler\Crawler;

use Silex\WebTestCase;

class FunctionalTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__.'/../../../../app/app.php';        

        $app['session.test'] = true;
        
        return $app;
    }
    
    
    /**
     * @dataProvider getMenuTests
     */
    public function testMenu($text, $link)
    {
        $client  = $this->createClient();
        $crawler = $client->request('GET', $link);
        
        $active  = $crawler->filter('.navbar .nav .active');
        $this->assertCount(1, $active);
        $this->assertEquals($text, trim($active->filter('> a')->eq(0)->text()));
        
        $links = $crawler->filter('.navbar .nav.main a');
        $this->assertCount(count($this->getMenuTests()), $links);
        foreach ($this->getMenuTests() as $k => $v) {
            $this->assertEquals($v[0], trim($links->eq($k)->text()));
            $this->assertEquals($v[1], trim($links->eq($k)->attr('href')));
        }
    }    
    
    public function getMenuTests()
    {
        return array(
            array('Home',	 '/',),
            array('About',   '/about',),
            array('Contact', '/contact',),
        );
    }
    
    public function testHomePage()
    {
        $client  = $this->createClient();
        $crawler = $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isOk());
    }
    
    public function testAbout()
    {
        $client  = $this->createClient();
        $crawler = $client->request('GET', '/about');
        $this->assertTrue($client->getResponse()->isOk());
    }  
    
    public function testContactPage()
    {
        $this->app['mailer'] = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()
            ->getMock(); 

        $this->app['mailer']
            ->expects($this->once())
            ->method('send');

        $client  = $this->createClient();
        $client->followRedirects();
        
        $crawler = $client->request('GET', '/contact');
        $this->assertTrue($client->getResponse()->isOk());

        $form    = $crawler->selectButton('Send')->form();
        
        $success = $client->submit($form, array(
            'contact_type[name]'    => 'Foo Bar',
            'contact_type[email]'   => 'foo@example.com',
        	'contact_type[subject]' => 'Foo bar',
        	'contact_type[content]' => 'Foo bar lorem',
        ));
        
        $this->assertRegExp('/Message successfully sent/', $success->filter('.alert-success')->text());
        
        $error = $client->submit($form, array(
            'contact_type[name]'    => 'Foo Bar',
            'contact_type[email]'   => 'foo',
        	'contact_type[subject]' => 'Foo bar',
        	'contact_type[content]' => 'Foo bar lorem',
        ));
        
        $this->assertEquals('This value is not a valid email address.', $error->filter('#contact_type_email')->nextAll()->eq(0)->filter('li')->text());
                 
    }
    
    public function testError404Page()
    {
        $this->app['debug'] = false;
        
        $client  = $this->createClient();
        
        $crawler = $client->request('GET', '/foobar');
        
        $this->assertTrue($client->getResponse()->isNotFound());
        
        $this->assertEquals('/foobar', $crawler->filter('.container p em')->text());        
    }
    
    public function testErrorOtherPage()
    {
        $this->app['debug'] = false;
        
        $this->app->get('/foobar', function() {
            throw new \Exception('foobarcrux');
        });
        
        $client  = $this->createClient();
        
        $crawler = $client->request('GET', '/foobar');
        
        $this->assertFalse($client->getResponse()->isOk());
        $this->assertNotContains('foobarcrux', $client->getResponse()->getContent());
    }
}
