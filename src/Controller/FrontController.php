<?php

namespace App\Controller;

use App\Entity\Video;
use App\Entity\Category;
use App\Entity\Comment;
use App\Repository\VideoRepository;
use App\Utils\CategoryTreeFrontPage;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class FrontController extends AbstractController
{
    #[Route('/', name: 'main_page')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig');
    }

    #[Route('/video-list/category/{categoryname},{id}/{page}', defaults: ['page' => 1], name: 'video_list')]
    public function videoList($id, $page, CategoryTreeFrontPage $categories, EntityManagerInterface $manager, PaginatorInterface $paginator, Request $request): Response
    {
        $categories->getCategoryListAndParent($id);
        $ids = $categories->getChildIds($id);
        array_push($ids, $id);
        $videos = $manager->getRepository(Video::class)
            ->findByChildIds($ids, $page, $paginator, $request->get('sortby'));
        return $this->render('front/video_list.html.twig', [
            'subcategories' => $categories,
            'videos' => $videos
        ]);
    }

    #[Route('/video-details/{video}', name: 'video_details')]
    public function videoDetails(VideoRepository $repository, $video): Response
    {
        return $this->render('front/video_details.html.twig', [
            'video' => $repository->videoDetails($video)
        ]);
    }

    #[Route('/search-results/{page}', methods: ['GET'], defaults: ['page' => 1], name: 'search_results')]
    public function searchResults($page, EntityManagerInterface $manager, PaginatorInterface $paginator, Request $request): Response
    {
        $videos = null;
        $query = null;
        if ($query = $request->get('query')) {
            $videos = $manager->getRepository(Video::class)
                ->findByTitle($query, $page, $paginator, $request->get('sortby'));

            if (!$videos->getItems()) $videos = null;
        }
        return $this->render('front/search_results.html.twig', [
            'videos' => $videos,
            'query' => $query
        ]);
    }

    #[Route('/pricing', name: 'pricing')]
    public function pricing(): Response
    {
        return $this->render('front/pricing.html.twig');
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $helper): Response
    {
        return $this->render('front/login.html.twig', [
            'error' => $helper->getLastAuthenticationError()
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(AuthenticationUtils $helper): Response
    {
        throw new \Exception('This should never be reached!');
    }

    #[Route('/payment', name: 'payment')]
    public function payment(): Response
    {
        return $this->render('front/payment.html.twig');
    }

    #[Route('/new-comment/{video}', methods: ['POST'], name: 'new_comment')]
    public function newComment(Video $video, Request $request, EntityManagerInterface $manager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        if (!empty(trim($request->request->get('comment')))) {
            $comment = new Comment();
            $comment->setContent($request->request->get('comment'));
            $comment->setUser($this->getUser());
            $comment->setVideo($video);

            $manager->persist($comment);
            $manager->flush();
        }


        return $this->redirectToRoute('video_details', ['video' => $video->getId()]);
    }

    #[Route('/video-list/{video}/like', name: 'like_video', methods: ['POST'])]
    #[Route('/video-list/{video}/dislike', name: 'dislike_video', methods: ['POST'])]
    #[Route('/video-list/{video}/unlike', name: 'undo_like_video', methods: ['POST'])]
    #[Route('/video-list/{video}/undodislike', name: 'undo_dislike_video', methods: ['POST'])]
    public function toggleLikesAjax(Video $video, Request $request)
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        switch ($request->get('_route')) {
            case 'like_video':
                $result = $this->likeVideo($video);
                break;

            case 'dislike_video':
                $result = $this->dislikeVideo($video);
                break;

            case 'undo_like_video':
                $result = $this->undoLikeVideo($video);
                break;

            case 'undo_dislike_video':
                $result = $this->undoDislikeVideo($video);
                break;
        }

        return $this->json(['action' => $result, 'id' => $video->getId()]);
    }

    private function likeVideo($video)
    {
        return 'liked';
    }
    private function dislikeVideo($video)
    {
        return 'disliked';
    }
    private function undoLikeVideo($video)
    {
        return 'undo liked';
    }
    private function undoDislikeVideo($video)
    {
        return 'undo disliked';
    }

    public function mainCategories(EntityManagerInterface $manager)
    {
        $categories = $manager->getRepository(Category::class)->findBy(['parent' => null], ['name' => 'ASC']);
        return $this->render('front/_main_categories.html.twig', ['categories' => $categories]);
    }
}
