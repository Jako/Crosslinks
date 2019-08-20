## Install from MODX Extras

Search for Crosslinks in the Package Manager of a MODX installation and install
it in there.

## Manual installation

If you can't access the MODX Extras Repository in your MODX installation, you
can manually install Crosslinks.

* Download the transport package from [MODX Extras](https://modx.com/extras/package/crosslinksofterms) (or one of the pre built transport packages in [_packages](https://github.com/Jako/Crosslinks/tree/master/_packages))
* Upload the zip file to your MODX installation's `core/packages` folder or upload it manually in the MODX Package Manager.
* In the MODX Manager, navigate to the Package Manager page, and select 'Search locally for packages' from the dropdown button.
* Crosslinks should now show up in the list of available packages. Click the corresponding 'Install' button and follow instructions to complete the installation.

## Build it from source

To build and install the package from source you could use [Git Package
Management](https://github.com/TheBoxer/Git-Package-Management). The GitHub
repository of Crosslinks contains a
[config.json](https://github.com/Jako/Crosslinks/blob/master/_build/config.json)
to build that package locally. Use this option, if you want to debug Crosslinks 
and/or contribute bugfixes and enhancements. 

If you want to run the unit tests, you have to rename the file
`test/properties.sample.inc.php` to `test/properties.inc.php` and change the
values in that file (if needed).
