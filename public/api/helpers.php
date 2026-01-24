<?php

function readConfig($url)
{
  $configFile = __DIR__ . '/config.json';

  if (!file_exists($configFile)) {
    return null;
  }

  $configs = json_decode(file_get_contents($configFile), true);
  $host = parse_url($url, PHP_URL_HOST);

  return isset($configs[$host]) ? $configs[$host] : null;
}



/**
 * -------------------------------------------------
 * Path Resolver (structural only)
 * -------------------------------------------------
 */

function resolvePath($data, $path)
{
  if ($path === null || $path === '')
    return null;

  // Split on dots, but keep bracket tokens
  preg_match_all("/([A-Za-z0-9_-]+|\['[^']+'\]|\[x\])/", $path, $matches);

  $tokens = $matches[0];

  return walkTokens($data, $tokens);
}

function walkTokens($current, $tokens)
{
  if ($current === null)
    return null;
  if (empty($tokens))
    return $current;

  $token = array_shift($tokens);

  // Array mapper
  if ($token === '[x]') {
    if (!is_array($current))
      return [];
    $out = [];
    foreach ($current as $item) {
      $val = walkTokens($item, $tokens);
      if ($val !== null)
        $out[] = $val;
    }
    return $out;
  }

  // Literal key ['Some Key']
  if (preg_match("/^\['(.+)'\]$/", $token, $m)) {
    return walkTokens($current[$m[1]] ?? null, $tokens);
  }

  // Normal object key
  return walkTokens($current[$token] ?? null, $tokens);
}

/**
 * -------------------------------------------------
 * Structural Normalization
 * -------------------------------------------------
 */

function normalizeStructure($rawData, $entityMap, $system)
{
  $norm = [];

  foreach ($entityMap['fields'] as $field) {
    $pathKey = $system === 'source' ? 'source_path' : 'target_path';
    if (!isset($field[$pathKey]))
      continue;

    $value = resolvePath($rawData, $field[$pathKey]);

    // Enforce empty array for [x] paths
    if (str_contains($field[$pathKey], '[x]') && $value === null) {
      $value = [];
    }

    $norm[$field['norm_name']] = $value;
  }

  return $norm;
}
