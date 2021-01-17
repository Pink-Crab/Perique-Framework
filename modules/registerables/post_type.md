---
description: >-
  Post_Type is an Abstract Class which can be extended within your codebase, to
  create fully customisable custom post types, easily and cleanly.
---

# Post\_Type

## Basic Setup

You can create a very simple, public facing post type with only the need to use a key, singluar and plural titles.

```php
//@file src/CPT/Public_Post_Type.php

class Public_Post_Type extends Post_Type {
    public $key = 'public_cpt';
    public $singular = 'Public Post';
    public $plural   = 'Public Posts';
}

/** Then add to registration array */

//@file config/registration.php
use My_Plugin\CPT\Public_Post_Type;

return array(
    .......
    Public_Post_Type::class,
    .......
);
```

As with all classes which implement the Registerable interface, adding the post type to the registration config file, will handle all the registration for you.

## Fields

The core register\_post\_type\(\) function takes a slug and an array of labels. To make this less complicated and messy, almost all args are defined as properties.

### $key

> @var string  
> @required

The Post Type key is the internal key used for your post type, this can be overwritten if you wish to use a more seo friendly slug for archives and permalinks \(see $SLUG below\).

### $singular

> @var string  
> @required

The Post Types singular label. Used for "Create new {$singular}"

### $plural

> @var string  
> @required

The Post Types plural label. Used for "View {$plural}"

### $slug

> @var string\|null

The slug is a value you can use if you wanted to change just the slug of the permalinks created for your post type. \(see example below\)

### $dashicon

> @var string  
> ****@default 'dashicons-pets'

You can set a custom dash icon for wp-admin, you can use either DashIcons or custom icons. If no dashicon is supplied, the pets \(dog paw\) will be used in its place.

### $menu\_position

> @var int  
> ****@default 60

Define the position in wp-admin menu.

### $metaboxes

> @var array\[MetaBox\] // PinkCrab\Modules\Registerables\MetaBox

This can be loaded with metaboxes for this post type. The array must be populated with pre-configured MetaBox objects. The MetaBoxes are registered in the Post Type registration process. While they can be added directly into this property, there is an overwritable method that makes this easier.  As you can not define an object as a property in a class, you will need to use either the metaboxes\(\) method in constructor or child obj&gt;ect.   
See the example below.

### $public

> @var bool  
> @default TRUE

Should this post type be accessible by both the frontend and within wp-admin. If set to true, will be a hidden post type, with no admin UI, permalinks,  or queryable from frontend. 

### $show\_in\_nav\_menus

> @var bool\|null  
> @default TRUE

Should post type be included in the menu selections.

### $show\_in\_menu

> @var bool\|null  
> @default TRUE

Should post type be included in the main wp-admin menu

### $show\_ui

> @var bool\|null  
> @default TRUE

Should post type have the post list, create/edit/delete UI in wp-admin.

### $has\_archive

> @var bool\|null  
> @default TRUE

Should post type have an archive created on the frontend.

### $hierarchical

> @var bool\|null  
> @default FALSE

Should this post type have hierarchical properties?

### $exclude\_from\_search

> @var bool\|null  
> @default FALSE

Should this post type be excluded from the site-wide search.

### $publicly\_queryable

> @var bool\|null  
> @default TRUE

Allow the Post Type to be accessible from URL params.

### $can\_export

> @var bool\|null  
> @default TRUE

Allow post type to be exportable.

### $query\_var

> @var bool\|string  
> @default FALSE

This can be used to assign this post type to any public query vars. [See the codex for more details.](https://codex.wordpress.org/WordPress_Query_Vars)

### $rewrite

> @var bool\|array\|null  
> @default \['slug' =&gt; $key/$slug, 'with\_front' =&gt; true, 'feeds'=&gt;false, 'pages'=&gt;false\]

This can be used to set the rewite rules for the post type. If $rewrite is left as NULL, it will be resolved to the defualt of  \['slug' =&gt; $key/$slug, 'with\_front' =&gt; true, 'feeds'=&gt;false, 'pages'=&gt;false\].  
If you wish to have no permalinks, you can pass FALSE here, else define with your own array.  
_Please note that we use the constructor to set default if left as null._

### $capability\_type

> @var string\|array  
> @default 'post'

The string to use to build the read, edit, and delete capabilities. [See the wordpress codex for more details](https://developer.wordpress.org/reference/functions/register_post_type/#capability_type)

### $supports

> @var bool\|array  
> @default \[ \]

Denotes all the edit post features supplied. If left as an empty array will include \(title and editor\), passing false will remove all features.

### $taxonmies

> @var array  
> @default \[ \]

All taxonomies to include with this post type. Please note if you are adding custom taxonomies using the Taxonomies Registerable, it's best to list the post types in the taxonomy and use this for core or plugin taxonomies.

## Methods

The Post\_Type class comes with a few methods you can use for setting and modifying the defined values. 

### public function metaboxes\(\): void

> @return void

This method is used for creating and defining all the metaboxes used for this post type. The method should be used to populate the $metaboxes array with partially completed MetaBox objects, then when the Post\_Type is registered, the metaboxes are automatically added and rendered. See the example below.

### public function slug\(\): ?string

> @return null\|string

This returns either the defined $slug or $key if the slug isn't defined.

### public function filter\_labels\(array $labels\): array

> @param array $labels The compiled labels array.  
> @return array

Before the labels are passed to register\_post\_type\(\), they can be filtered through this method. This allows the altering of label values, based on the result of operations.

### public function filter\_args\(array $args\): array

> @param array $args The compiled args array.  
> @return array

Like the labels, the full args array can be altered at run time as well, by overwriting this method.

### public static function get\_slug\(\): ?string

This can be used to get the defined slug for the post type, without directly constructing the object. If you plan to use this method, please be aware it creates its own internal instance BUT DOES NOT USE the DI container, so the use of custom constructor arguments will throw errors.

## Examples

### Using a custom slug for Permalinks.

If you wish to use a custom slug, it can be defined like this.

```php
class Public_Post_Type extends Post_Type {
    public $key = 'public_post_type';
    public $singular = 'Public Post';
    public $plural   = 'Public Posts';
    // Custom slug
    public $slug = 'my_post_type';
}
```

Now we have a post type which has a post\_type of '**public\_post\_type**' but all permalinks and archives have the slug if '**my\_post\_type'**

![](../../.gitbook/assets/shlq7z5.png)

![](../../.gitbook/assets/tivqelr.png)

### Registering MetaBoxes

To register MetaBoxes, populate the $this-&gt;metaboxes property \(an array\) with partially constructed MetaBox objects. When the registration process is run, they will be bound to your post type and included.

```php
use PinkCrab\Modules\Registerables\Post_Type;
use PinkCrab\Modules\Registerables\MetaBox;

class Public_Post_Type extends Post_Type {
    
    public $key = 'public_post_type';
    public $singular = 'Public Post';
    public $plural   = 'Public Posts';
    
    // Register metaboxes
    public function metaboxes(){
        $this->metaboxes[] = MetaBox::normal('custom_metabox')
            ->label( 'This is the main meta box' )
            ->view([$this, 'metabox_1_view'])
            ->view_vars(['key' => 'value'])
            ->add_action('edit_post', [$this, 'metabox_edit_post'], 10, 2);
            ->add_action('delete_post', [$this, 'metabox_delete_post'], 10, 2);
        
        // If you wish to add more than one.
        $this->metaboxes[] = MetaBox::side('another_metabox')
            ->label('Etc etc')
            ->view([$this, 'metabox_2_view'])
            ->view_vars(['key2' => 'value2'])
            ......
    }
        
    /**
     * Render metabox
     *
     * @param WP_Post $post The post
     * @paran array $view_vars Metabox view args
     */
    public function metabox_1_view(WP_Post $post, array $view_vars): void{
        // The values found in view_vars.
        // ['key' => 'value']
        
        echo 'Whatever you want in the MetaBox';
    }
    
    /**
     * Save metabox.
     *
     * @param int $post_id The post Id.
     * @param WP_Post $post The post
     */
    public function metabox_edit_post(int $post_id, WP_Post $post): void{
        // Save any post meta, or fire off actions etc.
    }
}
```

Please note if your MetaBox is to be displayed on other post types, it's often better to register them in a separate Controller. When registered in a Post\_Type object, the screen is automatically defined as this post type.

If you are adding more than 1 metabox, it's best to use shared hooks, rather than calling the same hook multiple times.

### Using filter\_labels\(\)

filter\_labels\(\) can be used to either alter the predefined value or adding in new ones.

```php
class Orders_CPT extends Post_Type {
    ...
    public $singular = 'Order';
    public $plural   = 'Orders';
    ...
    
    // Show different labels based on settings.
    public function filter_labels(array $labels): array{
        
        // Alter based on a conditional
        if( (bool) get_option('use_custom_order_labels') ) {
            $labels['name'] = get_option('custom_order_label_name');
            $labels['singular_name'] = get_option('custom_order_label_singular_name');
        }
        
        // Can also be used to add in additional labels not included above.
        $labels['use_featured_image'] = 'Set as featured images';
        
        return $labels;
    }
}
```

### Using filter\_args\(\)

filter\_args\(\) can be used to alter the post types properties at run time, based on operations and current state.

```php
class Secret_CPT extends Post_Type {
    ...
    // Assume its usally hidden.
    public $public = false;
    ...
    

    public function filter_args(array $args): array{
        
        // Get the users meta value and if true, change
        // the $public to true.
        $user_has_secret_access = get_user_meta(
             get_current_user_id(),
             'has_secret_cpt_access',
             true
         );        
        
        if( (bool) $user_has_secret_access ){
            $args['public'] = true;
        }
        return $args;
    }
}
```

### Using CPT::get\_slug\(\)

If you need access to the slug, you can call using get\_slug\(\).

```php
if(My_CPT::get_slug() === 'my_cpt_slug'){
    // Do something cool
}
```

However, if you have passed dependencies to your Post\_Types constructor, you will need to access it using the DI container.

```php
if(App::make(My_CPT::class)::get_slug() === 'my_cpt_slug'){
    // Do something cool
}
```

### Using App\_Config or Config \(proxy\)

If you wish to make use of the App_Config class, for defining your cpt slug/key, you can do either of the following._

```php
use PinkCrab\Modules\Registerables\Post_Type;
use PinkCrab\Core\Application\App_Config;

class Public_Post_Type extends Post_Type {
    
    public $singular = 'Public Post';
    public $plural   = 'Public Posts';
    
    public function __construct(App_Config $config){
        $this->key = $config->post_types('public_post', 'slug');
        parent::__construct();
    }
}
    
```

OR using the Config proxy, with prevents issues calling MyCpt::get\_slug\(\) as our constructor has not dependencies passed.

```php
use PinkCrab\Modules\Registerables\Post_Type;
use PinkCrab\Core\Application\Config;

class Public_Post_Type extends Post_Type {
    
    public $singular = 'Public Post';
    public $plural   = 'Public Posts';
    
    public function __construct(){
        // Using the Config proxy.
        $this->key = Config::post_types('public_post');
        parent::__construct();
    }
}
```

