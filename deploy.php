<?php

namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'git@github.com:rugsrme/stoicsobrietyV2.git');
set('http_user', 'quinni5');
set('writable_mode', 'chmod');
add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('quinnix.com')
    ->setPort(2222)
    ->set('remote_user', 'quinni5')
    ->set('deploy_path', '~/stoicrecovery')
    ->setIdentityFile('/home/quinn/.ssh/quinnix_deploy');

// Tasks

task('artisan:db:seed:books', artisan('db:seed --class=BookSeeder --force', ['skipIfNoEnv', 'showOutput']));

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'artisan:storage:link',
    'artisan:optimize',
    'artisan:migrate',
    'artisan:db:seed:books',
    'deploy:publish',
    'artisan:reload',
]);

// Hooks

after('deploy:failed', 'deploy:unlock');
