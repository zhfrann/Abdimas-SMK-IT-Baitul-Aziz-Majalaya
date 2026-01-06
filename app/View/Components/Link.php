<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Link extends Component
{

    public $title;
    public $text;
    public $link;
    /**
     * Create a new component instance.
     */
    public function __construct($title, $text, $link)
    {
        $this->title = $title;
        $this->text = $text;
        $this->link = $link;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.link');
    }
}
