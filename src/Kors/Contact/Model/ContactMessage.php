<?php

namespace Kors\Contact\Model;

use Symfony\Component\Validator\Constraints\Email,
    Symfony\Component\Validator\Constraints\NotBlank
    ;

use Symfony\Component\Validator\Mapping\ClassMetadata;

class ContactMessage
{
    protected $name;
    protected $email;
    protected $subject;
    protected $content;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', 	new NotBlank());
        $metadata->addPropertyConstraint('email', 	new NotBlank());
        $metadata->addPropertyConstraint('email', 	new Email());
        $metadata->addPropertyConstraint('subject', new NotBlank());
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }
}
