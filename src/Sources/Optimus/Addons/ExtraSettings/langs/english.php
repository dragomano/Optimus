<?php

return [
	'optimus_use_only_cookies' => 'Use cookies to store the session id on the client side',
	'optimus_use_only_cookies_help' => 'Enabling the setting <a href="https://www.php.net/manual/en/session.configuration.php#ini.session.use-only-cookies" target="_blank" rel="noopener" class="bbc_link">session.use_only_cookies</a> prevents attacks involved passing session ids in URLs.<br>In addition, you will be able to container rid of the session ids in the canonical addresses of the forum pages. Defaults to 1 (enabled).',
	'optimus_remove_index_php' => 'Remove "index.php" from the forum urls',
	'optimus_remove_index_php_help' => 'This option does not work if you have the SimpleSEF mod, the PrettyURLs mod, or the "Search engine friendly URLs" option enabled.',
	'optimus_extend_h1' => 'Add a page title to the <strong>H1</strong> tag',
];
