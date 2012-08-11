<?php

use Symfony\Component\HttpFoundation\RedirectResponse;
use Kors\Contact\Event\ContactEvent,
    Kors\Contact\Event\ContactEvents,
    Kors\Contact\Form\ContactType,
    Kors\Contact\Model\ContactMessage
    ;
    
use Symfony\Component\HttpFoundation\Request;

use Silex\Application;

/**
 * @var Silex\Application $app
 */
$app->get('/', function(Application $app) {
    return $app['twig']->render('home.html.twig');
})->bind('home');

$app->get('/about', function(Application $app) {
    return $app['twig']->render('about.html.twig');
})->bind('about');

$app->match('/contact', function(Request $request, Application $app) {
    $message = new ContactMessage();
    $form = $app['form.factory']->create(new ContactType(), $message);
    if ('POST' === $request->getMethod()) {
        $form->bindRequest($request);

        if ($form->isValid()) {
            $app['dispatcher']->dispatch(ContactEvents::SUBMIT, new ContactEvent($message));
            $app['session']->getFlashBag()->add('success', 'Message successfully send');
            return new RedirectResponse($app['url_generator']->generate('contact'));
        }
    }

    return $app['twig']->render('contact.html.twig', array(
        'form' 		=> $form->createView(),
        'action'	=> $app['url_generator']->generate('contact'),
    ));
})->method('GET|POST')
  ->bind('contact');
  
