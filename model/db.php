<?php

$dbh = null;

function connect_to_db()
{
    global $db_config;
    $dsn = 'mysql:dbname='.$db_config['name'].';host='.$db_config['host'];
    $user = $db_config['user'];
    $password = $db_config['pass'];
    
    try {
        $dbh = new PDO($dsn, $user, $password);
    } catch (PDOException $e) {
        echo 'Connexion échouée : ' . $e->getMessage();
    }
    
    return $dbh;
}

function get_dbh()
{
    global $dbh;
    if ($dbh === null)
        $dbh = connect_to_db();
    return $dbh;
}

function find_query_result($query, $data = [])
{
    $dbh = get_dbh();
    $sth = $dbh->prepare($query);
    $sth->execute($data);
    $result = $sth->fetchAll();
    return $result;
}

function put_data_into_database($query, $data = [])
{
    $dbh = get_dbh();
    $sth = $dbh->prepare($query);
    $sth->execute($data);
}

function do_query_db($query, $data = []){
    $dbh = get_dbh();
    $sth = $dbh->prepare($query);
    $sth->execute($data);
}