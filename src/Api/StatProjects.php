<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use SchulzeFelix\Stat\Objects\StatProject;
use SchulzeFelix\Stat\Stat;

class StatProjects extends BaseStat
{

    /**
     * @return Collection
     */
    public function list() : Collection
    {
        $response = $this->performQuery('projects/list');

        $projects = collect($response['Result'])->map(function ($project, $key) {
            return new StatProject([
                'id' => $project['Id'],
                'name' => $project['Name'],
                'total_sites' => $project['TotalSites'],
                'created_at' => $project['CreatedAt'],
                'updated_at' => $project['UpdatedAt'],
            ]);
        });

        return $projects;
    }

    /**
     * @param $name
     * @return StatProject
     */
    public function create($name)
    {
        $response = $this->performQuery('projects/create', ['name' => $name]);

        return new StatProject([
            'id' => $response['Result']['Id'],
            'name' => $response['Result']['Name'],
            'total_sites' => 0,
            'created_at' => $response['Result']['CreatedAt'],
            'updated_at' => $response['Result']['UpdatedAt'],
        ]);
    }

    /**
     * @param $id
     * @param $name
     * @return StatProject
     */
    public function update($id, $name)
    {
        $response = $this->performQuery('projects/update', ['id' => $id, 'name' => $name]);

        return new StatProject([
            'id' => $response['Result']['Id'],
            'name' => $response['Result']['Name'],
            'created_at' => $response['Result']['CreatedAt'],
            'updated_at' => $response['Result']['UpdatedAt'],
        ]);

    }

    /**
     * @param $id
     * @return int
     */
    public function delete($id)
    {
        $response = $this->performQuery('projects/delete', ['id' => $id]);

        return (int)$response['Result']['Id'];
    }
}
