<?php

namespace Kors\Contact\Listener;

use Symfony\Component\OptionsResolver\Options,
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    Symfony\Component\OptionsResolver\OptionsResolver
    ;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Kors\Contact\Event\ContactEvent,
    Kors\Contact\Event\ContactEvents
    ;

/**
 * Listener for contact submit event
 */
class MailListener implements EventSubscriberInterface
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;
    
    /**
     * @var \Twig_Environment
     */
    protected $twig;
    
    /**
     * @var array
     */
    protected $options;

    /**
     * Ctor
     *
     * @param \Swift_Mailer     $mailer
     * @param \Twig_Environment $twig
     * @param array             $options
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, array $options)
    {
        $this->setOptions(new OptionsResolver(), $options);

        $this->mailer     = $mailer;
        $this->twig     = $twig;
    }

    /**
     * Validates and processes injected options
     *
     * @param OptionsResolverInterface $resolver
     */
    protected function setOptions(OptionsResolverInterface $resolver, array $options)
    {
        $resolver
            ->setRequired(array('to', 'plain_template', 'html_template'))
            ->setDefaults(array(
                'from'    => function (Options $options) {
                    return $options->get('to');
                },
                'subject' => function (Options $options) {
                    return 'Contact mail';
                },
            ))
            ->setAllowedTypes(array(
                'to'	  => 'array',
                'from'    => array('array','string'),
                'subject' => 'string',
            
                'html_template'  => 'string',
                'plain_template' => 'string',
            ))
        ;

        $this->options = $resolver->resolve($options);
    }

    /**
     * Mails message of given event with objects options
     *
     * @param  ContactEvent  $event
     * @return Swift_Message		the msg being send
     */
    public function mailMessage(ContactEvent $event)
    {
        $plain = $this->twig->render($this->options['plain_template'], array('message' => $event->getMessage(),));

        $html  = $this->twig->render($this->options['html_template'], array('message' => $event->getMessage(),));

        $mailmsg = \Swift_Message::newInstance()
            ->setSubject($this->options['subject'])
            ->setFrom($this->options['from'])
            ->setTo($this->options['to'])
            ->setBody($plain)
            ->addPart($html, 'text/html')
        ;

        $this->mailer->send($mailmsg);
        return $mailmsg;
    }

    /**
     * Returns the events we're interested in
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            ContactEvents::SUBMIT => 'mailMessage',
        );
    }
}
