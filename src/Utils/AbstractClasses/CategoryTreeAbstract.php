<?php

namespace App\Utils\Abstractclass;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class CategoryTreeAbstract
{
    public $categoriesArrayFromDB;
    protected static $dbconnection;

    public function __construct(EntityManagerInterface $entitymanager, UrlGeneratorInterface $urlgenerator)
    {
        // $this->entitymanager = $entitymanager;
        // $this->urlgenerator = $urlgenerator;
        $this->categoriesArrayFromDB = $this->getCategories($entitymanager);
    }

    abstract public function getCategoryList(array $categories_array);

    private function getCategories(EntityManagerInterface $entitymanager): array
    {
        if ($this->categoriesArrayFromDB) {
            return $this->categoriesArrayFromDB;
        } else {
            $sql = "SELECT * FROM categories";
            $stmt = $entitymanager->getConnection()->prepare($sql)->execute()->fetchAllAssociative();
            return $stmt;
        }
    }
}
