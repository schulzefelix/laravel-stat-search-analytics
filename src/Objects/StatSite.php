<?php namespace SchulzeFelix\Stat\Objects;

use SchulzeFelix\DataTransferObject\DataTransferObject;

class StatSite extends DataTransferObject
{
    protected $casts = [
        'id' => 'integer',
        'project_id' => 'integer',
        'total_keywords' => 'integer',
        'drop_www_prefix' => 'boolean',
        'drop_directories' => 'boolean',
    ];


    public function setDropWWWPrefixAttribute($value)
    {
        return $this->attributes['drop_www_prefix'] = (bool)$value;
    }

    public function setDropDirectoriesAttribute($value)
    {
        return $this->attributes['drop_directories'] = (bool)$value;
    }

}