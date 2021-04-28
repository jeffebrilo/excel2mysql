<?php

require 'vendor/autoload.php';

$dsn = 'mysql:dbname=base_test;host=127.0.0.1;charset=utf8';
$user = 'base_test';
$password = file_get_contents('pwd.txt');
$pdo = new PDO($dsn, $user, $password);

$validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale('ru');
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$spreadSheet = $reader->load("test.xlsx");
$workSheet = $spreadSheet->getActiveSheet();

$stmt = $pdo->query("TRUNCATE TABLE excel"); //очистка таблицы

foreach ($workSheet->getRowIterator() as $r => $row) {
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(TRUE); //читаем только заполненные ячейки
    foreach ($cellIterator as $c => $cell) 
        $pdo->prepare("INSERT INTO excel (`row`, `col`, `val`) VALUES (?,?,?)")
            ->execute([$r, $c, $cell->getValue()]);
}

$stmt = $pdo->query("SELECT * FROM excel");//читаем уже из таблицы MYSQL
while ($row = $stmt->fetch()) echo var_export($row) . "<br>\n";//и выводим
