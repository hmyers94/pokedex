<?php

namespace App\Tasks;

use App\Model\AssetListings\Assets\Asset;
use App\Model\Pokemon\Pokemon;
use App\Model\Pokemon\PokemonAbility;
use App\Model\Pokemon\PokemonSpecies;
use App\Model\Pokemon\PokemonType;
use GuzzleHttp\Client;
use SilverStripe\Dev\BuildTask;

class ImportPokemonTask extends BuildTask
{
    const URL = "https://pokeapi.co/api/v2/pokemon";
    const SpeciesURL = "https://pokeapi.co/api/v2/pokemon-species";

    private static $segment = 'ImportPokemonTask';

    protected $title = 'Import Pokemon Task';

    protected $description = 'Import 50 Pokemon at a time';

    public function run($request)
    {
        $this->deletePokemon();

        $currentStep = 1;
        $offset = 1;
        $limit = 50;

        $results = [];

        $service = new Client(['base_uri' => self::URL]);

        $response = $service->request(
            'GET',
            self::URL . '?offset=' . $offset . '&limit=' . $limit
        );

        if ($response->getStatusCode() == 200) {
            $json = json_decode($response->getBody(), true);

            if (isset($json['results'])) {
                $results = $json['results'];
            }
        }

        if (empty($results) ) {
            die('No more Pokemon to add');
        }

        for ($i = 0; $i < count($results); $i++) {

            $service = new Client(['base_uri' => self::URL]);

            $response = $service->request(
                'GET',
                $results[$i]['url']
            );

            if ($response->getStatusCode() == 200) {
                $result = json_decode($response->getBody(), true);
            }

            if (empty($result) ) {
                die('Invalid pokemon');
            }

            $pokemon = Pokemon::create();

            // Base stats
            $pokemon->PokemonID = $result['id'];
            $pokemon->Name = ucfirst($result['name']);
            $pokemon->Order = $result['order'];
            $pokemon->Weight = $result['weight'];
            $pokemon->Height = $result['height'];

            $abilities = $result['abilities'];

            $types = $result['types'];

            // Abilities
            foreach($abilities as $ability) {
                $pokemonAbilities = $pokemon->Abilities();
                $pokemonAbility = PokemonAbility::create();
                $pokemonAbility->Name = ucfirst($ability['ability']['name']);
                $pokemonAbilities->add($pokemonAbility);
            }

            // Types
            foreach($types as $type) {
                $pokemonTypes = $pokemon->Types();
                $typesArray = $pokemonTypes->toArray();

                if (in_array($type['type']['name'], $typesArray)) {
                    return;
                }

                $pokemonType = PokemonType::create();
                $pokemonType->Name = ucfirst($type['type']['name']);
                $pokemonTypes->add($pokemonType);
            }


            $speciesService = new Client(['base_uri' => self::SpeciesURL]);
            $speciesResult = [];

            $speciesResponse = $speciesService->request(
                'GET',
                $result['species']['url']
            );

            if ($speciesResponse->getStatusCode() == 200) {
                $speciesJson = json_decode($speciesResponse->getBody(), true);



                if (isset($speciesJson)) {
                    $speciesResult = $speciesJson;
                }
            }

            //Species
            $pokemonSpecies = PokemonSpecies::create();
            $pokemonSpecies->Name = ucfirst($speciesResult['name']);
            $pokemonSpecies->SpeciesID = $speciesResult['id'];
            $pokemonSpecies->PokemonID = $pokemon->ID;
            $pokemonSpecies->IsBaby = $speciesResult['is_baby'];
            $pokemonSpecies->IsLegendary = $speciesResult['is_legendary'];
            $pokemonSpecies->IsMythical = $speciesResult['is_mythical'];

            $pokemonSpecies->write();
            $pokemon->SpeciesID = $pokemonSpecies->ID;
            $pokemon->write();

        }

        $currentStep += 1;
        $offset = $currentStep * 50;
        die('Pokemon added');

    }


    public function deletePokemon() {
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
    }
}
