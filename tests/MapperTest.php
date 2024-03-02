<?php

namespace Cable8mm\ImportFromDbToJekyll\Test;

use Cable8mm\ImportFromDbToJekyll\Mappers\Mapper;
use PHPUnit\Framework\TestCase;

final class MapperTest extends TestCase
{
    public function test_can_get_map_what_you_want()
    {
        $map = Mapper::getMap('DogStory');

        $this->assertIsArray($map);
    }

    public function test_can_get_flatten_fields_from_mapper()
    {
        $map = Mapper::getMap('DogStory');

        $fields = (new Mapper(...$map))->fields();

        $this->assertIsArray($fields);
    }
}
