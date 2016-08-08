<?php
namespace App\Model\Table;

use App\Model\Entity\Slot;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Slots Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Stages
 * @property \Cake\ORM\Association\BelongsTo $Bands
 */
class SlotsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('slots');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('Stages', [
            'foreignKey' => 'stage_id'
        ]);
        $this->belongsTo('Bands', [
            'foreignKey' => 'band_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        $validator
            ->add('time', 'valid', ['rule' => 'time'])
            ->allowEmpty('time');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['stage_id'], 'Stages'));
        $rules->add($rules->existsIn(['band_id'], 'Bands'));
        return $rules;
    }

    /**
     * Sorts stage slots so that PM times come before AM times
     *
     * Assumes that no bands are booked before noon. This assures that bands booked at midnight and 1am properly
     * appear AFTER bands at 11pm.
     *
     * @param $slots
     * @return array
     */
    public function sortSlots($slots)
    {
        $sortedSlots = [];
        foreach ($slots as $slot) {
            if ($slot->time->format('a') == 'pm') {
                $sortedSlots[] = $slot;
            }
        }
        foreach ($slots as $slot) {
            if ($slot->time->format('a') == 'am') {
                $sortedSlots[] = $slot;
            }
        }
        return $sortedSlots;
    }
}
