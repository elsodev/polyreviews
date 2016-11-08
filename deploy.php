<?php
/*
 * This file has been generated automatically.
 * Please change the configuration for correct use deploy.
 */
require 'recipe/laravel.php';

server('pr_prod', '128.199.125.135', 2228)
    ->user('root')
    ->forwardAgent() // You can use identity key, ssh config, or username/password to auth on the server.
    ->stage('production')
    ->env('deploy_path', '/usr/share/nginx/polyreviews/'); // Define the base path to deploy your project to.

set('repository', 'git@bitbucket.org:cwxorochi/polyreviews.git');

// Laravel shared dirs, removed it since im using file as cache and sessions.
// added thumb, because if its not shared, every deploy will overwrite users uploaded thumb
//  thumb includes, groups and user thumbs
set('shared_dirs', ['public/css', 'public/img', 'public/js', 'public/semantic-ui']);

// for uploads
set('writable_dirs', ['bootstrap/cache', 'storage', 'public']);

task('set_permissions', function () {
    run('chown -R www-data:www-data {{deploy_path}}/current/storage');
    run('chmod -R 775 {{deploy_path}}/current/storage');
    run('chown -R www-data:www-data {{deploy_path}}/current/bootstrap/cache');
    run('chmod -R 775 {{deploy_path}}/current/bootstrap/cache');
    run('chown -R www-data:www-data {{deploy_path}}/shared/');
    run('chmod -R 775 {{deploy_path}}/shared/');
    run('chmod -R 655 {{deploy_path}}/shared/.env');
})->desc('Changing ownership');

/**
 * Restart php-fpm on success deploy.
 */
task('php-fpm:restart', function () {
    // Attention: The user must have rights for restart service
    // Attention: the command "sudo /bin/systemctl restart php-fpm.service" used only on CentOS system
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart php-fpm.service
    run('sudo /bin/systemctl restart php-fpm.service');
})->desc('Restart PHP-FPM service');


task('deploy', [
    'deploy:prepare',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:symlink',
    'set_permissions',
    'deploy:vendors',
    'cleanup',
])->desc('Deploy Polyreviews');
after('deploy', 'success');

after('success', 'php-fpm:restart');

