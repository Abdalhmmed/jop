<?php
    session_start();
    function getConnection(){
        $servername = "localhost";
        $db = 'jop';
        $username = "root";
        $password = "";
        $connString = "mysql:host=$servername;dbname=$db;";
        try
        {
            $conn = new PDO($connString,$username,$password);
            $conn ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Connected successfully" . "<br>";
        } catch (PDOException $e)
        {
            echo "erorr :".$e->getMessage();
        }
        return $conn;
    }
?>