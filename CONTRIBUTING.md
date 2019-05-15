# Contributing

First off, thanks for your interest in contributing to Dopenote!

If you have any questions (or just want to chat) - join the [Dopenote Discord server](https://discord.gg/6VkYFwF).


## Pull Requests
If you're fixing a small, obvious bug, a typo or a translation, etc, just submit a pull request. Please look for existing issues or pull requests beforehand.

But if it's more complicated than that, you should open an issue on GitHub, or come chat on the [Discord server](https://discord.gg/6VkYFwF), before you start.

If you want to work on an existing issue, please comment before you'll start working on it, so others won't waste their time.

### Commits

When you're fixing an issue created on GitHub, start your commit message with referencing the issue, followed by a short message.

Example: `#53 Added CONTRIBUTING.md file`


## Security

If you've found a vulnerability bug please email xy2z@pm.me instead of opening an issue.


## Coding Style Guide

### General (php, js, html, css)
- Encoding should always be UTF-8.
- Use 1 tab for indenting, not spaces.
- Always spaces after `if`, `while`, `for`, etc.
  - Good: `if (check) {`
  - Bad: `if( check ){`
- Function and method names are lowercase with underscore
  - Good: `function create_note() {`
  - Bad: `function CreateNote() {`
- Classes are `CamelCase`.


### JS specific
- Try not to use unnessecary semi colons (read https://flaviocopes.com/javascript-automatic-semicolon-insertion/)


### PHP specific
Use https://www.php-fig.org/psr/psr-2/ EXCEPT for
- Use tab instead of spaces for indenting.
- Curley braces (`{`) are always written on the same line as the class/method/statements.


## Setup Development

### Requirements
- PHP 7.1+
- [Composer](https://getcomposer.org/)
- MySQL database


### Setup
1. Fork this repo (xy2z/dopenote)
1. Git clone your fork
1. Run `composer install` in the repo dir
1. Create a new database called `dopenote` (collation: `utf8mb4_unicode_ci`)
1. Rename the file `.env.example` to `.env`.
1. Change the `DB_*` configs in the `.env` file
1. Run `php artisan migrate:fresh`
1. Run `php artisan key:generate`
1. Run `php artisan serve`
1. See the site at http://127.0.0.1:8000
