mysqli_improved
===============

Simplified interface for executing parameterized queries through mysqli. This is gist-quality code, not a ready-to-use
library. If you find this bit of code useful and are interested in contributing back, please read the TODO in the
`__construct()` method in the `MySQL` class.

I thought this code might be handy for a PHP developer who doesn't have the luxury of an ORM. I've since moved on to
other languages, so I have no intention of updating this project unless others express interest in it.

Parts of this class were derived from comments on PHP documentation pages. Many thanks to TheJkWhoSaysNi for his
contributions to
the [`mysqli_stmt_bind_result` method discussion](http://php.net/manual/en/mysqli-stmt.bind-result.php), and to
tasdildiren for his contribution to
the [`mysqli_stmt_bind_param` method discussion](http://php.net/manual/en/mysqli-stmt.bind-param.php).

Usage
-----

Modify the `__construct()` method in the `MySQL` class to connect to your database. Once that's done, you can execute parameterized queries 
as follows:

    $bindParams = array( 'dave' );
    $types = 's'; // see types for the mysqli_stmt_bind_param method.
    $mysql = new MySQL();
    $results = $mysql->parameterizedQuery( 'SELECT * FROM user WHERE username=?', $types, $bindParams );

Or more succinctly:

    $mysql = new MySQL();
    $results = $mysql->parameterizedQuery( 'SELECT * FROM user WHERE username=?', 's', array( 'dave' ) );

The returned `$results` variable contains an array of records matching your query, with each record’s structure being
identical to the output expected from [`mysqli_fetch_array`](http://php.net/manual/en/mysqli-result.fetch-array.php).
