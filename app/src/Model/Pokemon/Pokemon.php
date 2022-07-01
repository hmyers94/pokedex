<?php

namespace App\Model\Pokemon;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;

class Pokemon extends DataObject
{
    private static $table_name = 'Pokemon';

    private static $db = [
        'PokemonID' => 'Int',
        'Name'      => 'Varchar',
        'Order'     => 'Int',
        'Weight'    => 'Int',
        'Height'    => 'Int',
    ];

    private static $has_one = [
        'Species'        => PokemonSpecies::class,
    ];

    private static $has_many = [
        'Types'          => PokemonType::class,
        'Abilities'      => PokemonAbility::class,
    ];

    private static $owns = [
        'Types',
        'Abilities',
        'Species',
    ];

    private static $default_sort = 'ID';

    private static $singular_name = 'Pokemon';

    private static $plural_name = 'Pokemon';

    private static $summary_fields = [
        'PokemonID',
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
                    'SpeciesID',
                ]);

                $fields->addFieldsToTab(
                    'Root.Main',
                    [
                        TextField::create('PokemonID', 'ID'),
                        TextField::create('Order', 'Order'),
                        TextField::create('Name', 'Name'),
                        TextField::create('Weight', 'Weight'),
                        TextField::create('Height', 'Height'),
                    ]
                );

                $fields->addFieldsToTab(
                    'Root.Species',
                    [
                        TextField::create('SpeciesName', 'Species', $this->Species()->Name),
                        CheckboxField::create('SpeciesIsBaby', 'Is baby', $this->Species()->IsBaby),
                        CheckboxField::create('SpeciesIsLegendary', 'Is legendary', $this->Species()->IsLegendary),
                        CheckboxField::create('SpeciesIsMythical', 'Is mythical', $this->Species()->IsMythical),
                    ]
                );

                $fields->addFieldsToTab(
                    'Root.Types',
                    [
                        GridField::create(
                            'Types',
                            'Types',
                            $this->Types(),
                            GridFieldConfig_RecordEditor::create()
                                ->removeComponentsByType(GridFieldAddNewButton::class))
                    ]
                );

                $fields->addFieldsToTab(
                    'Root.Abilities',
                    [
                        GridField::create(
                            'Abilities',
                            'Abilities',
                            $this->Abilities(),
                            GridFieldConfig_RecordEditor::create()
                                ->removeComponentsByType(GridFieldAddNewButton::class))
                    ]
                );
            }
        );

        return parent::getCMSFields();
    }

}
