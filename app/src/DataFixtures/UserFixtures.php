<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends BaseFixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(1000, 'main_users', function () {
            $user = new User();

            do {
                $email = $this->faker->unique()->email;
            } while ($this->userRepository->findBy(['email' => $email]));

            $user->setEmail($email);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword('demo');
            $user->setFirstName($this->faker->firstName());
            $user->setLastName($this->faker->lastName());
            $user->setDateCreated($this->faker->dateTimeThisDecade());

            return $user;
        });

        $this->createMany(2, 'admin_users', function ($i) {
            $user = new User();
            $user->setEmail(sprintf('admin%d@admin.com', $i));
            $user->setRoles(['ROLE_ADMIN']);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'admin'
            ));
            $user->setFirstName($this->faker->firstName());
            $user->setLastName($this->faker->lastName());
            $user->setDateCreated($this->faker->dateTimeThisDecade());

            return $user;
        });

        $manager->flush();
    }
}
