<?php

$routes = [
'home'     => 'default',
'about'    => 'default',
'contact'  => 'default',
'register' => 'user',
'login'    => 'user',
'logout'   => 'user',
'userconnected'   => 'user',
'upload'   => 'user',
'delete' => 'file',
'rename' => 'file',
'replace' => 'file',
'download' => 'file',
'moveFile' => 'file',
'openEditFile' => 'file',
'editFile' => 'file',
'visualizeFile' => 'file',
'createNewFolder' => 'directory',
'createDirectory' => 'directory',
'deleteDirectory' => 'directory',
'renameDirectory'=> 'directory',
'moveDirectory'=> 'directory',
];

$db_config = [
'name' => 'filer',
'host' => 'localhost',
'user' => 'root',
'pass' => '',
];