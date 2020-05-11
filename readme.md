# Convert PHP site to HTML site

Basically this script takes snapshot of your site time you run it and saves it as html.
If you encounter any php error script would also terminate!

### Use

```bash
$ php convert.php ip=input-folder op=output-folder replace-href=1 convert-includes=1 copy-git=1
```

### Options

Note: boolean options take 1 as true and 0 as false

- ip=input-folder : required
- op=output-folder : default=html
- replace-href=1 : whether to replace php links with html ex `<a href="demo.php">` will be replaced with `<a href="demo.html">`) **default is true**
- convert-includes=1 : if set false include/ includes/ or .inc.php files files will nor converted neither copied. **default is false**
- copy-git=1 : whether to copy .git folder or not **default is false**
