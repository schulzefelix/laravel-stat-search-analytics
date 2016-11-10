<?php

namespace SchulzeFelix\Stat\Api;

use Illuminate\Support\Collection;

class StatProjects extends BaseStat
{
    public function list() : Collection
    {
        $response = $this->performQuery('projects/list');

        $projects = collect($response['Result'])->transform(function ($project, $key) {
            return [
                'id' => $project['Id'],
                'name' => $project['Name'],
                'total_sites' => $project['TotalSites'],
                'created_at' => $project['CreatedAt'],
                'updated_at' => $project['UpdatedAt'],
            ];
        });

        return $projects;
    }

    public function create($name)
    {
        $response = $this->performQuery('projects/create', ['name' => $name]);

        $project = [
            'id' => $response['Result']['Id'],
            'name' => $response['Result']['Name'],
            'created_at' => $response['Result']['CreatedAt'],
            'updated_at' => $response['Result']['UpdatedAt'],
        ];

        return $project;
    }

    public function update($id, $name)
    {
        $response = $this->performQuery('projects/update', ['id' => $id, 'name' => $name]);

        $project = [
            'id' => $response['Result']['Id'],
            'name' => $response['Result']['Name'],
            'created_at' => $response['Result']['CreatedAt'],
            'updated_at' => $response['Result']['UpdatedAt'],
        ];

        return $project;
    }

    public function delete($id)
    {
        $response = $this->performQuery('projects/delete', ['id' => $id]);

        return (int)$response['Result']['Id'];
    }
}
