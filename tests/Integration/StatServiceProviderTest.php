<?php

namespace SchulzeFelix\Stat\Tests\Integration;

use SchulzeFelix\Stat\Exceptions\InvalidConfiguration;

class StatServiceProviderTest extends TestCase
{

    /** @test */
    public function it_will_throw_an_exception_if_no_key_is_set()
    {
        $this->app['config']->set('laravel-stat-search-analytics.key', '');

        $this->setExpectedException(InvalidConfiguration::class);

        \Stat::projects()->list();
    }
}
