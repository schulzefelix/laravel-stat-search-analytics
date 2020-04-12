<?php

namespace SchulzeFelix\Stat\Api;

use Illuminate\Support\Collection;
use SchulzeFelix\Stat\Objects\StatSubAccount;

class StatSubAccounts extends BaseStat
{
    /**
     * @return Collection
     */
    public function list(): Collection
    {
        $response = $this->performQuery('subaccounts/list');

        $subaccounts = collect();

        if (! isset($response['User'])) {
            return $subaccounts;
        }

        if (isset($response['User']['Id'])) {
            $subaccounts->push($response['User']);
        } else {
            $subaccounts = collect($response['User']);
        }

        $subaccounts->transform(function ($item, $key) {
            return new StatSubAccount([
                'id' => $item['Id'],
                'login' => $item['Login'],
                'api_key' => $item['ApiKey'],
                'created_at' => $item['CreatedAt'],
            ]);
        });

        return $subaccounts;
    }
}
