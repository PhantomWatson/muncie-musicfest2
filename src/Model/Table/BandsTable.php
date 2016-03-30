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
            ->requirePresence('user_id', 'create')
            ->notEmpty('user_id');

        $applicationSteps = $this->getApplicationSteps();
        foreach ($applicationSteps as $step) {
            $methodName = 'validationApplication'.ucfirst($step);
            $validator = $this->$methodName($validator);
        }

        return $validator;
    }

    /**
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationApplicationBasic(Validator $validator)
    {
        $validator
            ->requirePresence('name', 'create')
            ->add('name', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'An application has already been submitted for this band'
            ])
            ->notEmpty('name');

        $validator
            ->requirePresence('hometown', 'create')
            ->notEmpty('hometown');

        $validator
            ->requirePresence('description', 'create')
            ->notEmpty('description');

        $validator
            ->requirePresence('genre', 'create')
            ->notEmpty('genre');

        $validator
            ->requirePresence('tier', 'create')
            ->notEmpty('tier');

        return $validator;
    }

    /**
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationApplicationRepresentative(Validator $validator)
    {
        $validator
            ->requirePresence('rep_name', 'create')
            ->notEmpty('rep_name');

        $validator
            ->add('email', 'valid', ['rule' => 'email'])
            ->requirePresence('email', 'create')
            ->notEmpty('email');

        $validator
            ->requirePresence('phone', 'create')
            ->notEmpty('phone');

        return $validator;
    }

    /**
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationApplicationWebsites(Validator $validator)
    {
        return $validator;
    }

    /**
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationApplicationMembers(Validator $validator)
    {
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

        return $validator;
    }

    /**
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationApplicationPictures(Validator $validator)
    {
        return $validator;
    }

    /**
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationApplicationSongs(Validator $validator)
    {
        return $validator;
    }

    /**
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationApplicationStage(Validator $validator)
    {
        $validator
            ->requirePresence('stage_setup', 'create')
            ->notEmpty('stage_setup');

        return $validator;
    }

    /**
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationApplicationPayment(Validator $validator)
    {
        $validator
            ->requirePresence('check_name', 'create')
            ->notEmpty('check_name');

        return $validator;
    }

    /**
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationApplicationFinalize(Validator $validator)
    {
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
        $rules->add($rules->isUnique(['name']));
        return $rules;
    }

    /**
     * Returns the band submitted by the specified user or null if none found
     *
     * @param int $userId
     * @return Entity|null
     * @throws NotFoundException
     */
    public function getForUser($userId)
    {
        return $this->find('all')
            ->where(['user_id' => $userId])
            ->contain([
                'Songs' => function ($q) {
                    return $q->order(['title' => 'ASC']);
                },
                'Pictures' => function ($q) {
                    return $q->order([
                        'is_primary' => 'DESC',
                        'created' => 'ASC'
                    ]);
                }
            ])
            ->first();
    }

    public function getApplicationSteps()
    {
        return [
            'basic',
            'representative',
            'websites',
            'members',
            'pictures',
            'songs',
            'stage',
            'payment',
            'finalize'
        ];
    }

    /**
     * Returns true if any band by the given name exists in the database
     *
     * @param string $name
     * @return boolean
     */
    public function nameExists($name)
    {
        $count = $this->find('all')
            ->where(['name' => $name])
            ->count();
        return $count > 0;
    }
}
