<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use SchulzeFelix\Stat\Objects\StatBill;
use SchulzeFelix\Stat\Objects\StatBillKeywordType;
use SchulzeFelix\Stat\Objects\StatBillKeywordTypes;
use SchulzeFelix\Stat\Objects\StatBillOptionalServiceType;
use SchulzeFelix\Stat\Objects\StatBillServices;
use SchulzeFelix\Stat\Objects\StatBillSummary;
use SchulzeFelix\Stat\Objects\StatSite;
use SchulzeFelix\Stat\Objects\StatSubAccount;
use SchulzeFelix\Stat\Objects\StatTag;

class StatBilling extends BaseStat
{
    public function bill($year, $month)
    {
        $response = $this->performQuery('billing/bill', ['year' => $year, 'month' => $month]);

        $statBill = new StatBill();
        $statBill->summary = $this->extractSummary($response);
        $statBill->services = new StatBillServices();

        $statBill->services->keywords = new StatBillKeywordTypes();

        $statBill->services->keywords->under_commit = new StatBillKeywordType([
            'count' => $response['Services']['Keywords']['UnderCommit']['Count'],
            'price' => $response['Services']['Keywords']['UnderCommit']['Price'],
            'total' => $response['Services']['Keywords']['UnderCommit']['Total'],
        ]);

        $statBill->services->keywords->over_commit = new StatBillKeywordType([
            'count' => $response['Services']['Keywords']['OverCommit']['Count'],
            'price' => $response['Services']['Keywords']['OverCommit']['Price'],
            'total' => $response['Services']['Keywords']['OverCommit']['Total'],
        ]);

        $statBill->services->keywords->non_unique = new StatBillKeywordType([
            'count' => $response['Services']['Keywords']['NonUnique']['Count'],
            'price' => $response['Services']['Keywords']['NonUnique']['Price'],
            'total' => $response['Services']['Keywords']['NonUnique']['Total'],
        ]);

        $statBill->services->optional_services = $this->extractOptionalServices(array_get($response, 'Services.OptionalServices'));

        return $statBill;
    }

    public function userBreakdown($year, $month)
    {
        $response = $this->performQuery('billing/user_breakdown', ['year' => $year, 'month' => $month]);

        $statBill = new StatBill();
        $statBill->summary = $this->extractSummary($response);
        $statBill->users = new Collection();

        foreach ($response['Users']['User'] as $user) {
            $statBill->users->push(new StatSubAccount([
                'id' => array_get($user, 'Id'),
                'name' => array_get($user, 'Name'),
                'count' => array_get($user, 'Count'),
                'percentage_of_bill' => array_get($user, 'PercentageOfBill'),
                'deleted' => filter_var(array_get($user, 'Deleted'), FILTER_VALIDATE_BOOLEAN),
                'total' => array_get($user, 'Total'),
            ]));
        }

        return $statBill;
    }

    public function siteBreakdown($year, $month, $charged_only = false)
    {
        $response = $this->performQuery('billing/site_breakdown', ['year' => $year, 'month' => $month, 'charged_only' => $charged_only]);

        $statBill = new StatBill();
        $statBill->summary = $this->extractSummary($response);
        $statBill->sites = new Collection();

        foreach ($response['Sites']['Site'] as $site) {
            $statSite = new StatSite([
                'id' => array_get($site, 'Id'),
                'title' => array_get($site, 'Title'),
                'url' => array_get($site, 'URL'),
                'project_id' => array_get($site, 'ProjectId'),
                'project_name' => array_get($site, 'ProjectName'),
                'folder_id' => array_get($site, 'FolderId'),
                'folder_name' => array_get($site, 'FolderName'),
                'deleted' => filter_var(array_get($site, 'Deleted'), FILTER_VALIDATE_BOOLEAN),
                'services' => new StatBillServices(),
            ]);

            $statSite->services->keywords = new StatBillKeywordType([
                'count' => array_get($site, 'Services.Keywords.Count'),
                'percentage_of_bill' => array_get($site, 'Services.Keywords.PercentageOfBill'),
                'total' => array_get($site, 'Services.Keywords.Total'),
            ]);
            $statSite->services->optional_services = $this->extractOptionalServices(array_get($site, 'Services.OptionalServices'));
            $statSite->services->total = array_get($site, 'Services.Total');

            $statBill->sites->push($statSite);
        }

        return $statBill;
    }

    /**
     * @param $response
     * @return StatBillSummary
     */
    private function extractSummary($response)
    {
        $statBillSummary = new StatBillSummary([
            'start_date'             => $response['Summary']['StartDate'],
            'end_date'               => $response['Summary']['EndDate'],
            'min_committed_charge'   => (float)array_get($response, 'Summary.MinCommittedCharge', 0.0),
            'tracked_keywords'       => (int)$response['Summary']['TrackedKeywords'],
            'tracked_keywords_total' => (float)$response['Summary']['TrackedKeywordsTotal'],
            'optional_service_total' => (float)$response['Summary']['OptionalServiceTotal'],
            'total'                  => (float)$response['Summary']['Total'],
        ]);

        return $statBillSummary;
    }

    private function extractOptionalServices($optionalServices)
    {
        $services = new Collection();

        if (is_null($optionalServices['OptionalService'])) {
            return $services;
        }

        foreach ($optionalServices['OptionalService'] as $service) {
            $services->push(new StatBillOptionalServiceType([
                'type' => array_get($service, 'type'),
                'count' => array_get($service, 'Count'),
                'price' => array_get($service, 'Price'),
                'total' => array_get($service, 'Total'),
            ]));
        }

        return $services;
    }
}
