<?php

namespace App\CustomBundles\CsvExportBundle;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CsvExportBundle extends Bundle
{
    public function getResponseFromQueryBuilder(QueryBuilder $queryBuilder, $columns, $filename)
    {
        $entities = new ArrayCollection($queryBuilder->getQuery()->getResult());
        $response = new StreamedResponse();

        if (is_string($columns)) {
            $columns = $this->getColumnsForEntity($columns);
        }

        $response->setCallback(function () use ($entities, $columns) {
            $handle = fopen('php://output', 'w+');

            fputcsv($handle, array_keys($columns));

            while ($entity = $entities->current()) {
                $values = [];

                foreach ($columns as $column => $callback) {
                    $value = $callback;

                    if (is_callable($callback)) {
                        $value = $callback($entity);
                    }

                    $values[] = $value;
                }

                fputcsv($handle, $values);

                $entities->next();
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    private function getColumnsForEntity($class)
    {
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

        if (array_key_exists($class, $columns)) {
            return $columns[$class];
        }

        throw new \InvalidArgumentException(sprintf(
            'No columns set for "%s" entity',
            $class
        ));
    }
}