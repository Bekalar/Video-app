<?php

namespace App\Utils\AbstractClasses;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class CategoryTreeAbstract
{
    public $categoriesArrayFromDB;
    public $categorylist;
    protected static $dbconnection;
    public $entitymanager;
    public $urlgenerator;

    public function __construct(EntityManagerInterface $entitymanager, UrlGeneratorInterface $urlgenerator)
    {
        $this->entitymanager = $entitymanager;
        $this->urlgenerator = $urlgenerator;
        $this->categoriesArrayFromDB = $this->getCategories();
    }

    abstract public function getCategoryList(array $categories_array);

    public function buildTree(int $parent_id = null): array
    {
        $subcategory = [];
        foreach ($this->categoriesArrayFromDB as $category) {
            if ($category['parent_id'] == $parent_id) {
                $children = $this->buildTree($category['id']);
                if ($children) {
                    $category['children'] = $children;
                }
                $subcategory[] = $category;
            }
        }
        return $subcategory;
    }

    private function getCategories(): array
    {
        if (self::$dbconnection) {
            return self::$dbconnection;
        } else {
            $connection = $this->entitymanager->getConnection();
            $sql = "SELECT * FROM categories";
            $stmt = $connection->prepare($sql)->executeQuery();
            return self::$dbconnection = $stmt->fetchAllAssociative();
        }
    }
}
