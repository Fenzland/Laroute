Laroute (developing)
================================

A high readablility route syntax for Laravel.

# Documentation

See [laroute.fenzland.com](http://laroute.fenzland.com).

# Base Usage

Step 1. Get Laroute by composer
``` bash
composer require laroute/laroute
```
Step 2. Find your App\Providers\RouteServiceProvider in your Laravel project, and replace **require** statement.

``` php
// require balabala.php;
\Laroute\route(balabala);
```

Step 3. Write routes with Laroute, and save file named with extension '.laroute'.

# Editor support

[Sublime Text](https://github.com/Fenzland/Laroute-sublime)

# License

[MIT license](http://opensource.org/licenses/MIT).
