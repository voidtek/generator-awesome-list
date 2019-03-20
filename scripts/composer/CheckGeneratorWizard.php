<?php

declare(strict_types = 1);

namespace GeneratorAwesomeList\composer;

use Composer\Json\JsonFile;
use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Setup wizard to handle user input during initial composer installation.
 */
class CheckGeneratorWizard {

  /**
   * Check the function Setup Wizard.
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
    // Check the file structure.
    $filenames = [
      'CODE-OF-CONDUCT.md',
      'README.md',
      'CONTRIBUTING.md',
    ];
    foreach ($filenames as $filename) {
      self::assertExistFile($filename);
    }

    $filenames = [
      '.git',
      '.drone.yml',
      '.editconfig',
      '.gitignore',
      'CHANGELOG.md',
      'docker-compose.yml',
      'templates/readme.md',
      'templates/code-of-conduct.md',
      'templates/contributing.md',
      'packages.json',
    ];
    foreach ($filenames as $filename) {
      self::assertNotExistFile($filename);
    }

    $strings = [
      '<%= title %>',
      '<%= description %>',
      '<%= username %>',
    ];
    $assert_filename = "README.md";
    foreach ($strings as $string) {
      self::assertFileNotContain($assert_filename, $string);
    }

    $event->getIO()->write("Generator wizard checked.");
    return TRUE;
  }

  /**
   * Assert that the file exists.
   *
   * @param string $filename
   *   The filename of the file.
   */
  private static function assertExistFile(string $filename): void {
    $fs = new Filesystem();

    if (!$fs->exists($filename)) {
      throw new \RuntimeException('The ' . $filename . ' file/folder does not exist.');
    }
  }

  /**
   * Assert that the file doesn't exist.
   *
   * @param string $filename
   *   The filename of the file.
   */
  private static function assertNotExistFile(string $filename): void {
    $fs = new Filesystem();

    if ($fs->exists($filename)) {
      throw new \RuntimeException('The ' . $filename . ' file/folder should not exist.');
    }
  }

  /**
   * Assert that a file doesn't contain a string.
   *
   * @param string $filename
   *   The filename of the file.
   * @param string $string
   *   A string that the file should not contain.
   */
  private static function assertFileNotContain(string $filename, string $string): void {
    if (strpos(file_get_contents($filename), $string) !== FALSE) {
      throw new \RuntimeException('The ' . $filename . ' file should not contain ' . $string . '.');
    }
  }

}
