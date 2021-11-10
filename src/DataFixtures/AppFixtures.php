<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use function Symfony\Component\String\u;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Movie;
use App\Entity\Kind;

class AppFixtures extends Fixture
{
    private $passwordHasher;
    private $slugger;

    public function __construct(UserPasswordHasherInterface $passwordHasher, SluggerInterface $slugger)
    {
        $this->passwordHasher = $passwordHasher;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadKinds($manager);
        $this->loadMovies($manager);
    }

    private function loadUsers(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$password, $email, $roles]) {
            $user = new User();
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);

            $manager->persist($user);
            $this->addReference($email, $user);
        }

        $manager->flush();
    }

    private function loadKinds(ObjectManager $manager): void
    {
        foreach ($this->getKindData() as $index => $name) {
            $kind = new Kind();
            $kind->setName($name);

            $manager->persist($kind);
            $this->addReference('kind-'.$name, $kind);
        }

        $manager->flush();
    }

    private function loadMovies(ObjectManager $manager): void
    {
        foreach ($this->getMovieData() as [$name, $slug, $image, $created, $author, $kinds]) {
            $movie = new Movie();
            $movie->setName($name);
            $movie->setSlug($slug);
            $movie->setImage($image);
            $movie->setCreated($created);
            $movie->setAuthor($author);
            $movie->addKind(...$kinds);

            foreach (range(1, 5) as $i) {
                $comment = new Comment();
                $comment->setAuthor($this->getReference('usr1@gmail.com'));
                $comment->setContent($this->getRandomText(random_int(255, 512)));
                $comment->setCreated(new \DateTime('now + '.$i.'seconds'));

                $movie->addComment($comment);
            }

            $manager->persist($movie);
        }

        $manager->flush();
    }

    private function getUserData(): array
    {
        return [
            // $userData = [$fullname, $username, $password, $email, $roles];
            ['Admin,123456', 'adm1@gmail.com', ['ROLE_ADMIN']],
            ['Admin,123456', 'adm2@gmail.com', ['ROLE_ADMIN']],
            ['User,123456', 'usr1@gmail.com', ['ROLE_USER']],
        ];
    }

    private function getKindData(): array
    {
        return [
            'Action',
            'Thriller',
            'Horreur',
            'Fantastique',
            'Romance',
            'Science-fiction',
            'Manga',
            'Animation',
        ];
    }

    private function getMovieData()
    {
        $movies = [];
        foreach ($this->getPhrases() as $i => $name) {
            // $movieData = [$name, $slug, $image, $created, $author, $kinds, $comments];
            $movies[] = [
                $name,
                $this->slugger->slug($name)->lower(),
                'https://via.placeholder.com/1024x500',
                new \DateTime('now - '.$i.'days'),
                // Ensure that the first movie is written by usr1@gmail.com to simplify tests
                $this->getReference(['usr1@gmail.com', 'adm1@gmail.com'][0 === $i ? 0 : random_int(0, 1)]),
                $this->getRandomKinds(),
            ];
        }

        return $movies;
    }

    private function getRandomText(int $maxLength = 255): string
    {
        $phrases = $this->getPhrases();
        shuffle($phrases);

        do {
            $text = u('. ')->join($phrases)->append('.');
            array_pop($phrases);
        } while ($text->length() > $maxLength);

        return $text;
    }

    private function getPhrases(): array
    {
        return [
            'Lorem ipsum dolor sit amet consectetur adipiscing elit',
            'Pellentesque vitae velit ex',
            'Mauris dapibus risus quis suscipit vulputate',
            'Eros diam egestas libero eu vulputate risus',
            'In hac habitasse platea dictumst',
            'Morbi tempus commodo mattis',
            'Ut suscipit posuere justo at vulputate',
            'Ut eleifend mauris et risus ultrices egestas',
            'Aliquam sodales odio id eleifend tristique',
            'Urna nisl sollicitudin id varius orci quam id turpis',
            'Nulla porta lobortis ligula vel egestas',
            'Curabitur aliquam euismod dolor non ornare',
            'Sed varius a risus eget aliquam',
            'Nunc viverra elit ac laoreet suscipit',
            'Pellentesque et sapien pulvinar consectetur',
            'Ubi est barbatus nix',
            'Abnobas sunt hilotaes de placidus vita',
            'Ubi est audax amicitia',
            'Eposs sunt solems de superbus fortis',
            'Vae humani generis',
            'Diatrias tolerare tanquam noster caesium',
            'Teres talis saepe tractare de camerarius flavum sensorem',
            'Silva de secundus galatae demitto quadra',
            'Sunt accentores vitare salvus flavum parses',
            'Potus sensim ad ferox abnoba',
            'Sunt seculaes transferre talis camerarius fluctuies',
            'Era brevis ratione est',
            'Sunt torquises imitari velox mirabilis medicinaes',
            'Mineralis persuadere omnes finises desiderium',
            'Bassus fatalis classiss virtualiter transferre de flavum',
        ];
    }

    private function getRandomKinds(): array
    {
        $kindNames = $this->getKindData();
        shuffle($kindNames);
        $selectedKinds = \array_slice($kindNames, 0, random_int(2, 4));

        return array_map(function ($kindName) { return $this->getReference('kind-'.$kindName); }, $selectedKinds);
    }
}
