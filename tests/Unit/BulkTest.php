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

class BulkTest extends PHPUnit_Framework_TestCase
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
    public function it_can_list_bulk_exports_jobs()
    {
        $expectedArguments = [
            'bulk/list', []
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'resultsreturned' => "2",
                'totalresults' => "2",
                'Result' => [
                    [
                        'Id' => '100',
                        'JobType' => 'ranks',
                        'Format' => 'xml',
                        'Date' => '2011-02-05',
                        'Status' => 'NotStarted',
                        'Url' => null,
                        'StreamUrl' => null,
                        'CreatedAt' => '2011-02-06',
                    ],
                    [
                        'Id' => '99',
                        'JobType' => 'ranks',
                        'Format' => 'json',
                        'Date' => '2014-02-04',
                        'Status' => 'Completed',
                        'Url' => null,
                        'StreamUrl' => null,
                        'CreatedAt' => '2014-02-05',
                    ],
                ],
            ]]);


        $response = $this->stat->bulk()->list();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertEquals(2, $response->count());

        $this->assertArrayHasKey('id', $response->first());
        $this->assertArrayHasKey('job_type', $response->first());
        $this->assertArrayHasKey('format', $response->first());
        $this->assertArrayHasKey('date', $response->first());
        $this->assertArrayHasKey('status', $response->first());
        $this->assertArrayHasKey('url', $response->first());
        $this->assertArrayHasKey('stream_url', $response->first());
        $this->assertArrayHasKey('created_at', $response->first());

        $this->assertInstanceOf(Carbon::class, $response->first()['date']);
        $this->assertInstanceOf(Carbon::class, $response->first()['created_at']);

        $this->assertEquals('ranks', $response->first()['job_type']);
        $this->assertEquals('NotStarted', $response->first()['status']);

    }


    /** @test */
    public function it_can_create_bulk_exports_jobs_for_ranks()
    {
        $expectedArguments = [
            'bulk/ranks', ['date' => '2016-11-08', 'site_id' => '1,2', 'rank_type' => 'highest', 'crawled_keywords_only' => true, 'currently_tracked_only' => false]
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'Result' => [
                    'Id' => '1',
                ]
            ]]);

        $response = $this->stat->bulk()->ranks(Carbon::create(2016, 11, 8), [1,2], 'highest', null, false, true);
        $this->assertEquals(1, $response);
    }

    /** @test */
    public function it_throw_an_exception_if_i_want_create_a_bulk_job_for_today()
    {
        $this->setExpectedException(ApiException::class);

        $response = $this->stat->bulk()->ranks(Carbon::now());

    }

    /** @test */
    public function it_throw_an_exception_if_i_want_create_a_bulk_job_for_the_future()
    {
        $this->setExpectedException(ApiException::class);

        $response = $this->stat->bulk()->ranks(Carbon::now()->addWeek());
    }


    /** @test */
    public function it_can_get_bulk_exports_jobs_status()
    {
        $expectedArguments = [
            'bulk/status', ['id' => 1]
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'Result' => [
                    'Id' => '1',
                    'JobType' => 'ranks',
                    'Format' => 'json',
                    'Date' => '2011-04-15',
                    'SiteId' => '1,5,10',
                    'Status' => 'InProgress',
                    'Url' => null,
                    'CreatedAt' => '2011-05-03',
                ]
            ]]);

        $response = $this->stat->bulk()->status(1);
        $this->assertInternalType('array', $response);
        $this->assertEquals(9, count($response));

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('job_type', $response);
        $this->assertArrayHasKey('format', $response);
        $this->assertArrayHasKey('date', $response);
        $this->assertArrayHasKey('sites', $response);
        $this->assertArrayHasKey('url', $response);
        $this->assertArrayHasKey('stream_url', $response);
        $this->assertArrayHasKey('created_at', $response);
        $this->assertInstanceOf(Collection::class, $response['sites']);

        $this->assertEquals(1, $response['id']);
        $this->assertEquals('ranks', $response['job_type']);
        $this->assertEquals('json', $response['format']);
        $this->assertEquals('InProgress', $response['status']);
        $this->assertEquals(null, $response['url']);
        $this->assertEquals(null, $response['stream_url']);
        $this->assertEquals(3, $response['sites']->count());
        $this->assertInstanceOf(Carbon::class, $response['created_at']);
        $this->assertEquals('2011-05-03', $response['created_at']->toDateString());
        $this->assertInstanceOf(Carbon::class, $response['date']);
        $this->assertEquals('2011-04-15', $response['date']->toDateString());
    }

    /** @test */
    public function it_can_delete_a_bulk_exports_jobs()
    {
        $expectedArguments = [
            'bulk/delete', ['id' => 1]
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'Result' => [
                    'Id' => '1'
                ]
            ]]);

        $response = $this->stat->bulk()->delete(1);
        $this->assertInternalType('int', $response);
        $this->assertEquals(1, $response);
    }

    /** @test */
    public function it_can_create_bulk_exports_jobs_for_site_ranking_distributions()
    {
        $expectedArguments = [
            'bulk/site_ranking_distributions', ['date' => '2016-11-08']
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'Result' => [
                    'Id' => '1',
                ]
            ]]);

        $response = $this->stat->bulk()->siteRankingDistributions(Carbon::create(2016, 11, 8));
        $this->assertEquals(1, $response);
    }

    /** @test */
    public function it_throw_an_exception_if_i_want_create_a_bulk_job_site_ranking_distributions_for_the_future()
    {
        $this->setExpectedException(ApiException::class);
        $response = $this->stat->bulk()->siteRankingDistributions(Carbon::now()->addMonth());
    }

    /** @test */
    public function it_can_create_bulk_exports_jobs_for_tag_ranking_distributions()
    {
        $expectedArguments = [
            'bulk/tag_ranking_distributions', ['date' => '2016-11-08']
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'Result' => [
                    'Id' => '1',
                ]
            ]]);

        $response = $this->stat->bulk()->tagRankingDistributions(Carbon::create(2016, 11, 8));
        $this->assertEquals(1, $response);
    }

    /** @test */
    public function it_throw_an_exception_if_i_want_create_a_bulk_job_tag_ranking_distributions_for_the_future()
    {
        $this->setExpectedException(ApiException::class);
        $response = $this->stat->bulk()->siteRankingDistributions(Carbon::now()->addDay());
    }


    /** @test */
    public function it_can_retrieve_a_bulk_export_for_a_single_project_with_single_site_type_highest()
    {
        $expectedArguments = [
            'bulk/status', ['id' => 1787]
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'Result' => [
                    'Id' => '1787',
                    'JobType' => 'ranks',
                    'Format' => 'json',
                    'Date' => '2016-11-20',
                    'SiteId' => '19',
                    'Status' => 'Completed',
                    'Url' => 'https://try.getstat.com/bulk_reports/download_report/1787?key=l3fr7fzxwjolserpep3ndcstgo232uk4ok1l8o18',
                    'StreamUrl' => 'https://try.getstat.com/bulk_reports/stream_report/1787?key=l3fr7fzxwjolserpep3ndcstgo232uk4ok1l8o18',
                    'CreatedAt' => '2016-11-21',
                ]
            ]]);

        $expectedArguments = [
            'https://try.getstat.com/bulk_reports/stream_report/1787?key=l3fr7fzxwjolserpep3ndcstgo232uk4ok1l8o18'
        ];
        $expectedResponse = json_decode(file_get_contents('tests/Unit/json-responses/1787.json'), true);
        $this->statClient
            ->shouldReceive('downloadBulkJobStream')->withArgs($expectedArguments)
            ->once()
            ->andReturn($expectedResponse);


        $response = $this->stat->bulk()->get(1787);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertEquals(1, $response->count());

        $this->assertEquals(1, $response->first()['total_sites']);
        $this->assertEquals('Development', $response->first()['name']);
        $this->assertInstanceOf(Carbon::class, $response->first()['created_at']);
        $this->assertInstanceOf(Collection::class, $response->first()['sites']);

        $this->assertEquals(5, $response->first()['sites']->first()['total_keywords']);
        $this->assertInstanceOf(Carbon::class, $response->first()['sites']->first()['created_at']);
        $this->assertInstanceOf(Collection::class, $response->first()['sites']->first()['keywords']);

        $this->assertJsonStringEqualsJsonFile('tests/Unit/json-responses/1787-transformed.json', $response->toJson());
    }

    /** @test */
    public function it_can_retrieve_a_bulk_export_for_a_single_project_with_single_site_type_all()
    {
        $expectedArguments = [
            'bulk/status', ['id' => 1790]
        ];

        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['Response' => [
                'responsecode' => "200",
                'Result' => [
                    'Id' => '1790',
                    'JobType' => 'ranks',
                    'Format' => 'json',
                    'Date' => '2016-11-20',
                    'Status' => 'Completed',
                    'Url' => 'https://try.getstat.com/bulk_reports/download_report/1790?key=l3fr7fzxwjolserpep3ndcstgo232uk4ok1l8o18',
                    'StreamUrl' => 'https://try.getstat.com/bulk_reports/stream_report/1790?key=l3fr7fzxwjolserpep3ndcstgo232uk4ok1l8o18',
                    'CreatedAt' => '2016-11-21',
                ]
            ]]);

        $expectedArguments = [
            'https://try.getstat.com/bulk_reports/stream_report/1790?key=l3fr7fzxwjolserpep3ndcstgo232uk4ok1l8o18'
        ];
        $expectedResponse = json_decode(file_get_contents('tests/Unit/json-responses/1790.json'), true);
        $this->statClient
            ->shouldReceive('downloadBulkJobStream')->withArgs($expectedArguments)
            ->once()
            ->andReturn($expectedResponse);


        $response = $this->stat->bulk()->get(1790);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertEquals(1, $response->count());

        $this->assertEquals(1, $response->first()['total_sites']);
        $this->assertEquals('Development', $response->first()['name']);
        $this->assertInstanceOf(Carbon::class, $response->first()['created_at']);
        $this->assertInstanceOf(Collection::class, $response->first()['sites']);

        $this->assertEquals(5, $response->first()['sites']->first()['total_keywords']);
        $this->assertInstanceOf(Carbon::class, $response->first()['sites']->first()['created_at']);
        $this->assertInstanceOf(Collection::class, $response->first()['sites']->first()['keywords']);

        $this->assertJsonStringEqualsJsonFile('tests/Unit/json-responses/1790-transformed.json', $response->toJson());
    }

}
