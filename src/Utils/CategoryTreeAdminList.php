<?php

namespace App\Utils;

use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeAdminList extends CategoryTreeAbstract
{
    public function getCategoryList(array $categories_array)
    {
        $this->categorylist .= '<ul class="fa-ul text-left">';
        foreach ($categories_array as $value) {
            $url_edit = $this->urlgenerator->generate('edit_category', ['id' => $value['id']]);
            $url_delete = $this->urlgenerator->generate('delete_category', ['id' => $value['id']]);

            $this->categorylist .= '<li><i class="fa-li fa fa-arrow-right"></i>' . $value['name'] . '<a href="' . $url_edit . '">' . ' Edit' .
                '</a> <a onclick="return confirm(\'Are you sure?\');" href="' . $url_delete . '">' . 'Delete' . '</a>';
            if (!empty($value['children'])) {
                $this->getCategoryList($value['children']);
            }
            $this->categorylist .= '</li>';
        }
        $this->categorylist .= '</ul>';
        return $this->categorylist;
    }
}
