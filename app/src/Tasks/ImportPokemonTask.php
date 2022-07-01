<?php

namespace App\Tasks;

use App\Model\AssetListings\Assets\Asset;
use App\Model\Pokemon\Pokemon;
use App\Model\Pokemon\PokemonAbility;
use App\Model\Pokemon\PokemonSpecies;
use App\Model\Pokemon\PokemonType;
use SilverStripe\Dev\BuildTask;

class ImportPokemonTask extends BuildTask
{
    private static $segment = 'ImportPokemonTask';

    protected $title = 'Import Pokemon Task';

    protected $description = 'Import 50 Pokemon at a time';

    public function run($request)
    {
        $allPokemon = Pokemon::get();
        foreach($allPokemon as $pokemon) {
            $pokemon->delete();
        }

        $allTypes = PokemonType::get();
        foreach($allTypes as $type) {
            $type->delete();
        }

        $allAbilities = PokemonAbility::get();
        foreach($allAbilities as $ability) {
            $ability->delete();
        }

        $base = "https://pokeapi.co/api/v2/pokemon/";
        $speciesbase = "https://pokeapi.co/api/v2/pokemon-species/";

        $this->importPokemon();
    }

    public function import50Pokemon() {

        global $base;
        global $speciesbase;

        $currentStep = 1;
        $offset = $currentStep * 50;
        $results = [];

        $id = 1;

        $pokemonImportLimit = 50;
        $pokemonCount = 0;

        while ($pokemonCount <= $pokemonImportLimit) {

            $pokemon = Pokemon::create();

            $data = json_decode(file_get_contents($base.$id.'/'));
            $speciesdata = json_decode(file_get_contents($speciesbase.$id.'/'));

            if ($data->id) {

                // Base stats
                $pokemon->PokemonID = $data->id;
                $pokemon->Name = ucfirst($data->name);
                $pokemon->Order = $data->order;
                $pokemon->Weight = $data->weight;
                $pokemon->Height = $data->height;
                $pokemon->Species = $speciesdata->name;

                $types = $data->types;
                $abilities = $data->abilities;

                // Abilities
                foreach($abilities as $ability) {
                    $pokemonAbilities = $pokemon->Abilities();
                    $pokemonAbility = PokemonAbility::create();
                    $pokemonAbility->Name = ucfirst($ability->ability->name);
                    $pokemonAbilities->add($pokemonAbility);
                }

                // Types
                foreach($types as $type) {
                    $pokemonTypes = $pokemon->Types();
                    $typesArray = $pokemonTypes->toArray();

                    if (in_array($type->type->name, $typesArray)) {
                        return;
                    }

                    $pokemonType = PokemonType::create();
                    $pokemonType->Name = ucfirst($type->type->name);
                    $pokemonTypes->add($pokemonType);
                }

                //Species
                $pokemonSpecies = PokemonSpecies::create();
                $pokemonSpecies->Name = ucfirst($speciesdata->name);
                $pokemonSpecies->SpeciesID = $speciesdata->id;
                $pokemonSpecies->PokemonID = $pokemon->ID;
                if ($speciesdata->is_baby) $pokemonSpecies->IsBaby = true;
                if ($speciesdata->is_legendary) $pokemonSpecies->IsLegendary = true;
                if ($speciesdata->is_mythical) $pokemonSpecies->IsMythical = true;


                $pokemonSpecies->write();
                $pokemon->SpeciesID = $pokemonSpecies->ID;
                $pokemon->write();

                $id++;
                $pokemonCount++;

            } else {
                die('No more Pokemon to add');
            }
        }

        die('Pokemon added');
    }


}
