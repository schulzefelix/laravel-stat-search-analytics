<?php

namespace SchulzeFelix\Stat\Tests\Unit;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit_Framework_TestCase;
use SchulzeFelix\Stat\Exceptions\ApiException;
use SchulzeFelix\Stat\Stat;
use SchulzeFelix\Stat\StatClient;

class KeyswordsTest extends PHPUnit_Framework_TestCase
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
    public function testPlaceholder()
    {
        
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
