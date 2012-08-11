<?php
use Silex\Provider\SessionServiceProvider;
use Kors\Contact\Provider\ContactServiceProvider;
use SilexExtension\MarkdownExtension;

use Silex\Provider\SwiftmailerServiceProvider;
use Assetic\Filter\Yui\JsCompressorFilter;
use Assetic\Filter\Yui\CssCompressorFilter;
use Assetic\Filter\CssRewriteFilter;
use Knp\Provider\ConsoleServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\FormServiceProvider;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\Voter\UriVoter;
use Assetic\Filter\LessphpFilter;
use SilexExtension\AsseticExtension;
use Silex\Application;
use Knp\Menu\Silex\KnpMenuServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TwigServiceProvider;

require __DIR__.'/../vendor/autoload.php';

$app = new Application();

false === getenv('K_TEST') ? require __DIR__.'/config.php' : require __DIR__.'/config.dist.php';

/**
 * Application related dirs
 */
$app['dir.vendor'] = __DIR__.'/../vendor';
$app['dir.web']    = __DIR__.'/../web';
$app['dir.app']    = __DIR__.'/../app';

/**
 * Swift mailer
 */
$app->register(
    new SwiftmailerServiceProvider(),
    isset($app['swiftmailer.options']) ? array('swiftmailer.options' => $app['swiftmailer.options']) : array()
);

/**
 * Form related and dependant services
 */
$app->register(new SessionServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new TranslationServiceProvider());
$app->register(new FormServiceProvider());

/**
 * Twig related
 */
$app->register(new TwigServiceProvider(), array(
    'twig.path'           => array(
        __DIR__.'/../views',
    ),
    'twig.form.templates' => array(
        'form_div_layout.html.twig',
        'Form/form_div_layout.html.twig'
    ),
));

$app['twig.options'] = function() use ($app) {
    return array(
        'cache'       => $app['dir.cache'].'/twig',
        'auto_reload' => isset($app['debug']) ? (bool) $app['debug'] : false,
    );
};

/**
 * Assetic
 */
$app->register(new AsseticExtension(), array(
    'assetic.path_to_web' => __DIR__.'/../web',
    'assetic.filters'     => $app->protect(function($fm) use ($app) {
        $fm->set('less',       new LessphpFilter());
        $fm->set('cssrewrite', new CssRewriteFilter());
        $fm->set('yui_css',    new CssCompressorFilter("{$app['dir.app']}/java/yuicompressor-2.4.6.jar"));
        $fm->set('yui_js',     new JsCompressorFilter("{$app['dir.app']}/java/yuicompressor-2.4.6.jar"));
    }),
));

/**
 * Menu related
 */
$app->register(new UrlGeneratorServiceProvider());
$app->register(new KnpMenuServiceProvider());

$app['main_menu'] = function(Application $app) {

    $menu = $app['knp_menu.factory']->createItem('root');

    $menu->addChild('Home',    array('route' => 'home'));
    $menu->addChild('About',   array('route' => 'about'));
    $menu->addChild('Contact', array('route' => 'contact'));

    return $menu;
};

$app['knp_menu.matcher.configure'] = $app->protect(function (Matcher $matcher) use ($app) {
    $matcher->addVoter($app['knp_menu.voter']);
});

$app['knp_menu.menus'] = array('main_menu' => 'main_menu');

$app['knp_menu.voter'] = $app->share(function (Application $app) {
    return new UriVoter($app['request']->getRequestUri());
});

/**
 * Contact form relate
 */
$app->register(new ContactServiceProvider());

/**
 * Console
 */
$app->register(new ConsoleServiceProvider(), array(
    'console.name'              => $app['name'],
    'console.version'           => $app['version'],
    'console.project_directory' => __DIR__.'/..'
));

/**
 * md
 */
$app->register(new MarkdownExtension());

return $app;
