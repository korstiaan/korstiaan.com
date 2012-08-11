<?php

namespace Kors\Contact\Tests;

use Kors\Tests\SpoolStub;

use Kors\Contact\Event\ContactEvent;

use Kors\Contact\Listener\MailListener;

use Kors\Contact\Model\ContactMessage;

class MailerTest extends \PHPUnit_Framework_TestCase
{
    protected $swift;
    
    public function setUp()
    {
        if (!class_exists('\Swift_Mailer')) {
            $this->markTestSkipped('Swift mailer not found');
        }   
    }
    
    public function testSwiftIsCalled()
    {
        $swift  = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()
            ->getMock(); 
            
        $twig   = $this->getMock('\Twig_Environment');
        
        $mailer = new MailListener($swift, $twig, array(
        	'from'    => 'foo@example.com',
            'to'      => array('foo@example.com'),
            'subject' => 'Bar',
        
            'html_template'  => '',
        	'plain_template' => '',
        ));      
        $event  = new ContactEvent(new ContactMessage());
        
        $swift
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf('\Swift_Message'));
        
        $mailer->mailMessage($event);
        
    }
    
    public function testTemplates()
    {
        $swift  = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()
            ->getMock();
            
        $twig   = new \Twig_Environment(
            new \Twig_Loader_Filesystem(array(__DIR__.'/Fixtures')),
            array('auto_reload' => true));
            
        $mailer = new MailListener($swift, $twig, array(
        	'from'    => 'foo@example.com',
            'to'      => array('foo@example.com'),
            'subject' => 'Bar',
        
            'html_template'  => 'html.html.twig',
        	'plain_template' => 'plain.txt.twig',
        ));
        
        $contact = new ContactMessage();
        $contact->setName('name');
        $contact->setSubject('subject');
        
        $event   = new ContactEvent($contact);
             
        $msg  = $mailer->mailMessage($event);
        
        $this->assertEquals('subject',$msg->getBody());
        $html = null;
        foreach ($msg->getChildren() as $child) {
            if ('text/html' === $child->getContentType()) {
                $html = $child;
                break;
            }
        }
        $this->assertInstanceOf('\Swift_MimePart', $html);
        $this->assertEquals('name',$html->getBody());
    }    
}
