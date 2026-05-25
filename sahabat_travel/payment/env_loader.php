<?php
function loadEnv($filePath)
{
  if (!file_exists($filePath)) {
    die(".env file missing. Please create one based on instructions.");
  }

  $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
    // Skip comment lines
    if (strpos(trim($line), '#') === 0) {
      continue;
    }

    // Split by the first '=' found
    list($name, $value) = explode('=', $line, 2);
    $name = trim($name);
    $value = trim($value);

    if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
      putenv(sprintf('%s=%s', $name, $value));
      $_ENV[$name] = $value;
      $_SERVER[$name] = $value;
    }
  }
}

// Automatically execute loader looking at the current directory
loadEnv(__DIR__ . '/.env');
