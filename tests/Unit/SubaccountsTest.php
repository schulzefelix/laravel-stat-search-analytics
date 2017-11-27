<?php

namespace SchulzeFelix\Stat\Tests\Unit;

use Mockery;
use Carbon\Carbon;
use SchulzeFelix\Stat\Stat;
use PHPUnit\Framework\TestCase;
use SchulzeFelix\Stat\StatClient;
use Illuminate\Support\Collection;
use SchulzeFelix\Stat\Objects\StatSubAccount;

class SubaccountsTest extends TestCase
{
    protected $statClient;

    /** @var \SchulzeFelix\Stat\Stat */
    protected $stat;

    public function setUp()
    {
        $this->statClient = Mockery::mock(StatClient::class);

        $this->stat = new Stat($this->statClient);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    /** @test */
    public function it_can_list_subaccounts()
    {
        $expectedArguments = [
            'subaccounts/list', [],
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => '200',
                'User' => [
                    [
                        'Id' => '3008',
                        'Login' => 'user3008',
                        'ApiKey' => '540D245230F1638E3F6542F465232534',
                        'CreatedAt' => '2011-01-25',
                    ],
                    [
                        'Id' => '3009',
                        'Login' => 'user3009',
                        'ApiKey' => '697D211110F1611A3F7657F460987222',
                        'CreatedAt' => '2011-01-26',
                    ],
                ],
            ]]);

        $response = $this->stat->subaccounts()->list();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertInstanceOf(StatSubAccount::class, $response->first());
        $this->assertInstanceOf(Carbon::class, $response->first()->created_at);
        $this->assertEquals(2, $response->count());

        $this->assertArrayHasKey('id', $response->first());
        $this->assertArrayHasKey('login', $response->first());
        $this->assertArrayHasKey('api_key', $response->first());
        $this->assertArrayHasKey('created_at', $response->first());
    }
}
