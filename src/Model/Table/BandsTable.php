<?php
namespace App\Model\Table;

use App\Model\Entity\Band;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Bands Model
 *
 * @property \Cake\ORM\Association\HasMany $Pictures
 * @property \Cake\ORM\Association\HasMany $Slots
 * @property \Cake\ORM\Association\HasMany $Songs
 * @property \Cake\ORM\Association\HasMany $Votes
 */
class BandsTable extends Table
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

        $this->table('bands');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Pictures', [
            'foreignKey' => 'band_id'
        ]);
        $this->hasMany('Slots', [
            'foreignKey' => 'band_id'
        ]);
        $this->hasMany('Songs', [
            'foreignKey' => 'band_id'
        ]);
        $this->hasMany('Votes', [
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
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->requirePresence('genre', 'create')
            ->notEmpty('genre');

        $validator
            ->requirePresence('description', 'create')
            ->notEmpty('description');

        $validator
            ->requirePresence('hometown', 'create')
            ->notEmpty('hometown');

        $validator
            ->add('email', 'valid', ['rule' => 'email'])
            ->requirePresence('email', 'create')
            ->notEmpty('email');

        $validator
            ->requirePresence('check_name', 'create')
            ->notEmpty('check_name');

        $validator
            ->requirePresence('rep_name', 'create')
            ->notEmpty('rep_name');

        $validator
            ->requirePresence('phone', 'create')
            ->notEmpty('phone');

        $validator
            ->requirePresence('member_count', 'create')
            ->notEmpty('member_count');

        $validator
            ->requirePresence('member_names', 'create')
            ->notEmpty('member_names');

        $validator
            ->add('members_under_21', 'valid', ['rule' => 'boolean'])
            ->requirePresence('members_under_21', 'create')
            ->notEmpty('members_under_21');

        $validator
            ->requirePresence('tier', 'create')
            ->notEmpty('tier');

        $validator
            ->requirePresence('stage_setup', 'create')
            ->notEmpty('stage_setup');

        $validator
            ->requirePresence('user_id', 'create')
            ->notEmpty('user_id');

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
        $rules->add($rules->isUnique(['email']));
        return $rules;
    }
}
