<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Band Entity.
 *
 * @property int $id
 * @property string $name
 * @property string $genre
 * @property string $description
 * @property string $hometown
 * @property string $minimum_fee
 * @property string $website
 * @property string $social_networking
 * @property string $email
 * @property string $phone
 * @property string $address
 * @property string $member_count
 * @property string $member_names
 * @property bool $members_under_21
 * @property string $tier
 * @property int $approved
 * @property string $message
 * @property string $stage_setup
 * @property \Cake\I18n\Time $created
 * @property \App\Model\Entity\Picture[] $pictures
 * @property \App\Model\Entity\Slot[] $slots
 * @property \App\Model\Entity\Song[] $songs
 * @property \App\Model\Entity\Vote[] $votes
 */
class Band extends Entity
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
        'user_id' => false
    ];

    protected function _getName($name)
    {
        return trim($name);
    }

    protected function _getGenre($genre)
    {
        $genre = strtolower($genre);
        $genre = trim($genre);
        return $genre;
    }

    protected function _getDescription($description)
    {
        return trim($description);
    }

    protected function _getHometown($hometown)
    {
        return trim($hometown);
    }

    protected function _getWebsite($url)
    {
        return trim($url);
    }

    protected function _getEmail($email)
    {
        $email = strtolower($email);
        return trim($email);
    }
}
