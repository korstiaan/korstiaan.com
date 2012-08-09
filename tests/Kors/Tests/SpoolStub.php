<?php

namespace Kors\Tests;

class SpoolStub implements \Swift_Spool
{
    protected $messages = array();

    public function getMessages()
    {
        return $this->messages;
    }

    public function start()
    {
    }

    public function stop()
    {
    }

    public function isStarted()
    {
        return count($this->messages) > 0;
    }

    public function queueMessage(\Swift_Mime_Message $message)
    {
        $this->messages[] = $message;
    }

    public function flushQueue(\Swift_Transport $transport, &$failedRecipients = null)
    {
    }
}
