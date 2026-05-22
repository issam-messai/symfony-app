<?php

namespace App\Service;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;


class ReportService
{
    public function generate(int $userId): array
    {   
        $dsnParser = new DsnParser(['mysql' => 'pdo_mysql']);
        $params = $dsnParser->parse($_ENV['DATABASE_URL']);
        $conn = DriverManager::getConnection($params);

        $result = $conn->executeQuery(
            'SELECT * FROM `order` WHERE customer_id = ' . $userId
        )->fetchAllAssociative();

        return $result;
    }
}
