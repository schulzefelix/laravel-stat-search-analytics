<?php

namespace SchulzeFelix\Stat\Tests\Unit;

use Mockery;
use SchulzeFelix\Stat\Stat;
use PHPUnit\Framework\TestCase;
use SchulzeFelix\Stat\StatClient;
use Illuminate\Support\Collection;
use SchulzeFelix\Stat\Objects\StatSite;
use SchulzeFelix\Stat\Objects\StatSubAccount;
use SchulzeFelix\Stat\Objects\StatBillSummary;
use SchulzeFelix\Stat\Objects\StatBillServices;
use SchulzeFelix\Stat\Objects\StatBillKeywordType;
use SchulzeFelix\Stat\Objects\StatBillKeywordTypes;
use SchulzeFelix\Stat\Objects\StatBillOptionalServiceType;

class BillingTest extends TestCase
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
    public function it_can_pull_the_montly_bill()
    {
        $expectedArguments = [
            'billing/bill', ['year' => 2016, 'month' => 12],
        ];
        $expectedResponse = json_decode(file_get_contents('tests/Unit/json-responses/bill.json'), true);
        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn($expectedResponse);

        $response = $this->stat->billing()->bill(2016, 12);
        $this->assertInstanceOf(StatBillSummary::class, $response->summary);
        $this->assertInstanceOf(StatBillServices::class, $response->services);
        $this->assertInstanceOf(StatBillKeywordTypes::class, $response->services->keywords);
        $this->assertInstanceOf(StatBillKeywordType::class, $response->services->keywords->under_commit);
        $this->assertInstanceOf(Collection::class, $response->services->optional_services);
        $this->assertInstanceOf(StatBillOptionalServiceType::class, $response->services->optional_services->first());

        $this->assertJsonStringEqualsJsonFile('tests/Unit/json-responses/bill-transformed.json', $response->toJson());
    }

    /** @test */
    public function it_can_pull_the_montly_billing_user_breakdown()
    {
        $expectedArguments = [
            'billing/user_breakdown', ['year' => 2016, 'month' => 12],
        ];
        $expectedResponse = json_decode(file_get_contents('tests/Unit/json-responses/user_breakdown.json'), true);
        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn($expectedResponse);

        $response = $this->stat->billing()->userBreakdown(2016, 12);
        $this->assertInstanceOf(StatBillSummary::class, $response->summary);
        $this->assertInstanceOf(Collection::class, $response->users);
        $this->assertInstanceOf(StatSubAccount::class, $response->users->first());

        $this->assertJsonStringEqualsJsonFile('tests/Unit/json-responses/user_breakdown-transformed.json', $response->toJson());
    }

    /** @test */
    public function it_can_pull_the_montly_billing_site_breakdown()
    {
        $expectedArguments = [
            'billing/site_breakdown', ['year' => 2016, 'month' => 12, 'charged_only' => false],
        ];
        $expectedResponse = json_decode(file_get_contents('tests/Unit/json-responses/site_breakdown.json'), true);
        $this->statClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn($expectedResponse);

        $response = $this->stat->billing()->siteBreakdown(2016, 12);

        $this->assertInstanceOf(StatBillSummary::class, $response->summary);
        $this->assertInstanceOf(Collection::class, $response->sites);
        $this->assertInstanceOf(StatSite::class, $response->sites->first());
        $this->assertInstanceOf(StatBillServices::class, $response->sites->first()->services);
        $this->assertInstanceOf(StatBillKeywordType::class, $response->sites->first()->services->keywords);

        $this->assertJsonStringEqualsJsonFile('tests/Unit/json-responses/site_breakdown-transformed.json', $response->toJson());
    }
}
