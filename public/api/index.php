<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

require __DIR__ . '/CurlClient.php';
require __DIR__ . '/OAuthClient.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: *');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(204);
  exit;
}

$configPath = __DIR__ . '/config.json';
if (!file_exists($configPath)) {
  http_response_code(500);
  echo json_encode(['error' => 'Missing config.json']);
  exit;
}

$config = json_decode(file_get_contents($configPath), true);

$endpoint = $_GET['endpoint'] ?? null;
$entity = $_GET['entity'] ?? null;
$id = $_GET['id'] ?? null;

if ($endpoint !== 'target-fetch') {
  http_response_code(404);
  echo json_encode(['error' => 'Only target-fetch is supported']);
  exit;
}

if (!$entity || !$id) {
  http_response_code(400);
  echo json_encode(['error' => 'Missing entity or id']);
  exit;
}

if (
  !isset($config['entities'][$entity]) ||
  !isset($config['entities'][$entity]['target'])
) {
  http_response_code(404);
  echo json_encode(['error' => 'Unknown target entity']);
  exit;
}

$target = $config['entities'][$entity]['target'];
$system = $config['systems'][$target['system']];

$url =
  rtrim($system['base_url'], '/') . '/' .
  $target['base_id'] . '/' .
  rawurlencode($target['table']) . '/' .
  rawurlencode($id);

$client = new OAuthClient(false);

$headers = [
  'Authorization: Bearer ' . $system['access_token']
];

$info = $client->get($url, $headers);

if (!$info || ($info['http_code'] ?? 500) >= 400) {
  http_response_code(502);
  echo json_encode([
    'error' => 'Failed to fetch target record',
    'http_code' => $info['http_code'] ?? null
  ], JSON_PRETTY_PRINT);
  exit;
}

/**
 * IMPORTANT:
 * CurlClient currently writes body to temp file and does not return it.
 * For now, re-fetch body directly.
 */
$response = file_get_contents($url, false, stream_context_create([
  'http' => [
    'header' => implode("\r\n", $headers)
  ]
]));

echo json_encode([
  'system' => 'target',
  'entity' => $entity,
  'id' => $id,
  'data' => json_decode($response, true)
], JSON_PRETTY_PRINT);
