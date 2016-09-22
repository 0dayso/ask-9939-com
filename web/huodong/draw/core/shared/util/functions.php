<?php
function setUri() {
	if (env('HTTP_X_REWRITE_URL')) {
		$uri = env('HTTP_X_REWRITE_URL');
	} elseif(env('REQUEST_URI')) {
		$uri = env('REQUEST_URI');
	} else {
		if (env('argv')) {
			$uri = env('argv');

			if (defined('SERVER_IIS')) {
				$uri = BASE_URL . $uri[0];
			} else {
				$uri = env('PHP_SELF') . '/' . $uri[0];
			}
		} else {
			$uri = env('PHP_SELF') . '/' . env('QUERY_STRING');
		}
	}
	return $uri;
}

function env($key) {
	if (isset($_SERVER[$key])) {
		return $_SERVER[$key];
	} elseif (isset($_ENV[$key])) {
		return $_ENV[$key];
	} elseif (getenv($key) !== false) {
		return getenv($key);
	}

	if ($key == 'DOCUMENT_ROOT') {
		$offset=0;
		if (!strpos(env('SCRIPT_NAME'), '.php')) {
			$offset = 4;
		}
		return substr(env('SCRIPT_FILENAME'), 0, strlen(env('SCRIPT_FILENAME')) - (strlen(env('SCRIPT_NAME')) + $offset));
	}
	if ($key == 'PHP_SELF') {
		return r(env('DOCUMENT_ROOT'), '', env('SCRIPT_FILENAME'));
	}
	return null;
}
?>