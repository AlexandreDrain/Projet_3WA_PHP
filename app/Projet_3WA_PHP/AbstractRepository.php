<?php

namespace Projet_3WA_PHP;

use Projet_3WA_PHP\Database\MySql;
use Projet_3WA_PHP\EntityInterface;
use Projet_3WA_PHP\Database\MongoDB;

abstract class AbstractRepository
{
    protected $db;

    public function __construct($typeDB)
    {
        switch($typeDB) {
            case 'sql' :
            case 'mysql' :
                $connect = MySql::getInstance();
                break;
            case 'mongo' :
            case 'mongodb' :
                $connect = MongoDB::getInstance();
                break;
        }
        if($connect)
            $this->db = $connect->getDatabase();
        else
            throw new Exception('No database Connect');
    }
 
    protected function getEntity(EntityInterface $entity) 
    {
        if(is_object($entity))
        {   
            $result = [];
            // Boucle sur les attribut de l'entiity
            foreach ((array) $entity as $key => $value)
            {
                // exemple de $key : "App\Entity\Customer firstName"
                // attributName recevra uniquement "firstName" par rapport à notre exemple
                $attributName = trim(substr($key, strrpos($key, chr(0))));
                // à partir de attributName on va créer le nom du getter (exemple : getFirstName)
                $getter = 'get'.ucfirst($attributName); 
                // si la méthode existe et que la valeur n'est pas null
                if(method_exists($entity, $getter) && is_null($value) === false) {
                    // on l'ajoute à notre tableau associatif
                    $result[$attributName] = call_user_func([$entity,$getter]);
                }
            }
            // retourne le tableau associatif créé à partir de l'entity
            return $result;
        }
        return; // retourne null
    }
}