<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Service\MarkdownHelper;

// Hello Twig extension, we need you to create a markdown filter in twig with MarkDownHelper service.
// php bin/console make:twig-extension
// The AppExtenstion will hold all the new filter
// Here you can custom operators or tests

class AppExtension extends AbstractExtension
{
    private $markdownHelper;

    public function __construct(MarkdownHelper $markdownHelper)
    {
        $this->markdownHelper = $markdownHelper;
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
        return $this->markdownHelper->parse($value);
    }
}
