<?php

namespace SchulzeFelix\Stat\Tests\Integration;

use SchulzeFelix\Stat\StatFacade;
use SchulzeFelix\Stat\StatServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            StatServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Stat' => StatFacade::class,
        ];
    }
}
