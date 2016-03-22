<?php
namespace App\Model\Table;

use App\Model\Entity\Stage;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Stages Model
 *
 * @property \Cake\ORM\Association\HasMany $Slots
 */
class StagesTable extends Table
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

        $this->table('stages');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->hasMany('Slots', [
            'foreignKey' => 'stage_id'
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
            ->add('age_restriction', 'valid', ['rule' => 'boolean'])
            ->requirePresence('age_restriction', 'create')
            ->notEmpty('age_restriction');

        $validator
            ->requirePresence('address', 'create')
            ->notEmpty('address');

        return $validator;
    }
}
