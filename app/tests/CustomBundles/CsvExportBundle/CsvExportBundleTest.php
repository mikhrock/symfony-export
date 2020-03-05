<?php

namespace App\CustomBundles\CsvExportBundle;

use App\Entity\User;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class CsvExportBundleTest extends TestCase
{
    public function testGetFileFromQueryBuilder()
    {
        $csvExportBundle = new CsvExportBundle();

        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $getQuery = $this->getMockBuilder(AbstractQuery::class)
            ->setMethods(array('getResult'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($getQuery));

        $entity = new User();
        $result = [
            0 => $entity,
        ];

        $getQuery->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue($result));

        $userClass = User::class;

        $columns[User::class] = [
            'User ID' => function (User $user) {
                return $user->getId();
            },
            'First Name' => function (User $user) {
                return $user->getFirstName();
            },
            'Last Name' => function (User $user) {
                return $user->getLastName();
            },
            'Email' => function (User $user) {
                return $user->getEmail();
            },
        ];

        if (array_key_exists($userClass, $columns)) {
            $userClass =  $columns[$userClass];
        }

        $file = $csvExportBundle->getFileFromQueryBuilder($queryBuilder, $userClass);

        $this->assertFileExists($file);
    }
}