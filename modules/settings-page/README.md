---
description: Extendable class for creating WP_Settings API driven settings pages.
---

# Settings Page

### **Requires PinkCrab Framework & Form\_Fields Module**

If installing manually as a submodule, the Form\_Fields module will need to be added, this is used to render the fields for the settings.

[View on GitHub](https://github.com/Pink-Crab/Settings_Page)

### Installation

The **Settings\_Page** module can be installed as either just submodules or using the Module\_Manager. Ensure you are in the root of your plugin directory before calling these.

#### As Git Submodule.

This can either be done using a GUI git client such as GitKracken or from the terminal

```bash
# Create Modules directory (Skip if already exists)
$ mkdir Modules

# Install Form_Fields (Skip if already installed
$ git submodule add https://github.com/Pink-Crab/Form_Fields.git Modules/Form_Fields

# Install Registerables
$ git submodule add https://github.com/Pink-Crab/Settings_Page.git Modules/Registerables
```

**Using Module\_Manger**

If you have the **Module\_Manger** installed, modules can be added simply using the module add command.

```bash
$ bash PinkCrab.sh module add settings_page
```

### Location and Namespace

The **Settings\_Page** module must be installed in `Modules/Settings_Page` and uses the `PinkCrab\Modules\Settings_Page` namespace.

