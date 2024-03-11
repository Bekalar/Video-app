<?php

namespace App\Tests\Utils;

use App\Twig\Runtime\AppExtensionRuntime;
use App\Utils\CategoryTreeFrontPage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CategoryTest extends KernelTestCase
{
    protected $mockedCategoryTreeFrontPage;
    protected $mockedCategoryTreeAdminList;
    protected $mockedCategoryTreeAdminOptionList;
    // public $obj;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $urlgenerator = $kernel->getContainer()->get('router');
        // $entitymanager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        // $this->obj= new \App\Utils\CategoryTreeFrontPage($entity, $urlgenerator);
        // $tested_classes = [
        //     'CategoryTreeFrontPage',
        //     'CategoryTreeAdminList',
        //     'CategoryTreeAdminOptionList'
        // ];

        // foreach ($tested_classes as $class) {
        //     $name = 'mocked' . $class;

        //     $this->$name = $this->getMockBuilder('App\Utils\\' . $class)
        //         ->disableOriginalConstructor()
        //         ->getMock();
        //     $this->$name->urlgenerator = $urlgenerator;
        // }

        $this->mockedCategoryTreeFrontPage = $this->getMockBuilder(CategoryTreeFrontPage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockedCategoryTreeFrontPage->urlgenerator = $urlgenerator;

        var_dump("Mocked Category Tree Front Page: ", $this->mockedCategoryTreeFrontPage);
    }

    /**
     * @dataProvider dataForCategoryTreeFrontPage
     */
    // #[DataProvider('dataForCategoryTreeFrontPage')]
    public function testCategoryTreeFrontPage($string, $array, $id)
    {
        $this->mockedCategoryTreeFrontPage->categoriesArrayFromDB = $array;
        $this->mockedCategoryTreeFrontPage->slugger = new AppExtensionRuntime;

        var_dump("ID: " . $id);
        var_dump("Array: ", $array);

        $main_parent_id = $this->mockedCategoryTreeFrontPage->getMainParent($id)['id'];

        var_dump("Main Parent: ", $main_parent_id);

        $array = $this->mockedCategoryTreeFrontPage->buildTree($main_parent_id);
        $this->assertSame($string, $this->mockedCategoryTreeFrontPage->getCategoryList($array));
    }

    public function dataForCategoryTreeFrontPage()
    {

        yield [
            '<ul><li><a href="/video-app/public/video-list/category/cameras,5">Cameras</a></li>
        <li><a href="/video-app/public/video-list/category/computers,6">Computers</a>
        <ul><li><a href="/video-app/public/video-list/category/laptops,8">Laptops</a>
        <ul><li><a href="/video-app/public/video-list/category/apple,10">Apple</a></li>
        <li><a href="/video-app/public/video-list/category/asus,11">Asus</a></li>
        <li><a href="/video-app/public/video-list/category/dell,12">Dell</a></li>
        <li><a href="/video-app/public/video-list/category/lenovo,13">Lenovo</a></li>
        <li><a href="/video-app/public/video-list/category/hp,14">HP</a></li></ul></li>
        <li><a href="/video-app/public/video-list/category/desktops,9">Desktops</a></li></ul></li>
        <li><a href="/video-app/public/video-list/category/cell-phones,7">Cell phones</a></li></ul>',
            [
                ['name' => 'Electronics', 'id' => 1, 'parent_id' => null],
                ['name' => 'Cameras', 'id' => 5,   'parent_id' => 1],
                ['name' => 'Computers', 'id' => 6, 'parent_id' => 1],
                ['name' => 'Cell phones', 'id' => 7, 'parent_id' => 1],
                ['name' => 'Laptops', 'id' => 8, 'parent_id' => 6],
                ['name' => 'Desktops', 'id' => 9, 'parent_id' => 6],
                ['name' => 'Apple', 'id' => 10, 'parent_id' => 8],
                ['name' => 'Asus', 'id' => 11, 'parent_id' => 8],
                ['name' => 'Dell', 'id' => 12, 'parent_id' => 8],
                ['name' => 'Lenovo', 'id' => 13, 'parent_id' => 8],
                ['name' => 'HP', 'id' => 14, 'parent_id' => 8]
            ],
            1
        ];
    }
}
