<?php

use App\Entities\Event;
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
$user2 = new User("coordinator@mail.com", "Coordinator", $passwordHash);
$user3 = new User("student@mail.com", "Student", $passwordHash);
$user4 = new User("5@mail.com", "Root", $passwordHash);
$user5 = new User("6@mail.com", "Head", $passwordHash);
$user6 = new User("partner@mail.com", "Apple", $passwordHash);
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

echo "Done.\nAdding events...";

$eventData = [
    [
        "Innovate IT 2023: Shaping the Future of Tech",
        "Join us at 'Innovate IT 2023,' the premier technology event of the year, where industry leaders, innovative startups, and tech enthusiasts come together to explore the future of information technology. This year's conference will feature keynote speeches from thought leaders in AI, cybersecurity, and cloud computing, interactive workshops on the latest programming techniques, and panel discussions on the ethical implications of emerging technologies. Network with peers, engage with expert speakers, and gain insights into cutting-edge IT trends and solutions that will drive your organization forward. Whether you're a seasoned IT professional or just passionate about technology, 'Innovate IT 2023' is the event you can't afford to miss. Secure your spot today and be part of the conversation that will redefine the tech landscape!",
        new DateTime("now"),
        new DateTime("now"),
        new DateTime("now"),
        "Colombo"
    ],
    [
        "CyberSecCon 2023: Navigating the Digital Threat Landscape",
        "CyberSecCon 2023 invites cybersecurity experts and enthusiasts to delve into the dynamic world of digital security. This comprehensive conference will cover the latest strategies in protecting against cyber threats, managing risk, and ensuring compliance. Attend in-depth sessions on blockchain security, IoT vulnerabilities, and the future of encryption. Engage with hands-on demonstrations of cutting-edge security tools and network with professionals across the industry. Whether you're defending your organization's network or safeguarding personal data, CyberSecCon 2023 is your gateway to a safer digital tomorrow.",
        new DateTime("now"),
        new DateTime("now"),
        new DateTime("now"),
        "Colombo"
        ],
    [
        "AI Revolution Summit: Transforming Business with Intelligence",
        "The AI Revolution Summit is where the brightest minds in artificial intelligence converge to discuss the role of AI in transforming business practices. Explore how machine learning algorithms can optimize operations, enhance customer experiences, and drive innovation. This summit offers a unique opportunity to network with AI developers, business leaders, and researchers. Participate in workshops on natural language processing, predictive analytics, and ethical AI. Empower your business with the knowledge and tools needed to thrive in the AI era at the AI Revolution Summit.",
        new DateTime("now"),
        new DateTime("now"),
        new DateTime("now"),
        "Colombo"
    ],
    [
        "Cloud Expo 2023: Elevating Enterprises to the Cloud",
        "Elevate your business to new heights at Cloud Expo 2023! As cloud computing becomes the backbone of the digital economy, this event is a must-attend for IT professionals aiming to harness the power of the cloud. Discover the latest trends in cloud infrastructure, platform services, and SaaS applications. Gain insights from case studies on cloud migration, cost optimization, and security in the cloud. Connect with cloud service providers and partners to transform your IT strategy. Join us at Cloud Expo 2023 and embark on your cloud journey.",
        new DateTime("now"),
        new DateTime("now"),
        new DateTime("now"),
        "Colombo"
    ],
    [
        "DevOps Days: Accelerating Software Delivery",
        "DevOps Days is back, bringing together the community of developers, operations, and anyone involved in the software delivery process. Learn from industry experts about the latest DevOps practices, tools, and culture. Participate in sessions on continuous integration, containerization, and site reliability engineering. Experience hands-on workshops on infrastructure as code, microservices architecture, and collaboration techniques. Network with peers and accelerate your DevOps journey at DevOps Days.",
        new DateTime("now"),
        new DateTime("now"),
        new DateTime("now"),
        "Colombo"
    ],
    [
        "NextGen Data Science Conference: The Future of Data-Driven Decisions",
        "Data enthusiasts, rejoice! The NextGen Data Science Conference is here to explore the innovative ways data is transforming businesses and society. This event is the perfect meeting ground for data scientists, analysts, and business leaders. Immerse yourself in talks about big data analytics, the evolution of data warehousing, and the impact of data on customer experience. Engage in discussions about ethical data usage, privacy concerns, and data governance. Connect with industry pioneers and discover how to leverage data for smarter decisions at the NextGen Data Science Conference.",
        new DateTime("now"),
        new DateTime("now"),
        new DateTime("now"),
        "Colombo"
    ]
];

foreach ($eventData as $eventData){
    $event = new Event($eventData[0],$eventData[1],$eventData[3],$eventData[4],$eventData[2],$eventData[5]);
    $entityManager->persist($event);
}
$entityManager->flush();

echo "Done.\nAdding ...";