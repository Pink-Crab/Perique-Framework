---
description: >-
  The registerable interface can be added to any class which you wish to be
  included in the registration process.
---

# Registerable \(Interface\)

## Using the Registerable interface.

The registerable interface has a single method that must be implemented.

```php
class Events_Post_Table_Controller implements Registerable {
    /**
     * Gives us access to our events
     *
     * @var Event_Repository
     */
    protected $events;
    
    /**
     * Create controller with the event repository injected
     * We can then use the repository to handle all of our basic set/get
     * interactions with the post type.
     * 
     * @param Event_Repository $events Access to events
     */
    public function __construct( Event_Repository $events ) {
		  $this->events = $events;
	  }
	
		/**
		 * Register all hooks
		 *
		 * @param Loader $loader
		 * @return void
		 */
	  public function register( Loader $loader ): void {
		  $loader->action( "manage_{$this->events->cpt_key}_posts_columns", [$this, 'add_columns'] );
			$loader->action( "manage_{$this->events->cpt_key}_posts_custom_column", [$this, 'render_tickets_sold_cell'], 10, 2 );
	  }
	
		/**
		 * Add additional columns to post table.
		 *
		 * @param array $columns Current columns.
		 * @return array
		 */
		public function add_columns( array $columns ): array {
			$columns['tickets_sold'] = __( 'Sold', 'my_plugin' );
			return $columns;
		}
		
		/**
		 * Populates the custom columns cell.
		 *
		 * @param string $column Column being called from.
		 * @param int $post_id The post being displayed.
		 * @return void
		 */
		public function render_tickets_sold_cell( string $column, int $post_id ): void {
			if( $column === 'tickets_sold' ){
				printf(
					'<strong>%d</strong>',
					count($this->events->tickets_sold($post_id)
				);
			}
		}
	}
```

{% hint style="info" %}
 The class then needs to be added to the array found in /config/registration.php 
{% endhint %}

```php
/**
 * Holds all classes which are to be loaded on initalisation.
 */
 
use Your\Full\Namspaced\Path\Events_Post_Table_Controller;

return array(
  // All of our registables classes go here.
  Events_Post_Table_Controller::class,
);
```

Once this has been added into the registration array, it will then be constructed and your hooks will be registered.

{% hint style="info" %}
The hook calls are registered on init with a priority of 1. So if you need to defer the adding of methods until later you can use the following.
{% endhint %}

```php
class Events_Post_Table_Controller implements Registerable {
  
  // Register the hooks on some_later_action.
  public function register( Loader $loader ): void {
  
    // Hook in on the earlies hook where your data is ready.
    $loader->action('some_later_action', function() {
      
      // Now add out our action. This is useful in woo, as the cart
      // is not ready until plugins_loaded.
      add_action( 'init' [$this, 'my_method'] );
    }
  }
}
```

Once the loader has been called, it cant be used again. So for any deferred hooks, ensure you use the regular **add\_action** or **add\_filter**.

