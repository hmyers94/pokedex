<?php

namespace App\Model\Pokemon;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;

class PokemonSpecies extends DataObject
{
    private static $table_name = 'PokemonSpecies';

    private static $db = [
        'SpeciesID'         => 'Int',
        'Name'              => 'Varchar',
        'Order'             => 'Varchar',
        'IsBaby'            => 'Boolean',
        'IsLegendary'       => 'Boolean',
        'IsMythical'        => 'Boolean',
    ];

    private static $has_one = [
        'Pokemon' => Pokemon::class,
    ];

    private static $default_sort = 'Name';

    private static $singular_name = 'Pokemon species';

    private static $plural_name = 'Pokemon species';

    private static $summary_fields = [
        'Order',
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
                        TextField::create('Name', 'Name'),
                        CheckboxField::create('IsBaby'),
                        CheckboxField::create('IsLegendary'),
                        CheckboxField::create('IsMythical'),
                    ]
                );
            }
        );

        return parent::getCMSFields();
    }
}
