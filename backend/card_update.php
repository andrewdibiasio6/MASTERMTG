<!DOCTYPE html>
<html> 
    <head>
        <meta charset="UTF-8">
<!--
File: card_update.php
91.462 Project: Mastering Magic
Michael Mammosser, Computer Science Major @ UMass Lowell
Contact: michael_mammosser@student.uml.edu
Copyright (c) 2015 by Michael Mammosser.  All rights reserved.
Updated on March 24, 2015.

A simple php script to update `mtg_cards` database with data from a csv.
-->
        <title>MTG CARDS Update</title>     
    </head>
</html>
<?php
    echo "<p>Starting table update.</p>\n" ;

    # Database credentials.
    $username = "mmammoss" ;
    $password = "mm5119" ;

    /*
     * Error report configuration.
     * Code from https://www.teaching.cs.uml.edu/~heines/91.462/91.462-2014-15s/462-lecs/code/showphpsource.php?file=connect-v4i.php&numberlines
     */
    error_reporting( E_STRICT ) ;
    function terminate_missing_variabless( $errno, $errstr, $errfile, $errline ) {
        if (( $errno == E_NOTICE ) and ( strstr( $errstr, "Undefined variable" ) ) ) {
            die ( "$errstr in $errfile line $errline" ) ;
        }
        return false;
    }
    $old_error_handler = set_error_handler( "terminate_missing_variables" ) ;

    /*
     * Create connection with mysqli to the weblab mysql server.
     * Code from https://www.teaching.cs.uml.edu/~heines/91.462/91.462-2014-15s/462-lecs/code/showphpsource.php?file=connect-v4i.php&numberlines
     */
    $db = new mysqli("localhost", $username, $password, $username) ;
    if ( $db->connect_errno > 0 ) {
        die( '<p>Unable to connect to database [' . $db->connect_error . ']</p>\n' ) ;
    } else {
        echo "<p>Connected to MySQL, using database <b>" . $username . "</b>.</p>\n" ;
    }

    /*
     * Remove `mtg_cards` if it exists.
     */
    $sql = "DROP TABLE IF EXISTS `mtg_cards`;" ;
    $result = $db->query( $sql ) ;
    if (!$result) {
        die("<p>Error dropping table: " . $db->error . "</p>\n") ;
    }
    echo "<p>Dropped table MTG_CARDS.</p>\n" ;

    /*
     * Create `mtg_cards` if it does not exist.
     */
    $sql = "CREATE TABLE IF NOT EXISTS `mtg_cards` ( `name` varchar(150) NOT NULL, `name_simple` varchar(150) NOT NULL, `name_unicode` varchar(150) NOT NULL, `colors` varchar(50) NOT NULL, `type` varchar(50) DEFAULT NULL, `rarity` varchar(50) DEFAULT NULL, `mana_cost` varchar(50) DEFAULT NULL, `text` varchar(1000) DEFAULT NULL, `power` varchar(10) DEFAULT NULL, `toughness` varchar(10) DEFAULT NULL, `modern` int(11) DEFAULT NULL, `standard` int(11) DEFAULT NULL, `legacy` int(11) DEFAULT NULL, `vintage` int(11) DEFAULT NULL, `image` varchar(255) DEFAULT NULL, PRIMARY KEY (`colors`, `name`) );";
    $result = $db->query( $sql ) ;
    if (!$result) {
        die("<p>Error creating table: " . $db->error . "</p>\n") ;
    }
    echo "<p>Created table MTG_CARDS.</p>\n" ;

    /*
     * Open database.csv file and loop through all of the rows.
     * Remove "'" chracters from the data values.
     * Then insert the row into `mtg_cards`.
     */
    $file = fopen('/usr/cs/undergrad/2016/mmammoss/public_html/beta/backend/data/cards.csv', 'r') ;
    while (($line = fgetcsv($file)) !== FALSE) {
        for ($i = 0; $i < 15; $i++) {
            $line[$i] = str_replace("'", "\'", $line[$i]) ;
        }
        $sql = "INSERT INTO `mtg_cards` (`name`, `name_simple`, `name_unicode`, `colors`, `type`, `rarity`, `mana_cost`, `text`, `power`, `toughness`, `modern`, `standard`, `legacy`, `vintage`, `image`) VALUES ('" . $line[0] . "', '" . $line[1]. "', '" . $line[2]. "', '" . $line[3] . "', '" . $line[4] . "', '" . $line[5] . "', '" . $line[6] . "', '" . $line[7] . "', '" . $line[8] . "', '" . $line[9] . "', '" . $line[10] . "', '" . $line[11] . "', '" . $line[12] . "', '" . $line[13] . "', '" . $line[14] . "');" ;
        $result = $db->query( $sql ) ;
        if (!$result) {
            die("<p>Error inserting into table: " . $db->error . "</p>\n") ;
        }
    }
    fclose($file) ;

    /*
     * Fetch the row count from `mtg_cards`.
     */
    $sql = "SELECT count(*) as count FROM `mtg_cards`" ;
    if ( ! $result = $db->query( $sql ) ) {
        die( '<p>There was an error running the query [' . $db->error . ']</p>\n' ) ;
    }
    $count = $result->fetch_assoc() ;
    echo "<p>Updated table MTG_CARDS with " . $count['count'] ." cards.</p>\n" ;
?>