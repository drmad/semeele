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
<?xml version="1.0" encoding="utf-8"?><html><head><title>An XHTML</title><meta charset="utf-8"/></head><body><h1>An XHTML</h1><p>This is a XML-valid HTML. Yay!</p></body></html>
```

A more complex, real-life example, used in signed UBL documents:

```php
$xml = new Node('ext:UBLExtension');
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

## Main classes

### `drmad\semeele\Node`

Base class for all nodes. Its constructor has this parameters:

* `$nodeName`: Required.
* `$content`: Optional. This is the node content. If an array is passed, is used instead the next parameter.
* `$attributes`: Optional. Array with `['attribute name' => 'attribute value']`.

This class has two main methods for add new children nodes: `child()` and `add()`. Both returns a new `Node` object (the newly created node, and the parent node, respectively), for chaining. All the arguments are passed to the `Node` constructor.

Other methods are:

* `parent()`: Used for 'going up the chain': it returns the parent node, so you can create a new child with `child()`, for instance.
* `append(Node $node)`: Adds an already created node and its children to this node child list. Returns this node.
* `save(&$node)`: Save the node in $node. Useful for "returning" from a deep nested node.
* `attr($name, $value)`: Adds new properties to this node. You can specify both arguments, or pass an associative array with multiple properties and values as first argument.
* `comment($text)`: Adds a new `drmad\semeele\Comment` node, returns this node.

The final XML is generated with the `getXML()` method, or just using the object in a `string` context.

### `drmad\semeele\Document`

This class extends `Node`, but creates a [XML declaration](https://en.wikipedia.org/wiki/XHTML#XML_declaration) (using a `drmad\semeele\ProcessingInstruction` class) before its content. Its constructor has these parameters:

* `$rootNodeName`: Required. It will be the name of the main `Node` in the XML document.
* `$version`: Optional. XML version, defaults to `1.0`.
* `$encoding`: Optional. XML encoding, defaults to `utf-8`.

You can obtain the XML declaration node with `getDeclaration()`, perhaps to add new attributes, etc.

### `drmad\semeele\Cdata`
Used instead a string for a CDATA value. Only useful if a human needs to read the XML, because all string are always converted to XML-valid characters. E.g.:

```php
$xml = new drmad\semeele\Node('test');
$xml->add('without_cdata', 'Love this song from <strong>KC & The Sunshine</strong>')
    ->add('with_cdata', new drmad\semeele\Cdata('Love that song from <strong>KC & The Sunshine</strong>'))
;
echo $xml;
```
Outputs:

```xml
<test><without_cdata>Love this song from &lt;strong&gt;KC &amp; The Sunshine&lt;/strong&gt;</without_cdata><with_cdata><![CDATA[Love that song from <strong>KC & The Sunshine</strong>]]></with_cdata></test>
```