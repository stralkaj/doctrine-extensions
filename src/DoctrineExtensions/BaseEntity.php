<?php
/**
 * Created by PhpStorm.
 * User: Honza
 * Date: 21.04.2017
 * Time: 13:15
 */

namespace OnlineImperium\DoctrineExtensions;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

abstract class BaseEntity extends \Kdyby\Doctrine\Entities\BaseEntity
{

    private $skip;
    /**
     * Zdroj: https://forum.nette.org/cs/12381-doctrine-2-prevedeni-entity-na-pole
     * @return array
     */
    public function toArray()
    {
        $reflection = new \ReflectionClass($this);
        $details = [];
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PROTECTED) as $property) {
            if (!$property->isStatic()) {
                $value = $this->{$property->getName()};

                if ($value instanceof BaseEntity) {
                    if (property_exists($value, 'id')) {
                        $value = $value->id;
                    } else {
                        continue;
                    }
                } elseif ($value instanceof ArrayCollection || $value instanceof PersistentCollection) {
                    $this->skip = false;
                    $value = array_map(function (BaseEntity $entity) {
                        if (!property_exists($entity, 'id')) {
                            $this->skip = true;
                            return null;
                        }
                        return $entity->id;
                    }, $value->toArray());
                    if ($this->skip) {
                        continue;
                    }
                }
                $details[$property->getName()] = $value;
            }
        }
        return $details;
    }
}