<!DOCTYPE html>
<html> 
    <head>
        <meta charset="UTF-8">
<!--
File: winning_update.php
91.462 Project: Mastering Magic
Michael Mammosser, Computer Science Major @ UMass Lowell
Contact: michael_mammosser@student.uml.edu
Copyright (c) 2015 by Michael Mammosser.  All rights reserved.
Updated on March 24, 2015.

A simple php script to update `mtg_decks` database with data from a csv.
-->
        <title>Winning Update</title>
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
     * Remove `mtg_deck` if it exists.
     */
   /*  
    $sql = "DROP TABLE IF EXISTS `mtg_deck`;" ;
    $result = $db->query( $sql ) ;
    if (!$result) {
        die("<p>Error dropping table: " . $db->error . "</p>\n") ;
    }
    echo "<p>Dropped table MTG_DECK.</p>\n" ; */
    
    /*
     * Create `mtg_deck` if it does not exist.
     */
    $sql = "CREATE TABLE IF NOT EXISTS `mtg_deck` ( `deck` varchar(150) NOT NULL, `card` varchar(150) NOT NULL, `count` int(2) DEFAULT 0, PRIMARY KEY (`deck`, `card`) );" ;
    $result = $db->query( $sql ) ;
    if (!$result) {
        die("<p>Error creating MTG_DECK: " . $db->error . "</p>\n") ;
    }
    echo "<p>Created table MTG_DECK.</p>\n" ;

    /*
     * Open winning.csv file and loop through all of the rows.
     * Remove "'" characters from the data values.
     * Then insert the row into `mtg_deck`.
     */

    $file = fopen('/usr/cs/undergrad/2016/mmammoss/public_html/beta/backend/data/winning.csv', 'r') ;
    while (($line = fgetcsv($file)) !== FALSE) {
        for ($i = 0; $i < 3; $i++) {
            $line[$i] = str_replace("'", "\'", $line[$i]) ;
        }
    $sql = "INSERT INTO `mtg_deck` (`deck`, `card`, `count`) VALUES ('" . $line[0] . "', '" . $line[1]. "', '" . $line[2]. "') ON DUPLICATE KEY UPDATE count = '" . $line[2]. "';" ;
    $result = $db->query( $sql ) ;
    }
    fclose($file) ;
   
    /*
     * Fetch the row count from `mtg_deck`.
     */
    $sql = "SELECT count(*) as count FROM `mtg_deck`" ;
    if ( ! $result = $db->query( $sql ) ) {
        die( '<p>There was an error running MTG_DECK row count query [' . $db->error . ']</p>\n' ) ;
    }
    $count = $result->fetch_assoc() ;
    echo "<p>Updated table MTG_DECK with " . $count['count'] ." cards.</p>\n" ;
?>