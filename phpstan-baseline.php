<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method Jerowork\\\\FileClassReflector\\\\ClassReflector\\:\\:getClasses\\(\\) return type with generic class ReflectionClass does not specify its types\\: T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/ClassReflector.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method Jerowork\\\\FileClassReflector\\\\NikicParser\\\\NikicParserClassReflector\\:\\:getClasses\\(\\) return type with generic class ReflectionClass does not specify its types\\: T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/NikicParser/NikicParserClassReflector.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property Jerowork\\\\FileClassReflector\\\\NikicParser\\\\NikicParserClassReflector\\:\\:\\$classes with generic class ReflectionClass does not specify its types\\: T$#',
	'count' => 1,
	'path' => __DIR__ . '/src/NikicParser/NikicParserClassReflector.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
