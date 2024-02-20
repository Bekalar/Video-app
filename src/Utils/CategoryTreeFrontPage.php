<?php

namespace App\Utils;

use App\Twig\Runtime\AppExtensionRuntime;
use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeFrontPage extends CategoryTreeAbstract
{
    public $slugger;
    public $mainParentId, $mainParentName, $currentCategoryName;

    public function getCategoryListAndParent(int $id): string
    {
        $this->slugger = new AppExtensionRuntime;
        $parentData = $this->getMainParent($id);
        $this->mainParentName = $parentData['name'];
        $this->mainParentId = $parentData['id'];
        $key = array_search($id, array_column($this->categoriesArrayFromDB, 'id'));
        $this->currentCategoryName = $this->categoriesArrayFromDB[$key]['name'];

        $categories_array = $this->buildTree($parentData['id']);
        return $this->getCategoryList($categories_array);
    }

    public function getCategoryList(array $categories_array)
    {
        $this->categorylist .= '<ul>';
        foreach ($categories_array as $value) {
            $this->slugger = new AppExtensionRuntime;
            $catName = $this->slugger->slugify($value['name']);
            $url = $this->urlgenerator->generate('video_list', ['categoryname' => $catName, 'id' => $value['id']]);
            $this->categorylist .= '<li>' . '<a href="' . $url . '">' . $catName . '</a>';
            if (!empty($value['children'])) {
                $this->getCategoryList($value['children']);
            }
            $this->categorylist .= '</li>';
        }
        $this->categorylist .= '</ul>';
        return $this->categorylist;
    }

    public function getMainParent(int $id): array
    {
        $key = array_search($id, array_column($this->categoriesArrayFromDB, 'id'));
        if ($this->categoriesArrayFromDB[$key]['parent_id'] != null) {
            return $this->getMainParent($this->categoriesArrayFromDB[$key]['parent_id']);
        } else {
            return [
                'id' => $this->categoriesArrayFromDB[$key]['id'],
                'name' => $this->categoriesArrayFromDB[$key]['name']
            ];
        }
    }
}
