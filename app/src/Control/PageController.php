<?php

namespace App\Control;

use App\Model\Pokemon\Pokemon;
use App\Model\Pokemon\PokemonAbility;
use App\Model\Pokemon\PokemonType;
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\View\Requirements;

class PageController extends ContentController
{
    protected function init()
    {
        parent::init();

        Requirements::set_force_js_to_bottom(true);
        Requirements::themedJavascript('dist/js/app.js');
    }
}
