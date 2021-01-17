---
description: >-
  The PinkCrab framework comes with a very simple and lightweight PHP_Engine
  that implements the Renderable Interface.
---

# PHP\_Engine

## Setup

The PHP\_Engine must be constructed with a valid base path for your templates. This can be set globally using the Dependency rules, or at the time of construction.

```php
// @file - config/dependencies.php

use PinkCrab\Core\Application\App;
use PinkCrab\Core\Interfaces\Renderable;
use PinkCrab\Core\Services\View\PHP_Engine;

return array(
	// In the global rules set PHP_Engine to be used as the Renderable implementation
	'*'               => array(
		'substitutions' => array(
			Renderable::class => PHP_Engine::class,
		),
	),
	// Ensure the PHP_Engine is constructed with the defined view path 
	// Can be set to any path.
	PHP_Engine::class => array(
		'shared'          => true,
		'constructParams' => array( App::config( 'path', 'view' ) ),
	),
);

// These are included in the plugin boilerplate as are.
```

{% hint style="info" %}
Both **$app** & **$config** should already be availiable in the files function scope if included from _bootstrap.php_ file, so could be used over the **App::xx** singleton.
{% endhint %}

### Data

All data you wish to have access to can be passed into the `render()` methods as an array. The array keys are then cast into variables, allowing for cleaner HTML/PHP code. The standard PHP variable name rules apply, so all keys should be valid. Arrays and Objects can be passed into the views, allow for partials to be rendered too.

```php
$this->view->render('test/view',[
    'number'  => 5, 
    'colours' => ['red','green','blue'],
]);

// file - views/test/view.php

<?php
// Test View
?>
<p>We have a magic number, its $number <?php echo (int) $number; ?></p>
<ul>
<?php foreach($colours as $colour): ?>
    <li><?php echo esc_html( $colour );?></li>
<?php endforeach; ?>
</ul>
```

{% hint style="info" %}
**NO SANITIZATION OR ESCAPING IS CARRIED OUT AUTOMATICALLY AND SHOULD BE DONE IN THE TEMPLATE!**
{% endhint %}

### Partials

You can easily render a partial within your template. You have access to the PHP\_Engine class in your view, so you can call render\(\).

```php
$this->view->render('teams/members',[
    'title'  => 'Team 2', 
    'members' => [
        ['name' => 'Joe Bloggs', 'position' => 'Team Cpt.'.....],
        .......
    ],
]);

// file - views/teams/members.php

<?php
// Members list
?>
<h2><?php echo $title; ?></h2>
<p>Welcome to the team page for <?php echo $title; ?>.</p>
<p>Members</p>
<div>
    <?php foreach($members as $member) {
        $this->render('team/member-profile', $member, View::RETURN_VIEW );
    } ?>
</div>

// file - views/member-profile.php

<?php
// Members profile partial
?>
<div>
    <h2><?php echo $name; ?></h2>
    </p>Role: <span><?php echo $position; ?></span></p>
</div>
```

