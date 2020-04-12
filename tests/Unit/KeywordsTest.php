<?php

namespace SchulzeFelix\Stat\Tests\Unit;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;
use SchulzeFelix\Stat\Objects\StatKeyword;
use SchulzeFelix\Stat\Objects\StatKeywordRanking;
use SchulzeFelix\Stat\Objects\StatKeywordStats;
use SchulzeFelix\Stat\Stat;
use SchulzeFelix\Stat\StatClient;

class KeywordsTest extends TestCase
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
    public function it_can_list_keywords_for_a_site_with_more_than_one_keyword()
    {
        $expectedArguments = [
            'keywords/list', ['site_id' => 13, 'start' => 0, 'results' => '5000'],
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => '200',
                'resultsreturned' => '100',
                'totalresults' => '100',
                'nextpage' => '/keywords/list?site_id=1&start=1000&format=json',
                'Result' => [
                    [
                        'Id' => '11',
                        'Keyword' => 'black celebrity gossip',
                        'KeywordMarket' => 'US-en',
                        'KeywordLocation' => 'Boston',
                        'KeywordDevice' => 'Smartphone',
                        'KeywordTags' => 'gossip, usa',
                        'KeywordStats' => [
                            'AdvertiserCompetition' => '0.86748',
                            'GlobalSearchVolume' => '80000',
                            'RegionalSearchVolume' => '54000',
                            'LocalSearchTrendsByMonth' => [
                                'Oct' => '-',
                                'Sep' => '49500',
                                'Aug' => '60500',
                                'Jul' => '49500',
                                'Jun' => '49500',
                                'May' => '49500',
                                'Apr' => '49500',
                                'Mar' => '49500',
                                'Feb' => '49500',
                                'Jan' => '49500',
                                'Dec' => '40500',
                                'Nov' => '49500',
                            ],
                            'CPC' => '1.42',
                        ],
                        'KeywordRanking' => [
                            'date' => '2014-07-09',
                            'Google' => [
                                'Rank' => '1',
                                'BaseRank' => '1',
                                'Url' => 'www.zillow.com/mortgage-rates/ca/',
                            ],
                            'Bing' => [
                                'Rank' => '1',
                                'Url' => 'www.zillow.com/mortgage-rates/ca/',
                            ],
                        ],
                        'CreatedAt' => '2011-01-25',
                        'RequestUrl' => '/rankings?keyword_id=11&format=json&from_date=2011-01-25&to_date=',
                    ],
                    [
                        'Id' => '12',
                        'Keyword' => 'black celebrity news',
                        'KeywordMarket' => 'US-en',
                        'KeywordLocation' => 'Boston',
                        'KeywordDevice' => 'Smartphone',
                        'KeywordTags' => 'news, usa',
                        'KeywordStats' => [
                            'AdvertiserCompetition' => '0.86748',
                            'GlobalSearchVolume' => '70000',
                            'RegionalSearchVolume' => '52000',
                            'LocalSearchTrendsByMonth' => [
                                'Oct' => '-',
                                'Sep' => '49500',
                                'Aug' => '60500',
                                'Jul' => '49500',
                                'Jun' => '49500',
                                'May' => '49500',
                                'Apr' => '49500',
                                'Mar' => '49500',
                                'Feb' => '49500',
                                'Jan' => '49500',
                                'Dec' => '40500',
                                'Nov' => '49500',
                            ],
                            'CPC' => '1.42',
                        ],
                        'KeywordRanking' => [
                            'date' => '2014-07-09',
                            'Google' => [
                                'Rank' => '1',
                                'BaseRank' => '1',
                                'Url' => 'www.zillow.com/mortgage-rates/ca/',
                            ],
                            'Bing' => [
                                'Rank' => '1',
                                'Url' => 'www.zillow.com/mortgage-rates/ca/',
                            ],
                        ],
                        'CreatedAt' => '2011-01-25',
                        'RequestUrl' => '/rankings?keyword_id=11&format=json&from_date=2011-01-25&to_date=',
                    ],
                ],
            ]]);

        $response = $this->stat->keywords()->list(13);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertEquals(2, $response->count());
        $this->assertInstanceOf(StatKeyword::class, $response->first());

        $this->assertArrayHasKey('id', $response->first());
        $this->assertArrayHasKey('keyword', $response->first());
        $this->assertArrayHasKey('keyword_market', $response->first());
        $this->assertArrayHasKey('keyword_location', $response->first());
        $this->assertArrayHasKey('keyword_device', $response->first());
        $this->assertArrayHasKey('keyword_tags', $response->first());
        $this->assertArrayHasKey('keyword_stats', $response->first());
        $this->assertArrayHasKey('advertiser_competition', $response->first()['keyword_stats']);
        $this->assertArrayHasKey('global_search_volume', $response->first()['keyword_stats']);

        $this->assertArrayHasKey('regional_search_volume', $response->first()['keyword_stats']);
        $this->assertArrayHasKey('local_search_trends_by_month', $response->first()['keyword_stats']);
        $this->assertInstanceOf(StatKeywordStats::class, $response->first()->keyword_stats);
        $this->assertInstanceOf(Collection::class, $response->first()->keyword_stats->local_search_trends_by_month);

        $this->assertArrayHasKey('cpc', $response->first()['keyword_stats']);

        $this->assertArrayHasKey('keyword_ranking', $response->first());
        $this->assertArrayHasKey('date', $response->first()['keyword_ranking']);
        $this->assertArrayHasKey('google', $response->first()['keyword_ranking']);
        $this->assertArrayHasKey('bing', $response->first()['keyword_ranking']);
        $this->assertArrayHasKey('rank', $response->first()['keyword_ranking']['google']);
        $this->assertArrayHasKey('base_rank', $response->first()['keyword_ranking']['google']);
        $this->assertArrayHasKey('url', $response->first()['keyword_ranking']['google']);
        $this->assertArrayHasKey('rank', $response->first()['keyword_ranking']['bing']);
        $this->assertArrayHasKey('url', $response->first()['keyword_ranking']['bing']);

        $this->assertArrayHasKey('created_at', $response->first());

        $this->assertEquals(11, $response->first()['id']);
        $this->assertEquals('black celebrity gossip', $response->first()['keyword']);
        $this->assertEquals('US-en', $response->first()['keyword_market']);
        $this->assertEquals('Boston', $response->first()['keyword_location']);
        $this->assertEquals('Smartphone', $response->first()['keyword_device']);
        $this->assertInstanceOf(Collection::class, $response->first()['keyword_tags']);
        $this->assertEquals(2, $response->first()['keyword_tags']->count());
        $this->assertInstanceOf(StatKeywordRanking::class, $response->first()['keyword_ranking']);
        $this->assertEquals(1, $response->first()['keyword_ranking']['google']['rank']);

        $this->assertInstanceOf(Carbon::class, $response->first()['created_at']);
        $this->assertInstanceOf(Carbon::class, $response->first()['keyword_ranking']['date']);
    }

    /** @test */
    public function it_can_list_keywords_for_a_site_with_exactly_one_keyword()
    {
        $expectedArguments = [
            'keywords/list', ['site_id' => 13, 'start' => 0, 'results' => '5000'],
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => '200',
                'resultsreturned' => '100',
                'totalresults' => '100',
                'nextpage' => '/keywords/list?site_id=1&start=1000&format=json',
                'Result' => [
                    'Id' => '11',
                    'Keyword' => 'black celebrity gossip',
                    'KeywordMarket' => 'US-en',
                    'KeywordLocation' => 'Boston',
                    'KeywordDevice' => 'Smartphone',
                    'KeywordTags' => 'gossip, usa',
                    'KeywordStats' => [
                        'AdvertiserCompetition' => '0.86748',
                        'GlobalSearchVolume' => '80000',
                        'RegionalSearchVolume' => '54000',
                        'LocalSearchTrendsByMonth' => [
                            'Oct' => '-',
                            'Sep' => '49500',
                            'Aug' => '60500',
                            'Jul' => '49500',
                            'Jun' => '49500',
                            'May' => '49500',
                            'Apr' => '49500',
                            'Mar' => '49500',
                            'Feb' => '49500',
                            'Jan' => '49500',
                            'Dec' => '40500',
                            'Nov' => '49500',
                        ],
                        'CPC' => '1.42',
                    ],
                    'KeywordRanking' => [
                        'date' => '2014-07-09',
                        'Google' => [
                            'Rank' => '1',
                            'BaseRank' => '1',
                            'Url' => 'www.zillow.com/mortgage-rates/ca/',
                        ],
                        'Bing' => [
                            'Rank' => '1',
                            'Url' => 'www.zillow.com/mortgage-rates/ca/',
                        ],
                    ],
                    'CreatedAt' => '2011-01-25',
                    'RequestUrl' => '/rankings?keyword_id=11&format=json&from_date=2011-01-25&to_date=',
                ],
            ]]);

        $response = $this->stat->keywords()->list(13);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertEquals(1, $response->count());
        $this->assertInstanceOf(StatKeyword::class, $response->first());

        $this->assertArrayHasKey('id', $response->first());
        $this->assertArrayHasKey('keyword', $response->first());
        $this->assertArrayHasKey('keyword_market', $response->first());
        $this->assertArrayHasKey('keyword_location', $response->first());
        $this->assertArrayHasKey('keyword_device', $response->first());
        $this->assertArrayHasKey('keyword_tags', $response->first());
        $this->assertArrayHasKey('keyword_stats', $response->first());
        $this->assertArrayHasKey('advertiser_competition', $response->first()['keyword_stats']);
        $this->assertArrayHasKey('global_search_volume', $response->first()['keyword_stats']);

        $this->assertArrayHasKey('regional_search_volume', $response->first()['keyword_stats']);
        $this->assertArrayHasKey('local_search_trends_by_month', $response->first()['keyword_stats']);
        $this->assertInstanceOf(StatKeywordStats::class, $response->first()->keyword_stats);
        $this->assertInstanceOf(Collection::class, $response->first()->keyword_stats->local_search_trends_by_month);

        $this->assertArrayHasKey('cpc', $response->first()['keyword_stats']);

        $this->assertArrayHasKey('keyword_ranking', $response->first());
        $this->assertArrayHasKey('date', $response->first()['keyword_ranking']);
        $this->assertArrayHasKey('google', $response->first()['keyword_ranking']);
        $this->assertArrayHasKey('bing', $response->first()['keyword_ranking']);
        $this->assertArrayHasKey('rank', $response->first()['keyword_ranking']['google']);
        $this->assertArrayHasKey('base_rank', $response->first()['keyword_ranking']['google']);
        $this->assertArrayHasKey('url', $response->first()['keyword_ranking']['google']);
        $this->assertArrayHasKey('rank', $response->first()['keyword_ranking']['bing']);
        $this->assertArrayHasKey('url', $response->first()['keyword_ranking']['bing']);

        $this->assertArrayHasKey('created_at', $response->first());

        $this->assertEquals(11, $response->first()['id']);
        $this->assertEquals('black celebrity gossip', $response->first()['keyword']);
        $this->assertEquals('US-en', $response->first()['keyword_market']);
        $this->assertEquals('Boston', $response->first()['keyword_location']);
        $this->assertEquals('Smartphone', $response->first()['keyword_device']);
        $this->assertInstanceOf(Collection::class, $response->first()['keyword_tags']);
        $this->assertEquals(2, $response->first()['keyword_tags']->count());
        $this->assertInstanceOf(StatKeywordRanking::class, $response->first()['keyword_ranking']);
        $this->assertEquals(1, $response->first()['keyword_ranking']['google']['rank']);

        $this->assertInstanceOf(Carbon::class, $response->first()['created_at']);
        $this->assertInstanceOf(Carbon::class, $response->first()['keyword_ranking']['date']);
    }

    /** @test */
    public function it_can_create_keywords()
    {
        $expectedArguments = [
            'keywords/create', [
                'site_id' => 13,
                'market' => 'US-en',
                'device' => 'smartphone',
                'type' => 'regular',
                'keyword' => 'shirt%5C%2Cshoes,dress,boots',
                'tag' => 'clothes,brand',
                'location' => 'Boston',
            ],
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => '200',
                'resultsreturned' => '3',
                'Result' => [
                    [
                        'Id' => '3008',
                        'Keyword' => 'shirt,shoes',
                        'KeywordMarket' => 'US-en',
                        'KeywordLocation' => 'Boston',
                        'KeywordDevice' => 'Smartphone',
                        'CreatedAt' => '2011-01-25',
                    ],
                    [
                        'Id' => '3009',
                        'Keyword' => 'dress',
                        'KeywordMarket' => 'US-en',
                        'KeywordLocation' => 'Boston',
                        'KeywordDevice' => 'Smartphone',
                        'CreatedAt' => '2011-01-25',
                    ],
                    [
                        'Id' => '3010',
                        'Keyword' => 'boots',
                        'KeywordMarket' => 'US-en',
                        'KeywordLocation' => 'Boston',
                        'KeywordDevice' => 'Smartphone',
                        'CreatedAt' => '2011-01-25',
                    ],
                ],
            ]]);

        $response = $this->stat->keywords()->create(13, 'US-en', ['shirt,shoes', 'dress', 'boots'], ['clothes', 'brand'], 'Boston', 'smartphone');

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertInstanceOf(StatKeyword::class, $response->first());
        $this->assertArrayHasKey('id', $response->first());
        $this->assertArrayHasKey('keyword', $response->first());
        $this->assertArrayHasKey('keyword_market', $response->first());
        $this->assertArrayHasKey('keyword_location', $response->first());
        $this->assertArrayHasKey('keyword_device', $response->first());
        $this->assertArrayHasKey('created_at', $response->first());

        $this->assertEquals(3008, $response->first()['id']);
        $this->assertEquals('shirt,shoes', $response->first()['keyword']);
        $this->assertEquals('US-en', $response->first()['keyword_market']);
        $this->assertInstanceOf(Carbon::class, $response->first()['created_at']);
    }

    /** @test */
    public function it_can_delete_a_single_keyword()
    {
        $expectedArguments = ['keywords/delete', ['id' => 3008]];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => '200',
                'Result' => [
                    'Id' => '3008',
                ],
            ]]);

        $response = $this->stat->keywords()->delete(3008);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertEquals(3008, $response->first());
    }

    /** @test */
    public function it_can_delete_a_multiple_keywords()
    {
        $expectedArguments = ['keywords/delete', ['id' => '3008,3009,3010']];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => '200',
                'Result' => [
                    [
                        'Id' => '3008',
                    ],
                    [
                        'Id' => '3009',
                    ],
                    [
                        'Id' => '3010',
                    ],

                ],
            ]]);

        $response = $this->stat->keywords()->delete([3008, 3009, 3010]);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertEquals(3, $response->count());
        $this->assertEquals(3008, $response->first());
    }
}
