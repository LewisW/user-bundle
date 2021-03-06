<?php

namespace Vivait\UserBundle\Model\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    public function findByUniqueAttributes($identifer)
    {
        if (null != ($customer = $this->findOneBy(['id' => $identifer]))) {
            return $customer;
        } elseif (null != ($customer = $this->findOneBy(['username' => $identifer]))) {
            return $customer;
        } elseif (null != ($customer = $this->findOneBy(['email' => $identifer]))) {
            return $customer;
        } else {
            return null;
        }
    }

    public function getAllUsers()
    {
        return $this->_em->createQueryBuilder()
                         ->select('u')
                         ->from('Vivait\UserBundle\Model\User', 'u', 'u.username')
                         ->getQuery()
                         ->getResult();
    }

    /**
     * Gets a list of users with the fields specified, indexed by their ID
     * The order first field is used for ordering
     *
     * @param array $fields
     * @return array
     */
    public function getUserList(array $fields = ['username'])
    {
        $mapped_fields = array_map(
            function ($field) {
                return 'u.' . $field;
            },
            $fields
        );

        return $this->_em->createQueryBuilder()
                         ->select('u.id')
                         ->addSelect($mapped_fields)
            // Index by the ID
                         ->from($this->_entityName, 'u', 'u.id')
                         ->orderBy($mapped_fields ? reset($mapped_fields) : 'u.username')
                         ->where('u.enabled = 1')
                         ->getQuery()
                         ->getResult('ListHydrator');
    }
}
