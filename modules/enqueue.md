---
description: >-
  The PinkCrab Enqueue class allows for a clean and chainable alternative for
  enqueueing scripts and styles in WordPress
---

# Enqueue

> At present this class doesnt support BC in regards to named properties in PHP8. Argument names may change, although we have made this so most methods take a single or no properties at all.

```php
add_action('wp_enqueue_scripts', function(){

    // Enqueue a script
    Enqueue::script('My_Script')
        ->src('https://url.tld/wp-content/plugins/my_plugn/assets/js/my-script.js')
        ->deps('jquery')
        ->lastest_version()
        ->register();

    // Enqueue a stylesheet
    Enqueue::style('My_Stylesheet')
        ->src('https://url.tld/wp-content/plugins/my_plugn/assets/css/my-stylesheet.css')
        ->media('all and (min-width: 1200px)')
        ->lastest_version()
        ->register();
});
```

The above examples would enqueue the script and stylesheet using wp\_enqueue\_script\(\) and wp\_enqueue\_style\(\)

### Instantiation of \PinkCrab\Enqueue::class

You have 2 options when creating an instance of the Enqueue object.

```php
$enqueue_script = new Enqueue( 'my_script', 'script');
$enqueue_style = new Enqueue( 'my_style', 'style');

// OR 

$enqueue_script = Enqueue::script('my_script');
$enqueue_style = Enqueue::style('my_style');
```

When you call using the static methods script\(\) or style\(\), the current instance is returned, allowing for chaining into a single call. Rather than doing it in the more verbose methods.

```php
$enqueue_script = new Enqueue( 'my_script', 'script');
$enqueue_script->src('.....');
$enqueue_script->register();

// OR 

Enqueue::script('my_script')
    ->src('.....')
    ->register();
```

### File Location

The URI to the defined js or css file can be defined here. This must be passed as a url and not the file path.  
_This is the same for both styles and scripts_

```php
Enqueue::script('my_script')
    ->src(PLUGIN_BASE_URL . 'assets/js/my-script.js')
    ->register();
```

### Version

Like the underlying wp\_enqueue\_script\(\) and wp\_enqueue\_style\(\) function, we can define a verison number to our scripts. This can be done using the ver\('1.2.2'\) method.  
_This is the same for both styles and scripts_

```php
Enqueue::script('my_script')
    ->src(PLUGIN_BASE_URL . 'assets/js/my-script.js')
    ->ver('1.2.2') // Set to your current version number.
    ->register();
```

However, this can be fustrating while developing, so rather than using the current timestamp as a temp version. You can use the _lastest\_version\(\)_, this grabs the last modified date from the defined script or style sheet, allowing reducing the fustrations of caching during development. While this is really handy during development, it should be changed to **-&gt;ver\('1.2.2'\)** when used in production.

_This is the same for both styles and scripts_

```php
Enqueue::script('my_script')
    ->src(PLUGIN_BASE_URL . 'assets/js/my-script.js')
    ->lastest_version()
```

### Dependencies 

As with all wp\_enqueue\_script\(\) and wp\_enqueue\_style\(\) function, required dependencies can be called. This allows for your scripts and styles to be called in order.

_This is the same for both styles and scripts_

```php
Enqueue::script('my_script')
    ->src(PLUGIN_BASE_URL . 'assets/js/my-script.js')
    ->deps('jquery') // Only enqueued after jQuery.
```

### Front vs wp-admin 

By default enqueue can be used for both the frontend and wp-admin \(inc ajax, rest\). You have control over where they called.

_This is the same for both styles and scripts_

```php
// Dont enqueue if is_admin()
Enqueue::script('my_script')
    ->src(PLUGIN_BASE_URL . 'assets/js/my-script.js')
    ->lastest_version() 
    ->admin(false)

// Only enqueue if ! is_admin()
Enqueue::script('my_script')
    ->src(PLUGIN_BASE_URL . 'assets/js/my-script.js')
    ->lastest_version() 
    ->front(false)
```

### Localized Values 

One of the most useful parts of enqueuing scripts in WordPress is passing values from the server to your javascript files. Where as using the regular functions,  requires registerign the style, localizing your data and then registering the script. While it works perfectly fine, it can be a bit on the verbose side.

The localize\(\) method allows this all to be done within the single call.

_This can only be called for scripts_

```php
Enqueue::script('MyScriptHandle')
    ->src(PLUGIN_BASE_URL . 'assets/js/my-script.js')
    ->localize([ 
        'key1' => 'value1', 
        'key2' => 'value2', 
    ])
    ->register();
```

Usage within js file \(my-script.js\)

```javascript
console.log(MyScriptHandle.key1) // value1
console.log(MyScriptHandle.key2) // value2
```

### Footer

By default all scripts are enqueued in the footer, but this can be changed if it needs to be called in the head. By calling either _footer\(false\)_ or _header\(\)_

_This can only be called for scripts_

```php
Enqueue::script('my_script')
    ->src(PLUGIN_BASE_URL . 'assets/js/my-script.js')
    ->footer(false)
    ->register();
// OR 
Enqueue::script('my_script')
    ->src(PLUGIN_BASE_URL . 'assets/js/my-script.js')
    ->header()
    ->register();
```

### Media

As with wp\_enqueue\_style\(\) you can specifiy the media for which the sheet is defined for. Accepts all the same values as wp\_enqueue\_style\(\)

_This can only be called for styles_

```php
Enqueue::style('my_style')
    ->src(PLUGIN_BASE_URL . 'assets/js/my-style.css')
    ->media('(orientation: portrait)')
    ->register();
```

### Registration 

Once your Enqueue object has been populted all you need to call **register\(\)** for wp\_enqueue\_script\(\) or wp\_enqueue\_style\(\) to be called. You can either do all this inline \(like the first example\) or the Enqueue object can be populated and only called when required.

_This is the same for both styles and scripts_

```php
class My_Thingy{
    /**
     * Reutrns a partly finalised Enqueue scripts, with defined url.
     * 
     * @param string $script The file location.
     * @return Enqueue The populated enqueue object.
     */ 
    protected function enqueue($script): Enqueue {
        return Enqueue::script('My_Script')
            ->src($script)
            ->deps('jquery')
            ->lastest_version();
    } 

    /**
     * Called to initalise the class.
     * Registers our JS based on a constitional.
     * 
     * @return void
     */
    public function init(): void {
        if(some_conditional()){
            add_action('wp_enqueue_scripts', function(){
                $this->enqueue(SOME_FILE_LOCATION_CONSTANT)->register()
            });
        } else {
            add_action('wp_enqueue_scripts', function(){
                $this->enqueue(SOMEOTHER_FILE_LOCATION_CONSTANT)->register()
            });
        }
    }
}

add_action('wp_loaded', [new My_Thingy, 'init']);
```

## Public Methods

```php
   /**
    * Creates an Enqueue instance.
    *
    * @param string $handle
    * @param string $type
    */
   public function __construct( string $handle, string $type )

   /**
    * Creates a static instace of the Enqueue class for a script.
    *
    * @param string $handle
    * @return self
    */
   public static function script( string $handle ): self

   /**
    * Creates a static instace of the Enqueue class for a style.
    *
    * @param string $handle
    * @return self
    */
   public static function style( string $handle ): self

   /**
    * Defined the SRC of the file.
    *
    * @param string $src
    * @return self
    */
   public function src( string $src ): self

   /**
    * Defined the Dependencies of the enqueue.
    *
    * @param string ...$deps
    * @return self
    */
   public function deps( string ...$deps ): self

   /**
    * Defined the version of the enqueue
    *
    * @param string $ver
    * @return self
    */
   public function ver( string $ver ): self

   /**
    * Define the media type.
    *
    * @param string $media
    * @return self
    */
   public function media( string $media ): self

   /**
    * Sets the version as last modified file time.
    *
    * @return self
    */
   public function lastEditedVersion(): self

   /**
    * Should the script be called in the footer.
    *
    * @param boolean $footer
    * @return self
    */
   public function footer( bool $footer = true ): self

   /**
    * Should the script be called in the inline.
    *
    * @param boolean $inline
    * @return self
    */
   public function inline( bool $inline = true ):self

   /**
    * Pass any key => value pairs to be localised with the enqueue.
    *
    * @param array $args
    * @return self
    */
   public function localize( array $args ): self

   /**
    * Registers the file as either enqueued or inline parsed.
    *
    * @return void
    */
   public function register(): void
```

This obviously can be passed around between different classes/functions

### Contributions

If you would like to make any suggestions or contributions to this little class, please feel free to submit a pull request or reach out to speak to me. at glynn@pinkcrab.co.uk.

