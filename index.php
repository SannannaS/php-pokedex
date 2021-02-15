<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);


//get information from API
    function getPokemon(string $input): array
    {
        return json_decode(file_get_contents("https://pokeapi.co/api/v2/pokemon/" . $input), true);
    }

    //set default pokemon before the user inputs theirs
    if (empty($_GET["pokemon-name"])) {
        $input = "bulbasaur";
    } else {
        $input = $_GET['pokemon-name'];
    }
//declaring all variables/objects in php
    $pokemon = getPokemon($input);
    $pokeName = $pokemon['name'];
    $id = $pokemon['id'];
    $weight = $pokemon["weight"];
    $height = $pokemon["height"];

    //function to show the official artwork
   function bigPicture(array $pokemon)
   {
       $imgUrl = $pokemon["sprites"]["other"]["official-artwork"]["front_default"];
       echo '<img id="poke-display__img__front" class="poke-front-image" src="'. $imgUrl .'" alt="pokemon">';
   }
//function to show the evolutions with the sprites
    function showEvolutions(array $pokemon)
    {
        $species = json_decode(file_get_contents($pokemon['species']['url']),true);
        $evoChain = json_decode(file_get_contents($species['evolution_chain']['url']), true);
        $basePokemon = $evoChain['chain']['species']['name'];


        $evolution1Counter = count($evoChain['chain']['evolves_to']);
        if ($evolution1Counter!== 0) {
            $evolution2Counter = count($evoChain['chain']['evolves_to'][0]['evolves_to']);
            $evolve1 = $evoChain['chain']['evolves_to'][0]['species']['name'];
            function showEvoPicture($evoName)
            {
                $pokemonToDisplay = getPokemon($evoName);
                $src = $pokemonToDisplay['sprites']['front_default'];
                echo "<a href='http://localhost:63342/php-pokedex/index.php?pokemon-name=".$evoName."&submit=Search'>
                    <img src='" . $src . "' class='poke-front-sprite' alt='sprite'>
                    </a>";
            }

            if ($evolution1Counter === 1 && $evolution2Counter === 0) {
                showEvoPicture($basePokemon);
                showEvoPicture($evolve1);
            }

            else if ($evolution2Counter === 1) {
                $evolve2 = $evoChain['chain']['evolves_to'][0]['evolves_to'][0]['species']['name'];
                showEvoPicture($basePokemon);
                showEvoPicture($evolve1);
                showEvoPicture($evolve2);
            }

            else if ($evolution1Counter > 1) {
                $multiEvos = $evoChain["chain"]["evolves_to"];
                foreach ($multiEvos as $singleEvo){
                    $pokeName = $singleEvo["species"]["name"];
                    showEvoPicture($pokeName);
                }
            }
        }
        else
        {
            echo "";
        }

    }





//function to show the moves
    function showMoves(array $pokemon):array
    {
        $arrayMoves = $pokemon["moves"];
        shuffle($arrayMoves);
        $moves = array_slice($arrayMoves,0,4);

        foreach ($moves as $move) {
            echo "<p>" . $move["move"]["name"] . "</p>";
        }
        return $moves;
    }


?>


<html lang="eng">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="index.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <title>Pokedex</title>
</head>
<body>


<div class="pokedex">
    <div class="left-container" id="left-container__cosmetics">
        <div class="left-container__top-section">
            <div class="top-section__green"></div>
            <div class="top-section__small-buttons">
                <div class="top-section__red" id="teamRed"></div>
                <div class="top-section__yellow" id="teamYellow"></div>
                <div class="top-section__blue" id="teamBlue"></div>
            </div>
        </div>
        <div class="left-container__main-section-container">
            <div class="left-container__main-section">
                <div class="main-section__white">
                    <div class="main-section__black">
                        <div class="main-screen hide">
                            <div class="screen__header">
                                <span class="poke-name" id="poke-display__name"><?php echo $pokeName; ?></span>
                                <span class="poke-id" id="poke-display__id"><?php echo $id; ?></span>
                            </div>
                            <div class="screen__image">
                                <span><?php bigPicture($pokemon); ?></span>
                            </div>
                            <div class="screen__description">
                                <div class="stats__types">
                                    <span class="poke-type-one" id="poke__type__one"></span>
                                    <span class="poke-type-two" id="poke__type__two"></span>
                                </div>
                                <div class="screen__stats">
                                    <p class="stats__weight">
weight: <span class="poke-weight" id="poke-display__weight"><?php echo $weight; ?></span>
                                    </p>
                                    <p class="stats__height">
height: <span class="poke-height" id="poke-display__height"><?php echo $height; ?></span>
                                    </p>
                                </div>
                            </div>
                            <div class="screen__evolutions">
                                <?php showEvolutions($pokemon);  ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="left-container__controllers">
                    <div class="controllers__d-pad">
                        <div class="d-pad__cell top"></div>
                        <div class="d-pad__cell left"></div>
                        <div class="d-pad__cell middle"></div>
                        <div class="d-pad__cell right"></div>
                        <div class="d-pad__cell bottom"></div>
                    </div>
                    <div class="controllers__buttons">
                        <div class="buttons__button" id="button-B">B</div>
                        <div class="buttons__button" id="button-A">A</div>
                    </div>
                </div>
            </div>
            <div class="left-container__right">
                <div class="left-container__hinge" id = "left-container__hinge-top"></div>
                <div class="left-container__hinge" id = "left-container__hinge-bottom"></div>
            </div>
        </div>
    </div>
    <div class="right-container" id="right-container__cosmetics">
        <div class="right-container__black">
            <div class="searchbox">
                <form method="get">
                    <label for="pokemon-name">Name:</label>
                    <input id="pokemon-name" type="text" name="pokemon-name" value="">
                    <br><br>
                    <input class="left-button" id="search-button" type="submit" name="submit" value="Search">
                </form>
            </div>
            <div class="movesBox">
                <p>Moves</p>
                <div class="move"> <?php showMoves($pokemon); ?></div>

            </div>
        </div>

        <div class="right-container__buttons">
            <div class="left-button" id="search-button">Search</div>
            <div class="right-button" id="reset-button">Reset</div>
        </div>
    </div>
</div>
</body>
</html>