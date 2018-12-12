## Introduction

This MODX Extra adds a custom manager page that allows you to create and
maintain a list of crosslinks in your site. Entries into the crosslinks take the
form of `text => resource` where `text` is the linktext and `resource` is the
linked resource.

## Plugin

The crosslink plugin parses page content field on render and replaces all
occurrences of the linktext with the markup defined in the plugin's tpl chunk.
This can be used to set a link to the crosslinked resource for a given linktext.
The plugin replaces the linktext only in the content field of the resource.

The Plugin could be controlled by the following MODX System settings:

Setting | Description | Default
------------|---------|--------
debug | Log debug informations in the MODX error log. | No
disabledAttributes | (Comma separated list) Crosslinks does not replace text inside of this HTML tag attributes. | No
fullwords | Replace only full words of a crosslinks term in the resource content. | Yes
sections | Replace Crosslinks links only in sections marked with `<!— CrosslinksStart -->` and `<!— CrosslinksEnd -->`. The section markers could be changed with the settings `crosslinks.sectionsStart` and `crosslinks.sectionsEnd`. | No
sectionsEnd | Marker at the end of a section processed by Crosslinks. The restriction to marked sections can be activated in the setting `crosslinks.sections`. | No
sectionsStart | Marker at the start of a section processed by Crosslinks. The restriction to marked sections can be activated in the setting `crosslinks.sections`. | No
tpl | Template Chunk for the highlight replacement. | Crosslinks.linkTpl

## Available placeholders

The following placeholders are available in the chunks used by the plugin:

Placeholder | Description | Chunk
------------|-------------|------
text | The linktext being referenced. | linkTpl
link | The link to the resource | linkTpl
resource | The id of the linked resource | linkTpl
parameter | json encoded array of link parameters | linkTpl

The default chunk for these placeholders is available with the `Crosslinks.`
prefix. If you want to change the chunk, you have to duplicate it and change the
duplicate. The default chunk is reset with each update of the Crosslinks extra.
