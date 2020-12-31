# Fat-Free Environment Check

This will create a new route `/environment-check` in your Fat-Free project that you can use to verify that your environment has "all the good things".

See `index.php` for an example of how to implement this. It's pretty simple, just add `n0nag0n\Environment_Check::instance();` in your main index/bootstrap file and away you go!