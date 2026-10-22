# Installing the Magento Extension

## Log In to Admin Panel

Log in to the Magento Admin Panel, which can usually be accessed at `https://www.example.com/admin`.

## Disable the Compiler

Navigate to **System > Tools > Compilation** in the top Admin Panel menu.

If compilation is enabled, click the 'Disable' button in the top right corner of the screen.

## Enable the Magento Cache

Navigate to **System > Cache Management** in the top Admin Panel menu and check if all cache types in the Cache Storage Management list are enabled.

If not, select the disabled cache types from the overview, choose 'Enable' from the 'Actions' dropdown menu and click 'Submit' to enable them.

## Download and Extract the Extension

Download the latest release of the extension from the [releases page](https://github.com/PluginCompany/magento1-coupon-import/releases) of its GitHub repository (the source code ZIP of the most recent version tag), or clone the repository with Git.

Extract the archive locally to any folder. It unpacks into a folder named `magento1-coupon-import-<version>` that contains the Magento 1 files of the extension, plus documentation and packaging files.

## Upload Files to Server

From inside the extracted folder, copy the `app` folder to the root directory of your Magento installation, merging them with the existing folders.

Do not copy `docs/`, `README.md`, `CHANGELOG.md`, `LICENSE`, `THIRD-PARTY-NOTICES.md`, `modman` or `composer.json`; they are not part of the Magento code. To install with modman or Composer instead, see the alternatives below.

## Refresh the Magento Cache

Navigate to **System > Cache Management** in the top Admin Panel menu.

Click the 'Flush Magento Cache' button in the top right corner of the screen to clear the cache.

## Log Out from Admin Panel

Log out from the Magento Admin Panel, then log back in to complete the installation.

## Re-enable Compilation (Optional)

Navigate to **System > Tools > Compilation** in the top Admin Panel menu.

Click the 'Run Compilation Process' button in the top right corner of the screen to re-enable compilation.

## Alternative: Install with modman

[modman](https://github.com/colinmollenhour/modman) installs a module from its Git repository and links its files into your Magento installation, as listed in the repository's `modman` file. Run the following commands in your Magento root directory (`modman init` is only needed once per installation):

```bash
modman init
modman clone https://github.com/PluginCompany/magento1-coupon-import.git
```

modman creates symlinks, so enable **System > Configuration > Developer > Template Settings > Allow Symlinks**, then refresh the Magento cache. To update later, run `modman update magento1-coupon-import`.

## Alternative: Install Using Composer

If your Magento 1 / OpenMage project is managed with Composer, it needs `magento-hackathon/magento-composer-installer` (or an equivalent installer for packages of type `magento-module`), and the Magento root directory must be set in your project's `composer.json`, relative to that file:

```json
"extra": {
    "magento-root-dir": "htdocs/"
}
```

Then add the Plugin Company repository and require the package:

```bash
composer config repositories.plugin.company composer https://plugin.company/
composer require plugincompany/magento1-coupon-import
```

Refresh the Magento cache as described above.

---

[Documentation index](./README.md) · [README](../README.md) · [Online version](https://plugin.company/knowledge-base/docs/magento-coupon-import/installation-guide/installing-the-extension/)
