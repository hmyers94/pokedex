<?php

namespace App\Admin\Pokemon;

use App\Model\Pokemon\Pokemon;
use SilverStripe\Admin\ModelAdmin;

class PokemonAdmin extends ModelAdmin
{
    private static $managed_models = [
        Pokemon::class,
    ];

    private static $menu_title = 'Pokemon List';

    private static $url_segment = 'pokemon-list';

}
