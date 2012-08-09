<?php

namespace Kors\Contact\Event;

use Kors\Contact\Model\ContactMessage;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event passed to listeners in case of a command event
 */
class ContactEvent extends Event
{
    /**
     * @var ContactMessage
     */
    protected $message;

    /**
     * Ctor
     *
     * @param ContactMessage $message
     */
    public function __construct(ContactMessage $message)
    {
        $this->message = $message;
    }

    /**
     * @return ContactMessage
     */
    public function getMessage()
    {
        return $this->message;
    }
}
