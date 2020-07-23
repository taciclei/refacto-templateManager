<?php

namespace test;

use Context\ApplicationContext;
use Entity\Quote;
use Entity\Site;
use Entity\Template;
use Entity\User;
use Repository\DestinationRepository;


class TemplateManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Init the mocks
     */
    public function setUp()
    {
    }

    /**
     * Closes the mocks
     */
    public function tearDown()
    {
    }

    /**
     * @test
     */
    public function test()
    {
        $faker = \Faker\Factory::create();

        $destinationId                  = $faker->randomNumber();
        $expectedDestination = DestinationRepository::getInstance()->getById($destinationId);
        $expectedUser        = ApplicationContext::getInstance()->getCurrentUser();

        $quote = new Quote($faker->randomNumber(), $faker->randomNumber(), $destinationId, $faker->date());

        $template = new Template(
            1,
            'Votre livraison à <p>{{destination.countryName}}</p>',
            "
Bonjour {{user.firstname|capitalize}},

Merci de nous avoir contacté pour votre livraison à <p>{{destination.countryName}}</p>.

Bien cordialement,

L'équipe Convelio.com
");
        $templateManager = new \TemplateManager();

        $message = $templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $quote,
                'destination' => DestinationRepository::getInstance()->getById($destinationId),
                'user' => $expectedUser,
            ]
        );

        $this->assertEquals('Votre livraison à <p>' . $expectedDestination->countryName .'</p>', $message->subject);
        $this->assertEquals("
Bonjour " . $expectedUser->firstname . ",

Merci de nous avoir contacté pour votre livraison à <p>" . $expectedDestination->countryName . "</p>.

Bien cordialement,

L'équipe Convelio.com
", $message->content);
    }
}
