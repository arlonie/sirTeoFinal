<?php

require __DIR__.'/vendor/autoload.php';

use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount('code-crafter-68267-firebase-adminsdk-fhu65-e93e8a398d.json')
    ->withDatabaseUri('https://code-crafter-68267-default-rtdb.firebaseio.com');

$database = $factory->createDatabase();
?>