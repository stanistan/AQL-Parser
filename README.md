# AQL Parser

The purpose AQL is to be able to turn this:

```
table { field1, field2, field3 }
```

into

```sql
SELECT table.field1, table.field2, table.field3 FROM table;
```

or, with a variance of a restraint that will be configurable.

```sql
SELECT table.field1, table.field2, table.field3 FROM table WHERE table.active = 1;
```

And also use it to generate data models.

```php
<?

// not yet implemenented

class Table extends \AQL\Model { 
    // this object would automatically have a restriction
    // on what properties that it can have 
    // as  defined by its AQL statement
    
    public $required = array(
        'field1' => 'Some Field'
    );
    
}

```

#### Credit

Using [this](https://github.com/jakubkulhan/pacc/) as a Parser Generator to replicate and improve upon [AQL](https://github.com/SkyPHP/skyphp).

#### Run and compile the parser generator

In git directory, run:

```sh
$ ./pacc -i src/aql.y  -o lib/AQL/Parser.php -f
```

#### To Do:

- Add tests
- Make actual SQL out of the AQL. (although this ends up being fairly trivial)
- Enable a DB adapter so joins can be constructed automatically using foreign keys.
- Allow for optional table constraints (`active = 1`)
- Infinite Recursion for inner queries, and possibly subqueries, the current version does not support subqueries.
- Fix how the tokens are actually made (currently just `stdClass` with two properties). 