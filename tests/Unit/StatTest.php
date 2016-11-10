<?php

namespace SchulzeFelix\Stat\Tests\Unit;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit_Framework_TestCase;
use SchulzeFelix\Stat\Exceptions\ApiException;
use SchulzeFelix\Stat\Stat;
use SchulzeFelix\Stat\StatClient;

class StatTest extends PHPUnit_Framework_TestCase
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
    public function it_can_fetch_all_sites()
    {
        $expectedArguments = [
            'sites/all', ['start' => 0, 'results' => 5000]
        ];
        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'totalresults' => "50",
                'resultsreturned' => "50",
                'nextpage' => "/sites/all?start=50&format=json",
                'Result' => [
                    [
                        'Id' => "1",
                        'ProjectId' => "13",
                        'FolderId' => "22",
                        'FolderName' => "Blog",
                        'Title' => "gawker.com",
                        'Url' => "gawker.com",
                        'Synced' => "N/A",
                        'TotalKeywords' => "63",
                        'CreatedAt' => "2011-01-25",
                        'UpdatedAt' => "2011-01-25",
                        'RequestUrl' => "/keywords/list?site_id=1&format=json",
                    ],
                    [
                        'Id' => "2",
                        'ProjectId' => "13",
                        'FolderId' => "N/A",
                        'FolderName' => "N/A",
                        'Title' => "perezhilton.com",
                        'Url' => "perezhilton.com",
                        'Synced' => "1",
                        'TotalKeywords' => "63",
                        'CreatedAt' => "2011-01-25",
                        'UpdatedAt' => "2011-01-25",
                        'RequestUrl' => "/keywords/list?site_id=2&format=json",
                    ],
                ]
            ]]);

        $response = $this->stat->sites()->all();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertEquals(2, $response->count());
    }

    /** @test */
    public function it_can_list_sites_for_a_project()
    {
        $expectedArguments = [
            'sites/list', ['project_id' => 13]
        ];
        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'resultsreturned' => "2",
                'responsecode' => "200",
                'Result' => [
                    [
                        'Id' => "1",
                        'FolderId' => "22",
                        'FolderName' => "Blog",
                        'Title' => "gawker.com",
                        'Url' => "gawker.com",
                        'Synced' => "N/A",
                        'TotalKeywords' => "63",
                        'CreatedAt' => "2011-01-25",
                        'UpdatedAt' => "2011-01-25",
                        'RequestUrl' => "/keywords/list?site_id=1&format=json",
                    ],
                    [
                        'Id' => "2",
                        'FolderId' => "N/A",
                        'FolderName' => "N/A",
                        'Title' => "perezhilton.com",
                        'Url' => "perezhilton.com",
                        'Synced' => "1",
                        'TotalKeywords' => "63",
                        'CreatedAt' => "2011-01-25",
                        'UpdatedAt' => "2011-01-25",
                        'RequestUrl' => "/keywords/list?site_id=2&format=json",
                    ],
                ]
            ]]);

        $response = $this->stat->sites()->list(13);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertEquals(2, $response->count());
        $this->assertEquals('gawker.com', $response->first()['Title']);
        $this->assertEquals(63, $response->first()['TotalKeywords']);
    }

    /** @test */
    public function it_can_create_new_sites_for_a_project()
    {
        $expectedArguments = [
            'sites/create', ['project_id' => 13, 'url' => 'http://google.com', 'drop_www_prefix' => true, 'drop_directories' => true]
        ];
        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'Result' => [
                        'Id' => "146",
                        'ProjectId' => "13",
                        'Title' => "google.com",
                        'Url' => "google.com",
                        'DropWWWPrefix' => "true",
                        'DropDirectories' => "true",
                        'CreatedAt' => "2011-01-25",
                ]
            ]]);

        $response = $this->stat->sites()->create(13, 'http://google.com');

        $this->assertInternalType('array', $response);
        $this->assertEquals(146, $response['Id']);
        $this->assertEquals("google.com", $response['Title']);
    }

    /** @test */
    public function it_can_update_a_site()
    {
        $expectedArguments = [
            'sites/update', ['id' => 13, 'url' => 'http://google.com', 'title' => 'my site', 'drop_www_prefix' => true, 'drop_directories' => true]
        ];
        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'Result' => [
                        'Id' => "146",
                        'ProjectId' => "13",
                        'Title' => "my site",
                        'Url' => "google.com",
                        'DropWWWPrefix' => "true",
                        'DropDirectories' => "true",
                        'CreatedAt' => "2011-01-25",
                        'UpdatedAt' => "2011-01-25",
                ]
            ]]);

        $response = $this->stat->sites()->update(13, [
            'url' => 'http://google.com',
            'title' => 'my site',
            'drop_www_prefix' => true,
            'drop_directories' => true,
        ]);

        $this->assertInternalType('array', $response);
        $this->assertEquals(146, $response['Id']);
        $this->assertEquals("my site", $response['Title']);
    }

    /** @test */
    public function it_can_delete_a_site()
    {
        $expectedArguments = [
            'sites/delete', ['id' => 13]
        ];
        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'Result' => [
                    'Id' => "13",
                ]
            ]]);

        $response = $this->stat->sites()->delete(13);

        $this->assertInternalType('int', $response);
        $this->assertEquals(13, $response);
    }

    /** @test */
    public function it_can_pull_the_ranking_distributions()
    {
        $expectedArguments = [
            'sites/ranking_distributions', ['id' => 13, 'from_date' => '2016-10-01' , 'to_date' => '2016-10-02']
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

        $response = $this->stat->sites()->rankingDistributions(13, '2016-10-01', '2016-10-02');

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertEquals(2, $response->count());
        $this->assertEquals('2016-10-01', $response->first()['date']);
        $this->assertEquals(12, count($response->first()['Google']));
        $this->assertEquals(5, $response->first()['Google']['ElevenToTwenty']);
    }

    /** @test */
    public function it_should_throw_an_exception_if_the_date_range_is_higher_than_31_days_for_site_ranking_distribution()
    {
        $expectedArguments = [
            'sites/ranking_distributions', ['id' => 13, 'from_date' => '2016-09-01' , 'to_date' => '2016-10-05']
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

        $response = $this->stat->sites()->rankingDistributions(13, '2016-09-01', '2016-10-05');

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
        $this->assertInstanceOf(Collection::class, $response->first()['Keywords']);
        $this->assertEquals(2, $response->count());
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

        $response = $this->stat->tags()->rankingDistributions(13, '2016-10-01', '2016-10-02');

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertEquals(2, $response->count());
        $this->assertEquals('2016-10-01', $response->first()['date']);
        $this->assertEquals(12, count($response->first()['Google']));
        $this->assertEquals(5, $response->first()['Google']['ElevenToTwenty']);
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

        $response = $this->stat->tags()->rankingDistributions(13, '2016-09-01', '2016-10-05');

    }

//    /** @test */
//    public function it_can_list_keywords_for_a_site()
//    {
//        $expectedArguments = [
//            'keywords/list', ['site_id' => 13, 'results' => '5000']
//        ];
//
//        $this->statClient
//            ->shouldReceive('performQuery')->withArgs($expectedArguments)
//            ->once()
//            ->andReturn(['Response' => [
//                'responsecode' => "200",
//                'resultsreturned' => "63",
//                'totalresults' => "150",
//                'nextpage' => "/keywords/list?site_id=1&start=1000&format=json",
//                'Result' => [
//                    [
//                        'Id' => '11',
//                        'Keyword' => 'black celebrity gossip',
//                        'KeywordMarket' => 'US-en',
//                        'KeywordLocation' => 'Boston',
//                        'KeywordDevice' => 'Smartphone',
//                        'KeywordTags' => 'gossip',
//                        'KeywordStats' => [
//                            'AdvertiserCompetition' => '0.86748',
//                            'GlobalSearchVolume' => '80000',
//                            'RegionalSearchVolume' => '54000',
//                            'LocalSearchTrendsByMonth' => [
//                                'Sep' => '49500',
//                                'Aug' => '60500',
//                                'Jul' => '49500',
//                                'Jun' => '49500',
//                                'May' => '49500',
//                                'Apr' => '49500',
//                                'Mar' => '49500',
//                                'Feb' => '49500',
//                                'Jan' => '49500',
//                                'Dec' => '40500',
//                                'Nov' => '49500'
//                            ],
//                            'CPC' => '1.42'
//                        ],
//                        'KeywordRanking' => [
//                            'date' => '2014-07-09',
//                            'Google' => [
//                                'Rank' => '1',
//                                'BaseRank' => '1',
//                                'Url' => 'www.zillow.com/mortgage-rates/ca/',
//                            ],
//                            'Yahoo' => [
//                                'Rank' => '1',
//                                'Url' => 'www.zillow.com/mortgage-rates/ca/',
//                            ],
//                            'Bing' => [
//                                'Rank' => '1',
//                                'Url' => 'www.zillow.com/mortgage-rates/ca/',
//                            ],
//                        ],
//                        'CreatedAt' => '2011-01-25',
//                        'RequestUrl' => '/rankings?keyword_id=11&format=json&from_date=2011-01-25&to_date=',
//                    ],
//                ],
//            ]]);
//
//        $response = $this->stat->keywords()->list(13);
//    }

//    /** @test */
//    public function it_can_create_keywords()
//    {
//        $expectedArguments = [
//            'keywords/create', ['site_id' => 13, 'market' => 'US-en' , 'location' => 'Boston', 'device' => 'smartphone', 'type' => 'regular', 'keyword' => 'shirt,shoes', 'tag' => 'clothes']
//        ];
//
//        $this->statClient
//            ->shouldReceive('performQuery')->withArgs($expectedArguments)
//            ->once()
//            ->andReturn(['Response' => [
//                'responsecode' => "200",
//                'resultsreturned' => "0",
//                'totalresults' => "0",
//                'Result' => []
//            ]]);
//    }


}
