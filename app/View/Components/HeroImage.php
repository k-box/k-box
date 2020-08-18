<?php

namespace KBox\View\Components;

use Illuminate\View\Component;
use KBox\Appearance\HeroPicture;

/**
 * K-Box Hero image component.
 *
 * Show a picture that is suitable
 * for login and registration screens
 */
class HeroImage extends Component
{
    private $pictureInstance;

    /**
     * The picture to show
     *
     * @var HeroPicture
     */
    public $picture = null;
    
    /**
     * The background solid color
     *
     * @var string
     */
    public $fillColor = null;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->pictureInstance = new HeroPicture(config('appearance.picture'));
        $this->picture = $this->pictureInstance->url();
        $this->fillColor = config('appearance.color');
    }

    public function hasPicture()
    {
        return ! is_null($this->picture) && $this->pictureInstance->exists();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.hero-image');
    }
}
