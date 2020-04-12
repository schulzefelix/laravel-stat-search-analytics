<?php

namespace SchulzeFelix\Stat\Tests\Unit;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;
use SchulzeFelix\Stat\Objects\StatSerpItem;
use SchulzeFelix\Stat\Stat;
use SchulzeFelix\Stat\StatClient;

class SerpsTest extends TestCase
{
    protected $statClient;

    /** @var \SchulzeFelix\Stat\Stat */
    protected $stat;

    public function setUp(): void
    {
        $this->statClient = Mockery::mock(StatClient::class);

        $this->stat = new Stat($this->statClient);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function it_can_show_the_serps_for_a_keyword()
    {
        $expectedArguments = [
            'serps/show', ['keyword_id' => 14, 'engine' => 'google', 'date' => '2011-03-13'],
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => '200',
                'Result' => [
                    [
                        'ResultType' => 'regular',
                        'Rank' => '4',
                        'BaseRank' => '4',
                        'Url' => 'www.google.com/analytics/',
                    ],
                    [
                        'ResultType' => 'regular',
                        'Rank' => '7',
                        'BaseRank' => '7',
                        'Url' => 'www.google.com/support/googleanalytics/?hl=en',
                    ],
                ],
            ]]);

        $response = $this->stat->serps()->show(14, Carbon::createFromDate(2011, 3, 13), 'google');

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertInstanceOf(StatSerpItem::class, $response->first());
        $this->assertEquals(2, $response->count());

        $this->assertArrayHasKey('result_type', $response->first());
        $this->assertArrayHasKey('rank', $response->first());
        $this->assertArrayHasKey('base_rank', $response->first());
        $this->assertArrayHasKey('url', $response->first());
    }
}
