<?php

declare(strict_types=1);
/**
 * Class of constants and static functions for all hook handles.
 *
 * @since 0.4.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Application;

class Hooks {
	/** @var string */
	public const APP_INIT_PRE_BOOT = 'PinkCrab/App/Boot/pre_init_call';
	/** @var string */
	public const APP_INIT_PRE_REGISTRATION = 'PinkCrab/App/Boot/pre_registration';
	/** @var string */
	public const APP_INIT_POST_REGISTRATION = 'PinkCrab/App/Boot/post_registration';
	/** @var string */
	public const APP_INIT_CONFIG_VALUES = 'PinkCrab/App/Boot/app_config_values';
	/** @var string */
	public const APP_INIT_REGISTRATION_CLASS_LIST = 'PinkCrab/App/Boot/registration_class_list';
	/** @var string */
	public const APP_INIT_SET_DI_RULES = 'PinkCrab/App/Boot/set_di_rules';
}

