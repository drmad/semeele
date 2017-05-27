# Semeele
Extremely simple and minimalistic XML generator for PHP. Really, very simple.

I was using the [native XML libraries](http://php.net/manual/es/refs.xml.php) for creating a... XML document. But they are _extremely verbose_. So I coded this in about four hours.

It works with PHP 5.6 and newer versions.

## Usage

The base class is `drmad\semeele\Node`. Its constructor accepts three parameters:

* `$nodeName`: Required.
* `$content`: Optional. If an array is passed, is used instead the next parameter.
* `$attributes`: Optiona. Array with `['atribute name' => 'attribute value']`.

This class has two main methods: `child()` and `add()`. Both returns a new Node object (the newly created node, and the parent node, respectively), for chaining. All the arguments are passed to the Node constructor.

A method `parent()` is used for 'going up the chain': it returns the parent node, so you can create a new child with `child()`, for instance.

The final XML is generated with the `getXML()` method.

The class `drmad\semeele\Document` is a `Node` but creates the [XML declaration](https://en.wikipedia.org/wiki/XHTML#XML_declaration) before its content. Its constructor has this parameters:

* `$rootNodeName`: Required. It will be the main `Node` in the XML document.
* `$version`: Optional. XML version, defaults to '1.0'.
* `$encoding`: Optional. XML encoding, defaults to 'utf-8'.

## Example

```
require 'semele/Node.php';
require 'semele/Document.php';

$xml = new drmad\semeele\Document('html', '1.0', 'utf-8');
$xml->child('head')
    ->add('title', 'An XHTML')
    ->add('meta', ['charset' => 'utf-8'])
    ->parent()
->child('body')
    ->add('h1', 'An XHTML')
    ->add('p', 'This is a XML-valid HTML. Yay!')
;

echo $xml->getXML();
```

And that's it. This prints:

```
<?xml version="1.0" encoding="utf-8"?>
<html><head><title>An XHTML</title><meta charset="utf-8"/></head><body><h1>An XHTML</h1><p>This is a XML-valid HTML. Yay!</p></body></
html>

```
