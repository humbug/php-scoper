<?php

$e2eTests = [
    'e2e_004',
    'e2e_005',
    'e2e_011',
    'e2e_013',
    'e2e_014',
    'e2e_015',
    'e2e_016',
    'e2e_017',
    'e2e_018',
    'e2e_019',
    'e2e_020',
    'e2e_0210',
    'e2e_0211',
    'e2e_022',
    'e2e_023',
    'e2e_024',
    'e2e_025',
    'e2e_026',
    'e2e_027',
    'e2e_028',
    'e2e_029',
    'e2e_030',
    'e2e_031',
    'e2e_032',
];

$e2ePhpVersions = [
    'e2e_0210' => ['7.3'],
];

$matrix = [];
foreach ($e2eTests as $e2e) {
    foreach ($e2ePhpVersions[$e2e] ?? ['7.3', '8.0'] as $phpVersion) {
        $matrix[] = [
            'e2e' => $e2e,
            'php' => $phpVersion,
        ];
    }
}

echo json_encode($matrix);
