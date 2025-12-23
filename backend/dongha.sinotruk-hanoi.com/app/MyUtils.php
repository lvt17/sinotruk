<?php
/**
 * Created by PhpStorm.
 * User: Home
 * Date: 5/3/2020
 * Time: 3:07 PM
 */

namespace App;


class MyUtils
{
    public static function insertOrUpdate(array $rows, $table){
        $first = reset($rows);
        $columns = implode( ',',
            array_map( function( $value ) { return "$value"; } , array_keys($first) )
        );
        $values = implode( ',', array_map( function( $row ) {
                return '('.implode( ',',
                        array_map( function( $value ) { return '"'.str_replace('"', '""', $value).'"'; } , $row )
                    ).')';
            } , $rows )
        );
        $updates = implode( ',',
            array_map( function( $value ) { return "$value = VALUES($value)"; } , array_keys($first) )
        );
        $sql = "INSERT INTO {$table}({$columns}) VALUES {$values} ON DUPLICATE KEY UPDATE {$updates}";
        return \DB::statement( $sql );
    }
}