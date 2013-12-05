MyBackend
===

**MyBackend** is a [Zend Framework 2](//framework.zend.com) module that glues a bunch of packages to lay down the base for an admin backend UI.

It is meant to be installed in a ZF2 app built from [MySkeleton](//github.com/stefanotorresi/MySkeleton).

Features
---

-   User authentication and authorization through [Doctrine ORM](http://www.doctrine-project.org), [ZfcUser](//github.com/ZF-Commons/ZfcUser) and [ZfcRbac](//github.com/ZF-Commons/ZfcRbac).
-   [AssetManager](//github.com/RWOverdijk/AssetManager) integration
-   Base layout glued with [ZfcTwitterBootstrap](//github.com/mwillbanks/ZfcTwitterBootstrap) and [YATSATRAP!](//github.com/stefanotorresi/yatsatrap)
-   I18n support via [MyI18n](//github.com/stefanotorresi/MyI18n)
-   You can drop in your backend components by just adding a backend navigation entry

TO-DO List
---
-   Write some docs
-   User CLI Controller
-   Make `bower.json` mergeable by the root app
