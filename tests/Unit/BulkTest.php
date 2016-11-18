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



}
