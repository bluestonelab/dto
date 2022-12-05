<?php

namespace Tests;

use Bluestone\DataTransferObject\Attributes\CastWith;
use Bluestone\DataTransferObject\DataTransferObject;
use PHPUnit\Framework\TestCase;
use Tests\Artifacts\FullName;
use Tests\Artifacts\FullNameCaster;
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
        $jane = new Student(full_name: 'Jane Doe', skill: $skill, gender: Gender::FEMALE);
        $john = new Student(full_name: 'John Doe', gender: 'Male');
        $university = new University(name: 'High Tech School', students: [$jane, $john]);

        $this->assertEquals('High Tech School', $university->name);
        $this->assertContainsOnlyInstancesOf(Student::class, $university->students);
        $this->assertEquals('Jane', $university->students[0]->fullName->firstname);
        $this->assertInstanceOf(Gender::class, $university->students[0]->gender);
        $this->assertEquals(Gender::FEMALE, $university->students[0]->gender);
        $this->assertEquals('Developer', $university->students[0]->skill->name);
        $this->assertEquals('John', $university->students[1]->fullName->firstname);
        $this->assertInstanceOf(Gender::class, $university->students[1]->gender);
        $this->assertEquals(Gender::MALE, $university->students[1]->gender);
        $this->assertNull($university->students[1]->skill);
    }

    /** @test */
    public function can_instantiate_complex_dto_from_array()
    {
        $jane = new Student([
            'full_name' => 'Jane Doe',
            'skill' => [
                'name' => 'Architect',
            ],
            'gender' => 'Unknown',
        ]);

        $this->assertEquals('Jane', $jane->fullName->firstname);
        $this->assertEquals('Doe', $jane->fullName->lastname);
        $this->assertEquals(Gender::UNKNOWN, $jane->gender);
    }

    /** @test */
    public function can_instantiate_complex_dto_with_casting()
    {
        $university = new University([
            'name' => 'Nanar Factory',
            'students' => [
                ['full_name' => 'Steven Spielberg'],
                ['full_name' => 'James Cameron']
            ],
        ]);

        $this->assertContainsOnlyInstancesOf(Student::class, $university->students);
        $this->assertEquals('Spielberg', $university->students[0]->fullName->lastname);
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
        $jane = new Student(full_name: 'Jane Doe', skill: $skill, gender: 'Female', ratings: ['A', 'A+', 'B']);
        $john = new Student(full_name: 'John Doe', ratings: ['A', 'B']);
        $university = new University(name: 'High Tech School', students: [$jane, $john]);

        $expectedArray = [
            'name' => 'High Tech School',
            'students' => [
                [
                    'full_name' => 'Jane Doe',
                    'skill' => [
                        'name' => 'Developer',
                    ],
                    'ratings' => ['A', 'A+', 'B'],
                    'gender' => 'Female',
                ],
                [
                    'full_name' => 'John Doe',
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
        $jane = new Student(full_name: 'Jane Doe', skill: $skill, ratings: ['A'], gender: Gender::UNKNOWN);

        $expectedJson = '{"full_name":"Jane Doe","gender":"Unknown","skill":{"name":"Developer"},"ratings":["A"]}';

        $this->assertEquals($expectedJson, json_encode($jane));
    }

    /** @test */
    public function can_serialize_dto_with_casting_to_json()
    {
        $class = new class(fullName: "Bry Azure") extends DataTransferObject {
            #[CastWith(FullNameCaster::class)]
            public FullName $fullName;
        };

        $expectedJson = '{"fullName":"Bry Azure"}';

        $this->assertEquals($expectedJson, json_encode($class));
    }

    /** @test */
    public function can_unserialize_dto_from_json()
    {
        $json = '{"full_name":"Jane Doe","gender":"Unknown","skill":{"name":"Developer"},"gender":"Female","ratings":["A"]}';

        $jane = new Student(json_decode($json, true));

        $this->assertEquals('Jane', $jane->fullName->firstname);
        $this->assertEquals(Gender::FEMALE, $jane->gender);
        $this->assertEquals('Developer', $jane->skill->name);
        $this->assertEquals(['A'], $jane->ratings);
    }
}
