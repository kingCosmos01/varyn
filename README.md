# Varyn Website

This repository holds the code and resources behind the [Varyn.com](https://varyn.com) website.

The site is built on:

 * HTML 5 and CSS 3
 * JavaScript ES5
 * Bootstrap 5
 * [PHP](https://php.net) 8.0.3
 * [node.js](https://nodejs.org) 14.16.0
 * [Enginesis](https://enginesis.com)

## Development

All public facing resources are found in the `public` folder, and this is the folder pointed to by the web server to serve those resources.

Website services are found in the `services` folder. This is only accessible via PHP since it is outside the public website.

## Testing

JavaScript unit tests are run with Jest. Run `npm test`.

PHP unit tests are run with PHPUnit. Run as follows:

```bash
cd sitedev/test
sh runtests.sh
```

The tests run this way generate all output in log files. Check the log files for the results. Or run each test separately, check the file header comments to see the CLI.
