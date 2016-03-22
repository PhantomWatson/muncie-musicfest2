<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SongsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SongsTable Test Case
 */
class SongsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\SongsTable
     */
    public $Songs;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.songs',
        'app.bands',
        'app.band_music',
        'app.band_music2012',
        'app.band_music2013',
        'app.band_pictures',
        'app.band_pictures2012',
        'app.band_pictures2013',
        'app.pictures',
        'app.pictures2011',
        'app.pictures2012',
        'app.pictures2013',
        'app.slots',
        'app.stages',
        'app.slots2011',
        'app.slots2012',
        'app.slots2013',
        'app.songs2011',
        'app.songs2012',
        'app.songs2013',
        'app.votes',
        'app.users',
        'app.votes2012'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Songs') ? [] : ['className' => 'App\Model\Table\SongsTable'];
        $this->Songs = TableRegistry::get('Songs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Songs);

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
