<?php

namespace SchulzeFelix\Stat\Tests\Integration;

use Orchestra\Testbench\TestCase as Orchestra;
use SchulzeFelix\Stat\StatFacade;
use SchulzeFelix\Stat\StatServiceProvider;

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