<?php

namespace Kors\Contact\Tests;

use Kors\Contact\Model\ContactMessage;

class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $msg = new ContactMessage();
        $this->assertNull($msg->getName());
        $msg->setName('foo');
        $this->assertEquals('foo', $msg->getName());
    }
    
    public function testEmail()
    {
        $msg = new ContactMessage();
        $this->assertNull($msg->getEmail());
        $msg->setEmail('foo@example.com');
        $this->assertEquals('foo@example.com', $msg->getEmail());
    }
    
    public function testSubject()
    {
        $msg = new ContactMessage();
        $this->assertNull($msg->getSubject());
        $msg->setSubject('Foo');
        $this->assertEquals('Foo', $msg->getSubject());
    }
    
    public function testContent()
    {
        $msg = new ContactMessage();
        $this->assertNull($msg->getContent());
        $msg->setContent('Foo');
        $this->assertEquals('Foo', $msg->getContent());
    }
}
