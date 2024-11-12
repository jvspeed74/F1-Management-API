# Developer Guide

## Table of Contents

1. [Composer](#composer)
2. [Coding Standards](#coding-standards)
3. [GitHub Actions](#github-actions)

## Composer

- The project includes a preconfigured `composer.json` file.
- The repository includes `composer.phar`, allowing Composer to be run without a global installation.
- Several scripts are included in the `composer.json` file to help with common tasks. 
  - You can run the following commands in your terminal:
    - `composer test` runs the test suite.
    - `composer fix` runs PHP-CS-Fixer, fixing any coding standard issues.
    - `composer lint` runs PHPStan, identifying any potential issues in the code.
    - `composer start` starts the built-in PHP server.

## Coding Standards

- The project `.idea` file has been configured to adhere to PERCS-2.0 PHP standards.
- The `phpstan` and `php-cs-fixer` packages are required in the `dev` section of the `composer.json` file. Once
  downloaded, PhpStorm detects them and runs them in the background, providing code inspections on the fly.
- This setup does not require manual intervention.

## GitHub Actions

- On every PR leading to a major branch, a pipeline will run on GitHub to verify coding standards and ensure tests are successful.
- Nothing prevents code from being pushed to the repository; the pipeline is there to highlight key issues in our code.
- The project requires the `GitHub Action Manager` plugin, allowing you to see the results of the pipeline without
  needing to tab out from PhpStorm.
- To see the pipeline results, click on the circular icon with a play button, located in the bottom left corner of the
  IDE.



