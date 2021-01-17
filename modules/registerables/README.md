---
description: >-
  The Registerables modules brings a collection of Abstract and expandable
  classes, making registration of Post Types, Taxonomies and Ajax calls easy and
  clean thanks to the Registration process.
---

# Registerables

![](../../.gitbook/assets/taxonomy_simple%20%281%29.png)

### **Requires PinkCrab Framework & Enqueue Module**

If installing manually as a submodule, the _Abstract Ajax_ class uses the Enqueue module for Javascript includes. Please ensure this is included if you plan on using the **Ajax Registerable**.

[View on GitHub](https://github.com/Pink-Crab/pc_registerables)

### Installation

The **Registerables** module can be installed as either just submodules or using the Module\_Manager. Ensure you are in the root of your plugin directory before calling these.

#### As Git Submodule.

This can either be done using a GUI git client such as GitKracken or from the terminal

```bash
# Create Modules directory (Skip if already exists)
$ mkdir Modules

# Install Enqueue (Skip if already installed
$ git submodule add https://github.com/Pink-Crab/enqueue.git Modules/Enqueue

# Install Registerables
$ git submodule add https://github.com/Pink-Crab/pc_registerables.git Modules/Registerables
```

**Using Module\_Manger**

If you have the **Module\_Manger** installed, modules can be added simply using the module add command.

```bash
$ bash PinkCrab.sh module add registerables
```

### Location and Namespace

The **Registerables** module must be installed in `Modules/Registerables` and uses the `PinkCrab\Modules\Registerables` namespace.

