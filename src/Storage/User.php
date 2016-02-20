<?php
namespace AdminModule\Storage;

use AdminModule\Storage\Base as BaseStorage;
use AdminModule\Entity\User as UserEntity;

class User extends BaseStorage
{
    protected $meta_data = array(
        'conn'      => 'main',
        'table'     => 'user',
        'primary'   => 'id',
        'fetchMode' => \PDO::FETCH_ASSOC
    );

    /**
     * Get a blank user enitity
     *
     * @return mixed
     */
    public function getBlankEntity()
    {
        return new UserEntity();
    }

    /**
     * Get a user entity by its ID
     *
     * @param $user_id
     * @return mixed
     * @throws \Exception
     */
    public function getByID($user_id)
    {
        $row = $this->ds->createQueryBuilder()
            ->select('u.*, ul.title AS ul_title, ul.id AS ul_id')
            ->from($this->meta_data['table'], 'u')
            ->leftJoin('u', 'user_level', 'ul', 'u.user_level_id = ul.id')
            ->andWhere('u.id = :user_id')->setParameter(':user_id', $user_id)
            ->execute()
            ->fetch($this->meta_data['fetchMode']);

        if ($row === false) {
            throw new \Exception('Unable to obtain user row for id: ' . $user_id);
        }

        return new UserEntity($row);
    }

    /**
     * Find a user record by the email
     *
     * @param  string $email
     * @return mixed
     */
    public function findByEmail($email)
    {
        return $this->createQueryBuilder()
            ->select('u.*')
            ->from($this->meta_data['table'], 'u')
            ->andWhere('u.email = :email')->setParameter(':email', $email)
            ->execute()
            ->fetch($this->meta_data['fetchMode']);
    }

    public function getAll()
    {
        $entities = array();

        $rows = $this->ds->createQueryBuilder()
            ->select('u.*, ul.title AS level_title')
            ->from($this->meta_data['table'], 'u')
            ->leftJoin('u', 'user_level', 'ul', 'u.user_level_id = ul.id')
            ->execute()
            ->fetchAll($this->meta_data['fetchMode']);

        foreach ($rows as $row) {
            $entities[] = new UserEntity($row);
        }

        return $entities;
    }

    /**
     * Get a user entity by the email address
     *
     * @param  string $email
     * @return UserEntity
     * @throws \Exception
     */
    public function getByEmail($email)
    {
        $row = $this->findByEmail($email);

        if ($row === false) {
            throw new \Exception('Unable to find user record by email: ' . $email);
        }

        return new UserEntity($row);
    }

    /**
     * Get a user entity by username
     *
     * @param  string $username
     * @return UserEntity
     * @throws \Exception
     */
    public function getByUsername($username)
    {
        $row = $this->createQueryBuilder()
            ->select('u.*')
            ->from($this->meta_data['table'], 'u')
            ->andWhere('u.username = :username')
            ->setParameter(':username', $username)
            ->execute()
            ->fetch($this->meta_data['fetchMode']);

        if ($row === false) {
            throw new \Exception('Unable to find user record by username: ' . $username);
        }

        return new UserEntity($row);
    }

    /**
     * Check if a user record exists by email address
     *
     * @param $email
     * @return bool
     */
    public function existsByEmail($email)
    {
        $row = $this->createQueryBuilder()
            ->select('count(id) as total')
            ->from($this->meta_data['table'], 'u')
            ->andWhere('u.email = :email')
            ->setParameter(':email', $email)
            ->execute()
            ->fetch($this->meta_data['fetchMode']);

        return $row['total'] > 0;
    }

    /**
     * Check if a user record exists by username
     *
     * @param $email
     * @return bool
     */
    public function existsByUsername($username)
    {
        $row = $this->createQueryBuilder()
            ->select('count(id) as total')
            ->from($this->meta_data['table'], 'u')
            ->andWhere('u.username = :username')
            ->setParameter(':username', $username)
            ->execute()
            ->fetch($this->meta_data['fetchMode']);

        return $row['total'] > 0;
    }

    /**
     * Check if a user record exists by User ID
     *
     * @param integer $id
     * @return bool
     */
    public function existsByID($id)
    {
        $row = $this->createQueryBuilder()
            ->select('count(id) as total')
            ->from($this->meta_data['table'], 'u')
            ->andWhere('u.id = :id')
            ->setParameter(':id', $id)
            ->execute()
            ->fetch($this->meta_data['fetchMode']);

        return $row['total'] > 0;
    }

    /**
     * Delete a user by their email address
     *
     * @param  string $email
     * @return mixed
     */
    public function deleteByEmail($email)
    {
        return $this->delete(array('email' => $email));
    }

    /**
     * Delete a user by their ID
     *
     * @param  integer $userID
     * @return mixed
     */
    public function deleteByID($userID)
    {
        return $this->delete(array($this->getPrimaryKey() => $userID));
    }

    /**
     * Create a user record
     *
     * @param  UserEntity $user
     * @return integer
     */
    public function create(UserEntity $user)
    {
        return $this->ds->insert(self::TABLE_NAME, $user->toInsertArray());
    }

    public function rowsToEntities($rows)
    {
        $ent = array();
        foreach ($rows as $r) {
            $ent[] = new UserEntity($r);
        }
        return $ent;
    }
}
