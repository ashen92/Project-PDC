<?php
use App\Entities\Group;
use App\Entities\Policy;
use App\Entities\Role;
use App\Entities\User;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../src/container.php";

$entityManager = $container->get("doctrine.entity_manager");

$user = new User();
$user->setDetails("1@mail.com", "Green");
$user1 = new User();
$user1->setDetails("2@mail.com", "Admin");
$user2 = new User();
$user2->setDetails("3@mail.com", "Coordinator");
$user3 = new User();
$user3->setDetails("4@mail.com", "Wood");
$user4 = new User();
$user4->setDetails("5@mail.com", "Root");
$user5 = new User();
$user5->setDetails("5@mail.com", "Head");
$user6 = new User();
$user6->setDetails("6@mail.com", "Apple");
$user7 = new User();
$user7->setDetails("7@mail.com", "Orange");

$entityManager->persist($user);
$entityManager->persist($user1);
$entityManager->persist($user2);
$entityManager->persist($user3);
$entityManager->persist($user4);
$entityManager->persist($user5);
$entityManager->persist($user6);
$entityManager->persist($user7);
$entityManager->flush();

echo "Added users\n";

$groupCoordinators = new Group();
$groupPartners = new Group();
$groupStudents = new Group();
$groupThirdYearStudents = new Group();
$groupFirstYearStudents = new Group();

$groupThirdYearStudents->addUser($user);
$groupThirdYearStudents->addUser($user3);
$groupFirstYearStudents->addUser($user4);
$groupFirstYearStudents->addUser($user5);
$groupPartners->addUser($user6);
$groupPartners->addUser($user7);
$groupCoordinators->addUser($user1);
$groupCoordinators->addUser($user2);
$groupStudents->addUser($user);
$groupStudents->addUser($user3);
$groupStudents->addUser($user4);
$groupStudents->addUser($user5);

$entityManager->persist($groupCoordinators);
$entityManager->persist($groupPartners);
$entityManager->persist($groupStudents);
$entityManager->persist($groupFirstYearStudents);
$entityManager->persist($groupThirdYearStudents);
$entityManager->flush();

echo "Added groups\n";

$roleCoordinator = new Role();
$rolePartner = new Role();
$roleStudent = new Role();
$roleAdmin = new Role();

$roleCoordinator->addGroup($groupCoordinators);
$rolePartner->addGroup($groupPartners);
$roleStudent->addGroup($groupStudents);
$roleAdmin->addGroup($groupCoordinators);

$entityManager->persist($roleCoordinator);
$entityManager->persist($rolePartner);
$entityManager->persist($roleStudent);
$entityManager->persist($roleAdmin);
$entityManager->flush();

echo "Added roles\n";

$policyCanEditUsers = new Policy("CanEditUsers");
$policyCanDeleteUsers = new Policy("CanDeleteUsers");
$policyCanViewUsers = new Policy("CanViewUsers");

$roleAdmin->addPolicy($policyCanViewUsers);
$roleAdmin->addPolicy($policyCanEditUsers);
$roleAdmin->addPolicy($policyCanDeleteUsers);

$entityManager->persist($policyCanEditUsers);
$entityManager->persist($policyCanDeleteUsers);
$entityManager->persist($policyCanViewUsers);
$entityManager->flush();

echo "Added policies\n";
