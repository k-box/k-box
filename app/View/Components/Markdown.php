<?php

namespace KBox\View\Components;

use Illuminate\View\Component;
use League\CommonMark\MarkdownConverterInterface;

class Markdown extends Component
{
    /**
     * Value attribute
     *
     * @var string
     */
    public $value;

    protected $converter;
    
    public function __construct(MarkdownConverterInterface $converter, $value = null)
    {
        $this->value = $value;
        $this->converter = $converter;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.markdown');
    }

    /**
     * Convert markdown content to html
     *
     * @param string $text the markdown content
     * @return string html
     */
    public function convert($text)
    {
        return $this->converter->convertToHtml($text);
    }
}
