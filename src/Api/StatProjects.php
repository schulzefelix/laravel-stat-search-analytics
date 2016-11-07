<?php

namespace SchulzeFelix\Stat\Api;

use Illuminate\Support\Collection;

class StatProjects extends BaseStat
{
    public function list() : Collection
    {
        $response = $this->performQuery('projects/list');

        return collect($response['Result']);
    }

    public function create($name)
    {
        $response = $this->performQuery('projects/create', ['name' => $name]);

        return $response['Result'];
    }

    public function update($id, $name)
    {
        $response = $this->performQuery('projects/update', ['id' => $id, 'name' => $name]);

        return $response['Result'];
    }

    public function delete($id)
    {
        $response = $this->performQuery('projects/delete', ['id' => $id]);

        return (int)$response['Result']['Id'];
    }
}
