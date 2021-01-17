---
description: >-
  MetaBoxes can be constructed and used as either parts of registered post types
  or as independently for Users, Pages and anywhere else you can naively render
  one.
---

# MetaBox

### Basic Setup

There are 2 ways to create either kind of MetaBox

```php
use PinkCrab\Modules\Registerables\MetaBox;

// Manual Instancing
$metabox = new MetaBox('my_metabox_key_1');

// Create with normal (wider) context
$metabox = MetaBox::normal('my_metabox_key_1');

// Create with advanced (wider) context
$metabox = MetaBox::advanced('my_metabox_key_1');

// Create with side context
$metabox = MetaBox::side('my_metabox_key_1');
```

Depending on your preferences, you can use the static constru_c_tor to create your MetaBox a single chained method call.

### label

The MetaBox needs a label applying, this acts as the header value.

```php
// Depending on how you instantiated your metabox, the title can be added as.

$metabox = new MetaBox('my_metabox_key_1');
$metabox->label ='My First MetaBox';

// OR

$metabox = MetaBox::normal('my_metabox_key_1')
    ->label('My First MetaBox');
```

### Context

The MetaBox can be placed using the context property. By default, this is set as normal and can either be set using the static constructors or as follows.

```php
$metabox = new MetaBox('my_metabox_key_1');
$metabox->context = 'side';
$metabox->context = 'normal';
$metabox->context = 'advanced';

// OR

$metabox->as_side(); // for 'side'
$metabox->as_advanced(); // for 'advanced'
$metabox->as_normal(); // for 'normal'
```

### Screen

You can define whichever screen you wish to render the MetaBox on. This can be defined by-passing the screen id, post type, or WP\_Screen instance. These should be passed as single values.

```php
// To render on all post and page edit.php pages.
$metabox = MetaBox::normal('my_metabox_key_1')
    ->screen('post')
    ->screen('page');
```

If you are registering your MetaBox when defining a post type, the screen is automatically added when registered. So no need to pass the post type key.

### View

Each MetaBox has its own definable view callback, this can either be called inline or a separate method within your class.

```php
// Inline
$metabox = MetaBox::normal('my_metabox_key_1')
    ->view(static function($post, $args){
        echo 'Hi from my metabox, im called statically as i do not need to be bound to the class. Micro optimisations ;) ';
    });

// OR 

$metabox = new MetaBox('my_metabox_key_1');
$metabox->view = function($post, $args){
    echo 'Hi from my metabox';
};
```

```php
// As part of a controller class.

use Some\Namespace\My_Service;
use PinkCrab\Core\Interfaces\Registerable;
use PinkCrab\Modules\Registerables\MetaBox;
use PinkCrab\Core\Services\Registration\Loader

class MetaBox_Controller implements Registerable {
    
    /** Our service to use in view */
    protected $my_service;
    
    public function __construct(My_Service $my_service) {
        $this->my_service = $my_service;
    }
    
    /**
     * We use the register hook, to register
     * as many metaboxes as we need
     */
    public function register( Loader $loader): void {
        $this->register_metabox($loader);        
        // $this->register_another_metabox($loader);
    }
    
    /**
     * Registers the metabox
     */
    protected function register_metabox(Loader $loader){
        MetaBox::normal('my_metabox_key_1')
            ->title('My MetaBox')
            ->screen('post')
            ->screen('page')
            ->view([$this, 'metabox_view'])
            ->register($loader);
    }
    
    /**
     * Renders the metaboxes view
     * Is bound to class to access $my_service
     */
    public function metabox_view( WP_Post $post, array $args): void {
        $data = $this->my_service->from_post($post);
        printf( "Wow this %s is from data", $data->thing );
    }
}
    
```

### View Vars

Data can be passed through to the MetaBox view callable, unlike the native MetaBox functions. The view vars passed to the view callable are only those defined within the view\_vars\(\) method. _These are optional, can be omitted if you don't need to pass additional data._

```php
MetaBox::normal('my_metabox_key_1')
    ->view_vars(['user' => get_current_user_id(),...])
    ->view(function( WP_Post $post, args $args): void {
        printf("Hello user with ID:%d", $args['user']);
    });
```

If used with a View class, MetaBox content can be parsed with templating languages such as Blade, Twig, etc.

```php
class Some_Controller {
    protected $view;
    
    public function __construct(Renderable $view) {
        $this->view = $view;
    }
    
    protected function register_metabox(Loader $loader){
        MetaBox::normal('my_metabox_key_1')
            ->title('My MetaBox')
            ->view_vars(['user' => get_current_user_id(), 'foo' => 'bar'])
            ->view(function($post, $ags): void{
                $this->view->render('some.template', $args);
            })
            ->register($loader);
    }
}
```

### Priority

You can use the priority value to denote when the MetaBox is loaded in context with the rest of the page. By default, this is passed as 'default' but can be 

```php
$metabox = new MetaBox('my_metabox_key_1');
$metabox->priority = 'high';
$metabox->priority = 'core';
$metabox->priority = 'default';
$metabox->priority = 'low';

// OR

MetaBox::advanced('my_metabox_key_1')
    ->priority('high'); 
MetaBox::advanced('my_metabox_key_1')
    ->priority('core'); 
    
MetaBox::advanced('my_metabox_key_1')
    ->priority('default'); 
    
MetaBox::advanced('my_metabox_key_1')
    ->priority('low'); 
```

### Add Action

Actions can be applied to MetaBoxes,  this allows for the verification and processing of additional meta fields. Any form fields added, will be passed to the global POST array. _Care should be taken when using save\_post, as this is fired when the draft post is created and before the MetaBox is rendered._   
Multiple actions can be passed, allowing for granular control over your MetaBox data.

```php
// Inline
MetaBox::advanced('my_metabox_key_1')
    ->action(
        'post_updated', 
        function($id, $after_update, $before_update){
            if(isset($_POST['my_field']){
                update_post_meta($id, 'my_meta', sanitize_text_field($_POST['my_field']);
            }
        }, 
        10, 
        3
    ); 
    

// Part of class
public function register_metabox($loader): void {
    MetaBox::advanced('my_metabox_key_1')
    ->action('post_updated', [$this, 'post_updated_callback'], 10, 3)
    ->register($loader);
}

public function post_updated_callback($post_id, $after_update, $before_update): void {
    if(isset($_POST['my_field']){
        update_post_meta($id, 'my_meta', sanitize_text_field($_POST['my_field']);
    }
}

// Using the property.
$metabox = new MetaBox('my_metabox_key_1');
$metabox->action['post_updated'] = [
    'callback' => [$this, 'post_updated_callback'],
    'priority' => 10,
    'params' => 3
];
```

_Priority has a default of 10 and params of 1._

### Register

MetaBoxes must be registered as part of the registration process, and a valid instance of loader must be passed to the register\(\) method. Under the hood, the MetaBox is registered on the '**add\_meta\_box**' hook automatically. This can be used to treat the MetaBox as an abstract registerable like post\_type and taxonomy but isn't really intended to do so. 

Please see the example above under View to see an example of implementing in a custom metabox\_controller.

Whenever MetaBoxes are added via the Post\_Type registerable, the register method is called automatically when registering the post type, so is not required.

```php
// As part of a post type

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
            // No need to call the register method (no access to loader anyways)
    }
}
```

