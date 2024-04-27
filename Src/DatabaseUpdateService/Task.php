<?php
declare(strict_types=1);

use DatabaseUpdateService\InternMapper;
use DatabaseUpdateService\RequirementMapper;
use DatabaseUpdateService\UserRequirement;

require_once __DIR__ . '/vendor/autoload.php';

$dbConfig = require_once 'DatabaseConfig.php';

$pdo = new PDO(
    $dbConfig['driver'] . ':host=' . $dbConfig['host'] . ';dbname=' . $dbConfig['database'],
    $dbConfig['username'],
    $dbConfig['password']
);

$sql = "SELECT id
        FROM internship_cycles
        WHERE endedAt IS NULL";

$statement = $pdo->query($sql, PDO::FETCH_ASSOC);

$cycleId = $statement->fetch()['id'];

$sql = 'SELECT id, startWeek, durationWeeks, fulfillMethod, internship_cycle_id
        FROM requirements
        WHERE internship_cycle_id = :cycleId';

$statement = $pdo->prepare($sql);
$statement->execute(['cycleId' => $cycleId]);

$requirements = [];

while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    $requirements[] = RequirementMapper::map($row);
}

$sql = 'SELECT id, student_id, createdAt, internship_cycle_id
        FROM interns
        WHERE internship_cycle_id = :cycleId';

$statement = $pdo->prepare($sql);
$statement->execute(['cycleId' => $cycleId]);

$interns = [];

while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    $interns[] = InternMapper::map($row);
}

$now = new DateTimeImmutable();

foreach ($interns as $intern) {
    foreach ($requirements as $requirement) {

        $internStartedAt = $intern->createdAt;
        $currentWeek = $now->diff($internStartedAt);

        if ($currentWeek->d < 6) {
            continue;
        }

        if ($currentWeek->d < $requirement->startWeek) {
            $sql = 'SELECT * FROM user_requirements
                    WHERE user_id = :userId AND requirement_id = :requirementId';

            $statement = $pdo->prepare($sql);
            $statement->execute([
                'userId' => $intern->studentId,
                'requirementId' => $requirement->id
            ]);

            if ($statement->fetch()) {
                continue;
            }

            // Assume this script runs at 5:00 PM every day

            $startDate = $now->modify('tomorrow midnight');
            $endDate = $startDate->add($requirement->durationWeeks);

            $sql = "INSERT INTO user_requirements (
                user_id,
                requirement_id,
                status,
                fulfillMethod,
                startDate,
                endDate
            )
            VALUES(:userId, :reqId, :status, :fulfillMethod, :startDate, :endDate)";

            $statement = $pdo->prepare($sql);
            $statement->execute([
                'userId' => $intern->studentId,
                'reqId' => $requirement->id,
                'status' => UserRequirement::STATUS_PENDING,
                'fulfillMethod' => $requirement->fulfillMethod,
                'startDate' => $startDate->format('Y-m-d H:i:s'),
                'endDate' => $endDay->format('Y-m-d H:i:s')
            ]);
        }
    }
}