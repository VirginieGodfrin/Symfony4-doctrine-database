<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Service\MarkdownHelper;
use Symfony\Component\DependencyInjection\ServiceSubscriberInterface;
use Psr\Container\ContainerInterface;

// Hello Twig extension, we need you to create a markdown filter in twig with MarkDownHelper service.
// php bin/console make:twig-extension
// The AppExtenstion will hold all the new filter
// Here you can custom operators or tests

// Symfony's container does not instantiate this service until and unless you actually use it during a request.
// For example, if we try to use MarkdownHelper in a controller, 
// the container will, of course, instantiate MarkdownHelper and pass it to us.
// Twig extension are always instantiated each time we use twig
// But, in order to instantiate AppExtension, Symfony's container first needs to instantiate MarkdownHelper.
// We instanciated MarkdownHelper service each time a request use twig, if Twig extension grows it's could be problematic
// This is why we creating a Service Subscriber
// First: implement class with ServiceSubscriberInterface

class AppExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    private $container;

    // Thrid: modify the constructor, now we use a new "mini" service container
    // When you use ServiceSubscriberInterface symfony pass container to your contructor
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFilters(): array
    {
        // cached_markdown: the filter name
        // is_safe set to html: It tells Twig that the result of this filter should not be escaped through htmlentities().
        return [
            new TwigFilter('cached_markdown', [$this, 'processMarkdown'], ['is_safe' => ['html']]),
        ];
    }

    public function processMarkdown($value)
    {
        // use the markdowHelper service with dependencie injection
        // Fith: call MarkdownHelper with container
        return $this->container
            // ->get(MarkdownHelper::class)
            ->get('foo')
            ->parse($value);
    }

    // Second: call the getSubscribedServices
    public static function getSubscribedServices()
    {
        // Four: To tell Symfony which services you want in your mini-container use getSubscribedServices
        // Symfony autowire & make this service public
        return [
            // MarkdownHelper::class,
            'foo' => MarkdownHelper::class,
        ];
    }

    // ccl: Symfony is passing us a container that holds the MarkdownHelper. 
    // But, the key difference is that, thanks to this trick, 
    // the MarkdownHelper service is not instantiated until and unless we fetch it out of this container.
    // le service MarkdownHelper n'est instancié que lorsque nous le récupérions dans ce conteneur.
    // It would still mean that the MarkdownHelper service is autowired into the mini-container,
    // You can use dependencie injection to get services anywhere you want in symfony.
    // But in Twig extensions, event subscribers and security voters use ServiceSubscriberInterface and ContainerInterface
    // to call service and to avoid a performance hit.
}
