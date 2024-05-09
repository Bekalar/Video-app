<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Video;
use App\Entity\Comment;
use App\DataFixtures\UserFixtures;
use Doctrine\Persistence\ObjectManager;;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->CommentData() as [$content, $user, $video, $created_at]) {
            $comment = new Comment;
            $user = $manager->getRepository(User::class)->find($user);
            $video = $manager->getRepository(Video::class)->find($video);

            $comment->setContent($content);
            $comment->setUser($user);
            $comment->setVideo($video);
            $comment->setCreatedAtForFixtures(new \DateTime($created_at));

            $manager->persist($comment);
        }
        $manager->flush();
    }

    private function CommentData()
    {
        return [
            ['Comment 1 something to text.', 1, 10, '2022-11-05 12:34:55'],
            ['Comment 2 something to text.', 2, 10, '2023-03-06 13:45:55'],
            ['Comment 3 something to text.', 1, 11, '2024-01-01 9:35:00'],
            ['Comment 4 something to text.', 2, 11, '2023-09-04 4:40:02'],
            ['Comment 5 something to text.', 3, 11, '2021-12-13 21:45:45'],
            ['Comment 6 something to text.', 1, 8, '2022-01-10 11:40:03'],
        ];
    }

    public function getDependencies()
    {
        return array(UserFixtures::class);
    }
}
