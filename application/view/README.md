---
description: >-
  The core comes with a Renderable interface, allowing any PHP templating
  package to be used with a simple Provider.
---

# View

The Renderable interface can be injected into any class which you plan to construct using the Apps container. Combined with the Registration system, this allows for the creation of Controllers that can render views based on hook calls.

```php
class Post_Controller implements Registerable {
	/**
	 * @param View $view
	 */
	 protected $view;

	public function __construct( View $view ) {
		$this->view = $view;
	}

	/**
	 * @param Loader $loader
	 */
	public function register( Loader $loader ): void {
		$loader->filter( 'the_content', [ $this, 'render_content' ], 1, 20 );
	}

	/**
	 * @param string $content Inital content
	 * @return string Replaced with custom view template, passing in the post
	 */
	public function render_content( string $content ): string {
		return is_single() && get_post_type() === 'post'
			? $this->view->render( 
				'post/single', // for path "views/post/single.php"
				[ 'post' => get_post() ], 
				View::RETURN_VIEW // false, true(default) for print  
			)
			: $content;
	}
}
```

### PHP\_Engine

By default, the **PinkCrab** framework comes with a very basic PHP implementation of the **Renderable** interface.

```php
$this->view->render('test/view',['number'=>5, 'colours'=>['red','green','blue']);

// file - views/test/view.php

<?php
// Test View
?>
<p>We have a magic number, called $number <?php echo $number; ?></p>
<ul>
<?php foreach($colours as $colour): ?>
    <li><?php echo $colour;?></li>
<?php endforeach; ?>
</ul>
```

### Addition Providers

Other templating languages can be used, we currently have a version of BladeOne which will be ported over to 0.3.0 soon.

