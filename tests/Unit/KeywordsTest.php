<?php

namespace SchulzeFelix\Stat\Tests\Unit;

use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit_Framework_TestCase;
use SchulzeFelix\Stat\Exceptions\ApiException;
use SchulzeFelix\Stat\Stat;
use SchulzeFelix\Stat\StatClient;

class KeywordsTest extends PHPUnit_Framework_TestCase
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
    public function it_can_list_keywords_for_a_site()
    {
        $expectedArguments = [
            'keywords/list', ['site_id' => 13, 'start' => 0, 'results' => '5000']
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'resultsreturned' => "100",
                'totalresults' => "100",
                'nextpage' => "/keywords/list?site_id=1&start=1000&format=json",
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
                                'Nov' => '49500'
                            ],
                            'CPC' => '1.42'
                        ],
                        'KeywordRanking' => [
                            'date' => '2014-07-09',
                            'Google' => [
                                'Rank' => '1',
                                'BaseRank' => '1',
                                'Url' => 'www.zillow.com/mortgage-rates/ca/',
                            ],
                            'Yahoo' => [
                                'Rank' => '1',
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
        $this->assertEquals(1, $response->count());

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
        $this->assertArrayHasKey('sep', $response->first()['keyword_stats']['local_search_trends_by_month']);
        $this->assertArrayHasKey('aug', $response->first()['keyword_stats']['local_search_trends_by_month']);
        $this->assertArrayHasKey('jul', $response->first()['keyword_stats']['local_search_trends_by_month']);
        $this->assertArrayHasKey('jun', $response->first()['keyword_stats']['local_search_trends_by_month']);
        $this->assertArrayHasKey('may', $response->first()['keyword_stats']['local_search_trends_by_month']);
        $this->assertArrayHasKey('apr', $response->first()['keyword_stats']['local_search_trends_by_month']);
        $this->assertArrayHasKey('mar', $response->first()['keyword_stats']['local_search_trends_by_month']);
        $this->assertArrayHasKey('feb', $response->first()['keyword_stats']['local_search_trends_by_month']);
        $this->assertArrayHasKey('jan', $response->first()['keyword_stats']['local_search_trends_by_month']);
        $this->assertArrayHasKey('dec', $response->first()['keyword_stats']['local_search_trends_by_month']);
        $this->assertArrayHasKey('nov', $response->first()['keyword_stats']['local_search_trends_by_month']);
        $this->assertArrayHasKey('cpc', $response->first()['keyword_stats']);

        $this->assertArrayHasKey('keyword_ranking', $response->first());
        $this->assertArrayHasKey('date', $response->first()['keyword_ranking']);
        $this->assertArrayHasKey('google', $response->first()['keyword_ranking']);
        $this->assertArrayHasKey('yahoo', $response->first()['keyword_ranking']);
        $this->assertArrayHasKey('bing', $response->first()['keyword_ranking']);
        $this->assertArrayHasKey('rank', $response->first()['keyword_ranking']['google']);
        $this->assertArrayHasKey('base_rank', $response->first()['keyword_ranking']['google']);
        $this->assertArrayHasKey('url', $response->first()['keyword_ranking']['google']);
        $this->assertArrayHasKey('rank', $response->first()['keyword_ranking']['yahoo']);
        $this->assertArrayHasKey('url', $response->first()['keyword_ranking']['yahoo']);
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
        $this->assertInternalType('array', $response->first()['keyword_stats']);
        $this->assertInternalType('array', $response->first()['keyword_stats']['local_search_trends_by_month']);
        $this->assertInternalType('array', $response->first()['keyword_ranking']);
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
                'keyword' => 'shirt\,shoes,dress,boots',
                'tag' => 'clothes,brand',
                'location' => 'Boston'
            ]
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'resultsreturned' => "3",
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
                ]
            ]]);

        $response = $this->stat->keywords()->create(13, 'US-en', ['shirt,shoes', 'dress', 'boots'], ['clothes', 'brand'], 'Boston', 'smartphone');

        $this->assertInstanceOf(Collection::class, $response);
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
                'responsecode' => "200",
                'Result' => [
                    'Id' => '3008',
                ]
            ]]);


        $response = $this->stat->keywords()->delete(3008);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertEquals(3008, $response->first());

    }

    /** @test */
    public function it_can_delete_a_multiple_keywords()
    {
        $expectedArguments = ['keywords/delete', ['id' => "3008,3009,3010"]];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
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

                ]
            ]]);


        $response = $this->stat->keywords()->delete([3008,3009,3010]);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertEquals(3, $response->count());
        $this->assertEquals(3008, $response->first());

    }

}
