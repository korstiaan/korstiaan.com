<?php

namespace Kors\Com\Tests;

use Kors\Tests\SpoolStub;

use Symfony\Component\DomCrawler\Crawler;

use Silex\WebTestCase;

class FunctionalTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__.'/../../../../app/bootstrap.php';        
        require __DIR__.'/../../../../app/controllers.php';
        $app['debug'] = true;
        $app['session.test'] = true;
        unset($app['exception_handler']);
        
        $app['swiftmailer.spool'] = $app->share(function(){
            return new SpoolStub();
        });        
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
        
        $links = $crawler->filter('.navbar .nav a');
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
        $client  = $this->createClient();
        $client->followRedirects();
        
        $crawler = $client->request('GET', '/contact');
        $this->assertTrue($client->getResponse()->isOk());

        $form    = $crawler->selectButton('Save')->form();
        
        $this->assertEquals(0, count($this->app['swiftmailer.spool']->getMessages()));
        
        $success = $client->submit($form, array(
            'contact_type[name]'    => 'Foo Bar',
            'contact_type[email]'   => 'foo@example.com',
        	'contact_type[subject]' => 'Foo bar',
        	'contact_type[content]' => 'Foo bar lorem',
        ));
        
        $this->assertRegExp('/Message successfully send/', $success->filter('.alert-success')->text());
        $this->assertEquals(1, count($this->app['swiftmailer.spool']->getMessages()));
        
        $error = $client->submit($form, array(
            'contact_type[name]'    => 'Foo Bar',
            'contact_type[email]'   => 'foo',
        	'contact_type[subject]' => 'Foo bar',
        	'contact_type[content]' => 'Foo bar lorem',
        ));
        
        $this->assertEquals('This value is not a valid email address.', $error->filter('#contact_type_email')->nextAll()->eq(0)->filter('li')->text());
                 
    }
}
