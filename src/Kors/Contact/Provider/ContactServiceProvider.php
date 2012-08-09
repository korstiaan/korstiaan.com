<?php

namespace Kors\Contact\Provider;

use Kors\Contact\Listener\MailListener;

use Silex\Application,
    Silex\ServiceProviderInterface
;

/**
 * Registers contact services into given silex app 
 */
class ContactServiceProvider implements ServiceProviderInterface
{
    /**
     * Defines the mail subscriber service and adds it to the `contact.subscribers` param
     *
     * (non-PHPdoc)
     * @see Silex.ServiceProviderInterface::register()
     */
    public function register(Application $app)
    {
        $app['contact.mail_subscriber'] = $app->share(function() use ($app) {
            return new MailListener($app['mailer'], $app['twig'], $app['contact.options']);
        });

        $app['contact.subscribers'] = function() use ($app) {
            return array($app['contact.mail_subscriber']);
        };
    }

    /**
     * Adds defined `contact.subscribers` to the dispatcher
     *
     * (non-PHPdoc)
     * @see Silex.ServiceProviderInterface::boot()
     */
    public function boot(Application $app)
    {
        foreach ($app['contact.subscribers'] as $subscriber) {
            $app['dispatcher']->addSubscriber($subscriber);
        }
    }
}
