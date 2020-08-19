<?php

namespace KBox\View\Components;

use Illuminate\View\Component;
use KBox\Appearance\HeroPicture;
use Illuminate\Support\Facades\Validator;

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
        $this->fillColor = $this->ensureColorIsValid(config('appearance.color'));
    }

    /**
     * Check if a color is an expected value
     *
     * @param string $color
     * @return string
     */
    private function ensureColorIsValid($color)
    {
        if (! $color) {
            return null;
        }

        $validator = Validator::make(['color' => $color], [
            'color' => 'required|regex:/#([a-f0-9]{3}){1,2}\b/i',
        ]);

        if ($validator->fails()) {
            logs()->warning("Specified appearance color [$color] is not valid.");

            return null;
        }
        
        return $color;
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
