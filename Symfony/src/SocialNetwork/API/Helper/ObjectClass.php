<?php

namespace SocialNetwork\API\Helper;

use Symfony\Component\Yaml\Yaml;

/**
 * Class ObjectClass
 * @package SocialNetwork\API\Helper
 */
class ObjectClass
{
    /**
     * Add default objectClass required from attributes in entity
     *
     * @param $entity
     * @param $type
     * @return mixed
     */
    static function get( $entity, $type )
    {
        $serviceMap = Yaml::parse( file_get_contents(__DIR__ . '/../Resources/config/services.yml') );
        $map = $serviceMap['parameters']['ldap']['objectclass'][ $type ];

        $class = array();

        foreach( $entity as $key => $value )
        {
            foreach( $map as $object => $fields )
            {
                if( in_array($key, $fields) )
                {
                    $class[ $object ] = true;
                    break;
                }
            }
        }

        $required = $serviceMap['parameters']['ldap']['objectclassrequired'][ $type ];
        $entity['objectclass'] = array_keys( $class );
        return array_merge($entity['objectclass'], $required);
    }
}