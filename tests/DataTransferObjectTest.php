<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Artifacts\Skill;
use Tests\Artifacts\Student;
use Tests\Artifacts\University;
use Tests\Artifacts\Gender;

class DataTransferObjectTest extends TestCase
{
    /** @test */
    public function can_instantiate_dto()
    {
        $skill = new Skill(name: 'Developer');

        $this->assertEquals('Developer', $skill->name);
    }

    /** @test */
    public function can_instantiate_dto_from_array()
    {
        $skill = new Skill(['name' => 'Developer']);

        $this->assertEquals('Developer', $skill->name);
    }

    /** @test */
    public function can_instantiate_complex_dto()
    {
        $skill = new Skill(name: 'Developer');
        $jane = new Student(name: 'Jane', skill: $skill, gender: Gender::FEMALE);
        $john = new Student(name: 'John', gender: 'Male');
        $university = new University(name: 'High Tech School', students: [$jane, $john]);

        $this->assertEquals('High Tech School', $university->name);
        $this->assertContainsOnlyInstancesOf(Student::class, $university->students);
        $this->assertEquals('Jane', $university->students[0]->name);
        $this->assertInstanceOf(Gender::class, $university->students[0]->gender);
        $this->assertEquals(Gender::FEMALE, $university->students[0]->gender);
        $this->assertEquals('Developer', $university->students[0]->skill->name);
        $this->assertEquals('John', $university->students[1]->name);
        $this->assertInstanceOf(Gender::class, $university->students[1]->gender);
        $this->assertEquals(Gender::MALE, $university->students[1]->gender);
        $this->assertNull($university->students[1]->skill);
    }

    /** @test */
    public function can_instantiate_complexe_dto_from_array()
    {
        $jane = new Student([
            'name' => 'Jane',
            'skill' => [
                'name' => 'Architect',
            ],
        ]);

        $this->assertEquals('Jane', $jane->name);
    }

    /** @test */
    public function can_transform_dto_to_array()
    {
        $skill = new Skill(name: 'Developer');

        $this->assertEquals(['name' => 'Developer'], $skill->toArray());
    }

    /** @test */
    public function can_transform_complex_dto_to_array()
    {
        $skill = new Skill(name: 'Developer');
        $jane = new Student(name: 'Jane', skill: $skill, ratings: ['A', 'A+', 'B']);
        $john = new Student(name: 'John', ratings: ['A', 'B']);
        $university = new University(name: 'High Tech School', students: [$jane, $john]);

        $expectedArray = [
            'name' => 'High Tech School',
            'students' => [
                [
                    'name' => 'Jane',
                    'skill' => [
                        'name' => 'Developer',
                    ],
                    'ratings' => ['A', 'A+', 'B'],
                    'gender' => null,
                ],
                [
                    'name' => 'John',
                    'skill' => null,
                    'ratings' => ['A', 'B'],
                    'gender' => null,
                ],
            ],
        ];

        $this->assertEquals($expectedArray, $university->toArray());
    }

    /** @test */
    public function can_serialize_dto_to_json()
    {
        $skill = new Skill(name: 'Developer');
        $jane = new Student(name: 'Jane', skill: $skill, ratings: ['A'], gender: Gender::UNKNOWN);

        $expectedJson = '{"name":"Jane","gender":"Unknown","skill":{"name":"Developer"},"ratings":["A"]}';

        $this->assertEquals($expectedJson, json_encode($jane));
    }
}
