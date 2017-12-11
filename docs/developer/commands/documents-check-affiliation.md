# `documents:check-affiliation` command

Check if there are documents assigned to a different institution than the user uploader one.

In version 0.7.x and below the document could be linked to an institution that is 
different from the one attached to the user who has performed the file upload 
(or url import). This case could lead to have the same document indexed in the search 
engine with two institutions.

This command attempts to normalize the situation by checking the discrepancies in the private documents hosted by the K-Box.

## Check

By launching the following command you will be able to test if there is a difference between the user institution and the document one

```
$ php artisan documents:check-affiliation
```

The result will be similar to 

```
+----------+------------------------------------------------+
| Document | Error                                          |
+----------+------------------------------------------------+
| 101      | Different Institution (user: 19, document: 13) |
| 103      | Different Institution (user: 19, document: 21) |
+----------+------------------------------------------------+
```

which list the documents with user institution different than the one attached to the document itself.

Documents are listed by ID, as the institution of the user and the institution of the document.

The execution with no parameters do not act to the database to fix the difference.


## Fix 

You can fix the problem by making the document institution equal to the institution of the document's owner, which is also the original user that uploaded the document.

This can be done applying the option `--override-with-uploader`:

```
$ php artisan documents:check-affiliation --override-with-uploader
```

The change will only be applied to the database, no actual search engine update.

If you want also to update the information stored in the search engine add the `--update-search-engine` option

```
$ php artisan documents:check-affiliation --override-with-uploader --update-search-engine
```

With this configuration the document with the wrong institution will be removed from the search engine and only the document with the correct institution will be indexed.
