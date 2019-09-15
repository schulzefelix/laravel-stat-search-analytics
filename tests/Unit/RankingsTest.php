<?php

namespace SchulzeFelix\Stat\Tests\Unit;

use Mockery;
use Carbon\Carbon;
use SchulzeFelix\Stat\Stat;
use PHPUnit\Framework\TestCase;
use SchulzeFelix\Stat\StatClient;
use Illuminate\Support\Collection;
use SchulzeFelix\Stat\Objects\StatKeywordRanking;
use SchulzeFelix\Stat\Objects\StatKeywordEngineRanking;

class RankingsTest extends TestCase
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
    public function it_can_list_rankings_for_a_keyword()
    {
        $expectedArguments = [
            'rankings/list', ['keyword_id' => 14, 'from_date' => '2011-01-25', 'to_date' => '2011-03-13', 'start' => 0],
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => '200',
                'resultsreturned' => '30',
                'totalresults' => '30',
                'nextpage' => '/rankings/list?keyword_id=14&from_date=2011-01-25&to_date=2011-03-13&start=60&format=json',
                'Result' => [
                    [
                        'date' => '2011-01-25',
                        'Google' => [
                            'Rank' => '55',
                            'Url' => 'http://gawker.com/5014342/nobody-cares-about-perez-hilton',
                            'BaseRank' => '50',
                            'RequestUrl' => '/serps/show?keyword_id=14&engine=google&date=2011-01-25&format=json',
                        ],
                        'Bing' => [
                            'Rank' => '28',
                            'BaseRank' => '0',
                            'Url' => 'http://gawker.com/5303229/perez-hilton-is-scared-and-on-the-lam',
                            'RequestUrl' => '/serps/show?keyword_id=14&engine=bing&date=2011-01-25&format=json',
                        ],
                    ],
                ],
            ]]);

        $response = $this->stat->rankings()->list(14, Carbon::createFromDate(2011, 1, 25), Carbon::createFromDate(2011, 3, 13));

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertEquals(1, $response->count());

        $this->assertInstanceOf(StatKeywordRanking::class, $response->first());
        $this->assertInstanceOf(StatKeywordEngineRanking::class, $response->first()->google);

        $this->assertArrayHasKey('date', $response->first());
        $this->assertArrayHasKey('google', $response->first());
        $this->assertArrayHasKey('bing', $response->first());

        $this->assertArrayHasKey('rank', $response->first()['google']);
        $this->assertArrayHasKey('base_rank', $response->first()['google']);
        $this->assertArrayHasKey('url', $response->first()['google']);

        $this->assertArrayHasKey('rank', $response->first()['bing']);
        $this->assertArrayHasKey('base_rank', $response->first()['bing']);
        $this->assertArrayHasKey('url', $response->first()['bing']);

        $this->assertInstanceOf(Carbon::class, $response->first()['date']);
        $this->assertEquals(55, $response->first()['google']['rank']);
        $this->assertEquals(50, $response->first()['google']['base_rank']);
    }
}
