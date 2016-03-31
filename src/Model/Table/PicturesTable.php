<?php
namespace App\Model\Table;

use App\Media\Media;
use App\Model\Entity\Picture;
use Cake\Network\Exception\ForbiddenException;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Pictures Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Bands
 */
class PicturesTable extends Table
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

        $this->table('pictures');
        $this->displayField('id');
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
            ->requirePresence('filename', 'create')
            ->notEmpty('filename');

        $validator
            ->add('is_primary', 'valid', ['rule' => 'numeric']);

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

    /**
     * Makes the selected picture the primary one for the given band
     *
     * @param int $pictureId
     * @param int $bandId
     * @return boolean
     * @throws ForbiddenException
     */
    public function makePrimary($pictureId, $bandId)
    {
        $picture = $this->get($pictureId);
        if ($picture->band_id != $bandId) {
            throw new ForbiddenException('Cannot make picture primary, picture #'.$pictureId.' and band #'.$bandId.' do not match');
        }

        // Unset any previously primary pictures
        $previouslyPrimary = $this->find('all')
            ->select(['id'])
            ->where([
                'is_primary' => 1,
                'band_id' => $bandId
            ])
            ->all();
        foreach ($previouslyPrimary as $pic) {
            $entity = $this->get($pic->id);
            $entity->is_primary = 0;
            if (! $this->save($entity)) {
                return false;
            }
        }

        $picture->is_primary = 1;
        return (boolean) $this->save($picture);
    }

    /**
     * Returns ordered images for a band
     *
     * @param int $bandId
     * @return \Cake\ORM\ResultSet
     */
    public function getForBand($bandId)
    {
        return $this->find('all')
            ->where(['band_id' => $bandId])
            ->order([
                'is_primary' => 'DESC',
                'created' => 'ASC'
            ])
            ->all();
    }

    /**
     * Deletes the DB record, full-size image, and thumbnail for the image
     *
     * @param int $pictureId
     * @param int $bandId
     * @return boolean
     * @throws ForbiddenException
     */
    public function deletePicture($pictureId, $bandId)
    {
        $picture = $this->get($pictureId);
        if ($picture->band_id != $bandId) {
            throw new ForbiddenException('Cannot delete picture, picture #'.$pictureId.' and band #'.$bandId.' do not match');
        }

        if ($this->delete($picture)) {
            $Media = new Media();
            return $Media->deletePicture($picture->filename);
        }

        return false;
    }

    /**
     * Updates Picture.filename in the database and renames the file.
     * This is meant to be run after the band name changes.
     *
     * @param int $pictureId
     * @return boolean
     */
    public function resetFilename($pictureId)
    {
        $picture = $this->get($pictureId);
        $bandsTable = TableRegistry::get('Bands');
        $filenameParts = explode('.', $picture->filename);
        $extension = array_pop($filenameParts);
        $Media = new Media();
        $oldFilename = $picture->filename;
        $newFilename = $Media->generatePictureFilename($picture->band_id, $extension);
        if ($newFilename == $picture->filename) {
            return true;
        }

        $picture = $this->patchEntity($picture, [
            'filename' => $newFilename
        ]);
        if ($picture->errors()) {
            return false;
        }

        $Media = new Media();
        if ($Media->changePictureFilename($oldFilename, $newFilename)) {
            return (boolean) $this->save($picture);
        }
        return false;
    }
}
