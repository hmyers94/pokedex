<?php

namespace App\Model\Pokemon;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;

class PokemonAbility extends DataObject
{
    private static $table_name = 'PokemonAbilities';

    private static $db = [
        'Name'      => 'Varchar',
    ];

    private static $has_one = [
        'Pokemon' => Pokemon::class,
    ];

    private static $default_sort = 'Name';

    private static $singular_name = 'Pokemon ability';

    private static $plural_name = 'Pokemon abilities';

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
                $fields->addFieldsToTab(
                    'Root.Main',
                    [
                        TextField::create('ID', 'ID'),
                        TextField::create('Name', 'Name'),
                    ]
                );
            }
        );

        return parent::getCMSFields();
    }
}
