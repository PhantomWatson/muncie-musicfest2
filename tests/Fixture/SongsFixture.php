<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SongsFixture
 *
 */
class SongsFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'band_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'title' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => '', 'comment' => '', 'precision' => null, 'fixed' => null],
        'filename' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => '', 'comment' => '', 'precision' => null, 'fixed' => null],
        'seconds' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'length' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => '', 'comment' => '', 'precision' => null, 'fixed' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'MyISAM',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'band_id' => 1,
            'title' => 'Lorem ipsum dolor sit amet',
            'filename' => 'Lorem ipsum dolor sit amet',
            'seconds' => 1,
            'length' => 'Lorem ip'
        ],
    ];
}
