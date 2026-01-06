<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ComponentBreadcrumb extends Component
{
    public $item;
    public $text;
    public $link;
    /**
     * Create a new component instance.
     */
    public function __construct($item, $text, $link)
    {
        $this->item = $item;
        $this->text = $text;
        $this->link = $link;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.component-breadcrumb');
    }
}
