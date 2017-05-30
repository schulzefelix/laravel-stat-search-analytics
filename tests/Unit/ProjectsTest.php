<?php

namespace SchulzeFelix\Stat\Tests\Unit;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;
use SchulzeFelix\Stat\Objects\StatProject;
use SchulzeFelix\Stat\Stat;
use SchulzeFelix\Stat\StatClient;

class ProjectsTest extends TestCase
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
        $this->assertInstanceOf(StatProject::class, $response->first());

        $this->assertArrayHasKey('id', $response->first());
        $this->assertArrayHasKey('name', $response->first());
        $this->assertArrayHasKey('total_sites', $response->first());
        $this->assertArrayHasKey('created_at', $response->first());
        $this->assertArrayHasKey('updated_at', $response->first());

        $this->assertEquals(613, $response->first()->id);
        $this->assertEquals('Muffins', $response->first()->name);
        $this->assertEquals(1, $response->first()->total_sites);
        $this->assertInstanceOf(Carbon::class, $response->first()->created_at);
        $this->assertInstanceOf(Carbon::class, $response->first()->updated_at);
    }

    /** @test */
    public function it_can_create_a_new_project()
    {
        $expectedArguments = [
            'projects/create', ['name' => 'Cheese%20Cake']
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

        $this->assertInstanceOf(StatProject::class, $response);

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('created_at', $response);
        $this->assertArrayHasKey('updated_at', $response);

        $this->assertEquals(615, $response['id']);
        $this->assertEquals('Cheese Cake', $response['name']);
        $this->assertInstanceOf(Carbon::class, $response['created_at']);
        $this->assertInstanceOf(Carbon::class, $response['updated_at']);
    }

    /** @test */
    public function it_can_update_an_existing_project()
    {
        $expectedArguments = [
            'projects/update', ['id' => 615, 'name' => 'Cheese%20Cake%20Factory']
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

        $this->assertInstanceOf(StatProject::class, $response);

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('created_at', $response);
        $this->assertArrayHasKey('updated_at', $response);

        $this->assertEquals(615, $response->id);
        $this->assertEquals('Cheese Cake Factory', $response->name);
        $this->assertInstanceOf(Carbon::class, $response->created_at);
        $this->assertInstanceOf(Carbon::class, $response->updated_at);
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

        $this->assertInternalType('int', $response);
        $this->assertEquals(615, $response);
    }
}
