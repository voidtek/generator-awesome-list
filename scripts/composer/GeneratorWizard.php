<?php

declare(strict_types = 1);

namespace GeneratorAwesomeList\composer;

use Composer\Json\JsonFile;
use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Setup wizard to handle user input during initial composer installation.
 */
class GeneratorWizard {

  /**
   * The setup wizard.
   *
   * @param \Composer\Script\Event $event
   *   The Composer event that triggered the wizard.
   *
   * @return bool
   *   TRUE on success.
   *
   * @throws \Exception
   *   Thrown when an error occurs during the setup.
   */
  public static function run(Event $event): bool {
    $params = [];

    // Ask for the project name, and suggest the various machine names.
    $params['project_name'] = 'Stuff';
    $params['project_name'] = $event->getIO()->ask('<info>The name of your awesome list</info> [<comment>' . $params['project_name'] . '</comment>]? ', $params['project_name']);

    $params['description'] = 'A curated list of ' . $params['project_name'];
    $params['description'] = $event->getIO()->ask('<info>A short description</info> [<comment>' . $params['description'] . '</comment>]? ', $params['description']);

    $params['username'] = 'Nobody';
    $params['username'] = $event->getIO()->ask('<info>Your username</info> [<comment>' . $params['username'] . '</comment>]? ', $params['username']);

    self::createFiles($params);
    self::cleanFile();

    return TRUE;
  }

  /**
   * Update the template files.
   *
   * @param array $params
   *   The array of parameters.
   */
  private static function createFiles(array $params): void {
    copy('templates/code-of-conduct.md', 'CODE-OF-CONDUCT.md');
    copy('templates/contributing.md', 'CONTRIBUTING.md');

    $file = file_get_contents('templates/readme.md');
    $file = preg_replace('/<%= title %>/', trim($params['project_name']), $file);
    $file = preg_replace('/<%= description %>/', $params['description'], $file);
    $file = preg_replace('/<%= username %>/', $params['username'], $file);

    file_put_contents('README.md', $file);
  }

  /**
   * Clean file from the repo.
   */
  private static function cleanFile(): void {
    // Remove template files.
    $filenames = [
      '.editorconfig',
      '.drone.yml',
      '.gitignore',
      'CHANGELOG.md',
      'docker-compose.yml',
      'packages.json',
      'templates/code-of-conduct.md',
      'templates/contributing.md',
      'templates/readme.md',
    ];
    foreach ($filenames as $filename) {
      if (file_exists($filename)) {
        unlink($filename);
      }
    }

    rmdir('templates');
  }

  /**
   * Remove the setup wizard file.
   *
   * @param \Composer\Script\Event $event
   *   The Composer event that triggered the wizard.
   */
  public static function cleanup(Event $event): void {
    // Remove composer create-project files.
    $filenames = [
      'scripts/composer/CheckGeneratorWizard.php',
      'scripts/composer/GeneratorWizard.php',
      'composer.json',
      'composer.lock',
    ];

    foreach ($filenames as $filename) {
      if (file_exists($filename)) {
        unlink($filename);
      }
    }

    rmdir('scripts/composer');
    rmdir('scripts');

    // Message for the user.
    $event->getIO()->write("Generator wizard file cleaned.");
  }

}
