<?php
namespace App\Model\Table;

use App\Model\Entity\BandMusic;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * BandMusic Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Bands
 */
class BandMusicTable extends Table
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

        $this->table('band_music');
        $this->displayField('title');
        $this->primaryKey('id');

        $this->belongsTo('Bands', [
            'foreignKey' => 'band_id',
            'joinType' => 'INNER'
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
            ->requirePresence('title', 'create')
            ->notEmpty('title');

        $validator
            ->requirePresence('filename', 'create')
            ->notEmpty('filename');

        $validator
            ->add('seconds', 'valid', ['rule' => 'numeric'])
            ->requirePresence('seconds', 'create')
            ->notEmpty('seconds');

        $validator
            ->requirePresence('length', 'create')
            ->notEmpty('length');

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
        $rules->add($rules->existsIn(['band_id'], 'Bands'));
        return $rules;
    }
}
