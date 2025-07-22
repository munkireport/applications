<?php

return [
  'apps_bundlepath_ignorelist' => env('APPSBUNDLEPATH_IGNORELIST', [
        '/System/Library/.*',
        '/System/Applications/.*',
        '.*/Library/AutoPkg.*',
        '/.DocumentRevisions-V100/.*',
        '/Library/Application Support/Adobe/Uninstall/.*',
        '.*/Library/Application Support/Google/Chrome/Default/Web Applications/.*',
        '.*/Library/Application Support/Firefox/Profiles/.*.default/storage/default/.*',
  ]),
];
