<?php
namespace App\Model\Table;

use App\Model\Entity\Volunteer;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Security;
use Cake\Validation\Validator;

/**
 * Volunteers Model
 *
 * @property \Cake\ORM\Association\BelongsToMany $Jobs
 */
class VolunteersTable extends Table
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

        $this->table('volunteers');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('Jobs', [
            'foreignKey' => 'volunteer_id',
            'targetForeignKey' => 'job_id',
            'joinTable' => 'jobs_volunteers'
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
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmpty('email');

        $validator
            ->requirePresence('phone', 'create')
            ->notEmpty('phone');

        $validator
            ->requirePresence('availability', 'create')
            ->notEmpty('availability');

        $validator
            ->requirePresence('shirt_size', 'create')
            ->notEmpty('shirt_size');

        $validator
            ->requirePresence('emergency_contact', 'create')
            ->allowEmpty('emergency_contact');

        $validator
            ->requirePresence('message', 'create')
            ->allowEmpty('message');

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
        $msg = 'That email address has already signed up to volunteer.';
        $msg .= ' If you need to update your information, please use the link that we emailed to you';
        $msg .= ' or contact a site administrator for help.';
        $rules->add($rules->isUnique(['email'], $msg));
        return $rules;
    }

    /**
     * Returns a hash used in verifying attempts to update volunteer info
     *
     * @param string $email Email
     * @return string
     */
    public function getSecurityHash($email)
    {
        return Security::hash($email, 'sha1', true);
    }
}
