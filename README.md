<p align="center"><img title="Dopenote" alt="Dopenote" src="https://i.imgur.com/CaXtfc4.png" /></p>

# Dopenote

**Work in Progress - Project started Feb. 2019**

Dopenote is a self-hosted note webapp. The first releases will be very basic, and improve over time.


## Progress

Follow the progress via [Milestones](https://github.com/xy2z/dopenote/milestones)

## Setup

This project is still a work in progress.

## Contributing

Feel free to contribute! Check out the issues labelled `help wanted`.

### Requirements

- PHP 7.1+
- [Composer](https://getcomposer.org/)
- MySQL database

### Setup

1. Fork this repo (xy2z/dopenote)
1. Git clone your fork
1. `cd` to the project
1. Run `composer install`
1. Meanwhile, create a new database called `dopenote` (collation: `utf8mb4_unicode_ci`)
1. Rename `.env.example` to `.env`
1. Change the `DB_*` configs in the `.env` file
1. Run `php artisan migrate:fresh`
1. Run `php artisan key:generate`
1. Run `php artisan serve`
1. See the site at http://127.0.0.1:8000
