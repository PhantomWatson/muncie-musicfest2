<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\VolunteersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\VolunteersTable Test Case
 */
class VolunteersTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\VolunteersTable
     */
    public $Volunteers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.volunteers',
        'app.jobs',
        'app.jobs_volunteers'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Volunteers') ? [] : ['className' => 'App\Model\Table\VolunteersTable'];
        $this->Volunteers = TableRegistry::get('Volunteers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Volunteers);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
