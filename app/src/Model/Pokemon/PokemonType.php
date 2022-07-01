<?php

namespace App\Model\Pokemon;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;

class PokemonType extends DataObject
{
    private static $table_name = 'PokemonTypes';

    private static $db = [
        'Sort'      => 'Int',
        'Order'     => 'Int',
        'Name'      => 'Varchar',
    ];

    private static $has_one = [
        'Pokemon' => Pokemon::class,
    ];

    private static $default_sort = 'Name';

    private static $singular_name = 'Pokemon type';

    private static $plural_name = 'Pokemon types';

    private static $summary_fields = [
        'Name',
    ];

    private $extensions = [
        Versioned::class,
    ];

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(
            function (FieldList $fields) {
                $fields->removeByName([
                        'Sort'
                ]
                );

                $fields->addFieldsToTab(
                    'Root.Main',
                    [
                        TextField::create('Name', 'Name'),
                    ]
                );
            }
        );

        return parent::getCMSFields();
    }
}
