<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

require __DIR__ . '/CurlClient.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: *');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(204);
  exit;
}

/**
 * -------------------------------------------------
 * Load config.json (host-based headers)
 * -------------------------------------------------
 */
$configPath = __DIR__ . '/config.json';
if (!file_exists($configPath)) {
  http_response_code(500);
  echo json_encode(['error' => 'Missing config.json']);
  exit;
}

$config = json_decode(file_get_contents($configPath), true);

/**
 * -------------------------------------------------
 * Routing
 * -------------------------------------------------
 */
$endpoint = $_GET['endpoint'] ?? null;

/**
 * -------------------------------------------------
 * CONFIGS FETCH (frontend discovery only)
 * -------------------------------------------------
 */
if ($endpoint === 'configs-fetch') {
  $mappingPath = __DIR__ . '/mapping-config.json';

  if (!file_exists($mappingPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Missing mapping-config.json']);
    exit;
  }

  echo file_get_contents($mappingPath);
  exit;
}

/**
 * -------------------------------------------------
 * TARGET FETCH
 * -------------------------------------------------
 */
if ($endpoint !== 'target-fetch') {
  http_response_code(404);
  echo json_encode(['error' => 'Unknown endpoint']);
  exit;
}

$entity = $_GET['entity'] ?? null;
$id = $_GET['id'] ?? null;

if (!$entity || !$id) {
  http_response_code(400);
  echo json_encode(['error' => 'Missing entity or id']);
  exit;
}

/**
 * -------------------------------------------------
 * Build Airtable URL (hard-coded target for now)
 * Later this comes from mapping-config / config
 * -------------------------------------------------
 */
$baseUrl = 'https://api.airtable.com/v0';
$baseId = 'BASE_ID_HERE';
$table = $entity;

$url = $baseUrl . '/'
  . rawurlencode($baseId) . '/'
  . rawurlencode($table) . '/'
  . rawurlencode($id);

/**
 * -------------------------------------------------
 * Host-based auth injection (YOUR MODEL)
 * -------------------------------------------------
 */
$host = parse_url($url, PHP_URL_HOST);

if (!isset($config[$host])) {
  http_response_code(500);
  echo json_encode(['error' => 'No config for host: ' . $host]);
  exit;
}

$requestHeaders = [];

if (isset($config[$host]['headers'])) {
  foreach ($config[$host]['headers'] as $key => $value) {
    $requestHeaders[] = $key . ': ' . $value;
  }
}

/**
 * -------------------------------------------------
 * Fetch
 * -------------------------------------------------
 */
$client = new CurlClient(false);
$info = $client->get($url, $requestHeaders);

if (!$info || ($info['http_code'] ?? 500) >= 400) {
  http_response_code(502);
  echo json_encode([
    'error' => 'Failed to fetch target record',
    'http_code' => $info['http_code'] ?? null
  ], JSON_PRETTY_PRINT);
  exit;
}

/**
 * -------------------------------------------------
 * TEMP BODY FETCH (CurlClient limitation)
 * -------------------------------------------------
 */
$response = file_get_contents($url, false, stream_context_create([
  'http' => [
    'header' => implode("\r\n", $requestHeaders)
  ]
]));

echo json_encode([
  'system' => 'target',
  'entity' => $entity,
  'id' => $id,
  'data' => json_decode($response, true)
], JSON_PRETTY_PRINT);
