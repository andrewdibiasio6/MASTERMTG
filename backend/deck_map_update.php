<!DOCTYPE html>
<html> 
    <head>
        <meta charset="UTF-8">
<!--
File: deck_map_update.php
91.462 Project: Mastering Magic
Michael Mammosser, Computer Science Major @ UMass Lowell
Contact: michael_mammosser@student.uml.edu
Copyright (c) 2015 by Michael Mammosser.  All rights reserved.
Updated on March 24, 2015.

A simple php script to update `mtg_deck_map` database with data from a csv.
-->
        <title>MTG_DECK_MAP Update</title>
    </head>
    <body>

<?php    
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
    }

    /*
     * Fetch distinct deck names.
     */
    $sql = "SELECT DISTINCT DECK FROM `mtg_deck`;" ;
    if ( ! $result = $db->query( $sql ) ) {
        die( '<p>There was an error running card query [' . $db->error . ']</p>\n' ) ;
    }
    
    # Add distinct names to list.
    $deck_list = array() ;
    while ($deck = $result->fetch_assoc()) {
        array_push($deck_list, $deck['DECK']) ;
    }
    
    # Create a string of colors for each deck based on card colors in the deck.
    $deck_map = array() ;
    foreach ($deck_list as &$value) {
        $sql = "select DISTINCT colors from `mtg_deck` inner join `mtg_cards` on name = card where deck = '" . $value . "';";
        
        if ( ! $result = $db->query( $sql ) ) {
            die( '<p>There was an error running card query [' . $db->error . ']</p>\n' ) ;
        }
        $deck_colors = array() ;
        while ($colors = $result->fetch_assoc()) {
            if ($colors['colors'] != 'Colorless') {
                $color = explode(', ' , $colors['colors']) ;
                foreach ($color as &$c) {
                    if (!(in_array($c, $deck_colors))) {
                        array_push($deck_colors, $c) ;
                    } 
                }
            }
        }
        $deck_colors = implode(", ", $deck_colors) ;
        
        # Check format of deck.
        $sql = "select min(standard) as s from `mtg_deck` inner join `mtg_cards` on name = card where deck = '" . $value . "';";
        
        if ( ! $result = $db->query( $sql ) ) {
            die( '<p>There was an error running card query [' . $db->error . ']</p>\n' ) ;
        }
        $standard = $result->fetch_assoc() ;
        if ($standard['s'] == 1) {
            $type = 'Standard' ;
        }
        else {
            $sql = "select min(modern) as m from `mtg_deck` inner join `mtg_cards` on name = card where deck = '" . $value . "';";
            if ( ! $result = $db->query( $sql ) ) {
                die( '<p>There was an error running card query [' . $db->error . ']</p>\n' ) ;
            }
            $modern = $result->fetch_assoc() ;
            if ($modern['m'] == 1) {
                $type = 'Modern' ;
            }
            else {
                $sql = "select min(legacy) as l from `mtg_deck` inner join `mtg_cards` on name = card where deck = '" . $value . "';";
                if ( ! $result = $db->query( $sql ) ) {
                    die( '<p>There was an error running card query [' . $db->error . ']</p>\n' ) ;
                }
                $legacy = $result->fetch_assoc() ;
                if ($legacy['l'] == 1) {
                    $type = 'Legacy' ;
                }
                else {
                    $type = 'Vintage' ;
                }
            }
        }
        array_push($deck_map, array($value, $deck_colors, $type)) ;
    }
    
    # Drop mtg_deck_map.
    $sql = "DROP TABLE IF EXISTS `mtg_deck_map`;" ;
    $result = $db->query( $sql ) ;
    if (!$result) {
        die("<p>Error dropping table: " . $db->error . "</p>\n") ;
    }
    echo "<p>Dropped table MTG_DECK_MAP.</p>\n" ;
    
    # Create mtg_deck_map.
    $sql = "CREATE TABLE IF NOT EXISTS `mtg_deck_map` ( `deck` varchar(150) NOT NULL, `colors` varchar(150) NOT NULL, `format` varchar(50) NOT NULL, PRIMARY KEY (`deck`, `colors`) );" ;
    $result = $db->query( $sql ) ;
    if (!$result) {
        die("<p>Error creating MTG_DECK: " . $db->error . "</p>\n") ;
    }
    echo "<p>Created table MTG_DECK.</p>\n" ;
    
    # For each deck insert a row with its color and format.
    foreach ($deck_map as &$deck) {
        $sql = "INSERT INTO `mtg_deck_map` (`deck`, `colors`, `format`) VALUES ('" . $deck[0] . "', '" . $deck[1]. "', '" . $deck[2]. "');" ;
        $result = $db->query( $sql ) ;
    }
    
    # Check how many decks have been added.
    $sql = "SELECT count(*) as count FROM `mtg_deck_map`" ;
    if ( ! $result = $db->query( $sql ) ) {
        die( '<p>There was an error running MTG_DECK_MAP row count query [' . $db->error . ']</p>\n' ) ;
    }
    $count = $result->fetch_assoc() ;
    echo "<p>Updated table MTG_DECK_MAP with " . $count['count'] ." decks.</p>\n" ;
?>

    </body>
</html>
