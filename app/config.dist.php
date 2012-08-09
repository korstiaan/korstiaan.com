<?php

$app['name']      = 'Example.com';
$app['version']   = '1.0.0';

//$app['debug']     = true;

$app['dir.cache'] = __DIR__.'/../app/cache';

$app['assetic.options'] = array(
    'debug'            => true,
    'auto_dump_assets' => false,
);

$app['swiftmailer.options'] = array(
    'host' => 'localhost',
    'port' => 25,
);

$app['contact.options'] = array(
    'from'           => 'foo@example.com',
    'to'             => array('bar@example.com'),
    'subject'        => 'Contact mail',
    'html_template'  => 'Mail/contact.html.twig', 
	'plain_template' => 'Mail/contact.plain.twig',
);
