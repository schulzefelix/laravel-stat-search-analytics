<?php

namespace SchulzeFelix\Stat\Tests\Unit;

use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit_Framework_TestCase;
use SchulzeFelix\Stat\Exceptions\ApiException;
use SchulzeFelix\Stat\Objects\StatEngineRankDistribution;
use SchulzeFelix\Stat\Objects\StatRankDistribution;
use SchulzeFelix\Stat\Objects\StatShareOfVoice;
use SchulzeFelix\Stat\Objects\StatShareOfVoiceSite;
use SchulzeFelix\Stat\Objects\StatTag;
use SchulzeFelix\Stat\Stat;
use SchulzeFelix\Stat\StatClient;

class TagsTest extends PHPUnit_Framework_TestCase
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
    public function it_can_list_tags()
    {
        $expectedArguments = [
            'tags/list', ['site_id' => 13, 'results' => 5000]
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'resultsreturned' => "100",
                'totalresults' => "223",
                'Result' => [
                    [
                        'Id' => "13",
                        'Tag' => "abc",
                        'Type' => "Standard",
                        'Keywords' => [
                            'Id' => [
                                '4525',
                                '4526'
                            ],
                        ]
                    ],
                    [
                        'Id' => "16902",
                        'Tag' => "abcd",
                        'Type' => "Dynamic",
                        'Keywords' => 'none',
                    ],
                ]
            ]]);

        $response = $this->stat->tags()->list(13);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertInstanceOf(StatTag::class, $response->first());
        $this->assertEquals(2, $response->count());
        $this->assertEquals(4, count($response->first()->toArray()));

        $this->assertArrayHasKey('id', $response->first());
        $this->assertArrayHasKey('tag', $response->first());
        $this->assertArrayHasKey('type', $response->first());
        $this->assertArrayHasKey('keywords', $response->first());

        $this->assertInstanceOf(Collection::class, $response->first()->keywords);
        $this->assertEquals(2, $response->first()->keywords->count());
    }

    /** @test */
    public function it_can_list_single_tag()
    {
        $expectedArguments = [
            'tags/list', ['site_id' => 13, 'results' => 5000]
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'resultsreturned' => "1",
                'totalresults' => "1",
                'Result' => [
                    'Id' => "13",
                    'Tag' => "abc",
                    'Type' => "Standard",
                    'Keywords' => [
                        'Id' => [
                            '4525',
                            '4526'
                        ],
                    ]
                ]
            ]]);

        $response = $this->stat->tags()->list(13);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertInstanceOf(StatTag::class, $response->first());
        $this->assertEquals(1, $response->count());
        $this->assertEquals(4, count($response->first()->toArray()));

        $this->assertArrayHasKey('id', $response->first());
        $this->assertArrayHasKey('tag', $response->first());
        $this->assertArrayHasKey('type', $response->first());
        $this->assertArrayHasKey('keywords', $response->first());

        $this->assertInstanceOf(Collection::class, $response->first()->keywords);
        $this->assertEquals(2, $response->first()->keywords->count());
    }

    /** @test */
    public function it_can_pull_the_ranking_distributions_for_a_tag()
    {
        $expectedArguments = [
            'tags/ranking_distributions', ['id' => 13, 'from_date' => '2016-10-01' , 'to_date' => '2016-10-02']
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'RankDistribution' => [
                    [
                        'date' => '2016-10-01',
                        'Google' => [
                            "One" => "0",
                            "Two" => "0",
                            "Three" => "1",
                            "Four" => "2",
                            "Five" => "2",
                            "SixToTen" => "6",
                            "ElevenToTwenty" => "5",
                            "TwentyOneToThirty" => "5",
                            "ThirtyOneToForty" => "2",
                            "FortyOneToFifty" => "0",
                            "FiftyOneToHundred" => "3",
                            "NonRanking" => "11",
                        ],
                        'GoogleBaseRank' => [
                            "One" => "0",
                            "Two" => "0",
                            "Three" => "1",
                            "Four" => "3",
                            "Five" => "1",
                            "SixToTen" => "7",
                            "ElevenToTwenty" => "5",
                            "TwentyOneToThirty" => "4",
                            "ThirtyOneToForty" => "2",
                            "FortyOneToFifty" => "0",
                            "FiftyOneToHundred" => "3",
                            "NonRanking" => "11",
                        ],
                        'Yahoo' => [
                            "One" => "0",
                            "Two" => "0",
                            "Three" => "1",
                            "Four" => "0",
                            "Five" => "1",
                            "SixToTen" => "3",
                            "ElevenToTwenty" => "6",
                            "TwentyOneToThirty" => "5",
                            "ThirtyOneToForty" => "4",
                            "FortyOneToFifty" => "1",
                            "FiftyOneToHundred" => "0",
                            "NonRanking" => "16",
                        ],
                        'Bing' => [
                            "One" => "0",
                            "Two" => "0",
                            "Three" => "1",
                            "Four" => "0",
                            "Five" => "1",
                            "SixToTen" => "3",
                            "ElevenToTwenty" => "6",
                            "TwentyOneToThirty" => "5",
                            "ThirtyOneToForty" => "4",
                            "FortyOneToFifty" => "1",
                            "FiftyOneToHundred" => "0",
                            "NonRanking" => "16",
                        ]
                    ],
                    [
                        'date' => '2016-10-02',
                        'Google' => [
                            "One" => "0",
                            "Two" => "0",
                            "Three" => "1",
                            "Four" => "2",
                            "Five" => "2",
                            "SixToTen" => "6",
                            "ElevenToTwenty" => "5",
                            "TwentyOneToThirty" => "5",
                            "ThirtyOneToForty" => "2",
                            "FortyOneToFifty" => "0",
                            "FiftyOneToHundred" => "3",
                            "NonRanking" => "11",
                        ],
                        'GoogleBaseRank' => [
                            "One" => "0",
                            "Two" => "0",
                            "Three" => "1",
                            "Four" => "3",
                            "Five" => "1",
                            "SixToTen" => "7",
                            "ElevenToTwenty" => "5",
                            "TwentyOneToThirty" => "4",
                            "ThirtyOneToForty" => "2",
                            "FortyOneToFifty" => "0",
                            "FiftyOneToHundred" => "3",
                            "NonRanking" => "11",
                        ],
                        'Yahoo' => [
                            "One" => "0",
                            "Two" => "0",
                            "Three" => "1",
                            "Four" => "0",
                            "Five" => "1",
                            "SixToTen" => "3",
                            "ElevenToTwenty" => "6",
                            "TwentyOneToThirty" => "5",
                            "ThirtyOneToForty" => "4",
                            "FortyOneToFifty" => "1",
                            "FiftyOneToHundred" => "0",
                            "NonRanking" => "16",
                        ],
                        'Bing' => [
                            "One" => "0",
                            "Two" => "0",
                            "Three" => "1",
                            "Four" => "0",
                            "Five" => "1",
                            "SixToTen" => "3",
                            "ElevenToTwenty" => "6",
                            "TwentyOneToThirty" => "5",
                            "ThirtyOneToForty" => "4",
                            "FortyOneToFifty" => "1",
                            "FiftyOneToHundred" => "0",
                            "NonRanking" => "16",
                        ]
                    ],
                ]
            ]]);

        $response = $this->stat->tags()->rankingDistributions(13, Carbon::createFromDate(2016, 10, 1), Carbon::createFromDate(2016, 10, 2));

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertInstanceOf(StatRankDistribution::class, $response->first());
        $this->assertEquals(2, $response->count());
        $this->assertEquals(5, count($response->first()->toArray()));
        $this->assertInstanceOf(StatEngineRankDistribution::class, $response->first()->google);


        $this->assertArrayHasKey('date', $response->first());
        $this->assertArrayHasKey('google', $response->first());
        $this->assertArrayHasKey('google_base_rank', $response->first());
        $this->assertArrayHasKey('yahoo', $response->first());
        $this->assertArrayHasKey('bing', $response->first());


        $this->assertArrayHasKey('one', $response->first()['google']);
        $this->assertArrayHasKey('two', $response->first()['google']);
        $this->assertArrayHasKey('three', $response->first()['google']);
        $this->assertArrayHasKey('four', $response->first()['google']);
        $this->assertArrayHasKey('five', $response->first()['google']);
        $this->assertArrayHasKey('six_to_ten', $response->first()['google']);
        $this->assertArrayHasKey('eleven_to_twenty', $response->first()['google']);
        $this->assertArrayHasKey('twenty_one_to_thirty', $response->first()['google']);
        $this->assertArrayHasKey('thirty_one_to_forty', $response->first()['google']);
        $this->assertArrayHasKey('forty_one_to_fifty', $response->first()['google']);
        $this->assertArrayHasKey('fifty_one_to_hundred', $response->first()['google']);
        $this->assertArrayHasKey('non_ranking', $response->first()['google']);

        $this->assertInstanceOf(Carbon::class, $response->first()['date']);
        $this->assertEquals(12, count($response->first()->google->toArray()));
        $this->assertEquals(5, $response->first()['google']['eleven_to_twenty']);
    }

    /** @test */
    public function it_should_throw_an_exception_if_the_date_range_is_higher_than_31_days_for_tag_ranking_distribution()
    {
        $expectedArguments = [
            'tags/ranking_distributions', ['id' => 13, 'from_date' => '2016-09-01' , 'to_date' => '2016-10-05']
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'resultsreturned' => "0",
                'totalresults' => "0",
                'Result' => []
            ]]);

        $this->setExpectedException(ApiException::class);

        $response = $this->stat->tags()->rankingDistributions(13, Carbon::createFromDate(2016, 9, 1), Carbon::createFromDate(2016, 10, 5));
    }

    /** @test */
    public function it_can_get_the_sov_for_a_tag()
    {
        $expectedArguments = [
            'tags/sov', ['id' => 13, 'from_date' => '2016-10-01' , 'to_date' => '2016-10-02', 'start' => 0, 'results' => 5000]
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'resultsreturned' => "2",
                'totalresults' => "2",
                'ShareOfVoice' => [
                    [
                        'date' => '2016-10-01',
                        'Site' => [
                            [
                                'Domain' => "www.example.de",
                                'Share' => "13.45",
                                'Pinned' => "false",
                            ],
                            [
                                'Domain' => "www.example.com",
                                'Share' => "8.45",
                                'Pinned' => "false",
                            ],
                        ]
                    ],
                    [
                        'date' => '2016-10-02',
                        'Site' => [
                            [
                                'Domain' => "www.example.de",
                                'Share' => "13.55",
                                'Pinned' => "false",
                            ],
                            [
                                'Domain' => "www.example.com",
                                'Share' => "4.15",
                                'Pinned' => "false",
                            ],
                        ]
                    ],

                ]
            ]]);

        $response = $this->stat->tags()->sov(13, Carbon::createFromDate(2016, 10, 1), Carbon::createFromDate(2016, 10, 2));

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertInstanceOf(StatShareOfVoice::class, $response->first());
        $this->assertEquals(2, $response->count());
        $this->assertInstanceOf(Collection::class, $response->first()->sites);
        $this->assertInstanceOf(StatShareOfVoiceSite::class, $response->first()->sites->first());
        $this->assertInstanceOf(Carbon::class, $response->first()->date);
        $this->assertEquals('2016-10-01', $response->first()->date->toDateString());
        $this->assertEquals('www.example.de', $response->first()->sites->first()->domain);

    }
}
