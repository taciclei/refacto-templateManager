<?php

namespace Exemple;

use Entity\Quote;
use Entity\Site;
use Entity\Template;
use Entity\User;
use Repository\DestinationRepository;
use TemplateManager;
use Repository\SiteRepository;

require_once __DIR__ . '/../vendor/autoload.php';

$faker = \Faker\Factory::create();

$template = new Template(
    1,
    'Votre livraison à <p>{{destination.countryName}}</p>',
    "
Bonjour {{user.firstname|capitalize}},

Merci de nous avoir contacté pour votre livraison à <p>{{destination.countryName}}</p>

Bien cordialement,

L'équipe Convelio.com
");

$templateManager = new TemplateManager();

$quote = new Quote($faker->randomNumber(), $faker->randomNumber(), $faker->randomNumber(), $faker->date());

$message = $templateManager->getTemplateComputed(
    $template,
    [
        'quote' => $quote,
        'destination' => DestinationRepository::getInstance()->getById($quote->destinationId),
        'site' => new Site($faker->randomNumber(), $faker->url),
        'user' => new User($faker->randomNumber(), $faker->firstName, $faker->lastName, $faker->email),
    ]
);

echo $message->subject . "\n" . $message->content;
