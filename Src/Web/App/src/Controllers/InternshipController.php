<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\RequiredRole;
use App\DTOs\InternshipDTO;
use App\Interfaces\IInternshipService;
use App\Interfaces\IUserService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[RequiredRole([
    "ROLE_ADMIN",
    "ROLE_INTERNSHIP_PARTNER",
    "ROLE_INTERNSHIP_STUDENT"
])]
#[Route("/internship-program/internships")]
class InternshipController extends PageControllerBase
{
    public function __construct(
        Environment $twig,
        private IInternshipService $internshipService,
        private IUserService $userService,
    ) {
        parent::__construct($twig);
    }

    #[Route(["", "/"])]
    public function internships(Request $request): Response
    {
        $userId = $request->getSession()->get("user_id");
        $latestInternshipCycleId = $request->getSession()->get("latest_internship_cycle_id");

        $queryParams = $request->query->all();

        $searchQuery = $queryParams["q"] ?? null;

        $internships = [];

        $companies = [
            "Apple Inc.",
            "Microsoft Corporation",
            "Alphabet Inc. (Google)",
            "Samsung Electronics",
            "Amazon.com, Inc.",
            "Huawei Technologies Co., Ltd.",
            "Sony Corporation",
            "Intel Corporation",
            "Facebook, Inc. (Meta Platforms)",
            "IBM Corporation",
            "Tencent Holdings Ltd.",
            "Dell Technologies Inc.",
            "Oracle Corporation",
            "Cisco Systems, Inc.",
            "Xiaomi Corporation",
            "SAP SE",
            "Qualcomm Incorporated",
            "Adobe Inc.",
            "Nokia Corporation",
            "Ericsson",
            "Panasonic Corporation",
            "Hitachi, Ltd.",
            "LG Electronics Inc.",
            "Lenovo Group Ltd.",
            "ASUS (ASUSTeK Computer Inc.)",
            "Broadcom Inc.",
            "Salesforce.com, Inc.",
            "Toshiba Corporation",
            "Hewlett Packard Enterprise (HPE)",
            "VMware, Inc.",
            "Twitter, Inc.",
            "AMD (Advanced Micro Devices, Inc.)",
            "Philips (Koninklijke Philips N.V.)",
            "Texas Instruments Incorporated",
            "NEC Corporation",
            "Sharp Corporation",
            "Fujitsu Ltd.",
            "Square, Inc. (Block, Inc.)",
            "Spotify Technology S.A.",
            "Symantec Corporation",
            "Weibo Corporation",
            "Baidu, Inc.",
            "Zoom Video Communications, Inc.",
            "NetApp, Inc.",
            "Micron Technology, Inc.",
            "Western Digital Corporation",
            "Electronic Arts Inc. (EA)",
            "Autodesk, Inc.",
            "Rakuten, Inc.",
            "McAfee Corp.",
        ];

        if ($this->userService->hasRole($userId, "ROLE_PARTNER")) {
            $internships = $this->internshipService
                ->getInternshipsBy($latestInternshipCycleId, $userId, $searchQuery);
        } else {
            $internships = $this->internshipService
                ->getInternshipsBy($latestInternshipCycleId, null, $searchQuery);
        }

        return $this->render(
            "internship-program/internships.html",
            array_merge(
                ["section" => "internships"],
                ["internships" => $internships],
                ["companies" => $companies]
            )
        );
    }

    #[Route("/{id}", methods: ["GET"], requirements: ['id' => '\d+'])]
    public function internship(int $id): Response|RedirectResponse
    {
        $internship = $this->internshipService->getInternshipById($id);
        if ($internship) {
            $data = [
                "title" => $internship->getTitle(),
                "description" => $internship->getDescription(),
            ];
            return new Response(json_encode($data), 200, ["Content-Type" => "application/json"]);
        }
        return $this->redirect("/internship-program");
    }

    #[Route("/{id}", methods: ["DELETE"], requirements: ['id' => '\d+'])]
    public function delete(Request $request, int $id): Response
    {
        $this->internshipService->deleteInternshipById($id);
        return new Response(null, 204);
    }

    #[Route("/{id}/applicants")]
    public function internshipApplicants(int $id): Response
    {
        return $this->render("internship-program/internship/applicants.html", [
            "section" => "internships",
            "applicants" => [
                "Ashen",
                "Smith",
                "James",
                "Green",
                "Head",
                "Jimmy",
            ]
        ]);
    }

    #[Route("/{id}/modify", methods: ["GET"])]
    public function editGET(int $id): Response
    {
        return $this->render("internship-program/internship/modify.html", [
            "section" => "internships",
            "internship" => $this->internshipService->getInternshipById($id)
        ]);
    }

    #[Route("/{id}/modify", methods: ["POST"])]
    public function editPOST(Request $request): RedirectResponse
    {
        $this->internshipService->updateInternship(
            (int) $request->get("id"),
            $request->get("title"),
            $request->get("description")
        );
        return $this->redirect("/internship-program/internships");
    }

    #[Route("/create", methods: ["GET"])]
    public function addGET(): Response
    {
        return $this->render("internship-program/internship/create.html", ["section" => "internships"]);
    }

    #[Route("/create", methods: ["POST"])]
    public function addPOST(Request $request): RedirectResponse
    {
        $internshipDTO = new InternshipDTO(
            $request->get("title"),
            $request->get("description"),
            (int) $request->getSession()->get("user_id"),
        );
        // TODO: Validate DTO

        $this->internshipService->addInternship($internshipDTO);
        return $this->redirect("/internship-program/internships");
    }

    #[Route("/{id}/apply")]
    public function apply(Request $request, int $id): Response
    {
        $this->internshipService->applyToInternship($id, (int) $request->getSession()->get("user_id"));
        return $this->redirect("/internship-program/internships");
    }
}