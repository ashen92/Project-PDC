<?php

use App\DTOs\CreateRequirementDTO;
use App\Entities\Event;
use App\Entities\Internship;
use App\Entities\InternshipCycle;
use App\Entities\Organization;
use App\Entities\Partner;
use App\Entities\Permission;
use App\Entities\Permission\Action;
use App\Entities\Permission\Resource;
use App\Entities\Requirement;
use App\Entities\Role;
use App\Entities\Student;
use App\Entities\User;
use App\Entities\UserGroup;
use App\Entities\UserRequirement;
use App\Models\Requirement\FulFillMethod;
use App\Models\Requirement\RepeatInterval;
use App\Models\Requirement\Type;

#region Users

echo "Adding users... ";

$passwordHash = "$2y$10\$dLij/BtPMbPKtt/CxpzqVuSn1FBVq.es9spKQ87sdGVJmlu4J3zwq";

$user1 = new User("2@mail.com", "Admin", $passwordHash);
$user2 = new User("coordinator@mail.com", "Coordinator", $passwordHash);
$user3 = new Student(
    "student-email@mail.com",
    "Default Student User",
    "1900is001",
    "19090111",
    "student@mail.com",
    "Student",
    $passwordHash
);
$user4 = new Partner("partner@mail.com", "Partner", $passwordHash);

$entityManager->persist($user1);
$entityManager->persist($user2);
$entityManager->persist($user3);
$entityManager->persist($user4);

// Add student users ----------------------------------------------

$studentUsers = [];

$studentUsers[0] = new Student(
    "2021is084@stu.ucsc.cmb.ac.lk",
    "H.D.A.H. Sandaruwan",
    "2000is001",
    "21020841",
);
$entityManager->persist($studentUsers[0]);

for ($i = 1; $i < 600; $i++) {
    $studentUsers[$i] = new Student(
        "2021is084+{$i}@stu.ucsc.cmb.ac.lk",
        "Student User {$i}",
        "2000is001+{$i}",
        "21020841+{$i}",
    );
    $entityManager->persist($studentUsers[$i]);
}

// ----------------------------------------------------------------

// Add partner users ----------------------------------------------

$partnerUsers = [];

$partnerUsersData = [
    "Kaylie Moss",
    "Porter Stafford",
    "Bridget Whitney",
    "Jeffery Rangel",
    "Gloria Pham",
    "Russell Munoz",
    "Kehlani Burton",
    "Zander McKenzie",
    "Briar Compton",
    "Abner Elliott",
    "Noelle Hill",
    "Isaac Goodman",
    "Carolina Rich",
    "Miller Chambers",
    "Makayla Nicholson",
    "Rodrigo Swanson",
    "Helen Chase",
    "Otis McDaniel",
    "Dahlia Ibarra",
    "Asa Sampson",
    "Meilani Yu",
    "Bryant Sutton",
    "Izabella Carey",
    "Watson Wang",
    "Kailani Carson",
    "Ares Velazquez",
    "Jaliyah O’Neal",
    "Eddie Cochran",
    "Alma Rivera",
    "Charles Cantrell",
    "Yamileth Hampton",
    "Hank Andrade",
    "Emmy Bowers",
    "Dorian Hendricks",
    "Dani Berry",
    "Adonis Yu",
    "Navy Felix",
    "Rodney Valenzuela",
    "Henley Lara",
    "Caiden Hahn",
    "Fallon Russell",
    "Weston Montgomery",
    "Evangeline Trujillo",
    "Apollo Truong",
    "Judith Hamilton",
    "Jason Wall",
    "Jayda Todd",
    "Baylor Patrick",
    "Lyra Perez",
    "Owen Hurley",
    "Rylan Ortiz",
    "Landon Espinoza",
    "Lucille Harvey",
    "Cayden Harvey",
    "Nicole Martin",
    "Mateo Mays",
    "Denisse Rose",
    "Hayden Mayo",
    "Aarya Ward",
    "Jameson Anthony",
    "Macy Osborne",
    "Augustus Pope",
    "Aurelia Copeland",
    "Axton Watson",
    "Hailey Tang",
    "Rogelio Boyer",
    "Chaya Wilkinson",
    "Leonard Kemp",
    "Anika Johnston",
    "Felix Hickman",
    "Scarlette Ruiz",
    "Austin Hebert",
    "Kyleigh Snow",
    "Houston Barrera",
    "Beatrice Waters",
    "Maximilian Travis",
    "Mazikee Crawford",
    "Kevin Zhang",
    "Sarai Hendrix",
    "Korbyn Alvarez",
    "Leilani Nielsen",
    "Tru Gillespie",
    "Alianna Harrington",
    "Omari Byrd",
    "Giselle Navarro",
    "Reid Barker",
    "Remington Andrews",
    "Lukas Conley",
    "Salem Vaughn",
    "Remy O’Neal",
    "Treasure Blake",
    "Zyaire Kemp",
    "Anika Fowler",
    "Kameron Harris",
    "Penelope Powers",
    "Sean Bradford",
    "Rhea Sierra",
    "Dayton Bowen",
    "Dream Blair",
    "Troy Jensen",
];

for ($i = 0; $i < 100; $i++) {
    $partnerUsers[$i] = new Partner(
        "partner{$i}@mail.com",
        $partnerUsersData[$i],
        $passwordHash
    );
    $entityManager->persist($partnerUsers[$i]);
}
$entityManager->flush();

// ----------------------------------------------------------------
// Add managed partners -------------------------------------------

for ($i = 0; $i < 15; $i++) {
    $user4->addToManage($partnerUsers[$i]);
}

$entityManager->persist($user4);
$entityManager->flush();

#endregion

#region Organizations

echo "Done.\nAdding organizations...";

$orgData = [
    0 => [
        "Microsoft",
        "One Microsoft Way, Redmond, WA 98052, United States",
        "Redmond",
        "Technology",
        "https://www.microsoft.com/",
        "Be what's next",
        "image-jpeg-uuid-49df2eb0-9b7b-11ee-a730-9109ff162764",
    ],
    1 => [
        "Google",
        "1600 Amphitheatre Parkway, Mountain View, CA 94043, United States",
        "Mountain View",
        "Technology",
        "https://www.google.com/",
        "Do the right thing",
        "image-jpeg-uuid-49dbd350-9b7b-11ee-a730-9109ff162764",
    ],
    2 => [
        "Amazon",
        "410 Terry Ave N, Seattle, WA 98109, United States",
        "Seattle",
        "Technology",
        "https://www.amazon.com/",
        "Work hard. Have fun. Make history.",
        "image-jpeg-uuid-49d850e0-9b7b-11ee-a730-9109ff162764",
    ],
    3 => [
        "Meta",
        "1 Hacker Way, Menlo Park, CA 94025, United States",
        "Menlo Park",
        "Technology",
        "https://www.meta.com/",
        "Bring the world closer together",
        "image-jpeg-uuid-49dda810-9b7b-11ee-a730-9109ff162764",
    ],
    4 => [
        "Apple",
        "1 Apple Park Way, Cupertino, CA 95014, United States",
        "Cupertino",
        "Technology",
        "https://www.apple.com/",
        "Think different",
        "image-jpeg-uuid-49da25a0-9b7b-11ee-a730-9109ff162764",
    ],
    5 => [
        "Wiley",
        "111 River St, Hoboken, NJ 07030, United States",
        "Hoboken",
        "Publishing",
        "https://www.wiley.com/",
        "For over 200 years we have been helping people and organizations develop the skills and knowledge they need to succeed",
        "image-jpeg-uuid-a26cf3f0-a265-11ee-96e3-d1f5316d3366",
    ],
    6 => [
        "London Stock Exchange Group",
        "10 Paternoster Square, London EC4M 7LS, United Kingdom",
        "London",
        "Financial Services",
        "https://www.lseg.com/",
        "We are a global financial markets infrastructure business. We provide the platforms, tools and technologies that underpin the world’s financial markets.",
        "image-jpeg-uuid-475b0860-a26c-11ee-96e3-d1f5316d3366",
    ],
    7 => [
        "Dialog Axiata PLC",
        "475, Union Place, Colombo 02, Sri Lanka",
        "Colombo",
        "Telecommunications",
        "https://www.dialog.lk/",
        "We are Sri Lanka's largest and fastest growing mobile network operator, with over 14 million subscribers.",
        "image-jpeg-uuid-ebffa2a0-a270-11ee-96e3-d1f5316d3366",
    ],
    8 => [
        "Lockheed Martin",
        "6801 Rockledge Dr, Bethesda, MD 20817, United States",
        "Bethesda",
        "Aerospace",
        "https://www.lockheedmartin.com/",
        "We solve complex challenges, advance scientific discovery and deliver innovative solutions to help our customers keep people safe.",
        "image-jpeg-uuid-c3d893b0-a278-11ee-96e3-d1f5316d3366"
    ]
];

$organizations = [];

foreach ($orgData as $key => $org) {
    $o = new Organization(
        $org[0],
        $org[1],
        $org[2],
        $org[3],
        $org[4],
        $org[5],
        $org[6]
    );
    $entityManager->persist($o);
    $organizations[] = $o;
}

$user4->setOrganization($organizations[0]);
$partnerUsers[0]->setOrganization($organizations[1]);
$partnerUsers[1]->setOrganization($organizations[2]);
$partnerUsers[2]->setOrganization($organizations[3]);
$partnerUsers[3]->setOrganization($organizations[4]);
$partnerUsers[4]->setOrganization($organizations[5]);
$partnerUsers[5]->setOrganization($organizations[6]);
$partnerUsers[6]->setOrganization($organizations[7]);
$partnerUsers[7]->setOrganization($organizations[8]);

$entityManager->persist($user4);
$entityManager->flush();

#endregion

#region Groups

echo "Done.\nAdding groups...";

$groupCoordinators = new UserGroup('Coordinators');
$groupPartners = new UserGroup('Partners');
$groupStudents = new UserGroup('Students');
$groupThirdYearStudents = new UserGroup('ThirdYearStudents');
$groupFirstYearStudents = new UserGroup('FirstYearStudents');

foreach ($studentUsers as $user) {
    $groupStudents->addUser($user);
}

for ($i = 1; $i < 200; $i++) {
    $groupThirdYearStudents->addUser($studentUsers[$i]);
}

for ($i = 200; $i < 400; $i++) {
    $groupFirstYearStudents->addUser($studentUsers[$i]);
}

foreach ($partnerUsers as $user) {
    $groupPartners->addUser($user);
}

$groupStudents->addUser($user3);
$groupPartners->addUser($user4);

$groupCoordinators->addUser($user1);
$groupCoordinators->addUser($user2);

$groupCycleStudents = new UserGroup(
    \App\Models\UserGroup::AUTO_GENERATED_USER_GROUP_PREFIX . 'InternshipCycle-Students'
);
$entityManager->persist($groupCycleStudents);
$groupCycleStudents->addUser($user3);

for ($i = 1; $i < 200; $i++) {
    $groupCycleStudents->addUser($studentUsers[$i]);
}

$groupCyclePartnerAdmins = new UserGroup(
    \App\Models\UserGroup::AUTO_GENERATED_USER_GROUP_PREFIX . 'InternshipCycle-Partner-Admins'
);
$entityManager->persist($groupCyclePartnerAdmins);
$groupCyclePartnerAdmins->addUser($user4);

for ($i = 1; $i < 50; $i++) {
    $groupCyclePartnerAdmins->addUser($partnerUsers[$i]);
}

$groupCyclePartners = new UserGroup(
    \App\Models\UserGroup::AUTO_GENERATED_USER_GROUP_PREFIX . 'InternshipCycle-Partners'
);
$entityManager->persist($groupCyclePartners);

$entityManager->persist($groupCoordinators);
$entityManager->persist($groupPartners);
$entityManager->persist($groupStudents);
$entityManager->persist($groupFirstYearStudents);
$entityManager->persist($groupThirdYearStudents);
$entityManager->flush();

#endregion

#region Roles

echo "Done.\nAdding roles...";

$roleCoordinator = new Role('coordinator');
$entityManager->persist($roleCoordinator);
$roleCoordinator->addGroup($groupCoordinators);

$rolePartner = new Role('partner');
$entityManager->persist($rolePartner);
$rolePartner->addGroup($groupPartners);

$roleStudent = new Role('student');
$entityManager->persist($roleStudent);
$roleStudent->addGroup($groupStudents);

$roleAdmin = new Role('admin');
$entityManager->persist($roleAdmin);
$roleAdmin->addGroup($groupCoordinators);

$roleIntProgAdmin = new Role('internship_program_admin');
$entityManager->persist($roleIntProgAdmin);
$roleIntProgAdmin->addGroup($groupCoordinators);

$roleIntProgPartnerAdmin = new Role('internship_program_partner_admin');
$entityManager->persist($roleIntProgPartnerAdmin);
$roleIntProgPartnerAdmin->addGroup($groupCyclePartnerAdmins);

$roleIntProgStudent = new Role('internship_program_student');
$entityManager->persist($roleIntProgStudent);
$roleIntProgStudent->addGroup($groupCycleStudents);

$roleIntProgPartner = new Role('internship_program_partner');
$entityManager->persist($roleIntProgPartner);
$roleIntProgPartner->addGroup($groupCyclePartnerAdmins);

$entityManager->flush();

#endregion

#region Permissions

echo "Done.\nAdding permissions...";

$rInternship = new Resource('internship');
$entityManager->persist($rInternship);
$rApplication = new Resource('application');
$entityManager->persist($rApplication);
$rInternshipProgram = new Resource('internship_program');
$entityManager->persist($rInternshipProgram);

$aRead = new Action('read');
$aCreate = new Action('create');
$aUpdate = new Action('update');
$aDelete = new Action('delete');
$aApply = new Action('apply');
$entityManager->persist($aRead);
$entityManager->persist($aCreate);
$entityManager->persist($aUpdate);
$entityManager->persist($aDelete);
$entityManager->persist($aApply);

$pCInternship = new Permission($rInternship, $aCreate);
$entityManager->persist($pCInternship);
$pUInternship = new Permission($rInternship, $aUpdate);
$entityManager->persist($pUInternship);
$pDInternship = new Permission($rInternship, $aDelete);
$entityManager->persist($pDInternship);
$pAInternship = new Permission($rInternship, $aApply);
$entityManager->persist($pAInternship);
$pRApplication = new Permission($rApplication, $aRead);
$entityManager->persist($pRApplication);
$pRInternshipProgram = new Permission($rInternshipProgram, $aRead);
$entityManager->persist($pRInternshipProgram);

$entityManager->flush();

$roleIntProgPartnerAdmin->addPermission($pCInternship);
$roleIntProgPartnerAdmin->addPermission($pUInternship);
$roleIntProgPartnerAdmin->addPermission($pDInternship);
$roleIntProgPartnerAdmin->addPermission($pRApplication);
$roleIntProgPartnerAdmin->addPermission($pRInternshipProgram);

$roleIntProgAdmin->addPermission($pCInternship);
$roleIntProgAdmin->addPermission($pUInternship);
$roleIntProgAdmin->addPermission($pDInternship);
$roleIntProgAdmin->addPermission($pRApplication);
$roleIntProgAdmin->addPermission($pRInternshipProgram);

$roleIntProgStudent->addPermission($pAInternship);
$roleIntProgStudent->addPermission($pRInternshipProgram);

$entityManager->flush();

#endregion

#region Internships

echo "Done.\nAdding internships...";

$internData = [
    [
        "Software Development Intern",
        "<p>Are you passionate about software development and eager to apply your skills in the real world? We are looking for a highly motivated Software Development Intern to join our dynamic team. This is an exciting opportunity to work on cutting-edge technologies and projects that will make a real impact. </p><p><br></p><p><strong>Responsibilities: </strong></p><ul><li>Assist in developing and maintaining our web-based applications. </li><li>Work closely with senior developers to implement new features and optimize existing ones. </li><li>Write clean, maintainable, and efficient code. </li><li>Participate in code reviews to maintain a high-quality codebase. </li><li>Test software rigorously and work on bug fixes. </li><li>Collaborate with cross-functional teams to deliver on project goals. </li></ul><p><br></p><p><strong>Qualifications: </strong></p><ul><li>Currently pursuing a degree in Computer Science, Software Engineering, or a related field. </li><li>Familiarity with programming languages such as PHP, Java, or Python. </li><li>Strong problem-solving skills and attention to detail. </li><li>Excellent written and verbal communication skills. </li><li>Previous experience in software development is a plus, but not required. </li></ul><p><br></p><p><strong>Duration: </strong></p><p>3-6 months, with the possibility of extension or full-time employment. </p><p><br></p><p><strong>Location: </strong></p><p>Remote or in our office located in [City, State]. </p><p><br></p><p>If you are a proactive learner and thrive in a fast-paced environment, we would love to hear from you! Apply now to kickstart your career in software development.</p>",
        $partnerUsers[3],
        false,
    ],
    [
        "Frontend Development Intern",
        "<p>We are seeking a Frontend Development Intern to assist in building high-quality web applications. You will work closely with our experienced developers and designers to create user-friendly interfaces.</p><p><br></p><p><strong>Responsibilities:</strong></p><ul><li>Convert UI/UX designs to HTML, CSS, and JavaScript.</li><li>Assist in optimizing web pages for maximum speed and scalability.</li><li>Test website compatibility across different browsers.</li><li>Collaborate with backend developers to integrate RESTful APIs.</li></ul><p><br></p><p><strong>Qualifications: </strong></p><ul><li>Currently pursuing a degree in Computer Science, Web Development, or a related field.</li><li>Familiarity with HTML, CSS, and basic JavaScript.</li><li>Strong attention to detail and willingness to learn.</li></ul>",
        $partnerUsers[3],
        false,
    ],
    [
        "Mobile App Development Intern",
        "<p>We are looking for an enthusiastic Mobile App Development Intern to join our mobile team. You'll work on developing features for our iOS and Android applications. </p><p><br></p><p><strong>Responsibilities: </strong></p><ul><li>Work on bug fixes and implement new features under guidance from senior developers. </li><li>Write clean, maintainable code following best practices. </li><li>Learn and apply new technologies quickly. </li></ul><p><br></p><p><strong>Qualifications: </strong></p><ul><li>Currently enrolled in a Computer Science or related degree program. </li><li>Basic understanding of Swift or Kotlin. </li><li>A strong passion for mobile app development.</li></ul>",
        $partnerUsers[2],
        true,
    ],
    [
        "DevOps Intern",
        "<p>Join us as a DevOps Intern to gain hands-on experience in automating, configuring, and optimizing our development pipelines for high performance. </p><p><br></p><p><strong>Responsibilities: </strong></p><ul><li>Assist in managing and deploying cloud-based applications. Monitor system performance and troubleshoot issues. </li><li>Learn about CI/CD pipelines and assist in their implementation. </li></ul><p><br></p><p><strong>Qualifications: </strong></p><ul><li>Pursuing a degree in Computer Science, Information Systems, or a related field. </li><li>Basic understanding of cloud services like AWS, Azure, or GCP. </li><li>Familiarity with Linux/Unix commands.</li></ul>",
        $partnerUsers[1],
        false,
    ],
    [
        "Data Engineering Intern",
        "<p>We are in search of a Data Engineering Intern to assist in developing, constructing, testing, and maintaining architectures such as databases and large-scale processing systems.</p><p><br></p><p><strong>Responsibilities: </strong></p><ul><li>Assist in building scalable, high-performance data pipelines. </li><li>Work closely with data scientists to implement algorithms and models. </li><li>Learn to write complex SQL queries and optimize them for performance.</li></ul><p><br></p><p><strong>Qualifications: </strong></p><ul><li>Currently pursuing a degree in Computer Science, Data Science, or a related field. </li><li>Familiarity with SQL and Python. </li><li>Strong analytical and problem-solving skills.</li></ul>",
        $partnerUsers[0],
        false,
    ],
    [
        "Cybersecurity Intern",
        "<p>Are you passionate about cybersecurity and eager to apply your skills in the real world? We are looking for a highly motivated Cybersecurity Intern to join our dynamic team. This is an exciting opportunity to work on cutting-edge technologies and projects that will make a real impact. </p><p><br></p><p><strong>Responsibilities: </strong></p><ul><li>Assist in developing and maintaining our web-based applications. </li><li>Work closely with senior developers to implement new features and optimize existing ones. </li><li>Write clean, maintainable, and efficient code. </li><li>Participate in code reviews to maintain a high-quality codebase. </li><li>Test software rigorously and work on bug fixes. </li><li>Collaborate with cross-functional teams to deliver on project goals. </li></ul><p><br></p><p><strong>Qualifications: </strong></p><ul><li>Currently pursuing a degree in Computer Science, Software Engineering, or a related field. </li><li>Familiarity with programming languages such as PHP, Java, or Python. </li><li>Strong problem-solving skills and attention to detail. </li><li>Excellent written and verbal communication skills. </li><li>Previous experience in software development is a plus, but not required. </li></ul><p><br></p><p><strong>Duration: </strong></p><p>3-6 months, with the possibility of extension or full-time employment. </p><p><br></p><p><strong>Location: </strong></p><p>Remote or in our office located in [City, State]. </p><p><br></p><p>If you are a proactive learner and thrive in a fast-paced environment, we would love to hear from you! Apply now to kickstart your career in software development.</p>",
        $partnerUsers[1],
        false,
    ],
    [
        "Web Developer Intern - Angular/React",
        "<p><strong>Summary </strong></p><ul><li>We are seeking a highly motivated and talented Web Developer Intern with expertise in Angular/React to join our dynamic development team. As a Web Developer Intern, you will have the opportunity to gain hands-on experience in creating robust and interactive web applications using the Angular/React framework. This internship will provide you with valuable insights into the world of web development, allowing you to apply your skills and knowledge in a real-world professional setting. </li></ul><p><br></p><p><strong>Responsibilities </strong></p><ul><li>Collaborate with the development team to design, develop, and maintain web applications using the Angular/React framework. </li><li>Write clean, efficient, and well-documented code that adheres to best practices and coding standards. </li><li>Participate in the entire software development lifecycle, including requirements gathering, design, development, testing, and deployment. </li><li>Work closely with cross-functional teams to understand business requirements and translate them into technical solutions. </li><li>Conduct thorough testing and debugging of applications to ensure high-quality, reliable performance. </li><li>Stay up-to-date with the latest industry trends and technologies, and propose innovative ideas to enhance web development processes. </li></ul><p><br></p><p><strong>Requirements </strong></p><ul><li>Currently pursuing a degree in Computer Science, Software Engineering, or a related field. </li><li>Proficient in Angular/React framework, with experience in developing responsive, scalable web applications. </li><li>Strong understanding of web development concepts, HTML, CSS, TypeScript, and JavaScript. </li><li>Familiarity with RESTful APIs and integrating front-end applications with back-end services. </li><li>Solid problem-solving and analytical skills, with keen attention to detail. </li><li>Ability to work independently and collaboratively in a fast-paced, deadline-driven environment. </li><li>Excellent communication and interpersonal skills. </li></ul><p><br></p><p><strong>Job Type </strong></p><ul><li>Internship </li></ul><p><br></p><p><strong>Location </strong></p><ul><li>Remote </li></ul><p><br></p><p><strong>Duration </strong></p><ul><li>4 / 6 Months</li></ul>",
        $partnerUsers[2],
        false,
    ],
    [
        "Intern, Quality Assurance",
        "<p>The QA Intern will collaborate with cross-functional teams to execute test cases, identify software defects, and contribute to QA processes. This role offers hands-on experience in software testing methodologies and bug tracking, providing valuable insights into the software development lifecycle. </p><p><br></p><p><strong>Responsibilities </strong></p><ul><li>Collaborate with cross-functional teams to understand project requirements and specifications.</li><li>Execute test cases and analyze results to ensure software quality and functionality.</li><li>Identify, document, and track software defects to resolution.</li><li>Assist in creating and maintaining comprehensive test documentation.</li><li>Work closely with developers to reproduce, debug, and resolve issues.</li><li>Contribute to the improvement of QA processes and best practices. </li></ul><p><br></p><p><strong>Requirements</strong></p><ul><li>Pursuing a degree in Computer Science, Information Technology, or related field.</li><li>Strong analytical and problem-solving skills.</li><li>Excellent attention to detail and a passion for delivering high-quality software.</li><li>Basic understanding of software testing concepts and methodologies is a plus.</li><li>Ability to work independently and collaboratively in a team environment.</li><li>Strong communication skills, both written and verbal.</li></ul>",
        $partnerUsers[0],
        false,
    ],
    [
        "Intern - Network Operations",
        "<p><strong>Location</strong>: Colombo, Sri Lanka</p><p><br></p><p>Our mission is to unlock human potential. We welcome you for who you are, the background you bring, and we embrace individuals who get excited about learning. Bring your experiences, your perspectives, and your passion; it’s in our differences that we empower the way the world learns.</p><p><br></p><p><strong>About The Role</strong></p><p><br></p><p>Wiley is looking for enthusiastic interns to join our exciting, challenging, and rapidly expanding team in Sri Lanka. This position is a hands-on intern position where you will be working with the Network Operations Team for Wiley’s internal applications.</p><p><br></p><p><strong>How You Will Make an Impact</strong></p><p><br></p><ul><li>Proactively monitor network performance and identify anomalies or issues.</li><li>Work with network monitoring tools to ensure smooth operation of the network.</li><li>Collaborate with network engineers to troubleshoot and resolve issues within the SLA.</li><li>Participate in configuring and maintaining of the networking equipment (Firewalls, Routers, Switches etc.)</li><li>Contribute to updating and modifying network related configurations based on organizational standards.</li><li>Document network configurations, changes, and procedures.</li><li>Participate in periodic network device maintenance.</li><li>Participate in Major incident discussions with support teams and incident management teams leveraging support where possible on tasks.</li><li>Take part in innovation and development of Automation software / In house tools to support the operations and reduce the workload.</li></ul><p><br></p><p><strong>What We Look For</strong></p><p><br></p><ul><li>An undergraduate from a recognized university reading for a degree in Computer Science, Network Engineering, IT, or any other related discipline.</li><li>Willingness to work in a roster-based working environment.</li><li>Self-paced or hands-on Networking and firewall environments such as basic installation, basic commands, configurations, and basic capabilities.</li><li>Basic understanding of cloud-based environments such as AWS, Azure, and Google Cloud.</li><li>Good knowledge of networking fundamentals.</li><li>Excellent communication skills along with problem-solving skills</li></ul><p><br></p><p>Related Learnings and Outcomes of Internship</p><p><br></p><ul><li>Expertise in working in a multinational organization environment.</li><li>Hands-on understanding of ITIL-related processes</li><li>Hands-on expertise in most of the professional network skills</li><li>Proactive and Reactive Monitoring of Network devices and firewalls.</li><li>Improve communication skills, understand the dynamics of working in a team.</li><li>Reading, understanding and writing technical documentation.</li></ul><p><br></p><p><strong>About Wiley</strong></p><p><br></p><p>Enabling Discovery, Powering Education, Shaping Workforces.</p><p><br></p><p>We clear the way for seekers of knowledge: illuminating the path forward for research and education, tearing down barriers to society’s advancement, and giving seekers the help, they need to turn their steps into strides.</p><p><br></p><p>Wiley may have been founded over two centuries ago, but our secret to success remains the same: our people. We are willing to challenge the status quo, move the needle, and be innovative. Wiley’s headquarters are located in Hoboken, New Jersey, with operations across the globe in more than 40 countries.</p><p><br></p><p>Please attach your CV in order to be considered for this position.</p><p><br></p><p><strong>Location/Division:</strong></p><p><br></p><p>Colombo, Sri Lanka</p><p><br></p><p><strong>Job Requisition</strong></p><p><br></p><p>R2302479</p><p><br></p><p><strong>Remote Location:</strong></p><p><br></p><p>No</p><p><br></p><p><strong>Time Type</strong></p><p><br></p><p>Full Time</p>",
        $partnerUsers[4],
        false,
    ],
    [
        "Intern, Cloud Operations",
        "<p><strong>Role Responsibilities &amp; Key Accountabilities</strong></p><p><br></p><ul><li> Delivers tasks given related to business processes and reference data on time and to agreed quality, executing to set procedures.</li><li> Solves basic problems / queries.</li><li> Gets well versed with Company’s policies and procedures.</li><li> Performs routine assignments at entry level</li><li> Receives and relays telephone and email queries.</li><li> Performs moderately complex and varied assignments within an administrative function</li><li> Drafts parts of reports for internal and external stakeholders with a high degree of accuracy</li><li> Develops knowledge of and ensures any work is conducted in line with LSEG design and content standards</li><li> Provides support to the team by co-ordinating meetings, printing materials, taking minutes and maintaining accurate action and decision logs</li></ul><p><br></p><p><strong>Qualifications &amp; Experience</strong></p><p><br></p><ul><li> Applies general knowledge of business, developed through education, to make informed judgements</li></ul><p><br></p><p>LSEG is a leading global financial markets infrastructure and data provider. Our purpose is driving financial stability, empowering economies and enabling customers to create sustainable growth.</p><p><br></p><p>Our purpose is the foundation on which our culture is built. Our values of Integrity, Partnership, Excellence and Change underpin our purpose and set the standard for everything we do, every day. They go to the heart of who we are and guide our decision making and everyday actions.</p><p><br></p><p>Working with us means that you will be part of a dynamic organisation of 25,000 people across 65 countries. However, we will value your individuality and enable you to bring your true self to work so you can help enrich our diverse workforce. You will be part of a collaborative and creative culture where we encourage new ideas and are committed to sustainability across our global business. You will experience the critical role we have in helping to re-engineer the financial ecosystem to support and drive sustainable economic growth. Together, we are aiming to achieve this growth by accelerating the just transition to net zero, enabling growth of the green economy and creating inclusive economic opportunity.</p><p><br></p><p>LSEG offers a range of tailored benefits and support, including healthcare, retirement planning, paid volunteering days and wellbeing initiatives.</p><p><br></p><p>We are proud to be an equal opportunities employer. This means that we do not discriminate on the basis of anyone’s race, religion, colour, national origin, gender, sexual orientation, gender identity, gender expression, age, marital status, veteran status, pregnancy or disability, or any other basis protected under applicable law. Conforming with applicable law, we can reasonably accommodate applicants' and employees' religious practices and beliefs, as well as mental health or physical disability needs.</p><p><br></p><p>Please take a moment to read this privacy notice carefully, as it describes what personal information London Stock Exchange Group (LSEG) (we) may hold about you, what it’s used for, and how it’s obtained, your rights and how to contact us as a data subject.</p><p><br></p><p>If you are submitting as a Recruitment Agency Partner, it is essential and your responsibility to ensure that candidates applying to LSEG are aware of this privacy notice.</p>",
        $partnerUsers[5],
        false,
    ],
    [
        "Intern - Digital Platforms and Partnerships",
        "<p>WOW is a Superapp that consists of an array of services that aim to cater to a wide range of digital lifestyle needs! With a heavy focus on personalization, the WOW Superapp will mean different things to different users, from being the go-to platform for entertainment, rewards, discounts and convenience through digital interactions &amp; transactions. The WOW Superapp will offer opportunities to engage, be rewarded, transact, and enjoy convenience in a seamless manner within a single app interface. Users will be rewarded not only for their transactions but also for their engagement with the app, through daily mission accomplishments and exciting gamifications.</p><p>We are seeking a motivated and tech-savvy Digital Marketing Intern to join our dynamic team. This internship offers a hands-on experience in digital marketing, providing exposure to various digital channels and strategies.</p><p><br></p><p><strong>The Job</strong></p><p><br></p><p><strong>Social Media Management:</strong></p><ul><li>Assist in developing and implementing social media strategies to increase brand awareness.</li><li>Create engaging content for various social media platforms.</li><li>Monitor and respond to audience interactions when needed.</li></ul><p><br></p><p><strong>Content Creation:</strong></p><ul><li>Collaborate with the content team to create creative, compelling and shareable content for digital channels.</li><li>Assist in the development of blog posts, infographics, and multimedia content.</li></ul><p><br></p><p><strong>Performance Marketing (Digital Advertising):</strong></p><ul><li>Assist in the creation and optimization of digital advertising campaigns (Google Ads, Meta ads, etc.)</li><li>Monitor and analyze the performance of digital ads.</li></ul><p><br></p><p><strong>Analytics and Reporting:</strong></p><ul><li>Collect and analyze data from various digital marketing channels.</li><li>Generate reports to measure the effectiveness of campaigns and provide actionable insights.</li></ul><p><br></p><p><strong>Website Management:</strong></p><ul><li>Assist in the maintenance and optimization of the company website.</li><li>Collaborate with the web development team on digital projects.</li></ul><p><br></p><p><strong>Search Engine Optimization (SEO):</strong></p><ul><li>Support SEO initiatives by optimizing website content and structure.</li><li>Conduct keyword research and analysis.</li></ul><p><br></p><p><br></p><p><strong>The Person</strong></p><p><br></p><ul><li>Currently pursuing a degree in Marketing, Digital Marketing, Business, or a related field.</li><li>Basic understanding of digital marketing concepts and tools.</li><li>Familiarity with social media platforms, SEO, and digital advertising.</li><li>Strong written and verbal communication skills.</li><li>Proficiency in Microsoft Office packages</li><li>Ability to work independently and as part of a team.</li></ul><p><br></p><p><strong>Benefits</strong></p><p><br></p><ul><li>Practical exposure to diverse digital marketing channels and tools.</li><li>Mentorship from experienced digital marketing professionals.</li><li>Networking opportunities within the digital marketing industry.</li></ul><p><br></p><p>If you are a digitally inclined and creative individual looking to jumpstart your career in digital marketing, we invite you to apply for our Digital Marketing Intern position.</p>",
        $partnerUsers[6],
        false,
    ],
    [
        "Intern - Software Engineering",
        "<p><strong>Location</strong>: Colombo, Sri Lanka</p><p><br></p><p>Our mission is to unlock human potential. We welcome you for who you are, the background you bring, and we embrace individuals who get excited about learning. Bring your experiences, your perspectives, and your passion; it is in our differences that we empower the way the world learns.</p><p><br></p><p><strong>How You Will Make An Impact</strong></p><p><br></p><ul><li>Hands-on experience involving software development lifecycle.</li><li>Hands-on experience in Programming industrial level applications and related processes</li><li>Participate in new feature addition discussions with the other development and business teams.</li><li>Participate and contribute to all Agile/Scrum ceremonies within the team and cross teams.</li><li>Setting up non-prod environments and gain exposure of using them correctly.</li><li>Expertise in working in a multinational organization environment.</li><li>Proactive and reactive monitoring of Application Systems and Infrastructure.</li><li>Improve communication skills and understanding the dynamics of working in a team.</li><li>Reading, understanding, and writing technical documentations.</li></ul><p><br></p><p><strong>What We Look For</strong></p><p><br></p><ul><li>Reading for a degree in Computer Science, Software Engineering, IT, or any other related discipline who can join us for a 1-year internship.</li><li>Have strong knowledge in OOP concepts and its applications.</li><li>Have knowledge in basic programming and design patterns.</li><li>Have knowledge in database concepts and SQL queries.</li><li>Have knowledge in web programming languages such as React JS, Angular JS, Node, Java will be an added advantage.</li><li>Have a basic understanding of cloud-based environments such as AWS.</li><li>Have excellent communication and critical thinking skills.</li></ul><p><br></p><p><strong>About Wiley</strong></p><p><br></p><p>Enabling Discovery, Powering Education, Shaping Workforces.</p><p><br></p><p>We clear the way for seekers of knowledge: illuminating the path forward for research and education, tearing down barriers to society’s advancement, and giving seekers the help, they need to turn their steps into strides.</p><p><br></p><p>Wiley may have been founded over two centuries ago, but our secret to success remains the same: our people. We are willing to challenge the status quo, move the needle, and be innovative. Wiley’s headquarters are located in Hoboken, New Jersey, with operations across the globe in more than 40 countries.</p><p><br></p><p>Please attach your CV in order to be considered for this position.</p><p><br></p><p><strong>Location/Division:</strong></p><p><br></p><p>Colombo, Sri Lanka</p><p><br></p><p><strong>Job Requisition</strong></p><p><br></p><p>R2302464</p><p><br></p><p><strong>Remote Location:</strong></p><p><br></p><p>No</p><p><br></p><p><strong>Time Type</strong></p><p><br></p><p>Full Time</p>",
        $partnerUsers[4],
        true,
    ],
    [
        "Software Engineer- Intern",
        "<p><strong>Description</strong>: This is a summer 2024 intern position. Seeking an energetic intern candidate who is currently enrolled in an information science, computer science or software engineering degree program.</p><p><br></p><p>Applicants selected may be subject to a government security investigation and must meet eligibility requirements for access to classified information.</p><p><br></p><p><strong>Basic Qualifications</strong></p><p><br></p><ul><li> Working towards BS degree in Computer Science, Computer Engineering, Electrical Engineering, Software Engineering, or equivalent STEM field</li><li> Academic or professional experience using Java, C++, C#, Python, or similar programming languages</li><li> Strong verbal and written communication skills</li></ul><p><br></p><p><strong>Desired Skills</strong></p><p><br></p><ul><li> Fundamental knowledge of Software Development Methodologies</li><li> Fundamental knowledge of software development tools</li><li> Fundamental knowledge of computer operating system (Windows, Unix/Linux, Shell Scripting)</li><li> Ability to work with cross-functional teams (Hardware, Systems Engineering, Integration &amp; Test, and other disciplines)</li><li> Minimum 3.0 GPA</li><li><br></li></ul><p><strong>Security Clearance Statement</strong>: This position requires a government security clearance, you must be a US Citizen for consideration.</p><p><br></p><p><strong>Clearance Level</strong>: Secret</p><p><br></p><p>Other Important Information You Should Know</p><p><br></p><p><strong>Expression of Interest</strong>: By applying to this job, you are expressing interest in this position and could be considered for other career opportunities where similar skills and requirements have been identified as a match. Should this match be identified you may be contacted for this and future openings.</p><p><br></p><p><strong>Ability to Work Remotely</strong>: Part-time Remote Telework: The employee selected for this position will work part of their work schedule remotely and part of their work schedule at a designated Lockheed Martin facility. The specific weekly schedule will be discussed during the hiring process.</p><p><br></p><p><strong>Work Schedules</strong>: Lockheed Martin supports a variety of alternate work schedules that provide additional flexibility to our employees. Schedules range from standard 40 hours over a five day work week while others may be condensed. These condensed schedules provide employees with additional time away from the office and are in addition to our Paid Time off benefits.</p><p><br></p><p><strong>Schedule for this Position</strong>: 4x10 hour day, 3 days off per week</p><p><br></p><p><strong>Pay Rate</strong></p><p><br></p><p>The annual base salary range for this position in California and New York (excluding most major metropolitan areas), Colorado, or Washington is $20,904 - $67,288. Please note that the salary information is a general guideline only. Lockheed Martin considers factors such as (but not limited to) scope and responsibilities of the position, candidate's work experience, education/ training, key skills as well as market and business considerations when extending an offer.</p><p><br></p><p>Benefits offered: Medical, Dental, Vision, Life Insurance, Short-Term Disability, Long-Term Disability, 401(k) match, Flexible Spending Accounts, EAP, Education Assistance, Parental Leave, Paid time off, and Holidays.</p><p><br></p><p>(Washington state applicants only) Non-represented full time employees: accrue 10 hours per month of Paid Time Off (PTO); receive 40 hours of Granted PTO annually for incidental absences; receive at least 90 hours for holidays. Represented full time employees accrue 6.67 hours of PTO per month; accrue up to 52 hours of sick leave annually; receive at least 96 hours for holidays. PTO is prorated based on hours worked and start date during the calendar year.</p><p><br></p><p>Lockheed Martin is an Equal Opportunity/Affirmative Action Employer. All qualified applicants will receive consideration for employment without regard to race, color, religion, sex, pregnancy, sexual orientation, gender identity, national origin, age, protected veteran status, or disability status.</p><p><br></p><p>The application window will close in 90 days; applicants are encouraged to apply within 5 - 30 days of the requisition posting date in order to receive optimal consideration.</p><p><br></p><p>Join us at Lockheed Martin, where your mission is ours. Our customers tackle the hardest missions. Those that demand extraordinary amounts of courage, resilience and precision. They’re dangerous. Critical. Sometimes they even provide an opportunity to change the world and save lives. Those are the missions we care about.</p><p><br></p><p>As a leading technology innovation company, Lockheed Martin’s vast team works with partners around the world to bring proven performance to our customers’ toughest challenges. Lockheed Martin has employees based in many states throughout the U.S., and Internationally, with business locations in many nations and territories.</p><p><br></p><p><strong>Experience Level</strong>: Co-op/Summer Intern</p><p><br></p><p><strong>Business Unit</strong>: RMS</p><p><br></p><p><strong>Relocation Available</strong>: Possible</p><p><br></p><p><strong>Career Area</strong>: Software Engineering</p><p><br></p><p><strong>Type</strong>: Part-Time</p><p><br></p><p><strong>Shift</strong>: First</p>",
        $partnerUsers[7],
        true,
    ],
    [
        "Systems Engineering- Intern",
        "<p><strong>Description</strong>: This position is a Systems Engineering intern to support the C2BMC-Global program.</p><p><br></p><p><strong>Basic Qualifications</strong></p><p><br></p><p>College course exposure to Systems Engineering concepts and skills such as requirements engineering, use case development, logical architecture modeling, model-based systems engineering and interface definition.</p><p><br></p><p>College course work in common programming languages including Java, C++, Python, data base management, networking or data communications is a plus. Experience with Microsoft Excel including development of Pivot Tables and Excel Macros.</p><p><br></p><p>Must be a US Citizen; this position will require a government security clearance. This position is located at a facility that requires special access.</p><p><br></p><p><strong>Desired Skills</strong></p><p><br></p><p>Experience with performing systems engineering for DoD Information Systems is desirable. Formal Systems Engineering education is a significant plus. Experience with Model-Based Systems Engineering Tools is desired.</p><p><br></p><p>Security Clearance Statement: This position requires a government security clearance, you must be a US Citizen for consideration.</p><p><br></p><p><strong>Clearance Level</strong>: Top Secret</p><p><br></p><p><strong>Other Important Information You Should Know</strong></p><p><br></p><p><strong>Expression of Interest</strong>: By applying to this job, you are expressing interest in this position and could be considered for other career opportunities where similar skills and requirements have been identified as a match. Should this match be identified you may be contacted for this and future openings.</p><p><br></p><p><strong>Ability to Work Remotely</strong>: Onsite Full-time: The work associated with this position will be performed onsite at a designated Lockheed Martin facility.</p><p><br></p><p><strong>Work Schedules</strong>: Lockheed Martin supports a variety of alternate work schedules that provide additional flexibility to our employees. Schedules range from standard 40 hours over a five day work week while others may be condensed. These condensed schedules provide employees with additional time away from the office and are in addition to our Paid Time off benefits.</p><p><br></p><p><strong>Schedule for this Position</strong>: 4x10 hour day, 3 days off per week</p><p><br></p><p><strong>Pay Rate</strong></p><p><br></p><p>The annual base salary range for this position in California and New York (excluding most major metropolitan areas), Colorado, or Washington is $20,904 - $67,288. Please note that the salary information is a general guideline only. Lockheed Martin considers factors such as (but not limited to) scope and responsibilities of the position, candidate's work experience, education/ training, key skills as well as market and business considerations when extending an offer.</p><p><br></p><p>Benefits offered: Medical, Dental, Vision, Life Insurance, Short-Term Disability, Long-Term Disability, 401(k) match, Flexible Spending Accounts, EAP, Education Assistance, Parental Leave, Paid time off, and Holidays.</p><p><br></p><p>(Washington state applicants only) Non-represented full time employees: accrue 10 hours per month of Paid Time Off (PTO); receive 40 hours of Granted PTO annually for incidental absences; receive at least 90 hours for holidays. Represented full time employees accrue 6.67 hours of PTO per month; accrue up to 52 hours of sick leave annually; receive at least 96 hours for holidays. PTO is prorated based on hours worked and start date during the calendar year.</p><p><br></p><p>Lockheed Martin is an Equal Opportunity/Affirmative Action Employer. All qualified applicants will receive consideration for employment without regard to race, color, religion, sex, pregnancy, sexual orientation, gender identity, national origin, age, protected veteran status, or disability status.</p><p><br></p><p>The application window will close in 90 days; applicants are encouraged to apply within 5 - 30 days of the requisition posting date in order to receive optimal consideration.</p><p><br></p><p>At Lockheed Martin, we use our passion for purposeful innovation to help keep people safe and solve the world's most complex challenges. Our people are some of the greatest minds in the industry and truly make Lockheed Martin a great place to work.</p><p><br></p><p>With our employees as our priority, we provide diverse career opportunities designed to propel, develop, and boost agility. Our flexible schedules, competitive pay, and comprehensive benefits enable our employees to live a healthy, fulfilling life at and outside of work. We place an emphasis on empowering our employees by fostering an inclusive environment built upon integrity and corporate responsibility.</p><p><br></p><p>If this sounds like a culture you connect with, you’re invited to apply for this role. Or, if you are unsure whether your experience aligns with the requirements of this position, we encourage you to search on Lockheed Martin Jobs, and apply for roles that align with your qualifications.</p><p><br></p><p><strong>Experience Level</strong>: Co-op/Summer Intern</p><p><br></p><p><strong>Business Unit</strong>: RMS</p><p><br></p><p><strong>Relocation Available</strong>: Possible</p><p><br></p><p><strong>Career Area</strong>: Software Engineering</p><p><br></p><p><strong>Type</strong>: Part-Time</p><p><br></p><p><strong>Shift</strong>: First</p>",
        $partnerUsers[7],
        false,
    ]
];

$internshipCycle = new InternshipCycle();
$internshipCycle->addPartnerGroup($groupCyclePartnerAdmins);
$internshipCycle->addPartnerGroup($groupCyclePartners);
$internshipCycle->setStudentGroup($groupCycleStudents);

$internshipCycle->setJobCollectionStart(new DateTimeImmutable());

$internships = [];

$counter = 0;

$i = new Internship(
    $internData[0][0],
    $internData[0][1],
    \App\Models\Internship\Status::Public ,
    $internshipCycle,
    $user4,
    $user4->getOrganization(),
    $internData[0][3],
);
$entityManager->persist($i);

for ($x = 1; $x < count($internData); $x++) {
    $data = $internData[$x];
    $i = new Internship(
        $data[0],
        $data[1],
        \App\Models\Internship\Status::Draft,
        $internshipCycle,
        $data[2],
        $data[2]->getOrganization(),
        $data[3],
        $data[3] ? 'https://www.google.com/' : null,
    );
    $entityManager->persist($i);
    $internships[] = $i;
}

$entityManager->persist($internshipCycle);
$entityManager->flush();

#endregion

#region Events

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

foreach ($eventData as $eventData) {
    $event = new Event($eventData[0], $eventData[1], $eventData[3], $eventData[4], $eventData[2], $eventData[5]);
    $entityManager->persist($event);
}
$entityManager->flush();

#endregion

#region Requirements

echo "Done.\nAdding requirements...";

$requirementData = [
    new CreateRequirementDTO(
        'Internship Contract',
        'Upload the contract between you and the company.',
        Type::ONE_TIME,
        new DateTimeImmutable(),
        new DateTimeImmutable('+1 month'),
        null,
        FulFillMethod::FILE_UPLOAD,
        ['pdf'],
        5,
        3
    ),
    new CreateRequirementDTO(
        'Monthly Report',
        'Upload a report of your progress.',
        Type::RECURRING,
        new DateTimeImmutable(),
        null,
        RepeatInterval::MONTHLY,
        FulFillMethod::FILE_UPLOAD,
        ['pdf'],
        5,
        1
    ),
    new CreateRequirementDTO(
        'Weekly Report',
        'Upload a report of your progress.',
        Type::RECURRING,
        new DateTimeImmutable(),
        null,
        RepeatInterval::WEEKLY,
        FulFillMethod::FILE_UPLOAD,
        ['pdf'],
        5,
        1
    ),
    new CreateRequirementDTO(
        'Your feedback about the internship',
        'What do you think about the company. How was your experience? Your feedback will not be shared with the company.',
        Type::ONE_TIME,
        new DateTimeImmutable(),
        new DateTimeImmutable('+1 month'),
        null,
        FulFillMethod::TEXT_INPUT,
        [],
        null,
        null
    )
];

$requirements = [];

foreach ($requirementData as $requirement) {
    $r = new Requirement($requirement);
    $r->setInternshipCycle($internshipCycle);
    $entityManager->persist($r);
    $requirements[] = $r;
}
$entityManager->flush();

#endregion

#region User Requirements

echo "Done.\nAdding user requirements...";

$ur = new UserRequirement(
    $user3,
    $requirements[0],
    $requirements[0]->getStartDate(),
    $requirements[0]->getEndBeforeDate()
);
$entityManager->persist($ur);
$ur = new UserRequirement(
    $user3,
    $requirements[3],
    $requirements[3]->getStartDate(),
    $requirements[3]->getEndBeforeDate()
);
$entityManager->persist($ur);

$requirements[1]->createUserRequirements($user3, $entityManager);
$requirements[2]->createUserRequirements($user3, $entityManager);

$entityManager->flush();

foreach ($requirements as $r) {
    for ($i = 1; $i < 200; $i++) {
        $r->createUserRequirements($studentUsers[$i], $entityManager);
    }
}
$entityManager->flush();

echo "Done.\nDatabase seeded successfully.\n";

#endregion