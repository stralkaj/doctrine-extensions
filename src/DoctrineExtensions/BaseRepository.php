<?php
/**
 * Created by PhpStorm.
 * User: Honza
 * Date: 23.04.2017
 * Time: 9:47
 */

namespace OnlineImperium\DoctrineExtensions;

use Doctrine\ORM\NoResultException;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Kdyby\Doctrine\NotImplementedException;
use Nette\Utils\Strings;
use OnlineImperium\Globals;

/**
 * Class BaseRepository
 *
 * @property EntityManager $em
 * @property DaoCollector $dao
 */
abstract class BaseRepository extends EntityRepository
{
    use \Nette\SmartObject;

    /**
     * @param $id
     * @return BaseEntity|null
     */
    public function byId($id)
    {
        return $this->find($id);
    }

    /**
     * Find by idtxt
     * @param string $idtxt Text identifier of entity
     * @return null|BaseEntity
     */
    public function byIdtxt($idtxt)
    {
        return $this->findOneBy(['idtxt' => $idtxt]);
    }

    /**
     * Retrieves EntityManager
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->getEntityManager();
    }

    /**
     * Retrieves DAO Manager
     * @return \App\Model\DaoManager
     */
    public function getDao()
    {
        return Globals::dao();
    }


    /**
     * Default order of entity (array)
     * @return array
     */
    public function defaultOrder()
    {
        if (property_exists($this->getEntityName(), "priority")) {
            return ['priority' => 'DESC'];
        }
        return [];
    }

    /**
     * Default order of entity (string)
     * @return string
     */
    public function defaultOrderStr()
    {
        if (property_exists($this->getEntityName(), "priority")) {
            return $this->alias() . '.priority DESC';
        }
        return null;
    }

    /**
     * @return string
     */
    public abstract function alias();

    /**
     * @param $select
     * @return \Kdyby\Doctrine\DqlSelection
     */
    public function selection($select = null)
    {
        $selection = $this->em->createSelection()->from($this->getEntityName(), $this->alias());
        if ($select === null) {
            $selection->select($this->alias());
        } elseif ($select !== false) {
            $selection->select($select);
        }
        // $selection->order($this->defaultOrderStr()); // O toto se nepokouset, pak to nejde zrusit pozdeji v dotazu
        return $selection;
    }

    public function all($onlyActive = true, $limit = null, $offset = null)
    {
        $selection = $this->selection();
        if ($onlyActive && property_exists($this->getEntityName(), "active")) {
            $selection->where($this->alias() . '.active = 1');
        }
        $orderBy = $this->defaultOrderStr();
        if ($orderBy) {
            $selection->order($orderBy);
        }
        $query = $selection->createQuery();
        if ($limit !== null) {
            $query->setMaxResults($limit);
        }
        if ($offset !== null) {
            $query->setFirstResult($offset);
        }
        return $query->getResult();
    }

    /**
     * Vrati zaznamy v poli ve formatu $key => $value
     *   Je mozne nastavit kriteria, radi automaticky podle priority, pokud entita ma
     * @param string $key
     * @param string $value
     * @param array $criteria
     * @return array
     */
    public function getKeysValues($key = "id", $value = "name", $criteria = [], $sort = null)
    {
        $sort = $sort ?: $this->defaultOrder();
        return $this->findPairs($criteria, $value, $sort, $key);
    }

    public function totalCount($onlyActive = true)
    {
        $where = [];
        if ($onlyActive && property_exists($this->getEntityName(), "active")) {
            $where['active'] = true;
        }
        return $this->countBy($where);
    }

    /**
     * Generates unique text identifier for entity [a-zA-Z0-9\-]+
     * @param $text
     * @param string $columnName
     * @return string
     */
    public function generateUniqueIdTxt($text, $columnName = "idtxt")
    {
        for ($i = 0; ; $i++)
        {
            $idtxt = Strings::webalize($text) . (($i > 0) ? "-" . $i : "");
            $count = $this->countBy([$columnName => $idtxt]);
            if (!$count)
            {
                return $idtxt;
            }
        }
    }

    public function deleteById($id)
    {
        $this->dao->delete($this->getReference($id));
    }

    /**
     * Changes order of items by previous position and new position. Priority is recalculated.
     * @param BaseEntity $item
     * @param $newPosition
     * @param $prevPosition
     * @param null $where
     */
    public function movePriority(BaseEntity $item, $newPosition, $prevPosition, $where = null)
    {
        $entityName = $this->getEntityName();
        $em = $this->em;
        $qWhere = '';
        if ($where) {
            $qWhere = ' WHERE ' . $where . ' ';
        }
        $prev = null;
        if ($newPosition > 0) {
            $offset = $newPosition + (($newPosition > $prevPosition) ? 0 : -1);
            $prev = $em->createQuery("SELECT i FROM " . $entityName . " i". $qWhere
                . " ORDER BY i.priority DESC"
            )->setFirstResult($offset)->setMaxResults(1)
                ->getOneOrNullResult();
        }
        if ($prev) {
            $newIdx = $prev->priority;
        } else {
            $maxPriority = $em->createQuery("SELECT i.priority FROM " . $entityName . " i" . $qWhere
                . " ORDER BY i.priority DESC"
            )->setMaxResults(1)->getSingleScalarResult();
            //var_dump($result);
            $newIdx = $maxPriority + 1;
        }

        $em->createQuery("UPDATE " . $entityName . " i SET i.priority = i.priority + 1 WHERE"
            . " i.priority >= :priority"
            . (($where) ? ' AND ' . $where : '')
        )->setParameters(['priority' => $newIdx])->execute();

        $item->priority = $newIdx;
        $em->flush($item);
    }

    /**
     * Retrieves minimum existing priority for entity
     * @param null $where
     * @param bool $correction
     * @return int|mixed|null
     */
    public function getMinPriority($where = null, $correction = true)
    {
        $entityName = $this->getEntityName();
        $query = $this->em->createSelection()->from($entityName, 'i')->select('i.priority')
            ->order('i.priority ASC');
        if ($where) {
            $query->where($where);
        }
        try {
            return $query->createQuery()->setMaxResults(1)->getSingleScalarResult() - (($correction) ? 1 : 0);
        } catch (NoResultException $e) {
            return (($correction) ? 50 : null);
        }
    }
}