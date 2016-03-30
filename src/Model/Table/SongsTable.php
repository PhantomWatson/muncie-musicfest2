<?php
namespace App\Model\Table;

use App\Media\Media;
use App\Model\Entity\Song;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Songs Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Bands
 */
class SongsTable extends Table
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

        $this->table('songs');
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
            ->add('seconds', 'valid', ['rule' => 'numeric']);

        $validator->add(
            'title',
            ['unique' => [
                'rule' => 'isUniqueTitle',
                'provider' => 'table',
                'message' => 'Hold up! You can\'t have more than one song with the same title.']
            ]
        );

        return $validator;
    }

    /**
     * Ensures that a particular band can't have more than one song with the same title
     *
     * @param string $value
     * @param array $context
     * @return boolean
     */
    public function isUniqueTitle($value, array $context)
    {
        $bandId = $context['data']['band_id'];
        $query = $this->find('all')
            ->where([
                'title' => $value,
                'band_id' => $bandId
            ]);
        if (isset($context['data']['id'])) {
            $songId = $context['data']['id'];
            $query->where(['id IS NOT' => $songId]);
        }
        $count = $query->count();
        return $count == 0;
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
        $rules->add($rules->isUnique(['filename']));
        return $rules;
    }

    /**
     * Returns ordered songs for a band
     *
     * @param int $bandId
     * @return \Cake\ORM\ResultSet
     */
    public function getForBand($bandId)
    {
        return $this->find('all')
            ->where(['band_id' => $bandId])
            ->order(['title' => 'ASC'])
            ->all();
    }

    /**
     * Deletes the DB record and file for a song
     *
     * @param int $songId
     * @param int $bandId
     * @return boolean
     * @throws ForbiddenException
     */
    public function deleteSong($songId, $bandId)
    {
        $song = $this->get($songId);
        if ($song->band_id != $bandId) {
            throw new ForbiddenException('Cannot delete picture, picture #'.$songId.' and band #'.$bandId.' do not match');
        }

        if ($this->delete($song)) {
            $Media = new Media();
            return $Media->deleteSong($song->filename);
        }

        return false;
    }
}
