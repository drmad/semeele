# Semeele
Extremely simple and minimalistic XML generator for PHP. Really, very simple.

I was using the [native XML libraries](http://php.net/manual/es/refs.xml.php) to create an... XML document. But they are _extremely verbose_. So I coded this initially in about four hours.

It works with PHP 5.6 and newer versions.

## Example

```php
$xml = new drmad\semeele\Document('html');
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

```xml
<?xml version="1.0" encoding="utf-8"?><html><head><title>An XHTML</title><meta charset="utf-8" /></head><body><h1>An XHTML</h1><p>This is a XML-valid HTML. Yay!</p></body></html>
```

## Basic usage

The base class is `drmad\semeele\Node`. Its constructor accepts three parameters:

* `$nodeName`: Required.
* `$content`: Optional. This is the node content. If an array is passed, is used instead the next parameter.
* `$attributes`: Optional. Array with `['attribute name' => 'attribute value']`.

This class has two main methods for add new children nodes: `child()` and `add()`. Both returns a new `Node` object (the newly created node, and the parent node, respectively), for chaining. All the arguments are passed to the `Node` constructor.

Other methods are:

* `parent()`: Used for 'going up the chain': it returns the parent node, so you can create a new child with `child()`, for instance.
* `append(Node $node)`: Adds an already created node and its children to this node child list. Returns this node.
* `attr($name, $value)`: Adds new properties to this node. You can specify both arguments, or pass an associative array with multiple properties as first argument.
* `comment($text)`: Adds a new `drmad\semeele\Comment` node, returns this node.

The final XML is generated with the `getXML()` method, or just using the object in a `string` context.

The class `drmad\semeele\Document` is a `Node` but creates the [XML declaration](https://en.wikipedia.org/wiki/XHTML#XML_declaration) (using a `drmad\semeele\ProcessingInstruction` node) before its content. Its constructor has these parameters:

* `$rootNodeName`: Required. It will be the name of the main `Node` in the XML document.
* `$version`: Optional. XML version, defaults to `1.0`.
* `$encoding`: Optional. XML encoding, defaults to `utf-8`.

You can obtain the XML declaration node with `getDeclaration()`, perhaps to add new attributes, etc.

