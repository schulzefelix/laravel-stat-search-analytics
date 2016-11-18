<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class StatProjects extends BaseStat
{

    /**
     * @return Collection
     */
    public function list() : Collection
    {
        $response = $this->performQuery('projects/list');

        $projects = collect($response['Result'])->transform(function ($project, $key) {
            return [
                'id' => (int)$project['Id'],
                'name' => $project['Name'],
                'total_sites' => (int)$project['TotalSites'],
                'created_at' => Carbon::parse($project['CreatedAt']),
                'updated_at' => Carbon::parse($project['UpdatedAt']),
            ];
        });

        return $projects;
    }

    /**
     * @param $name
     * @return array
     */
    public function create($name)
    {
        $response = $this->performQuery('projects/create', ['name' => $name]);

        $project = [
            'id' => (int)$response['Result']['Id'],
            'name' => $response['Result']['Name'],
            'created_at' => Carbon::parse($response['Result']['CreatedAt']),
            'updated_at' => Carbon::parse($response['Result']['UpdatedAt']),
        ];

        return $project;
    }

    /**
     * @param $id
     * @param $name
     * @return array
     */
    public function update($id, $name)
    {
        $response = $this->performQuery('projects/update', ['id' => $id, 'name' => $name]);

        $project = [
            'id' => (int)$response['Result']['Id'],
            'name' => $response['Result']['Name'],
            'created_at' => Carbon::parse($response['Result']['CreatedAt']),
            'updated_at' => Carbon::parse($response['Result']['UpdatedAt']),
        ];

        return $project;
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
