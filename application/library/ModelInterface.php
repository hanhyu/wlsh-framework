<?php


namespace App\Library;


interface ModelInterface
{
    public static function getInstance(): static;

    public static function getPool(): string;

    public static function getDb(): object;
}
