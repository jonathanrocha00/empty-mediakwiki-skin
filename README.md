# vhv, a Bootstrap based skin for MediaWiki

vhv is a skin for **[MediaWiki](http://mediawiki.org/)** (1.25+) based on Twitter's 
**[Bootstrap](http://getbootstrap.com/)** (v3.3.7). It tries to implement as much of 
Bootstrap's functionality as possible, allowing to use it very easily and with very 
reduced markup, and features many configuration options.

You can find an instance of vhv in action and the extended documentation at the 
project's website **[vhv.thai-land.at](http://vhv.thai-land.at/)**


## Get started

1. Change to the "skins" subdirectory of your MediaWiki installation:

   ```
   cd skins
   ```

2. Clone the repository:

   ```
   git clone https://github.com/thaider/vhv vhv
   ```

3. Add the following to `LocalSettings.php`: 

   ```php
   wfLoadSkin( 'vhv' );
   $wgDefaultSkin = "vhv";
   ```

## Optional configuration

Please refer to the project's website **[vhv.thai-land.at](http://vhv.thai-land.at/)** 
for further information about configuration and customization options.


## Licensing, Copying, Usage

vhv is open source, and built on open source projects.

Please check out the [LICENSE file](https://github.com/thaider/vhv/blob/master/LICENSE) 
for details.
"#empty-mediawiki-skin" 
