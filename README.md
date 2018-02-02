# Hulksmash

A [Laravel Zero](http://laravel-zero.com) console application.

## What

Scaffolds a basic (framework-less) PHP project structure from a set (TODO) of boilerplates:
    
- git clone branch from boilerplate repo 
- run composer install
- clean up (delete .git folder)

## Why

I am lazy. 

Laravel Zero is fancier than Bash.

It's the modern way.

## Download

See https://github.com/macghriogair/hulksmash/releases

Make executable

    chmod a+x hulksmash-7.2

Optionally, move it to a folder on your PATH:

    mv hulksmash-7.2 /usr/local/bin/hulksmash

## Build phar executable yourself

    ./hulksmash app:build --env=production hulksmash
    mv builds/hulksmash /usr/local/bin/hulksmash

## Usage

In a shell at your workspace path run:

    # or: ./hulksmash if running locally
    hulksmash
