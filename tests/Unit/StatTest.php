<?php

namespace SchulzeFelix\Stat\Tests\Unit;

use Illuminate\Support\Collection;
use Mockery;
use PHPUnit_Framework_TestCase;
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
    public function it_can_list_all_projects()
    {
        $expectedArguments = [
            'projects/list', []
        ];
        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'resultsreturned' => "2",
                'responsecode' => "200",
                'Result' => [
                    [
                        'Id' => 613,
                        'Name' => "Muffins",
                        'TotalSites' => "1",
                        'CreatedAt' => "2016-11-03",
                        'UpdatedAt' => "2016-11-03",
                        'RequestUrl' => "/sites/list?project_id=613&format=json",
                    ],
                    [
                        'Id' => 614,
                        'Name' => "Apple Pie",
                        'TotalSites' => "2",
                        'CreatedAt' => "2016-11-05",
                        'UpdatedAt' => "2016-11-05",
                        'RequestUrl' => "/sites/list?project_id=613&format=json",
                    ],
                ]
            ]]);

        $response = $this->stat->projects()->list();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertEquals(2, $response->count());
        $this->assertEquals(613, $response->first()['Id']);
        $this->assertEquals('2016-11-03', $response->first()['UpdatedAt']);
    }

    /** @test */
    public function it_can_create_a_new_project()
    {
        $expectedArguments = [
            'projects/create', ['name' => 'Cheese Cake']
        ];
        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'Result' => [
                    'Id' => 615,
                    'Name' => "Cheese Cake",
                    'CreatedAt' => "2016-11-06",
                    'UpdatedAt' => "2016-11-06",
                ]
            ]]);

        $response = $this->stat->projects()->create('Cheese Cake');

        $this->assertInternalType('array', $response);
        $this->assertEquals(615, $response['Id']);
        $this->assertEquals('Cheese Cake', $response['Name']);
        $this->assertEquals('2016-11-06', $response['UpdatedAt']);
    }

    /** @test */
    public function it_can_update_an_existing_project()
    {
        $expectedArguments = [
            'projects/update', ['id' => 615, 'name' => 'Cheese Cake Factory']
        ];
        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'Result' => [
                    'Id' => 615,
                    'Name' => "Cheese Cake Factory",
                    'CreatedAt' => "2016-11-06",
                    'UpdatedAt' => "2016-11-07",
                ]
            ]]);

        $response = $this->stat->projects()->update(615, 'Cheese Cake Factory');

        $this->assertInternalType('array', $response);
        $this->assertEquals(615, $response['Id']);
        $this->assertEquals('Cheese Cake Factory', $response['Name']);
        $this->assertEquals('2016-11-06', $response['CreatedAt']);
        $this->assertEquals('2016-11-07', $response['UpdatedAt']);
    }

    /** @test */
    public function it_can_delete_a_project()
    {
        $expectedArguments = [
            'projects/delete', ['id' => 615]
        ];
        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'Result' => [
                    'Id' => 615
                ]
            ]]);

        $response = $this->stat->projects()->delete(615);

        $this->assertEquals(615, $response);
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

}
