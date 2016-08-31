<?php
namespace App\Model\Table;

use App\Model\Entity\Band;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
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
        $this->addBehavior('Xety/Cake3Sluggable.Sluggable', [
            'field' => 'name'
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

    public function afterSave(\Cake\Event\Event $event, \Cake\Datasource\EntityInterface $entity, \ArrayObject $options)
    {
        // Trigger resetting all song and pic filenames if name changes
        $oldData = $entity->extractOriginalChanged(['name']);
        if (isset($oldData['name'])) {
            $songsTable = TableRegistry::get('Songs');
            $songs = $songsTable->getForBand($entity->id);
            if (! empty($songs)) {
                foreach ($songs as $song) {
                    $songsTable->resetFilename($song->id);
                }
            }

            $picturesTable = TableRegistry::get('Pictures');
            $pictures = $picturesTable->getForBand($entity->id);
            if (! empty($pictures)) {
                foreach ($pictures as $picture) {
                    $picturesTable->resetFilename($picture->id);
                }
            }
        }
    }

    /**
     * Finds bands with completed applications
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findApplied(Query $query, array $options)
    {
        return $query->where(['Bands.application_step' => 'done']);
    }

    /**
     * Finds bands with incomplete applications
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findApplicationIncomplete(Query $query, array $options)
    {
        return $query->where(['Bands.application_step <>' => 'done']);
    }

    /**
     * Sets slugs for all bands without slugs
     *
     * @return bool
     */
    public function generateSlugs()
    {
        $bands = $this->find('all')
            ->select(['id', 'name'])
            ->where(['slug' => '']);
        foreach ($bands as $band) {
            $this->slug($band);
            if (! $this->save($band)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns a sorted array of bands with leading 'The ' not affecting the order
     *
     * @param array $bands
     * @return array
     */
    public function sortIgnoringThe($bands)
    {
        $sortedBands = [];
        foreach ($bands as $band) {
            $bandName = is_array($band) ? $band['name'] :$band->name;
            if (stripos($bandName, 'the ') === 0) {
                $sortingName = substr($bandName, 4) . ', ' . substr($bandName, 0, 3);
            } else {
                $sortingName = $bandName;
            }

            // Prevent bands with identical names from overwriting each other
            while (isset($sortedBands[$sortingName])) {
                $sortingName .= ' (duplicate)';
            }

            $sortedBands[$sortingName] = $band;
        }

        ksort($sortedBands);

        return $sortedBands;
    }
}
