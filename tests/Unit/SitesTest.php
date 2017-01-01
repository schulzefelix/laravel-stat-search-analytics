<?php

namespace SchulzeFelix\Stat\Tests\Unit;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit_Framework_TestCase;
use SchulzeFelix\Stat\Exceptions\ApiException;
use SchulzeFelix\Stat\Objects\StatEngineRankDistribution;
use SchulzeFelix\Stat\Objects\StatFrequentDomain;
use SchulzeFelix\Stat\Objects\StatRankDistribution;
use SchulzeFelix\Stat\Objects\StatShareOfVoice;
use SchulzeFelix\Stat\Objects\StatShareOfVoiceSite;
use SchulzeFelix\Stat\Objects\StatSite;
use SchulzeFelix\Stat\Stat;
use SchulzeFelix\Stat\StatClient;

class SitesTest extends PHPUnit_Framework_TestCase
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
        $this->assertInstanceOf(StatSite::class, $response->first());
        $this->assertEquals(2, $response->count());

        $this->assertArrayHasKey('id', $response->first());
        $this->assertArrayHasKey('project_id', $response->first());
        $this->assertArrayHasKey('folder_id', $response->first());
        $this->assertArrayHasKey('folder_name', $response->first());
        $this->assertArrayHasKey('title', $response->first());
        $this->assertArrayHasKey('url', $response->first());
        $this->assertArrayHasKey('synced', $response->first());
        $this->assertArrayHasKey('total_keywords', $response->first());
        $this->assertArrayHasKey('created_at', $response->first());
        $this->assertArrayHasKey('updated_at', $response->first());

        $this->assertEquals(1, $response->first()['id']);
        $this->assertEquals(13, $response->first()['project_id']);
        $this->assertEquals('22', $response->first()['folder_id']);
        $this->assertEquals('Blog', $response->first()['folder_name']);
        $this->assertEquals('gawker.com', $response->first()['title']);
        $this->assertEquals('gawker.com', $response->first()['url']);
        $this->assertEquals('N/A', $response->first()['synced']);
        $this->assertEquals(63, $response->first()['total_keywords']);
        $this->assertInstanceOf(Carbon::class, $response->first()['created_at']);
        $this->assertInstanceOf(Carbon::class, $response->first()['updated_at']);
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
        $this->assertInstanceOf(StatSite::class, $response->first());
        $this->assertEquals(2, $response->count());
        $this->assertEquals(10, count($response->first()->toArray()));

        $this->assertArrayHasKey('id', $response->first());
        $this->assertArrayHasKey('folder_id', $response->first());
        $this->assertArrayHasKey('folder_name', $response->first());
        $this->assertArrayHasKey('title', $response->first());
        $this->assertArrayHasKey('url', $response->first());
        $this->assertArrayHasKey('synced', $response->first());
        $this->assertArrayHasKey('total_keywords', $response->first());
        $this->assertArrayHasKey('created_at', $response->first());
        $this->assertArrayHasKey('updated_at', $response->first());

        $this->assertEquals(1, $response->first()['id']);
        $this->assertEquals('22', $response->first()['folder_id']);
        $this->assertEquals('Blog', $response->first()['folder_name']);
        $this->assertEquals('gawker.com', $response->first()['title']);
        $this->assertEquals('gawker.com', $response->first()['url']);
        $this->assertEquals('N/A', $response->first()['synced']);
        $this->assertEquals(63, $response->first()['total_keywords']);
        $this->assertInstanceOf(Carbon::class, $response->first()['created_at']);
        $this->assertInstanceOf(Carbon::class, $response->first()['updated_at']);
    }

    /** @test */
    public function it_can_create_new_sites_for_a_project()
    {
        $expectedArguments = [
            'sites/create', ['project_id' => 13, 'url' => 'http%3A%2F%2Fgoogle.com', 'drop_www_prefix' => true, 'drop_directories' => true]
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

        $this->assertInstanceOf(StatSite::class, $response);
        $this->assertEquals(7, count($response->toArray()));

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('project_id', $response);
        $this->assertArrayHasKey('title', $response);
        $this->assertArrayHasKey('url', $response);
        $this->assertArrayHasKey('drop_www_prefix', $response);
        $this->assertArrayHasKey('drop_directories', $response);
        $this->assertArrayHasKey('created_at', $response);


        $this->assertEquals(146, $response['id']);
        $this->assertEquals("google.com", $response['title']);
        $this->assertEquals("google.com", $response['url']);
        $this->assertEquals(true, $response['drop_www_prefix']);
        $this->assertEquals(true, $response['drop_directories']);
        $this->assertInstanceOf(Carbon::class, $response['created_at']);
    }

    /** @test */
    public function it_can_update_a_site()
    {
        $expectedArguments = [
            'sites/update', ['id' => 13, 'url' => 'http%3A%2F%2Fgoogle.com', 'title' => 'my%20site', 'drop_www_prefix' => true, 'drop_directories' => true]
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

        $response = $this->stat->sites()->update(13, 'my site', 'http://google.com', true, true);

        $this->assertInstanceOf(StatSite::class, $response);
        $this->assertEquals(8, count($response->toArray()));

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('project_id', $response);
        $this->assertArrayHasKey('title', $response);
        $this->assertArrayHasKey('url', $response);
        $this->assertArrayHasKey('drop_www_prefix', $response);
        $this->assertArrayHasKey('drop_directories', $response);
        $this->assertArrayHasKey('created_at', $response);
        $this->assertArrayHasKey('updated_at', $response);


        $this->assertEquals(146, $response['id']);
        $this->assertEquals(13, $response['project_id']);
        $this->assertEquals("my site", $response['title']);
        $this->assertEquals("google.com", $response['url']);
        $this->assertEquals(true, $response['drop_www_prefix']);
        $this->assertEquals(true, $response['drop_directories']);
        $this->assertInstanceOf(Carbon::class, $response['created_at']);
        $this->assertInstanceOf(Carbon::class, $response['updated_at']);
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

        $response = $this->stat->sites()->rankingDistributions(13, Carbon::createFromDate(2016, 10, 1), Carbon::createFromDate(2016, 10, 2));

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

        $this->stat->sites()->rankingDistributions(13, Carbon::createFromDate(2016, 9, 1), Carbon::createFromDate(2016, 10, 5));
    }
    
    /** @test */
    public function it_can_get_the_sov_for_a_site()
    {
        $expectedArguments = [
            'sites/sov', ['id' => 13, 'from_date' => '2016-10-01' , 'to_date' => '2016-10-02', 'start' => 0, 'results' => 5000]
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

        $response = $this->stat->sites()->sov(13, Carbon::createFromDate(2016, 10, 1), Carbon::createFromDate(2016, 10, 2));


        $this->assertInstanceOf(Collection::class, $response);
        $this->assertInstanceOf(StatShareOfVoice::class, $response->first());
        $this->assertEquals(2, $response->count());
        $this->assertInstanceOf(Collection::class, $response->first()->sites);
        $this->assertInstanceOf(StatShareOfVoiceSite::class, $response->first()->sites->first());
        $this->assertInstanceOf(Carbon::class, $response->first()->date);
        $this->assertEquals('2016-10-01', $response->first()->date->toDateString());
        $this->assertEquals('www.example.de', $response->first()->sites->first()->domain);
    }

    /** @test */
    public function it_can_get_the_most_frequent_domains_for_google_from_a_site()
    {
        $expectedArguments = [
            'sites/most_frequent_domains', ['id' => 13, 'engine' => 'google']
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'Site' => [
                    [
                        'Domain' => 'xxx.com',
                        'TopTenResults' => '800',
                        'ResultsAnalyzed' => '16800',
                        'Coverage' => '4.76',
                        'AnalyzedOn' => '2016-12-25',
                    ],
                    [
                        'Domain' => 'yyy.com',
                        'TopTenResults' => '686',
                        'ResultsAnalyzed' => '16800',
                        'Coverage' => '4.08',
                        'AnalyzedOn' => '2016-12-25',
                    ],
                ]
            ]]);

        $response = $this->stat->sites()->mostFrequentDomains(13);


        $this->assertInstanceOf(Collection::class, $response);
        $this->assertInstanceOf(StatFrequentDomain::class, $response->first());
        $this->assertEquals(2, $response->count());
        $this->assertEquals('2016-12-25', $response->first()->analyzed_on->toDateString());
        $this->assertEquals('xxx.com', $response->first()->domain);
        $this->assertEquals(800, $response->first()->top_ten_results);
    }

    /** @test */
    public function it_can_get_the_most_frequent_domains_for_yahoo_from_a_site()
    {
        $expectedArguments = [
            'sites/most_frequent_domains', ['id' => 13, 'engine' => 'yahoo']
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'Site' => [
                    [
                        'Domain' => 'xxx.com',
                        'TopTenResults' => '800',
                        'ResultsAnalyzed' => '16800',
                        'Coverage' => '4.76',
                        'AnalyzedOn' => '2016-12-25',
                    ],
                    [
                        'Domain' => 'yyy.com',
                        'TopTenResults' => '686',
                        'ResultsAnalyzed' => '16800',
                        'Coverage' => '4.08',
                        'AnalyzedOn' => '2016-12-25',
                    ],
                ]
            ]]);

        $response = $this->stat->sites()->mostFrequentDomains(13, 'yahoo');


        $this->assertInstanceOf(Collection::class, $response);
        $this->assertInstanceOf(StatFrequentDomain::class, $response->first());
        $this->assertEquals(2, $response->count());
        $this->assertEquals('2016-12-25', $response->first()->analyzed_on->toDateString());
        $this->assertEquals('xxx.com', $response->first()->domain);
        $this->assertEquals(800, $response->first()->top_ten_results);
    }
}
