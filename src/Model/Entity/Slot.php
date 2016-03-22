<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Slot Entity.
 *
 * @property int $id
 * @property int $stage_id
 * @property \App\Model\Entity\Stage $stage
 * @property int $band_id
 * @property \App\Model\Entity\Band $band
 * @property \Cake\I18n\Time $time
 */
class Slot extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
