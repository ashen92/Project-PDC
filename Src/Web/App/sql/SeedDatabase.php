<?php
use App\Entities\Group;
use App\Entities\Internship;
use App\Entities\InternshipCycle;
use App\Entities\Policy;
use App\Entities\Role;
use App\Entities\User;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../src/container.php";

$entityManager = $container->get("doctrine.entity_manager");

echo "Adding users... ";

$passwordHash = "$2y$10\$dLij/BtPMbPKtt/CxpzqVuSn1FBVq.es9spKQ87sdGVJmlu4J3zwq";

$user = new User("1@mail.com", "Green", $passwordHash);
$user1 = new User("2@mail.com", "Admin", $passwordHash);
$user2 = new User("3@mail.com", "Coordinator", $passwordHash);
$user3 = new User("4@mail.com", "Wood", $passwordHash);
$user4 = new User("5@mail.com", "Root", $passwordHash);
$user5 = new User("6@mail.com", "Head", $passwordHash);
$user6 = new User("7@mail.com", "Apple", $passwordHash);
$user7 = new User("8@mail.com", "Orange", $passwordHash);

$entityManager->persist($user);
$entityManager->persist($user1);
$entityManager->persist($user2);
$entityManager->persist($user3);
$entityManager->persist($user4);
$entityManager->persist($user5);
$entityManager->persist($user6);
$entityManager->persist($user7);
$entityManager->flush();

echo "Done.\nAdding groups...";

$groupCoordinators = new Group("Coordinators");
$groupPartners = new Group("Partners");
$groupStudents = new Group("Students");
$groupThirdYearStudents = new Group("ThirdYearStudents");
$groupFirstYearStudents = new Group("FirstYearStudents");

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

echo "Done.\nAdding roles...";

$roleCoordinator = new Role("ROLE_COORDINATOR");
$rolePartner = new Role("ROLE_PARTNER");
$roleStudent = new Role("ROLE_STUDENT");
$roleAdmin = new Role("ROLE_ADMIN");
$roleUser = new Role("ROLE_USER");

$roleCoordinator->addGroup($groupCoordinators);
$rolePartner->addGroup($groupPartners);
$roleStudent->addGroup($groupStudents);
$roleAdmin->addGroup($groupCoordinators);

$entityManager->persist($roleCoordinator);
$entityManager->persist($rolePartner);
$entityManager->persist($roleStudent);
$entityManager->persist($roleAdmin);
$entityManager->flush();

echo "Done.\nAdding policies...";

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

echo "Done.\nAdding internships...";

$internData = [
    [
        "Software Development Intern",
        "Are you passionate about software development and eager to apply your skills in the real world? We are looking for a highly motivated Software Development Intern to join our dynamic team. This is an exciting opportunity to work on cutting-edge technologies and projects that will make a real impact.
    
        Responsibilities:
        Assist in developing and maintaining our web-based applications.
        Work closely with senior developers to implement new features and optimize existing ones.
        Write clean, maintainable, and efficient code.
        Participate in code reviews to maintain a high-quality codebase.
        Test software rigorously and work on bug fixes.
        Collaborate with cross-functional teams to deliver on project goals.
        Qualifications:
        Currently pursuing a degree in Computer Science, Software Engineering, or a related field.
        Familiarity with programming languages such as PHP, Java, or Python.
        Strong problem-solving skills and attention to detail.
        Excellent written and verbal communication skills.
        Previous experience in software development is a plus, but not required.
        Duration:
        3-6 months, with the possibility of extension or full-time employment.
        Location:
        Remote or in our office located in [City, State].
        If you are a proactive learner and thrive in a fast-paced environment, we would love to hear from you! Apply now to kickstart your career in software development."
    ],
    [
        "Frontend Development Intern",
        "We are seeking a Frontend Development Intern to assist in building high-quality web applications. You will work closely with our experienced developers and designers to create user-friendly interfaces.

        Responsibilities:
        Convert UI/UX designs to HTML, CSS, and JavaScript.
        Assist in optimizing web pages for maximum speed and scalability.
        Test website compatibility across different browsers.
        Collaborate with backend developers to integrate RESTful APIs.
        Qualifications:
        Currently pursuing a degree in Computer Science, Web Development, or a related field.
        Familiarity with HTML, CSS, and basic JavaScript.
        Strong attention to detail and willingness to learn."
    ],
    [
        "Mobile App Development Intern",
        "We are looking for an enthusiastic Mobile App Development Intern to join our mobile team. You'll work on developing features for our iOS and Android applications.

        Responsibilities:
        Work on bug fixes and implement new features under guidance from senior developers.
        Write clean, maintainable code following best practices.
        Learn and apply new technologies quickly.
        Qualifications:
        Currently enrolled in a Computer Science or related degree program.
        Basic understanding of Swift or Kotlin.
        A strong passion for mobile app development."
    ],
    [
        "DevOps Intern",
        "Join us as a DevOps Intern to gain hands-on experience in automating, configuring, and optimizing our development pipelines for high performance.

        Responsibilities:
        Assist in managing and deploying cloud-based applications.
        Monitor system performance and troubleshoot issues.
        Learn about CI/CD pipelines and assist in their implementation.
        Qualifications:
        Pursuing a degree in Computer Science, Information Systems, or a related field.
        Basic understanding of cloud services like AWS, Azure, or GCP.
        Familiarity with Linux/Unix commands."
    ],
    [
        "Data Engineering Intern",
        "We are in search of a Data Engineering Intern to assist in developing, constructing, testing, and maintaining architectures such as databases and large-scale processing systems.

        Responsibilities:
        Assist in building scalable, high-performance data pipelines.
        Work closely with data scientists to implement algorithms and models.
        Learn to write complex SQL queries and optimize them for performance.
        Qualifications:
        Currently pursuing a degree in Computer Science, Data Science, or a related field.
        Familiarity with SQL and Python.
        Strong analytical and problem-solving skills."
    ],
];

$internshipCycle = new InternshipCycle();

$internship0 = new Internship($internData[0][0], $internData[0][1], $user7, $internshipCycle);
$internship1 = new Internship($internData[1][0], $internData[1][1], $user7, $internshipCycle);
$internship2 = new Internship($internData[2][0], $internData[2][1], $user7, $internshipCycle);
$internship3 = new Internship($internData[3][0], $internData[3][1], $user7, $internshipCycle);
$internship4 = new Internship($internData[4][0], $internData[4][1], $user7, $internshipCycle);

$entityManager->persist($internshipCycle);
$entityManager->persist($internship0);
$entityManager->persist($internship1);
$entityManager->persist($internship2);
$entityManager->persist($internship3);
$entityManager->persist($internship4);
$entityManager->flush();

echo "Done.\nAdding ...";
