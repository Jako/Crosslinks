## Introduction

This MODX Extra adds a custom manager page that allows you to create and
maintain a list of crosslinks. Each entry of the crosslinks list take the form
of `text => resource` where `text` is the searched linktext and `resource` is
the linked resource. A plugin replaces each linktext on a MODX resource with a
crosslink.

## Plugin

The crosslink plugin parses the content field on render and replaces all
occurrences of each linktext with the markup defined in the tpl chunk. This can
be used to set a link to the crosslinked resource for a given linktext. The
plugin replaces the linktext only in the content field of the resource.

The Plugin could be controlled by the following MODX System settings:

Setting | Description | Default
------------|---------|--------
debug | Log debug informations in the MODX error log. | No
disabledTags | (Comma separated list) Crosslinks does not replace text inside of this HTML tags. | a,form,select
fullwords | Replace only full words of a crosslinks term in the resource content. | Yes
limit | Limit the replacements of one crosslink text in one resource to this number (0 = not limited) | 0
sections | Replace Crosslinks links only in sections marked with `<!— CrosslinksStart -->` and `<!— CrosslinksEnd -->`. The section markers could be changed with the settings `crosslinks.sectionsStart` and `crosslinks.sectionsEnd`. | No
sectionsEnd | Marker at the end of a section processed by Crosslinks. The restriction to marked sections can be activated in the setting `crosslinks.sections`. | `<!— CrosslinksStart -->`
sectionsStart | Marker at the start of a section processed by Crosslinks. The restriction to marked sections can be activated in the setting `crosslinks.sections`. | `<!— CrosslinksEnd -->`
tpl | Template Chunk for the highlight replacement. | Crosslinks.linkTpl

## Available placeholders

The following placeholders are available in the linkTpl chunk used by the plugin:

Placeholder | Description
------------|------------
text | The linktext being referenced.
link | The link to the resource
resource | The id of the linked resource
parameter | json encoded array of link parameters

The default chunk for these placeholders is available with the `Crosslinks.`
prefix. If you want to change the chunk, you have to duplicate it, change the
duplicate and reference the duplicate in the MODX system settings. The default
chunk would be reset with each update of the Crosslinks extra.
