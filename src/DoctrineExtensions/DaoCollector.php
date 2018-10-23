<?php
/**
 * Created by PhpStorm.
 * User: Honza
 * Date: 22.04.2017
 * Time: 22:06
 */

namespace OnlineImperium\DoctrineExtensions;

use Kdyby\Doctrine\EntityManager;

/**
 * Class DaoManager
 * @package App\Model
 *
 * @property EntityManager $em
 */
class DaoCollector
{
    use \Nette\SmartObject;
    //protected static $_instance;

    public function __construct(EntityManager $entityManager)
    {
        self::initEntityManager($entityManager);
        $this->em = $entityManager;
        //self::$_instance = $this;
    }

    public static function initEntityManager(EntityManager $em)
    {
        $em->getConfiguration()->addCustomNumericFunction('RAND', 'OnlineImperium\DoctrineExtensions\DqlFunctions\RandFunction');
        $em->getConfiguration()->addCustomNumericFunction('ROUND', 'OnlineImperium\DoctrineExtensions\DqlFunctions\RoundFunction');
        $em->getConfiguration()->addCustomNumericFunction('GEODISTANCE', 'OnlineImperium\DoctrineExtensions\DqlFunctions\GeoDistanceFunction');
        $em->getConfiguration()->addCustomStringFunction('MATCH', 'OnlineImperium\DoctrineExtensions\DqlFunctions\MatchAgainstFunction');
        if (class_exists('DoctrineExtensions\Query\Mysql\Regexp')) {
            $em->getConfiguration()->addCustomStringFunction('REGEXP', 'DoctrineExtensions\Query\Mysql\Regexp');
        }
    }

    /**
     * Vrati jedinou instanci DaoManager v aplikaci
     * @return DaoCollector
     */
    public static function instance()
    {
        global $container;
        return $container->getService("dao");
        //return self::$_instance;
    }

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Vrati hlavni EntityManager
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }

    public function beginTransaction()
    {
        $this->em->beginTransaction();
    }

    public function commit()
    {
        $this->em->commit();
    }

    public function rollback()
    {
        $this->em->rollback();
    }

    /**
     * TODO:
     * - Vyresit, aby se flush() nevolalo po kazdem save
     * - persist() se nemusi volat, kdyz entita uz v db existuje - pri update
     */
    public function save($entity, $flush = true)
    {
        if (!$this->em->contains($entity)) {
            $this->em->persist($entity);
        }
        if ($flush) {
            $this->em->flush();
        }
    }

    public function delete($entity, $flush = true)
    {
        $this->em->remove($entity);
        $flush && $this->em->flush();
    }
}