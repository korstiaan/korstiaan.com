<?php

namespace Kors\Contact\Tests;

use Symfony\Component\Validator\ConstraintValidatorFactory;

use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;

use Symfony\Component\Validator\Mapping\ClassMetadataFactory;

use Symfony\Component\Validator\Validator;

use Kors\Contact\Model\ContactMessage;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
 
    public function testValidator()
    {
        $validator = $this->getValidator();
        $msg = new ContactMessage();
        $msg->setEmail('t@example.com');
        $msg->setContent('Foo');
        $msg->setSubject('Bar');
        $msg->setName('Foo Bar');
        $this->assertEquals(0,count($validator->validate($msg)));        
    }
    
    /**
     * @dataProvider getValidationTests
     */
    public function testValidation($property, $method, $fail, $success)
    {
        
        $validator = $this->getValidator();
        $msg = new ContactMessage();
        $msg->{$method}($fail);
        
        $fails = $validator->validate($msg);
        $this->assertTrue($this->containsViolation($fails, $property));
        
        $msg = new ContactMessage();
        $msg->{$method}($success);
        
        $fails = $validator->validate($msg);
        $this->assertFalse($this->containsViolation($fails, $property));        
    }
    
    public function getValidationTests()
    {
        return array(
            array('email', 	 'setEmail',   'foo', 'foo@example.com'),
            array('email',   'setEmail',   '',    'foo@example.com'),
            array('subject', 'setSubject', '',    'Foo'),
        );
    }
    
    public function containsViolation($fails, $property)
    {
        foreach ($fails as $violation) {
            if ($property == $violation->getPropertyPath()) {
                return true;
            }
        }
        
        return false;
    }
    
    protected function getValidator()
    {
        return new Validator(new ClassMetadataFactory(new StaticMethodLoader()), new ConstraintValidatorFactory());
    }
}
