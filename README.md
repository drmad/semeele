# Semeele
Extremely simple and minimalistic XML generator for PHP. Really, very simple.

I was using the [native XML libraries](http://php.net/manual/es/refs.xml.php) to create an... XML document. But they are _extremely verbose_. So I coded this initially in about four hours.

It works with PHP 5.6 and newer versions, with `mbstring` extension enabled.

## Examples

```php
$xml = new Drmad\Semeele\Document('html');
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

And that's it. That prints:

```xml
<?xml version="1.0" encoding="utf-8"?><html><head><title>An XHTML</title><meta charset="utf-8"/></head><body><h1>An XHTML</h1><p>This is a XML-valid HTML. Yay!</p></body></html>
```

A more complex, real-life example, used in signed UBL documents:

```php
$xml = new Drmad\Semeele\Node('ext:UBLExtension');
$xml->child('ext:ExtensionContent')
    ->child('ds:Signature', ['Id' => 'Signature'])->save($s) // Save this node for later
        ->child('ds:SignedInfo')
            ->add('ds:CanonicalizationMethod',
                ['Algorithm'=>'http://www.w3.org/TR/2001/REC-xml-c14n-20010315'])
            ->add('ds:SignatureMethod',
                ['Algorithm'=>'http://www.w3.org/2000/09/xmldsig#rsa-sha1'])
            ->child('ds:Reference', ['URI'=>''])
                ->child('ds:Transforms')
                    ->add('ds:Transform', ['Algorithm'=>'http://www.w3.org/2000/09/xmldsig#enveloped-signature'])
                    ->parent()
                ->add('ds:DigestMethod', ['Algorithm'=>'http://www.w3.org/2000/09/xmldsig#sha1'])
                ->add('ds:DigestValue')
;
        $s->add('ds:SignatureValue')    // Using the saved node
        ->child('ds:KeyInfo')
            ->child('ds:X509Data')
                ->add('ds:X509SubjectName')
                ->add('ds:X509Certificate')
;
echo $xml;
```
Outputs:

```xml
<ext:UBLExtension><ext:ExtensionContent><ds:Signature Id="Signature"><ds:SignedInfo><ds:CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/><ds:SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"/><ds:Reference URI=""><ds:Transforms><ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/></ds:Transforms><ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/><ds:DigestValue/></ds:Reference></ds:SignedInfo><ds:SignatureValue/><ds:KeyInfo><ds:X509Data><ds:X509SubjectName/><ds:X509Certificate/></ds:X509Data></ds:KeyInfo></ds:Signature></ext:ExtensionContent></ext:UBLExtension>
```

## Installation and basic usage

`Semeele` is available via [Composer](https://packagist.org/packages/drmad/semeele):

```
composer require drmad/semeele
```

Alternatively, you can `git clone` or download the ZIP from [GitHub](https://github.com/drmad/semeele), and use this simple autoloader function (update constant `SEMEELE_PATH` with the correct path as needed):

```php
const SEMEELE_PATH = './semeele-master';

spl_autoload_register(function($class) {
    if (substr($class, 0, 13) == 'Drmad\\Semeele') {
        $base_name = substr($class, strrpos($class, '\\') + 1);
        $file_name = SEMEELE_PATH . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $base_name . '.php';
        if (file_exists($file_name)) {
            require $file_name;
        }
    }
});

```

### `Drmad\Semeele\Node`

Base class for all nodes. Its constructor has these parameters:

* `$nodeName`: Required.
* `$content`: Optional. This is the node content. If an array is passed, it's used instead the `$attribute` parameter.
* `$attributes`: Optional. Array with `['attribute name' => 'attribute value']` structure.
* `$encoding`: Optional. Defaults to 'UTF-8'. Used to reencode `$content`, and it's passed to child nodes created with `child()` and `add()` methods.

This class has two main methods for adding new child nodes: `child()` and `add()`. Both returns a `Node` object, but `child()` returns the newly created node, and `add()` returns the current node, so you can keep adding new children to the same node.

All the arguments of both methods are passed to the `Node` constructor.

Other methods are (except otherwise noted, all methods returns the affected `Node` object):

* `parent()`: Returns the parent node. Used for 'going up the chain', perhaps after finished a run of `add()` methods (take a look at the examples above).
* `append(Node $node)`: Adds an already created node and its children to this node child tree.
* `save(&$node)`: Saves the node in `$node`. Useful for "returning" from a deep nested node.
* `attr($name, $value)`: Adds new properties to this node. You can specify both arguments, or pass an associative array with multiple properties and values as first argument.
* `comment($text)`: Adds a new `Drmad\Semeele\Comment` node.

The final XML is generated with the `getXML()` method, or just using the object in a `string` context.

### `Drmad\Semeele\Document`

This class extends `Node`, and adds an [XML declaration](https://en.wikipedia.org/wiki/XHTML#XML_declaration) (using a `Drmad\Semeele\ProcessingInstruction` class) before the node content. Its constructor has these parameters:

* `$rootNodeName`: Required. It will be the name of the main `Node` in the XML document.
* `$version`: Optional. XML version, defaults to `1.0`.
* `$encoding`: Optional. XML encoding, defaults to `UTF-8`.

You can obtain the XML declaration node with `getDeclaration()`, perhaps to add new attributes, etc.

### `Drmad\Semeele\Cdata`
Creates a CDATA value for escaping strings. Only useful if a human needs to read the XML, because all strings are always converted to XML entities when needed. E.g.:

```php
$xml = new Drmad\Semeele\Node('test');
$xml->add('without_cdata', 'Love this song from <strong>KC & The Sunshine</strong>')
    ->add('with_cdata', new Drmad\Semeele\Cdata('Love that song from <strong>KC & The Sunshine</strong>'))
;
echo $xml;
```
Outputs:

```xml
<test><without_cdata>Love this song from &lt;strong&gt;KC &amp; The Sunshine&lt;/strong&gt;</without_cdata><with_cdata><![CDATA[Love that song from <strong>KC & The Sunshine</strong>]]></with_cdata></test>
```
