<?php
// Verhindern, dass die Datei direkt aufgerufen wird
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Daten speichern
    $dataDir = __DIR__ . '/data/';
    $today = date('Y-m-d');
    $filename = $dataDir . "fho_{$today}.json";

    $data = file_get_contents('php://input');
    if ($data) {
        if (!file_exists($dataDir)) {
            mkdir($dataDir, 0777, true);
        }
        file_put_contents($filename, $data);
        echo json_encode(['status' => 'success', 'message' => 'Daten gespeichert.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Keine Daten empfangen.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Daten laden
    $dataDir = __DIR__ . '/data/';
    $today = date('Y-m-d');
    $filename = $dataDir . "fho_{$today}.json";

    if (file_exists($filename)) {
        // Lade und gebe die gespeicherten Daten zurück
        $data = file_get_contents($filename);
        echo $data;
    } else {
        // Keine gespeicherten Daten vorhanden
        echo json_encode([]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Nur POST- und GET-Anfragen erlaubt.']);
}
