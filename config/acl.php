<?php

use boctulus\grained_acl\Acl;
use simplerest\core\libs\Files;


// debería leerse de archivo
$acl_cache = false;
$acl_file  = config()['acl_file'];

// Check whether ACL data already exist
if (!$acl_cache || is_file($acl_file) !== true) {

    /*
        Roles are backed in database but role permissions not.
        Role permissions can be decorated and these decorators are backed.
    */

    $acl = new Acl();

    $acl
    ->addRole('guest', -1)   

    // ...
    //->setAsGuest('guest')

    ->addRole('registered', 1)
    ->setAsRegistered('registered')
    ->addInherit('guest') 
    ->addResourcePermissions('ricerca', ['read', 'write'])
    //->addSpecialPermissions(['read_all', 'write_all'])
    

    ->addRole('admin', 50) 
    ->addInherit('registered')
    ->addSpecialPermissions(['read_all', 'write_all'])
    
    ->addRole('superadmin', 10000)
    ->addInherit('admin')
    ->addSpecialPermissions([
        'read_all', 
        'write_all', 
        'read_all_folders', 
        'lock', 
        'fill_all', 
        'impersonate',        
        'read_all_trashcan',
        'write_all_trashcan',
        'write_all_folders', 

         // necesario para borrar en masa en tablas sin belongs_to
        'write_all_collections', 
        
        'transfer',
        'grant'
    ]);     

    if (!is_dir(SECURITY_PATH)){
        Files::mkDirOrFail(SECURITY_PATH);
    }

    // Store serialized list into plain file
    $bytes = file_put_contents(
        $acl_file,
        serialize($acl)
    );

    if ($bytes === 0){
        throw new \Exception("Internal Error. ACL File could not be written");
    }
} else {
    // Restore ACL object from serialized file

    $acl = unserialize(
        file_get_contents($acl_file)
    );
}


//var_export($acl->getRolePermissions());

return $acl;
